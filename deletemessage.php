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

$id = 0+$_GET["id"];

if (!is_numeric($id) || $id < 1 || floor($id) != $id)
	die;

$type = $_GET["type"];

db_connect(false);
logged_in();

if ($type == 'in')
{
	// make sure message is in CURUSER's Inbox
	$res = sql_query("SELECT receiver, location
						FROM messages
						WHERE id=" . sqlesc($id)) or die("barf");

	$arr = mysql_fetch_assoc($res) or die("Bad Message ID");

	if ($arr["receiver"] != $CURUSER["id"])
		die("I wouldn't do that if i were you...");

	if ($arr["location"] == 'in')
		sql_query("DELETE
					FROM messages
					WHERE id=" . sqlesc($id)) or die('Delete Failed (Error Code 1).. this should never happen, contact an Admin.');

	else if ($arr["location"] == 'both')
			sql_query("UPDATE messages
						SET location = 'out'
						WHERE id=" . sqlesc($id)) or die('Delete Failed (Error Code 2).. this should never happen, contact an Admin.');
	else
		die('The Message is NOT in your Inbox.');
}
	elseif ($type == 'out')
{
	// make sure message is in CURUSER's Sentbox
	$res = sql_query("SELECT sender, location
						FROM messages
						WHERE id=" . sqlesc($id)) or die("barf");

	$arr = mysql_fetch_assoc($res) or die("Bad Message ID");

	if ($arr["sender"] != $CURUSER["id"])
		die("I wouldn't do that if i were you...");

	if ($arr["location"] == 'out')
		sql_query("DELETE
					FROM messages
					WHERE id=" . sqlesc($id)) or die('Delete Failed (Error Code 3).. this should never happen, contact an Admin.');

	else if ($arr["location"] == 'both')
			sql_query("UPDATE messages
						SET location = 'in'
						WHERE id=" . sqlesc($id)) or die('Delete Failed (Error Code 4).. this should never happen, contact an admin.');
	else
		die('The Message is NOT in your Sentbox.');
}
else
	die('Unknown PM Type.');

header("Location: $site_url/messages.php".($type == 'out'?"?out=1":""));

?>