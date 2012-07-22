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

site_header("Staff");

// Get current datetime
$dt = gmtime() - 60;
$dt = sqlesc(get_date_time($dt));

// Search User Database for Moderators and above and display in alphabetical order
$res = sql_query("SELECT id, username, class, last_access 
					FROM users 
					WHERE class>=" . UC_UPLOADER . " 
					ORDER BY username") OR sqlerr();

while ($arr = mysql_fetch_assoc($res))
{
	$staff_class[$arr['class']] = $staff_class[$arr['class']] .
	"<table class='main' width='100%' cellspacing='0' cellpadding='5' border='0'><tr><td class='std' align='center' width='33%'><a class='altlink' href='userdetails.php?id=" . $arr['id'] . "'>" .$arr['username'] . "</a></td><td class='std' align='center' width='33%'> " . ("'" . $arr['last_access'] . "'" > $dt?"<img src='" . $image_dir . "online.png' width='32' height='32' border='0' alt='Online' title='Online' />":"<img src='" . $image_dir . "offline.png' width='32' height='32' border='0' alt='Offline' title='Offline' />") . "</td>" ."<td class='std' align='center' width='33%'><a href='sendmessage.php?receiver=" . $arr['id'] . "'><input type='submit' class='btn' value='PM' /></a></td></tr></table><br />";
}

?>
<script type='text/javascript' src='js/content_glider.js'></script>
<script type='text/javascript'>

featuredcontentglider.init(
{
	gliderid: 'FreeTSPstaff', //ID of main glider container
	contentclass: 'FreeTSPglidecontent', //Shared CSS class name of each glider content
	togglerid: 'FreeTSP', //ID of toggler container
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
Welcome to the Staff Members here at <span style='font-weight:bold;'><?php echo $site_name?>.</span><br/>
Please direct your questions to the correct member of Staff.<br/>
Any questions that are already covered in the F.A.Q.<br/>
Will be Ignored.
</span>
</div>
<br /><br />

<div id='FreeTSP' class='FreeTSPglidecontenttoggler'>
	<a href='#' class='toc'>Uploaders</a>
	<a href='#' class='toc'>Moderators</a>
	<a href='#' class='toc'>Administrators</a>
	<a href='#' class='toc'>Sysop</a>
</div>

<div id='FreeTSPstaff' class='FreeTSPglidecontentwrapper'>
	<div class='FreeTSPglidecontent'>
		<?php begin_frame("Uploaders"); ?>
		<?php echo $staff_class[UC_UPLOADER]?>
		<?php end_frame(); ?>
	</div>

	<div class='FreeTSPglidecontent'>
		<?php begin_frame("Moderators"); ?>
		<?php echo $staff_class[UC_MODERATOR]?>
		<?php end_frame(); ?>
	</div>

	<div class='FreeTSPglidecontent'>
		<?php begin_frame("Administrators"); ?>
		<?php echo $staff_class[UC_ADMINISTRATOR]?>
		<?php end_frame(); ?>
	</div>

	<div class='FreeTSPglidecontent'>
		<?php begin_frame("Sysop"); ?>
		<?php echo $staff_class[UC_SYSOP]?>
		<?php end_frame(); ?>
	</div>

</div>

<?php

echo("<br />");

site_footer();

?>