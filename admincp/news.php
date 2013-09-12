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

$action = isset($_GET["action"]) ? $_GET["action"] : '';

//-- Start Delete News Item --//
if ($action == 'delete')
{
    $newsid = isset($_GET['newsid']) ? (int) $_GET["newsid"] : 0;

    if (!is_valid_id($newsid))
    {
        error_message("error", "Error", "Invalid News Item ID - Code 1");
    }

    $returnto = isset($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : '';
    $sure     = isset($_GET["sure"]) ? (int) $_GET['sure'] : 0;

    if (!$sure)
    {
        error_message("warn", "Warning", "<a href='controlpanel.php?fileaction=6&amp;action=delete&amp;newsid=$newsid&amp;returnto=$returnto&amp;sure=1'>Do you really want to Delete a News Item?  Click if you are sure?</a>");
    }

    global $CURUSER;

    sql_query("DELETE
                FROM news
                WHERE id = $newsid AND userid = $CURUSER[id]");

    @unlink(CACHE_DIR."news.txt");

    if ($returnto != "")
    {
        $warning = "News Item was Deleted Successfully.";
    }
}
//-- End Delete News Item --//

//-- Start Add News Item --//
if ($action == 'add')
{
    $body = isset($_POST["body"]) ? (string) $_POST["body"] : 0;

    if (!$body)
    {
        error_message("error", "Error", "The News Item cannot be Empty!");
    }

    $body  = sqlesc($body);
    $added = isset($_POST["added"]) ? $_POST["added"] : 0;

    if (!$added)
    {
        $added = sqlesc(get_date_time());
    }

    @sql_query("INSERT INTO news (userid, added, body)
                VALUES ({$CURUSER['id']}, $added, $body)") or sqlerr(__FILE__, __LINE__);

    @unlink(CACHE_DIR."news.txt");

    if (mysql_affected_rows() == 1)
    {
        $warning = error_message("success", "Success", "News Item was Added Successfully.");
    }
    else
    {
        error_message("error", "Error", "Something weird just happened!");
    }
}
//-- End Add News Item --//

//-- Start Edit News Item --//
if ($action == 'edit')
{
    $newsid = isset($_GET["newsid"]) ? (int) $_GET["newsid"] : 0;

    if (!is_valid_id($newsid))
    {
        error_message("error", "Error", "Invalid News item ID - Code 2.");
    }

    $res = @sql_query("SELECT *
                        FROM news
                        WHERE id = $newsid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
    {
        error_message("error", "Error Message", "No News item with that ID.");
    }

    $arr = mysql_fetch_assoc($res);

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $body = isset($_POST['body']) ? $_POST['body'] : '';

        if ($body == "")
        {
            error_message("error", "Error", "Body cannot be Empty!");
        }

        $body     = sqlesc($body);
        $editedat = sqlesc(get_date_time());

        @sql_query("UPDATE news
                    SET body = $body
                    WHERE id = $newsid") or sqlerr(__FILE__, __LINE__);

        @unlink(CACHE_DIR."news.txt");

        $warning = error_message("success", "Success", "<div align='center'>News item was Edited Successfully.<br /> Return to <a href='index.php'>Main Page</a></div>");
    }
    else
    {
        site_header("",false);
        echo("<h1>Edit News Item</h1>\n");
        echo("<form method='post' name='ednews' action='controlpanel.php?fileaction=6&amp;action=edit&amp;newsid=$newsid'>\n");
        echo("<table border='1' width='100%' cellspacing='0' cellpadding='5'>\n");
        echo("<tr><td class='std'><input type='hidden' name='returnto' value='$returnto' /></td></tr>\n");
        echo("<tr><td class='std' style='padding: 0px'>".textbbcode("ednews", "body", htmlspecialchars($arr["body"]))."</td></tr>\n");
        echo("<tr><td class='std' align='center'><input type='submit' class='btn' value='Okay' /></td></tr>\n");
        echo("</table>\n");
        echo("</form>\n");
        echo("<br />");
        site_footer();
        die;
    }
}
//-- End Edit News Item --//

//-- Start Display News Form --//
site_header("Site News",false);
echo("<h1>Submit News Item</h1>\n");

if ($warning)
{
    echo("<p><span style='font-size: small;'>($warning)</span></p>");
}

echo("<form method='post' name='news' action='controlpanel.php?fileaction=6&amp;action=add'>\n");
echo("<table border='1' width='100%' cellspacing='0' cellpadding='5'>\n");
echo("<tr><td class='std' style='padding: 10px'>".textbbcode("news", "body", htmlspecialchars($arr["body"]))."\n");
echo("<br /><br /><div align='center'><input type='submit' class='btn' value='Okay' /></div></td></tr>\n");
echo("</table></form><br /><br />\n");

$res = @sql_query("SELECT *
                    FROM news
                    ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) > 0)
{
    begin_frame();

    while ($arr = mysql_fetch_assoc($res))
    {
        $newsid = $arr["id"];
        $body   = format_comment($arr["body"]);
        $userid = $arr["userid"];
        $added  = $arr["added"]." GMT (".(get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"])))." ago)";

        $res2 = @sql_query("SELECT username, donor
                            FROM users
                            WHERE id = $userid") or sqlerr(__FILE__, __LINE__);

        $arr2       = mysql_fetch_assoc($res2);
        $postername = $arr2["username"];

        if ($postername == "")
        {
            $by = "unknown[$userid]";
        }
        else
        {
            $by = "<a href='userdetails.php?id=$userid'><span style='font-weight:bold;'>$postername</span></a>".($arr2["donor"] == "yes" ? "<img src='{$image_dir}star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />" : "");
        }

        echo("<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>");
        echo("$added&nbsp;---&nbsp;by&nbsp;$by");
        echo(" - [<a href='controlpanel.php?fileaction=6&amp;action=edit&amp;newsid=$newsid'><span style='font-weight:bold;'>Edit</span></a>]");
        echo(" - [<a href='controlpanel.php?fileaction=6&amp;action=delete&amp;newsid=$newsid'><span style='font-weight:bold;'>Delete</span></a>]");
        echo("</td></tr></table>\n");

        begin_table(true);
            echo("<tr valign='top'><td class='comment'>$body</td></tr>\n");
        end_table();
    }
    end_frame();
    echo("<br />");
}
else
{
    error_message("info", "Sorry", "No News Available!");
}
//-- End Display News Form --//

site_footer();
die;

?>