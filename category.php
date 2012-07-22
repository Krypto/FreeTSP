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

//made by putyn @ tbdev
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');

db_connect(false);
logged_in();

if (get_user_class() < UC_SYSOP)
	error_message("warn", "Warning", "Permission Denied!");

$vactg		= array("delete","edit", "" );
$actiong	= (isset($_GET["action"]) ? $_GET["action"] : "" );

if (!in_array($actiong , $vactg))
	error_message("error", "Error", "Not an Valid Action!");

if (($actiong == "edit" || $actiong == "delete") && $_GET["cid"] == 0 )
	error_message("error", "Error", "Missing Argument Category ID");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$vaction	= array( "edit", "add" , "delete");
	$action		= ((isset($_POST["action"]) && in_array($_POST["action"], $vaction)) ? $_POST["action"] : "" );

	if (!$action)
		error_message("error", "Error", "Something Missing");

	if ($action== "add") //add new category
	{
		$name = htmlentities($_POST["cname"]);

		if (empty($name))
			error_message("error", "Error", "Missing Category Name!");

		$image = htmlentities($_POST["cimage"]);

		if (empty($image))
			error_message("error", "Error", "Missing Category Image!");

		$add = sql_query("INSERT INTO categories ( name ,image )
							VALUES ( ".sqlesc($name).", ".sqlesc($image).") ") or sqlerr(__FILE__, __LINE__);

		if ($add)
			error_message("success", "Success", "New Category Created.  Go <a href=\"".$site_url."/category.php\">back</a> and Create more!");
	}//end action add

	if ($action == "edit")
	{ //edit action

		$cid		= (isset($_POST["cid"]) ? 0 + $_POST["cid"] : "");
		$cname_edit = htmlentities($_POST["cname_edit"]);

		if (empty($cname_edit))
			error_message("error", "Error", "Missing Category Name!");

		$cimage_edit = htmlentities($_POST["cimage_edit"]);

		if (empty($cimage_edit))
			error_message("error", "Error", "Missing Category Image!");

		$edit = sql_query("UPDATE categories
							SET name=".sqlesc($cname_edit).", image=".sqlesc($cimage_edit)."
							WHERE id=".sqlesc($cid)." ") or sqlerr(__FILE__, __LINE__);

		if ($edit)
			error_message("success", "Success", "Category Successfully Edited! Go <a href=\"".$site_url."/category.php\">back</a>");
	}//end action edit
}

if ($actiong == "edit" )
{
	$catid = (isset($_GET["cid"]) ? 0 + $_GET["cid"] : "");

	site_header("Edit Category");

	$res = sql_query("SELECT id,name, image
						FROM categories
						WHERE id=".sqlesc($catid)."
						LIMIT 1 ") or sqlerr(__FILE__, __LINE__);

	$arr	= mysql_fetch_assoc($res);
	$cname	= htmlentities($arr["name"]);
	$cimage	= htmlentities($arr["image"]);

	begin_frame("Edit Category");

	print('<form action="category.php" method="post">');
	print('<table class="main" border="1" cellspacing="0" align="center" cellpadding="5">');

	print('<tr>
			<td class="colhead"><label for="cname_edit">Cat Name</label></td>
			<td class="rowhead" align="left"><input type="text" name="cname_edit" size="50" id="cname_edit" value="'.$cname."\" onclick=\"select()\" /></td>
		</tr>");

	print('<tr>
			<td class="colhead"><label for="cimage_edit">Cat Image</label></td>
			<td class="rowhead" align="left"><input type="text" name="cimage_edit" id="cimage_edit" size="50" value="'.$cimage."\" onclick=\"select()\" /></td>
		</tr>");

	print('<tr>
			<td class="std" align="center" colspan="2"><input type="submit" class="btn" name="submit" value="Edit Category" /><input type="hidden" name="action" value="edit" /><input type="hidden" name="cid" value="'.$arr["id"]."\" /></td>");

	print("</tr>");
	print("</table>");
	print("</form>");

	end_frame();
	site_footer();
}
elseif ($actiong == "delete")
{
	$catid = (isset($_GET["cid"]) ? 0 + $_GET["cid"] : "");

	$res = sql_query("SELECT id, name
						FROM categories
						WHERE id=".sqlesc($catid)."") or sqlerr(__FILE__, __LINE__);

	$arr	= mysql_fetch_assoc($res);
	$count	= mysql_num_rows($res);

	if ($count == 1)
	{
		$delete = sql_query("DELETE
								FROM categories
								WHERE id=".sqlesc($catid)."") or sqlerr(__FILE__, __LINE__);

		if ($delete)
		{
			write_log("".$CURUSER["username"]." Deleted Category ".$arr["name"]."");
			error_message("success", "Success", "Category Successfully Deleted! Go <a href=\"".$site_url."/category.php\">Back</a>");
		}
	}
else
	error_message("error", "Error", "No Category with that ID!");
}
else
{
	site_header("Categories");

	//add categories form
	begin_frame("Add a Category");

	print('<form action="category.php" method="post">');
	print('<table class="main" border="1" cellspacing="0" align="center" cellpadding="5">');

	print('<tr>
			<td class="colhead"><label for="cname">Cat Name</label></td>
			<td class="rowhead" align="left"><input type="text" name="cname" id="cname" size="50" /></td>
		</tr>');

	print('<tr>
			<td class="colhead"><label for="cimage">Cat Image</label></td>
			<td class="rowhead" align="left"><input type="text" name="cimage" id="cimage" size="50" /></td>
		</tr>');

	print('<tr>
			<td align="center" colspan="2"><input type="submit" name="submit" value="Add Category" class=\'btn\' /><input type="hidden" name="action" value="add" /></td>');
	print("</tr>");

	print("</table>");
	print("</form>");

	end_frame();

	//print existing catergories
	begin_frame("Categories");

	$res = sql_query("SELECT id, name, image
						FROM categories
						ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);

	$count = mysql_num_rows($res);

	if ($count > 0)
	{
		print("<table class='main' border='1' cellspacing='0' align='center' cellpadding='5'>");
		print("<tr>");
		print("<td class='colhead'>ID</td>");
		print("<td class='colhead'>Cat Name</td>");
		print("<td class='colhead'>Cat Image</td>");
		print("<td class='colhead' colspan='2'>Action</td>");
		print("</tr>");

			while ($arr = mysql_fetch_assoc($res))
			{
				$edit = "<a href='/category.php?action=edit&amp;cid=".$arr["id"]."'><img src='".$image_dir."button_edit2.gif' width='16' height='16' border='0' alt='Edit Category' title='Edit Category' style='border:none;padding:3px;' /></a>";

				$delete = "<a href='/category.php?action=delete&amp;cid=".$arr["id"]."'><img src='".$image_dir."del.png' width='16' height='16' border='0' alt='Drop Category' title='Drop Category' style='border:none;padding:3px;' /></a>";

				print("<tr>");
				print("<td class='rowhead' align='center'><a href='/browse.php?cat=".$arr["id"]."'>".$arr["id"]."</a></td>");
				print("<td class='rowhead' align='center'><a href='/browse.php?cat=".$arr["id"]."'>".$arr["name"]."</a></td>");
				print("<td class='rowhead' align='center'><a href='/browse.php?cat=".$arr["id"]."'><img src='".$image_dir."caticons/".$arr["image"]."' width='60' height='54' border='0' alt='".$arr["name"]."' title='".$arr["name"]."'/></a></td>");
				print("<td class='rowhead' align='center'>$edit</td><td class='rowhead' align='center'>$delete</td>");
				print("</tr>");
			}
		print("</table>");
	}
	else
		display_message("info", "Sorry", "No Categories were found!");

	end_frame();

	site_footer();
}

?>