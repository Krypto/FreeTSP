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
                            <span style='color : #ff0000;' title='You should optimize this query.'>".$value['seconds']."</span>" : "<span style='color : green;' title='Query good.'>".$value['seconds']."</span>")."
                        </span>
                    </td>
                    <td align='left'>".htmlspecialchars($value['query'])."<br /></td>
                </tr>");
        }
        print("</table></div><br />");
    }
}
//-- Query Stats --//

?>
            </td>
            </td>

            <td class='theme_border' width='20%' valign='top'>

            <!-- Start Side Status Block -->
                <table>
                    <?php print StatusBar(); ?>
                </table>
                <br /><br />
            <!-- Finish Side Status Block - -->

                <!-- Start Side Navigation Menu -->
                <?php if ( get_user_class() >= UC_MODERATOR )
                { ?>
                    <table width='200'>
                        <tr><td class='signed' align='center' height='30'>Staff Only</td></tr>
                    </table>

                    <div class='menu'>
                         <a href="controlpanel.php"><img src='stylesheets/unique/images/admin.png' width='16' height='16' alt='image' title='Admin CP' />&nbsp;&nbsp;&nbsp;Admin CP</a>
                    </div>
                    <br /><br />
                <?php
                } ?>

                <table width='200'>
                    <tr><td class='signed' align='center' height='30'>Navigation</td></tr>
                </table>

                <div class='menu'>
                    <a href="index.php"><img src='stylesheets/unique/images/home.png' width='16' height='16' alt='image' title='Home' />&nbsp;&nbsp;&nbsp;Home</a>
                    <a href="altusercp.php"><img src='stylesheets/unique/images/usercp.png' width='16' height='16' alt='image' title='User CP' />&nbsp;&nbsp;&nbsp;User CP</a>
                    <a href="browse.php"><img src='stylesheets/unique/images/browse.png' width='16' height='16' alt='image' title='Browse' />&nbsp;&nbsp;&nbsp;Browse</a>
                    <a href="upload.php"><img src='stylesheets/unique/images/upload.png' width='16' height='16' alt='image' title='Upload' />&nbsp;&nbsp;&nbsp;Upload</a>
                    <a href="forums.php"><img src='stylesheets/unique/images/forums.png' width='16' height='16' alt='image' title='Forums' />&nbsp;&nbsp;&nbsp;Forums</a>
                    <a href="logout.php"><img src='stylesheets/unique/images/leave.png' width='16' height='16' alt='image' title='Logout' />&nbsp;&nbsp;&nbsp;Log Out</a>
                </div>

                <br /><br />

                <table width='200'>
                    <tr><td class='signed' align='center' height='30'>Information & Help</td></tr>
                </table>

                <div class='menu'>
                    <a href="topten.php"><img src='stylesheets/unique/images/top10.png' width='16' height='16' alt='image' title='Top 10' />&nbsp;&nbsp;&nbsp;Top 10</a>
                    <a href="credits.php"><img src='stylesheets/unique/images/log.png' width='16' height='16' alt='image' title='Credits' />&nbsp;&nbsp;&nbsp;Credits</a>
                    <a href="rules.php"><img src='stylesheets/unique/images/rules.png' width='16' height='16' alt='image' title='Rules' />&nbsp;&nbsp;&nbsp;Rules</a>
                    <a href="faq.php"><img src='stylesheets/unique/images/faq.png' width='16' height='16' alt='image' title='FAQ' />&nbsp;&nbsp;&nbsp;F.A.Q</a>
                    <a href="helpdesk.php"><img src='stylesheets/unique/images/help.png' width='16' height='16' alt='image' title='Help Desk' />&nbsp;&nbsp;&nbsp;Help Desk</a>
                    <a href="staff.php"><img src='stylesheets/unique/images/staff.png' width='16' height='16' alt='image' title='Staff' />&nbsp;&nbsp;&nbsp;Staff</a>
                </div>
                <!-- Finish Side Navigation Menu -->

            </td>

        <!--  No Support From FreeTSP Will Be Given - If The Credits Below Are Removed Or Altered  -->
        <tr>
            <td class='theme_border' align='center' colspan='2'><?php copyright(); ?>
                <br />Original <a href='http://www.freetsp.info/topic/773-uniquefreetsp-10-alpha-by-kidvision/'>Unique</a> Theme By KidVision - Modified For v1.0 By Fireknight<br /><br />
            </td>
            <td class='theme_border' align='right' colspan='3'>
                <a href="#"><img src='stylesheets/unique/images/top.png' width='25' height='25' alt='image' title='Top Of Page' /></a>
            </td>
        </tr>
        <!-- End Of Credits  -->

    </tr>
</table>
</body></html>