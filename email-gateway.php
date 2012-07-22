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

$id = 0 + $_GET["id"];

if (!$id)
	error_message("error", "Error", "Bad or Missing ID.");

$res = sql_query("SELECT username, class, email
					FROM users
					WHERE id=$id");

$arr = mysql_fetch_assoc($res) or error_message("error", "Error", "No such User.");

$username = $arr["username"];

if ($arr["class"] < UC_MODERATOR)
	error_message("error", "Error", "The Gateway can only be used to e-mail Staff Members.");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$to = $arr["email"];

	$from = substr(trim($_POST["from"]), 0, 80);

	if ($from == "") $from = "Anonymous";

	$from_email = substr(trim($_POST["from_email"]), 0, 80);

	if ($from_email == "") $from_email = "{$site_email}";

	if (!strpos($from_email, "@"))
		error_message("error", "Error", "The entered e-mail address does not seem to be valid.");

	$from = "$from <$from_email>";

	$subject = substr(trim($_POST["subject"]), 0, 80);

	if ($subject == "") $subject = "(No subject)";

	$subject = "Fw: $subject";

	$message = trim($_POST["message"]);

	if ($message == "") error_message("error", "Error", "No Message Text!");

	$message = "Message submitted from {$_SERVER['REMOTE_ADDR']} at " . gmdate("Y-m-d H:i:s") . " GMT.\n" .
		"Note: By replying to this e-mail you will reveal your e-mail address.\n" .
		"---------------------------------------------------------------------\n\n" .
		$message . "\n\n" .
		"---------------------------------------------------------------------\n$site_name E-Mail Gateway\n";

	$success = mail($to, $subject, $message, "From: $from", "-f$site_email");

	if ($success)
		error_message("success", "Success", "E-mail successfully queued for delivery.");
	else
		error_message("error", "Error", "The mail could not be sent. Please try again later.");
}

site_header("E-mail Gateway");
?>
<table border='0' class='main' cellspacing='0' cellpadding='0'>
	<tr>
		<td class='embedded'><img src='<?php echo $image_dir?>/email.gif' width='32' height='32' border='0 'alt='Send Email' title='Send Email'/></td>
		<td class='embedded' style='padding-left: 10px'><span style='font-size: small; font-weight:bold;'>Send e-mail to <?php echo $username;?></span></td>
	</tr>
</table><br />

<form method='post' action='email-gateway.php?id='<?php $id?>>
	<table border='1' cellspacing='0' cellpadding='5'>
		<tr>
			<td class='rowhead'><label for='name'>Your Name</label></td><td><input type='text' name='from' id='name' size='80' /></td>
		</tr>
		<tr>
			<td class='rowhead'><label for='email'>Your e-mail</label></td><td><input type='text' name='from_email' id='email' size='80' /></td>
		</tr>
		<tr>
			<td class='rowhead'><label for='subject'>Subject</label></td><td><input type='text' name='subject' id='subject' size='80' /></td>
		</tr>
		<tr>
			<td class='rowhead'><label for='textarea'>Message</label></td><td><textarea name='message' cols='80' rows='20' id='textarea'></textarea></td>
		</tr>
		<tr>
			<td colspan='2' align='center'><input type='submit' value='Send' class='btn' id='send' /></td>
		</tr>
	</table>
</form>

<p>
<span style='font-size: small; font-weight:bold;'>Note: Your IP-address will be logged and visible to the recipient to prevent abuse.<br />
Make sure to supply a valid e-mail address if you expect a reply.</span>
</p>

<?php

site_footer();

?>