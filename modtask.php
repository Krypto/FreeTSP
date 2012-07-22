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

db_connect(false);
logged_in();

$newpage = new page_verify();
$newpage->check('_modtask_');

if ($CURUSER['class'] <	UC_MODERATOR)  die();

// Correct call	to script
if ((isset($_POST['action'])) && ($_POST['action'] == "edituser"))
{
	// Set user	id
	if (isset($_POST['userid'])) $userid = $_POST['userid'];
	else die();

	// and verify...
	if (!is_valid_id($userid)) error_message("error", "Error", "Bad	User ID.");

	// Handle CSRF (modtask	posts form other domains, especially to	update class)
	require_once(INCL_DIR.'function_user_validator.php');

	if (!validate($_POST[validator], "ModTask_$userid" ))
		die	("Invalid" );

	// Fetch current user data...
	$res =	sql_query("SELECT *
						FROM users
						WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);

	$user =	mysql_fetch_assoc($res)	or sqlerr(__FILE__,	__LINE__);

	//== Check to make sure	your not editing someone of	the	same or	higher class
	if ($CURUSER["class"] <= $user['class']	&& ($CURUSER['id']!= $userid &&	$CURUSER["class"] <	UC_ADMINISTRATOR))
		error_message("warn", "Warning", "You cannot edit someone of the same or higher	class..	 Action	Logged");

	$updateset = array();

	if ((isset($_POST['modcomment'])) && ($modcomment =	$_POST['modcomment'])) ;

	else $modcomment = "";

	// Set class
	if ((isset($_POST['class'])) &&	(($class = $_POST['class'])	!= $user['class']))
	{
		if ($class >= UC_SYSOP || ($class >= $CURUSER['class'])	|| ($user['class'] >= $CURUSER['class']))
			error_message("error", "User Error", "Please try again");

		if (!is_valid_user_class($class) ||	$CURUSER["class"] <= $_POST['class'])
			error_message("error", "Error",	"Bad Class");

		// Notify user
		$what	= ($class > $user['class'] ?	"Promoted" : "Demoted");
		$msg	= sqlesc("You have been $what to	'" . get_user_class_name($class) . "' by ".$CURUSER['username']);
		$added	= sqlesc(get_date_time());

		sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
					VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

		$updateset[] = "class =	".sqlesc($class);

		$modcomment	 = gmdate("Y-m-d") . " - $what to '" . get_user_class_name($class) . "'	by $CURUSER[username].\n". $modcomment;
	}

	// Clear Warning - Code	not	called for setting warning
	if (isset($_POST['warned'])	&& (($warned = $_POST['warned']) !=	$user['warned']))
	{
		$updateset[] = "warned = " . sqlesc($warned);
		$updateset[] = "warneduntil	= '0000-00-00 00:00:00'";

		if ($warned	== 'no')
		{
			$modcomment	= gmdate("Y-m-d") .	" -	Warning	removed	by " . $CURUSER['username']	. ".\n". $modcomment;
			$msg		= sqlesc("Your Warning has been	Removed	by " . $CURUSER['username']	. ".");
			$added		= sqlesc(get_date_time());

			sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
						VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
		}
	}

	// Set warning - Time based
	if (isset($_POST['warnlength'])	&& ($warnlength	= 0	+ $_POST['warnlength']))
	{
		unset($warnpm);
		if (isset($_POST['warnpm'])) $warnpm = $_POST['warnpm'];

		if ($warnlength	== 255)
		{
			$modcomment	 = gmdate("Y-m-d") . " - Warned	by " . $CURUSER['username']	. ".\nReason: $warnpm\n" . $modcomment;
			$msg		 = sqlesc("You have	received a Rules Warning from ".$CURUSER['username'].($warnpm ?	"\n\nReason: $warnpm" :	""));
			$updateset[] = "warneduntil	= '0000-00-00 00:00:00'";
		}
		else
		{
			$warneduntil = get_date_time(gmtime() +	$warnlength	* 604800);
			$dur		 = $warnlength . " week" . ($warnlength	> 1	? "s" :	"");
			$msg		 = sqlesc("You have	received a $dur	Rules Warning from ".$CURUSER['username'].($warnpm ? "\n\nReason: $warnpm" : ""));
			$modcomment	 = gmdate("Y-m-d") . " - Warned	for	$dur by	" .	$CURUSER['username'] .	".\nReason:	$warnpm\n" . $modcomment;
			$updateset[] = "warneduntil	= ".sqlesc($warneduntil);
		}
		$added = sqlesc(get_date_time());

		sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
					VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

		$updateset[] = "warned = 'yes'";
	}

	// Clear donor - Code not called for setting donor
	if (isset($_POST['donor']) && (($donor = $_POST['donor']) != $user['donor']))
	{
		$updateset[] = "donor =	" .	sqlesc($donor);
		//$updateset[] = "donoruntil = '0000-00-00 00:00:00'";
		if ($donor == 'no')
		{
			$modcomment	= gmdate("Y-m-d") .	" -	Donor Status Removed by	".$CURUSER['username'].".\n". $modcomment;
			$msg		= sqlesc("Your Donator Status has Expired.");
			$added		= sqlesc(get_date_time());

			sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
						VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
		}
		elseif ($donor == 'yes')
			$modcomment	= gmdate("Y-m-d") .	" -	Donor status added by ".$CURUSER['username'].".\n".	$modcomment;
	}

	// Set donor - Time	based
	/*
	if ((isset($_POST['donorlength'])) && ($donorlength	= 0	+ $_POST['donorlength']))
	{
		if ($donorlength ==	255)
		{
			$modcomment	 = gmdate("Y-m-d") . " - Donor Status set by " . $CURUSER['username'] .	".\n" .	$modcomment;
			$msg		 = sqlesc("You have	received Donor Status from ".$CURUSER['username']);
			$updateset[] = "donoruntil = '0000-00-00 00:00:00'";
		}
		else
		{
			$donoruntil	= get_date_time(gmtime() + $donorlength	* 604800);
			$dur		= $donorlength . " week" . ($donorlength > 1 ? "s" : "");
			$msg		= sqlesc("You have received	Donator	Status for $dur	from " . $CURUSER['username']);
			$modcomment	= gmdate("Y-m-d") .	" -	Donator	Status set for $dur	by " . $CURUSER['username']."\n".$modcomment;

			$updateset[] = "donoruntil = ".sqlesc($donoruntil);
		}
		$added = sqlesc(get_date_time());

		sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
					VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

		$updateset[] = "donor =	'yes'";
	}
*/

	// Change users	sig
	if ((isset($_POST['signature'])) &&	(($signature = $_POST['signature'])	!= ($cursignature =	$user['signature'])))
	{
		$modcomment	 = gmdate("Y-m-d") . " - Signature changed from	'".$cursignature."'	to '".$signature."'	by " . $CURUSER['username']	. ".\n"	. $modcomment;

		$updateset[] = "signature =	".sqlesc($signature);

		write_log("User	ID <a href=userdetails.php?id=$userid>$userid</a> had there	signature changed from $cursignature to	$signature by <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>");
	}

	// Enable /	Disable
	if ((isset($_POST['enabled'])) && (($enabled = $_POST['enabled']) != $user['enabled']))
	{
		if ($enabled ==	'yes')
			$modcomment	= gmdate("Y-m-d") .	" -	Enabled	by " . $CURUSER['username']	. ".\n"	. $modcomment;
		else
			$modcomment	= gmdate("Y-m-d") .	" -	Disabled by	" .	$CURUSER['username'] . ".\n" . $modcomment;

		$updateset[]	= "enabled = " . sqlesc($enabled);
	}

	/* If your running the forum post enable/disable, uncomment	this section
		// Forum Post Enable / Disable
		if ((isset($_POST['forumpost'])) &&	(($forumpost = $_POST['forumpost'])	!= $user['forumpost']))
		{
			if ($forumpost == 'yes')
			{
				$modcomment	= gmdate("Y-m-d")."	- Posting Enabled by ".$CURUSER['username'].".\n" .	$modcomment;
				$msg		= sqlesc("Your Posting rights have been	given back by ".$CURUSER['username'].".	You	can	post to	forum again.");
				$added		= sqlesc(get_date_time());

				sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
							VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			else
			{
				$modcomment	= gmdate("Y-m-d")."	- Posting Disabled by ".$CURUSER['username'].".\n" . $modcomment;
				$msg		= sqlesc("Your Posting rights have been	removed	by ".$CURUSER['username'].", Please	PM ".$CURUSER['username']."	for	the	reason why.");
				$added		= sqlesc(get_date_time());
				sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
							VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			$updateset[] = "forumpost =	" .	sqlesc($forumpost);
		} */

	// Change Custom Title
	if ((isset($_POST['title'])) &&	(($title = $_POST['title'])	!= ($curtitle =	$user['title'])))
	{
		$modcomment	 = gmdate("Y-m-d") . " - Custom	Title changed to '".$title."' from '".$curtitle."' by "	. $CURUSER['username'] . ".\n" . $modcomment;

		$updateset[] = "title =	" .	sqlesc($title);
	}

	// Change users	info
	if ((isset($_POST['info']))	&& (($info = $_POST['info']) !=	($curinfo =	$user['info'])))
	{
	$modcomment	= gmdate("Y-m-d") .	" -	Info changed from '".$curinfo."' to	'".$info."'	by " . $CURUSER['username']	. ".\n"	. $modcomment;

	$updateset[] = "info = ".sqlesc($info);
	}

	// The following code will place the old passkey in	the	mod	comment	and	create
	// a new passkey. This is good practice	as it allows usersearch	to find	old
	// passkeys	by searching the mod comments of members.

	// Reset Passkey
	if ((isset($_POST['resetpasskey']))	&& ($_POST['resetpasskey']))
	{
		$newpasskey	 = md5($user['username'].get_date_time().$user['passhash']);
		$modcomment	 = gmdate("Y-m-d") . " - Passkey ".sqlesc($user['passkey'])." Reset	to ".sqlesc($newpasskey)." by "	. $CURUSER['username'] . ".\n" . $modcomment;

		$updateset[] = "passkey=".sqlesc($newpasskey);
	}

	/* This	code is	for	use	with the safe mod comment modification.	If you have	installed
   the safe	mod	comment	mod, then uncomment	this section...*/

	// Add Comment to ModComment
	if ((isset($_POST['addcomment'])) && ($addcomment =	trim($_POST['addcomment'])))
	{
		$modcomment	= gmdate("Y-m-d") .	" -	".$addcomment."	- "	. $CURUSER['username'] . ".\n" . $modcomment;
	}

	/* Uncomment the following code	if you have	the	upload mod installed...

		// Set Upload Enable / Disable
		if ((isset($_POST['uploadpos'])) &&	(($uploadpos = $_POST['uploadpos'])	!= $user['uploadpos']))
		{
			if ($uploadpos == 'yes')
			{
				$modcomment	= gmdate("Y-m-d") .	" -	Upload Enabled by "	. $CURUSER['username'] . ".\n" . $modcomment;
				$msg		= sqlesc("You have been	given Upload Rights	by " . $CURUSER['username']	. ". You can now upload	torrents.");
				$added		= sqlesc(get_date_time());

				sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
							VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			elseif ($uploadpos == 'no')
			{
				$modcomment	= gmdate("Y-m-d") .	" -	Upload Disabled	by " . $CURUSER['username']	. ".\n"	. $modcomment;
				$msg		= sqlesc("Your Upload Rights have been Removed by "	. $CURUSER['username'] . ".	Please PM ".$CURUSER['username']." for the reason why.");
				$added		= sqlesc(get_date_time());

				sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
							VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			else
				die(); // Error

			$updateset[] = "uploadpos =	" .	sqlesc($uploadpos);
		} */

	/* Uncomment the following code	if you have	the	download mod installed...

		// Set Download	Enable / Disable
		if ((isset($_POST['downloadpos'])) && (($downloadpos = $_POST['downloadpos']) != $user['downloadpos']))
		{
			if ($downloadpos ==	'yes')
			{
				$modcomment	= gmdate("Y-m-d") .	" -	Download enabled by	" .	$CURUSER['username'] . ".\n" . $modcomment;
				$msg		= sqlesc("Your Download	Rights have	been given back	by " . $CURUSER['username']	. ". You can download torrents again.");
				$added		= sqlesc(get_date_time());

				sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
							VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			elseif ($downloadpos ==	'no')
			{
				$modcomment	= gmdate("Y-m-d") .	" -	Download Disabled by " . $CURUSER['username'] .	".\n" .	$modcomment;
				$msg		= sqlesc("Your Download	Rights have	been removed by	" .	$CURUSER['username'] . ", Please PM	".$CURUSER['username']." for the reason	why.");
				$added		= sqlesc(get_date_time());

				sql_query("INSERT INTO messages	(sender, receiver, msg,	added)
							VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
			}
			else
				die(); // Error

			$updateset[] = "downloadpos	= "	. sqlesc($downloadpos);
		}  */

	// Avatar Changed
	if ((isset($_POST['avatar'])) && (($avatar = $_POST['avatar']) != ($curavatar =	$user['avatar'])))
	{
		$modcomment	 = gmdate("Y-m-d") . " - Avatar	changed	from ".htmlspecialchars($curavatar)." to ".htmlspecialchars($avatar)." by "	. $CURUSER['username'] . ".\n" . $modcomment;

		$updateset[] = "avatar = ".sqlesc($avatar);
	}

	/* Uncomment if	you	have the First Line	Support	mod	installed...

		// Support
		if ((isset($_POST['support'])) && (($support = $_POST['support']) != $user['support']))
		{
			if ($support ==	'yes')
			{
				$modcomment	= gmdate("Y-m-d") .	" -	Promoted to	FLS	by " . $CURUSER['username']	. ".\n"	. $modcomment;
			}
			elseif ($support ==	'no')
			{
				$modcomment	= gmdate("Y-m-d") .	" -	Demoted	from FLS by	" .	$CURUSER['username'] . ".\n" . $modcomment;
			}
			else
				die();

			$supportfor	 = $_POST['supportfor'];

			$updateset[] = "support	= "	. sqlesc($support);
			$updateset[] = "supportfor = ".sqlesc($supportfor);
		} */

	// Add ModComment... (if we	changed	something we update	otherwise we dont include this..)
	if (($CURUSER['class'] == UC_SYSOP && ($user['modcomment'] != $_POST['modcomment'] || $modcomment!=$_POST['modcomment'])) || ($CURUSER['class']<UC_SYSOP &&	$modcomment	!= $user['modcomment']))
	$updateset[] = "modcomment = " . sqlesc($modcomment);

	if (sizeof($updateset)>0)
		sql_query("UPDATE users	SET	 " . implode(",	", $updateset) . " WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);

		$returnto =	$_POST["returnto"];
		header("Location: $site_url/$returnto");

		die();
}

?>