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
require_once(FUNC_DIR.'function_page_verify.php');

db_connect();

$newpage = new page_verify();
$newpage->check('_login_');

$res = sql_query("SELECT COUNT(*)
                    FROM users") or sqlerr(__FILE__, __LINE__);

$arr = mysql_fetch_row($res);

if ($arr[0] >= $max_users)
{
    error_message("info", "Sorry", "User Limit reached.  Please try again later.");
}

if (!mkglobal("wantusername:wantpassword:passagain:email"))
{
    die();
}

function validusername ($username)
{
    if ($username == "")
    {
        return false;
    }

    //-- The Following Characters Are Allowed In User Names --//
    $allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    for ($i = 0;
         $i < strlen($username);
         ++$i)
    {
        if (strpos($allowedchars, $username[$i]) === false)
        {
            return false;
        }
    }

    return true;
}

function isportopen ($port)
{
    $sd = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $errno, $errstr, 1);
    if ($sd)
    {
        fclose($sd);
        return true;
    }
    else
    {
        return false;
    }
}

if (empty($wantusername) || empty($wantpassword) || empty($email))
{
    error_message("error", "Signup Failed!", "Don't leave any fields blank.");
}

if (strlen($wantusername) > 12)
{
    error_message("error", "Signup Failed!", "Sorry, username is loo long (max 12 chars).");
}

if ($wantpassword != $passagain)
{
    error_message("error", "Signup Failed!", "Password did not match.  Try again.");
}

if (strlen($wantpassword) < 6)
{
    error_message("error", "Signup Failed!", "Sorry, Password is too short (min 6 chars).");
}

if (strlen($wantpassword) > 40)
{
    error_message("error", "Signup Failed!", "Sorry, Password is too long (max 40 chars)");
}

if ($wantpassword == $wantusername)
{
    error_message("error", "Signup Failed!", "Sorry, Password cannot be the same as Username.");
}

if (!validemail($email))
{
    error_message("error", "Signup Failed!", "The e-mail address is already in use");
}

if (!validusername($wantusername))
{
    error_message("error", "Signup Failed!", "Invalid Username.");
}

//-- Make Sure User Agrees To Everything... --//
if ($_GET["rulesverify"] != "yes" || $_GET["faqverify"] != "yes" || $_GET["ageverify"] != "yes")
{
    error_message("info", "Signup Failed!", "Sorry, your not qualified to become a member of this site.");
}

//-- Check If Email Addy Is Already In Use --//
$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*)
                                    FROM users
                                    WHERE email='$email'"))) or die(mysql_error());

if ($a[0] != 0)
{
    error_message("info", "Signup Failed!", "e-mail address is already in use");
}

$secret       = mksecret();
$wantpasshash = md5($secret.$wantpassword.$secret);
$editsecret   = (!$arr[0]  ? "" : $email_confirm ? mksecret() : "");

$ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, email, status, ".(!$arr[0] ? "class, " : "")."added)
                    VALUES (".implode(",", array_map("sqlesc", array($wantusername,
                                                                     $wantpasshash,
                                                                     $secret,
                                                                     $editsecret,
                                                                     $email,
                                                                     (!$arr[0] || !$email_confirm ? 'confirmed' : 'pending')))).", ".(!$arr[0] ? UC_MANAGER.", " : "")."'".get_date_time()."')");


if (!$ret)
{
    if (mysql_errno() == 1062)
    {
        error_message("info", "Signup Failed!", "Username already exists.");
    }
    error_message("info", "Signup Failed!", "borked");
}

$id = mysql_insert_id();

//write_log("User account $id ($wantusername) was created");

$psecret = md5($editsecret);

//-- Start Email Confirmation --//
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

if ($arr[0] || $email_confirm)
{
    mail($email, "$site_name user registration confirmation", $body, "From: $site_email", "-f$site_email");
}
else
{
    logincookie($id, $wantpasshash);
}

header("Refresh: 0; url=ok.php?type=".(!$arr[0] ? "manager" : $email_confirm ? ("signup&email=".urlencode($email)) :"confirm"));
//--End Email Confirmation --//

?>