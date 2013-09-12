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

db_connect(true);
logged_in();

site_header("Delete Account",false);

if (get_user_class() < UC_MODERATOR)
{
    error_message_center("error", "Access Denied",
                                  "You Are Not Permitted To Access This Area");
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $action = isset($_GET["action"]) ? $_GET["action"] : '';

    if ($action == 'deluser')
    {
        $username = trim($_POST["username"]);

        display_message_center("info", "Sanity Check",
                                       "Are you sure you wish to Delete <strong>$username</strong><br />
                                       <form method='post' action='delete_member.php?&amp;action=sure'>
                                       <input type='hidden' name='username' size='20' value='".$username."' />
                                       <input type='submit' class='btn' value='Yes Delete $username' />
                                       </form>");
    }

    if ($action == 'sure')
    {
        $username = trim($_POST["username"]);

        display_message_center("info", "Final Sanity Check",
                                       "Are you <strong>Really</strong> sure you wish to Delete <strong>$username</strong><br />
                                       <form method='post' action='delete_member.php?&amp;action=delete'>
                                       <input type='hidden' name='username' size='20' value='".$username."' />
                                       <input type='submit' class='btn' value='Yes Delete $username' />
                                       </form>");
    }

    if ($action == 'delete')
    {
        $username = trim($_POST["username"]);

        $res = sql_query("SELECT *
                            FROM users
                            WHERE username=".sqlesc($username)) or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_assoc($res);

        $id = $arr['id'];

        $res = sql_query("DELETE
                            FROM users
                            WHERE id=$id") or sqlerr(__FILE__, __LINE__);

        write_stafflog("The Member <strong>$username</strong> -- Has Been Deleted By ".$CURUSER["username"]." ");

        display_message_center("info", "Deleted",
                                       "The Member <strong>$username</strong> was Deleted");
    }
}

site_footer();

?>