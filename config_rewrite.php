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
$site_online  = <#site_online#>;                # Set to false to turn Site Offline
$members_only = <#members_only#>;               # Set to false to Allow Non-Members to Download
$site_url     = "<#site_url#>";                 # Set this to your Site's URL No ending slash!
$site_email   = "<#site_mail#>";                # Email for Sender/Return Path.
$email_confirm = <#email_confirm#>;             # Allow Members to Signup without Confirming their email.
$site_name    = "<#site_name#>";                # Name of your Site
$image_dir    = "<#image_dic#>";                # Images Directory
$torrent_dir  = "<#torrent_dic#>";              # FOR UNIX ONLY - must be writable for httpd user
//$torrent_dir  =   "C:/AppServ/www/torrents";  # FOR WINDOWS ONLY - must be writable for httpd user

$announce_urls   = array();
$announce_urls[] = "<#announce_url#>";

$peer_limit            = <#peer_limit#>;                 # Max Number Peers allowed before Torrents start to be Deleted to make room
$max_users             = <#max_users#>;                  # Max Users before Registration Closes
$max_users_then_invite = <#max_users_then_invite#>;  # Max Users before Invite Only
$invites               = <#invites#>;              # Max number of invites avalible
$signup_timeout        = <#signup_timeout#>;             # Default 3 Days
$min_votes             = <#min_votes#>;                  # Min Votes
$autoclean_interval    = <#autoclean_interval#>;         # Default 15 Mins
$announce_interval     = <#announce_interval#>;          # Default 30 Mins
$max_torrent_size      = <#max_torrent_size#>;           # Max Torrent File Size Allowed
$max_dead_torrent_time = <#max_dead_torrent_time#>;      # Default 3 Hours
$posts_read_expiry     = <#posts_read_expiry#>;          # Read Post Expiry time for Forums

$maxloginattempts = <#maxloginattempts#>;               # Max failed logins before getting banned
$dictbreaker      = "<#dictbreaker#>";                  # Folder Max failed logins

$oldtorrents = <#oldtorrents#>;                         # delete old torrents 0=Disabled 1=Enabled
$days        = <#days#>;                                # Amount of days before Dead Torrents are removed

$site_reputation  = <#site_reputation#>;   # Set to false to turn Reputation system off

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
$maxfilesize        = <#maxfilesize#>;                      # The max file size allowed to be uploaded - Default: 1024*1024 = 1MB
$attachment_dir     = ROOT_DIR."<#attachment_dir#>";        # The path to the attachment dir, no slahses
$forum_width        = '<#forum_width#>';                    # The width of the forum, in percent, 100% is the full width -- Note: the width is also
                                                            # set in the function begin_main_frame()

$maxsubjectlength   = <#maxsubjectlength#>;                 # The max subject length in the Topic Descriptions, Forum Name etc...
$postsperpage       = (empty($CURUSER['postsperpage']) ? <#postsperpage#>: (int)$CURUSER['postsperpage']);
                                                            # Get's the users posts per page, no need to change

$use_attachment_mod = <#use_attachment_mod#>;               # Set to true if you want to use the attachment mod
$use_poll_mod       = <#use_poll_mod#>;                     # Set to true if you want to use the forum poll mod
$forum_stats_mod    = <#forum_stats_mod#>;                  # Set to false to disable the forum stats

$use_flood_mod = <#use_flood_mod#>;                         # Set to true if you want to use the flood mod
$limit         = <#limmit#>;                                # If there are more than $limit(default 10) posts in the last $minutes(default 5)
                                                            # minutes, it will give them a error... -- Note: Requires the flood mod set to true
$minutes       = <#minutes#>;                               # If there are more than $limit(default 10) posts in the last $minutes(default 5)
                                                            # minutes, it will give them a error... -- Note: Requires the flood mod set to true

define ('FTSP', 'FreeTSP');
$curversion = 'v1.0';                               # FreeTSP Version DO NOT ALTER - This will help identify code for support issues at freetsp.info

?>