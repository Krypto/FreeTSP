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
*--   This program is free software; you can redistribute it and / or modify  --*
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

if (get_user_class() < UC_TRACKER_MANAGER) 
{
    error_message("warn", "Warning", "<div align='center'>Access Denied.</div>");
}

if ($_POST["action"] == '0')
{
    mysql_query("SELECT status
                    FROM emailconfirm
                    WHERE 1=1") or sqlerr(__FILE__, __LINE__);

    mysql_query("UPDATE emailconfirm
                    SET status='0' ") or sqlerr(__FILE__, __LINE__);

}
elseif ($_POST["action"] == '1')
{
    mysql_query("SELECT status
                    FROM emailconfirm
                    WHERE 1=1") or sqlerr(__FILE__, __LINE__);

    mysql_query("UPDATE emailconfirm
                    SET status='1' ") or sqlerr(__FILE__, __LINE__);
}

$query = "SELECT status
            FROM emailconfirm
            WHERE 1=1";

$sql = sql_query($query);

while ($row = mysql_fetch_array($sql))
{
    if ($row['status'] == 0)
    {
        site_header();

        print("<div align='center'><span style='font-weight:bold;'>Email Confirm On Sign Up:</span></div>");
        print("<br /><br />");

        print("<table class='main' align='center' cellspacing='0' cellpadding='5' width='50%'>");
        print("<form method='post' action='con_email.php'>");

        print("<tr><td class='rowhead' align='center'><select name='action'><option value='0'>" . Off . "</option>");
        print("<option value='1'>" . On . "</option></select></td>");

        print("<td class='rowhead' align='center'><input type='submit' value='Okay' /></td></tr>");

        print("</form>");
        print("</table><br />");

        site_footer();
    }
    else
        site_header();

    print("<div align='center'><span style='font-weight:bold;'>Email Confirm On Sign Up:</span></div>");
    print("<br /><br />");

    print("<table class='main' align='center' cellspacing='0' cellpadding='5' width='50%'>");
    print("<form method='post' action='con_email.php'>");

    print("<tr><td class='rowhead' align='center'><select name='action'><option value='1'>" . On . "</option>");
    print("<option value='0'>" . Off . "</option></select></td>");

    print("<td class='rowhead' align='center'><input type='submit' value='Okay' /></td></tr>");

    print("</form>");
    print("</table>");

    site_footer();
}

?>