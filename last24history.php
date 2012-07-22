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

//CREDITS TO putyn

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_bbcode.php');
require_once "ofc/lib/open-flash-chart.php";

db_connect();
logged_in();

if (get_user_class() < UC_ADMINISTRATOR)
	error_message("warn","Warning",	"Access	Denied.");

$do	= isset($_GET['do']) ? $_GET['do'] : '';

$var['month'] =	isset($_GET['month']) && is_numeric($_GET['month'])	? '0'.$_GET['month'] : '';
$var['year']  =	isset($_GET['year']) &&	is_numeric($_GET['year'])  ? $_GET['year'] : '';

$n2m = array(1 => 'January', 2 => 'February', 3	=> 'March',	4 => 'April', 5	=> 'May' ,6	=> 'June', 7 =>	'July',	8 => 'August', 9 =>	'September', 10	=> 'October', 11 =>	'November',	12 => 'December');

$m2n = array('January' => 1, 'February'	=> 2, 'March' => 3,	'April'	=> 4, 'May'	=> 5, 'June' =>	6, 'July' => 7,	'August' =>	8, 'September' => 9, 'October' => 10, 'November' =>	11,	'December' => 12);

$n2ms =	array (	1=>	'Jan', 2 =>	'Feb', 3 =>	'Mar' ,4 =>	'Apr', 5 =>	'May', 6 =>	'Jun', 7 =>	'Jul', 8 =>	'Aug', 9 =>	'Sep', 10 => 'Oct',	11 => 'Nov', 12	=> 'Dec');

site_header('Last 24 Hour History Log');

begin_frame('Last 24 Hour History Log','center');

switch($do)
{
	case 'showgrap':

	if((!$var['year'] && !$var['month']) ||	($var['month'] > 0 && !$var['year']))
		error_message('error','Error','You forgot to select	some data');

	$_dir =	ROOT_DIR.'/cache/last24/*'.join('',$var).'.txt';
	$log  =	$log['ops']	= array();

	foreach(glob($_dir)	as $file)
	{
		preg_match('/(?P<day>\d{2})(?P<month>\d{2})(?P<year>\d{4})\.txt/',$file,$date);
		$count = count(unserialize(file_get_contents($file)));
		switch(true)
		{
			case $var['year'] >	0 && $var['month'] > 0 :
			$log['ops'][$n2ms[(int)$var['month']].'	- '.$date['day']] =	$count;
			$log['month'] =	$n2m[(int)$date['month']];
			break;

			case $var['year'] >	0 && !$var['month']:
			$log['year'] = $date['year'];
			$mname = $n2m[(int)$date['month']];

			if(isset($log['ops'][$mname]))
				$log['ops'][$mname]	+=$count;
			else
				$log['ops'][$mname]	= $count;
			break;
		}
	}

	$chart = new open_flash_chart();
	$title = new title('24 Hour	Histroy	Log	for	'.(isset($log['year']) ? 'year '.$log['year'] :	'').(isset($log['month']) ?	'month '.$log['month'].' year '.$var['year'] : ''));

	$title->set_style( "{font-size:	20px; color: #A2ACBA; text-align: center;}"	);
	$chart->set_title( $title );
	$chart->set_bg_colour('#ECE9D8');
	$d = new hollow_dot();
	$d->size(3)->halo_size(1)->colour('#A2ACBA');
	$line =	new	line();
	$line->set_width(2);
	$line->set_default_dot_style($d);
	$line->set_values(array_values($log['ops']));
	$line->set_key(	'Number	of visits',	12 );
	$chart->add_element($line);

	$x_labels =	new	x_axis_labels();
	$x_labels->set_vertical();
	$x_labels->set_colour('#000');
	$x_labels->set_labels(array_keys($log['ops']));

	$x = new x_axis();
	$x->set_colour('#A2ACBA');
	$x->set_grid_colour('#DADADA');
	$x->set_labels(	$x_labels );

	$chart->set_x_axis(	$x );

	$y = new y_axis();
	$y->set_colour('#A2ACBA');
	$y->set_grid_colour('#DADADA');
	$max_y = max(array_values($log['ops']))+10;
	$y->set_range( 0, $max_y, round($max_y/3) );
	$chart->add_y_axis(	$y );


	$data =	$chart->toPrettyString();

	echo('
		<script	type="text/javascript" src="ofc/js/swfobject.js"></script>
		<script	type="text/javascript" src="ofc/js/json.js"></script>
		<script	type="text/javascript">

	function open_flash_chart_data()
	{
		return JSON.stringify(data);
	}

	function findSWF(movieName)
	{
		if (navigator.appName.indexOf("Microsoft")!= -1)
		{
			return window[movieName];
		}
		else
		{
		return document[movieName];
		}
	}

	var	data = '.$data.';

	swfobject.embedSWF("ofc/open-flash-chart.swf", "last_24", "640", "200",	"9.0.0", "expressInstall.swf",{"loading":"Please wait while	the	stats are loaded!"});

	</script>

	<div id="last_24" style="text-align:center;"></div>');

	break;
	default:

	$_dir =	ROOT_DIR.'/cache/last24/*.txt';
	$_log =	array();

	foreach(glob($_dir)	as $file)
	{
		$count = count(unserialize(file_get_contents($file)));

		preg_match('/(?P<day>\d{2})(?P<month>\d{2})(?P<year>\d{4})\.txt/',$file,$date);

		$log[$date['year']][$n2m[(int)$date['month']]][$date['day']] = $count;
	}

	if(count($log))
	{
		foreach($log as	$year=>$more)
		{
			echo('<fieldset><legend><a href="?do=showgrap&amp;year='.$year.'">Year '.$year.'</a></legend>');
			echo('<ul>');

			foreach($more as $month=>$f00)
			echo('<li><a href="?do=showgrap&amp;month='.$m2n[$month].'&amp;year='.$year.'">'.$month.'</a></li>');
			echo('</ul></fieldset>');
		}
	}
	else
		error_message('error','Error','There is	no data	to be displayed');
}

end_frame();

site_footer();

?>