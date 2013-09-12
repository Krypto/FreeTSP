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
    if ($_POST["username"] == "" || $_POST["password"] == "" || $_POST["email"] == "")
    {
        error_message("error", "Error", "Missing Data.");
    }

    if ($_POST["password"] != $_POST["password2"])
    {
        error_message("error", "Error", "Passwords Mismatch.");
    }

    if (!validemail($_POST['email']))
    {
        error_message("error", "Error", "Not a Valid Email");
    }

    $username = sqlesc($_POST["username"]);
    $password = $_POST["password"];
    $email    = sqlesc($_POST["email"]);
    $secret   = mksecret();
    $passhash = sqlesc(md5($secret.$password.$secret));
    $secret   = sqlesc($secret);

    sql_query("INSERT INTO users (added, last_access, secret, username, passhash, status, email)
                VALUES('".get_date_time()."', '".get_date_time()."', $secret, $username, $passhash, 'confirmed', $email)") or sqlerr(__FILE__, __LINE__);

    $res = sql_query("SELECT id
                        FROM users
                        WHERE username = $username");

    $arr = mysql_fetch_row($res);

    if (!$arr)
    {
        error_message("error", "Error", "Sorry, I'm unable to create the account, the Username you submitted is already in use.");
    }

    $id = 0 + $arr["0"];

    error_message_center("success", "Success", "<strong>The Account Has Been Successfully Created.</strong><br />
                                          <br /> View The New Members <a href='$site_url/userdetails.php?id=$arr[0]'>Details Page</a>
                                          <br /> Return to <a href='controlpanel.php?fileaction=5'>Add User Page</a>
                                          <br /> Return to <a href='index.php'>Main Page</a>");

    die();
}

site_header("Add User",false);

print("<h1>Add User</h1>");
print("<br />");
print("<form method='post' action='controlpanel.php?fileaction=5'>");
print("<table border='1' align='center' cellspacing='0' cellpadding='5'>");

print("<tr>
        <td class='colhead'><label for='username'>User Name</label></td>
        <td class='rowhead'>
            <input type='text' name='username' id='username' size='40' />
        </td>
    </tr>");

print("<tr>
        <td class='colhead'><label for='password'>Password</label></td>
        <td class='rowhead'>
            <input type='password' name='password' id='password' size='40' />
        </td>
    </tr>");

print("<tr>
        <td class='colhead'><label for='password2'>Re-type Password</label></td>
        <td class='rowhead'>
            <input type='password' name='password2' id='password2' size='40' />
        </td>
    </tr>");

print("<tr>
        <td class='colhead'><label for='email'>E-Mail</label></td>
        <td class='rowhead'>
            <input type='text' name='email' id='email' size='40' />
        </td>
    </tr>");

print("<tr>
        <td class='std' colspan='2' align='center'>
            <input type='submit' class='btn' value='Okay' />
        </td>
    </tr>");

print("</table>");
print("</form>");

print("<br />");

site_footer();

?>