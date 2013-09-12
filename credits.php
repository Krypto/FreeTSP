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

parked();

function CutName ($txt, $len)
{
	return (strlen($txt)>$len ? substr($txt,0,$len-4).'[...]':$txt);
}

	//-- Get Stuff For The Pager --//
	$count_query = sql_query('SELECT COUNT(id)
								FROM modscredits');

	$count_arr = mysql_fetch_row($count_query);
	$count     = $count_arr[0];
	$page      = isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$perpage   = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 10;

	list($menu, $LIMIT) = pager_new($count, $perpage, $page, 'credits.php?'.($perpage == 10 ? '' : '&amp;perpage='.$perpage));

	$res = sql_query('SELECT *
						FROM modscredits
						ORDER BY id ASC '.$LIMIT);

	site_header('Credits',false);

	print("<div align='center'><strong>Mods Installed In The $site_name Source Code</strong></div><br />");
    print("<div align='center'>".$menu."<br /><br /></div>");
	print("<table border='1' align='center' width='90%' cellpadding='10' cellspacing='1'>");
	print("<tr>");
	print("<td class='colhead' align='center' width='51%'>Modification Name</td>");
	print("<td class='colhead' align='center' width='15%'>Category</td>");
	print("<td class='colhead' align='center' width='10%'>Status</td>");
	print("<td class='colhead' align='center' width='10%'>Original Coder</td>");
	print("<td class='colhead' align='center' width='10%'>Modified By</td>");

	if (get_user_class() >= UC_MANAGER)
	{
		print("<td class='colhead' align='center' width='4%' colspan='2'>Action</td>");
	}

	print("</tr>");

while ($row = mysql_fetch_assoc($res))
{
	$id       = $row["id"];
	$name     = $row["name"];
	$category =$row["category"];

	if($row["status"]=="In-Progress")
	{
		$status = "[b][color=#ff0000]".$row["status"]."[/color][/b]";
	}
	else
	{
		$status = "[b][color=#018316]".$row["status"]."[/color][/b]";
	}

	$link     = $row["mod_link"];
	$credit   = $row["credit"];
	$modified = $row["modified"];
	$descr    = $row["description"];

	print("<tr><td class='rowhead'><a class='altlink' href='".$link."' target='_blank'>".htmlspecialchars(CutName($name,90))."</a>");

	print("<br/><font class='small'>".htmlspecialchars($descr)."</font></td>");
	print("<td class='rowhead' align='center'><strong>".htmlspecialchars($category)."</strong></td>");
	print("<td class='rowhead' align='center'><strong>".format_comment($status)."</strong></td>");
	print("<td class='rowhead' align='center'><strong>".htmlspecialchars($credit)."</strong></td>");
	print("<td class='rowhead' align='center'><strong>".htmlspecialchars($modified)."</strong></td>");

	if (get_user_class() >= UC_MANAGER)
	{
		print("<td class='rowhead'><a href='controlpanel.php?fileaction=29&amp;action=edit_credit&amp;id=".$id."'><img src='".$image_dir."admin/edit.png' width='16' height='16' border='0' alt='edit' title='Edit' /></a></td>");

		print("<td class='rowhead'><a href='controlpanel.php?fileaction=29&amp;action=delete_credit&amp;id=".$id."'><img src='".$image_dir."admin/delete.png' width='16' height='16' border='0' alt='delete' title='Delete' /></a></td>");
	}

	print("</tr>");
}

    print("</table><br /><br />");
    print("<div align='center'>".$menu."<br /><br /></div>");

    site_footer();
?>