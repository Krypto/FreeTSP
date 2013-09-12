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

        <link rel="stylesheet" href="stylesheets/darkx/darkx.css" type="text/css" />
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

    <div class="statusbar_container">
        <table class='mainouter' width='100%' border='0' cellspacing='0' cellpadding='10'>
            <?php print StatusBar();

            if ($CURUSER['menu'] == "1")
            {
                Dropmenu();
            }

            if ($CURUSER['menu'] == "2")
            {
                Stdmenu();
            }

            ?>
            <table class='std1' align='center' width='100%' cellspacing='0' cellpadding='0' style='background: transparent'>
                <tr>
                    <td class='std1'>
                        <img src="stylesheets/darkx/images/menu1.png" width="15" height="11" align="top" alt='' title='' />
                    </td>

                    <td class='std1' style='background: url("stylesheets/darkx/images/menu2.png");' width='100%' height='11'></td>
                    <td class='std1' align='right'>
                        <img src="stylesheets/darkx/images/menu3.png" width="15" height="11" align="top" alt='' title='' />
                    </td>
                </tr>
        </table>
    </div>

    <table class='logo' width='100%' cellspacing='0' cellpadding='0' style='background: transparent'>
        <table width='100%' cellspacing='0' cellpadding='0' style='background: transparent'>
            <tr>
                <td class='clear'>
                    <div align='center'>
                        <a href='index.php'><img src='<?php echo $image_dir?>logo.png' width='486' height='100' border='0' alt='<?php echo $site_name?>' title='<?php echo $site_name?>' style='vertical-align: middle;' /></a>
                    </div>
                </td>
                <td class='clear' width='49%' align='right'>
                    <a href='donate.php'><img src='<?php echo $image_dir?>donor/donate.png' width='170' height='58' border='0' alt='Make a Donation' title='Make a Donation' /></a>
                </td>
            </tr>
        </table>
    </table>
    <br />

    <?php $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>

    <table class='std1' width='100%' align='center' cellspacing='0' cellpadding='0' style='background: transparent'>
        <tr>
            <td class='std1'>
                <img src="stylesheets/darkx/images/top1.png" width="15" height="35" align="top" alt='' title='' />
            </td>

            <td class='std1' style='background: url("stylesheets/darkx/images/top2.png");' width='100%' height='35'></td>
            <td class='std1' align='right'>
                <img src="stylesheets/darkx/images/top3.png" width="15" height="35" align="top" alt='' title='' />
            </td>
        </tr>
    </table>

    <table class='mainouter' align='center' width='100%' border='0' cellspacing='0' cellpadding='10'>
        <tr>
            <td class='std1' align='center' style='padding-left: 1%; padding-right: 1%'>