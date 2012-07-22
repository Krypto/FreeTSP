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
require_once(INCL_DIR.'function_torrenttable.php');
require_once(INCL_DIR.'function_bbcode.php');

db_connect(false);
logged_in();

$userid	= (int)$_GET["id"];

if (!is_valid_id($userid))
	error_message("error", "Error",	"Invalid ID");

if (get_user_class()< UC_POWER_USER	|| ($CURUSER["id"] != $userid && get_user_class() <	UC_MODERATOR))
	error_message("warn", "Warning", "Permission Denied");

	$page =	(isset($_GET['page'])?$_GET["page"]:''); //	not	used?

	$action	= (isset($_GET['action'])?$_GET["action"]:'');

//-- Global variables

$perpage = 25;

//-- Action: View	posts

if ($action	== "viewposts")
{
	$select_is	= "COUNT(DISTINCT p.id)";
	$from_is	= "posts AS p
					LEFT JOIN topics AS t ON p.topicid = t.id
					LEFT JOIN forums AS f ON t.forumid = f.id";

	$where_is	= "p.userid = $userid and f.minclassread <= " . $CURUSER['class'];
	$order_is	= "p.id DESC";

	$query		= "SELECT $select_is
					FROM $from_is
					WHERE $where_is";

	$res = sql_query($query) or	sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_row($res) or	error_message("error", "Error",	"No	Posts Found");

	$postcount = $arr[0];

	//-- Make page menu

	list($pagertop,	$pagerbottom, $limit) =	pager($perpage,	$postcount,	$_SERVER["PHP_SELF"] . "?action=viewposts&id=$userid&");

	//-- Get user data

	$res = sql_query("SELECT username, donor, warned, enabled
						FROM users
						WHERE id=$userid")	or sqlerr(__FILE__,	__LINE__);

	if (mysql_num_rows($res) ==	1)
	{
	$arr = mysql_fetch_assoc($res);

		$subject = "<a href='userdetails.php?id=$userid'><span style='font-weight:bold;'>$arr[username]</span></a>"	. get_user_icons($arr, true);
	}
	else
		$subject = "unknown[$userid]";

	//-- Get posts

	$from_is = "posts as p
					LEFT JOIN topics AS t ON p.topicid = t.id
					LEFT JOIN forums AS f ON t.forumid = f.id
					LEFT JOIN readposts AS r ON p.topicid = r.topicid and p.userid = r.userid";

	$select_is = "f.id AS f_id,	f.name,	t.id as	t_id, t.subject, t.lastpost, r.lastpostread, p.*";

	$query = "SELECT $select_is
				FROM $from_is
				WHERE $where_is
				ORDER BY $order_is
				$limit";

	$res   = sql_query($query) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) ==	0) error_message("error", "Error", "No Posts Found");

	site_header("Posts History");

	echo("<h1>Post History for $subject</h1>\n");

	if ($postcount > $perpage) echo	$pagertop;

	//-- Print table

	begin_frame();

	while ($arr	= mysql_fetch_assoc($res))
	{
		$postid		= $arr["id"];
		$posterid	= $arr["userid"];
		$topicid	= $arr["t_id"];
		$topicname	= $arr["subject"];
		$forumid	= $arr["f_id"];
		$forumname	= $arr["name"];
		$dt			= (get_date_time(gmtime() - $posts_read_expiry));
		$newposts	= 0;

		if ($arr['added'] >	$dt)
			$newposts =	($arr["lastpostread"] <	$arr["lastpost"]) && $CURUSER["id"]	== $userid;

		$added = $arr["added"] . " GMT (" .	(get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

		echo("<br /><table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
		$added&nbsp;--&nbsp;<span style='font-weight:bold;'>Forum:&nbsp;</span><a href='forums.php?action=viewforum&amp;forumid=$forumid'>$forumname</a>&nbsp;--&nbsp;<span	style='font-weight:bold;'>Topic:&nbsp;</span>
		<a href='forums.php?action=viewtopic&amp;topicid=$topicid'>$topicname</a>&nbsp;--&nbsp;<span style='font-weight:bold;'>Post:&nbsp;</span>
		#<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=p$postid#$postid'>$postid</a>" . ($newposts ?	" &nbsp;<span style='font-weight:bold ;style='color	: #ff0000;'>NEW!</span>" : "") . "</td></tr></table>\n");

		begin_table(true);

		$body =	format_comment($arr["body"]);

		if (is_valid_id($arr['editedby']))
		{
			$subres	= sql_query("SELECT	username
									FROM users
									WHERE id=$arr[editedby]");

			if (mysql_num_rows($subres)	== 1)
			{
				$subrow	= mysql_fetch_assoc($subres);
				$body  .= "<p><span	style='font-size: xx-small;'>Last edited by	<a href='userdetails.php?id=$arr[editedby]'><span style='font-weight:bold;'>$subrow[username]</span></a> at	$arr[editedat] GMT</span></p>\n";
			}
		}

		echo("<tr valign='top'><td class='comment'>$body</td></tr>\n");

		end_table();
	}

	end_frame();

	if ($postcount > $perpage) echo	$pagerbottom;

	site_footer();

	die;
}

//-- Action: View	comments

if ($action	== "viewcomments")
{
	$select_is = "COUNT(*)";

	// LEFT	due	to orphan comments
	$from_is  =	"comments AS c
				LEFT JOIN torrents AS t	ON c.torrent = t.id";

	$where_is =	"c.user	= $userid";
	$order_is =	"c.id DESC";

	$query	  =	"SELECT	$select_is
					FROM $from_is
					WHERE $where_is
					ORDER BY $order_is";

	$res = sql_query($query) or	sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_row($res) or	error_message("error", "Error",	"No	Comments Found");

	$commentcount =	$arr[0];

	//-- Make page menu

	list($pagertop,	$pagerbottom, $limit) =	pager($perpage,	$commentcount, $_SERVER["PHP_SELF"]	. "?action=viewcomments&id=$userid&");

	//-- Get user data

	$res = sql_query("SELECT username, donor, warned, enabled
						FROM users
						WHERE id=$userid")	or sqlerr(__FILE__,	__LINE__);

	if (mysql_num_rows($res) ==	1)
	{
		$arr		= mysql_fetch_assoc($res);
		$subject	= "<a href='userdetails.php?id=$userid'><span style='font-weight:bold;'>$arr[username]</span></a>" .	get_user_icons($arr, true);
	}
	else
		$subject = "unknown[$userid]";

	//-- Get comments

	$select_is	= "t.name, c.torrent AS t_id, c.id, c.added, c.text";

	$query	= "SELECT $select_is
				FROM $from_is
				WHERE $where_is
				ORDER BY $order_is
				$limit";

	$res	= sql_query($query) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) ==	0) error_message("error", "Error", "No Comments	Found");

	site_header("Comments History");

	echo("<h1>Comments History for $subject</h1>\n");

	if ($commentcount >	$perpage) echo $pagertop;

	//-- Print table

	begin_frame();

	while ($arr	= mysql_fetch_assoc($res))
	{

		$commentid = $arr["id"];

		$torrent = $arr["name"];

	// make	sure the line doesn't wrap
	if (strlen($torrent) > 55) $torrent	= substr($torrent,0,52)	. "...";

	$torrentid = $arr["t_id"];

	// find the page; this code should probably be in details.php instead

	$subres	= sql_query("SELECT	COUNT(*)
							FROM comments
							WHERE torrent = $torrentid AND id < $commentid") or sqlerr(__FILE__,	__LINE__);

	$subrow		= mysql_fetch_row($subres);
	$count		= $subrow[0];
	$comm_page	= floor($count/20);
	$page_url	= $comm_page?"&page=$comm_page":"";
	$added		= $arr["added"] . " GMT (" .	(get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

	echo("<table border='0'	cellspacing='0'	cellpadding='0'><tr><td	class='embedded'>".
		"$added&nbsp;---&nbsp;<span style='font-weight:bold;'>Torrent:&nbsp;</span>".
		($torrent?("<a href='details.php?id=$torrentid&amp;tocomm=1'>$torrent</a>"):"	[Deleted] ").
		"&nbsp;---&nbsp;<span	style='font-weight:bold;'>Comment:&nbsp;</span>#<a href='details.php?id=$torrentid&amp;tocomm=1$page_url'>$commentid</a>
		</td></tr></table>\n");

		begin_table(true);

		$body	= format_comment($arr["text"]);

		echo("<tr	valign='top'><td class='comment'>$body</td></tr>\n");

		end_table();
	}

	end_frame();

	if ($commentcount >	$perpage) echo $pagerbottom;

	site_footer();

	die;
}

//-- Handle unknown action

if ($action	!= "")
	error_message("error", "History	Error",	"Unknown Action.");

//-- Any other case

error_message("error", "History	Error",	"Invalid or	No Query.");

?>