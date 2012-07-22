<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>

		<title><?php echo $title ?></title>
		<meta name="title" content="FreeTSP" />
		<meta name="description" content="The FreeTSP idea was conceived by a bunch of like minded folk who wanted to create a BitTorrent source that was fundamentally different and was easy for new comers to get a site up and running and was also easy to learn" />
		<meta name="keywords" content="freetsp, free, ftsp, bittorrent, simple, kiss, tracker, code, free torrent source project, free torrent downloader, source code torrent, torrent programs" />
		<meta name="author" content="Krypto, Fireknight" />
		<meta name="owner" content="Krypto" />
		<meta name="copyright" content="(c) 2010" />

<link rel="stylesheet" href="stylesheets/dark/dark.css" type="text/css" />
<link rel="stylesheet" href="css/notification.css" type="text/css" media="screen" />

<script type='text/javascript' src='js/jquery.js'></script>
<script type="text/javascript" src="js/java_klappe.js"></script>
<script type='text/javascript'>

function popUp(URL)
{
	day = new Date();
	id  = day.getTime();
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=740,height=380,left = 340,top = 280');");
}
</script>

</head>

<body>

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

<?php $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>

<!-- MENU -->
<?php
if ($CURUSER['stdmenu'] == "yes")
{
	require_once('stdmenu.php');
}

elseif ($CURUSER['dropmenu'] == "yes")
{
	require_once('menu.php');
}
?>
<!-- MENU -->

<table class='mainouter' width='100%' border='1' cellspacing='0' cellpadding='10'>
	<tr>
		<td align='center' class='outer' style='padding-top: 20px; padding-bottom: 20px'>