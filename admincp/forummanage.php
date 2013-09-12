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

$id = 0 + $_GET['id'];

//-- Delete Forum Action --//
if ($_GET['action'] == "del")
{
    if (!$id)
    {
        error_message("error", "Error", "There is no Forum with that ID.");
    }

    $result = sql_query("SELECT *
                            FROM topics
                            WHERE forumid = '$id'");

    if ($row = mysql_fetch_array($result))
    {
        do
        {
            sql_query("DELETE
                        FROM posts
                        WHERE topicid = '$id'") or sqlerr(__FILE__, __LINE__);
        }
        while ($row = mysql_fetch_array($result));
    }

    sql_query("DELETE
                FROM topics
                WHERE forumid = '$id'") or sqlerr(__FILE__, __LINE__);

    sql_query("DELETE
                FROM forums
                WHERE id = '$id'") or sqlerr(__FILE__, __LINE__);

    error_message_center("info", "Success", "<strong>The Forum has been Successfully Deleted.</strong><br />
                                     <br />Return to the <a href='controlpanel.php?fileaction=13'>Formum Manager</a>
                                     <br />Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                     <br />Return to the <a href='index.php'>Main Page</a>");
}

//-- Edit Forum Action --//
if ($_POST['action'] == "editforum")
{
    $name = ($_POST['name']);
    $desc = ($_POST['desc']);

    if (!$name && !$desc && !$id)
    {
        error_message("error", "Error", "There is no Forum with that ID.");
    }

    sql_query("UPDATE forums
                SET sort = '".$_POST['sort']."', name = ".sqlesc($_POST['name']).", description = ".sqlesc($_POST['desc']).", forid = ".sqlesc(($_POST['overforums'])).", minclassread = '".$_POST['readclass']."', minclasswrite = '".$_POST['writeclass']."', minclasscreate = '".$_POST['createclass']."'
                WHERE id = '".$_POST['id']."'") or sqlerr(__FILE__, __LINE__);


    error_message_center("info", "Success", "<strong>The Forum has been Successfully Edited.</strong><br />
                                     <br />Return to the <a href='controlpanel.php?fileaction=13'>Formum Manager</a>
                                     <br />Return to the <a href='controlpanel.php'>Staff Control Panel</a>
                                     <br />Return to the <a href='index.php'>Main Page</a>");

}

//-- Add Forum Action --//
if ($_POST['action'] == "addforum")
{
    $name = ($_POST['name']);
    $desc = ($_POST['desc']);

    if (!$name && !$desc)
    {
        //header("Location: $site_url/forummanage.php");
        //die();
    }

    sql_query("INSERT INTO forums (sort, name,  description,  minclassread,  minclasswrite, minclasscreate, forid)
                VALUES(".$_POST['sort'].", ".sqlesc($_POST['name']).", ".sqlesc($_POST['desc']).", ".$_POST['readclass'].", ".$_POST['writeclass'].", ".$_POST['createclass'].", ".sqlesc(($_POST['overforums'])).")") or sqlerr(__FILE__, __LINE__);

}

//-- Show Forums With Forum Managment Tools --//
site_header("Forum Management Tools",false);

begin_frame("Forums");

?>
<script type='text/javascript'>
    <!--
    function confirm_delete(id)
    {
        if (confirm('Are you sure you want to Delete this Forum?'))
        {
            self.location.href = 'controlpanel.php?fileaction=13&action=del&id=' + id;

        }
    }
    //-->
</script>

<?php

$result = sql_query("SELECT *
                        FROM forums
                        ORDER BY sort ASC");

if (mysql_num_rows($result) == 0)
{
    display_message("info", "Sorry", "No Records were Found!");
}

else
{
    echo "<table width='100%' border='0' align='center' cellpadding='2' cellspacing='0'>";
    echo "<tr>
            <td class='colhead' align='left'>Name</td>
            <td class='colhead'>OverForum</td>
            <td class='colhead'>Read</td>
            <td class='colhead'>Write</td>
            <td class='colhead'>Create Topic</td>
            <td class='colhead'>Modify</td>
        </tr>";

    if ($row = mysql_fetch_array($result))
    {
        do
        {
            $forid = 0 + $row['forid'];

            $res2 = sql_query("SELECT name
                                FROM overforums
                                WHERE id = $forid");

            $arr2 = mysql_fetch_array($res2);
            $name = $arr2['name'];

            echo "<tr>
                    <td class='rowhead'><a href='forums.php?action=viewforum&amp;forumid=".$row["id"]."'><span style='font-weight:bold;'>".$row["name"]."</span></a><br />".$row["description"]."</td>";
            echo "<td class='rowhead'>".$name."</td>
                <td class='rowhead'>".get_user_class_name($row["minclassread"])."</td>
                <td class='rowhead'>".get_user_class_name($row["minclasswrite"])."</td>
                <td class='rowhead'>".get_user_class_name($row["minclasscreate"])."</td>
                <td align='center'><span style='font-weight:bold;'><a href='controlpanel.php?fileaction=13&amp;action=editforum&amp;id=".$row["id"]."'>Edit</a> | <a href='javascript:confirm_delete(".$row["id"].");'><span style='color : #ff0000;'>Delete</span></a></span>
                </td></tr>";

        }
        while ($row = mysql_fetch_array($result));
    }
    echo "</table>";
}
?>

<br /><br />
<form method='post' action='<?php $_SERVER["PHP_SELF"];?>'>
    <table width='100%' border='0' align='center' cellspacing='0' cellpadding='3'>
        <tr align='center'>
            <td colspan='2' class='colhead'>Make New Forum</td>
        </tr>
        <tr>
            <td class='rowhead'><span style='font-weight:bold;'><label for='name'>Forum Name</label></span></td>
            <td class='rowhead'>
                <input type='text' name='name' id='name' size='20' maxlength='60' />
            </td>
        </tr>
        <tr>
            <td class='rowhead'><span style='font-weight:bold;'><label for='desc'>Forum Description</label></span></td>
            <td class='rowhead'>
                <input type='text' name='desc' id='desc' size='30' maxlength='200' />
            </td>
        </tr>
        <tr>
            <td class='rowhead'><span style='font-weight:bold;'>OverForum</span></td>
            <td class='rowhead'>
                <select name='overforums'>

                    <?php

                    $forid = 0 + $row["forid"];
                    $res   = sql_query("SELECT *
                                        FROM overforums");

                    while ($arr = mysql_fetch_array($res))
                    {

                        $name = $arr["name"];
                        $i    = 0 + $arr["id"];

                        print("<option value='$i'".($forid == $i ? " selected='selected'" : "").">$prefix".$name."</option>\n");
                    }

                    ?>

                </select>
            </td>
        </tr>

        <tr>
            <td class='rowhead'><span style='font-weight:bold;'>Minimum Read Permission</span></td>
            <td class='rowhead'>
                <select name='readclass'>

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
            <td class='rowhead'><span style='font-weight:bold;'>Minimum Write Permission</span></td>
            <td class='rowhead'>
                <select name='writeclass'>

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
            <td class='rowhead'><span style='font-weight:bold;'>Minimum Create Topic Permission</span></td>
            <td class='rowhead'>
                <select name='createclass'>

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
            <td class='rowhead'><span style='font-weight:bold;'>Forum Rank</span></td>
            <td class='rowhead'>
                <select name='sort'>

                    <?php

                    $res = sql_query("SELECT sort
                                        FROM forums");

                    $nr = mysql_num_rows($res);

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
        <tr align='center'>
            <td colspan='2'>
                <input type='hidden' name='action' value='addforum' />
                <input type='submit' class='btn' name='Submit' value='Make Forum' />
            </td>
        </tr>
    </table>
</form>

<?php

print("<table width='100%' border='0' align='center' cellspacing='0' cellpadding='3'>
        <tr>
            <td align='center' colspan='1'>
                <a href='controlpanel.php?fileaction=14'>
                  <input type='submit' class='btn' value='Over Forum Manager' />
                </a>
            </td>
        </tr>
</table>\n");

if ($_GET['action'] == "editforum")
{
    //-- Edit Page For The Forums --//

    $id = 0 + ($_GET["id"]);

    begin_frame("Edit Forum");

    $result = sql_query("SELECT *
                            FROM forums
                            WHERE id = ".sqlesc($id));

    if ($row = mysql_fetch_array($result))
    {

        //-- Get OverForum Name - To Be Written --//

        do
        {
            ?>

        <form method='post' action='<?php $_SERVER["PHP_SELF"];?>'>
            <table width='100%' border='0' align='center' cellspacing='0' cellpadding='3'>
                <tr align='center'>
                    <td colspan='2' class='colhead'>Edit Forum: <?php echo htmlentities($row["name"], ENT_QUOTES);?></td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'><label for='name1'>Forum Name</label></span>
                    </td>
                    <td class='rowhead'>
                        <input type='text' name='name' id='name1' size='20' maxlength='60' value='<?php echo htmlentities($row["name"], ENT_QUOTES);?>' />
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'><label for='desc1'>Forum Description</label></span>
                    </td>
                    <td class='rowhead'>
                        <input type='text' name='desc' id='desc1' size='30' maxlength='200' value='<?php echo htmlentities($row["description"], ENT_QUOTES);?>' />
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'>OverForum</span></td>
                    <td class='rowhead'>
                        <select name='overforums'>

                            <?
                            $forid = 0 + $row["forid"];
                            $res   = sql_query("SELECT *
                                                FROM overforums");

                            while ($arr = mysql_fetch_array($res))
                            {
                                $name = $arr["name"];
                                $i    = 0 + $arr["id"];

                                print("<option value='$i'".($forid == $i ? " selected='selected'" : "").">$prefix".$name."</option>\n");
                            }

                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'>Minimum Read Permission</span></td>
                    <td class='rowhead'>
                        <select name='readclass'>

                            <?php

                            $maxclass = get_user_class();

                            for ($i = 0;
                                 $i <= $maxclass;
                                 ++$i)

                            {
                                print("<option value='$i'".($row["minclassread"] == $i ? " selected='selected'" : "").">$prefix".get_user_class_name($i)."</option>\n");
                            }

                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'>Minimum Write Permission</span></td>
                    <td class='rowhead'>
                        <select name='writeclass'>

                            <?php

                            $maxclass = get_user_class();

                            for ($i = 0;
                                 $i <= $maxclass;
                                 ++$i)

                            {
                                print("<option value='$i'".($row["minclasswrite"] == $i ? " selected='selected'" : "").">$prefix".get_user_class_name($i)."</option>\n");
                            }

                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'>Minimum Create Topic Permission</span></td>
                    <td class='rowhead'>
                        <select name='createclass'>

                            <?php

                            $maxclass = get_user_class();

                            for ($i = 0;
                                 $i <= $maxclass;
                                 ++$i)

                            {
                                print("<option value='$i'".($row["minclasscreate"] == $i ? " selected='selected'" : "").">$prefix".get_user_class_name($i)."</option>\n");
                            }

                            ?>

                        </select>
                    </td>
                </tr>
                <tr>
                    <td class='rowhead'><span style='font-weight:bold;'>Forum Rank</span></td>
                    <td class='rowhead'>
                        <select name='sort'>

                            <?php

                            $res = sql_query("SELECT sort
                                                FROM forums");

                            $nr = mysql_num_rows($res);

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
                <tr align='center'>
                    <td colspan='2'>
                        <input type='hidden' name='action' value='editforum' />
                        <input type='hidden' name='id' value='<?php echo $id;?>' />
                        <input type='submit' class='btn' name='Submit' value='Edit Forum' />
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
        display_message("info", "Sorry", "No Records were Found!  Return to the <a href='controlpanel.php'>Staff Control Panel</a>");
    }

    end_frame();

}

print("</td></tr></table>");
echo("<br />");

site_footer();

?>