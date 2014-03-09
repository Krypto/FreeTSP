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
require_once(FUNC_DIR.'function_benc.php');
require_once(FUNC_DIR.'function_page_verify.php');

ini_set("upload_max_filesize", $max_torrent_size);

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->check('_upload_');

if (get_user_class() < UC_USER)
{
    die;
}

foreach (explode(":", "descr:type:name")
         AS
         $v)
{
    if (!isset($_POST[$v]))
    {
        error_message("error", "Upload Failed!", "Missing Form Data");
    }
}

if (!isset($_FILES["file"]))
{
    error_message("error", "Upload Failed!", "Missing Form Data");
}

$f     = $_FILES["file"];
$fname = unesc($f["name"]);

if (empty($fname))
{
    error_message("error", "Upload Failed!", "Empty filename!");
}

if ($_POST['uplver'] == 'yes')
{
    $anonymous = "yes";
    $anon      = "Anonymous";
}
else
{
    $anonymous = "no";
    $anon      = $CURUSER["username"];
}

if ($_POST['freeleech'] == 'yes')
{
    $freeleech = "yes";
}
else
{
    $freeleech = "no";
}

$nfo = sqlesc('');

if (isset($_FILES['nfo']) && !empty($_FILES['nfo']['name']))
{
    $nfofile = $_FILES['nfo'];

    if ($nfofile['name'] == '')
    {
        error_message("error", "Upload Failed!", "No NFO!");
    }

    if ($nfofile['size'] == 0)
    {
        error_message("error", "Upload Failed!", "0-byte NFO");
    }

    if ($nfofile['size'] > 65535)
    {
        error_message("error", "Upload Failed!", "NFO is too big! Max 65,535 bytes.");
    }

    $nfofilename = $nfofile['tmp_name'];

    if (@!is_uploaded_file($nfofilename))
    {
        error_message("error", "Upload Failed!", "NFO Upload Failed");
    }

    $nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));
}

$request = (((isset($_POST['request']) && is_valid_id($_POST['request'])) ? intval($_POST['request']) : 0));
$offer   = (((isset($_POST['offer']) && is_valid_id($_POST['offer'])) ? intval($_POST['offer']) : 0));

$descr = unesc($_POST["descr"]);

if (!$descr)
{
    error_message("error", "Upload Failed!", "You MUST Enter a Description!");
}

$catid = (0 + $_POST["type"]);

if (!is_valid_id($catid))
{
    error_message("error", "Upload Failed!", "You MUST Select a Category to put the torrent in!");
}

if (!validfilename($fname))
{
    error_message("error", "Upload Failed!", "Invalid Filename!");
}

if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
{
    error_message("error", "Upload Failed!", "Invalid Filename (Not a .torrent).");
}

$shortfname = $torrent = $matches[1];

if (!empty($_POST["name"]))
{
    $torrent = unesc($_POST["name"]);
}

if (!empty($_POST["poster"]))
{
     $poster = unesc($_POST["poster"]);
}

$tmpname = $f["tmp_name"];

if (!is_uploaded_file($tmpname))
{
    error_message("error", "Upload Failed!", "eek");
}

if (!filesize($tmpname))
{
    error_message("error", "Upload Failed!", "Empty File!");
}

$dict = bdec_file($tmpname, $max_torrent_size);

if (!isset($dict))
{
    error_message("error", "Upload Failed!", "What the hell did you upload? This is NOT a Bencoded File!");
}

function dict_check ($d, $s)
{
    if ($d["type"] != "dictionary")
    {
        error_message("error", "Upload Failed", "NOT a Dictionary");
    }

    $a   = explode(":", $s);
    $dd  = $d["value"];
    $ret = array();
    $t   = '';

    foreach ($a
             AS
             $k)
    {
        unset($t);

        if (preg_match('/^(.*)\((.*)\)$/', $k, $m))
        {
            $k = $m[1];
            $t = $m[2];
        }

        if (!isset($dd[$k]))
        {
            error_message("error", "Upload Failed!", "Dictionary is missing Key(s)");
        }

        if (isset($t))
        {
            if ($dd[$k]["type"] != $t)
            {
                error_message("error", "Upload Failed!", "Invalid entry in Dictionary");
            }
            $ret[] = $dd[$k]["value"];
        }
        else
        {
            $ret[] = $dd[$k];
        }
    }
    return $ret;
}

function dict_get ($d, $k, $t)
{
    if ($d["type"] != "dictionary")
    {
        error_message("error", "Upload Failed!", "Not a Dictionary");
    }

    $dd = $d["value"];

    if (!isset($dd[$k]))
    {
        return;
    }

    $v = $dd[$k];

    if ($v["type"] != $t)
    {
        error_message("error", "Upload Failed!", "Invalid Dictionary entry type");
    }
    return $v["value"];
}

list($ann, $info)            = dict_check($dict, "announce(string):info");
list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");

if (!in_array($ann, $announce_urls, 1))
{
    error_message("error", "Upload Failed!", "Invalid Announce URL! Must be <strong>".$announce_urls[0]."</strong>");
}

if (strlen($pieces) % 20 != 0)
{
    error_message("error", "Upload Failed!", "Invalid Pieces");
}

$filelist = array();
$totallen = dict_get($info, "length", "integer");

if (isset($totallen))
{
    $filelist[] = array($dname,
                        $totallen);

    $type = "single";
}
else
{
    $flist = dict_get($info, "files", "list");

    if (!isset($flist))
    {
        error_message("error", "Upload Failed!", "Missing both Length and Files");
    }

    if (!count($flist))
    {
        error_message("error", "Upload Failed!", "No Files");
    }

    $totallen = 0;

    foreach ($flist
             AS
             $fn)
    {
        list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
        $totallen += $ll;
        $ffa = array();

        foreach ($ff
                 AS
                 $ffe)
        {
            if ($ffe["type"] != "string")
            {
                error_message("error", "Upload Failed!", "Filename Error");
            }

            $ffa[] = $ffe["value"];
        }

        if (!count($ffa))
        {
            error_message("error", "Upload Failed!", "Filename Error");
        }

        $ffe        = implode("/", $ffa);
        $filelist[] = array($ffe,
                            $ll);
    }
    $type = "multi";
}

$infohash = pack("H*", sha1($info["string"]));

//-- Replace Punctuation Characters With Spaces --//

$torrent = str_replace("_", " ", $torrent);
$torrent = str_replace(".torrent", " ", $torrent);
$torrent = str_replace(".rar", " ", $torrent);
$torrent = str_replace(".avi", " ", $torrent);
$torrent = str_replace(".mpeg", " ", $torrent);
$torrent = str_replace(".exe", " ", $torrent);
$torrent = str_replace(".zip", " ", $torrent);
$torrent = str_replace(".wmv", " ", $torrent);
$torrent = str_replace(".iso", " ", $torrent);
$torrent = str_replace(".bin", " ", $torrent);
$torrent = str_replace(".txt", " ", $torrent);
$torrent = str_replace(".nfo", " ", $torrent);
$torrent = str_replace(".7z", " ", $torrent);
$torrent = str_replace(".mp3", " ", $torrent);
$torrent = str_replace(".", " ", $torrent);

$nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));
$poster = unesc($_POST['poster']);

$ret = sql_query("INSERT INTO torrents (search_text, filename, owner, visible, anonymous, freeleech,
info_hash, name, size, numfiles, type, descr, ori_descr, category, save_as, added, last_action, nfo, offer, request, poster)
                    VALUES (".implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"),
                                                                     $fname,
                                                                     $CURUSER["id"],
                                                                     "no",
                                                                     $anonymous,
                                                                     $freeleech,
                                                                     $infohash,
                                                                     $torrent,
                                                                     $totallen,
                                                                     count($filelist),
                                                                     $type,
                                                                     $descr,
                                                                     $descr,
                                                                     0 + $_POST["type"],
                                                                     $dname))).",
                                                                     '".get_date_time()."',
                                                                     '".get_date_time()."',
                                                                     $nfo,
                                                                     $offer,
                                                                     $request,
                                                                     '".$poster."')");

if (!$ret)
{
    if (mysql_errno() == 1062)
    {
        error_message("error", "Upload Failed!", "Torrent Already Uploaded!");
    }
    mysql_error();
}

$id = mysql_insert_id();

@sql_query("DELETE
            FROM files
            WHERE torrent = $id");

foreach ($filelist
         AS
         $file)
{
    @sql_query("INSERT
                INTO files (torrent, filename, size)
                VALUES ($id, ".sqlesc($file[0]).",".$file[1].")");
}

move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");

//-- Start Requests And Offers Notifications --//
$filled = 0;

//-- If It Was An Offer Notify The Folks Who Liked It --//
if ($offer > 0)
{
    $res_offer = sql_query('SELECT user_id
                             FROM offer_votes
                             WHERE vote = \'yes\'
                             AND user_id != '.$CURUSER['id'].'
                             AND offer_id = '.$offer) or sqlerr(__FILE__, __LINE__);

    $subject = sqlesc('An Offer you Voted for!');

    $message = sqlesc("Hi, \n\n A Offer you were interested in has just been uploaded! \n\n [url=$site_url/details.php?id=".$id."][b]".htmlspecialchars($torrent, ENT_QUOTES)."[/b][/url].");

    $time = sqlesc(get_date_time());

         while($arr_offer = mysql_fetch_assoc($res_offer))
         {
             sql_query('INSERT INTO messages (sender, receiver, added, msg, subject, saved, location)
                         VALUES(0, '.$arr_offer['user_id'].', '.$time.', '.$message.', '.$subject.', \'yes\', 1)') or sqlerr(__FILE__, __LINE__);
         }

    sql_query("UPDATE offers
                SET filled_torrent_id = '$id'
                WHERE id = $offer") or sqlerr(__FILE__, __LINE__);

    write_log('Offered torrent '.$id.' ('.$torrent.') was Uploaded by '.$CURUSER['username']);

    $filled = 1;
}

//-- If It Was A Request Notify The Folks Who Voted --//
if ($request > 0)
{
    $res_req = sql_query('SELECT user_id
                            FROM request_votes
                            WHERE vote = \'yes\'
                            AND request_id = '.$request) or sqlerr(__FILE__, __LINE__);

    $subject = sqlesc('A Request you were interested in!');

    $message = sqlesc("Hi, \n\n A Request you were interested in has just been Uploaded! \n\n [url=$site_url/details.php?id=".$id."][b]".htmlspecialchars($torrent, ENT_QUOTES)."[/b][/url].");

    $time = sqlesc(get_date_time());

    while($arr_req = mysql_fetch_assoc($res_req))
    {
        sql_query('INSERT INTO messages (sender, receiver, added, msg, subject, saved, location)
                    VALUES(0, '.$arr_req['user_id'].', '.$time.', '.$message.', '.$subject.', \'yes\', 1)') or sqlerr(__FILE__, __LINE__);
    }

    $res_req_owner = sql_query('SELECT requested_by_user_id
                                FROM requests
                                WHERE id = '.$request) or sqlerr(__FILE__, __LINE__);

    $subject = sqlesc('A Request you made!');

    $message = sqlesc("Hi, \n A Request you made has just been Uploaded! \n [url=$site_url/details.php?id=".$id."][b]".htmlspecialchars($torrent, ENT_QUOTES)."[/b][/url].");

    $time = sqlesc(get_date_time());

    while($arr_req_owner = mysql_fetch_assoc($res_req_owner))
    {
         sql_query('INSERT INTO messages (sender, receiver, added, msg, subject, saved, location)
                     VALUES(0, '.$arr_req_owner['requested_by_user_id'].', '.$time.', '.$message.', '.$subject.', \'yes\', 1)') or sqlerr(__FILE__, __LINE__);
    }

    sql_query("UPDATE requests
                SET filled_by_username = '$CURUSER[username]', filled_torrent_id = '$id', filled_by_user_id = '$CURUSER[id]'
                WHERE id = $request") or sqlerr(__FILE__, __LINE__);

/*
    sql_query('DELETE FROM requests WHERE id ='.$request);
    sql_query('DELETE FROM request_votes WHERE request_id ='.$request);
    sql_query('DELETE FROM comments WHERE request ='.$request);
*/

    write_log('Request for torrent '.$id.' ('.$torrent.') was Filled by '.$CURUSER['username']);

    $filled = 1;
}
//-- Finish Requests And Offers Notifications --//

write_log("Torrent $id ($torrent) was Uploaded by ".$CURUSER["username"]);

//-- RSS Feeds --//

if (($fd1 = @fopen("rss.xml", "w")) && ($fd2 = fopen("rssdd.xml", "w")))
{
    $cats = "";
    $res  = sql_query("SELECT id, name
                        FROM categories");

    while ($arr = mysql_fetch_assoc($res))
    {
        $cats[$arr["id"]] = $arr["name"];
    }

    $s = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n<rss version=\"0.91\">\n<channel>\n"."<title>$site_name</title>\n<description>0-week torrents</description>\n<link>$site_url/</link>\n";
    @fwrite($fd1, $s);
    @fwrite($fd2, $s);

    $r = sql_query("SELECT id,name,descr,filename,category
                    FROM torrents
                    ORDER BY added DESC
                    LIMIT 15") or sqlerr(__FILE__, __LINE__);

    while ($a = mysql_fetch_assoc($r))
    {
        $cat = $cats[$a["category"]];

        $s = "<item>\n<title>".htmlspecialchars($a["name"]." ($cat)")."</title>\n"."<description>".htmlspecialchars($a["descr"])."</description>\n";

        @fwrite($fd1, $s);
        @fwrite($fd2, $s);
        @fwrite($fd1, "<link>$site_url/details.php?id=$a[id]&amp;hit=1</link>\n</item>\n");
        $filename = htmlspecialchars($a["filename"]);
        @fwrite($fd2, "<link>$site_url/download.php/$a[id]/$filename</link>\n</item>\n");
    }
    $s = "</channel>\n</rss>\n";

    @fwrite($fd1, $s);
    @fwrite($fd2, $s);
    @fclose($fd1);
    @fclose($fd2);
}

//-- Email Notifs --//
/*
$res = sql_query("SELECT name
                    FROM categories
                    WHERE id = $catid") or sqlerr();

$arr = mysql_fetch_assoc($res);
$cat = $arr["name"];
$res = sql_query("SELECT email
                    FROM users
                    WHERE enabled='yes'
                    AND notifs LIKE '%[cat$catid]%'") or sqlerr();

$uploader = $CURUSER['username'];

$size = mksize($totallen);
$description = ($html ? strip_tags($descr) : $descr);

$body = <<<EOD
A new torrent has been uploaded.

Name: $torrent
Size: $size
Category: $cat
Uploaded by: $uploader

Description
-------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------

You can use the URL below to download the torrent (you may have to login).

$site_url/details.php?id=$id&hit=1

--
$site_name
EOD;
$to = "";
$nmax = 100; // Max recipients per message
$nthis = 0;
$ntotal = 0;
$total = mysql_num_rows($res);
while ($arr = mysql_fetch_row($res))
{
    if ($nthis == 0)
    {
        $to = $arr[0];
    }
    else
    {
        $to .= ",".$arr[0];
    }
    ++$nthis;
    ++$ntotal;
    if ($nthis == $nmax || $ntotal == $total)
    {
        if (!mail("Multiple recipients <$site_email>", "New torrent - $torrent", $body,
            "From: $site_email\r\nBcc: $to", "-f$site_email"))
        {
            "error","Signup Failed!",("error","Error", "Your torrent has been been Uploaded. DO NOT RELOAD THE PAGE!\n" .
                    "There was however a problem delivering the e-mail notifcations.\n" .
                    "Please let an Administrator know about this Error!\n");
        }
        $nthis = 0;
    }
}
*/

header("Location: $site_url/details.php?id=$id&uploaded=1");

?>
