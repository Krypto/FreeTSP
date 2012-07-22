<?php

/*
*-------------------------------------------------------------------------------*
*----------------	 |	____|		 |__   __/ ____|  __ \		  --------------*
*----------------	 | |__ _ __	___	 ___| |	| (___ | |__) |		  --------------*
*----------------	 |	__|	'__/ _ \/ _	\ |	 \___ \|  ___/		  --------------*
*----------------	 | |  |	| |	 __/  __/ |	 ____) | |			  --------------*
*----------------	 |_|  |_|  \___|\___|_|	|_____/|_|			  --------------*
*-------------------------------------------------------------------------------*
*---------------------------	FreeTSP	 v1.0	--------------------------------*
*-------------------   The Alternate BitTorrent	Source	 -----------------------*
*-------------------------------------------------------------------------------*
*-------------------------------------------------------------------------------*
*--	  This program is free software; you can redistribute it and /or modify	   --*
*--	  it under the terms of	the	GNU	General	Public License as published	by	  --*
*--	  the Free Software	Foundation;	either version 2 of	the	License, or		  --*
*--	  (at your option) any later version.									  --*
*--																			  --*
*--	  This program is distributed in the hope that it will be useful,		  --*
*--	  but WITHOUT ANY WARRANTY;	without	even the implied warranty of		  --*
*--	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See	the			  --*
*--	  GNU General Public License for more details.							  --*
*--																			  --*
*--	  You should have received a copy of the GNU General Public	License		  --*
*--	  along	with this program; if not, write to	the	Free Software			  --*
*--	Foundation,	Inc., 59 Temple	Place, Suite 330, Boston, MA  02111-1307 USA  --*
*--																			  --*
*-------------------------------------------------------------------------------*
*------------	Original Credits to	tbSource, Bytemonsoon, TBDev   -------------*
*-------------------------------------------------------------------------------*
*-------------			 Developed By: Krypto, Fireknight			------------*
*-------------------------------------------------------------------------------*
*-----------------		 First Release Date	August 2010		 -------------------*
*-----------				 http://www.freetsp.info				 -----------*
*------					   2010	FreeTSP	Development	Team				  ------*
*-------------------------------------------------------------------------------*
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');

// 0 - No debug; 1 - Show and run SQL query; 2 - Show SQL query	only
$DEBUG_MODE	= 0;

db_connect();
logged_in();

if (get_user_class() < UC_MODERATOR)
	error_message("warn", "Warning", "Permission Denied.");

site_header("Administrative	User Search");

echo "<h1>Administrative User Search</h1>";

if (isset($_GET['intrust']))
{
	print("<table width='65%' border='0' align='center'>
			<tr>
				<td class='embedded' bgcolor='#F5F4EA'>
					<div align='left'>
						Fields left blank will be ignored.<br />
						Wildcards * and ? may be	used in	Name, Email	and	Comments.<br />
						as well as multiple values separated	by spaces<br />
						(e.g. 'wyz Max*'	in Name	will list both users named
						'wyz' and those whose names start by	'Max'. Similarly  '~' can be used for
						negation, e.g. '~alfiest' in	comments will restrict the search to users
						that	do not have	'alfiest' in their comments).<br /><br />
						The Ratio field accepts 'Inf' and '---' besides the usual numeric values.<br	/><br />
						The subnet mask may be entered either in	dotted decimal or CIDR notation
						(e.g. 255.255.255.0 is the same as /24).<br /><br />
						Uploaded	and	Downloaded should be entered in	GB.<br /><br />
						For search parameters with multiple text	fields the second will be
						ignored unless relevant for the type	of search chosen. <br /><br	/>
						'Active only' restricts the search to users currently leeching or seeding,
						'Disabled IPs' to those whose IPs also show up in disabled accounts.<br /><br />
						The 'p' columns in the results show partial stats, that is, those
						of the torrents in progress.	<br	/><br />
						The History column lists	the	number of forum	posts and torrent comments,
						respectively, as	well as	linking	to the history page.
					</div>
				</td>
			</tr>
		</table><br /><br />");
}
else
{
	print("<p align='center'>(<a href='usersearch.php?action=usersearch&amp;intrust=1'>Instructions</a>)");
	print("&nbsp;-&nbsp;(<a	href='usersearch.php'>Reset</a>)</p>");
}

$highlight = " bgcolor='#BBAF9B'";

?>

<form method='get' action='usersearch.php'>
	<table border='1' width='80%' align='center' cellspacing='0' cellpadding='5'>
		<tr>
			<td	align="center" class='rowhead'><label for='n'>Name:</label></td>
			<td	class='rowhead'<?php echo $_GET['n']?$highlight:""?>>
				<input type="text" name="n"	id="n" size='35' value="<?php echo $_GET['n']?>" />
			</td>
			<td	align="center" class='rowhead'>Ratio:</td>
			<td	class='rowhead'<?php echo $_GET['r']?$highlight:""?>>
				<select	name="rt">
	<?php
	$options = array("equal", "above", "below",	"between");

	for	($i	= 0; $i	< count($options); $i++)
	{
		echo "<option value='$i' ".(($_GET['rt']=="$i")	? "selected='selected'"	: "").">".$options[$i]."</option>";
	}
	?>
				</select>
				<input type="text" name="r"	size="5" maxlength="4" value="<?php	echo $_GET['r']?>" />
				<input type="text" name="r2" size="5" maxlength="4"	value="<?php echo $_GET['r2']?>" />
			</td>
			<td	class='rowhead'	align="center">Member Status:</td>
			<td	class='rowhead'<?php echo $_GET['st']?$highlight:""?>>
				<select	name="st">
	<?php
	$options = array("(any)", "confirmed", "pending");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option value='$i' ".(($_GET['st']=="$i")	? "selected='selected'"	: "").">".$options[$i]."</option>";
	}
	?>
				</select>
			</td>
		</tr>
		<tr><td	class='rowhead'	align="center"><label for='em'>Email:</label></td>
			<td<?php echo $_GET['em']?$highlight:""?>>
				<input type="text" name="em" id="em" size="35" value="<?php	echo $_GET['em']?>"	/>
			</td>
			<td	class='rowhead'	align='center'><label for='ip'>IP:</label></td>
			<td	class='rowhead'<?php echo $_GET['ip']?$highlight:""?>>
				<input type="text" name="ip" id="ip" maxlength="17"	value="<?php echo $_GET['ip']?>" />
			</td>
			<td	class='rowhead'	align="center">Account Status:</td>
			<td	class='rowhead'<?php echo $_GET['as']?$highlight:""?>>
				<select	name="as">
	<?php
	$options = array("(any)","enabled","disabled");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option	value='$i' ".(($_GET['as']=="$i") ?	"selected='selected'" :	"").">".$options[$i]."</option>";
	}
	?>
				</select>
			</td>
		</tr>
		<tr>
			<td	align='center' class='rowhead'><label for='co'>Comment:</label></td>
			<td	class='rowhead'<?php echo $_GET['co']?$highlight:""?>>
				<input type="text" name="co" id="co" size="35" value="<?php	echo $_GET['co']?>"	/>
			</td>
			<td	align='center' class='rowhead'><label for='ma'>Mask:</label></td>
			<td	class='rowhead'<?php echo $_GET['ma']?$highlight:""?>>
				<input type="text" name="ma" id="ma" maxlength="17"	value="<?php echo $_GET['ma']?>"  />
			</td>
			<td	align='center' class='rowhead'>Class:</td>
			<td	class='rowhead'<?php echo ($_GET['c'] && $_GET['c']	!= 1)?$highlight:""?>>
				<select	name="c"><option value='1'>(any)</option>
	<?php
	$class = $_GET['c'];

	if (!is_valid_id($class))
		$class = '';

	for	($i	= 2;;++$i)
	{
		if ($c = get_user_class_name($i-2))
			print("<option value='"	. $i . ($class && $class ==	$i?	"' selected='selected" : "") . "'>$c</option>");
		else
			break;
	}
	?>
				</select>
			</td>
		</tr>
		<tr>
			<td	align='center' class='rowhead'>Joined:</td>
			<td	class='rowhead'<?php echo $_GET['d']?$highlight:""?>>
				<select	name="dt">
	<?php
	$options = array("on", "before", "after", "between");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option value='$i' ".(($_GET['dt']=="$i")	? "selected='selected'"	: "").">".$options[$i]."</option>";
	}
	?>
				</select>
				<input type="text" name="d"	size="12" maxlength="10" value="<?php echo $_GET['d']?>" />
				<input type="text" name="d2" size="12" maxlength="10" value="<?php echo	$_GET['d2']?>" />
			</td>
			<td	align='center' class='rowhead'>Uploaded:</td>
			<td	class='rowhead'<?php echo $_GET['ul'] ?	$highlight : ""?>><select name="ult" id="ult">
	<?php
	$options = array("equal","above", "below", "between");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option value='$i' ".(($_GET['ult']=="$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
	}
	?>
				</select>
				 <input	type="text"	name="ul" id="ul" size="8" maxlength="7" value="<?php echo $_GET['ul']?>" />
				 <input	type="text"	name="ul2" id="ul2"	size="8" maxlength="7" value="<?php	echo $_GET['ul2']?>" />
			</td>
			<td	align='center' class='rowhead'>Donor:</td>
			<td	class='rowhead'<?php echo $_GET['do']?$highlight:""?>>
				<select	name="do">
	<?php
	$options = array("(any)", "Yes", "No");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option value='$i' ".(($_GET['do']=="$i")	? "selected='selected'"	: "").">".$options[$i]."</option>";
	}
	?>
				</select>
			</td>
		</tr>
		<tr>
			<td	align='center' class='rowhead'>Last	Seen:</td>
			<td	class='rowhead'	<?php echo $_GET['ls'] ? $highlight	: ""?>>
				<select	name="lst">
	<?php
	$options = array("on", "before", "after", "between");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option value='$i' ".(($_GET['lst']=="$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
	}
	?>
				</select>
				<input type="text" name="ls" size="12" maxlength="10" value="<?php echo	$_GET['ls']?>"	/>
				<input type="text" name="ls2" size="12"	maxlength="10" value="<?php	echo $_GET['ls2']?>" />
			</td>
			<td	align='center' class='rowhead'>Downloaded:</td>
			<td	class='rowhead'<?php echo $_GET['dl'] ?	$highlight : ""?>>
				<select	name="dlt" id="dlt">
	<?php
	$options = array("equal", "above", "below",	"between");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option value='$i' ".(($_GET['dlt']=="$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
	}
	?>
				</select>
				<input type="text" name="dl" id="dl" size="8" maxlength="7"	value="<?php echo $_GET['dl']?>" />
				<input type="text" name="dl2" id="dl2" size="8"	maxlength="7" value="<?php echo	$_GET['dl2']?>"	/>
			</td>
			<td	align='center' class='rowhead'>Warned:</td>
			<td	class='rowhead'<?php echo $_GET['w']?$highlight:""?>>
				<select	name="w">
	<?php
	$options = array("(any)", "Yes", "No");

	for	($i	= 0;
		$i < count($options);
		$i++)
	{
		echo "<option value='$i' ".(($_GET['w']=="$i") ? "selected='selected'" : "").">".$options[$i]."</option>";
	}
	?>
				</select>
			</td>
		</tr>
		<tr>
			<td	class='rowhead'></td>
			<td	class='std'></td>
			<td	align='center' class='rowhead'>Active Only:</td>
			<td	class='rowhead'<?php echo $_GET['ac']?$highlight:""?>>
				<input type="checkbox" name="ac" value="1" <?php echo ($_GET['ac'])	? "checked='checked'" :	"" ?> />
			</td>
			<td	align='center' class='rowhead'>Disabled	IP:	</td>
			<td	class='rowhead'<?php echo $_GET['dip']?$highlight:""?>>
				<input type="checkbox" name="dip" value="1"	<?php echo ($_GET['dip']) ?	"checked='checked'"	: "" ?>	/>
			</td>
			</tr>
			<tr>
				<td	 class='rowhead' colspan='6' align='center'>
				<input type='submit' class='btn' name='submit'/>
			</td>
		</tr>
	</table>
	<br	/><br />
</form>

<?php

// Validates date in the form [yy]yy-mm-dd;
// Returns date	if valid, 0	otherwise.
function mkdate($date)
{
	if (strpos($date,'-'))
		$a = explode('-', $date);
	elseif (strpos($date,'/'))
		$a = explode('/', $date);
	else
		return 0;

	for	($i=0;
		$i<3;
		$i++)

	if (!is_numeric($a[$i]))
			return 0;
	if (checkdate($a[1], $a[2],	$a[0]))
			return	date ("Y-m-d", mktime (0,0,0,$a[1],$a[2],$a[0]));
	else
			return 0;
}

// ratio as	a string
function ratios($up, $down,	$color = true)
{
	if ($down >	0)
	{
		$r = number_format($up / $down,	2);

	if ($color)
			$r = "<span	style='color : ".get_ratio_color($r)."'>$r</span>";
	}
	else
		if ($up	> 0)
			$r = "Inf.";
	  else
			$r = "---";
	return $r;
}

// checks for the usual	wildcards *, ? plus	mySQL ones
function haswildcard($text)
{
	if (strpos($text,'*') === false	&& strpos($text,'?') === false	&& strpos($text,'%') === false && strpos($text,'_')	===	false)
		return false;
	else
		return true;
}

if (count($_GET) > 0 &&	!$_GET['h'])
{
	// name
	$names = explode(' ',trim($_GET['n']));

	if ($names[0] !== "")
	{
		foreach($names as $name)
		{
			if (substr($name,0,1) == '~')
		{
			if ($name == '~') continue;
				$names_exc[] = substr($name,1);
		}
		else
			$names_inc[] = $name;
		}

	if (is_array($names_inc))
	{
		$where_is .= isset($where_is)?"	and	(":"(";

		foreach($names_inc as $name)
		{
		if (!haswildcard($name))
			$name_is .=	(isset($name_is) ? " or	" :	"")	. "u.username =	" .	sqlesc($name);
		  else
		  {
			$name =	str_replace(array('?', '*'), array('_',	'%'), $name);
			$name_is .=	(isset($name_is) ? " or	" :	"")	. "u.username LIKE " . sqlesc($name);
		  }
		}
		$where_is .= $name_is.")";
		unset($name_is);
	}

	if (is_array($names_exc))
	{
		$where_is .= isset($where_is)?"	and	NOT	(":" NOT (";

		foreach($names_exc as $name)
		{
			if (!haswildcard($name))
				$name_is .=	(isset($name_is) ? " or	" :	"")	. "u.username =	" .	sqlesc($name);
			else
		{
			$name =	str_replace(array('?', '*'), array('_',	'%'), $name);
			$name_is .=	(isset($name_is)? "	or " : "") . "u.username LIKE "	. sqlesc($name);
		}
	}
	  $where_is	.= $name_is.")";
	}
	  $q .=	($q	? "&amp;" :	"")	. "n=".urlencode(trim($_GET['n']));
	}

	// email
	 $emaila = explode(' ',	trim($_GET['em']));

	if ($emaila[0] !== "")
	{
		$where_is .= isset($where_is)?"	and	(":"(";

		foreach($emaila	as $email)
		{
			if (strpos($email,'*') === false &&	strpos($email,'?') === false &&	strpos($email,'%') === false)
			{
				if (validemail($email) !== 1)
				{
					error_message("error", "Error",	"Bad Email.");
				}
				$email_is .= (isset($email_is) ? " or  ": "") .	" u.email =" . sqlesc($email);
			}
			else
			{
				$sql_email = str_replace(array('?',	'*'), array('_', '%'), $email);
				$email_is .= (isset($email_is) ? " or "	: "") .	"u.email LIKE "	. sqlesc($sql_email);
			}
		}
		$where_is .= $email_is.")";
		$q .= ($q ?	"&amp;"	: "") .	"em=".urlencode(trim($_GET['em']));
	}

	//class
	// NB: the c parameter is passed as	two	units above	the	real one
	$class = $_GET['c']	- 2;

	if (is_valid_id($class + 1))
	{
		$where_is .= (isset($where_is) ? " and " : "") . "u.class=$class";
		$q .= ($q ?	"&amp;"	: "") .	"c=".($class+2);
	}

	// IP
	$ip	= trim($_GET['ip']);

	if ($ip)
	{
		$regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";

		if (!preg_match($regex,	$ip))
		{
			error_message("error", "Error",	"Bad IP.");
		}

		$mask =	trim($_GET['ma']);

		if ($mask == ""	|| $mask ==	"255.255.255.255")
			$where_is .= (isset($where_is) ? " and " : "")."u.ip = '$ip'";
		else
		{
			if (substr($mask,0,1) == "/")
			{
				$n = substr($mask, 1, strlen($mask)	- 1);

				if (!is_numeric($n)	or $n <	0 or $n	> 32)
				{
					error_message("error", "Error",	"Bad Subnet	Mask.");
				}
				else
					$mask =	long2ip(pow(2,32) -	pow(2,32-$n));
			}
			elseif (!preg_match($regex,	$mask))
			{
				error_message("error", "Error",	"Bad subnet	mask.");
			}

			 $where_is .= (isset($where_is)	? "	and	" :	"")	. "INET_ATON(u.ip) & INET_ATON('$mask')	= INET_ATON('$ip') & INET_ATON('$mask')";

			$q .= ($q ?	"&amp;"	: "") .	"ma=$mask";
		}
		$q .= ($q ?	"&amp;"	: "") .	"ip=$ip";
	}

	// ratio
	$ratio = trim($_GET['r']);

	if ($ratio)
	{
		if ($ratio == '---')
		{
			$ratio2	   = "";
			$where_is .= isset($where_is) ?	" and "	: "";
			$where_is .= " u.uploaded =	0 and u.downloaded = 0";
		}
		elseif (strtolower(substr($ratio,0,3)) == 'inf')
		{
			$ratio2	   = "";
			$where_is .= isset($where_is) ?	" and "	: "";
			$where_is .= " u.uploaded >	0 and u.downloaded = 0";
		}
		else
		{
		if (!is_numeric($ratio)	|| $ratio <	0)
		{
			error_message("error", "Error",	"Bad Ratio.");
		}

		$where_is .= isset($where_is) ?	" and "	: "";
		$where_is .= " (u.uploaded/u.downloaded)";
		$ratiotype = $_GET['rt'];
		$q		 .=	($q	? "&amp;" :	"")	. "rt=$ratiotype";

		if ($ratiotype == "3")
		{
			$ratio2	= trim($_GET['r2']);

			if(!$ratio2)
			{
				error_message("error", "Error",	"Two Ratios	are	Needed for This	type of	Search.");
			}

			if (!is_numeric($ratio2) or	$ratio2	< $ratio)
			{
				error_message("error", "Error",	"Bad Second	Ratio.");
			}

			$where_is .= " BETWEEN $ratio and $ratio2";
			$q		 .=	($q	? "&amp;" :	"")	. "r2=$ratio2";

		}
		elseif ($ratiotype == "2")
			$where_is .= " < $ratio";

		elseif ($ratiotype == "1")
			$where_is .= " > $ratio";

		else
			$where_is .= " BETWEEN ($ratio - 0.004)	and	($ratio	+ 0.004)";
		}
		$q .= ($q ?	"&amp;"	: "") .	"r=$ratio";
	}

	// comment
	$comments =	explode(' ',trim($_GET['co']));

	if ($comments[0] !== "")
	{
		foreach($comments as $comment)
		{
			if (substr($comment,0,1) ==	'~')
		{
		if ($comment ==	'~') continue;
			$comments_exc[]	= substr($comment,1);
		}
		else
			$comments_inc[]	= $comment;
		}

		if (is_array($comments_inc))
		{
			$where_is .= isset($where_is) ?	" and (" : "(";

			foreach($comments_inc as $comment)
			{
				if (!haswildcard($comment))
					$comment_is	.= (isset($comment_is) ? " or "	: "") .	"u.modcomment LIKE " . sqlesc("%".$comment."%");
				else
				{
					$comment	 = str_replace(array('?', '*'),	array('_', '%'), $comment);
					$comment_is	.= (isset($comment_is) ? " or "	: "") .	"u.modcomment LIKE " . sqlesc($comment);
				}
			}
			$where_is .= $comment_is.")";

			unset($comment_is);
		}

		if (is_array($comments_exc))
		{
			$where_is .= isset($where_is) ?	" and NOT (" : " NOT (";

			foreach($comments_exc as $comment)
			{
				if (!haswildcard($comment))
					$comment_is	.= (isset($comment_is) ? " or "	: "") .	"u.modcomment LIKE " . sqlesc("%".$comment."%");
				else
				{
					$comment	 = str_replace(array('?', '*'),	array('_', '%'), $comment);
					$comment_is	.= (isset($comment_is) ? " or "	: "") .	"u.modcomment LIKE " . sqlesc($comment);
				}
			}
			$where_is .= $comment_is.")";
		}
		$q .= ($q ?	"&amp;"	: "") .	"co=".urlencode(trim($_GET['co']));
	}

	$unit =	1073741824;		// 1GB

	// uploaded
	$ul	= trim($_GET['ul']);

	if ($ul)
	{
		if (!is_numeric($ul) ||	$ul	< 0)
		{
			error_message("error", "Error",	"Bad Uploaded Amount.");
		}

		$where_is .= isset($where_is)?"	and	" :	"";
		$where_is .= " u.uploaded ";
		$ultype	   = $_GET['ult'];
		$q		 .=	($q	? "&amp;" :	"")	. "ult=$ultype";

		if ($ultype	== "3")
		{
			$ul2 = trim($_GET['ul2']);

			if(!$ul2)
			{
				error_message("error", "Error",	"Two Uploaded amounts needed for this type of search.");
			}

			if (!is_numeric($ul2) or $ul2 <	$ul)
			{
				error_message("error", "Error",	"Bad Second	Uploaded Amount.");
			}

			$where_is .= " BETWEEN ".$ul*$unit." and ".$ul2*$unit;
			$q		 .=	($q	? "&amp;" :	"")	. "ul2=$ul2";

		}

		elseif ($ultype	== "2")
			$where_is .= " < ".$ul*$unit;

		elseif ($ultype	== "1")
			$where_is .= " >". $ul*$unit;
		else
			$where_is .= " BETWEEN ".($ul -	0.004)*$unit." and ".($ul +	0.004)*$unit;
			$q		 .=	($q	? "&amp;" :	"")	. "ul=$ul";
		}

	// downloaded
	$dl	= trim($_GET['dl']);

	if ($dl)
	{
		if (!is_numeric($dl) ||	$dl	< 0)
		{
			error_message("error", "Error",	"Bad Downloaded	Amount.");
		}

		$where_is .= isset($where_is)?"	and	":"";
		$where_is .= " u.downloaded	";
		$dltype	   = $_GET['dlt'];
		$q		 .=	($q	? "&amp;" :	"")	. "dlt=$dltype";

		if ($dltype	== "3")
		{
			$dl2 = trim($_GET['dl2']);

			if(!$dl2)
			{
				error_message("error", "Error",	"Two Downloaded	Amounts	Needed for this	Type of	Search.");
			}

		if (!is_numeric($dl2) or $dl2 <	$dl)
		{
			error_message("error", "Error",	"Bad Second	Downloaded Amount.");

		}

		$where_is .= " BETWEEN ".$dl*$unit." and ".$dl2*$unit;
		$q .= ($q ?	"&amp;"	: "") .	"dl2=$dl2";
		}
		elseif ($dltype	== "2")
			$where_is .= " < ".$dl*$unit;

		elseif ($dltype	== "1")
			$where_is .= " > ".$dl*$unit;

		else
			$where_is .= " BETWEEN ".($dl -	0.004)*$unit." and ".($dl +	0.004)*$unit;
			$q .= ($q ?	"&amp;"	: "") .	"dl=$dl";
		}

	// date	joined
	$date =	trim($_GET['d']);

	if ($date)
	{
		if (!$date = mkdate($date))
		{
			error_message("error", "Error",	"Invalid Date.");
		}

		$q		.= ($q ? "&amp;" : "") . "d=$date";
		$datetype =	$_GET['dt'];
		$q		.= ($q ? "&amp;" : "") . "dt=$datetype";

		if ($datetype == "0")
		// For mySQL 4.1.1 or above	use	instead
		// $where_is .=	(isset($where_is)?"	and	":"")."DATE(added) = DATE('$date')";
		$where_is .= (isset($where_is) ? " and " : "").	"(UNIX_TIMESTAMP(added)	- UNIX_TIMESTAMP('$date')) BETWEEN 0 and 86400";
		else
		{
		$where_is .= (isset($where_is) ? " and " : "") ."u.added ";

			if ($datetype == "3")
			{
				$date2 = mkdate(trim($_GET['d2']));

				if ($date2)
				{
					if (!$date = mkdate($date))
					{
						error_message("error", "Error",	"Invalid Date.");
					}

					$q .= ($q ?	"&amp;"	: "") .	"d2=$date2";
					$where_is .= " BETWEEN '$date' and '$date2'";
				}
				else
				{
					error_message("error", "Error",	"Two Dates Needed for this Type	of Search.");
				}
			}
			elseif ($datetype == "1")
				$where_is .= "<	'$date'";

			elseif ($datetype == "2")
				$where_is .= ">	'$date'";
		}
	}

	// date	last seen
	$last =	trim($_GET['ls']);

	if ($last)
	{
		if (!$last = mkdate($last))
		{
			error_message("error", "Error",	"Invalid Date.");
		}

		$q		.= ($q ? "&amp;" : "") . "ls=$last";
		$lasttype =	$_GET['lst'];
		$q		.= ($q ? "&amp;" : "") . "lst=$lasttype";

		if ($lasttype == "0")

			// For mySQL 4.1.1 or above	use	instead
			// $where_is .=	(isset($where_is)?"	and	":"")."DATE(added) = DATE('$date')";
			$where_is .= (isset($where_is) ? " and " : "").	"(UNIX_TIMESTAMP(last_access) -	UNIX_TIMESTAMP('$last')) BETWEEN 0 and 86400";
		else
		{
			$where_is .= (isset($where_is) ? " and " : "") ."u.last_access ";
			if ($lasttype == "3")
			{
				$last2 = mkdate(trim($_GET['ls2']));
					if ($last2)
					{
						$where_is .= " BETWEEN '$last' and '$last2'";
						$q .= ($q ?	"&amp;"	: "") .	"ls2=$last2";
					}
					else
					{
						error_message("error", "Error",	"The Second	Date is	Not	Valid.");
					}
			}
			elseif ($lasttype == "1")
				$where_is .= "<	'$last'";

			elseif ($lasttype == "2")
				$where_is .= ">	'$last'";
		}
	}

	// status
	$status	= $_GET['st'];

	if ($status)
	{
		$where_is .= ((isset($where_is)) ? " and " : "");

		if ($status	== "1")
			$where_is .= "u.status = 'confirmed'";
		else
			$where_is .= "u.status = 'pending'";
			$q		  .= ($q ? "&amp;" : "") . "st=$status";
	}

	// account status
	$accountstatus = $_GET['as'];

	if ($accountstatus)
	{
		$where_is .= (isset($where_is))	? "	and	" :	"";

		if ($accountstatus == "1")
			$where_is .= " u.enabled = 'yes'";
		else
			$where_is .= " u.enabled = 'no'";
			$q		  .= ($q ? "&amp;" : "") . "as=$accountstatus";
	}

	//donor
	$donor = $_GET['do'];

	if ($donor)
	{
		$where_is .= (isset($where_is))	? "	and	" :	"";

		if ($donor == 1)
			$where_is .= " u.donor = 'yes'";
		else
			$where_is .= " u.donor = 'no'";
			$q		  .= ($q ? "&amp;" : "") . "do=$donor";
	}

	//warned
	$warned	= $_GET['w'];

	if ($warned)
	{
		$where_is .= (isset($where_is))	? "	and	" :	"";

		if ($warned	== 1)
			$where_is .= " u.warned	= 'yes'";
		else
			$where_is .= " u.warned	= 'no'";
			$q		  .= ($q ? "&amp;" : "") . "w=$warned";
	}

	// disabled	IP
	$disabled =	$_GET['dip'];

	if ($disabled)
	{
		$distinct  = "DISTINCT ";
		$join_is  .= " LEFT	JOIN users as u2 ON	u.ip = u2.ip";
		$where_is .= ((isset($where_is)) ? " and " : "")." u2.enabled =	'no'";
		$q		  .= ($q ? "&amp;" : "") . "dip=$disabled";
	}

	// active
	$active	= $_GET['ac'];

	if ($active	== "1")
	{
		$distinct =	"DISTINCT ";
		$join_is .=	" LEFT JOIN	peers as p ON u.id = p.userid";
		$q		 .=	($q	? "&amp;" :	"")	. "ac=$active";
	}

	$from_is  =	"users as u".$join_is;
	$distinct =	isset($distinct) ? $distinct : "";

	$queryc	= "SELECT COUNT(".$distinct."u.id) FROM	".$from_is.(($where_is == "") ?	"" : " WHERE $where_is ");

	$querypm = "FROM ".$from_is.(($where_is	== "") ? " " : " WHERE $where_is ");

	$select_is = "u.id,	u.username,	u.email, u.status, u.added,	u.last_access, u.ip, u.class, u.uploaded, u.downloaded,	u.donor, u.modcomment, u.enabled, u.warned";

	$query1	= "SELECT ".$distinct."	".$select_is." ".$querypm;

	//	<temporary>
	if ($DEBUG_MODE	> 0)
	{
		error_message("info", "Count Query", $queryc);
		echo "<br /><br	/>";

		error_message("info", "Search Query", $query);
		echo "<br /><br	/>";

		error_message("info", "URL ", $q);
		if ($DEBUG_MODE	== 2)
			die();
			echo "<br /><br	/>";
	}
	//	</temporary>

	$res		= sql_query($queryc) or sqlerr();
	$arr		= mysql_fetch_row($res);
	$count		= $arr[0];
	$q			= isset($q)	? ($q."&amp;"):"";
	$perpage	= 30;

	list($pagertop,	$pagerbottom, $limit) =	pager($perpage,	$count,	$_SERVER["PHP_SELF"]." ? ".$q);

	$query1	.= $pager['limit'];

	$res = sql_query($query1) or sqlerr();

	if (mysql_num_rows($res) ==	0)
		error_message("info", "Info", "No User was found.");
	else
	{
		if ($count > $perpage)
			echo $pagertop;
			echo "<table border='1'	width='80%'	align='center' cellspacing='0' cellpadding='5'>";
			echo "<tr>
					<td class='colhead' align='left'>Name</td>
					<td class='colhead' align='left'>Ratio</td>
					<td class='colhead' align='left'>IP</td>
					<td class='colhead' align='left'>Email</td>
					<td class='colhead' align='left'>Joined:</td>
					<td class='colhead' align='left'>Last	seen:</td>
					<td class='colhead' align='left'>Status</td>
					<td class='colhead' align='left'>Enabled</td>
					<td class='colhead'>pR</td>
					<td class='colhead'>pUL</td>
					<td class='colhead'>pDL</td>
					<td class='colhead'>History</td></tr>";

		while ($user = mysql_fetch_assoc($res))
		{
			if ($user['added'] == '0000-00-00 00:00:00')
				$user['added'] = '---';

			if ($user['last_access'] ==	'0000-00-00	00:00:00')
				$user['last_access'] = '---';

			if ($user['ip'])
			{
				$nip	= ip2long($user['ip']);
				$auxres	= sql_query("SELECT	COUNT(*)
										FROM bans
										WHERE $nip >= first and $nip <= last") or sqlerr(__FILE__, __LINE__);

				$array = mysql_fetch_row($auxres);

				if ($array[0] == 0)
					$ipstr = $user['ip'];
				else
					$ipstr = "<a href='testip.php?ip=" . $user['ip'] . "'><span	style='color : #ff0000;'><span style='font-weight:bold;'>" . $user['ip'] . "</span></span></a>";
			}
			else
				$ipstr = "---";

			$auxres	= sql_query("SELECT	SUM(uploaded) AS pul, SUM(downloaded) AS pdl
									FROM peers
									WHERE userid = " . $user['id'])	or sqlerr(__FILE__,	__LINE__);

			$array = mysql_fetch_assoc($auxres);

			$pul = $array['pul'];
			$pdl = $array['pdl'];

			$auxres	= sql_query("SELECT	COUNT(DISTINCT p.id) FROM posts	AS p
									LEFT JOIN topics AS t ON p.topicid = t.id
									LEFT JOIN forums AS	f ON t.forumid = f.id WHERE	p.userid = " . $user['id'] . " AND f.minclassread <= " .$CURUSER['class']) or sqlerr(__FILE__, __LINE__);

			$n = mysql_fetch_row($auxres);
			$n_posts = $n[0];

			$auxres	= sql_query("SELECT	COUNT(id)
									FROM comments
									WHERE user = ".$user['id'])	or sqlerr(__FILE__,	__LINE__);

			$n			= mysql_fetch_row($auxres);
			$n_comments	= $n[0];
			$ids		.= $user['id'].':';


			echo "<tr>
					<td><span	style='font-weight:bold;'><a href='userdetails.php?id="	. $user['id'] .	"'>" . $user['username']."</a></span>" .	get_user_icons($user) .	"</td>
					<td class='rowhead'>" . ratios($user['uploaded'], $user['downloaded'])	. "</td>
					<td class='rowhead'>" . $ipstr	. "</td>
					<td class='rowhead'>" . $user['email']	. "</td>
					<td class='rowhead' align='center'>" .	$user['added'] . "</td>
					<td class='rowhead' align='center'>" .	$user['last_access'] . "</td>
					<td class='rowhead' align='center'>" .	$user['status']	. "</td>
					<td class='rowhead' align='center'>" .	$user['enabled']."</td>
					<td class='rowhead' align='center'>" .	ratios($pul,$pdl) .	"</td>
					<td class='rowhead' align='right'>" . mksize($pul)	. "</td>
					<td class='rowhead' align='right'>" . mksize($pdl)	. "</td>
					<td class='rowhead' align='center'>".($n_posts?"<a	href='userhistory.php?action=viewposts&amp;id=".$user['id']."'>$n_posts</a>":$n_posts).
					"|".($n_comments?"<a href='userhistory.php?action=viewcomments&amp;id=".$user['id']."'>$n_comments</a>":$n_comments).
				"</td>
				</tr>";


		}
		echo "</table>";

		if ($count > $perpage)
			echo "$pagerbottom";
	}
}

echo("<p>$pagemenu<br />$browsemenu</p>");

site_footer();

die;

?>