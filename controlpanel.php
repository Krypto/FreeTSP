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
require_once(ROOT_DIR.'ofc/lib/open-flash-chart.php');
require_once(FUNC_DIR.'function_pager_new.php');

db_connect();
logged_in();

define("IN_FTSP_ADMIN", true);

if (get_user_class() < UC_MODERATOR)
{
	error_message("warn", "Warning", "Access Denied.");
}

site_header("Admin CP", false);

//-- Start Add New Tools --//
if (get_user_id() == UC_TRACKER_MANAGER)
{
    $create = $_GET['create'];

    if ($create == 'true')
    {
        $mod_name   = $_GET['mod_name'];
        $mod_url    = $_GET['mod_url'];
        $mod_image  = $_GET['mod_image'];
        $mod_status = $_GET['mod_status'];
        $max_class  = $_GET['max_class'];

        $query = "INSERT INTO controlpanel
                    SET name = '$mod_name', url = '$mod_url', image = '$mod_image', status = '$mod_status', max_class = '$max_class'" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        if ($sql)
        {
            $success = true;
        }
        else
        {
            $success = false;
        }
    }

    if ($success == true)
    {
        echo("<div align='center'>The File has be Successfully Added!<br /><a href='".$_SERVER['PHP_SELF']."'><span style='font-weight:bold;'>[ Return ]</span></a></div><br /><br />");

        site_footer();
        die();
    }
}
//-- Finish Add New Tools --//

//-- Start Remove and Edit Options for tracker_manager --//
$trakman = $_GET['trakman'];

if ($trakman == "yes")
{
    $deltrakmanid = $_GET['deltrakmanid'];

    $query = "DELETE FROM controlpanel
                WHERE id=" .sqlesc($deltrakmanid)."
                LIMIT 1" or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    echo("<div align='center'>The File has be Successfully Deleted!<br /><a href='".$_SERVER['PHP_SELF']."'><span style='font-weight:bold;'>[ Return ]</span></a></div><br /><br />");

    site_footer();
    die();
}

$deltrakmanid = $_GET['deltrakmanid'];
$name         = $_GET['mod'];

if ($deltrakmanid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<div align='center'><p>Are you sure you wish to Delete :- <span style='font-weight:bold;'>$name</span></p> (<span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."?deltrakmanid=$deltrakmanid&amp;mod=$name&amp;trakman=yes'>Yes!</a></span> / <span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."'>No!</a></span>)</div><br /><br />");
    }

    site_footer();
    die();
}

$edittrakman = $_GET['edittrakman'];

if ($edittrakman == 1)
{
    $id         = $_GET['id'];
    $mod_name   = $_GET['mod_name'];
    $mod_url    = $_GET['mod_url'];
    $mod_image  = $_GET['mod_image'];
    $mod_status = $_GET['mod_status'];
    $max_class  = $_GET['max_class'];

    $query = "UPDATE controlpanel
                SET name = '$mod_name', url = '$mod_url', image = '$mod_image', status = '$mod_status', max_class = '$max_class'
                WHERE id =".sqlesc($id) or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    if ($sql)
    {
        if (get_user_id() == UC_TRACKER_MANAGER)
            {
                echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
                echo("<tr><td><div align='center'><span style='font-weight:bold;'>The File has been Successfully Edited<br /><a href='".$_SERVER['PHP_SELF']."'>[ Return ]</a></span></div></td></tr>");
                echo("</table><br /><br />");
            }

        site_footer();
        die();
    }
}

$edittrakmanid = $_GET['edittrakmanid'];
$name          = $_GET['name'];
$url           = $_GET['url'];
$image         = $_GET['image'];
$status        = $_GET['status'];
$max_class     = $_GET['max_class'];

if ($edittrakmanid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<br />");
        echo("<form name='form1' method='get' action='".$_SERVER['PHP_SELF']."'>");
        echo("<input type='hidden' name='edittrakman' value='1' />");
        echo("<input type='hidden' name='id' value='$edittrakmanid' />");
        echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
        echo("<tr><td class='colhead'><label for='desc'>Description: </label></td><td align='left'><input type='text' name='mod_name' id='desc' size='50' value='$name' /></td></tr>");
        echo("<tr><td class='colhead'><label for='name'>File Name: </label></td><td align='left'><input type='text' name='mod_url' id='name' size='50' value='$url' /></td></tr>");
        echo("<tr><td class='colhead'><label for='image'>Image: </label></td><td align='left'><input type='text' name='mod_image' id='image' size='50' value='$image' /></td></tr>");
        echo("<tr><td class='colhead'><label for='active'>File Active: </label></td><td align='left'><select name='mod_status' id='active'><option value='1'>Yes</option><option value='0'>No</option></select><input type='hidden' name='add' value='true' /></td></tr>");
        echo("<tr>
                <td class='colhead'><label for='option'>Option For: </label></td>
                <td align='left'>
                <select name='max_class' id='option'>
                <option value='7'>Tracker Manager</option>
                <option value='4'>Moderator</option>
                <option value='5'>Administrator</option>
                <option value='6'>Sysop</option>
                </select>
                <input type='hidden' name='add' value='true' />
                </td>
            </tr>");
        echo("<tr><td class='std' align='center' colspan='2'><input type='submit' class='btn' value='Change' /></td></tr>");
        echo("</table></form><br /><br />");
    }

    site_footer();
    die();
}
//-- Finish Remove and Edit Options for tracker_manager --//

//-- Start Remove and Edit Options for Sysops --//
$sysop = $_GET['sysop'];

if ($sysop == "yes")
{
    $delsysopid = $_GET['delsysopid'];

    $query = "DELETE FROM controlpanel
                WHERE id=" .sqlesc($delsysopid)."
                LIMIT 1" or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    echo("<div align='center'>The File has be Successfully Deleted!<br /><a href='".$_SERVER['PHP_SELF']."'><span style='font-weight:bold;'>[ Return ]</span></a></div><br /><br />");

    site_footer();
    die();
}

$delsysopid = $_GET['delsysopid'];
$name       = $_GET['mod'];

if ($delsysopid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<div align='center'><p>Are you sure you wish to Delete :- <span style='font-weight:bold;'>$name</span></p> (<span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."?delsysopid=$delsysopid&amp;mod=$name&amp;sysop=yes'>Yes!</a></span> / <span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."'>No!</a></span>)</div><br /><br />");
    }

    site_footer();
    die();
}

$editsysop = $_GET['editsysop'];

if ($editsysop == 1)
{
    $id         = $_GET['id'];
    $mod_name   = $_GET['mod_name'];
    $mod_url    = $_GET['mod_url'];
    $mod_image  = $_GET['mod_image'];
    $mod_status = $_GET['mod_status'];
    $max_class  = $_GET['max_class'];

    $query = "UPDATE controlpanel
                SET name = '$mod_name', url = '$mod_url', image = '$mod_image', status = '$mod_status', max_class = '$max_class'
                WHERE id =".sqlesc($id) or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    if ($sql)
    {
        if (get_user_id() == UC_TRACKER_MANAGER)
        {
            echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
            echo("<tr><td><div align='center'><span style='font-weight:bold;'>The File has been Successfully Edited<br /><a href='".$_SERVER['PHP_SELF']."'>[ Return ]</a></span></div></td></tr>");
            echo("</table><br /><br />");
        }

    site_footer();
    die();
    }
}

$editsysopid = $_GET['editsysopid'];
$name        = $_GET['name'];
$url         = $_GET['url'];
$image       = $_GET['image'];
$status      = $_GET['status'];
$max_class   = $_GET['max_class'];

if ($editsysopid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<br />");
        echo("<form name='form1' method='get' action='".$_SERVER['PHP_SELF']."'>");
        echo("<input type='hidden' name='editsysop' value='1' />");
        echo("<input type='hidden' name='id' value='$editsysopid' />");
        echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
        echo("<tr><td class='colhead'><label for='desc'>Description: </label></td><td align='left'><input type='text' name='mod_name' id='desc' size='50' value='$name' /></td></tr>");
        echo("<tr><td class='colhead'><label for='name'>File Name: </label></td><td align='left'><input type='text' name='mod_url' id='name' size='50' value='$url' /></td></tr>");
        echo("<tr><td class='colhead'><label for='image'>Image: </label></td><td align='left'><input type='text' name='mod_image' id='image' size='50' value='$image' /></td></tr>");
        echo("<tr><td class='colhead'><label for='active'>File Active: </label></td><td align='left'><select name='mod_status' id='active'><option value='1'>Yes</option><option value='0'>No</option></select><input type='hidden' name='add' value='true' /></td></tr>");
        echo("<tr>
                <td class='colhead'><label for='option'>Option For: </label></td>
                <td align='left'>
                <select name='max_class' id='option'>
                <option value='6'>Sysop</option>
                <option value='4'>Moderator</option>
                <option value='5'>Administrator</option>
                <option value='7'>Tracker Manager</option>
                </select>
                <input type='hidden' name='add' value='true' />
                </td>
            </tr>");
        echo("<tr><td class='std' align='center' colspan='2'><input type='submit' class='btn' value='Change' /></td></tr>");
        echo("</table></form><br /><br />");
    }

    site_footer();
    die();
}
//-- Finish Remove and Edit Options for Sysops --//

//-- Start Remove and Edit Options for Admins --//
$admin = $_GET['admin'];

if ($admin == "yes")
{
    $deladminid = $_GET['deladminid'];

    $query = "DELETE FROM controlpanel
                WHERE id=" .sqlesc($deladminid)."
                LIMIT 1" or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    echo("<div align='center'>The File has be Successfully Deleted!<br /><a href='".$_SERVER['PHP_SELF']."'><span style='font-weight:bold;'>[ Return ]</span></a></div><br /><br />");

    site_footer();
    die();
}

$deladminid = $_GET['deladminid'];
$name       = $_GET['mod'];

if ($deladminid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<div align='center'><p>Are you sure you wish to Delete :- <span style='font-weight:bold;'>$name</span></p> (<span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."?deladminid=$deladminid&amp;mod=$name&amp;admin=yes'>Yes!</a></span> / <span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."'>No!</a></span>)</div><br /><br />");
    }

    site_footer();
    die();
}

$editadmin = $_GET['editadmin'];

if ($editadmin == 1)
{
    $id         = $_GET['id'];
    $mod_name   = $_GET['mod_name'];
    $mod_url    = $_GET['mod_url'];
    $mod_image  = $_GET['mod_image'];
    $mod_status = $_GET['mod_status'];
    $max_class  = $_GET['max_class'];

    $query = "UPDATE controlpanel
                SETname = '$mod_name',url = '$mod_url',image = '$mod_image',status = '$mod_status',max_class = '$max_class'
                WHERE id =".sqlesc($id) or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    if ($sql)
    {
        if (get_user_id() == UC_TRACKER_MANAGER)
        {
            echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
            echo("<tr><td><div align='center'><span style='font-weight:bold;'>The file has been successfully edited<br /><a href='".$_SERVER['PHP_SELF']."'>[ Return ]</span></a></div></td></tr>");
            echo("</table><br /><br />");
        }

        site_footer();
        die();
    }
}

$editadminid = $_GET['editadminid'];
$name        = $_GET['name'];
$url         = $_GET['url'];
$image       = $_GET['image'];
$status      = $_GET['status'];
$max_class   = $_GET['max_class'];

if ($editadminid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<br />");
        echo("<form name='form1' method='get' action='".$_SERVER['PHP_SELF']."'>");
        echo("<input type='hidden' name='editadmin' value='1'/>");
        echo("<input type='hidden' name='id' value='$editadminid' />");
        echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
        echo("<tr><td class='colhead'><label for='desc'>Description: </label></td><td align='left'><input type='text' name='mod_name' id='desc' size='50' value='$name' /></td></tr>");
        echo("<tr><td class='colhead'><label for='name'>File Name: </label></td><td align='left'><input type='text' name='mod_url' id='name' size='50' value='$url' /></td></tr>");
        echo("<tr><td class='colhead'><label for='image'>Image: </label></td><td align='left'><input type='text' name='mod_image' id='image' size='50' value='$image' /></td></tr>");
        echo("<tr><td class='colhead'><label for='active'>File Active: </label></td><td align='left'><select name='mod_status' id='active'><option value='1'>Yes</option><option value='0'>No</option></select><input type='hidden' name='add' value='true' /></td></tr>");
        echo("<tr>
                <td class='colhead'><label for='option'>Option For: </label></td>
                <td align='left'>
                <select name='max_class' id='option'>
                <option value='5'>Administrator</option>
                <option value='4'>Moderator</option>
                <option value='6'>Sysop</option>
                <option value='7'>Tracker Manager</option>
                </select>
                <input type='hidden' name='add' value='true'/>
                </td>
            </tr>");
        echo("<tr><td class='std' colspan='2' align='center'><input type='submit' class='btn' value='Change' /></td></tr>");
        echo("</table></form><br /><br />");
    }

    site_footer();
    die();
}
//-- Finish Remove and Edit Options for Admins --//

//-- Start Remove and Edit Options for Moderators --//
$mod = $_GET['mod'];

if ($mod == "yes")
{
    $delmodid = $_GET['delmodid'];

    $query = "DELETE FROM controlpanel
                WHERE id=" .sqlesc($delmodid)."
                LIMIT 1" or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    echo("<div align='center'>The File has be Successfully Deleted!<br /><a href='".$_SERVER['PHP_SELF']."'><span style='font-weight:bold;'>[ Return ]</span></a></div><br /><br />");

    site_footer();
    die();
}

$delmodid = $_GET['delmodid'];
$name     = $_GET['mod'];

if ($delmodid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<div align='center'><p>Are you sure you wish to Delete :- <span style='font-weight:bold;'>$name</span></p> (<span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."?delmodid=$delmodid&amp;mod=$name&amp;mod=yes'>Yes!</a></span> / <span style='font-weight:bold;'><a href='".$_SERVER['PHP_SELF']."'>No!</a></span>)</div><br /><br />");
    }

    site_footer();
    die();
}

$editmod = $_GET['editmod'];

if ($editmod == 1)
{
    $id         = $_GET['id'];
    $mod_name   = $_GET['mod_name'];
    $mod_url    = $_GET['mod_url'];
    $mod_image  = $_GET['mod_image'];
    $mod_status = $_GET['mod_status'];
    $max_class  = $_GET['max_class'];

    $query = "UPDATE controlpanel
                SET name = '$mod_name', url = '$mod_url', image = '$mod_image', status = '$mod_status', max_class = '$max_class'
                WHERE id =".sqlesc($id) or sqlerr(__FILE__, __LINE__);

    $sql = sql_query($query);

    if ($sql)
    {
        if (get_user_id() == UC_TRACKER_MANAGER)
        {
            echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
            echo("<tr><td><div align='center'><span style='font-weight:bold;'>The File has been Successfully Edited<br /><a href='".$_SERVER['PHP_SELF']."'>[ Return ]</span></a></div></td></tr>");
            echo("</table><br /><br />");
        }

    site_footer();
    die();
    }
}

$editmodid = $_GET['editmodid'];
$name      = $_GET['name'];
$url       = $_GET['url'];
$image     = $_GET['image'];
$status    = $_GET['status'];
$max_class = $_GET['max_class'];

if ($editmodid > 0)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        echo("<br />");
        echo("<form name='form1' method='get' action='".$_SERVER['PHP_SELF']."'>");
        echo("<input type='hidden' name='editmod' value='1' />");
        echo("<input type='hidden' name='id' value='$editmodid' />");
        echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
        echo("<tr><td class='colhead'><label for='desc'>Description: </label></td><td align='left'><input type='text' name='mod_name' id='desc' size='50' value='$name' /></td></tr>");
        echo("<tr><td class='colhead'><label for='name'>File Name: </label></td><td align='left'><input type='text' name='mod_url' id='name' size='50' value='$url' /></td></tr>");
        echo("<tr><td class='colhead'><label for='image'>Image: </label></td><td align='left'><input type='text' name='mod_image' id='image' size='50' value='$image' /></td></tr>");
        echo("<tr><td class='colhead'><label for='active'>File Active: </label></td><td align='left'><select name='mod_status' id='active'><option value='1'>Yes</option><option value='0'>No</option></select><input type='hidden' name='add' value='true' /></td></tr>");
        echo("<tr>
                <td class='colhead'><label for='option'>Option For: </label></td>
                <td align='left'>
                <select name='max_class' id='option'>
                <option value='4'>Moderator</option>
                <option value='5'>Administrator</option>
                <option value='6'>Sysop</option>
                <option value='7'>Tracker Manager</option>
                </select>
                <input type='hidden' name='add' value='true' />
                </td>
            </tr>");
        echo("<tr><td class='std' align='center' colspan='2' ><input type='submit' class='btn' value='Change' /></td></tr>");
        echo("</table></form><br /><br />");
    }

    site_footer();
    die();
}
//-- Finish Remove and Edit Options for Moderators --//

//-- Start Output View --//

//-- Start Add Tools --//
$addaction = $_GET['addaction'];

if ($addaction == "addtools")
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        print("<br />");
        print("<br />");

        echo("<form name='form1' method='get' action='".$_SERVER['PHP_SELF']."'>");
        echo("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
        echo("<tr><td class='colhead'><label for='desc'>Description: </label></td><td class='rowhead' align='left'><input type='text' name='mod_name' id='desc' size='50' /></td></tr>");
        echo("<tr><td class='colhead'><label for='name'> File Name:<br />( NO .php )</label></td><td class='rowhead' align='left'><input type='text' name='mod_url' id='name' size='50' /></td></tr>");
        echo("<tr><td class='colhead'><label for='image'>Image: </label></td><td class='rowhead' align='left'><input type='text' name='mod_image' id='image' size='50' /></td></tr>");
        echo("<tr><td class='colhead'><label for='active'>File Active: </label></td><td class='rowhead' align='left'><select name='mod_status' id='active'><option value='1'>Yes</option><option value='0'>No</option></select><input type='hidden' name='add' value='true' /></td></tr>");
        echo("<tr>
              <td class='colhead'><label for='option'>Option for: </label></td>
              <td class='rowhead' align='left'>
              <select name='max_class' id='option'>
              <option value='4'>Moderator</option>
              <option value='5'>Administrator</option>
              <option value='6'>Sysop</option>
              <option value='7'>Tracker Manager</option>
              </select>
              <input type='hidden' name='create' value='true' />
              </td>
              </tr>");
        echo("<tr><td colspan='3'><div align='center'><input type='submit' class='btn' value='Add' /></div></td></tr>");
        echo("</table>");
        echo("</form>");
    }

    site_footer();
    die();
}
//-- Finish Add Tools --//

//-- Start Deactive Tool List --//
$listaction = $_GET['listaction'];

if ($listaction == "list")
{
    //-- Tracker Manager Tools --//
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        print("<br /><br />");
        print("<div align='center'><h2>Deactivated Tool List:</h2></div><br />");
        print("<table class='main' align='center' width='50%' cellspacing='0' cellpadding='5'>");
        print("<tr>
               <td class='colhead'><span style='font-weight:bold;'>ID:</span></td>
               <td class='colhead'><span style='font-weight:bold;'>Name:</span></td>
               <td class='colhead'><span style='font-weight:bold;'>URL:</span></td>
               <td class='colhead'><span style='font-weight:bold;'>Image:</span></td>
               <td class='colhead'><span style='font-weight:bold;'>Class:</span></td>
               <td class='colhead'><span style='font-weight:bold;'>Edit:</span></td>
               <td class='colhead'><span style='font-weight:bold;'>Delete:</span></td>
               </tr>");

        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=0 AND max_class=7" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $id   = $row['id'];
            $name = $row['name'];
            $url  = $row['url'];
            $image = $row['image'];
            $max_class = $row['max_class'];

            if ($max_class == 7)
        	{
        	   $max_class = "Tracker&nbsp;&nbsp;Manager";
            }

            print("<tr>
                   <td class='rowhead'>$id </td>
                   <td class='rowhead'>$name </td>
                   <td class='rowhead'>$url</td>
                   <td class='rowhead'>$image</td>
                   <td class='rowhead'>$max_class</td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?edittrakmanid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."admin/edit.png' width='16' height='16' border='0' alt='Edit Item' title='Edit Item' /></a></div></td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?deltrakmanid=$id&amp;mod=$name'><img src='".$image_dir."disabled.png' width='16' height='16' border='0' alt='Delete Item' title='Delete Item' /></a></div></td>
                   </tr>");
        }

        //-- Sysop Tools --//
        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=0 AND max_class=6" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $id        = $row['id'];
            $name      = $row['name'];
            $url       = $row['url'];
            $image     = $row['image'];
            $max_class = $row['max_class'];

            if ($max_class == 6)
        	{
        	   $max_class = "Sysop";
            }

            print("<tr>
                   <td class='rowhead'>$id </td>
                   <td class='rowhead'>$name </td>
                   <td class='rowhead'>$url</td>
                   <td class='rowhead'>$image</td>
                   <td class='rowhead'>$max_class</td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?editsysopid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."/admin/edit.png' width='16' height='16' border='0' alt='Edit Item' title='Edit Item' /></a></div></td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?delsysopid=$id&amp;mod=$name'><img src='".$image_dir."disabled.png' width='16' height='16' border='0' alt='Delete Item' title='Delete Item' /></a></div></td>
                   </tr>");
        }

        //-- Admin Tools --//
        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=0 AND max_class=5" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $id        = $row['id'];
            $name      = $row['name'];
            $url       = $row['url'];
            $image     = $row['image'];
            $max_class = $row['max_class'];

            if ($max_class == 5)
        	{
        	   $max_class = "Administrator";
            }

            print("<tr>
                   <td class='rowhead'>$id </td>
                   <td class='rowhead'>$name </td>
                   <td class='rowhead'>$url</td>
                   <td class='rowhead'>$image</td>
                   <td class='rowhead'>$max_class</td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?editadminid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."/admin/edit.png' width='16' height='16' border='0' alt='Edit Item' title='Edit Item' /></a></div></td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?deladmin=$id&amp;mod=$name'><img src='".$image_dir."disabled.png' width='16' height='16' border='0' alt='Delete Item' title='Delete Item' /></a></div></td>
                   </tr>");
        }

        //-- Mod Tools --//
        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=0 AND max_class=4" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $id        = $row['id'];
            $name      = $row['name'];
            $url       = $row['url'];
            $image     = $row['image'];
            $max_class = $row['max_class'];

            if ($max_class == 4)
        	{
        	   $max_class = "Moderator";
            }

            print("<tr>
                   <td class='rowhead'>$id </td>
                   <td class='rowhead'>$name </td>
                   <td class='rowhead'>$url</td>
                   <td class='rowhead'>$image</td>
                   <td class='rowhead'>$max_class</td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?editmodid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."/admin/edit.png' width='16' height='16' border='0' alt='Edit Item' title='Edit Item' /></a></div></td>
                   <td class='rowhead'><div align='center'><a href='".$_SERVER['PHP_SELF']."?delmodid=$id&amp;mod=$name'><img src='".$image_dir."disabled.png' width='16' height='16' border='0' alt='Delete Item' title='Delete Item' /></a></div></td>
                   </tr>");
        }

        print("</table><br /><br />");

    }

    site_footer();
    die();
}
//-- Finish Deactive Tool List --//

//-- Start Enter the Tools --//
$query = "SELECT *
            FROM controlpanel
            WHERE 1=1" or sqlerr(__FILE__, __LINE__);

$sql = sql_query($query);

while ($row = mysql_fetch_array($sql))
{
    $file       = $row["url"];
    $id         = $row["id"];
    $status     = $row["status"];
    $max_class  = $row['max_class'];
    $fileaction = $_GET['fileaction'];

    if ($fileaction == $row[id] & $CURUSER['class'] < "$max_class")
    {
        error_message("warn", "Access Denied", "Your Staff Level Is Incorrect For This Area.");
    }

    if ($fileaction == $row[id] & $status == 0)
    {
        error_message("warn", "Access Denied", "This File Has Been Deactivated.");

        site_footer();
        die();
    }

    if ($fileaction == $row[id] & $status == 1)
    {
        require("admincp/".$file.".php");
        site_footer();
        die();
    }
}
//-- Finish Enter the Tools --//

//-- Start Tool List --//

//-- Start Output View --//
if (get_user_class() >= UC_MODERATOR)
{
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        print("<br />");
        print("<div class='btn' align='center'><a href='controlpanel.php?addaction=addtools'>Add New Options:</a></div><br /><br />");
        print("<div class='btn' align='center'><a href='controlpanel.php?listaction=list'>View All Inactive Files:</a></div><br /><br />");
        print("<table border='1' align='center' width='40%' cellspacing='0' cellpadding='5'>");
    }
    else
    {
        print("<table border='1' align='center' width='30%' cellspacing='0' cellpadding='5'>");
    }

    //-- Start Tracker Manager Output --//
    if (get_user_id() == UC_TRACKER_MANAGER)
    {
        print("<tr>
               <td class='colhead' align='center' colspan='4'>Tracker Managers Control Panel</td>
               </tr>");

        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=1 AND max_class=7" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $id    = $row['id'];
            $name  = $row['name'];
            $url   = $row['url'];
            $image = $row['image'];

            print("<tr>
                   <td class='rowhead' width='40' height='40'><img src='".$image_dir."admin/".$row['image']."' width='40' height='40' border='0' alt='File Image' title='File Image' /></td>
                   <td class='rowhead'><a href='controlpanel.php?fileaction=$id'>$name</a></td>
                   <td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?edittrakmanid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."/admin/edit.png' width='16' height='16' border='0' alt='Edit Item' title='Edit Item' /></a></div></td>
                   <td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?deltrakmanid=$id&amp;mod=$name'><img src='".$image_dir."admin/delete.png' width='16' height='16' border='0' alt='Delete Item' title='Delete Item' /></a></div></td>
                   </tr>");
        }
    }
    //-- Finish Tracker Manager Output --//

    //-- Start Sysop Output --//
    if (get_user_class() >= UC_SYSOP)
    {
        print("<tr>
               <td class='colhead' align='center' colspan='4'>Sysops Control Panel</td>
               </tr>");

        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=1 AND max_class=6" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {

            $id    = $row['id'];
            $name  = $row['name'];
            $url   = $row['url'];
            $image = $row['image'];

            print("<tr>
                   <td class='rowhead' width='48' height='48'><img src='".$image_dir."admin/".$row['image']."' width='48' height='48' border='0' alt='File Image' title='File Image' /></td>
                   <td class='rowhead'><a href='controlpanel.php?fileaction=$id'>$name</a></td>");

        if (get_user_id() == UC_TRACKER_MANAGER)
        {
            print("<td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?editsysopid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."/admin/edit.png' width='16' height='16' border='0' alt='Edit' title='Edit' /></a></div></td>
                   <td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?delsysopid=$id&amp;mod=$name'><img src='".$image_dir."admin/delete.png' width='16' height='16' border='0' alt='Delete' title='Delete' /></a></div></td>");
        }
            print("</tr>");
        }
    }
    //-- Finish Sysop Output --//

    //-- Start Admin Output --//
    if (get_user_class() >= UC_ADMINISTRATOR)
    {
        print("<tr>
               <td class='colhead' align='center' colspan='4'>Administrators Control Panel</td>
               </tr>");

        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=1 AND max_class=5" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $id    = $row['id'];
            $name  = $row['name'];
            $url   = $row['url'];
            $image = $row['image'];

            print("<tr>
                   <td class='rowhead' width='40' height='40'><img src='".$image_dir."admin/".$row['image']."' width='40' height='40' border='0' alt='File Image' title='File Image' /></td>
                   <td class='rowhead'><a href='controlpanel.php?fileaction=$id'>$name</a></td>");

            if (get_user_id() == UC_TRACKER_MANAGER)
            {
                print("<td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?editadminid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."/admin/edit.png' width='16' height='16' border='0' alt='Edit Item' title='Edit Item' /></a></div></td>
                   <td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?deladminid=$id&amp;mod=$name'><img src='".$image_dir."admin/delete.png' width='16' height='16' border='0' alt='Delete Item' title='Delete Item' /></a></div></td>");
            }
                print("</tr>");
        }
    }
    //-- Finish Admin Output --//

    //-- Start Mod Output --//
    {
        print("<tr>
               <td class='colhead' align='center' colspan='4'>Moderators Control Panel</td>
               </tr>");

        $query = "SELECT *
                    FROM controlpanel
                    WHERE status=1 AND max_class=4" or sqlerr(__FILE__, __LINE__);

        $sql = sql_query($query);

        while ($row = mysql_fetch_array($sql))
        {
            $id    = $row['id'];
            $name  = $row['name'];
            $url   = $row['url'];
            $image = $row['image'];

            print("<tr>
                   <td class='rowhead' width='40' height='40'><img src='".$image_dir."admin/".$row['image']."' width='40' height='40' border='0' alt='File Image' title='File Image' /></td>
                   <td class='rowhead'><a href='controlpanel.php?fileaction=$id' >$name</a></td>");

            if (get_user_id() == UC_TRACKER_MANAGER)
            {
                print("<td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?editmodid=$id&amp;name=$name&amp;url=$url&amp;image=$image'><img src='".$image_dir."/admin/edit.png' width='16' height='16' border='0' alt='Edit Item' title='Edit Item' /></a></div></td>
                   <td class='rowhead' width='40' height='40'><div align='center'><a href='".$_SERVER['PHP_SELF']."?delmodid=$id&amp;mod=$name'><img src='".$image_dir."admin/delete.png' width='16' height='16' border='0' alt='Delete Item' title='Delete Item' /></a></div></td>");
            }
            print("</tr>");
        }
    }
//-- Finish Mod Output --//

//-- Finish Tool List --//
    print("</table><br />");

}
//-- Finish Output View --//

site_footer();
?>