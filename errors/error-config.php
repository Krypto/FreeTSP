<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** http://www.freetsp.info
** https://github.com/Krypto/FreeTSP
** Licence Info: GPL
** Copyright (C) 2010 FreeTSP v1.0
** A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
** Project Leaders: Krypto, Fireknight.
**/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');

//-- Error Handler Configuration File --//

// Your Site URL ( Without The Trailing Slash )
$root = $site_url;

//-- If You Wish That The Script Give Suggessions To Users Whene They Requeste A Page With An Old Extention Uncomment Those Two Lines And Enter Your Old And New Pages Extension --//

//$oldExt = 'html'; // The Old Extension
//$newExt = 'php'; // The New Extension

//-- Do You Want A Notification Email To Be Sent (Y Or N)? --//
$email_notification = 'y';

//-- The Address The Notification Email Is To Be Sent To --//
$email = $site_email;

//-- The From Address For Your Notification Email --//
$from = $site_email;

//-- Error Messages (you Don't Need To Modify Any Thing Here) --//

$error401 = '<div style="padding-top:15px; text-align:center;"><img src="/errors/error-images/alert.png" alt="" /></div>';
$error401 .= '<h1 class="title">Error 401 - Authorisation Required</h1>';
$error401 .= '<p class="sub-title">You are trying to access a protected area.</p>';
$error401 .= '<p>Either you have typed in the wrong username and /or password or your browser cannot supply this information correctly.</p>';
$error401 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$error403 = '<div style="padding-top:15px; text-align:center;"><img src="/errors/error-images/alert.png" alt="" /></div>';
$error403 .= '<h1 class="title">Error 403 - Access Denied</h1>';
$error403 .= '<p class="sub-title">You do not have permission to access this area.</p>';
$error403 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$error404 = '<div style="padding-top:15px; text-align:center;"><img src="/errors/error-images/alert.png" alt="" /></div>';
$error404 .= '<h1 class="title">Error 404 - Page Not Found</h1>';
$error404 .= '<p class="sub-title">The page that you are looking for does not appear to exist on this site.</p>';
$error404 .= '<p>If you typed the address of the page into the address bar of your browser, please check that you typed it in correctly.</p>';
$error404 .= '<p>If you arrived at this page after you used an old Boomark or Favorite, the page in question has probably been 	moved. Try locating the page via the navigation menu and then updating your bookmark.</p>';
$error404 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$error500 = '<div style="padding-top:15px; text-align:center;"><img src="/errors/error-images/alert.png" alt="" /></div>';
$error500 .= '<h1 class="title">Error 500 - Server Error</h1>';
$error500 .= '<p class="sub-title">Our web server has encountered an unexpected condition that currently prevents it from fulfilling your submitted request.</p>';
$error500 .= '<p>If this situation persists, please <a href="mailto:'.$email.'">contact us</a>.</p>';

$suspect = '<div style="padding-top:15px; text-align:center;"><img src="/errors/error-images/alert.png" alt="" /></div>';
$suspect .= '<h1 class="title">Error</h1><p class="sub-title">This page has been called incorrectly!</p>';

$thanksMessage = '<h1 class="title" style="padding-top:150px">Thank You</h1>';
$thanksMessage .= '<p>Automatic notification has been sent. The report will be investigated as soon as possible.</p>'

?>