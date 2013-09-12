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

db_connect(true);
logged_in();

//-- Start Cached Latest User - Credits Bigjoos --//
if ($CURUSER)
{
    $cache_newuser      = CACHE_DIR."newuser.txt";
    $cache_newuser_life = 5 * 60; //-- 5 Min --//

    if (file_exists($cache_newuser) && is_array(unserialize(file_get_contents($cache_newuser))) && (time() - filemtime($cache_newuser)) < $cache_newuser_life)

    {
        $arr = unserialize(@file_get_contents($cache_newuser));
    }

    else
    {
        $r_new = sql_query("SELECT id, username
                            FROM users
                            ORDER BY id DESC
                            LIMIT 1 ") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_assoc($r_new);

        $handle = fopen($cache_newuser, "w+");

        fwrite($handle, serialize($arr));
        fclose($handle);
    }

    /*
        $new_user = "&nbsp;<a href=\"$site_url/userdetails.php?id={$arr["id"]}\">".htmlspecialchars($arr["username"])."</a>\n";
    */
}
//-- End Cached Latest User --//

//-- Start Stats - Credits Bigjoos --//
$cache_statsalt      = CACHE_DIR."statsalt.txt";
$cache_statsalt_life = 5 * 60; ///-- 5 Min --//

if (file_exists($cache_statsalt) && is_array(unserialize(file_get_contents($cache_statsalt))) && (time() - filemtime($cache_statsalt)) < $cache_statsalt_life)

{
    $row = unserialize(@file_get_contents($cache_statsalt));
}
else
{
    $statsalt = sql_query("SELECT *, seeders + leechers AS peers, seeders / leechers AS ratio, unconnectables / (seeders + leechers) AS ratiounconn
                            FROM stats
                            WHERE id = '1' LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $row = mysql_fetch_assoc($statsalt);

    $handle = fopen($cache_statsalt, "w+");

    fwrite($handle, serialize($row));
    fclose($handle);
}

$seeders        = number_format($row['seeders']);
$leechers       = number_format($row['leechers']);
$registered     = number_format($row['regusers']);
$unverified     = number_format($row['unconusers']);
$torrents       = number_format($row['torrents']);
$torrentstoday  = number_format($row['torrentstoday']);
$ratiounconn    = $row['ratiounconn'];
$unconnectables = $row['unconnectables'];
$ratio          = round(($row['ratio'] * 100));
$peers          = number_format($row['peers']);
$numactive      = number_format($row['numactive']);
$donors         = number_format($row['donors']);
$forumposts     = number_format($row['forumposts']);
$forumtopics    = number_format($row['forumtopics']);
$Users          = number_format($row['Users']);
$Poweruser      = number_format($row['Poweruser']);
$Vip            = number_format($row['Vip']);
$Uploaders      = number_format($row['Uploaders']);
$Moderator      = number_format($row['Moderator']);
$Adminisitrator = number_format($row['Adminisitrator']);
$Sysop          = number_format($row['Sysop']);
$Manager        = number_format($row['Manager']);
//-- End Stats --//

site_header();

//-- Start Check For Pending Invited Members --//
$query = sql_query("SELECT status
                        FROM users
                        WHERE invitedby = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

$arr = mysql_fetch_assoc($query);

if ($arr['status'] == 'pending')
{
    display_message_center("info","Invite Pending","Hi <strong>$CURUSER[username]</strong><br /><br />
                                                      You have an Invited Member waiting to be Confirmed.<br />
                                                      Click <a href='invite.php'>HERE</a> to go and Confirm the Invite");
}
//-- Finish Check For Pending Invited Members --//

/*
    ?>
    <span style='font-size: xx-small;'>Welcome to our Newest Member, <span style='font-weight:bold;'><?php echo $new_user?></span>!</span><br />
    <?php
*/

//-- Start Help Desk Alert Question --//
if (get_user_class() >= UC_MODERATOR)
{
    $resa = sql_query("SELECT COUNT(id) AS problems
                            FROM helpdesk
                            WHERE solved = 'no'");

    $arra     = mysql_fetch_assoc($resa);
    $problems = $arra['problems'];

    if ($problems > 0)
    {
        display_message_center("info", "Help Desk", "Hi <strong>$CURUSER[username]</strong><br /><br />
        There ".($problems == 1 ? 'is' : 'are')." <strong>$problems question".($problems == 1 ? '' : 's')."</strong> that needs an Answer.<br /><br />
        Please click <strong><a href='controlpanel.php?fileaction=23&amp;action=problems'>HERE</a></strong> to Deal with it.");
    }
}
//-- Finish Help Desk Alert Question --//

//-- Start Report Link --//
if (get_user_class() >= UC_MODERATOR)
{
    $res_reports = sql_query("SELECT COUNT(id)
                                FROM reports
                                WHERE delt_with = '0'");

    $arr_reports = mysql_fetch_row($res_reports);
    $num_reports = $arr_reports[0];

    if ($num_reports > 0)
    {
        display_message_center("info", "Reports", "Hi <strong>$CURUSER[username]</strong><br /><br />
            There ".($$num_reports == 1 ? 'is' : 'are')." <strong>$num_reports Report".($num_reports == 1 ? '' : 's')."</strong> to be dealt with.<br /><br />
            Please click <strong><a href='controlpanel.php?fileaction=26'>HERE</a></strong> to View Reports.");
    }
}
//-- Finish Report Link --//

//-- Start Staff News --//
if (get_user_class() >= UC_MODERATOR)
{
    print("<h2>Staff News</h2>");

    $staffnews_file = CACHE_DIR."staffnews.txt";
    $expire         = 15 * 60; //-- 15 Min --//

    if (file_exists($staffnews_file) && filemtime($staffnews_file) > (time() - $expire))
    {
        $staffnews2 = unserialize(file_get_contents($staffnews_file));
    }
    else
    {
        $res = sql_query("SELECT id, userid, added, body
                            FROM staffnews
                            WHERE added + ( 3600 *24 *45 ) > ".time()."
                            ORDER BY added DESC
                            LIMIT 10") or sqlerr(__FILE__, __LINE__);

        while ($staffnews1 = mysql_fetch_assoc($res))
        {
            $staffnews2[] = $staffnews1;
        }

        $output = serialize($staffnews2);
        $fp     = fopen($staffnews_file, "w");

        fputs($fp, $output);
        fclose($fp);
    }

    if ($staffnews2)
    {
        print("<table border='1' width='100%' cellspacing='0' cellpadding='10'><tr><td class='text'>\n<ul>");

        foreach ($staffnews2
                 AS
                 $array)
        {
            print("<li>".gmdate("Y-m-d", strtotime($array['added']))." - ".format_comment($array['body'], 0));

        /*
            if (get_user_class() >= UC_SYSOP)
            {
                print(" <span style='font-size: x-small; font-weight:bold;'>[<a class='altlink' href='controlpanel.php?fileaction=22&amp;action=edit&amp;staffnewsid=".$array['id']."&amp;returnto=".urlencode($_SERVER['PHP_SELF'])."'>E</a>]</span>");
                print(" <span style='font-size: x-small; font-weight:bold;'>[<a class='altlink' href='controlpanel.php?fileaction=22&amp;action=delete&amp;staffnewsid=".$array['id']."&amp;returnto=".urlencode($_SERVER['PHP_SELF'])."'>D</a>]</span>");
            }
        */
            print("</li>");
        }
        print("</ul></td></tr></table><br />");
    }
}
//-- Finish Staff News --//

//-- Start News --//
if (isset($CURUSER))
{
    print("<table class='main' border='0' width='100%' cellspacing='0' cellpadding='0'><tr><td class='embedded'>");
    print("<h2>Recent News</h2>\n");

    $news_file = CACHE_DIR."news.txt";
    $expire    = 15 * 60; //-- 15 Min --//

    if (file_exists($news_file) && filemtime($news_file) > (time() - $expire))
    {
        $news2 = unserialize(file_get_contents($news_file));
    }
    else
    {
        $res = sql_query("SELECT id, userid, added, body
                            FROM news
                            WHERE added + ( 3600 *24 *45 ) > ".time()."
                            ORDER BY added DESC
                            LIMIT 10") or sqlerr(__FILE__, __LINE__);

        while ($news1 = mysql_fetch_assoc($res))
        {
            $news2[] = $news1;
        }

        $output = serialize($news2);
        $fp     = fopen($news_file, "w");

        fputs($fp, $output);
        fclose($fp);
    }
    if ($news2)
    {
        print("<table border='1' width='100%' cellspacing='0' cellpadding='10'><tr><td class='text'>\n<ul>");

        foreach ($news2
                 AS
                 $array)
        {
            print("<li>".gmdate("Y-m-d", strtotime($array['added']))." - ".format_comment($array['body'], 0));

            print("</li>");
        }
        print("</ul></td></tr></table>\n");
    }
}
//-- End News --//
?>

    <br/><h2>Shoutbox</h2>
    <table border="1" width="100%" cellspacing="0" cellpadding="10">
        <tr>
            <td>
                <?php

                require_once(ROOT_DIR.'shout.php');

                if ($CURUSER)
{
                ?>
            </td>
        </tr>
    </table>

    <script type="text/javascript" src="js/poll.core.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>

    <script type="text/javascript">$(document).ready(function () {
            loadpoll();
        });
    </script>

    <h2>Poll</h2>

    <table border="1" width="100%" cellspacing="0" cellpadding="10">
        <tr>
            <td align="center">
                <div id="poll_container">
                    <div id="loading_poll" style="display:none"></div>
                    <noscript>
                        <span style='font-weight:bold;'>This Requires Javascript Enabled</span>
                    </noscript>
                </div>
                <br/>
            </td>
        </tr>
    </table>

<?php
}

//-- Start Stats With Classes --//
if (isset($CURUSER))
{
    print("<h2>Stats</h2>");
    print("<table width='100%' border='1' cellspacing='0' cellpadding='10'>");
    print("<tr>");
    print("<td align='center'>");
    print("<table class='main' border='1' cellspacing='0' cellpadding='5'>");

    print("<tr>");
    print("<td class='rowhead'>Registered Users</td>");
    print("<td class='rowhead' align='right'>$registered/$max_users</td>");
    print("<td class='rowhead'>Users Online</td>");
    print("<td class='rowhead' align='right'>$numactive</td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Unconfirmed Users</td>");
    print("<td class='rowhead' align='right'>$unverified</td>");
    print("<td class='rowhead'>Donors</td>");
    print("<td class='rowhead' align='right'>$donors</td>");
    print("</tr>");

    print("<tr>");
    print("<td colspan='4'></td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Forum Topics</td>");
    print("<td class='rowhead' align='right'>$forumtopics</td>");
    print("<td class='rowhead'>Torrents</td>");
    print("<td class='rowhead' align='right'>$torrents</td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Forum Posts</td>");
    print("<td class='rowhead' align='right'>$forumposts</td>");
    print("<td class='rowhead'>New Torrents Today</td>");
    print("<td class='rowhead' align='right'>$torrentstoday</td>");
    print("</tr>");

    print("<tr>");
    print("<td colspan='4'></td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Peers</td>");
    print("<td class='rowhead' align='right'>$peers</td>");
    print("<td class='rowhead'>Unconnectable Peers</td>");
    print("<td class='rowhead' align='right'>$unconnectables</td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Seeders</td>");
    print("<td class='rowhead' align='right'>$seeders</td>");
    print("<td class='rowhead' align='right'>Unconnectables Ratio (%)</td>");

    ?>
    <td class='rowhead' align='right'><?php echo round($ratiounconn * 100)?></td>
    <?php

    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Leechers</td>");
    print("<td class='rowhead' align='right'>$leechers</td>");
    print("<td class='rowhead'>Seeder/Leecher Ratio (%)</td>");
    print("<td class='rowhead' align='right'>$ratio</td>");
    print("</tr>");

    print("<tr>");
    print("<td colspan='4'></td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Users</td>");
    print("<td class='rowhead' align='right'>$Users</td>");
    print("<td class='rowhead'>Power Users</td>");
    print("<td class='rowhead' align='right'>$Poweruser</td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>VIP's</td>");
    print("<td class='rowhead' align='right'>$Vip</td>");
    print("<td class='rowhead'>Uploaders</td>");
    print("<td class='rowhead' align='right'>$Uploaders</td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Moderators</td>");
    print("<td class='rowhead' align='right'>$Moderator</td>");
    print("<td class='rowhead'>Administrators</td>");
    print("<td class='rowhead' align='right'>$Adminisitrator</td>");
    print("</tr>");

    print("<tr>");
    print("<td class='rowhead'>Sysops</td>");
    print("<td class='rowhead' align='right'>$Sysop</td>");
    print("<td class='rowhead'>Managers</td>");
    print("<td class='rowhead' align='right'>$Manager</td>");
    print("</tr>");

    print("</table>");
    print("</td>");
    print("</tr>");
    print("</table>");
    print("<br />");
}
//-- End Stats With Classes ---//

//-- Start Users on Index - Credits Bigjoos --//
    $active3 = "";
    $file    = CACHE_DIR."active.txt";
$expire  = 30; //-- 30 Seconds --//

if (file_exists($file) && filemtime($file) > (time() - $expire))
{
    $active3 = unserialize(file_get_contents($file));
}
else
{
    $dt      = sqlesc(get_date_time(gmtime() - 180));
    $active1 = sql_query("SELECT id, username, class, warned, enabled, added, donor
                            FROM users
                            WHERE last_access >= $dt
                            ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);

while ($active2 = mysql_fetch_assoc($active1))
{
    $active3[] = $active2;
}

    $OUTPUT = serialize($active3);
    $fp     = fopen($file, "w");

    fputs($fp, $OUTPUT);
    fclose($fp);
}

$activeusers = "";

if (is_array($active3))
{
    foreach ($active3
             AS
             $arr)
    {
        if ($activeusers)
        {
            $activeusers .= ",\n";
        }

        $activeusers .= "".format_username($arr)."";
    }
}

$fh     = fopen(CACHE_DIR."active.txt", "r");
$string = file_get_contents(CACHE_DIR."active.txt");
$count  = preg_match_all('/username/', $string, $dummy);

if (!$activeusers)
{
    $activeusers = "Sorry - No Users Presently Active ";
}

?>
    <h2>Active Users - <?php echo ($count)?> Online</h2>
    <table border='1' width='100%' cellpadding='10' cellspacing='0'>
        <tr class='table'>
            <td class='text'><?php echo $activeusers?></td>
        </tr>
    </table>

<?php
//-- End Users on Index --//

//-- Cached Last24 by putyn --//
function last24hours()
{
    global $CURUSER, $last24cache, $last24record;

    $last24cache   = CACHE_DIR.'last24/'.date('dmY').'.txt';
    $last24record  = CACHE_DIR.'last24/last24record.txt';
    $_last24       = (file_exists($last24cache) ? unserialize(file_get_contents($last24cache)) : array());
    $_last24record = (file_exists($last24record) ? unserialize(file_get_contents($last24record)) : array('num' => 0,
                                                                                                         'date' => 0));

    if (!isset($_last24[$CURUSER['id']]) || empty($_last24[$CURUSER['id']]))
    {
        $_last24[$CURUSER['id']] = array($CURUSER['username'],
                                             $CURUSER['class']);

        $_newcount = count($_last24);

        if (isset($_last24record['num']) && $_last24record['num'] < $_newcount)
        {
            $_last24record['num']  = $_newcount;
            $_last24record['date'] = time();

            file_put_contents($last24record, serialize($_last24record));
        }
        file_put_contents($last24cache, serialize($_last24));
    }
}

//-- Cached Last24 by putyn --//
function las24hours_display()
{
    global $CURUSER, $last24cache, $last24record;

    $_last24       = (file_exists($last24cache) ? unserialize(file_get_contents($last24cache)) : array());
    $_last24record = (file_exists($last24record) ? unserialize(file_get_contents($last24record)) : array('num' => 0,
                                                                                                         'date' => 0));
    $txt = '';

    $str = file_get_contents(CACHE_DIR.'last24/'.date('dmY').'.txt');
    //$_matches = preg_match_all('/a:2/', $str, $dummy);

if (!is_array($_last24))
{
    $txt = 'No 24 Hour Record';
}
else
{
    //$txt .= '<h2>Active Users in the Last 24hrs - ('.$_matches.') </h2>'
        $txt .= "<h2>Active Users in the Last 24hrs</h2><table border='1' width='100%' cellpadding='10' cellspacing='0'><tr class='table'><td class='text'><span>";

    $c = count($_last24);
    $i = 0;

        foreach ($_last24
                 AS
                 $id => $username)
    {

            $txt .= '<a class="altlink_user" href="./userdetails.php?id='.$id.'"><span style="font-weight:bold; color : #'.get_user_class_color($username[1]).'">'.$username[0].'</span></a>'.(($c - 1) == $i ? '' : ',')."\n";

        $i++;
    }

        $txt .= '</span></td></tr>';
        $txt .= '<tr class="table"><td class="rowhead" align="center"><span>Most ever Visited in 24 Hours was '.$_last24record['num'].' Members : '.get_date_time($_last24record['date'], 'DATE').' </span></td></tr></table><br />';
    }
    return $txt;
}

last24hours();
print las24hours_display();

if (isset($CURUSER))
{
    ?>
    <h2>Disclaimer</h2>
    <table border='1' width='100%' cellspacing='0' cellpadding='10'>
        <tr>
            <td align='center'>
                <span style='font-weight:bold;'>None of the files shown here are actually hosted on this server. The links are provided solely by this site's users.  The administrator of this site <span style='color : #ff0000'><?php echo $site_name?></span>
                cannot be held responsible for what its users post, or any other actions of its users. You may not use
                this site to distribute or download any material when you do not have the legal rights to do so. It is
                your own responsibility to adhere to these terms.</span>
            </td>
        </tr>
    </table>
<br/>

    </td></tr></table>

<?php

}

site_footer();

?>