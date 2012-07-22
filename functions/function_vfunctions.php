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

function pager($rpp, $count, $href,	$opts =	array())
{
	$pages = ceil($count / $rpp);

	if (!$opts["lastpagedefault"])
		$pagedefault = 0;
	else
	{
		$pagedefault = floor(($count - 1) /	$rpp);

		if ($pagedefault < 0)
		$pagedefault = 0;
	}

	if (isset($_GET["page"]))
	{
		$page =	0 +	$_GET["page"];

		if ($page <	0)
			$page =	$pagedefault;
	}
	else
		$page	= $pagedefault;
		$pager	= "";
		$mp		= $pages - 1;
		$as		= "<span style='font-weight:bold;'>&lt;&lt;&nbsp;Prev</span>";

		if ($page >= 1)
		{
			$pager .= "<a href='{$href}page=" .	($page - 1)	. "'>";
			$pager .= $as;
			$pager .= "</a>";
		}
		else
			$pager .= $as;

	$pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$as		= "<span style='font-weight:bold;'>Next&nbsp;&gt;&gt;</span>";

	if ($page <	$mp	&& $mp >= 0)
	{
		$pager .= "<a href='{$href}page=" .	($page + 1)	. "'>";
		$pager .= $as;
		$pager .= "</a>";
	}
	else
		$pager .= $as;

	if ($count)
	{
		$pagerarr		= array();
		$dotted			= 0;
		$dotspace		= 3;
		$dotend			= $pages - $dotspace;
		$curdotend		= $page	- $dotspace;
		$curdotstart	= $page	+ $dotspace;

		for	($i	= 0; $i	< $pages; $i++)
		{
			if (($i	>= $dotspace &&	$i <= $curdotend) || ($i >=	$curdotstart &&	$i < $dotend))
			{
				if (!$dotted)
					$pagerarr[]	= "...";

				$dotted	= 1;

				continue;
			}
			$dotted	= 0;
			$start	= $i * $rpp	+ 1;
			$end	= $start + $rpp	- 1;

			if ($end > $count)
				$end = $count;

			$text =	"$start&nbsp;-&nbsp;$end";

			if ($i != $page)
				$pagerarr[]	= "<a href='{$href}page=$i'><span style='font-weight:bold;'>$text</span></a>";
			else
				$pagerarr[]	= "<span style='font-weight:bold;'>$text</span>";
		}
		$pagerstr		= join(" | ", $pagerarr);
		$pagertop		= "<p align='center'>$pager<br />$pagerstr</p>\n";
		$pagerbottom	= "<p align='center'>$pagerstr<br />$pager</p>\n";
	}
	else
	{
		$pagertop		= "<p align='center'>$pager</p>\n";
		$pagerbottom	= $pagertop;
	}

	$start = $page * $rpp;

	return array($pagertop,	$pagerbottom, "LIMIT $start,$rpp");
}

function write_log($text)
{
	$text  = sqlesc($text);
	$added = sqlesc(get_date_time());

	sql_query("INSERT INTO sitelog (added, txt)
				VALUES($added, $text)")	or sqlerr(__FILE__,	__LINE__);
}

function searchfield($s)
{
	return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'),	array("	", "", "", " "), $s);
}

function genrelist()
{
	$ret = array();
	$res = sql_query("SELECT id, name
						FROM categories	ORDER BY name");

	while ($row	= mysql_fetch_array($res))
		$ret[] = $row;

	return $ret;
}

function hash_pad($hash)
{
	return str_pad($hash, 20);
}

function hash_where($name, $hash)
{
	$shhash	= preg_replace('/ *$/s', "", $hash);

	return "($name = " . sqlesc($hash) . " or $name	= "	. sqlesc($shhash) .	")";
}

function error_message($type, $heading,	$message)
{
	site_header();
	echo("<table class='main' width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td class='embedded'>");

	if ($heading)
		echo("<div class='notice notice-$type'><h2>$heading</h2>\n");

		echo("<p>".	$message . "</p><span></span></div>");
		echo("</td></tr></table>");
		site_footer();
		die;
}

function display_message($type,	$heading, $message)
{
	if ($heading)
		echo("<div class='notice notice-$type' align='left'><h2>$heading</h2>\n");
		echo("<p>".$message	. "</p><span></span></div>");
}

function sql_timestamp_to_unix_timestamp($s)
{
	return mktime(substr($s, 11, 2), substr($s,	14,	2),	substr($s, 17, 2), substr($s, 5, 2), substr($s,	8, 2), substr($s, 0, 4));
}

function get_elapsed_time($ts)
{
	$mins	= floor((gmtime() -	$ts) / 60);
	$hours	= floor($mins /	60);
	$mins	-= $hours *	60;
	$days	= floor($hours / 24);
	$hours	-= $days * 24;
	$weeks	= floor($days /	7);
	$days	-= $weeks *	7;
	$t		= "";

	if ($weeks > 0)
		return "$weeks week" . ($weeks > 1 ? "s" : "");

	if ($days >	0)
		return "$days day" . ($days	> 1	? "s" :	"");

	if ($hours > 0)
		return "$hours hour" . ($hours > 1 ? "s" : "");

	if ($mins >	0)
		return "$mins min" . ($mins	> 1	? "s" :	"");
	return "< 1	min";
}

function time_return($stamp)
{
	$ysecs = 365 * 24 *	60 * 60;
	$mosecs	= 31 * 24 *	60 * 60;
	$wsecs = 7 * 24	* 60 * 60;
	$dsecs = 24	* 60 * 60;
	$hsecs = 60	* 60;
	$msecs = 60;

	$years = floor($stamp /	$ysecs);
	$stamp %= $ysecs;
	$months	= floor($stamp / $mosecs);
	$stamp %= $mosecs;
	$weeks = floor($stamp /	$wsecs);
	$stamp %= $wsecs;
	$days =	floor($stamp / $dsecs);
	$stamp %= $dsecs;
	$hours = floor($stamp /	$hsecs);
	$stamp %= $hsecs;
	$minutes = floor($stamp	/ $msecs);
	$stamp %= $msecs;
	$seconds = $stamp;

	if ($years == 1)
	{
		$nicetime['years'] = "1	Year";
	}
	elseif ($years > 1)
	{
		$nicetime['years'] = $years	. "Year";
	}

	if ($months	== 1)
	{
		$nicetime['months']	= "1 Month";
	}
	elseif ($months	> 1)
	{
		$nicetime['months']	= $months .	" Month";
	}

	if ($weeks == 1)
	{
		$nicetime['weeks'] = "1	Weeks";
	}
	elseif ($weeks > 1)
	{
		$nicetime['weeks'] = $weeks	. "	Weeks";
	}

	if ($days == 1)
	{
		$nicetime['days'] =	"1 Days";
	}
	elseif ($days >	1)
	{
		$nicetime['days'] =	$days .	" Days";
	}

	if ($hours == 1)
	{
		$nicetime['hours'] = "1	Hours";
	}
	elseif ($hours > 1)
	{
		$nicetime['hours'] = $hours	. "	Hours";
	}

	if ($minutes ==	1)
	{
		$nicetime['minutes'] = "1 Minutes";
	}
	elseif ($minutes > 1)
	{
		$nicetime['minutes'] = $minutes	. "	Minutes";
	}

	if ($seconds ==	1)
	{
		$nicetime['seconds'] = "1 Seconds";
	}
	elseif ($seconds > 1)
	{
		$nicetime['seconds'] = $seconds	. "	Seconds";
	}

	if (is_array($nicetime))
	{
		return implode(", ", $nicetime);
	}
}

function failedloginscheck ()
{
	global $maxloginattempts;

	$total	= 0;
	$ip		= sqlesc(getip());
	$Query	= sql_query("SELECT SUM(attempts)
							FROM loginattempts
							WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);

	list($total) = mysql_fetch_array($Query);

	if ($total >= $maxloginattempts)
	{
		sql_query("UPDATE loginattempts
					SET	banned = 'yes'
					WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);

		error_message("error", "Login Locked!",	"You have <span	style='font-weight:bold;'>exceed thr maximum login attempts</span>,	therefore your IP Address <span	style='font-weight:bold;'>(".htmlspecialchars($ip).")</span> has been Banned.",false);
	}
}

function remaining ()
{
	global $maxloginattempts;

	$total	= 0;
	$ip		= sqlesc(getip());
	$Query	= sql_query("SELECT SUM(attempts)
						FROM loginattempts
						WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);

	list($total) = mysql_fetch_array($Query);
	$remaining = $maxloginattempts - $total;

	if ($remaining <= 2	)
		$remaining = "<span	style='color : #ff0000;'>".$remaining."</span>";
	else
		$remaining = "<span	style='color : green;'>".$remaining."</span>";

return $remaining;
}

?>