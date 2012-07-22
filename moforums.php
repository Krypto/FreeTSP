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

if (get_user_class() < UC_SYSOP)
	error_message("warn", "Warning", "Permission Denied.");

//Presets
$act = $_GET['act'];
$id  = 0 + $_GET['id'];

if (!$act)
{
	$act = "forum";
}

// Delete Forum Action
if ($act == "del")
{
	if (get_user_class() < UC_SYSOP)
		error_message("warn", "Warning", "Permission Denied.");

	if (!$id) { header("Location: $PHP_SELF?act=forum");
		die();}

	sql_query ("DELETE
				FROM overforums
				WHERE id = $id") or sqlerr(__FILE__, __LINE__);

	header("Location: $PHP_SELF?act=forum");
	die();
}

//Edit Forum Action
if ($_POST['action'] == "editforum")
{
	if (get_user_class() < UC_SYSOP)
		error_message("warn", "Warning", "Permission Denied.");

	$name = $_POST['name'];
	$desc = $_POST['desc'];

	if (!$name && !$desc && !$id)
	{
		header("Location: $PHP_SELF?act=forum");
		die();
	}

	sql_query("UPDATE overforums
				SET sort = '" . $_POST['sort'] . "', name = " . sqlesc($_POST['name']). ", description = " . sqlesc($_POST['desc']). ", forid = 0, minclassview = '" . $_POST['viewclass'] . "'
				WHERE id = '".$_POST['id']."'") or sqlerr(__FILE__, __LINE__);

	header("Location: $PHP_SELF?act=forum");
	die();
}

//Add Forum Action
if ($_POST['action'] == "addforum")
{
	if (get_user_class() < UC_SYSOP)
		error_message("warn", "Warning", "Permission Denied.");

	$name = trim($_POST['name']);
	$desc = trim($_POST['desc']);

	if (!$name && !$desc)
	{
		header("Location: $PHP_SELF?act=forum");
		die();
	}

	sql_query("INSERT INTO overforums (sort, name,  description,  minclassview, forid)
				VALUES(" . $_POST['sort'] . ", " . sqlesc($_POST['name']). ", " . sqlesc($_POST['desc']). ", " . $_POST['viewclass'] . ", 1)") or sqlerr(__FILE__, __LINE__);

	header("Location: $PHP_SELF?act=forum");
	die();
}

site_header("Overforum Edit");

if ($act == "forum")
{
	// Show Forums With Forum Managment Tools
	begin_frame("Overforums");
?>
<script type='text/javascript'>
<!--
function confirm_delete(id)
{
	if(confirm('Are you sure you want to Delete this Overforum?'))
	{
		self.location.href='<? $PHP_SELF; ?>?act=del&id='+id;
	}
}
//-->
</script>

<?php

	echo '<table width="100%"  border="0" align="center" cellpadding="2" cellspacing="0">';
	echo "<tr><td class='colhead' align='left'>Name</td><td class='colhead'>Viewed By</td><td class='colhead'>Modify</td></tr>";

	$result = sql_query ("SELECT *
							FROM overforums
							ORDER BY SORT ASC");

	if ($row = mysql_fetch_array($result))
	{
		do
		{
			echo "<tr><td class='rowhead'><a href='moforums.php?action=forumview&amp;forid=".$row["id"]."'><span style='font-weight:bold;'>".$row["name"]."</span></a><br />".$row["description"]."</td>";

			echo "<td class='rowhead'>" . get_user_class_name($row["minclassview"]) . "</td><td align='center'><span style='font-weight:bold;'><a href='".$PHP_SELF."?act=editforum&amp;id=".$row["id"]."'>Edit</a>&nbsp;|&nbsp;<a href='javascript:confirm_delete(".$row["id"].");'><span style='color : #ff0000;'>Delete</span></a></span></td></tr>";
		}
			while($row = mysql_fetch_array($result));
		}
		else
		{
			display_message("info", "Sorry", "No Records were Found!");
		}
		echo "</table>";

?>

<br /><br />
<form method='post' action='<?php echo $PHP_SELF;?>'>
	<table width='100%'  border='0' cellspacing='0' cellpadding='3' align='center'>
		<tr align='center'>
			<td colspan='2' class='colhead'>Make New Forum</td>
		</tr>
		<tr>
			<td class='rowhead'><span style='font-weight:bold;'><label for='name'>Overforum Name</label></span></td>
			<td class='rowhead'><input type="text" name="name" id="name" size="20" maxlength="60" /></td>
		</tr>
		<tr>
			<td class='rowhead'><span style='font-weight:bold;'><label for='desc'>Overforum Description</label></span></td>
			<td class='rowhead'>
				<input type="text" name="desc" id="desc" size="30" maxlength="200" />
			</td>
		</tr>

		<tr>
			<td class='rowhead'><span style='font-weight:bold;'>Minimun View Permission</span></td>
			<td class='rowhead'>
				<select name='viewclass'>
	<?php

	$maxclass = get_user_class();

	for ($i = 0; $i <= $maxclass; ++$i)

	print("<option value='$i'" . ($user["class"] == $i ? " selected='selected'" : "") . ">$prefix" . get_user_class_name($i) . "</option>\n");

	?>
				</select>
			</td>
		</tr>
		<tr>
			<td class='rowhead'><span style='font-weight:bold;'>Overforum Rank</span></td>
			<td class='rowhead'>
				<select name='sort'>

	<?

	$res = sql_query ("SELECT sort
							FROM overforums");

	$nr			= mysql_num_rows($res);
	$maxclass	= $nr + 1;

	for ($i = 0; $i <= $maxclass; ++$i)

	print("<option value='$i'>$i </option>\n");

	?>
				</select>
			</td>
		</tr>
		<tr align="center">
			<td class='rowhead' colspan="2"><input type="hidden" name="action" value="addforum" /><input type="submit" class="btn" name="Submit" value="Make Overforum" /></td>
		</tr>
	</table>
</form>

<?php

print("<table width='100%'  border='0' cellspacing='0'cellpadding='3' align='center'>
		<tr>
			<td class='rowhead' align='center' colspan='1' height='20px'>
				<form method='get' action='forummanage.php#add'><input type='submit' class='btn' value='Forum Manager'/></form>
			</td>
		</tr>
	</table>\n");

end_frame();
}

?>

<?php

if ($act == "editforum")
{
	//Edit Page For The Forums
	$id = 0+$_GET["id"];

	begin_frame("Edit Overforum");

	$result  = sql_query ("SELECT *
							FROM overforums
							WHERE id = '$id'");

	if ($row = mysql_fetch_array($result))
	{
	// Get OverForum Name - To Be Written
		do
		{
?>

<form method='post' action="<?php echo $PHP_SELF;?>">
	<table width="100%"  border="0" cellspacing="0" cellpadding="3" align="center">
		<tr align="center">
			<td colspan="2" class='colhead'>Edit Overforum: <?php echo $row["name"];?></td>
		</tr>
		<tr>
			<td class='rowhead'><span style='font-weight:bold;'><label for='name'>Overforum Name</label></span></td>
			<td class='rowhead'><input type="text" name="name" id="name" size="20" maxlength="60" value="<?php echo $row["name"];?>" /></td>
		</tr>
		<tr>
			<td class='rowhead'><span style='font-weight:bold;'><label for='desc'>Overforum Description</label></span></td>
			<td class='rowhead'><input type="text" name="desc" id="desc" size="30" maxlength="200" value="<?php echo $row["description"];?>" /></td>
		</tr>
		<tr>
			<td class='rowhead'><span style='font-weight:bold;'>Minimun View Permission</span></td>
			<td class='rowhead'>
				<select name='viewclass'>

	<?php

	$maxclass = get_user_class();

	for ($i = 0; $i <= $maxclass; ++$i)

	print("<option value='$i'" . ($row["minclassview"] == $i ? " selected='selected'" : "") . ">$prefix" . get_user_class_name($i) . "</option>\n");

	?>

				</select>
			</td>
		</tr>
		<tr>
			<td class='rowhead'><span style='font-weight:bold;'>Overforum Rank</span></td>
			<td class='rowhead'>
				<select name='sort'>
	<?php

	$res = sql_query ("SELECT sort
							FROM overforums");

	$nr			= mysql_num_rows($res);
	$maxclass	= $nr + 1;

	for ($i = 0; $i <= $maxclass; ++$i)

	print("<option value='$i'" . ($row["sort"] == $i ? " selected='selected'" : "") . ">$i </option>\n");

	?>
				</select>
			</td>
		</tr>
		<tr align="center">
			<td colspan="2"><input type="hidden" name="action" value="editforum" /><input type="hidden" name="id" value="<?php echo $id;?>" /><input type="submit" class="btn" "name="Submit" value="Edit Overforum" /></td>
		</tr>
	</table>
</form>

<?php
}
while($row = mysql_fetch_array($result));
}
else
{
	display_message("info", "Sorry", "No Records were Found!  <a href='moforums.php#add'>Click here to Return</a>");
}

end_frame();
}
?>

<?php

echo("<br />");

site_footer();
?>