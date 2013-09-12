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
require_once(FUNC_DIR.'function_pager_new.php');

db_connect(true);
logged_in();

if ($CURUSER['class'] < UC_POWER_USER)
{
    error_message_center("error", "Sorry", "Power Users and above Only");
}

//-- Possible Stuff To Be $_GETting lol --//
$id            = (isset($_GET['id']) ? intval($_GET['id']) :  (isset($_POST['id']) ? intval($_POST['id']) : 0));
$comment_id    = (isset($_GET['comment_id']) ? intval($_GET['comment_id']) :  (isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0));
$category      = (isset($_GET['category']) ? intval($_GET['category']) : (isset($_POST['category']) ? intval($_POST['category']) : 0));
$offered_by_id = isset($_GET['offered_by_id']) ? intval($_GET['offered_by_id']) : 0;
$vote          = isset($_POST['vote']) ? intval($_POST['vote']) : 0;
$posted_action = strip_tags((isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '')));

//=== Add All Possible Actions Here And Check Them To Be Sure They Are Ok --//
$valid_actions = array('add_new_offer',
                        'delete_offer',
                        'edit_offer',
                        'offer_details',
                        'vote',
                        'add_comment',
                        'edit_comment',
                        'view_original',
                        'delete_comment',
                        'alter_status');

//-- Check Posted Action, And If No Action Was Posted, Show The Default Page --//
$action = (in_array($posted_action, $valid_actions) ? $posted_action : 'default');

//-- Top Menu --//
$top_menu = '<p style="text-align: center;"><a class="altlink" href="offers.php">View Offers</a> || <a class="altlink" href="offers.php?action=add_new_offer">Make Offer</a></p>';

switch ($action)
{
    //-- Let Them Vote On It --//
     case 'vote';

        if (!isset($id) || !is_valid_id($id) || !isset($vote) || !is_valid_id($vote))
        {
            error_message_center("error", "ERROR", "Bad ID / Bad Vote. Go back and try again!");
        }

        //-- See If They Voted Yet --//
        $res_did_they_vote = sql_query('SELECT vote
                                        FROM offer_votes
                                        WHERE user_id = '.$CURUSER['id'].'
                                        AND offer_id = '.$id);

        $row_did_they_vote = mysql_fetch_row($res_did_they_vote);

        if ($row_did_they_vote[0] == '')
        {
            $yes_or_no = ($vote == 1 ? 'yes' : 'no');

            sql_query('INSERT INTO offer_votes (offer_id, user_id, vote)
                        VALUES ('.$id.', '.$CURUSER['id'].', "'.$yes_or_no.'")');

            sql_query('UPDATE offers
                        SET '.($yes_or_no == 'yes' ? 'vote_yes_count = vote_yes_count + 1' : 'vote_no_count = vote_no_count + 1').'
                        WHERE id = '.$id);

            header('Location: /offers.php?action=offer_details&voted=1&id='.$id);
            die();
        }
        else
        {
            error_message_center("error", "ERROR", "You have Voted on this Offer before");
        }

    break;

    //-- Default First Page With All The Offers --//
     case 'default';

        //-- Get Stuff For The Pager --//
        $count_query = sql_query('SELECT COUNT(id)
                                    FROM offers');

        $count_arr = mysql_fetch_row($count_query);
        $count     = $count_arr[0];
        $page      = isset($_GET['page']) ? (int)$_GET['page'] : 0;
        $perpage   = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 10;

        list ($menu, $LIMIT) = pager_new($count, $perpage, $page, 'offers.php?'.($perpage == 10 ? '' : '&amp;perpage='.$perpage));

        echo site_header('Offers');

        echo (isset($_GET['new']) ? '<h1>Offer Added!</h1>' : '' ).(isset($_GET['offer_deleted']) ? '<h1>Offer Deleted!</h1>' : '' ).'';
        echo '<div align="center">'.$top_menu.'<br /></div>';
        echo '<div align="center">'.$menu.'<br /><br /></div>';

        if ($count == 0)
        {
            error_message_center("info", "Sorry", "There are NO Offers at the moment.");
        }

        echo '<table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
                <tr>
                    <td class="colhead" align="center">Type</td>
                    <td class="colhead" align="center">Name</td>
                    <td class="colhead" align="center">Added</td>
                    <td class="colhead" align="center">Comm</td>
                    <td class="colhead" align="center">Votes</td>
                    <td class="colhead" align="center">Offered By</td>
                    <td class="colhead" align="center">Status</td>
                    <td class="colhead" align="center">Uploaded</td>
                </tr>';

        $main_query_res = sql_query('SELECT o.id AS offer_id, o.offer_name, o.category, o.added, o.offered_by_user_id, o.filled_torrent_id, o.vote_yes_count, o.vote_no_count, o.comments, o.status,u.id, u.username, u.warned, u.donor, u.class,c.id AS cat_id, c.name AS cat_name, c.image AS cat_image
                                        FROM offers AS o
                                        LEFT JOIN categories AS c ON o.category = c.id
                                        LEFT JOIN users AS u ON o.offered_by_user_id = u.id
                                        ORDER BY o.added DESC '.$LIMIT);

        while ($main_query_arr = mysql_fetch_assoc($main_query_res))
        {
            $status = ($main_query_arr['status'] == 'approved' ?
                                                    '<span style="color:green; font-weight:bold;">Approved!</span>' :
                                                    ($main_query_arr['status'] == 'pending' ?
                                                    '<span style="color:blue; font-weight:bold;">Pending...</span>' :
                                                    '<span style="color:red; font-weight:bold;">Denied</span>'));

            $uploaded = ($main_query_arr['filled_torrent_id'] >= '1' ?
                                                     '<a href="details.php?id='.$main_query_arr['filled_torrent_id'].'">
                                                     <span style="color:green; font-weight:bold;">Yes</span></a>' :
                                                     ($main_query_arr['filled_torrent_id'] == '0' ?
                                                     '<span style="color:red; font-weight:bold;">Not Yet</span>' :''));

            echo '<tr>
                        <td class="rowhead" align="center" style="margin: 0; padding: 1;"><img src="'.$image_dir.'caticons/'.htmlspecialchars($main_query_arr['cat_image'], ENT_QUOTES).'" border="0" width="60" height="54" alt="'.htmlspecialchars($main_query_arr['cat_name'], ENT_QUOTES).'" title="'.htmlspecialchars($main_query_arr['cat_name'], ENT_QUOTES).'"/></td>

                        <td class="rowhead" align="center"><a href="offers.php?action=offer_details&amp;id='.$main_query_arr['offer_id'].'">'.htmlspecialchars($main_query_arr['offer_name'], ENT_QUOTES).'</a></td>

                        <td class="rowhead" align="center">'.get_date_time($main_query_arr['added'],'LONG').'</td>
                        <td class="rowhead" align="center">'.number_format($main_query_arr['comments']).'</td>

                        <td class="rowhead" align="center">Yes: '.number_format($main_query_arr['vote_yes_count']).'<br />
                                                        No: '.number_format($main_query_arr['vote_no_count']).'</td>

                        <td class="rowhead" align="center">'.format_username($main_query_arr).'</td>
                        <td class="rowhead" align="center">'.$status.'</td>
                        <td class="rowhead" align="center">'.$uploaded.'</td>
                  </tr>';
        }

        echo '</table>';
        echo '<div align="center"><br />'.$menu.'<br /></div><br />';

        echo site_footer();

    break;

    //-- Details Page For The Offers --//
    case 'offer_details':

        if (!isset($id) || !is_valid_id($id))
        {
            error_message_center("error", "ERROR", "Bad ID! Go back and try again!");
        }

        $res = sql_query('SELECT o.id AS offer_id, o.offer_name, o.category, o.added, o.offered_by_user_id, o.vote_yes_count, o.status,o.vote_no_count, o.image, o.link, o.description, o.comments,u.id, u.username, u.warned, u.enabled, u.donor, u.class, u.uploaded, u.downloaded,c.name AS cat_name, c.image AS cat_image
                            FROM offers AS o
                            LEFT JOIN categories AS c ON o.category = c.id
                            LEFT JOIN users AS u ON o.offered_by_user_id = u.id
                            WHERE o.id = '.$id);

        $arr = mysql_fetch_assoc($res);

        //-- See If They Voted Yet --//
        $res_did_they_vote = sql_query('SELECT vote
                                        FROM offer_votes
                                        WHERE user_id = '.$CURUSER['id'].'
                                        AND offer_id = '.$id);

        $row_did_they_vote = mysql_fetch_row($res_did_they_vote);

        if ($row_did_they_vote[0] == '')
        {
            $vote_yes = '<form method="post" action="offers.php">
                            <input type="hidden" name="action" value="vote" />
                            <input type="hidden" name="id" value="'.$id.'" />
                            <input type="hidden" name="vote" value="1" />
                            <input type="submit" class="btn" value="Vote Yes!" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                        </form> ~ you will be notified when this Offer is filled.';

            $vote_no = '<form method="post" action="offers.php">
                            <input type="hidden" name="action" value="vote" />
                            <input type="hidden" name="id" value="'.$id.'" />
                            <input type="hidden" name="vote" value="2" />
                            <input type="submit" class="btn" value="Vote No!" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                        </form> ~ you are being a stick in the mud.';

            $your_vote_was = '';
        }
        else
        {
            $vote_yes      = '';
            $vote_no       = '';
            $your_vote_was = ' Your Vote: '.$row_did_they_vote[0].' ';
        }

        $status_drop_down = ($CURUSER['class'] < UC_MODERATOR ? '' : '<br />
                            <form method="post" action="offers.php">
                                <input type="hidden" name="action" value="alter_status" />
                                <input type="hidden" name="id" value="'.$id.'" />
                                <select name="set_status">
                                    <option class="body" value="pending"'.($arr['status'] == 'pending' ? ' selected="selected"' : '' ).' >Status: Pending</option>
                                    <option class="body" value="approved"'.($arr['status'] == 'approved' ? ' selected="selected"' : '' ).' >Status: Approved</option>
                                    <option class="body" value="denied"'.($arr['status'] == 'denied' ? ' selected="selected"' : '' ).' >Status: Denied</option>
                                </select>
                                <input type="submit" class="btn" value="Change Status!" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                            </form> ');

        //-- Start Page --//
        echo site_header('Offer details for: '.htmlspecialchars($arr['offer_name'], ENT_QUOTES));

        echo (isset($_GET['status_changed']) ? '<h1>Offer Status Updated!</h1>' : '' ).
             (isset($_GET['voted']) ? '<h1>Vote Added</h1>' : '' ).
             (isset($_GET['comment_deleted']) ? '<h1>Comment Deleted</h1>' : '' ).$top_menu.
             ($arr['status'] == 'approved' ? '<span style="color:green; font-weight:bold;">Status: Approved!</span>' :
             ($arr['status'] == 'pending' ? '<span style="color:blue; font-weight:bold;">Status: Pending...</span>' :
             '<span style="color:red; font-weight: bold;">Status: Denied</span>')).$status_drop_down.'<br /><br />

            <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
                <tr>
                    <td class="colhead" align="center" colspan="2"><h1>'.htmlspecialchars($arr['offer_name'], ENT_QUOTES).
                    ($CURUSER['class'] < UC_MODERATOR ? '' : ' [ <a href="offers.php?action=edit_offer&amp;id='.$id.'">Edit</a> ]
                    [ <a href="offers.php?action=delete_offer&amp;id='.$id.'">Delete</a> ]').'</h1></td>
                </tr>

                <tr>
                    <td class="rowhead" align="left" width="20%">&nbsp;<strong>Image:</strong></td>
                    <td class="rowhead"><a href="'.$arr['image'].'" rel="lightbox"><img src="'.strip_tags($arr['image']).'" width="" height="" alt="Posted Image" title="Posted Image" style="max-width:600px;" /></a></td>
                </tr>

                <tr>
                    <td class="rowhead" align="left">&nbsp;<strong>Description:</strong></td>
                    <td class="rowhead">'.format_comment($arr['description']).'</td>
                </tr>

                <tr>
                    <td class="rowhead" align="left">&nbsp;<strong>Category:</strong></td>
                    <td class="rowhead"><img src="'.$image_dir.'caticons/'.htmlspecialchars($arr['cat_image'], ENT_QUOTES).'" border="0" width="60" height="54" alt="'.htmlspecialchars($arr['cat_name'], ENT_QUOTES).'" title="'.htmlspecialchars($arr['cat_name'], ENT_QUOTES).'" /></td>
                </tr>

                <tr>
                    <td class="rowhead" align="left">&nbsp;<strong>Link:</strong></td>
                    <td class="rowhead"><a class="altlink" href="'.htmlspecialchars($arr['link'], ENT_QUOTES).'"  target="_blank">'.htmlspecialchars($arr['link'], ENT_QUOTES).'</a></td>
                </tr>

                <tr>
                    <td class="rowhead" align="left">&nbsp;<strong>Votes:</strong></td>
                    <td class="rowhead">
                        <span style="font-weight:bold;color: green;">Yes: '.number_format($arr['vote_yes_count']).'</span> '.$vote_yes.'<br />
                        <span style="font-weight:bold;color: red;">No: '.number_format($arr['vote_no_count']).'</span> '.$vote_no.'<br /> '.$your_vote_was.'
                    </td>
                </tr>

                <tr>
                    <td class="rowhead" align="left">&nbsp;<strong>Offered By:</strong></td>
                    <td class="rowhead">'.format_username($arr).'</td>
                </tr>

                <tr>
                    <td class="rowhead" align="left">&nbsp;<strong>Report Offer</strong></td>
                    <td class="rowhead" align="left">
                        <form action="report.php?type=Offer&amp;id='.$id.'" method="post">
                            <input type="submit" class="btn" value="Report This Offer" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />&nbsp;For breaking the <a class="altlink" href="rules.php">Rules</a>
                        </form>
                    </td>
                </tr>
            </table>';

        echo '<h1>Comments for '.htmlentities($arr['offer_name'], ENT_QUOTES ).'</h1>

        <p><a name="startcomments"></a></p>';

        if ( $CURUSER['offercompos'] == 'no' )
        {
            $commentbar = '<p align="center">Comment Privilege Disabled</p>';
        }
        else
        {
            $commentbar = '<p align="center"><a class="index" href="offers.php?action=add_comment&amp;id='.$id.'">Add a Comment</a></p>';
        }

        $count = $arr['comments'];

        if (!$count)
        {
            echo '<h2>No Comments yet</h2>';
        }
        else
        {
            //-- Get Stuff For The Pager --//
            $page    = isset($_GET['page']) ? (int)$_GET['page'] : 0;
            $perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 10;

            list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'offers.php?action=offer_details&amp;id='.$id, ($perpage == 10 ? '' : '&amp;perpage='.$perpage).'#comments');

            $subres = sql_query("SELECT comments_offer.id, text, user, comments_offer.added, editedby, editedat, avatar, warned, username, title, class, donor
                                    FROM comments_offer
                                    LEFT JOIN users ON comments_offer.user = users.id
                                    WHERE offer = $id
                                    ORDER BY comments_offer.id ".$LIMIT) or sqlerr(__FILE__, __LINE__);

            $allrows       = array();
            while ($subrow = mysql_fetch_assoc($subres))
            $allrows[]     = $subrow;

            echo $commentbar.'<a name="comments"></a>';
            echo ($count > $perpage) ? '<p>'.$menu.'<br /></p>' : '<br />';

            echo comment_table($allrows);

            echo ($count > $perpage) ? '<p>'.$menu.'<br /></p>' : '<br />';
        }

        echo $commentbar;

        echo site_footer();

    break;

    //-- Add A New Offer --//
    case 'add_new_offer':

        $offer_name = strip_tags(isset($_POST['offer_name']) ? trim($_POST['offer_name']) : '');
        $image      = strip_tags(isset($_POST['image']) ? trim($_POST['image']) : '');
        $body       = (isset($_POST['description']) ? trim($_POST['description']) : '');
        $link       = strip_tags(isset($_POST['link']) ? trim($_POST['link']) : '');

        //-- Do The Cat List :D --//
        $category_drop_down = '<select class="required" name="category"><option class="body" value="">Select Offer Category</option>';
        $cats               = genrelist();

        foreach ($cats
                 AS
                 $row)
        {
            $category_drop_down .= '<option class="body" value="'.$row['id'].'" '.($category == $row['id'] ? ' selected="selected"' : '').' >'.htmlspecialchars($row['name'], ENT_QUOTES).'</option>';
        }

        $category_drop_down .= '</select>';

        if (isset($_POST['category']))
            {
                $cat_res = sql_query('SELECT id AS cat_id, name AS cat_name, image AS cat_image
                                        FROM categories
                                        WHERE id = '.$category);

                $cat_arr   = mysql_fetch_assoc($cat_res);
                $cat_image = htmlspecialchars($cat_arr['cat_image'], ENT_QUOTES);
                $cat_name  = htmlspecialchars($cat_arr['cat_name'], ENT_QUOTES);
            }

        $username = $CURUSER ['username'];

        //-- If Posted And Not Preview, Process It :D --//
        if (isset($_POST['button']) && $_POST['button'] == 'Submit')
        {
            sql_query ('INSERT INTO offers (offer_name, image, description, category, added, offered_by_user_id, link)
                        VALUES ('.sqlesc($offer_name).', '.sqlesc($image).', '.sqlesc($body).', '.$category.', '.time().', '.$CURUSER['id'].',  '.sqlesc($link).');');

            $new_offer_id = mysql_insert_id();

            header('Location: offers.php?action=offer_details&new=1&id='.$new_offer_id);
            die();
        }

        //-- Start Page --//
         echo site_header('Add New Offer.');

         echo '<table class="main" border="0" align="center" width="750px" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="embedded" align="center">
                        <h1 style="text-align: center;">New Offer</h1>'.$top_menu.'
                        <form method="post" action="offers.php?action=add_new_offer" name="offer_form" id="offer_form">
                            '.(isset($_POST['button']) && $_POST['button'] == 'Preview' ? '<br />

                            <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
                                <tr>
                                    <td class="colhead" align="center" colspan="2"><h1>'.htmlspecialchars($offer_name, ENT_QUOTES).'</h1></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right" width="15%">Image:</td>
                                    <td class="rowhead"><img src="'.htmlspecialchars($image, ENT_QUOTES).'" border="0" width="" height="" alt="Posted Image" title="Posted Image" style="max-width:600px;" /></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right" width="15%">Description:</td>
                                    <td class="rowhead">'.format_comment($body).'</td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right" width="15%">Category:</td>
                                    <td class="rowhead"><img src="'.$image_dir.'caticons/'.htmlspecialchars($cat_image, ENT_QUOTES).'" border="0" width="60" height="54" alt="'.htmlspecialchars($cat_name, ENT_QUOTES).'" title="'.htmlspecialchars($cat_name, ENT_QUOTES).'" /></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right" width="15%">Link:</td>
                                    <td class="rowhead"><a class="altlink" href="'.htmlspecialchars($link, ENT_QUOTES).'" target="_blank">'.htmlspecialchars($link, ENT_QUOTES).'</a></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right" width="15%">Offered by:</td>
                                    <td class="rowhead">'.$username.'</td>
                                </tr>

                            </table><br />' : '').'

                            <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
                                <tr>
                                    <td class="colhead" align="center" colspan="2"><h1>Making a Offer</h1></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="center" colspan="2">Before you make an Offer, <a class="altlink" href="search.php">Search</a> to be sure it has not yet been Requested, Offered, or Uploaded!<br /><br />Be sure to fill in ALL fields!</td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Name:</td>
                                    <td class="rowhead">
                                        <input class="required" type="text" size="80" name="offer_name" value="'.htmlspecialchars($offer_name, ENT_QUOTES).'"  />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Image:</td>
                                    <td class="rowhead">
                                        <input type="text" class="required" size="80" name="image" value="'.htmlspecialchars($image, ENT_QUOTES).'" />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Link:</td>
                                    <td class="rowhead">
                                        <input type="text" size="80"  name="link" value="'.htmlspecialchars($link, ENT_QUOTES).'" class="required" />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Category:</td>
                                    <td class="rowhead">'.$category_drop_down.'</td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Description:</td>
                                    <td class="rowhead">'.textbbcode("compose","description",$body).'</td>
                                </tr>

                                <tr>
                                    <td colspan="2" align="center" class="rowhead">
                                        <input type="submit" name="button" class="btn" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                                        <input type="submit" name="button" class="btn" value="Submit" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table><br />

            <script type="text/javascript" src="scripts/jquery.validate.min.js"></script>
            <script type="text/javascript">
            <!--

            $(document).ready(function()
            {
                //=== form validation
                $("#offer_form").validate();
            }
            );

            -->
            </script>';

        echo site_footer();

    break;

//-- Delete An Offer --//
    case 'delete_offer':

        if ($CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        if (!isset($id) || !is_valid_id($id))
        {
            error_message_center("error", "ERROR", "Bad ID! Go back and try again!");
        }

        $res = sql_query('SELECT offer_name, offered_by_user_id
                            FROM offers
                            WHERE id ='.$id) or sqlerr(__FILE__,__LINE__);

        $arr = mysql_fetch_assoc($res);

        if (!$arr)
        {
            error_message_center("error", "ERROR", "Invalid ID! Go back and try again!");
        }

        if ($arr['offered_by_user_id'] !== $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permisson Denied!");
        }

        if (!isset($_GET['do_it']))
        {
            error_message_center("info", "Sanity Check", "Are you sure you would like to Delete the Offer <b>
                                                          : - ".htmlspecialchars($arr['offer_name'])."</b>.<br />
                                                          If so Click&nbsp;<a class='altlink' href='offers.php?action=delete_offer&amp;id=".$id."&amp;do_it=666'>HERE</a>.");
        }
        else
        {
            sql_query('DELETE FROM offers
                        WHERE id='.$id);

            sql_query('DELETE FROM offer_votes
                        WHERE offer_id ='.$id);

            sql_query('DELETE FROM comments_offer
                        WHERE id ='.$id);

            header('Location: /offers.php?offer_deleted=1');

            die();
        }

        echo site_footer();

    break;


//-- Edit An Offer --//
    case 'edit_offer':

        if ($CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        if (!isset($id) || !is_valid_id($id))
        {
            error_message_center("error", "ERROR", "Bad ID! Go back and try again!");
        }

        $edit_res = sql_query('SELECT offer_name, image, description, category, offered_by_user_id, link
                                FROM offers
                                WHERE id ='.$id) or sqlerr(__FILE__,__LINE__);

        $edit_arr = mysql_fetch_assoc($edit_res);

        if ($CURUSER['class'] < UC_MODERATOR && $CURUSER['id'] !== $edit_arr['offered_by_user_id'])
        {
            error_message_center("error", "ERROR", "This is NOT your Offer to Edit!");
        }


        $offer_name = strip_tags(isset($_POST['offer_name']) ? trim($_POST['offer_name']) : $edit_arr['offer_name']);
        $image      = strip_tags(isset($_POST['image']) ? trim($_POST['image']) : $edit_arr['image']);
        $body       = (isset($_POST['description']) ? trim($_POST['description']) : $edit_arr['description']);
        $link       = strip_tags(isset($_POST['link']) ? trim($_POST['link']) : $edit_arr['link']);
        $category   = (isset($_POST['category']) ? intval($_POST['category']) : $edit_arr['category']);

        //-- Do The Cat List :D --//
        $category_drop_down = '<select class="required" name="category"><option class="body" value="">Select Offer Category</option>';
        $cats               = genrelist();

        foreach ($cats
                 AS
                 $row)
        {
            $category_drop_down .= '<option class="body" value="'.$row['id'].'" '.($category == $row['id'] ? ' selected="selected"' : '').' >'.htmlspecialchars($row['name'], ENT_QUOTES).'</option>';
        }

        $category_drop_down .= '</select>';

        $cat_res = sql_query('SELECT id AS cat_id, name AS cat_name, image AS cat_image
                                FROM categories
                                WHERE id = '.$category);

        $cat_arr   = mysql_fetch_assoc($cat_res);
        $cat_image = htmlspecialchars($cat_arr['cat_image'], ENT_QUOTES);
        $cat_name  = htmlspecialchars($cat_arr['cat_name'], ENT_QUOTES);

        //-- If Posted And Not Preview, Process It :D --//
        if (isset($_POST['button']) && $_POST['button'] == 'Edit')
        {
            sql_query ('UPDATE offers
                        SET offer_name = '.sqlesc($offer_name).', image = '.sqlesc($image).', description = '.sqlesc($body).',category = '.sqlesc($category).', link = '.sqlesc($link).'
                        WHERE id = '.$id);

            header('Location: offers.php?action=offer_details&edited=1&id='.$id);
            die();
        }

        //-- Start Page --//
        echo site_header('Edit Offer.');

        echo '<table class="main" border="0" align="center" width="80%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="embedded" align="center">
                        <h1 style="text-align: center;">Edit Offer </h1>'.$top_menu.'
                        <form method="post" action="offers.php?action=edit_offer" name="offer_form" id="offer_form">
                            <input type="hidden" name="id" value="'.$id.'" />'.(isset($_POST['button']) && $_POST['button'] == 'Preview' ? '<br />

                            <table border="0" align="center" width="700px" cellspacing="0" cellpadding="5">
                                <tr>
                                    <td class="colhead" align="center" colspan="2"><h1>'.htmlspecialchars($offer_name, ENT_QUOTES).'</h1></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Image:</td>
                                    <td class="rowhead" align="left"><img src="'.htmlspecialchars($image, ENT_QUOTES).'" alt="image" style="max-width:600px;" /></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Description:</td>
                                    <td class="rowhead" align="left">'.format_comment($body).'</td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Category:</td>
                                    <td class="rowhead" align="left"><img src="'.$image_dir.'caticons/'.htmlspecialchars($cat_image, ENT_QUOTES).'"  border="0" alt="'.htmlspecialchars($cat_name, ENT_QUOTES).'" /></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Link:</td>
                                    <td class="rowhead" align="left"><a class="altlink" href="'.htmlspecialchars($link, ENT_QUOTES).'" target="_blank">'.htmlspecialchars($link, ENT_QUOTES).'</a></td>
                                </tr>

                            </table><br />' : '').'

                            <table border="0" align="center" width="700px" cellspacing="0" cellpadding="5">
                                <tr>
                                    <td class="colhead" align="center" colspan="2"><h1>Edit :- '.htmlspecialchars($offer_name, ENT_QUOTES).'</h1></td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="center" colspan="2">Be sure to fill in ALL fields!</td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Name:</td>
                                    <td class="rowhead" align="left">
                                        <input type="text" class="required" size="80" name="offer_name" value="'.htmlspecialchars($offer_name, ENT_QUOTES).'" />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Image:</td>
                                    <td align="left">
                                        <input type="text" class="required" size="80" name="image" value="'.htmlspecialchars($image, ENT_QUOTES).'" />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Link:</td>
                                    <td class="rowhead" align="left">
                                        <input type="text" class="required" size="80" name="link" value="'.htmlspecialchars($link, ENT_QUOTES).'" />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Category:</td>
                                    <td class="rowhead" align="left">'.$category_drop_down.'</td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="right">Description:</td>
                                    <td class="rowhead" align="left">'.textbbcode("compose","description",$body).'</td>
                                </tr>

                                <tr>
                                    <td class="rowhead" align="center" colspan="2">
                                        <input type="submit" name="button" class="btn" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                                        <input type="submit" name="button" class="btn" value="Edit" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                                    </td>
                                </tr>

                            </table>
                        </form>
                    </td>
                </tr>
            </table><br />

            <script type="text/javascript" src="scripts/jquery.validate.min.js"></script>
            <script type="text/javascript">

            <!--
            $(document).ready(function()
            {
                //=== form validation
                $("#offer_form").validate();
            });
            -->

            </script>';

        echo site_footer();

    break;

    //-- Add A Comment --//
    case 'add_comment':

        if ( $CURUSER['offercompos'] == 'no' )
        {
            error_message_center("error", "ERROR", "Comment Privilege Disabled!");
        }

        if (!isset($id) || !is_valid_id($id))
        {
            error_message_center("error", "ERROR", "Bad ID !! Go back and try again");
        }

            $res = sql_query('SELECT offer_name
                              FROM offers
                              WHERE id = '.$id) or sqlerr(__FILE__,__LINE__);

            $arr = mysql_fetch_array($res);

        if (!$arr)
        {
            error_message_center("error", "ERROR", "No Offer with that ID!");
        }

        if(isset($_POST['button']) && $_POST['button'] == 'Save')
        {
            $text = (isset($_POST['text']) ? trim($_POST['text']) : '');

            if (!$text)
            {
                error_message_center("error", "ERROR", "Comment body cannot be empty !! Go back and try again");
            }

            sql_query("INSERT INTO comments_offer (user, offer, added, text, ori_text)
                       VALUES (".$CURUSER["id"].",$id, '".get_date_time()."', ".sqlesc($text).",".sqlesc($text).")");

            $newid = mysql_insert_id();

            sql_query("UPDATE offers
                       SET comments = comments + 1
                       WHERE id = $id");

            header('Location: /offers.php?action=offer_details&id='.$id.'&viewcomm='.$newid.'#comm'.$newid);
            die();
        }

        $text = htmlspecialchars((isset($_POST['text']) ? $_POST['text'] : ''));

        $res = sql_query('SELECT avatar
                          FROM users
                          WHERE id = '.$CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

        $row = mysql_fetch_array($res);

        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");

        if (!$avatar)
        {
            $avatar = "{$image_dir}default_avatar.gif";
        }

        echo site_header('Add a Comment');

        echo $top_menu.'<p><form method="post" action="offers.php?action=add_comment">
                        <input type="hidden" name="id" value="'.$id.'"/>
                        '.(isset($_POST['button']) && $_POST['button'] == 'Preview' ? '

            <table border="0" align="center" width="80%" cellspacing="5" cellpadding="5">
                <tr>
                    <td class="colhead" colspan="2"><h1>Preview</h1></td>
                </tr>

                <tr>
                    <td align="center" width="100"><img src='.$avatar.' width="125" height="125" border="0" alt="" title="" /></td>
                    <td class="rowhead" align="left" valign="top">'.format_comment($text).'</td>
                </tr>
            </table><br />' : '').'

            <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
                <tr>
                    <td class="colhead" align="center" colspan="2"><h1>Add a Comment to "'.$arr['offer_name'].'"</h1></td>
                </tr>

                <tr>
                    <td class="rowhead" align="right" valign="top"><b>Comment:</b></td>
                    <td class="rowhead">'.textbbcode("compose","text",$text).'</td>
                </tr>

                <tr>
                    <td class="rowhead" align="center" colspan="2">
                        <input name="button" type="submit" class="btn" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                        <input name="button" type="submit" class="btn" value="Save" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                    </td>
                </tr>
            </table>
        </form></p>';

        //-- View Existing Comments --//
        $res = sql_query('SELECT c.offer, c.id, c.text, c.added, c.editedby, c.editedat, u.id, u.username, u.warned, u.enabled, u.donor, u.class, u.avatar, u.title
                            FROM comments_offer AS c
                            LEFT JOIN users AS u ON c.user = u.id
                            WHERE offer = '.$id.'
                            ORDER BY c.id DESC LIMIT 5');

        $allrows    = array();

        while ($row = mysql_fetch_assoc($res))

        $allrows[]  = $row;

        if (count($allrows))
        {
            echo '<h2>Most Recent Comments, in Reverse Order</h2>';
            echo comment_table($allrows);
        }

        echo site_footer();

    break;

    //-- Edit A Comment --//
    case 'edit_comment':

        if ( $CURUSER['offercompos'] == 'no' )
        {
            error_message_center("error", "ERROR", "Comment Privilege Disabled!");
        }

        if (!isset($comment_id) || !is_valid_id($comment_id))
        {
            error_message_center("error", "ERROR", "Bad ID !! Go back and try again");
        }

        $res = sql_query('SELECT c.*, o.offer_name
                          FROM comments_offer AS c
                          LEFT JOIN offers AS o ON c.offer = o.id
                          WHERE c.id='.$comment_id) or sqlerr(__FILE__,__LINE__);

        $arr = mysql_fetch_assoc($res);

        if (!$arr)
        {
            error_message_center("error", "ERROR", "Invalid ID !! Go back and try again");
        }

        if ($arr['user'] != $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        $body = htmlspecialchars((isset($_POST['edit']) ? $_POST['edit'] : $arr['text']));

        if (isset($_POST['button']) && $_POST['button'] == 'Edit')
        {
            if ($body == '')
            {
                error_message_center("error", "ERROR", "Comment body can not be empty!");
            }

            $text     = sqlesc($body);
            $editedat = sqlesc(get_date_time());

            sql_query("UPDATE comments_offer
                        SET text = $text, editedat = $editedat, editedby = $CURUSER[id]
                        WHERE id = $comment_id") or sqlerr(__FILE__, __LINE__);

            header('Location: /offers.php?action=offer_details&id='.$id.'&viewcomm='.$comment_id.'#comm'.$comment_id);
            die();
        }

        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");

        if (!$avatar)
        {
            $avatar = "{$image_dir}default_avatar.gif";
        }

        echo site_header('Edit comment to "'.htmlentities($arr['offer_name'], ENT_QUOTES ).'"');

        echo $top_menu.'<p><form method="post" action="offers.php?action=edit_comment">
                            <input type="hidden" name="id" value="'.$arr['offer'].'"/>
                            <input type="hidden" name="comment_id" value="'.$comment_id.'"/>'.
                            (isset($_POST['button']) && $_POST['button'] == 'Preview' ? '

            <table border="0" align="center" width="80%" cellspacing="5" cellpadding="5">
                <tr>
                    <td class="colhead" colspan="2"><h1>Preview</h1></td>
                </tr>

                <tr>
                    <td align="center" width="100"><img src='.$avatar.' width="125" height="125" border="0" alt="" title="" /></td>
                    <td class="rowhead" align="left" valign="top">'.format_comment($body).'</td>
                </tr>
            </table><br />' : '').'

            <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
                <tr>
                    <td class="colhead" align="center" colspan="2"><h1>Edit Comment to "'.htmlspecialchars($arr['offer_name'], ENT_QUOTES).'"</h1></td>
                </tr>

                <tr>
                    <tdclass="rowhead" align="right" valign="top" ><b>Comment:</b></td>
                    <td class="rowhead">'.textbbcode("compose","edit",$body).'</td>
                </tr>

                <tr>
                    <td align="center" colspan="2" class="rowhead">
                        <input name="button" type="submit" class="btn" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                        <input name="button" type="submit" class="btn" value="Edit" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                    </td>
                </tr>
            </table>
        </form></p>';

        echo site_footer();

    break;

    //-- View Original Comment --//
    case 'view_original';

        if ($CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        if (!isset($comment_id) || !is_valid_id($comment_id))
        {
            error_message_center("error", "ERROR", "Bad ID !! Go back and try again");
        }

        $res = sql_query('SELECT c.*, o.offer_name
                          FROM comments_offer AS c
                          LEFT JOIN offers AS o ON c.offer = o.id
                          WHERE c.id='.$comment_id) or sqlerr(__FILE__,__LINE__);

        $arr = mysql_fetch_assoc($res);

        if (!$arr)
        {
            error_message_center("error", "ERROR", "Invalid ID !! Go back and try again");
        }

        if ($arr['user'] != $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        site_header("Original Comment");

        print("<h1>Original contents of comment #$comment_id</h1>\n");
        print("<table width='100%' border='1' cellspacing='0' cellpadding='5'>");
        print("<tr>");
        print("<td class='comment'>\n");
        print format_comment($arr["ori_text"]);
        print("</td>");
        print("</tr>");
        print("</table><br />");

        $returnto = htmlspecialchars($_SERVER["HTTP_REFERER"]);

        if ($returnto)
        {
            error_message_center("info", "INFO ", "Return to the Offer details page.<br />
                                                   Click <a class='altlink' href='$returnto'>Here</a>");
        }

        site_footer();

    break;

    //-- DELETE A COMMENT --//
     case 'delete_comment':

        if ($CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        if (!isset($comment_id) || !is_valid_id($comment_id))
        {
            error_message_center("error", "ERROR", "Bad ID! Go back and try again.");
        }

        $res = sql_query('SELECT user, offer
                            FROM comments_offer
                            WHERE id='.$comment_id) or sqlerr(__FILE__,__LINE__);

        $arr = mysql_fetch_assoc($res);

        if (!$arr)
        {
            error_message_center("error", "ERROR", "Invalid ID! Go back and try again");
        }

        if ($arr['user'] != $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        if (!isset($_GET['do_it']))
        {
            error_message_center("error", "Sanity Check", "Are you sure you would like to Delete this Comment?<br />
                                                           If so Click<a href='offers.php?action=delete_comment&amp;id=".$arr['offer']."&amp;comment_id=".$comment_id."&amp;do_it=666'>HERE</a>.");
        }
        else
        {
            sql_query('DELETE
                        FROM comments_offer
                        WHERE id='.$comment_id);

            sql_query('UPDATE offers
                        SET comments = comments - 1
                        WHERE id = '.$arr['offer']);

            header('Location: /offers.php?action=offer_details&id='.$id.'&comment_deleted=1');
            die();
        }

    break;

    //-- ALTER AN OFFER STATUS --//
    case 'alter_status':

        if ($CURUSER['class'] < UC_MODERATOR)
        {
            error_message_center("error", "ERROR", "Permission Denied !!");
        }

        $set_status = strip_tags(isset($_POST['set_status']) ? $_POST['set_status'] : '');

        //-- Add All Possible Status' Check Them To Be Sure They Are Ok --//
        $ok_stuff = array('approved',
                            'pending',
                            'denied');

        //-- Check It --//
        $change_it = (in_array($set_status, $ok_stuff) ? $set_status : 'poop');

        if ($change_it == 'poop') //-- Ok, So I Had A Bit Of Fun With That *blush --//
        {
            error_message_center("error", "ERROR", "Nice try Mr. Fancy Pants! <br />Regards");
        }

        //-- Get Torrent Name :P --//
        $res_name = sql_query('SELECT offer_name, offered_by_user_id
                               FROM offers
                               WHERE id = '.$id) or sqlerr(__FILE__, __LINE__);

        $arr_name = mysql_fetch_assoc($res_name);

        if ($change_it == 'approved')
        {
            $time_now = sqlesc(get_date_time());
            $subject  = sqlesc('Your Offer has been Approved!');
            $message  = sqlesc("Hi, \n\n An Offer you made has been Approved!!! \n\n Please  [url=".$site_url."/upload.php]Upload ".htmlspecialchars($arr_name['offer_name'], ENT_QUOTES)."[/url] as soon as possible! \n Members who Voted on it will be notified as soon as you do! \n\n [url=".$site_url."/offers.php?action=offer_details&id=".$id."]HERE[/url] is your Offer.");

            sql_query('INSERT INTO messages (sender, receiver, added, msg, subject, saved, location)
                       VALUES(0, '.$arr_name['offered_by_user_id'].', '.$time_now.', '.$message.', '.$subject.', \'yes\', 1)') or sqlerr(__FILE__, __LINE__);
        }

        if ($change_it == 'denied')
        {
            $time_now = sqlesc(get_date_time());
            $subject  = sqlesc('Your Offer has been Denied!');
            $message  = sqlesc("Hi, \n\n An Offer you made has been Denied. \n\n  [url=".$site_url."/offers.php?action=offer_details&id=".$id."]".htmlspecialchars($arr_name['offer_name'], ENT_QUOTES)."[/url] was Denied by ".$CURUSER['username'].".Please contact them to find out why.");

            sql_query('INSERT INTO messages (sender, receiver, added, msg, subject, saved, location)
                              VALUES(0, '.$arr_name['offered_by_user_id'].', '.$time_now.', '.$message.', '.$subject.', \'yes\', 1)') or sqlerr(__FILE__, __LINE__);
        }

        //--  Ok, Looks Good :d Let's Set That Status! --//
        sql_query('UPDATE offers
                    SET status = '.sqlesc($change_it).'
                    WHERE id = '.$id) or sqlerr(__FILE__, __LINE__);

        header('Location: /offers.php?action=offer_details&status_changed=1&id='.$id);
        die();

    break;

} //-- End All Actions / Switch --//

//-- Functions N' Stuff --//
function comment_table ($rows)
{
    global $CURUSER, $image_dir;

    begin_frame();

    //$count = 0;

    foreach ($rows
             AS
             $row)
    {
        print("<p class='sub'>#".$row["id"]." by ");

        if (isset($row["username"]))
        {
            $title = $row["title"];

            if ($title == "")
            {
                $title = get_user_class_name($row["class"]);
            }
            else
            {
                $title = htmlspecialchars($title);
            }

            print("<a name='comm".$row["id"]."' href='userdetails.php?id=".$row["user"]."'><span style='font-weight:bold;'>".htmlspecialchars($row["username"])."</span></a>".($row["donor"] == "yes" ? "<img src='{$image_dir}star.png' width='16' height='16' border='0' alt='Donor' title='Donor' />" : "").($row["warned"] == "yes" ? "<img src="."'{$image_dir}warned.png' width='16' height='16' border='0' alt='Warned' title='Warned' />" : "")." ($title)\n");
        }
        else
        {
            print("<a name='comm".$row["id"]."'><span style='font-style: italic;'>(Orphaned)</span></a>\n");
        }

        if ( $CURUSER['offercompos'] == 'no' )
        {
            if ( $row["user"] == $CURUSER["id"] )
            {
                print(" at ".$row["added"]." GMT&nbsp;&nbsp;<a class='btn'>Edit Disabled</a> ");
            }
        }
        else
        {
            print(" at ".$row["added"]." GMT&nbsp;&nbsp;
                ".($row["user"] != $CURUSER["id"] ? "<a class='btn' href='report.php?type=Offer_Comment&id=$row[id]'>Report Comment</a>" : "").
                ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='offers.php?action=edit_comment&amp;comment_id=$row[id]'>Edit</a>" : "").
                (get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/comment.php?action=delete&amp;cid=$row[id]'>Delete</a>" : "").
                ($row["editedby"] && get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='offers.php?action=view_original&amp;comment_id=$row[id]'>View Original</a>" : "")."</p>\n");
        }

        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");

        if (!$avatar)
        {
            $avatar = "{$image_dir}default_avatar.gif";
        }

        $text = format_comment($row["text"]);

        if ($row["editedby"])
        {
            $text .= "<p><span style='font-size: x-small; '>Last edited by <a href='/userdetails.php?id=$row[editedby]'><span style='font-weight:bold;'>$row[username]</span></a> at $row[editedat] GMT</span></p>\n";
        }

        begin_table(true);

        print("<tr valign='top'>\n");
        print("<td align='center' width='100'><img src='{$avatar}' width='125' height='125' border='0' alt='' title='' /></td>\n");
        print("<td class='text'>$text</td>\n");
        print("</tr>\n");

        end_table();
    }

    end_frame();

}

?>