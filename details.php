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
require_once(INCL_DIR.'function_bbcode.php');
require_once(INCL_DIR.'function_commenttable.php');

function ratingpic($num)
{
	global $image_dir;

	$r = round($num * 2) / 2;

	if ($r < 1 || $r > 5)
		return;

	return "<img src='{$image_dir}ratings/{$r}.png' height='26' border='0' alt='rating: $num / 5' title='rating: $num /5' />";
}

function getagent($httpagent='', $peer_id="")
	{
		return ($httpagent ? $httpagent : ($peer_id ? $peer_id : Unknown));
	}

function dltable($name, $arr, $torrent)
{
	global $CURUSER, $moderator, $revived;

	$s = "<span style='font-weight:bold;'>" . count($arr) . " $name</span>\n";

	if (!count($arr))
		return $s;

	$s .= "\n";
	$s .= "<table width='100%' class='main' border='1' cellspacing='0' cellpadding='5'>\n";
	$s .= "<tr><td class='colhead'>User/IP</td>" .
			"<td class='colhead' align='center'>Connectable</td>".
			"<td class='colhead' align='right'>Uploaded</td>".
			"<td class='colhead' align='center'>Rate</td>".
			"<td class='colhead' align='right'>Downloaded</td>" .
			"<td class='colhead' align='center'>Rate</td>" .
			"<td class='colhead' align='center'>Ratio</td>" .
			"<td class='colhead' align='right'>Complete</td>" .
			"<td class='colhead' align='right'>Connected</td>" .
			"<td class='colhead' align='center'>Idle</td>" .
			"<td class='colhead' align='left'>Client</td></tr>\n";

	$now		= time();
	$moderator	= (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
	$mod		= get_user_class() >= UC_MODERATOR;

	foreach ($arr AS $e)
	{
		// user/ip/port - check if anyone has this ip
		($unr = sql_query("SELECT username, privacy
							FROM users
							WHERE id=$e[userid]
							ORDER BY last_access DESC
							LIMIT 1")) or die;

		$una = mysql_fetch_assoc($unr);

		if ($una["privacy"] == "strong") continue;

		$s .= "<tr>\n";

		if ($una["username"])
			$s .= "<td class='rowhead'><a href='userdetails.php?id=$e[userid]'><span style='font-weight:bold;'>$una[username]</span></a></td>\n";
		else
			$s .= "<td class='rowhead'>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";

		$secs		= max(1, ($now - $e["st"]) - ($now - $e["la"]));
		$revived	= $e["revived"] == "yes";

		$s .= "<td class='rowhead' align='center'>" . ($e[connectable] == "yes" ? "<span style='color : green;'>Yes</span>" : "<span style='color : #ff0000;'>No</span>") . "</td>\n";

		$s .= "<td class='rowhead' align='right'>" . mksize($e["uploaded"]) . "</td>\n";

		$s .= "<td class='rowhead' align='center'><span style=\"white-space: nowrap;\">" . mksize(($e["uploaded"] - $e["uploadoffset"]) / $secs) . "/s</span></td>\n";

		$s .= "<td class='rowhead' align='right'>" . mksize($e["downloaded"]) . "</td>\n";

		if ($e["seeder"] == "no")
			$s .= "<td class='rowhead' align='center'><span style=\"white-space: nowrap;\">" . mksize(($e["downloaded"] - $e["downloadoffset"]) / $secs) . "/s</span></td>\n";
		else
			$s .= "<td class='rowhead' align='center'><span style=\"white-space: nowrap;\">" . mksize(($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e[st])) .	"/s</span></td>\n";

		if ($e["downloaded"])
		{
			$ratio	= floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
			$s		.= "<td class='rowhead' align='right'><span style='color : " . get_ratio_color($ratio) . "'>" . number_format($ratio, 3) . "</span></td>\n";
		}
		else
		if ($e["uploaded"])
			$s .= "<td class='rowhead' align='center'>Inf.</td>\n";
		else
			$s .= "<td class='rowhead' align='center'>---</td>\n";
			$s .= "<td class='rowhead' align='right'>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
			$s .= "<td class='rowhead' align='right'>" . mkprettytime($now - $e["st"]) . "</td>\n";
			$s .= "<td class='rowhead' align='center'>" . mkprettytime($now - $e["la"]) . "</td>\n";
			$s .= "<td class='rowhead' align='left'>" . htmlspecialchars(getagent($e["agent"])) . "</td>\n";
			$s .= "</tr>\n";
	}
	$s .= "</table>\n";
	return $s;
}

db_connect(false);

logged_in();

$id = 0 + $_GET["id"];
$added = sqlesc(get_date_time());
if (!isset($id) || !$id)
	die();

$res = sql_query("SELECT torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, LENGTH(torrents.nfo) AS nfosz, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < $min_votes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.comments, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, categories.name AS cat_name, users.username
				FROM torrents
				LEFT JOIN categories ON torrents.category = categories.id
				LEFT JOIN users ON torrents.owner = users.id
				WHERE torrents.id = $id") or sqlerr();

$row = mysql_fetch_assoc($res);

$owned = $moderator = 0;

if (get_user_class() >= UC_MODERATOR)
	$owned = $moderator = 1;

elseif ($CURUSER["id"] == $row["owner"])
	$owned = 1;

if (!$row || ($row["banned"] == "yes" && !$moderator))
	error_message("error", "Error", "No torrent with ID.");
else
{
	if ($_GET["hit"])
	{
		sql_query("UPDATE torrents
					SET views = views + 1
					WHERE id = $id");

		if ($_GET["tocomm"])
			header("Location: $site_url/details.php?id=$id&page=0#startcomments");

		elseif ($_GET["filelist"])
			header("Location: $site_url/details.php?id=$id&filelist=1#filelist");

		elseif ($_GET["toseeders"])
			header("Location: $site_url/details.php?id=$id&dllist=1#seeders");

		elseif ($_GET["todlers"])
			header("Location: $site_url/details.php?id=$id&dllist=1#leechers");

		else
			header("Location: $site_url/details.php?id=$id");
		exit();
	}

	if (!isset($_GET["page"]))
	{
		site_header("Details for torrent '" . $row["name"] . "'");

		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;

		$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if ($_GET["uploaded"])
		{
			echo display_message("success", "Successfully Uploaded!", "Please Wait - Your torrent will Download Automatically.  Note: that the torrent will NOT be Visible until you start Seeding!");
			echo("<meta http-equiv='refresh' content='1;url=download.php/$id/" . rawurlencode($row["filename"]). "'/>");
		}
		elseif ($_GET["edited"])
		{
			echo display_message("success", " ", "Successfully Edited!");
			if (isset($_GET["returnto"]))
				print("<p><span style='font-weight:bold;'>Return<a href='" . htmlspecialchars("{$site_url}/{$_GET['returnto']}") . "'></a></span></p>\n");
		}
		elseif (isset($_GET["searched"]))
		{
			print("<h2>Your Search for '" . htmlspecialchars($_GET["searched"]) . "' gave a single result:</h2>\n");
		}
		elseif ($_GET["rated"])

		//$returnto = htmlspecialchars($_SERVER["HTTP_REFERER"]);

		//if ($returnto)
			echo error_message("success", " ", "Rating Added!");

		$s=$row["name"];

		print("<h1>$s</h1>\n");

		$url = "edit.php?id=" . $row["id"];

		if (isset($_GET["returnto"]))
		{
			$addthis	= "&amp;returnto=" . urlencode($_GET["returnto"]);
			$url		.= $addthis;
			$keepget	.= $addthis;
		}

		$editlink = "a href='$url' class='btn'";

		print("<p align='center'>
				<a class='main' href='download.php/$id/" . rawurlencode($row["filename"]) . "'>
				<img src='".$image_dir."download1.png' width='184' height='55' border='0' alt='Download' title='Download' />
				</a></p>");

		print("<table width='100%' border='1' cellspacing='0' cellpadding='5'>\n");

		function hex_esc($matches)
		{
			return sprintf("%02x", ord($matches[0]));
		}

		echo("<tr>
				<td class='detail'>Info Hash</td>
				<td class='rowhead'>".preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"]))."</td>
			</tr>");

		if (!empty($row["descr"]))
			echo("<tr>
					<td class='detail'>Description</td>
					<td class='rowhead'>".str_replace(array("\n", "  "), array("\n", "&nbsp; "), format_comment(htmlspecialchars($row["descr"])))."</td>
				</tr>");

		if (get_user_class() >= UC_POWER_USER && $row["nfosz"] > 0)
			echo("<tr>
					<td class='detail'>NFO</td>
					<td class='rowhead'>
					<a href='viewnfo.php?id=$row[id]'><span style='font-weight:bold;'>View NFO</span></a>
					(" . mksize($row["nfosz"]) . ")</td>
				</tr>");

		if ($row["visible"] == "no")
			echo("<tr>
					<td class='detail'>Visible</td>
					<td class='rowhead'><span style='font-weight:bold;'>No</span> (Dead)</td>
				</tr>");

		if ($moderator)
			echo("<tr>
					<td class='detail'>Banned</td>
					<td class='rowhead'>".$row["banned"]."</td>
				</tr>");

		if (isset($row["cat_name"]))
			echo("<tr>
					<td class='detail'>Type</td>
					<td class='rowhead'>".$row["cat_name"]."</td>
				</tr>");
		else
			echo("<tr>
					<td class='detail'>Type</td>
					<td class='rowhead'>(None Selected)</td>
				</tr>");

			echo("<tr>
					<td class='detail'>Last&nbsp;Seeder</td>
					<td class='rowhead'>Last Activity " . mkprettytime($row["lastseed"]) . " ago</td>
				</tr>");

			echo("<tr>
					<td class='detail'>Size</td>
					<td class='rowhead'>".mksize($row["size"])." (".number_format($row["size"])." bytes)</td>
				</tr>");

		$s	= "";
		$s .= "<table border='0' cellpadding='0' cellspacing='0'><tr><td valign='top' class='embedded'>";

		if (!isset($row["rating"]))
		{
			if ($min_votes > 1)
			{
				$s .= "None Yet (needs at least $min_votes Votes and has received ";

				if ($row["numratings"])
					$s .= "Only " . $row["numratings"];
				else
					$s .= "None";
				$s .= ")";
			}
			else
				$s .= "No Votes Yet";
		}
		else
		{
			$rpic = ratingpic($row["rating"]);

			if (!isset($rpic))
				$s .= "Invalid?";
			else
				$s .= "$rpic (" . $row["rating"] . " out of 5 with " . $row["numratings"] . " Vote(s) total)";
		}
			$s .= "\n";
			$s .= "</td><td class='embedded'>$spacer</td><td valign='top' class='embedded'>";

			if (!isset($CURUSER))
				$s .= "(<a href='login.php?returnto=" . urlencode(substr($_SERVER["REQUEST_URI"],1)) . "&amp;nowarn=1'>Log in</a> to Rate this torrent.)";
			else
			{
				$ratings = array(
						5 => "Great",
						4 => "Pretty Good",
						3 => "Decent",
						2 => "Pretty Bad",
						1 => "Terrible",
				);

				if (!$owned || $moderator)
				{
					$xres = sql_query("SELECT rating, added
										FROM ratings
										WHERE torrent = $id
										AND user = " . $CURUSER["id"]);

					$xrow = mysql_fetch_assoc($xres);

					if ($xrow)
						$s .= "(you Rated this torrent as '" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "')";
					else
					{
						$s .= "<form method='post' action='takerate.php'><input type='hidden' name='id' value='$id' />\n";
						$s .= "<select name='rating'>\n";
						$s .= "<option value='0'>(Add Rating)</option>\n";

						foreach ($ratings AS $k => $v)
						{
							$s .= "<option value='$k'>$k - $v</option>\n";
						}

						$s .= "</select>\n";
						$s .= "<input type='submit' class='btn' value='Vote!' />";
						$s .= "</form>\n";
					}
				}
			}
			$s .= "</td></tr></table>";

			echo("<tr>
					<td class='detail'>Rating</td>
					<td class='rowhead'>".$s."</td>
				</tr>");

			echo("<tr>
					<td class='detail'>Added</td>
					<td class='rowhead'>".$row["added"]."</td>
				</tr>");

			echo("<tr>
					<td class='detail'>Views</td>
					<td class='rowhead'>".$row["views"]."</td>
				</tr>");

			echo("<tr>
					<td class='detail'>Hits</td>
					<td class='rowhead'>".$row["hits"]."</td>
				</tr>");


			echo("<tr>
					<td class='detail'>Snatched</td>
					<td class='rowhead'>".($row["times_completed"] > 0 ? "<a href='snatches.php?id=$id'>$row[times_completed] time(s)</a>" : "0 times")."</td>
				</tr>");

			$keepget	= "";
			$uprow		= (isset($row["username"]) ? ("<a href='userdetails.php?id=" . $row["owner"] . "'><span style='font-weight:bold;'>" . htmlspecialchars($row["username"]) . "</span></a>") : "<span style='font-style: italic;'>Unknown</span>");

			if ($owned)
				$uprow .= " $spacer<$editlink>Edit this Torrent</a>";

			echo("<tr>
					<td class='detail'>Upped by</td>
					<td class='rowhead'>".$uprow."</td>
				</tr>");

			if ($row["type"] == "multi")
			{
			if (!$_GET["filelist"])
				echo("<tr>
						<td class='detail'>Num Files<br /><a href='details.php?id=$id&amp;filelist=1$keepget#filelist' class='sublink'>[See Full List]</a></td>
						<td class='rowhead'>". $row["numfiles"] . " files</td>
					</tr>");
			else
			{
				echo("<tr>
						<td class='detail'>Num Files</td>
						<td class='rowhead'>".$row["numfiles"]." files</td>
					</tr>");

				$s = "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n";

				$subres = sql_query("SELECT *
										FROM files
										WHERE torrent = $id
										ORDER BY id");

				$s.="<tr><td class='colhead'>Path</td><td class='colhead' align='right'>Size</td></tr>\n";

				while ($subrow = mysql_fetch_assoc($subres))
				{
					$s .= "<tr><td class='detail'>" . $subrow["filename"] .
							"</td><td class='rowhead' align='right'>" . mksize($subrow["size"]) . "</td></tr>\n";
				}

				$s .= "</table>\n";

				echo("<tr>
					<td class='detail'><a name='filelist'>File List</a><br /><a href='details.php?id=$id$keepget' class='sublink'>[Hide List]</a></td>
							<td class='rowhead'>". $s."</td>
					</tr>");
			}
		}

		if (!$_GET["dllist"])
		{
			echo("<tr>
					<td class='detail'>Peers<br /><a href='details.php?id=$id&amp;dllist=1$keepget#seeders' class='sublink'>[See Full list]</a></td>
					<td class='rowhead'>".$row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . " peer(s) total</td>
				</tr>");
		}
		else
		{
			$downloaders	= array();
			$seeders		= array();
			$subres			= sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, UNIX_TIMESTAMP(last_action) AS la, userid
										FROM peers
										WHERE torrent = $id") or sqlerr();

		while ($subrow = mysql_fetch_assoc($subres))
		{
			if ($subrow["seeder"] == "yes")
				$seeders[] = $subrow;
			else
				$downloaders[] = $subrow;
		}

		function leech_sort($a,$b)
		{
			if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);
				$x = $a["to_go"];
				$y = $b["to_go"];

			if ($x == $y)
				return 0;

			if ($x < $y)
				return -1;

			return 1;
		}

		function seed_sort($a,$b)
		{
			$x = $a["uploaded"];
			$y = $b["uploaded"];

			if ($x == $y)
				return 0;

			if ($x < $y)
				return 1;

			return -1;
		}

			usort($seeders, "seed_sort");
			usort($downloaders, "leech_sort");

			echo("<tr>
					<td class='detail'><a name='seeders'>Seeders</a><br /><a href='details.php?id=$id$keepget' class='sublink'>[Hide List]</a></td>
					<td class='rowhead'>". dltable("Seeder(s)", $seeders, $row)."</td>
				</tr>");


			echo("<tr>
					<td class='detail'><a name='leechers'>Leechers</a><br /><a href='details.php?id=$id$keepget' class='sublink'>[Hide List]</a></td>
					<td class='rowhead'>". dltable("Leecher(s)", $downloaders, $row)."</td>
				</tr>");
		}

		print("</table>\n");
	}
	else
	{
		site_header("Comments for Torrent '" . $row["name"] . "'");

		print("<h1>Comments for <a href='details.php?id=$id'>" . $row["name"] . "</a></h1>\n");
	}

	print("<p><a name='startcomments'></a></p>\n");

	$commentbar = "<p align='center'><a class='btn' href='comment.php?action=add&amp;tid=$id'>Add a Comment</a></p>\n";

	$count = $row['comments'];

	if (!$count)
	{
		echo display_message("info", " ", "No Comments Yet");
	}
	else
	{
		list($pagertop, $pagerbottom, $limit) = pager(20, $count, "details.php?id=$id&", array(lastpagedefault => 1));

		$subres = sql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, username, title, class, donor
								FROM comments
								LEFT JOIN users ON comments.user = users.id
								WHERE torrent = $id
								ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);

		$allrows = array();

		while ($subrow = mysql_fetch_assoc($subres))
		$allrows[] = $subrow;

		print($commentbar);
		print($pagertop);

		commenttable($allrows);

		print($pagerbottom);
	}

	print($commentbar);
}

site_footer();

?>