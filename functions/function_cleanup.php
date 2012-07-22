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
*--	  This program is free software; you can redistribute it and /or modify	  --*
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

require_once(INCL_DIR.'function_main.php');

function get_row_count($table, $suffix = "")
{
	if ($suffix)
		$suffix	= "	$suffix";

	($r	= sql_query("SELECT	COUNT(*)
						FROM $table$suffix")) or die(mysql_error());

	($a	= mysql_fetch_row($r)) or die(mysql_error());

	return $a[0];
}

function docleanup()
{
	global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $days, $oldtorrents, $autoclean_interval,	$posts_read_expiry;

	set_time_limit(0);
	ignore_user_abort(1);

	do
	{
		$res = sql_query("SELECT id
							FROM torrents");

		$ar	= array();

		while ($row	= mysql_fetch_array($res,MYSQL_NUM))
		{
			$id	= $row[0];
			$ar[$id] = 1;
		}

		if (!count($ar))
			break;

		$dp	= @opendir($torrent_dir);

		if (!$dp)
			break;

		$ar2 = array();

		while (($file =	readdir($dp)) !== false)
		{
			if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
				continue;

			$id	= $m[1];
			$ar2[$id] =	1;

			if (isset($ar[$id])	&& $ar[$id])
				continue;

			$ff	= $torrent_dir . "/$file";
			unlink($ff);
		}

		closedir($dp);

		if (!count($ar2))
			break;

		$delids	= array();

		foreach	(array_keys($ar) as	$k)
		{
			if (isset($ar2[$k])	&& $ar2[$k])
				continue;

			$delids[] =	$k;

			unset($ar[$k]);
		}

		if (count($delids))
			sql_query("DELETE
						FROM torrents
						WHERE id IN	(" . join(",", $delids)	. ")");

			$res = sql_query("SELECT torrent
								FROM peers
								GROUP BY torrent");

			$delids	= array();

			while ($row	= mysql_fetch_array($res,MYSQL_NUM))
			{
				$id	= $row[0];

				if (isset($ar[$id])	&& $ar[$id])
					continue;

				$delids[] =	$id;
			}

			if (count($delids))
				sql_query("DELETE
							FROM peers
							WHERE torrent IN ("	. join(",",	$delids) . ")");

			$res = sql_query("SELECT torrent
								FROM files
								GROUP BY torrent");

			$delids	= array();
			while ($row	= mysql_fetch_array($res,MYSQL_NUM))
			{
				$id	= $row[0];

				if ($ar[$id])
					continue;

				$delids[] =	$id;
			}

		if (count($delids))
			sql_query("DELETE
						FROM files
						WHERE torrent IN ("	. join(",",	$delids) . ")");
	}
	while (0);

	$deadtime =	deadtime();
	sql_query("DELETE
				FROM peers
				WHERE last_action <	FROM_UNIXTIME($deadtime)");

	$deadtime -= $max_dead_torrent_time;
	sql_query("UPDATE torrents
				SET	visible='no'
				WHERE visible='yes'
				AND	last_action	< FROM_UNIXTIME($deadtime)");

	$deadtime =	time() - $signup_timeout;
	sql_query("DELETE
				FROM users
				WHERE status = 'pending'
				AND	added <	FROM_UNIXTIME($deadtime)
				AND	last_login < FROM_UNIXTIME($deadtime)
				AND	last_access	< FROM_UNIXTIME($deadtime)");

	$torrents =	array();

	$res = sql_query("SELECT torrent, seeder, COUNT(*) AS c
						FROM peers
						GROUP BY torrent, seeder");

	while ($row	= mysql_fetch_assoc($res))
	{
		if ($row["seeder"] == "yes")
			$key = "seeders";
		else
			$key = "leechers";
		$torrents[$row["torrent"]][$key] = $row["c"];
	}

	$res = sql_query("SELECT torrent, COUNT(*) AS c
						FROM comments
						GROUP BY torrent");

	while ($row	= mysql_fetch_assoc($res))
	{
		$torrents[$row["torrent"]]["comments"] = $row["c"];
	}

	$fields	= explode(":", "comments:leechers:seeders");

	$res = sql_query("SELECT id, seeders, leechers,	comments
						FROM torrents");

	while ($row	= mysql_fetch_assoc($res))
	{
		$id		= $row["id"];
		$torr	= $torrents[$id];

		foreach	($fields as	$field)
		{
			if (!isset($torr[$field]))
				$torr[$field] =	0;
		}
		$update	= array();

		foreach	($fields as	$field)
		{
			if ($torr[$field] != $row[$field])
				$update[] =	"$field	= "	. $torr[$field];
		}

		if (count($update))
			sql_query("UPDATE torrents
						SET	" .	implode(",", $update) .	"
						WHERE id = $id");
	}

	//delete inactive user accounts
	$secs		= 42*86400;
	$dt			= sqlesc(get_date_time(gmtime() -	$secs));
	$maxclass	= UC_POWER_USER;

	sql_query("DELETE
				FROM users
				WHERE status='confirmed'
				AND	class <= $maxclass
				AND	last_access	< $dt");

	//delete old login attempts
	$secs	= 1*86400; //	Delete failed login	attempts per one day.
	$dt		= sqlesc(get_date_time(gmtime() -	$secs)); //	calculate date.

	sql_query("DELETE
				FROM loginattempts
				WHERE banned='no'
				AND	added <	$dt");

	//remove expired warnings
	$res = sql_query("SELECT id
						FROM users
						WHERE warned='yes'
						AND	warneduntil	< NOW()
						AND	warneduntil	<> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) > 0)
	{
		$dt		= sqlesc(get_date_time());
		$msg	= sqlesc("Your	warning	has	been removed. Please keep in your best behaviour from now on.\n");

		while ($arr	= mysql_fetch_assoc($res))
		{
			sql_query("UPDATE users
						SET	warned = 'no', warneduntil = '0000-00-00 00:00:00'
						WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

			sql_query("INSERT INTO messages	(sender, receiver, added, msg, poster)
						VALUES(0, $arr[id],	$dt, $msg, 0)")	or sqlerr(__FILE__,	__LINE__);
		}
	}

	// promote power users
	$limit		= 25*1024*1024*1024;
	$minratio	= 1.05;
	$maxdt		= sqlesc(get_date_time(gmtime() - 86400*28));

	$res = sql_query("SELECT id
						FROM users
						WHERE class	= 0
						AND	uploaded >=	$limit
						AND	uploaded / downloaded >= $minratio
						AND	added <	$maxdt") or	sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) > 0)
	{
		$dt		= sqlesc(get_date_time());
		$msg	= sqlesc("Congratulations,	you	have been auto-promoted	to [b]Power	User[/b]. :)\nYou can now download dox over	1 meg and view torrent NFOs.\n");

		while ($arr	= mysql_fetch_assoc($res))
		{
			sql_query("UPDATE users
						SET	class =	1
						WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

			sql_query("INSERT INTO messages	(sender, receiver, added, msg, poster)
						VALUES(0, $arr[id],	$dt, $msg, 0)")	or sqlerr(__FILE__,	__LINE__);
		}
	}

	// demote power	users
	$minratio =	0.95;

	$res = sql_query("SELECT id
						FROM users
						WHERE class	= 1
						AND	uploaded / downloaded <	$minratio")	or sqlerr(__FILE__,	__LINE__);

	if (mysql_num_rows($res) > 0)
	{
		$dt		= sqlesc(get_date_time());
		$msg	= sqlesc("You have	been auto-demoted from [b]Power	User[/b] to	[b]User[/b]	because	your share ratio has dropped below $minratio.\n");

		while ($arr	= mysql_fetch_assoc($res))
		{
			sql_query("UPDATE users
						SET	class =	0
						WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);

			sql_query("INSERT INTO messages	(sender, receiver, added, msg, poster)
						VALUES(0, $arr[id],	$dt, $msg, 0)")	or sqlerr(__FILE__,	__LINE__);
		}
	}

	$registered		= get_row_count('users');
	$unverified		= get_row_count('users', "WHERE	status='pending'");
	$torrents		= get_row_count('torrents');
	$seeders		= get_row_count('peers', "WHERE seeder='yes'");
	$leechers		= get_row_count('peers', "WHERE seeder='no'");
	$torrentstoday	= get_row_count('torrents', 'WHERE added	> DATE_SUB(NOW(), INTERVAL 1 DAY)');
	$donors			= get_row_count('users', "WHERE	donor='yes'");
	$unconnectables	= get_row_count("peers", " WHERE connectable='no'");
	$forumposts		= get_row_count("posts");
	$forumtopics	= get_row_count("topics");
	$dt				= sqlesc(get_date_time(gmtime()	- 300)); //	Active users last 5	minutes
	$numactive		= get_row_count("users",	"WHERE last_access >= $dt");

	sql_query("UPDATE stats
				SET	regusers = '$registered', unconusers = '$unverified', torrents = '$torrents', seeders =	'$seeders',	leechers = '$leechers',	unconnectables = '$unconnectables',	torrentstoday =	'$torrentstoday', donors = '$donors', forumposts = '$forumposts', forumtopics =	'$forumtopics',	numactive =	'$numactive'
				WHERE id = '1'
				LIMIT 1");

	if ($oldtorrents)
	{
		$dt	= sqlesc(get_date_time(gmtime()	- ($days * 86400)));

		$res = sql_query("SELECT id, name
							FROM torrents
							WHERE added	< $dt");

		while ($arr	= mysql_fetch_assoc($res))
		{
				@unlink("$torrent_dir/$arr[id].torrent");
				sql_query("DELETE
							FROM torrents
							WHERE id=$arr[id]");

				sql_query("DELETE
							FROM peers
							WHERE torrent=$arr[id]");

				sql_query("DELETE
							FROM comments
							WHERE torrent=$arr[id]");

				sql_query("DELETE
							FROM files
							WHERE torrent=$arr[id]");

				write_log("Torrent $arr[id]	($arr[name]) was deleted by	system (older than $days days)");
		}
	}
}

?>