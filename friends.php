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

db_connect(false);
logged_in();

$userid = isset($_GET['id']) ? (int)$_GET['id'] : $CURUSER['id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (!is_valid_id($userid))
	error_message("error", "Error", "Invalid ID.");

if ($userid != $CURUSER["id"])
	error_message("warn", "Warning", "Access Denied.");

// action: add -------------------------------------------------------------

if ($action == 'add')
{
	$targetid = 0+$_GET['targetid'];
	$type     = $_GET['type'];

	if (!is_valid_id($targetid))
		error_message("error", "Error", "Invalid ID.");

	if ($type == 'friend')
	{
		$table_is = $frag = 'friends';
		$field_is = 'friendid';
	}
	elseif ($type == 'block')
	{
		$table_is = $frag = 'blocks';
		$field_is = 'blockid';
	}
	else
		error_message("error", "Error", "Unknown Type.");

	$r = sql_query("SELECT id
					FROM $table_is
					WHERE userid=$userid AND $field_is=$targetid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($r) == 1)
		error_message("error", "Error", "User ID is already in your ".htmlentities($table_is)." list.");

	sql_query("INSERT INTO $table_is
				VALUES (0,$userid, $targetid)") or sqlerr(__FILE__, __LINE__);

	header("Location: $site_url/friends.php?id=$userid#$frag");
	die;
}

// action: delete

if ($action == 'delete')
{
	$targetid	= (int)$_GET['targetid'];
	$sure		= isset($_GET['sure']) ? htmlentities($_GET['sure']) : false;
	$type		= isset($_GET['type']) ? ($_GET['type'] == 'friend' ? 'friend' : 'block') : error_message("error", "Error", "LoL");

	if (!is_valid_id($targetid))
		error_message("error", "Error", "Invalid ID.");

	if (!$sure)
		error_message("warn", "Delete $type", "Do you really want to Delete a $type? Click\n" .
		"<a href='?id=$userid&amp;action=delete&amp;type=$type&amp;targetid=$targetid&amp;sure=1'>here</a> if you are sure.");

	if ($type == 'friend')
	{
		sql_query("DELETE
					FROM friends
					WHERE userid=$userid
					AND friendid=$targetid") or sqlerr(__FILE__, __LINE__);

		if (mysql_affected_rows() == 0)
			error_message("error", "Error", "No Friend found with that ID");

		$frag = "friends";
	}
	elseif ($type == 'block')
	{
	sql_query("DELETE
				FROM blocks
				WHERE userid=$userid
				AND blockid=$targetid") or sqlerr(__FILE__, __LINE__);

	if (mysql_affected_rows() == 0)
		error_message("error", "Error", "No Blocked Member found with that ID");

	$frag = "blocks";
	}
	else
		error_message("error", "Error", "Unknown Type.");

	header("Location: $site_url/friends.php?id=$userid#$frag");
	die;
}

// main body

site_header("Personal Lists for " . $user['username']);

if ($user["donor"] == "yes") $donor = "<img src='{$image_dir}starbig.png' width='32' height='32' border='0' alt='Donor' title='Donor' style='margin-left: 4pt' />";

if ($user["warned"] == "yes") $warned = "<img src='{$image_dir}warnedbig.png' width='32' height='32' border='0' alt='Warned' title='Warned' style='margin-left: 4pt' />";

print("<table class='main' border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td class='embedded'><h1 style='margin:0px'> Personal Lists for $user[username]</h1>$donor$warned$country</td>
		</tr>
	</table>\n");

print("<table class='main' width='100%' border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td class='embedded'>");

print("<br />");
print("<h2 align='left'><a name='friends'>Friends List</a></h2>\n");

echo("<table width='100%' border='1' cellspacing='0' cellpadding='5'>
		<tr>
			<td>");

$i = 0;

$res = sql_query("SELECT f.friendid AS id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access
					FROM friends AS f
					LEFT JOIN users AS u ON f.friendid = u.id
					WHERE userid=$userid
					ORDER BY name") or sqlerr(__FILE__, __LINE__);

if(mysql_num_rows($res) == 0)
	$friends = "<span style='font-style: italic;'>Your Friends List is Empty.</span>";
else
	while ($friend = mysql_fetch_assoc($res))
	{
	$title = $friend["title"];

		if (!$title)
			$title = get_user_class_name($friend["class"]);

			$body1 = "<a href='userdetails.php?id={$friend['id']}'><span style='font-weight:bold;'>".htmlentities($friend['name'], ENT_QUOTES)."</span></a>" .
			get_user_icons($friend) . " ($title)<br /><br />last seen on " . $friend['last_access'] .
			"<br />(" . get_elapsed_time(sql_timestamp_to_unix_timestamp($friend[last_access])) . " ago)";

			$body2 = "<br /><a href='friends.php?id=$userid&amp;action=delete&amp;type=friend&amp;targetid={$friend['id']}'><input type='submit' class='btn' value='Remove' /></a><br /><br /><a href='sendmessage.php?receiver={$friend['id']}'><input type='submit' class='btn' value='Send PM' /></a>";

			$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");

		if (!$avatar)
			$avatar = "{$image_dir}default_avatar.gif width='125' height='125' border='0' atl='' title='' ";

		if ($i % 2 == 0)
			print("<table width='100%' style='padding: 0px'><tr><td class='bottom' style='padding: 5px' width='50%' align='center'>");
		else
			print("<td class='bottom' style='padding: 5px' width='50%' align='center'>");
			print("<table class='main' style='width: 100%;height: 75'>");
			print("<tr valign='top'><td width='75' align='center' style='padding: 0px'>" .
			($avatar ? "<div style='width:75;height:75;overflow: hidden'><img src='$avatar' width='' height='' border='0' alt='' title='' /></div>" : ""). "</td><td>\n");

	print("<table class='main'>");
	print("<tr><td class='embedded' style='padding: 5px' width='80%'>$body1</td>\n");
	print("<td class='embedded' style='padding: 5px' width='20%'>$body2</td></tr>\n");
	print("</table>");

	echo("</td></tr></table>\n");

	if ($i % 2 == 1)
		print("</td></tr></table>\n");
	else
		print("</td>\n");
		$i++;
	}

if ($i % 2 == 1)
	print("<td class='bottom' width='50%'>&nbsp;</td></tr></table>\n");
	print($friends);
	print("</td></tr></table>\n");

$res = sql_query("SELECT b.blockid AS id, u.username AS name, u.donor, u.warned, u.username, u.enabled, u.last_access
					FROM blocks AS b
					LEFT JOIN users AS u ON b.blockid = u.id
					WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);

$blocks = '';

	if(mysql_num_rows($res) == 0)
	{
		$blocks = "<span style='font-style: italic;'>Your Blocked Users List is Empty.</span>";
	}
	else
	{
		while ($block = mysql_fetch_assoc($res))
		{
			$blocks .= "<div style='border: 1px solid black;padding:5px;'>";
			$blocks .= "<span class='btn' style='float:right;'><a href='friends.php?id=$userid&amp;action=delete&amp;type=block&amp;targetid=" .
			$block['id'] . "'>Delete</a></span><br />";
			$blocks .= "<p><a href='userdetails.php?id={$block['id']}'>";
			$blocks .= "<span style='font-weight:bold;'>" . htmlentities($block['name'], ENT_QUOTES) . "</span></a>" . get_user_icons($block) .  "</p></div><br />";
		}
	}

print("<br /><br />");
print("<table class='main' width='100%' border='0' cellspacing='0' cellpadding='10'><tr><td class='embedded'>");
print("<h2 align='left'><a name='blocks'>Blocked Users List</a></h2></td></tr>");
print("<tr><td style='padding: 10px;background-color: #ECE9D8'>");
print("$blocks\n");
print("</td></tr></table>\n");
print("</td></tr></table>\n");
print("<p><a href='users.php'><span style='font-weight:bold;'>Find User/Browse User List</span></a></p>");

site_footer();

?>