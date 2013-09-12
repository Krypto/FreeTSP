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

db_connect(false);
logged_in();

$id = 0 + $_GET["id"];

function code ($ibm_437, $swedishmagic = false)
{
    $table437 = array("\200",
                      "\201",
                      "\202",
                      "\203",
                      "\204",
                      "\205",
                      "\206",
                      "\207",
                      "\210",
                      "\211",
                      "\212",
                      "\213",
                      "\214",
                      "\215",
                      "\216",
                      "\217",
                      "\220",
                      "\221",
                      "\222",
                      "\223",
                      "\224",
                      "\225",
                      "\226",
                      "\227",
                      "\230",
                      "\231",
                      "\232",
                      "\233",
                      "\234",
                      "\235",
                      "\236",
                      "\237",
                      "\240",
                      "\241",
                      "\242",
                      "\243",
                      "\244",
                      "\245",
                      "\246",
                      "\247",
                      "\250",
                      "\251",
                      "\252",
                      "\253",
                      "\254",
                      "\255",
                      "\256",
                      "\257",
                      "\260",
                      "\261",
                      "\262",
                      "\263",
                      "\264",
                      "\265",
                      "\266",
                      "\267",
                      "\270",
                      "\271",
                      "\272",
                      "\273",
                      "\274",
                      "\275",
                      "\276",
                      "\277",
                      "\300",
                      "\301",
                      "\302",
                      "\303",
                      "\304",
                      "\305",
                      "\306",
                      "\307",
                      "\310",
                      "\311",
                      "\312",
                      "\313",
                      "\314",
                      "\315",
                      "\316",
                      "\317",
                      "\320",
                      "\321",
                      "\322",
                      "\323",
                      "\324",
                      "\325",
                      "\326",
                      "\327",
                      "\330",
                      "\331",
                      "\332",
                      "\333",
                      "\334",
                      "\335",
                      "\336",
                      "\337",
                      "\340",
                      "\341",
                      "\342",
                      "\343",
                      "\344",
                      "\345",
                      "\346",
                      "\347",
                      "\350",
                      "\351",
                      "\352",
                      "\353",
                      "\354",
                      "\355",
                      "\356",
                      "\357",
                      "\360",
                      "\361",
                      "\362",
                      "\363",
                      "\364",
                      "\365",
                      "\366",
                      "\367",
                      "\370",
                      "\371",
                      "\372",
                      "\373",
                      "\374",
                      "\375",
                      "\376",
                      "\377");

    $tablehtml = array("&#x00c7;",
                       "&#x00fc;",
                       "&#x00e9;",
                       "&#x00e2;",
                       "&#x00e4;",
                       "&#x00e0;",
                       "&#x00e5;",
                       "&#x00e7;",
                       "&#x00ea;",
                       "&#x00eb;",
                       "&#x00e8;",
                       "&#x00ef;",
                       "&#x00ee;",
                       "&#x00ec;",
                       "&#x00c4;",
                       "&#x00c5;",
                       "&#x00c9;",
                       "&#x00e6;",
                       "&#x00c6;",
                       "&#x00f4;",
                       "&#x00f6;",
                       "&#x00f2;",
                       "&#x00fb;",
                       "&#x00f9;",
                       "&#x00ff;",
                       "&#x00d6;",
                       "&#x00dc;",
                       "&#x00a2;",
                       "&#x00a3;",
                       "&#x00a5;",
                       "&#x20a7;",
                       "&#x0192;",
                       "&#x00e1;",
                       "&#x00ed;",
                       "&#x00f3;",
                       "&#x00fa;",
                       "&#x00f1;",
                       "&#x00d1;",
                       "&#x00aa;",
                       "&#x00ba;",
                       "&#x00bf;",
                       "&#x2310;",
                       "&#x00ac;",
                       "&#x00bd;",
                       "&#x00bc;",
                       "&#x00a1;",
                       "&#x00ab;",
                       "&#x00bb;",
                       "&#x2591;",
                       "&#x2592;",
                       "&#x2593;",
                       "&#x2502;",
                       "&#x2524;",
                       "&#x2561;",
                       "&#x2562;",
                       "&#x2556;",
                       "&#x2555;",
                       "&#x2563;",
                       "&#x2551;",
                       "&#x2557;",
                       "&#x255d;",
                       "&#x255c;",
                       "&#x255b;",
                       "&#x2510;",
                       "&#x2514;",
                       "&#x2534;",
                       "&#x252c;",
                       "&#x251c;",
                       "&#x2500;",
                       "&#x253c;",
                       "&#x255e;",
                       "&#x255f;",
                       "&#x255a;",
                       "&#x2554;",
                       "&#x2569;",
                       "&#x2566;",
                       "&#x2560;",
                       "&#x2550;",
                       "&#x256c;",
                       "&#x2567;",
                       "&#x2568;",
                       "&#x2564;",
                       "&#x2565;",
                       "&#x2559;",
                       "&#x2558;",
                       "&#x2552;",
                       "&#x2553;",
                       "&#x256b;",
                       "&#x256a;",
                       "&#x2518;",
                       "&#x250c;",
                       "&#x2588;",
                       "&#x2584;",
                       "&#x258c;",
                       "&#x2590;",
                       "&#x2580;",
                       "&#x03b1;",
                       "&#x00df;",
                       "&#x0393;",
                       "&#x03c0;",
                       "&#x03a3;",
                       "&#x03c3;",
                       "&#x03bc;",
                       "&#x03c4;",
                       "&#x03a6;",
                       "&#x0398;",
                       "&#x03a9;",
                       "&#x03b4;",
                       "&#x221e;",
                       "&#x03c6;",
                       "&#x03b5;",
                       "&#x2229;",
                       "&#x2261;",
                       "&#x00b1;",
                       "&#x2265;",
                       "&#x2264;",
                       "&#x2320;",
                       "&#x2321;",
                       "&#x00f7;",
                       "&#x2248;",
                       "&#x00b0;",
                       "&#x2219;",
                       "&#x00b7;",
                       "&#x221a;",
                       "&#x207f;",
                       "&#x00b2;",
                       "&#x25a0;",
                       "&#x00a0;");

    $s = htmlspecialchars($ibm_437);

    //-- 0-9, 11-12, 14-31, 127 (decimalt) --//
    $control = array("\000",
                     "\001",
                     "\002",
                     "\003",
                     "\004",
                     "\005",
                     "\006",
                     "\007",
                     "\010",
                     "\011",
        /*"\012",*/
                     "\013",
                     "\014",
        /*"\015",*/
                     "\016",
                     "\017",
                     "\020",
                     "\021",
                     "\022",
                     "\023",
                     "\024",
                     "\025",
                     "\026",
                     "\027",
                     "\030",
                     "\031",
                     "\032",
                     "\033",
                     "\034",
                     "\035",
                     "\036",
                     "\037",
                     "\177");

    $s = str_replace($control, "  ", $s);

    if ($swedishmagic)
    {
        $s = str_replace("\345", "\206", $s); //-- Code Windows "?" To Dos. --//
        $s = str_replace("\344", "\204", $s); //-- Code Windows "?" To Dos. --//
        $s = str_replace("\366", "\224", $s); //-- Code Windows "?" To Dos. --//
        //$s = str_replace("\304","\216",$s); //-- Code Windows "?" To Dos. --//
        //$s = "[ -~]\\xC4[a-za-z]";
        //-- Couldn't Get ^ And $ To Work, Even Through I Read The Man-pages, --//
        //-- I'm Probably Too Tired And Too Unfamiliar With Posix Regexps Right Now --//
        $s = str_replace("([ -~])\305([ -~])", "\\1\217\\2", $s); //-- ? --//
        $s = str_replace("([ -~])\304([ -~])", "\\1\216\\2", $s); //-- ? --//
        $s = str_replace("([ -~])\326([ -~])", "\\1\231\\2", $s); //-- ? --//
        $s = str_replace("\311", "\220", $s); // ?
        $s = str_replace("\351", "\202", $s); // ?
    }

    $s = str_replace($table437, $tablehtml, $s);

    return $s;
}

$id = 0 + $_GET["id"];

if ($CURUSER["class"] < UC_POWER_USER || !is_valid_id($id))
{
    die;
}

$r = sql_query("SELECT name,nfo
                FROM torrents
                WHERE id=".sqlesc($id)."") or sqlerr();

$a = mysql_fetch_assoc($r) or die("Puke");

//-- View Might Be One Of: "magic", "latin-1", "strict" Or "fonthack" --//
$view = "";

if (isset($_GET["view"]))
{
    $view = unesc($_GET["view"]);
}
else
{
    $view = "magic"; //-- Default Behavior --//
}

$nfo = "";

if ($view == "latin-1" || $view == "fonthack")
{
    //-- Do Not Convert From ibm-437, Read Bytes As Is. --//
    //-- NOTICE: TBSource Specifies Latin-1 Encoding In functions/function_main.php: --//
    //-- site_header() --//
    $nfo = htmlentities(($a["nfo"]));
}
else
{
    //-- Convert From ibm-437 To Html Unicode Entities. --//
    //-- Take Special Care Of Swedish Letters If In Magic View. --//
    $nfo = code($a["nfo"], $view == "magic");
}

site_header();

echo("<h1>NFO for <a href='details.php?id=$id'>".htmlentities($a["name"])."</a></h1>
<table border='1' align='center' cellspacing='0' cellpadding='10'>
  <tr>
    <td align='center' width='50%'>
      <a href='viewnfo.php?id=".$id."&amp;view=magic' title='Magisk IBM-437'>
      <span style='font-weight:bold;'>DOS-vy</span></a></td>
    <td align='center' width='50%'>
      <a href='viewnfo.php?id=".$id."&amp;view=latin-1' title='Latin-1'><span style='font-weight:bold;'>Windows-vy</span></a></td>
  </tr>
  <tr>
    <td colspan='3'>
      <table border='1' cellspacing='0' cellpadding='5'><tr>
        <td class='text'>");

//-- About To Output NFO Data --//
if ($view == "fonthack")
{
    //-- Please Notice: Ms Linedraw's Glyphs Are Included In The Courier New Font --//
    //-- As Of Courier New Version 2.0, But Uses The Correct Mappings Instead. --//
    //-- [url="http://support.microsoft.com/kb/q179422/"]http://support.microsoft.com/kb/q179422/[/url] --//
    echo("<pre style=\"font-size:10pt; font-family: 'MS LineDraw', 'Terminal', monospace;\">");
}
else
{
    //-- Ie6.0 Need To Know Which Font To Use, Mozilla Can Figure It Out In Its Own --//
    //-- (windows Firefox At Least) --//
    //-- Anything Else Than 'courier New' Looks Pretty Broken. --//
    //-- 'Lucida Console', 'Fixedsys' --//
    echo("<pre style=\"font-size:10pt; font-family: 'Courier New', monospace;\">");
}

//-- Writes The (Eventually Modified) NFO Data To Output, First Formating Urls. --//
echo format_urls($nfo);
echo("</pre>\n");
echo("</td></tr></table></td></tr></table>");

site_footer();

?>