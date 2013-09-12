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

    $posted_action = strip_tags((isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '')));

    //-- Add All Possible Actions Here And Check Them To Be Sure They Are Ok --//
    $valid_actions = array(
                            'add_new_credit',
                            'edit_credit',
                            'update_credit',
                            'delete_credit',
                            'delete_credit_yes');

    //-- Check Posted Action, And If No Action Was Posted, Show The Default Page --//
    $action = (in_array(
                        $posted_action,
                        $valid_actions) ? $posted_action : 'default');

switch ($action)
{
//-- Start Add New Credit --//
case 'add_new_credit';

    $name        = ($_POST['name']);
    $description = ($_POST['description']);
    $category    = ($_POST['category']);
    $link        = ($_POST['link']);
    $status      = ($_POST['status']);
    $credit      = ($_POST['credit']);
    $modified    = ($_POST['modified']);

    if (!$name)
    {
         error_message_center("error", "Error", "You forgot to enter a Name!");
    }

    if (!$description)
    {
         error_message_center("error", "Error", "You forgot to enter a Description!");
    }

    if (!$link)
    {
         error_message_center("error", "Error", "You forgot to enter a Link!");
    }

    if (!$credit)
    {
         error_message_center("error", "Error", "You forgot to give Credit to the Original Coder!");
    }

    if (!$modified)
    {
         error_message_center("error", "Error", "You forgot to give Credit to the person who Modified the Code for FreeTSP Source!");
    }

    sql_query("INSERT INTO modscredits (name, description,  category,  mod_link,  status, credit, modified)
                VALUES(".sqlesc($name).", ".sqlesc($description).", ".sqlesc($category).", ".sqlesc($link).", ".sqlesc($status).", ".sqlesc($credit).", ".sqlesc($modified).")") or sqlerr(__FILE__, __LINE__);

    display_message_center("info", "Success", "The New Mod Credit Has Been Created View <a href='credits.php'>HERE</a><br />
                                               Return To The <a href='controlpanel.php?fileaction=29&amp;action=default'>Credits Page</a> And Add More Credits<br />
                                               Return To The <a href='index.php'>Main Page</a>");

    site_footer();
    die();

break;

//-- Start Edit A Credit --//
case 'edit_credit';

    $id = (int)$_GET["id"];

    $res = sql_query("SELECT name, description, category, mod_link, status, credit, modified
                        FROM modscredits
                        WHERE id = $id") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 0)
    {
        error_message_center("error", "Error", "Invalid ID!");
    }

    while($mod = mysql_fetch_assoc($res))
    {
        print("<form method='post' action='controlpanel.php?fileaction=29&amp;action=update_credit&amp;id=$id'>
                <input type='hidden' name='action' value='add' />");

        print("<table border='1' align='center' width='70%' cellpadding='8' cellspacing='0'>");

        print("<tr>
                <td class='colhead' align='center' colspan='2'>Edit Modification Credits</td>
             </tr>");

        print("<tr>
                <td class='rowhead'>Mod Name</td>
                <td class='rowhead'>
                    <input type='text' size='60' maxlength='120' name='name' value='".htmlspecialchars($mod['name'])."' />
                </td>
             </tr>");

        print("<tr>
                <td class='rowhead'>Description</td>
                <td class='rowhead'>
                    <input type='text' size='60' maxlength='120' name='description' value='".htmlspecialchars($mod['description'])."' />
                </td>
             </tr>");

        print("<tr>
                <td class='rowhead'>Status</td>
                <td class='rowhead'>
                    <select name='modcategory'>");

        $result = sql_query("SHOW COLUMNS
                              FROM modscredits
                              WHERE field='category'");

            while ($row=mysql_fetch_row($result))
            {
                foreach(explode("','",substr($row[1],6,-2)) AS $y)
                {
                    print("<option value='$y'".($mod["category"] == $y ? " selected='selected'" : "").">$y</option>");
                }
            }

        print("</select>
               </td>
               </tr>");

        print("<tr>
                <td class='rowhead'>Link</td>
                <td class='rowhead'>
                    <input type='text' size='60' maxlength='120' name='link' value='".htmlspecialchars($mod['mod_link'])."' />
                </td>
             </tr>");

        print("<tr>
                <td class='rowhead'>Status</td>
                <td class='rowhead'>
                    <select name='modstatus'>");

        $result = sql_query("SHOW COLUMNS
                              FROM modscredits
                              WHERE field='status'");

            while ($row=mysql_fetch_row($result))
            {
                foreach(explode("','",substr($row[1],6,-2)) AS $y)
                {
                    print("<option value='$y'".($mod["status"] == $y ? " selected='selected'" : "").">$y</option>");
                }
            }

        print("</select>
               </td>
               </tr>");

        print("<tr>
                <td class='rowhead'>Original Coder</td>
                <td class='rowhead'>
                    <input type='text' size='60' maxlength='120' name='credits' value='".htmlspecialchars($mod['credit'])."' />
                </td>
            </tr>");

        print("<tr>
                <td class='rowhead'>Modified By</td>
                <td class='rowhead'>
                    <input type='text' size='60' maxlength='120' name='modified' value='".htmlspecialchars($mod['modified'])."' />
                </td>
            </tr>");

        print("<tr>
                <td class='rowhead' align='center' colspan='2'>
                    <input type='submit' value='Submit' />
                </td>
            </tr>");

        print("</table>");
        print("</form>");
    }

break;

//-- Start Update A Credit --//
case 'update_credit';

    $id = (int)$_GET["id"];

    if (!is_valid_id($id))
    {
        error_message_center("error", "Error", "Invalid ID!");
    }

    $res = sql_query('SELECT id
                      FROM modscredits
                      WHERE id = '.sqlesc($id));

    if (mysql_num_rows($res) == 0)
    {
        error_message_center("error", "No Mod Credit with that ID!");
    }

    $name        = $_POST['name'];
    $description = $_POST['description'];
    $modcategory = $_POST['modcategory'];
    $link        = $_POST['link'];
    $modstatus   = $_POST['modstatus'];
    $credit      = $_POST['credits'];
    $modified    = $_POST['modified'];

    if (!$name)
    {
        error_message_center("error", "Error", "You forgot to enter a Name!");
    }

    if (!$description)
    {
        error_message_center("error", "Error", "You forgot to enter a Description!");
    }

    if (!$link)
    {
         error_message_center("error", "Error", "You forgot to enter a Link!");
    }

    if (!$credit)
    {
         error_message_center("error","Error", "You forgot to give Credit to the Original Coder!");
    }

    if (!$modified)
    {
        error_message_center("error", "Error", "You forgot to give Credit to the person who Modified the Code for FreeTSP Source!");
    }

    sql_query("UPDATE modscredits
               SET name = ".sqlesc($name).", category = ".sqlesc($modcategory).", status = ".sqlesc($modstatus).",  mod_link = ".sqlesc($link).", credit = ".sqlesc($credit).", modified = ".sqlesc($modified).", description = ".sqlesc($description)."
               WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

    display_message_center("info", "Success", "The New Mod Credit Has Been Updated View <a href='credits.php'>HERE</a><br />
                                               Return To The <a href='controlpanel.php?fileaction=29&amp;action=default'>Credits Page</a><br />
                                               Return To The <a href='index.php'>Main Page</a>");

    //exit();

break;

//-- Start Delete A Credit --//
case 'delete_credit';

    $id = (int)$_GET["id"];

    if (!is_valid_id($id))
    {
        error_message_center("error", "Error", "Invalid ID!");
    }

    $res = sql_query('SELECT id, name
                      FROM modscredits
                      WHERE id = '.sqlesc($id));

    if (mysql_num_rows($res) == 0)
    {
        error_message_center("error", "Error", "No Mod Credit with that ID!");
    }

    while ($arr = mysql_fetch_assoc($res))
    {
        if (is_valid_id($id))
        {
            error_message_center("info", "Sanity Check", "Are you really sure you want to Delete the Credits for :- ".$arr["name"]."!<br />
                                                         <a href='controlpanel.php?fileaction=29&amp;action=delete_credit_yes&amp;id=".$id."'>Yes</a>&nbsp;&nbsp;/&nbsp;&nbsp;
                                                         <a href='credits.php'>No</a>");
        }
    }

break;

case 'delete_credit_yes';

    $id = (int)$_GET["id"];

    sql_query("DELETE
                    FROM modscredits
                    WHERE id = '$id'") or sqlerr(__FILE__, __LINE__);

    error_message_center("info", "Success", "The Credits have been Deleted<br />
                                            Return back to the <a href='credits.php'>Credits Page</a><br />
                                            Go to the <a href='index.php'>Home Page</a>");

break;

//-- Start Default Page --//
case 'default';

    print("<form method='post' action='controlpanel.php?fileaction=29&amp;action=add_new_credit'>
            <input type='hidden' name='action' value='add' />");

    print("<table border='1' align='center' width='90%' cellpadding='8' cellspacing='0'>");

    print("<tr>
            <td class='colhead' align='center' colspan='2'>Add Modification Credits</td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Mod Name:</td>
            <td class='rowhead'>
               <input name='name' type='text' size='120' />
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Description:</td>
            <td class='rowhead'>
                <input name='description' type='text' size='120' />
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Category:</td>
            <td class='rowhead'>
                <select name='category'>
                    <option value='Addon'>Addon</option>
                    <option value='Forum'>Forum</option>
                    <option value='Message/Email'>Message/Email</option>
                    <option value='Display/Style'>Display/Style</option>
                    <option value='Staff/Tools'>Staff/Tools</option>
                    <option value='Browse/Torrent/Details'>Browse/Torrent/Details</option>
                    <option value='Misc'>Misc</option>
                </select>
            </td>
        </tr>");

    print("<tr>
                <td class='rowhead'>Link:</td>
                <td class='rowhead'>
                    <input name='link' type='text' size='120' />
                </td>
            </tr>");

    print("<tr>
                <td class='rowhead'>Status:</td>
                <td class='rowhead'>
                    <select name='status'>
                        <option value='In-Progress'>In-Progress</option>
                        <option value='Complete'>Complete</option>
                    </select>
                </td>
            </tr>");

    print("<tr>
                <td class='rowhead'>Original Coder:</td>
                <td>
                    <input name='credit' type='text' size='120' /><br />
                    <font class='small'>Values separated by commas</font>
                </td>
            </tr>");

    print("<tr>
                <td class='rowhead'>Modified By:</td>
                <td>
                    <input name='modified' type='text' size='120' /><br />
                    <font class='small'>Values separated by commas</font>
                </td>
            </tr>");

    print("<tr>
                <td colspan='2' class='rowhead' align='center'>
                    <input type='submit' value='Add Credit' />
                </td>
            </tr>");

    print("</table>");
    print("</form>");

break;

} //-- End All Actions --//
?>