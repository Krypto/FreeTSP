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
require_once(INCL_DIR.'function_bbcode.php');
require_once(INCL_DIR.'function_page_verify.php');

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->create('_altusercp_');

site_header(htmlentities($CURUSER["username"], ENT_QUOTES) . "'s private page", false);

	if (isset($_GET["edited"]))
	{
		display_message("success", "Success", "<a href='altusercp.php'>Your Profile has been Updated!</a>");

	if ($_GET["mailsent"])
		display_message("success", "Success", "<a href='altusercp.php'>A Confirmation email has been Sent!");

	}
	elseif (isset($_GET["emailch"]))
	{
		display_message("success", "Success", "<a href='altusercp.php'>Email Address Changed!");
	}
else
	print("<h1>Welcome, $CURUSER[username]!</h1>");

	print("<table align='center'>");

	print("<tr>
			<td class='colhead' width='125' height='18' align='center'>" . htmlspecialchars($CURUSER["username"]) . "'s Avatar</td>
		</tr>");

if ($CURUSER['avatar'])
	print("<tr>
			<td class='std'><img src='" . htmlspecialchars($CURUSER["avatar"]) . "' width='125' height='125' alt='' title='' /></td>
		</tr>");
else
	print("<tr>
			<td class='std'><img src='".$image_dir."default_avatar.gif' width='125' height='125' alt=' title='' /></td>
		</tr>");
	print("</table>");

	print("<h1><a href='userdetails.php?id=$CURUSER[id]'>Your Details</a></h1>");

?>

<script type="text/javascript" src="js/content_glider.js"></script>
<script type="text/javascript">

featuredcontentglider.init(
{
	gliderid: "FreeTSPusercp", //ID of main glider container
	contentclass: "FreeTSPglidecontent3", //Shared CSS class name of each glider content
	togglerid: "FreeTSP3", //ID of toggler container
	remotecontent: "", //Get gliding contents from external file on server? "filename" or "" to disable
	selected: 0, //Default selected content index (0=1st)
	persiststate: false, //Remember last content shown within browser session (true/false)?
	speed: 700, //Glide animation duration (in milliseconds)
	direction: "downup" //set direction of glide: "updown", "downup", "leftright", or "rightleft"
}
)

</script>
<?php
print("
	<div id='FreeTSP3' class='FreeTSPglidecontenttoggler3'>
		<a href='#' class='toc'>Avatar</a>
		<a href='#' class='toc'>Personal</a>
		<a href='#' class='toc'>Private Messages</a>
		<a href='#' class='toc'>Security</a>
		<a href='#' class='toc'>Signature</a>
		<a href='#' class='toc'>Torrents</a>
		<a href='#' class='toc'>Logout</a>
	</div>
");

print("<div id='FreeTSPusercp' class='FreeTSPglidecontentwrapper3'>");

print("<div class='FreeTSPglidecontent3'>
<form method='post' action='takeeditaltusercp.php?action=avatar'><table align='center' width='100%' border='1'>");

print("<tr>
	<td class='colhead' colspan='2' height='18' align='center'>Avatar Options</td>
</tr>");

if (get_user_class() >= UC_SYSOP )
{
	print("<tr>
		<td class='rowhead_form' align='right'><label for='title'>Title &nbsp;&nbsp;</label></td>
		<td class='rowhead_form'><input type='text' name='title' id='title' size='50' value='" . htmlspecialchars($CURUSER["title"]) . "' /></td>
	</tr>");
}

print("<tr>
	<td class='rowhead_form' align='right'><label for='avatar'>Avatar URL &nbsp;&nbsp;</label></td>
	<td class='rowhead_form'><input type='text' name='avatar' id='avatar' size='50' value='" . htmlspecialchars($CURUSER["avatar"]) ."' /><br />
	Width should be 150 pixels (will be resized if necessary)</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'><label for='show'>Show Avatars &nbsp;&nbsp;</label></td>
	<td class='rowhead_form'><input type='checkbox' name='avatars' id='show' " . ($CURUSER["avatars"] == "yes" ? " checked='checked'" : "") . " value='yes' />
	All (Low Bandwidth users might want to turn this Off)<br /></td>
</tr>");

print("<tr>
	<td class='std' colspan='2' height='30' align='center'><input type='reset' class='btn' value='Revert Changes!' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='btn' value='Submit Changes!' /></td>
</tr>
</table></form></div>");

print("<div class='FreeTSPglidecontent3'><form method='post' action='takeeditaltusercp.php?action=personal'><table align='center' width='100%' border='1'>");

print("<tr>
	<td class='colhead' colspan='2' height='18' align='center'>Personal Options</td>
</tr>");

$ss_r = sql_query("SELECT id, name
					FROM stylesheets
					ORDER BY id") or die;

$ss_sa = array();

while ($ss_a = mysql_fetch_array($ss_r))
{
	$ss_id				= $ss_a["id"];
	$ss_name			= $ss_a["name"];
	$ss_sa[$ss_name]	= $ss_id;
}

ksort($ss_sa);
reset($ss_sa);

while (list($ss_name, $ss_id) = each($ss_sa))
{
	if ($ss_id == $CURUSER['stylesheet'])
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
					ORDER BY name") or sqlerr(__FILE__,__LINE__);

while ($ct_a = mysql_fetch_assoc($ct_r))
{
	$countries .= "<option value='{$ct_a['id']}'" . ($CURUSER["country"] == $ct_a['id'] ? " selected='selected'" : "") . ">{$ct_a['name']}</option>\n";
}

print("<tr>
	<td class='rowhead_form' align='right'>Stylesheet &nbsp;&nbsp;</td>
	<td class='rowhead_form'><select name='stylesheet'>\n$stylesheets\n</select></td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Drop Down Menu &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='radio' name='dropmenu'" . ($CURUSER["dropmenu"] == "yes" ? " checked='checked'" : "") . " value='yes' />Yes<input type='radio' name='dropmenu'" . ($CURUSER["dropmenu"] == "no" ? " checked='checked'" : "") . " value='no' />No</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Standard Menu &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='radio' name='stdmenu'" . ($CURUSER["stdmenu"] == "yes" ? " checked='checked'" : "") . " value='yes' />Yes<input type='radio' name='stdmenu'" . ($CURUSER["stdmenu"] == "no" ? " checked='checked'" : "") . " value='no' />No</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Country &nbsp;&nbsp;</td>
	<td class='rowhead_form'><select name='country'>\n$countries\n</select></td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Torrents Per Page &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='text' name='torrentsperpage' id='torrentsperpage' size='10' value='$CURUSER[torrentsperpage]' /> (0=Use Default Setting)</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Topics Per Page &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='text' name='topicsperpage' id='topicsperpage' size='10' value='$CURUSER[topicsperpage]' /> (0=Use Default Setting)</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Posts Per Page &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='text' name='postsperpage' id='postsperpage' size='10' value='$CURUSER[postsperpage]' /> (0=Use Default Setting)</td>
</tr>");

print("<tr>
	<td class='std' colspan='2' height='30' align='center'><input type='reset' class='btn' value='Revert Changes!' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='btn' value='Submit Changes!' /></td>
</tr>
</table></form></div>");

print("<div class='FreeTSPglidecontent3'><form method='post' action='takeeditaltusercp.php?action=pm'><table align='center' width='100%' border='1'>");

print("<tr>
	<td class='colhead' colspan='2' height='18' align='center'>Private Message Options</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Accept PM's &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='radio' name='acceptpms'" . ($CURUSER["acceptpms"] == "yes" ? " checked='checked'" : "") . " value='yes' />All (Except Blocks)<input type='radio' name='acceptpms'" . ($CURUSER["acceptpms"] == "friends" ? " checked='checked'" : "") . " value='friends' />Friends Only<input type='radio' name='acceptpms'" . ($CURUSER["acceptpms"] == "no" ? " checked='checked'" : "") . " value='no' />Staff Only</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Delete PM's &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='checkbox' name='deletepms'" . ($CURUSER["deletepms"] == "yes" ? " checked='checked'" : "") . " />(Default value for 'Delete PM on Reply')</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Save PM's &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='checkbox' name='savepms'" . ($CURUSER["savepms"] == "yes" ? " checked='checked'" : "") . " />(Default value for 'Save PM to Sentbox')</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Email Notification &nbsp;&nbsp;</td>
	<td class='rowhead_form'>&nbsp; Select under Torrents Option.</td>
</tr>");

print("<tr>
	<td class='std' colspan='2' height='30' align='center'><input type='reset' class='btn' value='Revert Changes!' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='btn' value='Submit Changes!' /></td>
</tr>
</table></form></div>");

print("<div class='FreeTSPglidecontent3'><form method='post' action='takeeditaltusercp.php?action=security'><table align='center' width='100%' border='1'>");

print("<tr>
	<td class='colhead' colspan='2' height='18' align='center'>Security Options</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Reset Passkey &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='checkbox' name='resetpasskey' value='1' /><br /><span style='font-size: x-small; '>&nbsp; Any active torrents MUST be downloaded again to continue Leeching/Seeding.</span></td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Email Address &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='text' name='email' id='email' size='50' value='" . htmlspecialchars($CURUSER["email"]) . "' /></td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>*Note: &nbsp;&nbsp;</td>
	<td align='left'>&nbsp;&nbsp; In order to change your email address, you will receive another<br />&nbsp;&nbsp; Confirmation Email to your new address.</td>
</tr>\n");

print("<tr>
	<td class='rowhead_form' align='right'>Change Password &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='password' name='chpassword' id='password' size='50' /></td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Type Password Again &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='password' name='passagain' id='passagain' size='50' /></td>
</tr>");

print("<tr>
	<td class='std' colspan='2' height='30' align='center'><input type='reset' class='btn' value='Revert Changes!' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='btn' value='Submit Changes!' /></td>
</tr>
</table></form></div>");

print("<div class='FreeTSPglidecontent3'><form method='post' action='takeeditaltusercp.php?action=signature'><table align='center' width='100%'  border='1'>");

print("<tr>
	<td class='colhead' colspan='2' height='18' align='center'>Signature Options<br />" . format_comment($CURUSER['signature']) . "</td></tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Signature &nbsp;&nbsp;</td>
	<td class='rowhead_form'><textarea name='signature' id='signature' cols='50' rows='4'>" . htmlspecialchars($CURUSER['signature']) . "</textarea><br /><span style='font-size: x-small; '>&nbsp;&nbsp; Max 225 characters. Max Image Size 500x100.</span>\n<br />&nbsp;&nbsp; May contain BB codes.</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>View Signatures &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='checkbox' name='signatures'" . ($CURUSER["signatures"] == "yes" ? " checked='checked'" : "") . " />&nbsp;&nbsp; (Low Bandwidth users might want to turn this Off)</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Info &nbsp;&nbsp;</td>
	<td class='rowhead_form'><textarea name='info' id='info' cols='50' rows='4'>" . $CURUSER["info"] . "</textarea><br />&nbsp;&nbsp; Displayed on your Public Page. May contain BB codes.</td>
</tr>");

print("<tr>
	<td class='std' colspan='2' height='30' align='center'><input type='reset' class='btn' value='Revert Changes!' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='btn' value='Submit Changes!' /></td>
</tr>
</table></form></div>");

print("<div class='FreeTSPglidecontent3'><form method='post' action='takeeditaltusercp.php?action=torrents'><table align='center' width='100%' border='1'>");

print("<tr>
	<td class='colhead' colspan='2' height='18' align='center'>Torrents Options</td>
</tr>");

$r = sql_query("SELECT id,name
				FROM categories
				ORDER BY name") or sqlerr();

if (mysql_num_rows($r) > 0)
{
	$categories	= "<table><tr>\n";
	$i			= 0;

	while ($a = mysql_fetch_assoc($r))
	{
		$categories .= ($i && $i % 2 == 0) ? "</tr><tr>" : "";
		$categories .= "<td class='bottom' style='padding-right: 5px'><input name='cat$a[id]' type='checkbox'" . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked='checked'" : "") . " value='yes' /> " . htmlspecialchars($a["name"]) . "</td>\n";
		++$i;
	}
	$categories .= "</tr></table>\n";
}

print("<tr>
	<td class='rowhead_form' align='right'>Email Notification &nbsp;&nbsp;</td>
	<td class='rowhead_form'><input type='checkbox' name='pmnotif'" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked='checked'" : "") . " value='yes' /> Notify me when I receive a Private Message<br />\n<input type='checkbox' name='emailnotif'" . (strpos($CURUSER['notifs'], "[email]") !== false ? " checked='checked'" : "") . " value='yes' /> Notify me when a torrent is uploaded in one of my Default Browsing Categories.\n</td>
</tr>");

print("<tr>
	<td class='rowhead_form' align='right'>Browse Default &nbsp;&nbsp;<br />Categories &nbsp;&nbsp;</td>
	<td class='rowhead_form'>$categories</td>
</tr>");

print("<tr>
	<td class='std' colspan='2' height='30' align='center'><input type='reset' class='btn' value='Revert Changes!' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='btn' value='Submit Changes!' /></td>
</tr>
</table></form></div></div>");

print("<br />");

site_footer();

?>