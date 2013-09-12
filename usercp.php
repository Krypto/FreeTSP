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

db_connect(false);
logged_in();

$newpage = new page_verify();
$newpage->create('_usercp_');

$action = isset($_GET["action"]) ? htmlspecialchars(trim($_GET["action"])) : '';

site_header(htmlentities($CURUSER["username"], ENT_QUOTES)."'s Private Page", false);

if (isset($_GET["edited"]))
{
    display_message("success", "Success", "<a href='/usercp.php'>Your Profile has been Updated!</a>");

    if (isset($_GET["mailsent"]))
    {
        display_message("success", "Success", "<a href='/usercp.php'>A Confirmation email has been sent!</a>");
    }
}

elseif (isset($_GET["emailch"]))
{
    display_message("success", "Success", "<a href='/usercp.php'>Email Address Changed!</a>");
}

print("<h1>Welcome <a href='userdetails.php?id=$CURUSER[id]'>$CURUSER[username]</a> !</h1>\n
    <form method='post' action='takeeditusercp.php'>
    <table border='1' align='center' width='600' cellspacing='0' cellpadding='3'><tr>
    <td width='600' valign='top'>");

print("<table border='1' width='502'>");

$maxbox = 100;
$maxpic = "warn";

//-- Check For Messages --//
$res1 = sql_query("SELECT COUNT(id)
                    FROM messages
                    WHERE receiver=".$CURUSER["id"]."
                    AND location >='1'") or print(mysql_error());

$arr1 = mysql_fetch_row($res1);

$messages = $arr1[0];
$res1 = sql_query("SELECT COUNT(id)
                    FROM messages
                    WHERE receiver=".$CURUSER["id"]."
                    AND location >='1'
                    AND unread='yes'") or print(mysql_error());

$arr1 = mysql_fetch_row($res1);

$unread = $arr1[0];
$res1 = sql_query("SELECT COUNT(id)
                    FROM messages
                    WHERE sender=".$CURUSER["id"]."
                    AND saved = 'yes'") or print(mysql_error());

$arr1 = mysql_fetch_row($res1);

$outmessages = $arr1[0];
$res1 = sql_query("SELECT COUNT(id)
                    FROM messages
                    WHERE receiver=".$CURUSER["id"]." && unread='yes'") or die("OopppsY!");

$arr1   = mysql_fetch_row($res1);
$unread = $arr1[0];

print("<tr>
        <td class='colhead' align='center' width='166' height='18'><a href='messages.php'>Inbox</a></td>
        <td class='colhead' align='center' width='166'><a href='messages.php?action=viewmailbox&amp;box=-1'> Sentbox</a></td></tr>");

print("<tr align='center'><td> ($messages)</td><td> ($outmessages)</td></tr>");

print("<tr><td align='center' height='25' colspan='3'><span style='font-weight:bold;'>You have $unread New Messages</span></td></tr>");

print("<tr><td align='center' height='25' colspan='3'><a href='friends.php'><span style='font-weight:bold;'>Friends List</span></a></td></tr>");

print("<tr><td align='center' height='25' colspan='3'><a href='users.php'><span style='font-weight:bold;'>Find User/Browse User List</span></a></td></tr>");

print("</table>");

//-- Avatar --//
if ($action == "avatar")
{
    begin_table(true);

    print("<tr>
            <td class='colhead' align='center' style='height:25px;' colspan='2'>
                <input type='hidden' name='action' value='avatar' />Avatar Options
            </td>
        </tr>");

    if (get_user_class() >= UC_SYSOP)
    {
        print("<tr>
                <td class='rowhead'><label for='title'>Title &nbsp;&nbsp;</label></td>
                <td class='rowhead'>
                    <input type='text' name='title' id='title' size='50' value='".htmlspecialchars($CURUSER["title"])."' />
                </td>
            </tr>");
    }

    print("<tr>
            <td class='rowhead'><label for='avatar'>Avatar URL</label></td>
            <td class='rowhead'>
                <input type='text' name='avatar' id='avatar' size='50' value='".htmlspecialchars($CURUSER["avatar"])."' /><br /><br />Width should be 150 pixels (will be resized if necessary).
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Show Avatars </td>
            <td class='rowhead'>
                <input type='checkbox' name='avatars'".($CURUSER["avatars"] == "yes" ? " checked='checked'" : "")." value='yes' /> All (Low bandwidth users might want to turn this off)<br />
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value='Submit Changes!' />
            </td>
        </tr>");

    end_table();
}

//-- Signature --//
elseif ($action == "signature")
{
    begin_table(true);

    print("<tr>
            <td class='colhead' align='center' style='height:25px;' colspan='2'>
                <input type='hidden' name='action' value='signature' />Signature Options
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='signature'>Signature</label></td>
            <td class='rowhead'>
                <input type='text' name='signature' id='signature' size='50' value='".htmlspecialchars($CURUSER['signature'])."' /><br /><span style='font-size: x-small;'>Max 225 Characters. Max Image Size 500x100.</span><br /> May contain BB Codes.
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>View Signatures</td>
            <td class='rowhead'>
                <input type='checkbox' name='signatures' ".($CURUSER["signatures"] == "yes" ? " checked='checked'" : "")." /> (Low Bandwidth Users might want to turn this OFF)
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='info'>Info</label></td>
            <td class='rowhead'>
                <textarea name='info' id='info' cols='50' rows='4'>".htmlentities($CURUSER["info"], ENT_QUOTES)."</textarea><br />
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value='Submit Changes!' />
            </td>
        </tr>");

    end_table();
}

//-- Security --//

elseif ($action == "security")
{
    begin_table(true);

    print("<tr>
            <td class='colhead' align='center' colspan='2' style='height:25px;'>
                <input type='hidden' name='action' value='security' />Security Options
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Reset Passkey</td>
            <td class='rowhead'>
                <input type='checkbox' name='resetpasskey' value='1' /><br /><span style='font-size: xx-small;'>Any Active Torrents Must be Downloaded again to Continue Leeching/Seeding.</span>
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='email'>Email Address</label></td>
            <td class='rowhead'>
                <input type='text' name='email' id='email' size='50' value='".htmlspecialchars($CURUSER["email"])."' />
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>*Note:</td>
            <td class='rowhead' align='left'>In order to change your email address, you will receive another<br />confirmation email to your new address.</td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='chpassword'>Change Password</label></td>
            <td class='rowhead'>
                <img src='{$image_dir}password/tooshort.gif' id='strength' alt='' />
                <input type='password' name='chpassword' maxlength='15' onkeyup='updatestrength( this.value );' id='chpassword' size='50' />
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='passagain'>Type Password again!</label></td>
            <td class='rowhead'>
                <input type='password' name='passagain' id='passagain' size='50' />
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value='Submit Changes!' />
            </td>
        </tr>");

    end_table();
}

//-- Torrents --//
elseif ($action == "torrents")
{
    begin_table(true);

    print("<tr>
            <td class='colhead' align='center' colspan='2' style='height:25px;'>
                <input type='hidden' name='action' value='torrents' />Torrent Options
            </td>
        </tr>");

    $categories = '';
    $r          = @sql_query("SELECT id,name
                                FROM categories
                                ORDER BY name") or sqlerr();

    if (mysql_num_rows($r) > 0)
    {
        $categories .= "<table><tr>\n";
        $i = 0;

        while ($a = mysql_fetch_assoc($r))
        {
            $categories .= ($i && $i % 2 == 0) ? "</tr><tr>" : "";

            $categories .= "<td class='bottom' style='padding-right: 5px'><input name='cat{$a['id']}' type='checkbox' ".(strpos($CURUSER['notifs'], "[cat{$a['id']}]") !== false ? " checked='checked'" : "")." value='yes' />&nbsp;".htmlspecialchars($a["name"])."</td>\n";

            ++$i;
        }
        $categories .= "</tr></table>\n";
    }
    print("<tr>
            <td class='rowhead'>Email Notification </td>
            <td class='rowhead'>
                <input type='checkbox' name='pmnotif'".(strpos($CURUSER['notifs'], "[pm]") !== false ? " checked='checked'" : "")." value='yes' /> Notify me when I have received a PM<br />
                <input type='checkbox' name='emailnotif'".(strpos($CURUSER['notifs'], "[email]") !== false ? " checked='checked'" : "")." value='yes' /> Notify me when a torrent is uploaded in one of <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;my Default Browsing Categories.
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Browse Default<br />Categories</td>
            <td class='rowhead'> ".$categories."</td>
        </tr>");

    print("<tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value='Submit Changes!' />
            </td>
        </tr>");

    end_table();
}

//-- Personal --//
elseif ($action == "personal")
{
    $ss_r = sql_query("SELECT id, name
                        FROM stylesheets
                        ORDER BY id") or die;

    $ss_sa = array();

    while ($ss_a = mysql_fetch_array($ss_r))
    {
        $ss_id           = $ss_a["id"];
        $ss_name         = $ss_a["name"];
        $ss_sa[$ss_name] = $ss_id;
    }

    ksort($ss_sa);
    reset($ss_sa);

    $stylesheets = '';

    while (list($ss_name, $ss_id) = each($ss_sa))
    {
        if ($ss_id == $CURUSER["stylesheet"])
        {
            $ss = " selected='selected'";
        }
        else
        {
            $ss = "";
        }
        $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
    }

    $countries = "<option value='0'>---- None Selected ----</option>\n";

    $ct_r = sql_query("SELECT id,name
                        FROM countries
                        ORDER BY name") or sqlerr(__FILE__, __LINE__);

    while ($ct_a = mysql_fetch_assoc($ct_r))
    {
        $countries .= "<option value='{$ct_a['id']}'".($CURUSER["country"] == $ct_a['id'] ? " selected='selected'" : "").">{$ct_a['name']}</option>\n";
    }

    begin_table(true);

    print("<tr>
            <td class='colhead' align='center' colspan='2' style='height:25px;'>
                <input type='hidden' name='action' value='personal' />Personal Options
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Stylesheet</td>
            <td class='rowhead'>
                <select name='stylesheet'>\n$stylesheets\n</select>
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Park Account</td>
            <td class='rowhead'>
                <input type='radio' name='parked'".($CURUSER["parked"] == "yes" ? " checked='checked'" : "")." value='yes' />Yes
                <input type='radio' name='parked'".($CURUSER["parked"] == "no" ? " checked='checked'" : "")." value='no' />No<br />
                    You can Park your Account to prevent it from being Deleted because of Inactivity if you go away on for example a Vacation.  When the Account has been Parked Limits are put on the Account, for example you cannot use the tracker and browse some of the pages.
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>PC On at Night</td>
            <td class='rowhead'>
                <input type='radio' name='pcoff'".($CURUSER["pcoff"] == "yes" ? " checked='checked'" : "")." value='yes' />Yes
                <input type='radio' name='pcoff'".($CURUSER["pcoff"] == "no" ? " checked='checked'" : "")." value='no' />No
            </td>
        </tr>");

    if ($CURUSER['menu'] == "2")
    {
        print("<tr>
                <td class='rowhead'>Menu Selection</td>
                <td class='rowhead' align='left'>
                    <select name='menu' id='input'>
                        <option value='2'>Standard</option>
                        <option value='1'>Dropdown</option>
                    </select>
                </td>
            </tr>");
    }
    else
    {
        print("<tr>
                <td class='rowhead'>Menu Selection</td>
                <td class='rowhead' align='left'>
                    <select name='menu' id='input'>
                        <option value='1'>Dropdown</option>
                        <option value='2'>Standard</option>
                    </select>
                </td>
            </tr>");
    }

    print("<tr>
            <td class='rowhead'>Country</td>
            <td class='rowhead'>
                <select name='country'>\n$countries\n</select>
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='torrentsperpage'>Torrents Per Page</label></td>
            <td class='rowhead'>
                <input type='text' size='10' name='torrentsperpage' id='torrentsperpage' value='$CURUSER[torrentsperpage]' /> (0 = Use Default Setting)
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='topicsperpage'>Topics Per Page</label></td>
            <td class='rowhead'>
                <input type='text' size='10' name='topicsperpage' id='topicsperpage' value='$CURUSER[topicsperpage]' /> (0 = Use Default Setting)
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'><label for='postsperpage'>Posts Per Page</label></td>
            <td class='rowhead'>
                <input type='text' size='10' name='postsperpage' id='postsperpage' value='$CURUSER[postsperpage]' /> (0 = Use Default Setting)
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value='Submit Changes!' />
            </td>
        </tr>");

    end_table();
}
elseif ($action == "pm")
{
    //-- PMs --//
    begin_table(true);

    print("<tr>
            <td class='colhead' align='center' colspan='2' style='height:25px;'>
                <input type='hidden' name='action' value='pm' />Private Message Options
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Private Message Options </td>
            <td class='rowhead'>
                <input type='radio' name='acceptpms'".($CURUSER["acceptpms"] == "yes" ? " checked='checked'" : "")." value='yes' />All (except blocks)
                <input type='radio' name='acceptpms'".($CURUSER["acceptpms"] == "friends" ? " checked='checked'" : "")." value='friends' />Friends Only
                <input type='radio' name='acceptpms'".($CURUSER["acceptpms"] == "no" ? " checked='checked'" : "")." value='no' />Staff Only</td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Delete PMs </td>
            <td class='rowhead'>
                <input type='checkbox' name='deletepms'".($CURUSER["deletepms"] == "yes" ? " checked='checked'" : "")." /> (Default value for Delete PM on Reply)
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Save PMs </td>
            <td class='rowhead'>
                <input type='checkbox' name='savepms'".($CURUSER["savepms"] == "yes" ? " checked='checked'" : "")." /> (Default value for Save PM to Sentbox)
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value='Submit Changes!' />
            </td>
        </tr>");

    end_table();
}

print("</td><td width='95' valign='top' ><table border='1'>");

print("<tr>
    <td class='colhead' width='95' style='height:25px;' >".htmlentities($CURUSER["username"], ENT_QUOTES)."'s Avatar</td></tr>");

if (!empty($CURUSER['avatar']))
{
    print("<tr>
    <td><img src='{$CURUSER['avatar']}' width='125' height='125' border='0' alt='' title='' /></td>
</tr>");
}
else
{
    print("<tr>
    <td class='std'><img src='".$image_dir."default_avatar.gif' height='125' width='125' border='0' alt='' title='' /></td>
</tr>");
}

print("<tr>
        <td class='colhead' width='95' style='height:18px;'>".htmlentities($CURUSER["username"], ENT_QUOTES)."'s Menu</td>
    </tr>");

print("<tr>
        <td class='rowhead' align='left'>
            <a href='usercp.php?action=avatar'>Avatar</a>
        </td>
    </tr>");

print("<tr>
        <td class='rowhead' align='left'>
            <a href='usercp.php?action=signature'>Signature</a>
        </td>
    </tr>");

print("<tr>
        <td class='rowhead' align='left'>
            <a href='usercp.php?action=security'>Security</a>
        </td>
    </tr>");

print("<tr>
        <td class='rowhead' align='left'>
            <a href='usercp.php?action=torrents'>Torrents</a>
        </td>
    </tr>");

print("<tr>
        <td class='rowhead' align='left'>
            <a href='usercp.php?action=personal'>Personal</a>
        </td>
    </tr>");

print("<tr>
        <td class='rowhead' align='left'>
            <a href='usercp.php?action=pm'>Private Messages</a>
        </td>
    </tr>");

print("</table></td></tr></table></form>");

?>
<script type="text/javascript" src="js/password.js"></script>
<?php

site_footer();

?>