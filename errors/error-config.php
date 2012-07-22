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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');

/************************************************************************
	Error Handler configuration file
************************************************************************/

// Your site URL ( Without the trailing slash )
$root = $site_url;

/*
 If you wish that the script give suggessions to users whene they requeste a page with an old extention
 Uncomment those two lines and enter your old and new pages extension
*/

//$oldExt = 'html'; // The old extension
//$newExt = 'php'; // The new extension

// Do you want a notification email to be sent (y or n)?
$email_notification = 'y';

// The address the notification email is to be sent TO
$email = $site_email;

//The From address for your notification email
$from = $site_email;

/*================== Error Messages (You don't need to modify any thing here) ======================*/

$error401  = '<div align="center" style="padding-top:20px"><img src="../errors/error-images/alert.png"></div>';
$error401 .= '<h1 class="title">Error 401 - Authorisation Required</h1>';
$error401 .= '<p class="sub-title">You are trying to access a protected area.</p>';
$error401 .= '<p>Either you have typed in the wrong username and /or password or your browser cannot supply this information correctly.</p>';
$error401 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$error403  = '<div align="center" style="padding-top:20px"><img src="../errors/error-images/alert.png"></div>';
$error403 .= '<h1 class="title">Error 403 - Access Denied</h1>';
$error403 .= '<p class="sub-title">You do not have permission to access this area.</p>';
$error403 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$error404  = '<div align="center" style="padding-top:15px"><img src="../errors/error-images/alert.png"></div>';
$error404 .= '<h1 class="title">Error 404 - Page Not Found</h1>';
$error404 .= '<p class="sub-title">The page that you are looking for does not appear to exist on this site.</p>';
$error404 .= '<p>If you typed the address of the page into the address bar of your browser, please check that you typed it in correctly.</p>';
$error404 .= '<p>If you arrived at this page after you used an old Boomark or Favorite, the page in question has probably been 	moved. Try locating the page via the navigation menu and then updating your bookmark.</p>';
$error404 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$error500  = '<div align="center" style="padding-top:20px"><img src="../errors/error-images/alert.png"></div>';
$error500 .= '<h1 class="title">Error 500 - Server Error</h1>';
$error500 .= '<p class="sub-title">Our web server has encountered an unexpected condition that currently prevents it from fulfilling your submitted request.</p>';
$error500 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$suspect  = '<div align="center" style="padding-top:20px"><img src="../errors/error-images/alert.png"></div>';
$suspect .= '<h1 class="title">Error</h1><p  class="sub-title">This page has been called incorrectly!</p>';

$thanksMessage  = '<h1 class="title" style="padding-top:150px">Thank You</h1>';
$thanksMessage .= '<p>Automatic notification has been sent. The report will be investigated as soon as possible.</p>'

?>