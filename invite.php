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

db_connect(true);
logged_in();

$do            = (isset($_GET["do"]) ? $_GET["do"] : (isset($_POST["do"]) ? $_POST["do"] : ''));
$valid_actions = array('create_invite', 'delete_invite', 'confirm_account', 'view_page');
$do            = (($do && in_array($do,$valid_actions,true)) ? $do : '') or header("Location: ?do=view_page");

//-- Show The Default Fist Page --//
if ($do == 'view_page')
{
    $query = sql_query("SELECT *
                        FROM users
                        WHERE invitedby = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

    $rows = mysql_num_rows($query);

    site_header('Invites');

    echo("<table border='1' width='81%' cellspacing='0' cellpadding='5'>");
    echo("<tr><td class='colhead' align='center' colspan='7'><strong>Invited Users</strong></td></tr>");

    if(!$rows)
    {
        echo("<tr><td class='rowhead' align='center' colspan='7'>No Invitees yet.</td></tr>");
    }
    else
    {
        echo ("<tr>
                   <td class='rowhead' align='center'><strong>Username</strong></td>
                   <td class='rowhead' align='center'><strong>Uploaded</strong></td>
                   <td class='rowhead' align='center'><strong>Downloaded</strong></td>
                   <td class='rowhead' align='center'><strong>Ratio</strong></td>
                   <td class='rowhead' align='center'><strong>Status</strong></td>
                   <td class='rowhead' align='center'><strong>Confirm</strong></td>
               </tr>");

        for ($i = 0; $i < $rows; ++$i)
        {
            $arr = mysql_fetch_assoc($query);

            if ($arr['status'] == 'pending')
            {
                $user = "".htmlspecialchars($arr['username'])."";
            }
            else
            {
                $user = "<a href='userdetails.php?id=$arr[id]'>".htmlspecialchars($arr['username'])."</a>
                        ".($arr["warned"] == "yes" ?"&nbsp;<img src='".$image_dir."warned.png' border='0' width='16' height='16' alt='Warned' title='Warned'>" : "")."&nbsp;
                        ".($arr["enabled"] == "no" ?"&nbsp;<img src='".$image_dir."disabled.png' border='0' width='16' height='16' alt='Disabled' title='Disabled'>" : "")."&nbsp;
                        ".($arr["donor"] == "yes" ?"<img src='".$image_dir."star.png' width='16' height='16' border='0' alt='Donor' title='Donor'>" : "")." ";
            }

            if ($arr['downloaded'] > 0)
            {
                $ratio = number_format($arr['uploaded'] / $arr['downloaded'], 3);
                $ratio = "<font color='".get_ratio_color($ratio)."'>".$ratio."</font>";
            }
            else
            {
                if ($arr['uploaded'] > 0)
                {
                    $ratio = 'Inf.';
                }
                else
                {
                    $ratio = '---';
                }
            }

            if ($arr["status"] == 'confirmed')
            {
                $status = "<font color='#1f7309'>Confirmed</font>";
            }
            else
            {
                $status = "<font color='#ca0226'>Pending</font>";
            }

            echo ("<tr>
                  <td class='rowhead'align='center'>".$user."</td>
                  <td class='rowhead'align='center'>".mksize($arr['uploaded'])."</td>
                  <td class='rowhead'align='center'>".mksize($arr['downloaded'])."</td>
                  <td class='rowhead'align='center'>".$ratio."</td>
                  <td class='rowhead'align='center'>".$status."</td>");

            if ($arr['status'] == 'pending')
            {
                echo("<td class='rowhead' align='center'><a href='?do=confirm_account&amp;userid=".$arr['id']."&amp;sender=".$CURUSER['id']."'><img src='".$image_dir."rep.png' width='24' height='25' border='0' alt='Confirm Invite' title='Confirm Invite' /></a></td>");
            }
            else
            {
                echo("<td class='rowhead' align='center'>---</td>");
            }
        }

        echo("</tr>");
        echo("</table><br />");
    }

    $select = sql_query("SELECT *
                           FROM invite_codes
                           WHERE sender = ".$CURUSER['id']."
                           AND status = 'Pending'") or sqlerr(__FILE__, __LINE__);

    $num_row = mysql_num_rows($select);

    echo("<table border='1' width='81%' cellspacing='0' cellpadding='5'>");
    echo("<tr><td class='colhead' align='center' colspan='6'><strong>Created Invite Codes</strong></td></tr>");

    if (!$num_row)
    {
        echo("<tr><td class='rowhead' align='center' colspan='6'>You have No Created Invite Codes at the moment!</td></tr>");
    }
    else
    {
        echo("<tr>
              <td class='rowhead' align='center'><strong>Invite Code</strong></td>
              <td class='rowhead' align='center'><strong>Created Date</strong></td>
              <td class='rowhead' align='center'><strong>Delete</strong></td>
              <td class='rowhead' align='center'><strong>Status</strong></td>
              </tr>");

        for ($i = 0; $i < $num_row; ++$i)
        {
            $fetch_assoc = mysql_fetch_assoc($select);

            echo("<tr>
                  <td class='rowhead' align='center'>".$fetch_assoc['code']."</td>
                  <td class='rowhead' align='center'>".get_elapsed_time(sql_timestamp_to_unix_timestamp($fetch_assoc['invite_added']))." ago</td>");

            echo("<td class='rowhead' align='center'><a href='?do=delete_invite&amp;id=".$fetch_assoc['id']."&amp;sender=".$CURUSER['id']."'><img src='images/button_delete2.gif' width-'12' height='14' border='0' alt='Delete' title='Delete' /></a></td>
                  <td class='rowhead' align='center'>".$fetch_assoc['status']."</td>
                  </tr>");
        }
    }

    echo("<tr>
          <td class='rowhead' align='center' colspan='7'>
            <form action='?do=create_invite' method='post'><input type='submit' class='btn' value='Create Invite Code' style='height: 20px' /></form>
          </td>
        </tr>");

    echo '</table>';

    site_footer();

    die();
}

//-- Create The Invite --//
if ($do =='create_invite')
{
    if ($CURUSER['invites'] <= 0)
    {
        error_message_center("error", "Error", "No Invites!");
    }

    if ($CURUSER["invite_rights"] == 'no')
    {
        error_message_center("error","Error", "Your Invite Sending Privileges has been Disabled by the Staff!");
    }

    $res = sql_query("SELECT COUNT(*)
                        FROM users") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    if ($arr[0] >= $invites)
    {
        error_message_center("error", "Error", "Sorry, User Limit Reached. Please try again later.");
    }

    $invite = md5(mksecret());

    sql_query("INSERT INTO invite_codes (sender, invite_added, code)
                VALUES (".sqlesc((int)$CURUSER['id']).", ".sqlesc(get_date_time()).", ".sqlesc($invite).")") or sqlerr(__FILE__, __LINE__);

    sql_query("UPDATE users
                SET invites = invites - 1
                WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

    header("Location: ?do=view_page");
}

//-- Delete The Invite --//
if ($do =='delete_invite')
{
    $id = (isset($_GET["id"]) ? (int)$_GET["id"] : (isset($_POST["id"]) ? (int)$_POST["id"] : ''));

    $query = sql_query('SELECT *
                            FROM invite_codes
                            WHERE id = '.sqlesc($id).'
                            AND sender = '.sqlesc($CURUSER['id']).'
                            AND status = "Pending"') or sqlerr(__FILE__, __LINE__);

    $assoc = mysql_fetch_assoc($query);

    if (!$assoc)
    {
        error_message_center("error", "Error", "This Invite Code Does Not Exist.");
    }

    isset($_GET['sure']) && $sure = htmlentities($_GET['sure']);

    if (!$sure)
    {
        error_message_center("info", "Sanity Check", "Are you sure you want to Delete this Invite Code?
                                              Click <a href='".$_SERVER['PHP_SELF']."?do=delete_invite&amp;id=".$id."&amp;sender=".$CURUSER['id']."&amp;sure=yes'>HERE</a>
                                              to Delete it or Click <a href='?do=view_page'>HERE</a> to go back.");
    }

    sql_query("DELETE FROM invite_codes
                WHERE id = ".sqlesc($id)."
                AND sender = ".sqlesc($CURUSER['id']."
                AND status = 'Pending'")) or sqlerr(__FILE__, __LINE__);

    sql_query("UPDATE users
                 SET invites = invites + 1
                 WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

    header("Location: ?do=view_page");
}

//-- Confirm The Invite --//
if ($do ='confirm_account')
{
    $userid = (isset($_GET["userid"]) ? (int)$_GET["userid"] : (isset($_POST["userid"]) ? (int)$_POST["userid"] : ''));

    if (!is_valid_id($userid))
    {
        error_message_center("error", "Error", "Invalid ID.");
    }

    $select = sql_query("SELECT id, username
                           FROM users
                           WHERE id = ".sqlesc($userid)."
                           AND invitedby = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

    $assoc = mysql_fetch_assoc($select);

    if (!$assoc)
    {
        error_message_center("error", "Error", "No User with this ID!");
    }

    isset($_GET['sure']) && $sure = htmlentities($_GET['sure']);

    if (!$sure)
    {
        error_message_center("info" , "Confirm Account", "Are you sure you want to Confirm ".htmlspecialchars($assoc['username'])." 's Account?<br />
                                                          Click <a href='?do=confirm_account&amp;userid=".$userid."&amp;sender=".$CURUSER['id']."&amp;sure=yes'>HERE</a> to Confirm it.<br />
                                                          Or Click <a href='?do=view_page'>HERE</a> to go back.");
    }

    sql_query("UPDATE users
                 SET status = 'confirmed'
                 WHERE id = ".sqlesc($userid)."
                 AND invitedby = ".sqlesc($CURUSER['id'])."
                 AND status='pending'") or sqlerr(__FILE__, __LINE__);

    header("Location: ?do=view_page");
}

?>