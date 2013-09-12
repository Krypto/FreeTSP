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

$id  = 0 + $_GET["id"];
$md5 = $_GET["secret"];

if (!$id)
{
    httperr();
}

db_connect();

$res = sql_query("SELECT passhash, editsecret, status
                    FROM users
                    WHERE id = $id") or sqlerr(__FILE__, __LINE__);

$row = mysql_fetch_assoc($res);

if (!$row)
{
    httperr();
}

if ($row["status"] != "pending")
{
    header("Refresh: 0; url=ok.php?type=confirmed");
    exit();
}

$sec = hash_pad($row["editsecret"]);

if ($md5 != md5($sec))
{
    httperr();
}

sql_query("UPDATE users
            SET status = 'confirmed', editsecret = ''
            WHERE id = $id
            AND status ='pending'") or sqlerr(__FILE__, __LINE__);

if (!mysql_affected_rows())
{
    httperr();
}

logincookie($id, $row["passhash"]);

header("Refresh: 0; url=ok.php?type=confirm");

?>