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

// Based on	Hanne's	Shoutbox With added	staff functions-putyn shout	and	reply added	Spook

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_bbcode.php');

db_connect(	false );
logged_in();

function autoshout($msg	= '')
{
	$message = $msg;
	sql_query("INSERT INTO shoutbox	(date, text)
				VALUES (" .	implode(", ", array_map("sqlesc", array(time(),	$message)))	. ")") or sqlerr(__FILE__, __LINE__);
}

// Get current datetime
$dt	= gmtime() - 60;
$dt	= sqlesc(get_date_time($dt));

unset (	$insert	);

$insert	= false;
$query	= "";

// DELETE SHOUT
if ( isset(	$_GET['del'] ) && get_user_class() >= UC_MODERATOR && is_valid_id( $_GET['del']	) )
	sql_query( "DELETE
				FROM shoutbox
				WHERE id=" . sqlesc( $_GET['del'] )	);

// Empty shout - coder/owner
if ( isset(	$_GET['delall']	) && get_user_class() >= UC_SYSOP )
	$query = "TRUNCATE
				TABLE shoutbox";

	sql_query( $query );
	unset($query);

// Edit	shout
if (isset($_GET['edit']) &&	get_user_class() >=	UC_MODERATOR &&	is_valid_id($_GET['edit']))
{
	$sql = sql_query('SELECT id,text
						FROM shoutbox
						WHERE id='.sqlesc($_GET['edit']));

	$res = mysql_fetch_assoc($sql);
	unset($sql);

?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
	<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<meta http-equiv='Pragma' content='no-cache' />
	<meta http-equiv='expires' content='-1'	/>
	<html xmlns='http://www.w3.org/1999/xhtml'>

<script	type='text/javascript' src='./js/shout.js'></script>

<style type='text/css'>
#specialbox
{
	border:	1px	solid gray;
	width: 600px;
	background:	#FBFCFA;
	font: 11px verdana,	sans-serif;
	color: #000000;
	padding: 3px;	outline: none;
}
#specialbox:focus
{
	border:	1px	solid black;
}
.btn
{
	cursor:pointer;
	border:outset 1px #ccc;
	background:#999;
	color:#666;
	font-weight:bold;
	padding: 1px 2px;
	background:	#000000	repeat-x left top;
}
</style>

</head>

<body bgcolor='#F5F4EA'	class='date'>

<?php

echo "<form	method='post' action='shoutbox.php'>
		<input type='hidden' name='id' value='".(int)$res['id']."' />
		<textarea name='text' rows='3' id='specialbox'>".htmlspecialchars($res['text'])."</textarea>
		<input type='submit' class='btn' name='save' value='save' />
		</form></body></html>";
	die();
}

// Power Users+	can	edit anyones single	shouts //==	pdq
if (isset($_GET['edit']) &&	($_GET['user'] == $CURUSER['id']) && ($CURUSER['class']	>= UC_MODERATOR	&& $CURUSER['class'] <=	UC_MODERATOR) && is_valid_id($_GET['edit']))
{
	$sql = sql_query('SELECT id, text, userid
						FROM shoutbox
						WHERE userid ='.sqlesc($_GET['user']).'
						AND	id='.sqlesc($_GET['edit']));

	$res = mysql_fetch_array($sql);
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
	<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<meta http-equiv='Pragma' content='no-cache' />
	<meta http-equiv='expires' content='-1'	/>
	<html xmlns='http://www.w3.org/1999/xhtml'>

<script	type='text/javascript' src='./js/shout.js'></script>

<style type='text/css'>
.specialbox
{
	border:	1px	solid gray;
	width: 600px;
	background:	#FBFCFA;
	font: 11px verdana,	sans-serif;
	color: #000000;
	padding: 3px;	outline: none;
}
	.specialbox:focus
{
	border:	1px	solid black;
}
.btn
{
	cursor:pointer;
	border:outset 1px #ccc;
	background:#999;
	color:#666;
	font-weight:bold;
	padding: 1px 2px;
	background:	#000000	repeat-x left top;
}
</style>

</head>

<body bgcolor='#F5F4EA'	class='date'>
<?php
	echo "<form	method='post' action='/shoutbox.php'>
			<input type='hidden' name='id' value='".(int)$res['id']."' />
			<input type='hidden' name='user' value='".(int)$res['userid']."' />
			<textarea name='text' rows='3' id='specialbox'>".htmlspecialchars($res['text'])."</textarea>
			<input type='submit' class='btn' name='save' value='save' />
			</form></body></html>";
	die;
}

// Staff shout edit
if (isset($_POST['text']) && $CURUSER['class'] >= UC_MODERATOR && is_valid_id($_POST['id']))
{
	$text			= trim($_POST['text']);
	$text_parsed	= format_comment($text);

	sql_query ('UPDATE shoutbox
				SET	text = '.sqlesc($text).', text_parsed =	'.sqlesc($text_parsed).'
				WHERE id='.sqlesc($_POST['id']));

	unset ($text, $text_parsed);
}
// Power User+ shout edit
if (isset($_POST['text']) && (isset($_POST['user'])	== $CURUSER['id']) && ($CURUSER['class'] >=	UC_POWER_USER && $CURUSER['class'] < UC_MODERATOR) && is_valid_id($_POST['id']))
{
	$text			= trim($_POST['text']);
	$text_parsed	= format_comment($text);

	sql_query ('UPDATE shoutbox
				SET	text = '.sqlesc($text).', text_parsed =	'.sqlesc($text_parsed).'
				WHERE userid='.sqlesc($_POST['user']).'
				AND	id='.sqlesc($_POST['id']));

	unset ($text, $text_parsed);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<meta http-equiv='Pragma' content='no-cache' />
	<meta http-equiv='expires' content='0' />
	<meta http-equiv='Content-Type'	content='text/html;	charset=utf-8' />
	<title>ShoutBox</title>
	<meta http-equiv='REFRESH' content='60;	URL=./shoutbox.php'	/>

<script	type='text/javascript' src='./js/shout.js'></script>

<style type='text/css'>
A {color: #356AA0; font-weight:	bold; font-size: 9pt; }
A:hover	{color:	#FF0000;}
.small {color: #ff0000;	font-size: 9pt;	font-family: arial;	}
.date {color: #ff0000; font-size: 9pt;}
.error
{
	 color:	#990000;
	 background-color: #FFF0F0;
	 padding: 7px;
	 margin-top: 5px;
	 margin-bottom:	10px;
	 border: 1px dashed	#990000;
}
A {color: #000000; font-weight:	bold; text-decoration :	none; }
A:hover	{color:	#000000;}
.small {font-size: 10pt; font-family: arial; }
.date {font-size: 8pt;}
</style>

</head>

<!-- START DEFINING	BACKGROUND COLOR TO	MATCH THEME	COLOR -->
<?php
echo"<body bgcolor='#F5F4EA'>";
?>
<!-- FINISH	DEFINING BACKGROUND	COLOR TO MATCH THEME COLOR -->

<?php
if ( isset(	$_GET['sent'] )	&& ( $_GET['sent'] == "yes"	) )
{
	$limit			= 1;
	$userid			= $CURUSER["id"];
	$date			= sqlesc( time() );
	$text			= (trim( $_GET["shbox_text"] ));
	$text_parsed	= format_comment($text);

	// quiz	bot
	if(stristr($text,"/quiz") && $CURUSER["class"] >= UC_MODERATOR)

	$userid			= 13767;
	$text			= str_replace(array("/quiz","/QUIZ	[color=red]"),"",$text);
	$text_parsed	= format_comment($text);

	 //	radio bot
	if (stristr($text,"/scast")	&& $CURUSER["class"] >=	UC_MODERATOR)

	$userid			= 13626;
	$text			= str_replace(array("/scast","/SCAST"),"",$text);
	$text_parsed	= format_comment($text);

	//Notice By	Subzero
	if (stristr($text,"/notice") &&	$CURUSER["class"] >= UC_MODERATOR)
		$userid			= 2;
		$text			= str_replace(array("/NOTICE","/notice"),"",$text);
		$text_parsed	= format_comment($text);

	if (stristr($text,"/system") &&	$CURUSER["class"] >= UC_MODERATOR)
	{
		$userid	= 2;
		$text	= str_replace(array("/SYSTEM","/system"),"",$text);
		//$text_parsed = format_comment($text);
	}

	// shoutbox	command	system by putyn	& pdq
	$commands		= array( "\/EMPTY", "\/GAG", "\/UNGAG", "\/WARN", "\/UNWARN", "\/DISABLE",	"\/ENABLE",	"\/" );	// this	/ was replaced with	\/ to work with	the	regex
	$pattern		= "/("	. implode( "|",	$commands )	. "\w+)\s([a-zA-Z0-9_:\s(?i)]+)/";
	//$private_pattern = "/(^\/private)\s([a-zA-Z0-9]+)\s([\w\W\s]+)/";

	if ( preg_match( $pattern, $text, $vars	) && $CURUSER["class"] >= UC_MODERATOR ) {
		$command	= $vars[1];
		$user		= $vars[2];

		$c = sql_query(	"SELECT	id,	class, modcomment
							FROM users
							WHERE username=" .sqlesc($user)) or sqlerr();

		$a = mysql_fetch_row( $c );

		if ( mysql_num_rows( $c	) == 1 && $CURUSER["class"]	> $a[1]	) {
			switch ( $command )	{
				case "/EMPTY" :
					$what	= 'Deleted ALL Shouts';
					$msg	= "[b]" . $user . "'s[/b] Shouts	have been Deleted";

					$query = "DELETE
								FROM shoutbox
								WHERE userid	= "	. $a[0];
					break;

				case "/GAG"	:
					$what		= 'Gagged';
					$modcomment	= gmdate( "Y-m-d" )	. "	- [SHOUTBOX] User has been gagged by " . $CURUSER["username"] .	"\n" . $a["modcomment"];
					$msg		= "[b]"	. $user	. "[/b]	- has been Gagged by " . $CURUSER["username"];

					$query		= "UPDATE users
										SET	chatpost='no', modcomment =	concat(" . sqlesc( $modcomment ) . ", modcomment)
										WHERE	id = " . $a[0];
					break;

				case "/UNGAG" :
					$what		= 'Un-Gagged';
					$modcomment	= gmdate( "Y-m-d" )	. "	- [SHOUTBOX] User has been ungagged	by " . $CURUSER["username"]	. "\n" . $a[2];
					$msg		= "[b]"	. $user	. "[/b]	- has been Un-Gagged by	" .	$CURUSER["username"];

					$query		= "UPDATE users
									SET	chatpost='yes',	modcomment = concat(" .	sqlesc(	$modcomment	) .	", modcomment)
									WHERE id	= "	. $a[0];
					break;

				case "/WARN" :
					$what		= 'Warned';
					$modcomment	= gmdate( "Y-m-d" )	. "	- [SHOUTBOX] User has been warned by " . $CURUSER["username"] .	"\n" . $a[2];
					$msg		= "[b]"	. $user	. "[/b]	- has been Warned by " . $CURUSER["username"];

					$query		= "UPDATE users
									SET	warned='yes', modcomment = concat("	. sqlesc( $modcomment )	. ", modcomment)
									WHERE id =	" .	$a[0];
					break;

				case "/UNWARN" :
					$what		= 'Un-Warned';
					$modcomment	= gmdate( "Y-m-d" )	. "	- [SHOUTBOX] User has been unwarned	by " . $CURUSER["username"]	. "\n" . $a[2];
					$msg		= "[b]"	. $user	. "[/b]	- has been Un-Warned by	" .	$CURUSER["username"];

					$query		= "UPDATE users
									SET	warned='no', modcomment	= concat(" . sqlesc( $modcomment ) . ",	modcomment)
									WHERE id = " . $a[0];
					break;

				case "/DISABLE"	:
					$what		= 'Disabled';
					$modcomment	= gmdate( "Y-m-d" )	. "	- [SHOUTBOX] User has been disabled	by " . $CURUSER["username"]	. "\n" . $a[2];
					$msg		= "[b]"	. $user	. "[/b]	- has been Disabled	by " . $CURUSER["username"];

					$query		= "UPDATE users
									SET	enabled='no', modcomment = concat("	. sqlesc( $modcomment )	. ", modcomment)
									WHERE id =	" .	$a[0];
					break;

				case "/ENABLE" :
					$what		= 'Enabled';
					$modcomment	= gmdate( "Y-m-d" )	. "	- [SHOUTBOX] User has been enabled by "	. $CURUSER["username"] . "\n" .	$a[2];
					$msg		= "[b]"	. $user	. "[/b]	- has been Enabled by "	. $CURUSER["username"];

					$query		= "UPDATE users
									SET	enabled='yes', modcomment =	concat(" . sqlesc( $modcomment ) . ", modcomment)
									WHERE	id = " . $a[0];
					break;
			}
			if ( sql_query(	$query ) )
				autoshout( $msg	);

			print "<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";

			write_log (	"[b]Shoutbox[/b] " . $user . " has been	" .	$what .	" by " . $CURUSER["username"] );

			unset ($text, $text_parsed,	$query,	$date, $modcomment,	$what, $msg, $commands);
		}
	}

/*##private	shout mode
	elseif (preg_match($private_pattern,$text,$vars))
	{
		$to_user = mysql_result(sql_query('SELECT id FROM users	WHERE username = '.sqlesc($vars[2])),0)	or exit(mysql_error());

		if ($to_user !=	0 && $to_user != $CURUSER['id'])
		{
			$text		 = $vars[2]." -	".$vars[3];
			$text_parsed = format_comment($text);

			sql_query (	"INSERT	INTO shoutbox (userid, date, text, text_parsed,to_user)
							VALUES (".sqlesc($userid).", $date,	" .	sqlesc(	$text )	. ",".sqlesc( $text_parsed)	.",".sqlesc($to_user).")") or sqlerr( __FILE__,	__LINE__ );
		}
		print "<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";
	}

	#private shout mod*/
	else {
		$a = mysql_fetch_row( sql_query( "SELECT userid, date
											FROM shoutbox ORDER by id DESC
											LIMIT 1	" )	) or print(	"First Shout or	an Error" );

		if ( empty(	$text )	|| strlen( $text ) == 1	)
			print( "<span style='font-size:	small; color : #ff0000;'>Shout can't be	empty</span>" );

		else
		{
			sql_query (	"INSERT	INTO shoutbox (id, userid, date, text)
							VALUES ('id'," .	sqlesc($userid) .	", $date, "	. sqlesc($text) .")") or sqlerr( __FILE__, __LINE__	);

			print "<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";
		}
	}
}

$res = sql_query ( "SELECT s.id, s.userid, s.date ,	s.text,	s.to_user, u.username, u.class,	u.donor, u.warned
					FROM shoutbox AS s
					LEFT JOIN users AS u ON s.userid=u.id
					ORDER BY s.date DESC
					LIMIT 30" )	or sqlerr( __FILE__, __LINE__ );

if ( mysql_num_rows( $res )	== 0 )
	print (	"No	Shouts Here	" );
else {
	print (	"<table	border='0' cellspacing='0' cellpadding='2' width='100%'	align='left' class='small'>\n" );

	while (	$arr = mysql_fetch_assoc( $res ) )
	{
		/*#private shout mod
		if (($arr['to_user'] !=	$CURUSER['id'] && $arr['to_user'] != 0)	&& $arr['userid'] != $CURUSER['id'])
			continue;

		elseif ($arr['to_user']	== $CURUSER['id'] || ($arr['userid'] ==	$CURUSER['id'] && $arr['to_user'] !=0) )
			$private = "<a href=\"javascript:private_reply('".$arr['username']."')\"><img src='{$image_dir}private-shout.png' title='Private shout!	click to reply to ".$arr['username']."'	width='16' height='16' alt='Private	shout' style='padding-left:2px;padding-right:2px;' border='0' /></a>";
		else
			$private = '';
		#private shout mod end*/

		$edit =	(get_user_class() >= UC_MODERATOR ?	"<a	href='shoutbox.php?edit=".$arr['id']."'><img src='{$image_dir}button_edit2.gif'	width='16' height='16' border='0' alt='Edit	Shout' title='Edit Shout' /></a> " : "");

		$delall	= (	get_user_class() >=	UC_SYSOP ? "<a href='shoutbox.php?delall' onclick=\"confirm_delete(); return false;	\"><img	src='{$image_dir}button_delete2.gif' width='12'	height='14'	border='0' alt='Empty Shout' title='Empty Shout' /></a>	" :	"" );

		$del = ( get_user_class() >= UC_MODERATOR ?	"<a	href='shoutbox.php?del=" . $arr['id'] .	"'><img	src='{$image_dir}del.png' width='16' height='16' border='0'	alt='Delete	Single Shout'  title='Delete Single	Shout' /></a> "	: "" );

		$pm	= "<span class='date' style=\"color:$dtcolor\"><a target='_blank' href='sendmessage.php?receiver=$arr[userid]'><img	src='{$image_dir}button_pm2.gif' width='16'	height='16'	border='0' alt='PM User'  title='PM	User' /></a></span>\n";

		//$private = (get_user_class() >= UC_MODERATOR ? "<a href=\"javascript:private_reply('".$arr['username']."')\"><img	src='{$image_dir}private-shout.png'	width='16' height='16' border='0' alt='Private Shout' title='Private Shout'	/></a>&nbsp;": "");


		$user_stuff			=	$arr;
		$user_stuff['id']	=	$arr['userid'];
		$datum				=	gmdate("d M	h:i",$arr["date"] +	($CURUSER['dst'] + $CURUSER["timezone"]) * 60);

				print("<tr $bg><td><span class='date'><font	color='red'>['$datum']</font></span>\n$delall $del $edit $pm $private $reply ".format_username($user_stuff)."\n" .

		format_comment($arr["text"])."\n</td></tr>\n");

	}
	print("</table>");
}
?>

</body>
</html>