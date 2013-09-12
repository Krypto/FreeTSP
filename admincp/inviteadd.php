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

$class = (isset($_POST['class']) ? $_POST['class'] : '');
$give  = (isset($_POST['give']) ? $_POST['give'] : '');

if ($give)
{
    $invite_options = array('>= 0' => 1,
                            '= 0' => 2,
                            '= 1' => 3,
                            '= 2' => 4,
                            '>= 3' => 5);

    if (!isset($invite_options[$class]))
    {
        error_message('warn', 'Error', 'Invalid Class!');
    }

    if ($give != 0)
    {
        $res = sql_query("UPDATE users
                            SET invites = invites + $give
                            WHERE class $class")or sqlerr(__FILE__, __LINE__);

        $expires   = get_date_time((strtotime(get_date_time()) + (86400 * 7))); // 86400 seconds in one day.
        $created   = get_date_time();

        $ann_query = ("SELECT u.id ".
                        "FROM users AS u ".
                        "WHERE class $class");

        $subject   = ("Invites from $site_name");
        $body      = ("You have had $give Invite".($give > 1 ? "s" : "")." Added to your Account");

        $query = sprintf('INSERT INTO announcement_main '.'(owner_id, created, expires, sql_query, subject, body) '.
                            'VALUES (%s, %s, %s, %s, %s, %s)',
                            sqlesc($CURUSER['id']),
                            sqlesc($created),
                            sqlesc($expires),
                            sqlesc($ann_query),
                            sqlesc($subject),
                            sqlesc($body));

        sql_query($query);

        error_message_center("info","Info", "Invites Added !!<br />
                                            Return to the <a href='controlpanel.php?fileaction=27'>Invite Page</a> and Create some more Invites<br />
                                            Return to the <a href='controlpanel.php'>Control Panel</a><br />
                                            Return to the <a href='index.php'>Main Page</a>");
    }
    else
    {
    $res = sql_query("UPDATE users
                        SET invites = 0
                        WHERE class $class");
    }
}

site_header("Add Invites",false);

print("<div align='center'><h2>Give Invites Per Class</h2></div>");
print("<form method='post' action='controlpanel.php?fileaction=27'>");
print("<table class='main' align='center' width='81%'>");

print("<tr>
        <td class='colhead' align='center' width='20%'>All Members</td>
        <td class='colhead' align='center' width='20%'>Users</td>
        <td class='colhead' align='center' width='20%'>Power Users</td>
        <td class='colhead' align='center' width='20%'>VIP</td>
        <td class='colhead' align='center' width='20%'>All Staff</td>
    </tr>");

print("<tr>
        <td class='rowhead' align='center' width='20%'>
            <input type='radio' name='class' value='>= 0' checked='checked' />
        </td>

        <td class='rowhead' align='center' width='20%'>
            <input type='radio' name='class' value='= 0' />
        </td>

        <td class='rowhead' align='center' width='20%'>
            <input type='radio' name='class' value='= 1' />
        </td>

        <td class='rowhead' align='center' width='20%'>
            <input type='radio' name='class' value='= 2' />
        </td>

        <td class='rowhead' align='center' width='20%'>
            <input type='radio' name='class' value='>= 3' />
        </td>
    </tr>");

print("<tr>
        <td class='rowhead' align='center' colspan='5'>
            <select name='give'>
                <option value='1'>1 Invite</option>
                <option value='2'>2 Invites</option>
                <option value='3'>3 Invites</option>
                <option value='5'>5 Invites</option>
                <option value='10'>10 Invites</option>
            </select>
            <input type='submit' class='btn' value='Add Invites' />
        </td>
    </tr>");

print("<tr>
            <td class='colhead' align='center' colspan='5'>Remove All Invites&nbsp;&nbsp; <a href=\"javascript: klappe_news('a5')\"><strong>HERE</strong></a>
               <div id='ka5' style='display: none'><input type='submit' class='btn' name='give' value='Remove All Invites' /></div>
            </td>
        </tr>");


print("</table>");
print("</form>");

site_footer();

die;

?>