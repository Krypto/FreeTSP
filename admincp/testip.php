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

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $ip = isset($_POST["ip"]) ? $_POST["ip"] : false;
}
else
{
    $ip = isset($_GET["ip"]) ? $_GET["ip"] : false;
}

//-- Start Display TestIP Results --//
if ($ip)
{
    $nip = ip2long($ip);

    if ($nip == -1)
    {
        error_message("error", "Error", "<div align='center'>Bad IP.</div>");
    }

    $res = sql_query("SELECT *
                        FROM bans
                        WHERE '$nip' >= first
                        AND '$nip' <= last") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 0)
    {
        error_message_center("info", "Result", "The IP Address <strong>".htmlentities($ip, ENT_QUOTES)."</strong> is not Banned.");
    }
    else
    {
        $banstable = "<table class='main' border='0' cellspacing='0' cellpadding='5'>
                        <tr>
                            <td class='colhead'>First</td>
                            <td class='colhead'>Last</td>
                            <td class='colhead'>Comment</td>
                        </tr>";

        while ($arr = mysql_fetch_assoc($res))
        {
            $first     = long2ip($arr["first"]);
            $last      = long2ip($arr["last"]);
            $comment   = htmlspecialchars($arr["comment"]);
            $banstable .= "<tr><td>$first</td><td>$last</td><td>$comment</td></tr>\n";
        }
        $banstable .= "</table>";

        error_message_center("info", "Result", "<img src='{$image_dir}warnedbig.png' width='32' height='32' border='0' alt='This IP is Banned' title='This IP is Banned' />
                                         <br /><br />The IP address <strong>$ip</strong> is <strong>Banned</strong>
                                         <br /><br />Reason for Ban == <strong>$comment</strong>");
    }
}
//-- End Display TestIP Results --//
site_header("",false);

//-- Start TestIP Form --//
?>
<h1>Test IP Address</h1>
<form method='post' action='controlpanel.php?fileaction=2'>
    <table border='1' cellspacing='0' cellpadding='5'>
        <tr>
            <td class='colhead'><label for='ip'>IP Address</label></td>
            <td>
                <input type='text' name='ip' id='ip' size='15' />
            </td>
        </tr>
        <tr>
            <td class='rowhead' colspan='2' align='center'>
                <input type='submit' class='btn' value='OK' />
            </td>
        </tr>
    </table>
</form>

<?php
//-- End TestIP Form --//

echo("<br />");

site_footer();

?>