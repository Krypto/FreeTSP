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

function get_ratio_color($ratio)
{
	if ($ratio < 0.1)	return "#ff0000";
	if ($ratio < 0.2)	return "#ee0000";
	if ($ratio < 0.3)	return "#dd0000";
	if ($ratio < 0.4)	return "#cc0000";
	if ($ratio < 0.5)	return "#bb0000";
	if ($ratio < 0.6)	return "#aa0000";
	if ($ratio < 0.7)	return "#990000";
	if ($ratio < 0.8)	return "#880000";
	if ($ratio < 0.9)	return "#770000";
	if ($ratio < 1)		return "#660000";
	return "#000000";
}

function get_slr_color($ratio)
{
	if ($ratio < 0.025)		return "#ff0000";
	if ($ratio < 0.05)		return "#ee0000";
	if ($ratio < 0.075)		return "#dd0000";
	if ($ratio < 0.1)		return "#cc0000";
	if ($ratio < 0.125)		return "#bb0000";
	if ($ratio < 0.15)		return "#aa0000";
	if ($ratio < 0.175)		return "#990000";
	if ($ratio < 0.2)		return "#880000";
	if ($ratio < 0.225)		return "#770000";
	if ($ratio < 0.25)		return "#660000";
	if ($ratio < 0.275)		return "#550000";
	if ($ratio < 0.3)		return "#440000";
	if ($ratio < 0.325)		return "#330000";
	if ($ratio < 0.35)		return "#220000";
	if ($ratio < 0.375)		return "#110000";
	return "#000000";
}

function get_user_id()
{
	global $CURUSER;
	return $CURUSER["id"];
}

define ('UC_TRACKER_MANAGER',1); ///---Set the ID# to match the member who will have access to the Tracker Manager---///

function get_user_class()
{
	global $CURUSER;
	return $CURUSER["class"];
}

define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);

function get_user_class_name($class)
{
	switch ($class)
	{
		case UC_USER: return "User";

		case UC_POWER_USER: return "Power User";

		case UC_VIP: return "VIP";

		case UC_UPLOADER: return "Uploader";

		case UC_MODERATOR: return "Moderator";

		case UC_ADMINISTRATOR: return "Administrator";

		case UC_SYSOP: return "SysOp";
	}
	return "";
}

function get_user_class_color($class)
{
	switch ($class)
	{
		case UC_USER: return "8E35EF";
		case UC_POWER_USER: return "f9a200";
		case UC_VIP: return "009F00";
		case UC_UPLOADER: return "0000FF";
		case UC_MODERATOR: return "FE2E2E";
		case UC_ADMINISTRATOR: return "B000B0";
		case UC_SYSOP: return "FF0000";
	}
	return "";
}

function format_username($user, $icons = true)
{
	global $image_dir, $site_url;

	$user['id']    = (int)$user['id'];
	$user['class'] = (int)$user['class'];

	if ( $user['id'] == 0 )
		return 'System';

	elseif ( $user['username'] == '' )
		return 'unknown[' . $user['id'] . ']';

	$username	= '<span style="font-weight:bold; color:#' . get_user_class_color( $user['class'] ) . ';">' . $user['username'] . '</span>';
	$str		= '<span style="white-space: nowrap;"><a class="user_' . $user['id'] . '" href="' . $site_url . '/userdetails.php?id=' . $user['id'] . '"target="_blank">' . $username . '</a>';

	if ($icons != false)
	{
		$str .= ( $user['donor'] == 'yes' ? '<img src="' . $image_dir . 'star.png" width="16" height="16" border="0" alt="Donor" title="Donor" />' : '' );
		$str .= ( $user['warned'] == 'yes' ? '<img src="' . $image_dir . 'warned.png" width="15" height="16" border="0" alt="Warned" title="Warned" />' : '' );
		$str .= ( $user['enabled'] == 'yes' ? '<img src="' . $image_dir . 'disabled.png" width="16" height="15" border="0" alt="Disabled" title="Disabled" />' : '' );
	}
	$str .= "</span>\n";

	return $str;
}

function format_user($user)
{
	global $site_url;
	return '<a href="' . $site_url . '/userdetails.php?id=' . $user['id'] . '" title="' . get_user_class_name( $user['class'] ) . '">
				<span style="color:' . get_user_class_color( $user['class'] ) . ';">' . $user['username'] . '</span>
			</a>' . get_user_icons( $user ) . ' ';
}

function is_valid_user_class($class)
{
	return is_numeric($class) && floor($class) == $class && $class >= UC_USER && $class <= UC_SYSOP;
}

function is_valid_id($id)
{
	return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function get_user_icons($arr, $big = false)
{
	global $image_dir;

	if ($big)
	{
		$donorpic		= "starbig.png";
		$warnedpic		= "warnedbig.png";
		$disabledpic	= "disabledbig.png";
		$style			= "style='margin-left: 4pt'";
	}
	else
	{
		$donorpic		= "star.png";
		$warnedpic		= "warned.png";
		$disabledpic	= "disabled.png";
		$style			= "style='margin-left: 2pt'";
	}

	$pics = $arr["donor"] == "yes" ? "<img src='{$image_dir}{$donorpic}' width='16' height='16' border='0' alt='Donor' title='Donor' $style />" : "";

	if ($arr["enabled"] == "yes")
		$pics .= $arr["warned"] == "yes" ? "<img src='{$image_dir}{$warnedpic}' width='15' height='16' border='0' alt='Warned' title='Warned' $style />" : "";
	else
		$pics .= "<img src='{$image_dir}{$disabledpic}' width='16' height='15' border='0' alt='Disabled' title='Disabled' $style />\n";

	return $pics;
}

?>