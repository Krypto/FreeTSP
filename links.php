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
logged_in();

site_header("Links");

function add_link($url, $title, $description = "")
{
	$text = "<a class='altlink' href='$url'>$title</a>";

	if ($description)
		$text = "$text - $description";
		echo("<li>$text</li>\n");
}

if ($CURUSER)
{
	?>
	<p align='center'><a href='sendmessage.php?receiver=1'><img src='<?php echo $image_dir?>deadlink.png' width='250' height='64' border='0' alt='Report Dead Links' title='Report Dead Links' /></a></p>
	<?php
}
	?>

<table width='100%' class='main' border='0' cellspacing='0' cellpadding='0'>
	<tr>
		<td class='embedded'>
		<h2>Other Pages on this Site</h2>
			<table width='100%' border='1' cellspacing='0' cellpadding='10'>
				<tr>
					<td class='text'>
						<ul>
							<li><a class='altlink' href='rss.xml'>RSS feed</a> - For use with RSS-enabled software. An alternative to torrent email notifications.</li>
							<li><a class='altlink' href='rssdd.xml'>RSS feed (direct download)</a> - Links directly to the torrent file.</li>
							<li><a class='altlink' href='http://imgur.com'>Imgur</a> - If you need a place to host your avatar or other pictures.</li>
						</ul>
					</td>
				</tr>
			</table>

			<h2>BitTorrent Information</h2>
			<table width='100%' border='1' cellspacing='0' cellpadding='10'>
				<tr>
					<td class='text'>
						<ul>
							<li><a class='altlink' href='http://dessent.net/btfaq/'>Brian's BitTorrent FAQ and Guide</a> - Everything you need to know about BitTorrent. Required reading for all n00bs.</li>
							<li><a class='altlink' href='http://10mbit.com/faq/bt/'>The Ultimate BitTorrent FAQ</a> - Another nice BitTorrent FAQ, by Evil Timmy.</li>
						</ul>
					</td>
				</tr>
			</table>

			<h2>BitTorrent Software</h2>
			<table width='100%' border='1' cellspacing='0' cellpadding='10'>
				<tr>
					<td class='text'>
						<ul>
							<li><a class='altlink' href='http://pingpong-abc.sourceforge.net/'>ABC</a> - "ABC is an improved client for the Bittorrent peer-to-peer file distribution solution."</li>
							<li><a class='altlink' href='http://azureus.sourceforge.net'>Azureus</a> - "Azureus is a java bittorrent client. It provides a quite full bittorrent protocol implementation using java language."</li>
							<li><a class='altlink' href='http://bnbt.go-dedicated.com/'>BNBT</a> - Nice BitTorrent tracker written in C++.</li>
							<li><a class='altlink' href='http://bittornado.com/'>BitTornado</a> - a.k.a "TheSHAD0W's Experimental BitTorrent Client".</li>
							<li><a class='altlink' href='http://www.bitconjurer.org/BitTorrent'>BitTorrent</a> - Bram Cohen's official BitTorrent client.</li>
							<li><a class='altlink' href='http://ei.kefro.st/projects/btclient/'>BitTorrent EXPERIMENTAL</a> - "This is an unsupported, unofficial, and , most importantly, experimental build of the BitTorrent GUI for Windows."</li>
							<li><a class='altlink' href='http://krypt.dyndns.org:81/torrent/'>Burst!</a> - Alternative Win32 BitTorrent client.</li>
							<li><a class='altlink' href='http://g3torrent.sourceforge.net/'>G3 Torrent</a> - "A feature rich and graphically empowered bittorrent client written in python."</li>
							<li><a class='ltlink' href='http://krypt.dyndns.org:81/torrent/maketorrent/'>MakeTorrent</a> - A tool for creating torrents.</li>
							<li><a class='altlink' href='http://ptc.sourceforge.net/'>Personal Torrent Collector</a> - BitTorrent client.</li>
							<li><a class='altlink' href='http://www.shareaza.com/'>Shareaza</a> - Gnutella, eDonkey and BitTorrent client.</li>
						</ul>
					</td>
				</tr>
			</table>

			<h2>Download Sites</h2>
			<table width='100%' border='1' cellspacing='0' cellpadding='10'>
				<tr>
					<td class='text'>
						<ul>
							<li><a class='altlink' href='http://www.suprnova.org/'>SuprNova</a> - Apps, games, movies, TV and other stuff. [popups]</li>
							<li><a class='altlink' href='http://empornium.us:6969/'>Empornium</a> - Pr0n, and then some!</li>
						</ul>
					</td>
				</tr>
			</table>

			<h2>Forum Communities</h2>
			<table width='100%' border='1' cellspacing='0' cellpadding='10'>
				<tr>
					<td class='text'>
						<ul>
							<li><a class='altlink' href='http://www.filesoup.com/'>Filesoup</a> -
							  BitTorrent community.</li>
							<li><a class='altlink' href='http://www.torrent-addiction.com/forums/index.php'>Torrent Addiction</a> -
							  Another BitTorrent community. [popups]</li>
							<li><a class='altlink' href='http://www.terabits.net'>TeraBits</a> -
							Games, movies, apps both unix and win, tracker support, music, xxx.</li>
							<li><a class='altlink' href='http://www.ftpdreams.com/new/forum/sitenews.asp'>FTP Dreams</a> - "Where Dreams Become a Reality".</li>
						</ul>
					</td>
				</tr>
			</table>

			<h2>Other Sites</h2>
			<table width='100%' border='1' cellspacing='0' cellpadding='10'>
				<tr>
					<td class='text'>
						<ul>
							<li><a class='altlink' href='http://www.nforce.nl/'>NFOrce</a> - Game and movie release tracker / forums.</li>
							<li><a class='altlink' href='http://www.grokmusiq.com/'>grokMusiQ</a> - Music release tracker.</li>
							<li><a class='altlink' href='http://www.izonews.com/'>iSONEWS</a> - Release tracker and forums.</li>
							<li><a class='altlink' href='http://www.btsites.tk'>BTSITES.TK</a> - BitTorrent link site. [popups]</li>
							<li><a class='altlink' href='http://www.litezone.com/'>Link2U</a> - BitTorrent link site.</li>
						</ul>
					</td>
				</tr>
			</table>

			<h2>Link to FreeTSP</h2>
			<table width='100%' border='1' cellspacing='0' cellpadding='10'>
				<tr>
					<td class='text'>
						Do you want a link to FreeTSP on your homepage?<br />
						Copy the following and paste it into your homepage code.<br />
						<br />
						<a href="http://www.freetsp.info"><img src="http://www.freetsp.info/public/style_images/logo-4.png" width='486' height='100' border='0' alt='FreeTSP Support Forum' title='FreeTSP Support Forum' /></a><br />
						<span style='color : #004E98;'>
						&lt;!-- FreeTSP Link --&gt;<br />
						<br />
						&lt;a href='http://www.freetsp.info'&gt;&lt;img src='http://www.freetsp.info/public/style_images/logo-4.png' border='0' alt='FreeTSP Support Forum' title='FreeTSP Support Forum' &gt;&lt;/a&gt; <br />
						<br />
						&lt;!-- End of FreeTSP Link --&gt;</span><br />
						<br />
						<br />
					</td>
				</tr>
		</table>

<p align='right'><span style='font-size: x-small; color : #004E98; font-weight:bold'>Links edited 2010-01-26 (08:38 GMT)</span></p>

		</td>
	</tr>
</table>

<?php

site_footer();

?>