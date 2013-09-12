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

         <link rel="stylesheet" type="text/css" href="/errors/error-style.css" />
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

if (($actiong == "edit" || $actiong == "delete") && $_GET["sid"] == 0)
{
    error_message("error", "Error", "Missing Argument Stylesheet ID");
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

    //-- Start Add Stylesheet --//
    if ($action == "add")
    {
        $name = htmlentities($_POST["sname"]);

        if (empty($name))
        {
            error_message("error", "Error", "Missing Stylesheet Name!");
        }

        $uri = htmlentities($_POST["suri"]);

        if (empty($uri))
        {
            error_message("error", "Error", "Missing Stylesheet CSS!");
        }

        $add = sql_query("INSERT INTO stylesheets (name ,uri)
                            VALUES (".sqlesc($name).", ".sqlesc($uri).") ") or sqlerr(__FILE__, __LINE__);

        if ($add)
        {
            write_stafflog("".$CURUSER["username"]." Added Stylesheet ".$name["sname"]."");

            error_message_center("success", "Success", "<strong>New Stylesheet Created.</strong><br />
                                                        <br />Return To <a href='controlpanel.php?fileaction=7'>Stylesheets</a>
                                                        <br />Return To <a href='controlpanel.php'>Staff Tools</a>
                                                        <br />Return To <a href='index.php'>Main Page</a>");
        }

    }
    //-- End Add Stylesheet --//

    //-- Start Edit Stylesheet --//
    if ($action == "edit")
    {
        $sid        = (isset($_POST["sid"]) ? 0 + $_POST["sid"] : "");
        $sname_edit = htmlentities($_POST["sname_edit"]);

        if (empty($sname_edit))
        {
            error_message("error", "Error", "Missing Stylesheet Name!");
        }

        $suri_edit = htmlentities($_POST["suri_edit"]);

        if (empty($suri_edit))
        {
            error_message("error", "Error", "Missing Stylesheet CSS!");
        }

        $edit = sql_query("UPDATE stylesheets
                            SET name=".sqlesc($sname_edit).", uri=".sqlesc($suri_edit)."
                            WHERE id=".sqlesc($sid)." ") or sqlerr(__FILE__, __LINE__);

        if ($edit)
        {
            error_message_center("success", "Success", "<strong>Stylesheet Successfully Edited!</strong><br />
                                                        <br />Return To <a href='controlpanel.php?fileaction=7'>Stylesheets</a>
                                                        <br />Return To <a href='controlpanel.php'>Staff Tools</a>
                                                        <br />Return To <a href='index.php'>Main Page</a>");
        }
    }
    //-- End Edit Stylesheet --//
}

//-- Start Edit Stylesheet Form --//
if ($actiong == "edit")
{
    $styid = (isset($_GET["sid"]) ? 0 + $_GET["sid"] : "");

    site_header("Edit Stylesheet");

    $res = sql_query("SELECT id,name, uri
                        FROM stylesheets
                        WHERE id=".sqlesc($styid)."
                        LIMIT 1 ") or sqlerr(__FILE__, __LINE__);

    $arr   = mysql_fetch_assoc($res);
    $sname = htmlentities($arr["name"]);
    $suri  = htmlentities($arr["uri"]);

    write_stafflog("".$CURUSER["username"]." Edited Stylesheet ".$arr["name"]."");

    begin_frame("Edit Stylesheet");

    print('<form action="controlpanel.php?fileaction=7" method="post">');
    print('<table class="main" border="1" align="center" cellspacing="0" cellpadding="5">');

    print('<tr>
            <td class="colhead"><label for="sname_edit">Style Name:</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="sname_edit" size="50" id="sname_edit" value="'.$sname."\" onclick=\"select()\" />
            </td>
        </tr>");

    print('<tr>
            <td class="colhead"><label for="suri_edit">Style CSS:</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="suri_edit" id="suri_edit" size="50" value="'.$suri."\" onclick=\"select()\" />
            </td>
        </tr>");

    print('<tr>
            <td class="std" align="center" colspan="2">
                <input type="submit" class="btn" name="submit" value="Edit Stylesheet" />
                <input type="hidden" name="action" value="edit" />
                <input type="hidden" name="sid" value="'.$arr["id"]."\" />
            </td>
        </tr>");

    print("</table>");
    print("</form>");

    end_frame();
    site_footer();

}
//-- Start Delete Existing Stylesheet --//
elseif ($actiong == "delete")
{
    $styid = (isset($_GET["sid"]) ? 0 + $_GET["sid"] : "");

    $res = sql_query("SELECT id, name
                        FROM stylesheets
                        WHERE id=".sqlesc($styid)."") or sqlerr(__FILE__, __LINE__);

    $arr      = mysql_fetch_assoc($res);
    $count    = mysql_num_rows($res);
    $returnto = isset($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : '';
    $sure     = isset($_GET["sure"]) ? (int) $_GET['sure'] : 0;

    if (!$sure)
    {
        error_message("warn", "Warning", "<a href='controlpanel.php?fileaction=7&amp;action=delete&amp;sid=".$arr["id"].";returnto=$returnto&amp;sure=1'>Do you really want to Delete this Stylesheet?  Click if you are sure?</a>");
    }

    if ($count == 1)
    {
        $delete = sql_query("DELETE
                               FROM stylesheets
                               WHERE id=".sqlesc($styid)."") or sqlerr(__FILE__, __LINE__);

        if ($delete)
        {
            write_stafflog("".$CURUSER["username"]." Deleted Stylesheet ".$arr["name"]."");

            error_message_center("success", "Success", "<strong>Stylesheet Successfully Deleted!</strong><br />
                                                        <br />Return To <a href='controlpanel.php?fileaction=7'>Stylesheets</a>
                                                        <br />Return To <a href='controlpanel.php'>Staff Tools</a>
                                                        <br />Return To <a href='index.php'>Main Page</a>");
        }
    }
    else
    {
        error_message("error", "Error", "No Stylesheet with that ID!");
    }

}
//-- End Delete Existing Stylesheet --//
else
{
    site_header("Stylesheets",false);

    //-- Start Add Stylesheet Form --//
    begin_frame("Add A Stylesheet");

    print('<form action="controlpanel.php?fileaction=7" method="post">');
    print('<table class="main" border="1" align="center" cellspacing="0" cellpadding="5">');

    print('<tr>
            <td class="colhead"><label for="suri">Style CSS:</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="suri" id="suri" size="50" />
            </td>
        </tr>');

    print('<tr>
            <td class="colhead"><label for="sname">Style Name:</label></td>
            <td class="rowhead" align="left">
                <input type="text" name="sname" id="sname" size="50" />
            </td>
        </tr>');

    print('<tr>
            <td align="center" colspan="2">
                <input type="submit" name="submit" value="Add Stylesheet" class="btn" />
                <input type="hidden" name="action" value="add" />
            </td>
        </tr>');

    print("</table>");
    print("</form>");

    end_frame();
    //-- End Add Stylesheet Form --//

    //-- Start Display Existing Stylesheets --//
    begin_frame("Existing Sylesheets");

    $res = sql_query("SELECT id, uri, name
                        FROM stylesheets
                        ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);

    $count = mysql_num_rows($res);

    if ($count > 0)
    {
        print("<table class='main' border='1' align='center' cellspacing='0' cellpadding='5'>");
        print("<tr>");
        print("<td class='colhead'>Style ID:</td>");
        print("<td class='colhead'>Style CSS:</td>");
        print("<td class='colhead'>Style Name:</td>");
        print("<td class='colhead' align='center' colspan='2'>Action</td>");
        print("</tr>");

        while ($arr = mysql_fetch_assoc($res))
        {
            $edit = "<a href='controlpanel.php?fileaction=7&amp;action=edit&amp;sid=".$arr["id"]."'><img src='".$image_dir."edit.png' width='16' height='16' border='0' alt='Edit Stylesheet' title='Edit Stylesheet' style='border:none;padding:3px;' /></a>";

            $delete = "<a href='controlpanel.php?fileaction=7&amp;action=delete&amp;sid=".$arr["id"]."'><img src='".$image_dir."delete.png' width='16' height='16' border='0' alt='Delete Stylesheet' title='Delete Stylesheet' style='border:none;padding:3px;' /></a>";

            print("<tr>");
            print("<td class='rowhead' align='center'>".$arr["id"]."</td>");
            print("<td class='rowhead' align='center'>".$arr["uri"]."</td>");
            print("<td class='rowhead' align='center'>".$arr["name"]."</td>");
            print("<td class='rowhead' align='center'>$edit</td>");
            print("<td class='rowhead' align='center'>$delete</td>");
            print("</tr>");
        }

        print("</table>");

    }
    else
    {
        display_message("info", "Sorry", "No Stylesheets were found!");
    }

    end_frame();
    //-- End Display Existing Stylesheets --//

    site_footer();
}

?>