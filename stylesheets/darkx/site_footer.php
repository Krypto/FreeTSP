<?php global $querytime, $CURUSER, $qtme, $queries, $query_stat, $site_name, $image_dir;

$queries = (!empty($queries) ? $queries : 0);

$qtme['debug']         = array(1); //==Add ids
$qtme['seconds']       = (microtime(true) - $qtme['start']);
$qtme['phptime']       = $qtme['seconds'] - $qtme['querytime'];
$qtme['percentphp']    = number_format(($qtme['phptime'] / $qtme['seconds']) * 100, 2);
$qtme['percentsql']    = number_format(($qtme['querytime'] / $qtme['seconds']) * 100, 2);
$qtme['howmany']       = ($queries != 1 ? 's ' : ' ');
$qtme['serverkillers'] = $queries > 6 ? '<br />'.($queries / 2).' Server Killers ran to show you this Page :) ! =[' : '=]';

if (get_user_class() >= UC_MANAGER)
{
    print("<br />
            <div class='roundedCorners' style='text-align:center;width:80%;border:1px solid black;padding:5px;'>
                <div style='text-align:left;background:transparent;height:25px;'>
                    <span style='font-weight:bold;font-size:12pt;'>Query Stats</span>
                </div>The ".$site_name." Server Killers generated this page in ".(round($qtme['seconds'], 4))." seconds and then took a nap.<br />They had to raid the server ".$queries." time'".$qtme['howmany']."using&nbsp;:&nbsp;
                <span style='font-weight:bold;'>".$qtme['percentphp']."</span>&nbsp;&#37;&nbsp;php&nbsp;&#38;&nbsp;
                <span style='font-weight:bold;'>".$qtme['percentsql']."</span>&nbsp;&#37;&nbsp;sql ".$qtme['serverkillers'].".<br /><br />
            </div>");

    // if (SQL_DEBUG && in_array($CURUSER['id'], $qtme['debug'])) {
    if ($qtme['query_stat'])
    {
        print("<br />
                <div class='roundedCorners' style='text-align:left;width:80%;border:1px solid black;padding:5px;'>
                    <div style='background:transparent;height:25px;'><span style='font-weight:bold;font-size:12pt;'>Querys</span></div>
                    <table border='0' align='center' width='100%' cellspacing='5' cellpadding='5'>
                        <tr>
                            <td class='colhead' align='center' width='5%'>ID</td>
                            <td class='colhead' align='center' width='10%'>Query Time</td>
                            <td class='colhead' align='left' width='85%'>Query String</td>
                        </tr>");

        foreach ($qtme['query_stat']
                 AS
                 $key
                 =>
                 $value)
        {
            print("<tr>
                    <td align='center'>".($key + 1)."</td>
                    <td align='center'>
                        <span style='font-weight:bold;'>".($value['seconds'] > 0.01 ? "
                            <span style='color : #ff0000;' title='You should optimize this query.'>".$value['seconds']."</span>" : "
                            <span style='color : green;' title='Query good.'>".$value['seconds']."</span>")."
                        </span>
                    </td>
                    <td align='left'>".htmlspecialchars($value['query'])."<br /></td>
                </tr>");
        }
        print("</table></div><br />");
    }
}
//-- Query Stats --//

//-- If You Want Support Do Not Remove/Alter These Lines --//
    copyright();

    echo("<table class='bottom'>
		      <tr>
		          <td class='std1'>Based On The <a href='http://www.freetsp.info/topic/1862-darkx/'>DarkX</a> Theme Ported By Subzero - Modified For v1.0 By Fireknight</td>
		      </tr>
	      </table>");
//-- End Of Credits --//

?>
        	<table class='bottom' width='100%'>
        		<tr>
        			<td class='std1' align='right'>
        				<a href='#'><img src="stylesheets/darkx/images/up.png" width="30" height="30" align="top" alt='Top Of Page' title='Top Of Page' /></a>
        			</td>
                </tr>
            </table>
		</td>
	</tr>
</table>

<table class='std1' width='100%' align='center' cellspacing='0' cellpadding='0' style='background: transparent'>
	<tr>
		  <td class='std1'><img src="stylesheets/darkx/images/bot1.png" width="15" height="35" align="top" alt='' /></td>
		  <td class='std1' style='background: url("stylesheets/darkx/images/bot2.png");' width='100%' height='35'></td>
		  <td class='std1' align='right'><img src="stylesheets/darkx/images/bot3.png" width="15" height="35" align="top" alt='' /></td>
	</tr>
</table>

</body></html>