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
require_once(FUNC_DIR.'function_user.php');
require_once(FUNC_DIR.'function_vfunctions.php');
require_once(FUNC_DIR.'function_page_verify.php');

$newpage = new page_verify();
$newpage->create('_login_');

db_connect();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?php echo $site_name ?></title>

    <!-- Style Sheet General -->
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/960.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />

    <!-- jQuery -->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/password.js"></script>

    <!-- Custom js -->
    <script type="text/javascript" src="js/custom.min.js"></script>

</head>

<body>
<table width='100%' cellspacing='0' cellpadding='0' style='background: transparent'>
    <tr>
        <td>
            <div align='center'>
                <a href='index.php'><img src='<?php echo $image_dir?>logo.png' width='486' height='100' border='0' alt='<?php echo $site_name?>' title='<?php echo $site_name?>' style='vertical-align: middle;' /></a>
            </div>
        </td>
    </tr>
</table>

<!-- Start Container -->
<div class="container_12">

    <div class="loading radius-left">
        <span>Loading...</span>
    </div>

    <!-- Start Forms -->
    <div class="grid_4 push_4">

        <div class="box radius">

            <form action="takelogin.php" id="login" class="active">
                <?php
                    $value = array('...','...','...','...','...','...');
                    $value[rand(1,count($value)-1)] = 'X';
                ?>

                <h1>Log in</h1>

                <fieldset class="radius">

                    <p><?php echo $maxloginattempts ?> Failed Logins in a row will result in Banning your IP.<br /><br /> You have&nbsp;<?php echo remaining();?> Login Attempt(s).
                    </p>

                    <p>
                        <label class="required" for="username">Username</label>
                        <br />
                        <input type="text" name="username" id="username" />
                    </p>

                    <p>
                        <label class="required" for="password">Password</label>
                        <br />
                        <input type="password" name="password" id="password" />
                    </p>

                    <p>
                        Now Click The Button Marked <strong>X</strong>
                    </p>

                    <p>
                        <?php
                        for ($i=0; $i < count($value); $i++)
                        {
                            echo("<input type='submit' class='btn' name='submitme' value='".$value[$i]."' />");
                        }
                        ?>
                    </p>

                    <br />
                    <p><a href="#" class="link" rel="registration">Create your Account Here!</a></p>
                    <p><a href="#" class="link" rel="lost_password">Forgot your Password?</a></p>
                    <p><a href="loginhelp.php">Login Help</a></p>

                </fieldset>

            </form>

            <form method='post' action='recover.php' id='lost_password'>

                <h1>Forgot Password</h1>

                <fieldset class="radius">

                    <p>
                        <label class="required" for="email">Registered Email</label>
                        <br />
                        <input type="text" name="email" id="email" />
                    </p>

                    <input type="submit" class="button button-orange float_right" value="Send" />
                    <br />
                    <p><a href="#" class="link" rel="login">Log in Here!</a></p>
                    <p><a href="#" class="link" rel="registration">Create your Account Here!</a></p>

                </fieldset>

            </form>

            <form action="takesignup.php" id="registration">

                <h1>Registration</h1>

                <?php

                $res = sql_query("SELECT COUNT(*)
                                    FROM users") or sqlerr(__FILE__, __LINE__);

                $arr = mysql_fetch_row($res);

                if ($arr[0] >= $max_users_then_invite)
                {
                    display_message("info", "<span style='color : #ff0000;'>Sorry</span>", "<span style='color : #00ff00;'>The Current User Account Limit (".number_format($max_users).") Has Been Reached. Inactive Accounts Are Pruned All The Time, Please Check Back Again Later...</span>");

                    echo('<p><a href="#" class="link" rel="invited_user"><font color="#FFFFFF">I Have An Invite</font></a></p>');
                }

                else

                { ?>

                <fieldset class="radius">

                    <p>
                        <label class="required" for="registration_username">Desired Username</label>
                        <br />
                        <input type="text" name="wantusername" id="registration_username" />
                    </p>

                    <p>
                        <label class="required" for="registration_password">Pick a Password</label>
                        <br /><img src="<?php echo $image_dir?>password/tooshort.gif" width="240" height="27" border="0" id="strength" alt="" title="" /><br />
                        <input type="password" name="wantpassword" id="registration_password" maxlength="15" onkeyup="updatestrength( this.value );" />
                    </p>

                    <p>
                        <label class="required" for="registration_password_repeat">Enter Password Again</label>
                        <br />
                        <input type="password" name="passagain" id="registration_password_repeat" />
                    </p>

                    <p>
                        <label class="required" for="registration_email">Email Address</label>
                        <br />
                        <input type="text" name="email" id="registration_email" />
                    </p>

                    <p>
                        <input type="checkbox" name="rulesverify" value="yes" /> I have read the Site Rules.<br />
                        <input type="checkbox" name="faqverify" value="yes" /> I agree to read the FAQ.<br />
                        <input type="checkbox" name="ageverify" value="yes" /> I am at least 13 years old.<br /><br />

                        <input type="submit" class="button button-orange float_right" value="Create Account" /><br />
                    </p>

                    <p><a href="#" class="link" rel="login">Log in Here!</a></p>

                </fieldset>



            <?php } ?>
            </form>


            <form method="post" action="take_invite_signup.php" id="invited_user">

                <h1>Invited Membership</h1>

                <fieldset class="radius">

                    <p>
                        <label class="required" for="invited_user_username">Desired Username</label>
                        <br />
                        <input type="text" name="wantusername" id="invited_user_username" />
                    </p>

                    <p>
                        <label class="required" for="invited_user_password">Pick a Password</label>
                        <br /><img src="<?php echo $image_dir?>password/tooshort.gif" width="240" height="27" border="0"  id="strength1" alt="" title="" /><br />
                        <input type="password" name="wantpassword" id="invited_user_password" maxlength="15" onkeyup="updatestrength( this.value );" />
                    </p>

                    <p>
                        <label class="required" for="invited_user_password_repeat">Enter Password Again</label>
                        <br />
                        <input type="password" name="passagain" id="invited_user_password_repeat" />
                    </p>

                    <p>
                        <label class="required" for="invited_user_invited_user">Enter Invite Code</label>
                        <br />
                        <input type="password" name="invite" id="invited_user_invited_user" />
                    </p>

                    <p>
                        <label class="required" for="invited_user_email">Email Address</label>
                        <br />
                        <input type="text" name="email" id="invited_user_email" />
                    </p>

                    <p>
                        <input type="checkbox" name="rulesverify" value="yes" /> I have read the Site Rules.<br />
                        <input type="checkbox" name="faqverify" value="yes" /> I agree to read the FAQ.<br />
                        <input type="checkbox" name="ageverify" value="yes" /> I am at least 13 years old.<br /><br />

                        <input type="submit" class="button button-orange float_right" value="Sign up! (PRESS ONLY ONCE)" /><br />
                    </p>

                </fieldset>

            </form>

        </div>

    </div>
    <!-- End Forms -->

</div>
<!-- End Container -->

</body>
</html>