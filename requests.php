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
$id              = (isset($_GET['id']) ? intval($_GET['id']) :  (isset($_POST['id']) ? intval($_POST['id']) : 0));
$comment_id      = (isset($_GET['comment_id']) ? intval($_GET['comment_id']) :  (isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0));
$category        = (isset($_GET['category']) ? intval($_GET['category']) : (isset($_POST['category']) ? intval($_POST['category']) : 0));
$requested_by_id = isset($_GET['requested_by_id']) ? intval($_GET['requested_by_id']) : 0;
$vote            = isset($_POST['vote']) ? intval($_POST['vote']) : 0;
$posted_action   = strip_tags((isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '')));

//-- Add All Possible Actions Here And Check Them To Be Sure They Are Ok --//
$valid_actions = array('add_new_request',
                        'delete_request',
                        'edit_request',
                        'request_details',
                        'vote',
                        'add_comment',
                        'edit_comment',
                        'view_orig_comment',
                        'delete_comment');

//-- Check Posted Action, And If No Action Was Posted, Show The Default Page --//
$action = (in_array($posted_action, $valid_actions) ? $posted_action : 'default');

//-- Ê :D --//
$top_menu = '<p style="text-align: center;"><a class="altlink" href="requests.php">View Requests</a> || <a class="altlink" href="requests.php?action=add_new_request">New Request</a></p>';

switch ($action)
{

//-- Let Them Vote On It --//
    case 'vote':

    if (!isset($id) || !is_valid_id($id) || !isset($vote) || !is_valid_id($vote))
    {
        error_message_center("error", "ERROR", "Bad ID / Bad Vote. Go back and try again!");
    }

    //-- See If They Voted Yet --//
    $res_did_they_vote = sql_query('SELECT vote
                                    FROM request_votes
                                    WHERE user_id = '.$CURUSER['id'].'
                                    AND request_id = '.$id);

    $row_did_they_vote = mysql_fetch_row($res_did_they_vote);

    if ($row_did_they_vote[0] == '')
    {
        $yes_or_no = ($vote == 1 ? 'yes' : 'no');

        sql_query('INSERT INTO request_votes (request_id, user_id, vote)
                    VALUES ('.$id.', '.$CURUSER['id'].', "'.$yes_or_no.'")');

        sql_query('UPDATE requests
                    SET '.($yes_or_no == 'yes' ? 'vote_yes_count = vote_yes_count + 1' : 'vote_no_count = vote_no_count + 1').'
                    WHERE id = '.$id);

        header('Location: /requests.php?action=request_details&amp;voted=1&amp;id='.$id);
        die();
    }
    else
    {
        error_message_center("error", "ERROR", "You have Voted on this Request before");
    }

break;

//-- Default First Page With All The Requests --//
case 'default':

//-- Get Stuff For The Pager --//
$count_query = sql_query('SELECT COUNT(id)
                             FROM requests');

$count_arr = mysql_fetch_row($count_query);
$count   = $count_arr[0];
$page    = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$perpage     = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;

list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'requests.php?'.($perpage == 20 ? '' : '&amp;perpage='.$perpage));

$main_query_res = sql_query('SELECT r.id AS request_id, r.request_name, r.category, r.added, r.requested_by_user_id, r.filled_by_user_id,
                                     r.filled_by_username, r.filled_torrent_id, r.vote_yes_count, r.vote_no_count, r.comments,
                                     u.id, u.username, u.warned, u.enabled, u.donor, u.class,
                                     t.id, t.anonymous,
                                     c.id AS cat_id, c.name AS cat_name, c.image AS cat_image
                                 FROM requests AS r
                                 LEFT JOIN categories AS c ON r.category = c.id
                                 LEFT JOIN torrents AS t ON r.filled_torrent_id = t.id
                                 LEFT JOIN users AS u ON r.requested_by_user_id = u.id
                                 ORDER BY r.added DESC '.$LIMIT);

echo site_header('Requests',false);

echo (isset($_GET['new']) ? '<h1>Request Added!</h1>' : '' ).(isset($_GET['request_deleted']) ? '<h1>Request Deleted!</h1>' : '' ).'';
echo '<div align="center">'.$top_menu.'<br /></div>';
echo '<div align="center">'.$menu.'<br /><br /></div>';

if ($count == 0)
{
     error_message_center("info", "Sorry", "There are NO Requests at the moment");
}

echo '<table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
         <tr>
             <td class="colhead" align="center">Type</td>
             <td class="colhead" align="center">Name</td>
             <td class="colhead" align="center">Added</td>
             <td class="colhead" align="center">Comm</td>
             <td class="colhead" align="center">Votes</td>
             <td class="colhead" align="center">Requested By</td>
             <td class="colhead" align="center">Filled</td>
             <td class="colhead" align="center">Filled By</td>
         </tr>';

while ($main_query_arr = mysql_fetch_assoc($main_query_res))
{
     echo '<tr>
             <td class="rowhead" align="center" style="margin: 0; padding: 1;"><img src="'.$image_dir.'caticons/'.htmlspecialchars($main_query_arr['cat_image'], ENT_QUOTES).'" width="60" height="54" border="0" alt="'.htmlspecialchars($main_query_arr['cat_name'], ENT_QUOTES).'"  title="'.htmlspecialchars($main_query_arr['cat_name'], ENT_QUOTES).'"/></td>

             <td class="rowhead" align="center"><a class="altlink" href="requests.php?action=request_details&amp;id='.$main_query_arr['request_id'].'">'.htmlspecialchars($main_query_arr['request_name'], ENT_QUOTES).'</a></td>

             <td class="rowhead" align="center">'.get_date_time($main_query_arr['added'],'LONG').'</td>

             <td class="rowhead" align="center">'.number_format($main_query_arr['comments']).'</td>

             <td class="rowhead" align="center">Yes: '.number_format($main_query_arr['vote_yes_count']).'<br />No: '.number_format($main_query_arr['vote_no_count']).'</td>

             <td class="rowhead" align="center">'.$main_query_arr['username'].'</td>

             <td class="rowhead" align="center">'.($main_query_arr['filled_by_user_id'] > 0 ? '<a href="details.php?id='.$main_query_arr['filled_torrent_id'].'" title="Go To Torrent Page!"><span style="color:green; font-weight:bold;">Yes!</span></a>' :'<span style="color:red; font-weight:bold;">No</span>').'</td>';

     if ($main_query_arr['filled_torrent_id'] == 0)
     {
echo'<td class="rowhead" align="center">Still<br />Waiting</td>';
     }

     if ($main_query_arr['filled_torrent_id'] >= 1)
     {
         if ($main_query_arr["anonymous"] == "no")
{
echo'<td class="rowhead" align="center">'.$main_query_arr['filled_by_username'].'</td>';
         }

         if ($main_query_arr["anonymous"] == "yes")
{
echo'<td class="rowhead" align="center"><i>Anonymous</i></td>';
         }
     }
}

echo '</tr></table>';
echo '<div align="center"><br />'.$menu.'<br /></div>';

echo site_footer();

break;

//-- Details Page For The Request --//
case 'request_details':

    if (!isset($id) || !is_valid_id($id))
    {
        error_message_center("error", "ERROR", "Bad ID! Go back and try again!");
    }

    $res = sql_query('SELECT r.id AS request_id, r.request_name, r.category, r.added, r.requested_by_user_id, r.filled_by_user_id, r.filled_torrent_id, r.vote_yes_count, r.vote_no_count, r.image, r.link, r.description, r.comments, u.id, u.username, u.warned, u.enabled, u.donor, u.class, u.uploaded, u.downloaded, c.name AS cat_name, c.image AS cat_image
                            FROM requests AS r
                            LEFT JOIN categories AS c ON r.category = c.id
                            LEFT JOIN users AS u ON r.requested_by_user_id = u.id
                            WHERE r.id = '.$id);

    $arr = mysql_fetch_assoc($res);

    //-- See If They Voted Yet --//
    $res_did_they_vote = sql_query('SELECT vote
                                    FROM request_votes
                                    WHERE user_id = '.$CURUSER['id'].'
                                    AND request_id = '.$id);

    $row_did_they_vote = mysql_fetch_row($res_did_they_vote);

    if ($row_did_they_vote[0] == '')
    {
        $vote_yes = '<form method="post" action="requests.php">
                        <input type="hidden" name="action" value="vote" />
                        <input type="hidden" name="id" value="'.$id.'" />
                        <input type="hidden" name="vote" value="1" />
                        <input type="submit" class="btn" value="Vote Yes!" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                    </form> ~ You will be notified when this Request is Filled.';

        $vote_no = '<form method="post" action="requests.php">
                        <input type="hidden" name="action" value="vote" />
                        <input type="hidden" name="id" value="'.$id.'" />
                        <input type="hidden" name="vote" value="2" />
                        <input type="submit" class="btn" value="Vote No!" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                    </form> ~ You are being a stick in the mud.';

        $your_vote_was = '';
    }
    else
    {
        $vote_yes      = '';
        $vote_no       = '';
        $your_vote_was = ' Your Vote: '.$row_did_they_vote[0].' ';
    }

    //-- Start Page --//
    echo site_header('Request Details for: '.htmlspecialchars($arr['request_name'], ENT_QUOTES));

    echo (isset($_GET['voted']) ? '<h1>Vote Added</h1>' : '' ).(isset($_GET['comment_deleted']) ? '<h1>Comment Deleted</h1>' : '' ).$top_menu.'

       <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
           <tr>
                <td class="colhead" align="center" colspan="2">
                    <h1>'.htmlspecialchars($arr['request_name'], ENT_QUOTES).($CURUSER['class'] < UC_MODERATOR ? '' : ' [ <a href="requests.php?action=edit_request&amp;id='.$id.'">Edit</a> ][ <a href="requests.php?action=delete_request&amp;id='.$id.'">Delete</a> ]').'</h1>
                </td>
            </tr>

        <tr>
            <td class="rowhead" align="left" width="20%">&nbsp;<strong>Image:</strong></td>
            <td class="rowhead" align="left"><a href="'.$arr['image'].'" rel="lightbox"><img src="'.strip_tags($arr['image']).'" width="" height="" alt="Posted Image" title="Posted Image" style="max-width:600px;" /></a></td>
        </tr>

        <tr>
            <td class="rowhead" align="left">&nbsp;<strong>Description:</strong></td>
            <td class="rowhead" align="left">'.format_comment($arr['description']).'</td>
        </tr>

        <tr>
            <td class="rowhead" align="left">&nbsp;<strong>Category:</strong></td>
            <td class="rowhead"><img src="'.$image_dir.'caticons/'.htmlspecialchars($arr['cat_image'], ENT_QUOTES).'" width="60" height="54" border="0" alt="'.htmlspecialchars($arr['cat_name'], ENT_QUOTES).'" title="'.htmlspecialchars($arr['cat_name'], ENT_QUOTES).'" /></td>
        </tr>

        <tr>
            <td class="rowhead" align="left">&nbsp;<strong>Link:</strong></td>
            <td class="rowhead" align="left"><a class="altlink" href="'.htmlspecialchars($arr['link'], ENT_QUOTES).'" target="_blank">'.htmlspecialchars($arr['link'], ENT_QUOTES).'</a></td>
        </tr>

        <tr>
            <td class="rowhead" align="left">&nbsp;<strong>Votes:</strong></td>
            <td class="rowhead" align="left">
                <span style="font-weight:bold; color:green;">Yes: '.number_format($arr['vote_yes_count']).'</span> '.$vote_yes.'<br />
                <span style="font-weight:bold; color:red;">No: '.number_format($arr['vote_no_count']).'</span> '.$vote_no.'<br /> '.$your_vote_was.'
            </td>
        </tr>

        <tr>
            <td class="rowhead" align="left">&nbsp;<strong>Requested by:</strong></td>
            <td class="rowhead" align="left">'.$arr['username'].'</td>
        </tr>';

    if ($arr['filled_torrent_id'] > 0)
    {
        echo'<tr>
                 <td class="rowhead" align="left">&nbsp;<strong>Filled:</strong></td>
                 <td class="rowhead" align="left"><a class="altlink" href="details.php?id='.$arr['filled_torrent_id'].'">Yes. Click To View Torrent!</a></td>
             </tr>';
    }

    echo'<tr>
             <td class="rowhead" align="left">&nbsp;<strong>Report Request</strong></td>
             <td class="rowhead" align="left">
                <form action="report.php?type=Request&amp;id='.$id.'" method="post">
                    <input type="submit" class="btn" value="Report This Request" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />&nbsp;For breaking the <a class="altlink" href="rules.php">Rules</a>
                </form>
            </td>
        </tr>

        </table>';

    echo'<h1>Comments for '.htmlentities($arr['request_name'], ENT_QUOTES ).'</h1>
         <p><a name="startcomments"></a></p>';

    if ( $CURUSER['requestcompos'] == 'no' )
    {
        $commentbar = '<p align="center">Comment Privilege Disabled</p>';
    }
    else
    {
        $commentbar = '<p align="center"><a class="index" href="requests.php?action=add_comment&amp;id='.$id.'">Add a Comment</a></p>';
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
        $perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;

        list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'requests.php?action=request_details&amp;id='.$id, ($perpage == 20 ? '' : '&amp;perpage='.$perpage).'#comments');

        $subres = sql_query("SELECT comments_request.id, text, user, comments_request.added, editedby, editedat, avatar, warned, username, title, class, donor
                                FROM comments_request
                                LEFT JOIN users ON comments_request.user = users.id
                                WHERE request = $id
                                ORDER BY comments_request.id ".$LIMIT) or sqlerr(__FILE__, __LINE__);

        while ($subrow = mysql_fetch_assoc($subres))

        $allrows[] = $subrow;

        echo $commentbar.'<a name="comments"></a>';
        echo ($count > $perpage) ? '<p>'.$menu.'<br /></p>' : '<br />';

        echo comment_table($allrows);

        echo ($count > $perpage) ? '<p>'.$menu.'<br /></p>' : '<br />';
    }

    echo $commentbar;
    echo site_footer();

break;

//-- Add A New Request --//
case 'add_new_request':

    $request_name = strip_tags(isset($_POST['request_name']) ? trim($_POST['request_name']) : '');
    $image        = strip_tags(isset($_POST['image']) ? trim($_POST['image']) : '');
    $body         = (isset($_POST['description']) ? trim($_POST['description']) : '');
    $link         = strip_tags(isset($_POST['link']) ? trim($_POST['link']) : '');

    //-- do the cat list :D
    $category_drop_down = '<select name="category" class="required"><option class="body" value="">Select Request Category</option>';
    $cats               = genrelist();

    foreach ($cats
             AS
             $row)

    {
        $category_drop_down .= '<option class="body" value="'.$row['id'].'" '.($category == $row['id'] ? ' selected="selected"' : '').' >'.htmlspecialchars($row['name']).'</option>';
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

    //-- If Posted And Not Preview, Process It :D --//
    if (isset($_POST['button']) && $_POST['button'] == 'Submit')
    {
        sql_query ('INSERT INTO requests (request_name, image, description, category, added, requested_by_user_id, link)
                           VALUES ('.sqlesc($request_name).', '.sqlesc($image).', '.sqlesc($body).', '.$category.', '.time().', '.$CURUSER['id'].',  '.sqlesc($link).');');

        $new_request_id = mysql_insert_id();

        header('Location: requests.php?action=request_details&new=1&id='.$new_request_id);

        die();
    }

    //-- Start Page --//
    echo site_header('Add New Request.');

    echo'<table class="main" border="0" align="center" width="80%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="embedded" align="center">
                    <h1 style="text-align: center;">New Request</h1>'.$top_menu.'
                    <form method="post" action="requests.php?action=add_new_request" name="request_form" id="request_form">
                    '.(isset($_POST['button']) && $_POST['button'] == 'Preview' ? '<br />

                        <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
                            <tr>
                                <td class="colhead" align="center" colspan="2"><h1>Preview</h1></td>
                            </tr>

                            <tr>
                                <td class="rowhead" align="right">Image:</td>
                                <td class="rowhead" align="left"><img src="'.htmlspecialchars($image, ENT_QUOTES).'" width="" height="" border="0" alt="Posted Image" title="Posted Image" style="max-width:600px;" /></td>
                            </tr>

                            <tr>
                                <td class="rowhead" align="right">Description:</td>
                                <td class="rowhead" align="left">'.format_comment($body).'</td>
                            </tr>

                            <tr>
                                <td class="rowhead" align="right">Category:</td>
                                <td class="rowhead" align="left"><img src="'.$image_dir.'caticons/'.htmlspecialchars($cat_image, ENT_QUOTES).'" width=60" height="54" border="0" alt="'.htmlspecialchars($cat_name, ENT_QUOTES).'" title="'.htmlspecialchars($cat_name, ENT_QUOTES).'" /></td>
                            </tr>

                            <tr>
                                <td class="rowhead" align="right">Link:</td>
                                <td class="rowhead" align="left"><a class="altlink" href="'.htmlspecialchars($link, ENT_QUOTES).'" target="_blank">'.htmlspecialchars($link, ENT_QUOTES).'</a></td>
                            </tr>

                            <tr>
                                <td class="rowhead" align="right">Requested by:</td>
                                <td class="rowhead" align="left">'.$CURUSER['username'].'</td>
                            </tr>

                        </table><br />' : '').' ';

    echo'<table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
            <tr>
                <td class="colhead" align="center" colspan="2"><h1>Making a Request</h1></td>
            </tr>

            <tr>
                <td class="rowhead" align="center" colspan="2">Before you make a Request, <a class="altlink" href="search.php">Search</a>
                 to be sure it has not yet been Requested, Offered, or Uploaded!<br /><br />
                 Be sure to fill in all fields!</td>
            </tr>

            <tr>
                <td class="rowhead" align="right">Name:</td>
                <td class="rowhead" align="left">
                    <input type="text" class="required" size="80" name="request_name" value="'.htmlspecialchars($request_name, ENT_QUOTES).'" />
                </td>
            </tr>

            <tr>
                <td class="rowhead" align="right">Image:</td>
                <td class="rowhead" align="left">
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
                    <input type="submit" class="btn" name="button" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                    <input type="submit" class="btn" name="button" value="Submit" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                </td>
            </tr>
        </table></form>
    </td></tr></table><br />';

    echo'<script type="text/javascript" src="scripts/jquery.validate.min.js"></script>
         <script type="text/javascript">
         <!--

         $(document).ready(function()
         {
             //=== form validation
             $("#request_form").validate();
         }
         );

         -->
         </script>';

    echo site_footer();

break;

//-- Edit A Request --//
case 'edit_request':

    if ($CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "ERROR", "Permission Denied!");
    }

    if (!isset($id) || !is_valid_id($id))
    {
        error_message_center("error", "Sorry", "Bad ID!");
    }

    $edit_res = sql_query('SELECT request_name, image, description, category, requested_by_user_id, filled_by_user_id, filled_torrent_id, link
                            FROM requests
                            WHERE id ='.$id) or sqlerr(__FILE__,__LINE__);

    $edit_arr = mysql_fetch_assoc($edit_res);

    if ($CURUSER['class'] < UC_MODERATOR && $CURUSER['id'] !== $edit_arr['requested_by_user_id'])
    {
        error_message_center("error", "Sorry", "This is NOT Your Request to Edit");
    }

/*
    $filled_by = '';

    if ($edit_arr['filled_by_user_id'] > 0)
    {
        $filled_by_res = sql_query('SELECT id, username, warned, enabled, donor, class
                                    FROM users
                                    WHERE id ='.sqlesc($edit_arr['filled_by_user_id'])) or sqlerr(__FILE__,__LINE__);

        $filled_by_arr = mysql_fetch_assoc($filled_by_res);
        $filled_by     = 'this Request was filled by '.format_user($filled_by_arr);
    }
*/

    $request_name = strip_tags(isset($_POST['request_name']) ? trim($_POST['request_name']) : $edit_arr['request_name']);
    $image        = strip_tags(isset($_POST['image']) ? trim($_POST['image']) : $edit_arr['image']);
    $body         = (isset($_POST['description']) ? trim($_POST['description']) : $edit_arr['description']);
    $link         = strip_tags(isset($_POST['link']) ? trim($_POST['link']) : $edit_arr['link']);
    $category     = (isset($_POST['category']) ? intval($_POST['category']) : $edit_arr['category']);

    //-- Do The Cat List :D --//
    $category_drop_down = '<select name="category" class="required"><option class="body" value="">Select Request Category</option>';
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

    $cat_arr = mysql_fetch_assoc($cat_res);

    $cat_image = htmlspecialchars($cat_arr['cat_image'], ENT_QUOTES);
    $cat_name  = htmlspecialchars($cat_arr['cat_name'], ENT_QUOTES);

    //-- If Posted And Not Preview, Process It :D --//
    if (isset($_POST['button']) && $_POST['button'] == 'Edit')
    {
        if (isset($_POST['filled_by']) && $_POST['filled_by'] == '1')
        {
            $filled_by_user_id = ("0");
            $filled_torrent_id = ("0");

            sql_query ('UPDATE requests
                        SET request_name = '.sqlesc($request_name).', image = '.sqlesc($image).', description = '.sqlesc($body).', category = '.sqlesc($category).', link = '.sqlesc($link).', filled_by_user_id = '.sqlesc($filled_by_user_id).', filled_torrent_id = '.sqlesc($filled_torrent_id).'
                        WHERE id = '.$id);
        }
        else
        {
            sql_query ('UPDATE requests
                        SET request_name = '.sqlesc($request_name).', image = '.sqlesc($image).', description = '.sqlesc($body).', category = '.sqlesc($category).', link = '.sqlesc($link).'
                        WHERE id = '.$id);
        }

        header('Location: requests.php?action=request_details&edited=1&id='.$id);
        die();
    }

    //-- Start Page --//
    echo site_header('Edit Request.');

    echo'<table border="0" align="center" width="80%" cellspacing="0" cellpadding="0">
        <tr>
            <td class="embedded" align="center">
                <h1 style="text-align: center;">Edit Request</h1>'.$top_menu.'
                <form method="post" action="requests.php?action=edit_request" name="request_form" id="request_form">
                    <input type="hidden" name="id" value="'.$id.'" />'.(isset($_POST['button']) && $_POST['button'] == 'Preview' ? '<br />' : '').' ';

    echo'<table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
        <tr>
            <td class="colhead" align="center" colspan="2"><h1>'.htmlspecialchars($request_name, ENT_QUOTES).'</h1></td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Image:</td>
            <td class="rowhead" align="left"><a href="'.$image.'" rel="lightbox"><img src="'.htmlspecialchars($image, ENT_QUOTES).'" width="" height="" alt="Posted Image" title="Posted Image" style="max-width:600px;" /></a></td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Description:</td>
            <td class="rowhead" align="left">'.format_comment($body).'</td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Category:</td>
            <td class="rowhead" align="left"><img src="'.$image_dir.'caticons/'.htmlspecialchars($cat_image, ENT_QUOTES).'" width="60" height="54" border="0" alt="'.htmlspecialchars($cat_name, ENT_QUOTES).'" title="'.htmlspecialchars($cat_name, ENT_QUOTES).'" /></td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Link:</td>
            <td class="rowhead" align="left"><a class="altlink" href="'.htmlspecialchars($link, ENT_QUOTES).'" target="_blank">'.htmlspecialchars($link, ENT_QUOTES).'</a></td>
        </tr>
        </table><br />';

    echo'<table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">
        <tr>
            <td class="colhead" align="center" colspan="2"><h1>Edit Request</h1></td>
        </tr>

        <tr>
            <td align="center" colspan="2" class="rowhead">Be sure to Fill in ALL Fields!</td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Name:</td>
            <td class="rowhead" align="left">
                <input type="text" class="required" size="80" name="request_name" value="'.htmlspecialchars($request_name, ENT_QUOTES).'" />
            </td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Image:</td>
            <td class="rowhead" align="left">
                <input type="text" class="required" size="80" name="image" value="'.htmlspecialchars($image, ENT_QUOTES).'" /></td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Link:</td>
            <td class="rowhead" align="left">
                <input type="text" class="required" size="80" name="link" value="'.htmlspecialchars($link, ENT_QUOTES).'" /></td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Category:</td>
            <td class="rowhead" align="left">'.$category_drop_down.'</td>
        </tr>

        <tr>
            <td class="rowhead" align="right">Description:</td>
            <td class="rowhead" align="left">'.textbbcode("compose","description",$body).'</td>
        </tr>';

    if ($edit_arr['filled_torrent_id'] > 0)
    {
        echo'<tr>
                <td class="rowhead" align="right">Reset Request:</td>
                <td class="rowhead" align="left">
                    <input type="radio" name="filled_by" value="0" '.($filled_by ? ' checked="checked"' : '').' />No
                    <input type="radio" name="filled_by" value="1" '.(!$filled_by ? ' checked="checked"' : '').' />Yes
                </td>
            </tr>';
    }

    echo'<tr>
            <td class="rowhead" align="center" colspan="2">
                <input type="submit" class="btn" name="button" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
                <input type="submit" class="btn" name="button" value="Edit" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
            </td>
        </tr>
        </table></form>
        </td></tr></table><br />

        <script type="text/javascript" src="scripts/jquery.validate.min.js"></script>
        <script type="text/javascript">
        <!--

        $(document).ready(function()
        {
            //=== form validation
            $("#request_form").validate();
        });

        -->
        </script>';

    echo site_footer();

break;

//-- Delete A Request --//
case 'delete_request':

    if ($CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "ERROR", "Permission Denied!");
    }

    if (!isset($id) || !is_valid_id($id))
    {
        error_message_center("error", "Error", "Bad ID!");
    }

        $res = sql_query('SELECT request_name, requested_by_user_id
                          FROM requests
                          WHERE id ='.$id) or sqlerr(__FILE__,__LINE__);

        $arr = mysql_fetch_assoc($res);

    if (!$arr)
    {
        error_message_center("error", "Error", "Invalid ID!");
    }

    if ($arr['requested_by_user_id'] !== $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "Error", "Permission Denied");
    }

    if (!isset($_GET['do_it']))
    {
        error_message_center("info", "Sanity Check", "Are you sure you would like to Delete the Request <b>
                                                      : - ".htmlspecialchars($arr['request_name'])."</b>.<br />
                                                      If so click&nbsp;
                                                      <a class='altlink' href='requests.php?action=delete_request&amp;id=".$id."&amp;do_it=666' >HERE</a>.");
    }
    else
    {
        sql_query('DELETE FROM requests
                          WHERE id='.$id);

        sql_query('DELETE FROM request_votes
                          WHERE request_id ='.$id);

        sql_query('DELETE FROM comments_request
                          WHERE request ='.$id);

        header('Location: /requests.php?request_deleted=1');
        die();
    }

    echo site_footer();

break;

//-- Add A Comment --//
case 'add_comment':

    if ( $CURUSER['requestcompos'] == 'no' )
    {
        error_message_center("error", "ERROR", "Comment Privilege Disabled");
    }

    global $CURUSER, $image_dir;

    if (!isset($id) || !is_valid_id($id))
    {
        error_message_center("error", "ERROR", "Bad ID! Go back and try again");
    }

        $res = sql_query('SELECT request_name
                          FROM requests
                          WHERE id = '.$id) or sqlerr(__FILE__,__LINE__);

        $arr = mysql_fetch_array($res);

    if (!$arr)
    {
        error_message_center("error", "ERROR", "No Request with that ID!");
    }

    if(isset($_POST['button']) && $_POST['button'] == 'Save')
    {
        $text = (isset($_POST['text']) ? trim($_POST['text']) : '');

        if (!$text)
        {
            error_message_center("error", "ERROR", "Comment body cannot be empty! Go back and try again");
        }

        sql_query("INSERT INTO comments_request (user, request, added, text, ori_text)
                          VALUES (".$CURUSER["id"].",$id, '".get_date_time()."', ".sqlesc($text).",".sqlesc($text).")");

        $newid = mysql_insert_id();

        sql_query("UPDATE requests
                   SET comments = comments + 1
                   WHERE id = $id");

        header('Location: /requests.php?action=request_details&id='.$id.'&viewcomm='.$newid.'#comm'.$newid);
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

    echo $top_menu.'<form method="post" action="requests.php?action=add_comment">
                    <input type="hidden" name="id" value="'.$id.'"/>
                    '.(isset($_POST['button']) && $_POST['button'] == 'Preview' ? '

    <table border="0" align="center" width="80%" cellspacing="5" cellpadding="5">

    <tr>
        <td class="colhead" colspan="2"><h1>Preview</h1></td>
    </tr>

    <tr>
        <td align="center" width="125"><img src='.$avatar.' width="125" height="125" border="0" alt="" title="" /></td>
        <td class="rowhead" align="left" valign="top">'.format_comment($text).'</td>
    </tr>

    </table><br />' : '').'

    <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">

    <tr>
        <td class="colhead" align="center" colspan="2">
        <h1>Add a Comment to "'.$arr['request_name'].'"</h1>
        </td>
    </tr>

    <tr>
        <td class="rowhead" align="right" valign="top"><b>Comment:</b></td>
        <td class="rowhead">'.textbbcode("compose","text",$text).'</td>
    </tr>

    <tr>
        <td class="rowhead" align="center" colspan="2">
            <input type="submit" class="btn" name="button" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
            <input type="submit" class="btn" name="button" value="Save" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
        </td>
    </tr>

    </table></form>';

//-- View Existing Comments --//
    $res = sql_query('SELECT r.request, r.id, r.text, r.added, r.editedby, r.editedat,
                             u.id, u.username, u.warned, u.enabled, u.donor, u.class, u.avatar, u.title
                             FROM comments_request AS r
                             LEFT JOIN users AS u ON r.user = u.id
                             WHERE request = '.$id.'
                             ORDER BY r.id DESC LIMIT 5');

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

    if ( $CURUSER['requestcompos'] == 'no' )
    {
        error_message_center("error", "ERROR", "Comment Privilege Disabled");
    }

    if (!isset($comment_id) || !is_valid_id($comment_id))
    {
        error_message_center("error", "ERROR", "Bad ID! Go back and try again");
    }

    $res = sql_query('SELECT c.*, r.request_name
                      FROM comments_request AS c
                      LEFT JOIN requests AS r ON c.request = r.id
                      WHERE c.id='.$comment_id) or sqlerr(__FILE__,__LINE__);

    $arr = mysql_fetch_assoc($res);

    if (!$arr)
    {
        error_message_center("error", "ERROR", "Invalid ID! Go back and try again");
    }

    if ($arr['user'] != $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "ERROR", "Permission Denied!");
    }

    $body = htmlspecialchars((isset($_POST['edit']) ? $_POST['edit'] : $arr['text']));

    if(isset($_POST['button']) && $_POST['button'] == 'Edit')
    {
        if ($body == '')
        {
            error_message_center("error", "ERROR", "Comment body can not be empty!");
        }

        $text     = sqlesc($body);
        $editedat = sqlesc(get_date_time());

        sql_query("UPDATE comments_request
                    SET text = $text, editedat = $editedat, editedby = $CURUSER[id]
                    WHERE id = $comment_id") or sqlerr(__FILE__, __LINE__);

        header('Location: /requests.php?action=request_details&id='.$id.'&viewcomm='.$comment_id.'#comm'.$comment_id);
        die();
    }

    $res = sql_query('SELECT avatar
                      FROM users
                      WHERE id = '.$CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

    $row = mysql_fetch_array($res);

    $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");

    if (!$avatar)
    {
        $avatar = "{$image_dir}default_avatar.gif";
    }

    echo site_header('Edit Comment to "'.htmlentities($arr['request_name'], ENT_QUOTES ).'"');

    echo $top_menu.'<form method="post" action="requests.php?action=edit_comment">
                        <input type="hidden" name="id" value="'.$arr['request'].'"/>
                        <input type="hidden" name="comment_id" value="'.$comment_id.'"/>'.
                        (isset($_POST['button']) && $_POST['button'] == 'Preview' ? '

    <table border="0" align="center" width="80%" cellspacing="5" cellpadding="5">

    <tr>
        <td class="colhead" colspan="2"><h1>Preview</h1></td>
    </tr>

    <tr>
        <td align="center" width="125"><img src="'.$avatar.'" width="125" height="125" border="0" alt="" title="" /></td>
        <td class="rowhead" align="left" valign="top">'.format_comment($text).'</td>
    </tr>

    </table><br />' : '').'

    <table border="0" align="center" width="80%" cellspacing="0" cellpadding="5">

    <tr>
        <td class="colhead" align="center" colspan="2"><h1>Edit Comment to "'.htmlspecialchars($arr['request_name'], ENT_QUOTES).'"</h1></td>
    </tr>

    <tr>
        <td class="rowhead" align="right" valign="top"><b>Comment:</b></td>
        <td class="rowhead">'.textbbcode("compose","edit",$body).'</td>
    </tr>

    <tr>
        <td class="rowhead" align="center" colspan="2">
            <input type="submit" class="btn" name="button" value="Preview" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'btn\'" />
            <input type="submit" class="btn" name="button" value="Edit" onmouseover="this.className=\'btn\'" onmouseout="this.className=\'bbtn\'" /></td>
    </tr>

    </table></form>';

    echo site_footer();

break;

//-- View Original Comment --//
case 'view_orig_comment':

    if ($CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "ERROR", "Permission Denied!");
    }

    if (!isset($comment_id) || !is_valid_id($comment_id))
    {
        error_message_center("error", "ERROR", "Bad ID! Go back and try again");
    }

    $res = sql_query('SELECT c.*, r.request_name
                      FROM comments_request AS c
                      LEFT JOIN requests AS r ON c.request = r.id
                      WHERE c.id='.$comment_id) or sqlerr(__FILE__,__LINE__);

    $arr = mysql_fetch_assoc($res);

    if (!$arr)
    {
        error_message_center("error", "ERROR", "Invalid ID! Go back and try again");
    }

    if ($arr['user'] != $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "ERROR", "Permission Denied!");
    }

    site_header("Original Comment");

    print("<h1>Original contents of comment #$comment_id</h1>\n");
    print("<table border='1' width='100%' cellspacing='0' cellpadding='5'>");
    print("<tr>");
    print("<td class='comment'>\n");
    print format_comment($arr["ori_text"]);
    print("</td>");
    print("</tr>");
    print("</table><br />");

    $returnto = htmlspecialchars($_SERVER["HTTP_REFERER"]);

    if ($returnto)
    {
        error_message_center("info", "INFO ", "Return to the Request Details Page.<br />
                                               Click <a class='altlink' href='$returnto'>HERE</a>");
    }

    site_footer();

break;

//-- Delete A Comment --//
case 'delete_comment':

    if ($CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "ERROR", "Permission Denied!");
    }

    if (!isset($comment_id) || !is_valid_id($comment_id))
    {
        error_message_center("error", "ERROR", "Bad ID! Go back and try again.");
    }

    $res = sql_query('SELECT user, request
                        FROM comments_request
                        WHERE id='.$comment_id) or sqlerr(__FILE__,__LINE__);

    $arr = mysql_fetch_assoc($res);

    if (!$arr)
    {
        error_message_center("error", "ERROR", "Invalid ID! Go back and try again");
    }

    if ($arr['user'] != $CURUSER['id'] && $CURUSER['class'] < UC_MODERATOR)
    {
        error_message_center("error", "ERROR", "Permission Denied!");
    }

    if (!isset($_GET['do_it']))
    {
        error_message_center("info", "Sanity Check", "Are you sure you would like to Delete this Comment?<br />
                                                       If so Click<a href='requests.php?action=delete_comment&amp;id=".$arr['request']."&amp;comment_id=".$comment_id."&amp;do_it=666'>HERE</a>.");
    }
    else
    {
        sql_query('DELETE
                    FROM comments_request
                    WHERE id='.$comment_id);

        sql_query('UPDATE requests
                    SET comments = comments - 1
                    WHERE id = '.$arr['request']);

        header('Location: /requests.php?action=request_details&id='.$id.'&comment_deleted=1');
        die();
    }

break;

}
//-- End All Actions / Switch --//

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
            print("<a name='comm".$row["id"]."'><span style='font-style: italic;'>(orphaned)</span></a>\n");
        }

        if ( $CURUSER['requestcompos'] == 'no' )
        {
            if ( $row["user"] == $CURUSER["id"] )
            {
            print(" at ".$row["added"]." GMT&nbsp;&nbsp;<a class='btn'>Edit Disabled</a> ");
            }
        }
        else
        {
            print(" at ".$row["added"]." GMT&nbsp;&nbsp;
            ".($row["user"] != $CURUSER["id"] ? "<a class='btn' href='report.php?type=Request_Comment&amp;id=$row[id]'>Report Comment</a>" : "").
            ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='requests.php?action=edit_comment&amp;comment_id=$row[id]'>Edit</a>" : "").
            (get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='/requests.php?action=delete_comment&amp;comment_id=$row[id]'>Delete</a>" : "").
            ($row["editedby"] && get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<a class='btn' href='requests.php?action=view_orig_comment&amp;comment_id=$row[id]'>View Original</a>" : "")."</p>\n");
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
        print("<td align='center' width='125'><img src='{$avatar}' width='125' height='125' border='0' alt='' title='' /></td>\n");
        print("<td class='text'>$text</td>\n");
        print("</tr>\n");

        end_table();
    }

    end_frame();

}

?>