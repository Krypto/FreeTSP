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

?>

<table class='mainouter' width='100%' border='1' cellspacing='0' cellpadding='10'>
<?php print StatusBar(); ?>
	<tr>
		<td class='outer' align='center'>
			<div class="navigation">
				<ul class="stn-menu TSP">
					<li><a href="index.php">Home</a></li>
					<li class="hasSubNav hasArrow">
					<a href="javascript:">Torrents</a>
					<span class="arrow"></span>
						<ul>
							<li><a href="browse.php">Browse</a></li>
							<li><a href="search.php">Search</a></li>
							<li><a href="upload.php">Upload</a></li>
							<li><a href="mytorrents.php">My Torrents</a></li>
						</ul>
					</li>

					<li class="hasSubNav hasArrow">
					<a href="javascript:">User CP</a>
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

					<li class="hasSubNav hasArrow">
					<a href="javascript:">Site Info</a>
					<span class="arrow"></span>
						<ul>
							<li><a href="rules.php">Rules</a></li>
							<li><a href="faq.php">F.A.Q.</a></li>
							<li><a href="topten.php">Top Ten</a></li>
							<li><a href="links.php">Links</a></li>
						</ul>
					</li>

					<li><a href="staff.php">Staff</a></li>

					<?php if (get_user_class() >= UC_MODERATOR){?>
					<li class="hasSubNav hasArrow">
					<a href="javascript:">Staff Tools</a>
					<span class="arrow"></span>
						<ul>
							<li>
							<?php if (get_user_class() >= UC_MODERATOR){?>
							<a href="javascript:">Moderators</a>
							<span class="arrow"></span>
								<ul>
									<li><a href="bans.php">Manage Bans</a></li>
									<li><a href="testip.php">Test an IP Address</a></li>
									<li><a href="usersearch.php">Search Users</a></li>
								</ul>
							<?php }?>
							</li>

							<li>
							<?php if (get_user_class() >= UC_ADMINISTRATOR){?>
							<a href="javascript:">Administrators</a>
							<span class="arrow"></span>
								<ul>
									<li><a href="last24history.php">Last 24 History</a></li>
									<li><a href="javascript:">T.B.A</a></li>
									<li><a href="javascript:">T.B.A</a></li>
								</ul>
							<?php }?>
							</li>

							<li>
							<?php if (get_user_class() >= UC_SYSOP){?>
							<a href="javascript:">Sysops</a>
							<span class="arrow"></span>
								<ul>
									<li><a href="adduser.php">Add User</a></li>
									<li><a href="stylesheets.php">Theme Manager</a></li>
									<li><a href="category.php">Category Manager</a></li>
									<li><a href="tracker_manager.php">Tracker Manager</a></li>
								</ul>
							<?php }?>
							</li>
						</ul>
					</li>
					<?php }?>
				</ul>
			</div>
		</td>
	</tr>
</table>
<br /><br />