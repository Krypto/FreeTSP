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
        color       : #DADADA;
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
        color     : #DADADA;
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

//-- Start Theme Requirements --//

// Stats Begin - Credits Bigjoos
$cache_stats      = ROOT_DIR."cache/stats.txt";
$cache_stats_life = 5 * 60; // 5min

if (file_exists($cache_stats) && is_array(unserialize(file_get_contents($cache_stats))) && (time() - filemtime($cache_stats)) < $cache_stats_life)

	$row = unserialize(@file_get_contents($cache_stats));

	else
	{
		$stats = sql_query("SELECT *, seeders + leechers AS peers, seeders / leechers AS ratio, unconnectables / (seeders + leechers) AS ratiounconn
                            FROM stats
                            WHERE id = '1'
                            LIMIT 1") or sqlerr(__FILE__, __LINE__);

		$row = mysql_fetch_assoc($stats);

		$handle = fopen($cache_stats, "w+");

		fwrite($handle, serialize($row));
		fclose($handle);
	}

    $registered2 = number_format($row['regusers']);
    $unverified2 = number_format($row['unconusers']);
    $torrents2   = number_format($row['torrents']);
    $numactive2  = number_format($row['numactive']);
    $forumposts2 = number_format($row['forumposts']);
    $ratio       = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded'] / $CURUSER['downloaded'] : 0;
    $ratio       = number_format($ratio, 2);

    $res1 = sql_query("SELECT COUNT(id)
                        FROM messages
                        WHERE receiver=".$CURUSER["id"]."
                        AND unread='yes'") or print(mysql_error());

    $arr1   = mysql_fetch_row($res1);
    $unread = $arr1[0];
    $inbox  = ($unread == 1 ? "$unread&nbsp;New Mail" : "$unread&nbsp;New Mail");


    if (get_user_class() < UC_MODERATOR)
    {
        $usrclass = "&nbsp;".get_user_class_name($CURUSER['class'])."";
    }

    if (get_user_class() >= UC_MODERATOR)
    {
        $usrclass = "&nbsp;<a href='setclass.php'>".get_user_class_name($CURUSER['class'])."</a>";
    }

	if ( $CURUSER["reputation"] >= 1 )
	{
	    $rep = "<img src='stylesheets/genx/images/001_63.png' alt='' title='' />&nbsp;Reputaion";
	    $reputation = "<span style='color : #4DDB4D'>".htmlspecialchars($CURUSER['reputation'])."</span>";
	}
	else
	{
        $rep = "<img src='stylesheets/genx/images/001_62.png' alt='' title='' />&nbsp;Reputaion";
        $reputation = "<span style='color : #FF0000'>&nbsp;0</span>";
	}

	if ($CURUSER["invites"] >= 1)
	{
	    $invites = "<a href='invite.php'><span style='color : #4DDB4D'>".htmlspecialchars($CURUSER['invites'])."</span></a>";
	}
	else
	{
        $invites = "<span style='color : #FF0000'>&nbsp;0</span>";
	}

	if ($CURUSER["donor"] == "yes")
	{
	    $donor ="&nbsp;<span style='color : #4DDB4D'>&nbsp;Yes</span>";
	}
	else
	{
	    $donor ="<span style='color : #FF0000'>&nbsp;No</span>";
	}


	$q1 = sql_query('SELECT connectable
                        FROM peers
                        WHERE userid = '.$CURUSER["id"].' LIMIT 1') or sqlerr();

	if ($a = mysql_fetch_row($q1))
	{
		$connect = $a[0];

		if ( $connect == "yes" )
		{
			$connectable = "<span style='color : #00ff00'>&nbsp;Yes</span>";
		}
		else
		{
			$connectable = "<span style='color : #ff0000'>&nbsp;No</span>";
		}
	}
	else
	{
		$connectable = "<span style='color : #99AD99'>&nbsp;???</span>";
	}

    $nameuser  = " ".format_username($CURUSER)." ";
    $menuspace = "&nbsp;&nbsp;&nbsp;&nbsp; |&nbsp;&nbsp;&nbsp;&nbsp;";
//-- Finish Theme Requirements --//

?>