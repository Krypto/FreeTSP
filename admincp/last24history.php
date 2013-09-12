<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** http://www.freetsp.info
** https://github.com/Krypto/FreeTSP
** Licence Info: GPL
** Copyright (C) 2010 FreeTSP v1.0
** A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
** Project Leaders: Krypto, Fireknight.
**/

//-- Credits To putyn --//

if (!defined("IN_FTSP_ADMIN"))
{
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html>
    <head>
         <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

         <title><?php if (isset($_GET['error']))
         {
             echo htmlspecialchars($_GET['error']);
         }
         ?> Error</title>

         <link rel="stylesheet" type="text/css" href="/errors/error-style.css" />
    </head>
    <body>
        <div id='container'>
            <div align='center' style='padding-top:15px'><img src='/errors/error-images/alert.png' width='89' height='94' alt='' title='' /></div>
            <h1 class='title'>Error 404 - Page Not Found</h1>
            <p class='sub-title' align='center'>The page that you are looking for does not appear to exist on this site.</p>
            <p>If you typed the address of the page into the address bar of your browser, please check that you typed it in correctly.</p>
            <p>If you arrived at this page after you used an old Boomark or Favorite, the page in question has probably been moved. Try locating the page via the navigation menu and then updating your bookmark.</p>
        </div>
    </body>
    </html>

    <?php
exit();
}

$do = isset($_GET['do']) ? $_GET['do'] : '';

$var['month'] = isset($_GET['month']) && is_numeric($_GET['month']) ? '0'.$_GET['month'] : '';
$var['year']  = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : '';

$n2m = array(1     => 'January',
             2     => 'February',
             3     => 'March',
             4     => 'April',
             5     => 'May',
             6     => 'June',
             7     => 'July',
             8     => 'August',
             9     => 'September',
             10    => 'October',
             11    => 'November',
             12    => 'December');

$m2n = array('January'     => 1,
             'February'    => 2,
             'March'       => 3,
             'April'       => 4,
             'May'         => 5,
             'June'        => 6,
             'July'        => 7,
             'August'      => 8,
             'September'   => 9,
             'October'     => 10,
             'November'    => 11,
             'December'    => 12);

$n2ms = array(1     => 'Jan',
              2     => 'Feb',
              3     => 'Mar',
              4     => 'Apr',
              5     => 'May',
              6     => 'Jun',
              7     => 'Jul',
              8     => 'Aug',
              9     => 'Sep',
              10    => 'Oct',
              11    => 'Nov',
              12    => 'Dec');

site_header('Last 24 Hour History Log',false);

begin_frame('Last 24 Hour History Log', 'center');

switch ($do)
{
    case 'showgrap':

        if ((!$var['year'] && !$var['month']) || ($var['month'] > 0 && !$var['year']))
        {
            error_message('error', 'Error', 'You forgot to select some data');
        }

        $_dir = CACHE_DIR.'last24/*'.join('', $var).'.txt';
        $log  = $log['ops'] = array();

        foreach (glob($_dir)
                 AS
                 $file)
        {
            preg_match('/(?P<day>\d{2})(?P<month>\d{2})(?P<year>\d{4})\.txt/', $file, $date);

            $count = count(unserialize(file_get_contents($file)));

            switch (true)
            {
                case $var['year'] > 0 && $var['month'] > 0 :
                     $log['ops'][$n2ms[(int) $var['month']].' - '.$date['day']] = $count;
                     $log['month'] = $n2m[(int) $date['month']];
                break;

                case $var['year'] > 0 && !$var['month']:
                     $log['year'] = $date['year'];
                     $mname       = $n2m[(int) $date['month']];

                    if (isset($log['ops'][$mname]))
                    {
                        $log['ops'][$mname] += $count;
                    }
                    else
                    {
                        $log['ops'][$mname] = $count;
                    }
                break;
            }
        }

        $chart = new open_flash_chart();
        $title = new title('24 Hour Histroy Log for '.(isset($log['year']) ? 'year '.$log['year'] : '').(isset($log['month']) ? 'month '.$log['month'].' year '.$var['year'] : ''));

        $title->set_style("{font-size: 20px; color: #A2ACBA; text-align: center;}");
        $chart->set_title($title);
        $chart->set_bg_colour('#ECE9D8');
        $d = new hollow_dot();
        $d->size(3)
            ->halo_size(1)
            ->colour('#A2ACBA');
        $line = new line();
        $line->set_width(2);
        $line->set_default_dot_style($d);
        $line->set_values(array_values($log['ops']));
        $line->set_key('Number of visits', 12);
        $chart->add_element($line);

        $x_labels = new x_axis_labels();
        $x_labels->set_vertical();
        $x_labels->set_colour('#000');
        $x_labels->set_labels(array_keys($log['ops']));

        $x = new x_axis();
        $x->set_colour('#A2ACBA');
        $x->set_grid_colour('#DADADA');
        $x->set_labels($x_labels);

        $chart->set_x_axis($x);

        $y = new y_axis();
        $y->set_colour('#A2ACBA');
        $y->set_grid_colour('#DADADA');
        $max_y = max(array_values($log['ops'])) + 10;
        $y->set_range(0, $max_y, round($max_y / 3));
        $chart->add_y_axis($y);

        $data = $chart->toPrettyString();

        echo('
        <script type="text/javascript" src="ofc/js/swfobject.js"></script>
        <script type="text/javascript" src="ofc/js/json.js"></script>
        <script type="text/javascript">

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

    var data = '.$data.';

    swfobject.embedSWF("ofc/open-flash-chart.swf", "last_24", "640", "200", "9.0.0", "expressInstall.swf",{"loading":"Please wait while the stats are loaded!"});

    </script>

    <div id="last_24" style="text-align:center;"></div>');

        break;
    default:

        $_dir = CACHE_DIR.'last24/*.txt';
        $_log = array();

        foreach (glob($_dir)
                 AS
                 $file)
        {
            $count = count(unserialize(file_get_contents($file)));

            preg_match('/(?P<day>\d{2})(?P<month>\d{2})(?P<year>\d{4})\.txt/', $file, $date);

            $log[$date['year']][$n2m[(int) $date['month']]][$date['day']] = $count;
        }

        if (count($log))
        {
            foreach ($log
                     AS
                     $year => $more)
            {
                echo('<fieldset><legend><a href="controlpanel.php?fileaction=4&amp;do=showgrap&amp;year='.$year.'">Year '.$year.'</a></legend>');
                echo('<ul>');

                foreach ($more
                         AS
                         $month => $f00)
                {
                    echo('<li><a href="controlpanel.php?fileaction=4&amp;do=showgrap&amp;month='.$m2n[$month].'&amp;year='.$year.'">'.$month.'</a></li>');
                }
                echo('</ul></fieldset>');
            }
        }
        else
        {
            error_message('error', 'Error', 'There is No Data to be Displayed');
        }
}

end_frame();

site_footer();

?>