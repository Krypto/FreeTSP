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
require_once(FUNC_DIR.'function_page_verify.php');

if (!mkglobal("id"))
{
    die();
}

$id = 0 + $id;

if (!$id)
{
    die();
}

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->create('_edit_');

$res = sql_query("SELECT *
                    FROM torrents
                    WHERE id = $id");

$row = mysql_fetch_assoc($res);

if (!$row)
{
    die();
}

site_header("Edit Torrent '".$row["name"]."'");

if (!isset($CURUSER) || ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR))
{
    echo display_message("warn", "You Cannot Edit This Torrent", "You're Not The Rightful Owner, Or You're Not <a href='login.php?returnto=".urlencode(substr($_SERVER["REQUEST_URI"], 1))."&amp;nowarn=1'>Logged In</a> Properly.");
}
else
{
    print("<form name='editupload' method='post' action='takeedit.php' enctype='multipart/form-data'>\n");
    print("<input type='hidden' name='id' value='$id' />\n");

    if (isset($_GET["returnto"]))
    {
        print("<input type='hidden' name='returnto' value='".htmlspecialchars($_GET["returnto"])."' />\n");
    }

    print("<table align='center' border='1' cellspacing='0' cellpadding='10'>\n");
    print("<tr>
            <td class='rowhead'><label for='name'>Torrent Name</label></td>
            <td class='rowhead'><input type='text' name='name' id='name' size='80' value='".htmlspecialchars($row["name"])."' /></td>
        </tr>");

    print("<tr>
         <td class='rowhead'><label for='poster'>Poster</label></td>
         <td class='rowhead'><input type='text' name='poster' id='poster' size='80' value='".htmlspecialchars($row["poster"])."' /><br />
         (Direct link for a poster image to be shown on the details page)</td>
     </tr>");

    print("<tr>
            <td class='rowhead'>NFO file</td>
            <td class='rowhead'>
                <input type='radio' name='nfoaction' value='keep' checked='checked' />Keep current<br />
                <input type='radio' name='nfoaction' value='update' />Update:<br /><input type='file' name='nfo' size='80' />
            </td>
        </tr>");

    if ((strpos($row["ori_descr"], "<") === false) || (strpos($row["ori_descr"], "&lt;") !== false))
    {
        $c = "";
    }
    else
    {
        $c = " checked";
    }

    print("<tr>
            <td class='rowhead' style='padding: 10px'>Description</td>
            <td class='rowhead' align='center' style='padding: 3px'>".textbbcode("editupload", "descr", htmlspecialchars($row["ori_descr"]))."</td>
        </tr>\n");

    $s = "<select name='type'>\n";

    $cats = genrelist();

    foreach ($cats
             AS
             $subrow)
    {
        $s .= "<option value='".$subrow["id"]."'";

        if ($subrow["id"] == $row["category"])
        {
            $s .= " selected='selected'";
        }

        $s .= ">".htmlspecialchars($subrow["name"])."</option>\n";
    }

    $s .= "</select>\n";

    print("<tr>
            <td class='rowhead'>Type</td>
            <td class='rowhead'>".$s."</td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Visible</td>
            <td class='rowhead'>
                <input type='checkbox' name='visible'".(($row["visible"] == "yes") ? " checked='checked'" : "")." value='1' /> Visible on main page<br />
                    <table border='0' width='420' cellspacing='0' cellpadding='0'>
                        <tr>
                            <td class='embedded'>Note that the torrent will automatically become visible when there's a Seeder, and will become automatically Invisible (Dead) when there has been No Seeder for a while. Use this switch to speed the process up manually. Also note that Invisible (Dead) torrents can still be viewed or searched for, it's just not the default.
                            </td>
                        </tr>
                    </table>
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead'>Anonymous Uploader</td>
            <td class='rowhead'>
                <input type='checkbox' name='anonymous'".(($row["anonymous"] == "yes") ? " checked='checked'" : "" )." value='1' /> Check this box if you DO NOT wish your name to be shown as the Uploader.
            </td>
        </tr>");

    if (get_user_class() >= UC_MODERATOR)
    {
        print("<tr>
                <td class='rowhead'>Banned</td>
                <td class='rowhead'>
                    <input type='checkbox' name='banned'".(($row["banned"] == "yes") ? " checked='checked'" : "")." value='1' /> Banned
                </td>
            </tr>");

        print("<tr>
                <td class='rowhead'>Sticky</td>
                <td class='rowhead'>
                    <input type='checkbox' name='sticky'".(($row["sticky"] == "yes") ? " checked='checked'" : "" )." value='yes' />Make Sticky!
                </td>
            </tr>");
    }

    print("<tr>
            <td class='rowhead'>Freeleech</td>
            <td class='rowhead'>
                <input type='checkbox' name='freeleech'".(($row["freeleech"] == "yes") ? " checked='checked'" : "")." value='yes' /> Set this torrent to Freeleech
            </td>
        </tr>");

    print("<tr>
            <td class='rowhead' colspan='2' align='center'>
                <input type='reset' class='btn' value='Revert Changes!' style='height: 25px; width: 100px' />&nbsp;&nbsp;&nbsp;&nbsp;
                <input type='submit' class='btn' value='Edit it!' style='height: 25px; width: 100px' />
            </td>
        </tr>\n");

    print("</table>\n");
    print("</form>\n");
    print("<br />\n");

    if (get_user_class() >= UC_MODERATOR)
    {
        print("<form method='post' action='delete.php'>\n");
        print("<table align='center' border='1' cellspacing='0' cellpadding='5'>\n");

        print("<tr>
                <td class='rowhead' style='padding-bottom: 5px' colspan='2'>
                <span style='font-weight:bold;'>Delete torrent.</span> Reason:</td>
            </tr>");

        print("<tr>
                <td class='rowhead'>
                    <input name='reasontype' type='radio' value='1' />&nbsp;Dead
                </td>
                <td class='rowhead'> 0 seeders, 0 leechers = 0 peers total</td>
            </tr>\n");

        print("<tr>
                <td class='rowhead'>
                    <input name='reasontype' type='radio' value='2' />&nbsp;Dupe
                </td>
                <td class='rowhead'><input type='text' name='reason[]' size='40' /></td>
            </tr>");

        print("<tr>
                <td class='rowhead'>
                    <input name='reasontype' type='radio' value='3' />&nbsp;Nuked
                </td>
                <td class='rowhead'>
                    <input type='text' name='reason[]' size='40' />
                </td>
            </tr>");

        print("<tr>
                <td class='rowhead'>
                    <input name='reasontype' type='radio' value='4' />&nbsp;$site_name Rules
                </td>
                <td class='rowhead'>
                    <input type='text' name='reason[]' size='40' />(Req)
                </td>
            </tr>");

        print("<tr>
                <td class='rowhead'>
                    <input name='reasontype' type='radio' value='5' checked='checked' />&nbsp;Other:
                </td>
                <td class='rowhead'>
                    <input type='text' name='reason[]' size='40' />(Req)");

        print("<input type='hidden' name='id' value='$id' /></td></tr>\n");

        if (isset($_GET["returnto"]))
        {
            print("<input type='hidden' name='returnto' value='".htmlspecialchars($_GET["returnto"])."' />\n");
        }
        print("<tr>
                <td colspan='2' align='center'>
                    <input type='submit' class='btn' value='Delete it!' style='height: 25px' />
                </td>
            </tr>");
        print("</table>");
        print("</form><br />");
    }
}

site_footer();

?>