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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_bbcode.php');

db_connect(true);
logged_in();

// Cached latest user - Credits Bigjoos
if ($CURUSER)
{
	$cache_newuser		= ROOT_DIR."cache/newuser.txt";
	$cache_newuser_life = 2 * 60 ; //2 min

	if (file_exists($cache_newuser) && is_array(unserialize(file_get_contents($cache_newuser))) && (time() - filemtime($cache_newuser)) < $cache_newuser_life)

		$arr = unserialize(@file_get_contents($cache_newuser));

	else
	{
		$r_new = sql_query("SELECT id , username
							FROM users
							ORDER BY id DESC
							LIMIT 1 ") or sqlerr(__FILE__, __LINE__);

		$arr = mysql_fetch_assoc($r_new);

		$handle = fopen($cache_newuser, "w+");

		fwrite($handle, serialize($arr));
		fclose($handle);
	}

	$new_user = "&nbsp;<a href=\"$site_url/userdetails.php?id={$arr["id"]}\">" . htmlspecialchars($arr["username"]) . "</a>\n";
}

// Stats Begin - Credits Bigjoos
$cache_stats		= ROOT_DIR."cache/stats.txt";
$cache_stats_life	= 5 * 60; // 5min

if (file_exists($cache_stats) && is_array(unserialize(file_get_contents($cache_stats))) && (time() - filemtime($cache_stats)) < $cache_stats_life)

	$row = unserialize(@file_get_contents($cache_stats));

	else
	{
		$stats = mysql_query("SELECT *, seeders + leechers as peers, seeders / leechers as ratio, unconnectables / (seeders + leechers) as ratiounconn
								FROM stats
								WHERE id = '1' LIMIT 1") or sqlerr(__FILE__, __LINE__);

		$row = mysql_fetch_assoc($stats);

		$handle = fopen($cache_stats, "w+");

		fwrite($handle, serialize($row));
		fclose($handle);
	}

	$seeders		= number_format($row['seeders']);
	$leechers		= number_format($row['leechers']);
	$registered		= number_format($row['regusers']);
	$unverified		= number_format($row['unconusers']);
	$torrents		= number_format($row['torrents']);
	$torrentstoday	= number_format($row['torrentstoday']);
	$ratiounconn	= $row['ratiounconn'];
	$unconnectables	= $row['unconnectables'];
	$ratio			= round(($row['ratio'] * 100));
	$peers			= number_format($row['peers']);
	$numactive		= number_format($row['numactive']);
	$donors			= number_format($row['donors']);
	$forumposts		= number_format($row['forumposts']);
	$forumtopics	= number_format($row['forumtopics']);
// End

site_header();
/*
?>
<span style='font-size: xx-small;'>Welcome to our Newest Member, <span style='font-weight:bold;'><?php echo $new_user?></span>!</span><br />
<?php
*/
if (isset($CURUSER))
{
	print("<table width='100%' class='main' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>");
	print("<h2>Recent News");

	if (get_user_class() >= UC_ADMINISTRATOR)
		print(" - <span style='font-weight:bold; font-size: xx-small;'>[<a class='altlink' href='news.php'>News Page</a>]</span>");
		print("</h2>\n");

	$news_file	= ROOT_DIR."cache/news.txt";
	$expire		=  15 * 60; // 15min

	if (file_exists($news_file) && filemtime($news_file) > (time() - $expire)) {
		$news2 = unserialize(file_get_contents($news_file));
		}
		else
		{
			$res = sql_query("SELECT id, userid, added, body
								FROM news
								WHERE added + ( 3600 *24 *45 ) > ".time()."
								ORDER BY added DESC
								LIMIT 10") or sqlerr(__FILE__, __LINE__);

		while ($news1 = mysql_fetch_assoc($res) )
		{
			$news2[] = $news1;
		}

			$output = serialize($news2);
			$fp		= fopen($news_file,"w");

			fputs($fp, $output);
			fclose($fp);
		}
	if (empty($news2)){
		
	}else
	{
		print("<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'>\n<ul>");
		foreach ($news2 as $array)
	{

		print("<li>" . gmdate("Y-m-d",strtotime($array['added'])) . " - " . format_comment($array['body'],0));
		if (get_user_class() >= UC_ADMINISTRATOR)
		{
			print(" <span style='font-size: x-small; font-weight:bold;'>[<a class='altlink' href='news.php?action=edit&amp;newsid=" . $array['id'] . "&amp;returnto=" . urlencode($_SERVER['PHP_SELF']) . "'>E</a>]</span>");
			echo(" <span style='font-size: x-small; font-weight:bold;'>[<a class='altlink' href='news.php?action=delete&amp;newsid=" . $array['id'] . "&amp;returnto=" . urlencode($_SERVER['PHP_SELF']) . "'>D</a>]</span>");
		}
		print("</li>");
	}
	print("</ul></td></tr></table>\n");
	}
}

?>

<br /><h2>Shoutbox</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="10">
	<tr>
		<td>
<?php

require_once('shout.php');

if ($CURUSER)
{
?>
		</td>
	</tr>
</table>

	<script type="text/javascript" src="js/poll.core.js"></script>
	<script type="text/javascript" src="js/jquery.js"></script>

	<script type="text/javascript">$(document).ready(function(){loadpoll();});</script>

	<h2>Poll
	<?php

if (get_user_class() >= UC_MODERATOR)
	print(" - <span style='font-size: x-small; font-weight:bold;'>[<a class='altlink' href='makepoll.php?returnto=/index.php'>New</a>]</span>\n");
	?>
	</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="10">
	<tr>
		<td align="center">
			<div id="poll_container">
			<div id="loading_poll" style="display:none"></div>
				<noscript>
					<span style='font-weight:bold;'>This Requires Javascript Enabled</span>
				</noscript>
			</div>
			<br/>
		</td>
	</tr>
</table>

<?php
}
?>

<?php
if (isset($CURUSER))
{
?>
	<h2>Stats</h2>
	<table width='100%' border='1' cellspacing='0' cellpadding='10'>
		<tr>
			<td align='center'>
				<table class='main' border='1' cellspacing='0' cellpadding='5'>
					<tr>
						<td class='rowhead'>Registered Users</td><td class='rowhead' align='right'><?php echo $registered?>/<?php echo $max_users?></td>
						<td class='rowhead'>Users Online</td><td class='rowhead' align='right'><?php echo $numactive?></td>
					</tr>
					<tr>
						<td class='rowhead'>Unconfirmed Users</td><td class='rowhead' align='right'><?php echo $unverified?></td>
						<td class='rowhead'>Donors</td><td class='rowhead' align='right'><?php echo $donors?></td>
					</tr>
					<tr>
						<td colspan='4'> </td>
					</tr>
					<tr>
						<td class='rowhead'>Forum Topics</td><td class='rowhead' align='right'><?php echo $forumtopics?></td>
						<td class='rowhead'>Torrents</td><td class='rowhead' align='right'><?php echo $torrents?></td>
					</tr>
					<tr>
						<td class='rowhead'>Forum Posts</td><td class='rowhead' align='right'><?php echo $forumposts?></td>
						<td class='rowhead'>New Torrents Today</td><td class='rowhead' align='right'><?php echo $torrentstoday?></td>
					</tr>
					<tr>
						<td colspan='4'> </td>
					</tr>
					<tr>
						<td class='rowhead'>Peers</td><td class='rowhead' align='right'><?php echo $peers?></td>
						<td class='rowhead'>Unconnectable Peers</td><td class='rowhead' align='right'><?php echo $unconnectables?></td>
					</tr>
					<tr>
						<td class='rowhead'>Seeders</td><td class='rowhead' align='right'><?php echo $seeders?></td>
						<td class='rowhead' align='right'>Unconnectables Ratio (%)</td><td class='rowhead' align='right'><?php echo round($ratiounconn * 100)?></td>
					</tr>
					<tr>
						<td class='rowhead'>Leechers</td><td class='rowhead' align='right'><?php echo $leechers?></td>
						<td class='rowhead'>Seeder/Leecher Ratio (%)</td><td class='rowhead' align='right'><?php echo $ratio?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br />
<?php
}

// users on index - Credits Bigjoos
$active3	= "";
$file		= ROOT_DIR."cache/active.txt";
$expire		= 30; // 30 seconds

if (file_exists($file) && filemtime($file) > (time() - $expire))
{
	$active3 = unserialize(file_get_contents($file));
}
	else
	{
		$dt			= sqlesc(get_date_time(gmtime() - 180));
		$active1	= sql_query("SELECT id, username, class, warned, enabled, added, donor
									FROM users
									WHERE last_access >= $dt
									ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);

		while ($active2 = mysql_fetch_assoc($active1))
	{
		$active3[] = $active2;
	}
		$OUTPUT = serialize($active3);
		$fp		= fopen($file, "w");

		fputs($fp, $OUTPUT);
		fclose($fp);
	} // end else

$activeusers = "";

if (is_array($active3))
	foreach ($active3 as $arr)
	{
		if ($activeusers) $activeusers .= ",\n";
			$activeusers .= "<span style=\"white-space: nowrap;\">";
			$arr["username"] = "<span style='color :#" . get_user_class_color($arr['class']) . "'> " . htmlspecialchars($arr['username']) . "</span>";

			$donator = $arr["donor"] === "yes";
			$warned  = $arr["warned"] === "yes";

		if ($CURUSER)
			$activeusers .= "<a class='altlink_user' href='$site_url/userdetails.php?id={$arr['id']}'><span style='font-weight:bold;'>{$arr["username"]}</span></a>";
		else
			$activeusers .= "<span style='font-weight:bold;'>{$arr['username']}</span>";

		if ($donator)
			$activeusers .= "<img src='".$image_dir."star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />";

		if ($warned)
			$activeusers .= "<img src='".$image_dir."warned.png' width='16' height='16' border='0' alt='Warned' title='Warned' />";
			$activeusers .= "</span>";
	}

$fh = fopen("./cache/active.txt", "r");

$string = file_get_contents("cache/active.txt");
$count	= preg_match_all( '/username/', $string, $dummy );

if (!$activeusers)
	$activeusers = "Sorry - No Users Presently Active ";
	?>
	<h2>Active Users - <?php echo ($count)?> Online</h2>
	<table border='1' cellpadding='10' cellspacing='0' width='100%'>
		<tr class='table'>
			<td class='text'><?php echo $activeusers?></td>
		</tr>
	</table>

<?php
//==Cached Last24 by putyn
function last24hours()
{
	global $CURUSER, $last24cache, $last24record ;

	$last24cache	= ROOT_DIR.'/cache/last24/'.date('dmY').'.txt';
	$last24record	= ROOT_DIR.'/cache/last24record.txt';
	$_last24		= (file_exists($last24cache) ? unserialize(file_get_contents($last24cache)) : array());
	$_last24record	= (file_exists($last24record) ? unserialize(file_get_contents($last24record)) : array('num'=>0,'date'=>0));

	if(!isset($_last24[$CURUSER['id']]) || empty($_last24[$CURUSER['id']]))
	{
		$_last24[$CURUSER['id']] = array($CURUSER['username'],$CURUSER['class']);
		$_newcount = count($_last24);

		if(isset($_last24record['num']) && $_last24record['num']<$_newcount)
		{
			$_last24record['num']  = $_newcount;
			$_last24record['date'] = time();

			file_put_contents($last24record,serialize($_last24record));
		}
			file_put_contents($last24cache,serialize($_last24));
	}
}

//==Cached Last24 by putyn
function las24hours_display()
{
	global $CURUSER, $last24cache, $last24record ;

	$_last24		= (file_exists($last24cache) ? unserialize(file_get_contents($last24cache)) : array());
	$_last24record	= (file_exists($last24record) ? unserialize(file_get_contents($last24record)) : array('num'=>0,'date'=>0));

	$txt = '';

	$str = file_get_contents(ROOT_DIR.'/cache/last24/'.date('dmY').'.txt');
	//$_matches = preg_match_all('/a:2/', $str, $dummy);

	if(!is_array($_last24))
		$txt = 'No 24 Hour Record';
	else
	{
		//$txt .= '<h2>Active Users in the Last 24hrs - ('.$_matches.') </h2>
		$txt .= "<h2>Active Users in the Last 24hrs</h2>
		<table border='1' cellpadding='10' cellspacing='0' width='100%'>
		<tr class='table'>
		<td class='text'><span>";
		$c = count($_last24);
		$i = 0;

		foreach($_last24 as $id=>$username)
		{
			$txt .= '<a class=\'altlink_user\' href=\'./userdetails.php?id='.$id.'\'><span style=\'font-weight:bold; color : #'.get_user_class_color($username[1]).'\'>'.$username[0].'</span></a>'.(($c-1) == $i ? '' : ',')."\n";
			$i++;
		}

		$txt .= '</span></td></tr>';
		$txt .= '<tr class=\'table\'><td class=\'rowhead\' align=\'center\'><span>Most ever visited in 24 hours was '.$_last24record['num'].' Members : '.get_date_time($_last24record['date'],'DATE').' </span></td></tr></table><br />';
	}
	return $txt;
}
last24hours();
print las24hours_display();

if (isset($CURUSER))
{
?>
	<h2>Disclaimer</h2>
	<table width='100%' border='1' cellspacing='0' cellpadding='10'>
		<tr>
			<td align='center'>
				<span style='font-weight:bold;'>None of the files shown here are actually hosted on this server. The links are provided solely by this site's users.  The administrator of this site <span style='color : #ff0000'><?php echo $site_name?></span> cannot be held responsible for what its users post, or any other actions of its users.  You may not use this site to distribute or download any material when you do not have the legal rights to do so.  It is your own responsibility to adhere to these terms.</span>
			</td>
		</tr>
	</table>
<br />

</td></tr></table>

<?php

}

site_footer();

?>