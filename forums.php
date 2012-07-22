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

/* Bleach Forums Improved and Optimized for TBDEV.NET by Alex2005 */

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_torrenttable.php');
require_once(INCL_DIR.'function_bbcode.php');

db_connect(true);
logged_in();

/* Configs Start */

/* The max class, ie: UC_CODER -  Is able to Delete, Edit the Forum etc... 	*/
define('MAX_CLASS', UC_SYSOP);

/* Set's the max file size in php.ini, no need to change */
ini_set("upload_max_filesize", $maxfilesize);

/* Set's the root path, change only if you know what you are doing */
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');

/* The extensions that are allowed to be uploaded by the users */
/* Note: you need to have the pics in the $image_dir folder, ie zip.gif, rar.gif */
$allowed_file_extensions = array('rar', 'zip');


/*  Just a check, so that the default url, wont have a ending backslash(to double backslash the links), no need to edit or delete */
$site_url_rev = strrev($site_url);
if ($site_url_rev[0] == '/')
{
	$site_url_rev[0]	= '';
	$site_url			= strrev($site_url_rev);
}

/*  Configs End */

$action = (isset($_GET["action"]) ? $_GET["action"] : (isset($_POST["action"]) ? $_POST["action"] : ''));

if (!function_exists('highlight'))
{
	function highlight($search, $subject, $hlstart = '<span style="color : #ff0000; font-weight:bold;">', $hlend = '</span>')
	{
		$srchlen = strlen($search); // length of searched string

		if ($srchlen == 0)
			return $subject;

		$find = $subject;

		while ($find = stristr($find, $search)) // find $search text in $subject -case insensitive
		{
			$srchtxt	= substr($find,0,$srchlen); // get new search text
			$find		= substr($find,$srchlen);
			$subject	= str_replace($srchtxt, $hlstart.$srchtxt.$hlend, $subject); // highlight found case insensitive search text
		}

		return $subject;
	}
}

function catch_up($id = 0)
{
	global $CURUSER, $posts_read_expiry;

	$userid = (int)$CURUSER['id'];

	$res = sql_query("SELECT t.id, t.lastpost, r.id AS r_id, r.lastpostread
						FROM topics AS t
						LEFT JOIN posts AS p ON p.id = t.lastpost
						LEFT JOIN readposts AS r ON r.userid=".sqlesc($userid)." AND r.topicid=t.id
						WHERE p.added > ".sqlesc(get_date_time(gmtime() - $posts_read_expiry)).
					(!empty($id) ? ' AND t.id '.(is_array($id) ? 'IN ('.implode(', ', $id).')' : '= '.sqlesc($id)) : '')) or sqlerr(__FILE__, __LINE__);

	while ($arr = mysql_fetch_assoc($res))
	{
		$postid = (int)$arr['lastpost'];

		if (!is_valid_id($arr['r_id']))
			sql_query("INSERT INTO readposts (userid, topicid, lastpostread)
						VALUES($userid, ".(int)$arr['id'].", $postid)") or sqlerr(__FILE__, __LINE__);

		else if ($arr['lastpostread'] < $postid)
			sql_query("UPDATE readposts
						SET lastpostread = $postid
						WHERE id = ".$arr['r_id']) or sqlerr(__FILE__, __LINE__);
	}
	mysql_free_result($res);
}

//==Begin cached online users
function forum_stats()
{
	//== Active users in Forums
	global $forum_width, $CURUSER, $image_dir, $site_url;

	$forum3 = "";
	$files  = ROOT_DIR."cache/forum.txt";
	$expire = 30; // 30 seconds

	if (file_exists($files) && filemtime($files) > (time() - $expire))
	{
		$forum3 = unserialize(file_get_contents($files));
	}
	else
	{
		$dt		= sqlesc(get_date_time(gmtime() - 180));
		$forum1 = sql_query("SELECT id, username, class, warned, donor
								FROM users
								WHERE forum_access >= $dt
								ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);

		while ($forum2 = mysql_fetch_assoc($forum1))
		{
			$forum3[] = $forum2;
		}

		$OUTPUT = serialize($forum3);
		$fp		= fopen($files, "w");

		fputs($fp, $OUTPUT);
		fclose($fp);
	} // end else

	$forumusers = "";

	if (is_array($forum3))

	foreach ($forum3 AS $arr)
	{
		if ($forumusers) $forumusers .= ",\n";
			$forumusers .= "<span style=\"white-space: nowrap;\">";
			$arr["username"] = "<span style='color:#" . get_user_class_color($arr['class']) . "'> " . htmlspecialchars($arr['username']) . "</span>";

			$donator = $arr["donor"] === "yes";
			$warned  = $arr["warned"] === "yes";

		if ($CURUSER)
			$forumusers .= "<a class='altlink_user' href='$site_url/userdetails.php?id={$arr["id"]}'><span style='font-weight:bold;'>{$arr["username"]}</span></a>";
		else
			$forumusers .= "<span style='font-weight:bold;'>{$arr["username"]}</span>";

		if ($donator)
			$forumusers .= "<img src='".$image_dir."star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />";

		if ($warned)
			$forumusers .= "<img src='".$image_dir."warned.png' width='15' height='16' border='0' alt='Warned' title='Warned' />";

		$forumusers .= "</span>";
	}

	if (!$forumusers)
		$forumusers = "There are currently No Active Members in the Forum";

	$topic_post_res = sql_query("SELECT SUM(topiccount) AS topics, SUM(postcount) AS posts
									FROM forums");

	$topic_post_arr = mysql_fetch_assoc($topic_post_res);

	?>

	<br />
	<table width='<?php echo $forum_width; ?>' border='0' cellspacing='0' cellpadding='5'>
		<tr>
			<td class='colhead' align='center'>Now Active in Forums:</td>
		</tr>

		<tr>
			<td class='text'><?php echo $forumusers; ?></td>
		</tr>

		<tr>
			<td class='colhead' align='center'><h2>Our Members Wrote <span style='font-weight:bold;'><?php echo number_format($topic_post_arr['posts']); ?></span> Posts in <span style='font-weight:bold;'><?php echo number_format($topic_post_arr['topics']); ?></span> Threads</h2></td>
	</tr>
	</table><?php
}

function show_forums($forid)
{
	global $CURUSER, $image_dir, $posts_read_expiry, $site_url;

	$forums_res = sql_query("SELECT f.id, f.name, f.description, f.postcount, f.topiccount, f.minclassread, p.added, p.topicid, p.userid, p.id AS pid, u.username, t.subject, t.lastpost, r.lastpostread
								FROM forums AS f
								LEFT JOIN posts AS p ON p.id = (SELECT MAX(lastpost) FROM topics WHERE forumid = f.id)
								LEFT JOIN users AS u ON u.id = p.userid
								LEFT JOIN topics AS t ON t.id = p.topicid
								LEFT JOIN readposts AS r ON r.userid = ".sqlesc($CURUSER['id'])." AND r.topicid = p.topicid
								WHERE f.forid = $forid
								ORDER BY sort ASC") or sqlerr(__FILE__, __LINE__);

	while ($forums_arr = mysql_fetch_assoc($forums_res))
	{
		if ($CURUSER['class'] < $forums_arr["minclassread"])
			continue;

		$forumid	= (int)$forums_arr["id"];
		$lastpostid = (int)$forums_arr['lastpost'];

		if (is_valid_id($forums_arr['pid']))
		{
			$lastpost = "<div style='white-space: nowrap;'>".$forums_arr["added"]."<br />" .
						"by <a class='altlink_user' href='$site_url/userdetails.php?id=".(int)$forums_arr["userid"]."'><span style='font-weight:bold;'>".htmlspecialchars($forums_arr['username'])."</span></a><br />" .
						"in <a href='forums.php?action=viewtopic&amp;topicid=".(int)$forums_arr["topicid"]."&amp;page=p$lastpostid#$lastpostid'><span style='font-weight:bold;'>".htmlspecialchars($forums_arr['subject'])."</span></a></div>";

			$img = 'unlocked'.((($forums_arr['added']>(get_date_time(gmtime()-$posts_read_expiry)))?((int)$forums_arr['pid'] > $forums_arr['lastpostread']):0)?'new':'');
		}
		else
		{
			$lastpost	= "N/A";
			$img		= "unlocked";
		}

		?><tr>
			<td align='left'>
				<table border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='embedded' style='padding-right: 5px'><img src="<?php echo $image_dir.$img; ?>.png" width='32' height='32' border='0' alt='No New Posts' title='No New Posts' /></td>
						<td class='embedded'>
							<a href='forums.php?action=viewforum&amp;forumid=<?php echo $forumid; ?>'><span style='font-weight:bold;'><?php echo htmlspecialchars($forums_arr["name"]); ?></span></a><?php
						if ($CURUSER['class'] >= UC_ADMINISTRATOR)
						{
							?>&nbsp;<span style='font-size: xx-small;'>[<a class='altlink' href='forums.php?action=editforum&amp;forumid=<?php echo $forumid; ?>'>Edit</a>][<a class='altlink' href='forums.php?action=deleteforum&amp;forumid=<?php echo $forumid; ?>'>Delete</a>]</span><?php
						}

						if (!empty($forums_arr["description"]))
						{
							?><br /><?php echo htmlspecialchars($forums_arr["description"]);
						}
						?></td>
					</tr>
				</table>
			</td>
			<td align='center'><?php echo number_format($forums_arr["topiccount"]); ?></td>
			<td align='center'><?php echo number_format($forums_arr["postcount"]); ?></td>
			<td align='left'><?php echo $lastpost; ?></td>
		</tr><?php
	}
}

//--Returns the Minimum Read/Write Class Levels of a Forum
function get_forum_access_levels($forumid)
{
	global $CURUSER, $site_url, $image_dir;
	$res = sql_query("SELECT minclassread, minclasswrite, minclasscreate
						FROM forums
						WHERE id = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) != 1)
		return false;

	$arr = mysql_fetch_assoc($res);

	return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"], "create" => $arr["minclasscreate"]);
}

//-- Returns the Forum ID of a Topic, or false on Error
function get_topic_forum($topicid)
{
	global $CURUSER, $image_dir, $posts_read_expiry, $site_url;
	$res = sql_query("SELECT forumid
						FROM topics
						WHERE id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) != 1)
		return false;

	$arr = mysql_fetch_assoc($res);

	return (int)$arr['forumid'];
}

//-- Returns the ID of the Last Post of a Forum
function update_topic_last_post($topicid)
{
	$res = sql_query("SELECT MAX(id) AS id
						FROM posts
						WHERE topicid = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res) or die("No Post Found!");

	sql_query("UPDATE topics
				SET lastpost = {$arr['id']}
				WHERE id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

//-- update Forum Post/Topic Count
	$forums = sql_query("SELECT id
							FROM forums");

	while ($forum = mysql_fetch_assoc($forums))
	{
		$postcount  = 0;
		$topiccount = 0;
		$topics		= sql_query("SELECT id
									FROM topics
									WHERE forumid=$forum[id]");

		while ($topic = mysql_fetch_assoc($topics))
		{
			$res = sql_query("SELECT COUNT(*)
								FROM posts
								WHERE topicid=$topic[id]");

			$arr = mysql_fetch_row($res);

			$postcount += $arr[0];
			++$topiccount;
		}
		sql_query("UPDATE forums
					SET postcount=$postcount, topiccount=$topiccount
					WHERE id=$forum[id]");
	}
}

function get_forum_last_post($forumid)
{
	$res = sql_query("SELECT MAX(lastpost) AS lastpost
						FROM topics
						WHERE forumid = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res);

	$postid = (int)$arr['lastpost'];

	return (is_valid_id($postid) ? $postid : 0);
}

//-- Inserts a Quick Jump Menu
function insert_quick_jump_menu($currentforum = 0)
{
	global $CURUSER;

	?>
	<div style='text-align:center;'>
		<form method='get' action='forums.php' name='jump'>
			<input type="hidden" name="action" value="viewforum" />
				<?php print("<span style='font-weight:bold;'>Quick jump:</span>"); ?>
				<select name='forumid' onchange="if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }">
				<?php
	$res = sql_query("SELECT id, name, minclassread
						FROM forums
						ORDER BY name") or sqlerr(__FILE__, __LINE__);

	while ($arr = mysql_fetch_assoc($res))
		if ($CURUSER['class'] >= $arr["minclassread"])
			echo "<option value='".$arr["id"].($currentforum == $arr["id"] ? "' selected='selected'" : "'").'>'.$arr["name"]."</option>";
	?>
				</select>
			<input type='submit' class='btn' value='Go!' />
		</form>
	</div>
	<br />
	<?php
}

//-- Inserts a Compose Frame
function insert_compose_frame($id, $newtopic = true, $quote = false, $attachment = false)
{
	global $maxsubjectlength, $CURUSER, $maxfilesize, $image_dir, $use_attachment_mod, $site_url, $image_dir;

	if ($newtopic)
	{
		$res = sql_query("SELECT name
							FROM forums
							WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

		$arr = mysql_fetch_assoc($res) or die("Bad Forum ID!");

		?><h3>New Topic in <a href='forums.php?action=viewforum&amp;forumid=<?php echo $id; ?>'><?php echo htmlspecialchars($arr["name"]); ?></a> forum</h3><?php
	}
	else
	{
		$res = sql_query("SELECT t.forumid, t.subject, t.locked, f.minclassread
							FROM topics AS t
							LEFT JOIN forums AS f ON f.id = t.forumid
							WHERE t.id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

		$arr = mysql_fetch_assoc($res) or die("Forum Error, Topic Not Found!");

		if($CURUSER['class'] < $arr['minclassread']){
			error_message("warn", "Sorry", "You are Not allowed in here!");
			end_table();
		exit();

		}

		if ($arr['locked'] == 'yes' && $CURUSER['class'] < UC_MODERATOR)
		{
			error_message("warn", "Sorry", "The Topic is Locked!");
			end_table();
			exit();
		}
		?><h3 align="center">Reply to Topic: <a href='forums.php?action=viewtopic&amp;topicid=<?php echo $id; ?>'><?php echo htmlspecialchars($arr["subject"]); ?></a></h3><?php
	}

	if ($quote)
	{
		$postid = (int)$_GET["postid"];

		if (!is_valid_id($postid))
		{
			error_message("error", "Error", "Invalid ID!");
		}

		$res = sql_query("SELECT posts.*, users.username
							FROM posts
							JOIN users ON posts.userid = users.id
							WHERE posts.id = $postid") or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) == 0)
		{
			error_message("error","Error", "No Post with that ID!");
		}

		$arr = mysql_fetch_assoc($res);
	}
	begin_frame("Compose", true);

	?><form method='post' name='compose' action='forums.php' enctype='multipart/form-data'>
		<input type='hidden' name='action' value='post' />
		<input type='hidden' name='<?php echo ($newtopic ? 'forumid' : 'topicid'); ?>' value='<?php echo $id; ?>' />
	<?php

	begin_table(true);

	if ($newtopic)
	{
		?>
		<tr>
			<td class='rowhead' width='10%'>Subject</td>
			<td align='left'>
				<input type='text' name='subject' size='100' maxlength='<?php echo $maxsubjectlength; ?>' style='border: 0px; height: 19px' />
			</td>
		</tr>
		<?php
	}

	?><tr>
		<td class='rowhead' width='10%'>Body</td>
		<td class='rowhead'>
	<?php
		$qbody = ($quote ? "[quote=".htmlspecialchars($arr["username"])."]".htmlspecialchars(unesc($arr["body"]))."[/quote]" : '');
		if (function_exists('textbbcode'))
			echo("".textbbcode("compose", "body", $qbody) . "");
		else
		{
			?><textarea name='body' style='width:99%' rows='7' cols='5'><?php echo $qbody; ?></textarea><?php
		}
		echo("</td></tr>");
		if ($use_attachment_mod && $attachment)
		{
			?><tr>
				<td colspan='2'><fieldset class='fieldset'><legend>Add Attachment</legend>
					<input type='checkbox' name='uploadattachment' value='yes' />
					<input type='file' name='file' size='60' />
					<div class='error'>Allowed Files: rar, zip<br />Size Limit <?php echo mksize($maxfilesize); ?></div></fieldset>
				</td>
			</tr>
			<?php
		}

		?><tr>
			<td colspan='2' align='center'>
				<input type='submit' class='btn' value='Submit' />
			</td>
		</tr>

		<?php

		end_table();

		?></form>

		<p align='center'><a href='<?php echo $site_url; ?>/smilies.php' target='_blank'>Smilies</a></p><?php

		end_frame();

		//-- Get Last 10 Posts if this is a Reply
		if (!$newtopic)
		{
			$postres = sql_query("SELECT p.id, p.added, p.body, u.id AS uid, u.username, u.avatar
									FROM posts AS p
									LEFT JOIN users AS u ON u.id = p.userid
									WHERE p.topicid = ".sqlesc($id)."
									ORDER BY p.id DESC
									LIMIT 10") or sqlerr(__FILE__, __LINE__);

			if (mysql_num_rows($postres) > 0)
			{
				?><br /><?php

				begin_frame("Last 10 Posts, in Reverse Order");

				while ($post = mysql_fetch_assoc($postres))
				{
					$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($post["avatar"]) : '');

					if (empty($avatar))
						$avatar = $image_dir."default_avatar.gif";

					?><p class='sub'>#<?php echo $post["id"]; ?> by <?php echo (!empty($post["username"]) ? $post["username"] : "unknown[{$post['uid']}]"); ?> at <?php echo $post["added"]; ?> GMT</p><?php

					begin_table(true);

					?>
					<tr>
						<td class='rowhead' height='100' width='100' align='center' style='padding: 0px' valign="top"><img src="<?php echo $avatar; ?>" width='125' height='125' border='0' alt='Avatar' title='' /></td>
						<td class='comment' valign='top'><?php echo format_comment($post["body"]); ?></td>
					</tr>
					<?php

					end_table();
				}

				end_frame();
			}
		}

		insert_quick_jump_menu();
}

if ($action == 'updatetopic' && $CURUSER['class'] >= UC_MODERATOR)
{
	$topicid = (isset($_GET['topicid']) ? (int)$_GET['topicid'] : (isset($_POST['topicid']) ? (int)$_POST['topicid'] : 0));

	if (!is_valid_id($topicid))
		error_message("error", "Error", "Invalid Topic ID!");

	$topic_res = sql_query('SELECT t.sticky, t.locked, t.subject, t.forumid, f.minclasswrite,
							(SELECT COUNT(id) FROM posts WHERE topicid = t.id) AS post_count
							FROM topics AS t
							LEFT JOIN forums AS f ON f.id = t.forumid
							WHERE t.id = '.sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($topic_res) == 0)
		error_message("error", "Error", "No Topic with that ID!");

	$topic_arr = mysql_fetch_assoc($topic_res);

	if ($CURUSER['class'] < (int)$topic_arr['minclasswrite'])
		error_message("error", "Error", "You are Not Allowed to Edit this Topic.");

	$forumid = (int)$topic_arr['forumid'];
	$subject = $topic_arr['subject'];

	if ((isset($_GET['delete']) ? $_GET['delete'] : (isset($_POST['delete']) ? $_POST['delete'] : '')) == 'yes')
	{
		if ((isset($_GET['sure']) ? $_GET['sure'] : (isset($_POST['sure']) ? $_POST['sure'] : '')) != 'yes')
			error_message("error", "Sanity Check", "<a href='forums.php?action=$action&amp;topicid=$topicid&amp;delete=yes&amp;sure=yes'>You are about to Delete this Topic.  Click here to Confirm!</a>");

		write_log("Topic <span style='font-weight:bold;'>".$subject."</span> was deleted by <a class='altlink_user' href='$site_url/userdetails.php?id=".$CURUSER['id']."'>".$CURUSER['username']."</a>.");

		if ($use_attachment_mod)
		{
			$res = sql_query("SELECT attachments.filename
								FROM posts
								LEFT JOIN attachments ON attachments.postid = posts.id
								WHERE posts.topicid = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

			while ($arr = mysql_fetch_assoc($res))
				if (!empty($arr['filename']) && is_file($attachment_dir."/".$arr['filename']))
					unlink($attachment_dir."/".$arr['filename']);
		}

		sql_query("DELETE posts, topics ".
					($use_attachment_mod ? ", attachments, attachmentdownloads " : "").
					($use_poll_mod ? ", postpolls, postpollanswers " : "").
					"FROM topics ".
					"LEFT JOIN posts ON posts.topicid = topics.id ".
					($use_attachment_mod ? "LEFT JOIN attachments ON attachments.postid = posts.id ".
					"LEFT JOIN attachmentdownloads ON attachmentdownloads.fileid = attachments.id " : "").
					($use_poll_mod ? "LEFT JOIN postpolls ON postpolls.id = topics.pollid ".
					"LEFT JOIN postpollanswers ON postpollanswers.pollid = postpolls.id " : "").
					"WHERE topics.id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

		header('Location: '.$_SERVER['PHP_SELF'].'?action=viewforum&forumid='.$forumid);
		exit();
	}

	$returnto	= $_SERVER['PHP_SELF'].'?action=viewtopic&topicid='.$topicid;
	$updateset	= array();
	$locked		= ($_POST['locked'] == 'yes' ? 'yes' : 'no');

	if ($locked != $topic_arr['locked'])
		$updateset[] = 'locked = '.sqlesc($locked);

	$sticky = ($_POST['sticky'] == 'yes' ? 'yes' : 'no');

	if ($sticky != $topic_arr['sticky'])
		$updateset[] = 'sticky = '.sqlesc($sticky);

	$new_subject = $_POST['subject'];

	if ($new_subject != $subject)
	{
		if (empty($new_subject))
		  error_message("error", "Error", "Topic Name Cannot be Empty.");

		$updateset[] = 'subject = '.sqlesc($new_subject);
	}
	$new_forumid = (int)$_POST['new_forumid'];

	if (!is_valid_id($new_forumid))
		error_message("error", "Error", "Invalid Forum ID!");

	if ($new_forumid != $forumid)
	{
		$post_count = (int)$topic_arr['post_count'];

		$res = sql_query("SELECT minclasswrite
							FROM forums
							WHERE id = ".sqlesc($new_forumid)) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) != 1)
			error_message("error", "Error", "Forum Not Found!");

		$arr = mysql_fetch_assoc($res);

		if ($CURUSER['class'] < (int)$arr['minclasswrite'])
			error_message("error", "Error", "You are Not Allowed to Move this Topic into the Selected Forum.");

		$updateset[] = 'forumid = '.sqlesc($new_forumid);

		sql_query("UPDATE forums
					SET topiccount = topiccount - 1, postcount = postcount - ".sqlesc($post_count)."
					WHERE id = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

		sql_query("UPDATE forums
					SET topiccount = topiccount + 1, postcount = postcount + ".sqlesc($post_count)."
					WHERE id = ".sqlesc($new_forumid)) or sqlerr(__FILE__, __LINE__);

		$returnto = $_SERVER['PHP_SELF'].'?action=viewforum&forumid='.$new_forumid;
	}

	if (sizeof($updateset) > 0)
		sql_query("UPDATE topics
					SET ".implode(', ', $updateset)."
					WHERE id = ".sqlesc($topicid));

	header('Location: '.$returnto);
	exit();
}
else if ($action == "editforum" && $CURUSER['class'] == MAX_CLASS) //-- Action: Edit Forum
{
	$forumid = (int)$_GET["forumid"];

	if (!is_valid_id($forumid))
		error_message("error", "Error", "Invalid ID!");

	$res = sql_query("SELECT name, description, minclassread, minclasswrite, minclasscreate
						FROM forums
						WHERE id = $forumid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
		error_message("error", "Error", "No Forum Found with that ID!");

	$forum = mysql_fetch_assoc($res);

	site_header("Edit Forum");
	begin_frame("Edit Forum", "center");

	print("<form method='post' action='forums.php?action=updateforum&amp;forumid=$forumid'>\n");

	begin_table();

	print("<tr>
			<td class='rowhead'>Forum name</td>
			<td align='left' style='padding: 0px'>
				<input type='text' name='name' size='60' maxlength='$maxsubjectlength' value=\"".htmlspecialchars($forum['name'])."\" style='border: 0px; height: 19px'  />
			</td>
		</tr>\n
		<tr>
			<td class='rowhead'>Description</td>
			<td align='left' style='padding: 0px'><textarea name='description' cols='68' rows='3' style='border: 0px'>".htmlspecialchars($forum['description'])."</textarea>
			</td>
		</tr>\n
		<tr>
			<td class='rowhead'></td>
			<td align='left' style='padding: 0px'>&nbsp;Minimum <select name='readclass'>");

	for ($i = 0; $i <= MAX_CLASS; ++$i)
		print("<option value='$i'" . ($i == $forum['minclassread'] ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n");

		print("</select> Class Required to View<br />\n&nbsp;Minimum <select name='writeclass'>");

	for ($i = 0; $i <= MAX_CLASS; ++$i)
		print("<option value='$i'" . ($i == $forum['minclasswrite'] ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n");

		print("</select> Class Required to Post<br />\n&nbsp;Minimum <select name='createclass'>");

	for ($i = 0; $i <= MAX_CLASS; ++$i)

		print("<option value='$i'" . ($i == $forum['minclasscreate'] ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n");

		print("</select> Class Required to Create Topics</td></tr>\n".
		"<tr><td colspan='2' align='center'><input type='submit' class='btn' value='Submit' /></td></tr>\n");

	end_table();

	print("</form><br />");

	end_frame();
	site_footer();
	exit();
}
else if ($action == "updateforum" && $CURUSER['class'] == MAX_CLASS) //-- Action: Update Forum
{
	$forumid = (int)$_GET["forumid"];

	if (!is_valid_id($forumid))
		error_message('error', 'Error', 'Invalid ID!');

	$res = sql_query('SELECT id
						FROM forums
						WHERE id = '.sqlesc($forumid));

	if (mysql_num_rows($res) == 0)
		error_message('error', 'Error', 'No Forum with that ID!');

	$name			= $_POST['name'];
	$description	= $_POST['description'];

	if (empty($name))
		error_message('error', 'Error', 'You Must Specify a Name for the Forum.');

	if (empty($description))
		error_message('error', 'Error', 'You Must Provide a Description for the Forum.');

	sql_query("UPDATE forums
				SET name = ".sqlesc($name).", description = ".sqlesc($description).", minclassread = ".sqlesc((int)$_POST['readclass']).", minclasswrite = ".sqlesc((int)$_POST['writeclass']).", minclasscreate = ".sqlesc((int)$_POST['createclass'])."
				WHERE id = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

	header("Location: {$_SERVER['PHP_SELF']}");
	exit();
}
else if ($action == 'deleteforum' && $CURUSER['class'] == MAX_CLASS) //-- Action: Delete Forum
{
	$forumid = (int)$_GET['forumid'];

	if (!is_valid_id($forumid))
		error_message('error','Error', 'Invalid ID!');

	$confirmed = (int)$_GET['confirmed'];

	if (!$confirmed)
	{
		$rt = sql_query("SELECT topics.id, forums.name
							FROM topics
							LEFT JOIN forums ON forums.id=topics.forumid
							WHERE topics.forumid = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

		$topics = mysql_num_rows($rt);
		$posts  = 0;

		if ($topics > 0)
		{
			while ($topic = mysql_fetch_assoc($rt))
			{
				$ids[] = $topic['id'];
				$forum = $topic['name'];
			}

			$rp = sql_query("SELECT COUNT(id)
								FROM posts
								WHERE topicid IN (".join(', ', $ids).")");

			foreach ($ids AS $id)

			if ($a = mysql_fetch_row($rp))
				$posts += $a[0];
		}

		if ($use_attachment_mod || $use_poll_mod)
		{
			$res = sql_query("SELECT ".
							($use_attachment_mod ? "COUNT(attachments.id) AS attachments " : "").
							($use_poll_mod ? ($use_attachment_mod ? ', ' : '')."COUNT(postpolls.id) AS polls " : "").
							"FROM topics ".
							"LEFT JOIN posts ON topics.id=posts.topicid ".
							($use_attachment_mod ? "LEFT JOIN attachments ON attachments.postid = posts.id " : "").
							($use_poll_mod ? "LEFT JOIN postpolls ON postpolls.id=topics.pollid " : "").
							"WHERE topics.forumid=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

			($use_attachment_mod ? $attachments = 0 : NULL);
			($use_poll_mod ? $polls = 0 : NULL);

			if ($arr = mysql_fetch_assoc($res))
			{
				($use_attachment_mod ? $attachments = $arr['attachments'] : NULL);
				($use_poll_mod ? $polls = $arr['polls'] : NULL);
			}
		}
		error_message("warn", "** WARNING! **", "Deleting this Forum with id=$forumid (".$forum.") will also Delete ".$posts." Post".($posts != 1 ? 's' : '').($use_attachment_mod ? ", ".$attachments." attachment".($attachments != 1 ? 's' : '') : "").($use_poll_mod ? " AND ".($polls-$attachments)." poll".(($polls-$attachments) != 1 ? 's' : '') : "")." in ".$topics." topic".($topics != 1 ? 's' : '').". [<a href='forums.php?action=deleteforum&amp;forumid=$forumid&amp;confirmed=1'>ACCEPT</a>] [<a href='forums.php?action=viewforum&amp;forumid=$forumid'>CANCEL</a>]");
	}

	$rt = sql_query("SELECT topics.id ".($use_attachment_mod ? ", attachments.filename " : "").
					"FROM topics ".
					"LEFT JOIN posts ON topics.id = posts.topicid ".
					($use_attachment_mod ? "LEFT JOIN attachments ON attachments.postid = posts.id " : "").
					"WHERE topics.forumid = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

	$topics = mysql_num_rows($rt);

	if ($topics == 0)
	{
		sql_query("DELETE
					FROM forums
					WHERE id = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

		header("Location: {$_SERVER['PHP_SELF']}");
		exit();
	} else

	while ($topic = mysql_fetch_assoc($rt))
	{
		$tids[] = $topic['id'];

		if ($use_attachment_mod && !empty($topic['filename']))
		{
			$filename = $attachment_dir."/".$topic['filename'];

			if (is_file($filename))
			unlink($filename);
		}
	}

	sql_query("DELETE posts.*, topics.*, forums.* ".($use_attachment_mod ? ", attachments.*, attachmentdownloads.* " : "").($use_poll_mod ? ", postpolls.*, postpollanswers.* " : "").
				"FROM posts ".
				($use_attachment_mod ? "LEFT JOIN attachments ON attachments.postid = posts.id ".
				"LEFT JOIN attachmentdownloads ON attachmentdownloads.fileid = attachments.id " : "").
				"LEFT JOIN topics ON topics.id = posts.topicid ".
				"LEFT JOIN forums ON forums.id = topics.forumid ".
				($use_poll_mod ? "LEFT JOIN postpolls ON postpolls.id = topics.pollid ".
				"LEFT JOIN postpollanswers ON postpollanswers.pollid = postpolls.id " : "").
				"WHERE posts.topicid IN (".join(', ', $tids).")") or sqlerr(__FILE__, __LINE__);

	header("Location: {$_SERVER['PHP_SELF']}");
	exit();
}
else if ($action == "newtopic") //-- Action: New topic
{
	$forumid = (int)$_GET["forumid"];

	if (!is_valid_id($forumid))
		error_message("error", "Error", "Invalid ID!");

	site_header("New Topic");
	insert_compose_frame($forumid, true, false, true);
	site_footer();
	exit();
}
else if ($action == "post") //-- Action: Post
{
	$forumid = (isset($_POST['forumid']) ? (int)$_POST['forumid'] : NULL);

	if (isset($forumid) && !is_valid_id($forumid))
		error_message("error", "Error", "Invalid Forum ID!");

	$topicid = (isset($_POST['topicid']) ? (int)$_POST['topicid'] : NULL);

	if (isset($topicid) && !is_valid_id($topicid))
		error_message("error", "Error", "Invalid Topic ID!");

	$newtopic = is_valid_id($forumid);

	$subject = (isset($_POST["subject"]) ? $_POST["subject"] : '');

	if ($newtopic)
	{
		$subject = trim($subject);

		if (empty($subject))
			error_message("error", "Error", "You must Enter a Subject.");

		if (strlen($subject) > $maxsubjectlength)
			error_message("error", "Error", "Subject is limited to ".$maxsubjectlength." characters.");
	}
	else
		$forumid = get_topic_forum($topicid) or die("Bad topic ID");

	if ($CURUSER["forumpost"] == 'no')
		error_message("warn", "Sorry", "Your are not Allowed to Post.)");

	//-- Make sure sure user has write access in forum
	$arr = get_forum_access_levels($forumid) or die("Bad Forum ID");

	if ($CURUSER['class'] < $arr["write"] || ($newtopic && $CURUSER['class'] < $arr["create"]))
		error_message("warn", "Warning", "Permission Denied.");

	$body = trim($_POST["body"]);

	if (empty($body))
		error_message("error", "Error", "No Body Text.");

	if (substr_count( strtolower($body), '[quote' ) > 3 )
	{
		error_message("info", "Sorry", "Quote Limit Reached");
	}

	$userid = (int)$CURUSER["id"];

	if ($use_flood_mod && $CURUSER['class'] < UC_MODERATOR)
	{
		$res = sql_query("SELECT COUNT(id) AS c
							FROM posts
							WHERE userid = ".$CURUSER['id']."
							AND added > '".get_date_time(gmtime() - ($minutes * 60))."'");

		$arr = mysql_fetch_assoc($res);

		if ($arr['c'] > $limit)
			error_message("info", "Flood", "More than ".$limit." Posts in the last ".$minutes." Minutes.");
	}

	if ($newtopic)
	{
		sql_query("INSERT INTO topics (userid, forumid, subject)
					VALUES($userid, $forumid, ".sqlesc($subject).")") or sqlerr(__FILE__, __LINE__);

		$topicid = mysql_insert_id() or error_message("error", "Error", "No Topic ID Returned!");

		sql_query("INSERT INTO posts (topicid, userid, added, body)
					VALUES($topicid, $userid, ".sqlesc(get_date_time()).", ".sqlesc($body).")") or sqlerr(__FILE__, __LINE__);

		$postid = mysql_insert_id() or error_message("error", "Error", "No Post ID Returned!");
	}
	else
	{
		//-- Make sure topic exists and is unlocked
		$res = sql_query("SELECT locked
							FROM topics
							WHERE id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) == 0)
			error_message("error", "Error", "Inexistent Topic!");

		$arr = mysql_fetch_assoc($res);

		if ($arr["locked"] == 'yes' && $CURUSER['class'] < UC_MODERATOR)
			error_message("info", "", "This Topic is Locked.  No New Posts are Allowed.");

		//-- Check double post
		$doublepost = sql_query("SELECT p.id, p.added, p.userid, p.body, t.lastpost, t.id
									FROM posts AS p
									INNER JOIN topics AS t ON p.id = t.lastpost
									WHERE t.id = $topicid
									AND p.userid = $userid
									AND p.added > ".sqlesc(get_date_time(gmtime() - 1*86400))."
									ORDER BY p.added DESC
									LIMIT 1") or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($doublepost) == 0 || $CURUSER['class'] >= UC_MODERATOR)
		{
			sql_query("INSERT INTO posts (topicid, userid, added, body)
						VALUES($topicid, $userid, ".sqlesc(get_date_time()).", ".sqlesc($body).")") or sqlerr(__FILE__, __LINE__);

			$postid = mysql_insert_id() or die("Post ID N/A");
		}
		else
		{
			$results = mysql_fetch_assoc($doublepost);
			$postid  = (int)$results['lastpost'];

			sql_query("UPDATE posts
						SET body = ".sqlesc(trim($results['body'])."\n\n".$body).", editedat = ".sqlesc(get_date_time()).", editedby = $userid
						WHERE id=$postid") or sqlerr(__FILE__, __LINE__);
		}
	}

	update_topic_last_post($topicid);

	if ($use_attachment_mod && ((isset($_POST['uploadattachment']) ? $_POST['uploadattachment'] : '') == 'yes'))
	{
		$file			= $_FILES['file'];
		$fname			= trim(stripslashes($file['name']));
		$size			= $file['size'];
		$tmpname		= $file['tmp_name'];
		$tgtfile		= $attachment_dir."/".$fname;
		$pp				= pathinfo($fname = $file['name']);
		$error			= $file['error'];
		$type			= $file['type'];
		$uploaderror	= '';

		if (empty($fname))
			$uploaderror = "Invalid Filename!";

		if (!validfilename($fname))
			$uploaderror = "Invalid Filename!";

		foreach ($allowed_file_extensions AS $allowed_file_extension);

			if (!preg_match('/^(.+)\.['.join(']|[', $allowed_file_extensions).']$/si', $fname, $matches))
				$uploaderror = 'Only files with the following extensions are allowed: '.join(', ', $allowed_file_extensions).'.';

		if ($size > $maxfilesize)
			$uploaderror = error_message("warn", "Sorry", "that file is too large.");

		if ($pp['basename'] != $fname)
			$uploaderror = error_message("warn", "Sorry", "Bad file name.");

		if (file_exists($tgtfile))
			$uploaderror = error_message("warn", "Sorry", "a file with the name already exists.");

		if (!is_uploaded_file($tmpname))
			$uploaderror = error_message("warn", "Sorry", "Can't upload that file!");

		if (!filesize($tmpname))
			$uploaderror = error_message("warn", "Sorry", "File Empty!)");

		if ($error != 0)
			$uploaderror = error_message("error", "Sorry", "There was an error while uploading that file.");

		if (empty($uploaderror))
		{
			sql_query("INSERT INTO attachments (topicid, postid, filename, size, owner, added, type)
						VALUES ('$topicid','$postid',".sqlesc($fname).", ".sqlesc($size).", '$userid', ".sqlesc(get_date_time()).", ".sqlesc($type).")") or sqlerr(__FILE__, __LINE__);

			move_uploaded_file($tmpname, $tgtfile);
		}
	}

	$headerstr = "Location: forums.php?action=viewtopic&topicid=$topicid".($use_attachment_mod && !empty($uploaderror) ? "&uploaderror=$uploaderror" : "")."&page=last";

	header($headerstr.($newtopic ? '' : "#$postid"));
	exit();
}
else if ($action == "viewtopic") //-- Action: View topic
{
	$userid = (int)$CURUSER["id"];

	if ($use_poll_mod && $_SERVER['REQUEST_METHOD'] == "POST")
	{
		$choice = $_POST['choice'];
		$pollid = (int)$_POST["pollid"];

		if (ctype_digit($choice) && $choice < 256 && $choice == floor($choice))
		{
			$res = sql_query("SELECT pa.id
								FROM postpolls AS p
								LEFT JOIN postpollanswers AS pa ON pa.pollid = p.id AND pa.userid = ".sqlesc($userid)."
								WHERE p.id = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);

			$arr = mysql_fetch_assoc($res) or error_message("error", "Sorry", "Inexistent Poll!");

			if (is_valid_id($arr['id']))
				error_message("error", "Error", "Dupe Vote");

			sql_query("INSERT INTO postpollanswers
						VALUES(id, ".sqlesc($pollid).", ".sqlesc($userid).", ".sqlesc($choice).")") or sqlerr(__FILE__, __LINE__);

			if (mysql_affected_rows() != 1)
				error_message("error", "Error", "An Error Occured. Your Vote has NOT been Counted.");
		}
		else
			error_message("error", "Error", "Please Select an Option." );
	}

	$topicid = (int)$_GET["topicid"];

	if (!is_valid_id($topicid))
		error_message("error", "Error", "Invalid Topic ID!2");

	$page = (isset($_GET["page"]) ? $_GET["page"] : 0);

	//-- Get topic info
	$res = sql_query("SELECT ".($use_poll_mod ? 't.pollid, ' : '')."t.locked, t.subject, t.sticky, t.userid AS t_userid, t.forumid, f.name AS forum_name, f.minclassread, f.minclasswrite, f.minclasscreate, (SELECT COUNT(id) FROM posts WHERE topicid = t.id) AS p_count
						FROM topics AS t
						LEFT JOIN forums AS f ON f.id = t.forumid
						WHERE t.id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res) or error_message("error", "Error", "Topic NOT Found");
	mysql_free_result($res);

	($use_poll_mod ? $pollid = (int)$arr["pollid"] : NULL);

	$t_userid	= (int)$arr['t_userid'];
	$locked		= ($arr['locked'] == 'yes' ? true : false);
	$subject	= $arr['subject'];
	$sticky		= ($arr['sticky'] == "yes" ? true : false);
	$forumid	= (int)$arr['forumid'];
	$forum		= $arr["forum_name"];
	$postcount	= (int)$arr['p_count'];

	if ($CURUSER["class"] < $arr["minclassread"])
		error_message("warn", "Warning", "You are NOT Permitted to View this Topic.");

	//-- Update hits column
	sql_query("UPDATE topics
				SET views = views + 1
				WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

	//-- Make page menu
	$pagemenu1	= "<p align='center'>";
	$perpage	= $postsperpage;
	$pages		= ceil($postcount / $perpage);

	if ($page[0] == "p")
	{
		$findpost = substr($page, 1);

		$res = sql_query("SELECT id
							FROM posts
							WHERE topicid=$topicid
							ORDER BY added") or sqlerr(__FILE__, __LINE__);

		$i = 1;

		while ($arr = mysql_fetch_row($res))
		{
			if ($arr[0] == $findpost)
				break;
			++$i;
		}
		$page = ceil($i / $perpage);
	}

	if ($page == "last")
		$page = $pages;
	else
	{
		if ($page < 1)
			$page = 1;
		else if ($page > $pages)
			$page = $pages;
	}

	$offset		= ((int)$page * $perpage) - $perpage;
	$offset		= ($offset < 0 ? 0 : $offset);
	$pagemenu2	= '';

	for ($i = 1; $i <= $pages; ++$i)
		$pagemenu2 .= ($i == $page ? "[<span style='text-decoration: underline; font-weight:bold;'>$i</span>]" : "<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=$i'><span style='font-weight:bold;'>$i</span></a>");

	$pagemenu1 .= ($page == 1 ? "<span style='font-weight:bold;'>&lt;&lt;&nbsp;Prev</span>" : "<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=".($page - 1)."'><span style='font-weight:bold;'>&lt;&lt;&nbsp;Prev</span></a>");

	$pmlb = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$pagemenu3 = ($page == $pages ? "<span style='font-weight:bold;'>Next&nbsp;&gt;&gt;</span></p>" : "<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=".($page + 1)."'><span style='font-weight:bold;'>Next&nbsp;&gt;&gt;</span></a></p>");

	site_header("Viewing Topic $subject");

	if ($use_poll_mod && is_valid_id($pollid))
	{
		$res = sql_query("SELECT p.*, pa.id AS pa_id, pa.selection
							FROM postpolls AS p
							LEFT JOIN postpollanswers AS pa ON pa.pollid = p.id AND pa.userid = ".$CURUSER['id']."
							WHERE p.id = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) > 0)
		{
			$arr1 = mysql_fetch_assoc($res);

			$userid = (int)$CURUSER['id'];
			$question = htmlspecialchars($arr1["question"]);

			$o = array($arr1["option0"], $arr1["option1"], $arr1["option2"], $arr1["option3"], $arr1["option4"],
					   $arr1["option5"], $arr1["option6"], $arr1["option7"], $arr1["option8"], $arr1["option9"],
					   $arr1["option10"], $arr1["option11"], $arr1["option12"], $arr1["option13"], $arr1["option14"], $arr1["option15"], $arr1["option16"], $arr1["option17"], $arr1["option18"], $arr1["option19"]);

			?>
			<table cellpadding='5' width='<?php echo $forum_width; ?>' align='center'>
				<tr>
					<td class='colhead' align='left'><h2>Poll
			<?php

			if ($userid == $t_userid || $CURUSER['class'] >= UC_MODERATOR)
			{
				?>
				<span style='font-size: xx-small; font-weight:bold;'> - [<a href='forums.php?action=makepoll&amp;subaction=edit&amp;pollid=<?php echo $pollid; ?>'>Edit</a>]</span><?php

				if ($CURUSER['class'] >= UC_MODERATOR)
				{
				?>
					<span style='font-size: xx-small; font-weight:bold;'> - [<a href='forums.php?action=deletepoll&amp;pollid=<?php echo $pollid; ?>'>Delete</a>]</span>
				<?php
				}
			}
			?>
					</h2></td>
				</tr>
				<tr>
					<td align='center'>
						<table width='55%'><tr><td class='rowhead'>
							<div align='center'><span style='font-weight:bold;'><?php echo $question; ?></span></div>
			<?php

			$voted = (is_valid_id($arr1['pa_id']) ? true : false);

			if (($locked && $CURUSER['class'] < UC_MODERATOR) ? true : $voted)
			{
				$uservote = ($arr1["selection"] != '' ? (int)$arr1["selection"] : -1);

				$res3 = sql_query("SELECT selection
									FROM postpollanswers
									WHERE pollid = ".sqlesc($pollid)."
									AND selection < 20");

				$tvotes = mysql_num_rows($res3);

				$vs = $os = array();

				while ($arr3 = mysql_fetch_row($res3))
					$vs[$arr3[0]] += 1;

				reset($o);

				for ($i = 0; $i < count($o); ++$i)
					if ($o[$i])
						$os[$i] = array($vs[$i], $o[$i]);

				function srt($a,$b)
				{
					if ($a[0] > $b[0])
						return -1;

					if ($a[0] < $b[0])
						return 1;

					return 0;
				}

				if ($arr1["sort"] == "yes")
					usort($os, "srt");

				?>
				<br />
				<table width='100%' cellpadding="5">
				<?php
				for ($i=0; $a = $os[$i]; ++$i)
				{
					if ($i == $uservote)
						$a[1] .= " *";

					$p = ($tvotes == 0 ? 0 : round($a[0] / $tvotes * 100));
					$c = ($i % 2 ? '' : "poll");

					?>
					<tr>
						<td width='1%' style="padding:3px;" class='main<?php echo $c; ?>'>
							<div style='white-space: nowrap;'><?php echo htmlspecialchars($a[1]); ?></div>
						</td>
						<td width='99%' class='main<?php echo $c; ?>' align="center"><img src='<?php echo $image_dir; ?>bar_left.gif' width='2' height='9' border='0' alt='' title='' /><img src='<?php echo $image_dir; ?>bar.gif' width='<?php echo ($p*3); ?>' height='9' border='0' alt='' title=''  /><img src='<?php echo $image_dir; ?>bar_right.gif' width='2' height='9' border='0' alt='' title='' />&nbsp;<?php echo $p; ?>%</td>
					</tr>
					<?php
				}

				?>
				</table>
				<p align='center'>Votes: <span style='font-weight:bold;'><?php echo number_format($tvotes); ?></span></p>
				<?php
			}
			else
			{
				?>
				<form method='post' action='forums.php?action=viewtopic&amp;topicid=<?php echo $topicid; ?>'>
					<input type='hidden' name='pollid' value='<?php echo $pollid; ?>' /><?php

				for ($i=0; $a = $o[$i]; ++$i)
					echo "<input type='radio' name='choice' value='$i' />".htmlspecialchars($a)."<br />";

				?>
				<br />
				<p align='center'><input type='submit' class='btn' value='Vote!' /></p>
				</form>
				<?php
			}
			?>
			</td></tr></table></td></tr></table>
			<?php

			$listvotes = (isset($_GET['listvotes']) ? true : false);

			if ($CURUSER['class'] >= UC_ADMINISTRATOR)
			{
				if (!$listvotes)
					echo "<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;listvotes'>List Voters</a>";
				else
				{
					$res4 = sql_query("SELECT pa.userid, u.username
										FROM postpollanswers AS pa
										LEFT JOIN users AS u ON u.id = pa.userid
										WHERE pa.pollid = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);
					$voters = '';
					while ($arr4 = mysql_fetch_assoc($res4))
					{
						if (!empty($voters) && !empty($arr4['username']))
							$voters .= ', ';

						$voters .= "<a class='altlink_user' href='$site_url/userdetails.php?id=".(int)$arr4['userid']."'><span style='font-weight:bold;'>".htmlspecialchars($arr4['username'])."</span></a>";
					}

					echo $voters." (<span style='font-size: xx-small;'><a href='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=$topicid'>Hide</a></span>)";
				}
			}
		}
		else
		{
			?>
			<br />
			<?php
			error_message("error", "Sorry", "Poll Does Not Exist");
		}
		?>
		<br />
		<?php
	}

	?>
	<a name='top'></a>
	<h1 align="left"><a href="forums.php?action=viewforum&amp;forumid=<?php echo $forumid; ?>"><?php echo $forum; ?>
	</a> &gt; <?php echo htmlspecialchars($subject); ?></h1>
	<?php

	//-- Print table
	begin_frame();

	$res = sql_query("SELECT p.id, p.added, p.userid, p.added, p.body, p.editedby, p.editedat, u.id AS uid, u.username AS uusername, u.class, u.avatar, u.donor, u.title, u.country, u.enabled, u.warned, u.uploaded, u.downloaded, u.signature, u.last_access, (SELECT COUNT(id) FROM posts WHERE userid = u.id) AS posts_count, u2.username AS u2_username ".($use_attachment_mod ? ", at.id AS at_id, at.filename AS at_filename, at.postid AS at_postid, at.size AS at_size, at.downloads AS at_downloads, at.owner AS at_owner " : "").
	", (SELECT lastpostread FROM readposts WHERE userid = ".sqlesc((int)$CURUSER['id'])."".
	"AND topicid = p.topicid LIMIT 1) AS lastpostread ".
	"FROM posts AS p ".
	"LEFT JOIN users AS u ON p.userid = u.id ".
	($use_attachment_mod ? "LEFT JOIN attachments AS at ON at.postid = p.id " : "").
	"LEFT JOIN users AS u2 ON u2.id = p.editedby ".
	"WHERE p.topicid = ".sqlesc($topicid)." ORDER BY id LIMIT $offset, $perpage") or sqlerr(__FILE__, __LINE__);

	$pc = mysql_num_rows($res);
	$pn = 0;

	while ($arr = mysql_fetch_assoc($res))
	{
		++$pn;

		$lpr		= $arr['lastpostread'];
		$postid		= (int)$arr["id"];
		$postadd	= $arr['added'];
		$posterid	= (int)$arr['userid'];
		$added		= $arr['added'] . " GMT <span style='font-size: x-small;'>(" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr['added']))) . ")</span>";

		//-- Get poster details
		$uploaded		= mksize($arr['uploaded']);
		$downloaded		= mksize($arr['downloaded']);
		$last_access	= $arr['last_access'];

		if ($arr['downloaded'] > 0)
		{
			$ratio = $arr['uploaded'] / $arr['downloaded'];
			$color = get_ratio_color($ratio);
			$ratio = number_format($ratio, 3);

			if ($color)
				$ratio = "<span style='color : $color'>".$ratio."</span>";
		}
		else if ($arr['uploaded'] > 0)
			$ratio = "&infin;";
		else
			$ratio = "---";

		if (($postid > $lpr) && ($postadd > (get_date_time(gmtime() - $posts_read_expiry))))
			{
				$newp = "&nbsp;&nbsp;<span class='red'>(New)</span>";
			}

		$signature = ($CURUSER['signatures'] == 'yes' ? format_comment($arr['signature']) : '');

		$postername = $arr['uusername'];

		$avatar = (!empty($postername) ? ($CURUSER['avatars'] == "yes" ? htmlspecialchars($arr['avatar']) : '') : '');

		$title = (!empty($postername) ? (empty($arr['title']) ? "(".get_user_class_name($arr['class']).")" : "(".format_comment($arr['title']).")") : '');

		$forumposts = (!empty($postername) ? ($arr['posts_count'] != 0 ? $arr['posts_count'] : 'N/A') : 'N/A');

		$by = (!empty($postername) ? "<a class='altlink_user' href='$site_url/userdetails.php?id=$posterid'>".$postername."</a>".($arr['donor'] == "yes" ? "<img src='".$image_dir."star.png' width='16' height='16' border='0' alt='Donor' title='Donor'  />" : '').($arr['enabled'] == 'no' ? "<img src=".$image_dir."disabled.png width='16' height='15' border='0' alt='This account is Disabled' title='This account is Disabled' style='margin-left: 2px' />" : ($arr['warned'] == 'yes'? "<img src='".$image_dir."warned.png' width='15' height='16' border='0' alt='Warned' title='Warned' />" : '')) : "unknown[".$posterid."]");

		if (empty($avatar))
			$avatar = $image_dir."default_avatar.gif";

		echo "<a name='$postid'></a>";
		echo ($pn == $pc ? '<a name="last"></a>' : '');

		begin_table(true);

		?>
			<tr>
				<td class='rowhead' width='100%' colspan="2">
					<table class="main">
						<tr>
							<td style="border:none;" width="100%"><a href='forums.php?action=viewtopic&amp;topicid=<?php echo $topicid;?>&amp;page=p<?php echo $postid;?>#<?php echo $postid;?>'>#<?php echo $postid;?></a> by <?php echo $by;?> <?php echo $title;?> at <?php echo $added;
							if (isset($newp))
							{
								echo ("$newp");
							}
		?>
							</td>
							<td style="border:none;"><a href="#top"><input type='submit' class='btn' value='Top' /></a></td>
						</tr>
					</table>
				</td>
			</tr>
		<?php

		$highlight = (isset($_GET['highlight']) ? $_GET['highlight'] : '');

		$body = (!empty($highlight) ? highlight(htmlspecialchars(trim($highlight)), format_comment($arr['body'])) : format_comment($arr['body']));

		if (is_valid_id($arr['editedby']))
			$body .= "<p><span style='font-size: xx-small;'>Last edited by <a class='altlink_user' href='$site_url/userdetails.php?id=".$arr['editedby']."'><span style='font-weight:bold;'>".$arr['u2_username']."</span></a> at ".$arr['editedat']." GMT</span></p>";

		if ($use_attachment_mod && ((!empty($arr['at_filename']) && is_valid_id($arr['at_id'])) && $arr['at_postid'] == $postid))
		{
			foreach ($allowed_file_extensions AS $allowed_file_extension)
				if (substr($arr['at_filename'], -3) == $allowed_file_extension)
					$aimg = $allowed_file_extension;

			$body .= "<div style='padding:6px'><fieldset class='fieldset'>
					<legend>Attached Files</legend><br />

					<img class='inlineimg' src='$image_dir$aimg.gif' width='16' height='16' border='0' alt='Download' title='Download' style='vertical-align:baseline' />&nbsp;
					<a href='forums.php?action=attachment&amp;attachmentid=".$arr['at_id']."' target='_blank'>".htmlspecialchars($arr['at_filename'])."</a> (".mksize($arr['at_size']).", ".$arr['at_downloads']." downloads)
					&nbsp;&nbsp;<input type='button' class='btn' value=\"See Who's Downloaded\" tabindex='1' onclick=\"window.open('forums.php?action=whodownloaded&amp;fileid=".$arr['at_id']."','whodownloaded','toolbar=no, scrollbars=yes, resizable=yes, width=600, height=250, top=50, left=50'); return false;\" />".($CURUSER['class'] >= UC_MODERATOR ? "&nbsp;&nbsp;<input type='button' class='btn' value='Delete' tabindex='2' onclick=\"window.open('forums.php?action=attachment&amp;subaction=delete&amp;attachmentid=".$arr['at_id']."','attachment','toolbar=no, scrollbars=yes, resizable=yes, width=600, height=250, top=50, left=50'); return false;\" />" : "")."<br /><br />
					</fieldset>
					</div>";
		}

		if (!empty($signature))
			$body .= "<p style='vertical-align:bottom'><br />____________________<br /></p>".$signature;

		?>
		<tr valign='top'>
			<td class='rowhead' width='150' align='center' style='padding: 0px'>
				<img src="<?php echo $avatar;?>" width='' height='' border='0' alt='' title='' /><br />
				<fieldset style='text-align:left;border:none;'>
					<div style='white-space: nowrap;'>
						<span style='font-weight:bold;'>Posts:</span>&nbsp;&nbsp;&nbsp;<?php echo $forumposts;?><br />
						<span style='font-weight:bold;'>Ratio:</span>&nbsp;&nbsp;&nbsp;<?php echo $ratio;?><br />
						<span style='font-weight:bold;'>Uploaded:</span>&nbsp;&nbsp;&nbsp;<?php echo $uploaded;?><br />
						<span style='font-weight:bold;'>Downloaded:</span>&nbsp;&nbsp;&nbsp;<?php echo $downloaded;?>
					</div>
				</fieldset>
			</td>
			<td class='rowhead' width='100%'><?php echo $body;?></td>
		</tr>
		<tr>
			<td class='rowhead'>
				<input type='submit' class='btn' value='<?php echo ($last_access > get_date_time(gmtime()-360) || $posterid == $CURUSER['id'] ? 'Online' : 'Offline') ?>' />&nbsp;
				<a href="<?php echo $site_url; ?>/sendmessage.php?receiver=<?php echo $posterid; ?>"><input type='submit'  class='btn' value='PM'/></a>&nbsp;
				<!--<a href='<?php echo $site_url; ?>/report.php?type=Post&amp;id=<?php echo $postid; ?>&amp;id_2=<?php echo $topicid; ?>&amp;id_3=<?php echo $posterid; ?>'><img src="<?php echo $image_dir.$forum_pics['p_report_btn']; ?>" border="0" alt="Report Post" title='Report Post' /></a>  -->
			</td>
			<td  class='rowhead' align='right'>
		<?php
		if (!$locked || $CURUSER['class'] >= UC_MODERATOR)
		{
			?>
			<a href='forums.php?action=quotepost&amp;topicid=<?php echo $topicid; ?>&amp;postid=<?php echo $postid; ?>'><input type='submit' class='btn' value='Quote' /></a>&nbsp;
			<?php
		}

if (($CURUSER["id"] == $posterid && !$locked) || $CURUSER['class'] >= UC_MODERATOR)
		{
			?>
			<a href='forums.php?action=editpost&amp;postid=<?php echo $postid; ?>'><input type='submit' class='btn' value='Edit' /></a>
			<?php
		}

		if ($CURUSER['class'] >= UC_MODERATOR)
		{
			?>
			<a href='forums.php?action=deletepost&amp;postid=<?php echo $postid; ?>'><input type='submit' class='btn' value='Delete' /></a>&nbsp;
			<?php
		}

		?>
		</td></tr>
		<?php

		end_table();
		?>
		<br />
		<?php
	}

	if ($use_poll_mod && (($userid == $t_userid || $CURUSER['class'] >= UC_MODERATOR) && !is_valid_id($pollid)))
	{
		?>
		<form method='post' action='forums.php'>
			<table cellpadding="5" width='<?php echo $forum_width; ?>'>
				<tr>
					<td align="right">
						<input type='hidden' name='action' value="makepoll" />
						<input type='hidden' name='topicid' value="<?php echo $topicid; ?>" />
						<input type='submit' class='btn' value='Add a Poll' />
					</td>
				 </tr>
			</table>
		</form>
		<br />
		<?php
	}

	if (($postid > $lpr) && ($postadd > (get_date_time(gmtime() - $posts_read_expiry))))
	{
		if ($lpr)
			sql_query("UPDATE readposts
						SET lastpostread = $postid
						WHERE userid = $userid
						AND topicid = $topicid") or sqlerr(__FILE__, __LINE__);
		else
			sql_query("INSERT INTO readposts (userid, topicid, lastpostread)
						VALUES($userid, $topicid, $postid)") or sqlerr(__FILE__, __LINE__);
	}

	//-- Mod options
	if ($CURUSER['class'] >= UC_MODERATOR)
	{
		?>
		<form method='post' action='forums.php'>
			<input type='hidden' name='action' value='updatetopic' />
			<input type='hidden' name='topicid' value='<?php echo $topicid; ?>' />
		<?php

		begin_table();

		?>
		<tr>
			<td colspan="2" class='colhead'>Staff Options</td>
		</tr>

		<tr>
			<td class="rowhead" width="1%">Sticky</td>
			<td class='rowhead'>
				<select name="sticky">
					<option value="yes"<?php echo ($sticky ? " selected='selected'" : ''); ?>>Yes</option>
					<option value="no"<?php echo (!$sticky ? " selected='selected'" : ''); ?>>No</option>
				</select>
			</td>
		</tr>

		<tr>
			<td class="rowhead">Locked</td>
			<td class='rowhead'>
				<select name="locked">
					<option value="yes"<?php echo ($locked ? " selected='selected'" : ''); ?>>Yes</option>
					<option value="no"<?php echo (!$locked ? " selected='selected'" : ''); ?>>No</option>
				</select>
			</td>
		</tr>

		<tr>
			<td class="rowhead">Topic Name</td>
			<td class='rowhead'>
				<input type="text" name="subject" size="60" maxlength="<?php echo $maxsubjectlength; ?>" value="<?php echo htmlspecialchars($subject); ?>" />
			</td>
		</tr>

		<tr>
			<td class="rowhead">Move Topic</td>
			<td class='rowhead'>
				<select name='new_forumid'>
				<?php
				$res = sql_query("SELECT id, name, minclasswrite
									FROM forums
									ORDER BY name") or sqlerr(__FILE__, __LINE__);

				while ($arr = mysql_fetch_assoc($res))
					if ($CURUSER['class'] >= $arr["minclasswrite"])
						echo '<option value="'.(int)$arr["id"].'"'.($arr["id"] == $forumid ? ' selected="selected"' : '').'>'.htmlspecialchars($arr["name"]).'</option>';
				?>
				</select>
			</td>
		</tr>

		<tr>
			<td class="rowhead"><div style='white-space: nowrap;'>Delete Topic</div></td>
			<td class='rowhead'>
				<select name="delete">
					<option value="no" selected="selected">No</option>
					<option value="yes">Yes</option>
				</select>

				<br />
				<span style='font-weight:bold;'>Note:</span> Any changes made to the Topic won't take effect if you select "Yes"
			</td>
		</tr>

		<tr>
			<td colspan="2" align="center">
				<input type="submit" class='btn' value="Update Topic" />
			</td>
		</tr>

		<?php

		end_table();

		?>

		</form>

		<?php
	}

	end_frame();

	echo $pagemenu1.$pmlb.$pagemenu2.$pmlb.$pagemenu3;

	if ($locked && $CURUSER['class'] < UC_MODERATOR)
	{

		display_message("warn", "Sorry", "This Topic is Locked.  No New Posts are Allowed.");

	}
	else
	{
		$arr = get_forum_access_levels($forumid);

		if ($CURUSER['class'] < $arr["write"])
		{

			display_message("warn", "Sorry", "You are Mot Permitted to Post in this Forum.");


			$maypost = false;
		}
		else
			$maypost = true;
	}

	//-- "View unread" / "Add reply" buttons
	?>
	<table align="center" class="main" border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td class='embedded'>
				<form method='get' action='forums.php'><input type='hidden' name='action' value='viewunread' /><input type='submit' class='btn' value='Show New' /></form>
			</td>
	<?php

	if ($maypost)
	{
		?>
			<td class='embedded' style='padding-left: 10px'>
				<form method='get' action='forums.php'>
				<input type='hidden' name='action' value='reply' /><input type='hidden' name='topicid' value='<?php echo $topicid; ?>' /><input type='submit' class='btn' value='Answer' /></form>
			</td>
		<?php
	}
	?>
		</tr>
	</table>
	<?php

	if ($maypost)
	{
		?>
		<table style='border:1px solid #000000;' align="center">
			<tr>
				<td style='padding:10px;text-align:center;'>
					<span style='font-weight:bold;'>Quick Reply</span>
					<form name='compose' method='post' action='forums.php'>
						<input type='hidden' name='action' value='post' />
						<input type='hidden' name='topicid' value='<?php echo $topicid; ?>' />
						<textarea name="body" rows="4" cols="70"></textarea><br />
						<input type='submit' class='btn' value="Submit" />
					</form>
				</td>
			</tr>
		</table>
		<?php
	}

	//-- Forum quick jump drop-down
	insert_quick_jump_menu($forumid);

	site_footer();

	$uploaderror = (isset($_GET['uploaderror']) ? htmlspecialchars($_GET['uploaderror']) : '');

	if (!empty($uploaderror))
	{
		?><script>alert("Upload Failed: <?php echo $uploaderror; ?>\nHowever your Post was Successfully  Saved!\n\nClick 'OK' to Continue.");</script><?php
	}

	exit();
}
else if ($action == "quotepost") //-- Action: Quote
{
	$topicid = (int)$_GET["topicid"];

	if (!is_valid_id($topicid))
		error_message("error", "Error", "Invalid ID!");

	site_header("Post Reply");
	insert_compose_frame($topicid, false, true);
	site_footer();
	exit();
}
else if ($action == "reply") //-- Action: Reply
{
	$topicid = (int)$_GET["topicid"];

	if (!is_valid_id($topicid))
		error_message("error", "Error", "Invalid ID!");

	site_header("Post Reply");
	insert_compose_frame($topicid, false, false, true);
	site_footer();
	exit();
}
else if ($action == "editpost") //-- Action: Edit post
{
	$postid = (int)$_GET["postid"];

	if (!is_valid_id($postid))
		error_message("error", "Error", "Invalid ID!");

	$res = sql_query("SELECT p.userid, p.topicid, p.body, t.locked
						FROM posts AS p
						LEFT JOIN topics AS t ON t.id = p.topicid
						WHERE p.id = ".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
		error_message("error", "Error", "No Post with that ID!");

	$arr = mysql_fetch_assoc($res);

	if (($CURUSER["id"] != $arr["userid"] || $arr["locked"] == 'yes') && $CURUSER['class'] < UC_MODERATOR)
		error_message("warn", "Warning", "Access Denied!");

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$body = trim($_POST['body']);

		if (empty($body))
			error_message("error", "Error", "Body Cannot be Empty!");

		if (substr_count( strtolower($body), '[quote' ) > 3 ){
			error_message("info", "Sorry", "Quote Limit Reached");
		}

		sql_query("UPDATE posts
					SET body = ".sqlesc($body).", editedat = ".sqlesc(get_date_time()).", editedby = {$CURUSER['id']}
					WHERE id = $postid") or sqlerr(__FILE__, __LINE__);

		header("Location: {$_SERVER['PHP_SELF']}?action=viewtopic&topicid={$arr['topicid']}&page=p$postid#$postid");
		exit();
	}

	site_header();

	?>
	<h3>Edit Post</h3>

	<form name='edit' method='post' action='forums.php?action=editpost&amp;postid=<?php echo $postid; ?>'>
		<table border='1' cellspacing='0' cellpadding='5' width='100%'>
			<tr>
				<td class='rowhead' width='10%'>Body</td>
				<td align='left' style='padding: 0px'>
		<?php
		$ebody = htmlspecialchars(unesc($arr["body"]));
		if (function_exists('textbbcode'))
			echo("".textbbcode("compose", "body", htmlspecialchars($arr["body"])) . "");
		else
		{
		?>			<textarea name='body' style='width:99%' rows='7'><?php echo $ebody; ?></textarea><?php
		}
		?>
				</td>
			</tr>
			<tr>
				<td align='center' colspan='2'><input type='submit' class='btn' value='Update post' /></td>
			</tr>
		</table>
	</form>
	<br />

	<?php

	site_footer();
	exit();
}
else if ($action == 'deletepost' && $CURUSER['class'] >= UC_MODERATOR) //-- Action: Delete post
{
	$postid = (int)$_GET['postid'];

	if (!is_valid_id($postid))
		error_message("error", "Error", "Invalid ID");

	$res = sql_query("SELECT p.topicid".($use_attachment_mod ? ", a.filename" : "").", (SELECT COUNT(id) FROM posts WHERE topicid=p.topicid) AS posts_count,
						(SELECT MAX(id) FROM posts WHERE topicid=p.topicid AND id < p.id) AS p_id FROM posts AS p ".
						($use_attachment_mod ? "LEFT JOIN attachments AS a ON a.postid = p.id " : "").
						"WHERE p.id=".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res) or error_message("error", "Error", "Post NOT Found");

	$topicid = (int)$arr['topicid'];

	if ($arr['posts_count'] < 2)
		error_message("error", "Error", "<a href='forums.php?action=deletetopic&amp;topicid=$topicid'>You cannot Delete the Post, it's the only Post!  Delete the Topic instead?  Click to Confirm.</a>");

	$redirtopost = (is_valid_id($arr['p_id']) ? "&page=p".$arr['p_id']."#".$arr['p_id'] : '');

	$sure = (int)$_GET['sure'];

	if (!$sure)
		error_message("error", "Sanity Check", "<a href='forums.php?action=deletepost&amp;postid=$postid&amp;sure=1'>You are about to Delete a Post!  Click here if you are sure?</a>");

	sql_query("DELETE posts.* ".($use_attachment_mod ? ", attachments.*, attachmentdownloads.* " : "").
				"FROM posts ".
				($use_attachment_mod ? "LEFT JOIN attachments ON attachments.postid = posts.id ".
				"LEFT JOIN attachmentdownloads ON attachmentdownloads.fileid = attachments.id " : "").
				"WHERE posts.id = ".sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

	if ($use_attachment_mod && !empty($arr['filename']))
	{
		$filename = $attachment_dir."/".$arr['filename'];

		if (is_file($filename))
			unlink($filename);
	}

	$headerstr = "Location: forums.php?action=viewtopic&amp;topicid=$topicid".($use_attachment_mod && !empty($uploaderror) ? "&amp;uploaderror=$uploaderror" : "")."&amp;page=last";
	update_topic_last_post($topicid);

	header("Location: {$_SERVER['PHP_SELF']}?action=viewtopic&topicid=".$topicid.$redirtopost);
	exit();
}
else if ($use_poll_mod && ($action == 'deletepoll' && $CURUSER['class'] >= UC_MODERATOR))
{
	$pollid = (int)$_GET["pollid"];

	if (!is_valid_id($pollid))
		error_message("error", "Error", "Invalid ID!");

	$res = sql_query("SELECT pp.id, t.id AS tid
						FROM postpolls AS pp
						LEFT JOIN topics AS t ON t.pollid = pp.id
						WHERE pp.id = ".sqlesc($pollid));

	if (mysql_num_rows($res) == 0)
		error_message("error", "Error", "No Poll Found with that ID.");

	$arr = mysql_fetch_array($res);

	$sure = (int)isset($_GET['sure']) && (int) $_GET['sure'];

	if (!$sure || $sure != 1)
		error_message("error", "Sanity Check", "<a href='".$_SERVER['PHP_SELF']."?action=".htmlspecialchars($action)."&amp;pollid=".$arr['id']."&amp;sure=1'>You are about to Delete a Poll!  Click here to confirm?</a>");

	sql_query("DELETE pp.*, ppa.*
				FROM postpolls AS pp
				LEFT JOIN postpollanswers AS ppa ON ppa.pollid = pp.id
				WHERE pp.id = ".sqlesc($pollid));

	if (mysql_affected_rows() == 0)
		error_message("error", "Sorry", "There was an Error while Deleting the Poll, Please try again.");

	sql_query("UPDATE topics
				SET pollid = '0'
				WHERE pollid = ".sqlesc($pollid));

	header('Location: '.$_SERVER['PHP_SELF'].'?action=viewtopic&topicid='.(int)$arr['tid']);
	exit();
}
else if ($use_poll_mod && $action == 'makepoll')
{
	$subaction = (isset($_GET["subaction"]) ? $_GET["subaction"] : (isset($_POST["subaction"]) ? $_POST["subaction"] : ''));

	$pollid = (isset($_GET["pollid"]) ? (int)$_GET["pollid"] : (isset($_POST["pollid"]) ? (int)$_POST["pollid"] : 0));

	$topicid = (isset($_POST["topicid"]) ? (int)$_POST["topicid"] : 0);

	if ($subaction == "edit")
	{
		if (!is_valid_id($pollid))
			error_message("error", "Error", "Invalid ID!");

		$res = sql_query("SELECT pp.*, t.id AS tid
							FROM postpolls AS pp
							LEFT JOIN topics AS t ON t.pollid = pp.id
							WHERE pp.id = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) == 0)
			error_message("error", "Error", "No Poll found with that ID.");

		$poll = mysql_fetch_assoc($res);
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && !$topicid)
	{
		$topicid = (int)($subaction == "edit" ? $poll['tid'] : $_POST["updatetopicid"]);

		$question = $_POST["question"];
		$option0  = $_POST["option0"];
		$option1  = $_POST["option1"];
		$option2  = $_POST["option2"];
		$option3  = $_POST["option3"];
		$option4  = $_POST["option4"];
		$option5  = $_POST["option5"];
		$option6  = $_POST["option6"];
		$option7  = $_POST["option7"];
		$option8  = $_POST["option8"];
		$option9  = $_POST["option9"];
		$option10 = $_POST["option10"];
		$option11 = $_POST["option11"];
		$option12 = $_POST["option12"];
		$option13 = $_POST["option13"];
		$option14 = $_POST["option14"];
		$option15 = $_POST["option15"];
		$option16 = $_POST["option16"];
		$option17 = $_POST["option17"];
		$option18 = $_POST["option18"];
		$option19 = $_POST["option19"];
		$sort     = $_POST["sort"];

		if (!$question || !$option0 || !$option1)
			error_message("warn", "Warning", "Missing Form Data!");

		if ($subaction == "edit" && is_valid_id($pollid))
			sql_query("UPDATE postpolls SET " .
							"question = " . sqlesc($question) . ", " .
							"option0  = " . sqlesc($option0) . ", " .
							"option1  = " . sqlesc($option1) . ", " .
							"option2  = " . sqlesc($option2) . ", " .
							"option3  = " . sqlesc($option3) . ", " .
							"option4  = " . sqlesc($option4) . ", " .
							"option5  = " . sqlesc($option5) . ", " .
							"option6  = " . sqlesc($option6) . ", " .
							"option7  = " . sqlesc($option7) . ", " .
							"option8  = " . sqlesc($option8) . ", " .
							"option9  = " . sqlesc($option9) . ", " .
							"option10 = " . sqlesc($option10) . ", " .
							"option11 = " . sqlesc($option11) . ", " .
							"option12 = " . sqlesc($option12) . ", " .
							"option13 = " . sqlesc($option13) . ", " .
							"option14 = " . sqlesc($option14) . ", " .
							"option15 = " . sqlesc($option15) . ", " .
							"option16 = " . sqlesc($option16) . ", " .
							"option17 = " . sqlesc($option17) . ", " .
							"option18 = " . sqlesc($option18) . ", " .
							"option19 = " . sqlesc($option19) . ", " .
							"sort     = " . sqlesc($sort) . " " .
					"WHERE id = ".sqlesc((int)$poll["id"])) or sqlerr(__FILE__, __LINE__);
		else
		{
			if (!is_valid_id($topicid))
				error_message("error", "Error", "Invalid Topic ID!");

			sql_query("INSERT INTO postpolls VALUES(id" .
							", " . sqlesc(get_date_time()) .
							", " . sqlesc($question) .
							", " . sqlesc($option0) .
							", " . sqlesc($option1) .
							", " . sqlesc($option2) .
							", " . sqlesc($option3) .
							", " . sqlesc($option4) .
							", " . sqlesc($option5) .
							", " . sqlesc($option6) .
							", " . sqlesc($option7) .
							", " . sqlesc($option8) .
							", " . sqlesc($option9) .
							", " . sqlesc($option10) .
							", " . sqlesc($option11) .
							", " . sqlesc($option12) .
							", " . sqlesc($option13) .
							", " . sqlesc($option14) .
							", " . sqlesc($option15) .
							", " . sqlesc($option16) .
							", " . sqlesc($option17) .
							", " . sqlesc($option18) .
							", " . sqlesc($option19) .
							", " . sqlesc($sort).")") or sqlerr(__FILE__, __LINE__);

			$pollnum = mysql_insert_id();

			sql_query("UPDATE topics
						SET pollid = ".sqlesc($pollnum)."
						WHERE id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
		}

		header("Location: {$_SERVER['PHP_SELF']}?action=viewtopic&topicid=$topicid");
		exit();
	}
	site_header();

	if ($subaction == "edit")
		echo "<h1>Edit Poll</h1>";
	?>
	<form method='post' action='forums.php'>
		<input type='hidden' name='action' value='<?php echo $action; ?>' />
		<input type='hidden' name='subaction' value='<?php echo $subaction; ?>' />
		<input type='hidden' name='updatetopicid' value='<?php echo (int)$topicid; ?>' />

	<?php
	if ($subaction == "edit")
	{
		?><input type='hidden' name='pollid' value='<?php echo (int)$poll["id"]; ?>' /><?php
	}
	?>
			<table border='1' cellspacing='0' cellpadding='5' width='100%'>
				<tr>
					<td class='rowhead'>Question <span style='color : #ff0000;'>*</span></td><td align='left'>
						<textarea name='question' cols='70' rows='4'><?php echo ($subaction == "edit" ? htmlspecialchars($poll['question']) : ''); ?></textarea>
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 1 <span style='color : #ff0000;'>*</span></td><td align='left'><input name='option0' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option0']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 2 <span style='color : #ff0000;'>*</span></td><td align='left'><input name='option1' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option1']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 3</td><td align='left'><input name='option2' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option2']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 4</td><td align='left'><input name='option3' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option3']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 5</td><td align='left'><input name='option4'size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option4']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 6</td><td align='left'><input name='option5' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option5']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 7</td><td align='left'><input name='option6' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option6']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 8</td><td align='left'><input name='option7' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option7']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 9</td><td align='left'><input name='option8' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option8']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 10</td><td align='left'><input name='option9' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option9']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 11</td><td align='left'><input name='option10' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option10']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 12</td><td align='left'><input name='option11' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option11']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 13</td><td align='left'><input name='option12' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option12']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 14</td><td align='left'><input name='option13' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option13']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 15</td><td align='left'><input name='option14' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option14']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 16</td><td align='left'><input name='option15' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option15']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 17</td><td align='left'><input name='option16' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option16']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 18</td><td align='left'><input name='option17' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option17']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 19</td><td align='left'><input name='option18' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option18']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Option 20</td><td align='left'><input name='option19' size='80' maxlength='40' value="<?php echo ($subaction == "edit" ? htmlspecialchars($poll['option19']) : ''); ?>" /><br />
					</td>
				</tr>

				<tr>
					<td class='rowhead'>Sort</td>
					<td class='rowhead'>

				<input type='radio' name='sort' value='yes' <?php echo ($subaction == "edit" ? ($poll["sort"] != "no" ? " checked='checked'" : "") : ''); ?> />Yes

				<input type='radio' name='sort' value='no' <?php echo ($subaction == "edit" ? ($poll["sort"] == "no" ? " checked='checked'" : "") : ''); ?> /> No

					</td>
				</tr>

				<tr>
					<td colspan='2' align='center'><input type='submit' class='btn' value='<?php echo ($pollid ? 'Edit poll' : 'Create poll'); ?>' style='height: 20pt' /></td>
				</tr>
			</table>
			<p align='center'><span style='color : #ff0000;'>*</span> Required</p>
		</form>
		<br />
		<?php

	site_footer();
//end_main_frame();
}
else if ($use_attachment_mod && $action == "attachment")
{
	@ini_set('zlib.output_compression', 'Off');
	@set_time_limit(0);

	if (@ini_get('output_handler') == 'ob_gzhandler' && @ob_get_length() !== false)
	{
		@ob_end_clean();
		header('Content-Encoding:');
	}

	$id = (int)$_GET['attachmentid'];

	if (!is_valid_id($id))
		die('Invalid Attachment ID!');

	$at = sql_query("SELECT filename, owner, type
						FROM attachments
						WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

	$resat = mysql_fetch_assoc($at) or die('No Attachment with that ID!');

	$filename = $attachment_dir.'/'.$resat['filename'];

	if (!is_file($filename))
		die('Non-existent Attachment.');

	if (!is_readable($filename))
		die('Attachment is Unreadable.');

	if ((isset($_GET['subaction']) ? $_GET['subaction'] : '') == 'delete')
	{
		if ($CURUSER['id'] <> $resat["owner"] && $CURUSER['class'] < UC_MODERATOR)
			die('Not your Attachment to Delete.');

		unlink($filename);

		sql_query("DELETE attachments, attachmentdownloads
					FROM attachments
					LEFT JOIN attachmentdownloads ON attachmentdownloads.fileid = attachments.id
					WHERE attachments.id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

		die("<span style='color : #ff0000;'>File Successfully Deleted</span>");
	}

	sql_query("UPDATE attachments
				SET downloads = downloads + 1
				WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

	$res = sql_query("SELECT fileid
						FROM attachmentdownloads
						WHERE fileid=".sqlesc($id)."
						AND userid=".sqlesc($CURUSER['id']));

	if (mysql_num_rows($res) == 0)
		sql_query("INSERT INTO attachmentdownloads (fileid, username, userid, date, downloads)
					VALUES (".sqlesc($id).", ".sqlesc($CURUSER['username']).", ".sqlesc($CURUSER['id']).", ".sqlesc(get_date_time()).", 1)") or sqlerr(__FILE__, __LINE__);
	else
		sql_query("UPDATE attachmentdownloads
					SET downloads = downloads + 1
					WHERE fileid = ".sqlesc($id)."
					AND userid = ".sqlesc($CURUSER['id']));

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false); // required for certain browsers
	header("Content-Type: ".$arr['type']);
	header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));
	readfile($filename);
	exit();
}
else if ($use_attachment_mod && $action == "whodownloaded")
{
	$fileid = (int)$_GET['fileid'];

	if (!is_valid_id($fileid))
		die('Invalid ID!');

	$res = sql_query("SELECT fileid, at.filename, userid, username, atdl.downloads, date, at.downloads AS dl
						FROM attachmentdownloads AS atdl
						LEFT JOIN attachments AS at ON at.id=atdl.fileid
						WHERE fileid = ".sqlesc($fileid).($CURUSER['class'] < UC_MODERATOR ? "
						AND owner=".$CURUSER['id'] : '')) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
		die('<h2 align="center">Nothing Found!</h2>');
	else
	{
		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>

			<meta name='generator' content='FreeTSP.info' />
			<meta name='MSSmartTagsPreventParsing' content='true' />

<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>Who Downloaded</title>
<link rel='stylesheet' href='./stylesheets/default/default.css' type='text/css' />
</head>
<body>
	<table width='100%' cellpadding='5' border='1'>
		<tr align='center'>
			<td class='rowhead'><span style='font-weight:bold;'>File Name</span></td>
			<td style='white-space: nowrap;'><span style='font-weight:bold;'>Downloaded by</span></td>
			<td class='rowhead'><span style='font-weight:bold;'>Downloads</span></td>
			<td class='rowhead'><span style='font-weight:bold;'>Date</span></td>
		</tr>
		<?php

	$dls = 0;

	while ($arr = mysql_fetch_assoc($res))
	{
		echo "<tr align='center'>
				<td style='white-space: nowrap;'>".htmlspecialchars($arr['filename'])."</td>
				<td style='white-space: nowrap;'><span style='cursor:pointer'><a class='pointer' onclick=\"opener.location=('/userdetails.php?id=".(int)$arr['userid']."'); self.close();\">".htmlspecialchars($arr['username'])."</a></span></td>
				<td style='white-space: nowrap;'>".(int)$arr['downloads']."</td>
				<td style='white-space: nowrap;'>".$arr['date']." (".get_elapsed_time(sql_timestamp_to_unix_timestamp($arr['date'])).")</td>
			</tr>";

		$dls += (int)$arr['downloads'];
	}
	?>
		<tr>
			<td colspan='4'><span style='font-weight:bold;'>Total Downloads:</span> <span style='font-weight:bold;'><?php echo number_format($dls); ?></span></td>
		</tr>
	</table>
</body></html>
<?php
	}
}
else if ($action == "viewforum") //-- Action: View forum
{
	$forumid = (int)$_GET['forumid'];
	if (!is_valid_id($forumid))
		error_message("error", "Error", "Invalid ID!");

	$page = (isset($_GET["page"]) ? (int)$_GET["page"] : 0);
	$userid = (int)$CURUSER["id"];

	//--  Get forum details
	$res = sql_query("SELECT f.name AS forum_name, f.minclassread, (SELECT COUNT(id) FROM topics WHERE forumid = f.id) AS t_count FROM forums AS f
						WHERE f.id = ".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res) or error_message("error", "Error", "No Forum with that ID!");

	if ($CURUSER['class'] < $arr["minclassread"])
		error_message("warn", "Warning", "Access Denied!");

	$perpage	= (empty($CURUSER['topicsperpage']) ? 20 : (int)$CURUSER['topicsperpage']);
	$num		= (int)$arr['t_count'];

	if ($page == 0)
		$page = 1;

	$first = ($page * $perpage) - $perpage + 1;
	$last  = $first + $perpage - 1;

	if ($last > $num)
		$last = $num;

	$pages = floor($num / $perpage);

	if ($perpage * $pages < $num)
		++$pages;

	//-- Build menu
	$menu1 = "<p class='success' align='center'>";
	$menu2 = '';

	$lastspace = false;
	for ($i = 1; $i <= $pages; ++$i)
	{
		if ($i == $page)
			$menu2 .= "[<span style='text-decoration: underline; font-weight:bold;'>$i</span>]\n";

		else if ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3))
		{
			if ($lastspace)
				continue;

			$menu2 .= "... \n";

			$lastspace = true;
		}
		else
		{
			$menu2 .= "<a href='forums.php?action=viewforum&amp;forumid=$forumid&amp;page=$i'><span style='font-weight:bold;'>$i</span></a>\n";

			$lastspace = false;
		}

		if ($i < $pages)
			$menu2 .= "|";
	}

	$menu1 .= ($page == 1 ? "<span style='font-weight:bold;'>&lt;&lt;&nbsp;Prev</span>" : "<a href='forums.php?action=viewforum&amp;forumid=$forumid&amp;page=" . ($page - 1) . "'><span style='font-weight:bold;'>&lt;&lt;&nbsp;Prev</span></a>");

	$mlb = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$menu3 = ($last == $num ? "<span style='font-weight:bold;'>Next&nbsp;&gt;&gt;</span></p>" : "<a href='forums.php?action=viewforum&amp;forumid=$forumid&amp;page=" . ($page + 1) . "'><span style='font-weight:bold;'>Next&nbsp;&gt;&gt;</span></a></p>");

	$offset = $first - 1;

	$topics_res = sql_query("SELECT t.id, t.userid, t.views, t.locked, t.sticky".($use_poll_mod ? ', t.pollid' : '').", t.subject, u1.username, r.lastpostread, p.id AS p_id, p.userid AS p_userid, p.added AS p_added, (SELECT COUNT(id) FROM posts WHERE topicid=t.id) AS p_count, u2.username AS u2_username
							FROM topics AS t
							LEFT JOIN users AS u1 ON u1.id=t.userid
							LEFT JOIN readposts AS r ON r.userid = ".sqlesc($userid)." AND r.topicid = t.id
							LEFT JOIN posts AS p ON p.id = (SELECT MAX(id) FROM posts WHERE topicid = t.id)
							LEFT JOIN users AS u2 ON u2.id = p.userid
							WHERE t.forumid = ".sqlesc($forumid)."
							ORDER BY t.sticky, t.lastpost DESC
							LIMIT $offset, $perpage") or sqlerr(__FILE__, __LINE__);

	site_header("Forum");

	?>
		<h1 align="center"><?php echo htmlspecialchars($arr["forum_name"]); ?></h1>
	<?php

	if (mysql_num_rows($topics_res) > 0)
	{
		?>
			<table border='1' cellspacing='0' cellpadding='5' width='<?php echo $forum_width; ?>'>
				<tr>
					<td class='colhead' align='center'>Topic Title</td>
					<td class='colhead' align='center'>Replies</td>
					<td class='colhead' align='center'>Views</td>
					<td class='colhead' align='center'>Author</td>
					<td class='colhead' align='center'>Last&nbsp;post</td>
				</tr>
		<?php
		while ($topic_arr = mysql_fetch_assoc($topics_res))
		{
			$topicid		= (int)$topic_arr['id'];
			$topic_userid	= (int)$topic_arr['userid'];
			$sticky			= ($topic_arr['sticky'] == "yes");

			($use_poll_mod ? $topicpoll = is_valid_id($topic_arr["pollid"]) : NULL);

			$tpages = floor($topic_arr['p_count'] / $postsperpage);

			if (($tpages * $postsperpage) != $topic_arr['p_count'])
				++$tpages;

			if ($tpages > 1)
			{
				$topicpages = "&nbsp;(<img src='".$image_dir."multipage.gif' width='8' height='10' border='0' alt='Multiple pages' title='Multiple pages' />";

				$split = ($tpages > 10) ? true : false;
				$flag  = false;

				for ($i = 1; $i <= $tpages; ++$i)
				{
					if ($split && ($i > 4 && $i < ($tpages - 3)))
					{
						if (!$flag)
						{
							$topicpages .= '&nbsp;...';
							$flag = true;
						}
						continue;
					}
					$topicpages .= "&nbsp;<a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=$i'>$i</a>";
				}
				$topicpages .= ")";
			}
			else
				$topicpages = '';

			$lpusername = (is_valid_id($topic_arr['p_userid']) && !empty($topic_arr['u2_username']) ? "<a class='altlink_user' href='$site_url/userdetails.php?id=".(int)$topic_arr['p_userid']."'><span style='font-weight:bold;'>".$topic_arr['u2_username']."</span></a>" : "unknown[$topic_userid]");

			$lpauthor = (is_valid_id($topic_arr['userid']) && !empty($topic_arr['username']) ? "<a class='altlink_user' href='$site_url/userdetails.php?id=$topic_userid'><span style='font-weight:bold;'>".$topic_arr['username']."</span></a>" : "unknown[$topic_userid]");

			$new = ($topic_arr["p_added"] > (get_date_time(gmtime() - $posts_read_expiry))) ? ((int)$topic_arr['p_id'] > $topic_arr['lastpostread']) : 0;

			$topicpic = ($topic_arr['locked'] == "yes" ? ($new ? "lockednew" : "locked") : ($new ? "newpost" : "post"));

			?>
			<tr>
				<td class='forum' align='left' width='100%'>
					<img src='<?php echo $image_dir.$topicpic; ?>.png' width='32' height='32 'border='0' alt='' title='' />
					<?php echo ($sticky ? 'Sticky:&nbsp;' : ''); ?><a href='forums.php?action=viewtopic&amp;topicid=<?php echo $topicid; ?>'>
					<?php echo htmlspecialchars($topic_arr['subject']); ?></a><?php echo $topicpages; ?>
				</td>

				<td class='forum' align='center'><?php echo max(0, $topic_arr['p_count'] - 1); ?></td>
				<td class='forum' align='center'><?php echo number_format($topic_arr['views']); ?></td>
				<td class='forum' align='center'>&nbsp;<?php echo $lpauthor; ?>&nbsp;</td>

				<td class='forum' align='center'>
					<div style='white-space: nowrap;'>
					&nbsp;<?php echo $topic_arr["p_added"]; ?>&nbsp;
					<br />by - <?php echo $lpusername; ?>
					</div>
				</td>

			</tr>
			<?php
		}

		end_table();
	}
	else
	{
		display_message("info", "Sorry", "No Topics Found");
	}

	echo $menu1.$mlb.$menu2.$mlb.$menu3;
	?>
	<table class='main' border='0' cellspacing='0' cellpadding='0' align='center'>
		<tr valign='middle'>
			<td class='embedded'><img src='<?php echo $image_dir; ?>new-post.png' width='48' height='48' border='0' alt='New Posts' title='New Posts' style='margin-right: 5px' /></td>
			<td class='embedded'>New Posts</td>
			<td class='embedded'><img src='<?php echo $image_dir; ?>lock.png'  width='48' height='48' border='0' alt='Locked Topic' title='Locked Topic' style='margin-left: 10px; margin-right: 5px' /></td>
			<td class='embedded'>Locked Topic</td>
		</tr>
	</table>
	<?php
	$arr = get_forum_access_levels($forumid) or die();

	$maypost = ($CURUSER['class'] >= $arr["write"] && $CURUSER['class'] >= $arr["create"]);

	if (!$maypost)
	{
			display_message("warn", "Sorry", "You are Not Permitted to start new Topics in this Forum.");
	}

	?>
	<table border='0' class='main' cellspacing='0' cellpadding='0' align='center'>
		<tr>
			<td class='embedded'>
				<form method='get' action='forums.php'>
					<input type='hidden' name='action' value='viewunread' />
					<input type='submit' class='btn' value='View Unread' />
				</form>
			</td>
	<?php
	if ($maypost)
	{
		?>
			<td class='embedded'>
				<form method='get' action='forums.php'>
					<input type='hidden' name='action' value='newtopic' />
					<input type='hidden' name='forumid' value='<?php echo $forumid; ?>' />
					<input type='submit' class='btn' value='New topic' style='margin-left: 10px' />
				</form>
			</td>
		<?php
	}

	?>
		</tr>
	</table>
	<br />
<?php

	insert_quick_jump_menu($forumid);

	site_footer();
	exit();
}
else if ($action == 'viewunread') //-- Action: View unread posts
{
	if ((isset($_POST[$action."_action"]) ? $_POST[$action."_action"] : '') == 'clear')
	{
		$topic_ids = (isset($_POST['topic_id']) ? $_POST['topic_id'] : array());

		if (empty($topic_ids))
		{
			header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action);
			exit();
		}

		foreach ($topic_ids AS $topic_id)
			if (!is_valid_id($topic_id))
				error_message("error", "Error", "Invalid ID!");

		catch_up($topic_ids);

		header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action);
		exit();
	}
	else
	{
		$added = sqlesc(get_date_time(gmtime() - $posts_read_expiry));

		$res = sql_query('SELECT t.lastpost, r.lastpostread, f.minclassread
							FROM topics AS t
							LEFT JOIN posts AS p ON t.lastpost=p.id
							LEFT JOIN readposts AS r ON r.userid='.sqlesc((int)$CURUSER['id']).' AND r.topicid=t.id
							LEFT JOIN forums AS f ON f.id=t.forumid
							WHERE p.added > '.$added) or sqlerr(__FILE__, __LINE__);
		$count = 0;

		while($arr = mysql_fetch_assoc($res))
		{
			if ($arr['lastpostread'] >= $arr['lastpost'] || $CURUSER['class'] < $arr['minclassread'])
				continue;

			$count++;
		}
		mysql_free_result($res);

		if ($count > 0)
		{
			list($pagertop, $pagerbottom, $limit) = pager(25, $count, $_SERVER['PHP_SELF'].'?action='.$action.'&');

			site_header();

			echo '<h1 align="center">Topics with Unread Posts</h1>';

			echo $pagertop;

			?>
			<script type='text/javascript'>
				var checkflag = 'false';

				function check(a)
				{
					if (checkflag == 'false')
					{
						for(i=0; i < a.length; i++)
							a[i].checked = true;

						checkflag = 'true';

						value = 'Uncheck';
					}
					else
					{
						for(i=0; i < a.length; i++)
							a[i].checked = false;

						checkflag = 'false';

						value = 'Check';
					}

					return value + ' All';
				}
			</script>

			<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?action='.$action; ?>">
				<input type="hidden" name="<?php echo $action.'_action'; ?>" value="clear" />
			<?php

			?>
					 <table cellpadding="5" width='<?php echo $forum_width; ?>'>
						<tr align="left">
							<td class="colhead" colspan="2">Topic</td>
							<td class="colhead" width="1%">Clear</td>
						</tr>
			<?php

			$res = sql_query('SELECT t.id, t.forumid, t.subject, t.lastpost, r.lastpostread, f.name, f.minclassread
								FROM topics AS t
								LEFT JOIN posts AS p ON t.lastpost=p.id
								LEFT JOIN readposts AS r ON r.userid='.sqlesc((int)$CURUSER['id']).' AND r.topicid=t.id
								LEFT JOIN forums AS f ON f.id=t.forumid
								WHERE p.added > '.$added.'
								ORDER BY t.forumid '.$limit) or sqlerr(__FILE__, __LINE__);

			while($arr = mysql_fetch_assoc($res))
			{
				if ($arr['lastpostread'] >= $arr['lastpost'] || $CURUSER['class'] < $arr['minclassread'])
					continue;

				$post_res = sql_query("SELECT id
										FROM posts
										WHERE topicid = ".(int)$arr['id']) or sqlerr(__FILE__, __LINE__);

				while ($post_arr = mysql_fetch_assoc($post_res))

					if ($arr['lastpostread'] < $post_arr['id'] && !isset($post[$i]))
						$post[$i] = $post_arr['id'];
				?>
				<tr>
					<td align="center" width="1%">
						<img src='<?php echo $image_dir; ?>newpost.png' width='32' height='32' border='0' alt='' title='' />
					</td>
					<td align="left">
						<a href='forums.php?action=viewtopic&amp;topicid=<?php echo (int)$arr['id']; ?>&amp;page=last#last'><?php echo htmlspecialchars($arr['subject']); ?></a><br />in&nbsp;<span style='font-size: small;'><a href='forums.php?action=viewforum&amp;forumid=<?php echo (int)$arr['forumid']; ?>'><?php echo htmlspecialchars($arr['name']); ?></a></span>
					 </td>
					<td align="center">
						<input type="checkbox" name="topic_id[]" value="<?php echo (int)$arr['id']; ?>" />
					</td>
				</tr>
				<?php
					$i++;
			}
			mysql_free_result($res);

			?>
			<tr>
				<td align="center" colspan="3">
					<input type='button' value="Check All" onclick="this.value = check(form);" class='btn' />&nbsp;<input type="submit" class='btn' value="Clear selected" />
				</td>
			</tr>
			<?php

			end_table();

			?>
			</form>
			<?php

			echo $pagerbottom;

			echo '<div align="center" class="btn"><a href="'.$_SERVER['PHP_SELF'].'?catchup">Mark All Posts as Read</a></div><br />';

			site_footer(); die();
		}
		else
			error_message("info", "Sorry", "There are NO Unread Posts.<br /><br />Click <a href='forums.php?action=getdaily'>here</a> to get Today's Posts (last 24h).");
	}
}
else if ($action == "getdaily")
{
	$res = sql_query('SELECT COUNT(p.id) AS post_count
						FROM posts AS p
						LEFT JOIN topics AS t ON t.id = p.topicid
						LEFT JOIN forums AS f ON f.id = t.forumid
						WHERE ADDDATE(p.added, INTERVAL 1 DAY) > '.sqlesc(get_date_time()).'
						AND f.minclassread <= '.$CURUSER['class']) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res);
	mysql_free_result($res);

	$count = (int)$arr['post_count'];
	if (empty($count))
		error_message("info", "Sorry", "No Posts in the Last 24 Hours.");

	site_header('Today Posts (Last 24 Hours)');

	list($pagertop, $pagerbottom, $limit) = pager(20, $count, $_SERVER['PHP_SELF'].'?action='.$action.'&');

	?><h2 align="center">Today Posts (Last 24 Hours)</h2><?php
	echo $pagertop;

	?>
	<table cellpadding="5" width="<?php echo $forum_width; ?>">
		<tr class="colhead" align="center">
			<td width="100%" align="left"><span style='font-weight:bold;'>Topic Title</span></td>
			<td class='rowhead'><span style='font-weight:bold;'>Views</span></td>
			<td class='rowhead'><span style='font-weight:bold;'>Author</span></td>
			<td class='rowhead'><span style='font-weight:bold;'>Posted At</span></td>
		</tr>
	<?php

	$res = sql_query('SELECT p.id AS pid, p.topicid, p.userid AS userpost, p.added, t.id AS tid, t.subject, t.forumid, t.lastpost, t.views, f.name, f.minclassread, f.topiccount, u.username
						FROM posts AS p
						LEFT JOIN topics AS t ON t.id = p.topicid
						LEFT JOIN forums AS f ON f.id = t.forumid
						LEFT JOIN users AS u ON u.id = p.userid
						LEFT JOIN users AS topicposter ON topicposter.id = t.userid
						WHERE ADDDATE(p.added, INTERVAL 1 DAY) > '.sqlesc(get_date_time()).'
						AND f.minclassread <= '.$CURUSER['class'].'
						ORDER BY p.added DESC '.$limit) or sqlerr(__FILE__, __LINE__);

	while ($getdaily = mysql_fetch_assoc($res))
	{
		$postid		= (int)$getdaily['pid'];
		$posterid	= (int)$getdaily['userpost'];

		?>
		<tr>
			<td class="rowhead" align="left">
				<a href='forums.php?action=viewtopic&amp;topicid=<?php echo $getdaily['tid']; ?>&amp;page=<?php echo $postid; ?>#<?php echo $postid ?>'><?php echo htmlspecialchars($getdaily['subject']); ?></a><br />
				<span style='font-weight:bold;'>In</span>&nbsp;<a href="forums.php?action=viewforum&amp;forumid=<?php echo (int)$getdaily['forumid']; ?>"><?php echo htmlspecialchars($getdaily['name']); ?></a>
			</td>
			<td class="rowhead" align="center"><?php echo number_format($getdaily['views']); ?></td>
			<td class="rowhead" align="center"><?php
				if (!empty($getdaily['username']))
				{
					?><a class='altlink_user' href="<?php echo $site_url; ?>/userdetails.php?id=<?php echo $posterid; ?>"><?php echo htmlspecialchars($getdaily['username']); ?></a>
					<?php
				}
				else
				{
					?><span style='font-weight:bold;'>Unknown[<?php echo $posterid; ?>]</span><?php
				}
			?></td>
			<td class='rowhead'>
				<div style='white-space: nowrap;'><?php echo $getdaily['added'];?><br />
				<?php
				echo get_elapsed_time(strtotime($getdaily['added']));
				?>
				</div>
			</td>
		</tr>
		<?php
	}
	mysql_free_result($res);

	end_table();

	echo $pagerbottom;

	site_footer();
}
else if ($action == "search") //-- Action: Search
{
	site_header("Forum Search");

	$error		= false;
	$found		= '';
	$keywords	= (isset($_GET['keywords']) ? trim($_GET['keywords']) : '');

	if (!empty($keywords))
	{
		$res = sql_query("SELECT COUNT(id) AS c
							FROM posts
							WHERE body LIKE ".sqlesc("%".sqlwildcardesc($keywords)."%")) or sqlerr(__FILE__, __LINE__);

		$arr = mysql_fetch_assoc($res);

		$count		= (int)$arr['c'];
		$keywords	= htmlspecialchars($keywords);

		if ($count == 0)
			$error = true;
		else
		{
			list($pagertop, $pagerbottom, $limit) = pager(10, $count, $_SERVER['PHP_SELF'].'?action='.$action.'&keywords='.$keywords.'&');

			$res = sql_query("SELECT p.id, p.topicid, p.userid, p.added, t.forumid, t.subject, f.name, f.minclassread, u.username
								FROM posts AS p
								LEFT JOIN topics AS t ON t.id=p.topicid
								LEFT JOIN forums AS f ON f.id=t.forumid
								LEFT JOIN users AS u ON u.id=p.userid
								WHERE p.body LIKE ".sqlesc("%".$keywords."%")." $limit");

			$num = mysql_num_rows($res);
			echo $pagertop;

			?>
			<table border='0' cellspacing='0' cellpadding='5' width='100%'>
				<tr align="left">
					<td class='colhead'>Post</td>
					<td class='colhead'>Topic</td>
					<td class='colhead'>Forum</td>
					<td class='colhead'>Posted by</td>
				</tr>
			<?php

			for ($i = 0; $i < $num; ++$i)
			{
				$post = mysql_fetch_assoc($res);

				if ($post['minclassread'] > $CURUSER['class'])
				{
					--$count;
					continue;
				}

				echo "<tr>
						<td align='center'>".$post['id']."</td>
						<td align='left' width='100%'><a href='forums.php?action=viewtopic&amp;highlight=$keywords&amp;topicid=".$post['topicid']."&amp;page=p".$post['id']."#".$post['id']."'><span style='font-weight:bold;'>" . htmlspecialchars($post['subject']) . "</span></a></td>
						<td align='left' style='white-space: nowrap'>".(empty($post['name']) ? 'unknown['.$post['forumid'].']' : "<a href='forums.php?action=viewforum&amp;forumid=".$post['forumid']."'><span style='font-weight:bold;'>" . htmlspecialchars($post['name']) . "</span></a>")."</td>
						<td align='left' style='white-space: nowrap'>".(empty($post['username']) ? 'unknown['.$post['userid'].']' : "<span style='font-weight:bold;'><a class='altlink_user' href='$site_url/userdetails.php?id=".$post['userid']."'>".$post['username']."</a></span>")."<br />at ".$post['added']."</td>
					</tr>";
			}
			end_table();

			echo $pagerbottom;

			$found ="[<span style='color : #ff0000; font-weight:bold;'> Found $count Post" . ($count != 1 ? "s" : "")." </span> ]";

		}
	}
	?><div>
	<div style='text-align:center;'><h1>Search on Forums</h1> <?php echo ($error ? "[<span style='color : #ff0000; font-weight:bold;'> Nothing Found</span> ]" : $found)?></div>
	<div style="margin-left: 53px; margin-top: 13px;">
		<form method="get" action="forums.php" id="search_form" style="margin: 0pt; padding: 0pt; font-family: Tahoma,Arial,Helvetica,sans-serif; font-size: 11px;">
			<input type="hidden" name="action" value="search" />
				<table border="0" cellpadding="0" cellspacing="0" width="50%">
					<tbody>
						<tr>
							<td valign="top"><span style='font-weight:bold;'>By Keyword:</span></td>
						 </tr>
						 <tr>
							<td valign="top">
								<input type="text" name="keywords" size="65" value="<?php echo $keywords; ?>" /><br /><span style='font-size: xx-small; font-weight:bold;'>Note: Searches <span style="text-decoration: underline;">Only</span> in Posts.</span></td>
							<td valign="top"><input type='submit' class='btn' value='search' /></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div><br />

	<?php

	site_footer();
	exit();
}
else if ($action == 'forumview')
{
	$ovfid = (isset($_GET["forid"]) ? (int)$_GET["forid"] : 0);

	if (!is_valid_id($ovfid))
		error_message("error", "Error", "Invalid ID!");

	$res = sql_query("SELECT name
						FROM overforums
						WHERE id = $ovfid
						ORDER BY sort") or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_assoc($res) or error_message("error", "Sorry", "No Forums with that ID!");

	sql_query("UPDATE users
				SET forum_access = ".sqlesc(get_date_time())."
				WHERE id = {$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);

	site_header("Forums");

	?>
	<h1 align="center"><span style='font-weight:bold;'><a href='forums.php'>Forums</a></span> -> <?php echo htmlspecialchars($arr["name"]); ?></h1>

	<table border='1' cellspacing='0' cellpadding='5' width='<?php echo $forum_width; ?>'>
		<tr>
			<td class='colhead' align='left'>Forums</td>
			<td class='colhead' align='right'>Topics</td>
			<td class='colhead' align='right'>Posts</td>
			<td class='colhead' align='left'>Last post</td>
		</tr>

	<?php

	show_forums($ovfid);

	end_table();

	site_footer();
	exit();
}
else //-- Default action: View forums
{
	if (isset($_GET["catchup"]))
	{
		catch_up();

		header('Location: '.$_SERVER['PHP_SELF']);
		exit();
	}

	sql_query("UPDATE users
				SET forum_access = '".get_date_time()."'
				WHERE id={$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);

	site_header("Forums");

	?><h1 align="center"><span style='font-weight:bold;'><?php echo $site_name; ?> - Forum</span></h1>
	<br />

	<table border='1' cellspacing='0' cellpadding='5' width='<?php echo $forum_width; ?>'><?php

	$ovf_res = sql_query("SELECT id, name, minclassview
							FROM overforums
							ORDER BY sort ASC") or sqlerr(__FILE__, __LINE__);

	while ($ovf_arr = mysql_fetch_assoc($ovf_res))
	{
		if ($CURUSER['class'] < $ovf_arr["minclassview"])
			continue;

		$ovfid = (int)$ovf_arr["id"];
		$ovfname = $ovf_arr["name"];

		?>
		<tr>
			<td align='left' class='colhead' width="100%">
				<a class='altlink_forum' href='forums.php?action=forumview&amp;forid=<?php echo $ovfid; ?>'><span style='font-weight:bold;'><?php echo htmlspecialchars($ovfname); ?></span></a>
			</td>
			<td align='right' class='colhead'><span style='font-weight:bold;'>Topics</span></td>
			<td align='right' class='colhead'><span style='font-weight:bold;'>Posts</span></td>
			<td align='left' class='colhead'><span style='font-weight:bold;'>Last post</span></td>
		</tr>
		<?php

		show_forums($ovfid);
	}
	end_table();

	if ($forum_stats_mod)
		forum_stats();

	?>
	<p align='center'>
	<a href='forums.php?action=search'><span style='font-weight:bold;'>Search Forums</span></a> |
	<a href='forums.php?action=viewunread'><span style='font-weight:bold;'>New Posts</span></a> |
	<a href='forums.php?action=getdaily'><span style='font-weight:bold;'>Todays Posts (Last 24 h.)</span></a> |
	<a href='forums.php?catchup'><span style='font-weight:bold;'>Mark all as read</span></a><?php
	echo ($CURUSER['class'] == MAX_CLASS ? " | <a href='$site_url/forummanage.php#add'><span style='font-weight:bold;'>Forum-Manager</span></a>":"");
	?>
	</p><br />
	<?php

	site_footer();
}

?>