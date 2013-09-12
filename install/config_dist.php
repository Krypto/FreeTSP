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
**
** Credit To CoLdFuSiOn For The TBDev Installer
** Moddified To Work With FreeTSP By Fireknight
**/

##############
## DB Setup ##
##############
$mysql_host = "<#mysql_host#>"; # Your mysql host name -- localhost is the default
$mysql_user = "<#mysql_user#>"; # Your mysql username
$mysql_pass = "<#mysql_pass#>"; # Your mysql password
$mysql_db   = "<#mysql_db#>";   # Your mysql data base name

#################
## Site Config ##
#################
$site_online  = true;               # Set to false to turn Site Offline
$members_only = true;               # Set to false to Allow Non-Members to Download
$site_url     = "<#site_url#>";     # Set this to your Site's URL No ending slash!
$site_email   = "<#site_email#>";   # Email for Sender/Return Path.
$email_confirm = false;             # Allow Members to Signup without Confirming their email.
$site_name    = "<#site_name#>";    # Name of your Site
$image_dir    = "/images/";         # Images Directory
$torrent_dir  = "torrents";         # FOR UNIX ONLY - must be writable for httpd user
//$torrent_dir      = "C:/AppServ/www/torrents";    # FOR WINDOWS ONLY - must be writable for httpd user

$announce_urls   = array();
$announce_urls[] = "<#announce_url#>";

$peer_limit            = 50000;                         # Max Number Peers allowed before Torrents start to be Deleted to make room
$max_users             = 7500;                          # Max Users before Registration Closes
$max_users_then_invite = 5000;                          # Max Users before Invite Only
$invites               = 2500;                          # Max number of invites avalible
$signup_timeout        = 3 * 86400;                     # Default 3 Days
$min_votes             = 1;                             # Min Votes
$autoclean_interval    = 900;                           # Default 15 Mins
$announce_interval     = 1800;                          # Default 30 Mins
$max_torrent_size      = 1000000;                       # Max Torrent File Size Allowed
$max_dead_torrent_time = 3 * 3600;                      # Default 3 Hours
$posts_read_expiry     = 14 * 86400;                    # Read Post Expiry time for Forums

$maxloginattempts = 6;                          # Max failed logins before getting banned
$dictbreaker      = "functions/dictbreaker";    # Folder Max failed logins

$oldtorrents = 0;   # delete old torrents 0=Disabled 1=Enabled
$days        = 28;  # Amount of days before Dead Torrents are removed

$site_reputation  = false;   # Set to false to turn Reputation system off

########################
## Define Directories ##
########################
define('FUNC_DIR',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('ROOT_DIR',realpath(FUNC_DIR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
define('ADMIN_DIR', ROOT_DIR.'admincp'.DIRECTORY_SEPARATOR);
define('CACHE_DIR', ROOT_DIR.'cache'.DIRECTORY_SEPARATOR);
define('STYLES_DIR', ROOT_DIR.'stylesheets'.DIRECTORY_SEPARATOR);

define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);
define ('UC_MANAGER', 7);

define ('UC_TRACKER_MANAGER', 1); ///---Set the ID# to match the member who will have access to the Tracker Manager---///

####################
## Forum Settings ##
####################
$maxfilesize        = 1024 * 1024;                  # The max file size allowed to be uploaded - Default: 1024*1024 = 1MB
$attachment_dir     = ROOT_DIR."forum_attachments"; # The path to the attachment dir, no slahses
$forum_width        = '100%';                       # The width of the forum, in percent, 100% is the full width -- Note: the width is also set in
                                                    # the function begin_main_frame()
$maxsubjectlength   = 80;                           # The max subject length in the Topic Descriptions, Forum Name etc...
$postsperpage       = (empty($CURUSER['postsperpage']) ? 25 : (int) $CURUSER['postsperpage']); # Get's the users posts per page, no need to change
$use_attachment_mod = true; # Set to true if you want to use the attachment mod
$use_poll_mod       = true; # Set to true if you want to use the forum poll mod
$forum_stats_mod    = true; # Set to false to disable the forum stats

$use_flood_mod = true;  # Set to true if you want to use the flood mod
$limit         = 10;    # If there are more than $limit(default 10) posts in the last $minutes(default 5) minutes, it will give them a error... --
                        # Note: Requires the flood mod set to true
$minutes       = 5;     # If there are more than $limit(default 10) posts in the last $minutes(default 5) minutes, it will give them a error... --
                        # Note: Requires the flood mod set to true

define ('FTSP', 'FreeTSP');
$curversion = 'v1.0'; # FreeTSP Version DO NOT ALTER - This will help identify code for support issues at freetsp.info

?>