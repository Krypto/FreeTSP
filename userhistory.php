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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');
require_once(FUNC_DIR.'function_torrenttable.php');
require_once(FUNC_DIR.'function_bbcode.php');

db_connect(false);
logged_in();

$userid = (int) $_GET["id"];

if (!is_valid_id($userid))
{
    error_message("error", "Error", "Invalid ID");
}

if (get_user_class() < UC_POWER_USER || ($CURUSER["id"] != $userid && get_user_class() < UC_MODERATOR))
{
    error_message("warn", "Warning", "Permission Denied");
}

$page   = (isset($_GET['page']) ? $_GET["page"] : ''); //-- Not Used? --//
$action = (isset($_GET['action']) ? $_GET["action"] : '');

//-- Global Variables --//
$perpage = 25;

//-- Action: View Posts --//
if ($action == "viewposts")
{
    $select_is = "COUNT(DISTINCT p.id)";
    $from_is   = "posts AS p
                    LEFT JOIN topics AS t ON p.topicid = t.id
                    LEFT JOIN forums AS f ON t.forumid = f.id";

    $where_is = "p.userid = $userid AND f.minclassread <= ".$CURUSER['class'];
    $order_is = "p.id DESC";

    $query = "SELECT $select_is
                FROM $from_is
                WHERE $where_is";

    $res = sql_query($query) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_row($res) or error_message("error", "Error", "No Posts Found");

    $postcount = $arr[0];

    //-- Make Page Menu --//
    list($pagertop, $pagerbottom, $limit) = pager($perpage, $postcount, $_SERVER["PHP_SELF"]."?action=viewposts&id=$userid&");

    //-- Get User Data --//
    $res = sql_query("SELECT username, donor, warned, enabled
                        FROM users
                        WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 1)
    {
        $arr = mysql_fetch_assoc($res);

        $subject = "<a href='userdetails.php?id=$userid'><span style='font-weight:bold;'>$arr[username]</span></a>".get_user_icons($arr, true);
    }
    else
    {
        $subject = "unknown[$userid]";
    }

    //-- Get Posts --//
    $from_is = "posts AS p
                    LEFT JOIN topics AS t ON p.topicid = t.id
                    LEFT JOIN forums AS f ON t.forumid = f.id
                    LEFT JOIN readposts AS r ON p.topicid = r.topicid and p.userid = r.userid";

    $select_is = "f.id AS f_id, f.name, t.id AS t_id, t.subject, t.lastpost, r.lastpostread, p.*";

    $query = "SELECT $select_is
                FROM $from_is
                WHERE $where_is
                ORDER BY $order_is
                $limit";

    $res = sql_query($query) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 0)
    {
        error_message("error", "Error", "No Posts Found");
    }

    site_header("Posts History");

    echo("<h1>Post History for $subject</h1>\n");

    if ($postcount > $perpage)
    {
        echo    $pagertop;
    }

    //-- Print Table --//
    begin_frame();

    while ($arr = mysql_fetch_assoc($res))
    {
        $postid    = $arr["id"];
        $posterid  = $arr["userid"];
        $topicid   = $arr["t_id"];
        $topicname = $arr["subject"];
        $forumid   = $arr["f_id"];
        $forumname = $arr["name"];
        $dt        = (get_date_time(gmtime() - $posts_read_expiry));
        $newposts  = 0;

        if ($arr['added'] > $dt)
        {
            $newposts = ($arr["lastpostread"] < $arr["lastpost"]) && $CURUSER["id"] == $userid;
        }

        $added = $arr["added"]." GMT (".(get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"])))." ago)";

        echo("<br />
                <table border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td class='embedded'>
                            $added&nbsp;--&nbsp;
                            <span style='font-weight:bold;'>Forum:&nbsp;</span>
                            <a href='forums.php?action=viewforum&amp;forumid=$forumid'>$forumname</a>&nbsp;--&nbsp;
                            <span style='font-weight:bold;'>Topic:&nbsp;</span>
                            <a href='forums.php?action=viewtopic&amp;topicid=$topicid'>$topicname</a>&nbsp;--&nbsp;
                            <span style='font-weight:bold;'>Post:&nbsp;</span>
                            #<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=p$postid#$postid'>$postid</a>
                            ".($newposts ? " &nbsp;
                            <span style='font-weight:bold ;style='color : #ff0000;'>NEW!</span>" : "")."
                        </td>
                    </tr>
                </table>\n");

        begin_table(true);

        $body = format_comment($arr["body"]);

        if (is_valid_id($arr['editedby']))
        {
            $subres = sql_query("SELECT username
                                    FROM users
                                    WHERE id = $arr[editedby]");

            if (mysql_num_rows($subres) == 1)
            {
                $subrow = mysql_fetch_assoc($subres);
                $body .= "<p><span style='font-size: xx-small;'>Last edited by <a href='userdetails.php?id=$arr[editedby]'><span style='font-weight:bold;'>$subrow[username]</span></a> at $arr[editedat] GMT</span></p>\n";
            }
        }

        echo("<tr valign='top'><td class='comment'>$body</td></tr>\n");

        end_table();
    }

    end_frame();

    if ($postcount > $perpage)
    {
        echo $pagerbottom;
    }

    site_footer();

    die;
}

//-- Action: View Comments --//
if ($action == "viewcomments")
{
    $select_is = "COUNT(*)";

    //-- LEFT Due To Orphan Comments --//
    $from_is = "comments AS c
                LEFT JOIN torrents AS t ON c.torrent = t.id";

    $where_is = "c.user = $userid";
    $order_is = "c.id DESC";

    $query = "SELECT $select_is
                FROM $from_is
                WHERE $where_is
                ORDER BY $order_is";

    $res = sql_query($query) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_row($res) or error_message("error", "Error", "No Comments Found");

    $commentcount = $arr[0];

    //-- Make Page Menu --//
    list($pagertop, $pagerbottom, $limit) = pager($perpage, $commentcount, $_SERVER["PHP_SELF"]."?action=viewcomments&id=$userid&");

    //-- Get User Data --//
    $res = sql_query("SELECT username, donor, warned, enabled
                        FROM users
                        WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 1)
    {
        $arr     = mysql_fetch_assoc($res);
        $subject = "<a href='userdetails.php?id=$userid'><span style='font-weight:bold;'>$arr[username]</span></a>".get_user_icons($arr, true);
    }
    else
    {
        $subject = "unknown[$userid]";
    }

    //-- Get Comments --//
    $select_is = "t.name, c.torrent AS t_id, c.id, c.added, c.text";

    $query = "SELECT $select_is
                FROM $from_is
                WHERE $where_is
                ORDER BY $order_is
                $limit";

    $res = sql_query($query) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 0)
    {
        error_message("error", "Error", "No Comments Found");
    }

    site_header("Comments History");

    echo("<h1>Comments History for $subject</h1>\n");

    if ($commentcount > $perpage)
    {
        echo $pagertop;
    }

    //-- Print Table --//
    begin_frame();

    while ($arr = mysql_fetch_assoc($res))
    {
        $commentid = $arr["id"];
        $torrent   = $arr["name"];

        //-- Make Sure The Line Doesn't Wrap --//
        if (strlen($torrent) > 55)
        {
            $torrent = substr($torrent, 0, 52)."...";
        }

        $torrentid = $arr["t_id"];

        //-- Find The Page; This Code Should Probably Be In details.php Instead --//
        $subres = sql_query("SELECT COUNT(id)
                                FROM comments
                                WHERE torrent = $torrentid AND id < $commentid") or sqlerr(__FILE__, __LINE__);

        $subrow    = mysql_fetch_row($subres);
        $count     = $subrow[0];
        $comm_page = floor($count / 20);
        $page_url  = $comm_page ? "&page=$comm_page" : "";
        $added     = $arr["added"]." GMT (".(get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"])))." ago)";

        echo("<table border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td class='embedded'>
                        "."$added&nbsp;---&nbsp;
                        <span style='font-weight:bold;'>Torrent:&nbsp;</span>
                        ".($torrent ? ("<a href='details.php?id=$torrentid&amp;tocomm=1'>$torrent</a>") : " [Deleted] ")."&nbsp;---&nbsp;
                        <span style='font-weight:bold;'>Comment:&nbsp;</span>
                        #<a href='details.php?id=$torrentid&amp;tocomm=1$page_url'>$commentid</a>
                    </td>
                </tr>
            </table>\n");

        begin_table(true);

        $body = format_comment($arr["text"]);

        echo("<tr valign='top'><td class='comment'>$body</td></tr>\n");

        end_table();
    }

    end_frame();

    if ($commentcount > $perpage)
    {
        echo $pagerbottom;
    }

    site_footer();

    die;
}

//-- Handle Unknown Action --//
if ($action != "")
{
    error_message("error", "History Error", "Unknown Action.");
}

//-- Any Other Case --//
error_message("error", "History Error", "Invalid or No Query.");

?>