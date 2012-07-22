<?php

/*
*-------------------------------------------------------------------------------*
*----------------	 |	____|		 |__   __/ ____|  __ \		  --------------*
*----------------	 | |__ _ __	___	 ___| |	| (___ | |__) |		  --------------*
*----------------	 |	__|	'__/ _ \/ _	\ |	 \___ \|  ___/		  --------------*
*----------------	 | |  |	| |	 __/  __/ |	 ____) | |			  --------------*
*----------------	 |_|  |_|  \___|\___|_|	|_____/|_|			  --------------*
*-------------------------------------------------------------------------------*
*---------------------------	FreeTSP	 v1.0	--------------------------------*
*-------------------   The Alternate BitTorrent	Source	 -----------------------*
*-------------------------------------------------------------------------------*
*-------------------------------------------------------------------------------*
*--	  This program is free software; you can redistribute it and /or modify	   --*
*--	  it under the terms of	the	GNU	General	Public License as published	by	  --*
*--	  the Free Software	Foundation;	either version 2 of	the	License, or		  --*
*--	  (at your option) any later version.									  --*
*--																			  --*
*--	  This program is distributed in the hope that it will be useful,		  --*
*--	  but WITHOUT ANY WARRANTY;	without	even the implied warranty of		  --*
*--	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See	the			  --*
*--	  GNU General Public License for more details.							  --*
*--																			  --*
*--	  You should have received a copy of the GNU General Public	License		  --*
*--	  along	with this program; if not, write to	the	Free Software			  --*
*--	Foundation,	Inc., 59 Temple	Place, Suite 330, Boston, MA  02111-1307 USA  --*
*--																			  --*
*-------------------------------------------------------------------------------*
*------------	Original Credits to	tbSource, Bytemonsoon, TBDev   -------------*
*-------------------------------------------------------------------------------*
*-------------			 Developed By: Krypto, Fireknight			------------*
*-------------------------------------------------------------------------------*
*-----------------		 First Release Date	August 2010		 -------------------*
*-----------				 http://www.freetsp.info				 -----------*
*------					   2010	FreeTSP	Development	Team				  ------*
*-------------------------------------------------------------------------------*
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_page_verify.php');

$newpage = new page_verify();
$newpage->create('_login_');

db_connect();

//site_header("Login");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"	content="text/html;	charset=utf-8" />

<title><? echo $site_name ?></title>

<!-- STYLE SHEET GENERAL -->
<link href="css/reset.css" rel="stylesheet"	type="text/css"	/>
<link href="css/960.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet"	type="text/css"	/>

<!-- JQUERY	-->
<script	type="text/javascript" src="js/jquery.min.js"></script>

<!-- CUSTOM	JS -->
<script	type="text/javascript" src="js/custom.min.js"></script>

</head>

<body>
<table width='100%'	cellspacing='0'	cellpadding='0'	style='background: transparent'>
	<tr>
		<td>
			<div align='center'>
				<a href='index.php'><img src='<?php	echo $image_dir?>logo.png' width='486' height='100'	border='0' alt='<?php echo $site_name?>' title='<?php echo $site_name?>' style='vertical-align:	middle;' /></a>
			</div>
		</td>
	</tr>
</table>
<!-- START CONTAINER -->
<div class="container_12">

	<div class="loading	radius-left">
		<span>Loading...</span>
	</div>

	<!-- START FORMS -->
	<div class="grid_4 push_4">

		<div class="box	radius">

			<form action="takelogin.php" id="login"	class="active">

				<h1>Log	in</h1>

				<fieldset class="radius">

					<p><?php echo $maxloginattempts	?> Failed Logins in	a row will result in Banning your IP.<br /><br />
					You	have&nbsp;<?php	echo remaining ();?> Login Attempt(s).</p>

					<p>
						<label class="required"	for="username">Username</label>
						<br	/>
						<input type="text" name="username" id="username" />
					</p>

					<p>
						<label class="required"	for="password">Password</label>
						<br	/>
						<input type="password" name="password" id="password" />
					</p>

					<input type="submit" class="button button-orange float_right" value="Log in" />

					<br	/>
					<p><a href="#" class="link"	rel="registration">Create your Account Here!</a></p>
					<p><a href="#" class="link"	rel="lost_password">Forgot your	Password?</a></p>

				</fieldset>

			</form>

			<form action="recover.php" id="lost_password">

				<h1>Forgot Password</h1>

				<fieldset class="radius">

					<p>
						<label class="required"	for="email">Registered Email</label>
						<br	/>
						<input type="text" name="email"	id="email" />
					</p>

					<input type="submit" class="button button-orange float_right" value="Send" />

					<br	/>
					<p><a href="#" class="link"	rel="login">Log	in Here!</a></p>
					<p><a href="#" class="link"	rel="registration">Create your Account Here!</a></p>

				</fieldset>

			</form>

			<form action="takesignup.php" id="registration">

				<h1>Registration</h1>
			<?php
				$res = sql_query("SELECT COUNT(*)
									FROM users") or sqlerr(__FILE__, __LINE__);

				$arr = mysql_fetch_row($res);

				if ($arr[0]	>= $max_users)
					display_message("info",	"<span style='color	: #ff0000;'>Sorry</span>", "<span style='color : #00ff00;'>The current user	account	limit (" . number_format($max_users) . ") has been reached.	Inactive accounts are pruned all the time, please check	back again later...</span>");
			?>
				<fieldset class="radius">

					<p>
						<label class="required"	for="registration_username">Desired	Username</label>
						<br	/>
						<input type="text" name="wantusername" id="registration_username" />
					</p>

					<p>
						<label class="required"	for="registration_password">Pick a Password</label>
						<br	/>
						<input type="password" name="wantpassword" id="registration_password" />
					</p>

					<p>
						<label class="required"	for="registration_password_repeat">Enter Password Again</label>
						<br	/>
						<input type="password" name="passagain"	id="registration_password_repeat" />
					</p>

					<p>
						<label class="required"	for="registration_email">Email Address</label>
						<br	/>
						<input type="text" name="email"	id="registration_email"	/>
					</p>

					<p>
						<input type="checkbox" name="rulesverify" value="yes" /> I have	read the Site Rules.<br	/>
						<input type="checkbox" name="faqverify"	value="yes"	/> I agree to read the FAQ.<br />
						<input type="checkbox" name="ageverify"	value="yes"	/> I am	at least 13	years old.<br /><br	/>

						<input type="submit" value="Create Account"	class="button button-orange	float_right" /><br />
					</p>

					<p><a href="#" class="link"	rel="login"	>Log in	Here!</a></p>

				</fieldset>

			</form>

		</div>

	</div>
	<!-- END FORMS -->

</div>
<!-- END CONTAINER -->

</body>
</html>
<?

//site_footer();

?>