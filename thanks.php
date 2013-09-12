<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** https://github.com/Krypto/FreeTSP
** http://www.freetsp.info
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

$uid = $CURUSER["id"];
$tid = 0 + $_POST["torrentid"];

if (isset($uid) && $tid > 0 )
{
    $count = mysql_fetch_row(sql_query("SELECT COUNT(id)
                                        FROM thanks
                                        WHERE userid={$uid}
                                        AND torrentid={$tid}"));

    if ($count[0] == 0)

    $res = sql_query("INSERT INTO thanks (torrentid, userid)
                        VALUES ($tid, $uid)");

    header("Location: $site_url/details.php?id=$tid&thanks=1");
}
else
    header("Location: $site_url/details.php?id=$tid");

?>