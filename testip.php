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

db_connect();
logged_in();

if (get_user_class() < UC_MODERATOR)
	error_message("warn", "Warning", "Permission Denied");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$ip = isset($_POST["ip"]) ? $_POST["ip"] : false;
}
else
{
	$ip = isset($_GET["ip"]) ? $_GET["ip"] : false;
}

if ($ip)
{
	$nip = ip2long($ip);

	if ($nip == -1)
		error_message("error", "Error", "Bad IP.");

	$res = sql_query("SELECT *
						FROM bans
						WHERE $nip >= first
						AND $nip <= last") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
		error_message("info", "Result", "The IP address <strong>".htmlentities($ip, ENT_QUOTES)."</strong> is not Banned.");
	else
	{
		$banstable = "<table class='main' border='0' cellspacing='0' cellpadding='5'><tr><td class='colhead'>First</td><td class='colhead'>Last</td><td class='colhead'>Comment</td></tr>\n";

		while ($arr = mysql_fetch_assoc($res))
		{
			$first		= long2ip($arr["first"]);
			$last		= long2ip($arr["last"]);
			$comment	= htmlspecialchars($arr["comment"]);
			$banstable .= "<tr><td>$first</td><td>$last</td><td>$comment</td></tr>\n";
		}
		$banstable .= "</table>\n";

		error_message("info", "Result", "<img src='{$image_dir}warnedbig.png' width='32' height='32' border='0' alt='This IP is Banned' title='This IP is Banned' />&nbsp;&nbsp;The IP address <strong>$ip</strong> is <strong>Banned</strong> - Reason for Ban <strong>$comment</strong>");
	}
}
site_header();

?>
<h1>Test IP address</h1>
<form method='post' action='testip.php'>
	<table border='1' cellspacing='0' cellpadding='5'>
		<tr><td class='rowhead'><label for='ip'>IP Address</label></td><td><input type='text' name='ip' id='ip' size='15' /></td></tr>
		<tr><td class='rowhead' colspan='2' align='center'><input type='submit' class='btn' value='OK' /></td></tr>
	</table>
</form>

<?php

echo("<br />");

site_footer();

?>