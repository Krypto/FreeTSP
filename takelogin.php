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
require_once(FUNC_DIR.'function_page_verify.php');

if (!mkglobal("username:password:submitme"))
{
    die();
}

$sha = sha1($_SERVER['REMOTE_ADDR']);

if (is_file(''.$dictbreaker.'/'.$sha) && filemtime(''.$dictbreaker.'/'.$sha) > (time() - 8))
{
    @fclose(@fopen(''.$dictbreaker.'/'.$sha, 'w'));
    die('Minimum 8 Seconds Between Login Attempts :)');
}

db_connect();

$newpage = new page_verify();
$newpage->check('_login_');

failedloginscheck();

$res = sql_query("SELECT id, passhash, secret, enabled
                    FROM users
                    WHERE username = ".sqlesc($username)."
                    AND status = 'confirmed'");

$row = mysql_fetch_assoc($res);

if (!$row)
{
    $ip    = sqlesc(getip());
    $added = sqlesc(get_date_time());

    $a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*)
                                        FROM loginattempts
                                        WHERE ip=$ip"))) or sqlerr(__FILE__, __LINE__);

    if ($a[0] == 0)
    {
        sql_query("INSERT INTO loginattempts (ip, added, attempts)
                    VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
    }
    else
    {
        sql_query("UPDATE loginattempts
                    SET attempts = attempts + 1
                    WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
    }

    @fclose(@fopen(''.$dictbreaker.'/'.sha1($_SERVER['REMOTE_ADDR']), 'w'));

    error_message("error", "Error", "<a href='/login.php'>Login Failed!  Username or Password Incorrect?</a>");
}

if ($row["passhash"] != md5($row["secret"].$password.$row["secret"]))
{
    $ip    = sqlesc(getip());
    $added = sqlesc(get_date_time());

    $a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*)
                                        FROM loginattempts
                                        WHERE ip=$ip"))) or sqlerr(__FILE__, __LINE__);

    if ($a[0] == 0)
    {
        sql_query("INSERT INTO loginattempts (ip, added, attempts)
                    VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
    }
    else
    {
        sql_query("UPDATE loginattempts
                    SET attempts = attempts + 1
                    WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
    }

    @fclose(@fopen(''.$dictbreaker.'/'.sha1($_SERVER['REMOTE_ADDR']), 'w'));

    $to  = ($row["id"]);
    $sub = "Security Alert";
    $msg = "[color=red]SECURITY ALERT[/color]\n\n Account: ID=".$row['id']." Somebody (probably you, [b]".$username."![/b]) tried to Login but Failed!"."\n\nTheir [b]IP ADDRESS [/b] was : ([b]".$ip." ".@gethostbyaddr($ip)."[/b])"."\n\n If this wasn't you please Report this event to a Staff \n\n - Thank you.\n";

    $sql = "INSERT INTO messages (subject, sender, receiver, msg, added)
            VALUES ('$sub', '$from', '$to', ".sqlesc($msg).", $added);";

    $res = sql_query($sql) or sqlerr(__FILE__, __LINE__);

    error_message("error", "Error", "<a href='/login.php'>Login Failed!  Username or Password Incorrect?</a>");
}

if ($row["enabled"] == "no")
{
    error_message("info", "Info", "This Account has been Disabled.");
}

if ($submitme != 'X')
{
    error_message("info", "Info", "Ha Ha I said click the X you stupid twit. Now go back and try again.");
}

logincookie($row["id"], $row["passhash"]);

$ip = sqlesc(getip());

sql_query("DELETE
            FROM loginattempts
            WHERE ip = $ip");

$returnto = str_replace('&amp;', '&', htmlspecialchars($_POST['returnto']));

if (!empty($returnto))
{
    header("Location: ".$returnto);
}
else
{
    header("Location: index.php");
}

?>