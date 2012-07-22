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

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_vfunctions.php');
require_once(INCL_DIR.'function_user.php');

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
			<input type="hidden" name="item_number" value="<?php echo $CURUSER['username'] . '-' . $CURUSER['id']?>" />
			<input type="hidden" name="item_name" value="Donation from <?php echo $CURUSER['username'] . '-' . $CURUSER['id']?>" />
			<input type='hidden' name='no_note' value='1' />
			<input type='hidden' name='amount' value='5' />
			<input type='hidden' name='currency_code' value='USD' />
			<input type="hidden" name='tax' value='0' />
			<input type="hidden" name="email" value="<?php echo $CURUSER['email']?>" />
			<input type='image' src='<?php echo $image_dir?>donor/5.png' name='submit' alt='Make payments with PayPal - its fast, free and secure' title='Make payments with PayPal - its fast, free and secure' style='margin-top: 5px' />
			<input type="hidden" name="return" value="<?php echo $site_url ?>/donate.php" />
			<input type="hidden" name="cancel_return" value="<?php  $site_url ?>/donate.php" />
		</form>
	</div>

	<div class='thumbnail'>
		<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
			<input type='hidden' name='cmd' value='_xclick' />
			<input type='hidden' name='business' value='<?php echo $site_email?>' />
			<input type='hidden' name='item_name' value='<?php echo $site_name?>' />
			<input type="hidden" name="item_number" value="<?php echo $CURUSER['username'] . '-' . $CURUSER['id']?>" />
			<input type="hidden" name="item_name" value="Donation from <?php echo $CURUSER['username'] . '-' . $CURUSER['id']?>" />
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
			<input type="hidden" name="item_number" value="<?php echo $CURUSER['username'] . '-' . $CURUSER['id']?>" />
			<input type="hidden" name="item_name" value="Donation from <?php echo $CURUSER['username'] . '-' . $CURUSER['id']?>" />
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