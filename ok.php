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

db_connect();

if (!mkglobal("type"))
	die();

    $type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type == "signup" && mkglobal("email"))
{
	site_header("User Signup");

	display_message("success", "Signup Successful!",
	"A confirmation email has been sent to the address you specified (" . htmlspecialchars($email) . "). You need to read and respond to this email before you can use your account. If you don't do this, the new account will be deleted automatically after a few days.");
	site_footer();
}
elseif ($type == "sysop")
{
	site_header("Sysop Account Activation");

	display_message("success", "Success", "Sysop Account Successfully Activated!");

	if (isset($CURUSER))
	{
		display_message("info", "Info", "Your account has been activated! You have been automatically logged in. You can now continue to the <strong><a href='index.php'>Main Page</a></strong> and start using your account.");
	}
	else
	{
		display_message("info", "Info", "Your account has been Activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href='login.php'>Log in</a> and try again.");
	}
		site_footer();
}

elseif ($type == "confirmed")
{
	site_header("Already Confirmed");

	display_message("info", "Confirmed", "This user account has already been confirmed. You can proceed to <a href='login.php'>Log in</a> with it.");

	site_footer();
}

elseif ($type == "confirm")
{
	if (isset($CURUSER))
	{
		site_header("Signup Confirmation");

		display_message("success", "Account Successfully Confirmed!", "Your account has been Activated! You have been automatically logged in. You can now continue to the <strong><a href='/'>Main Page</a></strong> and start using your account.<br/><br/>Before you start using <?php echo $site_name?> we urge you to read the <strong><a href='rules.php'>RULES</a></strong> and the <strong><a href=\"faq.php\">FAQ</a></strong>.");

		site_footer();
	}
	else
	{
		site_header("Signup Confirmation");

		display_message("success", "Account Successfully Confirmed!", "Your account has been activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href='login.php'>log in</a> and try again.");

		site_footer();
	}
}
else

die();

?>