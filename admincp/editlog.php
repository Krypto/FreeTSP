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

site_header('Php File Edit Log',false);

// Written by RetroKill to allow scripters to see what scripts have changed since
// they last updated their own list.
//
// This script will create a unique list for each member allowed to access this
// script. It allows them to see what scripts have been updated since they last
// updated their own list, allowing scripters to work together better.
//
// The first run will produce no results, as it will initialise the list for the
// member running the script. Further runs will show the scripter when a script
// has been updated from their original list (someone else, or they, have modified
// a script). When a member updates a script, they should run this script, which
// will show the update, then update their list using the update button, to bring
// their list up to date. If an update appears when the scripter hasn't made any
// changes, then they know that another scripter has modified a script.

function unsafeChar($var)
{
    return str_replace(array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $var);
}

function safeChar($var)
{
    return htmlspecialchars(unsafeChar($var));
}

function makeSafeText($arr)
{
    foreach ($arr
             AS
             $k => $v)
    {
        if (is_array($v))
            $arr[$k] = makeSafeText($v);
        else
            $arr[$k] = safeChar($v);
    }
    return $arr;
}

//-- Makes The Data Safe --//
if (!empty($_GET)) $_GET       = makeSafeText($_GET);
if (!empty($_POST)) $_POST     = makeSafeText($_POST);
if (!empty($_COOKIE)) $_COOKIE = makeSafeText($_COOKIE);

$file_data = CACHE_DIR.'edit_log/data_'.$CURUSER['username'].'.txt';

if (file_exists($file_data))
{
    //-- Fetch Existing Data --//
    $data  = unserialize(file_get_contents($file_data));
    $exist = true;
}
else
{
    // Initialise File --//
    $exist = false;
}

$fetch_set   = array();
$i           =0;
$directories = array();

//-- Enter Directories To Log... If You Dont Have Them - Comment Them Out Or Edit --//
$directories[] = './'; // Webroot
$directories[] = './admincp/';
$directories[] = './functions/';
$directories[] = './stylesheets/';
$directories[] = './cache/';
$directories[] = './functions/dictbreaker/';
$directories[] = './js/';
$directories[] = './forum_attachments/';

foreach ($directories
         AS
         $x)
{
    if ($handle = opendir($x))
    {
        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != "..")
            {
                if (!is_dir($x.'/'.$file))
                {
                    $fetch_set[$i]['modify'] = filemtime($x.$file);
                    $fetch_set[$i]['size']   = filesize($x.$file);
                    $fetch_set[$i]['name']   = $x.$file;
                    $fetch_set[$i]['key']    = $i;
                    $i++;
                }
            }
        }
        closedir($handle);
    }
}

if (!$exist OR (isset($_POST['update']) AND ($_POST['update'] == 'Update')))
{
    //-- Create First Disk Image Of Files --//
    //-- Or Update Existing Data --//
    $data   = serialize($fetch_set);
    $handle = fopen($file_data,"w");

    fputs($handle, $data);
    fclose($handle);

    $data   = unserialize($data);
}

//-- We Now Need To Link Current Contents With Stored Contents. --//
reset($fetch_set);
reset($data);

$current = $fetch_set;
$last    = $data;

foreach ($current
        AS
        $x)
{
    //-- Search The Data Sets For Differences --//
    foreach ($last
             AS
             $y)
    {
        if ($x['name'] == $y['name'])
        {
            if (($x['size'] == $y['size']) AND ($x['modify'] == $y['modify']))
            {
                unset ($current[$x['key']]);
                unset ($last[$y['key']]);
            }
            else
                $current[$x['key']]['status'] = 'modified';
        }
        if (isset($last[$y['key']])) $last[$y['key']]['status'] = 'deleted';
    }
    if (isset($current[$x['key']]['name']) AND !isset($current[$x['key']]['status'])) $current[$x['key']]['status'] = 'new';
}

//-- Add Deleted Entries To Current List --//
$current += $last;
unset ($last);

//-- $fetch_data Contains A Current List Of Directory --//
//-- $data Contains The Last Snapshot Of The Directory --//
//-- $current Contains A Current List Of Files In The Directory That Are --//
//-- New, Modified Or Deleted... --//
//-- Remove Lists From Current Code... --//
unset ($data);
unset ($fetch_set);

echo("<table border='1' width='750' align='center' cellspacing='2' cellpadding='5'>
        <tr>
            <td class='colhead' align='center' width='70%'><strong>New Files Added Since Last Check.</strong></td>
            <td class='colhead' align='center'><strong>Added.</strong></td>
    </tr>");

reset($current);

$count = 0;

foreach ($current
         AS
         $x)
{
    if ($x['status'] == 'new')
    {

?>
<tr>
    <td align='center'><?php echo safeChar(substr($x['name'],2));?></td>
    <td align='center'><?php echo get_date_time($x['modify'], 'LONG',0,1);?></td>
</tr>
<?php

        $count++;
    }
}

if (!$count)
{

    echo("<tr>
            <td align='center' colspan='2'><b>No New Files Added Since Last Check.</b></td>
        </tr>");
}

echo("</table>
    <br /><br /><br />
    <table border='1' width='750' align='center' cellspacing='2' cellpadding='5'>
        <tr>
            <td class='colhead' align='center' width='70%'><strong>Modified Files Since Last Check.</strong></td>
            <td class='colhead'align='center'><strong>Modified.</strong></td>
    </tr>");

reset($current);

$count = 0;

foreach ($current
         AS
         $x)
{
    if ($x['status'] == 'modified')
    {
?><tr>
    <td align='center'><?php echo safeChar(substr($x['name'],2))?></td>
    <td align='center'><?php echo get_date_time($x['modify'], 'long',0,1)?></td>
</tr>
<?php

    $count++;
    }
}

if (!$count)
{
    echo("<tr>
            <td align='center' colspan='2'><b>No Files Modified Since Last Check.</b></td>
        </tr>");
}

echo("</table>
    <br /><br /><br />
    <table border='1' width='750' align='center' cellspacing='2' cellpadding='5'>
        <tr>
            <td class='colhead' align='center' width='70%'><strong>Files Deleted Since Last Check.</strong></td>
            <td class='colhead' align='center'><strong>Deleted.</strong></td>
        </tr>");

reset($current);

$count = 0;

foreach ($current
         AS
         $x)
{
    if ($x['status'] == 'deleted')
    {
?>
<tr>
    <td align='center'><?php echo safeChar(substr($x['name'],2))?></td>
    <td align='center'><?php echo get_date_time($x['modify'], 'LONG',0,1)?></td>
</tr>
<?php

    $count++;
    }
}

if (!$count)
{
    echo("<tr>
            <td align='center' colspan='2'><b>No Files Deleted Since Last Check.</b></td>
        </tr>");
}

echo("</table>
    <br /><br /><br />
    <form method='post' action='controlpanel.php?fileaction=10'>
        <table border='1' width='750' align='center' cellspacing='2' cellpadding='5'>
            <tr>
                <td class='colhead' align='center'>
                    <input type='submit' class='btn' name='update' value='Update' />
                </td>
            </tr>
        </table>
    </form>");

site_footer();

?>