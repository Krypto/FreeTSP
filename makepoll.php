<?php

/*
*-------------------------------------------------------------------------------*
*----------------    |  ____|        |__   __/ ____|  __ \        --------------*
*----------------    | |__ _ __ ___  ___| | | (___ | |__) |       --------------*
*----------------    |  __| '__/ _ \/ _ \ |  \___ \|  ___/        --------------*
*----------------    | |  | | |  __/  __/ |  ____) | |            --------------*
*----------------    |_|  |_|  \___|\___|_| |_____/|_|            --------------*
*-------------------------------------------------------------------------------*
*---------------------------    FreeTSP  v1.0   --------------------------------*
*-------------------   The Alternate BitTorrent Source   -----------------------*
*-------------------------------------------------------------------------------*
*-------------------------------------------------------------------------------*
*--   This program is free software; you can redistribute it and /or modify   --*
*--   it under the terms of the GNU General Public License as published by    --*
*--   the Free Software Foundation; either version 2 of the License, or       --*
*--   (at your option) any later version.                                     --*
*--                                                                           --*
*--   This program is distributed in the hope that it will be useful,         --*
*--   but WITHOUT ANY WARRANTY; without even the implied warranty of          --*
*--   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           --*
*--   GNU General Public License for more details.                            --*
*--                                                                           --*
*--   You should have received a copy of the GNU General Public License       --*
*--   along with this program; if not, write to the Free Software             --*
*-- Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA  --*
*--                                                                           --*
*-------------------------------------------------------------------------------*
*------------   Original Credits to tbSource, Bytemonsoon, TBDev   -------------*
*-------------------------------------------------------------------------------*
*-------------           Developed By: Krypto, Fireknight           ------------*
*-------------------------------------------------------------------------------*
*-----------------       First Release Date August 2010      -------------------*
*-----------                 http://www.freetsp.info                 -----------*
*------                    2010 FreeTSP Development Team                  ------*
*-------------------------------------------------------------------------------*
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');

db_connect();
logged_in();

if (get_user_class() < UC_MODERATOR)
	error_message("warn","Warning","Permission Denied.");

$action = isset($_GET["action"]) ? $_GET["action"] : $_POST["action"];
$pollid = (int)(isset($_GET["pollid"]) ? $_GET["pollid"] : $_POST["pollid"]);

if ($action == "edit")
{
	if (!is_valid_id($pollid))
		error_message("error","Error","Invalid ID.");

	$res = sql_query("SELECT *
						FROM polls
						WHERE id = $pollid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0)
		error_message("error","Error","No Poll found with that ID.");

	$poll = mysql_fetch_assoc($res);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($action=='edit' && !is_valid_id($pollid))
		error_message("error","Error","Invalid ID.");

		$question = htmlentities($_POST["question"]);
		$option0  = htmlentities($_POST["option0"]);
		$option1  = htmlentities($_POST["option1"]);
		$option2  = htmlentities($_POST["option2"]);
		$option3  = htmlentities($_POST["option3"]);
		$option4  = htmlentities($_POST["option4"]);
		$option5  = htmlentities($_POST["option5"]);
		$option6  = htmlentities($_POST["option6"]);
		$option7  = htmlentities($_POST["option7"]);
		$option8  = htmlentities($_POST["option8"]);
		$option9  = htmlentities($_POST["option9"]);
		$option10 = htmlentities($_POST["option10"]);
		$option11 = htmlentities($_POST["option11"]);
		$option12 = htmlentities($_POST["option12"]);
		$option13 = htmlentities($_POST["option13"]);
		$option14 = htmlentities($_POST["option14"]);
		$option15 = htmlentities($_POST["option15"]);
		$option16 = htmlentities($_POST["option16"]);
		$option17 = htmlentities($_POST["option17"]);
		$option18 = htmlentities($_POST["option18"]);
		$option19 = htmlentities($_POST["option19"]);
		$sort     = (int)$_POST["sort"];
		$returnto = htmlentities($_POST["returnto"]);

	if (!$question || !$option0 || !$option1)
		error_message("Error","Error", "Missing Data!");

	if ($pollid)
		sql_query("UPDATE polls SET " .
		"question = " . sqlesc($question) . ", " .
		"option0  = " . sqlesc($option0) . ", " .
		"option1  = " . sqlesc($option1) . ", " .
		"option2  = " . sqlesc($option2) . ", " .
		"option3  = " . sqlesc($option3) . ", " .
		"option4  = " . sqlesc($option4) . ", " .
		"option5  = " . sqlesc($option5) . ", " .
		"option6  = " . sqlesc($option6) . ", " .
		"option7  = " . sqlesc($option7) . ", " .
		"option8  = " . sqlesc($option8) . ", " .
		"option9  = " . sqlesc($option9) . ", " .
		"option10 = " . sqlesc($option10) . ", " .
		"option11 = " . sqlesc($option11) . ", " .
		"option12 = " . sqlesc($option12) . ", " .
		"option13 = " . sqlesc($option13) . ", " .
		"option14 = " . sqlesc($option14) . ", " .
		"option15 = " . sqlesc($option15) . ", " .
		"option16 = " . sqlesc($option16) . ", " .
		"option17 = " . sqlesc($option17) . ", " .
		"option18 = " . sqlesc($option18) . ", " .
		"option19 = " . sqlesc($option19) . ", " .
		"sort     = " . sqlesc($sort) . " " .
	"WHERE id = $pollid") or sqlerr(__FILE__, __LINE__);
	else
		sql_query("INSERT INTO polls VALUES(0" .
		", '" . get_date_time() . "'" .
		", " . sqlesc($question) .
		", " . sqlesc($option0) .
		", " . sqlesc($option1) .
		", " . sqlesc($option2) .
		", " . sqlesc($option3) .
		", " . sqlesc($option4) .
		", " . sqlesc($option5) .
		", " . sqlesc($option6) .
		", " . sqlesc($option7) .
		", " . sqlesc($option8) .
		", " . sqlesc($option9) .
 		", " . sqlesc($option10) .
		", " . sqlesc($option11) .
		", " . sqlesc($option12) .
		", " . sqlesc($option13) .
		", " . sqlesc($option14) .
		", " . sqlesc($option15) .
		", " . sqlesc($option16) .
		", " . sqlesc($option17) .
		", " . sqlesc($option18) .
		", " . sqlesc($option19) .
		", " . sqlesc($sort) .
		")") or sqlerr(__FILE__, __LINE__);

	if ($returnto == "main")
		header("Location: $site_url");
	elseif ($pollid)
		header("Location: $site_url/polls.php#$pollid");
	else
		header("Location: $site_url");
	die;
}

site_header();

if ($pollid)
	print("<h1>Edit Poll</h1>");
else
{
	// Warn if current poll is less than 3 days old
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
				$t = "$days Day" . ($days > 1 ? "s" : "");
			else
				$t = "$hours Hour" . ($hours > 1 ? "s" : "");
				print("<p><span style='color : #ff0000; font-weight:bold;'>Note: The Current Poll (<span style='font-style: italic;'>" . $arr["question"] . "</span>) is only $t Old.</span></p>");
		}
	}
	print("<h1>Make Poll</h1>");
}
?>

<form method='post' action='makepoll.php'>
	<table align='center' border='1' cellspacing='0' cellpadding='5'>
		<tr>
		<td class='rowhead'>Question <span style='color : #ff0000;'>*</span></td>
		<td class='rowhead' align='left'><input name='question' size='80' maxlength='255' value="<?php echo isset($poll['question']) ? $poll['question'] : ''?>" /></td>
		</tr>

		<tr>
		<td class='rowhead'>Option 1 <span style='color : #ff0000;'>*</span></td>
		<td class='rowhead' align='left'><input name='option0' size='80' maxlength='40' value="<?php echo isset($poll['option0']) ? $poll['option0'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 2 <span style='color : #ff0000;'>*</span></td>
			<td class='rowhead' align='left'><input name='option1' size='80' maxlength='40' value="<?php echo isset($poll['option1']) ? $poll['option1'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 3</td>
			<td class='rowhead' align='left'><input name='option2' size='80' maxlength='40' value="<?php echo isset($poll['option2']) ? $poll['option2'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 4</td>
			<td class='rowhead' align='left'><input name='option3' size='80' maxlength='40' value="<?php echo isset($poll['option3']) ? $poll['option3'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 5</td>
			<td class='rowhead' align='left'><input name='option4' size='80' maxlength='40' value="<?php echo isset($poll['option4']) ? $poll['option4'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 6</td>
			<td class='rowhead' align='left'><input name='option5' size='80' maxlength='40' value="<?php echo isset($poll['option5']) ? $poll['option5'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 7</td>
			<td class='rowhead' align='left'><input name='option6' size='80' maxlength='40' value="<?php echo isset($poll['option6']) ? $poll['option6'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 8</td>
			<td class='rowhead' align='left'><input name='option7' size='80' maxlength='40' value="<?php echo isset($poll['option7']) ? $poll['option7'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 9</td>
			<td class='rowhead' align='left'><input name='option8' size='80' maxlength='40' value="<?php echo isset($poll['option8']) ? $poll['option8'] : ''?>" /><br /></td>
		</tr>


		<tr>
			<td class='rowhead'>Option 10</td>
			<td class='rowhead' align='left'><input name='option9' size='80' maxlength='40' value="<?php echo isset($poll['option9']) ? $poll['option9'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 11</td>
			<td class='rowhead' align='left'><input name='option10' size='80' maxlength='40' value="<?php echo isset($poll['option10']) ? $poll['option10'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 12</td>
			<td class='rowhead' align='left'><input name='option11' size='80' maxlength='40' value="<?php echo isset($poll['option11']) ? $poll['option11'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 13</td>
			<td class='rowhead' align='left'><input name='option12' size='80' maxlength='40' value="<?php echo isset($poll['option12']) ? $poll['option12'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 14</td>
			<td class='rowhead' align='left'><input name='option13' size='80' maxlength='40' value="<?php echo isset($poll['option13']) ? $poll['option13'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 15</td>
			<td class='rowhead' align='left'><input name='option14' size='80' maxlength='40' value="<?php echo isset($poll['option14']) ? $poll['option14'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 16</td>
			<td class='rowhead' align='left'><input name='option15' size='80' maxlength='40' value="<?php echo isset($poll['option15']) ? $poll['option15'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 17</td>
			<td class='rowhead' align='left'><input name='option16' size='80' maxlength='40' value="<?php echo isset($poll['option16']) ? $poll['option16'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 18</td>
			<td class='rowhead' align='left'><input name='option17' size='80' maxlength='40' value="<?php echo isset($poll['option17']) ? $poll['option17'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 19</td>
			<td class='rowhead' align='left'><input name='option18' size='80' maxlength='40' value="<?php echo isset($poll['option18']) ? $poll['option18'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Option 20</td>
			<td class='rowhead' align='left'><input name='option19' size='80' maxlength='40' value="<?php echo isset($poll['option19']) ? $poll['option19'] : ''?>" /><br /></td>
		</tr>

		<tr>
			<td class='rowhead'>Sort</td>
			<td class='rowhead'>
				<input type='radio' name='sort' value='yes' <?php echo isset($poll['sort']) ? ($poll["sort"] != "no" ? " checked='checked'" : "") : '' ?> />Yes
				<input type='radio' name='sort' value='no' <?php echo isset($poll['sort']) ? ($poll["sort"] == "no" ? " checked=checked'" : "") : '' ?> /> No
			</td>
		</tr>
		<tr>
			<td class='rowhead' colspan='2' align='center'><input type='submit' class='btn' value=<?php echo $pollid?"'Edit Poll'":"'Create Poll'"?> style='height: 20pt' /></td>
		</tr>
	</table>
	<p align='center'><span style='color : #ff0000;'>* </span> = Required Fields</p>
	<input type='hidden' name='pollid' value='<?php echo isset($poll["id"]) ? $poll['id'] : 0?>' />
	<input type='hidden' name='action' value='<?php echo $pollid?'edit':'create'?>' />
	<input type='hidden' name='returnto' value='<?php echo htmlentities($_GET["returnto"]) ? htmlentities($_GET["returnto"]) : htmlentities($_SERVER["HTTP_REFERER"])?>' />
</form>

<?php

site_footer();

?>