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

db_connect();
logged_in();

$action = isset($_GET["action"]) ? $_GET["action"] : $_POST["action"];
$pollid = (int) (isset($_GET["pollid"]) ? $_GET["pollid"] : $_POST["pollid"]);

if ($action == "edit")
{
    if (!is_valid_id($pollid))
    {
        error_message("error", "Error", "Invalid ID.");
    }

    $res = sql_query("SELECT *
                        FROM polls
                        WHERE id = $pollid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 0)
    {
        error_message("error", "Error", "No Poll found with that ID.");
    }

    $poll = mysql_fetch_assoc($res);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if ($action == 'edit' && !is_valid_id($pollid))
    {
        error_message("error", "Error", "Invalid ID.");
    }

    $question = $_POST["question"];
    $option0  = $_POST["option0"];
    $option1  = $_POST["option1"];
    $option2  = $_POST["option2"];
    $option3  = $_POST["option3"];
    $option4  = $_POST["option4"];
    $option5  = $_POST["option5"];
    $option6  = $_POST["option6"];
    $option7  = $_POST["option7"];
    $option8  = $_POST["option8"];
    $option9  = $_POST["option9"];
    $option10 = $_POST["option10"];
    $option11 = $_POST["option11"];
    $option12 = $_POST["option12"];
    $option13 = $_POST["option13"];
    $option14 = $_POST["option14"];
    $option15 = $_POST["option15"];
    $option16 = $_POST["option16"];
    $option17 = $_POST["option17"];
    $option18 = $_POST["option18"];
    $option19 = $_POST["option19"];
    $sort = isset($_POST["sort"]) ? (int)($_POST["sort"]) : '';
    $returnto = isset($_POST["returnto"]) ? htmlentities($_POST["returnto"]) : '';

    if (!$question || !$option0 || !$option1)
    {
        error_message("Error", "Error", "Missing Data!");
    }

    if ($pollid)
    {
        sql_query("UPDATE polls
                    SET "."question = ".sqlesc($question).", "."option0  = ".sqlesc($option0).", "."option1  = ".sqlesc($option1).", "."option2  = ".sqlesc($option2).", "."option3  = ".sqlesc($option3).", "."option4  = ".sqlesc($option4).", "."option5  = ".sqlesc($option5).", "."option6  = ".sqlesc($option6).", "."option7  = ".sqlesc($option7).", "."option8  = ".sqlesc($option8).", "."option9  = ".sqlesc($option9).", "."option10 = ".sqlesc($option10).", "."option11 = ".sqlesc($option11).", "."option12 = ".sqlesc($option12).", "."option13 = ".sqlesc($option13).", "."option14 = ".sqlesc($option14).", "."option15 = ".sqlesc($option15).", "."option16 = ".sqlesc($option16).", "."option17 = ".sqlesc($option17).", "."option18 = ".sqlesc($option18).", "."option19 = ".sqlesc($option19).", "."sort     = ".sqlesc($sort)." "."WHERE id = $pollid") or sqlerr(__FILE__, __LINE__);
    }
    else
    {
        sql_query("INSERT INTO polls
                    VALUES(0".", '".get_date_time()."'".", ".sqlesc($question).", ".sqlesc($option0).", ".sqlesc($option1).", ".sqlesc($option2).", ".sqlesc($option3).", ".sqlesc($option4).", ".sqlesc($option5).", ".sqlesc($option6).", ".sqlesc($option7).", ".sqlesc($option8).", ".sqlesc($option9).", ".sqlesc($option10).", ".sqlesc($option11).", ".sqlesc($option12).", ".sqlesc($option13).", ".sqlesc($option14).", ".sqlesc($option15).", ".sqlesc($option16).", ".sqlesc($option17).", ".sqlesc($option18).", ".sqlesc($option19).", ".sqlesc($sort).")") or sqlerr(__FILE__, __LINE__);
    }

    if ($returnto == "main")
    {
        error_message_center("info", "Success", "Return to the <a href='index.php'>Main Page</a>");
    }
    elseif ($pollid)
    {
        //header("Location: $site_url/polls.php#$pollid");
        error_message_center("info", "Success", "<strong>The Poll has been Successfully Edited.</strong><br />
                                                <br /> Return to the <a href='controlpanel.php'>Control Panel</a>
                                                <br />Return to the <a href='index.php'>Main Page</a>");
    }
    else
    {
        error_message_center("info", "Success", "<strong>The Poll has been Successfully Created.</strong><br />
                                                <br />Return to the <a href='index.php'>Main Page</a>");
    }
    die;
}

site_header("",false);

if ($pollid)
{
    print("<h1>Edit Poll</h1>");
}
else
{
    //-- Warn If Current Poll Is Less Than 3 Days Old --//
    $res = sql_query("SELECT question,added
                        FROM polls
                        ORDER BY added DESC
                        LIMIT 1") or sqlerr();

    $arr = mysql_fetch_assoc($res);

    if ($arr)
    {
        $hours = floor((gmtime() - sql_timestamp_to_unix_timestamp($arr["added"])) / 3600);
        $days  = floor($hours / 24);

        if ($days < 3)
        {
            $hours -= $days * 24;

            if ($days)
            {
                $t = "$days Day".($days > 1 ? "s" : "");
            }
            else
            {
                $t = "$hours Hour".($hours > 1 ? "s" : "");
            }
            print("<p><span style='color : #ff0000; font-weight:bold;'>Note: The Current Poll (<span style='font-style: italic;'>".$arr["question"]."</span>) is only $t Old.</span></p>");
        }
    }
    print("<h1>Make Poll</h1>");
}
?>

<form method='post' action='controlpanel.php?fileaction=15'>
    <table border='1' align='center' cellspacing='0' cellpadding='5'>
        <tr>
            <td class='rowhead'>Question <span style='color : #ff0000;'>*</span></td>
            <td class='rowhead' align='left'>
                <input name='question' size='80' maxlength='255' value="<?php echo isset($poll['question']) ? $poll['question'] : ''?>" />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 1 <span style='color : #ff0000;'>*</span></td>
            <td class='rowhead' align='left'>
                <input name='option0' size='80' maxlength='40' value="<?php echo isset($poll['option0']) ? $poll['option0'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 2 <span style='color : #ff0000;'>*</span></td>
            <td class='rowhead' align='left'>
                <input name='option1' size='80' maxlength='40' value="<?php echo isset($poll['option1']) ? $poll['option1'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 3</td>
            <td class='rowhead' align='left'>
                <input name='option2' size='80' maxlength='40' value="<?php echo isset($poll['option2']) ? $poll['option2'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 4</td>
            <td class='rowhead' align='left'>
                <input name='option3' size='80' maxlength='40' value="<?php echo isset($poll['option3']) ? $poll['option3'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 5</td>
            <td class='rowhead' align='left'>
                <input name='option4' size='80' maxlength='40' value="<?php echo isset($poll['option4']) ? $poll['option4'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 6</td>
            <td class='rowhead' align='left'>
                <input name='option5' size='80' maxlength='40' value="<?php echo isset($poll['option5']) ? $poll['option5'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 7</td>
            <td class='rowhead' align='left'>
                <input name='option6' size='80' maxlength='40' value="<?php echo isset($poll['option6']) ? $poll['option6'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 8</td>
            <td class='rowhead' align='left'>
                <input name='option7' size='80' maxlength='40' value="<?php echo isset($poll['option7']) ? $poll['option7'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 9</td>
            <td class='rowhead' align='left'>
                <input name='option8' size='80' maxlength='40' value="<?php echo isset($poll['option8']) ? $poll['option8'] : ''?>" /><br />
            </td>
        </tr>


        <tr>
            <td class='rowhead'>Option 10</td>
            <td class='rowhead' align='left'>
                <input name='option9' size='80' maxlength='40' value="<?php echo isset($poll['option9']) ? $poll['option9'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 11</td>
            <td class='rowhead' align='left'>
                <input name='option10' size='80' maxlength='40' value="<?php echo isset($poll['option10']) ? $poll['option10'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 12</td>
            <td class='rowhead' align='left'>
                <input name='option11' size='80' maxlength='40' value="<?php echo isset($poll['option11']) ? $poll['option11'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 13</td>
            <td class='rowhead' align='left'>
                <input name='option12' size='80' maxlength='40' value="<?php echo isset($poll['option12']) ? $poll['option12'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 14</td>
            <td class='rowhead' align='left'>
                <input name='option13' size='80' maxlength='40' value="<?php echo isset($poll['option13']) ? $poll['option13'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 15</td>
            <td class='rowhead' align='left'>
                <input name='option14' size='80' maxlength='40' value="<?php echo isset($poll['option14']) ? $poll['option14'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 16</td>
            <td class='rowhead' align='left'>
                <input name='option15' size='80' maxlength='40' value="<?php echo isset($poll['option15']) ? $poll['option15'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 17</td>
            <td class='rowhead' align='left'>
                <input name='option16' size='80' maxlength='40' value="<?php echo isset($poll['option16']) ? $poll['option16'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 18</td>
            <td class='rowhead' align='left'>
                <input name='option17' size='80' maxlength='40' value="<?php echo isset($poll['option17']) ? $poll['option17'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 19</td>
            <td class='rowhead' align='left'>
                <input name='option18' size='80' maxlength='40' value="<?php echo isset($poll['option18']) ? $poll['option18'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Option 20</td>
            <td class='rowhead' align='left'>
                <input name='option19' size='80' maxlength='40' value="<?php echo isset($poll['option19']) ? $poll['option19'] : ''?>" /><br />
            </td>
        </tr>

        <tr>
            <td class='rowhead'>Sort</td>
            <td class='rowhead'>
                <input type='radio' name='sort' value='yes' <?php echo isset($poll['sort']) ? ($poll["sort"] != "no" ? " checked='checked'" : "") : '' ?> />Yes
                <input type='radio' name='sort' value='no' <?php echo isset($poll['sort']) ? ($poll["sort"] == "no" ? " checked=checked'" : "") : '' ?> />No
            </td>
        </tr>
        <tr>
            <td class='rowhead' align='center' colspan='2'>
                <input type='submit' class='btn' value=<?php echo $pollid ? "'Edit Poll'" : "'Create Poll'"?> style='height: 20pt' />
            </td>
        </tr>
    </table>
    <p align='center'><span style='color : #ff0000;'>* </span> = Required Fields</p>
    <input type='hidden' name='pollid' value='<?php echo isset($poll["id"]) ? $poll['id'] : 0?>' />
    <input type='hidden' name='action' value='<?php echo $pollid ? 'edit' : 'create'?>' />
    <input type='hidden' name='returnto' value='<?php echo htmlentities($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"])?>' />
</form>

<?php

site_footer();

?>