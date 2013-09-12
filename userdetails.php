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
require_once(FUNC_DIR.'function_torrenttable.php');
require_once(FUNC_DIR.'function_bbcode.php');
require_once(FUNC_DIR.'function_page_verify.php');

db_connect(false);
logged_in();

$newpage = new page_verify();
$newpage->create('_modtask_');

function snatchtable($res)
{
    global $image_dir;

    $table = "<table class='main' border='1' width='100%' cellspacing='0' cellpadding='5'>
                <tr>
                    <td class='colhead' width='5%' align='center'>Category</td>
                    <td class='colhead' align='center'>Torrent</td>
                    <td class='colhead' align='center'>Up.</td>
                    <td class='colhead' align='center'>Rate</td>
                    <td class='colhead' align='center'>Downl.</td>
                    <td class='colhead' align='center'>Rate</td>
                    <td class='colhead' align='center'>Ratio</td>
                    <td class='colhead' align='center'>Activity</td>
                    <td class='colhead' align='center'>Finished</td>
                </tr>";

    while ($arr = mysql_fetch_assoc($res))
    {
        $upspeed   = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));

        $downspeed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));

        $ratio     = ($arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 3) : ($arr["uploaded"] > 0 ? "Inf." : "---"));
        $table     .= "<tr>
                    <td class='rowhead' align='center' style='padding: 0px'>
                        <img src='".$image_dir."caticons/".htmlspecialchars($arr["catimg"])."' width='60' height='54' border='0' alt='".htmlspecialchars($arr["catname"])."' title='".htmlspecialchars($arr["catname"])."' />
                    </td>
                    <td class='rowhead' align='center'>
                        <a href='details.php?id=$arr[torrentid]'><strong>".(strlen($arr["name"]) > 50 ? substr($arr["name"], 0, 50 - 3)."..." : $arr["name"])."</strong></a>
                    </td>
                    <td class='rowhead' align='center'>".mksize($arr["uploaded"])."</td>
                    <td class='rowhead' align='center'>$upspeed/s</td>
                    <td class='rowhead' align='center'>".mksize($arr["downloaded"])."</td>
                    <td class='rowhead' align='center'>$downspeed/s</td>
                    <td class='rowhead' align='center'>$ratio</td>
                    <td class='rowhead' align='center'>".mkprettytime($arr["seedtime"] + $arr["leechtime"])."</td>
                    <td class='rowhead' align='center'>
                        ".($arr["complete_date"] <> "0000-00-00 00:00:00" ? "<font color='green'><strong>Yes</strong></font>" : "<font color='red'><strong>No</strong></font>")."
                    </td>
                </tr>";
    }
    $table .= "</table>\n";

    return $table;
}

function snatch_table($res)
{
    global $image_dir;

    $table1 = "<table class='main' border='1' width='100%' cellspacing='0' cellpadding='5'>
                <tr>
                    <td class='colhead' align='center'>Category</td>
                    <td class='colhead' align='center'>Torrent</td>
                    <td class='colhead' align='center'>S / L</td>
                    <td class='colhead' align='center'>Up / Down</td>
                    <td class='colhead' align='center'>Torrent Size</td>
                    <td class='colhead' align='center'>Ratio</td>
                    <td class='colhead' align='center'>Client</td>
                </tr>";

    while ($arr = mysql_fetch_assoc($res))
    {
        //-- Speed Color Red Fast Green Slow ;) --//
        if ($arr["upspeed"] > 0)
        {
            $ul_speed = ($arr["upspeed"] > 0 ? mksize( $arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize( $arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
        }
        else
        {
            $ul_speed = mksize(($arr["uploaded"] / ($arr['l_a'] - $arr['s'] + 1)));
        }

        if ($arr["downspeed"] > 0)
        {
            $dl_speed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize( $arr["downloaded"] / $arr["leechtime"] ) : mksize(0)));
        }
        else
        {
            $dl_speed = mksize(($arr["downloaded"] / ($arr['c'] - $arr['s'] + 1)));
        }

        if ($arr["downloaded"] > 0)
        {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
            $ratio = "<font color='".get_ratio_color( $ratio )."'><b>Ratio:</b><br />$ratio</font>";
        }
        else
        {
            if ($arr["uploaded"] > 0)
            {
                $ratio = "Inf.";
            }
            else
            {
                $ratio = "N/A";
            }
        }
        $table1 .= "<tr>
            <td align='center'>
                ".($arr['owner'] == $id ? "<b>Torrent Owner</b><br />" : "
                ".($arr['complete_date'] != '0000-00-00 00:00:00' ? "<b>Finished</b><br />" : "<b>Not Finished</b><br />")."")."
                <img src='".$image_dir."caticons/".htmlspecialchars($arr["image"])."' width='60' height='54' border='0' alt='".htmlspecialchars($arr["name"])."' title='".htmlspecialchars($arr["name"])."' />
            </td>

            <td align='center'>
                <a class='altlink' href='details.php?id=$arr[torrentid]'><b>$arr[torrent_name]</b></a>
                ".($arr['complete_date'] != '0000-00-00 00:00:00' ? "<br />

                <b>Started: </b><br />".$arr['start_date']."<br />

                <b>Completed: </b><br />".$arr['complete_date']."" : "<br />

                <b>Last Action: </b>".$arr['last_action']."
                ".($arr['complete_date'] == '0000-00-00 00:00:00' ? "
                ".($arr['owner'] == $id ? "" : "[ ".mksize( $arr["size"] - $arr["downloaded"])." still to go ]")."" : "")."")."
                ".($arr['complete_date'] != '0000-00-00 00:00:00' ? "<br />

                <b>Time to Download: </b>".($arr['leechtime'] != '0' ? mkprettytime($arr['leechtime']) : mkprettytime($arr['c'] - $arr['s'])."" )."<br />

                [ DLed @ $dl_speed ]<br />" : "<br />" )."

                ".( $arr['seedtime'] != '0' ? "<b>Total Seeding Time: </b>".mkprettytime($arr['seedtime'])." " : "<b>Total Seeding Time: </b>N/A" )."<br />

                [ ULed @ ".$ul_speed." ]

                ".($arr['complete_date'] == '0000-00-00 00:00:00' ? "<br />

                <b>Download Speed:</b> $dl_speed" : "")."
            </td>

            <td align='center'>
                <font color='#0000ff'>Seeds: <b>".$arr['seeders']."</b></font><br />
                <font color='#ff0000'>Leech: <b>".$arr['leechers']."</b></font>
            </td>

            <td align='center'>
                <font color='#0000ff'>Uploaded:<br /><b>".$uploaded = mksize($arr["uploaded"])."</b></font><br />
                <font color='#ff0000'>Downloaded:<br /><b>".$downloaded = mksize($arr["downloaded"])."</b></font>
            </td>

            <td align='center'>
                <font color='#0000ff'>".mksize($arr["size"])."</font><br /><b>Difference of:</b><br /><b>
                <font color='#ff0000'>".mksize($arr['size'] - $arr["downloaded"])."</font></b>
            </td>

            <td align='center'>$ratio<br />".($arr['seeder'] == 'yes' ? "
                <font color='#0000ff'><b>Seeding</b></font>" : "
                <font color='#ff0000'><b>NOT Seeding</b></font>")."
            </td>

            <td align='center'>
                ".$arr["agent"]."<br /><b>Port: </b>".$arr["port"]."<br />

                ".($arr["connectable"] == 'yes' ? "<b>Connectable: <font color='green'>Yes</font></b>" : "<b>Connectable:</b> <font color='#ff0000'><b>No</b></font>")."
            </td>
        </tr>\n";
    }
    $table1 .= "</table>\n";

    return $table1;
}

function maketable($res)
{
    global $image_dir;

    $ret = "<table class='main' border='1' width='100%' cellspacing='0' cellpadding='5'>
                <tr>
                    <td class='colhead' width='5%' align='center'>Category</td>
                    <td class='colhead' align='center'>Name</td>
                    <td class='colhead' align='center' width='7%'>Size</td>
                    <td class='colhead' align='right' width='5%'>Se.</td>
                    <td class='colhead' align='right' width='5%'>Le.</td>
                    <td class='colhead' align='center' width='7%'>Upl.</td>
                    <td class='colhead' align='center' width='7%'>Downl.</td>
                    <td class='colhead' align='center' width='7%'>Ratio</td>
                </tr>\n";

    foreach ($res
             AS
             $arr)
    {
        if ($arr["downloaded"] > 0)
        {
            $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
            $ratio = "<font color='".get_ratio_color($ratio)."'>$ratio</font>";
        }
        else
        {
            if ($arr["uploaded"] > 0)
            {
                $ratio = "Inf.";
            }
            else
            {
                $ratio = "---";
            }
        }

        $catimage = "{$image_dir}caticons/{$arr['image']}";
        $catname  = htmlspecialchars($arr["catname"]);
        $catimage = "<img src='".htmlspecialchars($catimage)."' width='60' height='54' border='0' alt='$catname' title='$catname' />";

/*
        $ttl = (28*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
        if ($ttl == 1) $ttl .= "<br />hour"; else $ttl .= "<br />hours";
*/
        $size       = str_replace(" ", "<br />", mksize($arr["size"]));
        $uploaded   = str_replace(" ", "<br />", mksize($arr["uploaded"]));
        $downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));
        $seeders    = number_format($arr["seeders"]);
        $leechers   = number_format($arr["leechers"]);

        $ret .= "<tr>
                    <td class='rowhead' align='center' style='padding: 0px'>$catimage</td>
                    <td class='rowhead' align='left'><a href='details.php?id=$arr[torrent]&amp;hit=1'><strong>".htmlspecialchars($arr["torrentname"])."</strong></a></td>
                    <td class='rowhead' align='center'>$size</td>
                    <td class='rowhead' align='right'>$seeders</td>
                    <td class='rowhead' align='right'>$leechers</td>
                    <td class='rowhead' align='center'>$uploaded</td>
                    <td class='rowhead' align='center'>$downloaded</td>
                    <td class='rowhead' align='center'>$ratio</td>
                </tr>\n";
    }
    $ret .= "</table>\n";
    return $ret;
}

$id = 0 + $_GET["id"];

if (!is_valid_id($id))
{
    error_message("error", "Error", "Bad ID $id.");
}

$r = @sql_query("SELECT *
                    FROM users
                    WHERE id=".mysql_real_escape_string($id)) or sqlerr();

$user = mysql_fetch_assoc($r) or error_message("error", "Error", "No user with ID $id.");

if ($user["status"] == "pending")
{
    die;
}

$r = sql_query("SELECT id, name, seeders, leechers, category
                FROM torrents
                WHERE owner = $id
                ORDER BY name") or sqlerr();

if (mysql_num_rows($r) > 0)
{
    $torrents = "<table class='main' border='1' width='100%' cellspacing='0' cellpadding='5'>
    <tr>
        <td class='colhead' align='center' width='5%' style='padding: 0px'>Category</td>
        <td class='colhead' align='center'>Name</td>
        <td class='colhead' align='center' width='10%'>Seeders</td>
        <td class='colhead' align='center' width='10%'>Leechers</td>
    </tr>";

    while ($a = mysql_fetch_assoc($r))
    {
        $r2 = sql_query("SELECT name, image
                            FROM categories
                            WHERE id = $a[category]") or sqlerr(__FILE__, __LINE__);

        $a2       = mysql_fetch_assoc($r2);
        $cat      = "<img src='".htmlspecialchars("{$image_dir}caticons/{$a2['image']}")."' width='60' height='54' border='0' alt='".htmlspecialchars($a2["name"])."' title='".htmlspecialchars($a2["name"])."' />";
        $torrents .= "<tr>
                        <td class='rowhead' style='padding: 0px'>$cat</td>
                        <td class='rowhead' align='left' style='padding: 0px'>
                            <a href='details.php?id=".$a["id"]."&amp;hit=1'>&nbsp;<strong>".htmlspecialchars($a["name"])."</strong></a>
                        </td>
                        <td class='rowhead' align='right'>$a[seeders]</td>
                        <td class='rowhead' align='right'>$a[leechers]</td>
                    </tr>";
    }
    $torrents .= "</table>";
}

if ($user["ip"] && (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"]))
{
    $ip  = $user["ip"];
    $dom = @gethostbyaddr($user["ip"]);

    if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"])
    {
        $addr = $ip;
    }
    else
    {
        $dom      = strtoupper($dom);
        $domparts = explode(".", $dom);
        $domain   = $domparts[count($domparts) - 2];

        if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR")
        {
            $l = 2;
        }
        else
        {
            $l = 1;
        }
        $addr = "$ip ($dom)";
    }
}

if ($user[added] == "0000-00-00 00:00:00")
{
    $joindate = 'N/A';
}
else
{
    $joindate = "$user[added] (".get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"]))." ago)";
    $lastseen = $user["last_access"];
}

if ($lastseen == "0000-00-00 00:00:00")
{
    $lastseen = "never";
}
else
{
    $lastseen .= " (".get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen))." ago)";
}

if ($user['onlinetime'] > 0)
{
    $onlinetime = time_return($user['onlinetime']);
}
else
{
    $onlinetime = "Never";
}

if ($user['last_access'] > (get_date_time(gmtime() - 60)))
{
    $status = "<span style='color : #008000;'>Online</span>";
}
else
{
    $status = "<span style='color : #ff0000;'>Offline</span>";
}

$res = sql_query("SELECT COUNT(id)
                    FROM comments
                    WHERE user=".$user['id']) or sqlerr();

$arr3            = mysql_fetch_row($res);
$torrentcomments = $arr3[0];

$res = sql_query("SELECT COUNT(id)
                    FROM posts
                    WHERE userid=".$user['id']) or sqlerr();

$arr3       = mysql_fetch_row($res);
$forumposts = $arr3[0];
$country    = '';

$res = sql_query("SELECT name,flagpic
                    FROM countries
                    WHERE id=".$user['country']."
                    LIMIT 1") or sqlerr();

if (mysql_num_rows($res) == 1)
{
    $arr     = mysql_fetch_assoc($res);
    $country = "<td class='embedded'><img src='{$image_dir}flag/{$arr[flagpic]}' width='32' height='20' border='0' alt='".htmlspecialchars($arr[name])."' title='".htmlspecialchars($arr[name])."' style='margin-left: 8pt' /></td>";
}

$res = sql_query("SELECT p.torrent, p.uploaded, p.downloaded, p.seeder, t.added, t.name AS torrentname, t.size, t.category, t.seeders, t.leechers, c.name AS catname, c.image
                    FROM peers p
                    LEFT JOIN torrents t ON p.torrent = t.id
                    LEFT JOIN categories c ON t.category = c.id
                    WHERE p.userid = $id") or sqlerr();

while ($arr = mysql_fetch_assoc($res))
{
    if ($arr['seeder'] == 'yes')
    {
        $seeding[] = $arr;
    }
    else
    {
        $leeching[] = $arr;
    }
}

if ($user["downloaded"] > 0)
{
    $sr = $user["uploaded"] / $user["downloaded"];
    if ($sr >= 4)
        $s = "cool";

    else if ($sr >= 2)
        $s = "grin";

    else if ($sr >= 1)
        $s = "happy";

    else if ($sr >= 0.5)
        $s = "expressionless";

    else if ($sr >= 0.25)
        $s = "sad";

    else
        $s = "reallyevil";

    $sr = floor($sr * 1000) / 1000;
    $sr = "<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'><font color='".get_ratio_color($sr)."'>".number_format($sr, 3)."</font></td><td class='embedded'>&nbsp;&nbsp;<img src='{$image_dir}smilies/{$s}.png' width='16' height='16' border='0' alt='$s' title='$s' /></td></tr></table>";
}

//-- Connectable And Port Shit --//
$q1 = sql_query('SELECT connectable, port,agent
                    FROM peers
                    WHERE userid = '.$id.' LIMIT 1') or sqlerr();

if ($a = mysql_fetch_row($q1))
{
    $connect = $a[0];

    if ($connect == "yes")
    {
        $connectable = "<font color='green'><span style='font-weight:bold;'>Yes</span></font>";
    }
    else
    {
        $connectable = "<font color='red'><span style='font-weight:bold;'>No</span></font>";
    }
}
else
{
    $connectable = "<img src='".$image_dir."smilies/expressionless.png' width='16' height='16' border='0' alt='Unknown' title='Not Connected To Peers' style='border:none;padding:2px;' /><font color='blue'><span style='font-weight:bold;'>Unknown</span></font>";
}

site_header("Details for ".$user["username"]);

//-- Start Reset Members Password Part 1 Of 2 --//
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = trim($_POST["username"]);

    $res = sql_query("SELECT *
                        FROM users
                        WHERE username=".sqlesc($username)." ") or sqlerr();

    $arr = mysql_fetch_assoc($res);

    $nick         = ($username . rand(1000, 9999));
    $id           = $arr['id'];
    $wantpassword = "$nick";
    $secret       = mksecret();
    $wantpasshash = md5($secret . $wantpassword . $secret);

    sql_query("UPDATE users
                SET passhash ='$wantpasshash'
                WHERE id=$id");

    sql_query("UPDATE users
                SET secret = '$secret'
                WHERE id=$id");

    // write_log("Password Reset For $username by $CURUSER[username]");
    if (mysql_affected_rows() != 1)
        error_message("warn", "Warning", "Unable to RESET PASSWORD on this Account.");

    error_message("success", "Success", "The Password for <strong>$username</strong> has been Reset to <br /><br /><strong>$nick</strong><br /><br /> please inform the member of this change.");
}
//-- Finish Reset Members Password Part 1 Of 2 --//

$enabled = $user["enabled"] == 'yes';

print("<br /><table class='main' border='0' align='center' cellspacing='0' cellpadding='0'>");
print("<tr><td class='embedded'><h1 style='margin:0px'>{$user['username']}".get_user_icons($user, true)."</h1></td>$country</tr>");
print("</table><br />");
print("<table class='main' border='0' align='center' cellspacing='0' cellpadding='0'>");

if (!empty($user["avatar"]))
{
    print("<tr><td class='rowhead' align='left'><img src='".htmlspecialchars($user["avatar"])."' width='125' height='125' border='0' alt='' title='' /></td></tr>");
}
else
{
    print("<tr><td class='rowhead' align='left'><img src='".$image_dir."default_avatar.gif' width='125' height='125' border='0' alt='' title='' /></td></tr>");
}

print("</table><br />");

if (!$enabled)
{
    print("<p><strong>This Account has been Disabled</strong></p>\n");
}
elseif ($CURUSER["id"] <> $user["id"])
{
    $r = sql_query("SELECT id
                    FROM friends
                    WHERE userid = $CURUSER[id]
                    AND friendid = $id") or sqlerr(__FILE__, __LINE__);

    $friend = mysql_num_rows($r);

    $r = sql_query("SELECT id
                    FROM blocks
                    WHERE userid = $CURUSER[id]
                    AND blockid = $id") or sqlerr(__FILE__, __LINE__);

    $block = mysql_num_rows($r);

    if ($friend)
    {
        print("<div align='center'>(<a href='friends.php?action=delete&amp;type=friend&amp;targetid=$id'>Remove from Friends</a>)</div>\n");
    }
    elseif ($block)
    {
        print("<div align='center'>(<a href='friends.php?action=delete&amp;type=block&amp;targetid=$id'>Remove from Blocks</a>)</div>\n");
    }
    else
    {
        print("<div align='center'><a href='friends.php?action=add&amp;type=friend&amp;targetid=$id' class='btn'>Add to Friends</a>");
        print("&nbsp;&nbsp;&nbsp;&nbsp;<a href='friends.php?action=add&amp;type=block&amp;targetid=$id' class='btn'>Add to Blocks</a></div><br />\n");
    }
}

if ($user[enabled] == "yes")
{
    if (get_user_class() >= UC_SYSOP && $user["class"] < get_user_class())

    print("<a href='quickban.php?id={$user['id']}' class='btn'>Quick Ban</a><br /><br />");
}

if ($user["pcoff"] == 'yes')
{
    print("<font size='2' color='green'><strong>My PC is On at Night.</strong></font>");
}
else
{
    print("<font size='2' color='red'><strong>My PC is Off at Night.</strong></font>");
}

if ( $user["parked"] == "yes" )
{
    print("<br /><br /><font size='2' color='red'><strong>This Account is Parked.</strong></font>");
}

print("<div align='center' id='featured'>");
print("<br />");
print("<ul>
       <li><a class='btn' href='#fragment-1'>General</a></li>
       <li><a class='btn' href='#fragment-2'>Torrents</a></li>
       <li><a class='btn' href='#fragment-3'>Info</a></li>
       ");

if (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"])
{
    print("<li><a class='btn' href='#fragment-4'>Snatch List</a></li>");
}

if (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"])
{
    print("<li><a class='btn' href='#fragment-8'>Invited Members</a></li>");
}

if (get_user_class() >= UC_SYSOP && $user["class"] < get_user_class())
{
    print("<li><a class='btn' href='#fragment-5'>Alter Ratio</a></li>");
}

if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
{
    print("<li><a class='btn' href='#fragment-6'>Edit User</a></li>");
}

if (get_user_class() >= UC_SYSOP && $user["class"] < get_user_class())
{
    print("<li><a class='btn' href='#fragment-7'>Reset Password</a></li>");
}

print("</ul>");
print("<br />");

//-- Start General Details Content --//
print("<div class='ui-tabs-panel' id='fragment-1'>");
print("<table class='coltable' width='70%'>");

print("<tr>
       <td class='std' align='center' colspan='2'><h2>General Details</h2></td>
       </tr>");

print("<tr>
       <td class='colhead' width='20%'>&nbsp;Join&nbsp;Date</td>
       <td class='rowhead' align='left' width='99%'>&nbsp;$joindate</td>
       </tr>");

print("<tr>
       <td class='colhead' width='20%'>&nbsp;Last&nbsp;Seen</td>
       <td class='rowhead' align='left'>&nbsp;$lastseen</td>
       </tr>");

print("<tr>
        <td class='colhead'>&nbsp;Time Online</td>
        <td class='rowhead' align='left'>&nbsp;$onlinetime</td>
        </tr>");

print("<tr>
       <td class='colhead'>&nbsp;Status</td>
       <td class='rowhead' align='left'>&nbsp;$status</td>
       </tr>");

if (get_user_class() >= UC_MODERATOR && $user['invitedby'] > 0 || $user['id'] == $CURUSER['id'] && $user['invitedby'] > 0)
{
    $invitedby  = sql_query('SELECT username
                                FROM users
                                WHERE id = '. sqlesc($user['invitedby']));

    $invitedby2 = mysql_fetch_array($invitedby);

    print("<tr>
            <td class='colhead'>&nbsp;Invited by</td>
            <td class='rowhead' align='left'>
                &nbsp;<a href='userdetails.php?id=".$user['invitedby']."'>".htmlspecialchars($invitedby2['username'])."</a>
            </td>
        </tr>");
}

if ( get_user_class() >= UC_MODERATOR )
{
    print("<tr>
        <td class='colhead' width='20%'>&nbsp;Email</td>
        <td class='rowhead' align='left'>
            <a href='mailto:$user[email]'>&nbsp;$user[email]</a>
        </td>
        </tr>");

    print("<tr>
        <td class='colhead'>&nbsp;Address</td>
        <td class='rowhead' align='left'>&nbsp;$addr</td>
        </tr>");
}

    if ($site_reputation == true)
    {
        print("<tr>
                <td class='colhead'>&nbsp;Give Reputation</td>
                <td class='rowhead' align='left'>
                    <form method='post' action='takereppoints.php?id=$user[id]'>
                        <input type='submit' class='btn' name='givepoints' value='Give A Reputation Point' />
                    </form>
                </td>
            </tr>");

        print("<tr>
                <td class='colhead'>&nbsp;Current Reputation</td>
                <td class='rowhead' align='left'>&nbsp;Reputation Points : $user[reputation]");

        $total   = 0 + $user["reputation"];
        $nbrpics = 0 + $total / 5;
        $nbrpics = (int) $nbrpics;

        while ($nbrpics > 0)
        {
            echo "&nbsp;<img src='".$image_dir."rep.png' width='24' height='25' border='0' alt='Reputation' title='Reputation' />&nbsp;";
            $nbrpics = 0 + $nbrpics - 1;
        }
        print("<br /></td></tr>");

    }

if ($user["title"])
{
    print("<tr>
           <td class='colhead' width='20%'>&nbsp;Title</td>
           <td class='rowhead' align='left'>&nbsp;".htmlspecialchars($user["title"])."</td>
        </tr>");
}

print("<tr>
       <td class='colhead' width='20%'>&nbsp;Class</td>
       <td class='rowhead' align='left'>&nbsp;".get_user_class_name($user["class"])."</td>
    </tr>");

if ($user["supportfor"])
{
    print("<tr>
            <td class='colhead'>&nbsp;First Line Support</td>
            <td class='rowhead' align='left'>&nbsp;".htmlspecialchars($user["supportfor"])."</td>
        </tr>");
}

print("<tr>
       <td class='colhead' width='20%'>&nbsp;Forum&nbsp;Posts</td>");

if ($forumposts && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
{
    print("<td class='rowhead' align='left'><a href='userhistory.php?action=viewposts&amp;id=$id'>&nbsp;$forumposts</a></td>
       </tr>");
}
else
{
    print("<td class='rowhead' align='left'>&nbsp;$forumposts</td>
       </tr>");
}

if ($CURUSER["id"] != $user["id"])
{
    if (get_user_class() >= UC_MODERATOR)
    {
        $showpmbutton = 1;
    }
}

if ($user["acceptpms"] == "yes")
{
    $r = sql_query("SELECT id
                    FROM blocks
                    WHERE userid = {$user['id']}
                    AND blockid= {$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);

    $showpmbutton = (mysql_num_rows($r) == 1 ? 0 : 1);
}

if ($user["acceptpms"] == "friends")
{
    $r = sql_query("SELECT id
                    FROM friends
                    WHERE userid = {$user['id']}
                    AND friendid = {$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);

    $showpmbutton = (mysql_num_rows($r) == 1 ? 1 : 0);
}

if ($user["id"] != $CURUSER["id"])
{
    print("<tr>
            <td class='std' align='center' colspan='2'>
                <form method='post' action='report.php?type=User&amp;id=$id'>
                    <input type='submit' class='btn' value='Report User' />
                </form>
            </td>
        </tr>");

    if (isset($showpmbutton))
    {
        print("<tr>
                <td class='std' align='center' colspan='2'>
                    <form method='get' action='sendmessage.php'>
                        <input type='hidden' name='receiver' value='".$user["id"]."' />
                        <input type='submit' class='btn' value='Send Message' style='height: 23px' />
                    </form>
                </td>
            </tr>");
    }
}

print("</table>");
print("</div>");
//-- Finish General Details Content --//

//-- Start Torrent Details Content --//
print("<div class='ui-tabs-panel' id='fragment-2'>");
print("<table class='coltable' width='70%'>");

print("<tr>
       <td class='std' align='center' colspan='2'><h2>Torrent Details</h2></td>
    </tr>");

$port  = $a[1];
$agent = $a[2];

if (!empty($port))
{
    print("<tr>
            <td class='colhead'>&nbsp;Port</td><td align='left'>&nbsp;$port</td>
        </tr>
        <tr>
            <td class='colhead'>&nbsp;Client</td>
            <td align='left'>&nbsp;".htmlentities($agent)."</td>
        </tr>");

    print("<tr>
            <td class='colhead' width='20%'>&nbsp;Connectable</td>
            <td class='rowhead' align='left'>&nbsp;".$connectable."</td>
       </tr>");
}

    print("<tr>
            <td class='colhead' width='20%'>&nbsp;Uploaded</td>
            <td class='rowhead' align='left'>&nbsp;".mksize($user["uploaded"])."</td>
       </tr>");

    print("<tr>
            <td class='colhead' width='20%'>&nbsp;Downloaded</td>
            <td class='rowhead' align='left'>&nbsp;".mksize($user["downloaded"])."</td>
       </tr>");

    print("<tr>
            <td class='colhead' width='20%'>&nbsp;Share Ratio</td>
            <td class='rowhead' align='left'>&nbsp;$sr</td>
       </tr>");

if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
{
    print("<tr>
            <td class='colhead' width='20%'>&nbsp;Torrent Comments</td>");
    print("<td class='rowhead' align='left'><a href='userhistory.php?action=viewcomments&amp;id=$id'>&nbsp;$torrentcomments</a></td>
    </tr>");
}
else
{
    print("<tr>
            <td class='colhead' width='20%'>&nbsp;Torrent Comments</td>");
    print("<td class='rowhead' align='left'>&nbsp;$torrentcomments</td>
    </tr>");
}

print("<tr>
        <td class='colhead' width='20%'>&nbsp;Flush</td>
        <td align='left'><a href='flushghosts.php?id={$user['id']}'>&nbsp;<strong>Flush Ghost Torrents Now.</strong></a>  (Use if Seeding / Leeching is shown wrong.)</td>
    </tr>");

print("</table>");
print("</div>");
//-- Finish Torrent Details Content --//

//-- Start Information Details Content --//
print("<div class='ui-tabs-panel' id='fragment-3'>");
print("<table class='coltable' width='70%'>");

print("<tr>
        <td class='std' align='center' colspan='2'><h2>Information Details</h2></td>
    </tr>");

if ($user["info"])
{
print("<tr>
       <td class='colhead' width='20%'>&nbsp;Members Shared Infomation</td>
       <td class='rowhead' align='left'>&nbsp;".format_comment($user["info"])."</td>
    </tr>");
}

if ($user["signature"])
{
print("<tr>
        <td class='colhead' width='20%'>&nbsp;Signature</td>
        <td class='rowhead' align='left'>&nbsp;".format_comment($user["signature"])."</td>
    </tr>");
}

print("</table>");
print("</div>");
//-- Finish Information Details Content --//

//-- Start Snatch List Details Content --//
if (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"])
{
    print("<div class='ui-tabs-panel' id='fragment-4'>");
    print("<table class='coltable' width='70%'>");

    print("<tr>
            <td class='std' align='center' colspan='2'><h2>Snatched Details</h2></td>
        </tr>");

    //-- Start Recently Snatched Expanding Table --//
    $snatches = "";

    $r= sql_query("SELECT id, name, seeders, leechers, category
                    FROM torrents
                    WHERE owner=$id
                    ORDER BY name") or sqlerr();

    if (mysql_num_rows($r) > 0)
    {
        $numbupl = mysql_num_rows($r);
    }

    if (isset($torrents))
    {
        print("<tr valign='top'>
                <td class='colhead' width='10%'>&nbsp;Uploaded&nbsp;Torrents&nbsp;</td>
                <td class='rowhead' align='left' colspan='90%'>
                    <a href=\"javascript: klappe_news('a1')\"><img src='".$image_dir."plus.png' width='16' height='16' border='0' id='pica1' alt='Show/Hide' title='Show/Hide' /></a>
                    <strong><font color='red'>&nbsp;&nbsp;$numbupl</font></strong>
                    <div id='ka1' style='display: none; overflow: auto; width: 100%; height: 200px'>$torrents</div>
                </td>
            </tr>");
    }

    //-- Start Expanding Currently Seeding --//
    if (mysql_num_rows($res) > 0)
    {
        $numbseeding = mysql_num_rows($res);
    }

    if (isset($seeding))
    {
        print("<tr valign='top'>
                <td class='colhead' width='10%'>&nbsp;Currently Seeding&nbsp;</td>
                <td class='rowhead' align='left' colspan='90%'>
                    <a href=\"javascript: klappe_news('a2')\"><img src='".$image_dir."plus.png' width='16' height='16' border='0' id='pica2' alt='Show/Hide' title='Show/Hide' /></a>
                    <span style='font-weight:bold;'><font color='red'>&nbsp;&nbsp;$numbseeding</font></span>
                    <div id='ka2' style='display: none; overflow: auto; width: 100%; height: 200px'>&nbsp".maketable($seeding)."</div>
                </td>
            </tr>");
    }
    //-- Finish Expanding Currently Seeding --//

    //-- Start Expanding Currently Leeching --//
    if (mysql_num_rows($res) > 0)
    {
        $numbleeching = mysql_num_rows($res);
    }

    if (isset($leeching))
    {
        print("<tr valign='top'>
                <td class='colhead' width='10%'>&nbsp;Currently Leeching&nbsp;</td>
                <td class='rowhead' align='left' width='90%'>
                <a href=\"javascript: klappe_news('a3')\"><img src='".$image_dir."plus.png' width='16' height='16' border='0' id='pica3' alt='Show/Hide' title='Show/Hide' /></a>
                <strong><font color='red'>&nbsp;&nbsp;$numbleeching</font></strong>
                <div id='ka3' style='display: none; overflow: auto; width: 100%; height: 200px'>&nbsp;".maketable($leeching)."</div>
                </td>
            </tr>");
    }
    //-- Finish Expanding Currently Leeching --//

    //-- Start Snatched Table --//
    $snatches = "";

    $res = sql_query("SELECT s.*, t.name AS name, c.name AS catname, c.image AS catimg
                        FROM snatched AS s
                        INNER JOIN torrents AS t ON s.torrentid = t.id
                        LEFT JOIN categories AS c ON t.category = c.id
                        WHERE s.userid = $user[id]") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
        $snatches = snatchtable($res);
    }

    $numbsnatched = mysql_num_rows($res);

    if (isset($snatches))
    {
        print("<tr valign='top'>
                <td class='colhead' width='10%'>&nbsp;Recently&nbsp;Snatched&nbsp;</td>
                <td class='rowhead' align='left' width='90%'>
                    <a href=\"javascript: klappe_news('a4')\"><img src='".$image_dir."plus.png' width='16' height='16' border='0'  id='pica4' alt='Show/Hide' title='Show/Hide' /></a>
                    <strong><font color='red'>&nbsp;&nbsp;$numbsnatched</font></strong>
                    <div id='ka4' style='display: none; overflow: auto; width: 100%; height: 200px'>$snatches</div>
                </td>
            </tr>");
    }
    //-- Finish Snatched Table --//

    //-- Finish Recently Snatched Expanding Table --//
    $res1 = sql_query("SELECT UNIX_TIMESTAMP(sn.start_date) AS s,
                            UNIX_TIMESTAMP(sn.complete_date) AS c,
                            UNIX_TIMESTAMP(sn.last_action) AS l_a,
                            UNIX_TIMESTAMP(sn.seedtime) AS s_t,
                            sn.seedtime,
                            UNIX_TIMESTAMP(sn.leechtime) AS l_t,
                            sn.leechtime, sn.downspeed, sn.upspeed, sn.uploaded, sn.downloaded, sn.torrentid, sn.start_date, sn.complete_date, sn.seeder, sn.last_action, sn.connectable, sn.agent, sn.seedtime, sn.port, cat.name, cat.image, t.size, t.seeders, t.leechers, t.owner, t.name AS torrent_name
                            FROM snatched AS sn
                            LEFT JOIN torrents AS t ON t.id = sn.torrentid
                            LEFT JOIN categories AS cat ON cat.id = t.category
                            WHERE sn.userid=$id
                            ORDER BY sn.start_date DESC") or die(mysql_error());

    if (mysql_num_rows($res1) > 0)
    {
        $snatched = snatch_table($res1);
    }

    $numbsnatched = mysql_num_rows($res1);

    if (isset($snatched))
    {
        print("<tr valign='top'>
                <td class='colhead' width='10%'>&nbsp;Snatched&nbsp;Status&nbsp;</td>
                <td class='rowhead' align='left' width='90%'>
                    <a href=\"javascript: klappe_news('a5')\"><img src='".$image_dir."plus.png' width='16' height='16' border='0' id='pica5' alt='Show/Hide' title='Show/Hide' /></a>
                    <strong><font color='red'>&nbsp;&nbsp;$numbsnatched</font></strong>
                    <div id='ka5' style='display: none; overflow: auto; width: 100%; height: 200px'>$snatched</div>
                </td>
            </tr>");
    }

    print("</table>");
    print("</div>");}
//-- Finish Snatch List Details Content --//

//-- Start Alter Ratio Details Content --//
if (get_user_class() >= UC_SYSOP && $user["class"] < get_user_class())
{
    print("<div class='ui-tabs-panel' id='fragment-5'>");
    print("<form method='post' action='ratio.php'>");
    print("<table class='coltable' width='70%'>");

    print("<tr>
            <td class='std' align='center' colspan='2'><h2>Alter Ratio Details</h2></td>
        </tr>");

//-- Start Create Ratio By Fireknight Based On The Original Code By Dodge --//
    print("<tr>
            <td class='colhead'>&nbsp;User Name</td>
            <td class='rowhead'>
                <input name='username' value='$user[username]' size='40' readonly='readonly' />
            </td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Uploaded</td>
            <td class='rowhead'>
                <input type='text' name='uploaded' value='0' size='40' />
            </td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Downloaded</td>
            <td class='rowhead'>
                <input type='text' name='downloaded' value='0' size='40' />
            </td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Select Input Measure</td>
            <td class='rowhead'>
                <select name='bytes'>");

    print("<option value='1'>".MBytes."</option>");
    print("<option value='2'>".GBytes."</option>");
    print("<option value='3'>".TBytes."</option>");
    print("</select></td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Action</td>
            <td class='rowhead'>
                <select name='action'>");

    print("<option value='1'>".Add."</option>");
    print("<option value='2'>".Remove."</option>");
    print("<option value='3'>".Replace."</option>");
    print("</select></td></tr>");

    print("<tr>
            <td class='std' align='center' colspan='2'>
                <input type='submit' class='btn' value='Okay' />
            </td>
        </tr>");
//-- End Create Ratio By Fireknight Based On The Original Code By Dodge --//

    print("</table>");
    print("</form>");
    print("</div>");
}
//--- Finish Alter Ratio Details Content --//

//-- Start Edit User Details Content --//
if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
{
    print("<div class='ui-tabs-panel' id='fragment-6'>");
    print("<form method='post' action='modtask.php'>");

    require_once(FUNC_DIR.'function_user_validator.php');

    print(validatorForm("ModTask_$user[id]"));
    print("<input type='hidden' name='action' value='edituser' />");
    print("<input type='hidden' name='userid' value='$id' />");
    print("<input type='hidden' name='returnto' value='userdetails.php?id=$id' />");
    print("<table class='coltable' width='70%'>");

    print("<tr>
            <td class='std' align='center' colspan='3'><h2>Edit Members Details</h2></td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Title</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='text' name='title' size='60' value='".htmlspecialchars($user[title])."' />
            </td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Username</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='text' name='username' size='60' value='".htmlspecialchars($user[username])."' />
            </td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Email</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='text' name='email' size='60' value='".htmlspecialchars($user[email])."' />
            </td>
        </tr>");

    $avatar = htmlspecialchars($user["avatar"]);

    print("<tr>
            <td class='colhead' align='left'>&nbsp;Avatar&nbsp;URL</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='text' name='avatar' size='60' value='".htmlspecialchars($user[avatar])."' />
            </td>
        </tr>");

    $info = htmlspecialchars($user["info"]);

    print("<tr>
            <td class='colhead'>&nbsp;User&nbsp;Info&nbsp;URL</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='text' name='info' size='60' value='".htmlspecialchars($user[info])."' />
            </td>
        </tr>");

    $signature = htmlspecialchars($user["signature"]);

    print("<tr>
            <td class='colhead'>&nbsp;Signature&nbsp;URL</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='text' name='signature' size='60' value='$signature' />
            </td>
        </tr>");

    //-- We Do Not Want Mods To Be Able To Change User Classes Or Amount Donated... --//
    if ($CURUSER["class"] >= UC_ADMINISTRATOR)
    {
        print("<tr>
                <td class='colhead'>&nbsp;Donor</td>
                <td class='rowhead' align='left' colspan='2'>
                    <input type='radio' name='donor' value='yes'".($user["donor"] == "yes" ? " checked='checked'" : "")." />Yes
                    <input type='radio' name='donor' value='no'".($user["donor"] == "no" ? " checked='checked'" : "")." />No
                </td>
            </tr>");
    }

    if (get_user_class() == UC_MODERATOR && $user["class"] > UC_VIP)
    {
        print("<input type='hidden' name='class' value='$user[class]' />");
    }
    else
    {
        print("<tr>
                <td class='colhead'>&nbsp;Class</td>
                <td class='rowhead' align='left' colspan='2'>
                    <select name='class'>");

        if (get_user_class() == UC_MODERATOR)
        {
            $maxclass = UC_VIP;
        }
        else
        {
            $maxclass = get_user_class() - 1;
        }

        for ($i = 0;
             $i <= $maxclass;
             ++$i)
        {
            print("<option value='$i'".($user["class"] == $i ? " selected='selected'" : "").">$prefix".get_user_class_name($i)."</option>\n");
        }

        print("</select></td></tr>\n");
    }

    //-- First Line Support --//
    print("<tr>
            <td class='colhead'>&nbsp;Support</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='radio' name='support' value='yes'" .($user["support"] == "yes" ? " checked='checked'" : "")." />Yes
                <input type='radio' name='support' value='no'" .($user["support"] == "no" ? " checked='checked'" : "")." />No
            </td>
        </tr>");

    $supportfor = htmlspecialchars($user["supportfor"]);

    print("<tr>
            <td class='colhead'>&nbsp;Support for</td>
            <td class='rowhead' align='left' colspan='2'>
                <textarea name='supportfor' cols='60' rows='3'>$supportfor</textarea>
            </td>
        </tr>");

    $support_language = "<option value=''>---- None Selected ----</option>";

    $sl_r = sql_query("SELECT name
                        FROM support_lang
                        ORDER BY name") or sqlerr(__FILE__, __LINE__);

    while ($sl_a = mysql_fetch_assoc($sl_r))
    {
        $support_language .= "<option value='{$sl_a['name']}'".($user["support_lang"] == $sl_a['name'] ? " selected='selected'" : "").">{$sl_a['name']}</option>";
    }

    print("<tr>
             <td class='colhead'>&nbsp;Support Language</td>
             <td class='rowhead' colspan='2'>
                <select name='support_lang'>$support_language</select>
             </td>
         </tr>");
    //-- End --//

    print("<tr>
            <td class='colhead'>&nbsp;Invite Rights</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='radio' name='invite_rights' value='yes'".($user["invite_rights"]=="yes" ? " checked='checked'" : "")." />Yes
                <input type='radio' name='invite_rights' value='no'".($user["invite_rights"]=="no" ? " checked='checked'" : "")." />No
            </td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Invites</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='text' size='3' name='invites' value='".htmlspecialchars($user[invites])."' />
            </td>
        </tr>");

    $modcomment = htmlspecialchars($user["modcomment"]);

    if (get_user_class() < UC_SYSOP)
    {
        print("<tr>
                <td class='colhead'>&nbsp;Comment</td>
                <td class='rowhead' align='left' colspan='2'>
                    <textarea name='modcomment' cols='60' rows='18' readonly='readonly'>$modcomment</textarea>
                </td>
            </tr>");
    }
    else
    {
        print("<tr>
                <td class='colhead'>&nbsp;Comment</td>
                <td class='rowhead' align='left' colspan='2'>
                    <textarea name='modcomment' cols='60' rows='18'>$modcomment</textarea>
                </td>
            </tr>");
    }

    print("<tr>
            <td class='colhead'>&nbsp;Add Comment</td>
            <td class='rowhead' align='left' colspan='2'>
                <textarea name='addcomment' cols='60' rows='2'></textarea>
            </td>
        </tr>");

    $warned = $user["warned"] == "yes";

    print("<tr>
            <td class='colhead'".(!$warned ? " rowspan='2'" : "").">&nbsp;Warned</td>
            <td class='rowhead' align='left' width='20%'>&nbsp;".($warned ? "
                <input type='radio' name='warned' value='yes' checked='checked' />Yes
                <input type='radio' name='warned' value='no' />No" : "No")."
            </td>");

    if ($warned)
    {
        $warneduntil = $user['warneduntil'];

        if ($warneduntil == '0000-00-00 00:00:00')
        {
            print("<td class='rowhead' align='center'>(Arbitrary Duration)</td></tr>");
        }
        else
        {
            print("<td class='rowhead' align='center'>Until $warneduntil");
            print(" (".mkprettytime(strtotime($warneduntil) - gmtime())." to go)</td></tr>");
        }
    }
    else
    {
        print("<td class='rowhead'>&nbsp;Warn for <select name='warnlength'>\n");
        print("<option value='0'>------</option>\n");
        print("<option value='1'>1 week</option>\n");
        print("<option value='2'>2 weeks</option>\n");
        print("<option value='4'>4 weeks</option>\n");
        print("<option value='8'>8 weeks</option>\n");
        print("<option value='255'>Unlimited</option>\n");
        print("</select></td></tr>");

        print("<tr>
                <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                    <input type='text' name='warnpm' size='60' />
                </td>
            </tr>");
    }

    //-- Start Upload Enable / Disable --//
    if ($user["uploadpos"] == "no")
    {
        $uploadposuntil = $user['uploadposuntil'];
        $uploadpos      = $user['uploadpos'];

        print("<tr>
                <td class='colhead'>&nbsp;Upload Enabled</td>
                <td class='rowhead' align='left' width='20%'>
                    <input type='radio' name='uploadpos' value='yes' ".(!$uploadpos ? " checked='checked'" : "")." />Yes
                    <input type='radio' name='uploadpos' value='no' ".($uploadpos ? " checked='checked'" : "")." />No
            </td>");

        if ($user["uploadposuntil"] == "0000-00-00 00:00:00")
        {
            print("<td class='rowhead'>&nbsp;<strong>Total Ban</strong> - Seek Higher Staff Advice Before You Enable!</td></tr>");
        }
        else
        {
            print("<td class='rowhead'>&nbsp;Until $uploadposuntil");
            print(" (".mkprettytime(strtotime($uploadposuntil) - gmtime())." to go)</td></tr>");
        }
    }

    if ($user["uploadpos"] == "yes")
    {
        print("<tr>
                <td class='colhead' rowspan='2'>&nbsp;Upload Enabled</td>
                <td class='rowhead'>&nbsp;Yes</td>");

        print("<td class='rowhead'>&nbsp;Disable for:-&nbsp;<select name='uploadposuntillength'>\n");
        print("<option value='0'>------</option>\n");
        print("<option value='1'>1 week</option>\n");
        print("<option value='2'>2 weeks</option>\n");
        print("<option value='4'>4 weeks</option>\n");
        print("<option value='8'>8 weeks</option>\n");
        print("<option value='255'>Unlimited</option>\n");
        print("</select></td></tr>");

        print("<tr>
                <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                    <input type='text' name='uploadposuntilpm' size='60' />
                </td>
            </tr>");
    }
    //-- Finish Upload Enable / Disable --//

    //-- Start Download Enable - Disable --//
    if ($user["downloadpos"] == "no")
    {
        $downloadposuntil = $user['downloadposuntil'];
        $downloadpos      = $user['downloadpos'];

        print("<tr>
                <td class='colhead'>&nbsp;Download Enabled</td>
                <td class='rowhead' align='left' width='20%'>
                    <input type='radio' name='downloadpos' value='yes' ".(!$downloadpos ? " checked='checked'" : "")." />Yes
                    <input type='radio' name='downloadpos' value='no' ".($downloadpos ? " checked='checked'" : "")." />No
                </td>");

        if ($user["downloadposuntil"] == "0000-00-00 00:00:00")
        {
            print("<td class='rowhead'>&nbsp;<strong>Total Ban</strong> - Seek Higher Staff Advice Before You Enable!</td></tr>");
        }
        else
        {
            print("<td class='rowhead'>&nbsp;Until $downloadposuntil");
            print(" (".mkprettytime(strtotime($downloadposuntil) - gmtime())." to go)</td></tr>");
        }
    }

    if ($user["downloadpos"] == "yes")
    {
        print("<tr>
                <td class='colhead' rowspan='2'>&nbsp;Download Enabled</td>
                <td class='rowhead'>&nbsp;Yes</td>");

        print("<td class='rowhead'>&nbsp;Disable for:-&nbsp;<select name='downloadposuntillength'>\n");
        print("<option value='0'>------</option>\n");
        print("<option value='1'>1 week</option>\n");
        print("<option value='2'>2 weeks</option>\n");
        print("<option value='4'>4 weeks</option>\n");
        print("<option value='8'>8 weeks</option>\n");
        print("<option value='255'>Unlimited</option>\n");
        print("</select></td></tr>");

        print("<tr>
                <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                    <input type='text' name='downloadposuntilpm' size='60' />
                </td>
            </tr>");
    }
    //-- Finish Download Enable - Disable --//

    //-- Start Shoutbox Enable - Disable --//
    if ($user["shoutboxpos"] == "no")
    {
        $shoutboxposuntil = $user['shoutboxposuntil'];
        $shoutboxpos      = $user['shoutboxpos'];

        print("<tr>
                <td class='colhead'>&nbsp;Shoutbox Enabled</td>
                <td class='rowhead' align='left' width='20%'>
                <input type='radio' name='shoutboxpos' value='yes' ".(!$shoutboxpos ? " checked='checked'" : "")." />Yes
                <input type='radio' name='shoutboxpos' value='no' ".($shoutboxpos ? " checked='checked'" : "")." />No
                </td>");

        if ($user["shoutboxposuntil"] == "0000-00-00 00:00:00")
        {
            print("<td class='rowhead'>&nbsp;<strong>Total Ban</strong> - Seek Higher Staff Advice Before You Enable!</td></tr>");
        }
        else
        {
            print("<td class='rowhead'>&nbsp;Until $shoutboxposuntil");
            print(" (".mkprettytime(strtotime($shoutboxposuntil) - gmtime())." to go)</td></tr>");
        }
    }

    if ($user["shoutboxpos"] == "yes")
    {
        print("<tr>
                <td class='colhead' rowspan='2'>&nbsp;Shoutbox Enabled</td>
                <td class='rowhead'>&nbsp;Yes</td>");

        print("<td class='rowhead'>&nbsp;Disable for:-&nbsp;<select name='shoutboxposuntillength'>\n");
        print("<option value='0'>------</option>\n");
        print("<option value='1'>1 week</option>\n");
        print("<option value='2'>2 weeks</option>\n");
        print("<option value='4'>4 weeks</option>\n");
        print("<option value='8'>8 weeks</option>\n");
        print("<option value='255'>Unlimited</option>\n");
        print("</select></td></tr>");

        print("<tr>
                <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                    <input type='text' name='shoutboxposuntilpm' size='60' />
                </td>
            </tr>");
    }
    //-- Finish Shoutbox Enable - Disable --//

    //-- Start Torrent Comment Enable - Disable --//
    if ($user["torrcompos"] == "no")
    {
        $torrcomposuntil = $user['torrcomposuntil'];
        $torrcompos      = $user['torrcompos'];

        print("<tr>
                <td class='colhead'>&nbsp;Comments Enabled</td>
                <td class='rowhead' align='left' width='20%'>
                <input type='radio' name='torrcompos' value='yes' ".(!$torrcompos ? " checked='checked'" : "")." />Yes
                <input type='radio' name='torrcompos' value='no' ".($torrcompos ? " checked='checked'" : "")." />No
                </td>");

        if ($user["torrcomposuntil"] == "0000-00-00 00:00:00")
        {
            print("<td class='rowhead'>&nbsp;<strong>Total Ban</strong> - Seek Higher Staff Advice Before You Enable!</td></tr>");
        }
        else
        {
            print("<td class='rowhead'>&nbsp;Until $torrcomposuntil");
            print(" (".mkprettytime(strtotime($torrcomposuntil) - gmtime())." to go)</td></tr>");
        }
    }

    if ($user["torrcompos"] == "yes")
    {
        print("<tr>
                <td class='colhead' rowspan='2'>&nbsp;Comments Enabled</td>
                <td class='rowhead'>&nbsp;Yes</td>");

        print("<td class='rowhead'>&nbsp;Disable For:-&nbsp;<select name='torrcomposuntillength'>\n");
        print("<option value='0'>------</option>\n");
        print("<option value='1'>1 week</option>\n");
        print("<option value='2'>2 weeks</option>\n");
        print("<option value='4'>4 weeks</option>\n");
        print("<option value='8'>8 weeks</option>\n");
        print("<option value='255'>Unlimited</option>\n");
        print("</select></td></tr>");

        print("<tr>
                <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                    <input type='text' name='torrcomposuntilpm' size='60' />
                </td>
            </tr>");
    }
    //-- Finish Torrent Comment Enable - Disable --//

    //-- Start Offer Comment Enable - Disable --//
if ($user["offercompos"] == "no")
{
     $offercomposuntil = $user['offercomposuntil'];
     $offercompos    = $user['offercompos'];

     print("<tr>
             <td class='colhead'>&nbsp;Offer Comments Enabled</td>
             <td class='rowhead' align='left' width='20%'>
             <input type='radio' name='offercompos' value='yes' ".(!$offercompos ? " checked='checked'" : "")." />Yes
             <input type='radio' name='offercompos' value='no' ".($offercompos ? " checked='checked'" : "")." />No
             </td>");

     if ($user["offercomposuntil"] == "0000-00-00 00:00:00")
     {
         print("<td class='rowhead'>&nbsp;<strong>Total Ban</strong> - Seek Higher Staff Advice Before You Enable!</td></tr>");
     }
     else
     {
         print("<td class='rowhead'>&nbsp;Until $offercomposuntil");
         print(" (".mkprettytime(strtotime($offercomposuntil) - gmtime())." to go)</td></tr>");
     }
}

if ($user["offercompos"] == "yes")
{
     print("<tr>
             <td class='colhead' rowspan='2'>&nbsp;Offer Comments Enabled</td>
             <td class='rowhead'>&nbsp;Yes</td>");

     print("<td class='rowhead'>&nbsp;Disable For:-&nbsp;<select name='offercomposuntillength'>\n");
     print("<option value='0'>------</option>\n");
     print("<option value='1'>1 week</option>\n");
     print("<option value='2'>2 weeks</option>\n");
     print("<option value='4'>4 weeks</option>\n");
     print("<option value='8'>8 weeks</option>\n");
     print("<option value='255'>Unlimited</option>\n");
     print("</select></td></tr>");

     print("<tr>
             <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                 <input type='text' name='offercomposuntilpm' size='60' />
             </td>
         </tr>");
}
//-- Finish Offer Comment Enable - Disable --//

//-- Start Request Comment Enable - Disable --//
if ($user["requestcompos"] == "no")
{
     $requestcomposuntil = $user['requestcomposuntil'];
     $requestcompos  = $user['requestcompos'];

     print("<tr>
             <td class='colhead'>&nbsp;Request Comments Enabled</td>
             <td class='rowhead' align='left' width='20%'>
             <input type='radio' name='requestcompos' value='yes' ".(!$requestcompos ? " checked='checked'" : "")." />Yes
             <input type='radio' name='requestcompos' value='no' ".($requestcompos ? " checked='checked'" : "")." />No
             </td>");

     if ($user["requestcomposuntil"] == "0000-00-00 00:00:00")
     {
         print("<td class='rowhead'>&nbsp;<strong>Total Ban</strong> - Seek Higher Staff Advice Before You Enable!</td></tr>");
     }
     else
     {
         print("<td class='rowhead'>&nbsp;Until $requestcomposuntil");
         print(" (".mkprettytime(strtotime($requestcomposuntil) - gmtime())." to go)</td></tr>");
     }
}

if ($user["requestcompos"] == "yes")
{
     print("<tr>
             <td class='colhead' rowspan='2'>&nbsp;Request Comments Enabled</td>
             <td class='rowhead'>&nbsp;Yes</td>");

     print("<td class='rowhead'>&nbsp;Disable For:-&nbsp;<select name='requestcomposuntillength'>\n");
     print("<option value='0'>------</option>\n");
     print("<option value='1'>1 week</option>\n");
     print("<option value='2'>2 weeks</option>\n");
     print("<option value='4'>4 weeks</option>\n");
     print("<option value='8'>8 weeks</option>\n");
     print("<option value='255'>Unlimited</option>\n");
     print("</select></td></tr>");

     print("<tr>
             <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                 <input type='text' name='requestcomposuntilpm' size='60' />
             </td>
         </tr>");
}
//-- Finish Request Comment Enable - Disable --//


    //-- Start Forum Enable - Disable --//
    if ($user["forumpos"] == "no")
    {
        $forumposuntil = $user['forumposuntil'];
        $forumpos = $user['forumpos'];

        print("<tr>
                <td class='colhead'>&nbsp;Forum Enabled</td>
                <td class='rowhead' align='left' width='20%'>
                <input type='radio' name='forumpos' value='yes' ".(!$forumpos ? " checked='checked'" : "")." />Yes
                <input type='radio' name='forumpos' value='no' ".($forumpos ? " checked='checked'" : "")." />No
                </td>");

        if ($user["forumposuntil"] == "0000-00-00 00:00:00")
        {
            print("<td class='rowhead'>&nbsp;<strong>Total Ban</strong> - Seek Higher Staff Advice Before You Enable!</td></tr>");
        }
        else
        {
            print("<td class='rowhead'>&nbsp;Until $forumposuntil");
            print(" (".mkprettytime(strtotime($forumposuntil) - gmtime())." to go)</td></tr>");
        }
    }

    if ($user["forumpos"] == "yes")
    {
        print("<tr>
                <td class='colhead' rowspan='2'>&nbsp;Forum Enabled</td>
                <td class='rowhead'>&nbsp;Yes</td>");

        print("<td class='rowhead'>&nbsp;Disable For:-&nbsp;<select name='forumposuntillength'>\n");
        print("<option value='0'>------</option>\n");
        print("<option value='1'>1 week</option>\n");
        print("<option value='2'>2 weeks</option>\n");
        print("<option value='4'>4 weeks</option>\n");
        print("<option value='8'>8 weeks</option>\n");
        print("<option value='255'>Unlimited</option>\n");
        print("</select></td></tr>");

        print("<tr>
                <td class='rowhead' align='left' colspan='2'>&nbsp;Comment:-&nbsp;&nbsp;&nbsp;
                    <input type='text' name='forumposuntilpm' size='60' />
                </td>
            </tr>");
    }
    //-- Finish Forum Enable - Disable --//

    print("<tr>
            <td class='colhead'>&nbsp;Account Enabled</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='radio' name='enabled' value='yes' ".($enabled ? " checked='checked'" : "")." />Yes
                <input type='radio' name='enabled' value='no' ".(!$enabled ? " checked='checked'" : "")." />No</td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Account Parked</td>
            <td class='rowhead' colspan='2' align='left'>
                <input type='radio' name='parked' value='yes'" .($user['parked']=='yes' ? " checked='checked'" : "")." />Yes
                <input type='radio' name='parked' value='no'" .($user['parked']=='no' ? " checked='checked'" : "")." />No
            </td>
        </tr>");

    print("<tr>
            <td class='colhead'>&nbsp;Passkey</td>
            <td class='rowhead' align='left' colspan='2'>
                <input type='checkbox' name='resetpasskey' value='1' /> Reset Passkey
            </td>
        </tr>");

    print("<tr>
            <td colspan='3' align='center'>
                <input type='submit' class='btn' value='Okay' />
            </td>
        </tr>");

    print("</table>");
    print("</form>");

    //-- Start Delete Member By Wilba --//
    if (get_user_class() >= UC_SYSOP && $user["class"] < get_user_class())
    {
    print("<br />");
    print("<form method='post' action='delete_member.php?&amp;action=deluser'>");
    print("<table class='coltable' width='70%'>");
    print("<tr><td class='rowhead' align='center'><h2>Delete This Member</h2></td></tr>");

    $username = htmlspecialchars($user["username"]);

    print("<tr><td class='rowhead' align='center'>");
    print("<input name='username' size='20' value='".$username."' type='hidden' />");
    print("<input type='submit' class='btn' value='Delete $username' />");
    print("<br />");
    print("</td></tr>");
    print("</table>");
    print("</form>");
    }
    //-- Finish Delete Member By Wilba --//

    print("</div>");
}
//-- Finish Edit User Details Content --//

//-- Start Reset Password Content --//
if (get_user_class() >= UC_SYSOP && $user["class"] < get_user_class())
{
    print("<div class='ui-tabs-panel' id='fragment-7'>");
    print("<form method='post' action=''>");
    print("<table class='main' border='0' cellspacing='0' cellpadding='0'>\n" );
    print("<tr><td style='border:none;'>");
    print("<input type='hidden' name='username' value='$user[username]' size='40' readonly='readonly' />");
    print("<input type='submit' class='btn' value='RESET' />");
    print("</td></tr>");
    print("</table>");
    print("</form>");
    print("</div>");
}
//-- Finish Reset Password Content --//

//-- Start Invite Tree --//
if (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"])
{
    print("<div class='ui-tabs-panel' id='fragment-8'>");

    $query = sql_query("SELECT id, username, uploaded, downloaded, status, warned, enabled, donor
                        FROM users
                        WHERE invitedby = ".sqlesc($user['id'])) or sqlerr(__FILE__, __LINE__);

    $rows = mysql_num_rows($query);

    print("<table border='1' width='81%' cellspacing='0' cellpadding='5'>");
    print("<tr><td class='colhead' align='center' colspan='7'><strong>Invited Users</strong></td></tr>");

    if(!$rows)
    {
        print("<tr><td class='rowhead' align='center' colspan='7'>No invitees yet.</td></tr>");
        print("</table><br />");
    }
    else
    {
        print("<tr>
        <td class='rowhead' align='center'><strong>Username</strong></td>
        <td class='rowhead' align='center'><strong>Uploaded</strong></td>
        <td class='rowhead' align='center'><strong>Downloaded</strong></td>
        <td class='rowhead' align='center'><strong>Ratio</strong></td>
        <td class='rowhead' align='center'><strong>Status</strong></td>
        </tr>");

        for ($i = 0; $i < $rows; ++$i)
        {
            $arr = mysql_fetch_assoc($query);

            if ($arr['status'] == 'pending')
            {
                $user = "".htmlspecialchars($arr['username'])."";
            }
            else
            {
                $user = "<a href='userdetails.php?id=$arr[id]'>".htmlspecialchars($arr['username'])."</a>
                ".($arr["warned"] == "yes" ?"&nbsp;<img src='".$image_dir."warned.png' width='16' height='16' border='0' alt='Warned' title='Warned'>" : "")."&nbsp;
                ".($arr["enabled"] == "no" ?"&nbsp;<img src='".$image_dir."disabled.png' width='16' height='16' border='0' alt='Disabled' title='Disabled'>" : "")."&nbsp;
                ".($arr["donor"] == "yes" ?"<img src='".$image_dir."star.png' width='16' height='16' border='0' alt='Donor' title='Donor'>" : "")." ";
            }

            if ($arr['downloaded'] > 0)
            {
                $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
                $ratio = "<font color='".get_ratio_color($ratio)."'>".$ratio."</font>";
            }
            else
            {
                if ($arr['uploaded'] > 0)
                {
                    $ratio = 'Inf.';
                }
                else
                {
                    $ratio = '---';
                }
            }

            if ($arr["status"] == 'confirmed')
            {
                $status = "<font color='#1f7309'>Confirmed</font>";
            }
            else
            {
                $status = "<font color='#ca0226'>Pending</font>";
            }

            print("<tr>
            <td class='rowhead'align='center'>".$user."</td>
            <td class='rowhead'align='center'>".mksize($arr['uploaded'])."</td>
            <td class='rowhead'align='center'>".mksize($arr['downloaded'])."</td>
            <td class='rowhead'align='center'>".$ratio."</td>
            <td class='rowhead'align='center'>".$status."</td>");
        }

        print("</tr>");
        print("</table><br />");
    }
        print("</div>");
}
//-- Finish Invite Tree --//

print("</div>");

?>

<script type="text/javascript" src="/js/jquery-1.8.2.js" ></script>
<script type="text/javascript" src="/js/jquery-ui-1.9.0.custom.min.js" ></script>
<script type="text/javascript">
$(document).ready(function()
{
$("#featured").tabs({fx:{opacity: "toggle"}}).tabs("rotate", 5000, true);
});
</script>

<?php

site_footer();

?>