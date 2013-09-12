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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');
require_once(FUNC_DIR.'function_bbcode.php');

db_connect();
logged_in();

site_header("Staff", false);

//-- Get Current Datetime --//
$dt = gmtime() - 60;
$dt = sqlesc(get_date_time($dt));

//-- Search User Database For Moderators And Above And Display In Alphabetical Order --//
$res = sql_query("SELECT id, username, class, last_access
                    FROM users
                    WHERE class>=".UC_UPLOADER."
                    ORDER BY username") or sqlerr();

while ($arr = mysql_fetch_assoc($res))
{
    $staff_class[$arr['class']] = $staff_class[$arr['class']]."
        <tr>
            <td class='std' align='center' width='33%'>
                <a class='altlink' href='userdetails.php?id=".$arr['id']."'>".$arr['username']."</a>
            </td>
            <td class='std' align='center' width='33%'> ".("'".$arr['last_access']."'" > $dt ? "
                <img src='".$image_dir."online.png' width='32' height='32' border='0' alt='Online' title='Online' />" : "
                <img src='".$image_dir."offline.png' width='32' height='32' border='0' alt='Offline' title='Offline' />")."
            </td>"."
            <td class='std' align='center' width='33%'>
                <a href='sendmessage.php?receiver=".$arr['id']."'><input type='submit' class='btn' value='PM' /></a>
            </td>
        </tr>";
}

$support_msg = ("General Support Questions should be directed to these users first.<br />
                 Note that they are Volunteers, giving away their time and effort to help you.<br />
                 Treat them accordingly. (Languages listed are those besides English.)<br /><br />");

?>

<div align='center'>
    <span style='text-align:center; font-size: small;'>
        Welcome to the Staff Members here at <span style='font-weight:bold;'><?php echo $site_name?>.</span><br />
        Please direct your questions to the correct member of Staff.<br />
        Any questions that are already covered in the F.A.Q.<br />
        Will be Ignored.
    </span>
</div>

<div id='featured' align='center'>
    <br />
    <ul>
           <li><a href='#fragment-0'></a></li>
           <li><a class='btn' href='#fragment-1'>First Line Support</a></li>
           <li><a class='btn' href='#fragment-2'>Uploaders</a></li>
           <li><a class='btn' href='#fragment-3'>Moderators</a></li>
           <li><a class='btn' href='#fragment-4'>Administrators</a></li>
           <li><a class='btn' href='#fragment-5'>Sysop</a></li>
           <li><a class='btn' href='#fragment-6'>Manager</a></li>
    </ul>

    <div class='ui-tabs-panel' id='fragment-1'>
        <table class='coltable' width='81%'>
            <tr>
              <td class='std' align='center' colspan='3'><h2>First Line Of Support</h2><?php echo $support_msg?></td>
            </tr>
        </table>
                    <?php

                    $dt        = gmtime() - 60;
                    $dt        = sqlesc(get_date_time($dt));
                    $firstline = '';

                    $q = sql_query("SELECT users.id, username, email, last_access, country, status, support, supportfor, countries.flagpic, countries.name, support_lang
                                    FROM users
                                    LEFT JOIN countries ON countries.id = users.country
                                    WHERE support='yes'
                                    AND status='confirmed'
                                    ORDER BY username
                                    LIMIT 20") or sqlerr();

                    while ($a = mysql_fetch_assoc($q))
                    {
                        $country   = "<img src='{$image_dir}flag/{$a[flagpic]}' width='32' height='20' border='0' alt='".htmlspecialchars($a[name])."' title='".htmlspecialchars($a[name])."' style='margin-left: 8pt' />";

                        $firstline .= "<tr>
                                        <td class='rowhead'>
                                            <a class='altlink' href='userdetails.php?id=".$a['id']."'>&nbsp;".$a['username']."</a>
                                        </td>

                                    <td class='rowhead' align='center'>
                                         ".("'".$a['last_access']."'" > $dt ? "
                                        <img src='".$image_dir."online.png' width='32' height='32' border='0' alt='Online' title='Online' />" : "
                                        <img src='".$image_dir."offline.png' width='32' height='32' border='0' alt='Offline' title='Offline' />")."
                                    </td>


                                    <td class='rowhead' align='center'>
                                        <a href='sendmessage.php?receiver=".$a['id']."'>"."<input type='submit' class='btn' value='PM' /></a>
                                    </td>
                                    <td class='rowhead'>&nbsp;".$a['support_lang']."</td>
                                    <td class='rowhead' align='center'>".$country."</td>
                                    <td class='rowhead'>&nbsp;".$a['supportfor']."</td>
                                    </tr>";
                    }

                    echo("<table cellspacing='0' width='81%'>
                            <tr>
                                <td class='colhead' width='30'>&nbsp;<b>Username</b></td>
                                <td class='colhead' width='5' align='center'><b>Status</b></td>
                                <td class='colhead' width='5' align='center'><b>Contact</b></td>
                                <td class='colhead' width='85'>&nbsp;<b>Language</b></td>
                                <td class='colhead' width='40' align='center'><b>Country</b></td>
                                <td class='colhead' width='200'>&nbsp;<b>Support for</b></td>
                            </tr>
                            ".$firstline."
                        </table>");
                    ?>
     </div>

    <div class='ui-tabs-panel' id='fragment-2'>
        <table class='coltable' width='81%'>
            <tr>
                <td class='std' align='center' colspan='3'><h2>Uploaders</h2></td>
                </tr>
                    <?php echo $staff_class[UC_UPLOADER]?>
        </table>
     </div>

    <div class='ui-tabs-panel' id='fragment-3'>
        <table class='coltable' width='81%'>
            <tr>
                <td class='std' align='center' colspan='3'><h2>Moderators</h2></td>
            </tr>
                <?php echo $staff_class[UC_MODERATOR]?>
        </table>
    </div>

    <div class='ui-tabs-panel' id='fragment-4'>
        <table class='coltable' width='81%'>
            <tr>
                <td class='std' align='center' colspan='3'><h2>Admin</h2></td>
            </tr>
                <?php echo $staff_class[UC_ADMINISTRATOR]?>
        </table>
    </div>

    <div class='ui-tabs-panel' id='fragment-5'>
        <table class='coltable' width='81%'>
            <tr>
                <td class='std' align='center' colspan='3'><h2>Sysop</h2></td>
            </tr>
                <?php echo $staff_class[UC_SYSOP]?>
        </table>
    </div>

    <div class='ui-tabs-panel' id='fragment-6'>
        <table class='coltable' width='81%'>
            <tr>
                <td class='std' align='center' colspan='3'><h2>Manager</h2></td>
            </tr>
                <?php echo $staff_class[UC_MANAGER]?>
        </table>
    </div>

</div>

<script type="text/javascript" src="js/jquery-1.8.2.js" ></script>
<script type="text/javascript" src="js/jquery-ui-1.9.0.custom.min.js" ></script>

<script type="text/javascript">
    $(document).ready(function()
    {
        $("#featured").tabs({fx:{opacity: "toggle"}}).tabs("rotate", 5000, true);
    });
</script>

<?php

site_footer();

?>