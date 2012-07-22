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

/////FreeTSP Shoutbox_Commands Spook/////

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');

db_connect();
logged_in();

if (get_user_class() < UC_MODERATOR)
	error_message("warn","Warning", "Permission Denied.");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<meta name='generator' content='FreeTSP.info' />
	<meta name='MSSmartTagsPreventParsing' content='true' />
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>Staff ShoutBox Commands</title>
	</head>

<body>

<script type='text/javascript'>

function command(command,form,text)
{
	window.opener.document.forms[form].elements[text].value = window.opener.document.forms[form].elements[text].value+command+' ';
	window.opener.document.forms[form].elements[text].focus();
	window.close();
}

</script>

<table width='100%' cellpadding='1' cellspacing='1'>
	<tr>
		<td align='center'><span style='font-weight:bold;'>empty</span>. To use type /EMPTY <br />[username here without the brackets]</td>
	</tr>
	<tr>
		<td align='center'><span style='font-weight:bold;'><input type='text' size='20' value='/EMPTY' onclick="command('/EMPTY','shbox','shbox_text')" /></span></td>
	</tr>

	<tr>
		<td align='center'><span style='font-weight:bold;'>gag</span>. To use type /GAG <br />[username here without the brackets]</td>
		<td align='center'><span style='font-weight:bold;'>ungag</span>. To use type /UNGAG <br /> [username here without the brackets]</td>
	</tr>
	<tr>
		<td align='center'><span style='font-weight:bold;'><input type='text' size='20' value='/GAG' onclick="command('/GAG','shbox','shbox_text')" /></span></td>
		<td align='center'><span style='font-weight:bold;'><input type='text' size='20' value='/UNGAG' onclick="command('/UNGAG','shbox','shbox_text')" /></span></td>
	</tr>

	<tr>
		<td align='center'><span style='font-weight:bold;'>warn</span>. To use type /WARN <br />[username here without the brackets]</td>
		<td align='center'><span style='font-weight:bold;'>unwarn</span>. To use type /UNWARN <br />[username here without the brackets]</td>
	</tr>
	<tr>
		<td align='center'><span style='font-weight:bold;'><input type='text' size='20' value='/WARN' onclick="command('/WARN','shbox','shbox_text')" /></span></td>
		<td align='center'><span style='font-weight:bold;'><input type='text' size='20' value='/UNWARN' onclick="command('/UNWARN','shbox','shbox_text')" /></span></td>
	</tr>

	<tr>
		<td align='center'><span style='font-weight:bold;'>disable</span>. To use type /DISABLE <br />[username here without the brackets]</td>
		<td align='center'><span style='font-weight:bold;'>enable</span>. To use type /ENABLE <br />username here without the brackets]</td>
	</tr>
	<tr>
		<td align='center'><span style='font-weight:bold;'><input type='text' size='20' value='/DISABLE' onclick="command('/DISABLE','shbox','shbox_text')" /></span></td>
		<td align='center'><span style='font-weight:bold;'><input type='text' size='20' value='/ENABLE' onclick="command('/ENABLE','shbox','shbox_text')" /></span></td>
	</tr>
</table>
<br />

<div align='center'><a class='altlink' href='javascript: window.close()'><span style='font-weight:bold;'>[ Close window ]</span></a></div>

</body>
</html>

<?php

die();

?>