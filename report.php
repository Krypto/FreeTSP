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

db_connect();
logged_in();

//-- Now All Reports Just Use A Single Var $id And A Type --//
    $id   = ($_GET["id"] ? $_GET["id"] : $_POST["id"]);
    $type = ($_GET["type"] ? $_GET["type"] : $_POST["type"]);

if (!is_valid_id($id))
{
    error_message_center("error", "Error", "<strong>BAD ID!</strong>");
}

    $typesallowed = array("User",
                          "Comment",
                          "Request_Comment",
                          "Offer_Comment",
                          "Request",
                          "Offer",
                          "Torrent",
                          "Hit_And_Run",
                          "Post");

if (!in_array($type, $typesallowed))
{
    error_message_center("error", "Error", "<strong>What you are trying to Report Does Not Exist!</strong>");
}

//-- Start Get Some Names And Limitations For The Array Types --//

if ($type == 'User')
{
    $query = "SELECT username, class
                FROM users
                WHERE id=$id";

    $sql   = sql_query($query);
    $row   = mysql_fetch_array($sql);
    $name  = $row['username'];

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff!</strong>");
    }
}

if ($type == 'Comment')
{
    $query  = "SELECT user, text
                FROM comments
                WHERE id=$id";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);
    $userid = $row['user'];
    $name   = $row['text'];

    $query  = "SELECT class
                FROM users
                WHERE id=$userid";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff Comments!</strong>");
    }
}

if($type == 'Request')
{
    $query  = "SELECT requested_by_user_id, request_name
                FROM requests
                WHERE id=$id";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);
    $userid = $row['requested_by_user_id'];
    $name   = $row['request_name'];

    $query  = "SELECT class
                FROM users
                WHERE id=$userid";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff Requests!</strong>");
    }
}

if($type == 'Request_Comment')
{
    $query  = "SELECT user, text
                FROM comments_request
                WHERE id=$id";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);
    $userid = $row['user'];
    $name   = $row['text'];

    $query  = "SELECT class
                FROM users
                WHERE id=$userid";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff Comments!</strong>");
    }
}

if($type == 'Offer')
{
    $query  = "SELECT offered_by_user_id, offer_name
                FROM offers
                WHERE id=$id";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);
    $userid = $row['offered_by_user_id'];
    $name   = $row['offer_name'];

    $query  = "SELECT class
                FROM users
                WHERE id=$userid";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff Offers!</strong>");
    }
}

if($type == 'Offer_Comment')
{
    $query  = "SELECT user, text
                FROM comments_offer
                WHERE id=$id";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);
    $userid = $row['user'];
    $name   = $row['text'];

    $query  = "SELECT class
                FROM users
                WHERE id=$userid";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff Comments!</strong>");
    }
}

if ($type == 'Torrent')
{
    $query  = "SELECT owner, name
                FROM torrents
                WHERE id=$id";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);
    $userid = $row['owner'];
    $name   = $row['name'];

    $query = "SELECT class
                FROM users
                WHERE id=$userid";

    $sql   = sql_query($query);
    $row   = mysql_fetch_array($sql);

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff Uploads!</strong>");
    }
}

if ($type == 'Post')
{
    $query  = "SELECT userid, body
                FROM posts
                WHERE id=$id";

    $sql    = sql_query($query);
    $row    = mysql_fetch_array($sql);
    $userid = $row['userid'];
    $name   = $row['body'];

    $query = "SELECT class
                FROM users
                WHERE id=$userid";

    $sql   = sql_query($query);
    $row   = mysql_fetch_array($sql);

    if ($row["class"] > 3)
    {
        error_message_center("error", "Error", "<strong>You Are Not Permited To Report Staff Posts!</strong>");
    }
}
//-- Finish Get Some Names And Limitations For The Array Types --//

//-- Still Need A Second Value Passed For Stuff Like Hit And Run Where You Need Two ID Numbers --//
if ((isset($_GET["id_2"])) || (isset($_POST["id_2"])))
{
    $id_2 = ($_GET["id_2"] ? $_GET["id_2"] : $_POST["id_2"]);

    if (!is_valid_id($id_2))
    {
        error_message_center("error", "Error", "<strong>Some thing is missing!</strong>");
    }

    $id_2b = "&amp;id_2=$id_2";
}

//-- -Start Updating The Report SQL --//
if ((isset($_GET["do_it"])) || (isset($_POST["do_it"])))
{
    $do_it = ($_GET["do_it"] ? $_GET["do_it"] : $_POST["do_it"]);

    if (!is_valid_id($do_it))
    {
        error_message_center("error", "Error", "<strong>Some thing is wrong!</strong>");
    }

    //-- Make Sure The Reason Is Filled Out And Is Set --//
    $reason = sqlesc($_POST["reason"]);

    if (!$reason)
    {
        error_message_center("error", "Error", "<strong>You MUST Enter a Reason for this Report! Use your Back Button and Fill in the Reason</strong>");
    }

    //-- Check If It Has Been Reported Already --//
    $res = sql_query("SELECT id
                        FROM reports
                        WHERE reported_by = $CURUSER[id]
                        AND reporting_what = $id
                        AND reporting_type = '$type'") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 0)
    {
        error_message_center("error", "Report Failure!", "You have already Reported <strong>".str_replace("_" , " ",$type)."</strong> with ID: <strong>$id</strong>!");
    }

    //-- OK It Has Not Been Reported Yet, So Carry On --//
    $dt = sqlesc(get_date_time());

    sql_query("INSERT INTO reports (reported_by, reporting_what, reporting_type, reason, added, 2nd_value)
                VALUES ($CURUSER[id], '$id', '$type', $reason, $dt, '$id_2')") or sqlerr(__FILE__, __LINE__);

    site_header("Confirm");

    echo("<table width='80%'>
          <tr>
            <td class='colhead'><h1>Success!</h1></td>
          </tr>
          <tr>
            <td class='rowhead' align='center'>Successfully Reported The&nbsp;--&nbsp;<strong>".str_replace("_" , " ",$type)."</strong>&nbsp;&nbsp;$name!<br /><br /><strong>Reason:</strong><br /><br /> $reason</td>
          </tr>
        </table>");

    site_footer();
    die();
}
//-- Finish Updating The Report SQL --//

//-- Starting Main Page For Reporting All... --//
site_header("Report");

    echo("<form method='post' action='report.php?type=$type$id_2b&amp;id=$id&amp;do_it=1'>
           <table width='80%'>");

    echo("<tr>
            <td class='colhead' colspan='2'><h1>Report:&nbsp;&nbsp;$name</h1></td>
        </tr>");

    echo("<tr>
            <td class='rowhead' align='center' colspan='2'>Are you sure you would like to Report this ".str_replace("_" , " ",$type)."<br />
                To the Staff for Violation of the <a class='altlink' href='rules.php' target='_blank'>Rules</a>?
            </td>
        </tr>");

    echo("<tr>
            <td class='rowhead' align='center' width='10%'><strong>Reason:</strong><br /><br />( <strong>Required</strong> )</td>
            <td class='rowhead' align='center'>
                <textarea name='reason' cols='80' rows='5'></textarea><br />
            </td>
        </tr>");

    echo("<tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value='Confirm Report' />
            </td>
        </tr>");

    echo("</table></form>");

site_footer();
die;

?>