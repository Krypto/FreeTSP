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

site_header("Rules");

begin_frame("General Rules - <span style='color : #004E98;'>Breaking these Rules Can and Will get you Banned!</span>");

?>

<ul>
	<li>Do NOT defy the Moderators expressed wishes!</li>
	<li>Do NOT upload our torrents to other trackers! (See the <a href='faq.php#up3' class='altlink'><span style='font-weight:bold;'>FAQ</span></a> for details.)</li>
	<li><a name='warning' id="warning1"></a>Disruptive behaviour in the forums will result in a Warning (<img src='<?php echo $image_dir?>warned.png' width='16' height='16' border='0' alt='Warned' title='Warned' /> ).<br />
	You will only get <span style='font-weight:bold;'>one</span> Warning! After that it's Bye Bye!</li>
</ul>

<?php

end_frame();

begin_frame("Downloading Rules - <span style='color : #004E98;'>By Not Following these Rules you will loose your Download Privileges!</span>");
?>

<ul>
	<li>Access to the newest torrents is conditional on a good Ratio! (See the <a href='faq.php#dl8' class='altlink'><span style='font-weight:bold;'>FAQ</span></a> for details.)</li>
	<li>Low Ratios may result in severe consequences, including Banning in extreme cases.</li>
</ul>

<?php

end_frame();

begin_frame("General Forum Guidelines - <span style='color : #004E98;'>Please follow these Guidelines or else you might end up with a Warning!</span>");
?>

<ul>
	<li>No Aggressive Behaviour or Flaming in the Forums.</li>
	<li>No Trashing of other members Topics (i.e. SPAM).</li>
	<li>No Language other than English in the Forums.</li>
	<li>No Systematic Foul Language (and none at all on Titles).</li>
	<li>No Links to Warez or Crack sites in the Forums.</li>
	<li>No Requesting or Posting of Serials, CD keys, Passwords or Cracks in the Forums.</li>
	<li>No Bumping... (All Bumped Threads will be Deleted.)</li>
	<li>No Images larger than 800x600, and preferably Web-Optimised.</li>
	<li>No Double Posting. If you wish to post again, and yours is the last post in the thread please use the EDIT function, instead of posting a double.</li>
	<li>Please ensure all questions are posted in the correct section!<br />
	(Game questions in the Games section, Apps questions in the Apps section, etc.)</li>
	<li>Last, please read the <a href='faq.php' class='altlink'><span style='font-weight:bold;'>FAQ</span></a> before asking any questions!</li>
</ul>

<?php

end_frame();

begin_frame("Avatar Guidelines - <span style='color : #004E98;'>Please try to follow these Guidelines</span>");

?>

<ul>
	<li>The allowed formats are .gif, .jpg and .png.</li>
	<li>Be considerate. Resize your images to a width of 150 px and a size of no more than 150 KB. (Browsers will re scale them anyway: smaller images will be expanded and will not look good; larger images will just waste bandwidth and CPU cycles.) For now this is just a guideline but it will be automatically enforced in the near future.</li>
	<li>Do not use potentially offensive material involving porn, religious material, animal / human cruelty or ideologically charged images. Mods have wide discretion on what is acceptable. If in doubt
	PM one.</li>
</ul>

<?php

end_frame();

if (get_user_class() >= UC_UPLOADER)
{

	begin_frame("Uploading Rules - <span style='color : #004E98;'>Torrents Violating these Rules may be Deleted without notice</span>");
?>

<ul>
	<li>All Uploads to preferably include a proper NFO.</li>
	<li>All files must be in original format (usually 14.3 MB RARs).</li>
	<li>Make sure not to include any Serial Numbers, CD Keys or similar in the description (you do <span style='font-weight:bold;'>not</span> need to edit the NFO!).</li>
	<li>Make sure your torrents are well-seeded for at least 24 Hours.</li>
	<li>Do NOT include the release date in the torrent name.</li>
	<li>Stay active! You risk being Demoted if you have No Active Torrents.</li>
</ul>

<br />
<div style="margin-left: 2em">If you have something interesting that somehow violate these Rules (e.g. not ISO format), ask a Mod and we might make an exception.</div>

<?php

end_frame();

}
if (get_user_class() >= UC_MODERATOR)
{

	begin_frame("Moderating Rules - <span style='color : #004E98;'>Whom to Promote and Why</span>");
?>

<br />
<table border='0' cellspacing='3' cellpadding='0'>
	<tr>
		<td class='embedded' bgcolor='#F5F4EA' valign='top' width='80'>&nbsp; <span style='font-weight:bold;'>Power User</span></td>
		<td class='embedded' width='5'>&nbsp;</td>
		<td class='embedded'>Automatically given to (and revoked from) users who have been members for at least 4 weeks, have uploaded at least
		25 GB and have a share ratio above 1.05. Moderator changes of status last only until the next execution of the script.</td>
	</tr>
	<tr>
		<td class='embedded' bgcolor='#F5F4EA' valign='top'>&nbsp; <span style='font-weight:bold;'><img src='<?php echo $image_dir?>star.png' width='16' height='16' border='0' alt='Donor' title='Donor' /></span></td>
		<td class='embedded' width='5'>&nbsp;</td>
		<td class='embedded'>This status is given ONLY by Sysops since they are the only one's who can verify that they actually donated something.</td>
	</tr>
	<tr>
		<td class='embedded' bgcolor='#F5F4EA' valign='top'>&nbsp; <span style='font-weight:bold;'>VIP</span></td>
		<td class='embedded' width='5'>&nbsp;</td>
		<td class='embedded'>Conferred to users you feel contribute something special to the site. (Anyone begging for VIP status will be automatically disqualified)</td>
	</tr>
	<tr>
		<td class='embedded' bgcolor='#F5F4EA' valign='top'>&nbsp; <span style='font-weight:bold;'>Other</span></td>
		<td class='embedded' width='5'>&nbsp;</td>
		<td class='embedded'>Customised title given to special users only (Not available to Users or Power Users).</td>
	</tr>
	<tr>
		<td class='embedded' bgcolor='#F5F4EA' valign='top'>&nbsp; <span style='font-weight:bold;'><span style='color : #4040c0;'>Uploader</span></span></td>
		<td class='embedded' width='5'>&nbsp;</td>
		<td class='embedded'>Appointed by Admins/SysOp. Send a PM to <a class='altlink' href='sendmessage.php?receiver=1'>Admin</a> if you think you've got a good candidate.</td>
	</tr>
	<tr>
		<td class='embedded' bgcolor='#F5F4EA' valign='top'>&nbsp; <span style='font-weight:bold;'><span style='color : #A83838;'>Moderator</span></span></td>
		<td class='embedded' width='5'>&nbsp;</td>
		<td class='embedded'>Appointed by Admin only. If you think you've got a good candidate,
		send him a <a class='altlink' href='sendmessage.php?receiver=1'>PM</a>.</td>
	</tr>
</table>
<br />

<?php

end_frame();

begin_frame("Moderating Rules - <span style='color : #004E98;'>Use your Better Judgement!</span>");

?>

<ul>
	<li>The most important Rule: Use your better judgement!</li>
	<li>Don't be afraid to say <span style='font-weight:bold;'>NO</span>!</li>
	<li>Don't Defy another Mod in Public, instead send a PM.</li>
	<li>Be Tolerant! Give the Member(s) a chance to reform.</li>
	<li>Don't act prematurely, let the Member(s) make their mistakes and THEN correct them.</li>
	<li>Try correcting any "Off Topics" rather then Closing a Thread.</li>
	<li>Move Topics rather than Locking them.</li>
	<li>Be tolerant when Moderating the Chit-Chat Section (give them some slack).</li>
	<li>If you Lock a Topic, give a brief explanation as to why you're locking it.</li>
	<li>Before you Disable a Member Account, send him/her a PM and if they reply, put them on a 2 week trial.</li>
	<li>Don't Disable a Members Account until he or she has been a member for at least 4 weeks.</li>
	<li><span style='font-weight:bold;'>Always</span> state a Reason (in the user comment box) as to why the Member is being Banned / Warned.</li>
	<li style="list-style: none"><br /></li>
</ul>

<?php

end_frame();

begin_frame("Moderating Options - <span style='color : #004E98;'>What are my Privileges as a Mod?</span>");

?>

<ul>
	<li>You can Delete and Edit Forum Posts.</li>
	<li>You can Delete and Edit Torrents.</li>
	<li>You can Delete and Change Users Avatars.</li>
	<li>You can Disable User Accounts.</li>
	<li>You can Edit the Title of VIP's.</li>
	<li>You can see the complete Info of all Users.</li>
	<li>You can Add Comments to Users (for other Mods and Admins to read).</li>
	<li>You can stop reading now because you already knew about these options. ;)</li>
</ul>

<?php

end_frame();

?>

<p align='right'><span style='color : #004E98; font-size: x-small;'><span style='font-weight:bold;'>Rules edited 2011-10-02 (22:24 GMT)</span></span></p>

<?php

}

site_footer();

?>