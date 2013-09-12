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

if ($_SERVER["REQUEST_METHOD"] == "POST")

{
    $class = (isset($_POST['class']) ? $_POST['class'] : '');

    if (!$_POST['class'])
    {
        error_message_center('warn', 'Error', 'Invalid Class!');
    }

    $body = trim((isset($_POST['message']) ? $_POST['message'] : ''));

    if (!$_POST['message'])
    {
        error_message_center('warn', 'Error', 'Missing Announcement!');
    }

    $subject = trim((isset($_POST['subject']) ? $_POST['subject'] : ''));

    if (!$_POST['subject'])
    {
        error_message_center('warn', 'Error', 'No Subject!');
    }

    $expires   = get_date_time((strtotime(get_date_time()) + (86400 * $expiry))); // 86400 seconds in one day.
    $created   = get_date_time();

    $ann_query = ("SELECT u.id FROM users AS u WHERE class $class");

    $query = sprintf('INSERT INTO announcement_main '.'(owner_id, created, expires, sql_query, subject, body) '.
                     'VALUES (%s, %s, %s, %s, %s, %s)',
                              sqlesc($CURUSER['id']),
                              sqlesc($created),
                              sqlesc($expires),
                              sqlesc($ann_query),
                              sqlesc($subject),
                              sqlesc($body));

    sql_query($query);

    error_message_center("info", "Info", "Announcement Made !!<br />
                                        Return to the <a href='controlpanel.php?fileaction=28'>Announcement Page</a><br />
                                        Return to the <a href='controlpanel.php'>Control Panel</a><br />
                                        Return to the <a href='index.php'>Main Page</a>");
}

site_header("Mass Announcement",false);

    print("<div align='center'><h2>Mass Announcement By Class</h2></div>
           <div align='center'>( For a more refined option click <a href='controlpanel.php?fileaction=3'>HERE</a> )</div>
           <div align='center'>( Example: = Announcement by Ratio )</div><br />");

    print("<form method='post' name='compose' action='controlpanel.php?fileaction=28'>
           <table class='main' width='81%' cellspacing='0' cellpadding='5' >");

    print("<tr>
               <td class='rowhead' align='center' colspan='5'>Viewable By ( check all that apply )</td>
           </tr>");

    print("<tr>
                <td class='colhead' align='center' width='20%'>All Members</td>
                <td class='colhead' align='center' width='20%'>Users</td>
                <td class='colhead' align='center' width='20%'>Power Users</td>
                <td class='colhead' align='center' width='20%'>VIP</td>
                <td class='colhead' align='center' width='20%'>All Staff</td>
            </tr>");

    print("<tr>
                <td class='rowhead' align='center' width='20%'>
                    <input type='checkbox' name='class' value='>= 0' />
                </td>

                <td class='rowhead' align='center' width='20%'>
                    <input type='checkbox' name='class' value='= 0' />
                </td>

                <td class='rowhead' align='center' width='20%'>
                    <input type='checkbox' name='class' value='= 1' />
                </td>

                <td class='rowhead' align='center' width='20%'>
                    <input type='checkbox' name='class' value='= 2' />
                </td>

                <td class='rowhead' align='center' width='20%'>
                    <input type='checkbox' name='class' value='>= 3' />
                </td>
            </tr>");

    print("<tr>
                <td class='rowhead' align='center' colspan='5'>Subject&nbsp;&nbsp;
                    <input type='text' name='subject' size='76' />
                </td>
           </tr>");

    print("<tr>
                <td class='rowhead' align='center' colspan='5'>".textbbcode("compose", "message", $body)."</td>
            </tr>");

    print("<tr><td class='rowhead' align='center' colspan='5'>");

    print("<select name='expiry'>");

           $days = array(
                         array(7,'7 Days'),
                         array(14,'14 Days'),
                         array(21,'21 Days'),
                         array(28,'28 Days'),
                         array(56,'2 Months')
                         );
           reset($days);
           foreach($days AS $x)

    print("<option value='".$x[0]."'".(($expiry == $x[0] ? "" : "")).">".$x[1]."</option>");

    print("</select>&nbsp;&nbsp;<input type='submit' class='btn' value='Submit' />");

    print("</td></tr>");

    print("</table>
           </form>");

site_footer();
?>