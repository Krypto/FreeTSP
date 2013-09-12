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

db_connect();
logged_in();

if (!isset($CURUSER))
{
    error_message("error", "Rating Failed!", "Must Be Logged In To Vote");
}

if (!mkglobal("rating:id"))
{
    error_message("error", "Rating Failed!", "Missing Form Data");
}

$id = 0 + $id;

if (!$id)
{
    error_message("error", "Rating Failed!", "Invalid ID");
}

$rating = 0 + $rating;

if ($rating <= 0 || $rating > 5)
{
    error_message("error", "Rating Failed!", "Invalid Rating");
}

$res = sql_query("SELECT owner
                    FROM torrents
                    WHERE id = ".htmlspecialchars($id)."");

$row = mysql_fetch_assoc($res);

if (!$row)
{
    error_message("error", "Rating Failed!", "No Such Torrent");
}

$res = sql_query("INSERT INTO ratings (torrent, user, rating, added)
                    VALUES ($id, ".htmlspecialchars($CURUSER["id"]).", $rating, NOW())");

if (!$res)
{
    if (mysql_errno() == 1062)
    {
        error_message("error", "Rating Failed!", "You Have Already Rated This Torrent.");
    }
    else
    {
        error_message("error", "Rating Failed!", "mysql_error()");
    }
}

sql_query("UPDATE torrents
            SET numratings = numratings + 1, ratingsum = ratingsum + $rating
            WHERE id = ".htmlspecialchars($id)."");

header("Refresh: 0; url=details.php?id=$id&rated=1");

?>