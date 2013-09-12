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

if (!preg_match(':^/(\d{1,10})/([\w]{32})/(.+)$:', $_SERVER["PATH_INFO"], $matches))
{
    httperr();
}

$id    = 0 + $matches[1];
$md5   = $matches[2];
$email = urldecode($matches[3]);

if (!$id)
{
    httperr();
}

db_connect();

$res = sql_query("SELECT editsecret
                    FROM users
                    WHERE id = $id");

$row = mysql_fetch_assoc($res);

if (!$row)
{
    httperr();
}

$sec = hash_pad($row["editsecret"]);

if (preg_match('/^ *$/s', $sec))
{
    httperr();
}

if ($md5 != md5($sec .$email. $sec))
{
    httperr();
}

sql_query("UPDATE users
            SET editsecret = '', email = ".sqlesc($email)."
            WHERE id = $id
            AND editsecret = ".sqlesc($row["editsecret"])) or sqlerr(__FILE__, __LINE__);

if (!mysql_affected_rows())
{
    httperr();
}

header("Refresh: 0; url=$site_url/usercp.php?emailch=1");

?>