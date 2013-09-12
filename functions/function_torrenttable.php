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

function linkcolor ($num)
{
    if (!$num)
    {
        return "red";
    }

    return "green";
}

function torrenttable ($res, $variant = "index")
{
    global $CURUSER, $image_dir, $added;

    $browse_res = sql_query("SELECT last_browse
                                FROM users
                                WHERE id='".$CURUSER['id']."'");

    $browse_arr = mysql_fetch_row($browse_res);

    $last_browse = $browse_arr[0];

    $time_now = gmtime();

    if ($last_browse > $time_now)
    {
      $last_browse = $time_now;
    }

    ?>
    <table border='1' align='center' cellspacing='0' cellpadding='5'>
        <tr>
            <td class='colhead' align='center'>Type</td>
            <td class='colhead' align='left'>Name</td>
            <td class='colhead' align='left'>DL</td>
            <td class='colhead' align='right'>Files</td>
            <td class='colhead' align='right'>Comm.</td>

            <!--<td class='colhead' align='center'>Rating</td>-->
            <!--<td class='colhead' align='center'>Added</td>-->

            <td class='colhead' align='center'>Size</td>

            <!--<td class='colhead' align='right'>Views</td>-->
            <!--<td class='colhead' align='right'>Hits</td>-->

            <td class='colhead' align='center'>Snatched</td>
            <td class='colhead' align='right'>Seeders</td>
            <td class='colhead' align='right'>Leechers</td>
    <?php

    if ($variant == "index")
    {
        print("<td class='colhead' align='center'>Upped&nbsp;by</td>\n");
    }

    print("</tr>\n");

    while ($row = mysql_fetch_assoc($res))
    {
        $id = $row["id"];

        if ($row["sticky"] == "yes")
        {
            echo("<tr class='sticky'>\n");
        }
        else
        {
            echo("<tr>\n");
        }

        print("<td class='rowhead' align='center' style='padding: 0px'>");

        if (isset($row["cat_name"]))
        {
            print("<a href='/browse.php?cat=".$row["category"]."'>");

            if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
            {
                print("<img src='{$image_dir}caticons/{$row['cat_pic']}' width='60' height='54' border='0' alt='{$row['cat_name']}' title='{$row['cat_name']}' />");
            }
            else
            {
                print($row["cat_name"]);
            }
            echo("</a>");
        }
        else
        {
            print("-");
        }

        print("</td>\n");

        $freeleech = ($row[freeleech]=="yes" ? "&nbsp;<img align='right' src='".$image_dir."free.png' width='32' height='15' border='0' alt='Free Torrent' title='Free Torrent' />" : "");
        $dispname = htmlspecialchars($row["name"]);
        $added    = sqlesc(get_date_time());

        print("<td class='rowhead' align='left'><a href='/details.php?");

        if ($variant == "mytorrents")
        {
            print("returnto=".urlencode($_SERVER["REQUEST_URI"])."&amp;");
        }

        print("id=$id");

        if ($variant == "index")
        {
            print("&amp;hit=1");
        }

        $sticky = ($row['sticky']=="yes" ? "<img align='right' src='".$image_dir."sticky.png' width='40' height='15' border='0' alt='Sticky' title='Sticky' />" : "");

        if (sql_timestamp_to_unix_timestamp($row["added"]) >= $last_browse)
        {
            print("'><span style='font-weight:bold;'>$dispname&nbsp;</span></a><img align='right' src='".$image_dir."new.png' width='30' height='15' border='0' alt='New' title='New' />&nbsp;&nbsp;$sticky$freeleech<br />".$row["added"]."</td>\n");
        }
        else
        {
            print("'><span style='font-weight:bold;'>$dispname&nbsp;&nbsp;$sticky$freeleech</span></a><br />".$row["added"]."</td>\n");
        }

        if ($CURUSER['downloadpos'] == 'no' || ($row['banned']=='yes'))
        {
            print("<td align='center'><span style='color:red;'>Download<br />Disabled</span></td>\n");
        }
        else
        {
            print("<td class='rowhead' align='center'><a href='/download.php/$id/".rawurlencode($row["filename"])."'><img src='".$image_dir."download.png' width='16' height='16' border='0' alt='Download' title='Download' /></a></td>\n");
        }

        if ($row["type"] == "single")
        {
            print("<td align='center'>".$row["numfiles"]."</td>\n");
        }
        else
        {
            if ($variant == "index")
            {
                print("<td class='rowhead' align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;filelist=1'>".$row["numfiles"]."</a></span></td>\n");
            }
            else
            {
                print("<td class='rowhead' align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;filelist=1#filelist'>".$row["numfiles"]."</a></span></td>\n");
            }
        }

        if (!$row["comments"])
        {
            print("<td class='rowhead' align='center'>".$row["comments"]."</td>\n");
        }
        else
        {
            if ($variant == "index")
            {
                print("<td class='rowhead' align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;tocomm=1'>".$row["comments"]."</a></span></td>\n");
            }
            else
            {
                print("<td class='rowhead' align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;page=0#startcomments'>".$row["comments"]."</a></span></td>\n");
            }
        }

    /*
        print("<td class='rowhead' align='center'>");
        if (!isset($row["rating"]))
            print("---");
        else {
            $rating = round($row["rating"] * 2) / 2;
            $rating = ratingpic($row["rating"]);
            if (!isset($rating))
                print("---");
            else
                print($rating);
        }
        print("</td>\n");

        print("<td class='rowhead' align='center'><table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>".str_replace(" ", "<br />")."</td></tr></table></td>\n");
    */

        print("<td class='rowhead' align='center'>".str_replace(" ", "<br />", mksize($row["size"]))."</td>\n");

        // print("<td class='rowhead' align='right'>".$row["views"]."</td>\n");
        // print("<td class='rowhead' align='right'>".$row["hits"]."</td>\n");

        $_s = "";

        if ($row["times_completed"] != 1)
        {
            $_s = "s";
        }

        print("<td class='rowhead' align='center'>".($row["times_completed"] > 0 ? "<a href='/snatches.php?id=$id'>".number_format($row["times_completed"])."<br />time$_s</a>" : "0 times")."</td>\n");

        if ($row["seeders"])
        {
            if ($variant == "index")
            {
                if ($row["leechers"])
                {
                    $ratio = $row["seeders"] / $row["leechers"];
                }
                else
                {
                    $ratio = 1;
                }

                print("<td class='rowhead' align='right'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;toseeders=1'><span style='color :".get_slr_color($ratio)."'>".$row["seeders"]."</span></a></span></td>\n");
            }
            else
            {
                print("<td class='rowhead' align='right'><span style='font-weight:bold;'><a class='".linkcolor($row["seeders"])."' href='details.php?id=$id&amp;dllist=1#seeders'>".$row["seeders"]."</a></span></td>\n");
            }
        }
        else
        {
            print("<td class='rowhead' align='right'><span class='".linkcolor($row["seeders"])."'>".$row["seeders"]."</span></td>\n");
        }

        if ($row["leechers"])
        {
            if ($variant == "index")
            {
                print("<td class='rowhead' align='right'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;todlers=1'>".number_format($row["leechers"]).($peerlink ? "</a>" : "")."</span></td>\n");
            }
            else
            {
                print("<td class='rowhead' align='right'><span style='font-weight:bold;'><a class='".linkcolor($row["leechers"])."' href='/details.php?id=$id&amp;dllist=1#leechers'>".$row["leechers"]."</a></span></td>\n");
            }
        }
        else
        {
            print("<td class='rowhead' align='right'>0</td>\n");
        }

        if ($variant == "index")
        {
            if ($row["anonymous"] == "yes")
            {
                print("<td align='center'><em>Anonymous</em></td>\n");
            }
            else
            {
                print("<td align='center'>".(isset($row["username"]) ? ("<a href='userdetails.php?id=".$row["owner"]."'><strong>".htmlspecialchars($row["username"])."</strong></a>") : "<em>(Unknown)</em>")."</td>\n");
            }
        }

        print("</tr>\n");
    }

    print("</table>\n");

    return $rows;
}