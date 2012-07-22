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

db_connect();
logged_in();

if (get_user_class() < UC_POWER_USER)
	error_message("warn", "Warning", "Permission Denied");

function usertable($res, $frame_caption)
{
	global $CURUSER;

	begin_frame($frame_caption,	true);
	begin_table();
?>
		<tr>
			<td	class='colhead'>Rank</td>
			<td	class='colhead'	align='left'>User</td>
			<td	class='colhead'>Uploaded</td>
			<td	class='colhead'	align='left'>UL	Speed</td>
			<td	class='colhead'>Downloaded</td>
			<td	class='colhead'	align='left'>DL	Speed</td>
			<td	class='colhead'	align='right'>Ratio</td>
			<td	class='colhead'	align='left'>Joined</td>
		</tr>

<?php
		$num = 0;

		while ($a =	mysql_fetch_assoc($res))
		{
			++$num;
			$highlight = $CURUSER["id"]	== $a["userid"]	? "	bgcolor='#BBAF9B'" : "";

			if ($a["downloaded"])
				{
					$ratio = $a["uploaded"]	/ $a["downloaded"];
					$color = get_ratio_color($ratio);
					$ratio = number_format($ratio, 2);

					if ($color)
						$ratio = "<span	style='color : $color'>$ratio</span>";
				}
			else
				$ratio = "Inf.";
				echo("<tr $highlight><td class='rowhead' align='center'>$num</td><td class='rowhead' align='left' $highlight><a	href='userdetails.php?id=" .
					$a["userid"] . "'><span	style='font-weight:bold;'>"	. $a["username"] . "</span></a>" .
					"</td><td class='rowhead' align='right'	$highlight>" . mksize($a["uploaded"]) .
					"</td><td class='rowhead' align='right'	$highlight>" . mksize($a["upspeed"]) . "/s"	.
					 "</td><td class='rowhead' align='right	'$highlight>" .	mksize($a["downloaded"]) .
					 "</td><td class='rowhead' align='right	'$highlight>" .	mksize($a["downspeed"])	. "/s" .
					"</td><td class='rowhead' align='right'	$highlight>" . $ratio .
					"</td><td class='rowhead' align='left'>" . gmdate("Y-m-d",strtotime($a['added'])) .	" (" .
					get_elapsed_time(sql_timestamp_to_unix_timestamp($a['added'])) . " ago)</td></tr>");
				}
end_table();
end_frame();
}

	function _torrenttable($res, $frame_caption)
	{
		begin_frame($frame_caption, true);
		begin_table();
	?>
		<tr>
			<td class='colhead' align='center'>Rank</td>
			<td class='colhead' align='left'>Name</td>
			<td class='colhead' align='right'>Sna.</td>
			<td class='colhead' align='right'>Data</td>
			<td class='colhead' align='right'>Se.</td>
			<td class='colhead' align='right'>Le.</td>
			<td class='colhead' align='right'>To.</td>
			<td class='colhead' align='right'>Ratio</td>
		</tr>
	<?php
	$num = 0;
	while ($a = mysql_fetch_assoc($res))
	{
		++$num;
		if ($a["leechers"])
		{
			$r = $a["seeders"] / $a["leechers"];
			$ratio = "<span style='color : '"	. get_ratio_color($r) .	"'>" . number_format($r, 2)	. "</span>";
		}
		else
			$ratio = "Inf.";
		echo("<tr><td class='rowhead' align='center'>$num</td><td class='rowhead' align='left'><a href='details.php?id=" . $a["id"]	. "&hit=1'><span style='font-weight:bold;'>" .
			$a["name"] . "</span></a></td><td	class='rowhead'	align='right'>"	. number_format($a["times_completed"]) .
			"</td><td class='rowhead' align='right'>" .	mksize($a["data"]) . "</td><td align='right'>" . number_format($a["seeders"]) .
			"</td><td	class='rowhead'	align='right'>"	. number_format($a["leechers"])	. "</td><td	align='right'>"	. ($a["leechers"] +	$a["seeders"]) .
			"</td><td	class='rowhead'	align='right'>$ratio</td>\n");
		}
		end_table();
		end_frame();
	}

function countriestable($res, $frame_caption, $what)
{
	global $image_dir;

	begin_frame($frame_caption, true);
	begin_table();
	?>
	<tr>
		<td class='colhead'>Rank</td>
		<td class='colhead' align='left'>Country</td>
		<td class='colhead' align='right'><?php echo $what?></td>
	</tr>
	<?php
	$num = 0;
	while ($a =	mysql_fetch_assoc($res))
	{
	++$num;

	if ($what == "Users")
		$value = number_format($a["num"]);

	elseif ($what == "Uploaded")
		$value = mksize($a["ul"]);

	elseif ($what == "Average")
		$value = mksize($a["ul_avg"]);

	elseif ($what == "Ratio")
		$value = number_format($a["r"],2);

	echo("<tr>
			<td class='rowhead' align='center'>$num</td>
			<td class='rowhead' align='left'>
				<table border='0' class='main' cellspacing='0' cellpadding='0'>
					<tr>
						<td class='embedded'><img style='text-align: center;' src='{$image_dir}flag/{$a['flagpic']}' width='32' height='20' alt='{$a['name']}' title='{$a['name']}' /></td>
						<td class='embedded' style='padding-left: 5px'>{$a['name']}</td>
					</tr>
				</table>
			</td>
			<td class='rowhead' align='right'>$value</td>
		</tr>\n");
	}
	end_table();
	end_frame();
}

site_header("Top Ten");

?>

<script	type='text/javascript' src='js/content_glider.js'></script>
<script	type='text/javascript'>

featuredcontentglider.init(
{
	gliderid: 'FreeTSPtopten', //ID	of main	glider container
	contentclass: 'FreeTSPglidecontent5', //Shared CSS class name of each glider content
	togglerid: 'FreeTSP5', //ID	of toggler container
	remotecontent: '', //Get gliding contents from external	file on	server?	"filename" or "" to	disable
	selected: 0, //Default selected	content	index (0=1st)
	persiststate: false, //Remember	last content shown within browser session (true/false)?
	speed: 700,	//Glide	animation duration (in milliseconds)
	direction: 'downup'	//set direction	of glide: "updown",	"downup", "leftright", or "rightleft"
}
)

</script>

<br	/><br />
<div style="text-align:	center;">
<span style="font-size:	small; ">Welcome to<br/><span style='font-weight:bold;'><?php echo $site_name?>.</span><br/>Top	Ten	Menu<br/></span>
</div>
<br	/><br />

<table width='100%'	cellpadding='4'>
	<tr>
		<td	class='std'	align='center'>
			<div id='FreeTSP5' class='FreeTSPglidecontenttoggler5'>
				<div style="text-align:	center;	text-decoration: underline;">Members</div><br/>
				<a href='#'	class='toc'>Uploaders</a>
				<a href='#'	class='toc'>Fastest	Uploaders</a>
				<a href='#'	class='toc'>Downloaders</a>
				<a href='#'	class='toc'>Fastest	Downloaders</a>
				<a href='#'	class='toc'>Best Sharers</a>
				<a href='#'	class='toc'>Worst Sharers</a>
				<br/><br/>
				<div style="text-align:	center;	text-decoration: underline;">Torrents</div><br/>
				<a href='#'	class='toc'>Most Active</a>
				<a href='#'	class='toc'>Most Snatched</a>
				<a href='#'	class='toc'>Most Data Transferred</a>
				<a href='#'	class='toc'>Best Seeded</a>
				<a href='#'	class='toc'>Worst Seeded</a>
				<br/><br/>
				<div style="text-align:	center;	text-decoration: underline;">Countries</div><br/>
				<a href='#'	class='toc'>Members</a>
				<a href='#'	class='toc'>Total Uploaded</a>
				<a href='#'	class='toc'>Average	Total Uploaded</a>
				<a href='#'	class='toc'>Ratio</a>
			</div><br/>
		</td>
	</tr>
</table>

<div id='FreeTSPtopten'	class='FreeTSPglidecontentwrapper5'>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Uploaders</td>
			</tr>
		</table><br/>
<?php
		$pu	= get_user_class() >= UC_POWER_USER;

	if (!$pu)
		$limit = 10;

	$mainquery = "SELECT id	as userid, username, added,	uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW())	- UNIX_TIMESTAMP(added)) AS	upspeed, downloaded	/ (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed
					FROM users
					WHERE enabled = 'yes'";

	$limit = 10;
	$subtype ='';

	if ($limit == 10 ||	$subtype ==	"ul")
	{
		$order	= "uploaded DESC";
		$r		= sql_query($mainquery . " ORDER	BY $order "	. "	LIMIT $limit") or sqlerr();

		usertable($r, "" . ($limit == 10 &&	$pu	? "" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Fastest Uploaders	(average, includes inactive	time)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"uls")
	{
		$order	= "upspeed DESC";
		$r		= sql_query($mainquery . " ORDER BY $order "	. "	LIMIT $limit") or sqlerr();

		usertable($r, "" . ($limit == 10 &&	$pu	? "" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Downloaders</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"dl")
	{
		$order	= "downloaded DESC";
		$r		= sql_query($mainquery ." ORDER	BY $order "	. "	LIMIT $limit") or sqlerr();

		usertable($r, "" . ($limit == 10 &&	$pu	? "" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Fastest Downloaders (average,	includes inactive time)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"dls")
	{
		$order	= "downspeed	DESC";
		$r		= sql_query($mainquery ." ORDER	BY $order "	. "	LIMIT $limit") or sqlerr();

		usertable($r, "" . ($limit == 10 &&	$pu	? "" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Best Sharers (with minimum 1 GB downloaded)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"bsh")
	{
		$order		= "uploaded	/ downloaded DESC";
		$extrawhere	= "	and	downloaded > 1073741824";
		$r			= sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();

		usertable($r, "" . ($limit == 10 &&	$pu	? "" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Worst	Sharers	(with minimum 1	GB downloaded)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"wsh")
	{
		$order		= "uploaded	/ downloaded ASC, downloaded DESC";
		$extrawhere	= "	and	downloaded > 1073741824";
		$r			= sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();

		usertable($r, "" . ($limit == 10 &&	$pu	? "" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Most Active Torrents</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"act")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
						FROM torrents AS t
						LEFT JOIN peers AS p ON t.id = p.torrent
						WHERE p.seeder = 'no'
						GROUP BY t.id
						ORDER BY seeders + leechers	DESC, seeders DESC,	added ASC
						LIMIT	$limit") or	sqlerr();

		_torrenttable($r,	"" . ($limit ==	10 && $pu ?	"" : ""));
	}
?>
</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Most Snatched	Torrents</td>
			</tr>
	</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"sna")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded))	AS data
						FROM torrents AS t
						LEFT JOIN peers AS p ON t.id = p.torrent
						WHERE p.seeder = 'no'
						GROUP BY t.id
						ORDER BY times_completed DESC
						LIMIT	$limit") or	sqlerr();

		_torrenttable($r,	"" . ($limit ==	10 && $pu ?	"" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Most Data	Transferred	Torrents</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"mdt")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
						FROM torrents AS t
						LEFT JOIN peers AS p ON t.id = p.torrent
						WHERE p.seeder = 'no' and leechers >= 5	AND	times_completed	> 0
						GROUP BY t.id
						ORDER BY data DESC, added ASC
						LIMIT $limit") or sqlerr();

		_torrenttable($r,	"" . ($limit ==	10 && $pu ?	"" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Best Seeded Torrents (with minimum 5 seeders)</td>
			</tr>
		</table><br/>
<?php

		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"bse")
	{
		$r	= sql_query("SELECT	t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
						FROM torrents AS t
						LEFT JOIN peers AS p ON t.id = p.torrent
						WHERE p.seeder = 'no'
						AND	seeders	>= 5
						GROUP BY t.id
						ORDER BY seeders / leechers DESC, seeders DESC, added ASC
						LIMIT $limit")	or sqlerr();

		_torrenttable($r, "" . ($limit == 10 &&	$pu	? "" : ""));
	}

?>
</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Worst	Seeded Torrents	(with minimum 5	leechers, excluding	unsnatched torrents)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"wse")
	{
		$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data
							FROM torrents AS t
							LEFT JOIN peers AS p ON t.id = p.torrent
							WHERE p.seeder = 'no'
							AND leechers >= 5
							AND	times_completed	> 0
							GROUP BY t.id
							ORDER BY seeders / leechers ASC, leechers DESC
							LIMIT $limit")	or sqlerr();

		_torrenttable($r,	"" . ($limit ==	10 && $pu ?	"" : ""));
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Countries	(memebers)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"us")
	{
		$r = sql_query("SELECT name, flagpic, COUNT(users.country) AS num
						FROM countries
						LEFT JOIN users ON users.country = countries.id
						GROUP BY name
						ORDER BY num DESC
						LIMIT $limit") or sqlerr();

		countriestable($r, ""	. ($limit == 10	&& $pu ? ""	: ""),"Users");
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Countries	(total uploaded)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"ul")
	{
		$r = sql_query("SELECT c.name, c.flagpic,	sum(u.uploaded)	AS ul
						FROM users AS u
						LEFT JOIN countries as c ON u.country = c.id
						WHERE u.enabled = 'yes'
						GROUP BY c.name
						ORDER BY ul DESC
						LIMIT $limit") or sqlerr();

		countriestable($r, ""	. ($limit == 10	&& $pu ? ""	: ""),"Uploaded");
	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Countries	(average total uploaded	per	member,	with minimum 1TB uploaded and 100 members)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"avg")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/count(u.id) AS ul_avg FROM users AS u
						LEFT JOIN countries AS c ON u.country = c.id
						WHERE u.enabled = 'yes'
						GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776
						AND count(u.id) >= 100
						ORDER BY ul_avg DESC
						LIMIT	$limit") or	sqlerr();

		countriestable($r, ""	. ($limit == 10	&& $pu ? ""	: ""),"Average");

	}
?>
	</div>
	<div class='FreeTSPglidecontent5'>
		<table width='100%'	cellpadding='4'>
			<tr>
				<td	class='colhead'	align='center'>Top 10 Countries	(ratio,	with minimum 1TB uploaded, 1TB downloaded and 100 members)</td>
			</tr>
		</table><br/>
<?php
		$limit = 10;

	if ($limit == 10 ||	$subtype ==	"r")
	{
		$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/sum(u.downloaded) as r
						FROM users as u
						LEFT JOIN countries as c ON u.country = c.id
						WHERE u.enabled	= 'yes'
						GROUP BY c.name	HAVING sum(u.uploaded) > 1099511627776
						AND sum(u.downloaded) > 1099511627776
						AND count(u.id) >=	100
						ORDER BY r DESC
						LIMIT $limit") or sqlerr();

		countriestable($r, ""	. ($limit == 10	&& $pu ? ""	: ""),"Ratio");
	}
?>
	</div>
</div>

<?php

site_footer();

?>