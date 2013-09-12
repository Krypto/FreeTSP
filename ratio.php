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
require_once(FUNC_DIR.'function_torrenttable.php');

db_connect(false);
logged_in();

if (get_user_class() < UC_MODERATOR)
{
    error_message("warn", "Warning", "Access Denied!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if ($_POST["username"] == "" || $_POST["uploaded"] == "" || $_POST["downloaded"] == "")
    {
        error_message("error", "Error", "Missing Form Data.");
    }

    $username = sqlesc($_POST["username"]);

    if ($_POST["bytes"] == '1')
    {
        $uploaded   = $_POST["uploaded"] * 1024 * 1024;
        $downloaded = $_POST["downloaded"] * 1024 * 1024;
    }
    elseif ($_POST["bytes"] == '2')
    {
        $uploaded   = $_POST["uploaded"] * 1024 * 1024 * 1024;
        $downloaded = $_POST["downloaded"] * 1024 * 1024 * 1024;
    }
    elseif ($_POST["bytes"] == '3')
    {
        $uploaded   = $_POST["uploaded"] * 1024 * 1024 * 1024 * 1024;
        $downloaded = $_POST["downloaded"] * 1024 * 1024 * 1024 * 1024;
    }

    if ($_POST["action"] == '1')
    {
        $result = sql_query("SELECT uploaded, downloaded
                                FROM users
                                WHERE username=$username") or sqlerr(__FILE__, __LINE__);

        $arr        = mysql_fetch_assoc($result);
        $uploaded   = $arr["uploaded"] + $uploaded;
        $downloaded = $arr["downloaded"] + $downloaded;

        sql_query("UPDATE users
                    SET uploaded=$uploaded, downloaded=$downloaded
                    WHERE username=$username") or sqlerr(__FILE__, __LINE__);
    }
    elseif ($_POST["action"] == '2')
    {
        $result = sql_query("SELECT uploaded, downloaded
                                FROM users
                                WHERE username=$username") or sqlerr(__FILE__, __LINE__);

        $arr        = mysql_fetch_assoc($result);
        $uploaded   = $arr["uploaded"] - $uploaded;
        $downloaded = $arr["downloaded"] - $downloaded;

        sql_query("UPDATE users
                    SET uploaded=$uploaded, downloaded=$downloaded
                    WHERE username=$username") or sqlerr(__FILE__, __LINE__);
    }
    elseif ($_POST["action"] == '3')

    {
        sql_query("UPDATE users
                    SET uploaded=$uploaded, downloaded=$downloaded
                    WHERE username=$username") or sqlerr(__FILE__, __LINE__);
    }

    $result = sql_query("SELECT id
                            FROM users
                            WHERE username=$username") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($result);
    $id  = $arr["id"];

    $result = sql_query("SELECT id
                            FROM users
                            WHERE username=$username") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($result);
    $id  = $arr["id"];

    header("Location: $site_url/userdetails.php?id=$id");

    die;
}

?>