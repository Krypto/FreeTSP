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
require_once(FUNC_DIR.'function_vfunctions.php');

$id = 0 + $_GET["id"];

if (!is_numeric($id) || $id < 1 || floor($id) != $id)
{
    die;
}

$type = $_GET["type"];

db_connect(false);
logged_in();

if ($type == 'in')
{
    //-- Make Sure Message Is In CURUSER's Inbox --//
    $res = sql_query("SELECT receiver, location
                        FROM messages
                        WHERE id=".sqlesc($id)) or die ("barf");

    $arr = mysql_fetch_assoc($res) or die ("Bad Message ID");

    if ($arr["receiver"] != $CURUSER["id"])
    {
        die ("I wouldn't do that if i were you...");
    }

    if ($arr["location"] == 'in')
    {
        sql_query("DELETE
                    FROM messages
                    WHERE id=".sqlesc($id)) or die ('Delete Failed (Error Code 1).. this should never happen, contact an Admin.');
    }
    else
    {
        if ($arr["location"] == 'both')
        {
            sql_query("UPDATE messages
                        SET location = 'out'
                        WHERE id=".sqlesc($id)) or die ('Delete Failed (Error Code 2).. this should never happen, contact an Admin.');
        }
        else
        {
            die('The Message is NOT in your Inbox.');
        }
    }
}
elseif ($type == 'out')
{
    //-- Make Sure Message Is In CURUSER's Sentbox --//
    $res = sql_query("SELECT sender, location
                        FROM messages
                        WHERE id=".sqlesc($id)) or die ("barf");

    $arr = mysql_fetch_assoc($res) or die ("Bad Message ID");

    if ($arr["sender"] != $CURUSER["id"])
    {
        die("I wouldn't do that if i were you...");
    }

    if ($arr["location"] == 'out')
    {
        sql_query("DELETE
                    FROM messages
                    WHERE id=".sqlesc($id)) or die ('Delete Failed (Error Code 3).. this should never happen, contact an Admin.');
    }
    else
    {
        if ($arr["location"] == 'both')
        {
            sql_query("UPDATE messages
                        SET location = 'in'
                        WHERE id=".sqlesc($id)) or die ('Delete Failed (Error Code 4).. this should never happen, contact an admin.');
        }
        else
        {
            die('The Message is NOT in your Sentbox.');
        }
    }
}
else
{
    die('Unknown PM Type.');
}

header("Location: $site_url/messages.php".($type == 'out' ? "?out=1" : ""));

?>