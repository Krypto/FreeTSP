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

$query = sprintf('UPDATE users
                    SET curr_ann_id = 0, curr_ann_last_check = \'0000-00-00 00:00:00\'
                    WHERE id = %s
                    AND curr_ann_id != 0', sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

sql_query($query);

header("Location: $site_url/index.php");

?>