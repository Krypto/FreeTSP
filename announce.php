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
require_once(INCL_DIR.'function_benc.php');
require_once(INCL_DIR.'function_vfunctions.php');

function err($msg)
{
	benc_resp(array('failure reason' => array('type' => 'string', 'value' => $msg)));
	exit();
}

function benc_resp($d)
{
	benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
}

function benc_resp_raw($x)
{
	header("Content-Type: text/plain");
	header("Pragma: no-cache");
	print($x);
}

foreach (array("passkey", "info_hash", "peer_id", "ip", "event") AS $x)

$GLOBALS[$x] = '' . $_GET[$x];

foreach (array("port", "downloaded", "uploaded", "left") AS $x)

$GLOBALS[$x] = 0 + $_GET[$x];

if (strpos($passkey, "?"))
{
	$tmp     		= substr($passkey, strpos($passkey, "?"));
	$passkey 	= substr($passkey, 0, strpos($passkey, "?"));
	$tmpname 	= substr($tmp, 1, strpos($tmp, "=")-1);
	$tmpvalue	= substr($tmp, strpos($tmp, "=")+1);

	$GLOBALS[$tmpname] = $tmpvalue;
}

foreach (array("passkey", "info_hash", "peer_id", "port", "downloaded", "uploaded", "left") AS $x)

if (!isset($x)) err('Missing Key: $x');

foreach (array("info_hash","peer_id") AS $x)

if (strlen($GLOBALS[$x]) != 20) err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");

if (strlen($passkey) != 32) err("Invalid Passkey (" . strlen($passkey) . " - $passkey)");

$ip        			= getip();
$port      		= 0 + $port;
$downloaded	= 0 + $downloaded;
$uploaded  	= 0 + $uploaded;
$left      		= 0 + $left;
$rsize     		= 50;

foreach(array("num want", "numwant", "num_want") AS $k)
{
	if (isset($_GET[$k]))
	{
		$rsize = 0 + $_GET[$k];
		break;
	}
}

$agent = $_SERVER["HTTP_USER_AGENT"];

// Deny access made with a browser
if (ereg("^Mozilla\\/", $agent) || ereg("^Opera\\/", $agent) || ereg("^Links ", $agent) || ereg("^Lynx\\/", $agent))
	err("Sorry, this torrent is not Registered with $site_name");

if (!$port || $port > 0xffff)
	err("Invalid Port");

if (!isset($event))
	$event = "";

$seeder = ($left == 0) ? "yes" : "no";

db_connect();

$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(id)
										FROM users
										WHERE passkey=" . sqlesc($passkey)));

if ($valid[0] != 1) err("Invalid Passkey! Download the .torrent file again from $site_url");

$res = mysql_query("SELECT id, banned, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts
					FROM torrents
					WHERE " . hash_where("info_hash", $info_hash));

$torrent = mysql_fetch_assoc($res);

if (!$torrent)
	err("Sorry, this torrent is not Registered with $site_name");

$torrentid = $torrent["id"];

$fields = "seeder, peer_id, ip, port, uploaded, downloaded, userid, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(last_action)) AS announcetime";

$numpeers	= $torrent["numpeers"];
$limit   		= "";

if ($numpeers > $rsize)
	$limit = "ORDER BY RAND() LIMIT $rsize";

$res = mysql_query("SELECT $fields
					FROM peers
					WHERE torrent = $torrentid
					AND connectable = 'yes' $limit");

$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("peers") . "l";

unset($self);
while ($row = mysql_fetch_assoc($res))
{
	$row["peer_id"] = hash_pad($row["peer_id"]);

	if ($row["peer_id"] === $peer_id)
	{
		$userid	= $row["userid"];
		$self  	= $row;
		continue;
	}

	$resp .= "d" .
		benc_str("ip") . benc_str($row["ip"]) .
		benc_str("peer id") . benc_str($row["peer_id"]) .
		benc_str("port") . "i" . $row["port"] . "e" . "e";
}

$resp .= "ee";

$selfwhere = "torrent = $torrentid and " . hash_where("peer_id", $peer_id);

if (!isset($self))
{
	$res = mysql_query("SELECT $fields
						FROM peers
						WHERE $selfwhere");

	$row = mysql_fetch_assoc($res);

	if ($row)
	{
		$userid	= $row["userid"];
		$self  	= $row;
	}
}

/* Start of Upload & Download Stats */

if (!isset($self))
{
	$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(id)
											FROM peers
											WHERE torrent=$torrentid
											AND passkey=" . sqlesc($passkey)));

	if ($valid[0] >= 1 && $seeder == 'no') err("Connection Limit Exceeded! You may Only Leech from One Location at a time.");

	if ($valid[0] >= 3 && $seeder == 'yes') err("Connection Limit Exceeded!");

	$rz = mysql_query("SELECT id, uploaded, downloaded, class
						FROM users
						WHERE passkey=".sqlesc($passkey)."
						AND enabled = 'yes'
						ORDER BY last_access DESC
						LIMIT 1") or err("Tracker Error 2");

	if ($members_only && mysql_num_rows($rz) == 0)
		err("Unknown Passkey. Please redownload the torrent from $site_url.");

	$az = mysql_fetch_assoc($rz);
	$userid = $az["id"];

	if ($az["class"] < UC_USER)
	{
		$gigs   		= $az["uploaded"] / (1024*1024*1024);
		$elapsed	= floor((gmtime() - $torrent["ts"]) / 3600);
		$ratio  		= (($az["downloaded"] > 0) ? ($az["uploaded"] / $az["downloaded"]) : 1);

		if ($ratio < 0.5 || $gigs < 5) $wait = 0;
		elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 0;
		elseif ($ratio < 0.8 || $gigs < 8) $wait = 0;
		elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 0;
		else $wait = 0;

		if ($elapsed < $wait)
			err("Not Authorized (" . ($wait - $elapsed) . "h) - READ THE FAQ!");
	}
}
else
{
	$upthis      			= max(0, $uploaded - $self["uploaded"]);
	$downthis    		= max(0, $downloaded - $self["downloaded"]);
	$upspeed     		= ($upthis > 0 ? $upthis / $self["announcetime"] : 0);
	$downspeed   		= ($downthis > 0 ? $downthis / $self["announcetime"] : 0);
	$announcetime	= ($self["seeder"] == "yes" ? "seedtime = seedtime + $self[announcetime]" : "leechtime = leechtime + $self[announcetime]");

	if ($upthis > 0 || $downthis > 0)
		mysql_query("UPDATE users
						SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis
						WHERE id=$userid") or err("Tracker error 3");
}

/* End of Upload & Download Stats */

function portblacklisted($port)
{
	// Direct Connect
	if ($port >= 411 && $port <= 413) return true;

	// BitTorrent
	if ($port >= 6881 && $port <= 6889) return true;

	// Kazaa
	if ($port == 1214) return true;

	// Gnutella
	if ($port >= 6346 && $port <= 6347) return true;

	// Emule
	if ($port == 4662) return true;

	// WinMX
	if ($port == 6699) return true;

	return false;
}

if (portblacklisted($port))
	err("Port $port is Blacklisted.");
else
{
	$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);

	if (!$sockres)
		$connectable = "no";
	else
	{
		$connectable = "yes";
		@fclose($sockres);
	}
}

$updateset = array();

if (isset($self) && $event == "stopped")
{
	$seeder = 'no';

	mysql_query("DELETE
				FROM peers
				WHERE $selfwhere") or err("D Err");

	if (mysql_affected_rows())
	{
		$updateset[] = ($self["seeder"] == "yes" ? "seeders = seeders - 1" : "leechers = leechers - 1");

		mysql_query("UPDATE snatched
					SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = '".get_date_time()."', seeder = '$seeder', agent = ".sqlesc($agent)."
					WHERE torrentid = $torrentid
					AND userid = $userid") or err("SL Err 1");
	}
}
elseif (isset($self))
{
	if ($event == "completed")
	{
		$updateset[]	= "times_completed = times_completed + 1";
		$finished   		= ", finishedat = UNIX_TIMESTAMP()";
		$finished1  		= ", complete_date = '".get_date_time()."'";
	}

	mysql_query("UPDATE peers
				SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = NOW(), seeder = '$seeder', agent = ".sqlesc($agent)." $finished
				WHERE $selfwhere") or err("PL Err 1");

	if (mysql_affected_rows())
	{
		if ($seeder <> $self["seeder"])
			$updateset[] = ($seeder == "yes" ? "seeders = seeders + 1, leechers = leechers - 1" : "seeders = seeders - 1, leechers = leechers + 1");

			mysql_query("UPDATE snatched
						SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = '".get_date_time()."', seeder = '$seeder', agent = ".sqlesc($agent)." $finished1
						WHERE torrentid = $torrentid
						AND userid = $userid") or err("SL Err 2");
	}
}
else
{
	mysql_query("INSERT INTO peers (torrent, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, started, last_action, seeder, agent, downloadoffset, uploadoffset, passkey)
				VALUES ($torrentid, $userid, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, NOW(), NOW(), '$seeder', ".sqlesc($agent).", $downloaded, $uploaded, ".sqlesc(unesc($passkey)).")") or err("PL Err 2");

	if (mysql_affected_rows())
	{
		$updateset[] = ($seeder == "yes" ? "seeders = seeders + 1" : "leechers = leechers + 1");

		mysql_query("UPDATE snatched
					SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', to_go = $left, last_action = '".get_date_time()."', seeder = '$seeder', agent = ".sqlesc($agent)."
					WHERE torrentid = $torrentid
					AND userid = $userid") or err("SL Err 3");

	if (!mysql_affected_rows() && $seeder == "no")

		mysql_query("INSERT INTO snatched (torrentid, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, start_date, last_action, seeder, agent)
					VALUES ($torrentid, $userid, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, '".get_date_time()."', '".get_date_time()."', '$seeder', ".sqlesc($agent).")") or err("SL Err 4");
	}
}

if ($seeder == "yes")
{
	if ($torrent["banned"] != "yes")
		$updateset[] = "visible = 'yes'";
		$updateset[] = "last_action = NOW()";
}

if (count($updateset))
	mysql_query("UPDATE torrents
				SET " . join(",", $updateset) . "
				WHERE id = $torrentid");

benc_resp_raw($resp);

?>