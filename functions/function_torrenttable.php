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
*--	  This program is free software; you can redistribute it and /or modify	  --*
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

function linkcolor($num)
{
	if (!$num)
		return "red";

	return "green";
}

function torrenttable( $res, $variant =	"index"	)
{
	global $image_dir, $added;

?>
<table align ='center' border='1' cellspacing='0' cellpadding='5'>
	<tr>
		<td	class='colhead'	align='center'>Type</td>
		<td	class='colhead'	align='left'>Name</td>
		<td	class='colhead'	align='left'>DL</td>
		<td	class='colhead'	align='right'>Files</td>
		<td	class='colhead'	align='right'>Comm.</td>


		<!--<td	class='colhead'	align='center'>Rating</td>-->
		<!--<td	class='colhead'	align='center'>Added</td>-->

		<td	class='colhead'	align='center'>Size</td>

		<!--
		<td	class='colhead'	align='right'>Views</td>
		<td	class='colhead'	align='right'>Hits</td>
		-->

		<td	class='colhead'	align='center'>Snatched</td>
		<td	class='colhead'	align='right'>Seeders</td>
		<td	class='colhead'	align='right'>Leechers</td>
<?php

if ( $variant == "index" )
	print( "<td	class='colhead'	align='center'>Upped&nbsp;by</td>\n" );

	print( "</tr>\n" );

	while (	$row = mysql_fetch_assoc( $res ) )
	{
		$id	= $row["id"];

		print( "<tr>\n"	);

		print( "<td	class='rowhead'	align='center' style='padding: 0px'>" );

		if ( isset(	$row["cat_name"] ) )
		{
			print( "<a href='/browse.php?cat=" . $row["category"] .	"'>" );

			if ( isset(	$row["cat_pic"]	) && $row["cat_pic"] !=	"" )
				print( "<img src='{$image_dir}caticons/{$row['cat_pic']}' width='60' height='54' border='0'	alt='{$row['cat_name']}' title='{$row['cat_name']}'	/>"	);
			else
				print( $row["cat_name"]	);
			echo( "</a>" );
		}
		else
			print( "-" );

		print( "</td>\n" );

		$dispname =	htmlspecialchars( $row["name"] );
		$added = sqlesc(get_date_time());

		print( "<td	class='rowhead'	align='left'><a	href='/details.php?" );

		if ( $variant == "mytorrents" )
			print( "returnto=" . urlencode(	$_SERVER["REQUEST_URI"]	) .	"&amp;"	);

		print( "id=$id"	);

		if ( $variant == "index" )
			print( "&amp;hit=1"	);

		print( "'><span	style='font-weight:bold;'>$dispname</span></a><br />".$row["added"]."</td>\n" );

		print( "<td	class='rowhead'	align='center'><a href='/download.php/$id/"	. rawurlencode(	$row["filename"] ) . "'><img src='".$image_dir."download.png' width='16' height='16' border='0'	alt='Download' title='Download' /></a></td>\n" );

		if ( $row["type"] == "single" )
			print( "<td	align='center'>" . $row["numfiles"]	. "</td>\n"	);
		else
		{
			if ( $variant == "index" )
				print( "<td	class='rowhead'	align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;filelist=1'>"	. $row["numfiles"] . "</a></span></td>\n" );
			else
				print( "<td	class='rowhead'	align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;filelist=1#filelist'>" . $row["numfiles"] .	"</a></span></td>\n" );
		}

		if ( !$row["comments"] )
			print( "<td	class='rowhead'	align='center'>" . $row["comments"]	. "</td>\n"	);
		else
		{
			if ( $variant == "index" )
				print( "<td	class='rowhead'	align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;tocomm=1'>" .	$row["comments"] . "</a></span></td>\n"	);
			else
				print( "<td	class='rowhead'	align='center'><span style='font-weight:bold;'><a href='/details.php?id=$id&amp;page=0#startcomments'>"	. $row["comments"] . "</a></span></td>\n" );
		}

		/*
		print("<td class='rowhead' align='center'>");
		if (!isset($row["rating"]))
			print("---");
		else {
			$rating	= round($row["rating"] * 2)	/ 2;
			$rating	= ratingpic($row["rating"]);
			if (!isset($rating))
				print("---");
			else
				print($rating);
		}
		print("</td>\n");
		*/
		//print( "<td class='rowhead' align='center'><table	border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>" .	str_replace( " ", "<br />" ) . "</td></tr></table></td>\n" );

		print( "<td	class='rowhead'	align='center'>" . str_replace(	" ", "<br />", mksize( $row["size"]	) )	. "</td>\n"	);

		// print("<td class='rowhead' align='right'>" .	$row["views"] .	"</td>\n");
		// print("<td class='rowhead' align='right'>" .	$row["hits"] . "</td>\n");

		$_s	= "";

		if ( $row["times_completed"] !=	1 )
			$_s	= "s";

		print( "<td	class='rowhead'	align='center'>" . ( $row["times_completed"] > 0 ? "<a href='/snatches.php?id=$id'>" . number_format( $row["times_completed"] )	. "<br />time$_s</a>" :	"0 times" )	. "</td>\n"	);

		if ( $row["seeders"] )
		{
			if ( $variant == "index" )
			{
				if ( $row["leechers"] )	$ratio = $row["seeders"] / $row["leechers"];
				else $ratio	= 1;

				print( "<td	class='rowhead'	align='right'><span	style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;toseeders=1'><span	style='color :"	.
					get_slr_color( $ratio )	. "'>" . $row["seeders"] . "</span></a></span></td>\n" );
			}
			else
				print( "<td	class='rowhead'	align='right'><span	style='font-weight:bold;'><a class='" .	linkcolor( $row["seeders"] ) . "' href='details.php?id=$id&amp;dllist=1#seeders'>" .
					$row["seeders"]	. "</a></span></td>\n" );
		}
		else
			print( "<td	class='rowhead'	align='right'><span	class='" . linkcolor( $row["seeders"] )	. "'>" . $row["seeders"] . "</span></td>\n"	);

		if ( $row["leechers"] )
		{
			if ( $variant == "index" )
				print( "<td	class='rowhead'	align='right'><span	style='font-weight:bold;'><a href='/details.php?id=$id&amp;hit=1&amp;todlers=1'>" .
					number_format( $row["leechers"]	) .	( $peerlink	? "</a>" : "" )	.
					"</span></td>\n" );
			else
				print( "<td	class='rowhead'	align='right'><span	style='font-weight:bold;'><a class='" .	linkcolor( $row["leechers"]	) .	"' href='/details.php?id=$id&amp;dllist=1#leechers'>" .
					$row["leechers"] . "</a></span></td>\n"	);
		}
		else
			print( "<td	class='rowhead'	align='right'>0</td>\n"	);

		if ( $variant == "index" )
			print( "<td	class='rowhead'	align='center'>" . ( isset(	$row["username"] ) ? ( "<a href='/userdetails.php?id=" . $row["owner"] . "'><span style='font-weight:bold;'>" .	htmlspecialchars( $row["username"] ) . "</span></a>" ) : "<span	style='font-style: italic;'>(unknown)</span>" )	. "</td>\n"	);

		print( "</tr>\n" );
	}

	print( "</table>\n"	);

	return $rows;
}