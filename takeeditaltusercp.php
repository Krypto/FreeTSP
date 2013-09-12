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
require_once(FUNC_DIR.'function_page_verify.php');

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->check('_altusercp_');

$action = $_GET["action"];

$updateset = array();

if ($action == "avatar")
{
    $title   = $_POST["title"];
    $avatar  = $_POST["avatar"];
    $avatars = $_POST["avatars"];

    $updateset[] = "title = '$title'";
    $updateset[] = "avatar = ".sqlesc($avatar);
    $updateset[] = "avatars = '$avatars'";

    $action = "avatar";
}

else {
    if ($action == "signature")
    {
        $signature  = $_POST["signature"];
        $signatures = ($_POST["signatures"] != "" ? "yes" : "no");
        $info       = $_POST["info"];

        $updateset[] = "signature = ".sqlesc($signature);
        $updateset[] = "signatures = '$signatures'";
        $updateset[] = "info = ".sqlesc($info);

        $action = "signature";
    }

    else
    {
        if ($action == "security")
        {
            if (!mkglobal("email:chpassword:passagain"))
            {
                error_message("error", "Update Failed!", "Missing Form Data");
            }

            if ($chpassword != "")
            {
                if (strlen($chpassword) > 40)
                {
                    error_message("error", "Update Failed!", "Sorry, Password is Too Long (max is 40 chars)");
                }

                if ($chpassword != $passagain)
                {
                    error_message("error", "Update Failed!", "The Passwords Didn't Match. Try again.");
                }

                $sec      = mksecret();
                $passhash = md5($sec.$chpassword.$sec);

                $updateset[] = "secret = ".sqlesc($sec);
                $updateset[] = "passhash = ".sqlesc($passhash);

                logincookie($CURUSER["id"], $passhash);
            }

            if ($email != $CURUSER["email"])
            {
                if (!validemail($email))
                {
                    error_message("error", "Update Failed!", "That Doesn't Look Like A Valid Email Address.");
                }

                $r = sql_query("SELECT id
                                FROM users
                                WHERE email=".sqlesc($email)) or sqlerr();

                if (mysql_num_rows($r) > 0)

                {
                    error_message("error", "Update Failed!", "The E-mail Address Is Already In Use.");
                }

                $changedemail = 1;
            }

            if ($_POST['resetpasskey'] == 1)
            {
                $res = sql_query("SELECT username, passhash, passkey
                                    FROM users
                                    WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);

                $arr = mysql_fetch_assoc($res) or puke();

                $newpasskey = md5($arr['username'].get_date_time().$arr['passhash']);
                $modcomment = gmdate("Y-m-d")." - Passkey ".$arr['passkey']." Reset to ".$newpasskey." by ".$CURUSER['username'].".\n".$modcomment;

                $updateset[] = "passkey=".sqlesc($newpasskey);
            }

            $urladd = "";

            if ($changedemail)
            {
                $sec     = mksecret();
                $hash    = md5($sec.$email.$sec);
                $obemail = urlencode($email);

                $updateset[] = "editsecret = ".sqlesc($sec);

                $thishost   = $_SERVER["HTTP_HOST"];
                $thisdomain = preg_replace('/^www\./is', "", $thishost);

$body = <<<EOD
You have Requested that your User Profile (Username {$CURUSER["username"]})
on $thisdomain should be Updated with this Email Address ($email) as
user contact.

If you did not do this, please Ignore this Email. The person who entered your
Email Address had the IP Address {$_SERVER["REMOTE_ADDR"]}. Please Do Not reply.

To Complete the Update of your User Profile, please follow this link:

$site_url/confirmemail.php/{$CURUSER["id"]}/$hash/$obemail

Your New Email Address will appear in your Profile after you do this. Otherwise
your Profile will remain unchanged.
EOD;

                mail($email, "$thisdomain Profile Change Confirmation", $body, "From: $site_email", "-f$site_email");

                $urladd .= "&mailsent=1";
            }
            $action = "security";
        }

        //-- Torrent Stuffs --//
        elseif ($action == "torrents")
        {
            $pmnotif    = $_POST["pmnotif"];
            $emailnotif = $_POST["emailnotif"];
            $notifs     = ($pmnotif == 'yes' ? "[pm]" : "");
            $notifs     .= ($emailnotif == 'yes' ? "[email]" : "");

            $r = sql_query("SELECT id
                    FROM categories") or sqlerr();

            $rows = mysql_num_rows($r);

            for ($i = 0;
                 $i < $rows;
                 ++$i)
            {
                $a = mysql_fetch_assoc($r);

                if ($_POST["cat$a[id]"] == 'yes')
                {
                    $notifs .= "[cat$a[id]]";
                }
            }

            $updateset[] = "notifs = '$notifs'";

            if ($_POST['resetpasskey'] == 1)
            {
                $res = sql_query("SELECT username, passhash, passkey
                                    FROM users
                                    WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);

                $arr = mysql_fetch_assoc($res) or puke();

                $passkey = md5($arr['username'].get_date_time().$arr['passhash']);

                $updateset[] = "passkey = ".sqlesc($passkey);
            }

            $action = "torrents";
        }

        else
        {
            if ($action == "personal")
            {
                $stylesheet = $_POST["stylesheet"];
                $parked     = $_POST["parked"];
                $pcoff      = $_POST["pcoff"];
                $menu       = $_POST["menu"];
                $country    = $_POST["country"];

                $updateset[] = "parked = ".sqlesc($parked);
                $updateset[] = "pcoff = ".sqlesc($pcoff);
                $updateset[] = "menu = ".sqlesc($menu);
                $updateset[] = "torrentsperpage = ".min(100, 0 + $_POST["torrentsperpage"]);
                $updateset[] = "topicsperpage = ".min(100, 0 + $_POST["topicsperpage"]);
                $updateset[] = "postsperpage = ".min(100, 0 + $_POST["postsperpage"]);

                if (is_valid_id($stylesheet))
                {
                    $updateset[] = "stylesheet = '$stylesheet'";
                }

                if (is_valid_id($country))
                {
                    $updateset[] = "country = $country";
                }

                $action = "personal";
            }

            else
            {
                if ($action == "pm")
                {
                    $acceptpms = $_POST["acceptpms"];
                    $deletepms = ($_POST["deletepms"] != "" ? "yes" : "no");
                    $savepms   = ($_POST["savepms"] != "" ? "yes" : "no");

                    $updateset[] = "acceptpms = ".sqlesc($acceptpms);
                    $updateset[] = "deletepms = '$deletepms'";
                    $updateset[] = "savepms = '$savepms'";

                    $action = "";
                }
            }
        }
    }
}

sql_query("UPDATE users
            SET ".implode(",", $updateset)."
            WHERE id = ".$CURUSER['id']) or sqlerr(__FILE__, __LINE__);

header("Location: $site_url/altusercp.php?edited=1&action=$action".$urladd);

?>