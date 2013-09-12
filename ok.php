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
require_once(FUNC_DIR.'function_bbcode.php');

db_connect();

if (!mkglobal("type"))
{
    die();
}

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type == "signup" && mkglobal("email"))
{
    site_header("User Signup");

    display_message("success", "Signup Successful!", "A Confirmation email has been sent to the Address you specified (".htmlspecialchars($email)."). You need to Read and Respond to this email before you can use your Account. If you don't do this, the New Account will be Deleted Automatically after a few days.");

    site_footer();
}
elseif ($type == "sysop")
{
    site_header("Sysop Account Activation");

    display_message("success", "Success", "Sysop Account Successfully Activated!");

    if (isset($CURUSER))
    {
        display_message("info", "Info", "Your Account has been Activated! You have been Automatically Logged In. You can now continue to the <strong><a href='index.php'>Main Page</a></strong> and start using your account.");
    }
    else
    {
        display_message("info", "Info", "Your Account has been Activated! However, it appears that you could NOT be Logged In Automatically. A possible reason is that you Disabled Cookies in your Browser. You have to Enable Cookies to USE your Account. Please DO that and then <a href='login.php'>Log In</a> and try again.");
    }
    site_footer();
}

elseif ($type == "confirmed")
{
    site_header("Already Confirmed");

    display_message("info", "Confirmed", "This User Account has ALREADY been Confirmed. You can proceed to <a href='login.php'>Log In</a> with it.");

    site_footer();
}

elseif ($type == "confirm")
{
    if (isset($CURUSER))
    {
        site_header("Signup Confirmation");

        display_message("success", "Account Successfully Confirmed!", "Your Account has been Activated! You have been Automatically Logged In. You can now continue to the <strong><a href='/'>Main Page</a></strong> and START using your Account.<br/><br/>Before you start using <?php echo $site_name?> we URGE you to READ the <strong><a href='rules.php'>RULES</a></strong> and the <strong><a href=\"faq.php\">FAQ</a></strong>.");

        site_footer();
    }
    else
    {
        site_header("Signup Confirmation");

        display_message("success", "Account Successfully Confirmed!", "Your Account has been Activated! However, it appears that you could not be Logged In Automatically. A possible reason is that you Disabled Cookies in your Browser. You have to Enable Cookies to USE your Account. Please DO that and then <a href='login.php'>log In</a> and try again.");

        site_footer();
    }
}
else
{
    die();
}

?>