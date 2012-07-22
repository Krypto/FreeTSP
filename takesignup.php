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

db_connect();

$newpage = new page_verify();
$newpage->check('_login_');

$res = sql_query("SELECT COUNT(*)
					FROM users") or sqlerr(__FILE__, __LINE__);

$arr = mysql_fetch_row($res);

if ($arr[0] >= $max_users)
	error_message("info", "Sorry", "User Limit reached.  Please try again later.");

if (!mkglobal("wantusername:wantpassword:passagain:email"))
	die();

function validusername($username)
{
	if ($username == "")
		return false;

	// The following characters are allowed in user names
	$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	for ($i = 0; $i < strlen($username); ++$i)
		if (strpos($allowedchars, $username[$i]) === false)
			return false;

	return true;
}

function isportopen($port)
{
	$sd = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $errno, $errstr, 1);
		if ($sd)
		{
			fclose($sd);
			return true;
		}
		else
			return false;
}

if (empty($wantusername) || empty($wantpassword) || empty($email))
		error_message("error", "Signup Failed!", "Don't leave any fields blank.");

if (strlen($wantusername) > 12)
		error_message("error", "Signup Failed!", "Sorry, username is loo long (max 12 chars).");

if ($wantpassword != $passagain)
		error_message("error", "Signup Failed!", "Password did not match.  Try again.");

if (strlen($wantpassword) < 6)
		error_message("error", "Signup Failed!", "Sorry, Password is too short (min 6 chars).");

if (strlen($wantpassword) > 40)
		error_message("error", "Signup Failed!", "Sorry, Password is too long (max 40 chars)");

if ($wantpassword == $wantusername)
		error_message("error", "Signup Failed!", "Sorry, Password cannot be the same as Username.");

if (!validemail($email))
		error_message("error", "Signup Failed!", "The e-mail address is already in use");

if (!validusername($wantusername))
		error_message("error", "Signup Failed!", "Invalid Username.");

// make sure user agrees to everything...
if ($_GET["rulesverify"] != "yes" || $_GET["faqverify"] != "yes" || $_GET["ageverify"] != "yes")
	error_message("info", "Signup Failed!", "Sorry, your not qualified to become a member of this site.");

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*)
									FROM users
									WHERE email='$email'"))) or die(mysql_error());

if ($a[0] != 0)
	error_message("info","Signup Failed!", "e-mail address is already in use");

$secret			= mksecret();
$wantpasshash	= md5($secret . $wantpassword . $secret);
$editsecret		= (!$arr[0]?"":mksecret());

$ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, email, status, ". (!$arr[0]?"class, ":"") ."added)
					VALUES (" . implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $email, (!$arr[0]?'confirmed':'pending')))). ", ". (!$arr[0]?UC_SYSOP.", ":""). "'". get_date_time() ."')");

if (!$ret)
{
	if (mysql_errno() == 1062)
		error_message("info","Signup Failed!", "Username already exists.");
		error_message("info","Signup Failed!", "borked");
}

$id = mysql_insert_id();

//write_log("User account $id ($wantusername) was created");

$psecret = md5($editsecret);

// Start email Confirmation
// Comment out if you do not want to use email to validate accounts
$body = <<<EOD
You have requested a new user account on $site_name and you have
specified this address ($email) as user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To confirm your user registration, you have to follow this link:

$site_url/confirm.php?id=$id&secret=$psecret

After you do this, you will be able to use your new account. If you fail to
do this, you account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using $site_name.
EOD;

if($arr[0])
	mail($email, "$site_name user registration confirmation", $body, "From: $site_email", "-f$site_email");
else
	logincookie($id, $wantpasshash);

header("Refresh: 0; url=ok.php?type=". (!$arr[0]?"sysop":("signup&email=" . urlencode($email))));

//End email Confirmation

// Uncomment if you do not want to use email to validate accounts
/*logincookie($id, $wantpasshash);

header("Refresh: 0; url=$site_url/confirm.php?id=$id&secret=$psecret");*/

?>