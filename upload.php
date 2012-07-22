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
require_once(INCL_DIR.'function_bbcode.php');
require_once(INCL_DIR.'function_page_verify.php');

db_connect(false);
logged_in();

$newpage = new page_verify();
$newpage->create('_upload_');

site_header("Upload");

if (get_user_class() < UC_USER)
{
	error_message("warn", "Sorry...", "You are not Authorized to upload torrents.  (See <a href='faq.php#up'>Uploading</a> in the FAQ.)");
	site_footer();
	exit;
}

?>
<div align='center'>
	<form name='upload' enctype='multipart/form-data' action='takeupload.php' method='post'>
		<input type='hidden' name='MAX_FILE_SIZE' value="<?php echo $max_torrent_size?>" />
		<p>The tracker's announce url is <span style='font-weight:bold;'><?php echo  $announce_urls[0] ?></span></p>
		<table width='100%' border='1' cellspacing='0' cellpadding='10'>
<?php

		echo("<tr>
			<td class='rowhead'>Torrent File</td>
			<td class='rowhead'><input type='file' name='file' size='80' />\n</td>
			</tr>\n");

		echo("<tr>
			<td class='rowhead'>Torrent Name</td>
			<td class='rowhead'><input type='text' name='name' size='80' /><br />
			(Taken from filename if not specified. <strong>Please use descriptive names.</strong>)\n</td>
			</tr>\n");

		echo("<tr>
			<td class='rowhead'>NFO File</td>
			<td class='rowhead'><input type='file' name='nfo' size='80' /><br />
			(<strong>Required.</strong> Can only be viewed by Power Users.)\n</td>
			</tr>\n");

		echo("<tr>
			<td class='rowhead' style='padding: 10px'>Description</td>
			<td class='rowhead' align='center' style='padding: 3px'>".textbbcode("upload", "descr", htmlspecialchars($row["ori_descr"])) . "</td>
			</tr>\n");

			$s = "<select name='type'>\n<option value='0'>(choose one)</option>\n";

			$cats = genrelist();

			foreach ($cats as $row)
				{
				$s .= "<option value='" . $row["id"] . "'>" . htmlspecialchars($row["name"]) . "</option>\n";
				}
			$s .= "</select>\n";

		echo("<tr>
			<td class='rowhead'>Type</td>
			<td class='rowhead'>$s</td></tr>");

			?>
			<tr><td class='std' align='center' colspan='2'><input type='submit' class='btn' value='Upload' /></td></tr>
		</table>
	</form>
</div>
<br />

<?php

site_footer();

?>