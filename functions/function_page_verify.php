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

/*
    Session So That Repeated Access Of This Page Cannot Happen Without The Calling Script.
    You Use The Create Function With The Sending Script, And The Check Function With The
    Receiving Script...
    You Need To Pass The Value Of $task From The Calling Script To The Receiving Script. While
    This May Appear Dangerous, It Still Only Allows A One Shot At The Receiving Script, Which
    Effectively Stops Flooding.
    Page Verify By Retro
*/

class page_verify
{
    function page_verify ()
    {
        if (session_id() == '')
        {
            session_start();
        }
    }

    function create ($task_name = 'Default')
    {
        global $CURUSER;

        $_SESSION['Task_Time']       = time();
        $_SESSION['Task']            = md5('user_id:'.$CURUSER['id'].'::taskname-'.$task_name.'::'.$_SESSION['Task_Time']);
        $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
    }

    function check ($task_name = 'Default')
    {
        global $CURUSER, $site_url;

        $returl = (isset($CURUSER) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : $site_url."/login.php");
        $returl = str_replace('&', '&', $returl);

        if (isset($_SESSION['HTTP_USER_AGENT']) && $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'])
        {
            error_message("error", "Error", "Please resubmit the form. <a href='".$returl."'>Click HERE</a>", false);
        }

        if ($_SESSION['Task'] != md5('user_id:'.$CURUSER['id'].'::taskname-'.$task_name.'::'.$_SESSION['Task_Time']))
        {
            error_message("error", "Error", "Please resubmit the form. <a href='".$returl."'>Click HERE</a>", false);
        }
        $this->create();
    }
}

?>