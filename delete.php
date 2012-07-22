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

function deletetorrent($id)
{
	global $torrent_dir;

	sql_query("DELETE
				FROM torrents
				WHERE id = $id");

	foreach(explode(".","peers.files.comments.ratings") AS $x)

	sql_query("DELETE
				FROM $x
				WHERE torrent = $id");

	unlink("$torrent_dir/$id.torrent");
}

if (!mkglobal("id"))
	error_message("error", "Delete Failed", "Missing Form Data");

$id = 0 + $id;

if (!is_valid_id($id))
	die();

db_connect();
logged_in();

$res = sql_query("SELECT name,owner,seeders
					FROM torrents
					WHERE id = $id");

$row = mysql_fetch_assoc($res);

if (!$row)
	die();

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	error_message("error", "Delete Failed", "You're NOT the Owner! How did that happen?");

$rt = 0 + $_POST["reasontype"];

if (!is_int($rt) || $rt < 1 || $rt > 5)
	error_message("error", "Delete Failed", "Invalid Reason $rt.");

$r = $_POST["r"];
$reason = $_POST["reason"];

if ($rt == 1)
	$reasonstr = "Dead: 0 Seeders, 0 Leechers = 0 Peers Total";
elseif ($rt == 2)
	$reasonstr = "Dupe" . ($reason[0] ? (": " . trim($reason[0])) : "!");
elseif ($rt == 3)
	$reasonstr = "Nuked" . ($reason[1] ? (": " . trim($reason[1])) : "!");
elseif ($rt == 4)
{
	if (!$reason[2])
		error_message("error", "Delete Failed", "Please describe the Violated Rule.");

	$reasonstr = $site_name." Rules Broken: " . trim($reason[2]);
}
else
{
	if (!$reason[3])
		error_message("error", "Delete Failed", "Please enter a reason for Deleting this torrent.");

	$reasonstr = trim($reason[3]);
}

deletetorrent($id);

write_log("Torrent $id ($row[name]) was deleted by $CURUSER[username] ($reasonstr)\n");

site_header("Torrent Deleted!");

if (isset($_POST["returnto"]))
	echo $ret = display_message("info", " ", "<a href='" . htmlspecialchars("{$site_url}/{$_POST['returnto']}") . "'>Return</a>");
else
	echo $ret = display_message("info", "Torrent Deleted!", "<a href='index.php'>Back to index</a>");

?>

<p><?php $ret ?></p>

<?php

site_footer();

?>