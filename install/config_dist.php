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
*--   This program is free software; you can redistribute it and/or modify    --*
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
*--------               Developed By: Krypto, Fireknight                --------*
*-------------------------------------------------------------------------------*
*-----------------       First Release Date August 2010      -------------------*
*-----------                 http://www.freetsp.info                 -----------*
*------                    2010 FreeTSP Development Team                  ------*
*-------------------------------------------------------------------------------*
*------           Credit To CoLdFuSiOn For TheTBDEv Installer             ------*
*------           Moddified To Work With FreeTSP By Fireknight            ------*
*-------------------------------------------------------------------------------*
*/

##############
## DB Setup ##
##############
$mysql_host = "<#mysql_host#>";
$mysql_user = "<#mysql_user#>";
$mysql_pass = "<#mysql_pass#>";
$mysql_db   = "<#mysql_db#>";

#################
## Site Config ##
#################
$site_online		= true;							# Set to false to turn Site Offline
$members_only		= true;							# Set to false to Allow Non-Members to Download
$site_url			= "<#site_url#>";				# Set this to your Site's URL No ending slash!
$site_email			= "<#site_email#>";				# Email for Sender/Return Path.
$site_name			= "<#site_name#>";				# Name of your Site
$image_dir			= "/images/";					# Images Directory
$torrent_dir		= "torrents";					# FOR UNIX ONLY - must be writable for httpd user
//$torrent_dir		= "C:/AppServ/www/torrents"; 	# FOR WINDOWS ONLY - must be writable for httpd user

$announce_urls		= array();
$announce_urls[]	= "<#announce_url#>";

$peer_limit				= 50000;		# Max Number Peers allowed before Torrents start to be Deleted to make room
$max_users				= 7500;			# Max Users before Registration Closes
$signup_timeout			= 3 * 86400;	# Default 3 Days
$min_votes				= 1;		   	# Min Votes
$autoclean_interval		= 900;			# Default 15 Mins
$announce_interval		= 1800;			# Default 30 Mins
$max_torrent_size		= 1000000;		# Max Torrent File Size Allowed
$max_dead_torrent_time	= 3 * 3600;		# Default 3 Hours
$posts_read_expiry		= 14 * 86400;	# Read Post Expiry time for Forums

$maxloginattempts       = 6;            # Max failed logins before getting banned
$dictbreaker            = "functions/dictbreaker";  # Folder Max failed logins

$oldtorrents            = 0; # delete old torrents 0=Disabled 1=Enabled
$days                   = 28; # Amount of days before Dead Torrents are removed

$GLOBALS['SitePath']            =	ROOT_DIR."";					# Dont forget to include '/' end of the line - Set to your absolute Root Directory

####################
## Forum Settings ##
####################
$maxfilesize		            =	1024*1024;			    	# The max file size allowed to be uploaded - Default: 1024*1024 = 1MB
$attachment_dir		            =	ROOT_DIR."forum_attachments";		# The path to the attachment dir, no slahses
$forum_width		            =	'100%';					# The width of the forum, in percent, 100% is the full width -- Note: the width is also set in the function begin_main_frame()
$maxsubjectlength		        =	80;				# The max subject length in the Topic Descriptions, Forum Name etc...
$postsperpage		            =	(empty($CURUSER['postsperpage']) ? 25: (int)$CURUSER['postsperpage']);				# Get's the users posts per page, no need to change
$use_attachment_mod		        =	true;				# Set to true if you want to use the attachment mod
$use_poll_mod		            =	true;					# Set to true if you want to use the forum poll mod
$forum_stats_mod                =	true;				# Set to false to disable the forum stats

$use_flood_mod		            =	true;			# Set to true if you want to use the flood mod
$limit		                    =	10;					# If there are more than $limit(default 10) posts in the last $minutes(default 5) minutes, it will give them a error... -- Note: Requires the flood mod set to true
$minutes		                =	5;				# If there are more than $limit(default 10) posts in the last $minutes(default 5) minutes, it will give them a error... -- Note: Requires the flood mod set to true


$curversion = '1.0'; # FreeTSP Version DO NOT ALTER - This will help identify code for support issues at freetsp.info
?>