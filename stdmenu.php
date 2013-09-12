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

<!-- Menu -->
<table class='mainouter' border='1' width='100%' cellspacing='0' cellpadding='10'>
    <?php print StatusBar(); ?>
    <tr>
        <td class='outer' align='center'>
            <table class='main' border='0' width='100%' cellspacing='0' cellpadding='5'>
                <tr>

                    <?php if (!$CURUSER)

                {
                    header("Refresh: 3; url='index.php'");
                }
                else
                {
                    if ($CURUSER['menu'] == "2")
                    {

                        ?>
                        <td class='navigation' align='center'><a href='/index.php'>Home</a></td>
                        <td class='navigation' align='center'><a href='/browse.php'>Browse</a></td>
                        <td class='navigation' align='center'><a href='/requests.php'>Offer/Request</a></td>
                        <td class='navigation' align='center'><a href='/search.php'>Search</a></td>
                        <td class='navigation' align='center'><a href='/upload.php'>Upload</a></td>
                        <td class='navigation' align='center'><a href='/altusercp.php'>Profile</a></td>
                        <td class='navigation' align='center'><a href='/forums.php'>Forums</a></td>
                        <td class='navigation' align='center'><a href='/topten.php'>Top 10</a></td>
                        <td class='navigation' align='center'><a href='/rules.php'>Rules</a></td>
                        <td class='navigation' align='center'><a href='/faq.php'>FAQ</a></td>
                        <td class='navigation' align='center'><a href='/links.php'>Links</a></td>
                        <td class='navigation' align='center'><a href='/credits.php'>Credits</a></td>
                        <td class='navigation' align='center'><a href='/helpdesk.php'>Help Desk</a></td>
                        <td class='navigation' align='center'><a href='/staff.php'>Staff</a></td>
                        <?php
                        if (get_user_class() >= UC_MODERATOR)
                        {
                            ?>
                            <td class='navigation' align='center'><a href='/controlpanel.php'>Staff Tools</a></td>
                            <?php
                        }
                    }
                }
                    ?>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br /><br />