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

db_connect();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $email = trim($_POST["email"]);

    if (!validemail($email))
    {
        error_message("error", "Error", "You MUST enter an email address");
    }

    $res = sql_query("SELECT *
                        FROM users
                        WHERE email=".sqlesc($email)."
                        LIMIT 1") or sqlerr();

    $arr = mysql_fetch_assoc($res) or error_message("error", "Error", "The email address was NOT found in the Database.\n");
    $sec = mksecret();

    sql_query("UPDATE users
                SET editsecret=".sqlesc($sec)."
                WHERE id=".$arr["id"]) or sqlerr();

    if (!mysql_affected_rows())
    {
        error_message("error", "Database Error", "Please contact an Administrator about this.");
    }

    //$hash = md5($sec.$email.$arr["passhash"].$sec);
    $hash = md5($sec.$arr["email"].$arr["passhash"] . $sec);

$body = <<<EOD
Someone, hopefully you, requested that the password for the account
associated with this email address ($email) be reset.

The request originated from {$_SERVER["REMOTE_ADDR"]}.

If you did not do this ignore this email. Please do not reply.


Should you wish to confirm this request, please follow this link:

$site_url/recover.php?id={$arr["id"]}&secret=$hash


After you do this, your password will be reset and emailed back
to you.

--
$site_name
EOD;

    @mail($arr["email"], "$site_name password reset confirmation", $body, "From: $site_email", "-f$site_email")
        or
        error_message("error", "Error", "Unable to send mail. Please contact an Administrator about this Error.");
        error_message("success", "Success", "A Confirmation email has been mailed.\n"." Please allow a few minutes for the mail to arrive.  Also check your Spam/Junk Folders.");
}
elseif ($_GET)
{
    $id  = 0 + $_GET["id"];
    $md5 = $_GET["secret"];

    if (!$id)
    {
        httperr();
    }

    $res = sql_query("SELECT username, email, passhash, editsecret
                        FROM users
                        WHERE id = $id");

    $arr = mysql_fetch_assoc($res) or httperr();
    $email = $arr["email"];
    $sec   = hash_pad($arr["editsecret"]);

    if (preg_match('/^ *$/s', $sec))
    {
        httperr();
    }

    if ($md5 != md5($sec.$email.$arr["passhash"].$sec))
    {
        httperr();
    }

    //-- Generate New Password --//
    $chars       = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $newpassword = "";

    for ($i = 0; $i < 10; $i++)
    {
        $newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    $sec         = mksecret();
    $newpasshash = md5($sec.$newpassword.$sec);

    sql_query("UPDATE users
                SET secret = ".sqlesc($sec).", editsecret='', passhash = ".sqlesc($newpasshash)."
                WHERE id = $id
                AND editsecret = ".sqlesc($arr["editsecret"]));

    if (!mysql_affected_rows())
    {
        error_message("error", "Error", "Unable to Update User Data. Please contact an Administrator about this Error.");
    }

$body = <<<EOD
As per your request we have generated a new password for your account.

Here is the information we now have on file for this account:

User name: {$arr["username"]}
Password: $newpassword

You may login at $site_url/login.php

--
$site_name
EOD;

    @mail($email, "$site_name account details", $body, "From: $site_email", "-f$site_email")
        or
        error_message("error", "Error", "Unable to send mail. Please contact an Administrator about this Error.");
        error_message("success", "Success", "The New Account details have been mailed to <span style='font-weight:bold;'>$email</span>.\n"."Please allow a few minutes for the mail to arrive.  Also check your Spam/Junk Folders.");
}

?>