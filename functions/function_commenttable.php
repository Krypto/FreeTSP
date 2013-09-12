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

function commenttable ($rows)
{
    global $CURUSER, $image_dir;

    begin_frame();

    //$count = 0;

    foreach ($rows
             AS
             $row)
    {
        print("<p class='sub'>#".$row["id"]." by ");

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

            print("<a name='comm".$row["id"]."' href='userdetails.php?id=".$row["user"]."'><span style='font-weight:bold;'>".htmlspecialchars($row["username"])."</span></a>".($row["donor"] == "yes" ? "<img src='{$image_dir}star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />" : "").($row["warned"] == "yes" ? "<img src="."'{$image_dir}warned.png' width='16' height='16' border='0' alt='Warned' title='Warned' />" : "")." ($title)\n");
        }
        else
        {
            print("<a name='comm".$row["id"]."'><span style='font-style: italic;'>(Orphaned)</span></a>\n");
        }

        if ( $CURUSER['torrcompos'] == 'no' )
        {
            if ($row["user"] == $CURUSER["id"])
            {
                print(" at ".$row["added"]." GMT&nbsp;&nbsp;<a class='btn'>Edit Disabled</a> ");
            }
        }
        else
        {
            print(" at ".$row["added"]." GMT&nbsp;&nbsp;".($row["user"] <> $CURUSER["id"] ? "<a class='btn' href='report.php?type=Comment&amp;id=$row[id]'>Report Comment</a>" : "").($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=edit&amp;cid=$row[id]'>Edit</a>" : "").(get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=delete&amp;cid=$row[id]'>Delete</a>" : "").($row["editedby"] && get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=vieworiginal&amp;cid=$row[id]'>View Original</a>" : "")."</p>\n");
        }

        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");

        if (!$avatar)
        {
            $avatar = "{$image_dir}default_avatar.gif";
        }

        $text = format_comment($row["text"]);

        if ($row["editedby"])
        {
            $text .= "<p><span style='font-size: x-small; '>Last edited by <a href='/userdetails.php?id=$row[editedby]'><span style='font-weight:bold;'>$row[username]</span></a> at $row[editedat] GMT</span></p>\n";
        }

        begin_table(true);

        print("<tr valign='top'>\n");
        print("<td align='center' width='125'><img src='{$avatar}' width='125' height='125' border='0' alt='' title='' /></td>\n");
        print("<td class='text'>$text</td>\n");
        print("</tr>\n");

        end_table();
    }

    end_frame();

}

?>