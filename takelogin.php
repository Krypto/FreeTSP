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
*-----------------         First Release Date July 2010      -------------------*
*-----------                 http://www.freetsp.info                 -----------*
*------                    2010 FreeTSP Development Team                  ------*
*-------------------------------------------------------------------------------*
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_page_verify.php');

if (!mkglobal("username:password"))
	die();

$sha=sha1($_SERVER['REMOTE_ADDR']);

if(is_file(''.$dictbreaker.'/'.$sha) && filemtime(''.$dictbreaker.'/'.$sha)>(time()-8))
{
	@fclose(@fopen(''.$dictbreaker.'/'.$sha,'w'));
	die('Minimum 8 seconds between Login Attempts :)');
}

db_connect();

$newpage = new page_verify();
$newpage->check('_login_');

failedloginscheck ();

$res = sql_query("SELECT id, passhash, secret, enabled
					FROM users
					WHERE username = " . sqlesc($username) . "
					AND status = 'confirmed'");
$row = mysql_fetch_assoc($res);

if (!$row)
{
	$ip		= sqlesc(getip());
	$added	= sqlesc(get_date_time());

	$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*)
										FROM loginattempts
										WHERE ip=$ip"))) or sqlerr(__FILE__, __LINE__);

	if ($a[0] == 0)
		sql_query("INSERT INTO loginattempts (ip, added, attempts)
					VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
	else
		sql_query("UPDATE loginattempts
					SET attempts = attempts + 1
					WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);

		@fclose(@fopen(''.$dictbreaker.'/'.sha1($_SERVER['REMOTE_ADDR']),'w'));

		error_message("error", "Error", "<a href='/login.php'>Login Failed!  Username or Password Incorrect?</a>");
}

if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"]))
{
	$ip		= sqlesc(getip());
	$added	= sqlesc(get_date_time());

	$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*)
										FROM loginattempts
										WHERE ip=$ip"))) or sqlerr(__FILE__, __LINE__);

	if ($a[0] == 0)
		sql_query("INSERT INTO loginattempts (ip, added, attempts)
					VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
	else
		sql_query("UPDATE loginattempts
					SET attempts = attempts + 1
					WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);

		@fclose(@fopen(''.$dictbreaker.'/'.sha1($_SERVER['REMOTE_ADDR']),'w'));

	$to		= ($row["id"]);
	$sub	= "Security Alert";
	$msg	= "[color=red]SECURITY ALERT[/color]\n\n Account: ID=".$row['id']." Somebody (probably you, [b]".$username."![/b]) tried to Login but Failed!". "\n\nTheir [b]IP ADDRESS [/b] was : ([b]". $ip . " ". @gethostbyaddr($ip) . "[/b])". "\n\n If this wasn't you please report this event to a staff \n\n - Thank you.\n";

	$sql = "INSERT INTO messages (subject, sender, receiver, msg, added)
			VALUES ('$sub', '$from', '$to', ". sqlesc($msg).", $added);";

	$res = sql_query($sql) or sqlerr(__FILE__, __LINE__);

	error_message("error", "Error", "<a href='/login.php'>Login Failed!  Username or Password Incorrect?</a>");
}

if ($row["enabled"] == "no")
	error_message("info", "Info", "This Account has been Disabled.");

logincookie($row["id"], $row["passhash"]);

$ip = sqlesc(getip());
sql_query("DELETE
			FROM loginattempts
			WHERE ip = $ip");

$returnto = str_replace('&amp;', '&', htmlspecialchars($_POST['returnto']));

if (!empty($returnto))
	header("Location: ".$returnto);
else
	header("Location: index.php");

?>