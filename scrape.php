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

$r = "d".benc_str("files")."d";

$fields = "info_hash, times_completed, seeders, leechers";

if (!isset($_GET["info_hash"]))
{
    $query = "SELECT $fields
                FROM torrents
                ORDER BY info_hash";
}
else
{
    $query = "SELECT $fields
                FROM torrents
                WHERE ".hash_where("info_hash", unesc($_GET["info_hash"]));
}

$res = sql_query($query);

while ($row = mysql_fetch_assoc($res))
{
    $r .= "20:".hash_pad($row["info_hash"])."d".benc_str("complete")."i".$row["seeders"]."e".benc_str("downloaded")."i".$row["times_completed"]."e".benc_str("incomplete")."i".$row["leechers"]."e"."e";
}

$r .= "ee";

header("Content-Type: text/plain");
print($r);

?>