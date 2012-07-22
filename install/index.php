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
*--   This program is free software; you can redistribute it and/or modify    --*
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
*--------               Developed By: Krypto, Fireknight                --------*
*-------------------------------------------------------------------------------*
*-----------------       First Release Date August 2010      -------------------*
*-----------                 http://www.freetsp.info                 -----------*
*------                    2010 FreeTSP Development Team                  ------*
*-------------------------------------------------------------------------------*
*------           Credit To CoLdFuSiOn For TheTBDEv Installer             ------*
*------           Moddified To Work With FreeTSP By Fireknight            ------*
*-------------------------------------------------------------------------------*
*/
error_reporting  (E_ERROR | E_WARNING | E_PARSE);
//set_magic_quotes_runtime(0);

define('INSTALLER_ROOT_PATH', './');
define('FTSP_ROOT_PATH', '../');
define('CACHE_PATH', FTSP_ROOT_PATH);
define('REQ_PHP_VER', '2.10.1');
define('REQ_MYSQL_VER', '2.10.3');
define('FreeTSP_REV', 'FreeTSP v1.0');

$installer = new installer;

class installer
{
	var $htmlout 	= "";
	var $VARS 		= array();

	function installer()
	{
	    $this->VARS = array_merge( $_GET, $_POST);

	    if (file_exists(INSTALLER_ROOT_PATH.'install.lock'))
	    {
			$this->install_error("This Installer is Locked!
			<br />You cannot Install unless you Delete the 'install.lock' file");
			exit();
	    }

		switch($this->VARS['progress'])
		{
		    case '1':
				$this->do_step_one();
			break;

		    case '2':
				$this->do_step_two();
			break;

		    case '3':
				$this->do_step_three();
			break;

		    case '4':
				$this->do_step_four();
			break;

			case 'end':
				$this->do_end();
			break;

			default:
				$this->do_start();
			break;
		}
	}

	function do_start()
	{
		$this->stdhead('Welcome');

		$this->htmlout .= "<div class='box_content'>
								<p>Before we go any further, please ensure that all the files have been uploaded, and that the file 'function_config.php' <br />
								   ( You will find this in the functions folder.) <br />
								   Has suitable Permissions to allow this script to write to it ( 0777 is sufficient for all servers ).
								</p>
								<br /><br />
								<h3>".FreeTSP_REV." requires the following software installed to function at maximum quality.<br />
								    phpMyAdmin - ".REQ_PHP_VER." or better.<br />
								    MYSQL - ".REQ_MYSQL_VER." Data Base or better.<br /></h3>
								<br /><br />
								   You will also need the following information:
								<ul>
								   <li>Your mySQL database name</li>
								   <li>Your mySQL username</li>
								   <li>Your mySQL password</li>
								   <li>Your mySQL host address (usually localhost)</li>
								</ul>
								<br />
								    Once you have clicked on the proceed button,
								    you will be taken to the next page.<br />
								    Where you will be required to enter information regarding your server details.<br />
								    The Installer needs this infomation to Install your tracker.
								<br /><br />
								<strong>TAKE NOTICE:- USING THIS INSTALLER WILL DELETE ANY CURRENT FreeTSP DATABASE AND OVERWRITE ANY FUNCTION_CONFIG.PHP FILE</strong>
								   ";

		$warnings   = array();

		$checkfiles = array(
								INSTALLER_ROOT_PATH."sql",
								FTSP_ROOT_PATH ."functions/function_config.php"
							);

		$writeable  = array(
				            	FTSP_ROOT_PATH."functions/function_config.php",
				            	FTSP_ROOT_PATH."torrents",
					      		FTSP_ROOT_PATH."cache",
					      		FTSP_ROOT_PATH."cache/last24"
	    					);

		foreach ($checkfiles as $cf)
		{
			if (!file_exists($cf))
			{
				$warnings[] = "Cannot locate the file '$cf'.";
			}
		}

		foreach ($writeable as $cf)
		{
			if (!is_writeable($cf))
			{
				$warnings[] = "Cannot write to the file '$cf'. Please CHMOD to 0777.";
			}
		}

		$phpversion = phpversion();

		if ($phpversion < REQ_PHP_VER)
		{
			$warnings[] = "<strong>FreeTSP Tracker requires PHP Version ".REQ_PHP_VER." or better.</strong>";
		}

		if (!function_exists('get_cfg_var'))
		{
			$warnings[] = "<strong>Your PHP installation isn't sufficient to run FreeTSP Tracker.</strong>";
		}

		if (function_exists('ini_get') AND @ini_get("safe_mode"))
		{
			$warnings[] = "<strong>FreeTSP Tracker won't run when safe_mode is on.</strong>";
		}

		if( function_exists('gd_info' ))
		{
	    	$gd	= gd_info();
	    	$fail	= true;

	    	if ($gd["GD Version"])
	    	{
	      		preg_match("/.*?([\d\.]+).*?/", $gd["GD Version"], $matches);

	      		if( $matches[1] )
	      		{
	        		$gdversions	= version_compare('2.0', $matches[1], '<=');

	        		if(!$gdversions)
	        		{
	          			$fail = false;
	        		}
	      		}
	    	}

	    	!$fail ? $warnings[] = "FreeTSP requires GD library version 2. The version on your server is'{$gd['GD Version']}'.  Find the upgrade here <a href='http://us.php.net/manual/en/image.setup.php'>libgd library</a>." : false;
		}

		$ext = get_loaded_extensions();

		if(!in_array('mysql', $ext))
		{
	    	$warnings[] = "<strong>Your server doesn't appear to have a MySQL library, you will need this before you can continue.</strong>";
		}

		if (count($warnings) > 0)
		{
			$err_string = implode("<br /><br />", $warnings);

			$this->htmlout .= "<br /><br />
								<div class='error-box' style='width: 500px;'>
									<strong>Warning!
									The following errors must be rectified before continuing!</strong>
									<br /><br />
									$err_string
								</div>";
		}
		else
		{
			$this->htmlout .= "<br /><br /><div class='proceed-btn-div'><a href='index.php?progress=1'><span class='btn'>CONTINUE</span></a></div>";
		}

		$this->htmlout .= "</div>";

		$this->htmlout();
	}

	function do_step_one()
	{
		$this->stdhead('Set Up form');

		$this->htmlout .= "
							<div class='box_content'>
								<form action='index.php' method='post'>
							<div>
								<input type='hidden' name='progress' value='2' />
							</div>

							<h2>Your Server Environment</h2>";

		$this->htmlout .= "
		<p>This section requires you to enter your SQL information.<br />
		   If in doubt, please check with your webhost before asking for support.<br />
		   You may choose to enter an existing database name.<br />
		   If you do not have an existing database, you can create one from here.</p>

		<legend><strong>MySQL Settings</strong></legend>

		<fieldset>
	    	<legend><strong>MySQL Host</strong></legend>
	    	<input type='text' name='mysql_host' value='' />
	    	(localhost is usually sufficient)
	 	</fieldset>

		<fieldset>
			<legend><strong>MySQL Database Name</strong></legend>
			<input type='text' name='mysql_db' value='' />
		</fieldset>

		<fieldset>
			<legend><strong>SQL Username</strong></legend>
			<input type='text' name='mysql_user' value='' />
		</fieldset>

		<fieldset>
			<legend><strong>SQL Password</strong></legend>
			<input type='text' name='mysql_pass' value='' />
		</fieldset>

		<legend><strong>General Settings</strong></legend>

		<fieldset>
			<legend><strong>Site URL</strong></legend>
			<input type='text' name='site_url' value='http://' />
			( Example - http://www.yoursite.com )
		</fieldset>

		<fieldset>
			<legend><strong>Announce URL</strong></legend>
			<input type='text' name='announce_url' value='http://' />
			( No ending slash - Example - http://www.yoursite.com/announce.php )
		</fieldset>

		<fieldset>
			<legend><strong>Site Name</strong></legend>
			<input type='text' name='site_name' value='' />
		</fieldset>

		<div class='proceed-btn-div'>

		<input class='btn' type='submit' value='CONTINUE' /></div>

		</form>
		</div>";

		$this->htmlout();

	}

	function do_step_two()
	{
		$in = array('mysql_host','mysql_db','mysql_user', 'mysql_pass', 'site_url', 'announce_url', 'site_name');

		foreach($in as $out)
		{
			if ($this->VARS[ $out ] == "")
			{
				$this->install_error("You must complete all of the form");
			}
		}

		if (!@mysql_connect($this->VARS['mysql_host'], $this->VARS['mysql_user'], $this->VARS['mysql_pass']))
	    {
	      $this->install_error("Connection error:<br /><br />[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
	    }
	    //mysql_select_db($FTSP['mysql_db']) or die('dbconn: mysql_select_db: ' . mysql_error());
	    //mysql_set_charset('utf8');

		if(!mysql_select_db($this->VARS['mysql_db']))
		{
	    	if(!mysql_query("CREATE DATABASE {$this->VARS['mysql_db']} DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci"))
	    	{
	      		$this->install_error("Unable to create database");
	      		exit();
	    	}

	    	mysql_select_db($this->VARS['mysql_db']);

	  }
	  else
	  {
	    mysql_select_db($this->VARS['mysql_db']);
	  }

		require_once(INSTALLER_ROOT_PATH.'sql/mysql_tables.php');
		require_once(INSTALLER_ROOT_PATH.'sql/mysql_inserts.php');

		foreach($TABLE as $q)
		{
		   preg_match("/CREATE TABLE (\S+) \(/", $q, $match);

		   if ($match[1])
		   {
			   mysql_query("DROP TABLE {$match[1]}");
		   }

		   if (!mysql_query($q))
		   {
			   $this->install_error($q."<br /><br />".mysql_error());
		   }
		}

		foreach ($INSERT as $q)
		{
			if (! mysql_query($q))
		    {
		      $this->install_error($q."<br /><br />".mysql_error());
		    }
	  	}


		mysql_query("UPDATE config SET mysql_host=('".$_POST['mysql_host']."'),mysql_db=('".$_POST['mysql_db']."'),mysql_user=('".$_POST['mysql_user']."'),mysql_pass=('".$_POST['mysql_pass']."'),domain_url=('".$_POST['site_url']."'),announce_url=('".$_POST['announce_url']."'),site_name=('".$_POST['site_name']."')");

		$this->stdhead('Database Success!');

		$this->htmlout .= "
		<div class='box_content'>

			<h2>Database Success</h2>

			<strong>Your database has been installed!</strong>
			<br /><br />
			The installation process is almost complete.
			<br />
			The next step will configure the tracker settings.
			<br /><br />

			<form action='index.php' method='post'>
				<div>
					<input type='hidden' name='progress' value='3' />
					<input type='hidden' name='mysql_host' value='{$this->VARS['mysql_host']}' />
					<input type='hidden' name='mysql_db' value='{$this->VARS['mysql_db']}' />
					<input type='hidden' name='mysql_user' value='{$this->VARS['mysql_user']}' />
					<input type='hidden' name='mysql_pass' value='{$this->VARS['mysql_pass']}' />
					<input type='hidden' name='site_url' value='{$this->VARS['site_url']}' />
					<input type='hidden' name='announce_url' value='{$this->VARS['announce_url']}' />
					<input type='hidden' name='site_name' value='{$this->VARS['site_name']}' />
				</div>
				<div class='proceed-btn-div'>
				<input class='btn' type='submit' value='CONTINUE' /></div>
			</form>
		</div>";

		$this->htmlout();
	}

	function do_step_three()
	{
		$this->stdhead('Config Set Up form');

		$this->htmlout .= "
		<div class='box_content'>

		<form action='index.php' method='post'>
		<div>
		<input type='hidden' name='progress' value='4' />
		</div>

		<h2>Setting up your Config file</h2>";

		$this->htmlout .= "
		<p>This section requires you to enter your all information. If in doubt, please check with your webhost before asking for support. Please note: Any settings you enter here will overwrite any settings in your function_config.php file!</p>

		<fieldset>
	    <legend><strong>MySQL Settings</strong></legend>

	    <div class='form-field'>
		    <label>MySQL Host</label>
		    <input type='text' name='mysql_host' value='{$this->VARS['mysql_host']}' /><br />
	    </div>

	    <div class='form-field'>
		    <label>MySQL Database Name</label>
			<input type='text' name='mysql_db' value='{$this->VARS['mysql_db']}' /><br />
	    </div>

		<div class='form-field'>
		    <label>SQL Username</label>
			<input type='text' name='mysql_user' value='{$this->VARS['mysql_user']}' /><br />
	    </div>

	    <div class='form-field'>
		    <label>SQL Password</label>
			<input type='text' name='mysql_pass' value='{$this->VARS['mysql_pass']}' /><br />
		</div>
		</fieldset>

		<fieldset>
		<legend><strong>General Tracker Settings</strong></legend>

		<div class='form-field'>
		    <label>Base URL</label>
			<input type='text' name='site_url' value='{$this->VARS['site_url']}' />
			<br /><span class='form-field-info'>Check that this setting is correct, as it was automatic!</span>
		</div>

		<div class='form-field'>
		    <label>Announce URL</label>
			<input type='text' name='announce_url' value='{$this->VARS['announce_url']}' />
			<br /><span class='form-field-info'>Check that this setting is correct, as it was automatic!</span>
		</div>

		<div class='form-field'>
		    <label>Site Name</label>
			<input type='text' name='site_name' value='{$this->VARS['site_name']}' />
		</div>

		</fieldset>

		<div class='proceed-btn-div'>

		<input class='btn' type='submit' value='CONTINUE' /></div>

		</form>
		</div>";

		$this->htmlout();

	}

	function do_step_four()
	{
		$DB = "";

		$NEW_INFO = array();

		$in = array('mysql_host','mysql_db','mysql_user', 'mysql_pass','site_url','announce_url','site_name');
		//print_r($this->VARS); exit;
		foreach($in as $out)
		{
			if ($this->VARS[ $out ] == "")
			{
				$this->install_error("You must complete all of the form.");
			}
		}

		// open config_dist.php
		$conf_string = file_get_contents('./config_dist.php');

		$placeholders = array('<#mysql_host#>', '<#mysql_db#>', '<#mysql_user#>', '<#mysql_pass#>', '<#announce_url#>', '<#site_url#>', '<#site_name#>');

		$replacements = array($this->VARS['mysql_host'], $this->VARS['mysql_db'], $this->VARS['mysql_user'], $this->VARS['mysql_pass'], $this->VARS['announce_url'], $this->VARS['site_url'], $this->VARS['site_name']);

		$conf_string = str_replace($placeholders, $replacements, $conf_string);

		if ( $fh = fopen( FTSP_ROOT_PATH.'functions/function_config.php', 'w' ) )
		{
			fputs($fh, $conf_string, strlen($conf_string) );
			fclose($fh);

		}
		else
		{
			$this->install_error("Could not write to 'function_config.php'");
		}

		$this->stdhead('Wrote Config Success!');

		$this->htmlout .= "
		<div class='box_content'>
			<h2>Success! Your configuration file was written to successfully!</h2>
			<br /><br />
			<div class='proceed-btn-div'><a href='index.php?progress=end'><span class='btn'>CONTINUE</span></a></div>
		</div>";

		$this->htmlout();

	}

	function do_end()
	{
		if ($FH = @fopen(INSTALLER_ROOT_PATH.'install.lock', 'w' ))
		{
			@fwrite($FH, date(DATE_RFC822), 40);
			@fclose($FH);

			@chmod(INSTALLER_ROOT_PATH.'install.lock', 0666);

			$this->stdhead('Install Complete!');

			$txt = "Although the installer is now locked (to re-install, remove the file 'install.lock'), for added security, please rename the install folder after installation is complete.
			     <br /><br /><br />
				 <div style='text-align: center;'><a href='../login.php'>Create Sysop Account</a></div>";
		}
		else
		{
			$this->stdhead('Install Complete!');

			$txt = "PLEASE REMOVE THE INSTALLER ('index.php') BEFORE CONTINUING!<br />
			Not doing this will open you up to a situation where anyone could delete your tracker &amp; data!
					<br /><br />
					<div style='text-align: center;'><a href='../login.php'>Create Sysop Account</a></div>";
		}

		$warn = '';

		if( !@chmod(FTSP_ROOT_PATH.'functions/function_config.php', 0644))
		{
	    	$warn .= "<br />Warning, please chmod functions/function_config.php to 0777 via ftp or shell.";
		}

		$this->htmlout .= "
		<div class='box_content'>
			<h2>Installation Successfully Completed!</h2>
			<br />
			<strong>The Installation is now Complete!</strong>
			{$warn}
			<br /><br />
			{$txt}
		</div>";

		$this->htmlout();

	}
	////////////////////////////////////////////////////////////
	/////////////    WORKER FUNCTIONS //////////////////////////
	////////////////////////////////////////////////////////////

	function install_error($msg="")
	{
		$this->stdhead('Warning!');

		$this->htmlout .= "	<div class='error-box'>
							    <h2>Warning!</h2>
							    <br /><br />
							    <h3>The following errors must be rectified before continuing!</h3>
							    <br />Please <a href='javascript:history.back()'><span class='btn'>go back</span></a> and try again!
							    <br /><br />
							    $msg
							</div>";

		$this->htmlout();
	}

	function htmlout()
	{
		echo $this->htmlout;
		echo "</div>
		<div id='siteInfo'><p class='center'>
	    <a href='http://www.freetsp.info'><img src='/images/button.png' alt='Powered By FreeTSP v1.0 &copy;&nbsp;2010 - 2012' title='Powered By FreeTSP v1.0 &copy;&nbsp;2010 - 2012' /></a></p>
	    </div>

	    </body></html>";
			exit();
	}

	function stdhead($title="")
	{
			$this->htmlout = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
	        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
	    <html xmlns=\"http://www.w3.org/1999/xhtml\">

			<head>

				<meta name='generator' content='FreeTSP' />
				<meta http-equiv='Content-Language' content='en-us' />
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />

				<title>FTSP.NET :: {$title}</title>
				<link rel='stylesheet' href='1.css' type='text/css' />

			</head>

	    <body>

	      <div class='text-header' style='text-align:center;'><img src='/images/logo.png' alt='' /><br /><h6>Welcome to the FreeTSP Tracker Installer</h6></div>

				<div>";

	}

	function mksecret($len=5)
	{
		$salt = '';

		for ($i = 0; $i < $len; $i++)
		{
			$num   = rand(33, 126);

			if ($num == '92')
			{
				$num = 93;
			}

			$salt .= chr($num);
		}

		return $salt;
	}

	function make_passhash($salt, $md5_once_password)
	{
		return md5(md5($salt) . $md5_once_password);
	}

} //end class

?>