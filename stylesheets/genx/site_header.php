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

        <link rel="stylesheet" href="stylesheets/genx/genx.css" type="text/css" />
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

    <!-- Start Header / Logo And Menu Section -->
    <table class='genx' align='center' border='0' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T1.1.png' width='21' height='23' alt='image' title='' />
            </td>

            <td class='genx' width='100%' height='23' style='background: url("stylesheets/genx/images/T1.2.png");'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T1.3.png' width='21' height='23' alt='image' title='' />
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T2.1.png' width='18' height='54' alt='image' title='' />
            </td>

            <td class='genx' width='100%' height='54' style='background: url("stylesheets/genx/images/T2.2.png");'>

                <table class='genx' border='0' align='center' width='100%' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td class='genx' width='32' height='54' colspan='2'>
                            <img src='stylesheets/genx/images/statut-membre.png' width='30' height='30' border='0' align='right' alt='Total Members' title='Total Members' />
                        </td>

                        <td class='genx' height='54'>
                            &nbsp;&nbsp;<font color='#FFFFFF'>Total Members:<strong> <?php echo $registered2;?></strong></font>
                        </td>

                        <td class='genx' width='32' height='54'>
                            <img src='stylesheets/genx/images/Graduation.png' width='30' height='30' border='0' alt='Members Online' title='Members Online' />
                        </td>

                        <td class='genx' height='54'>
                            &nbsp;&nbsp;<font color='#FFFFFF'>Members Online:<strong> <?php echo $numactive2;?></strong></font>
                        </td>

                        <td class='genx' width='32' height='54'>
                            <img src='stylesheets/genx/images/torrent-top.png' width='30' height='30' border='0' alt='Total Members' title='Total Members' />
                        </td>

                        <td class='genx' height='54'>
                            &nbsp;&nbsp;<font color='#FFFFFF'>Total Torrents:<strong> <?php echo $torrents2;?> </strong></font>
                        </td>

                        <td class='genx' width='32' height='54'>
                            <img src='stylesheets/genx/images/sources-top.png' width='30' height='30' border='0' alt='Total Posts' title='Total Posts' />
                        </td>

                        <td class='genx' height='54'>
                            &nbsp;&nbsp;<font color='#FFFFFF'>Total Posts:<strong> <?php echo $forumposts2;?> </strong></font>
                        </td>
                    </tr>
                </table>
            </td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T2.3.png' width='20' height='54' alt='image' title='' />
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T3.1.png' width='80' height='147' alt='image' title='' />
            </td>

            <td class='genx_logo'>
                <a href='index.php'><img src='<?php echo $image_dir?>logo.png' width='486' height='100' border='0' alt='<?php echo $site_name?>' title='<?php echo $site_name?>' style='vertical-align: middle;' /></a>
            </td>

            <td class='genx' align='right' width='100%' height='147' style='background: url("stylesheets/genx/images/T3.2.png");'>
                <script type="text/javascript">
                    var d=new Date();
                    document.write(d);
                </script><br />
            </td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T3.3.png' width='110' height='147' alt='image' title='' />
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.1a.png' width='20' height='25' alt='image' title='' />
            </td>

            <td class='genx' width='58%' height='25' style='background: url("stylesheets/genx/images/T4.2a.png");'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.3a.png' width='47' height='25' alt='image' title='' />
            </td>

            <td class='genx' width='400' height='25' style='background: url("stylesheets/genx/images/T4.4a.png");'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.5a.png' width='161' height='25' alt='image' title='' />
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.1b.png' width='20' height='49' alt='image' title='' />
            </td>

            <td class='genx' width='58%' height='49' style='background: url("stylesheets/genx/images/T4.2b.png");'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.3b.png' width='47' height='49' alt='image' title='' />
            </td>

            <!-- Start Torrent Activity -->
            <td class='genx' width='230' height='49' style='background: url("stylesheets/genx/images/T4.5b.png");'>
                <img src='stylesheets/genx/images/001_52.png' alt='' title='' />&nbsp;Up :- <font color='#4DDB4D'>&nbsp;<?php echo mksize($CURUSER["uploaded"])?></font><br />
                <img src='stylesheets/genx/images/001_51.png' alt='' title='' />&nbsp;Down :- <font color='#FF0000'>&nbsp;<?php echo mksize($CURUSER["downloaded"])?></font>
            </td>

            <td class='genx' width='170' height='49' style='background: url("stylesheets/genx/images/T4.5b.png");'>
                <img src='stylesheets/genx/images/001_53.png' width='11' height='11' alt='' title='' />&nbsp;Ratio :- <font color='lightblue'><?php echo $ratio ?></font><br />
                <img src='stylesheets/genx/images/001_52.png' width='11' height='10' alt='' title='' />&nbsp;Conectable :- <?php echo $connectable; ?>
            </td>

            <!-- Finish Torrent Activity -->
            <td class='genx' align='right'>
                <a href='index.php'><img src='stylesheets/genx/images/T4.6b.png' width='161' height='49' alt='image' title='' /></a>
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.1c.png' width='20' height='40' alt='image' title='' />
            </td>

            <td class='genx' width='58%' height='40' style='background: url("stylesheets/genx/images/T4.2c.png");'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.3c.png' width='47' height='40' alt='image' title='' />
            </td>

            <!-- Start Status Bar Info -->
            <td class='genx' width='220' height='40' style='background: url("stylesheets/genx/images/T4.4c.png");'>
                <img src='stylesheets/genx/images/001_54.png' width='11' height='11' alt='' title='' />&nbsp;Name :-&nbsp;<?php echo $nameuser; ?><br />
                <img src='stylesheets/genx/images/001_61.png' width='11' height='11' alt='' title='' />&nbsp;Class :-<?php echo $usrclass; ?>
            </td>
            <td class='genx' width='180' height='40' style='background: url("stylesheets/genx/images/T4.4c.png");'>
                <img src='stylesheets/genx/images/001_58.png' width='11' alt='' title='' />&nbsp;Messages :-&nbsp;<a href='messages.php'><?php echo $inbox ?></a><br />
                <img src='stylesheets/genx/images/001_15.png' width='11' height='11' alt='' title='' />&nbsp;Donor :-&nbsp;<?php echo $donor; ?>
            </td>

            <!-- Finish Status Bar Info -->
            <td class='genx' align='right'>
                <a href='index.php'><img src='stylesheets/genx/images/T4.5c.png' width='100' height='40' alt='image' title='' /></a>
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.1d.png' width='20' height='40' alt='image' title='' />
            </td>

            <td class='genx' width='58%' height='40' style='background: url("stylesheets/genx/images/T4.2d.png");'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.3d.png' width='47' height='40' alt='image' title='' />
            </td>

            <!-- Start Status Bar Info -->
            <td class='genx' width='200' height='40' style='background: url("stylesheets/genx/images/T4.4d.png");'><?php echo $rep ?> :-&nbsp;<?php echo $reputation ?>
            </td>
            <td class='genx' width='200' height='40' style='background: url("stylesheets/genx/images/T4.4d.png");'>
                <img src='stylesheets/genx/images/invite1.png' width='12' height='12' alt='' title='' />&nbsp;Invites :-&nbsp;<?php echo $invites ?>
            </td>

            <!-- Finish Status Bar Info -->
            <td class='genx' align='right'>
                <a href='index.php'><img src='stylesheets/genx/images/T4.5d.png' width='100' height='40' alt='image' title='' /></a>
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.1e.png' width='20' height='30' alt='image' title='' />
            </td>

            <td class='genx' width='58%' height='30' style='background: url("stylesheets/genx/images/T4.2e.png");'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T4.3e.png' width='47' height='30' alt='image' title='' />
            </td>

            <td class='genx' width='400' height='30' style='background: url("stylesheets/genx/images/T4.4e.png");'></td>
            <td class='genx' align='right'>
                <a href='index.php'><img src='stylesheets/genx/images/T4.5e.png' width='100' height='30' alt='image' title='' /></a>
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T5.1.png' width='21' height='52' alt='image' title='' />
            </td>

            <td class='genx' width='100%' height='52' style='background: url("stylesheets/genx/images/T5.2.png");'>

                <!-- Start Site Links Silver Buttons Align Right -->
                <table class='genx' border='0' align='right' cellpadding='0' cellspacing='0'>
                    <tr>
                    <?php if (get_user_class() >= UC_MODERATOR) { ?>
                        <td class='genx' align='right'>
                            <img src='stylesheets/genx/images/B1.1.png' width='16' height='41' alt='image' title='' />
                        </td>

                        <td class='genx' align='center' width='150' height='16' style='background: url("stylesheets/genx/images/B1.2a.png");'>
                            <a href='controlpanel.php'><font color='#000000'>Admin CP</font></a>
                        </td>
                        <td class='genx' align='right'>
                            <img src='stylesheets/genx/images/B1.3.png' width='16' height='41' alt='image' title='' />
                        </td>
                    <?php } ?>

                        <td class='genx' align='right'>
                            <img src='stylesheets/genx/images/B1.1.png' width='16' height='41' alt='image' title='' />
                        </td>
                        <td class='genx' align='center' width='150' height='16' style='background: url("stylesheets/genx/images/B1.2a.png");'>
                            <a href='usercp.php'><font color='#000000'>My Profile</font></a>
                        </td>
                        <td class='genx' align='right'>
                            <img src='stylesheets/genx/images/B1.3.png' width='16' height='41' alt='image' title='' />
                        </td>

                        <td class='genx' align='right'>
                            <img src='stylesheets/genx/images/B1.1.png' width='16' height='41' alt='image' title='' />
                        </td>

                        <td class='genx' align='center' width='150' height='16' style='background: url("stylesheets/genx/images/B1.2a.png");'>
                            <a href='mytorrents.php'><font color='#000000'>My Uploads</font></a>
                        </td>

                        <td class='genx' align='right'>
                            <img src='stylesheets/genx/images/B1.3.png' width='16' height='41' alt='image' title='' />
                        </td>
                    </tr>
                </table>

            <!-- Finish Site Links Silver Buttons Align Right -->
            </td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T5.3.png' width='21' height='52' alt='image' title='' />
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T6.1.png' width='21' height='43' alt='image' title='' />
            </td>

            <td class='genx' style='background: url("stylesheets/genx/images/T6.2.png");' width='100%' height='43'></td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T6.3.png' width='20' height='43' alt='image' title='' />
            </td>
        </tr>
    </table>

    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx'>
                <img src='stylesheets/genx/images/T7.1.png' width='18' height='52' alt='image' title='' />
            </td>

            <td class='genx' align='center' width='100%' height='52' style='background: url("stylesheets/genx/images/T7.2.png");'>
                <a href='index.php'><font color='#000000'>HOME</font></a><?php echo $menuspace ?>
                <a href='browse.php'><font color='#000000'>TORRENTS</font></a><?php echo $menuspace ?>
                <a href='upload.php'><font color='#000000'>UPLOAD</font></a><?php echo $menuspace ?>
                <a href='requests.php'><font color='#000000'>REQUESTS</font></a><?php echo $menuspace ?>
                <a href='offers.php'><font color='#000000'>OFFERS</font></a><?php echo $menuspace ?>
                <a href='credits.php'><font color='#000000'>CREDITS</font></a><?php echo $menuspace ?>
                <a href='forums.php'><font color='#000000'>FORUMS</font></a><?php echo $menuspace ?>
                <a href='faq.php'><font color='#000000'>FAQ</font></a><?php echo $menuspace ?>
                <a href='rules.php'><font color='#000000'>RULES</font></a><?php echo $menuspace ?>
                <a href='topten.php'><font color='#000000'>TOP 10</font></a><?php echo $menuspace ?>
                <a href='helpdesk.php'><font color='#000000'>HELP</font></a><?php echo $menuspace ?>
                <a href='staff.php'><font color='#000000'>STAFF</font></a><?php echo $menuspace ?>
                <a href='donate.php'><font color='#000000'>DONATE</font></a>
            </td>
            <td class='genx'>
                <img src='stylesheets/genx/images/T7.3.png' width='17' height='52' alt='image' title='' />
            </td>
        </tr>
    </table>

    <!-- Finish Header / Logo And Menu Section -->
    <table class='genx' border='0' align='center' width='1100' cellpadding='0' cellspacing='0'>
        <tr>
            <td class='genx' valign='top'>
                <table class='genx' border='0' align='center' width='1077' cellspacing='0' cellpadding='0'>
                    <tr>
                        <!-- important class -->
                        <td class='mainbody' valign='top'>
                        <!-- important class -->

                            <table class='genx' border='0' align='center' width='100%' cellspacing='0' cellpadding='0'>
                                <tr>
                                    <td class='genx' valign='top'><br /><br />
                                        <!-- Main Center Content Start -->
                                        <?php $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>
                                        <table class='genx' border='0' align='center' width='950' cellspacing='0' cellpadding='10'>
                                            <tr>
                                                <td class='genx' align='center' style='padding-top: 20px; padding-bottom: 20px'>