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

db_connect(false);
logged_in();

$userid = isset($_GET['id']) ? (int) $_GET['id'] : $CURUSER['id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (!is_valid_id($userid))
{
    error_message("error", "Error", "Invalid ID.");
}

if ($userid != $CURUSER["id"])
{
    error_message("warn", "Warning", "Access Denied.");
}

//-- Action: Add --//
if ($action == 'add')
{
    $targetid = 0 + $_GET['targetid'];
    $type     = $_GET['type'];

    if (!is_valid_id($targetid))
    {
        error_message("error", "Error", "Invalid ID.");
    }

    if ($type == 'friend')
    {
        $table_is = $frag = 'friends';
        $field_is = 'friendid';
    }
    elseif ($type == 'block')
    {
        $table_is = $frag = 'blocks';
        $field_is = 'blockid';
    }
    else
    {
        error_message("error", "Error", "Unknown Type.");
    }

    $r = sql_query("SELECT id
                    FROM $table_is
                    WHERE userid = $userid AND $field_is = $targetid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($r) == 1)
    {
        error_message("error", "Error", "User ID is already in your ".htmlentities($table_is)." list.");
    }

    sql_query("INSERT INTO $table_is
                VALUES (0,$userid, $targetid)") or sqlerr(__FILE__, __LINE__);

    header("Location: $site_url/friends.php?id=$userid#$frag");
    die;
}

//-- Action: Delete --//
if ($action == 'delete')
{
    $targetid = (int) $_GET['targetid'];
    $sure     = isset($_GET['sure']) ? htmlentities($_GET['sure']) : false;
    $type     = isset($_GET['type']) ? ($_GET['type'] == 'friend' ? 'friend' : 'block') : error_message("error", "Error", "LoL");

    if (!is_valid_id($targetid))
    {
        error_message("error", "Error", "Invalid ID.");
    }

    if (!$sure)
    {
        error_message("warn", "Delete $type", "Do you really want to Delete a $type? Click\n"."<a href='?id=$userid&amp;action=delete&amp;type=$type&amp;targetid=$targetid&amp;sure=1'>HERE</a> if you are sure?");
    }

    if ($type == 'friend')
    {
        sql_query("DELETE
                    FROM friends
                    WHERE userid = $userid
                    AND friendid = $targetid") or sqlerr(__FILE__, __LINE__);

        if (mysql_affected_rows() == 0)
        {
            error_message("error", "Error", "No Friend found with that ID");
        }

        $frag = "friends";
    }
    elseif ($type == 'block')
    {
        sql_query("DELETE
                    FROM blocks
                    WHERE userid = $userid
                    AND blockid = $targetid") or sqlerr(__FILE__, __LINE__);

        if (mysql_affected_rows() == 0)
        {
            error_message("error", "Error", "No Blocked Member found with that ID");
        }

        $frag = "blocks";
    }
    else
    {
        error_message("error", "Error", "Unknown Type.");
    }

    header("Location: $site_url/friends.php?id=$userid#$frag");
    die;
}

//-- Main Body --//
site_header("Personal Lists for ".$user['username']);

if ($user["donor"] == "yes")
{
    $donor = "<img src='{$image_dir}starbig.png' width='32' height='32' border='0' alt='Donor' title='Donor' style='margin-left: 4pt' />";
}

if ($user["warned"] == "yes")
{
    $warned = "<img src='{$image_dir}warnedbig.png' width='32' height='32' border='0' alt='Warned' title='Warned' style='margin-left: 4pt' />";
}

print("<table class='main' border='0' cellspacing='0' cellpadding='0'>
        <tr>
            <td class='embedded'><h1 style='margin:0px'> Personal Lists for $user[username]</h1>$donor$warned$country</td>
        </tr>
    </table>\n");

print("<table class='main' border='0' width='100%' cellspacing='0' cellpadding='0'>
        <tr>
            <td class='embedded'>");

print("<br />");
print("<h2 align='left'><a name='friends'>Friends List</a></h2>\n");

echo("<table border='1' width='100%' cellspacing='0' cellpadding='5'>
        <tr>
            <td>");

$i = 0;

$res = sql_query("SELECT f.friendid AS id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access
                    FROM friends AS f
                    LEFT JOIN users AS u ON f.friendid = u.id
                    WHERE userid = $userid
                    ORDER BY name") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) == 0)
{
    $friends = "<span style='font-style: italic;'>Your Friends List is Empty.</span>";
}
else
{
    while ($friend = mysql_fetch_assoc($res))
    {
        $title = $friend["title"];

        if (!$title)
        {
            $title = get_user_class_name($friend["class"]);
        }

        $body1 = "<a href='userdetails.php?id={$friend['id']}'><span style='font-weight:bold;'>".htmlentities($friend['name'], ENT_QUOTES)."</span></a>".get_user_icons($friend)." ($title)<br /><br />last seen on ".$friend['last_access']."<br />(".get_elapsed_time(sql_timestamp_to_unix_timestamp($friend[last_access]))." ago)";

        $body2 = "<br /><a href='friends.php?id=$userid&amp;action=delete&amp;type=friend&amp;targetid={$friend['id']}'>
                        <input type='submit' class='btn' value='Remove' /></a><br /><br />
                        <a href='sendmessage.php?receiver={$friend['id']}'>
                        <input type='submit' class='btn' value='Send PM' /></a>";

        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");

        if (!$avatar)
        {
            $avatar = "{$image_dir}default_avatar.gif";
        }

        if ($i % 2 == 0)
        {
            print("<table width='100%' style='padding: 0px'><tr><td class='bottom' align='center' width='50%' style='padding: 5px'>");
        }
        else
        {
            print("<td class='bottom' align='center' width='50%' style='padding: 5px'>");
        }
        print("<table class='main' style='width:100%; height:75'>");
        print("<tr valign='top'><td align='center' width='75' style='padding: 0px'>".($avatar ? "<div style='width:75;height:75;overflow: hidden'><img src='$avatar' width='125' height='125' border='0' alt='' title='' /></div>" : "")."</td><td>\n");

        print("<table class='main'>");
        print("<tr><td class='embedded' width='80%' style='padding: 5px'>$body1</td>\n");
        print("<td class='embedded' width='20%' style='padding: 5px'>$body2</td></tr>\n");
        print("</table>");

        echo("</td></tr></table>\n");

        if ($i % 2 == 1)
        {
            print("</td></tr></table>\n");
        }
        else
        {
            print("</td>\n");
        }
        $i++;
    }
}

if ($i % 2 == 1)
{
    print("<td class='bottom' width='50%'>&nbsp;</td></tr></table>\n");
}
print($friends);
print("</td></tr></table>\n");

$res = sql_query("SELECT b.blockid AS id, u.username AS name, u.donor, u.warned, u.username, u.enabled, u.last_access
                    FROM blocks AS b
                    LEFT JOIN users AS u ON b.blockid = u.id
                    WHERE userid = $userid ORDER BY name") or sqlerr(__FILE__, __LINE__);

$blocks = '';

if (mysql_num_rows($res) == 0)
{
    $blocks = "<span style='font-style: italic;'>Your Blocked Users List is Empty.</span>";
}
else
{
    while ($block = mysql_fetch_assoc($res))
    {
        $blocks .= "<div style='border: 1px solid black;padding:5px;'>";
        $blocks .= "<span class='btn' style='float:right;'><a href='friends.php?id=$userid&amp;action=delete&amp;type=block&amp;targetid=".$block['id']."'>Delete</a></span><br />";
        $blocks .= "<p><a href='userdetails.php?id={$block['id']}'>";
        $blocks .= "<span style='font-weight:bold;'>".htmlentities($block['name'], ENT_QUOTES)."</span></a>".get_user_icons($block)."</p></div><br />";
    }
}

print("<br /><br />");
print("<table class='main' border='0' width='100%' cellspacing='0' cellpadding='10'><tr><td class='embedded'>");
print("<h2 align='left'><a name='blocks'>Blocked Users List</a></h2></td></tr>");
print("<tr><td style='padding: 10px;background-color: #ECE9D8'>");
print("$blocks\n");
print("</td></tr></table>\n");
print("</td></tr></table>\n");
print("<p><a href='users.php'><span style='font-weight:bold;'>Find User/Browse User List</span></a></p>");

site_footer();

?>