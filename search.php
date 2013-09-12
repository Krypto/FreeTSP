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
require_once(FUNC_DIR.'function_vfunctions.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_bbcode.php');

db_connect();
logged_in();

site_header("Search");

?>

<table class='main' border='0' width='100%' cellspacing='0' cellpadding='0'>
    <tr>
        <td class='embedded'>
            <form method='get' action='browse.php'>
                <p align='center'>
                    Search:
                    <input type='text' name='search' size='40' value='<?php echo htmlspecialchars($searchstr) ?>' />
                    in
                    <select name='cat'>
                        <option value='0'>(all types)</option>

                        <?php

                        $cats        = genrelist();
                        $catdropdown = "";

                        foreach ($cats
                                 AS
                                 $cat)
                        {
                            $catdropdown .= "<option value='".$cat["id"]."'";
                            $getcat = (isset($_GET["cat"]) ? $_GET["cat"] : '');

                            if ($cat["id"] == $getcat)

                            {
                                $catdropdown .= " selected='selected'";
                            }

                            $catdropdown .= ">".htmlspecialchars($cat["name"])."</option>\n";
                        }

                        $deadchkbox = "<input type='checkbox' name='incldead' value='1'";

                        if (isset($_GET["incldead"]))
                        {
                            $deadchkbox .= " checked='checked'";
                        }

                        $deadchkbox .= " /> including dead torrents\n";

                        $catdropdown

                        ?>

                    </select>

                    <?php echo $deadchkbox ?>

                    <input type='submit' class='btn' value='Search!' />
                </p>
            </form>
        </td>
    </tr>
</table>

<?php

site_footer();

?>