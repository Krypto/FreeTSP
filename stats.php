<?php

/*
*-------------------------------------------------------------------------------*
*----------------    |  ____|        |__   __/ ____|  __ \        --------------*
*----------------    | |__ _ __ ___  ___| | | (___ | |__) |       --------------*
*----------------    |  __| '__/ _ \/ _ \ |  \___ \|  ___/        --------------*
*----------------    | |  | | |  __/  __/ |  ____) | |            --------------*
*----------------    |_|  |_|  \___|\___|_| |_____/|_|            --------------*
*-------------------------------------------------------------------------------*
*---------------------------    FreeTSP  v1.0   --------------------------------*
*-------------------   The Alternate BitTorrent Source   -----------------------*
*-------------------------------------------------------------------------------*
*-------------------------------------------------------------------------------*
*--   This program is free software; you can redistribute it and /or modify   --*
*--   it under the terms of the GNU General Public License as published by    --*
*--   the Free Software Foundation; either version 2 of the License, or       --*
*--   (at your option) any later version.                                     --*
*--                                                                           --*
*--   This program is distributed in the hope that it will be useful,         --*
*--   but WITHOUT ANY WARRANTY; without even the implied warranty of          --*
*--   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           --*
*--   GNU General Public License for more details.                            --*
*--                                                                           --*
*--   You should have received a copy of the GNU General Public License       --*
*--   along with this program; if not, write to the Free Software             --*
*-- Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA  --*
*--                                                                           --*
*-------------------------------------------------------------------------------*
*------------   Original Credits to tbSource, Bytemonsoon, TBDev   -------------*
*-------------------------------------------------------------------------------*
*-------------           Developed By: Krypto, Fireknight           ------------*
*-------------------------------------------------------------------------------*
*-----------------       First Release Date August 2010      -------------------*
*-----------                 http://www.freetsp.info                 -----------*
*------                    2010 FreeTSP Development Team                  ------*
*-------------------------------------------------------------------------------*
*/

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'function_main.php' );
require_once( INCL_DIR . 'function_user.php' );
require_once( INCL_DIR . 'function_vfunctions.php' );

db_connect( false );
logged_in();

if ( get_user_class() < UC_MODERATOR )
	error_message("warn", "Warning", "Permission Denied.");

site_header( "Stats" );

?>

<?php

$res = sql_query( "SELECT COUNT(*)
					FROM torrents" ) or sqlerr( __FILE__, __LINE__ );

$n		= mysql_fetch_row( $res );
$n_tor	= $n[0];

$res = sql_query( "SELECT COUNT(*)
						FROM peers" ) or sqlerr( __FILE__, __LINE__ );

$n			= mysql_fetch_row( $res );
$n_peers	= $n[0];

$uporder	= isset( $_GET['uporder'] ) ? $_GET['uporder'] : '';
$catorder	= isset( $_GET["catorder"] ) ? $_GET["catorder"] : '';

if ( $uporder == "lastul" )
	$orderby = "last DESC, name";

elseif ( $uporder == "torrents" )
	$orderby = "n_t DESC, name";

elseif ( $uporder == "peers" )
	$orderby = "n_p DESC, name";
else
	$orderby = "name";

$query = "SELECT u.id, u.username AS name, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t, COUNT(p.id) AS n_p
			FROM users AS u
			LEFT JOIN torrents AS t ON u.id = t.owner
			LEFT JOIN peers AS p ON t.id = p.torrent
			WHERE u.class = " . UC_UPLOADER . "
			GROUP BY u.id
			UNION SELECT u.id, u.username AS name, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t, COUNT(p.id) AS n_p
			FROM users AS u
			LEFT JOIN torrents AS t ON u.id = t.owner
			LEFT JOIN peers AS p ON t.id = p.torrent
			WHERE u.class > " . UC_UPLOADER . "
			GROUP BY u.id
			ORDER BY $orderby";

$res = sql_query( $query ) or sqlerr( __FILE__, __LINE__ );

if ( mysql_num_rows( $res ) == 0 )
	error_message("info", "Sorry...", "No Uploaders." );
else
{
	begin_frame( "Uploader Activity", true );
	begin_table();

	echo( "<tr>\n
	<td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=uploader&amp;catorder=$catorder' class='colheadlink'>Uploader</a></td>\n
	<td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=lastul&amp;catorder=$catorder' class='colheadlink'>Last Upload</a></td>\n
	<td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=torrents&amp;catorder=$catorder' class='colheadlink'>Torrents</a></td>\n
	<td class='colhead'>Perc.</td>\n
	<td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=peers&amp;catorder=$catorder' class='colheadlink'>Peers</a></td>\n
	<td class='colhead'>Perc.</td>\n
	</tr>\n" );

	while ( $uper = mysql_fetch_assoc( $res ) )
	{
		echo( "<tr><td class='std'><a href='userdetails.php?id=" . $uper['id'] . "'><span style='font-weight:bold;'>" . $uper['name'] . "</span></a></td>\n" );
		echo( "<td " . ( $uper['last']?( ">" . $uper['last'] . " (" . get_elapsed_time( sql_timestamp_to_unix_timestamp( $uper['last'] ) ) . " ago)" ):" class='rowhead' align='center'>---" ) . "</td>\n" );
		echo( "<td class='rowhead' align='right'>" . $uper['n_t'] . "</td>\n" );
		echo( "<td class='rowhead' align='right'>" . ( $n_tor > 0?number_format( 100 * $uper['n_t'] / $n_tor, 1 ) . "%":"---" ) . "</td>\n" );
		echo( "<td class='rowhead' align='right'>" . $uper['n_p'] . "</td>\n" );
		echo( "<td class='rowhead' align='right'>" . ( $n_peers > 0?number_format( 100 * $uper['n_p'] / $n_peers, 1 ) . "%":"---" ) . "</td></tr>\n" );
	}

	end_table();
	end_frame();
}

if ( $n_tor == 0 )
	error_message("info", "Sorry...", "No Categories Defined!" );
else
{
	if ( $catorder == "lastul" )
		$orderby = "last DESC, c.name";

	elseif ( $catorder == "torrents" )
		$orderby = "n_t DESC, c.name";

	elseif ( $catorder == "peers" )
		$orderby = "n_p DESC, name";
	else
		$orderby = "c.name";

	$res = sql_query( "SELECT c.name, MAX(t.added) AS last, COUNT(DISTINCT t.id) AS n_t, COUNT(p.id) AS n_p
						FROM categories AS c
						LEFT JOIN torrents AS t ON t.category = c.id
						LEFT JOIN peers AS p ON t.id = p.torrent
						GROUP BY c.id
						ORDER BY $orderby" ) or sqlerr( __FILE__, __LINE__ );

	begin_frame( "Category Activity", true );
	begin_table();

	echo( "<tr><td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=$uporder&amp;catorder=category' class='colheadlink'>Category</a></td>
	<td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=$uporder&amp;catorder=lastul' class='colheadlink'>Last Upload</a></td>
	<td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=$uporder&amp;catorder=torrents' class='colheadlink'>Torrents</a></td>
	<td class='colhead'>Perc.</td>
	<td class='colhead'><a href='" . $_SERVER['PHP_SELF'] . "?uporder=$uporder&amp;catorder=peers' class='colheadlink'>Peers</a></td>
	<td class='colhead'>Perc.</td></tr>\n" );

	while ( $cat = mysql_fetch_assoc( $res ) )
	{
		echo( "<tr><td class='rowhead'>" . $cat['name'] . "</td>" );
		echo( "<td " . ( $cat['last']?( ">" . $cat['last'] . " (" . get_elapsed_time( sql_timestamp_to_unix_timestamp( $cat['last'] ) ) . " ago)" ):"align = 'center'>---" ) . "</td>" );
		echo( "<td class='rowhead' align='right'>" . $cat['n_t'] . "</td>" );
		echo( "<td class='rowhead' align='right'>" . number_format( 100 * $cat['n_t'] / $n_tor, 1 ) . "%</td>" );
		echo( "<td class='rowhead' align='right'>" . $cat['n_p'] . "</td>" );
		echo( "<td class='rowhead' align='right'>" . ( $n_peers > 0?number_format( 100 * $cat['n_p'] / $n_peers, 1 ) . "%":"---" ) . "</td></tr>\n" );
	}

	end_table();
	end_frame();
}

site_footer();

die;

?>