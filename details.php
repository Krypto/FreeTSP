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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');
require_once(FUNC_DIR.'function_bbcode.php');
require_once(FUNC_DIR.'function_commenttable.php');

function ratingpic ($num)
{
    global $image_dir;

    $r = round($num * 2) / 2;

    if ($r < 1 || $r > 5)
    {
        return;
    }

    return "<img src='{$image_dir}ratings/{$r}.png' width='25' height='26' border='0' alt='rating: $num / 5' title='rating: $num /5' />";
}

function getagent ($httpagent = '', $peer_id = "")
{
    return ($httpagent ? $httpagent : ($peer_id ? $peer_id : Unknown));
}

function dltable ($name, $arr, $torrent)
{
    global $CURUSER, $moderator, $revived;

    $s = "<span style='font-weight:bold;'>".count($arr)." $name</span>\n";

    if (!count($arr))
    {
        return $s;
    }

    $s .= "\n";
    $s .= "<table class='main' border='1' width='100%' cellspacing='0' cellpadding='5'>\n";
    $s .= "<tr>
            <td class='colhead'>User/IP</td>
            <td class='colhead' align='center'>Connectable</td>
            <td class='colhead' align='right'>Uploaded</td>
            <td class='colhead' align='center'>Rate</td>
            <td class='colhead' align='right'>Downloaded</td>
            <td class='colhead' align='center'>Rate</td>
            <td class='colhead' align='center'>Ratio</td>
            <td class='colhead' align='right'>Complete</td>
            <td class='colhead' align='right'>Connected</td>
            <td class='colhead' align='center'>Idle</td>
            <td class='colhead' align='left'>Client</td>
        </tr>\n";

    $now       = time();
    $moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
    $mod       = get_user_class() >= UC_MODERATOR;

    foreach ($arr
             AS
             $e)
    {
        //-- User, IP, Port - Check If Anyone Has This IP --//
        ($unr = sql_query("SELECT id, class, username, privacy, donor, warned, enabled
                            FROM users
                            WHERE id = $e[userid]
                            ORDER BY last_access DESC
                            LIMIT 1")) or sqlerr(__FILE__, __LINE__);

        $una = mysql_fetch_assoc($unr);

        if ($una["privacy"] == "strong")
        {
            continue;
        }

        $highlight = $CURUSER["id"] == $una["id"] ? " class='sticky'" : "";
        $s         .= "<tr $highlight>\n";

        if ($una["username"])
        {
            if (get_user_class() >= UC_MODERATOR || $torrent['anonymous'] != 'yes' || $e['userid'] != $torrent['owner'])
            {
                $s .= "<td class='rowhead'>".format_username($una)."</td>\n";
            }
            elseif (get_user_class() >= UC_MODERATOR || $torrent['anonymous'] = 'yes')
            {
                $s .= "<td><i>Anonymous</i></td>\n";
            }
        }
        else
        {
            $s .= "<td class='rowhead'>".($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"]))."</td>\n";
        }

        $secs    = max(1, ($now - $e["st"]) - ($now - $e["la"]));
        $revived = $e["revived"] == "yes";

        $s .= "<td class='rowhead' align='center'>".($e[connectable] == "yes" ? "<span style='color : green;'>Yes</span>" : "<span style='color : #ff0000;'>No</span>")."</td>\n";

        $s .= "<td class='rowhead' align='right'>".mksize($e["uploaded"])."</td>\n";

        $s .= "<td class='rowhead' align='center'><span style=\"white-space: nowrap;\">".mksize(($e["uploaded"] - $e["uploadoffset"]) / $secs)."/s</span></td>\n";

        $s .= "<td class='rowhead' align='right'>".mksize($e["downloaded"])."</td>\n";

        if ($e["seeder"] == "no")
        {
            $s .= "<td class='rowhead' align='center'><span style=\"white-space: nowrap;\">".mksize(($e["downloaded"] - $e["downloadoffset"]) / $secs)."/s</span></td>\n";
        }
        else
        {
            $s .= "<td class='rowhead' align='center'><span style=\"white-space: nowrap;\">".mksize(($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e[st]))."/s</span></td>\n";
        }

        if ($e["downloaded"])
        {
            $ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
            $s .= "<td class='rowhead' align='right'><span style='color : ".get_ratio_color($ratio)."'>".number_format($ratio, 3)."</span></td>\n";
        }
        else {
            if ($e["uploaded"])
            {
                $s .= "<td class='rowhead' align='center'>Inf.</td>\n";
            }
            else
            {
                $s .= "<td class='rowhead' align='center'>---</td>\n";
            }
        }
        $s .= "<td class='rowhead' align='right'>".sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"])))."</td>\n";
        $s .= "<td class='rowhead' align='right'>".mkprettytime($now - $e["st"])."</td>\n";
        $s .= "<td class='rowhead' align='center'>".mkprettytime($now - $e["la"])."</td>\n";
        $s .= "<td class='rowhead' align='left'>".htmlspecialchars(getagent($e["agent"]))."</td>\n";
        $s .= "</tr>\n";
    }
    $s .= "</table>\n";
    return $s;
}

db_connect(false);
logged_in();

parked();

$id    = 0 + $_GET["id"];
$added = sqlesc(get_date_time());

if (!isset($id) || !$id)
{
    die();
}

$res = sql_query("SELECT torrents.seeders, torrents.freeleech, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, LENGTH(torrents.nfo) AS nfosz, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < $min_votes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.comments, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, torrents.anonymous,torrents.poster,categories.name AS cat_name, users.username
                FROM torrents
                LEFT JOIN categories ON torrents.category = categories.id
                LEFT JOIN users ON torrents.owner = users.id
                WHERE torrents.id = $id") or sqlerr();

$row = mysql_fetch_assoc($res);

$owned = $moderator = 0;

if (get_user_class() >= UC_MODERATOR)
{
    $owned = $moderator = 1;
}

elseif ($CURUSER["id"] == $row["owner"])
{
    $owned = 1;
}

if (!$row || ($row["banned"] == "yes" && !$moderator))
{
    error_message("error", "Error", "No torrent with ID.");
}
else
{
    if ($_GET["hit"])
    {
        sql_query("UPDATE torrents
                    SET views = views + 1
                    WHERE id = $id");

        if ($_GET["tocomm"])
        {
            header("Location: $site_url/details.php?id=$id&page=0#startcomments");
        }

        elseif ($_GET["filelist"])
        {
            header("Location: $site_url/details.php?id=$id&filelist=1#filelist");
        }

        elseif ($_GET["toseeders"])
        {
            header("Location: $site_url/details.php?id=$id&dllist=1#seeders");
        }

        elseif ($_GET["todlers"])
        {
            header("Location: $site_url/details.php?id=$id&dllist=1#leechers");
        }

        else
        {
            header("Location: $site_url/details.php?id=$id");
        }
        exit();
    }

    if (!isset($_GET["page"]))
    {
        site_header("Details for torrent '".$row["name"]."'", false);

        if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
        {
            $owned = 1;
        }
        else
        {
            $owned = 0;
        }

        $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        if ($_GET["uploaded"])
        {
            echo display_message("success", "Successfully Uploaded!", "Please Wait - Your torrent will Download Automatically.  Note: that the torrent will NOT be Visible until you Start Seeding!");
            echo("<meta http-equiv='refresh' content='1;url=download.php/$id/".rawurlencode($row["filename"])."'/>");
        }
        elseif ($_GET["edited"])
        {
            echo display_message("success", " ", "Successfully Edited!");
            if (isset($_GET["returnto"]))
            {
                print("<p><span style='font-weight:bold;'>Return<a href='".htmlspecialchars("{$site_url}/{$_GET['returnto']}")."'></a></span></p>\n");
            }
        }
        elseif (isset($_GET["searched"]))
        {
            print("<h2>Your Search for '".htmlspecialchars($_GET["searched"])."' gave a Single Result:</h2>\n");
        }
        elseif ($_GET["rated"])

            //$returnto = htmlspecialchars($_SERVER["HTTP_REFERER"]);

            //if ($returnto)
        {
           $redirectid = $row["id"];
            echo error_message("success", "Success", "Rating Added! <a href='/details.php?id=$redirectid'>Click here</a> to go back to the torrent");
        
        }

        $s = $row["name"];

        print("<h1>$s</h1>\n");

        $url = "edit.php?id=".$row["id"];

        if (isset($_GET["returnto"]))
        {
            $addthis = "&amp;returnto=".urlencode($_GET["returnto"]);
            $url     .= $addthis;
            $keepget .= $addthis;
        }

        $editlink = "a href='$url' class='btn'";

        if ($CURUSER['downloadpos'] == 'no')
        {
            print("<span style='color:red;'><strong>Your Download Rights have been Removed.<br />You need to contact a member of Staff to resolve this situation!!</strong></span><br/><br />");
        }
        else
        {
            print("<p align='center'>
                   <a class='main' href='download.php/$id/".rawurlencode($row["filename"])."'>
                   <img src='".$image_dir."download1.png' width='184' height='55' border='0' alt='Download' title='Download' />
                   </a></p>");
        }

        print("<table border='1' width='100%' cellspacing='0' cellpadding='5'>\n");

        function hex_esc ($matches)
        {
            return sprintf("%02x", ord($matches[0]));
        }

        echo("<tr>
                <td class='detail' width='20%'>Info Hash</td>
                <td class='rowhead'>".preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"]))."</td>
            </tr>");

        $downl = ($CURUSER["downloaded"] + $row["size"]);
        $sr    = $CURUSER["uploaded"] / $downl;

        switch (true)
        {
            case ($sr >= 4):
                $s = "yawn";
                break;
            case ($sr >= 2):
                $s = "yawn";
                break;
            case ($sr >= 1):
                $s = "yawn";
                break;
            case ($sr >= 0.5):
                $s = "yawn";
                break;
            case ($sr >= 0.25):
                $s = "yawn";
                break;
                case ($sr > 0.00):
                $s = "yawn";
                break;
            default;
                $s = "yawn";
                break;
        }

        $sr = floor($sr * 1000) / 1000;
        $sr = "<font color='".get_ratio_color($sr)."'>".number_format($sr, 3).
              "</font>&nbsp;&nbsp;<img src='".$image_dir."smilies/{$s}.png' width='16' height='16' alt='$s' title='$s' />";

        echo("<tr>
                <td class='detail'>Ratio After Download</td>
                <td class='rowhead'>".$sr."&nbsp;&nbsp;Your new Ratio if you Download this torrent.</td>
            </tr>");

        if (!empty($row["poster"]))
        {
            echo("<tr>
                    <td class='detail'>Poster</td>
                    <td class='rowhead'><a href='".$row["poster"]."' rel='lightbox'><img src='".$row["poster"]."' width='' height='' border='0' align='left' alt='Posted Image' title='Posted Image' /></a></td>
                </tr>");
        }
        else
        {
            echo("<tr>
                    <td class='detail'>Poster</td>
                    <td class='rowhead'><a href='".$image_dir."poster.png' rel='lightbox'><img src='".$image_dir."poster.png' width='' height='' border='0' align='left' alt='Posted Image' title='Posted Image' /></a></td>
                </tr>");
        }

        if (!empty($row["descr"]))
        {
            echo("<tr>
                    <td class='detail'>Description</td>
                    <td class='rowhead'>".str_replace(array("\n",
                                                            "  "), array("\n",
                                                                         "&nbsp; "), format_comment(htmlspecialchars($row["descr"])))."</td>
                </tr>");
        }

        if (get_user_class() >= UC_POWER_USER && $row["nfosz"] > 0)
        {
            echo("<tr>
                    <td class='detail'>NFO</td>
                    <td class='rowhead'>
                        <a href='viewnfo.php?id=$row[id]'><span style='font-weight:bold;'>View NFO</span></a>
                        (".mksize($row["nfosz"]).")
                    </td>
                </tr>");
        }

        if ($row["visible"] == "no")
        {
            echo("<tr>
                    <td class='detail'>Visible</td>
                    <td class='rowhead'><span style='font-weight:bold;'>No</span> (Dead)</td>
                </tr>");
        }

        if ($moderator)
        {
            echo("<tr>
                    <td class='detail'>Banned</td>
                    <td class='rowhead'>".$row["banned"]."</td>
                </tr>");
        }

        if ($row['freeleech'] == 'yes')
        {
            echo("<tr>
                    <td class='detail'>Freeleech</td>
                    <td class='rowhead'>".$row["freeleech"]."</td>
                </tr>");
        }

        if (isset($row["cat_name"]))
        {
            echo("<tr>
                    <td class='detail'>Type</td>
                    <td class='rowhead'>".$row["cat_name"]."</td>
                </tr>");
        }
        else
        {
            echo("<tr>
                    <td class='detail'>Type</td>
                    <td class='rowhead'>(None Selected)</td>
                </tr>");
        }

        echo("<tr>
                <td class='detail'>Last&nbsp;Seeder</td>
                <td class='rowhead'>Last Activity ".mkprettytime($row["lastseed"])." ago</td>
            </tr>");

        echo("<tr>
                <td class='detail'>Size</td>
                <td class='rowhead'>".mksize($row["size"])." (".number_format($row["size"])." bytes)</td>
            </tr>");

        $s = "";
        $s .= "<table border='0' cellpadding='0' cellspacing='0'><tr><td valign='top' class='embedded'>";

        if (!isset($row["rating"]))
        {
            if ($min_votes > 1)
            {
                $s .= "None Yet (needs at least $min_votes Votes and has received ";

                if ($row["numratings"])
                {
                    $s .= "Only ".$row["numratings"];
                }
                else
                {
                    $s .= "None";
                }
                $s .= ")";
            }
            else
            {
                $s .= "No Votes Yet";
            }
        }
        else
        {
            $rpic = ratingpic($row["rating"]);

            if (!isset($rpic))
            {
                $s .= "Invalid?";
            }
            else
            {
                $s .= "$rpic (".$row["rating"]." out of 5 with ".$row["numratings"]." Vote(s) total)";
            }
        }
        $s .= "\n";
        $s .= "</td><td class='embedded'>$spacer</td><td valign='top' class='embedded'>";

        if (!isset($CURUSER))
        {
            $s .= "(<a href='login.php?returnto=".urlencode(substr($_SERVER["REQUEST_URI"], 1))."&amp;nowarn=1'>Log in</a> to Rate this torrent.)";
        }
        else
        {
            $ratings = array(5 => "Great",
                             4 => "Pretty Good",
                             3 => "Decent",
                             2 => "Pretty Bad",
                             1 => "Terrible",);

            if (!$owned || $moderator)
            {
                $xres = sql_query("SELECT rating, added
                                    FROM ratings
                                    WHERE torrent = $id
                                    AND user = ".$CURUSER["id"]);

                $xrow = mysql_fetch_assoc($xres);

                if ($xrow)
                {
                    $s .= "(you Rated this torrent as '".$xrow["rating"]." - ".$ratings[$xrow["rating"]]."')";
                }
                else
                {
                    $s .= "<form method='post' action='takerate.php'><input type='hidden' name='id' value='$id' />\n";
                    $s .= "<select name='rating'>\n";
                    $s .= "<option value='0'>(Add Rating)</option>\n";

                    foreach ($ratings
                             AS
                             $k => $v)
                    {
                        $s .= "<option value='$k'>$k - $v</option>\n";
                    }

                    $s .= "</select>\n";
                    $s .= "<input type='submit' class='btn' value='Vote!' />";
                    $s .= "</form>\n";
                }
            }
        }
        $s .= "</td></tr></table>";

        echo("<tr>
                <td class='detail'>Rating</td>
                <td class='rowhead'>".$s."</td>
            </tr>");

        echo("<tr>
                <td class='detail'>Added</td>
                <td class='rowhead'>".$row["added"]."</td>
            </tr>");

        echo("<tr>
                <td class='detail'>Views</td>
                <td class='rowhead'>".$row["views"]."</td>
            </tr>");

        echo("<tr>
                <td class='detail'>Hits</td>
                <td class='rowhead'>".$row["hits"]."</td>
            </tr>");


        echo("<tr>
                <td class='detail'>Snatched</td>
                <td class='rowhead'>".($row["times_completed"] > 0 ? "<a href='snatches.php?id=$id'>$row[times_completed] time(s)</a>" : "0 times")."</td>
            </tr>");

        $keepget = "";

        if ($row['anonymous'] == 'yes')
        {
            if (get_user_class() < UC_UPLOADER)
                $uprow = "<em>Anonymous</em>";
            else
                $uprow = "<em>Anonymous</em> (<a href='userdetails.php?id=$row[owner]'><strong>$row[username]</strong></a>)";
        }
        else
        {
            $uprow = (isset($row["username"]) ? ("<a href='userdetails.php?id=".$row["owner"]."'><strong>".htmlspecialchars($row["username"])."</strong></a>") : "<em>(Unknown)</em>");
        }

        if ($owned)
        {
            $uprow .= " $spacer<$editlink>Edit this Torrent</a>";
        }

        echo("<tr>
                <td class='detail'>Upped by</td>
                <td class='rowhead'>".$uprow."</td>
            </tr>");

        if ($row["type"] == "multi")
        {
            if (!$_GET["filelist"])
            {
                echo("<tr>
                        <td class='detail'>Num Files<br /><a href='details.php?id=$id&amp;filelist=1$keepget#filelist' class='sublink'>[See Full List]</a></td>
                        <td class='rowhead'>".$row["numfiles"]." files</td>
                    </tr>");
            }
            else
            {
                echo("<tr>
                        <td class='detail'>Num Files</td>
                        <td class='rowhead'>".$row["numfiles"]." files</td>
                    </tr>");

                $s = "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n";

                $subres = sql_query("SELECT *
                                        FROM files
                                        WHERE torrent = $id
                                        ORDER BY id");

                $s .= "<tr><td class='colhead'>Path</td><td class='colhead' align='right'>Size</td></tr>\n";

                while ($subrow = mysql_fetch_assoc($subres))
                {
                    $s .= "<tr><td class='detail'>".$subrow["filename"]."</td><td class='rowhead' align='right'>".mksize($subrow["size"])."</td></tr>\n";
                }

                $s .= "</table>\n";

                echo("<tr>
                        <td class='detail'><a name='filelist'>File List</a><br /><a href='details.php?id=$id$keepget' class='sublink'>[Hide List]</a></td>
                        <td class='rowhead'>".$s."</td>
                    </tr>");
            }
        }

        if (!$_GET["dllist"])
        {
            echo("<tr>
                    <td class='detail'>Peers<br /><a href='details.php?id=$id&amp;dllist=1$keepget#seeders' class='sublink'>[See Full list]</a></td>
                    <td class='rowhead'>".$row["seeders"]." seeder(s), ".$row["leechers"]." leecher(s) = ".($row["seeders"] + $row["leechers"])." peer(s) total</td>
                </tr>");
        }
        else
        {
            $downloaders = array();
            $seeders     = array();

            $subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, UNIX_TIMESTAMP(last_action) AS la, userid
                                FROM peers
                                WHERE torrent = $id") or sqlerr();

            while ($subrow = mysql_fetch_assoc($subres))
            {
                if ($subrow["seeder"] == "yes")
                {
                    $seeders[] = $subrow;
                }
                else
                {
                    $downloaders[] = $subrow;
                }
            }

            function leech_sort ($a, $b)
            {
                if (isset($_GET["usort"]))
                {
                    return seed_sort($a, $b);
                }

                $x = $a["to_go"];
                $y = $b["to_go"];

                if ($x == $y)
                {
                    return 0;
                }

                if ($x < $y)
                {
                    return -1;
                }

                return 1;
            }

            function seed_sort ($a, $b)
            {
                $x = $a["uploaded"];
                $y = $b["uploaded"];

                if ($x == $y)
                {
                    return 0;
                }

                if ($x < $y)
                {
                    return 1;
                }

                return -1;
            }

            usort($seeders, "seed_sort");
            usort($downloaders, "leech_sort");

            echo("<tr>
                    <td class='detail'><a name='seeders'>Seeders</a><br /><a href='details.php?id=$id$keepget' class='sublink'>[Hide List]</a></td>
                    <td class='rowhead'>".dltable("Seeder(s)", $seeders, $row)."</td>
                </tr>");


            echo("<tr>
                    <td class='detail'><a name='leechers'>Leechers</a><br /><a href='details.php?id=$id$keepget' class='sublink'>[Hide List]</a></td>
                    <td class='rowhead'>".dltable("Leecher(s)", $downloaders, $row)."</td>
                </tr>");
        }

        $rt = sql_query("SELECT th.userid,u.username,u.class, u.donor, u.warned, u.id, u.enabled
                            FROM thanks AS th INNER JOIN users AS u ON u.id=th.userid
                            WHERE th.torrentid=".$id." ORDER BY u.class DESC") or sqlerr();
        $ids = array();

        if (mysql_num_rows($rt) > 0)
        {
            $list = "";
            $i    = 0;

            while ($ar = mysql_fetch_assoc($rt))
            {
                $ids[] = $ar["userid"];
                $list .= "".format_username($ar)."".((mysql_num_rows($rt)-1) == $i ? "" : "")."";
                ++$i;
            }

            echo("<tr>
                    <td class='detail' width='20%'>Thanks List</td>
                    <td class='rowhead' width='80%'>$list</td>
                </tr>");
        }
        else
        {
            $list ="&nbsp;None yet";

            if ($CURUSER["id"] != $row["owner"] && !in_array($CURUSER["id"],$ids))
            {
                echo("<tr><td class='detail'>Thanks List</td><td class='rowhead'>$list<form action=\"thanks.php\" method=\"post\">
                    <input type='submit' name='submit' class='btn' value='Thanks!' />
                    <input type='hidden' name='torrentid' value='$id' />
                    </form></td></tr>");
            }
        }

        if ($row["owner"] != $CURUSER["id"])
        {
            echo("<tr>
                    <td class='detail'>Report</td>
                    <td class='rowhead'>
                        <form method='post' action='report.php?type=Torrent&amp;id=$id'>
                            <input type='submit' class='btn' name='submit' value='Report This Torrent' />
                            &nbsp;&nbsp;For Breaking the <a href='rules.php'>Rules</a>
                        </form>
                    </td>
            </tr>");
        }

        print("</table>\n");
    }
    else
    {
        site_header("Comments for Torrent '".$row["name"]."'");

        print("<h1>Comments for <a href='details.php?id=$id'>".$row["name"]."</a></h1>\n");
    }

    print("<p><a name='startcomments'></a></p>\n");

    if ($CURUSER['torrcompos'] == 'no')
    {
        $commentbar = "<p align='center'><a class='btn'>Comment - Disabled</a></p>\n";
    }
    else
    {
        $commentbar = "<p align='center'><a class='btn' href='comment.php?action=add&amp;tid=$id'>Add a Comment</a></p>\n";
    }

    $count = $row['comments'];

    if (!$count)
    {
        echo display_message("info", " ", "No Comments Yet");
    }
    else
    {
        list($pagertop, $pagerbottom, $limit) = pager(5, $count, "details.php?id=$id&amp;", array(lastpagedefault => 1));

        $subres = sql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, username, title, class, donor, enabled
                                FROM comments
                                LEFT JOIN users ON comments.user = users.id
                                WHERE torrent = $id
                                ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);

        $allrows = array();

        while ($subrow = mysql_fetch_assoc($subres))
        {
            $allrows[] = $subrow;
        }

        print($commentbar);
        print($pagertop);

        commenttable($allrows);

        print($pagerbottom);
    }

    print($commentbar);
}

site_footer();

?>
