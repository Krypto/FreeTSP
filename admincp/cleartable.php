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

        <link rel='stylesheet' type='text/css' href='/errors/error-style.css' />
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

site_header("Mysql Table Cleaner", false);

$action = ($_POST['action'] ? $_POST['action'] : ($_GET['action'] ? $_GET['action'] :''));

//-- Clear Shout Box --//
if ($action == 'clshout')
{
    error_message_center("warn", "Sanity Check",
                                 "Are you sure you wish to Clear the <strong>Shout Box</strong><br />
                                 <form method='post' action='controlpanel.php?fileaction=25&amp;action=clearshout'>
                                 <input type='hidden' name='username' size='20' value='".$username."' />
                                 <input type='submit' class='btn' value='Yes Clear' />
                                 </form>");
}

if ($action == 'clearshout')
{
    sql_query('TRUNCATE TABLE shoutbox') or sqlerr(__FILE__, __LINE__);

    error_message_center("success", "Success",
                                    "The Shout Box table has been emptied!<br />
                                    Return to the <a href='controlpanel.php?fileaction=25'>Mysql Table Clear</a><br />
                                    Return to the <a href='controlpanel.php'>Admin Control Panel</a>");
}

//-- Clear Private Messages --//
if ($action == 'clmsg')
{
    error_message_center("warn", "Sanity Check",
                                 "Are you sure you wish to Clear All the <strong>Private Messages</strong><br />
                                 <form method='post' action='controlpanel.php?fileaction=25&amp;action=clearmsg'>
                                 <input  type='hidden'name='username' size='20' value='".$username."' />
                                 <input type='submit' class='btn' value='Yes Clear' />
                                 </form>");
}

if ($action == 'clearmsg')
{
    sql_query('TRUNCATE TABLE messages') or sqlerr(__FILE__, __LINE__);

    error_message_center("success", "Success",
                                    "The Private Message table has been emptied!<br />
                                    Return to the <a href='controlpanel.php?fileaction=25'>Mysql Table Clear</a><br />
                                    Return to the <a href='controlpanel.php'>Admin Control Panel</a>");
}

//-- Clear Staff Log --//
if ($action == 'clstlog')
{
    error_message_center("warn", "Sanity Check",
                                 "Are you sure you wish to Clear the <strong>Staff Log</strong><br />
                                 <form method='post' action='controlpanel.php?fileaction=25&amp;action=clearstlog'>
                                 <input type='hidden' name='username' size='20' value='".$username."' />
                                 <input type='submit' class='btn' value='Yes Clear' />
                                 </form>");
}

if ($action == 'clearstlog')
{
    sql_query('TRUNCATE TABLE stafflog') or sqlerr(__FILE__, __LINE__);

    error_message_center("success", "Success",
                                    "The Staff Log table has been emptied!<br />
                                    Return to the <a href='controlpanel.php?fileaction=25'>Mysql Table Clear</a><br />
                                    Return to the <a href='controlpanel.php'>Admin Control Panel</a>");
}

//-- Clear Max Login Attempts --//
if ($action == 'clsmaxlogin')
{
    error_message_center("warn", "Sanity Check",
                                 "Are you sure you wish to Clear the <strong>Max Login Attempts</strong><br />
                                 <form method='post' action='controlpanel.php?fileaction=25&amp;action=clearmaxlogin'>
                                 <input type='hidden' name='username' size='20' value='".$username."' />
                                 <input type='submit' class='btn' value='Yes Clear' />
                                 </form>");
}

if ($action == 'clearmaxlogin')
{
    sql_query('TRUNCATE TABLE loginattempts') or sqlerr(__FILE__, __LINE__);

    error_message_center("success", "Success",
                                    "The Max Login Attempts table has been emptied!<br />
                                    Return to the <a href='controlpanel.php?fileaction=25'>Mysql Table Clear</a><br />
                                    Return to the <a href='controlpanel.php'>Admin Control Panel</a>");
}

//-- Viewable Message On Entry To File --//
error_message_center("info", "Select A Table To Clear",
                             "Empty Shout Box <a href='controlpanel.php?fileaction=25&amp;action=clshout'>HERE</a><br />
                             Empty Messages <a href='controlpanel.php?fileaction=25&amp;action=clmsg'>HERE</a><br />
                             Empty Staff Log <a href='controlpanel.php?fileaction=25&amp;action=clstlog'>HERE</a><br />
                             Empty Max Login Attempts <a href='controlpanel.php?fileaction=25&amp;action=clsmaxlogin'>HERE</a><br />");

site_footer();

?>