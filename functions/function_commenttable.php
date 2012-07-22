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

function commenttable($rows)
{
	global $CURUSER, $image_dir;

	begin_frame();

	//$count = 0;

	foreach ($rows as $row)
	{
		print("<p class='sub'>#" . $row["id"] . " by ");

		if (isset($row["username"]))
		{
			$title = $row["title"];

			if ($title == "")
				$title = get_user_class_name($row["class"]);
			else
				$title = htmlspecialchars($title);

			print("<a name='comm". $row["id"] .
        	"' href='userdetails.php?id=" . $row["user"] . "'><span style='font-weight:bold;'>" .
        	htmlspecialchars($row["username"]) . "</span></a>" . ($row["donor"] == "yes" ? "<img src='{$image_dir}star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />" : "") . ($row["warned"] == "yes" ? "<img src=".
    			"'{$image_dir}warned.png' width='16' height='16' border='0' alt='Warned' title='Warned' />" : "") . " ($title)\n");
		}
		else
   		print("<a name='comm" . $row["id"] . "'><span style='font-style: italic;'>(orphaned)</span></a>\n");

		print(" at " . $row["added"] . " GMT" .
			($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=edit&amp;cid=$row[id]'>Edit</a>" : "") .
			(get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=delete&amp;cid=$row[id]'>Delete</a>" : "") .
			($row["editedby"] && get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=vieworiginal&amp;cid=$row[id]'>View Original</a>" : "") . "</p>\n");

		$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");

		if (!$avatar)
			$avatar = "{$image_dir}default_avatar.gif width='125' height='125' border='0' atl='' title='' ";

		$text = format_comment($row["text"]);

		if ($row["editedby"])
    	$text .= "<p><span style='font-size: x-small; '>Last edited by <a href='/userdetails.php?id=$row[editedby]'><span style='font-weight:bold;'>$row[username]</span></a> at $row[editedat] GMT</span></p>\n";

		begin_table(true);

		print("<tr valign='top'>\n");
		print("<td align='center' width='150'><img src='{$avatar}' width='' height='' border='' alt='' title='' /></td>\n");
		print("<td class='text'>$text</td>\n");
		print("</tr>\n");

		end_table();
	}

	end_frame();

}

?>