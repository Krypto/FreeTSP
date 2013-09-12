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

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->check('_edit_');

if (!mkglobal("id:name:descr:type"))
{
    error_message("error", "Edit Failed!", "Missing Form Data");
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if (!is_valid_id($id))
{
    die();
}

$res = sql_query("SELECT owner, filename, save_as
                    FROM torrents
                    WHERE id = $id");

$row = mysql_fetch_assoc($res);

if (!$row)
{
    die();
}

if ($CURUSER['id'] != $row['owner'] && get_user_class() < UC_MODERATOR)
{
    error_message("error", "Edit Failed!", "You're Not the Owner! How did that happen?");
}

$updateset = array();
$fname     = $row['filename'];

preg_match('/^(.+)\.torrent$/si', $fname, $matches);

$shortfname = $matches[1];
$dname      = $row['save_as'];
$nfoaction  = $_POST['nfoaction'];

if (!empty($_POST['poster']))
{
    $poster = unesc($_POST['poster']);
}

if ($nfoaction == 'update')
{
    $nfofile = $_FILES['nfo'];

    if (!$nfofile)
    {
        die("No Data ".var_dump($_FILES));
    }

    if ($nfofile['size'] > 65535)
    {
        error_message("error", "Edit Failed!", "NFO is too Big! Max 65,535 bytes.");
    }

    $nfofilename = $nfofile['tmp_name'];

    if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0)
    {
        $updateset[] = "nfo = ".sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
    }
}
else
{
    if ($nfoaction == 'remove')
    {
        $updateset[] = 'nfo = ""';
    }
}

$updateset[] = "name = ".sqlesc($name);
$updateset[] = "anonymous = '".($_POST["anonymous"] ? "yes" : "no")."'";
$updateset[] = "search_text = ".sqlesc(searchfield("$shortfname $dname $torrent"));
$updateset[] = "descr = ".sqlesc($descr);
$updateset[] = "ori_descr = ".sqlesc($descr);
$updateset[] = "category = ".(0 + $type);

if (get_user_class() >= UC_MODERATOR)
{
    if (isset($_POST['banned']))
    {
        $updateset[]      = 'banned = "yes"';
        $_POST['visible'] = 0;
    }
    else
    {
        $updateset[] = 'banned = "no"';
    }

    if ($_POST["sticky"] == "yes")
    {
        $updateset[] = "sticky = 'yes'";
    }
    else
    {
        $updateset[] = "sticky = 'no'";
    }
}

$updateset[] = "freeleech = '".( isset($_POST['freeleech']) ? 'yes' : 'no')."'";
$updateset[] = "visible = '".(isset($_POST['visible']) ? 'yes' : 'no')."'";
$updateset[] = "poster = ".sqlesc($poster);

sql_query("UPDATE torrents
            SET ".join(",", $updateset)."
            WHERE id = $id");

write_log(htmlspecialchars($name).' was edited by '.htmlspecialchars($CURUSER['username']));

$returl = "details.php?id=$id&edited=1";

if (isset($_POST["returnto"]))
{
    $returl .= "&returnto=".urlencode($_POST["returnto"]);
}

header("Refresh: 0; url=$returl");

?>