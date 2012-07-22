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

//FreeTSP Reputation System v1.0 - Please Leave Credits In Place.
//Reputation Mod - - Subzero Thanks to google.com! for the reputation image!
//File Completed 02 July 2010 At 19:42 Secound Offical Submit

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');

db_connect();
logged_in();

$id = 0 + $_GET['id'];

$res = sql_query("SELECT id
					FROM users
					WHERE id = ".sqlesc($id)) or die();

$row = mysql_fetch_assoc($res) or error_message("error", "Error", "User was not found");

$userid = $row['id'];

if ($userid == $CURUSER['id'])
	error_message("warn", "Sorry", "You cant give yourself Reputation Points!!");

	site_header();
{

	//Lets update the database with new reputation points do not alter if you do not know what you are doing - Subzero
	sql_query ("UPDATE users
				SET reputation=reputation+1
				WHERE id = '$id'") or sqlerr(__FILE__, __LINE__);

	begin_frame("Adding Reputation Point");

	display_message("success", "Success", "You Added A Reputation Point To This User!!");

	header("Refresh: 3; url='userdetails.php?id=$id'");

	end_frame();
}

site_footer();

?>