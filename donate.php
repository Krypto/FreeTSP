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

db_connect();
site_header();

?>
<div align='center'>
    <span style='font-weight:bold;'>Click one of the buttons below if you wish to make a Donation!</span>

    <br /><br />

    <div id='wrapper'>
        <div class='thumbnail'>
            <form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
                <input type='hidden' name='cmd' value='_xclick' />
                <input type='hidden' name='business' value='<?php echo $site_email?>' />
                <input type='hidden' name='item_name' value='<?php echo $site_name?>' />
                <input type="hidden" name="item_number" value="<?php echo $CURUSER['username'].'-'.$CURUSER['id']?>" />
                <input type="hidden" name="item_name" value="Donation from <?php echo $CURUSER['username'].'-'.$CURUSER['id']?>" />
                <input type='hidden' name='no_note' value='1' />
                <input type='hidden' name='amount' value='5' />
                <input type='hidden' name='currency_code' value='USD' />
                <input type="hidden" name='tax' value='0' />
                <input type="hidden" name="email" value="<?php echo $CURUSER['email']?>" />
                <input type='image' src='<?php echo $image_dir?>donor/5.png' name='submit' alt='Make payments with PayPal - its fast, free and secure' title='Make payments with PayPal - its fast, free and secure' style='margin-top: 5px' />
                <input type="hidden" name="return" value="<?php echo $site_url ?>/donate.php" />
                <input type="hidden" name="cancel_return" value="<?php $site_url ?>/donate.php" />
            </form>
        </div>

        <div class='thumbnail'>
            <form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
                <input type='hidden' name='cmd' value='_xclick' />
                <input type='hidden' name='business' value='<?php echo $site_email?>' />
                <input type='hidden' name='item_name' value='<?php echo $site_name?>' />
                <input type="hidden" name="item_number" value="<?php echo $CURUSER['username'].'-'.$CURUSER['id']?>" />
                <input type="hidden" name="item_name" value="Donation from <?php echo $CURUSER['username'].'-'.$CURUSER['id']?>" />
                <input type='hidden' name='no_note' value='1' />
                <input type='hidden' name='amount' value='10' />
                <input type='hidden' name='currency_code' value='USD' />
                <input type="hidden" name='tax' value='0' />
                <input type="hidden" name="email" value="<?php echo $CURUSER['email']?>" />
                <input type='image' src='<?php echo $image_dir?>donor/10.png' name='submit' alt='Make payments with PayPal - its fast, free and secure' title='Make payments with PayPal - its fast, free and secure' style='margin-top: 5px' />
                <input type="hidden" name="return" value="<?php echo $site_url ?>/donate.php" />
                <input type="hidden" name="cancel_return" value="<?php echo $site_url ?>/donate.php" />
            </form>
        </div>

        <div class='thumbnail'>
            <form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
                <input type='hidden' name='cmd' value='_xclick' />
                <input type='hidden' name='business' value='<?php echo $site_email?>' />
                <input type='hidden' name='item_name' value='<?php echo $site_name?>' />
                <input type="hidden" name="item_number" value="<?php echo $CURUSER['username'].'-'.$CURUSER['id']?>" />
                <input type="hidden" name="item_name" value="Donation from <?php echo $CURUSER['username'].'-'.$CURUSER['id']?>" />
                <input type='hidden' name='no_note' value='1' />
                <input type='hidden' name='amount' value='15' />
                <input type='hidden' name='currency_code' value='USD' />
                <input type="hidden" name='tax' value='0' />
                <input type="hidden" name="email" value="<?php echo $CURUSER['email']?>" />
                <input type='image' src='<?php echo $image_dir?>donor/15.png' name='submit' alt='Make payments with PayPal - its fast, free and secure' title='Make payments with PayPal - its fast, free and secure' style='margin-top: 5px' />
                <input type="hidden" name="return" value="<?php echo $site_url ?>/donate.php" />
                <input type="hidden" name="cancel_return" value="<?php echo $site_url ?>/donate.php" />
            </form>
        </div>
        <br class='clearboth' />
    </div>

    <span style='font-weight:bold;'>After you have Donated -- make sure to <a href='sendmessage.php?receiver=1'>Send us</a> the <span style="color : #ff0000;">Transaction ID</span> so we can Credit your Account!</span><br /><br />
    
</div>

<?php

site_footer();

?>