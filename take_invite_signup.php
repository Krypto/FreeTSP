<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** http://www.freetsp.info
** https://github.com/Krypto/FreeTSP
** Licence Info: GPL
** Copyright (C) 2010 FreeTSP v1.0
** A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
** Project Leaders: Krypto, Fireknight.
**/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');

db_connect(true);


	if (!mkglobal("wantusername:wantpassword:passagain:email:invite"))
	die();

	function validusername($username)
	{
		if ($username == "")
		return false;
		//-- The Following Characters Are Allowed In User Names --//
		$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		for ($i = 0; $i < strlen($username); ++$i)

		if (strpos($allowedchars, $username[$i]) === false)
		return false;
		return true;
	}

/*
	function isportopen($port)
	{
		global $HTTP_SERVER_VARS;
		$sd = @fsockopen($HTTP_SERVER_VARS["REMOTE_ADDR"], $port, $errno, $errstr, 1);
		if ($sd)
	{
		fclose($sd);
		return true;
	}

	else
	return false;

	}

	function isproxy()
	{
		$ports = array(80, 88, 1075, 1080, 1180, 1182, 2282, 3128, 3332, 5490, 6588, 7033, 7441, 8000, 8080, 8085, 8090, 8095, 8100, 8105, 8110, 8888, 22788);
		for ($i = 0; $i < count($ports); ++$i)
		if (isportopen($ports[$i])) return true;
		return false;
	}
*/

	if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($invite))
	{
		error_message("error", "Signup Failed!", "Don't leave any fields blank.");
	}

	if (strlen($wantusername) > 12)
	{
		error_message("error", "Signup Failed!", "Sorry, Username Is Too Long (max Is 12 Chars)");
	}

	if ($wantpassword != $passagain)
	{
		error_message("error", "Signup Failed!", "The Passwords Didn't Match! Must've Typoed. Try Again.");
	}

	if (strlen($wantpassword) < 6)
	{
		error_message("error", "Signup Failed!", "Sorry, Password Is Too Short (min Is 6 Chars)");
	}

	if (strlen($wantpassword) > 40)
	{
		error_message("error", "Signup Failed!", "Sorry, Password Is Too Long (max Is 40 Chars)");
	}

	if ($wantpassword == $wantusername)
	{
		error_message("error", "Signup Failed!", "Sorry, Password Cannot Be Same As User Name.");
	}

	if (!validemail($email))
	{
		error_message("error", "Signup Failed!", "That Doesn't Look Like A Valid Email Address.");
	}

	if (!validusername($wantusername))
	error_message("error", "Signup Failed!", "Invalid Username.");

	//-- Make Sure User Agrees To Everything... --//
	if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
	{
		error_message("error","Signup Failed!","Sorry, You're Not Qualified To Become A Member Of This Site.");
	}

	//-- Check If Email Addy Is Already In Use --//
	$a = (@mysql_fetch_row(@mysql_query('SELECT COUNT(*) FROM users WHERE email = '.sqlesc($email)))) or die(mysql_error());
	if ($a[0] != 0)
	{
		error_message("error", "Signup Failed!", 'The e-mail address <b>'.htmlspecialchars($email).'</b> is already in use.');
	}

	$select_inv = sql_query('SELECT sender, receiver, status
								FROM invite_codes
								WHERE code = '.sqlesc($invite)) or die(mysql_error());

	$rows = mysql_num_rows($select_inv);
	$assoc = mysql_fetch_assoc($select_inv);

	if ($rows == 0)
	{
		error_message("error", "Signup Failed!", "INvite Not Found.\nplease Request A Invite To One Of Our Members.");
	}

	if ($assoc["receiver"]!=0)
	{
		error_message("error", "Signup Failed!", "Invite Already Taken.\nplease Request A New One To Your Inviter.");
	}

/*
	//-- Do Simple Proxy Check --//
	if (isproxy())
	{
		error_message("error", "Signup Failed!", "You appear to be connecting through a proxy server. Your organization or ISP may use a transparent caching HTTP proxy. Please try and access the site on <a href=".$site_url.:81"login.php>Port 81</a> (this should bypass the proxy server). <p><b>Note:</b> if you run an Internet-accessible web server on the local machine you need to shut it down until the sign-up is complete.");
	}
*/

	$secret       = mksecret();
	$wantpasshash = md5($secret . $wantpassword . $secret);
	$editsecret   = (!$arr[0]?"":mksecret());

	$new_user = sql_query("INSERT INTO users (username, passhash, secret, editsecret, invitedby, email, ".(!$arr[0]?"class, ":"")."added)
								VALUES (" .implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, (int)$assoc['sender'], $email))).", ".(!$arr[0]?UC_USER.", ":""). "'".get_date_time()."')");

	if (!$new_user)
	{
		if (mysql_errno() == 1062)
		{
			error_message("error", "Signup Failed!", "Username Already Exists!");
		}
	}

	$id = mysql_insert_id();

	sql_query('UPDATE invite_codes
					SET receiver = '.sqlesc($id).', status = "Confirmed"
					WHERE sender = '.sqlesc((int)$assoc['sender']).'
					AND code = '.sqlesc($invite)) or sqlerr(__FILE__, __LINE__);

	write_log('User Account '.htmlspecialchars($wantusername).' was Created!');

	error_message('info','Signup Successfull', 'Your Inviter Needs To Confirm Your Account Before You Can Log In');

?>