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

db_connect();
logged_in();

$search	= isset($_GET['search']) ? strip_tags(trim($_GET['search'])) : '';
$class	= isset($_GET['class'])	? $_GET['class'] : '-';
$letter	= '';
$q = '';

if ($class == '-' || !is_valid_id($class))
	$class = '';

if ($search	!= '' || $class)
{
	$query = "username LIKE	" .	sqlesc("%$search%")	. "	and	status='confirmed'";

	if ($search)
		$q = "search=" . htmlspecialchars($search);
}
else
{
	$letter	= isset($_GET['letter']) ? trim((string)$_GET["letter"]) : '';

	if (strlen($letter)	> 1)
		die;

	if ($letter	== "" || strpos("abcdefghijklmnopqrstuvwxyz0123456789",	$letter) === false)
		$letter	= "";
		$query	= "username	LIKE '$letter%'	and	status='confirmed'";
		$q		= "letter=$letter";
}

if (ctype_digit($class))
{
	$query .= "	and	class=$class";
	$q	   .= ($q ?	"&amp;"	: "") .	"class=$class";
}

site_header("Users");

echo("<h1>Users</h1>\n");

echo("<div align='center'>");
echo("<form	method='get' action='users.php?'>\n");
echo("Search: <input type='text' name='search' size='30' />\n");
echo("<select name='class'>\n");
echo("<option value='-'>(any class)</option>\n");

for	($i	= 0;;++$i)
{
	if ($c = get_user_class_name($i))
		echo("<option value='$i'" .	(ctype_digit($class) &&	$class == $i ? " selected='selected'" :	"")	. ">$c</option>\n");
	else
		break;
}

echo("</select>\n");
echo("<input type='submit' class='btn' value='Okay'	/>\n");
echo("</form>\n");
echo("<p>\n");
echo("<a href='users.php'><span	style='font-weight:bold;'>ALL</span></a> - \n");

for	($i	= 97; $i < 123;	++$i)
{
	$l = chr($i);
	$L = chr($i	- 32);

	if ($l == $letter)
		echo("<span	style='font-weight:bold;'>$L</span>\n");
	else
		echo("<a href='?letter=$l'><span style='font-weight:bold;'>$L</span></a>\n");
}

echo("</p>\n");

$page		=	isset($_GET['page']) ? (int)$_GET['page'] :	1;
$perpage	= 25;
$browsemenu	= '';
$pagemenu	=	'';

$res = sql_query("SELECT COUNT(*)
					FROM users
					WHERE $query")	or sqlerr(__FILE__,__LINE__);

$arr = mysql_fetch_row($res);

if($arr[0] > $perpage)
{
	$pages = floor($arr[0] / $perpage);

	if ($pages * $perpage <	$arr[0])
		++$pages;

	if ($page <	1)
		$page =	1;
	else
	if ($page >	$pages)
		$page =	$pages;

	for	($i	= 1; $i	<= $pages; ++$i)
	{
		$PageNo	= $i+1;
		if($PageNo < ($page	- 2))
			continue;

		if ($i == $page)
			$pagemenu .= "<span	style='font-weight:bold;'>$i</span>\n";
		else
			$pagemenu .= "<a href='?$q&amp;page=$i'><span style='font-weight:bold;'>$i</span></a>\n";

		if($PageNo > ($page	+ 3)) break;
	}

	if ($page == 1)
		$browsemenu	.= "<span style='font-weight:bold;'>&lsaquo; Prev</span>";
	else
		$browsemenu	.= "<a href='users.php?$q&amp;page=" . ($page -	1) . "'><span style='font-weight:bold;'>&laquo;	Prev</span></a>";
		$browsemenu	.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
	$browsemenu	.= "<span style='font-weight:bold;'>Next &rsaquo;</span>";
else
	$browsemenu	.= "<a href='users.php?$q&amp;page=" . ($page +	1) . "'><span style='font-weight:bold;'>Next &raquo;</span></a>";
}

echo("<p>$browsemenu<br	/>$pagemenu</p>");
//echo ($arr[0]	> $perpage)	? "<p>$browsemenu<br /><br /></p>" : '<br /><br	/>';

$offset	= ($page * $perpage) - $perpage;

if($arr[0] > 0)
{
	$res = sql_query("SELECT users.*, countries.name, countries.flagpic
						FROM users FORCE INDEX ( username )
						LEFT JOIN countries	ON country = countries.id
						WHERE $query
						ORDER BY username
						LIMIT $offset,$perpage") or sqlerr(__FILE__,__LINE__);


	echo("<table border='1'	cellspacing='0'	cellpadding='5'>");
	echo("<tr>
			<td class='colhead' align='left'>User	name</td>
			<td class='colhead'>Registered</td>
			<td class='colhead'>Last access</td>
			<td class='colhead' align='left'>Class</td>
			<td class='colhead'>Country</td>
			</tr>");

	while($row = mysql_fetch_assoc($res))
	{
		$country = ($row['name'] !=	NULL) ?	"<td  class='rowhead'style='padding: 0px' align='center'><img src='{$image_dir}flag/{$row[flagpic]}' width='32'	height='20'	border='0' alt='". htmlspecialchars($row[name])	."'	title='". htmlspecialchars($row[name]) ."'/></td>" : "<td class='rowhead' align='center'>---</td>";

		echo("<tr>
				<td class='rowhead' align='left'><a href='userdetails.php?id=$row[id]'><span style='font-weight:bold;'>$row[username]</span></a>&nbsp;&nbsp;"	.($row['donor']	== 'yes' ? "<img src='{$image_dir}star.png'	width='16' height='16' border='0' alt='Donor' title='Donor'	/>"	: "")."&nbsp;" .($row['warned']	== 'yes' ? "<img src='{$image_dir}warned.png' width='16' height='16' border='0'	alt='Warned' title='Warned'	/>"	: "")."</td>
				<td class='rowhead'>$row[added]</td>
				<td class='rowhead'>$row[last_access]</td>
				<td class='rowhead' align='left'>" . get_user_class_name($row["class"]) .	"</td>
				$country
			</tr>");
	}
	echo("</table>\n");
}

//echo("<p>$pagemenu<br	/>$browsemenu</p>");
echo ($arr[0] >	$perpage) ?	"<br /><p>$browsemenu</p>" : '<br /><br	/></div>';

site_footer();

die;

?>