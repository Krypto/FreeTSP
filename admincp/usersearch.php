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

         <link rel="stylesheet" type="text/css" href="/errors/error-style.css" />
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

//-- 0 - No Debug; 1 - Show And Run SQL Query; 2 - Show SQL Query Only --//
$DEBUG_MODE = 0;

site_header("Administrative User Search", false);

echo "<h1>Administrative User Search</h1>";

if (isset($_GET['intrust']))
{
    print("<table width='65%' border='0' align='center'>
            <tr>
                <td class='embedded' bgcolor='#F5F4EA'>
                    <div align='left'>
                        Fields left blank will be ignored.<br />
                        Wildcards * and ? may be used in Name, Email and Comments.<br />
                        as well as multiple values separated by spaces<br />
                        (e.g. 'wyz Max*' in Name will list both users named
                        'wyz' and those whose names start by 'Max'. Similarly '~' can be used for
                        negation, e.g. '~alfiest' in comments will restrict the search to users
                        that do not have 'alfiest' in their comments).<br /><br />
                        The Ratio field accepts 'Inf' and '---' besides the usual numeric values.<br /><br />
                        The subnet mask may be entered either in dotted decimal or CIDR notation
                        (e.g. 255.255.255.0 is the same as /24).<br /><br />
                        Uploaded and Downloaded should be entered in GB.<br /><br />
                        For search parameters with multiple text fields the second will be
                        ignored unless relevant for the type of search chosen. <br /><br />
                        'Active only' restricts the search to users currently leeching or seeding,
                        'Disabled IPs' to those whose IPs also show up in disabled accounts.<br /><br />
                        The 'p' columns in the results show partial stats, that is, those
                        of the torrents in progress. <br /><br />
                        The History column lists the number of forum posts and torrent comments,
                        respectively, as well as linking to the history page.
                    </div>
                </td>
            </tr>
        </table><br /><br />");
}
else
{
    print("<p align='center'><a href='controlpanel.php?fileaction=3&amp;action=usersearch&amp;intrust=1' class='btn'>Instructions</a>");
    print("&nbsp;-&nbsp;<a href='controlpanel.php?fileaction=3' class='btn'>Reset</a></p>");
}

$highlight = " bgcolor='#BBAF9B'";

?>

<form method='post' action='controlpanel.php?fileaction=3&amp;action=usersearch'>
<table border='1' align='center' width='80%' cellspacing='0' cellpadding='5'>
<tr>
    <!-- Search Name -->
    <td class='colhead' align="center"><label for='n'>Name:</label></td>
    <td class='rowhead'<?php echo $_POST['n'] ? $highlight : ""?>>
        <input type="text" name="n" id="n" size='35' value="<?php echo $_POST['n']?>" />
    </td>
    <!-- Search Ratio -->
    <td class='colhead' align="center" ><label for='rt'>Ratio:</label></td>
    <td class='rowhead' <?php echo $_POST['r'] ? $highlight : ""?> >
        <select name="rt" id='rt'>
            <?php
            $options = array("equal",
                             "above",
                             "below",
                             "between");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['rt'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
        <input type="text" name="r" size="5" maxlength="4" value="<?php echo $_POST['r']?>" />
        <input type="text" name="r2" size="5" maxlength="4" value="<?php echo $_POST['r2']?>" />
    </td>
    <!-- Search Status -->
    <td class='colhead' align="center"><label for='st'>Member Status:</label></td>
    <td class='rowhead' <?php echo $_POST['st'] ? $highlight : ""?> >
        <select name="st" id='st'>
            <?php
            $options = array("(any)",
                             "confirmed",
                             "pending");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['st'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <!-- Search Email -->
    <td class='colhead' align="center"><label for='em'>Email:</label></td>
    <td <?php echo $_POST['em'] ? $highlight : ""?> >
        <input type="text" name="em" id="em" size="35" value="<?php echo $_POST['em']?>" />
    </td>
    <!-- Search IP -->
    <td class='colhead' align='center'><label for='ip'>IP:</label></td>
    <td class='rowhead' <?php echo $_POST['ip'] ? $highlight : ""?> >
        <input type="text" name="ip" id="ip" maxlength="17" value="<?php echo $_POST['ip']?>" />
    </td>
    <!-- Search Enabled / Disabled -->
    <td class='colhead' align="center"><label for='as'>Account Status:</label></td>
    <td class='rowhead' <?php echo $_POST['as'] ? $highlight : ""?> >
        <select name="as" id='as'>
            <?php
            $options = array("(any)",
                             "enabled",
                             "disabled");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['as'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <!-- Search Comment -->
    <td class='colhead' align='center'><label for='co'>Comment:</label></td>
    <td class='rowhead' <?php echo $_POST['co'] ? $highlight : ""?>>
        <input type="text" name="co" id="co" size="35" value="<?php echo $_POST['co']?>" />
    </td>
    <!-- Search Mask -->
    <td class='colhead' align='center'><label for='ma'>Mask:</label></td>
    <td class='rowhead' <?php echo $_POST['ma'] ? $highlight : ""?>>
        <input type="text" name="ma" id="ma" maxlength="17" value="<?php echo $_POST['ma']?>" />
    </td>
    <!-- Search Class -->
    <td class='colhead' align='center'><label for='c'>Class:</label></td>
    <td class='rowhead' <?php echo ($_POST['c'] && $_POST['c'] != 1) ? $highlight : ""?>>
        <select name="c" id='c'>
            <option value='1'>(any)</option>
            <?php
            $class = $_POST['c'];

            if (!is_valid_id($class))
            {
                $class = '';
            }

            for ($i = 2;;
                 ++$i)
            {
                if ($c = get_user_class_name($i - 2))
                {
                    print("<option value='".$i.($class && $class == $i ? "' selected='selected" : "")."'>$c</option>");
                }
                else
                {
                    break;
                }
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <!-- Search Date Joined -->
    <td class='colhead' align='center'><label for='dj'>Joined:</label></td>
    <td class='rowhead' <?php echo $_POST['d'] ? $highlight : ""?>>
        <select name="dt" id='dj'>
            <?php
            $options = array("on",
                             "before",
                             "after",
                             "between");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['dt'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
        <input type="text" name="d" size="12" maxlength="10" value="<?php echo $_POST['d']?>" />
        <input type="text" name="d2" size="12" maxlength="10" value="<?php echo $_POST['d2']?>" />
    </td>
    <!-- Search Ammount Uploaded -->
    <td class='colhead' align='center'><label for='ult'>Uploaded:</label></td>
    <td class='rowhead' <?php echo $_POST['ul'] ? $highlight : ""?>><select name="ult" id="ult">
        <?php
        $options = array("equal",
                         "above",
                         "below",
                         "between");

        for ($i = 0;
             $i < count($options);
             $i++)
        {
            echo "<option value='$i' ".(($_POST['ult'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
        }
        ?>
    </select>
        <input type="text" name="ul" id="ul" size="8" maxlength="7" value="<?php echo $_POST['ul']?>" />
        <input type="text" name="ul2" id="ul2" size="8" maxlength="7" value="<?php echo $_POST['ul2']?>" />
    </td>
    <!-- Search If Donor -->
    <td class='colhead' align='center'><label for='do'>Donor:</label></td>
    <td class='rowhead' <?php echo $_POST['do'] ? $highlight : ""?>>
        <select name="do" id='do'>
            <?php
            $options = array("(any)",
                             "Yes",
                             "No");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['do'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <!-- Search Last Seen -->
    <td class='colhead' align='center'><label for='lst'>Last Seen:</label></td>
    <td class='rowhead' <?php echo $_POST['ls'] ? $highlight : ""?>>
        <select name="lst" id='lst'>
            <?php
            $options = array("on",
                             "before",
                             "after",
                             "between");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['lst'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
        <input type="text" name="ls" size="12" maxlength="10" value="<?php echo $_POST['ls']?>" />
        <input type="text" name="ls2" size="12" maxlength="10" value="<?php echo $_POST['ls2']?>" />
    </td>
    <!-- Search Ammount Downloaded -->
    <td class='colhead' align='center'><label for='dlt'>Downloaded:</label></td>
    <td class='rowhead' <?php echo $_POST['dl'] ? $highlight : ""?>>
        <select name="dlt" id="dlt">
            <?php
            $options = array("equal",
                             "above",
                             "below",
                             "between");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['dlt'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
        <input type="text" name="dl" id="dl" size="8" maxlength="7" value="<?php echo $_POST['dl']?>" />
        <input type="text" name="dl2" id="dl2" size="8" maxlength="7" value="<?php echo $_POST['dl2']?>" />
    </td>
    <!-- Search Warned -->
    <td class='colhead' align='center'><label for='w'>Warned:</label></td>
    <td class='rowhead' <?php echo $_POST['w'] ? $highlight : ""?>>
        <select name="w" id='w'>
            <?php
            $options = array("(any)",
                             "Yes",
                             "No");

            for ($i = 0;
                 $i < count($options);
                 $i++)
            {
                echo "<option value='$i' ".(($_POST['w'] == "$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td class='rowhead'></td>
    <td class='std'></td>
    <!-- SEarch Active Only -->
    <td class='colhead' align='center'><label for='ac'>Active Only:</label></td>
    <td class='rowhead' <?php echo $_POST['ac'] ? $highlight : ""?>>
        <input type="checkbox" name="ac" id='ac' value="1" <?php echo ($_POST['ac']) ? "checked='checked'" : "" ?> />
    </td>
    <!-- Search Disabled IP -->
    <td class='colhead' align='center'><label for='dip'>Disabled IP:</label></td>
    <td class='rowhead' <?php echo $_POST['dip'] ? $highlight : ""?>>
        <input type="checkbox" name="dip" id='dip' value="1" <?php echo ($_POST['dip']) ? "checked='checked'" : "" ?> />
    </td>
</tr>
<tr>
    <td class='rowhead' colspan='6' align='center'>
        <input type='submit' class='btn' name='submit' />
    </td>
</tr>
</table>
<br /><br />
</form>

<?php

//-- Validates Date In The Form [yy]yy-mm-dd; --//
//-- Returns Date If Valid, 0 Otherwise. --//
function mkdate ($date)
{
    if (strpos($date, '-'))
    {
        $a = explode('-', $date);
    }
    elseif (strpos($date, '/'))
    {
        $a = explode('/', $date);
    }
    else
    {
        return 0;
    }

    for ($i = 0;
         $i < 3;
         $i++)

    {
        if (!is_numeric($a[$i]))
        {
            return 0;
        }
    }
    if (checkdate($a[1], $a[2], $a[0]))
    {
        return date("Y-m-d", mktime(0, 0, 0, $a[1], $a[2], $a[0]));
    }
    else
    {
        return 0;
    }
}

//-- Ratio As A String --//
function ratios ($up, $down, $color = true)
{
    if ($down > 0)
    {
        $r = number_format($up / $down, 2);

        if ($color)
        {
            $r = "<span style='color : ".get_ratio_color($r)."'>$r</span>";
        }
    }
    else
    {
        if ($up > 0)
        {
            $r = "Inf.";
        }
        else
        {
            $r = "---";
        }
    }
    return $r;
}

//-- Checks For The Usual Wildcards *, ? Plus MySQL Ones --//
function haswildcard ($text)
{
    if (strpos($text, '*') === false && strpos($text, '?') === false && strpos($text, '%') === false && strpos($text, '_') === false)
    {
        return false;
    }
    else
    {
        return true;
    }
}

if (count($_POST) > 0 && !$_POST['h'])
{
    //-- Name --//
    $names = explode(' ', trim($_POST['n']));

    if ($names[0] !== "")
    {
        foreach ($names
                 AS
                 $name)
        {
            if (substr($name, 0, 1) == '~')
            {
                if ($name == '~')
                {
                    continue;
                }
                $names_exc[] = substr($name, 1);
            }
            else
            {
                $names_inc[] = $name;
            }
        }

        if (is_array($names_inc))
        {
            $where_is .= isset($where_is) ? " AND (" : "(";

            foreach ($names_inc
                     AS
                     $name)
            {
                if (!haswildcard($name))
                {
                    $name_is .= (isset($name_is) ? " OR " : "")."u.username = ".sqlesc($name);
                }
                else
                {
                    $name = str_replace(array('?',
                                              '*'), array('_',
                                                          '%'), $name);
                    $name_is .= (isset($name_is) ? " OR " : "")."u.username LIKE ".sqlesc($name);
                }
            }
            $where_is .= $name_is.")";
            unset($name_is);
        }

        if (is_array($names_exc))
        {
            $where_is .= isset($where_is) ? " AND NOT (" : " NOT (";

            foreach ($names_exc
                     AS
                     $name)
            {
                if (!haswildcard($name))
                {
                    $name_is .= (isset($name_is) ? " OR " : "")."u.username = ".sqlesc($name);
                }
                else
                {
                    $name = str_replace(array('?',
                                              '*'), array('_',
                                                          '%'), $name);
                    $name_is .= (isset($name_is) ? " OR " : "")."u.username LIKE ".sqlesc($name);
                }
            }
            $where_is .= $name_is.")";
        }
        $q .= ($q ? "&amp;" : "")."n=".urlencode(trim($_POST['n']));
    }

    //-- Email --//
    $emaila = explode(' ', trim($_POST['em']));

    if ($emaila[0] !== "")
    {
        $where_is .= isset($where_is) ? " AND (" : "(";

        foreach ($emaila
                 AS
                 $email)
        {
            if (strpos($email, '*') === false && strpos($email, '?') === false && strpos($email, '%') === false)
            {
                if (validemail($email) !== 1)
                {
                    error_message("error", "Error", "Bad Email.");
                }
                $email_is .= (isset($email_is) ? " OR " : "")." u.email =".sqlesc($email);
            }
            else
            {
                $sql_email = str_replace(array('?',
                                               '*'), array('_',
                                                           '%'), $email);
                $email_is .= (isset($email_is) ? " OR " : "")."u.email LIKE ".sqlesc($sql_email);
            }
        }
        $where_is .= $email_is.")";
        $q        .= ($q ? "&amp;" : "")."em=".urlencode(trim($_POST['em']));
    }

    //-- Class --//
    //-- NB: The c Parameter Is Passed As Two Units Above The Real One --//
    $class = $_POST['c'] - 2;

    if (is_valid_id($class + 1))
    {
        $where_is .= (isset($where_is) ? " AND " : "")."u.class=$class";
        $q        .= ($q ? "&amp;" : "")."c=".($class + 2);
    }

    //-- IP --//
    $ip = trim($_POST['ip']);

    if ($ip)
    {
        $regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";

        if (!preg_match($regex, $ip))
        {
            error_message("error", "Error", "Bad IP.");
        }

        $mask = trim($_POST['ma']);

        if ($mask == "" || $mask == "255.255.255.255")
        {
            $where_is .= (isset($where_is) ? " AND " : "")."u.ip = '$ip'";
        }
        else
        {
            if (substr($mask, 0, 1) == "/")
            {
                $n = substr($mask, 1, strlen($mask) - 1);

                if (!is_numeric($n) OR $n < 0 or $n > 32)
                {
                    error_message("error", "Error", "Bad Subnet Mask.");
                }
                else
                {
                    $mask = long2ip(pow(2, 32) - pow(2, 32 - $n));
                }
            }
            elseif (!preg_match($regex, $mask))
            {
                error_message("error", "Error", "Bad subnet mask.");
            }

            $where_is .= (isset($where_is) ? " AND " : "")."INET_ATON(u.ip) & INET_ATON('$mask') = INET_ATON('$ip') & INET_ATON('$mask')";

            $q .= ($q ? "&amp;" : "")."ma=$mask";
        }
        $q .= ($q ? "&amp;" : "")."ip=$ip";
    }

    //-- Ratio --//
    $ratio = trim($_POST['r']);

    if ($ratio)
    {
        if ($ratio == '---')
        {
            $ratio2   = "";
            $where_is .= isset($where_is) ? " AND " : "";
            $where_is .= " u.uploaded = 0 AND u.downloaded = 0";
        }
        elseif (strtolower(substr($ratio, 0, 3)) == 'inf')
        {
            $ratio2   = "";
            $where_is .= isset($where_is) ? " AND " : "";
            $where_is .= " u.uploaded > 0 AND u.downloaded = 0";
        }
        else
        {
            if (!is_numeric($ratio) || $ratio < 0)
            {
                error_message("error", "Error", "Bad Ratio.");
            }

            $where_is  .= isset($where_is) ? " AND " : "";
            $where_is  .= " (u.uploaded/u.downloaded)";
            $ratiotype = $_POST['rt'];
            $q         .= ($q ? "&amp;" : "")."rt=$ratiotype";

            if ($ratiotype == "3")
            {
                $ratio2 = trim($_POST['r2']);

                if (!$ratio2)
                {
                    error_message("error", "Error", "Two Ratios are Needed for This type of Search.");
                }

                if (!is_numeric($ratio2) OR $ratio2 < $ratio)
                {
                    error_message("error", "Error", "Bad Second Ratio.");
                }

                $where_is .= " BETWEEN $ratio AND $ratio2";
                $q        .= ($q ? "&amp;" : "")."r2=$ratio2";

            }
            elseif ($ratiotype == "2")
            {
                $where_is .= " < $ratio";
            }
            elseif ($ratiotype == "1")
            {
                $where_is .= " > $ratio";
            }
            else
            {
                $where_is .= " BETWEEN ($ratio - 0.004) AND ($ratio + 0.004)";
            }
        }
        $q .= ($q ? "&amp;" : "")."r=$ratio";
    }

    //-- Comment --//
    $comments = explode(' ', trim($_POST['co']));

    if ($comments[0] !== "")
    {
        foreach ($comments
                 AS
                 $comment)
        {
            if (substr($comment, 0, 1) == '~')
            {
                if ($comment == '~')
                {
                    continue;
                }
                $comments_exc[] = substr($comment, 1);
            }
            else
            {
                $comments_inc[] = $comment;
            }
        }

        if (is_array($comments_inc))
        {
            $where_is .= isset($where_is) ? " AND (" : "(";

            foreach ($comments_inc
                     AS
                     $comment)
            {
                if (!haswildcard($comment))
                {
                    $comment_is .= (isset($comment_is) ? " OR " : "")."u.modcomment LIKE ".sqlesc("%".$comment."%");
                }
                else
                {
                    $comment = str_replace(array('?',
                                                 '*'), array('_',
                                                             '%'), $comment);
                    $comment_is .= (isset($comment_is) ? " OR " : "")."u.modcomment LIKE ".sqlesc($comment);
                }
            }
            $where_is .= $comment_is.")";

            unset($comment_is);
        }

        if (is_array($comments_exc))
        {
            $where_is .= isset($where_is) ? " AND NOT (" : " NOT (";

            foreach ($comments_exc
                     AS
                     $comment)
            {
                if (!haswildcard($comment))
                {
                    $comment_is .= (isset($comment_is) ? " OR " : "")."u.modcomment LIKE ".sqlesc("%".$comment."%");
                }
                else
                {
                    $comment = str_replace(array('?',
                                                 '*'), array('_',
                                                             '%'), $comment);
                    $comment_is .= (isset($comment_is) ? " OR " : "")."u.modcomment LIKE ".sqlesc($comment);
                }
            }
            $where_is .= $comment_is.")";
        }
        $q .= ($q ? "&amp;" : "")."co=".urlencode(trim($_POST['co']));
    }

    $unit = 1073741824; // 1GB

    //-- Uploaded --//
    $ul = trim($_POST['ul']);

    if ($ul)
    {
        if (!is_numeric($ul) || $ul < 0)
        {
            error_message("error", "Error", "Bad Uploaded Amount.");
        }

        $where_is .= isset($where_is) ? " AND " : "";
        $where_is .= " u.uploaded ";
        $ultype   = $_POST['ult'];
        $q        .= ($q ? "&amp;" : "")."ult=$ultype";

        if ($ultype == "3")
        {
            $ul2 = trim($_POST['ul2']);

            if (!$ul2)
            {
                error_message("error", "Error", "Two Uploaded amounts needed for this type of search.");
            }

            if (!is_numeric($ul2) OR $ul2 < $ul)
            {
                error_message("error", "Error", "Bad Second Uploaded Amount.");
            }

            $where_is .= " BETWEEN ".$ul * $unit." AND ".$ul2 * $unit;
            $q        .= ($q ? "&amp;" : "")."ul2=$ul2";
        }

        elseif ($ultype == "2")
        {
            $where_is .= " < ".$ul * $unit;
        }
        elseif ($ultype == "1")
        {
            $where_is .= " >".$ul * $unit;
        }
        else
        {
            $where_is .= " BETWEEN ".($ul - 0.004) * $unit." AND ".($ul + 0.004) * $unit;
        }
        $q .= ($q ? "&amp;" : "")."ul=$ul";
    }

    //-- Downloaded --//
    $dl = trim($_POST['dl']);

    if ($dl)
    {
        if (!is_numeric($dl) || $dl < 0)
        {
            error_message("error", "Error", "Bad Downloaded Amount.");
        }

        $where_is .= isset($where_is) ? " AND " : "";
        $where_is .= " u.downloaded ";
        $dltype   = $_POST['dlt'];
        $q        .= ($q ? "&amp;" : "")."dlt=$dltype";

        if ($dltype == "3")
        {
            $dl2 = trim($_POST['dl2']);

            if (!$dl2)
            {
                error_message("error", "Error", "Two Downloaded Amounts Needed for this Type of Search.");
            }

            if (!is_numeric($dl2) OR $dl2 < $dl)
            {
                error_message("error", "Error", "Bad Second Downloaded Amount.");

            }

            $where_is .= " BETWEEN ".$dl * $unit." AND ".$dl2 * $unit;
            $q        .= ($q ? "&amp;" : "")."dl2=$dl2";
        }
        elseif ($dltype == "2")
        {
            $where_is .= " < ".$dl * $unit;
        }
        elseif ($dltype == "1")
        {
            $where_is .= " > ".$dl * $unit;
        }
        else
        {
            $where_is .= " BETWEEN ".($dl - 0.004) * $unit." AND ".($dl + 0.004) * $unit;
        }
        $q .= ($q ? "&amp;" : "")."dl=$dl";
    }

    //-- Date Joined --//
    $date = trim($_POST['d']);

    if ($date)
    {
        if (!$date = mkdate($date))
        {
            error_message("error", "Error", "Invalid Date.");
        }

        $q        .= ($q ? "&amp;" : "")."d=$date";
        $datetype = $_POST['dt'];
        $q        .= ($q ? "&amp;" : "")."dt=$datetype";

        if ($datetype == "0")
            //-- For MySQL 4.1.1 Or Above Use Instead --//
            //-- $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')"; --//
        {
            $where_is .= (isset($where_is) ? " AND " : "")."(UNIX_TIMESTAMP(added) - UNIX_TIMESTAMP('$date')) BETWEEN 0 AND 86400";
        }
        else
        {
            $where_is .= (isset($where_is) ? " AND " : "")."u.added ";

            if ($datetype == "3")
            {
                $date2 = mkdate(trim($_POST['d2']));

                if ($date2)
                {
                    if (!$date = mkdate($date))
                    {
                        error_message("error", "Error", "Invalid Date.");
                    }

                    $q .= ($q ? "&amp;" : "")."d2=$date2";
                    $where_is .= " BETWEEN '$date' AND '$date2'";
                }
                else
                {
                    error_message("error", "Error", "Two Dates Needed for this Type of Search.");
                }
            }
            elseif ($datetype == "1")
            {
                $where_is .= "< '$date'";
            }
            elseif ($datetype == "2")
            {
                $where_is .= "> '$date'";
            }
        }
    }

    //-- Date Last Seen --//
    $last = trim($_POST['ls']);

    if ($last)
    {
        if (!$last = mkdate($last))
        {
            error_message("error", "Error", "Invalid Date.");
        }

        $q        .= ($q ? "&amp;" : "")."ls=$last";
        $lasttype = $_POST['lst'];
        $q        .= ($q ? "&amp;" : "")."lst=$lasttype";

        if ($lasttype == "0")

            //-- For MySQL 4.1.1 Or Above Use Instead --//
            //-- $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')"; --//
        {
            $where_is .= (isset($where_is) ? " AND " : "")."(UNIX_TIMESTAMP(last_access) - UNIX_TIMESTAMP('$last')) BETWEEN 0 AND 86400";
        }
        else
        {
            $where_is .= (isset($where_is) ? " AND " : "")."u.last_access ";
            if ($lasttype == "3")
            {
                $last2 = mkdate(trim($_POST['ls2']));
                if ($last2)
                {
                    $where_is .= " BETWEEN '$last' AND '$last2'";
                    $q        .= ($q ? "&amp;" : "")."ls2=$last2";
                }
                else
                {
                    error_message("error", "Error", "The Second Date is Not Valid.");
                }
            }
            elseif ($lasttype == "1")
            {
                $where_is .= "< '$last'";
            }
            elseif ($lasttype == "2")
            {
                $where_is .= "> '$last'";
            }
        }
    }

    //-- Status --//
    $status = $_POST['st'];

    if ($status)
    {
        $where_is .= ((isset($where_is)) ? " AND " : "");

        if ($status == "1")
        {
            $where_is .= "u.status = 'confirmed'";
        }
        else
        {
            $where_is .= "u.status = 'pending'";
        }
        $q .= ($q ? "&amp;" : "")."st=$status";
    }

    //-- Account Status --//
    $accountstatus = $_POST['as'];

    if ($accountstatus)
    {
        $where_is .= (isset($where_is)) ? " AND " : "";

        if ($accountstatus == "1")
        {
            $where_is .= " u.enabled = 'yes'";
        }
        else
        {
            $where_is .= " u.enabled = 'no'";
        }
        $q .= ($q ? "&amp;" : "")."as=$accountstatus";
    }

    //-- Donor --//
    $donor = $_POST['do'];

    if ($donor)
    {
        $where_is .= (isset($where_is)) ? " AND " : "";

        if ($donor == 1)
        {
            $where_is .= " u.donor = 'yes'";
        }
        else
        {
            $where_is .= " u.donor = 'no'";
        }
        $q .= ($q ? "&amp;" : "")."do=$donor";
    }

    //-- Warned --//
    $warned = $_POST['w'];

    if ($warned)
    {
        $where_is .= (isset($where_is)) ? " AND " : "";

        if ($warned == 1)
        {
            $where_is .= " u.warned = 'yes'";
        }
        else
        {
            $where_is .= " u.warned = 'no'";
        }
        $q .= ($q ? "&amp;" : "")."w=$warned";
    }

    //-- Disabled IP --//
    $disabled = $_POST['dip'];

    if ($disabled)
    {
        $distinct = "DISTINCT ";
        $join_is  .= " LEFT JOIN users AS u2 ON u.ip = u2.ip";
        $where_is .= ((isset($where_is)) ? " AND " : "")." u2.enabled = 'no'";
        $q        .= ($q ? "&amp;" : "")."dip=$disabled";
    }

    //-- Active --//
    $active = $_POST['ac'];

    if ($active == "1")
    {
        $distinct = "DISTINCT ";
        $join_is  .= " LEFT JOIN peers AS p ON u.id = p.userid";
        $q        .= ($q ? "&amp;" : "")."ac=$active";
    }

    $from_is  = "users AS u".$join_is;
    $distinct = isset($distinct) ? $distinct : "";

    $queryc    = "SELECT COUNT(".$distinct."u.id) FROM ".$from_is.(($where_is == "") ? "" : " WHERE $where_is ");
    $querypm   = "FROM ".$from_is.(($where_is == "") ? " " : " WHERE $where_is ");

    $announcement_query = 'SELECT u.id FROM '.$from_is. (($where_is == "")? " WHERE 1=1":" WHERE $where_is");

    $select_is = "u.id, u.username, u.email, u.status, u.added, u.last_access, u.ip, u.class, u.uploaded, u.downloaded, u.donor, u.modcomment, u.enabled, u.warned";

    $query1 = "SELECT ".$distinct." ".$select_is." ".$querypm;

    //-- Start Temporary --//
    if ($DEBUG_MODE > 0)
    {
        error_message("info", "Count Query", $queryc);
        echo "<br /><br />";

        error_message("info", "Search Query", $query);
        echo "<br /><br />";

        error_message("info", "URL ", $q);

        echo "<br /><br />";

        error_message("info", "Announce Query", $announcement_query);
        echo "<br /><br />";

        if ($DEBUG_MODE == 2)
        {
            die();
        }
        echo "<br /><br />";
    }
    //-- End Temporary --//

    $res     = sql_query($queryc) or sqlerr();
    $arr     = mysql_fetch_row($res);
    $count   = $arr[0];
    $q       = isset($q) ? ($q."&amp;") : "";
    $perpage = 30;

    list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"]." ? ".$q);

    $query1 .= $pager['limit'];

    $res = sql_query($query1) or sqlerr();

    if (mysql_num_rows($res) == 0)
    {
        error_message("info", "Info", "No User was found.");
    }
    else
    {
        if ($count > $perpage)
        {
            echo $pagertop;
        }
        echo "<table border='1' align='center' width='80%' cellspacing='0' cellpadding='5'>";
        echo "<tr>
                    <td class='colhead' align='left'>Name</td>
                    <td class='colhead' align='left'>Ratio</td>
                    <td class='colhead' align='left'>IP</td>
                    <td class='colhead' align='left'>Email</td>
                    <td class='colhead' align='left'>Joined:</td>
                    <td class='colhead' align='left'>Last seen:</td>
                    <td class='colhead' align='left'>Status</td>
                    <td class='colhead' align='left'>Enabled</td>
                    <td class='colhead'>pR</td>
                    <td class='colhead'>pUL</td>
                    <td class='colhead'>pDL</td>
                    <td class='colhead'>History</td>
                </tr>";

        while ($user = mysql_fetch_assoc($res))
        {
            if ($user['added'] == '0000-00-00 00:00:00')
            {
                $user['added'] = '---';
            }

            if ($user['last_access'] == '0000-00-00 00:00:00')
            {
                $user['last_access'] = '---';
            }

            if ($user['ip'])
            {
                $nip = ip2long($user['ip']);
                $auxres = sql_query("SELECT COUNT(*)
                                        FROM bans
                                        WHERE '$nip' >= first AND '$nip' <= last") or sqlerr(__FILE__, __LINE__);

                $array = mysql_fetch_row($auxres);

                if ($array[0] == 0)
                {
                    $ipstr = $user['ip'];
                }
                else
                {
                    $ipstr = "<a href='testip.php?ip=".$user['ip']."'><span style='color : #ff0000;'><span style='font-weight:bold;'>".$user['ip']."</span></span></a>";
                }
            }
            else
            {
                $ipstr = "---";
            }

            $auxres = sql_query("SELECT SUM(uploaded) AS pul, SUM(downloaded) AS pdl
                                    FROM peers
                                    WHERE userid = ".$user['id']) or sqlerr(__FILE__, __LINE__);

            $array = mysql_fetch_assoc($auxres);

            $pul = $array['pul'];
            $pdl = $array['pdl'];

            $auxres = sql_query("SELECT COUNT(DISTINCT p.id)
                                    FROM posts AS p
                                    LEFT JOIN topics AS t ON p.topicid = t.id
                                    LEFT JOIN forums AS f ON t.forumid = f.id
                                    WHERE p.userid = ".$user['id']."
                                    AND f.minclassread <= ".$CURUSER['class']) or sqlerr(__FILE__, __LINE__);

            $n       = mysql_fetch_row($auxres);
            $n_posts = $n[0];

            $auxres = sql_query("SELECT COUNT(id)
                                    FROM comments
                                    WHERE user = ".$user['id']) or sqlerr(__FILE__, __LINE__);

            $n          = mysql_fetch_row($auxres);
            $n_comments = $n[0];
            $ids        .= $user['id'].':';

            echo "<tr>
                    <td><span style='font-weight:bold;'><a href='userdetails.php?id=".$user['id']."'>".$user['username']."</a></span>".get_user_icons($user)."</td>
                    <td class='rowhead'>".ratios($user['uploaded'], $user['downloaded'])."</td>
                    <td class='rowhead'>".$ipstr."</td>
                    <td class='rowhead'>".$user['email']."</td>
                    <td class='rowhead' align='center'>".$user['added']."</td>
                    <td class='rowhead' align='center'>".$user['last_access']."</td>
                    <td class='rowhead' align='center'>".$user['status']."</td>
                    <td class='rowhead' align='center'>".$user['enabled']."</td>
                    <td class='rowhead' align='center'>".ratios($pul, $pdl)."</td>
                    <td class='rowhead' align='right'>".mksize($pul)."</td>
                    <td class='rowhead' align='right'>".mksize($pdl)."</td>
                    <td class='rowhead' align='center'>".($n_posts ? "<a href='userhistory.php?action=viewposts&amp;id=".$user['id']."'>$n_posts</a>" : $n_posts)."|".($n_comments ? "<a href='userhistory.php?action=viewcomments&amp;id=".$user['id']."'>$n_comments</a>" : $n_comments)."</td>
                </tr>";
        }
        echo "</table>";

        if ($count > $perpage)
        {
            echo "$pagerbottom";
        }
    }
/*    ?>
  <br /><br />
    <form method='post' action='sendmessage.php'>
      <table border="1" cellpadding="5" cellspacing="0">
        <tr>
          <td>
            <div align="center">
              <!--<input name="pmees" type="hidden" value="<?php echo $querypm?>" size=10>-->
              <input name="pmees" type="hidden" value="<?php echo htmlentities(rtrim($ids, ':'))?>" />
              <input name="PM" type="submit" value="PM" class='btn' />
              <input name="n_pms" type="hidden" value="<?php echo htmlentities($count)?>" size='10' />
            </div></td>
        </tr>
      </table>
    </form>
<?php
*/
?>
    <form method='post' action='new_announcement.php'>
        <table border='0' cellpadding='5' cellspacing='10'>
            <tr>
                <td class='bottom'>
                    <input type='hidden' name='n_pms' value='<?php print($count);?>' />
                    <input type='hidden' name='ann_query' value='<?php print($announcement_query);?>' />
                    <input type='hidden' name='ann_hash' value ='<?php print(hashit($announcement_query, $count));?>' />
                    <input type='submit' class='btn' value='Create New Announcement' />
                </td>
            </tr>
        </table>
    </form>
<?php

    echo("<p>$pagemenu<br />$browsemenu</p>");
}

site_footer();

die;

?>