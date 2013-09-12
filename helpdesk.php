<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** https://github.com/Krypto/FreeTSP
** http://www.freetsp.info
** Licence Info: GPL
** Copyright (C) 2010 FreeTSP v1.0
** A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
** Project Leaders: Krypto, Fireknight.
**/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');
require_once(FUNC_DIR.'function_bbcode.php');

db_connect(true);
logged_in();

site_header("HELP DESK");

if (get_user_class() >= UC_MODERATOR)
{
    display_message_center("info", "Info", "You are a member of Staff.<br />
                                            If you need help, ask another Staff Member.");
    site_footer();
    die();
}

if (($msg_problem != "") && ($title != ""))
{
    $dt = sqlesc(get_date_time());

    sql_query("INSERT INTO helpdesk (title, msg_problem, added, added_by)
                VALUES (".sqlesc($title).", ".sqlesc($msg_problem).", $dt, $CURUSER[id])") or sqlerr();

    display_message_center("info", "Info", "Message Has Been Sent<br />
                                            Please Wait For A Reply");
    site_footer();
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $msg_problem = trim((isset($_POST['msg_problem']) ? $_POST['msg_problem'] : ''));
    $title       = trim((isset($_POST['title']) ? $_POST['title'] : ''));

    // Check Values Before Inserting Into Row --//
    if (empty($msg_problem))
    {
        error_message_center("error", "Error", "You Foregot To Ask A Question");
    }

    if (!$title)
    {
        error_message_center("error", "Error", "There Is No Title To Your Question");
    }

    if (($msg_problem != "") && ($title != ""))
    {
        $dt = sqlesc(get_date_time());

        sql_query("INSERT INTO helpdesk (title, msg_problem, added, added_by)
                    VALUES (".sqlesc($title).", ".sqlesc($msg_problem).", $dt, $CURUSER[id])") or sqlerr();

        display_message_center("info", "Info", "Message Has Been Sent<br />
                                                Please Wait For A Reply");
        site_footer();
        exit;
    }
}
//-- Main Help Desk --//

    print("<div align='center'>
               Before using the Help Desk<br />
               Please make sure you have read through the
               <a href='faq.php'><b>F.A.Q</b></a> section.<br />
               And searched through the
               <a href='forums.php'><b>Forums</b></a> first.
           </div><br />");

    print("<div align='center'>
                As any question already answered in either section will be <b>IGNORED</b>
           </div><br />");

    print("<form method='post' action='helpdesk.php'>");
    print("<table border='0' align='center' cellpadding='5' cellspacing='0'>");

    print("<tr>
            <td align='center' colspan='2'> <b>TITLE&nbsp;&nbsp;:-&nbsp;&nbsp;</b>
                <input type='text' size='73' maxlength='60' name='title' />
            </td>
           </tr>");

    print("<tr>
            <td colspan='2'>
                ".textbbcode("compose","msg_problem",$body)."
            </td>
           </tr>");

    print("<tr>
            <td align='center' colspan='2'><input type='submit' class='btn' value='Help me!' /></td>
           </tr>");

    print("</table>");
    print("</form>");

site_footer();

?>