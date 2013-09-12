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

// Connect To DB & Check Login --//
db_connect();
logged_in();

parked();

// Define constants
define('PM_DELETED', 0); //-- Message Was Deleted --//
define('PM_INBOX', 1); //-- Message Located In Inbox For Reciever --//
define('PM_SENTBOX', -1); //-- GET Value For Sent Box --//

//-- Determine Action --//
$action = isset($_GET['action']) ? (string) $_GET['action'] : false;

if (!$action)
{
    $action = isset($_POST['action']) ? (string) $_POST['action'] : 'viewmailbox';
}

//-- View Listing Of Messages In Mail Box --//
if ($action == "viewmailbox")
{
    //-- Get Mailbox Number --//
    $mailbox = isset($_GET['box']) ? (int) $_GET['box'] : PM_INBOX;

    //-- Get Mailbox Name --//
    if ($mailbox != PM_INBOX && $mailbox != PM_SENTBOX)
    {
        $res = sql_query('SELECT name
                            FROM pmboxes
                            WHERE userid='.sqlesc($CURUSER['id']).'
                            AND boxnumber='.sqlesc($mailbox).'
                            LIMIT 1') or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 0)
        {
            error_message("error", "Error", "Invalid Mailbox");
        }

        $mailbox_name = mysql_fetch_array($res);
        $mailbox_name = htmlspecialchars($mailbox_name[0]);
    }
    else
    {
        if ($mailbox == PM_INBOX)
        {
            $mailbox_name = "Inbox";
        }
        else
        {
            $mailbox_name = "Sentbox";
        }
    }
    $pmcount = mysql_fetch_row(sql_query("SELECT COUNT(id)
                                            FROM messages
                                            WHERE receiver=".$CURUSER['id']."
                                            AND location >= '1' || sender=".$CURUSER['id']."
                                            AND saved = 'yes' ")) or sqlerr(__FILE__, __LINE__);

    $pm_perc     = $pmcount[0] ? ($pmcount[0] / 50 * 100) : 0;
    $perc_image  = ($pm_perc > 66) ? 'loadbarred.gif' : (($pm_perc > 33) ? 'loadbaryellow.gif' : 'loadbargreen.gif');
    $image_width = $pm_perc > 0 ? round($pm_perc * 2.5) : 1;

    if ($image_width > 250)
    {
        $image_width = 250;
    }

    //-- Start Page --//
    site_header($mailbox_name, false);
    ?>

<!-- Check All -->
<script type='text/javascript'>

    function checkAll(field)
    {
        if (field.CheckAll.checked == true)
        {
            for (i = 0; i < field.length; i++)
            {
                field[i].checked = true;
            }
        }
        else
        {
            for (i = 0; i < field.length; i++)
            {
                field[i].checked = false;
            }
        }
    }

</script>
<!-- Check All -->

<table align='center' width='30%' cellspacing='1'>
    <tbody>
    <tr>
        <td class='rowhead' align='left' colspan='3'>Your Folders are <?php echo $pm_perc?>% Full</td>
    </tr>
    <tr>
        <td class='rowhead' align='center' nowrap='nowrap' colspan='3'>
            <img src='<?php echo $image_dir?><?php echo $perc_image?>' width='<?php echo $image_width?>' height='10' align='middle' alt='' title='' />
        </td>
    </tr>
    <tr>
        <td class='rowhead' align='center' width='33%'>0%</td>
        <td class='rowhead' align='center' width='33%'>50%</td>
        <td class='rowhead' align='center' width='33%'>100%</td>
    </tr>
    </tbody>
</table>
<br />

<table border='0' width='100%' cellpadding='4' cellspacing='0'>
    <tr>
        <td class='rowhead' align='right'><?php echo insertJumpTo($mailbox);?></td>
    </tr>
</table>

<form action='messages.php' method='post' name='mutliact'>
    <input type='hidden' name='action' value='moveordel' />
    <table border='0' width='100%' cellpadding='4' cellspacing='0'>
        <tr>
            <td class='colhead' width='1%'>Status</td>
            <td class='colhead'>Subject</td>

            <?php

            if ($mailbox != PM_SENTBOX)
            {
                ?>

                <td class='colhead' width='35%'>Sender</td>

                <?php
            }
            else
            {
                ?>

                <td class='colhead' width='35%'>Receiver</td>

                <?php
            }
            ?>

            <td class='colhead' width='1%'>Date</td>
            <td class='colhead' width='1%'>
                <input type='checkbox' name='CheckAll' id='CheckAll' class='checkbox' value='1' onclick='checkAll(mutliact)' title='Check All' />
            </td>
        </tr>

        <?php

        if ($mailbox != PM_SENTBOX)
        {
            $res = sql_query('SELECT *
                                FROM messages
                                WHERE receiver='.sqlesc($CURUSER['id']).'
                                AND location='.sqlesc($mailbox).'
                                ORDER BY id DESC') or sqlerr(__FILE__, __LINE__);
        }
        else
        {
            $res = sql_query('SELECT *
                                FROM messages
                                WHERE sender='.sqlesc($CURUSER['id']).'
                                AND saved=\'yes\'
                                ORDER BY id DESC') or sqlerr(__FILE__, __LINE__);
        }

        if (mysql_num_rows($res) == 0)
        {
            echo("<tr><td class='colhead' colspan='5' align='center'>No Messages.</td></tr>\n");
        }
        else
        {
            while ($row = mysql_fetch_assoc($res))
            {
                //-- Get Sender Username --//
                if ($row['sender'] != 0 && $row['sender'] != $CURUSER['id'])
                {
                    $res2 = sql_query("SELECT username
                                        FROM users
                                        WHERE id=".sqlesc($row['sender']));

                    $username = mysql_fetch_array($res2);
                    $username = "<a href=\"userdetails.php?id=".$row['sender']."\">".$username[0]."</a>";
                    $id       = 0 + $row['sender'];

                    $r = sql_query("SELECT id
                                    FROM friends
                                    WHERE userid = $CURUSER[id]
                                    AND friendid = $id") or sqlerr(__FILE__, __LINE__);

                    $friend = mysql_num_rows($r);

                    if ($friend)
                    {
                        $username .= "&nbsp;<a href='friends.php?action=delete&amp;type=friend&amp;targetid=$id'>[Remove from Friends]</a>";
                    }
                    else
                    {
                        $username .= "&nbsp;<a href='friends.php?action=add&amp;type=friend&amp;targetid=$id'>[Add to Friends]</a>";
                    }
                }
                elseif ($row['sender'] == $CURUSER['id'])
                {
                    $res2 = sql_query("SELECT username
                                        FROM users
                                        WHERE id = ".sqlesc($row['receiver']));

                    $username = mysql_fetch_array($res2);
                    $username = "<a href=\"userdetails.php?id=".$row['receiver']."\">".$username[0]."</a>";
                    $id       = 0 + $row['receiver'];
                }
                else
                {
                    $username = "System";
                }
                $subject = htmlspecialchars($row['subject']);

                if (strlen($subject) <= 0)
                {
                    $subject = "No Subject";
                }

                if ($row['unread'] == 'yes')
                {
                    echo("<tr>\n<td class='rowhead' align='center'><img src='".$image_dir."unreadpm.gif' width='19' height='15' border='0' alt='Unread' title='Unread Message' /></td>\n");
                }
                else
                {
                    echo("<tr>\n<td class='rowhead' align='center'><img src='".$image_dir."readpm.gif' width='19' height='15' border='0' alt='Read' title='Read Message' /></td>\n");
                }
                echo("<td class='rowhead' align='left'><a href=\"messages.php?action=viewmessage&amp;id=".$row['id']."\">".$subject."</a></td>\n");
                echo("<td class='rowhead' align='left'>$username</td>\n");
                echo("<td class='rowhead' nowrap='nowrap'>".$row['added']."</td>\n");
                echo("<td class='rowhead'><input type='checkbox' name='messages[]' value='".$row['id']."' /></td>\n</tr>\n");
            }
        }
        ?>
        <tr class='colhead'>
            <td class='colhead' align='right' colspan='5'>
                <input type='submit' class='btn' name='move' value='Move to' />
                <select name='box'>
                    <option value='1'>Inbox</option>

                    <?php

                    $res = sql_query('SELECT *
                                        FROM pmboxes
                                        WHERE userid='.sqlesc($CURUSER['id']).'
                                        ORDER BY boxnumber') or sqlerr(__FILE__, __LINE__);

                    while ($row = mysql_fetch_assoc($res))
                    {
                        echo("<option value=\"".$row['boxnumber']."\">".htmlspecialchars($row['name'])."</option>\n");
                    }

                    ?>

                </select>
                <input type='submit' class='btn' name='delete' value='Delete' />
            </td>
        </tr>
    </table>
</form>
<table border='0' width='100%' cellpadding='4' cellspacing='0'>
    <tr>
        <td colspan='5'>
            <div align='left'><img src='<?php echo $image_dir?>unreadpm.gif' width='19' height='15' border='0' alt='Unread' title='Unread Meassages' /> Unread Messages.<br /> <img src='<?php echo $image_dir?>readpm.gif' width='19' height='15' border='0' alt='Read' title='Read Messages' /> Read Messages.
            </div>
            <div align='right'>
                <a href='messages.php?action=editmailboxes'><input type='submit' class='btn' name='Edit' value='Add/Edit Mailboxes' /></a>
                <a href='messages.php'><input type='submit' class='btn' name='return' value='Return To Inbox' /></a>
            </div>
        </td>
    </tr>
</table>

<?php

    site_footer();

}

if ($action == "viewmessage")
{
    $pm_id = (int) $_GET['id'];

    if (!$pm_id)
    {
        error_message("warn", "Warning", "You Do Not have Permission to View this Message.");
    }

    //-- Get The Message --//
    $res = sql_query('SELECT *
                        FROM messages
                        WHERE id='.sqlesc($pm_id).'
                        AND (receiver='.sqlesc($CURUSER['id']).'
                        OR (sender='.sqlesc($CURUSER['id']).'
                        AND saved=\'yes\'))
                        LIMIT 1') or sqlerr(__FILE__, __LINE__);

    if (!$res)
    {
        error_message("warn", "Warning", "You Do Not have Permission to View this Message.");
    }

    //-- Prepare For Displaying Message --//
    $message = mysql_fetch_assoc($res) or header("Location: messages.php");

    if ($message['sender'] == $CURUSER['id'])
    {
        //-- Display To --//
        $res2 = sql_query("SELECT username
                            FROM users
                            WHERE id=".sqlesc($message['receiver'])) or sqlerr(__FILE__, __LINE__);

        $sender = mysql_fetch_array($res2);
        $sender = "<a href=\"userdetails.php?id=".$message['receiver']."\">".$sender[0]."</a>";
        $reply  = "";
        $from   = "To";
    }
    else
    {
        $from = "From";

        if ($message['sender'] == 0)
        {
            $sender = "System";
            $reply  = "";
        }
        else
        {
            $res2 = sql_query("SELECT username
                                FROM users
                                WHERE id=".sqlesc($message['sender'])) or sqlerr(__FILE__, __LINE__);

            $sender = mysql_fetch_array($res2);
            $sender = "<a href=\"userdetails.php?id=".$message['sender']."\">".$sender[0]."</a>";
            //$reply  = " [ <a href=\"sendmessage.php?receiver=".$message['sender']."&amp;replyto=".$pm_id."\">Reply</a> ]";
        }
    }
    $body  = format_comment($message['msg']);
    $added = $message['added'];

    if ($CURUSER['class'] >= UC_MODERATOR && $message['sender'] == $CURUSER['id'])
    {
        $unread = ($message['unread'] == 'yes' ? "<span style=\"color: #FF0000; font-weight:bold;\">(New)</span>" : "");
    }
    else
    {
        $unread = "";
    }
    $subject = htmlspecialchars($message['subject']);

    if (strlen($subject) <= 0)
    {
        $subject = "No Subject";
    }

    if ($message['unread'] === 'yes')
    {
        //-- Mark Message Unread --//
        sql_query("UPDATE messages
                    SET unread='no'
                    WHERE id=".sqlesc($pm_id)."
                    AND receiver=".sqlesc($CURUSER['id'])."
                    LIMIT 1");
    }

    //-- Display Message --//
    site_header("PM ($subject)", false); ?>

<h1><?php echo $subject?></h1>

<table border="0" width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <td class="colhead" width="50%"><?php echo $from?></td>
        <td class="colhead" width="50%">Date</td>
    </tr>
    <tr>
        <td class="rowhead"><?php echo $sender?></td>
        <td class="rowhead"><?php echo $added?>&nbsp;&nbsp;<?php echo $unread?></td>
    </tr>
    <tr>
        <td class="rowhead" align="left" colspan="2"><?php echo $body?></td>
    </tr>
    <tr>
        <td class="rowhead" align='left'>
            <form action="messages.php" method="post">
                <input type="hidden" name="action" value="moveordel" />
                <input type="hidden" name="id" value="<?php echo $pm_id?>" />
                <select name="box">
                    <option value="1">Inbox</option>
                    <?
                    $res = sql_query('SELECT *
                                        FROM pmboxes
                                        WHERE userid='.sqlesc($CURUSER['id']).'
                                        ORDER BY boxnumber') or sqlerr(__FILE__, __LINE__);

                    while ($row = mysql_fetch_assoc($res))
                    {
                        echo("<option value=\"".$row['boxnumber']."\">".htmlspecialchars($row['name'])."</option>\n");
                    }?>
                </select>
                <input type="submit" class="btn" name="move" value="Move" title="Move" />
            </form>
        </td>
        <td class="rowhead" align="right">
            <a href="messages.php">
                <input type="submit" class="btn" name="return" value="Return To Inbox" title="Return To Inbox" />
            </a>
            <a href="messages.php?action=deletemessage&amp;id=<?php echo $pm_id?>">
                <input type="submit" class="btn" name="delete" value="Delete" title="Delete" />
            </a>
            <a href="sendmessage.php?receiver=<?php echo $message['sender']?>&amp;replyto=<?php echo $pm_id?>">
                <input type="submit" class="btn" name="delete" value="Reply" title="Reply" />
            </a>
            <a href="messages.php?action=forward&amp;id=<?php echo $pm_id?>">
                <input type="submit" class="btn" name="forward" value="Forward" title="Forward" />
            </a>
        </td>
    </tr>
</table>

<?php

    site_footer();

}

if ($action == "moveordel")
{
    $pm_id       = (int) $_POST['id'];
    $pm_box      = (int) $_POST['box'];
    $pm_messages = $_POST['messages'];

    if ($_POST['move'])
    {
        if ($pm_id)
        {
            //-- Move A Single Message --//
            @sql_query("UPDATE messages
                        SET location=".sqlesc($pm_box)."
                        WHERE id=".sqlesc($pm_id)."
                        AND receiver=".$CURUSER['id']."
                        LIMIT 1");
        }
        else
        {
            //-- Move Multiple Messages --//
            @sql_query("UPDATE messages
                        SET location=".sqlesc($pm_box)."
                        WHERE id IN (".implode(", ", array_map("sqlesc", $pm_messages)).')
                        AND receiver='.$CURUSER['id']);
        }
        //-- Check If Messages Were Moved --//
        if (@mysql_affected_rows() == 0)
        {
            error_message("error", "Error", "Messages could Not be Moved! ");
        }

        header("Location: messages.php?action=viewmailbox&box=".$pm_box);
        exit();
    }
    elseif ($_POST['delete'])
    {
        if ($pm_id)
        {
            //-- Delete A Single Message --//
            $res = sql_query("SELECT *
                                FROM messages
                                WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);

            $message = mysql_fetch_assoc($res);

            if ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'no')
            {
                sql_query("DELETE
                            FROM messages
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
            }

            elseif ($message['sender'] == $CURUSER['id'] && $message['location'] == PM_DELETED)
            {
                sql_query("DELETE
                            FROM messages
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
            }

            elseif ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'yes')
            {
                sql_query("UPDATE messages
                            SET location=0
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
            }

            elseif ($message['sender'] == $CURUSER['id'] && $message['location'] != PM_DELETED)
            {
                sql_query("UPDATE messages
                            SET saved='no'
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
            }
        }
        else
        {
            //-- Delete Multiple Messages --//
            foreach ($pm_messages
                     AS
                     $id)
            {
                $res = sql_query("SELECT *
                                    FROM messages
                                    WHERE id=".sqlesc((int) $id));

                $message = mysql_fetch_assoc($res);

                if ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'no')
                {
                    sql_query("DELETE
                                FROM messages
                                WHERE id=".sqlesc((int) $id)) or sqlerr(__FILE__, __LINE__);
                }

                elseif ($message['sender'] == $CURUSER['id'] && $message['location'] == PM_DELETED)
                {
                    sql_query("DELETE
                                FROM messages
                                WHERE id=".sqlesc((int) $id)) or sqlerr(__FILE__, __LINE__);
                }

                elseif ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'yes')
                {
                    sql_query("UPDATE messages
                                SET location=0
                                WHERE id=".sqlesc((int) $id)) or sqlerr(__FILE__, __LINE__);
                }

                elseif ($message['sender'] == $CURUSER['id'] && $message['location'] != PM_DELETED)
                {
                    sql_query("UPDATE messages
                                SET saved='no'
                                WHERE id=".sqlesc((int) $id)) or sqlerr(__FILE__, __LINE__);
                }
            }
        }
        //-- Check If Messages Were Moved --//
        if (@mysql_affected_rows() == 0)
        {
            error_message("error", "Error", "Messages could Not be Deleted! ");
        }
        else
        {
            header("Location: messages.php?action=viewmailbox");
            exit();
        }
    }

    error_message("error", "Error", "No Action");
}

if ($action == "forward")
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        //-- Display Form --//
        $pm_id = (int) $_GET['id'];

        //-- Get The Message --//
        $res = sql_query('SELECT *
                            FROM messages
                            WHERE id='.sqlesc($pm_id).'
                            AND (receiver='.sqlesc($CURUSER['id']).'
                            OR sender='.sqlesc($CURUSER['id']).')
                            LIMIT 1') or sqlerr(__FILE__, __LINE__);

        if (!$res)
        {
            error_message("warn", "Warning", "You Do Not have Permission to Forward this Message.");
        }

        if (mysql_num_rows($res) == 0)
        {
            error_message("warn", "Warning", "You Do Not have Permission to Forward this Message.");
        }

        $message = mysql_fetch_assoc($res);

        //-- Prepare Variables --//
        $subject = "Fwd: ".htmlspecialchars($message['subject']);
        $from    = $message['sender'];
        $orig    = $message['receiver'];

        $res = sql_query("SELECT username
                            FROM users
                            WHERE id=".sqlesc($orig)."
                            OR id=".sqlesc($from)) or sqlerr(__FILE__, __LINE__);

        $orig2 = mysql_fetch_assoc($res);

        $orig_name = "<a href=\"userdetails.php?id=".$from."\">".$orig2['username']."</a>";

        if ($from == 0)
        {
            $from_name         = "System";
            $from2['username'] = "System";
        }
        else
        {
            $from2     = mysql_fetch_array($res);
            $from_name = "<a href=\"userdetails.php?id=".$from."\">".$from2['username']."</a>";
        }

        $body = "-------- Original Message from ".$from2['username'].": --------<br />".format_comment($message['msg']);

        site_header($subject, false);?>

    <h1><?php echo $subject?></h1>

    <form action="messages.php" method="post">
        <input type="hidden" name="action" value="forward" />
        <input type="hidden" name="id" value="<?php echo $pm_id?>" />
        <table border="0" width="100%" cellpadding="4" cellspacing="0" width="100%">
            <tr>
                <td class="colhead">To:</td>
                <td align='left'><input type="text" name="to" size="83" value="Enter Username" /></td>
            </tr>
            <tr>
                <td class="colhead">Orignal<br />Receiver:</td>
                <td class="rowhead" align='left'><?php echo $orig_name?></td>
            </tr>
            <tr>
                <td class="colhead">From:</td>
                <td class="rowhead" align='left'><?php echo $from_name?></td>
            </tr>
            <tr>
                <td class="colhead">Subject:</td>
                <td class="rowhead" align='left'>
                    <input type="text" name="subject" size="83" value="<?php echo $subject?>" />
                </td>
            </tr>
            <tr>
                <td class="colhead">Message:</td>
                <td class="rowhead" align='left'>
                    <textarea name="msg" cols="80" rows="8"></textarea><br /><?php echo $body?>
                </td>
            </tr>
            <tr>
                <td class="rowhead" align="left" colspan="2">Save Message
                    <input type="checkbox" name="save" value="1"<?php echo $CURUSER['savepms'] == 'yes' ? " checked='checked'" : ""?> />&nbsp;
                    <input type="submit" class="btn" value="Forward" />
                </td>
            </tr>
        </table>
    </form>

    <?php
        site_footer();
    }
    else
    {
        //-- Forward The Message --//
        $pm_id = (int) $_POST['id'];

        //-- Get The Message --//
        $res = sql_query('SELECT *
                            FROM messages
                            WHERE id='.sqlesc($pm_id).'
                            AND (receiver='.sqlesc($CURUSER['id']).'
                            OR sender='.sqlesc($CURUSER['id']).')
                            LIMIT 1') or sqlerr(__FILE__, __LINE__);

        if (!$res)
        {
            error_message("warn", "Warning", "You Do Not have Permission to Forward this Message.");
        }

        if (mysql_num_rows($res) == 0)
        {
            error_message("warn", "Warning", "You Do Not have Permission to Forward this Message.");
        }

        $message  = mysql_fetch_assoc($res);
        $subject  = (string) $_POST['subject'];
        $username = strip_tags($_POST['to']);

        //-- Try Finding A User With Specified Name --//
        $res = sql_query("SELECT id
                            FROM users
                            WHERE LOWER(username)=LOWER(".sqlesc($username).")
                            LIMIT 1");

        if (!$res)
        {
            error_message("error", "Error", "Sorry, there is No Member with that Name.");
        }

        if (mysql_num_rows($res) == 0)
        {
            error_message("error", "Error", "Sorry, there is no Member with that Name.");
        }

        $to = mysql_fetch_array($res);
        $to = $to[0];

        //-- Get Orignal Sender's Username --//
        if ($message['sender'] == 0)
        {
            $from = "System";
        }
        else
        {
            $res = sql_query("SELECT *
                                FROM users
                                WHERE id=".sqlesc($message['sender'])) or sqlerr(__FILE__, __LINE__);

            $from = mysql_fetch_assoc($res);
            $from = $from['username'];
        }

        $body = (isset($_POST['msg']) ? (string) $_POST['msg'] : '');
        $body .= "\n-------- Original Message from ".$from.": --------\n".$message['msg'];
        $save = (isset($_POST['save']) ? (int) $_POST['save'] : '');

        if ($save)
        {
            $save = 'yes';
        }
        else
        {
            $save = 'no';
        }

        //-- Make Sure Recipient Wants This Message --//
        if ($CURUSER['class'] < UC_MODERATOR)
        {
            if ($from["acceptpms"] == "yes")
            {
                $res2 = sql_query("SELECT *
                                    FROM blocks
                                    WHERE userid = $to
                                    AND blockid = ".$CURUSER["id"]) or sqlerr(__FILE__, __LINE__);

                if (mysql_num_rows($res2) == 1)
                {
                    error_message("info", "Refused", "This Member has Blocked PMs from you.");
                }
            }

            elseif ($from["acceptpms"] == "friends")
            {
                $res2 = sql_query("SELECT *
                                    FROM friends
                                    WHERE userid = $to
                                    AND friendid = ".$CURUSER["id"]) or sqlerr(__FILE__, __LINE__);

                if (mysql_num_rows($res2) != 1)
                {
                    error_message("info", "Refused", "This Member ONLY Accepts PMs from Members in their Friends list.");
                }
            }

            elseif ($from["acceptpms"] == "no")
            {
                error_message("info", "Refused", "This Member Does Not Accept PMs.");
            }
        }

        sql_query("INSERT INTO messages (poster, sender, receiver, added, subject, msg, location, saved)
                    VALUES(".$CURUSER["id"].", ".$CURUSER["id"].", $to, '".get_date_time()."', ".sqlesc($subject).",".sqlesc($body).", ".sqlesc(PM_INBOX).", ".sqlesc($save).")") or sqlerr(__FILE__, __LINE__);

        error_message("success", "Success", "PM Forwarded");
    }
}

if ($action == "editmailboxes")
{
    $res = sql_query("SELECT *
                        FROM pmboxes
                        WHERE userid=".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

    site_header("Editing Mailboxes", false); ?>

<h1>Editing Mailboxes</h1>
<table  border="0" width="100%"cellpadding="4" cellspacing="0">
    <tr>
        <td class="colhead" align="left">Add Mailboxes</td>
    </tr>
    <tr>
        <td class="rowhead" align='left'>You may add extra Mailboxes. You do not have to use all the input boxes.<br />

            <form action="messages.php" method="get">
                <input type="hidden" name="action" value="editmailboxes2" />
                <input type="hidden" name="action2" value="add" />
                <input type="text" name="new1" size="40" maxlength="14" /><br />
                <input type="text" name="new2" size="40" maxlength="14" /><br />
                <input type="text" name="new3" size="40" maxlength="14" /><br />
                <input type="submit" class="btn" value="Add" />
            </form>
        </td>
    </tr>
    <tr>
        <td class="colhead" align='left'>Edit Mailboxes</td>
    </tr>
    <tr>
        <td class="rowhead" align='left'>You may edit the names, or delete the name to delete this virtual directory.<br />Please note, that all messages in this directory will be lost if you delete this directory.
            <form action="messages.php" method="get">
                <input type="hidden" name="action" value="editmailboxes2" />
                <input type="hidden" name="action2" value="edit" />

                <?php

                if (!$res)
                {
                    echo ('<span style="text-align:center; font-weight:bold;">There are No Mailboxes to Edit.</span>');
                }

                if (mysql_num_rows($res) == 0)
                {
                    echo ('<span style="text-align:center; font-weight:bold;">There are No Mailboxes to Edit.</span>');
                }
                else
                {
                    while ($row = mysql_fetch_assoc($res))
                    {
                        $id   = $row['id'];
                        $name = htmlspecialchars($row['name']);

                        echo("<input type='text' name='edit$id' size='40' maxlength='14' value='$name' /><br />\n");
                    }

                    echo("<input type='submit' class='btn' value='Edit' />");
                }
                ?>

            </form>
        </td>
    </tr>
</table>

<?php

    site_footer();
}

if ($action == "editmailboxes2")
{
    $action2 = (string) $_GET['action2'];

    if (!$action2)
    {
        error_message("error", "Error", "No action.");
    }

    if ($action2 == "add")
    {
        $name1 = $_GET['new1'];
        $name2 = $_GET['new2'];
        $name3 = $_GET['new3'];

        //-- Get Current Max Box Number --//
        $res = sql_query("SELECT MAX(boxnumber)
                            FROM pmboxes
                            WHERE userid=".sqlesc($CURUSER['id']));

        $box = mysql_fetch_array($res);
        $box = (int) $box[0];

        if ($box < 2)
        {
            $box = 1;
        }

        if (strlen($name1) > 0)
        {
            ++$box;
            sql_query("INSERT INTO pmboxes (userid, name, boxnumber)
                        VALUES (".sqlesc($CURUSER['id']).", ".sqlesc($name1).", $box)") or sqlerr(__FILE__, __LINE__);
        }

        if (strlen($name2) > 0)
        {
            ++$box;
            sql_query("INSERT INTO pmboxes (userid, name, boxnumber)
                        VALUES (".sqlesc($CURUSER['id']).", ".sqlesc($name2).", $box)") or sqlerr(__FILE__, __LINE__);
        }

        if (strlen($name3) > 0)
        {
            ++$box;
            sql_query("INSERT INTO pmboxes (userid, name, boxnumber)
                        VALUES (".sqlesc($CURUSER['id']).", ".sqlesc($name3).", $box)") or sqlerr(__FILE__, __LINE__);
        }

        header("Location: messages.php?action=editmailboxes");
        exit();
    }

    if ($action2 == "edit")
    {
        $res = sql_query("SELECT *
                            FROM pmboxes
                            WHERE userid=".sqlesc($CURUSER['id']));

        if (!$res)
        {
            error_message("error", "Error", "No Mailboxes to Edit");
        }

        if (mysql_num_rows($res) == 0)
        {
            error_message("error", "Error", "No Mailboxes to Edit");
        }
        else
        {
            while ($row = mysql_fetch_assoc($res))
            {
                if (isset($_GET['edit'.$row['id']]))
                {
                    if ($_GET['edit'.$row['id']] != $row['name'])
                    {
                        //-- Do Something --//
                        if (strlen($_GET['edit'.$row['id']]) > 0)
                        {
                            //-- Edit Name --//
                            sql_query("UPDATE pmboxes
                                        SET name=".sqlesc($_GET['edit'.$row['id']])."
                                        WHERE id=".sqlesc($row['id'])."
                                        LIMIT 1");
                        }
                        else
                        {
                            //-- Delete --//
                            sql_query("DELETE
                                        FROM pmboxes
                                        WHERE id=".sqlesc($row['id'])."
                                        LIMIT 1");

                            //-- Delete All Messages From This Folder (uses Multiple Queries Because We Can Only Perform Security Checks In WHERE Clauses) --//
                            sql_query("UPDATE messages
                                        SET location=0
                                        WHERE saved='yes'
                                        AND location=".sqlesc($row['boxnumber'])."
                                        AND receiver=".sqlesc($CURUSER['id']));

                            sql_query("UPDATE messages
                                        SET saved='no'
                                        WHERE saved='yes'
                                        AND sender=".sqlesc($CURUSER['id']));

                            sql_query("DELETE
                                        FROM messages
                                        WHERE saved='no'
                                        AND location=".sqlesc($row['boxnumber'])."
                                        AND receiver=".sqlesc($CURUSER['id']));

                            sql_query("DELETE
                                        FROM messages
                                        WHERE location=0
                                        AND saved='yes'
                                        AND sender=".sqlesc($CURUSER['id']));
                        }
                    }
                }
            }
            header("Location: messages.php?action=editmailboxes");
            exit();
        }
    }
}

if ($action == "deletemessage")
{
    $pm_id = (int) $_GET['id'];

    //-- Delete Message --//
    $res = sql_query("SELECT *
                        FROM messages
                        WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);

    if (!$res)
    {
        error_message("error", "Error", "No Message with this ID.");
    }

    if (mysql_num_rows($res) == 0)
    {
        error_message("error", "Error", "No Message with this ID.");
    }

    $message = mysql_fetch_assoc($res);

    if ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'no')
    {
        $res2 = sql_query("DELETE
                            FROM messages
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
    }

    elseif ($message['sender'] == $CURUSER['id'] && $message['location'] == PM_DELETED)
    {
        $res2 = sql_query("DELETE
                            FROM messages
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
    }

    elseif ($message['receiver'] == $CURUSER['id'] && $message['saved'] == 'yes')
    {
        $res2 = sql_query("UPDATE messages
                            SET location=0
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
    }

    elseif ($message['sender'] == $CURUSER['id'] && $message['location'] != PM_DELETED)
    {
        $res2 = sql_query("UPDATE messages
                            SET saved='no'
                            WHERE id=".sqlesc($pm_id)) or sqlerr(__FILE__, __LINE__);
    }

    if (!$res2)
    {
        error_message("error", "Error", "Could Not Delete Message.");
    }

    if (mysql_affected_rows() == 0)
    {
        error_message("error", "Error", "Could Not Delete Message.");
    }
    else
    {
        header("Location: messages.php?action=viewmailbox&id=".$message['location']);
        exit();
    }
}

//-- Functions --//
function insertJumpTo ($selected = 0)
{
    global $CURUSER;

    $res = sql_query('SELECT *
                    FROM pmboxes
                    WHERE userid='.sqlesc($CURUSER['id']).'
                    ORDER BY boxnumber'); ?>

<form action="messages.php" method="get">
    <input type="hidden" name="action" value="viewmailbox" />
    <select name="box">
        <option value="1"<?php echo ($selected == PM_INBOX ? " selected='selected'" : "")?>>Inbox</option>
        <option value="-1"<?php echo ($selected == PM_SENTBOX ? " selected='selected'" : "")?>>Sentbox</option>
        <?
        while ($row = mysql_fetch_assoc($res))
        {
            if ($row['boxnumber'] == $selected)
            {
                echo("<option value=\"".$row['boxnumber']."\" selected='selected'>".$row['name']."</option>\n");
            }
            else
            {
                echo("<option value=\"".$row['boxnumber']."\">".$row['name']."</option>\n");
            }
        }
        ?>
    </select>
    <input type='submit' class='btn' value='Go' />
</form>

<?php

}

?>