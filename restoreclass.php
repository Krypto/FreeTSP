<?
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

/*
if ($CURUSER['override_class'] == 255)
{
    $userid   = $CURUSER['id'];
    $username = $CURUSER['username'];
    write_stafflog ("<strong><a href='userdetails.php?id=$userid'>$username.</a></strong> -- Attempted to access Restore Class");

    error_message_center("error","Error", "<strong>$username</strong> This Is A Hacking Attempt.<br />Your action has been reported");
}
*/
if ($CURUSER['override_class'] == 255)
{
    error_message_center("error", "Error", "Access Denied");
}

sql_query("UPDATE users 
            SET override_class = 255 
            WHERE id = ".$CURUSER['id']);

header("Location: $site_url/index.php");

die();

?>