<?php

/**
**************************
** FreeTSP Version: 1.0 **
**************************
** https://github.com/Krypto/FreeTSP
** http://www.freetsp.info
** Licence Info: GPL
** Copyright (C) 2010 FreeTSP v1.0
** A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
** Project Leaders: Krypto, Fireknight.
**/

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

        <link rel='stylesheet' type='text/css' href='/errors/error-style.css' />
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

function round_time($ts)
{
    $mins  = floor($ts / 60);
    $hours = floor($mins / 60);
    $mins  -= $hours * 60;
    $days  = floor($hours / 24);
    $hours -= $days * 24;
    $weeks = floor($days / 7);
    $days  -= $weeks * 7;
    $t     = "";

    if ($weeks > 0)
    {
        return "$weeks week".($weeks > 1 ? "s" : "");
    }

    if ($days > 0)
    {
        return "$days day".($days > 1 ? "s" : "");
    }

    if ($hours > 0)
    {
        return "$hours hour".($hours > 1 ? "s" : "");
    }

    if ($mins > 0)
    {
        return "$mins min".($mins > 1 ? "s" : "");
    }
    return "< 1 min";
}

    $msg_problem = trim($_POST["msg_problem"]);
    $msg_answer  = trim($_POST["msg_answer"]);
    $id          = $_POST["id"];
    $addedbyid   = $_POST["addedbyid"];
    $title       = trim($_POST["title"]);
    $action      = $_GET["action"];
    $solve       = $_GET["solve"];

//-- Action: Cleanuphd --//
if ($action == 'cleanuphd')
{
    sql_query("DELETE FROM helpdesk
                    WHERE solved='yes'
                    OR solved='ignored'");

    $action = 'problems';
}

//-- Action: Problems --//
if ($action == 'problems')
{
    //-- Post & Get --//
    $id  = $_GET["id"];

    begin_frame("Problems");

    //-- View Problem Details --//
    if ($id != 0)
    {
        $res = sql_query("SELECT *
                            FROM helpdesk
                            WHERE id = $id");

        $arr = mysql_fetch_array($res);

        $problem = format_comment($arr["msg_problem"]);
        $answer  = format_comment($arr["msg_answer"]);

        $zap = sql_query("SELECT username
                            FROM users
                            WHERE id = $arr[added_by]");

        $wyn = mysql_fetch_array($zap);

        $added_by_name = $wyn["username"];

        $zap_s = sql_query("SELECT username
                            FROM users
                            WHERE id = $arr[solved_by]");

        $wyn_s = mysql_fetch_array($zap_s);

        $solved_by_name = $wyn_s["username"];

        print("<form method='post' action='controlpanel.php?fileaction=23'>");

        print("<table width='70%' border='1' align='center' cellpadding='5' cellspacing='0'>");

        /*print("<tr>
                <td colspan='2' class='rowhead' align='center' ><strong>".$arr["title"]."</strong></td>
               </tr>");*/

        print("<tr>
                <td class='colhead' align='right'><strong>Added</strong></td>
                <td align='left'>On <strong>".$arr["added"]."</strong> by <a href='userdetails.php?id=".$arr["added_by"]."'><strong>".$added_by_name."</strong></a></td>
               </tr>");

//-- Start View Question Answered --//
        if ($arr["solved"] == 'yes')
        {
            print("<tr>
                    <td class='colhead' align='right'><strong>Problem</strong></td>
                    <td class='comment'>$problem</td>
                   </tr>");

            print("<tr>
                    <td class='colhead' align='right'><strong>Solved</strong></td>
                    <td align='left'><font color='green'><strong>Yes</strong></font> on <strong>".$arr["solved_date"]."</strong> by <a href='userdetails.php?id=".$arr["solved_by"]."'><strong>".$solved_by_name."</strong></a></td>
                   </tr>");

            print("<tr>
                    <td class='colhead' align='right'><strong>Answer</strong></td>
                    <td class='comment'>$answer</td>
                   </tr>");

            print("</table>");
            print("</form>");
        }
//-- Finish View Question Answered --//

//-- Start View Question Ignored --//
        else if ($arr["solved"] == 'ignored')
        {
            print("<tr>
                    <td class='colhead' align='right'><strong>Problem</strong></td>
                    <td class='comment'>$problem</td>
                   </tr>");

            print("<tr>
                    <td class='colhead' align='right'><strong>Answer</strong></td>
                    <td class='comment'>$answer</td>
                   </tr>");

            print("<tr>
                    <td class='colhead' align='right'><strong>Solved</strong></td>
                    <td class='rowhead' align='left'><font color='blue'><strong>Ignored</strong></font> on <strong>".$arr["solved_date"]."</strong> by <a href='userdetails.php?id=".$arr["solved_by"]."'><strong>".$solved_by_name."</strong></a></td>
                   </tr>");

            print("</table>");
            print("</form>");
        }
//-- Finish View Question Ignored --//

//-- Start View Question --//
        else if ($arr["solved"] == 'no')
        {
            $addedbyid = $arr["added_by"];

            print("<tr>
                    <td class='colhead' align='right'><strong>Problem</strong></td>
                    <td class='comment'>$problem</td>
                   </tr>");

            print("<tr>
                    <td class='colhead' align='right'><strong>Solved</strong></td>
                    <td class='colhead' align='center'><font color='red'><strong>No</strong></font></td>
                   </tr>");

            print("<tr>
                    <td class='colhead' align='right'><strong>Answer</strong></td>
                    <td>
                        ".textbbcode("compose","msg_answer",$body)."
                        <input type='hidden' name='id' value='$id' />
                        <input type='hidden' name='addedbyid' value='$addedbyid' />
                    </td>
                   </tr>");

            print("<tr>
                    <td class='rowhead' colspan='2' align='center'>
                        <input type='submit' class='btn' value='Answer!' />
                        <b>||</b> <a href='controlpanel.php?fileaction=23&amp;action=solve&amp;pid=$id&amp;solved=ignored'><font color='red'><b>IGNORE</b></font></a>
                    </td>
                   </tr>");

            print("</table>");
            print("</form>");
        }
    }
//-- Finish View Question --//

//-- Start Question List --//
    else
    {
        print("<table border='1' align='center' cellpadding='5' cellspacing='0'>
                <tr>
                   <td class='colhead' align='center'>Added</td>
                   <td class='colhead' align='center'>Added by</td>
                   <td class='colhead' align='center'>Problem</td>
                   <td class='colhead' align='center'>Solved - by</td>
                   <td class='colhead' align='center'>Solved in*</td>
                </tr>");

        $res = sql_query("SELECT *
                            FROM helpdesk
                            ORDER BY added DESC");

        while($arr = mysql_fetch_array($res))
        {
            $zap = sql_query("SELECT username
                                FROM users
                                WHERE id = $arr[added_by]");

            $wyn = mysql_fetch_array($zap);

            $added_by_name = $wyn["username"];

            $zap_s = sql_query("SELECT username
                                FROM users
                                WHERE id = $arr[solved_by]");

            $wyn_s = mysql_fetch_array($zap_s);

            $solved_by_name = $wyn_s["username"];

            //-- Solved In --//
            $added       = $arr["added"];
            $solved_date = $arr["solved_date"];

            if ($solved_date == "0000-00-00 00:00:00")
            {
                $solved_in    = " [N/A]";
                $solved_color = "'black'";
            }
            else
            {
                $solved_in_wtf = sql_timestamp_to_unix_timestamp($arr["solved_date"]) - sql_timestamp_to_unix_timestamp($arr["added"]);
                $solved_in     = " [".round_time($solved_in_wtf)."]";

                if ($solved_in_wtf > 2*3600)
                {
                    $solved_color = "'red'";
                }
                else if ($solved_in_wtf > 3600)
                {
                    $solved_color = "'black'";
                }
                else if ($solved_in_wtf <= 1800)
                {
                    $solved_color = "'green'";
                }
            }

            print("<tr>
                    <td>".$arr["added"]."</td>
                    <td><a href='userdetails.php?id=".$arr["added_by"]."'>".$added_by_name."</a></td>
                    <td><a href='controlpanel.php?fileaction=23&amp;action=problems&amp;id=".$arr["id"]."'><strong>".$arr["title"]."</strong></a></td>");

            if ($arr["solved"] == 'no')
            {
                $solved_by = "N/A";

                print("<td><font color='red'><strong>No</strong></font> - ".$solved_by."</td>");
            }
            else if ($arr["solved"] == 'yes')
            {
                $solved_by = "<a href='userdetails.php?id=".$arr["solved_by"]."'>".$solved_by_name."</a>";

                print("<td><font color='green'><strong>Yes</strong></font> - ".$solved_by."</td>");
            }
            else if ($arr["solved"] == 'ignored')
            {
                $solved_by = "<a href='userdetails.php?id=".$arr["solved_by"]."'>".$solved_by_name."</a>";

                print("<td><font color='blue'><strong>Ignored</strong></font> - ".$solved_by."</td>");
            }

            print("<td><font color=".$solved_color.">".$solved_in."</font></td></tr>");
        }

        if (get_user_class() >= UC_SYSOP)
        {
            print("<tr>
                        <td colspan='5' align='center'>
                            <form method='post' action='controlpanel.php?fileaction=23&amp;action=cleanuphd'>
                                <input type='submit' class='btn' value='Delete Solved or Ignored Problems' />
                            </form>
                       </td>
                    </tr>
                </table>");
        }
    }
//-- Finish Question List --//

    end_frame();

    site_footer();
    exit;
}

//-- Main File --//

site_header("Help Desk", false);

//--- Start Ignored Updates --//
if ($action == 'solve')
{
    $pid = $_GET["pid"];

    if ($solve = 'ignored')
    {
        $answer = sqlesc("Question ignored");
        $dt     = sqlesc(get_date_time());

        sql_query("UPDATE helpdesk SET solved='ignored', solved_by=$CURUSER[id], solved_date = $dt, msg_answer = $answer WHERE id=$pid");

        display_message_center("info", "Info", "The Problem has been Ignored !<br />
                                                Return to the <a href='controlpanel.php?fileaction=23&amp;action=problems'><strong>HELP DESK</strong></a> and Solve more Problems.
                                                <br />");
        site_footer();
        exit;
    }
}
//-- Finish Ignored Updates --//

//-- Start Answer Updates --//
if (($msg_answer != "") && ($id != 0))
{
    $zap_usr = sql_query("SELECT username
                            FROM users
                            WHERE id = $addedbyid");

    $wyn_usr = mysql_fetch_array($zap_usr);

    $addedby_name = $wyn_usr["username"];
    $system_name  = sqlesc("HELPDESK");
    $subject      = sqlesc("Reply from Help Desk");
    $msg          = sqlesc("[b]//---THE REPLY FROM THE HELPDESK---//[/b]\n\n".$msg_answer."\n\nRegards\n\nThe Staff At :- ".$site_name." ");
    $dt           = sqlesc(get_date_time());

    sql_query("UPDATE helpdesk
                SET solved='yes', solved_by=$CURUSER[id], solved_date = $dt, msg_answer = ".sqlesc($msg_answer)."
                WHERE id=$id");

    sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster, unread)
                VALUES($system_name, $addedbyid, $dt, $subject, $msg, $CURUSER[id], 'yes')");

    display_message_center("info", "Info", "Problem ID:-&nbsp;&nbsp;No $id<br />
                                            Tittled:- <strong>".$msg_problem."</strong><br />
                                            Requested by :-<strong> ".$addedby_name."</strong><br />
                                            Has been Answered by<strong> ".$CURUSER["username"]."</strong><br />
                                            And a message has been sent to <strong>".$addedby_name."</strong><br /><br />
                                            Return to the <a href='controlpanel.php?fileaction=23&amp;action=problems'><strong>HELP DESK</strong></a> and Solve more Problems.
                                            <br />");
  site_footer();
  exit;
}
//--- Finish Answer Updates --//

display_message_center("info", "Help Desk", "View ALL Solved and Unsolved Problems <a href='controlpanel.php?fileaction=23&amp;action=problems'><strong>HERE</strong></a><br />");

?>