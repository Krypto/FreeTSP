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
*--	  This program is free software; you can redistribute it and /or modify	  --*
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

//== Geshi highlighter by putyn
function source_highlighter($code)
{
	require_once(INCL_DIR.'function_geshi.php');

	$source	= str_replace(array("&#039;", "&gt;", "&lt;", "&quot;",	"&amp;"), array("'", ">", "<", "\"", "&"), $code[1]);

	if(false!==stristr($code[0],"[php]"))
		$lang2geshi	= "php";

	elseif (false!==stristr($code[0],"[sql]"))
		$lang2geshi	= "sql";

	elseif (false!==stristr($code[0],"[html]"))
		$lang2geshi	= "html4strict";
	else
		$lang2geshi	= "txt";

		$geshi = new GeSHi($source,$lang2geshi);
		$geshi->set_header_type(GESHI_HEADER_PRE_VALID);
		$geshi->set_overall_style('font: normal	normal 100%	monospace; color: #000066;', false);
		$geshi->set_line_style('color: #003030;', 'font-weight:	bold; color: #006060;',	true);
		$geshi->set_code_style('color: #000020;font-family:monospace; font-size:12px;line-height:6px;',	true);
		$geshi->enable_classes(false);
		$geshi->set_link_styles(GESHI_LINK,	'color:	#000060;');
		$geshi->set_link_styles(GESHI_HOVER, 'background-color:	#f0f000;');
		$return	= "<div	class=\"codetop\">Code</div><div class=\"codemain\">\n";
		$return	.= $geshi->parse_code();
		$return	.= "\n</div>\n";
	return	$return;

	}

$smilies = array(
	":)"					=> "happy.png",
	":("					=> "sad.png",
	":P"					=> "tongue.png",
	":wink:"				=> "wink.png",
	":x"					=>	"angry.png",
	":|"					=> "expressionless.png",
	":D"					=> "laugh.png",
	":S"					=> "puzzled.png",
	"8-)"					=>	"cool.png",
	":O"					=> "surprised.png",
	":asleep:"				=>	"asleep.png",
	":bashful:"				=> "bashful.png",
	":bashfulcute:"			=> "bashfulcute.png",
	":bigevilgrin:"			=> "bigevilgrin.png",
	":bigsmile:"			=> "bigsmile.png",
	":bigwink:"				=> "bigwink.png",
	":chuckle:"				=> "chuckle.png",
	":crying:"				=> "crying.png",
	":confused:"			=> "confused.png",
	":confusedsad:"			=> "confusedsad.png",
	":dead:"				=>	"dead.png",
	":delicious:"			=> "delicious.png",
	":depressed:"			=> "depressed.png",
	":evil:"				=> "evil.png",
	":evilgrin:"			=> "evilgrin.png",
	":grin:"				=> "grin.png",
	":impatient:"			=> "impatient.png",
	":inlove:"				=>	"inlove.png",
	":kiss:"				=>	"kiss.png",
	":mad:"					=> "mad.png",
	":nerdy:"				=>	"nerdy.png",
	":notfunny:"			=>	"notfunny.png",
	":ohrly:"				=>	"ohrly.png",
	":reallyevil:"			=>	"reallyevil.png",
	":sarcasm:"				=>	"sarcasm.png",
	":shocked:"				=> "shocked.png",
	":sick:"				=> "sick.png",
	":silly:"				=> "silly.png",
	":sing:"				=>	"sing.png",
	":smitten:"				=> "smitten.png",
	":smug:"				=>	"smug.png",
	":stress:"				=>	"stress.png",
	":sunglasses:"			=> "sunglasses.png",
	":sunglasses2:"			=> "sunglasses2.png",
	":superbashfulcute:"	=> "superbashfulcute.png",
	":tired:"				=>	"tired.png",
	":whistle:"				=> "whistle.png",
	":winktongue:"			=> "winktongue.png",
	":yawn:"				=> "yawn.png",
	":zipped:"				=> "zipped.png",
);

/*$privatesmilies =	array(
	":)"			=> "happy.png",
	":wink:"		=> "wink.gif",
	":D"			=> "grin.gif",
	":P"			=> "tongue.gif",
	":("			=> "sad.gif",
	":'("			=> "cry.gif",
	":|"			=> "noexpression.gif",
	":Boozer:"		=> "alcoholic.gif",
	":deadhorse:"	=> "deadhorse.gif",
	":spank:"		=> "spank.gif",
	":yoji:"		=> "yoji.gif",
	":locked:"		=> "locked.gif",
	":grrr:"		=> "angry.gif",		// legacy
	"O:-"			=> "innocent.gif",	// legacy
	":sleeping:"	=> "sleeping.gif",	// legacy
	"-_-"			=> "unsure.gif",		// legacy
	":clown:"		=> "clown.gif",
	":mml:"			=> "mml.gif",
	":rtf:"			=> "rtf.gif",
	":morepics:"	=> "morepics.gif",
	":rb:"			=> "rb.gif",
	":rblocked:"	=> "rblocked.gif",
	":maxlocked:"	=> "maxlocked.gif",
	":hslocked:"	=> "hslocked.gif",
);
*/
function scale($src)
{
	$max = 350;

	if (!isset($max, $src))
		return;

	$src		=	str_replace(" ", "%20",	$src[1]);
	$info		=	@getimagesize($src);
	$sw			=	$info[0];
	$sh			=	$info[1];
	$addclass	=	false;
	$max_em		=	0.06 * $max;

	if ($max < max($sw,	$sh))
	{
		if ($sw	> $sh)
			$new = array($max_em . "em", "auto");

		if ($sw	< $sh)
			$new	  =	array("auto", $max_em .	"em");
			$addclass =	true;
	}
		else
			$new = array("auto", "auto");
			$id	 = mt_rand(0000, 9999);

		if ($new[0]	== "auto" && $new[1] ==	"auto")
			$img	= "<img	src=\""	. $src . "\" border=\"0\" alt=\"\" title=\"\" />";
		else
			$img = "<img src=\"" . $src	. "\" id=\"r" .	$id	. "\" style=\"width:" .	$new[0]	. ";height:" . $new[1] . ";	border=\"0\" alt=\"\" title=\"\"  "	. ($addclass ? "class=\"resized\"" : "") . " \"	/>";
		return $img;
}

// Set this	to the line	break character	sequence of	your system
$linebreak = "\r\n";

function format_urls($s)
{
	return preg_replace(
		"/(\A|[^=\]'\"a-zA-Z0-9])((http|ftp|https|ftps|irc):\/\/[^()<>\s]+)/i",
		"\\1<a href=\"\\2\">\\2</a>", $s);
}

function format_quotes($s)
{
	$old_s = '';

	while ($old_s != $s)
	{
		$old_s = $s;

		//find first occurrence	of [/quote]
		$close = strpos($s,	"[/quote]");

		if ($close === false)
			return $s;

		//find last	[quote]	before first [/quote]
		//note that	there is no	check for correct syntax
		$open =	_strlastpos(substr($s,0,$close), "[quote");

		if ($open === false)
			return $s;

		$quote = substr($s,$open,$close	- $open	+ 8);

		//[quote]Text[/quote]
		$quote = preg_replace(
		"/\[quote\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
		"<span class='sub'><strong>Quote:</strong></span><table	class='main' border='1'	cellspacing='0'	cellpadding='10'><tr><td style='border:	1px	black dotted'>\\1</td></tr></table><br />",	$quote);

		//[quote=Author]Text[/quote]
		$quote = preg_replace(
		"/\[quote=(.+?)\]\s*((\s|.)+?)\s*\[\/quote\]\s*/i",
		"<span class='sub'><strong>\\1 wrote:</strong></span><table	class='main' border='1'	cellspacing='0'	cellpadding='10'><tr><td style='border:	1px	black dotted'>\\2</td></tr></table><br />",	$quote);

		$s = substr($s,0,$open)	. $quote . substr($s,$close	+ 8);
	}
	return $s;
}

function format_comment($text, $strip_html = true)
{
	global $smilies, $image_dir, $CURUSER;

	$s = $text;

	unset($text);
	// This	fixes the extraneous ;)	smilies	problem. When there	was	an html	escaped
	// char	before a closing bracket - like	>),	"),	...	- this would be	encoded
	// to &xxx;), hence	all	the	extra smilies. I created a new :wink: label, removed
	// the ;) one, and replace all genuine ;) by :wink:	before escaping	the	body.
	// (What took us so	long? :blush:)-	wyz

	$s = str_replace(";)", ":wink:", $s);

	if ($strip_html)
		$s = htmlentities($s, ENT_QUOTES, 'UTF-8');

	if(	preg_match(	"#function\s*\((.*?)\|\|#is", $s ) )
	{
		$s = str_replace( ":"	  ,	"&#58;", $s	);
		$s = str_replace( "["	  ,	"&#91;", $s	);
		$s = str_replace( "]"	  ,	"&#93;", $s	);
		$s = str_replace( ")"	  ,	"&#41;", $s	);
		$s = str_replace( "("	  ,	"&#40;", $s	);
		$s = str_replace( "{"	 , "&#123;", $s	);
		$s = str_replace( "}"	 , "&#125;", $s	);
		$s = str_replace( "$"	 , "&#36;",	$s );
	}

	// [*]
	if (stripos($s,	'[*]') !== false)
		$s = preg_replace("/\[\*\]/", "<img	src=\"".$image_dir."list.gif\" alt=\"List\"	title=\"List\" class=\"listitem\" />", $s);

	// [b]Bold[/b]
	if (stripos($s,	'[b]') !== false)
		$s = preg_replace('/\[b\](.+?)\[\/b\]/is', "<span style='font-weight:bold;'>\\1</span>", $s);

	// [i]Italic[/i]
	if (stripos($s,	'[i]') !== false)
		$s = preg_replace('/\[i\](.+?)\[\/i\]/is', "<span style='font-style: italic;'>\\1</span>", $s);

	// [u]Underline[/u]
	if (stripos($s,	'[u]') !== false)
		$s = preg_replace('/\[u\](.+?)\[\/u\]/is', "<span style='text-decoration:underline;'>\\1</span>", $s);

	// [color=blue]Text[/color]
	if (stripos($s,	'[color=') !== false)
	{
		$s = preg_replace('/\[color=([a-zA-Z]+)\](.+?)\[\/color\]/is','<span style="color: \\1">\\2</span>', $s);

		// [color=#ffcc99]Text[/color]
		$s = preg_replace('/\[color=(#[a-f0-9]{6})\](.+?)\[\/color\]/is','<span	style="color: \\1">\\2</span>',	$s);

	}

	//==Media tag
	if (stripos($s,	'[media=') !== false)
	{
		$s = preg_replace( "#\[media=(youtube|liveleak|GameTrailers|imdb)\](.+?)\[/media\]#ies", "_MediaTag('\\2','\\1')" ,	$s );
		$s = preg_replace( "#\[media=(youtube|liveleak|GameTrailers|vimeo)\](.+?)\[/media\]#ies", "_MediaTag('\\2','\\1')" , $s	);
	}

	//--img
	if (stripos($s,	'[img')	!==	false)
	{
		$s = preg_replace_callback("/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]/i", "scale", $s);
		$s = preg_replace_callback("/\[img=(http:\/\/[^\s'\"<>]+(\.(gif|jpg|png)))\]/i", "scale", $s);
	}

	// [size=4]Text[/size]
	if (stripos($s,	'[size=') !== false)
		$s = preg_replace('/\[size=([1-7])\](.+?)\[\/size\]/is', '<span	class="font_size_\1">\2</span>', $s);

	  // [font=Arial]Text[/font]
	if (stripos($s,	'[font=') !== false)
		$s = preg_replace('/\[font=([a-zA-Z	,]+)\](.+?)\[\/font\]/is','<span style="font-family: \\1">\\2</span>', $s);

	  // [s]Stroke[/s]
	if (stripos($s,	'[s]') !== false)
		$s = preg_replace("/\[s\](.+?)\[\/s\]/is", "<s>\\1</s>", $s);

	// the [you] tag
	if (stripos($s,	'[you]') !== false)
		$s = preg_replace("/\[you\]/i",	$CURUSER['username'], $s);

	// Dynamic Vars

	// [Spoiler]TEXT[/Spoiler]
	if (stripos($s,	'[spoiler]') !== false)
		$s = preg_replace("/\[spoiler\](.+?)\[\/spoiler\]/is", "<div class=\"smallfont\" align=\"left\">
		<input type=\"button\" value=\"Show\" style=\"width:75px;font-size:10px;margin:0px;padding:0px;\" onclick=\"if (this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display != '') {this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display =	'';this.innerText =	'';	this.value = 'Hide'; } else	{ this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = 'none'; this.innerText =	'';	this.value = 'Show'; }\" /><div	style=\"margin:	10px; padding: 10px; border: 1px inset;\" align=\"left\"><div style=\"display: none;\">\\1</div></div></div>", $s);

	// [mcom]Text[/mcom]
	if (stripos($s,	'[mcom]') !== false)
		$s = preg_replace("/\[mcom\](.+?)\[\/mcom\]/is","<div style=\"font-size: 18pt; line-height:	50%;\"><div	style=\"border-color: red; background-color: red; color: white;	text-align:	center;	font-weight: bold; font-size: large;\"><strong>\\1</strong></div></div>", $s);

	// the [you] tag
	if (stripos($s,	'[you]') !== false)
	$s = preg_replace("/\[you\]/i",	$CURUSER['username'], $s);

	// [php]php	code[/php]
	if (stripos($s,	'[php]') !== false)
		$s = preg_replace_callback(	"/\[php\](.+?)\[\/php\]/ims", "source_highlighter",	$s );

	// [sql]sql	code[/sql]
	if (stripos($s,	'[sql]') !== false)
		$s = preg_replace_callback(	"/\[sql\](.+?)\[\/sql\]/ims", "source_highlighter",	$s );

	// [html]html code[/html]
	if (stripos($s,	'[html]') !== false)
		$s = preg_replace_callback(	"/\[html\](.+?)\[\/html\]/ims",	"source_highlighter", $s );

	//[mail]mail[/mail]
	if (stripos($s,	'[mail]') !== false)
		$s = preg_replace("/\[mail\](.+?)\[\/mail\]/is","<a	href=\"mailto:\\1\"	target=\"_blank\">\\1</a>",	$s);

	//[align=(center|left|right|justify)]text[/align]
	if (stripos($s,	'[align=') !== false)
		$s = preg_replace("/\[align=([a-zA-Z]+)\](.+?)\[\/align\]/is","<div	style=\"text-align:\\1\">\\2</div>", $s);

	// Quotes
	$s = format_quotes($s);

	// URLs
	$s = format_urls($s);

	if (stripos($s,	'[url')	!==	false)
	{
		// [url=http://www.example.com]Text[/url]
		$s = preg_replace("/\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i","<span style='text-decoration: underline;'><a href=\"\\1\" target=\"_blank\">\\2</a></span>", $s);

		// [url]http://www.example.com[/url]
		$s = preg_replace("/\[url\]([^()<>\s]+?)\[\/url\]/i","<span	style='text-decoration:	underline;'><a href=\"\\1\"	target=\"_blank\">\\1</a></span>", $s);
	}

	// Linebreaks
	$s = nl2br($s);

	// [pre]Preformatted[/pre]
	if (stripos($s,	'[pre]') !== false)
		$s = preg_replace("/\[pre\](.+?)\[\/pre\]/is", "<tt><span style=\"white-space: nowrap;\">\\1</span></tt>", $s);

	// [nfo]NFO-preformatted[/nfo]
	if (stripos($s,	'[nfo]') !== false)
		$s = preg_replace("/\[nfo\](.+?)\[\/nfo\]/i", "<tt><span style=\"white-space: nowrap;\"><font face='MS Linedraw' size='2' style='font-size:	10pt; line-height: " ."10pt'>\\1</font></span></tt>", $s);

	// Maintain	spacing
	$s = str_replace("	", " &nbsp;", $s);

	reset($smilies);
	while (list($code, $url) = each($smilies))
		$s = str_replace($code,	"<img src='".$image_dir."smilies/{$url}' width='' height=''	border='0' alt='" .	htmlspecialchars($code)	. "' title='" .	htmlspecialchars($code)	. "' />", $s);

	//reset($privatesmilies);
	//while	(list($code, $url) = each($privatesmilies))
		//$s = str_replace($code, "<img	src='".$image_dir."smilies/{$url}' width=''	height='' border='0' alt=''	title='' />", $s);

	return $s;
}

function _MediaTag(	$content, $type	)
{
	if(	$content ==	'' or $type	== '' )
		return;

	$return	= '';

	switch ($type)
	{
		case 'youtube':
			$return	= preg_replace("#^http://(?:|www\.)youtube\.com/watch\?v=([\-_a-zA-Z0-9]+)+?$#i","<object type='application/x-shockwave-flash' height='355'	width='425'	data='http://www.youtube.com/v/\\1'><param name='movie'	value='http://www.youtube.com/v/\\1' /><param name='allowScriptAccess' value='sameDomain' /><param name='quality' value='best' /><param	name='bgcolor' value='#FFFFFF' /><param	name='scale' value='noScale' /><param name='salign'	value='TL' /><param	name='FlashVars' value='playerMode=embedded' /><param name='wmode' value='transparent' /></object>", $content);
		break;

		case 'liveleak':
			$return	= preg_replace("#^http://(?:|www\.)liveleak\.com/view\?i=([_a-zA-Z0-9]+)+?$#i","<object	type='application/x-shockwave-flash' height='355' width='425' data='http://www.liveleak.com/e/\\1'><param name='movie' value='http://www.liveleak.com/e/\\1' /><param name='allowScriptAccess' value='sameDomain' /><param name='quality' value='best' /><param	name='bgcolor' value='#FFFFFF' /><param	name='scale' value='noScale' /><param name='salign'	value='TL' /><param	name='FlashVars' value='playerMode=embedded' /><param name='wmode' value='transparent' /></object>", $content);
		break;

		case 'GameTrailers':
			$return	= preg_replace("#^http://(?:|www\.)gametrailers\.com/video/([\-_a-zA-Z0-9]+)+?/([0-9]+)+?$#i","<object type='application/x-shockwave-flash'	height='355' width='425' data='http://www.gametrailers.com/remote_wrap.php?mid=\\2'><param name='movie'	value='http://www.gametrailers.com/remote_wrap.php?mid=\\2'	/><param name='allowScriptAccess' value='sameDomain' />	<param name='allowFullScreen' value='true' /><param	name='quality' value='high'	/></object>", $content);
		break;

		case 'imdb':
			$return	= preg_replace("#^http://(?:|www\.)imdb\.com/video/screenplay/([_a-zA-Z0-9]+)+?$#i","<div class='\\1'><div style=\"padding:	3px; background-color: transparent;	border:	none; width:690px;\"><div style=\"text-transform: uppercase; border-bottom:	1px	solid #CCCCCC; margin-bottom: 3px; font-size: 0.8em; font-weight: bold;	display: block;\"><span	onclick=\"if (this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display !=	'')	{ this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = ''; this.innerHTML =	'<strong>Imdb Trailer: </strong><a href=\'#\' onclick=\'return false;\'>hide</a>'; } else {	this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = 'none'; this.innerHTML	= '<b>Imdb Trailer:	</b><a href=\'#\' onclick=\'return false;\'>show</a>'; }\" ><b>Imdb	Trailer: </b><a	href=\"#\" onclick=\"return	false;\">show</a></span></div><div class=\"quotecontent\"><div style=\"display:	none;\"><iframe	style='vertical-align: middle;'	src='http://www.imdb.com/video/screenplay/\\1/player' scrolling='no' width='660' height='490' frameborder='0'></iframe></div></div></div></div>", $content);
		break;

		case 'vimeo':
			$return	= preg_replace("#^http://(?:|www\.)vimeo\.com/([0-9]+)+?$#i","<object type='application/x-shockwave-flash' width='425' height='355'	data='http://vimeo.com/moogaloop.swf?clip_id=\\1&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1'>
			<param name='allowFullScreen' value='true' />
			<param name='allowScriptAccess'	value='sameDomain' />
			<param name='movie'	value='http://vimeo.com/moogaloop.swf?clip_id=\\1&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1' />
			<param name='quality' value='high' />
			</object>",	$content);
		break;

	default:

	$return	= 'not found';
	}

	return $return;
}

//Finds	last occurrence	of needle in haystack
//in PHP5 use strripos() instead of	this
function _strlastpos ($haystack, $needle, $offset =	0)
{
	$addLen	= strlen ($needle);
	$endPos	= $offset -	$addLen;
	while (true)
	{
		if (($newPos = strpos ($haystack, $needle, $endPos + $addLen)) === false) break;
		$endPos	= $newPos;
	}
	return ($endPos	>= 0) ?	$endPos	: false;
}

//this is made by putyn
function textbbcode($form,$text,$content="")
{
	global $CURUSER, $image_dir;

	$custombutton =	'';

	$bbcodebody	=<<<HTML
	<script	type="text/javascript">
		var	textBBcode = "{$text}";
	</script>

	<script	type="text/javascript" src="./js/textbbcode.js"></script>

	<div id="hover_pick" style="width:25px;	height:25px; position:absolute;	border:1px solid #333333; display:none;	z-index:20;"></div>

	<div id="pickerholder"></div>

	<table cellpadding="5" cellspacing="0" align="center"  border="1">
		<tr>
			<td	width="100%" style="padding:0" colspan="2">
				<div style="float:left;padding:4px 0px 0px 2px;">
					<img src="{$image_dir}bbcode/bold.png" width="16" height="16" border="0" alt="B" title="Bold" onclick="tag('b')" />
					<img src="{$image_dir}bbcode/italic.png" width="16"	height="16"	border="0" alt="I" title="Italic" onclick="tag('i')" />
					<img src="{$image_dir}bbcode/underline.png"	width="16" height="16" border="0" alt="U" title="Underline"	onclick="tag('u')" />
					<img src="{$image_dir}bbcode/strike.png" width="16"	height="16"	border="0" alt="S" title="Strike" onclick="tag('s')" />
					<img src="{$image_dir}bbcode/link.png" width="16" height="16" border="0" alt="Link"	title="Link" onclick="clink()" />
					<img src="{$image_dir}bbcode/picture.png" width="16" height="16" border="0"	alt="Image"	title="Add Image"  onclick="cimage()" />
					<img src="{$image_dir}bbcode/email.png"	width="16" height="16" border="0" alt="Email" title="Add Email"	onclick="mail()" />
HTML;

if($CURUSER['class'] >=	UC_MODERATOR)
	$bbcodebody	.=<<<HTML
	<img src="{$image_dir}bbcode/php.png" width="16" height="16" border="0"	alt="Php" title="Add php" onclick="tag('php')" />
	<img src="{$image_dir}bbcode/sql.png" width="16" height="16" border="0"	alt="Sql" title="Add sql" onclick="tag('sql')" />
	<img src="{$image_dir}bbcode/script.png" width="16"	height="16"	border="0" alt="Html" title="Add HTML" onclick="tag('html')" />
	<img src="{$image_dir}bbcode/modcom.png" width="16"	height="16"	border="0" alt="Mod	Comment" title="Mod	comment" onclick="tag('mcom')" />
HTML;

					$bbcodebody	.=<<<HTML
				</div>
				<div style="float:right;padding:4px	2px	0px	0px;"> <img	src="{$image_dir}bbcode/align_left.png"	width="16" height="16" border="0" alt="Left" title="Align -	Left" onclick="wrap('align', '', 'Left')" /> <img src="{$image_dir}bbcode/align_center.png"	width="16" height="16" border="0" alt="Center" title="Align	- Center" onclick="wrap('align', '', 'center')"	/> <img	src="{$image_dir}bbcode/align_justify.png"	width="16" height="16" border="0" alt="justify"	title="Align - Justify"	onclick="wrap('align', '', 'justify')" /> <img src="{$image_dir}bbcode/align_right.png"	width="16" height="16" border="0" alt="Right" title="Align - Right"	onclick="wrap('align', '' ,'right')" />	</div>
			</td>
		</tr>
		<tr>
			<td	width="100%" style="padding:0;"	colspan="2">
				<div style="float:left;padding:4px 0px 0px 2px;">
					<select	name="fontfont"	id="fontfont"  onchange="font('font',this.value);" title="Font Face">
						<option	value="0">Font</option>
						<option	value="Arial" style="font-family: Arial;">Arial</option>
						<option	value="Arial Black"	style="font-family:	Arial Black;">Arial	Black</option>
						<option	value="Comic Sans MS" style="font-family: Comic	Sans MS;">Comic	Sans MS</option>
						<option	value="Courier New"	style="font-family:	Courier	New;">Courier New</option>
						<option	value="Franklin	Gothic Medium" style="font-family: Franklin	Gothic Medium;">Franklin Gothic	Medium</option>
						<option	value="Georgia"	style="font-family:	Georgia;">Georgia</option>
						<option	value="Helvetica" style="font-family: Helvetica;">Helvetica</option>
						<option	value="Impact" style="font-family: Impact;">Impact</option>
						<option	value="Lucida Console" style="font-family: Lucida Console;">Lucida Console</option>
						<option	value="Lucida Sans Unicode"	style="font-family:	Lucida Sans	Unicode;">Lucida Sans Unicode</option>
						<option	value="Microsoft Sans Serif" style="font-family: Microsoft Sans	Serif;">Microsoft Sans Serif</option>
						<option	value="Palatino	Linotype" style="font-family: Palatino Linotype;">Palatino Linotype</option>
						<option	value="Tahoma" style="font-family: Tahoma;">Tahoma</option>
						<option	value="Times New Roman"	style="font-family:	Times New Roman;">Times	New	Roman</option>
						<option	value="Trebuchet MS" style="font-family: Trebuchet MS;">Trebuchet MS</option>
						<option	value="Verdana"	style="font-family:	Verdana;">Verdana</option>
						<option	value="Symbol" style="font-family: Symbol;">Symbol</option>
					</select>
					<select	name="fontsize"	id="fontsize" style="padding-bottom:3px;" onchange="font('size',this.value);" title="Font Size">
						<option	value="0">Font size</option>
						<option	value="1">1</option>
						<option	value="2">2</option>
						<option	value="3">3</option>
						<option	value="4">4</option>
						<option	value="5">5</option>
						<option	value="6">6</option>
						<option	value="7">7</option>
					</select>
					<select	name="fontcolor" id="fontcolor"	style="padding-bottom:3px;"	onchange="font('color',this.value);" title="Font Color">
						<option	value="0">Font color</option>
						<option	value="FF0000" style="color:#FF0000">Red</option>
						<option	value="00FFFF" style="color:#00FFFF">Turquoise</option>
						<option	value="0000FF" style="color:#0000FF">Light Blue</option>
						<option	value="0000A0" style="color:#0000A0">Dark Blue</option>
						<option	value="FF0080" style="color:#FF0080">Light Purple</option>
						<option	value="800080" style="color:#800080">Dark Purple</option>
						<option	value="FFFF00" style="color:#FFFF00">Yellow</option>
						<option	value="00FF00" style="color:#00FF00">Pastel	Green</option>
						<option	value="C0C0C0" style="color:#C0C0C0">Light Grey</option>
						<option	value="FF8040" style="color:#FF8040">Orange</option>
						<option	value="808000" style="color:#808000">Forest	Green</option>
					</select>
				</div>
				<div style="float:right;padding:4px	2px	0px	0px;"> <img	src="{$image_dir}bbcode/text_uppercase.png"	width="16" height="16" border="0" alt="Up" title="To Uppercase"	onclick="text('up')" />	<img src="{$image_dir}bbcode/text_lowercase.png" width="16"	height="16"	border="0" alt="Low" title="To Lowercase" onclick="text('low')"	/> <img	src="{$image_dir}bbcode/zoom_in.png" width="16"	height="16"	border="0" alt="S up" title="Font Size Up" onclick="fonts('up')" />	<img src="{$image_dir}bbcode/zoom_out.png" width="16" height="16" border="0" alt="S	down" title="Font Size Down" onclick="fonts('down')" /></div>
			</td>
		</tr>
		<tr>
			<td><textarea id="{$text}" name="{$text}" rows="2" cols="2"	style="width:450px;	height:250px;font-size:12px;">{$content}</textarea></td>
			<td	align="center" valign="top">
				<table width="0" cellpadding="2" border="1"	class="em_holder" cellspacing="2">
					<tr>
						<td	align="center"><a href="javascript:em(':)');"><img src="{$image_dir}smilies/happy.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':(');"><img src="{$image_dir}smilies/sad.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':P');"><img src="{$image_dir}smilies/tongue.png"	width="16" height="16" border="0" alt="Smilies"	title="" /></a></td>
						<td	align="center"><a href="javascript:em(';)');"><img src="{$image_dir}smilies/wink.png" width="16" height="16" border="0"	alt="Smilies" title="" /></a></td>
					</tr>
					<tr>
						<td	align="center"><a href="javascript:em(':x');"><img src="{$image_dir}smilies/angry.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':|');"><img src="{$image_dir}smilies/expressionless.png"	width="16" height="16" border="0" alt="Smilies"	title="" /></a></td>
						<td	align="center"><a href="javascript:em(':D');"><img src="{$image_dir}smilies/laugh.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':S');"><img src="{$image_dir}smilies/puzzled.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
					</tr>
					<tr>
						<td	align="center"><a href="javascript:em('8-)');"><img	src="{$image_dir}smilies/cool.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':O');"><img src="{$image_dir}smilies/surprised.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':asleep:');"><img src="{$image_dir}smilies/asleep.png" width="16" height="16" border="0"	alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':bashful:');"><img src="{$image_dir}smilies/bashful.png"	width="16" height="16"border="0"  alt="Smilies"	title="" /></a></td>
					</tr>
					<tr>
						<td	align="center"><a href="javascript:em(':bashfulcute:');"><img src="{$image_dir}smilies/bashfulcute.png"	width="16" height="16" border="0" alt="Smilies"	title="" /></a></td>
						<td	align="center"><a href="javascript:em(':bigevilgrin:');"><img src="{$image_dir}smilies/bigevilgrin.png"	width="16" height="16" border="0" alt="Smilies"	title="" /></a></td>
						<td	align="center"><a href="javascript:em(':bigsmile:');"><img src="{$image_dir}smilies/bigsmile.png" width="16" height="16" border="0"	alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':bigwink:');"><img src="{$image_dir}smilies/bigwink.png"	width="16" height="16" border="0" alt="Smilies"	title="" /></a></td>
					</tr>
					<tr>
						<td	align="center"><a href="javascript:em(':chuckle:');" ><img src="{$image_dir}smilies/chuckle.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':crying:');"	><img src="{$image_dir}smilies/crying.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':confused:');"><img src="{$image_dir}smilies/confused.png" width="16" height="16" border="0"	alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':confusedsad:');" ><img src="{$image_dir}smilies/confusedsad.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
					</tr>
					<tr>
						<td	align="center"><a href="javascript:em(':dead:');" ><img	src="{$image_dir}smilies/dead.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':delicious:');" ><img src="{$image_dir}smilies/delicious.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':depressed:');" ><img src="{$image_dir}smilies/depressed.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':evil:');" ><img	src="{$image_dir}smilies/evil.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
					</tr>
					<tr>
						<td	align="center"><a href="javascript:em(':grin:');" ><img	src="{$image_dir}smilies/grin.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':impatient:');" ><img src="{$image_dir}smilies/impatient.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':inlove:');"	><img src="{$image_dir}smilies/inlove.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':kiss:');" ><img	src="{$image_dir}smilies/kiss.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
					</tr>
					<tr>
						<td	align="center"><a href="javascript:em(':mad:');" ><img src="{$image_dir}smilies/mad.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':nerdy:');" ><img src="{$image_dir}smilies/nerdy.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':notfunny:');" ><img	src="{$image_dir}smilies/notfunny.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':ohrly:');" ><img src="{$image_dir}smilies/ohrly.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
					</tr>
						<tr>
						<td	align="center"><a href="javascript:em(':reallyevil:');"	><img src="{$image_dir}smilies/reallyevil.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':sarcasm:');" ><img src="{$image_dir}smilies/sarcasm.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':shocked:');" ><img src="{$image_dir}smilies/shocked.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':sick:');" ><img	src="{$image_dir}smilies/sick.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
					</tr>
						<tr>
						<td	align="center"><a href="javascript:em(':silly:');" ><img src="{$image_dir}smilies/silly.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':sing:');" ><img	src="{$image_dir}smilies/sing.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':smitten:');" ><img src="{$image_dir}smilies/smitten.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':smug:');" ><img	src="{$image_dir}smilies/smug.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
					</tr>
						<tr>
						<td	align="center"><a href="javascript:em(':stress:');"	><img src="{$image_dir}smilies/stress.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':sunglasses:');"	><img src="{$image_dir}smilies/sunglasses.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':sunglasses2:');" ><img src="{$image_dir}smilies/sunglasses2.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':superbashfulcute:');" ><img	src="{$image_dir}smilies/superbashfulcute.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
					</tr>
						<tr>
						<td	align="center"><a href="javascript:em(':tired:');" ><img src="{$image_dir}smilies/tired.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':whistle:');" ><img src="{$image_dir}smilies/whistle.png" width="16"	height="16"	border="0" alt="Smilies" title="" /></a></td>
						<td	align="center"><a href="javascript:em(':winktongue:');"	><img src="{$image_dir}smilies/winktongue.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
						<td	align="center"><a href="javascript:em(':yawn:');" ><img	src="{$image_dir}smilies/yawn.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
					</tr>
						<tr>
						<td	align="center"><a href="javascript:em(':zipped:');"	><img src="{$image_dir}smilies/zipped.png" width="16" height="16" border="0" alt="Smilies" title=""	/></a></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
HTML;
	return $bbcodebody;
}

?>