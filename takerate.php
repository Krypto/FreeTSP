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
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_torrenttable.php');

db_connect();
logged_in();

if (!isset($CURUSER))
	error_message("error", "Rating Failed!", "Must be logged in to Vote");

if (!mkglobal("rating:id"))
	error_message("error", "Rating Failed!", "Missing Form Data");

$id = 0 + $id;

if (!$id)
	error_message("error", "Rating Failed!", "Invalid ID");

$rating = 0 + $rating;

if ($rating <= 0 || $rating > 5)
	error_message("error", "Rating Failed!", "Invalid Rating");

$res = sql_query("SELECT owner
					FROM torrents
					WHERE id = ".htmlspecialchars($id)."");

$row = mysql_fetch_assoc($res);

if (!$row)
	error_message("error", "Rating Failed!", "no such torrent");

$res = sql_query("INSERT INTO ratings (torrent, user, rating, added)
					VALUES ($id, " . htmlspecialchars($CURUSER["id"]) . ", $rating, NOW())");

if (!$res) {
	if (mysql_errno() == 1062)
		error_message("error", "Rating Failed!", "You have already Rated this torrent.");
	else
		error_message("error", "Rating Failed!", "mysql_error()");
}

sql_query("UPDATE torrents
			SET numratings = numratings + 1, ratingsum = ratingsum + $rating
			WHERE id = ".htmlspecialchars($id)."");

header("Refresh: 0; url=details.php?id=$id&rated=1");

?>