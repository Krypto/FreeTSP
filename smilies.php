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
require_once(FUNC_DIR.'function_bbcode.php');
require_once(FUNC_DIR.'function_user.php');

db_connect(false);
logged_in();

site_header();

global $smilies, $site_url, $image_dir;

begin_frame("Smilies", true);
begin_table(false, 5);

print("<tr><td class='colhead'>Type...</td><td class='colhead'>To make a...</td></tr>\n");

while (list($code, $url) = each($smilies))

{
    print("<tr><td>$code</td><td><img src='{$image_dir}smilies/{$url}' width='16' height='16' border='0' alt='' title='' /></td></tr>\n");
}

end_table();
end_frame();

echo('<br />');

site_footer();

?>