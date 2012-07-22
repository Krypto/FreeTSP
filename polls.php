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

db_connect(false);
logged_in();

$action		= htmlspecialchars($_GET["action"]);
$pollid		= 0+$_GET["pollid"];
$returnto	= htmlentities($_POST["returnto"]);

if ($action	== "delete")
{
	if (get_user_class() < UC_MODERATOR)
		error_message("warn", "Warning", "Permission Denied.");

	if (!is_valid_id($pollid))
		error_message("error", "Error",	"Invalid ID.");

	$sure =	(int)$_GET['sure'];

	if (!$sure)
		error_message("warn", "Delete Poll", "<a href='?action=delete&amp;pollid=$pollid&amp;returnto=$returnto&amp;sure=1'>Do you really want to Delete a Poll?  Click	here if	you	are	sure!</a>");

		sql_query("DELETE
					FROM pollanswers
					WHERE pollid = $pollid") or	sqlerr();

		sql_query("DELETE
					FROM polls
					WHERE id = $pollid") or	sqlerr();

	if ($returnto == "main")
		header("Location: $site_url");
	else
		header("Location: $site_url/polls.php?deleted=1");
	die;
}

$rows =	sql_query("SELECT COUNT(*)
						FROM polls") or	sqlerr();

$row		= mysql_fetch_row($rows);
$pollcount	= $row[0];

if ($pollcount == 0)
	error_message("info", "Sorry...", "There are NO	Polls!");

$polls = sql_query("SELECT *
					FROM polls
					ORDER BY id	DESC
					LIMIT 1," .	($pollcount	- 1	)) or sqlerr();

site_header("Previous Polls");

print("<h1>Previous	Polls</h1>");

function srt($a,$b)
{
	if ($a[0] >	$b[0]) return -1;

	if ($a[0] <	$b[0]) return 1;

	return 0;
}

while ($poll = mysql_fetch_assoc($polls))
{
	$o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"],
	$poll["option5"], $poll["option6"],	$poll["option7"], $poll["option8"],	$poll["option9"],
	$poll["option10"], $poll["option11"], $poll["option12"], $poll["option13"],	$poll["option14"],
	$poll["option15"], $poll["option16"], $poll["option17"], $poll["option18"],	$poll["option19"]);

	print("<table width='737' border='1' cellspacing='0' cellpadding='10'><tr><td align='center'>\n");

	print("<p class='sub'>");

	$added = gmdate("Y-m-d",strtotime($poll['added'])) . " GMT (" .	(get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"])))	. "	ago)";

	print("$added");

	if (get_user_class() >=	UC_ADMINISTRATOR)
	{
		print("	- [<a href='makepoll.php?action=edit&amp;pollid=$poll[id]'><span style='font-weight:bold;'>Edit</span></a>]\n");
		print("	- [<a href='?action=delete&amp;pollid=$poll[id]'><span style='font-weight:bold;'>Delete</span></a>]\n");
	}

	print("<a name='$poll[id]'>");

	print("</a></p>\n");

	print("<table class='main' border='1' cellspacing='0' cellpadding='5'><tr><td class='text'>\n");

	print("<p align='center'><span style='font-weight:bold;'>" . $poll["question"] . "</span></p>");

	$pollanswers = sql_query("SELECT selection FROM	pollanswers	WHERE pollid=" . $poll["id"] . " and  selection	< 20") or sqlerr();

	$tvotes	= mysql_num_rows($pollanswers);

	$vs	= array(); // count	for	each option	([0]..[19])
	$os	= array(); // votes	and	options: array(array(123, "Option 1"), array(45, "Option 2"))

	// Count votes
	while ($pollanswer = mysql_fetch_row($pollanswers))

	$vs[$pollanswer[0]]	+= 1;

	reset($o);
	for	($i	= 0; $i	< count($o); ++$i)

	if ($o[$i])
		$os[$i]	= array($vs[$i], $o[$i]);

	// now os is an	array like this:
	if ($poll["sort"] == "yes")
		usort($os, srt);

	print("<table width='100%' class='main'	border='0' cellspacing='0' cellpadding='0'>\n");

	$i = 0;

	while ($a =	$os[$i])
	{
		if ($tvotes	> 0)
			$p = round($a[0] / $tvotes * 100);
		else
			$p = 0;

		if ($i % 2)
			$c = "";
		else
			$c = " bgcolor='#ECE9D8'";

		print("<tr><td class='embedded'$c>"	. $a[1]	. "&nbsp;&nbsp;</td><td	class='embedded'$c><img	src='{$image_dir}bar_left.gif' width='2' height='9'	border='0' alt='' title=''	/><img src='{$image_dir}bar.gif' width=' . ($p * 3)	. '	height='9' alt='' title='' /><img src='{$image_dir}bar_right.gif' width='2'	height='9' border='0' alt='' title='' /> $p%</td></tr>\n");
		++$i;
	}

	print("</table>\n");

	$tvotes	= number_format($tvotes);

	print("<p align='center'>Votes:	$tvotes</p>\n");
	print("</td></tr></table>\n");
	print("</td></tr></table>\n");

}

site_footer();

?>