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
require_once(FUNC_DIR.'function_torrenttable.php');
require_once(FUNC_DIR.'function_bbcode.php');

db_connect(false);
logged_in();

parked();

if (isset($_GET['clear_new']) && $_GET['clear_new'] == '1')
{
    sql_query("UPDATE users
                SET last_browse=".gmtime()."+3600
                WHERE id=".$CURUSER['id']);

    header("Location: {$site_url}/browse.php");
}

$cats = genrelist();

if (isset($_GET["search"]))
{
    $searchstr      = unesc($_GET["search"]);
    $cleansearchstr = searchfield($searchstr);

    if (empty($cleansearchstr))
    {
        unset($cleansearchstr);
    }
}

$orderby     = "ORDER BY torrents.sticky ASC, torrents.id DESC";
$addparam    = "";
$wherea      = array();
$wherecatina = array();

if (isset($_GET["incldead"]) && $_GET["incldead"] == 1)
{
    $addparam .= "incldead=1&amp;";

    if (!isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR)
    {
        $wherea[] = "banned != 'yes'";
    }
}
else
{
    if (isset($_GET["incldead"]) && $_GET["incldead"] == 2)
    {
        $addparam .= "incldead=2&amp;";
        $wherea[] = "visible = 'no'";
    }
    else
    {
        $wherea[] = "visible = 'yes'";
    }
}

$category = (isset($_GET["cat"])) ? (int) $_GET["cat"] : false;
$all      = isset($_GET["all"]) ? $_GET["all"] : false;

if (!$all)
{
    if (!$_GET && $CURUSER["notifs"])
    {
        $all = true;

        foreach ($cats
                 AS
                 $cat)
        {
            $all &= $cat['id'];

            if (strpos($CURUSER["notifs"], "[cat".$cat['id']."]") !== false)
            {
                $wherecatina[] = $cat['id'];
                $addparam .= "c{$cat['id']}=1&amp;";
            }
        }
    }
    elseif ($category)
    {
        if (!is_valid_id($category))
        {
            error_message("error", "Error", "Invalid Category ID.");
        }

        $wherecatina[] = $category;
        $addparam .= "cat=$category&amp;";
    }
    else
    {
        $all = true;

        foreach ($cats
                 AS
                 $cat)
        {
            $all &= isset($_GET["c{$cat['id']}"]);

            if (isset($_GET["c{$cat['id']}"]))
            {
                $wherecatina[] = $cat['id'];
                $addparam .= "c{$cat['id']}=1&amp;";
            }
        }
    }
}

if ($all)
{
    $wherecatina = array();
    $addparam    = "";
}

if (count($wherecatina) > 1)
{
    $wherecatin = implode(",", $wherecatina);
}

elseif (count($wherecatina) == 1)
{
    $wherea[] = "category = $wherecatina[0]";
}

$wherebase = $wherea;

if (isset($cleansearchstr))
{
    $wherea[] = "MATCH (search_text, ori_descr) AGAINST (".sqlesc($searchstr).")";
    $addparam .= "search=".urlencode($searchstr)."&amp;";
    $orderby  = "";
}

$where = implode(" AND ", $wherea);

if (isset($wherecatin))
{
    $where .= ($where ? " AND " : "")."category IN(".$wherecatin.")";
}

if ($where != "")
{
    $where = "WHERE $where";
}

$res = sql_query("SELECT COUNT(id)
                    FROM torrents $where") or die(mysql_error());

$row = mysql_fetch_array($res, MYSQL_NUM);

$count = $row[0];

if (!$count && isset($cleansearchstr))
{
    $wherea  = $wherebase;
    $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
    $searcha = explode(" ", $cleansearchstr);
    $sc      = 0;

    foreach ($searcha
            AS
            $searchss)
    {
        if (strlen($searchss) <= 1)
        {
            continue;
        }

        $sc++;

        if ($sc > 5)
        {
            break;
        }

        $ssa = array();

        foreach (array("search_text",
                       "ori_descr")
                    AS
                    $sss)

        {
            $ssa[] = "$sss LIKE '%".sqlwildcardesc($searchss)."%'";
        }

        $wherea[] = "(".implode(" or ", $ssa).")";
    }

    if ($sc)
    {
        $where = implode(" AND ", $wherea);
        if ($where != "")
        {
            $where = "WHERE $where";
        }

        $res = sql_query("SELECT COUNT(id)
                            FROM torrents $where");

        $row   = mysql_fetch_array($res, MYSQL_NUM);
        $count = $row[0];
    }
}

$torrentsperpage = $CURUSER["torrentsperpage"];

if (!$torrentsperpage)
{
    $torrentsperpage = 15;
}

if ($count)
{
    list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "browse.php?".$addparam);

    $query = "SELECT torrents.id, torrents.sticky, torrents.category, torrents.leechers, torrents.seeders, torrents.freeleech, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.sticky, torrents.anonymous, torrents.banned,
torrents.owner,IF(torrents.nfo <> '', 1, 0) AS nfoav,".//   "IF(torrents.numratings < $min_votes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
            "categories.name AS cat_name, categories.image AS cat_pic, users.username
            FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";

    $res = sql_query($query) or die(mysql_error());
}
else
{
    unset($res);
}

if (isset($cleansearchstr))
{
    site_header("Search Results for '$searchstr'");
}
else
{
    site_header('',false);
}

?>

<table class='bottom' width='100%'>
    <tr>
        <td class='embedded'>
            <form method='get' action='browse.php'>
                <p align='center'>
                    <label for='search'><span style='font-weight:bold;'>Search &nbsp;</span></label>
                    <input type='text' name='search' id='search' size='40' value='<?php htmlspecialchars($searchstr)?>' />
                    <input type='submit' class='btn' value='Okay' />
                </p>
            </form>
        </td>
    </tr>
</table>

<form method='get' action='browse.php'>
    <table class='bottom' width='100%'>
        <tr>
            <td class='bottom'>
                <table class='bottom' align='right' width='75%'>
                    <tr>
                        <?php

                        $i = 0;

                        foreach ($cats
                                 AS
                                 $cat)
                        {
                            $catsperrow = 6;

                            print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");

                            print("<td class='bottom' style='padding-bottom: 2px;padding-left: 7px'>
                                    <input name='c".$cat['id']."' type='checkbox' ".(in_array($cat['id'], $wherecatina) ? "checked='checked'" : "")."value='1' /><a class='catlink' href='browse.php?cat={$cat['id']}'>".htmlspecialchars($cat['name'])."</a>
                                    </td>\n");

                            $i++;
                        }

                        $alllink     = "<div align='left'>(<a href='browse.php?all=1'><span style='font-weight:bold;'>Show All</span></a>)</div>";
                        $ncats       = count($cats);
                        $nrows       = ceil($ncats / $catsperrow);
                        $lastrowcols = $ncats % $catsperrow;

                        if ($lastrowcols != 0)
                        {
                            if ($catsperrow - $lastrowcols != 1)
                            {
                                print("<td class='bottom' rowspan='".($catsperrow - $lastrowcols - 1)."'>&nbsp;</td>");
                            }
                            print("<td class='bottom' style='padding-left: 5px'>$alllink</td>\n");
                        }

                        ?>
                    </tr>
                </table>
            </td>
            <td class='bottom'>
                <table class='bottom' width='50'>
                    <tr>
                        <td class='bottom' style='padding: 1px;padding-left: 10px'>
                            <label><select name='incldead'>
                                <option value='0'>Active</option>
                                <option value='1'<?php echo($_GET["incldead"] == 1 ? " selected='selected'" : ""); ?>>
                                    Including Dead
                                </option>
                                <option value='2'<?php echo($_GET["incldead"] == 2 ? " selected='selected'" : ""); ?>>
                                    Only Dead
                                </option>
                            </select></label>
                        </td>

                        <?php

                        if ($ncats % $catsperrow == 0)
                        {
                            print("<td class='bottom' style='padding-left: 15px' rowspan='$nrows' valign='center' align='right'>$alllink</td>\n");
                        }

                        ?>

                    </tr>
                    <tr>
                        <td class='bottom' style='padding: 1px 1px 1px 10px;padding-left: 10px'>
                            <div align='center'>
                                <input type='submit' class='btn' value='Go!' />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

<?php

if (isset($cleansearchstr))
{
    print("<h2>Search Results for '".htmlspecialchars($searchstr)."'</h2>\n");
}

//-- If you want a Button --//
echo ("<a href='?clear_new=1'><input type='submit' class='btn' value='Clear New Tag' /></a><br />");
//-- If you want a Link --//

/*
    echo ("<a class='altlink' href='?clear_new=1'><span style='font-weight:bold; font-color:#FF0000;'>Clear New Tag</span></a>");
*/

if ($count)
{
    print($pagertop);

    torrenttable($res);

    print($pagerbottom);
}
else
{
    if (isset($cleansearchstr))
    {
        error_message("info", "Nothing Found!", "Please Refine your Search String.");
    }
    else
    {
        echo ("<br />");
        display_message("info", "Sorry.", "Nothing Here!");
    }
}
//
site_footer();

?>