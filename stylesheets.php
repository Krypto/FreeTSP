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

require_once( dirname( __FILE__	) .	DIRECTORY_SEPARATOR	. 'functions' .	DIRECTORY_SEPARATOR	. 'function_main.php' );
require_once( INCL_DIR . 'function_user.php' );
require_once( INCL_DIR . 'function_vfunctions.php' );

db_connect(	true );
logged_in();

if ( get_user_class() <	UC_ADMINISTRATOR )
{
	die("Access Denied.");
}

site_header("Stylesheets");

//	Delete A Stylesheet
$sure =	$_GET['sure'];

if ( $sure == "yes"	)
{
	$delid = $_GET['delid'];

	$query = "DELETE
				FROM stylesheets
				WHERE id=" . sqlesc( $delid	) .	"
				LIMIT 1";

	$sql = sql_query( $query );

	error_message("success", "Success",	"<a	href='/stylesheets.php'>Stylesheet Successfully	Deleted!  Click	to Return.</a>");
}

$delid	= $_GET['delid'];
$name	= $_GET['cat'];

if ( $delid	> 0	)
{
	error_message("warn", "Warning", "<a href='/stylesheets.php?delid=$delid&amp;cat=$name&amp;sure=yes'>Click here	if you want	to Delete this Stylesheet</a>");
}

//	Edit A Stylesheet
$edited	= $_GET['edited'];

if ( $edited ==	1 )
{
	$id		= $_GET['id'];
	$uri	= $_GET['uri'];
	$name	= $_GET['name'];

	$query = "UPDATE stylesheets
				SET	uri	= '$uri', name = '$name'
				WHERE id='$id'";

	$sql = sql_query( $query );

	if ( $sql )
	{
		error_message("success", "Success",	"<a	href='/stylesheets.php'>Stylesheet Successfully	Edited!	 Click to Return</a>");
	}
}

$editid	= $_GET['editid'];
$uri	= $_GET['uri'];
$name	= $_GET['name'];

//	Add	A New StyleSheet!
$add = $_GET['add'];

if ( $add == 'true'	)
{
	$id		= $_GET['id'];
	$uri	= $_GET['uri'];
	$name	= $_GET['name'];

	$query = "INSERT INTO stylesheets
				SET	id = '$id',	uri	= '$uri', name = '$name'";

	$sql = sql_query( $query );

	if ( $sql )
	{
		$success = true;
	}
	else
	{
		$success = false;
	}
}

if ( $success == true )
{
	error_message("success", "Success",	"<a	href='/stylesheets.php'>Stylesheet Successfully	Created</a>");
}

begin_frame("Stylesheets",	center );

//	Edit StyleSheet:
if ( $editid > 0 )
{
	print("<form name='form1' method='get'	action='/stylesheets.php'>"	);
	print("<div align='center'><input type='hidden' name='edited' value='1' />Now Editing Stylesheet <span	style='font-weight:bold;'>$name</span></div>");
	print("<br	/>"	);
	print("<input type='hidden' name='id' value='$editid' /><table	class='main' cellspacing='0' cellpadding='5' width='50%'>");

	print("<tr>
			<td	class='rowhead'><label for='name'>Stylesheets Name:</label></td>
			<td	class='rowhead'><input type='text' name='name' id='name' size='50' value='$name' /></td>
		</tr>");

	print("<tr>
			<td	class='rowhead'><label for='uri'>Stylesheets Name.css:</label></td>
			<td	class='rowhead'><input type='text' name='uri' id='uri' size='50' value='$uri' /></td>
		</tr>");

	print("<tr>
			<td	class='rowhead'	colspan='2'><div align='center'><input type='submit' class='btn' value='Submit'	/></div></td>
		</tr>
	</table>");

	print("</form></td></tr>");
	end_table();
	site_footer();
	die();
}

//	Add	a StyleSheet:
	print("<span style='font-weight:bold;'>Add	A New StyleSheet!</span>");
	print("<br	/>"	);
	print("<br	/>"	);
	print("<form name='form1' method='get'	action='/stylesheets.php'>"	);
	print("<table class='main'	cellspacing='0'	cellpadding='5'>");

	print("<tr>
			<td class='rowhead'><span style='font-weight:bold;'><label for='id'>Style ID:</label></span></td>
			<td class='rowhead' align='right'><input	type='text'	name='id' id='id' size='50'	/></td>
		</tr>");

	print("<tr>
			<td class='rowhead'><span style='font-weight:bold;'><label for='uri'>Style URL:</label></span></td>
			<td class='rowhead' align='right'><input	type='text'	name='uri' id='uri'	size='50' /></td>
		</tr>");

	print("<tr>
			<td class='rowhead'><span style='font-weight:bold;'><label for='name'>Style Name:</label></span></td>
			<td class='rowhead' align='right'><input	type='text'	name='name'	id='name' size='50'	/><input type='hidden' name='add' value='true' /></td>
		</tr>");

	print("<tr>
			<td class='rowhead' colspan='2'><div	align='center'><input type='submit'	class='btn'	value='Submit' /></div></td>
			</tr>");

	print("</table>");
	print("<br	/>"	);
	print("</form>");

//	Existing StyleSheets:
	print("<span style='font-weight:bold;'>Existing StyleSheets:</span>");
	print("<br	/>"	);
	print("<br	/>"	);
	print("<table class='main'	cellspacing='0'	cellpadding='5'>");
	print("<tr>
			<td class='colhead'><span style='font-weight:bold;'>ID:</span></td>
			<td class='colhead'><span style='font-weight:bold;'>URL:</span></td>
			<td class='colhead'><span style='font-weight:bold;'>Name:</span></td>
			<td class='colhead'><span style='font-weight:bold;'>Edit:</span></td>
			<td class='colhead'><span style='font-weight:bold;'>Delete:</span></td>
			</tr>");

$query = "SELECT *
			FROM stylesheets
			WHERE 1=1";

$sql = sql_query($query);

while (	$row = mysql_fetch_array( $sql ) )
{
	$id	  =	$row['id'];
	$uri  =	$row['uri'];
	$name =	$row['name'];

	print("<tr>
			<td class='rowhead'>$id </td>
			<td class='rowhead'>$uri</td>
			<td class='rowhead'>$name</td>
			<td class='rowhead'><div	align='center'><a href='/stylesheets.php?editid=$id&amp;name=$name&amp;uri=$uri'><img src='".$image_dir."leeches.png' border='0' alt=''	/></a></div></td>
			<td class='rowhead'><div	align='center'><a href='/stylesheets.php?delid=$id&amp;name=$name'><img	src='".$image_dir."disabled.png' border='0'	alt='' /></a></div></td>
			</tr>");
}

end_table();
end_frame();

print("<br	/>"	);

site_footer();

?>