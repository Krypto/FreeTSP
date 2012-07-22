<?php
/*
*-------------------------------------------------------------------------------*
*----------------    |  ____|        |__   __/ ____|  __ \        --------------*
*----------------    | |__ _ __ ___  ___| | | (___ | |__) |       --------------*
*----------------    |  __| '__/ _ \/ _ \ |  \___ \|  ___/        --------------*
*----------------    | |  | | |  __/  __/ |  ____) | |            --------------*
*----------------    |_|  |_|  \___|\___|_| |_____/|_|            --------------*
*-------------------------------------------------------------------------------*
*---------------------------    FreeTSP RC 3.0   -------------------------------*
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
*--------           Developed By: Krypto, Fireknight, Subzero           --------*
*-------------------------------------------------------------------------------*
*-----------------       First Release Date August 2010      -------------------*
*-----------                 http://www.freetsp.info                 -----------*
*------                    2010 FreeTSP Development Team                  ------*
*-------------------------------------------------------------------------------*
*/


require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_torrenttable.php');

db_connect(false);
logged_in();

if (get_user_class() < UC_MODERATOR)
	error_message("warn", "Warning", "Access Denied!");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($_POST["username"] == "" || $_POST["uploaded"] == "" || $_POST["downloaded"] == "")
	{
		error_message("error", "Error", "Missing form data.");
	}

	$username = sqlesc($_POST["username"]);

	if($_POST["bytes"]=='1')
	{
		$uploaded = $_POST["uploaded"]*1024*1024;
		$downloaded = $_POST["downloaded"]*1024*1024;
	}
	elseif($_POST["bytes"]=='2')
	{
		$uploaded = $_POST["uploaded"]*1024*1024*1024;
		$downloaded = $_POST["downloaded"]*1024*1024*1024;
	}
	elseif($_POST["bytes"]=='3')
	{
		$uploaded = $_POST["uploaded"]*1024*1024*1024*1024;
		$downloaded = $_POST["downloaded"]*1024*1024*1024*1024;
	}

	if($_POST["action"] =='1')
	{
		$result = sql_query("SELECT uploaded, downloaded
								FROM users
								WHERE username=$username") or sqlerr(__FILE__, __LINE__);

		$arr		= mysql_fetch_assoc($result);
		$uploaded	= $arr["uploaded"]+$uploaded;
		$downloaded = $arr["downloaded"]+$downloaded;

		mysql_query("UPDATE users
						SET uploaded=$uploaded, downloaded=$downloaded
						WHERE username=$username") or sqlerr(__FILE__, __LINE__);
	}
	elseif($_POST["action"] =='2')
	{
		$result = sql_query("SELECT uploaded, downloaded
								FROM users
								WHERE username=$username") or sqlerr(__FILE__, __LINE__);

		$arr		= mysql_fetch_assoc($result);
		$uploaded	= $arr["uploaded"]-$uploaded;
		$downloaded = $arr["downloaded"]-$downloaded;

		sql_query("UPDATE users
						SET uploaded=$uploaded, downloaded=$downloaded
						WHERE username=$username") or sqlerr(__FILE__, __LINE__);
	}
	elseif($_POST["action"] =='3')

		sql_query("UPDATE users
						SET uploaded=$uploaded, downloaded=$downloaded
						WHERE username=$username") or sqlerr(__FILE__, __LINE__);

		$result = sql_query("SELECT id
								FROM users
								WHERE username=$username") or sqlerr(__FILE__, __LINE__);

		$arr	= mysql_fetch_assoc($result);
		$id		= $arr["id"];

		$result = sql_query("SELECT id
								FROM users
								WHERE username=$username") or sqlerr(__FILE__, __LINE__);

		$arr	= mysql_fetch_assoc($result);
		$id		= $arr["id"];

		header("Location: $site_url/userdetails.php?id=$id");

	die;
}

?>