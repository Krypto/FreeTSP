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

db_connect();
logged_in();
illegal_access($page, UC_MODERATOR);

if ($CURUSER['override_class'] != 255)
{
    error_message_center("info", "Warning", "You need to Restore your Class, before you can change it again !!.");
}

//-- Process The Querystring - No Security Checks Are Done As A Temporary Class Higher Than The Actual Class Mean Absoluetly Nothing. --//

if ($_GET['action'] == 'editclass')
{
    $newclass = 0 + $_GET['class'];
    $returnto = $_GET['returnto'];

    sql_query("UPDATE users
                SET override_class = ".sqlesc($newclass)."
                WHERE id = ".$CURUSER['id']); //-- Set Temporary Class --

    header("Location: ".$site_url."/".$returnto);
    die();
}

site_header("Set override class for ".$CURUSER["username"]);

print("<form method='get' action='setclass.php'>");
print("<input type='hidden' name='action' value='editclass' />");
print("<input type='hidden' name='returnto' value='index.php' />"); //-- Change To Any Page You Want --//

begin_frame("Allows you to Change your User Class on the fly.",true,5,true);
begin_table();

print("<tr>
        <td class='colhead'>Class <select name='class'>");

$maxclass = get_user_class() - 1;

for ($i = 0; $i <= $maxclass; ++$i)
{
    $currentclass = get_user_class_name($i);

    if ($currentclass)
        print("<option value=\"$i\"".">".get_user_class_name($i)."</option>\n");
}

print("</select>
        </td>
        </tr>");

print("<tr>
        <td class='rowhead' align='center'>
            <input type='submit' class='btn' value='Okay'/>
        </td>
    </tr>");

end_table();
end_frame();

print("</form>
        <br />");

site_footer();

?>