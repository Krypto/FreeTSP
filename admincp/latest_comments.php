<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** https://github.com/Krypto/FreeTSP
** http://www.freetsp.info
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

site_header("Latest Comments",false);

$limit = 25;

if (isset($_GET["amount"]) && (int)$_GET["amount"])
{
    if (intval($_GET["amount"]) != $_GET["amount"])
    {
        error_message_center("error", "Error", "Amount wasn't an Integer.");
    }

    $limit = 0 + $_GET["amount"];

    if ($limit > 999)
    {
        $limit = 1000;
    }

    if ($limit < 10)
    {
        $limit = 10;
    }

}

$subres = sql_query("SELECT comments.id, torrent, text, user, comments.added , editedby, editedat, avatar, warned, "."username, title, class
                        FROM comments LEFT JOIN users ON comments.user = users.id "."
                        ORDER BY comments.id
                        DESC limit 0,".$limit) or sqlerr(__FILE__, __LINE__);

$allrows = array();

while ($subrow = mysql_fetch_assoc($subres))

    $allrows[] = $subrow;

    print("<h2>Showing the Latest&nbsp;{$limit}&nbsp;Comments.</h2>");
    print("<table class='main' width='100%'><tr>");

function commenttable_new($rows)
{
    global $CURUSER, $image_dir;

    foreach ($rows
             AS
             $row)
    {
        $subres = sql_query("SELECT name
                                FROM torrents
                                WHERE id=".sqlesc($row["torrent"])) or sqlerr(__FILE__, __LINE__);

        $subrow = mysql_fetch_assoc($subres);

        print("<td align='center' colspan='2'>");
        print("Torrent Name:&nbsp;<span style='font-weight:bold;'>
                <a href='details.php?id=".htmlspecialchars($row["torrent"])."'>".htmlspecialchars($subrow["name"])."</a>
            </span><br />");

        print("Comment #".$row["id"]." by ");

        if (isset($row["username"]))
        {
            $title = $row["title"];

            if ($title == "")
            {
                $title = get_user_class_name($row["class"]);
            }
            else
            {
                $title = htmlspecialchars($title);
            }

            print("<a name='comm".$row["id"]."' href='userdetails.php?id=".$row["user"]."'>
                   <span style='font-weight:bold;'>" .htmlspecialchars($row["username"])."</span>
                   </a>".($row["donor"] == "yes" ? "<img src='{$image_dir}star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />" : "") . ($row["warned"] == "yes" ? "<img src=".
                   "'{$image_dir}warned.png' width='16' height='16' border='0' alt='Warned' title='Warned'>" : "")." ($title)");
        }
        else
        {
            print("(Not A Member Anymore - <span style='font-style: italic;'>orphaned )</span>");
        }

        print(" at ".$row["added"]." GMT" .
             ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=edit&amp;cid=$row[id]'>Edit</a>" : "") .
             (get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=delete&amp;cid=$row[id]'>Delete</a>" : "") .
             ($row["editedby"] && get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=vieworiginal&amp;cid=$row[id]'>View Original</a>" : "")."");
        print("</td></tr>");

        $text = format_comment($row["text"]);

        if ($row["editedby"])
        {
            $text .= "<p><span style='font-size: x-small; '>Last edited by <a href='/userdetails.php?id=$row[editedby]'><span style='font-weight:bold;'>$row[username]</span></a> at $row[editedat] GMT</span></p>";
        }
            print("<tr valign='top'>");

        if (!empty($row["avatar"]))
        {
            print("<td align='center' width='150' height='150' style='padding: 0px'><img src='".htmlspecialchars($row["avatar"])."'  width='125' height='125' alt='' title='' /></td>\n");
        }
        else
        {
            print("<td align='center' width='150' height='150' style='padding: 0px'><img src='".$image_dir."default_avatar.gif'  width='125' height='125' alt='' title='' /></td>\n");
        }
        print("<td class='text'>$text</td>");
        print("</tr>");
    }
}

commenttable_new($allrows);

print("</table><br />");

site_footer();

?>