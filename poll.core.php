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

//sleep(2);

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_bbcode.php');

db_connect();

$do		= (isset($_POST["action"]) ? $_POST["action"] : "");
$choice = (isset($_POST["choice"]) ? 0+$_POST["choice"] : 0 );
$pollId = (isset($_POST["pollId"]) ? 0+$_POST["pollId"] : 0 );
$userId = 0+$CURUSER["id"];

if($do == "load")
{
	//check to see if user voted :)
	$r_check = sql_query("SELECT p.id,p.added,p.question,pa.selection,pa.userid
							FROM polls AS p
							LEFT JOIN pollanswers AS pa ON p.id=pa.pollid
							AND pa.userid=".$userId."
							ORDER BY p.id DESC
							LIMIT 1") or sqlerr();

	$ar_check = mysql_fetch_assoc($r_check);

	if(mysql_num_rows($r_check) == 1)
	{
		$r_op = sql_query("SELECT *
							FROM polls
							WHERE id=".$ar_check["id"]) or sqlerr();

		$a_op = mysql_fetch_assoc($r_op);

		for($i=0;$i<20;$i++)
		{
			if(!empty($a_op["option$i"]))

			$options[$i] = format_comment($a_op["option$i"]);
		}

		if(get_user_class() >= UC_MODERATOR)
		{
			$modop = "<a href=\"polls.php?action=delete&pollid=".$ar_check["id"]."&returnto=/index.php\">D</a>&nbsp;";

			$modop .= "<a href=\"makepoll.php?action=edit&pollid=".$ar_check["id"]."&returnto=/index.php\">E</a>";
		}

		if($ar_check["userid"] == NULL )
		{
			print("<div id=\"poll_title\">".format_comment($ar_check["question"])."&nbsp;[".$modop."]&nbsp;</div>\n");

			foreach($options as $op_id=>$op_val)
			{
				print("<div align=\"left\"><input type=\"radio\" onclick=\"addvote(".$op_id.")\" name=\"choices\" value=\"".$op_id."\" id=\"opt_".$op_id."\" /><label for=\"opt_".$op_id."\">&nbsp;".$op_val."</label></div>\n");
			}

			print("<div align=\"left\"><input type=\"radio\" onclick=\"addvote(255)\" name=\"choices\" value=\"255\" id=\"opt_255\" /><label for=\"opt_255\">&nbsp;Blank Vote (a.k.a. \"I just want to see the results!\")</label></div>\n");

			print("<input type=\"hidden\" value=\"\" name=\"choice\" id=\"choice\"/>");
			print("<input type=\"hidden\" value=\"".$ar_check["id"]."\" name=\"pollId\" id=\"pollId\"/>");
			print("<div align=\"center\"><input type=\"button\" class='btn' value=\"Vote ->\" style=\"display:none;\" id=\"vote_b\" onclick=\"vote();\"/></div>");
		}
		else
		{
			$r = sql_query("SELECT count(id) AS count , selection
							FROM pollanswers
							WHERE pollid=".$ar_check["id"]."
							AND selection < 20 GROUP BY selection") or sqlerr();

			while($a = mysql_fetch_assoc($r))
			{
				$total += $a["count"];
				$votes[$a["selection"]] = 0+$a["count"];
			}

			foreach($options as $k=>$op)
			{
				$results[] = array(0+$votes[$k],$op);
			}

			function srt($a,$b)
			{
				if ($a[0] > $b[0]) return -1;
				if ($a[0] < $b[0]) return 1;
					return 0;
			}
			usort($results, srt);

			print("<div id=\"poll_title\" class='poll'>".format_comment($ar_check["question"])."&nbsp;[".$modop."]&nbsp;</div>\n");

			print("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" style=\"border:none\" id=\"results\" class=\"results\">");

			$i= 0;

			foreach($results as $result)
			{
				print("<tr>
						<td align=\"left\" width=\"40%\" style=\"border:none;\">".$result[1]."</td>
						<td class=\"std\" align=\"left\" width=\"60%\" valing=\"middle\">
						<div class=\"bar".($i == 0 ? "max" : "")."\"  name=\"".($result[0] / $total * 100)."\" id=\"poll_result\">&nbsp;</div></td>
						<td class=\"std\">&nbsp;<span style=\"font-weight:bold;\">".number_format(($result[0] / $total * 100),2)."%</span></td>
					</tr>\n");

				$i++;
			}

			print("</table>");
			print("<div align=\"center\" class=\"poll\"><span style=\"font-weight:bold;\">Votes</span> : ".$total."</div>");
		}
	}
else
	print("No Current Poll");
}
elseif($do == "vote")
{
	if ($pollId == 0 )
		print(json_encode(array("status" =>0 , "msg"=>"Something was not good!")));

	else
	{
		$check = mysql_result(sql_query("SELECT count(id)
											FROM pollanswers
											WHERE pollid=".$pollId."
											AND userid=".$userId.""),0);

		if($check == 0)
		{
			sql_query("INSERT INTO pollanswers
						VALUES(0,$pollId, $userId, $choice)") or die(mysql_error());

			if (mysql_affected_rows() != 1)
				print(json_encode(array("status" =>0 , "msg"=>"There was an Error with storing your Vote! Please try again")));
			else
				print(json_encode(array("status" =>1)));
		}
	else
		print(json_encode(array("status" =>0 , "msg"=>"Dupe Vote")));
	}
}

?>