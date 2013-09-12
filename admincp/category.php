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

//-- Credits to putyn

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

$vactg   = array("delete",
                 "edit",
                 "");

$actiong = (isset($_GET["action"]) ? $_GET["action"] : "");

if (!in_array($actiong, $vactg))
{
    error_message("error", "Error", "Not an Valid Action!");
}

if (($actiong == "edit" || $actiong == "delete") && $_GET["cid"] == 0)
{
    error_message("error", "Error", "Missing Argument Category ID");
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $vaction = array("edit",
                     "add",
                     "delete");

    $action = ((isset($_POST["action"]) && in_array($_POST["action"], $vaction)) ? $_POST["action"] : "");

    if (!$action)
    {
        error_message("error", "Error", "Something Missing");
    }

    //-- Start Add Action --//
    if ($action == "add")
    {
        $name = htmlentities($_POST["cname"]);

        if (empty($name))
        {
            error_message("error", "Error", "Missing Category Name!");
        }

        $image = htmlentities($_POST["cimage"]);

        if (empty($image))
        {
            error_message("error", "Error", "Missing Category Image!");
        }

        $add = sql_query("INSERT INTO categories (name ,image)
                            VALUES (".sqlesc($name).", ".sqlesc($image).") ") or sqlerr(__FILE__, __LINE__);

        if ($add)
        {
            error_message_center("success", "Success", "<strong>New Category Created.</strong><br />
                                                 <br /> Return To <a href='controlpanel.php?fileaction=8'>Catergoies</a>
                                                 <br /> Return To <a href='controlpanel.php'>Staff Tools</a>
                                                 <br /> Return To <a href='index.php'>Main Page</a>");
        }
    }
    //-- End Add Action --//

    //-- Start Edit Action --//
    if ($action == "edit")
    {
        $cid        = (isset($_POST["cid"]) ? 0 + $_POST["cid"] : "");
        $cname_edit = htmlentities($_POST["cname_edit"]);

        if (empty($cname_edit))
        {
            error_message("error", "Error", "Missing Category Name!");
        }

        $cimage_edit = htmlentities($_POST["cimage_edit"]);

        if (empty($cimage_edit))
        {
            error_message("error", "Error", "Missing Category Image!");
        }

        $edit = sql_query("UPDATE categories
                            SET name=".sqlesc($cname_edit).", image=".sqlesc($cimage_edit)."
                            WHERE id=".sqlesc($cid)." ") or sqlerr(__FILE__, __LINE__);

        if ($edit)
        {
            error_message_center("success", "Success", "<strong>Category Successfully Edited!</strong><br />
                                                 <br /> Return To <a href='controlpanel.php?fileaction=8'>Catergories</a>
                                                 <br /> Return To <a href='controlpanel.php'>Staff Tools</a>
                                                 <br /> Return To <a href='index.php'>Main Page</a>");
        }
    }
    //-- End Edit Action --//
}

//-- Start Edit Existing Categories --//
if ($actiong == "edit")
{
    $catid = (isset($_GET["cid"]) ? 0 + $_GET["cid"] : "");

    site_header("Edit Category");

    $res = sql_query("SELECT id,name, image
                        FROM categories
                        WHERE id=".sqlesc($catid)."
                        LIMIT 1 ") or sqlerr(__FILE__, __LINE__);

    $arr    = mysql_fetch_assoc($res);
    $cname  = htmlentities($arr["name"]);
    $cimage = htmlentities($arr["image"]);

    begin_frame("Edit Category");

    print('<form action="controlpanel.php?fileaction=8" method="post">');
    print('<table class="main" border="1" cellspacing="0" align="center" cellpadding="5">');

    print('<tr>
            <td class="colhead"><label for="cname_edit">Cat Name</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="cname_edit" size="50" id="cname_edit" value="'.$cname."\" onclick=\"select()\" />
            </td>
        </tr>");

    print('<tr>
            <td class="colhead"><label for="cimage_edit">Cat Image</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="cimage_edit" id="cimage_edit" size="50" value="'.$cimage."\" onclick=\"select()\" />
            </td>
        </tr>");

    print('<tr>
            <td class="std" align="center" colspan="2">
                <input type="submit" class="btn" name="submit" value="Edit Category" />
                <input type="hidden" name="action" value="edit" />
                <input type="hidden" name="cid" value="'.$arr["id"]."\" />
            </td>");

    print("</tr>");
    print("</table>");
    print("</form>");

    end_frame();
    site_footer();
}
//-- End Edit Existing Categories --//

//-- Start Delete Existing Categories --//
elseif ($actiong == "delete")
{
    $catid = (isset($_GET["cid"]) ? 0 + $_GET["cid"] : "");

    $res = sql_query("SELECT id, name
                        FROM categories
                        WHERE id=".sqlesc($catid)."") or sqlerr(__FILE__, __LINE__);

    $arr      = mysql_fetch_assoc($res);
    $count    = mysql_num_rows($res);
    $returnto = isset($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : '';
    $sure     = isset($_GET["sure"]) ? (int) $_GET['sure'] : 0;

    if (!$sure)
    {
        error_message("warn", "Warning", "<a href='controlpanel.php?fileaction=8&amp;action=delete&amp;cid=".$arr["id"].";returnto=$returnto&amp;sure=1'>Do you really want to Delete this Category?  Click if you are sure?</a>");
    }

    if ($count == 1)
    {
        $delete = sql_query("DELETE
                                FROM categories
                                WHERE id=".sqlesc($catid)."") or sqlerr(__FILE__, __LINE__);

        if ($delete)
        {
            write_log("".$CURUSER["username"]." Deleted Category ".$arr["name"]."");
            error_message_center("success", "Success", "<strong>Category Successfully Deleted!</strong><br />
                                                 <br /> Return To <a href='controlpanel.php?fileaction=8'>Catergories</a>
                                                 <br /> Return To <a href='controlpanel.php'>Staff Tools</a>
                                                 <br /> Return To <a href='index.php'>Main Page</a>");
        }
    }
    else
    {
        error_message("error", "Error", "No Category with that ID!");
    }
}
//-- End Delete Existing Categories --//
else
{
    site_header("Categories");

    //-- Start Add New Category Form --//
    begin_frame("Add a Category");

    print('<form action="controlpanel.php?fileaction=8" method="post">');
    print('<table class="main" border="1" cellspacing="0" align="center" cellpadding="5">');

    print('<tr>
            <td class="colhead"><label for="cname">Cat Name</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="cname" id="cname" size="50" />
            </td>
        </tr>');

    print('<tr>
            <td class="colhead"><label for="cimage">Cat Image</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="cimage" id="cimage" size="50" />
            </td>
        </tr>');

    print('<tr>
            <td align="center" colspan="2">
                <input type="submit" class="btn" name="submit" value="Add Category" />
                <input type="hidden" name="action" value="add" />
            </td>');
    print("</tr>");

    print("</table>");
    print("</form>");

    end_frame();
    //-- End Add New Category Form --//

    //-- Start Display Existing Categories --//
    begin_frame("Categories");

    $res = sql_query("SELECT id, name, image
                        FROM categories
                        ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);

    $count = mysql_num_rows($res);

    if ($count > 0)
    {
        print("<table class='main' border='1' align='center' cellspacing='0' cellpadding='5'>");
        print("<tr>");
        print("<td class='colhead'>ID</td>");
        print("<td class='colhead'>Cat Name</td>");
        print("<td class='colhead'>Cat Image</td>");
        print("<td class='colhead' align='center' colspan='2'>Action</td>");
        print("</tr>");

        while ($arr = mysql_fetch_assoc($res))
        {
            $edit = "<a href='controlpanel.php?fileaction=8&amp;action=edit&amp;cid=".$arr["id"]."'><img src='".$image_dir."edit.png' width='16' height='16' border='0' alt='Edit Category' title='Edit Category' style='border:none;padding:3px;' /></a>";

            $delete = "<a href='controlpanel.php?fileaction=8&amp;action=delete&amp;cid=".$arr["id"]."'><img src='".$image_dir."delete.png' width='16' height='16' border='0' alt='Drop Category' title='Drop Category' style='border:none;padding:3px;' /></a>";

            print("<tr>");
            print("<td class='rowhead' align='center'><a href='browse.php?cat=".$arr["id"]."'>".$arr["id"]."</a></td>");
            print("<td class='rowhead' align='center'><a href='browse.php?cat=".$arr["id"]."'>".$arr["name"]."</a></td>");
            print("<td class='rowhead' align='center'><a href='browse.php?cat=".$arr["id"]."'><img src='".$image_dir."caticons/".$arr["image"]."' width='60' height='54' border='0' alt='".$arr["name"]."' title='".$arr["name"]."'/></a></td>");
            print("<td class='rowhead' align='center'>$edit</td>");
            print("<td class='rowhead' align='center'>$delete</td>");
            print("</tr>");
        }
        print("</table>");
    }
    else
    {
        display_message("info", "Sorry", "No Categories were found!");
    }

    end_frame();
    //-- End Display Existing Categories --//

    site_footer();
}

?>