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

//-- Start Execution Time --//
$qtme['start'] = microtime(true);
//-- End --//

//-- Strip Slashes By System --//
function cleanquotes (&$in)
{
    if (is_array($in))
    {
        return array_walk($in, 'cleanquotes');
    }
    return $in = stripslashes($in);
}

if (get_magic_quotes_gpc())
{
    array_walk($_GET, 'cleanquotes');
    array_walk($_POST, 'cleanquotes');
    array_walk($_COOKIE, 'cleanquotes');
    array_walk($_REQUEST, 'cleanquotes');
}

function local_user ()
{
    return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"];
}

function illegal_access($page, $class)
{
    global $CURUSER;

    $page =  $_SERVER['PHP_SELF'];

    if (get_user_class() < $class)
    {
        $added    = sqlesc(get_date_time());
        $subject  = sqlesc("Illegal Hacking Attempt");
        $username = $CURUSER['username'];
        $userid   = $CURUSER['id'];
        $msg      = sqlesc("Your have been caught making a Illegal Hacking Attempt.\n\nYour attempt has been Reported to the Staff.\n\nYou will be notified in due course, of the consequences regarding this action");

        sql_query("INSERT INTO messages (sender, receiver, added, subject, msg)
                        VALUES (0, $userid, $added, $subject, $msg)") or sqlerr(__FILE__, __LINE__);

        write_stafflog ("<strong><a href='userdetails.php?id=$userid'>$username.</a></strong> -- Attempted to access $page");

        error_message_center("error","Error", "<strong>$username</strong> This Is A Hacking Attempt!!!");
    }
}

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'function_config.php');
require_once(FUNC_DIR.'function_cleanup.php');

/*
function tr($x,$y,$noesc=0)
{
    if ($noesc)
        $a = $y;

    else
    {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }

    print("<tr><td class='heading' align='right' valign='top'>$x</td><td valign='top' align='left'>$a</td></tr>\n");
}

if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
{
    $file_path = str_replace("\\", "/", dirname(__FILE__));
    $file_path = str_replace("/functions", "", $file_path);
}
else
{
    $file_path = dirname(__FILE__);
    $file_path = str_replace("/functions", "", $file_path);
}

define('ROOT_PATH', $file_path);
*/

//-- Do Not Modify -- Versioning System --//
//-- This Will Help Identify Code For Support Issues At freetsp.info --//

function copyright ()
{
    global $curversion;

    echo("Powered by <a href='http://www.freetsp.info'>".FTSP." Version ".$curversion."</a> &copy; <a href='http://www.freetsp.info'>".FTSP."</a> ".(date("Y") > 2010 ? "2010-" : "").date("Y"));
}

//-- validip/getip Curtesy Of Manolete <manolete@myway.com> --//
//-- IP Validation --//
function validip ($ip)
{
    if (!empty($ip) && $ip == long2ip(ip2long($ip)))
    {
        //-- Reserved IANA IPv4 Addresses --//
        //-- http://www.iana.org/assignments/ipv4-address-space --//
        $reserved_ips = array(array('0.0.0.0',
                                    '0.255.255.255'),
                              array('10.0.0.0',
                                    '10.255.255.255'),
                              array('127.0.0.0',
                                    '127.255.255.255'),
                              array('169.254.0.0',
                                    '169.254.255.255'),
                              array('172.16.0.0',
                                    '172.31.255.255'),
                              array('192.0.2.0',
                                    '192.0.2.255'),
                              array('192.168.0.0',
                                    '192.168.255.255'),
                              array('255.255.255.0',
                                    '255.255.255.255'));

        foreach ($reserved_ips
                 AS
                 $r)
        {
            $min = ip2long($r[0]);
            $max = ip2long($r[1]);
            if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max))
            {
                return false;
            }
        }
        return true;
    }
    else
    {
        return false;
    }
}

//-- Patched Function To Detect Real IP Address If It's Valid --//
function getip ()
{
    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }

        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR')))
        {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }

        elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP')))
        {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $ip = getenv('REMOTE_ADDR');
        }
    }
    return $ip;
}

function db_connect ($autoclean = false)
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    {
        switch (mysql_errno())
        {
            case 1040:
            case 2002:

                if ($_SERVER['REQUEST_METHOD'] == "GET")
                {
                    die("<html><head><meta http-equiv='refresh' content='5 $_SERVER[REQUEST_URI]'></head><body><table width='100%' height='100%' border='0'><tr><td><h3 align='center'>The Server Load is Very High at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
                }
                else
                {
                    die("Too many users. Please press the Refresh button in your Browser to retry.");
                }
            default:

                die("[".mysql_errno()."] db_connect: mysql_connect: ".mysql_error());
        }
    }
    mysql_select_db($mysql_db)
        or die('db_connect: mysql_select_db: ' + mysql_error());

    // mysql_set_charset('utf8');

    userlogin();

    if ($autoclean)
    {
        register_shutdown_function("autoclean");
    }
}

function userlogin ()
{
    global $site_online;
    unset($GLOBALS["CURUSER"]);
    $dt = get_date_time();

    $ip  = getip();
    $nip = ip2long($ip);
    $res = sql_query("SELECT *
                        FROM bans
                        WHERE '$nip' >= first
                        AND '$nip' <= last") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
        header("HTTP/1.0 403 Forbidden");
        print("<html><body><h1>403 Forbidden</h1>Unauthorized IP Address.</body></html>\n");
        die;
    }

    if (!$site_online || empty($_COOKIE["uid"]) || empty($_COOKIE["pass"]))
    {
        return;
    }

    $id = 0 + $_COOKIE["uid"];

    if (!$id || strlen($_COOKIE["pass"]) != 32)
    {
        return;
    }

    $res = sql_query("SELECT u.*, ann_main.subject AS curr_ann_subject, ann_main.body AS curr_ann_body, ann_main.expires AS curr_ann_expires "."
                        FROM users AS u "."
                        LEFT JOIN announcement_main AS ann_main "."ON ann_main.main_id = u.curr_ann_id "."
                        WHERE u.id = $id AND u.enabled='yes' AND u.status = 'confirmed'") or sqlerr(__FILE__, __LINE__);


    $row = mysql_fetch_array($res);

    if (!$row)
    {
        return;
    }

    $sec = hash_pad($row["secret"]);

    if ($_COOKIE["pass"] !== $row["passhash"])
    {
        return;
    }

    //-- If curr_ann_id > 0 But curr_ann_body IS NULL, Then Force A Refresh --//
    if (($row['curr_ann_id'] > 0) AND ($row['curr_ann_body'] == NULL))
    {
        $row['curr_ann_id'] = 0;
        $row['curr_ann_last_check'] = '0';
    }

    // If Elapsed > 10 Minutes, Force An Announcement Refresh. --//
    if (($row['curr_ann_last_check'] != '0') AND ($row['curr_ann_last_check']) < (time($dt) - 600))
    {
        $row['curr_ann_last_check'] = '0';
    }

    if (($row['curr_ann_id'] == 0) AND ($row['curr_ann_last_check'] == '0'))
    { //-- Force An Immediate Check... --//
        $query = sprintf('SELECT m.*,p.process_id FROM announcement_main AS m '.
                         'LEFT JOIN announcement_process AS p ON m.main_id = p.main_id '.
                         'AND p.user_id = %s '.
                         'WHERE p.process_id IS NULL '.
                         'OR p.status = 0 '.
                         'ORDER BY m.main_id ASC '.
                         'LIMIT 1',

        sqlesc($row['id']));

        $result = sql_query($query);

        if (mysql_num_rows($result))
        { //-- Main Result Set Exists --//
            $ann_row = mysql_fetch_assoc($result);

            $query = $ann_row['sql_query'];

            //-- Ensure It Only Selects... --//
            if (!preg_match('/\\ASELECT.+?FROM.+?WHERE.+?\\z/', $query)) die();

            //-- The Following Line Modifies The Query To Only Return The Current User --//
            //-- Row If The Existing Query Matches Any Attributes. --//
            $query .= ' AND u.id = '.sqlesc($row['id']).' LIMIT 1';

            $result = sql_query($query);

            if (mysql_num_rows($result))
            { //-- Announcement Valid For Member --//
                $row['curr_ann_id'] = $ann_row['main_id'];

                //-- Create Three Row Elements To Hold Announcement Subject, Body And Expiry Date. --//
                $row['curr_ann_subject'] = $ann_row['subject'];
                $row['curr_ann_body']    = $ann_row['body'];
                $row['curr_ann_expires'] = $ann_row['expires'];

                //-- Create Additional Set For Main UPDATE Query. --//
                $add_set = ', curr_ann_id = '.sqlesc($ann_row['main_id']);
                $status  = 2;
            }
            else
            //-- Announcement Not Valid For Member... --//
            {
                $add_set = ', curr_ann_last_check = '.sqlesc($dt);
                $status  = 1;
            }

            //-- Create Or Set Status Of Process --//
            if ($ann_row['process_id'] === NULL)
            {
                //-- Insert Process Result Set Status = 1 (Ignore) --//
                $query = sprintf('INSERT INTO announcement_process (main_id, user_id, status)
                                    VALUES (%s, %s, %s)', sqlesc($ann_row['main_id']), sqlesc($row['id']), sqlesc($status));
            }
            else
            //-- Update Process Result Set Status = 2 (Read) --//
            {
                $query = sprintf('UPDATE announcement_process
                                    SET status = %s
                                    WHERE process_id = %s', sqlesc($status), sqlesc($ann_row['process_id']));
            }
            sql_query($query);
        }
        else
        //-- No Main Result Set. Set Last Update To Now... --//
        {
            $add_set = ', curr_ann_last_check = '.sqlesc($dt);
        }

        unset($result);
        unset($ann_row);
    }

    $time = time();

    if ($time - $row['last_access_numb'] < 300)
    {
        $onlinetime   = time() - $row['last_access_numb'];
        $userupdate[] = "onlinetime = onlinetime + ".sqlesc($onlinetime);
    }

    //-- Start Hide Staff IP Address by Fireknight --//
    if ($row['class'] >= UC_MODERATOR)
    {
       $ip = '127.0.0.1';
    }
    //-- End Hide Staff IP Address by Fireknight --//

    $add_set = (isset($add_set))?$add_set:'';

    $userupdate[] = "last_access_numb = ".sqlesc($time);
    $userupdate[] = "last_access = ".sqlesc($dt);
    $userupdate[] = "ip = ".sqlesc($ip).$add_set;

    sql_query("UPDATE users
                SET ".implode(", ", $userupdate)."
                WHERE id=".$row["id"]);

    $row['ip']          = $ip;

    //-- Start Temp Demote By Retro 1 of 3 --//
    if ($row['override_class'] < $row['class'])
    {
        $row['class'] = $row['override_class']; //-- Override Class And Save In Global Array Below. --//
    }
    //-- Finish Temp Demote By Retro 1 of 3 --//

    $GLOBALS["CURUSER"] = $row;
}

function autoclean()
{
    global $autoclean_interval;

    $now       = time();
    $docleanup = 0;

    $res = sql_query("SELECT value_u
                        FROM avps
                        WHERE arg = 'lastcleantime'");

    $row = mysql_fetch_array($res);

    if (!$row)
    {
        sql_query("INSERT INTO avps (arg, value_u)
                    VALUES ('lastcleantime',$now)");
        return;
    }

    $ts = $row[0];

    if ($ts + $autoclean_interval > $now)
    {
        return;
    }

    sql_query("UPDATE avps
                SET value_u = $now
                WHERE arg = 'lastcleantime'
                AND value_u = $ts");

    if (!mysql_affected_rows())
    {
        return;
    }

    docleanup();
}

function unesc ($x)
{
    if (get_magic_quotes_gpc())
    {
        return stripslashes($x);
    }

    return $x;
}

function mksize($bytes)
{
    $bytes = max(0, $bytes);
    //-- Kilobytes 1024^1 --//
    if ($bytes < 1024000)
    {
        return number_format($bytes / 1024, 2).' KB';
    }
    //-- Megabytes 1024^2 --//
    elseif ($bytes < 1048576000)
    {
        return number_format($bytes / 1048576, 2).' MB';
    }
    //-- Gigebytes 1024^3 --//
    elseif ($bytes < 1073741824000)
    {
        return number_format($bytes / 1073741824, 2).' GB';
    }
    //-- Terabytes 1024^4 --//
    elseif ($bytes < 1099511627776000)
    {
        return number_format($bytes / 1099511627776, 3).' TB';
    }
    //-- Petabytes 1024^5 --//
    elseif ($bytes < 1125899906842624000)
    {
        return number_format($bytes / 1125899906842624, 3).' PB';
    }
    //-- Exabytes 1024^6 --//
    elseif ($bytes < 1152921504606846976000)
    {
        return number_format($bytes / 1152921504606846976, 3).' EB';
    }
    //-- Zettabyres 1024^7 --//
    elseif ($bytes < 1180591620717411303424000)
    {
        return number_format($bytes / 1180591620717411303424, 3).' ZB';
    }
    //-- Yottabytes 1024^8 --//
    else
    {
        return number_format($bytes / 1208925819614629174706176, 3).' YB';
    }
}

function mksizeint ($bytes)
{
    $bytes = max(0, $bytes);

    if ($bytes < 1000)
    {
        return floor($bytes)." B";
    }

    elseif ($bytes < 1000 * 1024)
    {
        return floor($bytes / 1024)." kB";
    }

    elseif ($bytes < 1000 * 1048576)
    {
        return floor($bytes / 1048576)." MB";
    }

    elseif ($bytes < 1000 * 1073741824)
    {
        return floor($bytes / 1073741824)." GB";
    }

    else
    {
        return floor($bytes / 1099511627776)." TB";
    }
}

function deadtime ()
{
    global $announce_interval;

    return time() - floor($announce_interval * 1.3);
}

function mkprettytime ($s)
{
    if ($s < 0)
    {
        $s = 0;
    }

    $t = array();

    foreach (array("60:sec",
                   "60:min",
                   "24:hour",
                   "0:day")
            AS
            $x)
    {
        $y = explode(":", $x);

        if ($y[0] > 1)
        {
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        }
        else
        {
            $v = $s;
        }

        $t[$y[1]] = $v;
    }

    if ($t["day"])
    {
        return $t["day"]."d ".sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    }

    if ($t["hour"])
    {
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    }

    return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

function mkglobal ($vars)
{
    if (!is_array($vars))
    {
        $vars = explode(":", $vars);
    }

    foreach ($vars
             AS
             $v)
    {
        if (isset($_GET[$v]))
        {
            $GLOBALS[$v] = unesc($_GET[$v]);
        }

        elseif (isset($_POST[$v]))
        {
            $GLOBALS[$v] = unesc($_POST[$v]);
        }

        else
        {
            return 0;
        }
    }

    return 1;
}

function validfilename ($name)
{
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail ($email)
{
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

function sqlesc ($x)
{
    return "'".mysql_real_escape_string($x)."'";
}

function sqlwildcardesc ($x)
{
    return str_replace(array("%",
                             "_"), array("\\%",
                                         "\\_"), mysql_real_escape_string($x));
}

function urlparse ($m)
{
    $t = $m[0];

    if (preg_match(',^\w+://,', $t))
    {
        return "<a href='$t'>$t</a>";
    }

    return "<a href='http://$t'>$t</a>";
}

function parsedescr ($d, $html)
{
    if (!$html)
    {
        $d = htmlspecialchars($d);
        $d = str_replace("\n", "\n<br />", $d);
    }
    return $d;
}

function site_header ($title = "", $msgalert = true)
{
    global $CURUSER, $site_online, $site_name, $image_dir;

    if (!$site_online)
    {
        die("Site is Down for Maintenance. Please check back again later. Thank You.<br />");
    }

    if ($title == "")
    {
        $title = $site_name.(isset($_GET['ftsp']) ? " (".FTSP." $curversion)" : '');
    }
    else
    {
        $title = $site_name.(isset($_GET['ftsp']) ? " (".FTSP." $curversion)" : '')." :: ".htmlspecialchars($title);
    }

    if ($CURUSER)
    {
        $ss_a = @mysql_fetch_array(sql_query("SELECT uri
                                                FROM stylesheets
                                                WHERE id = ".$CURUSER["stylesheet"]));

        if ($ss_a)
        {
            $ss_uri = $ss_a["uri"];
        }
    }

    if (!$ss_uri)
    {
        ($r = sql_query("SELECT uri
                            FROM stylesheets
                            WHERE id = 1")) or die(mysql_error());

        ($a = mysql_fetch_array($r)) or die(mysql_error());

        $ss_uri = $a["uri"];
    }

    if ($msgalert && $CURUSER)
    {
        $res = sql_query("SELECT COUNT(id)
                           FROM messages
                           WHERE receiver = ".$CURUSER["id"]." && unread = 'yes'") or die("OopppsY!");

        $arr = mysql_fetch_row($res);

        $unread = $arr[0];
    }

    require_once(STYLES_DIR.$ss_uri.DIRECTORY_SEPARATOR.'theme_function.php');
    require_once(STYLES_DIR.$ss_uri.DIRECTORY_SEPARATOR.'site_header.php');

    //-- Start Temp Demote By Retro 2 of 3 --//
    if ($CURUSER['override_class'] != 255 && $CURUSER) //-- Second Condition Needed So That This Box Is Not Displayed For Non Members/logged Out Members --//
    {
        display_message_center("warn", "Warning", "You are Running under a Lower Class. Click <a href='$site_url/restoreclass.php'><strong>HERE</strong></a> to Restore.");
    }
    //-- Finish Temp Demote By Retro 2 of 3 --//

    if (isset($unread) && !empty($unread))
    {
        /*print("<table border='0' cellspacing='0' cellpadding='10' bgcolor='red'><tr><td style='padding: 10px; background: red'>\n");
        print("<a href='messages.php'><span style='color : #ffffff; font-weight:bold;'>You have $unread New Message".($unread > 1 ? "s" : "")."!</span></a>");
        print("</td></tr></table>\n");*/
        print("<div align='center'>");
        print("<div class='silver mail round small inset'>");
        print("<p><strong>You Have Mail</strong>");
        print("<br /><a href='messages.php'>&nbsp;&nbsp;&nbsp;&nbsp;<span class='emphasis'>You have $unread New Message".($unread > 1 ? "s" : "")."</span></a></p>");
        print("<div class='shadow-out'></div>");
        print("</div>");
        print("</div><br />");
    }

    //-- Start Announcement Message Display --//
    $res = sql_query("SELECT created
                        FROM announcement_main
                        WHERE 1 = 1");

    while ($arr = mysql_fetch_assoc($res))

    if ($arr['created'] >= $CURUSER['added'])
    {
        $ann_subject = trim($CURUSER['curr_ann_subject']);
        $ann_body    = trim($CURUSER['curr_ann_body']);
        $ann_expires = trim($CURUSER['curr_ann_expires']);

        if ((!empty($ann_subject)) AND (!empty($ann_body)))
        {
            print("<table border='1' width='600' cellspacing='0' cellpadding='5'>");
            print("<tr><td class='colhead' align='center'><strong><font color='#0000FF'>Announcement :- ");
            print("$ann_subject");
            print("</font></strong></td></tr>");
            print("<tr><td class='rowhead'>");
            print(format_comment($ann_body));
            print("<br /><hr />");
            print("Expires :-&nbsp;$ann_expires :-&nbsp;");
            print(" (".mkprettytime(strtotime($ann_expires) - gmtime())." to go)");
            print("<br /><hr />");
            print("Click <a href='clear_announcement.php'><strong>Here</strong></a> To Clear This Announcement.");
            print("</td></tr></table>");
            site_footer();
            die();
        }
    }
//-- Finish Announcement Message Display --//
}

function site_footer ()
{
    global $CURUSER;

    if ($CURUSER)
    {
        $ss_a = @mysql_fetch_array(sql_query("SELECT uri
                                                FROM stylesheets
                                                WHERE id=".$CURUSER["stylesheet"]));

        if ($ss_a)
        {
            $ss_uri = $ss_a["uri"];
        }
    }

    if (!$ss_uri)
    {
        ($r = sql_query("SELECT uri
                            FROM stylesheets
                            WHERE id=1")) or die(mysql_error());

        ($a = mysql_fetch_array($r)) or die(mysql_error());

        $ss_uri = $a["uri"];
    }

    require_once(STYLES_DIR.$ss_uri.DIRECTORY_SEPARATOR.'theme_function.php');
    require_once(STYLES_DIR.$ss_uri.DIRECTORY_SEPARATOR.'site_footer.php');
}

function mksecret ($len = 20)
{
    $ret = "";

    for ($i = 0;
         $i < $len;
         $i++)
    {
        $ret .= chr(mt_rand(0, 255));
    }

    return $ret;
}

function httperr ($code = 404)
{
    header("HTTP/1.0 404 Not found");

    print("<h1>Not Found!</h1>\n");
    print("<p>Sorry.</p>\n");

    exit();
}

function gmtime ()
{
    return strtotime(get_date_time());
}

function logincookie ($id, $passhash, $updatedb = 1, $expires = 0x7fffffff)
{
    setcookie("uid", $id, $expires, "/");
    setcookie("pass", $passhash, $expires, "/");

    if ($updatedb)
    {
        sql_query("UPDATE users
                      SET last_login = NOW()
                      WHERE id = $id");
    }
}

function logoutcookie ()
{
    setcookie("uid", "", 0x7fffffff, "/");
    setcookie("pass", "", 0x7fffffff, "/");
}

function logged_in ()
{
    global $CURUSER, $site_url;

    if (!$CURUSER)
    {
        header("Location: $site_url/login.php?returnto=".urlencode($_SERVER["REQUEST_URI"]));

        exit();
    }
}

function status_change($id)
{
    sql_query('UPDATE announcement_process
                SET status = 0
                WHERE user_id = '.sqlesc($id).'
                AND status = 1');
}

function hashit($var,$addtext="")
{
//-- I Would Suggest That You Change The Literal Text To Something That Only You Know (unique For Each Community Installing This Function). --//
    return md5("This Text ".$addtext.$var.$addtext." is added to muddy the water...");
}

//-- Returns The Current Time In GMT In MySQL Compatible Format. --//
function get_date_time ($timestamp = 0)
{
    if ($timestamp)
    {
        return date("Y-m-d H:i:s", $timestamp);
    }
    else
    {
        return gmdate("Y-m-d H:i:s");
    }
}

function sqlerr ($file = '', $line = '')
{
    error_message("error", "SQL Error", "".mysql_error().($file != '' && $line != '' ? "in $file, line $line" : "")."");
}

//-- SQL Query Count --//
$qtme['querytime'] = 0;

function sql_query ($querytme)
{
    global $queries, $qtme, $querytime, $query_stat;

    $qtme               = isset($qtme) && is_array($qtme) ? $qtme : array();
    $qtme['query_stat'] = isset($qtme['query_stat']) && is_array($qtme['query_stat']) ? $qtme['query_stat'] : array();

    $queries++;
    $query_start_time     = microtime(true); //-- Start Time --//
    $result               = mysql_query($querytme);
    $query_end_time       = microtime(true); //-- End Time --//
    $query_time           = ($query_end_time - $query_start_time);
    $querytime            = $querytime + $query_time;
    $qtme['querytime']    = (isset($qtme['querytime']) ? $qtme['querytime'] : 0) + $query_time;
    $query_time           = substr($query_time, 0, 8);
    $qtme['query_stat'][] = array('seconds' => $query_time,
                                  'query'   => $querytme);
    return $result;
}

if (file_exists(ROOT_DIR."install/index.php")) {
    echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
            <title>Warning</title>
        </head>
        <body>
            <div style='font-size:33px;color:white;background-color:red;text-align:center;'>Delete the Install Directory</div>
        </body>
    </html>");
    exit();
}

?>