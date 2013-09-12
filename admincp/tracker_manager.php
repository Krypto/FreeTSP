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

if (!defined("IN_FTSP_ADMIN"))
{
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html>
    <head>
         <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

         <title><?php if (isset($_GET['error']))
         {
             echo htmlspecialchars($_GET['error']);
         }
         ?> Error</title>

         <link rel="stylesheet" type="text/css" href="/errors/error-style.css" />
    </head>
    <body>
        <div id='container'>
            <div align='center' style='padding-top:15px'><img src='/errors/error-images/alert.png' width='89' height='94' alt='' title='' /></div>
            <h1 class='title'>Error 404 - Page Not Found</h1>
            <p class='sub-title' align='center'>The page that you are looking for does not appear to exist on this site.</p>
            <p>If you typed the address of the page into the address bar of your browser, please check that you typed it in correctly.</p>
            <p>If you arrived at this page after you used an old Boomark or Favorite, the page in question has probably been moved. Try locating the page via the navigation menu and then updating your bookmark.</p>
        </div>
    </body>
    </html>

    <?php
exit();
}

// if (get_user_class() < UC_SYSOP)

//-- Uncomment The Line Above & Comment Out The Line Below - If You Want All Sysop's To Access This Page - They Still Need All Mysql Infomation To Alter Anything --//

if (get_user_id() <> UC_TRACKER_MANAGER) //-- Only The Person Who's Id Has Been Set In Functions/function_config.php And /config_rewrite.php Can Access --//

{
    error_message('warn', 'Warning', 'Access Denied');
}

define('FTSP_ROOT_PATH', '');

$tracker_manager = new tracker_manager;

class tracker_manager
{
    var $VARS = array();

    function tracker_manager ()
    {
        $this->VARS = array_merge($_GET, $_POST);

        switch ($this->VARS['progress'])
        {
            case '1':
                $this->do_step_one();
                break;

            default:
                $this->do_start();
                break;
        }
    }

    function do_start ()
    {
        site_header('Tracker Manager',false);

        print("<div class='box_content'>");
        print("<form action='controlpanel.php?fileaction=9' method='post'>");
        print("<div>");
        print("<input type='hidden' name='progress' value='1' />");
        print("</div>");
        print("<h2 align='center'>Manage Your Tracker Settings</h2><br />");

        $query = "SELECT *
                    FROM config
                    WHERE 1=1";

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $mysql_host            = $row['mysql_host'];
            $mysql_db              = $row['mysql_db'];
            $mysql_user            = $row['mysql_user'];
            $mysql_pass            = $row['mysql_pass'];
            $site_url              = $row['site_url'];
            $announce_url          = $row['announce_url'];
            $site_online           = $row['site_online'];
            $members_only          = $row['members_only'];
            $site_mail             = $row['site_mail'];
            $email_confirm         = $row['email_confirm'];
            $site_name             = $row['site_name'];
            $image_dic             = $row['image_dic'];
            $torrent_dic           = $row['torrent_dic'];
            $peer_limit            = $row['peer_limit'];
            $max_members           = $row['max_members'];
            $max_users_then_invite = $row['max_users_then_invite'];
            $invites               = $row['invites'];
            $signup_timeout        = $row['signup_timeout'];
            $min_votes             = $row['min_votes'];
            $autoclean_interval    = $row['autoclean_interval'];
            $announce_interval     = $row['announce_interval'];
            $max_torrent_size      = $row['max_torrent_size'];
            $max_dead_torrent_time = $row['max_dead_torrent_time'];
            $posts_read_expiry     = $row['posts_read_expiry'];
            $max_login_attempts    = $row['max_login_attempts'];
            $dictbreaker           = $row['dictbreaker'];
            $delete_old_torrents   = $row['delete_old_torrents'];
            $dead_torrents         = $row['dead_torrents'];
            $site_reputation       = $row['site_reputation'];
            $maxfilesize           = $row['maxfilesize'];
            $attachment_dir        = $row['attachment_dir'];
            $forum_width           = $row['forum_width'];
            $maxsubjectlength      = $row['maxsubjectlength'];
            $postsperpage          = $row['postsperpage'];
            $use_attachment_mod    = $row['use_attachment_mod'];
            $use_poll_mod          = $row['use_poll_mod'];
            $forum_stats_mod       = $row['forum_stats_mod'];
            $use_flood_mod         = $row['use_flood_mod'];
            $limmit                = $row['limmit'];
            $minutes               = $row['minutes'];
        }

        //-- Start Database Details --//
        print("<fieldset>");
        print("<legend class='legend'><strong>MySQL Settings</strong></legend><br />");

        print("<table border='0' align='center' width='100%' cellspacing='0' cellpadding='5'>
                <tr>
                    <td class='track'><label for='host'>MySQL Host</label></td>
                    <td class='track'>
                        <input type='text' name='mysql_host' id='host' value='$mysql_host' size='20' />
                            &nbsp;localhost is normaly enough
                    </td>
                </tr>
            ");

        print("<tr>
                <td class='track'><label for='dbname'>MySQL Database Name</label></td>
                <td class='track'>
                    <input type='text' name='mysql_db' id='dbname' value='$mysql_db' size='20' />
                        &nbsp;The Name of your Database
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='user'>MySQL Username</label></td>
                <td class='track'>
                    <input type='text' name='mysql_user' id='user' value='$mysql_user' size='20' />
                        &nbsp;Your MySQL Username
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='pass'>MySQL Password</label></td>
                <td class='track'>
                    <input type='text' name='mysql_pass' id='pass' value='$mysql_pass' size='20' />
                        &nbsp;Your MySQL Password
                </td>
            </tr>
        </table>
            ");

        print("</fieldset><br />");
        //-- Finish Database Details --//

        //-- Start General Config Details --//
        print("<fieldset>");
        print("<legend class='legend'><strong>General Settings</strong></legend><br />");

        print("<table border='0' align='center' width='100%' cellspacing='0' cellpadding='5'>
                <tr>
                    <td class='track'><label for='url'>Site URL</label></td>
                    <td class='track'>
                        <input type='text' name='site_url' id='url' value='$site_url' size='50' />
                            &nbsp; Example = ( http://www.mysite.com )
                    </td>
                </tr>
            ");

        print("<tr>
                <td class='track'><label for='ann'>Announce URL</label></td>
                <td class='track'>
                    <input type='text' name='announce_url' id='ann' value='$announce_url' size='50' />
                        &nbsp; No ending slash!
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='online'>Site Online</label></td>
                <td class='track'>
                    <input type='text' name='site_online' id='online' value='$site_online' size='50' />
                        &nbsp;false = Site Offline
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='members'>Members Only</label></td>
                <td class='track'>
                    <input type='text' name='members_only' id='members' value='$members_only' size='50' />
                        &nbsp;false = Allow Non-Members to Download
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='email'>Site Email</label></td>
                <td class='track'>
                    <input type='text' name='site_mail' id='email' value='$site_mail' size='50' />
                        &nbsp;Email for Sender/Return Path
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='emailconf'>Confirm email on Signup</label></td>
                <td class='track'>
                    <input type='text' name='email_confirm' id='emailconf' value='$email_confirm' size='50' />
                        &nbsp;false = Allow Members to Signup without Confirming their email
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='name'>Site Name</label></td>
                <td class='track'>
                    <input type='text' name='site_name' id='name' value='$site_name' size='50' />
                        &nbsp;Name of your Site
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='image'>Image Directory</label></td>
                <td class='track'>
                    <input type='text' name='image_dic' id='image' value='$image_dic' size='50' />
                        &nbsp;Images Directory
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='tordir'>Torrent Directory</label></td>
                <td class='track'>
                    <input type='text' name='torrent_dic' id='tordir' value='$torrent_dic' size='50' />
                        &nbsp;See function_config for further details
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='peer'>Peer Limit</label></td>
                <td class='track'>
                    <input type='text' name='peer_limit' id='peer' value='$peer_limit' size='50' />
                        &nbsp;Max allowed before Torrents are auto Deleted
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='maxmem'>Max Members</label></td>
                <td class='track'>
                    <input type='text' name='max_users' id='maxmem' value='$max_members' size='50' />
                        &nbsp;Max Users before Registration Closes
                </td>
            </tr>
            ");

        print("<tr>
             <td class='track'><label for='max_users_then_invite'>Max Members Before Invite Only</label></td>
             <td class='track'>
                <input type='text' name='max_users_then_invite' id='max_users_then_invite' value='$max_users_then_invite' size='50' />
                    &nbsp;Max Users before Invite Only
             </td>
         </tr>
         ");

     print("<tr>
             <td class='track'><label for='invites'>Max Invites Avalible</label></td>
             <td class='track'>
                <input type='text' name='invites' id='invites' value='$invites' size='50' />
                    &nbsp;Max Number Of Invites Avalible
             </td>
         </tr>
         ");

        print("<tr>
                <td class='track'><label for='signup'>Signup Timeout</label></td>
                <td class='track'>
                    <input type='text' name='signup_timeout' id='signup' value='$signup_timeout' size='50' />
                        &nbsp;Default 3 Days, Before Signup Expires
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='votes'>Min Votes</label></td>
                <td class='track'>
                    <input type='text' name='min_votes' id='votes' value='$min_votes' size='50' />
                        &nbsp;Min Votes Required for Poll
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='clean'>Autoclean Interval</label></td>
                <td class='track'>
                    <input type='text' name='autoclean_interval' id='clean' value='$autoclean_interval' size='50' />
                        &nbsp;Default 15 Mins
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='annint'>Announce Interval</label></td>
                <td class='track'>
                    <input type='text' name='announce_interval' id='annint' value='$announce_interval' size='50' />
                        &nbsp;Default 30 Mins
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='torsize'>Max Torrent Size</label></td>
                <td class='track'>
                    <input type='text' name='max_torrent_size' id='torsize' value='$max_torrent_size' size='50' />
                        &nbsp;Max Torrent File Size Allowed
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='dead'>Max Dead Torrent Time</label></td>
                <td class='track'>
                    <input type='text' name='max_dead_torrent_time' id='dead' value='$max_dead_torrent_time' size='50' />
                        &nbsp;Default 3 Hours
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='postread'>Post Read Expiry Time</label></td>
                <td class='track'>
                    <input type='text' name='posts_read_expiry' id='postread' value='$posts_read_expiry' size='50' />
                        &nbsp;Default 14 Days
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='attempts'>Max Login Attempts</label></td>
                <td class='track'>
                    <input type='text' name='maxloginattempts' id='attempts' value='$max_login_attempts' size='50' />
                        &nbsp;Max Failed Logins before IP is Banned
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='dict'>dictbreaker.php Path</label></td>
                <td class='track'>
                    <input type='text' name='dictbreaker' id='dict' value='$dictbreaker' size='50' />
                        &nbsp;Folder for Max Failed Logins Attempts
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='autodel'>Auto Delete Old Torrents</label></td>
                <td class='track'>
                    <input type='text' name='oldtorrents' id='autodel' value='$delete_old_torrents' size='50' />
                        &nbsp;Delete Old Torrents (0 = Disabled - 1 = Enabled)
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='deadtor'>Dead Torrents</label></td>
                <td class='track'>
                    <input type='text' name='days' id='deadtor' value='$dead_torrents' size='50' />
                        &nbsp;Amount of Days before Dead Torrents are Removed
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='site_reputation'>Site Reputation</label></td>
                <td class='track'>
                    <input type='text' name='site_reputation' id='site_reputation' value='$site_reputation' size='50' />
                        &nbsp;Show Site Repution
                </td>
            </tr>
        </table>
            ");

        print("</fieldset><br />");
        //-- Finish General Config Details --//

        //-- Start Forum Config Details --//
        print("<fieldset>");
        print("<legend class='legend'><strong>Forum Settings</strong></legend><br />");

        print("<table border='0' align='center' width='100%' cellspacing='0' cellpadding='5'>
                <tr>
                    <td class='track'><label for='filesize'>Max File Upload Size</label></td>
                    <td class='track'>
                        <input type='text' name='maxfilesize' id='filesize' value='$maxfilesize' size='50' />
                            &nbsp;Default Size: 1024*1024 = 1MB
                    </td>
                </tr>
            ");

        print("<tr>
                <td class='track'><label for='attdir'>Attachment File Directory</label></td>
                <td class='track'>
                    <input type='text' name='attachment_dir' id='attdir' value='$attachment_dir' size='50' />
                        &nbsp;The Path to the Attachment Folder, NO Slahses
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='width'>Forum Width %</label></td>
                <td class='track'>
                    <input type='text' name='forum_width' id='width' value='$forum_width' size='50' />
                        &nbsp;The Width of the Forum - 100% is the Full Width
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='forname'>Max Length of the Forum Name</label></td>
                <td class='track'>
                    <input type='text' name='maxsubjectlength' id='forname' value='$maxsubjectlength' size='50' />
                        &nbsp;Max ( Default = 80 Characters )
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='ppp'>Posts Per Page</label></td>
                <td class='track'>
                    <input type='text' name='postsperpage' id='ppp' value='$postsperpage' size='50' />
                        &nbsp;Max ( Default = 25 Posts )
                </td>
            </tr>
            ");

        print("<tr>
                <td class='track'><label for='attach'>Attachments</label></td>
                <td class='track'>
                    <input type='text' name='use_attachment_mod' id='attach' value='$use_attachment_mod' size='50' />
                        &nbsp;Set to True if you want to use the Attachment Mod
                </td>
            </tr>");

        print("<tr>
                <td class='track'><label for='poll'>Forum Poll</label></td>
                <td class='track'>
                    <input type='text' name='use_poll_mod' id='poll' value='$use_poll_mod' size='50' />
                        &nbsp;Set to True if you want to use the Forum Poll Mod
                </td>
            </tr>");

        print("<tr>
                <td class='track'><label for='stats'>Forum Stats</label></td>
                <td class='track'>
                    <input type='text' name='forum_stats_mod' id='stats' value='$forum_stats_mod' size='50' />
                        &nbsp;Set to True if you want to use the Forum Stats Mod
                </td>
            </tr>
        </table><br />");

        print("<div align='center'><strong><span style='color : #FF0000; font-weight:bold;'>If a Member makes more than ? Posts (Default = 10) in the last ? minutes (Default = 5) - they will receive an Error...</span></strong></div><br />");

        print("<table width='100%' align='center'border='0' cellspacing='0' cellpadding='5'>
                <tr>
                    <td class='track'><label for='flood'>Forum Flood Control Mod</label></td>
                    <td class='track'>
                        <input type='text' name='use_flood_mod' id='flood' value='$use_flood_mod' size='50' />
                            &nbsp;Set to True to use the Forum Flood Control Mod
                    </td>
                </tr>");

        print("<tr>
                <td class='track'><label for='posts'>Posts Limit</label></td>
                <td class='track'>
                    <input type='text' name='limmit' id='posts' value='$limmit' size='50' />
                        &nbsp;( Default 10 ) Requires Flood Control Mod set to True
                </td>
            </tr>");

        print("<tr>
                <td class='track'><label for='time'>Posts Time Limit</label></td>
                <td class='track'>
                    <input type='text' name='minutes' id='time' value='$minutes' size='50' />
                        &nbsp;( Default 5 ) Requires Flood Control Mod set to True
                </td>
            </tr>
        </table>");

        print("</fieldset><br />");
        //-- Finish Forum Config Details-- //

        //-- Start Display Legend --//
        print("<fieldset>");
        print("<legend class='legend'><strong>Info on Calculating Time</strong></legend><br />");
        print("<table class='main' border='0' align='center' cellspacing='0' cellpadding='5'>");
        print("<tr><td class='colhead'>15 Mins = 900</td><td class='colhead'>1 Hour = 3600</td><td class='colhead'>1 Day = 86400</td></tr></table>");
        print("<br /></fieldset>");
        //-- End Display Legend --//

        print("<div align='center'><br /><strong><span style='color : #FF0000; font-weight:bold;'>If you leave any boxes empty!<br />This will create empty spaces in your function_config.php<br />Causing your site, NOT to function correctly<br /></span></strong></div><br />");

        print("<div class='proceed-btn-div' align='center'><input type='submit' class='btn' value='Submit' /></div>
        </form>
        </div>");

        site_footer();

    }

    function do_step_one ()
    {
        //-- Open config_rewrite.php --//
        $conf_string = file_get_contents('./config_rewrite.php');

        $placeholders = array('<#mysql_host#>',
                              '<#mysql_db#>',
                              '<#mysql_user#>',
                              '<#mysql_pass#>',
                              '<#announce_url#>',
                              '<#site_url#>',
                              '<#site_online#>',
                              '<#members_only#>',
                              '<#site_mail#>',
                              '<#email_confirm#>',
                              '<#site_name#>',
                              '<#image_dic#>',
                              '<#torrent_dic#>',
                              '<#peer_limit#>',
                              '<#max_users#>',
                              '<#max_users_then_invite#>',
                              '<#invites#>',
                              '<#signup_timeout#>',
                              '<#min_votes#>',
                              '<#autoclean_interval#>',
                              '<#announce_interval#>',
                              '<#max_torrent_size#>',
                              '<#max_dead_torrent_time#>',
                              '<#posts_read_expiry#>',
                              '<#maxloginattempts#>',
                              '<#dictbreaker#>',
                              '<#oldtorrents#>',
                              '<#days#>',
                              '<#site_reputation#>',
                              '<#maxfilesize#>',
                              '<#attachment_dir#>',
                              '<#forum_width#>',
                              '<#maxsubjectlength#>',
                              '<#postsperpage#>',
                              '<#use_attachment_mod#>',
                              '<#use_poll_mod#>',
                              '<#forum_stats_mod#>',
                              '<#use_flood_mod#>',
                              '<#limmit#>',
                              '<#minutes#>');

        $replacements = array($this->VARS['mysql_host'],
                              $this->VARS['mysql_db'],
                              $this->VARS['mysql_user'],
                              $this->VARS['mysql_pass'],
                              $this->VARS['announce_url'],
                              $this->VARS['site_url'],
                              $this->VARS['site_online'],
                              $this->VARS['members_only'],
                              $this->VARS['site_mail'],
                              $this->VARS['email_confirm'],
                              $this->VARS['site_name'],
                              $this->VARS['image_dic'],
                              $this->VARS['torrent_dic'],
                              $this->VARS['peer_limit'],
                              $this->VARS['max_users'],
                              $this->VARS['max_users_then_invite'],
                              $this->VARS['invites'],
                              $this->VARS['signup_timeout'],
                              $this->VARS['min_votes'],
                              $this->VARS['autoclean_interval'],
                              $this->VARS['announce_interval'],
                              $this->VARS['max_torrent_size'],
                              $this->VARS['max_dead_torrent_time'],
                              $this->VARS['posts_read_expiry'],
                              $this->VARS['maxloginattempts'],
                              $this->VARS['dictbreaker'],
                              $this->VARS['oldtorrents'],
                              $this->VARS['days'],
                              $this->VARS['site_reputation'],
                              $this->VARS['maxfilesize'],
                              $this->VARS['attachment_dir'],
                              $this->VARS['forum_width'],
                              $this->VARS['maxsubjectlength'],
                              $this->VARS['postsperpage'],
                              $this->VARS['use_attachment_mod'],
                              $this->VARS['use_poll_mod'],
                              $this->VARS['forum_stats_mod'],
                              $this->VARS['use_flood_mod'],
                              $this->VARS['limmit'],
                              $this->VARS['minutes']);

        $conf_string = str_replace($placeholders, $replacements, $conf_string);

        if ($fh = fopen(FTSP_ROOT_PATH.'functions/function_config.php', 'w'))
        {
            fputs($fh, $conf_string, strlen($conf_string));
            fclose($fh);
        }

        //-- Write To Database -- Config Setup -- To Keep Setup Changes --//
        function config ()
        {
            sql_query("TRUNCATE TABLE config") or sqlerr(__FILE__, __LINE__);

            sql_query("INSERT INTO config "."(mysql_host, mysql_db, mysql_user, mysql_pass, site_url, announce_url, site_online, members_only, site_mail, email_confirm, site_name, image_dic, torrent_dic, peer_limit, max_members, max_users_then_invite, invites, signup_timeout, min_votes, autoclean_interval, announce_interval, max_torrent_size, max_dead_torrent_time, posts_read_expiry, max_login_attempts, dictbreaker, delete_old_torrents, dead_torrents, site_reputation, maxfilesize, attachment_dir, forum_width, maxsubjectlength, postsperpage, use_attachment_mod, use_poll_mod, forum_stats_mod, use_flood_mod, limmit,minutes )"."
            VALUES('".$_POST['mysql_host']."', '".$_POST['mysql_db']."', '".$_POST['mysql_user']."', '".$_POST['mysql_pass']."', '".$_POST['site_url']."', '".$_POST['announce_url']."', '".$_POST['site_online']."', '".$_POST['members_only']."', '".$_POST['site_mail']."', '".$_POST['email_confirm']."', '".$_POST['site_name']."', '".$_POST['image_dic']."', '".$_POST['torrent_dic']."', '".$_POST['peer_limit']."', '".$_POST['max_users']."', '".$_POST['max_users_then_invite']."', '".$_POST['invites']."', '".$_POST['signup_timeout']."', '".$_POST['min_votes']."', '".$_POST['autoclean_interval']."', '".$_POST['announce_interval']."', '".$_POST['max_torrent_size']."', '".$_POST['max_dead_torrent_time']."', '".$_POST['posts_read_expiry']."', '".$_POST['maxloginattempts']."', '".$_POST['dictbreaker']."', '".$_POST['oldtorrents']."', '".$_POST['days']."', '".$_POST['site_reputation']."', '".$_POST['maxfilesize']."', '".$_POST['attachment_dir']."', '".$_POST['forum_width']."', '".$_POST['maxsubjectlength']."', '".$_POST['postsperpage']."', '".$_POST['use_attachment_mod']."', '".$_POST['use_poll_mod']."', '".$_POST['forum_stats_mod']."', '".$_POST['use_flood_mod']."', '".$_POST['limmit']."', '".$_POST['minutes']."')");
        }

        config();

        global $site_url;

        error_message_center("success", "Success", "<strong>The Data Base Has Been Successfully Updated.</strong><br />
                                                  <br /> Return to <a href='controlpanel.php?fileaction=9'>Tracker Manager Page</a>
                                                  <br /> Return to <a href='controlpanel.php'>Control Panel</a>
                                                  <br /> Return to <a href='index.php'>Main Page</a>");

        site_header('Update Successful!');

        display_message('success', 'Success', 'The Required Updates to your function_config.php have been made');

        site_footer();

    }

} //-- End Class --//

?>