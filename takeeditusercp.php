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
require_once(INCL_DIR.'function_page_verify.php');

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->check('_usercp_');

$action = isset($_POST["action"]) ? htmlspecialchars(trim($_POST["action"])) : '';

$updateset = array();

$urladd='';

//== Avatars stuffs
if ($action == "avatar")
{
	$title		= (isset($_POST['title']) ? $_POST['title'] : '');
	$avatars	= (isset($_POST['avatars']) ? $_POST['avatars'] : '');
	$avatar		= trim( urldecode( $_POST["avatar"] ) );

	if ( preg_match( "/^http:\/\/$/i", $avatar )

	OR preg_match( "/[?&;]/", $avatar )
	OR preg_match("#javascript:#is", $avatar )
	OR !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $avatar ) )

	{
		$avatar='';
	}

	$updateset[] = "title = '$title'";
	$updateset[] = "avatars = '$avatars'";
	$updateset[] = "avatar = " . sqlesc($avatar);

	$action = "avatar";
}

//== Signature stuffs
elseif ($action == "signature")
{
	$signature  = '';
	$signatures = (isset($_POST['signatures']) && $_POST["signatures"] != "" ? "yes" : "no");
	$signature  = trim( urldecode( $_POST["signature"] ) );

	if ( preg_match( "/^http:\/\/$/i", $signature )

	OR preg_match( "/[?&;]/", $signature )
	OR preg_match("#javascript:#is", $signature )
	OR !preg_match("#^https?://(?:[^<>*\"]+|[a-z0-9/\._\-!]+)$#iU", $signature ))

	$updateset[] = "signature = " . sqlesc($signature);
	$updateset[] = "signatures = '$signatures'";

	$info = $_POST["info"];

	if (isset($_POST["info"]) && (($info = $_POST["info"]) != $CURUSER["info"]))
	{
		$updateset[] = "info = " . sqlesc($info);
	}

	$action = "signature";
}

//== Security Stuffs
else if ($action == "security")
{
	if (!mkglobal("email:chpassword:passagain"))
		error_message("error", "Update Failed!", "Missing Form Data");

if ($chpassword != "")
{
	if (strlen($chpassword) > 40)
		error_message("error", "Update Failed!", "Sorry, Password is too long (max is 40 chars)");

	if ($chpassword != $passagain)
		error_message("error", "Update Failed!", "The Passwords didn't match. Try again.");

	$sec		= mksecret();
	$passhash	= md5($sec . $chpassword . $sec);

	$updateset[] = "secret = " . sqlesc($sec);
	$updateset[] = "passhash = " . sqlesc($passhash);

	logincookie($CURUSER['id'], $passhash);
}

if ($email != $CURUSER["email"])
{
	if (!validemail($email))
		error_message("error", "Update Failed!", "That doesn't look like a valid email address.");

	$r = sql_query("SELECT id
					FROM users
					WHERE email=" . sqlesc($email)) or sqlerr();

	if (mysql_num_rows($r) > 0)
		error_message("error", "Update Failed!", "The e-mail address is already in use.");

	$changedemail = 1;
}

if ($_POST['resetpasskey'] == 1)
{
	$res = sql_query("SELECT username, passhash, passkey
						FROM users
						WHERE id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res) or puke();

	$newpasskey = md5($arr['username'].get_date_time().$arr['passhash']);
	$modcomment = gmdate("Y-m-d") . " - Passkey ".$arr['passkey']." Reset to ".$newpasskey." by " . $CURUSER['username'] . ".\n" . $modcomment;

	$updateset[] = "passkey=".sqlesc($newpasskey);
}

$urladd = "";

if ($changedemail)
{
	$sec		= mksecret();
	$hash		= md5($sec . $email . $sec);
	$obemail	= urlencode($email);

	$updateset[] = "editsecret = " . sqlesc($sec);

	$thishost	= $_SERVER["HTTP_HOST"];
	$thisdomain = preg_replace('/^www\./is', "", $thishost);

	$body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on $thisdomain should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

$site_url/confirmemail.php/{$CURUSER["id"]}/$hash/$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

	mail($email, "$thisdomain profile change confirmation", $body, "From: $site_email", "-f$site_email");

	$urladd .= "&mailsent=1";
}
	$action  = "security";
}

//== Torrent stuffs
elseif ($action == "torrents")
{
	$pmnotif	= isset($_POST["pmnotif"]) ? $_POST["pmnotif"] : '';
	$emailnotif = isset($_POST["emailnotif"]) ? $_POST["emailnotif"] : '';
	$notifs		 = ($pmnotif == 'yes' ? "[pm]" : "");
	$notifs		.= ($emailnotif == 'yes' ? "[email]" : "");

	$r = @sql_query("SELECT id
						FROM categories") or sqlerr();

	$rows = mysql_num_rows($r);

	for ($i = 0; $i < $rows; ++$i)
	{
		$a = mysql_fetch_assoc($r);

		if (isset($_POST["cat{$a['id']}"]) && $_POST["cat{$a['id']}"] == 'yes')
			$notifs .= "[cat{$a['id']}]";
	}

	$updateset[] = "notifs = '$notifs'";

	if ($_POST['resetpasskey'] == 1)
	{
		$res = sql_query("SELECT username, passhash, passkey
							FROM users
							WHERE id=$CURUSER[id]") or sqlerr(__FILE__, __LINE__);

		$arr = mysql_fetch_assoc($res) or puke();

		$passkey= md5($arr['username'].get_date_time().$arr['passhash']);
		$updateset[] = "passkey = " . sqlesc($passkey);
	}

	$action = "torrents";
}

//== Personal stuffs
elseif ($action == "personal")
{
	$stylesheet = $_POST["stylesheet"];
	$dropmenu	= $_POST["dropmenu"];
	$stdmenu	= $_POST["stdmenu"];
	$country	= $_POST["country"];

	if ($dropmenu == 'no' && $stdmenu == 'no' || $dropmenu == 'yes' && $stdmenu == 'yes')
		error_message("info", "Info", "You must choose either the Drop Down Menu or Standard Menu!");

	$updateset[] = "dropmenu =  " . sqlesc($dropmenu);
	$updateset[] = "stdmenu =  " . sqlesc($stdmenu);
	$updateset[] = "torrentsperpage = " . min(100, 0 + $_POST["torrentsperpage"]);
	$updateset[] = "topicsperpage = " . min(100, 0 + $_POST["topicsperpage"]);
	$updateset[] = "postsperpage = " . min(100, 0 + $_POST["postsperpage"]);

	if (is_valid_id($stylesheet))
		$updateset[] = "stylesheet = '$stylesheet'";

	if (is_valid_id($country))
		$updateset[] = "country = $country";

		$action      = "personal";
	}

else if ($action == "pm")
{
	$acceptpms_choices	= array('yes' => 1, 'friends' => 2, 'no' => 3);
	$acceptpms			= (isset($_POST['acceptpms']) ? $_POST['acceptpms'] : 'all');

	if (isset($acceptpms_choices[$acceptpms]))
		$updateset[] = "acceptpms = " . sqlesc($acceptpms);

		$deletepms	= isset($_POST["deletepms"]) ? "yes" : "no";

		$updateset[] = "deletepms = '$deletepms'";

		$savepms	= (isset($_POST['savepms']) && $_POST["savepms"] != "" ? "yes" : "no");

		$updateset[] = "savepms = '$savepms'";

	$action = "pm";
}

@sql_query("UPDATE users
			SET " . implode(", ", $updateset) . "
			WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

header("Location: $site_url/usercp.php?edited=1&action=$action" . $urladd);

?>