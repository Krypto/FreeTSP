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

db_connect();
logged_in();

site_header("Create Announcement",false);

if (get_user_class() < UC_ADMINISTRATOR)
{
  error_message_center("error", "Access Denied",
                                "You Are Not Permitted To Access This Area");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //-- The Expiry Days. --//
    $days = array(
                  array(7,'7 Days'),
                  array(14,'14 Days'),
                  array(21,'21 Days'),
                  array(28,'28 Days'),
                  array(56,'2 Months')
                  );

    //-- Usersearch POST Data... --//
    $n_pms     = (isset($_POST['n_pms']) ? $_POST['n_pms'] : 0);
    $ann_query = (isset($_POST['ann_query']) ? trim($_POST['ann_query']) : '');
    $ann_hash  = (isset($_POST['ann_hash']) ? trim($_POST['ann_hash']) : '');

    if (hashit($ann_query,$n_pms) != $ann_hash) //-- Validate POST... --//
        error_message_center("info", "Info", "You cannot make an Announcement for just 1 Member.  PM the Member instead!");

    if (!preg_match('/\\ASELECT.+?FROM.+?WHERE.+?\\z/', $ann_query))
        error_message_center("error", "Error", "Misformed Query");

    if (!$n_pms)
        error_message_center("error", "Error", "No Recipients");

    //-- Preview POST Data ... --//
    $body    = trim((isset($_POST['msg']) ? $_POST['msg'] : ''));
    $subject = trim((isset($_POST['subject']) ? $_POST['subject'] : ''));
    $expiry  = 0 + (isset($_POST['expiry']) ? $_POST['expiry'] : 0);

    if ((isset($_POST['buttonval']) AND $_POST['buttonval'] == 'Submit'))
    {
        //-- Check Values Before Inserting Into Row... --//
        if (empty($body))
            error_message_center("error", "Error", "There Is Nothing To Announce");

        if (!$subject)
            error_message_center("error", "Error", "There Is No Subject To The Announcement");

        unset ($flag);
        reset ($days);

        foreach ($days
                 AS
                 $x)

    if ($expiry == $x[0]) $flag = 1;

    if (!isset($flag))
        error_message_center("error", "Error", "You Have Selected An Invalid Expiry Choice");

        $expires = get_date_time((strtotime(get_date_time()) + (86400 * $expiry))); //-- 86400 seconds in one day. --//
        $created = get_date_time();

        $query = sprintf('INSERT INTO announcement_main '.
                            '(owner_id, created, expires, sql_query, subject, body) '.
                            'VALUES (%s, %s, %s, %s, %s, %s)',
                        sqlesc($CURUSER['id']),
                        sqlesc($created),
                        sqlesc($expires),
                        sqlesc($ann_query),
                        sqlesc($subject),
                        sqlesc($body));

        sql_query($query);

    if (mysql_affected_rows() == 1)

        error_message_center("success", "Success", "The Announcement Has Been Successfully Created");
        error_message_center("error", "Error", "Something Went Wrong - Please Contact The Site Manager");
    }

    echo("<div align='center'><h1>Create Announcement for ($n_pms) Members !</h1></div>");
    echo("<form method='post' name='compose' action='new_announcement.php'>");
    echo("<table border='1' cellspacing='0' cellpadding='5'>");

    echo("<tr>
          <td align='center' colspan='2'>
          <strong>Subject</strong>
          <input  type='text'name='subject' size='76' value='".htmlspecialchars_decode($subject)."' />
          </td>
          </tr>");

    echo("<tr>
          <td class='std' style='padding: 10px'>
          ".textbbcode("compose", "msg", $body)."
          </td>
          </tr>");

    echo("<tr>
          <td align='center' colspan='2'>");

    echo("<select name='expiry'>");

    reset ($days);

    foreach ($days
             AS
             $x)

    echo('<option value="'.$x[0].'"'.(($expiry == $x[0] ? '' : '')).'>'.$x[1].'</option>');

    echo("</select>&nbsp;&nbsp;");

    echo("&nbsp;&nbsp;<input type='submit' class='btn' name='buttonval' value='Preview' />
          &nbsp;&nbsp;<input type='submit' class='btn' name='buttonval' value='Submit' />
          <input type='hidden' name='n_pms' value='".$n_pms."' />
          <input type='hidden' name='ann_query' value='".$ann_query."' />
          <input type='hidden' name='ann_hash' value='".$ann_hash."' />
          </td>
          </tr>");

    echo("</table></form><br /><br />");

    if ($body)
    {
        $newtime = (strtotime(get_date_time()) + (86400 * $expiry));

        echo("<table class='main' border='0' width='700' cellspacing='1' cellpadding='1'>");
        echo("<tr><td align='center' bgcolor='#663366'><h2><font color='white'>Announcement Preview :-&nbsp;&nbsp;");
        echo("Title = $subject");
        echo("</font></h2></td></tr>");
        echo("<tr><td class='text'>");
        echo(format_comment($body).'<br /><hr />'.'Expires: '.get_date_time($newtime));
        echo("</td></tr></table>");
    }
}

site_footer();

?>