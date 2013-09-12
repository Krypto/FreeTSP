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

//-- Start Theme Based Functions --//
function begin_frame ($caption="", $center=false, $padding=10)
{
    $tdextra = "";

    if ($caption)
    {
        print("<h2>$caption</h2>");
    }

    if ($center)
    {
        $tdextra .= " align='center'";
    }

    print("<table border='1' width='100%' cellspacing='0' cellpadding='$padding'><tr><td $tdextra>\n");
}

function end_frame()
{
    print("</td></tr></table>");
}

function begin_table ($fullwidth=false, $padding=5)
{
    $width = "";

    if ($fullwidth)
    {
        $width .= " width='100%'";
    }

    print("<table class='main' border='1' $width cellspacing='0' cellpadding='$padding'>");
}

function end_table()
{
    echo("</table>");
}
//-- Finish Theme Based Functions --//

//-- Start Shoutbox Functions --//
function sb_images()
{
    global $CURUSER, $image_dir;

    $res = sql_query("SELECT s.id, s.userid, s.date, s.text, s.to_user, u.username, u.class, u.donor, u.warned, u.avatar
                        FROM shoutbox AS s
                        LEFT JOIN users AS u ON s.userid=u.id
                        ORDER BY s.date DESC
                        LIMIT 30") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 0)
    {
        print ("No Shouts Here");
    }
    else
    {
        print ("<table class='main_font' border='0' align='left' width='100%' cellspacing='0' cellpadding='2'>");

        while ($arr = mysql_fetch_assoc($res))
        {
        /*
            //-- Private Shout Mod --//

            if (($arr['to_user'] != $CURUSER['id'] && $arr['to_user'] != 0) && $arr['userid'] != $CURUSER['id'])
            continue;

            elseif
                ($arr['to_user'] == $CURUSER['id'] || ($arr['userid'] == $CURUSER['id'] && $arr['to_user'] !=0) )
                $private = "<a href=\"javascript:private_reply('".$arr['username']."')\"><img src='{$image_dir}private-shout.png'  width='16' height='16' border='0' alt='Private Shout'title='Private Shout! click to reply to ".$arr['username']."' style='padding-left:2px;padding-right:2px;' /></a>";
            else
                $private = '';*/
            //-- Private Shout Mod End --//

            //-- Original Code Does Not Allow Self Edit Of Posts For Power Users & Above --//
        /*
            $edit = (get_user_class() >= UC_MODERATOR ? "<a href='shoutbox.php?edit=".$arr['id']."'><img src='{$image_dir}button_edit2.gif' width='16' height='16' border='0' alt='Edit Shout' title='Edit Shout' /></a> " : "");
        */

            //-- Power Users & Above Can Edit Their Own Posts - Correction By Fireknight --//
            $edit = (get_user_class() >= UC_MODERATOR || ($arr['userid'] == $CURUSER['id']) && ($CURUSER['class'] >= UC_POWER_USER && $CURUSER['class'] <= UC_MODERATOR) ? "<a href='shoutbox.php?edit=".$arr["id"]."&amp;user=".$arr['userid']."'><img src='{$image_dir}edit.png' width='16' height='16' border='0' alt='Edit Shout' title='Edit Shout' style='vertical-align:bottom;' /></a> " : '');

            $delall = (get_user_class() >= UC_SYSOP ? "<a href='shoutbox.php?delall' onclick=\"confirm_delete(); return false; \"><img src='{$image_dir}delete_all.png' width='16' height='16' border='0' alt='Empty Shout' title='Empty Shout' style='vertical-align:bottom;' /></a> " : "");

            $del = (get_user_class() >= UC_MODERATOR ? "<a href='shoutbox.php?del=".$arr['id']."'><img src='{$image_dir}delete.png' width='16' height='16' border='0' alt='Delete Single Shout' title='Delete Single Shout' style='vertical-align:bottom;' /></a> " : "");

            $pm = "<a target='_blank' href='sendmessage.php?receiver=".$arr['userid']."'><img src='{$image_dir}mail.png' width='16' height='16' border='0' alt='PM User' title='PM User' style='vertical-align:bottom;'/></a>";

            // Uncomment If You Wish To Have The Members Avatar Shown --//
        /*
            if (!$arr[avatar])
            {
                $avatar = ("<a target='_blank' href='userdetails.php?id=$arr[userid]'></a>\n");
            }
            else
            {
                $avatar = ("<a target='_blank' href='userdetails.php?id=$arr[userid]'><img src='$arr[avatar]' width='50' height='50' border='0' alt='' title='' /></a>\n");
            }

        */

        /*
            $private = (get_user_class() >= UC_MODERATOR ? "<a href=\"javascript:private_reply('".$arr['username']."')\"><img src='{$image_dir}private-shout.png' width='16' height='16' border='0' alt='Private Shout' title='Private Shout' /></a>&nbsp;": "");
        */

            $user_stuff       = $arr;
            $user_stuff['id'] = $arr['userid'];
            $datum            = gmdate("d M h:i", $arr["date"]);

            print("<tr>
                    <td style='width:85px;vertical-align:bottom;'>
                        <span class='date'>['$datum']</span>
                    </td>
                    <td>
                        $delall $del $edit $pm $avatar
                        ".format_username($user_stuff)."
                        ".format_comment($arr["text"])."
                    </td>
                </tr>");
        }
        print("</table>");
    }
}

function sb_style()
{
    ?>
    <style type='text/css'>

    /*-- Start Main Shout --*/
    body
    {
        background-color : transparent;
    }

    .main_font
    {
        color       : #256EB8;
        font-size   : 9pt;
        font-family : arial;
    }

    a
    {
        color           : #356AA0;
        font-weight     : bold;
        font-size       : 9pt;
        text-decoration : none;
    }

    a:hover
    {
        color : #0B610B;
    }

    .date
    {
        color     : #1947D1;
        font-size : 9pt;
    }

    .error
    {
        color            : #990000;
        background-color : #FFF0F0;
        padding          : 7px;
        margin-top       : 5px;
        margin-bottom    : 10px;
        border           : 1px dashed #990000;
    }
    /*-- Finish Main Shout --*/

    /*-- Start Staff Edit Box --*/
    #staff_specialbox
    {
        border     : 1px solid gray;
        width      : 600px;
        background : #FBFCFA;
        font       : 11px verdana, sans-serif;
        color      : #000000;
        padding    : 3px;
        outline    : none;
    }
    /*-- Finish Staff Edit Box --*/

    /*-- Start Member Edit Box --*/
    #member_specialbox
    {
        border     : 1px solid gray;
        width      : 600px;
        background : #FBFCFA;
        font       : 11px verdana, sans-serif;
        color      : #000000;
        padding    : 3px;
        outline    : none;
    }
    /*-- Finish Member Edit Box --*/

    </style>
    <?php
}
//-- Finish Shoutbox Functions --//

function StatusBar()
{
    global $CURUSER, $image_dir, $site_reputation;

    if (!$CURUSER)
    {
        return "";
    }

    $upped  = mksize($CURUSER['uploaded']);
    $downed = mksize($CURUSER['downloaded']);
    $ratio  = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded']/$CURUSER['downloaded'] : 0;
    $ratio  = number_format($ratio, 2);

	$IsDonor = '';

	if ($CURUSER['donor'] == "yes")
	{
		$IsDonor = "<img src='".$image_dir."star.png' width='15' height='16' border='0' alt='Donor' title='Donor' />";
	}

	$warn = '';

	if ($CURUSER['warned'] == "yes")
	{
		$warn = "<img src='".$image_dir."warned.png' width='15' height='16' border='0' alt='Warned' title='Warned' />";
	}

    $res1 = sql_query("SELECT COUNT(id)
                        FROM messages
                        WHERE receiver=".$CURUSER["id"]."
                        AND unread='yes'") or print(mysql_error());

    $arr1   = mysql_fetch_row($res1);
    $unread = $arr1[0];
    $inbox  = ($unread == 1 ? "$unread&nbsp;New Message" : "$unread&nbsp;New Messages");

    $res2 = sql_query("SELECT seeder, COUNT(id) AS pCount
                        FROM peers
                        WHERE userid=".$CURUSER['id']."
                        GROUP BY seeder") or print(mysql_error());

    $seedleech = array('yes' => '0',
                       'no'  => '0');

    while ($row = mysql_fetch_assoc($res2))
    {
        if ($row['seeder'] == 'yes')
        {
            $seedleech['yes'] = $row['pCount'];
        }
        else
        {
            $seedleech['no'] = $row['pCount'];
        }
    }

    //-- Start Temp Demote By Retro 3 of 3 --//
    $usrclass = "";

    if ($CURUSER['override_class'] != 255)
    {
        $usrclass = "&nbsp;<strong>(".get_user_class_name($CURUSER['class']).")</strong>&nbsp;";
    }

    if (get_user_class() >= UC_MODERATOR)
    {
        $usrclass = "&nbsp;<a href='setclass.php'><strong>(".get_user_class_name($CURUSER['class']).")</strong></a>&nbsp;";
    }

    $StatusBar = '';

    if ($site_reputation == true)
    {
		$StatusBar = "<div id='statusbar'><p class='home'>Welcome back, <a class='altlink_user' href='userdetails.php?id=".$CURUSER['id']."'><font color='#".get_user_class_color($CURUSER['class'])."'> ".htmlspecialchars($CURUSER['username'])."</font></a>&nbsp;$IsDonor&nbsp;$warn&nbsp;$usrclass<br />Reputation Points : ".$CURUSER['reputation']."<br />Invites: <a href='invite.php'>". htmlspecialchars($CURUSER['invites'])."</a></p><p>";
	}
	else
	{
		$StatusBar = "<div id='statusbar'><p class='home'>Welcome back, <a class='altlink_user' href='userdetails.php?id=".$CURUSER['id']."'><font color='#".get_user_class_color($CURUSER['class'])."'> ".htmlspecialchars($CURUSER['username'])."</font></a>&nbsp;$IsDonor&nbsp;$warn&nbsp;$usrclass<br />Invites: <a href='invite.php'>". htmlspecialchars($CURUSER['invites'])."</a></p><p>";
	}

    //-- Finish Temp Demote By Retro 3 of 3 --//
	$StatusBar .= "</p><p class='home'>Ratio:$ratio<br />&nbsp;&nbsp;Uploaded:$upped<br />&nbsp;&nbsp;Downloaded:$downed<br />&nbsp;&nbsp;Active Torrents:&nbsp;<img alt='Torrents seeding' title='Torrents seeding' src='".$image_dir."up.png' height='12' width='12'/>&nbsp;{$seedleech['yes']}&nbsp;&nbsp;<img alt='Torrents leeching' title='Torrents leeching' src='".$image_dir."dl.png' height='12' width='12'/>&nbsp;{$seedleech['no']}</p><div align='center'>
    <script type='text/javascript'>
	var d=new Date();
	document.write(d);
    </script>
    </div>";

	$StatusBar .= "&nbsp;&nbsp;</div>";
	   return $StatusBar;
}

?>