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
        color       : #FFFFFF;
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
        color     : #FFFFFF;
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

    $upped   = mksize($CURUSER['uploaded']);
    $downed  = mksize($CURUSER['downloaded']);
    $ratio   = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded'] / $CURUSER['downloaded'] : 0;
    $ratio   = number_format($ratio, 2);

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
         $StatusBar = "<tr>"."<td class='status'>"."<div id='statusbar'>"."<p class='home'>Welcome back, ".format_username($CURUSER)."&nbsp; ".$usrclass."&nbsp;[<a href='logout.php'>Logout</a>] Invites: <a href='invite.php'>". htmlspecialchars($CURUSER['invites']) . "</a> Reputation Points : ".$CURUSER['reputation']."</p>"."<p>".date(DATE_RFC822)."</p><p>";
    }
    else
    {
         $StatusBar = "<tr>"."<td class='status'>"."<div id='statusbar'>"."<p class='home'>Welcome back, ".format_username($CURUSER)."&nbsp; ".$usrclass."&nbsp;[<a href='logout.php'>Logout</a>] Invites: <a href='invite.php'>". htmlspecialchars($CURUSER['invites']) . "</a></p>"."<p>".date(DATE_RFC822)."</p><p>";
    }
    //-- Finish Temp Demote By Retro 3 of 3 --//

    $StatusBar .= ""."</p><p class='home'>Ratio:$ratio"."&nbsp;&nbsp;Uploaded:$upped"."&nbsp;&nbsp;Downloaded:$downed"."&nbsp;&nbsp;Active Torrents:&nbsp;<img src='".$image_dir."up.png' width='9' height='7' border='0' alt='Torrents Seeding' title='Torrents Seeding' />&nbsp;{$seedleech['yes']}"."&nbsp;&nbsp;<img src='".$image_dir."dl.png' width='9' height='7' border='0' alt='Torrents Leeching' title='Torrents Leeching' />&nbsp;{$seedleech['no']}</p>";

    $StatusBar .= "<p>"."<a href='messages.php'>$inbox</a>"."</p></div></td></tr></table>";

    return $StatusBar;
}

function Dropmenu()
{
?>
    <table class='mainouter' width='100%' border='0' cellspacing='0' cellpadding='10'>
        <tr>
            <td align='center' class='std' style='padding-left: 1%; padding-right: 1%'>
                <div class="navigation">
                    <ul class="stn-menu TSP">

                        <li><a href="index.php">Home</a></li>

                        <li class="hasSubNav hasArrow"><a href="javascript:">Torrents</a>
                            <span class="arrow"></span>
                            <ul>
                                <li><a href="browse.php">Browse</a></li>
                                <li><a href="search.php">Search</a></li>
                                <li><a href="upload.php">Upload</a></li>
                                <li><a href="offers.php">Offers</a></li>
                                <li><a href="requests.php">Requests</a></li>
                                <li><a href="mytorrents.php">My Torrents</a></li>
                            </ul>
                        </li>

                        <li class="hasSubNav hasArrow"><a href="javascript:">User CP</a>
                            <span class="arrow"></span>
                            <ul>
                                <li><a href="usercp.php?action=avatar">Avatar</a></li>
                                <li><a href="usercp.php?action=signature">Signature</a></li>
                                <li><a href="usercp.php">Messages</a></li>
                                <li><a href="usercp.php?action=security">Security</a></li>
                                <li><a href="usercp.php?action=torrents">Torrents</a></li>
                                <li><a href="usercp.php?action=personal">Personal</a></li>
                                <li><a href="logout.php">Logout</a></li>
                            </ul>
                        </li>

                        <li><a href="forums.php">Forums</a></li>

                        <li class="hasSubNav hasArrow"><a href="javascript:">Site Info</a>
                            <span class="arrow"></span>
                            <ul>
                                <li><a href="rules.php">Rules</a></li>
                                <li><a href="faq.php">F.A.Q.</a></li>
                                <li><a href="topten.php">Top Ten</a></li>
                                <li><a href="links.php">Links</a></li>
                                <li><a href="credits.php">Credits</a></li>
                            </ul>
                        </li>

                        <li><a href="helpdesk.php">Help Desk</a></li>
                        <li><a href="staff.php">Staff</a></li>

                        <?php if (get_user_class() >= UC_MODERATOR) { ?>
                        <li><a href='controlpanel.php'>Staff Tools</a></li>
                        <?php }?>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
<?php
}

function Stdmenu()
{
?>
    <table class='mainouter' width='100%' border='0' cellspacing='0' cellpadding='10'>
        <tr>
            <td align='center' class='navigation'><a href='/index.php'>Home</a></td>
            <td align='center' class='navigation'><a href='/browse.php'>Browse</a></td>
            <td align='center' class='navigation'><a href='/offers.php'>Offer</a></td>
            <td align='center' class='navigation'><a href='/requests.php'>Request</a></td>
            <td align='center' class='navigation'><a href='/search.php'>Search</a></td>
            <td align='center' class='navigation'><a href='/upload.php'>Upload</a></td>
            <td align='center' class='navigation'><a href='/altusercp.php'>Profile</a></td>
            <td align='center' class='navigation'><a href='/forums.php'>Forums</a></td>
            <td align='center' class='navigation'><a href='/topten.php'>Top 10</a></td>
            <td align='center' class='navigation'><a href='/rules.php'>Rules</a></td>
            <td align='center' class='navigation'><a href='/faq.php'>FAQ</a></td>
            <td align='center' class='navigation'><a href='/links.php'>Links</a></td>
            <td align='center' class='navigation'><a href='/credits.php'>Credits</a></td>
            <td align='center' class='navigation'><a href='/helpdesk.php'>Help Desk</a></td>
            <td align='center' class='navigation'><a href='/staff.php'>Staff</a></td>

            <?php if (get_user_class() >= UC_MODERATOR) { ?>
            <td align='center' class='navigation'><a href='/controlpanel.php'>Staff Tools</a></td>
            <?php } ?>
        </tr>
    </table>
<?php
}

?>