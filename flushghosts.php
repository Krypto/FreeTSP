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

$id = 0 + $_GET['id'];

$res = sql_query("SELECT id
                    FROM users
                    WHERE id = ".sqlesc($id)) or die();

$row    = mysql_fetch_assoc($res) or error_message("error", "Error", "User was not found");
$userid = $row['id'];

if ($userid == $CURUSER['id'])
{
    $res  = sql_query("SELECT
                        COUNT(id)
                        FROM peers
                        WHERE userid = $userid") or sqlerr();

    $ghost       = mysql_fetch_row($res);
    $ghostnumber = $ghost[0];

    sql_query("DELETE
                FROM peers
                WHERE userid = $userid");

    site_header();

    begin_frame("Flushing Your Ghost Torrents");

    display_message("success", "Success", "You have Flushed $ghostnumber Ghost torrents! Please Update ALL Torrents in your Client.");

    header("Refresh: 0; url='userdetails.php?id=$id'");

    end_frame();

    site_footer();
}
elseif (get_user_class() >= UC_MODERATOR)
{
    $res  = sql_query("SELECT COUNT(id)
                        FROM peers
                        WHERE userid = $userid") or sqlerr();

    $ghost       = mysql_fetch_row($res);
    $ghostnumber = $ghost[0];

    sql_query("DELETE
                FROM peers
                WHERE userid = $userid");

    site_header();

    begin_frame("Flushing Your Ghost Torrents");

    display_message("success", "Success", "You have Flushed $ghostnumber Ghost torrents! Please Update ALL Torrents in your Client.");

    header("Refresh: 0; url='userdetails.php?id=$id'");

    end_frame();
}
else
{
    error_message("warn", "Sorry", "You Cant Flush Other Peoples Torrents!!");

    header("Refresh: 0; url='userdetails.php?id=$id'");

    end_frame();
}

    site_footer();

?>