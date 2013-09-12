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
require_once(FUNC_DIR.'function_page_verify.php');

db_connect(false);
logged_in();

parked();

$newpage = new page_verify();
$newpage->create('_upload_');

site_header("Upload", false);

if ($CURUSER['uploadpos'] == 'no')
{
    error_message_center("warn", "Sorry...", "Your Upload Privilage Has Been Removed!<br />
                                              Please Contact A Member Of Staff To Resolve This Problem.");
    site_footer();
    exit;
}

if (get_user_class() < UC_USER)
{
    error_message("warn", "Sorry...", "You are NOT Authorized to Upload Torrents.  (See <a href='faq.php#up'>Uploading</a> in the FAQ.)");
    site_footer();
    exit;
}

//-- Start Offer List, If The Uploading Member Has Made Any Offers --//
$res_offer = sql_query('SELECT id, offer_name
                        FROM offers
                        WHERE offered_by_user_id = '.$CURUSER['id'].'
                        AND status = \'approved\'
                        AND filled_torrent_id = \'0\'
                        ORDER BY offer_name ASC');

if (mysql_num_rows($res_offer) >= 0)
{
    $offers = "
                <tr>
                    <td class='rowhead'>My Offers</td>
                    <td class='rowhead'>
                        <select name='offer'>
                            <option value='0'>My Offers</option>";

                                $message = "<option value='0'>Your Have No Approved Offers Yet</option>";

                                while($arr_offer = mysql_fetch_assoc($res_offer))
                                {
                                    $offers .= "<option value='".$arr_offer['id']."'>".htmlspecialchars($arr_offer['offer_name'])."</option>";
                                }

                                $offers .= "</select>&nbsp;&nbsp;( If You Are Uploading One Of Your Offers, Please Select It Here So Interested Members Will Be Notified. )</td></tr>";
}
//-- Finish Offer List, If The Uploading Member Has Made Any Offers --//

//-- Start Request Section Dropdown --//
$res_request = sql_query('SELECT id, request_name
                            FROM requests
                            WHERE filled_by_user_id = 0
                            ORDER BY request_name ASC');
$request = "
            <tr>
                <td class='rowhead'>Request</td>
                <td class='rowhead'>
                    <select name='request'>
                        <option value='0'> Requests </option>";

                            if ($res_request)
                            {
                                while($arr_request = mysql_fetch_assoc($res_request))
                                {
                                    $request .= "<option value='".$arr_request['id']."'>".htmlspecialchars($arr_request['request_name'])."</option>";
                                }
                            }
                            else
                            {
                                $request .= '<option value="0">Currently No Requests</option>';
                            }

                            $request .= '</select>&nbsp;&nbsp;( If You Are Filling A Request Please Select It Here So Interested Members Can Be Notified. )</td></tr>';
//-- Finish Request Section Dropdown --//

?>
<div align='center'>
    <form name='upload' enctype='multipart/form-data' action='takeupload.php' method='post'>
        <input type='hidden' name='MAX_FILE_SIZE' value="<?php echo $max_torrent_size?>" />

        <p>The Tracker's Announce URL is <span style='font-weight:bold;'><?php echo $announce_urls[0] ?></span></p>
        <table border='1' width='100%' cellspacing='0' cellpadding='10'>
            <?php

            echo("<tr>
            <td class='rowhead'><label for='file'>Torrent File</label></td>
            <td class='rowhead'><input type='file' name='file' id='file' size='80' />\n</td>
            </tr>\n");

            echo("<tr>
            <td class='rowhead'><label for='name'>Torrent Name</label></td>
            <td class='rowhead'><input type='text' name='name' id='name' size='80' /><br />
            (Taken from filename if not specified. <strong>Please Use Descriptive Names.</strong>)\n</td>
            </tr>\n");

            echo("<tr>
            <td class='rowhead'><label for='poster'>Poster</label></td>
            <td class='rowhead'><input type='text' name='poster' id='poster' size='80' /><br />
            (Direct Link For A Poster Image To Be Shown On The Details Page)\n</td>
            </tr>\n");

            echo("<tr>
            <td class='rowhead'><label for='nfo'>NFO File</label></td>
            <td class='rowhead'><input type='file' name='nfo' id='nfo' size='80' /><br />
            (<strong>Required.</strong> Can Only Be Viewed By Power Users.)\n</td>
            </tr>\n");

            echo("$offers");
            echo("$request");

            echo("<tr>
            <td class='rowhead' style='padding: 10px'>Description</td>
            <td class='rowhead' align='center' style='padding: 3px'>".textbbcode("upload", "descr", htmlspecialchars($row["ori_descr"]))."</td>
            </tr>\n");

            $s = "<select name='type'>\n<option value='0'>(choose one)</option>\n";

            $cats = genrelist();

            foreach ($cats
                     AS
                     $row)
            {
                $s .= "<option value='".$row["id"]."'>".htmlspecialchars($row["name"])."</option>\n";
            }

            $s .= "</select>\n";

            echo("<tr>
                    <td class='rowhead'>Type</td>
                    <td class='rowhead'>$s</td>
                </tr>");

            echo("<tr>
                    <td class='rowhead'>Show Uploader</td>
                    <td class='rowhead'>
                        <input type='checkbox' name='uplver' value='yes' />Check This Box If You DO NOT Wish Your Name To Be Shown As The Uploader
                    </td>
                </tr>");

            echo("<tr>
                    <td class='rowhead'>Freeleech</td>
                    <td class='rowhead'>
                        <input type='checkbox' name='freeleech' value='yes' />Make This Torrent Freeleech
                    </td>
                </tr>");

            ?>

            <tr>
                <td class='std' align='center' colspan='2'>
                    <input type='submit' class='btn' value='Upload' />
                </td>
            </tr>
        </table>
    </form>
</div>
<br />

<?php

site_footer();

?>