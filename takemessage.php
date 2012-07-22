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
require_once(INCL_DIR.'function_page_verify.php');

if ($_SERVER["REQUEST_METHOD"] != "POST")
	error_message("error", "Error", "Method");

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->check('_sendmessage_');

$n_pms = isset($_POST["n_pms"]) ? $_POST["n_pms"] : false;

if ($n_pms)
{
	if (get_user_class() < UC_MODERATOR)
		error_message("warn", "Warning", "Permission Denied");

	$msg = trim($_POST["msg"]);

	if (!$msg)
		error_message("error", "Error", "Please enter something!");

	$subject   = trim($_POST['subject']);
	$sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);

	$from_is = $_POST['pmees'];

	$query = "INSERT INTO messages (sender, receiver, added, msg, subject, location, poster)
				SELECT $sender_id, u.id, '" . get_date_time() . "', " . sqlesc($msg) .", ". sqlesc($subject).", 1, $sender_id " . $from_is;

	sql_query($query) or sqlerr(__FILE__, __LINE__);

	$n = mysql_affected_rows();

	$comment  = isset($_POST['comment']) ? $_POST['comment'] : '';
	$snapshot = isset($_POST['snap']) ? $_POST['snap'] : '';

	// add a custom text or stats snapshot to comments in profile
	if ($comment || $snapshot)
	{
		$res = sql_query("SELECT u.id, u.uploaded, u.downloaded, u.modcomment ".$from_is) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) > 0)
		{
			$l = 0;

			while ($user = mysql_fetch_assoc($res))
			{
				unset($new);

				$old = $user['modcomment'];

				if ($comment)
					$new = $comment;

				if ($snapshot)
				{
					$new .= ($new?"\n":"") .
					"MMed, " . gmdate("Y-m-d") . ", " .
					"UL: " . mksizegb($user['uploaded']) . ", " .
					"DL: " . mksizegb($user['downloaded']) . ", " .
					"r: " . ratios($user['uploaded'],$user['downloaded'], false) . " - " .
					($_POST['sender'] == "system" ? "System" : $CURUSER['username']);
				}

				$new .= $old?("\n".$old):$old;

				sql_query("UPDATE users
							SET modcomment = " . sqlesc($new) . "
							WHERE id = " . $user['id']) or sqlerr(__FILE__, __LINE__);

				if (mysql_affected_rows())
					$l++;
			}
		}
	}
}
else
{
	$receiver	= isset($_POST["receiver"]) ? $_POST["receiver"] : false;
	$origmsg	= isset($_POST["origmsg"]) ? $_POST["origmsg"] : false;
	$save		= isset($_POST["save"]) ? $_POST["save"] : false;

	if(!isset($save)) $save="no";

	$returnto = isset($_POST["returnto"]) ? $_POST["returnto"] : '';

	if (!is_valid_id($receiver) || ($origmsg && !is_valid_id($origmsg)))
		error_message("error", "Error", "Invalid ID");

	$msg = trim($_POST["msg"]);

	if (!$msg)
		error_message("error", "Error", "Please enter something!");

		$save = ($save == 'yes') ? "yes" : "no";

	$res  = sql_query("SELECT acceptpms, email, notifs, UNIX_TIMESTAMP(last_access) as la
						FROM users
						WHERE id=$receiver") or sqlerr(__FILE__, __LINE__);

	$user = mysql_fetch_assoc($res);

	if (!$user)
		error_message("error", "Error", "No User with ID.");

	//Make sure recipient wants this message
	if (get_user_class() < UC_MODERATOR)
	{
		if ($user["acceptpms"] == "yes")
	{
	$res2 = sql_query("SELECT *
						FROM blocks
						WHERE userid=$receiver
						AND blockid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res2) == 1)
		error_message("info", "Refused", "This User has Blocked PMs from you.");
	}
	elseif ($user["acceptpms"] == "friends")
	{
		$res2 = sql_query("SELECT *
							FROM friends
							WHERE userid=$receiver
							AND friendid=" . $CURUSER["id"]) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res2) != 1)
			error_message("info", "Refused", "This User ONLY accepts PMs from Users in their Friends List.");
	}
	elseif ($user["acceptpms"] == "no")
		error_message("info", "Refused", "This User does NOT accept PMs.");
	}

	$subject = trim($_POST['subject']);
	sql_query("INSERT INTO messages (poster, sender, receiver, added, msg, subject, saved, location)
				VALUES(" . $CURUSER["id"] . ", " .  $CURUSER["id"] . ", $receiver, '" . get_date_time() . "', " .sqlesc($msg) . ", " . sqlesc($subject) . ", " . sqlesc($save) . ", 1)") or sqlerr(__FILE__, __LINE__);

	if (strpos($user['notifs'], '[pm]') !== false)
	{
	if (gmtime() - $user["la"] >= 300)
		{
			$username = $CURUSER["username"];

			$body = <<<EOD
			You have received a PM from $username!

			You can use the URL below to view the message (you may have to login).

			$site_url/messages.php

			--
			$site_name
EOD;
			@mail($user["email"], "You have received a PM from " . $username . "!",
			$body, "From: $site_email", "-f$site_email");
		}
	}
	$delete = isset($_POST["delete"]) ? $_POST["delete"] : '';

	if ($origmsg)
	{
	if ($delete == "yes")
	{
		// Make sure receiver of $origmsg is current user
		$res = sql_query("SELECT *
							FROM messages
							WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) == 1)
		{
			$arr = mysql_fetch_assoc($res);

			if ($arr["receiver"] != $CURUSER["id"])
				error_message("error", "Error", "This shouldn't happen.");

			if ($arr["saved"] == "no")
				sql_query("DELETE
							FROM messages
							WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);

				elseif ($arr["saved"] == "yes")
				sql_query("UPDATE messages
							SET location = '0'
							WHERE id=$origmsg") or sqlerr(__FILE__, __LINE__);
		}
	}
	if (!$returnto)
		$returnto = "messages.php";
	}
	if ($returnto)
	{
		header("Location: $returnto");
		die;
	}

site_header();

//header("refresh:1; $site_url/index.php");
display_message("success", "Succeeded", "Message was successfully sent!");

}

site_footer();

exit;

?>