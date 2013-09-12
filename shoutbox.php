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

// Based on Hanne's Shoutbox With added staff functions-putyn shout and reply added Spook

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');
require_once(FUNC_DIR.'function_bbcode.php');

db_connect(false);
logged_in();

//-- Start link To Customise Shoutbox Per Individual Theme --//
if ($CURUSER)
{
    $ss_a = @mysql_fetch_array(sql_query("SELECT uri
                                            FROM stylesheets
                                            WHERE id = ".$CURUSER["stylesheet"]));

    if ($ss_a)
    {
        $ss_uri = $ss_a["uri"];
    }
}

if (!$ss_uri)
{
    ($r = sql_query("SELECT uri
                        FROM stylesheets
                        WHERE id = 1")) or die(mysql_error());

    ($a = mysql_fetch_array($r)) or die(mysql_error());

    $ss_uri = $a["uri"];
}

require_once(STYLES_DIR.$ss_uri.DIRECTORY_SEPARATOR.'theme_function.php');

//-- Finish link To Customise Shoutbox Per Individual Theme --//

function autoshout ($msg = '')
{
    $message = $msg;

    sql_query("INSERT INTO shoutbox (date, text)
                VALUES (".implode(", ", array_map("sqlesc", array(time(), $message))).")") or sqlerr(__FILE__, __LINE__);
}

/*
    Get current datetime
    $dt = gmtime() - 60;
    $dt = sqlesc(get_date_time($dt));
*/

unset ($insert);

$insert = false;
$query  = "";

//-- Delete Shout --//
if (isset($_GET['del']) && get_user_class() >= UC_MODERATOR && is_valid_id($_GET['del']))
{
    sql_query("DELETE
                FROM shoutbox
                WHERE id=".sqlesc($_GET['del']));
}

//-- Empty Shout - Coder/Owner --//
if (isset($_GET['delall']) && get_user_class() >= UC_SYSOP)
{
    $query = "TRUNCATE
                TABLE shoutbox";
}

sql_query($query);
unset($query);

//-- Edit Shout --//
if (isset($_GET['edit']) && get_user_class() >= UC_MODERATOR && is_valid_id($_GET['edit']))
{
    $sql = sql_query('SELECT id,text
                        FROM shoutbox
                        WHERE id='.sqlesc($_GET['edit']));

    $res = mysql_fetch_assoc($sql);
    unset($sql);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <meta http-equiv='Pragma' content='no-cache' />
        <meta http-equiv='expires' content='-1' />

        <?php sb_style(); ?>

        <script type='text/javascript' src='js/shout.js'></script>

    </head>

<body>

<?php

    echo "<form method='post' action='shoutbox.php'>
          <input type='hidden' name='id' value='".(int) $res['id']."' />
          <textarea name='text' rows='3' id='staff_specialbox'>".htmlspecialchars($res['text'])."</textarea>
          <input type='submit' name='save' value='Save' />
          </form></body></html>";
    die();
}

//-- Power Users+ Can Edit Anyones Single Shouts - pdq --//
if (isset($_GET['edit']) && ($_GET['user'] == $CURUSER['id']) && ($CURUSER['class'] >= UC_POWER_USER && $CURUSER['class'] <= UC_MODERATOR) && is_valid_id($_GET['edit']))
{
    $sql = sql_query('SELECT id, text, userid
                        FROM shoutbox
                        WHERE userid ='.sqlesc($_GET['user']).'
                        AND id ='.sqlesc($_GET['edit']));

    $res = mysql_fetch_array($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <meta http-equiv='Pragma' content='no-cache' />
        <meta http-equiv='expires' content='-1' />

        <?php sb_style(); ?>

        <script type='text/javascript' src='js/shout.js'></script>

    </head>

<body>
<?php

    echo "<form method='post' action='shoutbox.php'>
            <input type='hidden' name='id' value='".(int) $res['id']."' />
            <input type='hidden' name='user' value='".(int) $res['userid']."' />
            <textarea name='text' rows='3' id='member_specialbox'>".htmlspecialchars($res['text'])."</textarea>
            <input type='submit' name='save' value='Save' />
            </form></body></html>";
    die;
}

//-- Staff Shout Edit --//
if (isset($_POST['text']) && $CURUSER['class'] >= UC_MODERATOR && is_valid_id($_POST['id']))
{
    $text        = trim($_POST['text']);
    $text_parsed = format_comment($text);

    sql_query('UPDATE shoutbox
                SET text = '.sqlesc($text).', text_parsed = '.sqlesc($text_parsed).'
                WHERE id ='.sqlesc($_POST['id']));

    unset ($text, $text_parsed);
}
// Power User+ Shout Edit --//
//-- Correction By Fireknight Added In theme_function.php --//
if (isset($_POST['text']) && (isset($_POST['user']) == $CURUSER['id']) && ($CURUSER['class'] >= UC_POWER_USER && $CURUSER['class'] < UC_MODERATOR) && is_valid_id($_POST['id']))
{
    $text        = trim($_POST['text']);
    $text_parsed = format_comment($text);

    sql_query('UPDATE shoutbox
                SET text = '.sqlesc($text).', text_parsed = '.sqlesc($text_parsed).'
                WHERE userid='.sqlesc($_POST['user']).'
                AND id='.sqlesc($_POST['id']));

    unset ($text, $text_parsed);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <meta http-equiv='Pragma' content='no-cache' />
        <meta http-equiv='expires' content='0' />
    <title>ShoutBox</title>
    <meta http-equiv='REFRESH' content='60; URL=shoutbox.php' />

    <?php sb_style(); ?>

    <script type='text/javascript' src='js/shout.js'></script>

</head>

<?php

//-- Start Defining Background Color And Font Color To Match Theme Color --//
echo "<body>";
//-- Finish Defining Background Color And Font Color To Match Theme Color --//

if ($CURUSER['shoutboxpos'] == 'no')
{
    echo("<div class='error' align='center'><br /><font color='red'>Sorry, you are NOT Authorized to Shout.</font>  (<font color='red'>Check your PMs for the reason why?</font>)<br /><br /></div></body></html>");
    exit;
}

if (isset($_GET['sent']) && ($_GET['sent'] == "yes"))
{
    $limit       = 1;
    $userid      = $CURUSER["id"];
    $date        = sqlesc(time());
    $text        = (trim($_GET["shbox_text"]));
    $text_parsed = format_comment($text);
/*
    // quiz bot
    if (stristr($text, "/quiz") && $CURUSER["class"] >= UC_MODERATOR)

    {
        $userid = 13767;
    }
    $text = str_replace(array("/quiz",
                              "/QUIZ [color=red]"), "", $text);
    $text_parsed = format_comment($text);

    //  radio bot
    if (stristr($text, "/scast") && $CURUSER["class"] >= UC_MODERATOR)

    {
        $userid = 13626;
    }
    $text = str_replace(array("/scast",
                              "/SCAST"), "", $text);
    $text_parsed = format_comment($text);

    //Notice By Subzero
    if (stristr($text, "/notice") && $CURUSER["class"] >= UC_MODERATOR)
    {
        $userid = 2;
    }
    $text = str_replace(array("/NOTICE",
                              "/notice"), "", $text);
    $text_parsed = format_comment($text);

    if (stristr($text, "/system") && $CURUSER["class"] >= UC_MODERATOR)
    {
        $userid = 2;
        $text   = str_replace(array("/SYSTEM",
                                    "/system"), "", $text);
        //$text_parsed = format_comment($text);
    }
*/
    //-- Shoutbox Command System By Putyn & pdq --//
    $commands = array("\/EMPTY",
                      "\/GAG",
                      "\/UNGAG",
                      "\/WARN",
                      "\/UNWARN",
                      "\/DISABLE",
                      "\/ENABLE",
                      "\/"); //-- This / Was Replaced With \/ To Work With The Regex --//

    $pattern  = "/(".implode("|", $commands)."\w+)\s([a-zA-Z0-9_:\s(?i)]+)/";

    //-- $private_pattern = "/(^\/private)\s([a-zA-Z0-9]+)\s([\w\W\s]+)/";  --//

    if (preg_match($pattern, $text, $vars) && $CURUSER["class"] >= UC_MODERATOR)
    {
        $command = $vars[1];
        $user    = $vars[2];

        $c = sql_query("SELECT id, class, modcomment
                            FROM users
                            WHERE username=".sqlesc($user)) or sqlerr();

        $a = mysql_fetch_row($c);

        if (mysql_num_rows($c) == 1 && $CURUSER["class"] > $a[1])
        {
            switch ($command)
            {
                case "/EMPTY" :
                    $what = 'Deleted ALL Shouts';
                    $msg  = " - [b]".$user."'s[/b] - Shouts have been Deleted";

                    $query = "DELETE
                                FROM shoutbox
                                WHERE userid = ".$a[0];
                    break;

                case "/GAG"    :
                    $what       = 'Gagged';
                    $modcomment = gmdate("Y-m-d")." - [SHOUTBOX] User has been Gagged by ".$CURUSER["username"]."\n".$a["modcomment"];
                    $msg        = " - [b]".$user."[/b] - has been Gagged by ".$CURUSER["username"];

                    $query = "UPDATE users
                                 SET shoutboxpos='no', modcomment = concat(".sqlesc($modcomment).", modcomment)
                                 WHERE id = ".$a[0];
                    break;

                case "/UNGAG" :
                    $what       = 'Un-Gagged';
                    $modcomment = gmdate("Y-m-d")." - [SHOUTBOX] User has been Un-Gagged by ".$CURUSER["username"]."\n".$a[2];
                    $msg        = " - [b]".$user."[/b] - has been Un-Gagged by ".$CURUSER["username"];

                    $query = "UPDATE users
                                 SET shoutboxpos='yes', modcomment = concat(".sqlesc($modcomment).", modcomment)
                                 WHERE id = ".$a[0];

                    break;

                case "/WARN" :
                    $what       = 'Warned';
                    $modcomment = gmdate("Y-m-d")." - [SHOUTBOX] User has been Warned by ".$CURUSER["username"]."\n".$a[2];
                    $msg        = " - [b]".$user."[/b] - has been Warned by ".$CURUSER["username"];

                    $query = "UPDATE users
                                SET warned='yes', modcomment = concat(".sqlesc($modcomment).", modcomment)
                                WHERE id = ".$a[0];
                    break;

                case "/UNWARN" :
                    $what       = 'Un-Warned';
                    $modcomment = gmdate("Y-m-d")." - [SHOUTBOX] User has been Un-Warned by ".$CURUSER["username"]."\n".$a[2];
                    $msg        = " - [b]".$user."[/b] - has been Un-Warned by ".$CURUSER["username"];

                    $query = "UPDATE users
                                SET warned='no', modcomment = concat(".sqlesc($modcomment).", modcomment)
                                WHERE id = ".$a[0];
                    break;

                case "/DISABLE"    :
                    $what       = 'Disabled';
                    $modcomment = gmdate("Y-m-d")." - [SHOUTBOX] User has been Disabled by ".$CURUSER["username"]."\n".$a[2];
                    $msg        = " - [b]".$user."[/b] - has been Disabled by ".$CURUSER["username"];

                    $query = "UPDATE users
                                SET enabled='no', modcomment = concat(".sqlesc($modcomment).", modcomment)
                                WHERE id = ".$a[0];
                    break;

                case "/ENABLE" :
                    $what       = 'Enabled';
                    $modcomment = gmdate("Y-m-d")." - [SHOUTBOX] User has been Enabled by ".$CURUSER["username"]."\n".$a[2];
                    $msg        = " - [b]".$user."[/b] - has been Enabled by ".$CURUSER["username"];

                    $query = "UPDATE users
                                SET enabled='yes', modcomment = concat(".sqlesc($modcomment).", modcomment)
                                WHERE id = ".$a[0];
                    break;
            }
            if (sql_query($query))
            {
                autoshout($msg);
            }

            print "<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";

            write_log("[b]Shoutbox[/b] ".$user." has been ".$what." by ".$CURUSER["username"]);

            unset ($text, $text_parsed, $query, $date, $modcomment, $what, $msg, $commands);
        }
    }

/*
    //-- Private Shout Mode --//
        elseif (preg_match($private_pattern,$text,$vars))
        {
            $to_user = mysql_result(sql_query('SELECT id
                                                FROM users
                                                WHERE username = '.sqlesc($vars[2])),0) or exit(mysql_error());

            if ($to_user != 0 && $to_user != $CURUSER['id'])
            {
                $text        = $vars[2]." - ".$vars[3];
                $text_parsed = format_comment($text);

                sql_query ("INSERT INTO shoutbox (userid, date, text, text_parsed,to_user)
                                VALUES (".sqlesc($userid).", $date, ".sqlesc( $text ).",".sqlesc( $text_parsed).",".sqlesc($to_user).")") or sqlerr( __FILE__, __LINE__ );
            }
            print "<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";
        }

    //-- Private Shout Mod --//
*/
    else
    {
        $a = mysql_fetch_row(sql_query("SELECT userid, date
                                            FROM shoutbox ORDER by id DESC
                                            LIMIT 1 ")) or print ("First Shout or an Error");

        if (empty($text) || strlen($text) == 1)
        {
            print("<span style='font-size: small; color: #ff0000;'>Shout can't be empty</span>");
        }

        else
        {
            sql_query("INSERT INTO shoutbox (id, userid, date, text)
                            VALUES ('id',".sqlesc($userid).", $date, ".sqlesc($text).")") or sqlerr(__FILE__, __LINE__);

            print "<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";
        }
    }
}

sb_images();

?>

</body>
</html>