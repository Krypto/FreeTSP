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
require_once(FUNC_DIR.'function_bbcode.php');

db_connect(true);
logged_in();

//QuickBan v1

if (get_user_class() < UC_SYSOP)
{
    error_message_center("error", "Error", "You Cant Be Here!!");
}

$id = 0 + $_GET['id'];

$res = sql_query("SELECT id
                    FROM users
                    WHERE id = ".sqlesc($id)) or die();

$row = mysql_fetch_assoc($res) or error_message_center("error", "Error", "User was not found");

$userid = $row['id'];

//--Sysop Can Not Ban Thereselfs! --//
if ($userid == $CURUSER['id'])
{
    error_message_center("warn", "Sorry", "You Cant Ban Yourself!!");
    site_header();
}

//-- Start Error Meassage If Already Banned --//
$res = sql_query("SELECT enabled
                    FROM users
                    WHERE id = $userid");

$row    = mysql_fetch_assoc($res);
$banned = $row['enabled'];

if ($banned == no)
{
    error_message_center("error", "Error", "This members account is already disabled!!");
}
//--Finish Error Meassage If Already Banned --//

//--Start Error Message If Member Is A Sysop --//
$res = sql_query("SELECT class
                    FROM users
                    WHERE id = $userid");

$row   = mysql_fetch_assoc($res);
$class = $row['class'];

if($class >= 6) //-- Change Class ID To Suit Your Coding --//
{
    error_message_center("error", "Error", "You cannot ban another Sysop!!");
}
//--Finish Error Message If Member Is A Sysop --//

//--Run The Code --//
site_header();

$res = sql_query("SELECT *
                    FROM users
                    WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

$row      = mysql_fetch_assoc($res);
$ip       = $row['ip'];
$username = $row['username'];
$longip   = ip2long($ip);
$comment  = 'Banned By Quick Ban System';
$added    = sqlesc(get_date_time());

sql_query("UPDATE users
            SET enabled = 'no'
            WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

sql_query("INSERT INTO bans (added, addedby, first, last, comment)
                  VALUES($added, $CURUSER[id], '$longip','$longip', '$comment')")or sqlerr(__FILE__, __LINE__);

begin_frame("Banning User");

display_message_center("success", "Success","<strong>$username</strong> Has Been Banned!!<br />
                                            <br />Return To <a href='userdetails.php?id=$id'><strong>$username</strong>'s Page</a>
                                            <br />Return to The <a href='index.php'>Main Page</a>");

end_frame();

site_footer();

?>