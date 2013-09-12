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

/////FreeTSP Shoutbox_Commands Spook/////

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');

db_connect();
logged_in();

if (get_user_class() < UC_MODERATOR)
{
    error_message("warn", "Warning", "Permission Denied.");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
    <meta name='generator' content='FreeTSP.info' />
    <meta name='MSSmartTagsPreventParsing' content='true' />
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <title>Staff ShoutBox Commands</title>
</head>

<body>

<script type='text/javascript'>

    function command(command, form, text)
    {
        window.opener.document.forms[form].elements[text].value = window.opener.document.forms[form].elements[text].value + command + ' ';
        window.opener.document.forms[form].elements[text].focus();
        window.close();
    }

</script>

<table width='100%' cellpadding='1' cellspacing='1'>
    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>empty</span>. To use type /EMPTY <br />[username here without the brackets]
        </td>
    </tr>
    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>
                <input type='text' size='20' value='/EMPTY' onclick="command('/EMPTY','shbox','shbox_text')" />
            </span>
        </td>
    </tr>

    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>gag</span>. To use type /GAG <br />[username here without the brackets]
        </td>
        <td align='center'>
            <span style='font-weight:bold;'>ungag</span>. To use type /UNGAG <br /> [username here without the brackets]
        </td>
    </tr>
    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>
                <input type='text' size='20' value='/GAG' onclick="command('/GAG','shbox','shbox_text')" />
            </span>
        </td>
        <td align='center'>
            <span style='font-weight:bold;'>
                <input type='text' size='20' value='/UNGAG' onclick="command('/UNGAG','shbox','shbox_text')" />
            </span>
        </td>
    </tr>

    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>warn</span>. To use type /WARN <br />[username here without the brackets]
        </td>
        <td align='center'>
            <span style='font-weight:bold;'>unwarn</span>. To use type /UNWARN <br />[username here without the brackets]
        </td>
    </tr>
    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>
                <input type='text' size='20' value='/WARN' onclick="command('/WARN','shbox','shbox_text')" />
            </span>
        </td>
        <td align='center'>
            <span style='font-weight:bold;'>
                <input type='text' size='20' value='/UNWARN' onclick="command('/UNWARN','shbox','shbox_text')" />
            </span>
        </td>
    </tr>

    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>disable</span>. To use type /DISABLE <br />[username here without the brackets]
        </td>
        <td align='center'>
            <span style='font-weight:bold;'>enable</span>. To use type /ENABLE <br />username here without the brackets]
        </td>
    </tr>
    <tr>
        <td align='center'>
            <span style='font-weight:bold;'>
                <input type='text' size='20' value='/DISABLE' onclick="command('/DISABLE','shbox','shbox_text')" />
            </span>
        </td>
        <td align='center'>
            <span style='font-weight:bold;'>
                <input type='text' size='20' value='/ENABLE' onclick="command('/ENABLE','shbox','shbox_text')" />
            </span>
        </td>
    </tr>
</table>
<br />

<div align='center'>
    <a class='altlink' href='javascript: window.close()'><span style='font-weight:bold;'>[ Close window ]</span></a>
</div>

</body>
</html>

<?php

die();

?>