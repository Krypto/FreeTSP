<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** http://www.freetsp.info
** https://github.com/Krypto/FreeTSP
** Licence Info: GPL
** Copyright (C) 2010 FreeTSP v1.0
** A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
** Project Leaders: Krypto, Fireknight.
**/

require_once(FUNC_DIR.'function_main.php');

function get_row_count ($table, $suffix = "")
{
    if ($suffix)
    {
        $suffix = " $suffix";
    }

    ($r = sql_query("SELECT COUNT(*)
                        FROM $table$suffix")) or die(mysql_error());

    ($a = mysql_fetch_row($r)) or die(mysql_error());

    return $a[0];
}

function docleanup ()
{
    global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $days, $oldtorrents, $autoclean_interval, $posts_read_expiry;

    set_time_limit(0);
    ignore_user_abort(1);

    do
    {
        $res = sql_query("SELECT id
                            FROM torrents");

        $ar = array();

        while ($row = mysql_fetch_array($res, MYSQL_NUM))
        {
            $id      = $row[0];
            $ar[$id] = 1;
        }

        if (!count($ar))
        {
            break;
        }

        $dp = @opendir($torrent_dir);

        if (!$dp)
        {
            break;
        }

        $ar2 = array();

        while (($file = readdir($dp)) !== false)
        {
            if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
            {
                continue;
            }

            $id       = $m[1];
            $ar2[$id] = 1;

            if (isset($ar[$id]) && $ar[$id])
            {
                continue;
            }

            $ff = $torrent_dir."/$file";
            unlink($ff);
        }

        closedir($dp);

        if (!count($ar2))
        {
            break;
        }

        $delids = array();

        foreach (array_keys($ar)
                 AS
                 $k)
        {
            if (isset($ar2[$k]) && $ar2[$k])
            {
                continue;
            }

            $delids[] = $k;

            unset($ar[$k]);
        }

        if (count($delids))
        {
            sql_query("DELETE
                        FROM torrents
                        WHERE id IN (".join(",", $delids).")");
        }

        $res = sql_query("SELECT torrent
                            FROM peers
                            GROUP BY torrent");

        $delids = array();

        while ($row = mysql_fetch_array($res, MYSQL_NUM))
        {
            $id = $row[0];

            if (isset($ar[$id]) && $ar[$id])
            {
                continue;
            }

            $delids[] = $id;
        }

        if (count($delids))
        {
            sql_query("DELETE
                        FROM peers
                        WHERE torrent IN (".join(",", $delids).")");
        }

        $res = sql_query("SELECT torrent
                            FROM files
                            GROUP BY torrent");

        $delids = array();
        while ($row = mysql_fetch_array($res, MYSQL_NUM))
        {
            $id = $row[0];

            if ($ar[$id])
            {
                continue;
            }

            $delids[] = $id;
        }

        if (count($delids))
        {
            sql_query("DELETE
                        FROM files
                        WHERE torrent IN (".join(",", $delids).")");
        }
    }
    while (0);

    $deadtime = deadtime();

    sql_query("DELETE
                FROM peers
                WHERE last_action < FROM_UNIXTIME($deadtime)");

    $deadtime -= $max_dead_torrent_time;

    sql_query("UPDATE torrents
                SET visible='no'
                WHERE visible='yes'
                AND last_action < FROM_UNIXTIME($deadtime)");

    $deadtime = time() - $signup_timeout;

    sql_query("DELETE
                FROM users
                WHERE status = 'pending'
                AND added < FROM_UNIXTIME($deadtime)
                AND last_login < FROM_UNIXTIME($deadtime)
                AND last_access < FROM_UNIXTIME($deadtime)");

    $torrents = array();

    $res = sql_query("SELECT torrent, seeder, COUNT(*) AS c
                        FROM peers
                        GROUP BY torrent, seeder");

    while ($row = mysql_fetch_assoc($res))
    {
        if ($row["seeder"] == "yes")
        {
            $key = "seeders";
        }
        else
        {
            $key = "leechers";
        }
        $torrents[$row["torrent"]][$key] = $row["c"];
    }

    $res = sql_query("SELECT torrent, COUNT(*) AS c
                        FROM comments
                        GROUP BY torrent");

    while ($row = mysql_fetch_assoc($res))
    {
        $torrents[$row["torrent"]]["comments"] = $row["c"];
    }

    $fields = explode(":", "comments:leechers:seeders");

    $res = sql_query("SELECT id, seeders, leechers, comments
                        FROM torrents");

    while ($row = mysql_fetch_assoc($res))
    {
        $id   = $row["id"];
        $torr = $torrents[$id];

        foreach ($fields
                 AS
                 $field)
        {
            if (!isset($torr[$field]))
            {
                $torr[$field] = 0;
            }
        }
        $update = array();

        foreach ($fields
                 AS
                 $field)
        {
            if ($torr[$field] != $row[$field])
            {
                $update[] = "$field = ".$torr[$field];
            }
        }

        if (count($update))
        {
            sql_query("UPDATE torrents
                        SET ".implode(",", $update)."
                        WHERE id = $id");
        }
    }

    //-- Delete Parked User Accounts --//
    $secs     = 175 * 86400; //-- Set At 175 Days - Change The Time To Fit Your Needs --//
    $dt       = (time() - $secs);
    $maxclass = UC_POWER_USER;

    sql_query("DELETE
                FROM users
                WHERE parked='yes'
                AND status='confirmed'
                AND class <= $maxclass
                AND last_access < $dt");

    //-- Delete Inactive User Accounts --//
    $secs     = 42 * 86400;
    $dt       = sqlesc(get_date_time(gmtime() - $secs));
    $maxclass = UC_POWER_USER;

    sql_query("DELETE
                FROM users
                WHERE parked='yes'
                AND status='confirmed'
                AND class <= $maxclass
                AND last_access < $dt");

    //-- Delete Old Login Attempts --//
    $secs = 1 * 86400; //--  Delete Failed Login Attempts Per One Day. --//
    $dt   = sqlesc(get_date_time(gmtime() - $secs)); //-- Calculate Date. --//

    sql_query("DELETE
                FROM loginattempts
                WHERE banned='no'
                AND added < $dt");

    //-- Remove Expired Warnings --//
    $res = sql_query("SELECT id
                        FROM users
                        WHERE warned='yes'
                        AND warneduntil < NOW()
                        AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
        $dt  = sqlesc(get_date_time());
        $msg = sqlesc("Your Warning has been Removed. Please keep in your best behaviour from now on.\n");

        while ($arr = mysql_fetch_assoc($res))
        {
            sql_query("UPDATE users
                        SET warned = 'no', warneduntil = '0000-00-00 00:00:00'
                        WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

            sql_query("INSERT INTO messages (sender, receiver, added, msg, poster)
                        VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
        }
    }

    //-- Delete Old Help Desk Questions --//
    $secs = 7 * 86400; //-- Delete Old Questions After 7 Days
    $dt   = sqlesc(get_date_time(gmtime() - $secs)); //-- Calculate Date & Time Based On GMT.

    sql_query("DELETE
                FROM helpdesk
                WHERE added < $dt");

    //-- Remove Disabled Offer Comment Status Time Based --//
    $res = sql_query("SELECT id, username
                        FROM users
                        WHERE offercompos='no'
                        AND offercomposuntil < NOW()
                        AND offercomposuntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
         $dt      = sqlesc(get_date_time());
         $msg     = sqlesc("Your Offer Comment Privilege has been returned to you. \n Please be more careful in the future.");
         $subject = sqlesc("Your Offer Comment Status");

         while ($arr = mysql_fetch_assoc($res))
         {
             sql_query("UPDATE users
                        SET offercompos = 'yes', offercomposuntil = '0000-00-00 00:00:00'
                        WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

             sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster)
                        VALUES(0, $arr[id], $dt, $subject, $msg, 0)") or sqlerr(__FILE__, __LINE__);

             write_stafflog("<a href=userdetails.php?id=$arr[id]><strong>$arr[username]</strong></a> - had their Offer Comment Privilage returned by - <strong>Clean Up System</strong>");

         }
    }

    //-- Remove Disabled Request Comment Status Time Based --//
    $res = sql_query("SELECT id, username
                        FROM users
                        WHERE requestcompos='no'
                        AND requestcomposuntil < NOW()
                        AND requestcomposuntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
         $dt = sqlesc(get_date_time());
         $msg    = sqlesc("Your Request Comment Privilege has been returned to you. \n Please be more careful in the future.");
         $subject = sqlesc("Your Request Comment Status");

         while ($arr = mysql_fetch_assoc($res))
         {
             sql_query("UPDATE users
                        SET requestcompos = 'yes', requestcomposuntil = '0000-00-00 00:00:00'
                        WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

             sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster)
                        VALUES(0, $arr[id], $dt, $subject, $msg, 0)") or sqlerr(__FILE__, __LINE__);

             write_stafflog("<a href=userdetails.php?id=$arr[id]><strong>$arr[username]</strong></a> - had their request comment privilage returned by - <strong>Clean Up System</strong>");

         }
    }

    //-- Promote Power Users --//
    $limit    = 25 * 1024 * 1024 * 1024;
    $minratio = 1.05;
    $maxdt    = sqlesc(get_date_time(gmtime() - 86400 * 28));

    $res = sql_query("SELECT id
                        FROM users
                        WHERE class = 0
                        AND uploaded >= $limit
                        AND uploaded / downloaded >= $minratio
                        AND added < $maxdt") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
        $dt  = sqlesc(get_date_time());
        $msg = sqlesc("Congratulations, you have been Auto-Promoted to [b]Power User[/b]. :)\n");

        while ($arr = mysql_fetch_assoc($res))
        {
            sql_query("UPDATE users
                        SET class = 1
                        WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

            sql_query("INSERT INTO messages (sender, receiver, added, msg, poster)
                        VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);

            status_change($arr['id']);
        }
    }

    //-- Demote Power Users --//
    $minratio = 0.95;

    $res = sql_query("SELECT id
                        FROM users
                        WHERE class = 1
                        AND uploaded / downloaded < $minratio") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
        $dt  = sqlesc(get_date_time());
        $msg = sqlesc("You have been Auto-Demoted from [b]Power User[/b] to [b]User[/b] because your Share Ratio has dropped below $minratio.\n");

        while ($arr = mysql_fetch_assoc($res))
        {
            sql_query("UPDATE users
                        SET class = 0
                        WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

            sql_query("INSERT INTO messages (sender, receiver, added, msg, poster)
                        VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);

            status_change($arr['id']);
        }
    }

    //-- Delete Orphaned Announcement Processors --//
    sql_query("DELETE announcement_process
                FROM announcement_process
                LEFT JOIN users ON announcement_process.user_id = users.id
                WHERE users.id IS NULL");

    //-- Delete Expired Announcements And Processors --//
    sql_query("DELETE FROM announcement_main
                WHERE expires < ".sqlesc(time()));

    sql_query("DELETE announcement_process
                FROM announcement_process
                LEFT JOIN announcement_main ON announcement_process.main_id = announcement_main.main_id
                WHERE announcement_main.main_id IS NULL");


    $registered     = get_row_count('users');
    $unverified     = get_row_count('users', "WHERE status='pending'");
    $torrents       = get_row_count('torrents');
    $seeders        = get_row_count('peers', "WHERE seeder='yes'");
    $leechers       = get_row_count('peers', "WHERE seeder='no'");
    $torrentstoday  = get_row_count('torrents', 'WHERE added > DATE_SUB(NOW(), INTERVAL 1 DAY)');
    $donors         = get_row_count('users', "WHERE donor='yes'");
    $unconnectables = get_row_count('peers', "WHERE connectable='no'");
    $forumposts     = get_row_count('posts');
    $forumtopics    = get_row_count('topics');
    $dt             = sqlesc(get_date_time(gmtime() - 300)); //-- Active Users Last 5 Minutes --//
    $numactive      = get_row_count('users', "WHERE last_access >= $dt");
    $Users          = get_row_count('users', "WHERE class='0'");
    $Poweruser      = get_row_count('users', "WHERE class='1'");
    $Vip            = get_row_count('users', "WHERE class='2'");
    $Uploaders      = get_row_count('users', "WHERE class = '3'");
    $Moderator      = get_row_count('users', "WHERE class = '4'");
    $Adminisitrator = get_row_count('users', "WHERE class = '5'");
    $Sysop          = get_row_count('users', "WHERE class = '6'");
    $Manager        = get_row_count('users', "WHERE class = '7'");

    sql_query("UPDATE stats
                SET regusers = '$registered', unconusers = '$unverified', torrents = '$torrents', seeders = '$seeders', leechers = '$leechers', unconnectables = '$unconnectables', torrentstoday = '$torrentstoday', donors = '$donors', forumposts = '$forumposts', forumtopics = '$forumtopics', numactive = '$numactive', Users = '$Users', Poweruser = '$Poweruser', Vip = '$Vip', Uploaders = '$Uploaders', Moderator = '$Moderator', Adminisitrator = '$Adminisitrator', Sysop ='$Sysop', Manager = '$Manager'
                WHERE id = '1'
                LIMIT 1");

    if ($oldtorrents)
    {
        $dt = sqlesc(get_date_time(gmtime() - ($days * 86400)));

        $res = sql_query("SELECT id, name
                            FROM torrents
                            WHERE added < $dt");

        while ($arr = mysql_fetch_assoc($res))
        {
            @unlink("$torrent_dir/$arr[id].torrent");
            sql_query("DELETE
                        FROM torrents
                        WHERE id=$arr[id]");

            sql_query("DELETE
                        FROM peers
                        WHERE torrent=$arr[id]");

            sql_query("DELETE
                        FROM comments
                        WHERE torrent=$arr[id]");

            sql_query("DELETE
                        FROM files
                        WHERE torrent=$arr[id]");

            sql_query("DELETE
                        FROM thanks
                        WHERE torrentid=$arr[id]");

            write_log("Torrent $arr[id] ($arr[name]) was Deleted by System (Older than $days days)");
        }
    }
}

?>