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

db_connect();
logged_in();

if (isset($_POST["nowarned"]) && ($_POST["nowarned"] == "nowarned"))
{
    if (empty($_POST["usernw"]) && empty($_POST["desact"]) && empty($_POST["delete"]))
    {
        error_message_center("error", "Error", "You Must Tick One Of The Boxes.");
    }

    if (!empty($_POST["usernw"]))
    {
        $msg    = sqlesc("Your Warning Has Been Removed By: ".$CURUSER['username'].".");
        $added  = sqlesc(get_date_time());
        $userid = implode(", ", $_POST[usernw]);

    /*
    sql_query("INSERT INTO messages (sender, receiver, msg, added)
                 VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
    */

        $warn = sql_query("SELECT username
                                FROM users
                                WHERE id IN (".implode(", ", $_POST[usernw]).")") or sqlerr(__FILE__, __LINE__);

        $user     = mysql_fetch_array($warn);
        $username = $user["username"];

        write_stafflog ("<strong><a href='userdetails.php?id=$userid'>$username .</a></strong> -- Warning was Removed -- via Warned Members Panel -- by -- $CURUSER[username].");

        $do  = "UPDATE users
                SET warned='no', warneduntil='0000-00-00 00:00:00'
                WHERE id IN (".implode(", ", $_POST[usernw]).")";

        $res = sql_query($do);
    }

    if (!empty($_POST["desact"]))
    {
        $userid   = implode(", ", $_POST[desact]);
        $disable  = sql_query("SELECT username
                                FROM users
                                WHERE id IN (".implode(", ", $_POST[desact]).")") or sqlerr(__FILE__, __LINE__);

        $user     = mysql_fetch_array($disable);
        $username = $user["username"];

        write_stafflog("<strong><a href='userdetails.php?id=$userid'>$username .</a></strong> -- Account was Disabled -- via Warned Members Panel -- by -- $CURUSER[username].");

        $do  = "UPDATE users
                SET enabled='no'
                WHERE id IN (".implode(", ", $_POST['desact']).")";

        $res = sql_query($do);
    }
}

site_header("Warned Members", false);

$warned = number_format(get_row_count("users", "WHERE warned='yes' AND enabled='yes'"));

list($pagertop, $pagerbottom, $limit) = pager(25, $warned, "warned.php?");

$res = sql_query("SELECT id, username, uploaded, downloaded, added, last_access, class, donor, warned, enabled
                    FROM users
                    WHERE warned='yes'
                    AND enabled='yes'
                    ORDER BY username $limit ") or sqlerr();

$num = mysql_num_rows($res);

$res = sql_query("SELECT id, username, uploaded, downloaded, added, last_access, class, donor, warned, warneduntil
                    FROM users
                    WHERE warned=1
                    AND enabled='yes'
                    ORDER BY (users.uploaded/users.downloaded)") or sqlerr(__FILE__, __LINE__);

$num = mysql_num_rows($res);

print("<h1>Warned Accounts: (<span style='color:#FF3030'>$warned</span>)</h1>");

print($pagertop);

print("<form action='controlpanel.php?fileaction=20' method='post'><table border='1' width='81%' cellspacing='0' cellpadding='2'>");

print("<tr align='center'>
        <td class='colhead' width='250'>User Name</td>
        <td class='colhead' width='70'>Registered</td>
        <td class='colhead' width='75'>Last access</td>
        <td class='colhead' width='75'>User Class</td>
        <td class='colhead' width='70'>Downloaded</td>
        <td class='colhead' width='70'>UpLoaded</td>
        <td class='colhead' width='45'>Ratio</td>
        <td class='colhead' width='125'>End<br/>Of Warning</td>
        <td class='colhead' width='65'>Remove<br/>Warning</td>
        <td class='colhead' width='65'>Disable<br/>Account</td>
    </tr>");

for ($i = 1;
     $i <= $num;
     $i++)
{
    $arr = mysql_fetch_assoc($res);

    if ($arr['added'] == '0000-00-00 00:00:00')
    {
        $arr['added'] = '-';
    }

    if ($arr['last_access'] == '0000-00-00 00:00:00')
    {
        $arr['last_access'] = '-';
    }

    if ($arr["downloaded"] != 0)
    {
        $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
    }
    else
    {
        $ratio       = "---";
        $ratio       = "<font color='".get_ratio_color($ratio)."'>$ratio</font>";
        $uploaded    = mksize($arr["uploaded"]);
        $downloaded  = mksize($arr["downloaded"]);
        $added       = substr($arr['added'],0,10);
        $last_access = substr($arr['last_access'],0,10);
        $class       = get_user_class_name($arr["class"]);
    }

    print("<tr>
            <td align='center'>".format_username($arr)."</td>
            <td class='rowhead' align='center'>$added</td>
            <td class='rowhead' align='center'>$last_access</td>
            <td class='rowhead' align='center'>$class</td>
            <td class='rowhead' align='center'>$downloaded</td>
            <td class='rowhead' align='center'>$uploaded</td>
            <td class='rowhead' align='center'>$ratio</td>
            <td class='rowhead' align='center'>$arr[warneduntil]</td>
            <td class='rowhead' align='center' bgcolor='#008000'>
                <input type='checkbox' name='usernw[]' value='$arr[id]' /></td>
            <td class='rowhead' align='center' bgcolor='#FF3030'>
                <input type='checkbox' name='desact[]' value='$arr[id]' /></td>
        </tr>");
}

    print("<tr>
            <td align='center' colspan='10'>
                <input type='submit' name='submit' value='Apply Changes' />
                <input type='hidden' name='nowarned' value='nowarned' />
            </td>
        </tr>");

    print("</table></form>");

print($pagerbottom);

site_footer();

?>