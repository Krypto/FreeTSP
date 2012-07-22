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

/////FreeTSP shout.php Spook////

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'function_main.php');
require_once(INCL_DIR.'function_user.php');
require_once(INCL_DIR.'function_vfunctions.php');

db_connect();
logged_in();

?>

<script type='text/javascript'>

function SmileIT(smile,form,text)
{
	document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";
	document.forms[form].elements[text].focus();
}
</script>

<script type='text/javascript'><!--

function mySubmit()
{
	setTimeout('document.shbox.reset()',100);
}
//--></script>

<iframe src='shoutbox.php' width='100%' height='200' frameborder='1' name='sbox' marginwidth='0' marginheight='0'></iframe>

<br /><br />

<form action='shoutbox.php' method='get' target='sbox' name='shbox' onsubmit='mySubmit()'>
<script type='text/javascript'>
var b_open = 0;
var i_open = 0;
var u_open = 0;
var color_open = 0;
var html_open = 0;

var myAgent = navigator.userAgent.toLowerCase();
var myVersion = parseInt(navigator.appVersion);

var is_ie = ((myAgent.indexOf("msie") != -1) && (myAgent.indexOf("opera") == -1));
var is_nav = ((myAgent.indexOf('mozilla')!=-1) && (myAgent.indexOf('spoofer')==-1)
&& (myAgent.indexOf('compatible') == -1) && (myAgent.indexOf('opera')==-1)
&& (myAgent.indexOf('webtv') ==-1) && (myAgent.indexOf('hotjava')==-1));

var is_win = ((myAgent.indexOf("win")!=-1) || (myAgent.indexOf("16bit")!=-1));
var is_mac = (myAgent.indexOf("mac")!=-1);
var bbtags = new Array();

function cstat()
{
	var c = stacksize(bbtags);
	if ( (c < 1) || (c == null) ) {c = 0;}
	if ( ! bbtags[0] ) {c = 0;}
	document.shbox.tagcount.value = "Tags "+c;
}

function stacksize(thearray)
{
	for (i = 0; i < thearray.length; i++ )
	{
		if ((thearray[i] == "") || (thearray[i] == null) || (thearray == 'undefined'))
		{
			return i;
		}
	}
	return thearray.length;
}

function pushstack(thearray, newval)
{
	arraysize = stacksize(thearray);
	thearray[arraysize] = newval;
}

function popstackd(thearray)
{
	arraysize = stacksize(thearray);
	theval = thearray[arraysize - 1];
	return theval;
}

function popstack(thearray)
{
	arraysize = stacksize(thearray);
	theval = thearray[arraysize - 1];
	delete thearray[arraysize - 1];
	return theval;
}

function closeall()
{
	if (bbtags[0])
	{
		while (bbtags[0])
		{
			tagRemove = popstack(bbtags)
			if ( (tagRemove != 'color') )
			{
				doInsert("[/"+tagRemove+"]", "", false);
				eval("document.shbox." + tagRemove + ".value = ' " + tagRemove + " '");
				eval(tagRemove + "_open = 0");
			}
			else
			{
				doInsert("[/"+tagRemove+"]", "", false);
			}
			cstat();
			return;
		}
	}
	document.shbox.tagcount.value = "Tags 0";
	bbtags = new Array();
	document.shbox.shbox_text.focus();
}

function add_code(NewCode)
{
	document.shbox.shbox_text.value += NewCode;
	document.shbox.shbox_text.focus();
}

function alterfont(theval, thetag)
{
	if (theval == 0) return;
	if(doInsert("[" + thetag + "=" + theval + "]", "[/" + thetag + "]", true)) pushstack(bbtags, thetag);
	document.shbox.color.selectedIndex = 0;
	cstat();
}

function tag_url()
{
	var FoundErrors = '';
	var enterURL = prompt("You must register URL", "http://");
	var enterTITLE = prompt("You must register a title", "");
	if (!enterURL || enterURL=="") {FoundErrors += " " + "You must indicate URL,";}
	if (!enterTITLE) {FoundErrors += " " + "You must indicate a title";}
	if (FoundErrors) {alert("Error !"+FoundErrors);return;}
	doInsert("[url="+enterURL+"]"+enterTITLE+"[/url]", "", false);
}

function tag_image()
{
	var FoundErrors = '';
	var enterURL = prompt("You must register the URL", "http://");
	if (!enterURL || enterURL=="http://")
	{
		alert("Error !"+"You must register URL");
		return;
	}
	doInsert("[img]"+enterURL+"[/img]", "", false);
}

function tag_email()
{
	var emailAddress = prompt("You must email", "");
	if (!emailAddress)
	{
		alert("Error !"+"You must email");
		return;
	}
	doInsert("[email]"+emailAddress+"[/email]", "", false);
}

function doInsert(ibTag, ibClsTag, isSingle)
{
	var isClose = false;
	var obj_ta = document.shbox.shbox_text;
	if ( (myVersion >= 4) && is_ie && is_win)
	{
		if(obj_ta.isTextEdit)
		{
			obj_ta.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if(ibClsTag != "" && rng.text.length > 0)
				ibTag += rng.text + ibClsTag;
				else if(isSingle) isClose = true;
				rng.text = ibTag;
			}
		}
		else
		{
			if(isSingle) isClose = true;
			obj_ta.value += ibTag;
		}
	}
	else
	{
		if(isSingle) isClose = true;
		obj_ta.value += ibTag;
	}
	obj_ta.focus();
	// obj_ta.value = obj_ta.value.replace(/ /, " ");
	return isClose;
}

function em(theSmilie)
{
	doInsert(" " + theSmilie + " ", "", false);
}

function ShowSmilies()
{
	var SmiliesWindow = window.open("/smilies.php", "Smilies","")
}

function ShowTags()
{
	var TagsWindow = window.open("/tags.php", "Tags","")
}

function winop()
{
	windop = window.open("smilies.php","mywin","");
}

function addText(theTag, theClsTag, isSingle, theForm)
{
	var isClose = false;
	var message = theForm.shbox_text;
	var set=false;
	var old=false;
	var selected="";

	if(navigator.appName=="Netscape" && message.textLength>=0 )
	{ // mozilla, firebird, netscape
		if(theClsTag!="" && message.selectionStart!=message.selectionEnd)
		{
			selected=message.value.substring(message.selectionStart,message.selectionEnd);
			str=theTag + selected+ theClsTag;
			old=true;
			isClose = true;
		}
		else
		{
			str=theTag;
		}

		message.focus();
		start=message.selectionStart;
		end=message.textLength;
		endtext=message.value.substring(message.selectionEnd,end);
		starttext=message.value.substring(0,start);
		message.value=starttext + str + endtext;
		message.selectionStart=start;
		message.selectionEnd=start;
		message.selectionStart = message.selectionStart + str.length;

		if(old)
		{
			return false;
		}

		set=true;

		if(isSingle)
		{
			isClose = false;
		}
	}

	if ((myVersion >= 4) && is_ie && is_win)
	{ // Internet Explorer
		if(message.isTextEdit)
		{
			message.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if(theClsTag != "" && rng.text.length > 0)
				theTag += rng.text + theClsTag;
			else if(isSingle)
				isClose = true;
				rng.text = theTag;
			}
		}
		else
		{
			if(isSingle) isClose = true;

			if(!set)
			{
				message.value += theTag;
			}
		}
	}
	else
	{
		if(isSingle) isClose = true;

		if(!set)
		{
			message.value += theTag;
		}
	}
	message.focus();

	return isClose;
}

function smilie(theSmilie)
{
	addText(" " + theSmilie, "", false, document.shbox);
}

function simpletag(thetag)
{
	var tagOpen = eval(thetag + "_open");
	if (tagOpen == 0)
	{
		if(doInsert("[" + thetag + "]", "[/" + thetag + "]", true))
	{
	eval(thetag + "_open = 1");
	eval("document.shbox." + thetag + ".value += '*'");
	pushstack(bbtags, thetag);
	cstat();
	}
}
else
{
	lastindex = 0;
	for (i = 0; i < bbtags.length; i++ )
	{
		if ( bbtags[i] == thetag )
		{
			lastindex = i;
		}
	}

	while (bbtags[lastindex])
	{
		tagRemove = popstack(bbtags);
		doInsert("[/" + tagRemove + "]", "", false)
		if ((tagRemove != 'COLOR'))
		{
			eval("document.shbox." + tagRemove + ".value = ' " + tagRemove + " '");
			eval(tagRemove + "_open = 0");
		}
	}
	cstat();
	}
}
</script>
<center>
<table width='600' cellspacing='0' cellpadding='1'>
	<tr>
		<td colspan='1' align='center'>
			<table cellspacing='1' cellpadding='1'>
				<tr>
					<td class='embedded'>
						<input style='font-weight: bold;font-size:9px;' type='button' name='b' value='B' onclick='javascript: simpletag("b")' />
					</td>
					<td class='embedded'>
						<input class='codebuttons' style='font-style: italic;font-size:10px;' type='button' name='i' value='I' onclick='javascript: simpletag("i")' />
					</td>
					<td class='embedded'>
						<input class='codebuttons' style='text-decoration: underline;font-size:9px;' type='button' name='u' value='U' onclick='javascript: simpletag("u")' />
					</td>
					<td class='embedded'>
						<input class='codebuttons' style='font-size:10px;' type='button' name='url' value='URL' onclick='tag_url()' />
					</td>
					<td class='embedded'>
						<input class='codebuttons' style='font-size:10px;' type='button' name='IMG' value='IMG' onclick='javascript: tag_image()' />
					</td>
					<td class='embedded'>
						<input class='width:188' style='font-size:9px;' type='button' onclick='javascript:closeall();' name='tagcount' value='Close Tags' />
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table width='600' cellspacing='0' cellpadding='1'>
	<tr>
		<td colspan='1' align='center'>
			<table cellspacing='1' cellpadding='1'>
				<tr>
					<td class='embedded'>
						<select name='color' class='codebuttons' onchange='alterfont(this.options[this.selectedIndex].value, "color")'>
							<option value='0'>---------- Color ----------</option>
							<option style='background-color: black' value='Black'>Black</option>
							<option style='background-color: sienna' value='Sienna'>Sienna</option>
							<option style='background-color: darkolivegreen' value='DarkOliveGreen'>Dark Olive Green</option>
							<option style='background-color: darkgreen' value='DarkGreen'>Dark Green</option>
							<option style='background-color: darkslateblue' value='DarkSlateBlue'>Dark Slate Blue</option>
							<option style='background-color: navy' value='Navy'>Navy</option>
							<option style='background-color: indigo' value='Indigo'>Indigo</option>
							<option style='background-color: darkslategray' value='DarkSlateGray'>Dark Slate Gray</option>
							<option style='background-color: darkred' value='DarkRed'>Dark Red</option>
							<option style='background-color: darkorange' value='DarkOrange'>Dark Orange</option>
							<option style='background-color: olive' value='Olive'>Olive</option>
							<option style='background-color: green' value='Green'>Green</option>
							<option style='background-color: teal' value='Teal'>Teal</option>
							<option style='background-color: blue' value='Blue'>Blue</option>
							<option style='background-color: slategray' value='SlateGray'>Slate Gray</option>
							<option style='background-color: dimgray' value='DimGray'>Dim Gray</option>
							<option style='background-color: red' value='Red'>Red</option>
							<option style='background-color: sandybrown' value='SandyBrown'>Sandy Brown</option>
							<option style='background-color: yellowgreen' value='YellowGreen'>Yellow Green</option>
							<option style='background-color: seagreen' value='SeaGreen'>Sea Green</option>
							<option style='background-color: mediumturquoise' value='MediumTurquoise'>Medium Turquoise</option>
							<option style='background-color: royalblue' value='RoyalBlue'>Royal Blue</option>
							<option style='background-color: purple' value='Purple'>Purple</option>
							<option style='background-color: gray' value='Gray'>Gray</option>
							<option style='background-color: magenta' value='Magenta'>Magenta</option>
							<option style='background-color: orange' value='Orange'>Orange</option>
							<option style='background-color: yellow' value='Yellow'>Yellow</option>
							<option style='background-color: lime' value='Lime'>Lime</option>
							<option style='background-color: cyan' value='Cyan'>Cyan</option>
							<option style='background-color: deepskyblue' value='DeepSkyBlue'>Deep Sky Blue</option>
							<option style='background-color: darkorchid' value='DarkOrchid'>Dark Orchid</option>
							<option style='background-color: silver' value='Silver'>Silver</option>
							<option style='background-color: pink' value='Pink'>Pink</option>
							<option style='background-color: wheat' value='Wheat'>Wheat</option>
							<option style='background-color: lemonchiffon' value='LemonChiffon'>Lemon Chiffon</option>
							<option style='background-color: palegreen' value='PaleGreen'>Pale Green</option>
							<option style='background-color: paleturquoise' value='PaleTurquoise'>Pale Turquoise</option>
							<option style='background-color: lightblue' value='LightBlue'>Light Blue</option>
							<option style='background-color: plum' value='Plum'>Plum</option>
							<option style='background-color: white' value='White'>White</option>
						</select>

						<select name='font' class='codebuttons' onchange='alterfont(this.options[this.selectedIndex].value, "font")'>
							<option value='0'>-------------- Font --------------</option>
							<option value='Arial'>Arial</option>
							<option value='Arial Black'>Arial Black</option>
							<option value='Arial Narrow'>Arial Narrow</option>
							<option value='Book Antiqua'>Book Antiqua</option>
							<option value='Century Gothic'>Century Gothic</option>
							<option value='Comic Sans MS'>Comic Sans MS</option>
							<option value='Courier New'>Courier New</option>
							<option value='Fixedsys'>Fixedsys</option>
							<option value='Franklin Gothic Medium'>Franklin GothicMedium</option>
							<option value='Garamond'>Garamond</option>
							<option value='Georgia'>Georgia</option>
							<option value='Impact'>Impact</option>
							<option value='Lucida Console'>Lucida Console</option>
							<option value='Lucida Sans Unicode'>Lucida Sans Unicode</option>
							<option value='Microsoft Sans Serif'>Microsoft Sans Serif</option>
							<option value='Palatino Linotype'>Palatino Linotype</option>
							<option value='System'>System</option>
							<option value='Tahoma'>Tahoma</option>
							<option value='Times New Roman'>Times New Roman</option>
							<option value='Trebuchet MS'>Trebuchet MS</option>
							<option value='Verdana'>Verdana</option>
						</select>

						<select name='size' class='codebuttons' onchange='alterfont(this.options[this.selectedIndex].value, "size")'>
							<option value='0'>------ Size ------</option>
							<option value='1'>1</option>
							<option value='2'>2</option>
							<option value='3'>3</option>
							<option value='4'>4</option>
							<option value='5'>5</option>
							<option value='6'>6</option>
							<option value='7'>7</option>
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align='center'><br />Message: <br />
			<input type='text' name='shbox_text' size='95' /><br />
			<input type='submit' name='submit' class='btn' value='Shout !' style="width:115" />
			<input type='hidden' name='sent' value='yes' /><br />
			<br /><br />
			<span style='font-size: small;'><a href='shoutbox.php' target='sbox'>[ Refresh ]</a></span>

<?php
if (get_user_class() >= UC_MODERATOR)
{
	?>
	<a href="javascript:popUp('shoutbox_commands.php')">[ Commands ]</a><br /><br />
	<?php
}
?>
	<a href="javascript:SmileIT(':)','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/happy.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':(','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/sad.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':P','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/tongue.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':wink:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/wink.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':x','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/angry.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':confused:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/confused.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':whistle:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/whistle.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':D','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/laugh.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':S','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/puzzled.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT('8-)','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/cool.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':O','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/surprised.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':asleep:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/asleep.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':bashful:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/bashful.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':reallyevil:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/reallyevil.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':inlove:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/inlove.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':bigwink:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/bigwink.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':crying:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/crying.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':confused:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/confused.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':zipped:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/zipped.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':evil:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/evil.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':sunglasses:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/sunglasses.png' width='16' height='16' border='0' alt='' title='' /></a>

	<a href="javascript:SmileIT(':kiss:','shbox','shbox_text')"><img src='<?php echo $image_dir?>smilies/kiss.png' width='16' height='16' border='0' alt='' title='' /></a>

	<br /><br />
				</td>
			</tr>
		</table>
	</center>
</form>