<?php

/*
*-------------------------------------------------------------------------------*
*----------------    |  ____|        |__   __/ ____|  __ \        --------------*
*----------------    | |__ _ __ ___  ___| | | (___ | |__) |       --------------*
*----------------    |  __| '__/ _ \/ _ \ |  \___ \|  ___/        --------------*
*----------------    | |  | | |  __/  __/ |  ____) | |            --------------*
*----------------    |_|  |_|  \___|\___|_| |_____/|_|            --------------*
*-------------------------------------------------------------------------------*
*---------------------------    FreeTSP  v1.0   --------------------------------*
*-------------------   The Alternate BitTorrent Source   -----------------------*
*-------------------------------------------------------------------------------*
*-------------------------------------------------------------------------------*
*--   This program is free software; you can redistribute it and /or modify   --*
*--   it under the terms of the GNU General Public License as published by    --*
*--   the Free Software Foundation; either version 2 of the License, or       --*
*--   (at your option) any later version.                                     --*
*--                                                                           --*
*--   This program is distributed in the hope that it will be useful,         --*
*--   but WITHOUT ANY WARRANTY; without even the implied warranty of          --*
*--   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           --*
*--   GNU General Public License for more details.                            --*
*--                                                                           --*
*--   You should have received a copy of the GNU General Public License       --*
*--   along with this program; if not, write to the Free Software             --*
*-- Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA  --*
*--                                                                           --*
*-------------------------------------------------------------------------------*
*------------   Original Credits to tbSource, Bytemonsoon, TBDev   -------------*
*-------------------------------------------------------------------------------*
*-------------           Developed By: Krypto, Fireknight           ------------*
*-------------------------------------------------------------------------------*
*-----------------       First Release Date August 2010      -------------------*
*-----------                 http://www.freetsp.info                 -----------*
*------                    2010 FreeTSP Development Team                  ------*
*-------------------------------------------------------------------------------*
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_vfunctions.php');

db_connect(false);

if(!isset($CURUSER))
	die();

$url = '';

while (list($var,$val) = each($_GET))

$url .= "&$var=$val";

if(preg_match( "/([<>'\"]|&#039|&#33;|&#34|%27|%22|%3E|%3C|&#x27|&#x22|&#x3E|&#x3C|\.js)/i", $url ))
	header("Location: http://www.urbandictionary.com/define.php?term=twat");

$i = strpos($url, "&url=");

if ($i !== false)
	$url = substr($url, $i + 5);

if (substr($url, 0, 4) == "www.")
	$url = "http://" . $url;

if (strlen($url) < 10) die();
	echo("<html><head><meta http-equiv='refresh' content='3;url=$url'></head><body>\n");
	echo("<div style='width:100%;text-align:center;background: #E9D58F;border: 1px solid #CEAA49;margin: 5px 0 5px 0;padding: 0 5px 0 5px;font-weight: bold;'>Redirecting you to:<br />\n");
	echo(htmlentities($url)."</div></body></html>\n");

?>