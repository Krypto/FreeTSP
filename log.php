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
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');

db_connect(false);
logged_in();

// delete items older than a week
$secs = 24 * 60 * 60;

if (get_user_class() < UC_MODERATOR)
	error_message("warn", "Warning", "Permission Denied!");

site_header("Site Log");

sql_query("DELETE
			FROM sitelog
			WHERE " . gmtime() . " - UNIX_TIMESTAMP(added) > $secs") or sqlerr(__FILE__, __LINE__);

$res = sql_query("SELECT added, txt
					FROM sitelog
					ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

echo("<h1>Site Log</h1>\n");

if (mysql_num_rows($res) == 0)
	echo("<span style='font-weight:bold;'>Log is empty</span>\n");
else
{
	echo("<table border='1' cellspacing='0' cellpadding='5'>\n");
	echo("<tr>
			<td class='colhead' align='left'>Date</td>
			<td class='colhead' align='left'>Time</td>
			<td class='colhead' align='left'>Event</td>
		</tr>");

	while ($arr = mysql_fetch_assoc($res))
	{
		$date = substr($arr['added'], 0, strpos($arr['added'], " "));
		$time = substr($arr['added'], strpos($arr['added'], " ") + 1);

		echo("<tr>
				<td class='rowhead'>$date</td>
				<td class='rowhead'>$time</td>
				<td class='rowhead' align='left'>".htmlentities($arr['txt'], ENT_QUOTES)."</td>
			</tr>");
	}
	echo("</table>");
}
echo("<p>Times are in GMT.</p>\n");

site_footer();

?>