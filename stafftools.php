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
logged_in();

if (get_user_class() < UC_MODERATOR)
	error_message("warn", "Warning", "Permission Denied");

site_header("Staff Tools");

?>

<script type='text/javascript' src='js/content_glider.js'></script>

<script type='text/javascript'>

featuredcontentglider.init(
{
	gliderid: 'FreeTSPstafftools', //ID of main glider container
	contentclass: 'FreeTSPglidecontent4', //Shared CSS class name of each glider content
	togglerid: 'FreeTSP4', //ID of toggler container
	remotecontent: '', //Get gliding contents from external file on server? "filename" or "" to disable
	selected: 0, //Default selected content index (0=1st)
	persiststate: false, //Remember last content shown within browser session (true/false)?
	speed: 700, //Glide animation duration (in milliseconds)
	direction: 'downup' //set direction of glide: "updown", "downup", "leftright", or "rightleft"
}
)

</script>

<br /><br />

<div align='center'>
<span style='text-align:center; font-size: small;'>
Welcome <?echo $CURUSER[username]?> to <span style='font-weight:bold;'><?php echo $site_name?>.</span> Staff Menu<br/>
</span>
</div>

<br /><br />

<div id='FreeTSP4' class='FreeTSPglidecontenttoggler4'>
	<a href='#' class='toc'>Moderators</a>
	<?php if (get_user_class() >= UC_ADMINISTRATOR){?>
	<a href='#' class='toc'>Administrators</a>
	<?php }?>
	<?php if (get_user_class() >= UC_SYSOP){?>
	<a href='#' class='toc'>Sysop</a>
	<?php }?>
</div>

<div id='FreeTSPstafftools' class='FreeTSPglidecontentwrapper4'>
	<div class='FreeTSPglidecontent4'>
		<?php if (get_user_class() >= UC_MODERATOR){?>
		<table width='100%' cellpadding='4'>
			<tr><td class='colhead' align='center'>Moderator's Tools</td></tr>
		</table><br/>

		<table width='100%' cellpadding='4'>
			<tr>
				<td align='center' class='navigation'><a href='bans.php' class='TSPbutton'><span>Manage Bans</span></a></td>
				<td align='center' class='navigation'><a href='testip.php' class='TSPbutton'><span>Test an IP Address</span></a></td>
				<td align='center' class='navigation'><a href='usersearch.php' class='TSPbutton'><span>Seach Users</span></a></td>
			</tr>
			<tr>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
			</tr>
			<tr>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
			</tr>
		</table>
		<?php }?>
	</div>

	<div class='FreeTSPglidecontent4'>
		<?php if (get_user_class() >= UC_ADMINISTRATOR){?>
		<table width='100%' cellpadding='4'>
			<tr><td class='colhead' align='center'>Administrator's Tools</td></tr>
		</table><br/>

		<table width='100%' cellpadding='4'>
			<tr>
				<td align='center' class='navigation'><a href='last24history.php' class='TSPbutton'><span>Last 24 History</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
			</tr>

			<tr>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
			</tr>

			<tr>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
			</tr>
		</table>
		<?php }?>
	</div>

	<div class='FreeTSPglidecontent4'>
		<?php if (get_user_class() >= UC_SYSOP){?>
		<table width='100%' cellpadding='4'>
			<tr><td class='colhead' align='center'>Sysop's Tools</td></tr>
		</table><br/>

		<table width='100%' cellpadding='4'>
			<tr>
				<td align='center' class='navigation'><a href='adduser.php' class='TSPbutton'><span>Add User</span></a></td>
				<td align='center' class='navigation'><a href='stylesheets.php' class='TSPbutton'><span>Theme Manager</span></a></td>
				<td align='center' class='navigation'><a href='category.php' class='TSPbutton'><span>Category Manager</span></a></td>
			</tr>

			<tr>
				<td align='center' class='navigation'><a href='tracker_manager.php' class='TSPbutton'><span>Tracker Manager</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
			</tr>

			<tr>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
				<td align='center' class='navigation'><a href='#' class='TSPbutton'><span>T.B.A</span></a></td>
			</tr>
		</table>
		<?php }?>
	</div>

</div><br />

<?php

site_footer();

?>