<?php

/*
*-------------------------------------------------------------------------------*
*----------------	 |	____|		 |__   __/ ____|  __ \		  --------------*
*----------------	 | |__ _ __	___	 ___| |	| (___ | |__) |		  --------------*
*----------------	 |	__|	'__/ _ \/ _	\ |	 \___ \|  ___/		  --------------*
*----------------	 | |  |	| |	 __/  __/ |	 ____) | |			  --------------*
*----------------	 |_|  |_|  \___|\___|_|	|_____/|_|			  --------------*
*-------------------------------------------------------------------------------*
*---------------------------	FreeTSP	 v1.0	--------------------------------*
*-------------------   The Alternate BitTorrent	Source	 -----------------------*
*-------------------------------------------------------------------------------*
*-------------------------------------------------------------------------------*
*--	  This program is free software; you can redistribute it and /or modify	   --*
*--	  it under the terms of	the	GNU	General	Public License as published	by	  --*
*--	  the Free Software	Foundation;	either version 2 of	the	License, or		  --*
*--	  (at your option) any later version.									  --*
*--																			  --*
*--	  This program is distributed in the hope that it will be useful,		  --*
*--	  but WITHOUT ANY WARRANTY;	without	even the implied warranty of		  --*
*--	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See	the			  --*
*--	  GNU General Public License for more details.							  --*
*--																			  --*
*--	  You should have received a copy of the GNU General Public	License		  --*
*--	  along	with this program; if not, write to	the	Free Software			  --*
*--	Foundation,	Inc., 59 Temple	Place, Suite 330, Boston, MA  02111-1307 USA  --*
*--																			  --*
*-------------------------------------------------------------------------------*
*------------	Original Credits to	tbSource, Bytemonsoon, TBDev   -------------*
*-------------------------------------------------------------------------------*
*-------------			 Developed By: Krypto, Fireknight			------------*
*-------------------------------------------------------------------------------*
*-----------------		 First Release Date	August 2010		 -------------------*
*-----------				 http://www.freetsp.info				 -----------*
*------					   2010	FreeTSP	Development	Team				  ------*
*-------------------------------------------------------------------------------*
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_page_verify.php');

db_connect();
logged_in();

$newpage = new page_verify();
$newpage->check('_edit_');

if (!mkglobal("id:name:descr:type"))
	error_message("error", "Edit Failed!", "Missing	Form Data");

	$id	= isset($_POST['id']) ?	(int)$_POST['id'] :	0;

	if ( !is_valid_id($id) )
		die();

$res = sql_query("SELECT owner,	filename, save_as
					FROM torrents
					WHERE id = $id");

$row = mysql_fetch_assoc($res);

if (!$row)
	die();

if ($CURUSER['id'] != $row['owner']	&& get_user_class()	< UC_MODERATOR)
	error_message("error", "Edit Failed!", "You're Not the Owner! How did that happen?");

$updateset	= array();
$fname		= $row['filename'];

preg_match('/^(.+)\.torrent$/si', $fname, $matches);

$shortfname	= $matches[1];
$dname		= $row['save_as'];
$nfoaction	= $_POST['nfoaction'];

if ($nfoaction == 'update')
{
	$nfofile = $_FILES['nfo'];

	if (!$nfofile)
		die("No	Data " . var_dump($_FILES));

	if ($nfofile['size'] > 65535)
		error_message("error", "Edit Failed!", "NFO	is too Big!	Max	65,535 bytes.");

	$nfofilename = $nfofile['tmp_name'];

	if (@is_uploaded_file($nfofilename)	&& @filesize($nfofilename) > 0)
		$updateset[] = "nfo	= "	. sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
}
else
	if ($nfoaction == 'remove')
		$updateset[] = 'nfo	= ""';
		$updateset[] = "name = " . sqlesc($name);
		$updateset[] = "search_text	= "	. sqlesc(searchfield("$shortfname $dname $torrent"));
		$updateset[] = "descr =	" .	sqlesc($descr);
		$updateset[] = "ori_descr =	" .	sqlesc($descr);
		$updateset[] = "category = " . (0 +	$type);

	if (get_user_class() >=	UC_MODERATOR)
	{
		if ( isset($_POST['banned']) )
		{
			$updateset[] = 'banned = "yes"';
			$_POST['visible'] =	0;
		}
	else
		$updateset[] = 'banned = "no"';
	}

$updateset[] = "visible	= '" . ( isset($_POST['visible']) ?	'yes' :	'no') .	"'";

sql_query("UPDATE torrents
			SET " . join(",", $updateset) . "
			WHERE id =	$id");

write_log(htmlspecialchars($name) .	' was edited by	' .	 htmlspecialchars($CURUSER['username']));

$returl	= "details.php?id=$id&edited=1";

if (isset($_POST["returnto"]))
	$returl	.= "&returnto="	. urlencode($_POST["returnto"]);

header("Refresh: 0;	url=$returl");

?>