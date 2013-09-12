<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <!-- ******************************************************* -->
        <!-- *       This website is powered by FreeTSP v1.0       * -->
        <!-- *              Download and support at:               * -->
        <!-- *              http://www.freetsp.info                * -->
        <!-- ******************************************************* -->
        <title><?php echo $title ?></title>
        <meta name="title" content="FreeTSP" />
        <meta name="description"
              content="The FreeTSP idea was conceived by a bunch of like minded folk who wanted to create a BitTorrent source that was fundamentally different and was easy for new comers to get a site up and running and was also easy to learn" />
        <meta name="keywords"
              content="freetsp, free, ftsp, bittorrent, simple, kiss, tracker, code, free torrent source project, free torrent downloader, source code torrent, torrent programs" />
        <meta name="author" content="Krypto, Fireknight" />
        <meta name="owner" content="Krypto" />
        <meta name="copyright" content="(c) 2010" />

        <link rel="stylesheet" href="stylesheets/default/default.css" type="text/css" />
        <link rel="stylesheet" href="css/notification.css" type="text/css" media="screen" />

        <script type='text/javascript' src='js/jquery.js'></script>
        <script type="text/javascript" src="js/java_klappe.js"></script>
        <!-- Uncomment If You Wish To Ise Image-Resize Instead Of LightBox -->
        <!--<link type='text/css' rel='stylesheet' href='css/resize.css'  />
        <script type='text/javascript' src='js/core-resize.js'></script> -->
        <!-- COmment Out The Two Lines Below And The LightBox Section If You Wish To Use Image-Resize Instead Of LightBox -->
        <script type='text/javascript' src='js/jquery.lightbox-0.5.min.js'></script>
        <link rel='stylesheet' type='text/css' href='css/jquery.lightbox-0.5.css' media='screen' />

        <script type='text/javascript'>
            function popUp(URL)
            {
                day = new Date();
                id = day.getTime();
                eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=380,left = 340,top = 280');");
            }
        </script>

        <!-- Comment Out To Use Core-Resize Instead -->
        <script type='text/javascript'>
            /*<![CDATA[*/
            //$(function () {
            $('document').ready(function () {
            $('a[rel=\"lightbox\"]').lightBox(); //-- Select All Links That Contains Lightbox In The Attribute rel --//
            });
            /*]]>*/
        </script>
        <!-- Comment Out To Use Core-Resize Instead -->

    </head>

    <body>

    <table width='100%' cellspacing='0' cellpadding='0' style='background: transparent'>
        <tr>
            <td class='clear'>
                <div align='center'>
                    <a href='index.php'><img src='<?php echo $image_dir?>logo.png' width='486' height='100' border='0'
                                             alt='<?php echo $site_name?>' title='<?php echo $site_name?>'
                                             style='vertical-align: middle;' /></a>
                </div>
            </td>
            <td class='clear' align='right' width='49%'>
                <a href='donate.php'><img src='<?php echo $image_dir?>donor/donate.png' width='170' height='58'
                                          border='0' alt='Make a Donation' title='Make a Donation' /></a>
            </td>
        </tr>
    </table>

    <?php $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>

    <!-- MENU -->
    <?php
    if ($CURUSER['menu'] == "2")
    {
        require_once(ROOT_DIR.'stdmenu.php');
    }

    elseif ($CURUSER['menu'] == "1")
    {
        require_once(ROOT_DIR.'menu.php');
    }
    ?>
    <!-- MENU -->

    <table class='mainouter' border='1' width='100%' cellspacing='0' cellpadding='10'>
        <tr>
            <td class='outer' align='center' style='padding-top: 20px; padding-bottom: 20px'>