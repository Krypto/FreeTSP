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
require_once(FUNC_DIR.'function_bbcode.php');

db_connect(false);
logged_in();

site_header("".htmlspecialchars($CURUSER["username"])."'s torrent's ");

$where = "WHERE owner = ".$CURUSER["id"]." and banned != 'yes'";

$res = sql_query("SELECT COUNT(id)
                    FROM torrents $where");

$row   = mysql_fetch_array($res, MYSQL_NUM);
$count = $row[0];

if (!$count)
{
    display_message("info", "No Torrents", "You haven't Uploaded any torrents yet, so there's nothing to show you.");
}
else
{
    list($pagertop, $pagerbottom, $limit) = pager(20, $count, "mytorrents.php?");

    $res = sql_query("SELECT torrents.type, torrents.comments, torrents.leechers, torrents.seeders, IF(torrents.numratings < $min_votes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.id, categories.name AS cat_name, categories.image AS cat_pic, torrents.name, save_as, numfiles, added, size, views, visible, hits, times_completed, category
                        FROM torrents
                        LEFT JOIN categories ON torrents.category = categories.id $where
                        ORDER BY id DESC
                        $limit");

    print($pagertop);

    torrenttable($res, "mytorrents");

    print($pagerbottom);
}

site_footer();

?>