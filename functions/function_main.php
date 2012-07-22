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

//==Start execution time
$qtme['start'] = microtime(true);
//==End

/////////Strip slashes by system//////////
function cleanquotes(&$in)
{
    if(is_array($in)) return array_walk($in,'cleanquotes');
		return $in=stripslashes($in);
}

if(get_magic_quotes_gpc())
{
	array_walk($_GET,'cleanquotes');
    array_walk($_POST,'cleanquotes');
    array_walk($_COOKIE,'cleanquotes');
    array_walk($_REQUEST,'cleanquotes');
}

function local_user()
{
  return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"];
}

define('INCL_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('ROOT_DIR', realpath(INCL_DIR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);

require_once(INCL_DIR.'function_cleanup.php');
require_once(INCL_DIR.'function_config.php');

if ( strtoupper( substr(PHP_OS, 0, 3) ) == 'WIN' )
{
	$file_path = str_replace( "\\", "/", dirname(__FILE__) );
    $file_path = str_replace( "/functions", "", $file_path );
}
else
{
    $file_path = dirname(__FILE__);
    $file_path = str_replace( "/functions", "", $file_path );
}

//define('ROOT_PATH', $file_path);

//Do not modify -- versioning system
//This will help identify code for support issues at freetsp.info
define ('FTSP','FreeTSP');

function copyright()
{
    global $curversion;

    echo("Powered by <a href='http://www.freetsp.info'>".  FTSP ." Version " . $curversion . "</a> &copy; <a href='http://www.freetsp.info'>" . FTSP . "</a> " . (date ( "Y" ) > 2010 ? "2010-" : "") . date ( "Y" )) ;
}

/**** validip/getip courtesy of manolete <manolete@myway.com> ****/
// IP Validation
function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','0.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}

// Patched function to detect REAL IP address if it's valid
function getip()
{
	if (isset($_SERVER))
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}

		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	}
	else
	{
		if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR')))
		{
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}

		elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP')))
		{
			$ip = getenv('HTTP_CLIENT_IP');
		}
		else
		{
			$ip = getenv('REMOTE_ADDR');
		}
	}
	return $ip;
}

function db_connect($autoclean = false)
{
	global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    {
		switch (mysql_errno())
		{
			case 1040:
			case 2002:

			if ($_SERVER['REQUEST_METHOD'] == "GET")
				die("<html><head><meta http-equiv='refresh' content='5 $_SERVER[REQUEST_URI]'></head><body><table width='100%' height='100%' border='0'><tr><td><h3 align='center'>The server load is very high at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
			else
				die("Too many users. Please press the Refresh button in your browser to retry.");
			default:

    	    die("[" . mysql_errno() . "] db_connect: mysql_connect: " . mysql_error());
		}
	}
    mysql_select_db($mysql_db)
        or die('db_connect: mysql_select_db: ' + mysql_error());

    userlogin();

    if ($autoclean)
		register_shutdown_function("autoclean");
}

function userlogin() {
    global $site_online;
    unset($GLOBALS["CURUSER"]);

	$ip = getip();
	$nip = ip2long($ip);
    $res = mysql_query("SELECT *
                       FROM bans
                       WHERE $nip >= first
                       AND $nip <= last") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {
      header("HTTP/1.0 403 Forbidden");
      print("<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n");
      die;
    }

    if (!$site_online || empty($_COOKIE["uid"]) || empty($_COOKIE["pass"]))
		return;

    $id = 0 + $_COOKIE["uid"];

    if (!$id || strlen($_COOKIE["pass"]) != 32)
        return;

    $res = mysql_query("SELECT *
						FROM users
						WHERE id = $id
						AND enabled='yes'
						AND status = 'confirmed'");// or die(mysql_error());

    $row = mysql_fetch_array($res);

    if (!$row)
        return;

    $sec = hash_pad($row["secret"]);

    if ($_COOKIE["pass"] !== $row["passhash"])
        return;

    $time = time();

    if($time - $row['last_access_numb'] < 300)
    {
        $onlinetime = time() - $row['last_access_numb'];
        $userupdate[] = "onlinetime = onlinetime + " . sqlesc($onlinetime);
    }

    $userupdate[] = "last_access_numb = " . sqlesc($time);
    $userupdate[] = "last_access = " . sqlesc(get_date_time());
    $userupdate[] = "ip = " . sqlesc($ip);

    sql_query("UPDATE users
              SET "  .implode(", ", $userupdate)."
              WHERE id=" . $row["id"]);

    $row['ip'] = $ip;
    $GLOBALS["CURUSER"] = $row;
}

function autoclean()
{
    global $autoclean_interval;

    $now = time();
    $docleanup = 0;

    $res = sql_query("SELECT value_u
                     FROM avps
                     WHERE arg = 'lastcleantime'");

    $row = mysql_fetch_array($res);

    if (!$row)
	{
        sql_query("INSERT INTO avps (arg, value_u)
                  VALUES ('lastcleantime',$now)");

        return;
    }

    $ts = $row[0];

    if ($ts + $autoclean_interval > $now)
        return;

    sql_query("UPDATE avps
              SET value_u=$now
              WHERE arg='lastcleantime'
              AND value_u = $ts");

    if (!mysql_affected_rows())
        return;

    docleanup();
}

function unesc($x)
{
	if (get_magic_quotes_gpc())
        return stripslashes($x);

    return $x;
}

function mksize($bytes)
{
	if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " kB";

	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " MB";

	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " GB";

	else
		return number_format($bytes / 1099511627776, 2) . " TB";
}

function mksizeint($bytes)
{
	$bytes = max(0, $bytes);

	if ($bytes < 1000)
		return floor($bytes) . " B";

	elseif ($bytes < 1000 * 1024)
		return floor($bytes / 1024) . " kB";

	elseif ($bytes < 1000 * 1048576)
		return floor($bytes / 1048576) . " MB";

	elseif ($bytes < 1000 * 1073741824)
		return floor($bytes / 1073741824) . " GB";

	else
		return floor($bytes / 1099511627776) . " TB";
}

function deadtime()
{
    global $announce_interval;

    return time() - floor($announce_interval * 1.3);
}

function mkprettytime($s)
{
    if ($s < 0)
        $s = 0;

    $t = array();

    foreach (array("60:sec", "60:min", "24:hour", "0:day") as $x)
	{
        $y = explode(":", $x);

        if ($y[0] > 1)
		{
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        }
        else
            $v = $s;

        $t[$y[1]] = $v;
    }

    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);

    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);


	return sprintf("%d:%02d", $t["min"], $t["sec"]);
}

function mkglobal($vars)
{
    if (!is_array($vars))
        $vars = explode(":", $vars);

    foreach ($vars as $v)
	{
        if (isset($_GET[$v]))
            $GLOBALS[$v] = unesc($_GET[$v]);

        elseif (isset($_POST[$v]))
            $GLOBALS[$v] = unesc($_POST[$v]);

        else
            return 0;
    }
    return 1;
}

function validfilename($name)
{
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email)
{
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}

function sqlesc($x)
{
    return "'".mysql_real_escape_string($x)."'";
}

function sqlwildcardesc($x)
{
    return str_replace(array("%", "_"), array("\\%", "\\_"), mysql_real_escape_string($x));
}

function urlparse($m)
{
    $t = $m[0];

    if (preg_match(',^\w+://,', $t))
        return "<a href='$t'>$t</a>";

    return "<a href='http://$t'>$t</a>";
}

function parsedescr($d, $html)
{
    if (!$html)
    {
      $d = htmlspecialchars($d);
      $d = str_replace("\n", "\n<br />", $d);
    }
    return $d;
}

function site_header($title = "", $msgalert = true)
{
    global $CURUSER, $site_online, $FUNDS, $site_name, $image_dir;

	//header('Content-Type: text/html; charset=utf-8');

	if (!$site_online)
		die("Site is down for Maintenance. Please check back again later. Thank You.<br />");

    if ($title == "")
        $title = $site_name .(isset($_GET['ftsp'])?" (".FTSPVERSION.")":'');
    else
        $title = $site_name .(isset($_GET['ftsp'])?" (".FTSPVERSION.")":''). " :: " . htmlspecialchars($title);

	if ($CURUSER)
	{
		$ss_a = @mysql_fetch_array(mysql_query("SELECT uri
												FROM stylesheets
												WHERE id=" . $CURUSER["stylesheet"]));

		if ($ss_a) $ss_uri = $ss_a["uri"];
	}

	if (!$ss_uri)
	{
		($r = mysql_query("SELECT uri
							FROM stylesheets
							WHERE id=1")) or die(mysql_error());

		($a = mysql_fetch_array($r)) or die(mysql_error());

		$ss_uri = $a["uri"];
	}

	if ($msgalert && $CURUSER)
	{
		$res = mysql_query("SELECT COUNT(id)
		                   FROM messages
		                   WHERE receiver=" . $CURUSER["id"] . " && unread='yes'") or die("OopppsY!");

		$arr = mysql_fetch_row($res);

		$unread = $arr[0];
	}

	require_once "stylesheets/".$ss_uri."/site_header.php";

	if (isset($unread) && !empty($unread))
	{
		//print("<table cellspacing='0' cellpadding='10' border='0' bgcolor='red'><tr><td style='padding: 10px; background: red'>\n");
		//print("<a href='messages.php'><span style='color : #ffffff; font-weight:bold;'>You have $unread new message" . ($unread > 1 ? "s" : "") . "!</span></a>");
		//print("</td></tr></table>\n");
		print("<div align='center'>");
		print("<div class='silver mail round small inset'>");
		print("<p><strong>You Have Mail</strong>");
		print("<br /><a href='messages.php'>&nbsp;&nbsp;&nbsp;&nbsp;<span class='emphasis'>You have $unread New Message" . ($unread > 1 ? "s" : "") . "</span></a></p>");
		print("<div class='shadow-out'></div>");
		print("</div>");
		print("</div>");
	}
}

function site_footer()
{
	global $CURUSER;

	if ($CURUSER)
	{
		$ss_a = @mysql_fetch_array(mysql_query("SELECT uri
												FROM stylesheets
												WHERE id=" . $CURUSER["stylesheet"]));

		if ($ss_a) $ss_uri = $ss_a["uri"];
	}

	if (!$ss_uri)
	{
		($r = mysql_query("SELECT uri
							FROM stylesheets
							WHERE id=1")) or die(mysql_error());

		($a = mysql_fetch_array($r)) or die(mysql_error());

		$ss_uri = $a["uri"];
	}

	require_once("stylesheets/" . $ss_uri . "/site_footer.php");
}

function mksecret($len = 20)
{
    $ret = "";

    for ($i = 0; $i < $len; $i++)
        $ret .= chr(mt_rand(0, 255));

    return $ret;
}

function httperr($code = 404)
{
    header("HTTP/1.0 404 Not found");

    print("<h1>Not Found!</h1>\n");
    print("<p>Sorry.</p>\n");

    exit();
}

function gmtime()
{
    return strtotime(get_date_time());
}

function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff)
{
	setcookie("uid", $id, $expires, "/");
	setcookie("pass", $passhash, $expires, "/");

  if ($updatedb)
 	sql_query("UPDATE users
 	          SET last_login = NOW()
 	          WHERE id = $id");
}

function logoutcookie()
{
    setcookie("uid", "", 0x7fffffff, "/");
    setcookie("pass", "", 0x7fffffff, "/");
}

function logged_in()
{
    global $CURUSER, $site_url;
    if (!$CURUSER)
	{
        header("Location: $site_url/login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));

        exit();
    }
}

// Returns the current time in GMT in MySQL compatible format.
function get_date_time($timestamp = 0)
{
	if ($timestamp)
		return date("Y-m-d H:i:s", $timestamp);
	else
		return gmdate("Y-m-d H:i:s");
}

function sqlerr($file = '', $line = '')
{
	error_message("error", "SQL Error", "" . mysql_error() . ($file != '' && $line != '' ? "in $file, line $line" : "") . "");
}

function StatusBar()
{
	global $CURUSER, $image_dir;

	if (!$CURUSER)
		return "";

	$upped = mksize($CURUSER['uploaded']);

	$downed = mksize($CURUSER['downloaded']);

	$ratio = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded']/$CURUSER['downloaded'] : 0;

	$ratio = number_format($ratio, 2);

	$IsDonor = '';

	if ($CURUSER['donor'] == "yes")

		$IsDonor = "<img src='".$image_dir."star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />";

	$warn = '';

	if ($CURUSER['warned'] == "yes")

		$warn = "<img src='".$image_dir."warned.png' width='15' height='16' border='0' alt='Warned' title='Warned' />";

	$res1 = mysql_query("SELECT COUNT(id)
	                    FROM messages
	                    WHERE receiver=" . $CURUSER["id"] . "
	                    AND unread='yes'") or print(mysql_error());

	$arr1 = mysql_fetch_row($res1);

	$unread = $arr1[0];

	$inbox = ($unread == 1 ? "$unread&nbsp;New Message" : "$unread&nbsp;New Messages");


	$res2 = mysql_query("SELECT seeder, COUNT(id) AS pCount
	                    FROM peers
	                    WHERE userid=".$CURUSER['id']."
	                    GROUP BY seeder") or print(mysql_error());

	$seedleech = array('yes' => '0', 'no' => '0');

	while( $row = mysql_fetch_assoc($res2) )
	{
		if($row['seeder'] == 'yes')
			$seedleech['yes'] = $row['pCount'];
		else
			$seedleech['no'] = $row['pCount'];
	}

	$StatusBar = '';

	$StatusBar = "<tr>".
		"<td colspan='2' style='padding: 2px;'>".
		"<div id='statusbar'>".
		"<p class='home'>Welcome back, <a class='altlink_user' href='userdetails.php?id=".$CURUSER['id']."'><span style='color : #" . get_user_class_color($CURUSER['class']) . "'> ". htmlspecialchars($CURUSER['username']) . "</span></a>".
		"$IsDonor$warn&nbsp; [<a href='logout.php'>Logout</a>] Reputation Points  : ".$CURUSER['reputation']."</p>".
		"<p>".date(DATE_RFC822)."</p><p>";

	$StatusBar .= "".
		"</p><p class='home'>Ratio:$ratio".
		"&nbsp;&nbsp;Uploaded:$upped".
		"&nbsp;&nbsp;Downloaded:$downed".
		"&nbsp;&nbsp;Active Torrents:&nbsp;<img src='".$image_dir."up.png' width='9' height='7' border='0' alt='Torrents Seeding' title='Torrents Seeding' />&nbsp;{$seedleech['yes']}".
		"&nbsp;&nbsp;<img src='".$image_dir."dl.png' width='9' height='7' border='0' alt='Torrents Leeching' title='Torrents Leeching' />&nbsp;{$seedleech['no']}</p>";

	$StatusBar .= "<p>".
	"<a href='messages.php'>$inbox</a>".
	"</p></div></td></tr>";

	return $StatusBar;
}

//==Sql query count
$qtme['querytime'] = 0;

function sql_query($querytme)
{
	global $queries, $qtme, $querytime, $query_stat;

	$qtme = isset($qtme) && is_array($qtme) ? $qtme : array();
	$qtme['query_stat']= isset($qtme['query_stat']) && is_array($qtme['query_stat']) ? $qtme['query_stat'] : array();

    $queries++;
    $query_start_time  = microtime(true); // Start time
    $result            = mysql_query($querytme);
    $query_end_time    = microtime(true); // End time
    $query_time        = ($query_end_time - $query_start_time);
    $querytime = $querytime + $query_time;
    $qtme['querytime']    = (isset($qtme['querytime']) ? $qtme['querytime'] : 0) + $query_time;
    $query_time        = substr($query_time, 0, 8);
    $qtme['query_stat'][] = array('seconds' => $query_time, 'query' => $querytme);
    return $result;
    }

require_once(INCL_DIR.'function_global.php');
?>