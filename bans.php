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

if (get_user_class() < UC_MODERATOR)
	die;

$remove = isset($_GET['remove']) ? (int)$_GET['remove'] : 0;

if (is_valid_id($remove))
{
	@sql_query("DELETE
				FROM bans
				WHERE id='$remove'") or sqlerr();

	$removed = sprintf('Ban %s was removed by ', $remove);

	write_log("{$removed}".$CURUSER['id']." (".$CURUSER['username'].")");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $CURUSER['class'] >= UC_ADMINISTRATOR)
{
	$first  		= trim($_POST["first"]);
	$last   		= trim($_POST["last"]);
	$comment	= trim($_POST["comment"]);

	if (!$first || !$last || !$comment)
		error_message("error", "Error", "Missing Data.");

	$first	= ip2long($first);
	$last 	= ip2long($last);

	if ($first == -1 || $first === false || $last == -1 || $last === false)
		error_message("error", "Error", "Bad IP Address.");

	$comment	= sqlesc($comment);
	$added  		= sqlesc(get_date_time());

	sql_query("INSERT INTO bans (added, addedby, first, last, comment)
				VALUES($added, {$CURUSER['id']}, $first, $last, $comment)") or sqlerr(__FILE__, __LINE__);

	header("Location: $site_url$_SERVER[REQUEST_URI]");
	die;
}

$res = sql_query("SELECT first, last, added, addedby, comment, id
                 FROM bans
                 ORDER BY added DESC") or sqlerr();

site_header("Bans");

print("<h1>Current Bans</h1>\n");

if (mysql_num_rows($res) == 0)
	display_message("info", "Sorry.", "Nothing Found!");

else
{
	print("<table align='center' border='1' cellspacing='0' cellpadding='5'>");
	print("<tr>
			<td class='colhead'>Added</td>
			<td class='colhead' align='left'>First IP</td>
			<td class='colhead' align='left'>Last IP</td>
			<td class='colhead' align='left'>By</td>
			<td class='colhead' align='left'>Comment</td>
			<td class='colhead'>Remove</td>
		</tr>");

	while ($arr = mysql_fetch_assoc($res))
	{
		$r2 = sql_query("SELECT username
						FROM users
						WHERE id={$arr['addedby']}") or sqlerr();

		$a2 = mysql_fetch_assoc($r2);

		$arr["first"] = long2ip($arr["first"]);
		$arr["last"]  = long2ip($arr["last"]);

		print("<tr>
				<td class='rowhead'>{$arr['added']}</td>
				<td class='rowhead' align='left'>{$arr['first']}</td>
				<td class='rowhead' align='left'>{$arr['last']}</td>
				<td class='rowhead' align='left'><a href='userdetails.php?id={$arr['addedby']}'>{$a2['username']}</a></td>
				<td class='rowhead' align='left'>".htmlentities($arr['comment'], ENT_QUOTES)."</td>
				<td class='rowhead'><a href='bans.php?remove={$arr['id']}'>Remove</a></td>
			</tr>");
	}
print("</table>");
}

if (get_user_class() >= UC_MODERATOR)
{
	print("<h2>Add Ban</h2>\n");
	print("<form method='post' action='bans.php'>\n");
	print("<table align='center' border='1' cellspacing='0' cellpadding='5'>");

	print("<tr>
			<td class='rowhead'><label for='first'>First IP</label></td>
			<td class='rowhead'><input type='text' name='first' id='first' size='40' /></td>
		</tr>");

	print("<tr>
			<td class='rowhead'><label for='last'>Last IP</label></td>
			<td class='rowhead'><input type='text' name='last' id='last' size='40' /></td>
		</tr>");

	print("<tr>
			<td class='rowhead'><label for='comment'>Comment</label></td>
			<td class='rowhead'><input type='text' name='comment' id='comment' size='40' /></td>
		</tr>");

	print("<tr>
			<td class='std' colspan='2' align='center'><input type='submit' class='btn' value='Okay' /></td>
		</tr>");

	print("</table>\n");
	print("</form><br />");
}

site_footer();

?>