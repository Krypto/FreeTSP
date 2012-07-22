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
*--   This program is free software; you can redistribute it and/or modify    --*
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

if (get_user_class() < UC_SYSOP)
	error_message("warn", "Warning", "Access Denied.");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($_POST["username"] == "" || $_POST["password"] == "" || $_POST["email"] == "")
		error_message("error", "Error", "Missing Data.");

	if ($_POST["password"] != $_POST["password2"])
		error_message("error", "Error", "Passwords Mismatch.");

	if (!validemail($_POST['email']))
		error_message("error", "Error", "Not a Valid Email");

	$username	= sqlesc($_POST["username"]);
	$password	= $_POST["password"];
	$email		= sqlesc($_POST["email"]);
	$secret		= mksecret();
	$passhash	= sqlesc(md5($secret . $password . $secret));
	$secret		= sqlesc($secret);

	sql_query("INSERT INTO users (added, last_access, secret, username, passhash, status, email)
				VALUES(NOW(), NOW(), $secret, $username, $passhash, 'confirmed', $email)") OR sqlerr(__FILE__, __LINE__);

	$res = sql_query("SELECT id
						FROM users
						WHERE username = $username");

	$arr = mysql_fetch_row($res);

	if (!$arr)
		error_message("error", "Error", "Sorry, I'm unable to create the account, the username you submitted is already in use.");

	$id = 0 + $arr["0"];

	header("Location: $site_url/userdetails.php?id=$arr[0]");
	die;
}

site_header("Add User");

print("<h1>Add User</h1>");
print("<br />");
print("<form method='post' action='adduser.php'>");
print("<table border='1' align='center' cellspacing='0' cellpadding='5'>");

print("<tr>
		<td class='rowhead'><label for='username'>User Name</label></td>
		<td class='rowhead'><input type='text' name='username' id='username' size='40' /></td>
	</tr>");

print("<tr>
		<td class='rowhead'><label for='password'>Password</label></td>
		<td class='rowhead'><input type='password' name='password' id='password' size='40' /></td>
	</tr>");

print("<tr>
		<td class='rowhead'><label for='password2'>Re-type Password</label></td>
		<td class='rowhead'><input type='password' name='password2' id='password2' size='40' /></td>
	</tr>");

print("<tr>
		<td class='rowhead'><label for='email'>E-Mail</label></td>
		<td class='rowhead'><input type='text' name='email' id='email' size='40' /></td>
	</tr>");

print("<tr>
		<td class='std' colspan='2' align='center'><input type='submit' class='btn' value='Okay' /></td>
	</tr>");

print("</table>");
print("</form>");

print("<br />");

site_footer();

?>