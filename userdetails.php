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
*--   This program is free software; you can redistribute it and/or modify    --*
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

require_once(dirname (__FILE__) . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'function_main.php');
require_once(INCL_DIR . 'function_user.php');
require_once(INCL_DIR . 'function_vfunctions.php');
require_once(INCL_DIR . 'function_torrenttable.php');
require_once(INCL_DIR . 'function_bbcode.php');
require_once(INCL_DIR . 'function_page_verify.php');

db_connect (false);
logged_in ();

$newpage = new page_verify();
$newpage->create ('_modtask_');

function snatchtable ($res)
{
	global $image_dir;

	$table = "<table class='main' border='1' cellspacing='0' cellpadding='5'>
				<tr>
					<td class='colhead'>Category</td>
					<td class='colhead'>Torrent</td>
					<td class='colhead'>Up.</td>
					<td class='colhead'>Rate</td>
					<td class='colhead'>Downl.</td>
					<td class='colhead'>Rate</td>
					<td class='colhead'>Ratio</td>
					<td class='colhead'>Activity</td>
					<td class='colhead'>Finished</td>
				</tr>";

	while ($arr = mysql_fetch_assoc ($res))
	{
		$upspeed = ($arr["upspeed"] > 0 ? mksize ($arr["upspeed"]) : ($arr["seedtime"] > 0
				? mksize ($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize (0)));
		$downspeed = ($arr["downspeed"] > 0 ? mksize ($arr["downspeed"]) : ($arr["leechtime"] > 0
				? mksize ($arr["downloaded"] / $arr["leechtime"]) : mksize (0)));
		$ratio = ($arr["downloaded"] > 0 ? number_format ($arr["uploaded"] / $arr["downloaded"], 3)
				: ($arr["uploaded"] > 0 ? "Inf." : "---"));
		$table = "<tr>
					<td class='rowhead' align='center' style='padding: 0px'><img src='" . $image_dir . "caticons/" . htmlspecialchars ($arr["catimg"]) . "' alt='" . htmlspecialchars ($arr["catname"]) . "' width='42' height='42' /></td>
					<td class='rowhead' align='center'><a href='details.php?id=$arr[torrentid]'><strong>" . (strlen ($arr["name"]) > 50
				? substr ($arr["name"], 0, 50 - 3) . "..." : $arr["name"]) . "</strong></a></td>
					<td class='rowhead' align='center'>" . mksize ($arr["uploaded"]) . "</td>
					<td class='rowhead' align='center'>$upspeed/s</td>
					<td class='rowhead' align='center'>" . mksize ($arr["downloaded"]) . "</td>
					<td class='rowhead' align='center'>$downspeed/s</td>
					<td class='rowhead' align='center'>$ratio</td>
					<td class='rowhead' align='center'>" . mkprettytime ($arr["seedtime"] + $arr["leechtime"]) . "</td>
					<td class='rowhead' align='center'>" . ($arr["complete_date"] <> "0000-00-00 00:00:00"
				? "<font color='green'><strong>Yes</strong></font>" : "<font color='red'><strong>No</strong></font>") . "</td>
				</tr>";
	}
	$table .= "</table>\n";
	return $table;
}

function maketable ($res)
{
	global $image_dir;

	$ret = "<table class='main' width='100%' border='1' cellspacing='0' cellpadding='5'>
				<tr>
					<td class='colhead' align='center'>Type</td>
					<td class='colhead'>Name</td>
					<td class='colhead' align='center'>Size</td>
					<td class='colhead' align='right'>Se.</td>
					<td class='colhead' align='right'>Le.</td>
					<td class='colhead' align='center'>Upl.</td>
					<td class='colhead' align='center'>Downl.</td>
					<td class='colhead' align='center'>Ratio</td>
				</tr>\n";

	foreach ($res
			  as
			  $arr)
	{
		if ($arr["downloaded"] > 0)
		{
			$ratio = number_format ($arr["uploaded"] / $arr["downloaded"], 3);
			$ratio = "<font color='" . get_ratio_color ($ratio) . "'>$ratio</font>";
		}
		else
		{
			if ($arr["uploaded"] > 0)
			{
				$ratio = "Inf.";
			}
			else
			{
				$ratio = "---";
			}
		}

		$catimage = "{$image_dir}caticons/{$arr['image']}";
		$catname = htmlspecialchars ($arr["catname"]);
		$catimage = "<img src='" . htmlspecialchars ($catimage) . "' title='$catname' alt='$catname' width='42' height='42' />";

		// $ttl = (28*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
		// if ($ttl == 1) $ttl .= "<br />hour"; else $ttl .= "<br />hours";

		$size = str_replace (" ", "<br />", mksize ($arr["size"]));
		$uploaded = str_replace (" ", "<br />", mksize ($arr["uploaded"]));
		$downloaded = str_replace (" ", "<br />", mksize ($arr["downloaded"]));
		$seeders = number_format ($arr["seeders"]);
		$leechers = number_format ($arr["leechers"]);

		$ret .= "<tr>
					<td class='rowhead' style='padding: 0px'>$catimage</td>
					<td class='rowhead'><a href='details.php?id=$arr[torrent]&amp;hit=1'><strong>" . htmlspecialchars ($arr["torrentname"]) . "</strong></a></td>
					<td class='rowhead' align='center'>$size</td>
					<td class='rowhead' align='right'>$seeders</td>
					<td class='rowhead' align='right'>$leechers</td>
					<td class='rowhead' align='center'>$uploaded</td>
					<td class='rowhead' align='center'>$downloaded</td>
					<td class='rowhead' align='center'>$ratio</td>
				</tr>\n";
	}
	$ret .= "</table>\n";
	return $ret;
}

$id = 0 + $_GET["id"];

if (!is_valid_id ($id))
{
	error_message ("error", "Error", "Bad ID $id.");
}

$r = @sql_query ("SELECT *
					FROM users
					WHERE  id=" . mysql_real_escape_string ($id)) or sqlerr ();

$user = mysql_fetch_assoc ($r) or error_message ("error", "Error", "No user with ID $id.");

if ($user["status"] == "pending")
{
	die;
}

$r = sql_query ("SELECT id, name, seeders, leechers, category
					FROM torrents
					WHERE owner=$id
					ORDER BY name") or sqlerr ();

if (mysql_num_rows ($r) > 0)
{
	$torrents = "<table class='main' width='100%' border='1' cellspacing='0' cellpadding='5'>
	<tr>
		<td class='colhead'>Type</td>
		<td class='colhead'>Name</td>
		<td class='colhead'>Seeders</td>
		<td class='colhead'>Leechers</td>
	</tr>";

	while ($a = mysql_fetch_assoc ($r))
	{
		$r2 = sql_query ("SELECT name, image
							FROM categories
							WHERE id=$a[category]") or sqlerr (__FILE__, __LINE__);

		$a2 = mysql_fetch_assoc ($r2);
		$cat = "<img src='" . htmlspecialchars ("{$image_dir}caticons/{$a2['image']}") . "' alt='$a2[name]' />";
		$torrents .= "<tr>
						<td class='rowhead' style='padding: 0px'>$cat</td>
						<td class='rowhead' style='padding: 0px'><a href='details.php?id=" . $a["id"] . "&amp;hit=1'><strong>" . htmlspecialchars ($a["name"]) . "</strong></a></td>
						<td class='rowhead' align='right'>$a[seeders]</td>
						<td class='rowhead' align='right'>$a[leechers]</td>
					</tr>";
	}
	$torrents .= "</table>";
}

if ($user["ip"] && (get_user_class () >= UC_MODERATOR || $user["id"] == $CURUSER["id"]))
{
	$ip = $user["ip"];
	$dom = @gethostbyaddr ($user["ip"]);

	if ($dom == $user["ip"] || @gethostbyname ($dom) != $user["ip"])
	{
		$addr = $ip;
	}
	else
	{
		$dom = strtoupper ($dom);
		$domparts = explode (".", $dom);
		$domain = $domparts[count ($domparts) - 2];

		if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR")
		{
			$l = 2;
		}
		else
		{
			$l = 1;
		}
		$addr = "$ip ($dom)";
	}
}

if ($user[added] == "0000-00-00 00:00:00")
{
	$joindate = 'N/A';
}
else
{
	$joindate = "$user[added] (" . get_elapsed_time (sql_timestamp_to_unix_timestamp ($user["added"])) . " ago)";
	$lastseen = $user["last_access"];
}

if ($lastseen == "0000-00-00 00:00:00")
{
	$lastseen = "never";
}
else
{
	$lastseen .= " (" . get_elapsed_time (sql_timestamp_to_unix_timestamp ($lastseen)) . " ago)";
}

$res = sql_query ("SELECT COUNT(*)
					FROM comments
					WHERE user=" . $user['id']) or sqlerr ();

$arr3 = mysql_fetch_row ($res);
$torrentcomments = $arr3[0];

$res = sql_query ("SELECT COUNT(*)
					FROM posts
					WHERE userid=" . $user['id']) or sqlerr ();

$arr3 = mysql_fetch_row ($res);
$forumposts = $arr3[0];
$country = '';

$res = sql_query ("SELECT name,flagpic
					FROM countries
					WHERE id=" . $user['country'] . "
					LIMIT 1") or sqlerr ();

if (mysql_num_rows ($res) == 1)
{
	$arr = mysql_fetch_assoc ($res);
	$country = "<td class='embedded'><img src='{$image_dir}flag/{$arr[flagpic]}' alt='" . htmlspecialchars ($arr[name]) . "' style='margin-left: 8pt' /></td>";
}

$res = sql_query ("SELECT p.torrent, p.uploaded, p.downloaded, p.seeder, t.added, t.name AS torrentname, t.size, t.category, t.seeders, t.leechers, c.name as catname, c.image
					FROM peers p
					LEFT JOIN torrents t ON p.torrent = t.id
					LEFT JOIN categories c ON t.category = c.id
					WHERE p.userid=$id") or sqlerr ();

while ($arr = mysql_fetch_assoc ($res))
{
	if ($arr['seeder'] == 'yes')
	{
		$seeding[] = $arr;
	}
	else
	{
		$leeching[] = $arr;
	}
}

// Connectable,port and client stuff

$q1 = sql_query ('SELECT connectable, port,agent
					FROM peers
					WHERE userid = ' . $id . '
					LIMIT 1') or sqlerr ();

if ($a = mysql_fetch_row ($q1))
{
	$connect = $a[0];

	if ($connect == "yes")
	{
		$connectable = "<font color='green'><strong>Yes</strong></font>";
	}
	else
	{
		$connectable = "<font color='red'><strong>No</strong></font>";
	}
}
else
{
	$connectable = "<font color='blue'><strong>Unknown</strong></font>";
}

// Ratio shit
if ($user["downloaded"] > 0)
{
	$sr = $user["uploaded"] / $user["downloaded"];
	if ($sr >= 4)
	{
		$s = "w00t";
	}

	else
	{
		if ($sr >= 2)
		{
			$s = "grin";
		}

		else
		{
			if ($sr >= 1)
			{
				$s = "smile1";
			}

			else
			{
				if ($sr >= 0.5)
				{
					$s = "noexpression";
				}

				else
				{
					if ($sr >= 0.25)
					{
						$s = "sad";
					}

					else
					{
						$s = "cry";
					}
				}
			}
		}
	}

	$sr = floor ($sr * 1000) / 1000;
	$sr = "<table class='bottom' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td class='std'><font color='" . get_ratio_color ($sr) . "'>" . number_format ($sr, 3) . "</font></td>
				<td class='std'>&nbsp;&nbsp;<img src='{$image_dir}smilies/{$s}.gif' alt='' /></td>
			</tr>
		</table>";
}

site_header ("Details for " . $user["username"]);

$enabled = $user["enabled"] == 'yes';
$down_space = "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";

print("$down_space<table class='main' align='center' border='0' cellspacing='0' cellpadding='0'>");
print("<tr><div><td class='embedded'><h1 style='margin:0px'>{$user['username']}" . get_user_icons ($user, true) . "</h1></td>$country<?div></tr>");

if (!$enabled)
{
	print("<p><strong>This Account has been Disabled</strong></p>\n");
}
elseif ($CURUSER["id"] <> $user["id"])
{
	$r = sql_query ("SELECT id
						FROM friends
						WHERE userid=$CURUSER[id]
						AND friendid=$id") or sqlerr (__FILE__, __LINE__);

	$friend = mysql_num_rows ($r);

	$r = sql_query ("SELECT id
							FROM blocks
							WHERE userid=$CURUSER[id]
							AND blockid=$id") or sqlerr (__FILE__, __LINE__);

	$block = mysql_num_rows ($r);

	if ($friend)
	{
		print("<div align='center'>(<a href='friends.php?action=delete&amp;type=friend&amp;targetid=$id'>Remove from Friends</a>)</div>\n");
	}
	elseif ($block)
	{
		print("<div align='center'>(<a href='friends.php?action=delete&amp;type=block&amp;targetid=$id'>Remove from Blocks</a>)</div>\n");
	}
	else
	{
		print("<div align='center'>(<a href='friends.php?action=add&amp;type=friend&amp;targetid=$id'>Add to Friends</a>)");
		print(" - (<a href='friends.php?action=add&amp;type=block&amp;targetid=$id'>Add to Blocks</a>)</div>\n");
	}
}
print("</table>");

?>

<script type = "text/javascript" src = "js/content_glider.js"></script>
<script type = "text/javascript">

	featuredcontentglider.init(
			{
				gliderid: "FreeTSPuserdetails", //ID of main glider container
				contentclass: "FreeTSPglidecontent8", //Shared CSS class name of each glider content
				togglerid: "FreeTSP8", //ID of toggler container
				remotecontent: "", //Get gliding contents from external file on server? "filename" or "" to disable
				selected: 0, //Default selected content index (0=1st)
				persiststate: false, //Remember last content shown within browser session (true/false)?
				speed: 700, //Glide animation duration (in milliseconds)
				direction: "downup" //set direction of glide: "updown", "downup", "leftright", or "rightleft"
			}
	)

</script>
<?php
print("<div id='FreeTSP8' class='FreeTSPglidecontenttoggler8'>
		<a href='#' class='toc'>General</a>
		<a href='#' class='toc'>Torrents</a>
		<a href='#' class='toc'>Info</a>");

if (get_user_class () >= UC_MODERATOR)
{
	print("<a href='#' class='toc'>Snatch List</a>");
}
if (get_user_class () >= UC_MODERATOR && $user["class"] < get_user_class ())
{
	print("<a href='#' class='toc'>Alter Ratio</a>");
	print("<a href='#' class='toc'>Edit User</a>");
}
print("</div>");


//--Start General Details

print("<div id='FreeTSPuserdetails' class='FreeTSPglidecontentwrapper8'>");

print("<div class='FreeTSPglidecontent8'>
		<table align='center' width='100%' border='1'>");

print("<tr>
		<td class='std' colspan='2' height='18' align='center'><strong>General Details</strong></td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Join&nbsp;date</td>
		<td class='rowhead' align='left' width='99%'>$joindate</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Last&nbsp;seen</td>
		<td class='rowhead' align='left'>$lastseen</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Title</td>
		<td class='rowhead' align='left'>" . htmlspecialchars ($user["title"]) . "</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Class</td>
		<td class='rowhead' align='left'>" . get_user_class_name ($user["class"]) . "</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Forum&nbsp;Posts</td>");

if ($forumposts && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class () >= UC_MODERATOR))
{
	print("<td class='rowhead' align='left'><a href='userhistory.php?action=viewposts&amp;id=$id'>$forumposts</a></td></tr>");
}
else
{
	print("<td class='rowhead' align='left'>$forumposts</td></tr>");
}


if ($user["avatar"])
{
	print("<tr>
				<td class='rowhead' width='15%'>Avatar</td>
				<td class='rowhead' align='left'><img height='150' width='150' src='" . htmlspecialchars ($user["avatar"]) . "' alt='' /></td>
			</tr>");
}

if ($CURUSER["id"] != $user["id"])
{
	if (get_user_class () >= UC_MODERATOR)
	{
		$showpmbutton = 1;
	}
}
if ($user["acceptpms"] == "yes")
{
	$r = sql_query ("SELECT id
						FROM blocks
						WHERE userid={$user['id']}
						AND blockid={$CURUSER['id']}") or sqlerr (__FILE__, __LINE__);

	$showpmbutton = (mysql_num_rows ($r) == 1 ? 0 : 1);
}
if ($user["acceptpms"] == "friends")
{
	$r = sql_query ("SELECT id
						FROM friends
						WHERE userid=$user[id]
						AND friendid=$CURUSER[id]") or sqlerr (__FILE__, __LINE__);

	$showpmbutton = (mysql_num_rows ($r) == 1 ? 1 : 0);
}
if (isset($showpmbutton))
{
	print("<tr>
				<td class='std' colspan='2' align='center'><form method='get' action='sendmessage.php'><input type='hidden' name='receiver' value='" . $user["id"] . "' /><input type='submit' class='btn' value='Send Message' style='height: 23px' /></form></td>
			</tr>");
}

print("</table></div>");

//-- Finish General Details

//-- Start Torrent Details

print("<div class='FreeTSPglidecontent8'>
		<table align='center' width='100%' border='1'>");

print("<tr>
		<td class='std' colspan='2' height='18' align='center'><strong>Torrents</strong></td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Connectable</td><td class='rowhead' align='left'>" . $connectable . "</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Uploaded</td>
		<td class='rowhead' align='left'>" . mksize ($user["uploaded"]) . "</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Downloaded</td>
		<td class='rowhead' align='left'>" . mksize ($user["downloaded"]) . "</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Share Ratio</td>
		<td class='rowhead' align='left'>$sr</td>
	</tr>");

print("<tr>
		<td class='rowhead' width='15%'>Torrent&nbsp;Comments</td>");

if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class () >= UC_MODERATOR))
{
	print("<td class='rowhead' align='left'><a href='userhistory.php?action=viewcomments&amp;id=$id'>$torrentcomments</a></td></tr>");
}
else
{
	print("<td class='rowhead' align='left'>$torrentcomments</td></tr>");
}

print("</table></div>");

//-- Finish Torrent Details

//-- Start Info Details

print("<div class='FreeTSPglidecontent8'>
		<table align='center' width='100%' border='1'>");

print("<tr><td class='std' colspan='2' height='18' align='center'><strong>Infomation</strong></td></tr>");

if ($user["info"])
{
	print("<tr><td class='rowhead' align='center' width='15%'>Members Shared Infomation</td><td class='rowhead' align='left'>" . format_comment ($user["info"]) . "</td></tr>");
}

if ($user["signature"])
{
	print("<tr><td class='rowhead' width='15%'>Signature</td><td class='rowhead' align='left'>" . format_comment ($user["signature"]) . "</td></tr>\n");
}

print("</table></div>");

//-- Finish Info Details


//-- Start Snatch Details

print("<div class='FreeTSPglidecontent8'>
		<table align='center' width='100%' border='1'>");

print("<tr><td class='std' colspan='2' height='18' align='center'><strong>Snatched List</strong></td></tr>");

//-- Start Recently Snatched Expanding Table

$snatches = "";

$r = sql_query ("SELECT id, name, seeders, leechers, category
					FROM torrents
					WHERE owner=$id
					ORDER BY name") or sqlerr ();

if (mysql_num_rows ($r) > 0)
{
	$numbupl = mysql_num_rows ($r);
}

if (isset($torrents))
{
	print("<tr align='center'>
				<td class='rowhead' width='10%'>Uploaded&nbsp;Torrents</td>
				<td class='rowhead' align='left' colspan='2'>
					<div style='overflow: auto; width: 100%; height: 200px'>$torrents</div>
				</td>
			</tr>");
}


//-- Start Expanding Currently Seeding

$numbseeding = mysql_num_rows ($res);
if (isset($seeding))
{
	print("<tr valign='top'>
		<td class='rowhead' width='10%'>Currently&nbsp;Seeding</td>
		<td class='rowhead' align='left' colspan='90%'>
			<a href=\"javascript: klappe_news('a1')\"><img border=\"0\" src=\"" . $image_dir . "plus.gif\" id=\"pica1\" alt=\"Show/Hide\" /></a>
			<strong><font color=\"red\">&nbsp;&nbsp;$numbseeding</font></strong>
			<div id='ka1' style='overflow: auto; width: 100%; height: 200px'>" . maketable ($seeding) . "</div>
		</td>
	</tr>");
}

//-- Finish Expanding Currently Seeding


//-- Start Expanding Currently leeching

$numbleeching = mysql_num_rows ($res);

if (isset($leeching))

{
	print("<tr valign=\"top\">
	<td class=\"rowhead\" width=\"10%\">Currently&nbsp;Leeching</td>
	<td class=\"rowhead\" align=\"left\" width=\"90%\">
		<a href=\"javascript: klappe_news('a2')\"><img border=\"0\" src=\"" . $image_dir . "plus.gif\" id=\"pica2\" alt=\"Show/Hide\" /></a>
		<strong><font color=\"red\">&nbsp;&nbsp;$numbleeching</font></strong>
		<div id='ka2' style='overflow: auto; width: 100%; height: 200px'>" . maketable ($leeching) . "</div>
		</td>
	</tr>");
}

//-- Finish Expanding Currently leeching

//-- Start Snatched Table
$snatches = '';

$res = sql_query ("SELECT s.*, t.name AS name, c.name AS catname, c.image AS catimg
					FROM snatched AS s
					INNER JOIN torrents AS t ON s.torrentid = t.id
					LEFT JOIN categories AS c ON t.category = c.id
					WHERE s.userid = $user[id]") or sqlerr (__FILE__, __LINE__);

if (mysql_num_rows ($res) > 0)
{
	$snatches = snatchtable ($res);
}
$numbsnatched = mysql_num_rows ($res);

if (isset($snatches))
{
	print("<tr valign=\"top\">
		<td class=\"rowhead\" width=\"10%\">Recently&nbsp;Snatched</td>
		<td class=\"rowhead\" align=\"left\" width=\"90%\">
			<a href=\"javascript: klappe_news('a3')\"><img border=\"0\" src=\"" . $image_dir . "plus.gif\" id=\"pica3\" alt=\"Show/Hide\" /></a>
			<strong><font color=\"red\">&nbsp;&nbsp;$numbsnatched</font></strong>
			<div id='ka3' style='overflow: auto; width: 100%; height: 200px'>$snatches</div>
		</td>
	</tr>");
}

//-- Finish Snatched Table

//-- Finish Recently Snatched Expanding Table


print("</table></div>");

//-- Finish Snatch Details

//-- Start Alter Ratio

print("<div class='FreeTSPglidecontent8'>
		<table align='center' width='100%' border='1'>");

print("<tr><td class='std' colspan='2' height='18' align='center'><strong>Alter Ratio</strong></td></tr>");

//-- Start Create Ratio By == Fireknight ----Based On The Original Code By Dodge
if (get_user_class () >= UC_SYSOP && $user["class"] < get_user_class ())
{
	print("<form method='post' action='ratio.php'>");
	print("<tr><td class='rowhead'>User name</td><td class='rowhead'><input READONLY name='username' value='$user[username]' size='40'></td></tr>");
	print("<tr><td class='rowhead'>Uploaded</td><td class='rowhead'><input type='uploaded' name='uploaded' value='0' size='40'></td></tr>");
	print("<tr><td class='rowhead'>Downloaded</td><td class='rowhead'><input type='downloaded' name='downloaded' value='0' size='40'></td></tr>");
	print("<tr><td class='rowhead'>Select input measure</td><td class='rowhead'><select name='bytes'><option value='1'>" . MBytes . "</option>");
	print("<option value='2'>" . GBytes . "</option>");
	print("<option value='3'>" . TBytes . "</option></select>");
	print("</td></tr>");
	print("<tr><td class='rowhead'>" . Action . "</td><td class='rowhead'><select name='action'><option value='1'>" . Add . "</option>");
	print("<option value='2'>" . Remove . "</option>");
	print("<option value='3'>" . Replace . "</option></select>");
	print("</td></tr>");
	print("<tr><td class='std' colspan='2' align='center'><input type='submit' value='Okay'></td></tr>");
	print("</form>");
}

// End Create Ratio By == Fireknight ----Based On The Original Code By Dodge


print("</table></div>");

//-- Finish Alter Ratio

//-- Start Edit Members Details

print("<div class='FreeTSPglidecontent8'>
		<table align='center' width='100%' border='1'>");

print("<tr><td class='std' height='18' align='center'><strong>Edit Member</strong></td></tr>");

if (get_user_class () >= UC_MODERATOR && $user["class"] < get_user_class ())
{
	print("<form method='post' action='modtask.php'>");

	require_once INCL_DIR . 'function_user_validator.php';

	print(validatorForm ("ModTask_$user[id]"));
	print("<input type='hidden' name='action' value='edituser' />");
	print("<input type='hidden' name='userid' value='$id' />");
	print("<input type='hidden' name='returnto' value='userdetails.php?id=$id' />");
	print("<table class='main' width='100%' border='1' cellspacing='0' cellpadding='5'>");
	print("<tr>
			<td class='rowhead'>Title</td>
			<td class='rowhead' colspan='2' align='left'><input type='text' name='title' size='60' value='" . htmlspecialchars ($user[title]) . "' /></td>
		</tr>");

	$avatar = htmlspecialchars ($user["avatar"]);

	print("<tr>
			<td class='rowhead' align='left'>avatar&nbsp;URL</td>
			<td class='rowhead' colspan='2' align='left'><input type='text' name='avatar' size='60' value='" . htmlspecialchars ($user[avatar]) . "' /></td>
		</tr>");

	$info = htmlspecialchars ($user["info"]);

	print("<tr>
			<td class='rowhead'>User&nbsp;Info&nbsp;URL</td>
			<td class='rowhead' colspan='2' align='left'><input type='text' name='info' size='60' value='" . htmlspecialchars ($user[info]) . "' /></td>
		</tr>");

	$signature = htmlspecialchars ($user["signature"]);

	print("<tr>
			<td class='rowhead'>Signature&nbsp;URL</td>
			<td class='rowhead' colspan='2' align='left'><input type='text' name='signature' size='60' value='$signature' /></td>
		</tr>");

	// we do not want mods to be able to change user classes or amount donated...
	if ($CURUSER["class"] >= UC_ADMINISTRATOR)
	{
		print("<tr>
			<td class='rowhead'>Donor</td>
			<td class='rowhead' colspan='2' align='left'><input type='radio' name='donor' value='yes'" . ($user["donor"] == "yes"
				? " checked='checked'"
				: "") . " />Yes <input type='radio' name='donor' value='no'" . ($user["donor"] == "no"
				? " checked='checked'" : "") . " />No</td>
		</tr>");
	}

	if (get_user_class () == UC_MODERATOR && $user["class"] > UC_VIP)

	{
		print("<input type='hidden' name='class' value='$user[class]' />");
	}
	else
	{
		print("<tr>
				<td class='rowhead'>Class</td>
				<td class='rowhead' colspan='2' align='left'><select name='class'>");

		if (get_user_class () == UC_MODERATOR)
		{
			$maxclass = UC_VIP;
		}
		else
		{
			$maxclass = get_user_class () - 1;
		}

		for ($i = 0 ;
			  $i <= $maxclass ;
			  ++$i)
		{
			print("<option value='$i'" . ($user["class"] == $i ? " selected='selected'"
					: "") . ">$prefix" . get_user_class_name ($i) . "</option>\n");
		}
		print("</select></td></tr>\n");
	}

	$modcomment = htmlspecialchars ($user["modcomment"]);

	if (get_user_class () < UC_SYSOP)
	{
		print("<tr>
			<td class='rowhead'>Comment</td>
			<td class='rowhead' colspan='2' align='left'><textarea cols='60' rows='18' name='modcomment' READONLY>$modcomment</textarea></td>
		</tr>");
	}
	else
	{
		print("<tr>
			<td class='rowhead'>Comment</td>
			<td class='rowhead' colspan='2' align='left'><textarea cols='60' rows='18' name='modcomment' >$modcomment</textarea></td>
		</tr>");
	}

	print("<tr>
			<td class='rowhead'>Add&nbsp;Comment</td>
			<td class='rowhead' colspan='2' align='left'><textarea cols='60' rows='2' name='addcomment' ></textarea></td>
		</tr>");

	$warned = $user["warned"] == "yes";

	print("<tr>
			<td class='rowhead'" . (!$warned ? " rowspan='2'" : "") . ">Warned</td>
			<td class='rowhead' align='left' width='20%'>" . ($warned
			? "<input type='radio' name='warned' value='yes' checked='checked' />Yes<input type='radio' name='warned' value='no' />No"
			: "No") . "</td>");

	if ($warned)
	{
		$warneduntil = $user['warneduntil'];

		if ($warneduntil == '0000-00-00 00:00:00')
		{
			print("<td class='rowhead' align='center'>(arbitrary duration)</td></tr>");
		}
		else
		{
			print("<td class='rowhead' align='center'>Until $warneduntil");
			print(" (" . mkprettytime (strtotime ($warneduntil) - gmtime ()) . " to go)</td></tr>");
		}
	}
	else
	{
		print("<td class='rowhead'>Warn for <select name='warnlength'>\n");
		print("<option value='0'>------</option>\n");
		print("<option value='1'>1 week</option>\n");
		print("<option value='2'>2 weeks</option>\n");
		print("<option value='4'>4 weeks</option>\n");
		print("<option value='8'>8 weeks</option>\n");
		print("<option value='255'>Unlimited</option>\n");
		print("</select></td></tr>");

		print("<tr>
				<td class='rowhead' colspan='2' align='left'>Comment:-&nbsp;&nbsp;&nbsp;<input type='text' name='warnpm' size='60' /></td>
			</tr>");
	}

	print("<tr>
			<td class='rowhead'>Enabled</td>
			<td class='rowhead' colspan='2' align='left'><input type='radio' name='enabled' value='yes' " . ($enabled
			? " checked='checked'" : "") . " />Yes <input type='radio' name='enabled' value='no' " . (!$enabled
			? " checked='checked'" : "") . " />No</td>
		</tr>");

	print("<tr>
			<td class='rowhead'>passkey</td>
			<td class='rowhead' colspan='2' align='left'><input type='checkbox' name='resetpasskey' value='1' /> Reset passkey</td>
		</tr>");

	print("<tr>
			<td colspan='3' align='center'><input type='submit' class='btn' value='Okay' /></td>
		</tr>");

	print("</form>");

}

print("</table></div>");

//--Finish Edit Members Details

print("<br />");

site_footer ();

?>