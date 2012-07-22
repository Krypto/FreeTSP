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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_user.php');

db_connect();
logged_in();

site_header("Search");

?>

<table width='100%' class='main' border='0' cellspacing='0' cellpadding='0'>
	<tr>
		<td class='embedded'>
			<form method='get' action='browse.php'>
				<p align='center'>
				Search:
				<input type='text' name='search' size='40' value='<?php echo  htmlspecialchars($searchstr) ?>' />
				in
					<select name='cat'>
						<option value='0'>(all types)</option>

<?php

$cats			= genrelist();
$catdropdown	= "";

foreach ($cats as $cat)
{
	$catdropdown	.= "<option value='" . $cat["id"] . "'";
	$getcat			= (isset($_GET["cat"])?$_GET["cat"]:'');

	if ($cat["id"] == $getcat)

	$catdropdown .= " selected='selected'";
	$catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

	$deadchkbox = "<input type='checkbox' name='incldead' value='1'";

if (isset($_GET["incldead"]))

	$deadchkbox .= " checked='checked'";
	$deadchkbox .= " /> including dead torrents\n";

	$catdropdown ?>
					</select>
	<?php echo $deadchkbox ?>
					<input type='submit' class='btn' value='Search!' />
				</p>
			</form>
		</td>
	</tr>
</table>

<?php

site_footer();

?>