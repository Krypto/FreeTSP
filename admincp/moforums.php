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

//-- Presets --//
$act    = $_GET['act'];
$id     = 0 + $_GET['id'];
$action = $_GET['action'];

if (!$act)
{
    $act = "forum";
}

//-- Delete Forum Action --//
if ($act == "del")
{
    if (!$id)
    {
        error_message("error", "Error", "There is no Overforum with that ID.");
    }

    sql_query("DELETE
                FROM overforums
                WHERE id = $id") or sqlerr(__FILE__, __LINE__);

    error_message_center("info", "Success", "The Overforum has been Successfully Deleted.
                                     <br />Return to the <a href='controlpanel.php?fileaction=14'>Over Formum Manager</a>
                                     <br />Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                     <br />Return to the <a href='index.php'>Main Page</a>");

}

//-- Edit Forum Action --//
if ($_POST['action'] == "editforum")
{
    $name = $_POST['name'];
    $desc = $_POST['desc'];

    if (!$name && !$desc && !$id)
    {
        error_message("error", "Error", "The Overforum must have a Name and a Description.");
    }

    sql_query("UPDATE overforums
                SET sort = '".$_POST['sort']."', name = ".sqlesc($_POST['name']).", description = ".sqlesc($_POST['desc']).", forid = 0, minclassview = '".$_POST['viewclass']."'
                WHERE id = '".$_POST['id']."'") or sqlerr(__FILE__, __LINE__);

    error_message_center("info", "Success", "The overforum has been Successfully Editted.
                                     <br />Return to the <a href='controlpanel.php?fileaction=14'>Over Formum Manager</a>
                                     <br />Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                     <br />Return to the <a href='index.php'>Main Page</a>");

}

//--Add Forum Action --//
if ($_POST['action'] == "addforum")
{
    $name = trim($_POST['name']);
    $desc = trim($_POST['desc']);

    if (!$name && !$desc)
    {
        error_message("error", "Error", "The Overforum must have a Name and a Description.");
    }

    sql_query("INSERT INTO overforums (sort, name,  description,  minclassview, forid)
                VALUES(".$_POST['sort'].", ".sqlesc($_POST['name']).", ".sqlesc($_POST['desc']).", ".$_POST['viewclass'].", 1)") or sqlerr(__FILE__, __LINE__);

    error_message_center("info", "Success", "The Overforum has been Successfully Created.
                                     <br />Return to the <a href='controlpanel.php?fileaction=14'>Over Formum Manager</a>
                                     <br />Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                     <br />Return to the <a href='index.php'>Main Page</a>");
}

site_header("Overforum Edit",false);

if ($act == "forum")
{
    //-- Show Forums With Forum Managment Tools --//
    begin_frame("Overforums");
    ?>
    <script type='text/javascript'>
    <!--
    function confirm_delete(id)
    {
        if (confirm('Are you sure you want to Delete this Overforum?'))
        {
            self.location.href = 'controlpanel.php?fileaction=14&act=del&id=' +id;
        }
    }
    //-->
</script>

<?php

    $result = sql_query("SELECT *
                            FROM overforums
                            ORDER BY SORT ASC");

    if ($row = mysql_fetch_array($result))
    {
        do
        {
            echo '<table border="0" width="100%" align="center" cellpadding="2" cellspacing="0">';

            echo "<tr>
                    <td class='colhead' align='left'>Name</td>
                    <td class='colhead'>Viewed By</td>
                    <td class='colhead'>Modify</td>
                </tr>";

            echo "<tr>
                    <td class='rowhead'>
                        <a href='controlpanel.php?fileaction=14&amp;action=forumview&amp;forid=".$row["id"]."'>
                        <span style='font-weight:bold;'>".$row["name"]."</span></a><br />".$row["description"]."
                    </td>";

            echo "<td class='rowhead'>".get_user_class_name($row["minclassview"])."</td>
                    <td align='center'><span style='font-weight:bold;'>
                        <a href='controlpanel.php?fileaction=14&amp;act=editforum&amp;id=".$row["id"]."'>Edit</a>
                        &nbsp;|&nbsp;<a href='javascript:confirm_delete(".$row["id"].");'><span style='color : #ff0000;'>Delete</span></a></span>
                    </td>
                </tr>";
        }
        while ($row = mysql_fetch_array($result));

        echo "</table>";
    }
    else
    {
        display_message_center("info", "Sorry", "No Records were Found!");
    }

    ?>

<br /><br />
<form method='post' action='controlpanel.php?fileaction=14&amp;action=addforum'>
    <table border='0' align='center' width='100%' cellspacing='0' cellpadding='3'>
        <tr align='center'>
            <td class='colhead' colspan='2'>Make New Over Forum</td>
        </tr>
        <tr>
            <td class='rowhead'><span style='font-weight:bold;'><label for='name'>Overforum Name</label></span></td>
            <td class='rowhead'><input type="text" name="name" id="name" size="20" maxlength="60" /></td>
        </tr>
        <tr>
            <td class='rowhead'><span style='font-weight:bold;'><label for='desc'>Overforum Description</label></span></td>
            <td class='rowhead'>
                <input type="text" name="desc" id="desc" size="30" maxlength="200" />
            </td>
        </tr>

        <tr>
            <td class='rowhead'><span style='font-weight:bold;'>Minimun View Permission</span></td>
            <td class='rowhead'>
                <select name='viewclass'>

                    <?php

                    $maxclass = get_user_class();

                    for ($i = 0;
                         $i <= $maxclass;
                         ++$i)

                    {
                        print("<option value='$i'".($user["class"] == $i ? " selected='selected'" : "").">$prefix".get_user_class_name($i)."</option>\n");
                    }

                    ?>

                </select>
            </td>
        </tr>
        <tr>
            <td class='rowhead'><span style='font-weight:bold;'>Overforum Rank</span></td>
            <td class='rowhead'>
                <select name='sort'>

                    <?php

                    $res = sql_query("SELECT sort
                                        FROM overforums");

                    $nr       = mysql_num_rows($res);
                    $maxclass = $nr + 1;

                    for ($i = 0;
                         $i <= $maxclass;
                         ++$i)

                    {
                        print("<option value='$i'>$i </option>\n");
                    }

                    ?>

                </select>
            </td>
        </tr>
        <tr align="center">
            <td class='rowhead' colspan="2">
                <input type="hidden" name="action" value="addforum" />
                <input type="submit" class="btn" name="Submit" value="Make Overforum" />
            </td>
        </tr>
    </table>
</form>

<?php

    print("<table  border='0' align='center' width='100%' cellspacing='0' cellpadding='3'>
        <tr>
            <td class='rowhead' align='center' colspan='1' height='20px'>
                <a href='controlpanel.php?fileaction=13'>
				<input type='submit' class='btn' value='Forum Manager' />
                </a>
            </td>
        </tr>
    </table>\n");

    end_frame();
}

if ($act == "editforum")
{
    //--Edit Page For The Forums --//
    $id = 0 + $_GET["id"];

    begin_frame("Edit Overforum");

    $result = sql_query("SELECT *
                            FROM overforums
                            WHERE id = '$id'");

    if ($row = mysql_fetch_array($result))
    {
        //-- Get OverForum Name - To Be Written --//
        do
        {
        ?>
        <form method='post' action="<?php $_SERVER["PHP_SELF"];?>">
            <table border="0" align="center" width="100%" cellspacing="0" cellpadding="3">
                <tr align="center">
                    <td class='colhead' colspan="2">Edit Overforum: <?php echo $row["name"];?></td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'><label for='name'>Overforum Name</label></span>
                    </td>
                    <td class='rowhead'>
                        <input type="text" name="name" id="name" size="20" maxlength="60" value="<?php echo $row["name"];?>" />
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'><label for='desc'>Overforum Description</label></span>
                    </td>
                    <td class='rowhead'>
                        <input type="text" name="desc" id="desc" size="30" maxlength="200" value="<?php echo $row["description"];?>" />
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'>Minimun View Permission</span></td>
                    <td class='rowhead'>
                        <select name='viewclass'>

                            <?php

                            $maxclass = get_user_class();

                            for ($i = 0;
                                 $i <= $maxclass;
                                 ++$i)

                            {
                                print("<option value='$i'".($row["minclassview"] == $i ? " selected='selected'" : "").">$prefix".get_user_class_name($i)."</option>\n");
                            }

                            ?>

                        </select>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'>Overforum Rank</span></td>
                    <td class='rowhead'>
                        <select name='sort'>

                            <?php

                            $res = sql_query("SELECT sort
                                                FROM overforums");

                            $nr       = mysql_num_rows($res);
                            $maxclass = $nr + 1;

                            for ($i = 0;
                                 $i <= $maxclass;
                                 ++$i)

                            {
                                print("<option value='$i'".($row["sort"] == $i ? " selected='selected'" : "").">$i </option>\n");
                            }

                            ?>
                        </select>
                    </td>
                </tr>
                <tr align="center">
                    <td colspan="2">
                        <input type="hidden" name="action" value="editforum" />
                        <input type="hidden" name="id" value="<?php echo $id;?>" />
                        <input type="submit" class="btn" "name="Submit" value="Edit Overforum" />
                    </td>
                </tr>
            </table>
        </form>

        <?php
        }
        while ($row = mysql_fetch_array($result));
    }
    else
    {
        display_message_center("info", "Sorry", "No Records were Found!  Return to the <a href='controlpanel.php'>Staff Control Panel</a>");
    }

    end_frame();
}
?>

<?php

echo("<br />");

site_footer();
?>