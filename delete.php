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

function deletetorrent ($id)
{
    global $torrent_dir;

    sql_query("DELETE
                FROM torrents
                WHERE id = $id");

    foreach (explode(".", "peers.files.comments.ratings")
             AS
             $x)

    {
        sql_query("DELETE
                    FROM $x
                    WHERE torrent = $id");
    }

    unlink("$torrent_dir/$id.torrent");
}

if (!mkglobal("id"))
{
    error_message("error", "Delete Failed", "Missing Form Data");
}

$id = 0 + $id;

if (!is_valid_id($id))
{
    die();
}

db_connect();
logged_in();

$res = sql_query("SELECT name,owner,seeders
                    FROM torrents
                    WHERE id = $id");

$row = mysql_fetch_assoc($res);

if (!$row)
{
    die();
}

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
{
    error_message("error", "Delete Failed", "You're NOT the Owner! How did that happen?");
}

$rt = 0 + $_POST["reasontype"];

if (!is_int($rt) || $rt < 1 || $rt > 5)
{
    error_message("error", "Delete Failed", "Invalid Reason $rt.");
}

$r      = $_POST["r"];
$reason = $_POST["reason"];

if ($rt == 1)
{
    $reasonstr = "Dead: 0 Seeders, 0 Leechers = 0 Peers Total";
}
elseif ($rt == 2)
{
    $reasonstr = "Dupe".($reason[0] ? (": ".trim($reason[0])) : "!");
}
elseif ($rt == 3)
{
    $reasonstr = "Nuked".($reason[1] ? (": ".trim($reason[1])) : "!");
}
elseif ($rt == 4)
{
    if (!$reason[2])
    {
        error_message("error", "Delete Failed", "Please describe the Violated Rule.");
    }

    $reasonstr = $site_name." Rules Broken: ".trim($reason[2]);
}
else
{
    if (!$reason[3])
    {
        error_message("error", "Delete Failed", "Please enter a Reason for Deleting this torrent.");
    }

    $reasonstr = trim($reason[3]);
}

deletetorrent($id);

write_log("Torrent $id ($row[name]) was Deleted by $CURUSER[username] ($reasonstr)\n");

site_header("Torrent Deleted!");

if (isset($_POST["returnto"]))
{
    echo $ret = display_message("info", " ", "<a href='".htmlspecialchars("{$site_url}/{$_POST['returnto']}")."'>Return</a>");
}
else
{
    echo $ret = display_message("info", "Torrent Deleted!", "<a href='index.php'>Back to Index</a>");
}

?>

<p><?php $ret ?></p>

<?php

site_footer();

?>