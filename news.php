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

db_connect();
logged_in();

if (get_user_class() < UC_ADMINISTRATOR)
	error_message("warn", "Warning", "Access Denied!");

$action = isset($_GET["action"]) ?$_GET["action"] : '';

//--Delete News Item

if ($action == 'delete')
{
	$newsid = isset($_GET['newsid']) ? (int)$_GET["newsid"] : 0;

	if (!is_valid_id($newsid))
		error_message("error", "Error", "Invalid News Item ID - Code 1");

	$returnto = isset($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : '';

	$sure = isset($_GET["sure"]) ? (int)$_GET['sure'] : 0;
	if (!$sure)

		error_message("warn", "Warning", "<a href='?action=delete&amp;newsid=$newsid&amp;returnto=$returnto&amp;sure=1'>Do you really want to Delete a News Item?  Click if you are sure?</a>");

		global $CURUSER;

		sql_query("DELETE
					FROM news
					WHERE id = $newsid AND userid = $CURUSER[id]");

		@unlink(ROOT_DIR."cache/news.txt");

	if ($returnto != "")
		header("Location: $returnto");
	else
		$warning = "News Item was Deleted Successfully.";
}

//--Add News Item

if ($action == 'add')
{
	$body = isset($_POST["body"]) ? (string)$_POST["body"] : 0;

	if (!$body)
		error_message("error", "Error", "The News Item cannot be Empty!");

	$body = sqlesc($body);

	$added = isset($_POST["added"]) ?$_POST["added"] : 0;

	if (!$added)
		$added = sqlesc(get_date_time());

		@sql_query("INSERT INTO news (userid, added, body)
					VALUES ({$CURUSER['id']}, $added, $body)") or sqlerr(__FILE__, __LINE__);

		@unlink(ROOT_DIR."cache/news.txt");

	header("refresh:1; $site_url/index.php");

	if (mysql_affected_rows() == 1)
		$warning = error_message("success", "Success", "News Item was Added Successfully.");
	else
		error_message("error", "Error", "Something weird just happened.");
}

//-- Edit News Item

if ($action == 'edit')
{
	$newsid = isset($_GET["newsid"]) ? (int)$_GET["newsid"] : 0;

	if (!is_valid_id($newsid))
		error_message("error", "Error", "Invalid News item ID - Code 2.");

	$res = @sql_query("SELECT *
						FROM news
						WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) != 1)
		error_message("error", "Error Message", "No News item with that ID.");

	$arr = mysql_fetch_assoc($res);

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$body = isset($_POST['body']) ? $_POST['body'] : '';

		if ($body == "")
			error_message("error", "Error", "Body cannot be Empty!");

		$body = sqlesc($body);

		$editedat = sqlesc(get_date_time());

		@sql_query("UPDATE news
					SET body=$body
					WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

		@unlink(ROOT_DIR."cache/news.txt");

		header("refresh:1; $site_url/index.php");
			$warning = error_message("success", "Success", "News item was Edited Successfully.");
	}
	else
	{
		site_header();
		echo("<h1>Edit News Item</h1>\n");
		echo("<form method='post' name='ednews' action='?action=edit&amp;newsid=$newsid'>\n");
		echo("<table width='100%' border='1' cellspacing='0' cellpadding='5'>\n");
		echo("<tr><td class='std'><input type='hidden' name='returnto' value='$returnto' /></td></tr>\n");
		echo("<tr><td class='std' style='padding: 0px'>".textbbcode("ednews", "body", htmlspecialchars($arr["body"])) . "</td></tr>\n");
		echo("<tr><td class='std' align='center'><input type='submit' class='btn' value='Okay' /></td></tr>\n");
		echo("</table>\n");
		echo("</form>\n");
		echo("<br />");
		site_footer();
		die;
	}
}

//-- Other Actions and follow-up

site_header("Site News");
echo("<h1>Submit News Item</h1>\n");

if ($warning)
	echo("<p><span style='font-size: small;'>($warning)</span></p>");
	echo("<form method='post' name='news' action='?action=add'>\n");
	echo("<table width='100%' border='1' cellspacing='0' cellpadding='5'>\n");
	echo("<tr><td class='std' style='padding: 10px'>".textbbcode("news", "body", htmlspecialchars($arr["body"])) . "\n");
	echo("<br /><br /><div align='center'><input type='submit' class='btn' value='Okay' /></div></td></tr>\n");
	echo("</table></form><br /><br />\n");

$res = @sql_query("SELECT *
					FROM news
					ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) > 0)
{
	begin_frame();

	while ($arr = mysql_fetch_assoc($res))
	{
		$newsid = $arr["id"];
		$body	= format_comment($arr["body"]);
		$userid = $arr["userid"];
		$added	= $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

		$res2 = @sql_query("SELECT username, donor
							FROM users
							WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

		$arr2 = mysql_fetch_assoc($res2);

		$postername = $arr2["username"];

		if ($postername == "")
			$by = "unknown[$userid]";
		else
			$by = "<a href='userdetails.php?id=$userid'><span style='font-weight:bold;'>$postername</span></a>" .
			($arr2["donor"] == "yes" ? "<img src='{$image_dir}star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />" : "");

		echo("<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>");
		echo("$added&nbsp;---&nbsp;by&nbsp;$by");
		echo(" - [<a href='?action=edit&amp;newsid=$newsid'><span style='font-weight:bold;'>Edit</span></a>]");
		echo(" - [<a href='?action=delete&amp;newsid=$newsid'><span style='font-weight:bold;'>Delete</span></a>]");
		echo("</td></tr></table>\n");

		begin_table(true);
		echo("<tr valign='top'><td class='comment'>$body</td></tr>\n");
		end_table();
	}
	end_frame();
	echo("<br />");
}
else
	error_message("info", "Sorry", "No News Available!");

site_footer();
die;

?>