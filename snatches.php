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

db_connect();
logged_in();

$id = 0 + $_GET["id"];

if (!is_valid_id($id))
	error_message("error", "Error", "It appears that you have entered an Invalid ID.");

$res = sql_query("SELECT id, name
					FROM torrents
					WHERE id = $id") or sqlerr();

$arr = mysql_fetch_assoc($res);

if (!$arr)
	error_message("error", "Error", "It appears that there is NO torrent with that ID.");

$res = sql_query("SELECT COUNT(*)
					FROM snatched
					WHERE torrentid = $id") or sqlerr();

$row = mysql_fetch_row($res);

$count = $row[0];
$perpage = 100;

if (!$count)
	error_message("info", "No Snatches", "It appears that there are currently NO Snatches for the torrent <a href=details.php?id=$arr[id]>$arr[name]</a>.");

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "?id=$id&");

site_header("Snatches");

print("<h1>Snatches for torrent <a href='details.php?id=$arr[id]'>$arr[name]</a></h1>\n");
print("<h2>Currently $row[0] Snatch".($row[0] == 1 ? "" : "es")."</h2>\n");

if ($count > $perpage)

	print("$pagertop");
	print("<table border='0' cellspacing='0' cellpadding='5'>\n");
	print("<tr>\n");
	print("<td class='colhead' align='left'>Username</td>\n");
	print("<td class='colhead' align='center'>Connectable</td>\n");
	print("<td class='colhead' align='right'>Uploaded</td>\n");
	print("<td class='colhead' align='right'>Downloaded</td>\n");
	print("<td class='colhead' align='right'>Ratio</td>\n");
	print("<td class='colhead' align='right'>Completed</td>\n");
	print("<td class='colhead' align='center'>Seed Time</td>\n");
	print("<td class='colhead' align='center'>Leech Time</td>\n");
	print("<td class='colhead' align='center'>Last Action</td>\n");
	print("<td class='colhead' align='center'>Completed at</td>\n");
	print("<td class='colhead' align='center'>Client</td>\n");
	print("<td class='colhead' align='center'>Port</td>\n");
	print("<td class='colhead' align='center'>Seeding</td>\n");
	print("</tr>\n");

	$res = sql_query("SELECT s.*, size, username, warned, enabled, donor
						FROM snatched AS s
						INNER JOIN users ON s.userid = users.id
						INNER JOIN torrents ON s.torrentid = torrents.id
						WHERE torrentid = $id
						ORDER BY complete_date DESC
						$limit") or sqlerr();

	while ($arr = mysql_fetch_assoc($res))
	{
		$upspeed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));

		$downspeed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));

		$ratio = ($arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 3) : ($arr["uploaded"] > 0 ? "Inf." : "---"));

		$completed = sprintf("%.2f%%", 100 * (1 - ($arr["to_go"] / $arr["size"])));

		$res1 = sql_query("SELECT seeder
							FROM peers
							WHERE torrent=$_GET[id]
							AND userid=$arr[userid]");

		$arr1 = mysql_fetch_assoc($res1);

		print("<tr>\n");
		print("<td class='rowhead' align='left'><a href='userdetails.php?id=$arr[userid]'>$arr[username]</a>".get_user_icons($arr)."</td>\n");
		print("<td class='rowhead' align='center'>".($arr["connectable"] == "yes" ? "<span style='color : #00ff00;'>Yes</span>" : "<span style='color : #ff0000;'>No</span>")."</td>\n");
		print("<td class='rowhead' align='right'>".mksize($arr["uploaded"])."</td>\n");
		print("<td class='rowhead' align='right'>".mksize($arr["downloaded"])."</td>\n");
		print("<td class='rowhead' align='right'>$ratio</td>\n");
		print("<td class='rowhead' align='center'>$completed</td>\n");
		print("<td class='rowhead' align='right'>".mkprettytime($arr["seedtime"])."</td>\n");
		print("<td class='rowhead' align='right'>".mkprettytime($arr["leechtime"])."</td>\n");
		print("<td class='rowhead' align='center'>$arr[last_action]</td>\n");
		print("<td class='rowhead' align='center'>".($arr["complete_date"] == "0000-00-00 00:00:00" ? "Not completed" : $arr["complete_date"])."</td>\n");
		print("<td class='rowhead' align='center'>$arr[agent]</td>\n");
		print("<td class='rowhead' align='center'>$arr[port]</td>\n");
		print("<td class='rowhead' align='center'>" . ($arr1["seeder"] == "yes" ? "<span style='color : #00ff00; font-weight:bold;'>Yes</span>" : "<span style='color : #ff0000; font-weight:bold;'>No</span>") . "</td>\n");
		print("</tr>\n");
	}
	print("</table><br />\n");

if ($count > $perpage)

	print("$pagerbottom");

site_footer();

?>