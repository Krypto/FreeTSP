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

//FreeTSP Reputation System v1.0 - Please Leave Credits In Place.
//Reputation Mod - - Subzero Thanks to google.com! for the reputation image!
//File Completed 02 July 2010 At 19:42 Secound Offical Submit

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');

db_connect();
logged_in();

$id = 0 + $_GET['id'];

$res = sql_query("SELECT id
                    FROM users
                    WHERE id = ".sqlesc($id)) or die();

$row = mysql_fetch_assoc($res) or error_message("error", "Error", "User Was Not Found");

$userid = $row['id'];

if ($userid == $CURUSER['id'])
{
    error_message("warn", "Sorry", "You Cant Give Yourself Reputation Points!!");
}

site_header();
{

    //-- Lets Update The Database With New Reputation Points Do Not Alter If You Do Not Know What You Are Doing - Subzero --//
    sql_query("UPDATE users
                SET reputation = reputation+1
                WHERE id = '$id'") or sqlerr(__FILE__, __LINE__);

    begin_frame("Adding Reputation Point");

    display_message("success", "Success", "You Added A Reputation Point To This User!!");

    header("Refresh: 3; url='userdetails.php?id=$id'");

    end_frame();
}

site_footer();

?>