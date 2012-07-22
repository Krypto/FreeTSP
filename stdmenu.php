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

db_connect();
logged_in();

?>

<!-- MENU -->
<table class='mainouter' width='100%' border='1' cellspacing='0' cellpadding='10'>
	<?php print StatusBar(); ?>
	<tr>
		<td class='outer' align='center'>
			<table class='main' width='100%' cellspacing='0' cellpadding='5' border='0'>
				<tr>

<?php if ( !$CURUSER )

{
	header( "Refresh: 3; url='index.php'" );
}
else
{
	if ( $CURUSER['stdmenu'] == "yes" )
	{

?>
					<td align='center' class='navigation'><a href='/index.php'>Home</a></td>
					<td align='center' class='navigation'><a href='/browse.php'>Browse</a></td>
					<td align='center' class='navigation'><a href='/search.php'>Search</a></td>
					<td align='center' class='navigation'><a href='/upload.php'>Upload</a></td>
					<td align='center' class='navigation'><a href='/altusercp.php'>Profile</a></td>
					<td align='center' class='navigation'><a href='/forums.php'>Forums</a></td>
					<td align='center' class='navigation'><a href='/topten.php'>Top 10</a></td>
					<td align='center' class='navigation'><a href='/log.php'>Log</a></td>
					<td align='center' class='navigation'><a href='/rules.php'>Rules</a></td>
					<td align='center' class='navigation'><a href='/faq.php'>FAQ</a></td>
					<td align='center' class='navigation'><a href='/links.php'>Links</a></td>
					<td align='center' class='navigation'><a href='/staff.php'>Staff</a></td>
	<?php
		if ( get_user_class() >= UC_MODERATOR )
		{
	?>
			<td align='center' class='navigation'><a href='/stafftools.php'>Staff Tools</a></td>
	<?php
		}
	}
}
?>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br /><br />