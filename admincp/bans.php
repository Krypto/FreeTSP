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

$remove = isset($_GET['remove']) ? (int) $_GET['remove'] : 0;

if (is_valid_id($remove))
{
    @sql_query("DELETE
                FROM bans
                WHERE id='$remove'") or sqlerr();

    $removed = sprintf('Ban %s was Removed by ', $remove);

    write_log("{$removed}".$CURUSER['id']." (".$CURUSER['username'].")");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $CURUSER['class'] >= UC_MODERATOR)
{
    $first   = trim($_POST["first"]);
    $last    = trim($_POST["last"]);
    $comment = trim($_POST["comment"]);

    if (!$first || !$last || !$comment)
    {
        error_message("error", "Error", "Missing Data.");
    }

    $first = ip2long($first);
    $last  = ip2long($last);

    if ($first == -1 || $first === false || $last == -1 || $last === false)
    {
        error_message("error", "Error", "Bad IP Address.");
    }

    $comment = sqlesc($comment);
    $added   = sqlesc(get_date_time());

    sql_query("INSERT INTO bans (added, addedby, first, last, comment)
                VALUES($added, {$CURUSER['id']}, $first, $last, $comment)") or sqlerr(__FILE__, __LINE__);

      error_message_center("success", "Success", "<strong>Ban Has Been Successfully Added.</strong><br />
                                            <br /> Return to <a href='controlpanel.php?fileaction=1'>Bans Page</a>
                                            <br /> Return to <a href='index.php'>Main Page</a>");
    die;
}

$res = sql_query("SELECT first, last, added, addedby, comment, id
                    FROM bans
                    ORDER BY added DESC") or sqlerr();

//-- Start Display Existing Bans --//
site_header("Bans",false);

print("<h1>Current Bans</h1>");

if (mysql_num_rows($res) == 0)
{
    display_message_center("info", "Sorry.", "Nothing Found!");
}
else
{
    print("<table border='1' align='center' cellspacing='0' cellpadding='5'>");
    print("<tr>
            <td class='colhead'>Added</td>
            <td class='colhead' align='left'>First IP</td>
            <td class='colhead' align='left'>Last IP</td>
            <td class='colhead' align='left'>By</td>
            <td class='colhead' align='left'>Comment</td>
            <td class='colhead'>Remove</td>
        </tr>");

    while ($arr = mysql_fetch_assoc($res))
    {
        $r2 = sql_query("SELECT username
                            FROM users
                            WHERE id={$arr['addedby']}") or sqlerr();

        $a2 = mysql_fetch_assoc($r2);

        $arr["first"] = long2ip($arr["first"]);
        $arr["last"]  = long2ip($arr["last"]);

        print("<tr>
                <td class='rowhead'>{$arr['added']}</td>
                <td class='rowhead' align='left'>{$arr['first']}</td>
                <td class='rowhead' align='left'>{$arr['last']}</td>
                <td class='rowhead' align='left'><a href='userdetails.php?id={$arr['addedby']}'>{$a2['username']}</a></td>
                <td class='rowhead' align='left'>".htmlentities($arr['comment'], ENT_QUOTES)."</td>
                <td class='rowhead'><a href='controlpanel.php?fileaction=1&amp;remove={$arr['id']}'>Remove</a></td>
            </tr>");
    }
    print("</table>");
}
//-- End Display Existing Bans --//

//-- Start Ban Form --//
print("<h2>Add Ban</h2>");
print("<form method='post' action='controlpanel.php?fileaction=1'>");
print("<table border='1' align='center' cellspacing='0' cellpadding='5'>");

print("<tr>
        <td class='colhead'><label for='first'>First IP</label></td>
        <td class='rowhead'>
            <input type='text' name='first' id='first' size='40' />
        </td>
    </tr>");

print("<tr>
        <td class='colhead'><label for='last'>Last IP</label></td>
        <td class='rowhead'>
            <input type='text' name='last' id='last' size='40' />
        </td>
    </tr>");

print("<tr>
        <td class='colhead'><label for='comment'>Comment</label></td>
        <td class='rowhead'>
            <input type='text' name='comment' id='comment' size='40' />
        </td>
    </tr>");

print("<tr>
        <td class='std' colspan='2' align='center'>
            <input type='submit' class='btn' value='Okay' />
        </td>
    </tr>");

print("</table>");
print("</form><br />");
//-- End Form --//

site_footer();

?>