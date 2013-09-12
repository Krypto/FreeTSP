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

db_connect(false);
logged_in();

$newpage = new page_verify();
$newpage->check('_modtask_');

if ($CURUSER['class'] < UC_MODERATOR)
{
    die();
}

//-- Correct Call To Script --//
if ((isset($_POST['action'])) && ($_POST['action'] == "edituser"))
{
    // Set user id
    if (isset($_POST['userid']))
    {
        $userid = $_POST['userid'];
    }
    else
    {
        die();
    }

    //-- And Verify... --//
    if (!is_valid_id($userid))
    {
        error_message("error", "Error", "Bad User ID.");
    }

    //-- Handle CSRF (Modtask Posts Form Other Domains, Especially To Update Class) --//
    require_once(FUNC_DIR.'function_user_validator.php');

    if (!validate($_POST[validator], "ModTask_$userid"))
    {
        die ("Invalid");
    }

    // Fetch Current User Data... --//
    $res = sql_query("SELECT *
                        FROM users
                        WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);

    $user = mysql_fetch_assoc($res) or sqlerr(__FILE__, __LINE__);

    //--Used In Writing To Staff Log --//
    $username = $user["username"];

    //-- Check To Make Sure Your Not Editing Someone Of The Same Or Higher Class --//
    if ($CURUSER["class"] <= $user['class'] && ($CURUSER['id'] != $userid && $CURUSER["class"] < UC_ADMINISTRATOR))
    {
        error_message("warn", "Warning", "You cannot edit someone of the same or higher class..  Action Logged");
    }

    $updateset = array();

    $modcomment = (isset($_POST['modcomment']) && $CURUSER['class'] == UC_SYSOP) ? $_POST['modcomment'] : $user['modcomment'];

    //-- Set Class --//
    if ((isset($_POST['class'])) && (($class = $_POST['class']) != $user['class']))
    {
        if ($class >= UC_MANAGER || ($class >= $CURUSER['class']) || ($user['class'] >= $CURUSER['class']))
        {
            error_message("error", "User Error", "Please try again");
        }

        if (!is_valid_user_class($class) || $CURUSER["class"] <= $_POST['class'])
        {
            error_message("error", "Error", "Bad Class");
        }

        //-- Notify User --//
        $what  = ($class > $user['class'] ? "Promoted" : "Demoted");
        $msg   = sqlesc("You have been $what to '".get_user_class_name($class)."' by ".$CURUSER['username']);
        $added = sqlesc(get_date_time());

        sql_query("INSERT INTO messages (sender, receiver, msg, added)
                    VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        $updateset[] = "class = ".sqlesc($class);

        $modcomment = gmdate("Y-m-d")." - $what to '".get_user_class_name($class)."' by $CURUSER[username].\n".$modcomment;
    }

    //-- Invite Rights --//
    if ((isset($_POST['invite_rights'])) && (($invite_rights = $_POST['invite_rights']) != $user['invite_rights']))
    {
        if ($invite_rights == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Invite Rights Enabled by ".htmlspecialchars($CURUSER['username']).".\n".$modcomment;
            $msg        = sqlesc("Your invite rights have been given back by ".htmlspecialchars($CURUSER['username']).".You can Invite Users again.");
            $subject    = sqlesc("Invite Rights");
            $added      = sqlesc(get_date_time());

            sql_query("INSERT INTO messages (sender, receiver, msg, added, subject)
                        VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
        }

        if ($invite_rights == 'no')
        {
            $modcomment = gmdate("Y-m-d")." - Invite Rights Disabled by ".htmlspecialchars($CURUSER['username']).".\n".$modcomment;
            $msg        = sqlesc("Your Invite Rights have been Removed by ".htmlspecialchars($CURUSER['username']).", probably because you Invited a Bad User.");
            $subject    = sqlesc("Invite Rights");
            $added      = sqlesc(get_date_time());

            sql_query("INSERT INTO messages (sender, receiver, msg, added, subject)
                        VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
        }

        $updateset[] = "invite_rights = ".sqlesc($invite_rights);
    }

    //-- Change Invite Amount --//
    if ((isset($_POST['invites'])) && (($invites = $_POST['invites']) != ($curinvites = $user['invites'])))
    {
    $modcomment = gmdate("Y-m-d")." - Invite Amount changed to ".$invites." from ".$curinvites." by ".htmlspecialchars($CURUSER['username']).".\n".$modcomment;
    $updateset[] = "invites = ".sqlesc($invites);
    }

    //-- Clear Warning - Code Not Called For Setting Warning --//
    if (isset($_POST['warned']) && (($warned = $_POST['warned']) != $user['warned']))
    {
        $updateset[] = "warned = ".sqlesc($warned);
        $updateset[] = "warneduntil = '0000-00-00 00:00:00'";

        if ($warned == 'no')
        {
            $modcomment = gmdate("Y-m-d")." - Warning Removed by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("Your Warning has been Removed by ".$CURUSER['username'].".");
            $added      = sqlesc(get_date_time());

            sql_query("INSERT INTO messages (sender, receiver, msg, added)
                        VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
        }
    }

    //-- Set Warning - Time Based --//
    if (isset($_POST['warnlength']) && ($warnlength = 0 + $_POST['warnlength']))
    {
        unset($warnpm);
        if (isset($_POST['warnpm']))
        {
            $warnpm = $_POST['warnpm'];
        }

        if ($warnlength == 255)
        {
            $modcomment  = gmdate("Y-m-d")." - Warned by ".$CURUSER['username'].".\nReason: $warnpm\n".$modcomment;
            $msg         = sqlesc("You have received a Rules Warning from ".$CURUSER['username'].($warnpm ? "\n\nReason: $warnpm" : ""));
            $updateset[] = "warneduntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $warneduntil = get_date_time(gmtime() + $warnlength * 604800);
            $dur         = $warnlength." week".($warnlength > 1 ? "s" : "");
            $msg         = sqlesc("You have received a $dur Rules Warning from ".$CURUSER['username'].($warnpm ? "\n\nReason: $warnpm" : ""));
            $modcomment  = gmdate("Y-m-d")." - Warned for $dur by ".$CURUSER['username'].".\nReason: $warnpm\n".$modcomment;
            $updateset[] = "warneduntil = ".sqlesc($warneduntil);
        }
        $added = sqlesc(get_date_time());

        sql_query("INSERT INTO messages (sender, receiver, msg, added)
                    VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        $updateset[] = "warned = 'yes'";
    }

    //-- Clear Donor - Code Not Called For Setting Donor --//
    if (isset($_POST['donor']) && (($donor = $_POST['donor']) != $user['donor']))
    {
        $updateset[] = "donor = ".sqlesc($donor);
        //$updateset[] = "donoruntil = '0000-00-00 00:00:00'";
        if ($donor == 'no')
        {
            $modcomment = gmdate("Y-m-d")." - Donor Status Removed by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("Your Donator Status has Expired.");
            $added      = sqlesc(get_date_time());

            sql_query("INSERT INTO messages (sender, receiver, msg, added)
                        VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
        }
        elseif ($donor == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Donor Status Added by ".$CURUSER['username'].".\n".$modcomment;
        }
    }

    //-- Set Donor - Time Based --//
/*
    if ((isset($_POST['donorlength'])) && ($donorlength = 0 + $_POST['donorlength']))
    {
        if ($donorlength == 255)
        {
            $modcomment  = gmdate("Y-m-d")." - Donor Status set by ".$CURUSER['username'].".\n".$modcomment;
            $msg         = sqlesc("You have received Donor Status from ".$CURUSER['username']);
            $updateset[] = "donoruntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $donoruntil = get_date_time(gmtime() + $donorlength * 604800);
            $dur        = $donorlength." week".($donorlength > 1 ? "s" : "");
            $msg        = sqlesc("You have received Donator Status for $dur from ".$CURUSER['username']);
            $modcomment = gmdate("Y-m-d")." - Donator Status set for $dur by ".$CURUSER['username']."\n".$modcomment;

            $updateset[] = "donoruntil = ".sqlesc($donoruntil);
        }
        $added = sqlesc(get_date_time());

        sql_query("INSERT INTO messages (sender, receiver, msg, added)
                    VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        $updateset[] = "donor = 'yes'";
    }
*/

    //-- Change Users Sig --//
    if ((isset($_POST['signature'])) && (($signature = $_POST['signature']) != ($cursignature = $user['signature'])))
    {
        $modcomment = gmdate("Y-m-d")." - Signature changed from '".$cursignature."' to '".$signature."' by ".$CURUSER['username'].".\n".$modcomment;

        $updateset[] = "signature = ".sqlesc($signature);

        write_log("User ID <a href=userdetails.php?id=$userid>$userid</a> had there Signature changed from $cursignature to $signature by <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>");
    }

    //-- Enable / Disable --//
    if ((isset($_POST['enabled'])) && (($enabled = $_POST['enabled']) != $user['enabled']))
    {
        if ($enabled == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Enabled by ".$CURUSER['username'].".\n".$modcomment;
        }
        else
        {
            $modcomment = gmdate("Y-m-d")." - Disabled by ".$CURUSER['username'].".\n".$modcomment;
        }

        $updateset[] = "enabled = ".sqlesc($enabled);
    }

    //-- Park / Un-Park --//
    if ((isset($_POST['parked'])) && (($parked = $_POST['parked']) != $user['parked']))
    {
        if ($parked == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Account Parked by ".$CURUSER['username'].".\n".$modcomment;
        }
        else
        {
            $modcomment = gmdate("Y-m-d")." - Account Un-Parked by ".$CURUSER['username'].".\n".$modcomment;
        }

        $updateset[] = "parked = ".sqlesc($parked);
    }

    //-- Forum Permission - Enable --//
    if ((isset($_POST['forumpos'])) && (($forumpos = $_POST['forumpos']) != $user['forumpos']))
    {
        if ($forumpos == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Forum Permission - Enabled by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("Your Forum Privilege has been returned to you. \n Please be more careful in the future.");
            $added      = sqlesc(get_date_time());
            $subject    = sqlesc("Your Forum Status");

            sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                        VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

            write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Forum Privilage returned by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");
        }
            $updateset[] = "forumpos = ".sqlesc($forumpos);
    }

    //-- Set Forum Permission - Disabled - Time based --//
    if (isset($_POST['forumposuntillength']) && ($forumposuntillength = 0 + $_POST['forumposuntillength']))
    {
        unset($forumposuntilpm);

        if (isset($_POST['forumposuntilpm']))
        {
            $forumposuntilpm = $_POST['forumposuntilpm'];
        }

        if ($forumposuntillength == 255)
        {
            $modcomment  = gmdate("Y-m-d")." - Forum Disabled By - ".$CURUSER['username'].".\nReason: $forumposuntilpm\n".$modcomment;
            $msg         = sqlesc("Your Forum Privilage has been Removed - until further notice. \nPlease contact a member of Staff to try to resolve this issue");

            $updateset[] = "forumposuntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $forumposuntil = get_date_time(gmtime() + $forumposuntillength * 604800);
            $dur           = $forumposuntillength." week".($forumposuntillength > 1 ? "s" : "");
            $msg           = sqlesc("Your Forum Privilage has been Removed for - $dur.".$CURUSER['username'].($forumposuntilpm ? "\n\nReason: $forumposuntilpm" : ""));

            $modcomment  = gmdate("Y-m-d")." - Forum Disabled for $dur by ".$CURUSER['username'].".\nReason: $forumposuntilpm\n".$modcomment;
            $updateset[] = "forumposuntil = ".sqlesc($forumposuntil);
        }

        $added   = sqlesc(get_date_time());
        $subject = sqlesc("Your Forum Status");

        sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                    VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Forum Privilage Removed for $dur by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");

        $updateset[] = "forumpos = 'no'";
    }

    //-- Change Custom Title --//
    if ((isset($_POST['title'])) && (($title = $_POST['title']) != ($curtitle = $user['title'])))
    {
        $modcomment = gmdate("Y-m-d")." - Custom Title changed to '".$title."' from '".$curtitle."' by ".$CURUSER['username'].".\n".$modcomment;

        $updateset[] = "title = ".sqlesc($title);
    }

    //-- Change Members Username --//
    if ((isset($_POST['username'])) && (($username = $_POST['username']) != ($curusername = $user['username'])))
    {
        $modcomment = gmdate("Y-m-d")." - Username changed to '".$username."' from '".$curusername."' by ".$CURUSER['username'].".\n".$modcomment;

        $updateset[] = "username = ".sqlesc($username);
    }

    //-- Change Members Email --//
    if ((isset($_POST['email'])) && (($email = $_POST['email']) != ($curemail = $user['email'])))
    {
        $modcomment = gmdate("Y-m-d")." - Email changed to '".$email."' from '".$curemail."' by ".$CURUSER['username'].".\n".$modcomment;

        $updateset[] = "email = ".sqlesc($email);
    }

    //-- Change Users Info --//
    if ((isset($_POST['info'])) && (($info = $_POST['info']) != ($curinfo = $user['info'])))
    {
        $modcomment = gmdate("Y-m-d")." - Info changed from '".$curinfo."' to '".$info."' by ".$CURUSER['username'].".\n".$modcomment;

        $updateset[] = "info = ".sqlesc($info);
    }

/*
    The Following Code Will Place The Old Passkey In The Mod Comment And Create A New Passkey.
    This Is Good Practice As It Allows Usersearch To Find Old Passkeys By Searching The Mod Comments Of Members.
*/

    //-- Reset Passkey --//
    if ((isset($_POST['resetpasskey'])) && ($_POST['resetpasskey']))
    {
        $newpasskey = md5($user['username'].get_date_time().$user['passhash']);
        $modcomment = gmdate("Y-m-d")." - Passkey ".sqlesc($user['passkey'])." Reset to ".sqlesc($newpasskey)." by ".$CURUSER['username'].".\n".$modcomment;

        $updateset[] = "passkey=".sqlesc($newpasskey);
    }

/*
    This Code Is For Use With The Safe Mod Comment Modification. If You Have Installed The Safe Mod Comment Mod, Then Uncomment This Section...
*/

    //-- Add Comment to ModComment --//
    if ((isset($_POST['addcomment'])) && ($addcomment = trim($_POST['addcomment'])))
    {
        $modcomment = gmdate("Y-m-d")." - ".$addcomment." - ".$CURUSER['username'].".\n".$modcomment;
    }

    //-- Upload Permission - Enable --//
    if ((isset($_POST['uploadpos'])) && (($uploadpos = $_POST['uploadpos']) != $user['uploadpos']))
    {
        if ($uploadpos == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Upload Permission - Enabled by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("\nYour Upload Privilege has been returned to you. \n Please be more careful in the future.");
            $added      = sqlesc(get_date_time());
            $subject    = sqlesc("Your Upload Status");

            sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                        VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

            write_stafflog("<a href='userdetails.php?id=$userid'>$username</a> had their Upload Privilage returned by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");
        }
            $updateset[] = "uploadpos = ".sqlesc($uploadpos);
    }

    //-- Set Upload Permission - Disabled - Time Based --//
    if (isset($_POST['uploadposuntillength']) && ($uploadposuntillength = 0 + $_POST['uploadposuntillength']))
    {
        unset($uploadposuntilpm);

        if (isset($_POST['uploadposuntilpm']))
        {
            $uploadposuntilpm = $_POST['uploadposuntilpm'];
        }

        if ($uploadposuntillength == 255)
        {
            $modcomment  = gmdate("Y-m-d")." - Upload Disabled By - ".$CURUSER['username'].".\nReason: $uploadposuntilpm\n".$modcomment;
            $msg         = sqlesc("Your Upload Privilage has been Removed - until further notice. \nPlease contact a member of Staff to try to resolve this issue");

            $updateset[] = "uploadposuntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $uploadposuntil = get_date_time(gmtime() + $uploadposuntillength * 604800);
            $dur            = $uploadposuntillength." week".($uploadposuntillength > 1 ? "s" : "");

            $msg            = sqlesc("Your Upload Privilage has been Removed for - $dur.".$CURUSER['username'].($uploadposuntilpm ? "\n\nReason: $uploadposuntilpm" : ""));

            $modcomment     = gmdate("Y-m-d")." - Upload Disabled for $dur by ".$CURUSER['username'].".\nReason: $uploadposuntilpm\n".$modcomment;

            $updateset[]    = "uploadposuntil = ".sqlesc($uploadposuntil);
        }

        $added   = sqlesc(get_date_time());
        $subject = sqlesc("Your Upload Status");

        sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                    VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        write_stafflog("<a href='userdetails.php?id=$userid'>$username</a> had their Upload Privilage Removed for $dur by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");

        $updateset[] = "uploadpos = 'no'";
    }

    //-- Download Permission - Enable --//
    if ((isset($_POST['downloadpos'])) && (($downloadpos = $_POST['downloadpos']) != $user['downloadpos']))
    {
        if ($downloadpos == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Download Permission - Enabled by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("Your Download Privilege has been Returned to you. \n Please be more careful in the future.");
            $added      = sqlesc(get_date_time());
            $subject    = sqlesc("Your Download Status");

            sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                        VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

            write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Download Privilage Returned by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");
        }
            $updateset[] = "downloadpos = ".sqlesc($downloadpos);
    }

    //-- Set Download Permission - Disabled - Time Based --//
    if (isset($_POST['downloadposuntillength']) && ($downloadposuntillength = 0 + $_POST['downloadposuntillength']))
    {
        unset($downloadposuntilpm);

        if (isset($_POST['downloadposuntilpm']))
        {
            $downloadposuntilpm = $_POST['downloadposuntilpm'];
        }

        if ($downloadposuntillength == 255)
        {
            $modcomment = gmdate("Y-m-d")." - Download Disabled By - ".$CURUSER['username'].".\nReason: $downloadposuntilpm\n".$modcomment;
            $msg        = sqlesc("Your Download Privilage has been removed - until further notice. \nPlease contact a member of Staff to try to resolve this issue");

            $updateset[] = "downloadposuntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $downloadposuntil = get_date_time(gmtime() + $downloadposuntillength * 604800);
            $dur              = $downloadposuntillength." week".($downloadposuntillength > 1 ? "s" : "");
            $msg              = sqlesc("Your download privilage has been Removed for - $dur.".$CURUSER['username'].($downloadposuntilpm ? "\n\nReason: $downloadposuntilpm" : ""));

            $modcomment  = gmdate("Y-m-d")." - Download Disabled for $dur by ".$CURUSER['username'].".\nReason: $downloadposuntilpm\n".$modcomment;
            $updateset[] = "downloadposuntil = ".sqlesc($downloadposuntil);
        }

        $added   = sqlesc(get_date_time());
        $subject = sqlesc("Your Download Status");

        sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                    VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Download Privilage Removed for $dur by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");

        $updateset[] = "downloadpos = 'no'";
    }

    // --Shoutbox Permission - Enable --//
    if ((isset($_POST['shoutboxpos'])) && (($shoutboxpos = $_POST['shoutboxpos']) != $user['shoutboxpos']))
    {
        if ($shoutboxpos == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Shoutbox Permission - Enabled by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("Your Shoutbox Privilege has been Returned to you. \n Please be more careful in the future.");
            $added      = sqlesc(get_date_time());
            $subject    = sqlesc("Your Shoutbox Status");

            sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                        VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

            write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Shoutbox Privilage Returned by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");
         }
            $updateset[] = "shoutboxpos = ".sqlesc($shoutboxpos);
    }

    //-- Set Shoutbox Permission - Disabled - Time Based --//
    if (isset($_POST['shoutboxposuntillength']) && ($shoutboxposuntillength = 0 + $_POST['shoutboxposuntillength']))
    {
        unset($shoutboxposuntilpm);

        if (isset($_POST['shoutboxposuntilpm']))
        {
             $shoutboxposuntilpm = $_POST['shoutboxposuntilpm'];
        }

        if ($shoutboxposuntillength == 255)
        {
            $modcomment = gmdate("Y-m-d")." - Shoutbox Disabled By - ".$CURUSER['username'].".\nReason: $shoutboxposuntilpm\n".$modcomment;
            $msg        = sqlesc("Your Shoutbox Privilage has been Removed - until further notice. \nPlease contact a member of Staff to try to resolve this issue");

            $updateset[] = "shoutboxposuntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $shoutboxposuntil = get_date_time(gmtime() + $shoutboxposuntillength * 604800);
            $dur              = $shoutboxposuntillength." week".($shoutboxposuntillength > 1 ? "s" : "");
            $msg              = sqlesc("Your Shoutbox Privilage has been Removed for - $dur.".$CURUSER['username'].($shoutboxposuntilpm ? "\n\nReason: $shoutboxposuntilpm" : ""));

            $modcomment  = gmdate("Y-m-d")." - Shoutbox Disabled for $dur by ".$CURUSER['username'].".\nReason: $shoutboxposuntilpm\n".$modcomment;
            $updateset[] = "shoutboxposuntil = ".sqlesc($shoutboxposuntil);
        }

        $added   = sqlesc(get_date_time());
        $subject = sqlesc("Your Shoutbox Status");

        sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                    VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Shoutbox Privilage Removed for $dur by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");

        $updateset[] = "shoutboxpos = 'no'";
    }

    // Torrent Comments Permission - Enable
    if ((isset($_POST['torrcompos'])) && (($torrcompos = $_POST['torrcompos']) != $user['torrcompos']))
    {
        if ($torrcompos == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Torrent Comment Permission - Enabled by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("Your Torrent Comment Privilege has been Returned to you. \n Please be more careful in the future.");
            $added      = sqlesc(get_date_time());
            $subject    = sqlesc("Your Torrent Comment Status");

            sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                        VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

            write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Torrent Comment Privilage Returned by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");
        }
            $updateset[] = "torrcompos = ".sqlesc($torrcompos);
    }

    //-- Set Torrent Comments - Disabled - Time Based --//
    if (isset($_POST['torrcomposuntillength']) && ($torrcomposuntillength = 0 + $_POST['torrcomposuntillength']))
    {
        unset($torrcomposuntilpm);

        if (isset($_POST['torrcomposuntilpm']))
        {
            $torrcomposuntilpm = $_POST['torrcomposuntilpm'];
        }

        if ($torrcomposuntillength == 255)
        {
            $modcomment = gmdate("Y-m-d")." - Torrent Comment Disabled By - ".$CURUSER['username'].".\nReason: $torrcomposuntilpm\n".$modcomment;
            $msg        = sqlesc("Your Torrent Comment Privilage has been Removed - until further notice. \nPlease contact a member of Staff to try to resolve this issue");

            $updateset[] = "torrcomposuntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $torrcomposuntil = get_date_time(gmtime() + $torrcomposuntillength * 604800);
            $dur             = $torrcomposuntillength." week".($torrcomposuntillength > 1 ? "s" : "");
            $msg             = sqlesc("Your Torrent Comment Privilage has been Removed for - $dur.".$CURUSER['username'].($torrcomposuntilpm ? "\n\nReason: $torrcomposuntilpm" : ""));

            $modcomment  = gmdate("Y-m-d")." - Torrent Comment Disabled for $dur by ".$CURUSER['username'].".\nReason: $torrcomposuntilpm\n".$modcomment;
            $updateset[] = "torrcomposuntil = ".sqlesc($torrcomposuntil);
        }

        $added   = sqlesc(get_date_time());
        $subject = sqlesc("Your Torrent Comment Status");

        sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                    VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        write_stafflog("<a href='userdetails.php?id=$userid'><strong>$username</strong></a> had their Torrent Comment Privilage Removed for $dur by <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a>");

        $updateset[] = "torrcompos = 'no'";
    }

    //-- Offer Comments Permission - Enable --//
    if ((isset($_POST['offercompos'])) && (($offercompos = $_POST['offercompos']) != $user['offercompos']))
    {
        if ($offercompos == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Offer Comment Permission - Enabled by ".$CURUSER['username'].".\n".$modcomment;
            $msg    = sqlesc("Your Offer Comment Privilege has been Returned to you. \n Please be more careful in the future.");
            $added   = sqlesc(get_date_time());
            $subject = sqlesc("Your Offer Comment Status");

            sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                        VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

            write_stafflog("<a href=userdetails.php?id=$userid><strong>$username</strong></a> had their Offer Comment Privilage Returned by <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>");
        }
            $updateset[] = "offercompos = ".sqlesc($offercompos);
    }

    //-- Set Offer Comments - Disabled - Time Based --//
    if (isset($_POST['offercomposuntillength']) && ($offercomposuntillength = 0 + $_POST['offercomposuntillength']))
    {
        unset($offercomposuntilpm);

        if (isset($_POST['offercomposuntilpm']))
        {
            $offercomposuntilpm = $_POST['offercomposuntilpm'];
        }

        if ($offercomposuntillength == 255)
        {
            $modcomment = gmdate("Y-m-d")." - Offer Comment Disabled By - ".$CURUSER['username'].".\nReason: $offercomposuntilpm\n".$modcomment;
            $msg        = sqlesc("Your Offer Comment Privilage has been Removed - until further notice. \nPlease contact a member of Staff to try to resolve this issue");

            $updateset[] = "offercomposuntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $offercomposuntil = get_date_time(gmtime() + $offercomposuntillength * 604800);
            $dur              = $offercomposuntillength." week".($offercomposuntillength > 1 ? "s" : "");
            $msg              = sqlesc("Your Offer Comment Privilage has been Removed for - $dur.".$CURUSER['username'].($offercomposuntilpm ? "\n\nReason: $offercomposuntilpm" : ""));
            $modcomment       = gmdate("Y-m-d")." - Offer Comment Disabled for $dur by ".$CURUSER['username'].".\nReason: $offercomposuntilpm\n".$modcomment;

            $updateset[] = "offercomposuntil = ".sqlesc($offercomposuntil);
        }
        $added   = sqlesc(get_date_time());
        $subject = sqlesc("Your Offer Comment Status");

        sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                    VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        write_stafflog("<a href=userdetails.php?id=$userid><strong>$username</strong></a> had their Offer Comment Privilage Removed for $dur by <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>");

        $updateset[] = "offercompos = 'no'";
    }

    //-- Request Comments Permission - Enable --//
        if ((isset($_POST['requestcompos'])) && (($requestcompos = $_POST['requestcompos']) != $user['requestcompos']))
        {
            if ($requestcompos == 'yes')
            {
                $modcomment = gmdate("Y-m-d")." - Request Comment Permission - Enabled by ".$CURUSER['username'].".\n".$modcomment;
                $msg        = sqlesc("Your Request Comment Privilege has been Returned to you. \n Please be more careful in the future.");
                $added      = sqlesc(get_date_time());
                $subject    = sqlesc("Your Request Comment Status");

                sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                            VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

                write_stafflog("<a href=userdetails.php?id=$userid><strong>$username</strong></a> had their Request Comment PrivilageRreturned by <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>");
            }
            $updateset[] = "requestcompos = ".sqlesc($requestcompos);
        }

    //-- Set Request Comments - Disabled - Time Based --//
    if (isset($_POST['requestcomposuntillength']) && ($requestcomposuntillength = 0 + $_POST['requestcomposuntillength']))
    {
        unset($requestcomposuntilpm);

        if (isset($_POST['requestcomposuntilpm']))
        {
            $requestcomposuntilpm = $_POST['requestcomposuntilpm'];
        }

        if ($requestcomposuntillength == 255)
        {
            $modcomment = gmdate("Y-m-d")." - Request Comment Disabled By - ".$CURUSER['username'].".\nReason: $requestcomposuntilpm\n".$modcomment;
            $msg        = sqlesc("Your Request Comment Privilage has been Removed - until further notice. \nPlease contact a member of Staff to try to resolve this issue");

            $updateset[] = "requestcomposuntil = '0000-00-00 00:00:00'";
        }
        else
        {
            $requestcomposuntil = get_date_time(gmtime() + $requestcomposuntillength * 604800);
            $dur                = $requestcomposuntillength." week".($requestcomposuntillength > 1 ? "s" : "");
            $msg                = sqlesc("Your Request Comment Privilage has been Removed for - $dur.".$CURUSER['username'].($requestcomposuntilpm ? "\n\nReason: $requestcomposuntilpm" : ""));
            $modcomment         = gmdate("Y-m-d")." - Request Comment Disabled for $dur by ".$CURUSER['username'].".\nReason: $requestcomposuntilpm\n".$modcomment;

            $updateset[] = "requestcomposuntil = ".sqlesc($requestcomposuntil);
        }
        $added   = sqlesc(get_date_time());
        $subject = sqlesc("Your Request Comment Status");

        sql_query("INSERT INTO messages (sender, receiver, subject, msg, added)
                    VALUES (0, $userid, $subject, $msg, $added)") or sqlerr(__FILE__, __LINE__);

        write_stafflog("<a href=userdetails.php?id=$userid><strong>$username</strong></a> had their Request Comment Privilage Removed for $dur by <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>");

        $updateset[] = "requestcompos = 'no'";
    }

    //-- Avatar Changed --//
    if ((isset($_POST['avatar'])) && (($avatar = $_POST['avatar']) != ($curavatar = $user['avatar'])))
    {
        $modcomment = gmdate("Y-m-d")." - Avatar changed from ".htmlspecialchars($curavatar)." to ".htmlspecialchars($avatar)." by ".$CURUSER['username'].".\n".$modcomment;

        $updateset[] = "avatar = ".sqlesc($avatar);
    }

    //-- Set First Line Support Yes / No --//
    if ((isset($_POST['support'])) && (($support = $_POST['support']) != $user['support']))
    {
        if ($support == 'yes')
        {
            $modcomment = gmdate("Y-m-d")." - Promoted to FLS by ".$CURUSER['username'].".\n".$modcomment;
            $msg        = sqlesc("You have been Added to First Line Support by ".htmlspecialchars($CURUSER['username']).".");
            $added      = sqlesc(get_date_time());

            sql_query("INSERT INTO messages (sender, receiver, msg, added)
                        VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
        }

        if ($support == 'no')
        {
            $updateset[] = "support_lang =''";
            $updateset[] = "supportfor =''";
            $modcomment  = gmdate("Y-m-d")." - Demoted from FLS by ".$CURUSER['username'].".\n".$modcomment;
            $msg         = sqlesc("You have been Removed from First Line Support by ".htmlspecialchars($CURUSER['username']).", probably because you were Inactive or asked to be Removed.");
            $added       = sqlesc(get_date_time());

            sql_query("INSERT INTO messages (sender, receiver, msg, added)
                        VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
        }

        $updateset[] = "support = ".sqlesc($support);
    }

    //-- Set First Line Support For --//
    if (isset($_POST["supportfor"]) && ($supportfor = $_POST["supportfor"]) != $user["supportfor"])
    {
        $updateset[] = "supportfor = ".sqlesc($supportfor);
    }

    //-- Set First Line Support Language --//
    if (isset($_POST["support_lang"]) && ($support_lang = $_POST["support_lang"]) != $user["support_lang"])
    {
        $updateset[] = "support_lang = ".sqlesc($support_lang);
    }

    //-- Add ModComment... (If We Changed Something We Update Otherwise We Dont Include This..) --//
    if (($CURUSER['class'] >= UC_SYSOP && ($user['modcomment'] != $_POST['modcomment'] || $modcomment != $_POST['modcomment'])) || ($CURUSER['class'] < UC_SYSOP && $modcomment != $user['modcomment']))
    {
        $updateset[] = "modcomment = ".sqlesc($modcomment);
    }

    if (sizeof($updateset) > 0)
    {
        sql_query("UPDATE users
                    SET ".implode(", ", $updateset)."
                    WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);

        status_change($userid);
    }

    $returnto = $_POST["returnto"];

    header("Location: $site_url/$returnto");

    die();
}

?>