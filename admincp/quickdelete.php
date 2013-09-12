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
    $username = $_POST['username'];

    if (!$username)
    {
        error_message_center("error", "Error", "You Must Enter A Username.");
    }

        $res = sql_query("SELECT id, username, class
                            FROM users
                            WHERE username = ".sqlesc($username)) or sqlerr(__FILE__, __LINE__);

        $arr      = mysql_fetch_assoc($res);
        $id       = $arr['id'];
        $username = $arr['username'];
        $class    = $arr['class'];

    if (mysql_num_rows($res) != 1)
    {
        error_message_center("error", "Error", "<strong>The Member Does Not Exist.</strong><br />
                                                <br /> Return to the <a href='controlpanel.php?fileaction=17'>Quick Delete Member</a>
                                                <br /> Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                                <br /> Return to the <a href='index.php'>Main Page</a>");
    }

    if ($username == $CURUSER['username'])
    {
        error_message_center("error", "Error", "<strong>You Cannot Delete Yourself!</strong><br />
                                                <br /> Return to <a href='controlpanel.php?fileaction=17'>Quick Delete Member</a>
                                                <br /> Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                                <br /> Return to the <a href='index.php'>Main Page</a>");
    }

    if ($class >= $CURUSER['class'])
    {
        error_message("error", "Error", "<strong>You Do Not Have Permission To Delete This Member!</strong><br />
                                                <br /> Return to <a href='controlpanel.php?fileaction=17'>Quick Delete Member</a>
                                                <br /> Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                                <br /> Return to the <a href='index.php'>Main Page</a>");
    }

        $res = sql_query("DELETE
                            FROM users
                            WHERE id=$id") or sqlerr(__FILE__, __LINE__);

        error_message_center("success", "Success", "<strong>The Members Account Has Been Successfully Deleted.</strong><br />
                                                <br /> Return to <a href='controlpanel.php?fileaction=17'>Quick Delete Member</a>
                                                <br /> Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                                <br /> Return to the <a href='index.php'>Main Page</a>");
}

site_header("Delete Account",false);

print("<h1>Delete A Members Account</h1>");
print("<form method='post' action='controlpanel.php?fileaction=17'>");
print("<table border='1' cellspacing='0' cellpadding='5'>");

print("<tr>
        <td class='colhead'><label for='delete'>User Name</label></td>
        <td class='rowhead'>
            <input type='text' name='username' id='delete' size='40' /></td>
    </tr>");

print("<tr>
        <td class='std' align='center' colspan='2'>
            <input type='submit' class='btn' value='Delete' /></td>
    </tr>");

print("</table>");
print("</form>");

site_footer();

?>