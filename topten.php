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

db_connect();
logged_in();

if (get_user_class() < UC_POWER_USER)
{
    error_message("warn", "Warning", "Permission Denied");
}

function usertable ($res, $frame_caption)
{
    global $CURUSER;

    begin_frame($frame_caption, true);
    begin_table();
    ?>
<tr>
    <td class='colhead'>Rank</td>
    <td class='colhead' align='left'>User</td>
    <td class='colhead'>Uploaded</td>
    <td class='colhead' align='left'>UL Speed</td>
    <td class='colhead'>Downloaded</td>
    <td class='colhead' align='left'>DL Speed</td>
    <td class='colhead' align='right'>Ratio</td>
    <td class='colhead' align='left'>Joined</td>
</tr>

<?php
    $num = 0;

    while ($a = mysql_fetch_assoc($res))
    {
        ++$num;
        $highlight = $CURUSER["id"] == $a["userid"] ? " bgcolor='#BBAF9B'" : "";

        if ($a["downloaded"])
        {
            $ratio = $a["uploaded"] / $a["downloaded"];
            $color = get_ratio_color($ratio);
            $ratio = number_format($ratio, 2);

            if ($color)
            {
                $ratio = "<span style='color : $color'>$ratio</span>";
            }
        }
        else
        {
            $ratio = "Inf.";
        }
        echo("<tr $highlight>
                <td class='rowhead' align='center'>$num</td>
                <td class='rowhead' align='left' $highlight>
                    <a href='userdetails.php?id=".$a["userid"]."'><span style='font-weight:bold;'>".$a["username"]."</span></a>"."
                </td>
                <td class='rowhead' align='right'$highlight>".mksize($a["uploaded"])."</td>
                <td class='rowhead' align='right' $highlight>".mksize($a["upspeed"])."/s"."</td>
                <td class='rowhead' align='right '$highlight>".mksize($a["downloaded"])."</td>
                <td class='rowhead' align='right '$highlight>".mksize($a["downspeed"])."/s"."</td>
                <td class='rowhead' align='right' $highlight>".$ratio."</td>
                <td class='rowhead' align='left'>".gmdate("Y-m-d", strtotime($a['added']))." (".get_elapsed_time(sql_timestamp_to_unix_timestamp($a['added']))." ago)</td>
            </tr>");
    }
    end_table();
    end_frame();
}

function _torrenttable ($res, $frame_caption)
{
    begin_frame($frame_caption, true);
    begin_table();
    ?>
<tr>
    <td class='colhead' align='center'>Rank</td>
    <td class='colhead' align='left'>Name</td>
    <td class='colhead' align='right'>Sna.</td>
    <td class='colhead' align='right'>Data</td>
    <td class='colhead' align='right'>Se.</td>
    <td class='colhead' align='right'>Le.</td>
    <td class='colhead' align='right'>To.</td>
    <td class='colhead' align='right'>Ratio</td>
</tr>
<?php
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
        ++$num;
        if ($a["leechers"])
        {
            $r     = $a["seeders"] / $a["leechers"];
            $ratio = "<span style='color : '".get_ratio_color($r)."'>".number_format($r, 2)."</span>";
        }
        else
        {
            $ratio = "Inf.";
        }
        echo("<tr>
                <td class='rowhead' align='center'>$num</td>
                <td class='rowhead' align='left'>
                    <a href='details.php?id=".$a["id"]."&hit=1'><span style='font-weight:bold;'>".$a["name"]."</span></a>
                </td>
                <td class='rowhead' align='right'>".number_format($a["times_completed"])."</td>
                <td class='rowhead' align='right'>".mksize($a["data"])."</td>
                <td align='right'>".number_format($a["seeders"])."</td>
                <td class='rowhead' align='right'>".number_format($a["leechers"])."</td>
                <td align='right'>".($a["leechers"] + $a["seeders"])."</td>
                <td class='rowhead' align='right'>$ratio</td>
            </tr>\n");
    }
    end_table();
    end_frame();
}

function countriestable ($res, $frame_caption, $what)
{
    global $image_dir;

    begin_frame($frame_caption, true);
    begin_table();
    ?>
<tr>
    <td class='colhead'>Rank</td>
    <td class='colhead' align='left'>Country</td>
    <td class='colhead' align='right'><?php echo $what?></td>
</tr>
<?php
    $num = 0;
    while ($a = mysql_fetch_assoc($res))
    {
        ++$num;

        if ($what == "Users")
        {
            $value = number_format($a["num"]);
        }

        elseif ($what == "Uploaded")
        {
            $value = mksize($a["ul"]);
        }

        elseif ($what == "Average")
        {
            $value = mksize($a["ul_avg"]);
        }

        elseif ($what == "Ratio")
        {
            $value = number_format($a["r"], 2);
        }

        echo("<tr>
            <td class='rowhead' align='center'>$num</td>
            <td class='rowhead' align='left'>
                <table class='main' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td class='embedded'><img style='text-align: center;' src='{$image_dir}flag/{$a['flagpic']}' width='32' height='20' alt='{$a['name']}' title='{$a['name']}' /></td>
                        <td class='embedded' style='padding-left: 5px'>{$a['name']}</td>
                    </tr>
                </table>
            </td>
            <td class='rowhead' align='right'>$value</td>
        </tr>\n");
    }
    end_table();
    end_frame();
}

site_header("Top Ten", false);

?>

<br /><br />
<div style="text-align: center;">
    <span style="font-size: small;">Welcome to<br /><span style='font-weight:bold;'><?php echo $site_name?>.</span><br />Top Ten Menu<br /></span>
</div>
<br /><br />

<table width='81%' cellpadding='4'>
    <tr>
        <td class='std' align='center'>
            <div id='featured'><br />
                <div style="text-align: center; text-decoration: underline; font-weight: bold;">Members</div>
                <br />
                <ul>
                <li><a href='#fragment-0'></a></li>
                <li><a class='btn' href='#fragment-1'>Uploaders</a></li>
                <li><a class='btn' href='#fragment-2'>Fastest Uploaders</a></li>
                <li><a class='btn' href='#fragment-3'>Downloaders</a></li>
                <li><a class='btn' href='#fragment-4'>Fastest Downloaders</a></li>
                <li><a class='btn' href='#fragment-5'>Best Sharers</a></li>
                <li><a class='btn' href='#fragment-6'>Worst Sharers</a>

                <br /><br />

                <div style="text-align: center; text-decoration: underline; font-weight: bold;">Torrents</div>
                <br /></li>

                <li><a class='btn' href='#fragment-7'>Most Active</a></li>
                <li><a class='btn' href='#fragment-8'>Most Snatched</a></li>
                <li><a class='btn' href='#fragment-9'>Most Data Transferred</a></li>
                <li><a class='btn' href='#fragment-10'>Best Seeded</a></li>
                <li><a class='btn' href='#fragment-11'>Worst Seeded</a>

                <br /><br />

                <div style="text-align: center; text-decoration: underline; font-weight: bold;">Countries</div>
                <br /></li>

                <li><a class='btn' href='#fragment-12'>Members</a></li>
                <li><a class='btn' href='#fragment-13'>Total Uploaded</a></li>
                <li><a class='btn' href='#fragment-14'>Average Total Uploaded</a></li>
                <li><a class='btn' href='#fragment-15'>Ratio</a></li>
                </ul>

<div class='ui-tabs-panel' id='fragment-1'>

    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Uploaders</td>
        </tr>
    </table>
    <br />
    <?php
    $pu = get_user_class() >= UC_POWER_USER;

    if (!$pu)
    {
        $limit = 10;
    }

    $mainquery = "SELECT id AS userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed
                    FROM users
                    WHERE enabled = 'yes'";

    $limit   = 10;
    $subtype = '';

    if ($limit == 10 || $subtype == "ul")
    {
        $order = "uploaded DESC";
        $r = sql_query($mainquery." ORDER BY $order "." LIMIT $limit") or sqlerr();

        usertable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-2'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Fastest Uploaders (average, includes inactive time)</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "uls")
    {
        $order = "upspeed DESC";
        $r = sql_query($mainquery." ORDER BY $order "." LIMIT $limit") or sqlerr();

        usertable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-3'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Downloaders</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "dl")
    {
        $order = "downloaded DESC";
        $r = sql_query($mainquery." ORDER BY $order "." LIMIT $limit") or sqlerr();

        usertable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-4'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Fastest Downloaders (average, includes inactive time)</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "dls")
    {
        $order = "downspeed DESC";
        $r = sql_query($mainquery." ORDER BY $order "." LIMIT $limit") or sqlerr();

        usertable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-5'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Best Sharers (with minimum 1 GB downloaded)</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "bsh")
    {
        $order      = "uploaded / downloaded DESC";
        $extrawhere = " and downloaded > 1073741824";

        $r = sql_query($mainquery.$extrawhere." ORDER BY $order "." LIMIT $limit") or sqlerr();

        usertable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-6'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Worst Sharers (with minimum 1 GB downloaded)</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "wsh")
    {
        $order      = "uploaded / downloaded ASC, downloaded DESC";
        $extrawhere = " and downloaded > 1073741824";
        $r = sql_query($mainquery.$extrawhere." ORDER BY $order "." LIMIT $limit") or sqlerr();

        usertable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-7'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Most Active Torrents</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "act")
    {
        $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
                        FROM torrents AS t
                        LEFT JOIN peers AS p ON t.id = p.torrent
                        WHERE p.seeder = 'no'
                        GROUP BY t.id
                        ORDER BY seeders + leechers DESC, seeders DESC, added ASC
                        LIMIT $limit") or sqlerr();

        _torrenttable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-8'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Most Snatched Torrents</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "sna")
    {
        $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
                        FROM torrents AS t
                        LEFT JOIN peers AS p ON t.id = p.torrent
                        WHERE p.seeder = 'no'
                        GROUP BY t.id
                        ORDER BY times_completed DESC
                        LIMIT $limit") or sqlerr();

        _torrenttable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-9'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Most Data Transferred Torrents</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "mdt")
    {
        $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
                        FROM torrents AS t
                        LEFT JOIN peers AS p ON t.id = p.torrent
                        WHERE p.seeder = 'no' and leechers >= 5 AND times_completed > 0
                        GROUP BY t.id
                        ORDER BY data DESC, added ASC
                        LIMIT $limit") or sqlerr();

        _torrenttable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-10'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Best Seeded Torrents (with minimum 5 seeders)</td>
        </tr>
    </table>
    <br />
    <?php

    $limit = 10;

    if ($limit == 10 || $subtype == "bse")
    {
        $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
                        FROM torrents AS t
                        LEFT JOIN peers AS p ON t.id = p.torrent
                        WHERE p.seeder = 'no'
                        AND seeders >= 5
                        GROUP BY t.id
                        ORDER BY seeders / leechers DESC, seeders DESC, added ASC
                        LIMIT $limit") or sqlerr();

        _torrenttable($r, "".($limit == 10 && $pu ? "" : ""));
    }

    ?>
</div>
<div class='ui-tabs-panel' id='fragment-11'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Worst Seeded Torrents (with minimum 5 leechers, excluding unsnatched torrents)
            </td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "wse")
    {
        $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
                            FROM torrents AS t
                            LEFT JOIN peers AS p ON t.id = p.torrent
                            WHERE p.seeder = 'no'
                            AND leechers >= 5
                            AND times_completed > 0
                            GROUP BY t.id
                            ORDER BY seeders / leechers ASC, leechers DESC
                            LIMIT $limit") or sqlerr();

        _torrenttable($r, "".($limit == 10 && $pu ? "" : ""));
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-12'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Countries (memebers)</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "us")
    {
        $r = sql_query("SELECT name, flagpic, COUNT(users.country) AS num
                        FROM countries
                        LEFT JOIN users ON users.country = countries.id
                        GROUP BY name
                        ORDER BY num DESC
                        LIMIT $limit") or sqlerr();

        countriestable($r, "".($limit == 10 && $pu ? "" : ""), "Users");
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-13'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Countries (total uploaded)</td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "ul")
    {
        $r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul
                        FROM users AS u
                        LEFT JOIN countries AS c ON u.country = c.id
                        WHERE u.enabled = 'yes'
                        GROUP BY c.name
                        ORDER BY ul DESC
                        LIMIT $limit") or sqlerr();

        countriestable($r, "".($limit == 10 && $pu ? "" : ""), "Uploaded");
    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-14'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Countries (average total uploaded per member, with minimum 1TB uploaded and 100 members)
            </td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "avg")
    {
        $r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/count(u.id) AS ul_avg FROM users AS u
                        LEFT JOIN countries AS c ON u.country = c.id
                        WHERE u.enabled = 'yes'
                        GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776
                        AND count(u.id) >= 100
                        ORDER BY ul_avg DESC
                        LIMIT $limit") or sqlerr();

        countriestable($r, "".($limit == 10 && $pu ? "" : ""), "Average");

    }
    ?>
</div>
<div class='ui-tabs-panel' id='fragment-15'>
    <table width='81%' cellpadding='4'>
        <tr>
            <td class='colhead' align='center'>Top 10 Countries (ratio, with minimum 1TB uploaded, 1TB downloaded and 100 members)
            </td>
        </tr>
    </table>
    <br />
    <?php
    $limit = 10;

    if ($limit == 10 || $subtype == "r")
    {
        $r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/sum(u.downloaded) AS r
                        FROM users AS u
                        LEFT JOIN countries AS c ON u.country = c.id
                        WHERE u.enabled = 'yes'
                        GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776
                        AND sum(u.downloaded) > 1099511627776
                        AND count(u.id) >= 100
                        ORDER BY r DESC
                        LIMIT $limit") or sqlerr();

        countriestable($r, "".($limit == 10 && $pu ? "" : ""), "Ratio");
    }
    ?>
</div>
            <br />
            </div>
        </td>
    </tr>
</table>

<script type="text/javascript" src="js/jquery-1.8.2.js" ></script>
<script type="text/javascript" src="js/jquery-ui-1.9.0.custom.min.js" ></script>

<script type="text/javascript">
    $(document).ready(function()
    {
        $("#featured").tabs({fx:{opacity: "toggle"}}).tabs("rotate", 5000, true);
    });
</script>

<?php

site_footer();

?>