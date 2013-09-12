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

db_connect(false);
logged_in();

$action   = htmlspecialchars($_GET["action"]);
$pollid   = 0 + $_GET["pollid"];
$returnto = htmlentities($_POST["returnto"]);

if ($action == "delete")
{

    if (!is_valid_id($pollid))
    {
        error_message("error", "Error", "Invalid ID.");
    }

    $sure = (int) $_GET['sure'];

    if (!$sure)
    {
        error_message("warn", "Delete Poll", "<a href='controlpanel.php?fileaction=16&amp;action=delete&amp;pollid=$pollid&amp;returnto=$returnto&amp;sure=1'>Do you really want to Delete a Poll?  Click Here if you are sure?</a>");
    }

    sql_query("DELETE
                FROM pollanswers
                WHERE pollid = $pollid") or sqlerr();

    sql_query("DELETE
                FROM polls
                WHERE id = $pollid") or sqlerr();

    if ($returnto == "main")
    {
        display_message("info", "Info", "<div align='center'>Return to the <a href='index.php'>Main Page</a></div>");
    }
    else
    {
        error_message_center("info", "Info", "<strong>The Poll has been Deleted.</strong><br />
                                                <br />Return to <a href='controlpanel.php?fileaction=16'>View Polls Page</a>
                                                <br />Return to the <a href='index.php'>Main Page</a>");
    }
    die;
}

$rows = sql_query("SELECT COUNT(*)
                    FROM polls") or sqlerr();

$row       = mysql_fetch_row($rows);
$pollcount = $row[0];

if ($pollcount == 0)
{
    error_message("info", "Sorry...", "There are NO Polls!");
}

$polls = sql_query("SELECT *
                    FROM polls
                    ORDER BY id DESC
                    LIMIT 1,".($pollcount - 1)) or sqlerr();

print("<h1>Previous Polls</h1>");

function srt ($a, $b)
{
    if ($a[0] > $b[0])
    {
        return -1;
    }

    if ($a[0] < $b[0])
    {
        return 1;
    }

    return 0;
}

while ($poll = mysql_fetch_assoc($polls))
{
    $o = array($poll["option0"],
               $poll["option1"],
               $poll["option2"],
               $poll["option3"],
               $poll["option4"],
               $poll["option5"],
               $poll["option6"],
               $poll["option7"],
               $poll["option8"],
               $poll["option9"],
               $poll["option10"],
               $poll["option11"],
               $poll["option12"],
               $poll["option13"],
               $poll["option14"],
               $poll["option15"],
               $poll["option16"],
               $poll["option17"],
               $poll["option18"],
               $poll["option19"]);

    print("<table border='1' width='81%' cellspacing='0' cellpadding='10'><tr><td align='center'>\n");

    print("<p class='sub'>");

    $added = gmdate("Y-m-d", strtotime($poll['added']))." GMT (".(get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"])))."  ago)";

    print("$added");


        print(" - [<a href='controlpanel.php?fileaction=15&amp;action=edit&amp;pollid=$poll[id]'><span style='font-weight:bold;'>Edit</span></a>]");
        print(" - [<a href='controlpanel.php?fileaction=16&amp;action=delete&amp;pollid=$poll[id]'><span style='font-weight:bold;'>Delete</span></a>]");


    print("<a name='$poll[id]'>");
    print("</a></p>\n");
    print("<table class='main' border='1' cellspacing='0' cellpadding='5'><tr><td class='text'>");
    print("<p align='center'><span style='font-weight:bold;'>".$poll["question"]."</span></p>");

    $pollanswers = sql_query("SELECT selection
                                FROM pollanswers
                                WHERE pollid=".$poll["id"]." and  selection < 20") or sqlerr();

    $tvotes = mysql_num_rows($pollanswers);

    $vs = array(); //-- Count For Each Option ([0]..[19]) --//
    $os = array(); //-- Votes And Options: array(array(123, "Option 1"), array(45, "Option 2")) --//

    //-- Count Votes --//
    while ($pollanswer = mysql_fetch_row($pollanswers))

    {
        $vs[$pollanswer[0]] += 1;
    }

    reset($o);

    for ($i = 0;
         $i < count($o);
         ++$i)
    {
        if ($o[$i])
        {
            $os[$i] = array($vs[$i],
                            $o[$i]);
        }
    }

    //-- Now O's Is An Array Like This: --//
    if ($poll["sort"] == "yes")
    {
        usort($os, srt);
    }

    print("<table class='main' border='0' width='100%' cellspacing='0' cellpadding='0'>");

    $i = 0;

    while ($a = $os[$i])
    {
        if ($tvotes > 0)
        {
            $p = round($a[0] / $tvotes * 100);
        }
        else
        {
            $p = 0;
        }

        if ($i % 2)
        {
            $c = "";
        }
        else
        {
            $c = " bgcolor='#ECE9D8'";
        }

        print("<tr><td class='embedded'$c>".$a[1]."&nbsp;&nbsp;</td><td class='embedded'$c><img src='{$image_dir}bar_left.gif' width='2' height='9' border='0' alt='' title=''  /><img src='{$image_dir}bar.gif' width='.($p * 3).' height='9' border='0' alt='' title='' /><img src='{$image_dir}bar_right.gif' width='2' height='9' border='0' alt='' title='' /> $p%</td></tr>");
        ++$i;
    }

    print("</table>\n");

    $tvotes = number_format($tvotes);

    print("<p align='center'>Votes: $tvotes</p>\n");
    print("</td></tr></table>");
    print("</td></tr></table>");

}

//site_footer();

?>