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
                            <li><a href="offers.php">Offers</a></li>
                            <li><a href="requests.php">Requests</a></li>
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
                            <li><a href="credits.php">Credits</a></li>
                        </ul>
                    </li>

                    <li><a href="helpdesk.php">Help Desk</a></li>
                    <li><a href="staff.php">Staff</a></li>

                    <?php if (get_user_class() >= UC_MODERATOR)
                { ?>
                    <li><a href='controlpanel.php'>Staff Tools</a></li>
                <?php }?>
                </ul>
            </div>
        </td>
    </tr>
</table>
<br /><br />