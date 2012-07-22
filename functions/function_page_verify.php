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
// session so that repeated access of this page cannot happen without the calling script.
// You use the create function with the sending script, and the check function with the
// receiving script...
// You need to pass the value of $task from the calling script to the receiving script. While
// this may appear dangerous, it still only allows a one shot at the receiving script, which
// effectively stops flooding.
// page verify by Retro

class page_verify
{
	function page_verify ()
	{
		if ( session_id () == '' )
		{
			session_start ();
		}
	}

	function create ( $task_name = 'Default' )
	{
		global $CURUSER;

		$_SESSION['Task_Time'] = time ();
		$_SESSION['Task'] = md5( 'user_id:' . $CURUSER['id'] . '::taskname-' . $task_name . '::' . $_SESSION['Task_Time'] );
		$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	}

	function check ( $task_name = 'Default' )
	{
		global $CURUSER, $site_url;

		$returl = ( isset( $CURUSER )?htmlspecialchars( $_SERVER['HTTP_REFERER'] ):$site_url . "/login.php" );
		$returl = str_replace( '&', '&', $returl );

		if ( isset( $_SESSION['HTTP_USER_AGENT'] ) && $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'] )
			error_message("error", "Error", "Please resubmit the form. <a href='" . $returl . "'>Click HERE</a>", false );

		if ( $_SESSION['Task'] != md5( 'user_id:' . $CURUSER['id'] . '::taskname-' . $task_name . '::' . $_SESSION['Task_Time'] ) )
			error_message("error", "Error", "Please resubmit the form. <a href='" . $returl . "'>Click HERE</a>", false );
		$this->create ();
	}
}

?>