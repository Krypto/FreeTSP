<?php

$TABLE[] = "CREATE TABLE attachmentdownloads (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fileid` int(10) NOT NULL default '0',
  `username` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `userid` int(10) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `downloads` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fileid_userid` (`fileid`,`userid`)
)";

$TABLE[] = "CREATE TABLE attachments (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topicid` int(10) unsigned NOT NULL default '0',
  `postid` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  `owner` int(10) unsigned NOT NULL default '0',
  `downloads` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`),
  KEY `postid` (`postid`)
)";

$TABLE[] = "CREATE TABLE avps (
  `arg` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `value_s` text collate utf8_unicode_ci NOT NULL,
  `value_i` int(11) NOT NULL default '0',
  `value_u` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arg`)
)";

$TABLE[] = "CREATE TABLE bans (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `first` int(11) default NULL,
  `last` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `first_last` (`first`,`last`)
)";

$TABLE[] = "CREATE TABLE blocks (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `blockid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`blockid`)
)";

$TABLE[] = "CREATE TABLE categories (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `image` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE comments (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text collate utf8_unicode_ci NOT NULL,
  `ori_text` text collate utf8_unicode_ci NOT NULL,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
)";

$TABLE[] = "CREATE TABLE config (
  `mysql_host` varchar(255) NOT NULL default '',
  `mysql_db` varchar(255) NOT NULL default '',
  `mysql_user` varchar(255) NOT NULL default '',
  `mysql_pass` varchar(255) NOT NULL default '',
  `domain_url` varchar(255) NOT NULL default '',
  `announce_url` varchar(255) NOT NULL default '',
  `site_online` varchar(255) NOT NULL default '',
  `members_only` varchar(255) NOT NULL default '',
  `site_mail` varchar(255) NOT NULL default '',
  `site_name` varchar(255) NOT NULL default '',
  `image_dic` varchar(255) NOT NULL default '',
  `torrent_dic` varchar(255) NOT NULL default '',
  `peer_limit` varchar(255) NOT NULL default '',
  `max_members` varchar(255) NOT NULL default '',
  `signup_timeout` varchar(255) NOT NULL default '',
  `min_votes` varchar(255) NOT NULL default '',
  `autoclean_interval` varchar(255) NOT NULL default '',
  `announce_interval` varchar(255) NOT NULL default '',
  `max_torrent_size` varchar(255) NOT NULL,
  `max_dead_torrent_time` varchar(255) NOT NULL default '',
  `posts_read_expiry` varchar(255) NOT NULL default '',
  `max_login_attempts` varchar(255) NOT NULL default '',
  `dictbreaker` varchar(255) NOT NULL default '',
  `delete_old_torrents` varchar(255) NOT NULL default '',
  `dead_torrents` varchar(255) NOT NULL default '',
  `maxfilesize` varchar(255) NOT NULL default '',
  `attachment_dir` varchar(255) NOT NULL default '',
  `forum_width` varchar(255) NOT NULL default '',
  `maxsubjectlength` varchar(255) NOT NULL default '',
  `postsperpage` varchar(255) NOT NULL default '',
  `use_attachment_mod` varchar(255) NOT NULL default '',
  `use_poll_mod` varchar(255) NOT NULL default '',
  `forum_stats_mod` varchar(255) NOT NULL default '',
  `use_flood_mod` varchar(255) NOT NULL default '',
  `limmit` varchar(255) NOT NULL default '',
  `minutes` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`mysql_host`)
)";

$TABLE[] = "CREATE TABLE countries (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci default NULL,
  `flagpic` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE files (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrent` (`torrent`)
)";

$TABLE[] = "CREATE TABLE forums (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(200) collate utf8_unicode_ci default NULL,
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `forid` tinyint(4) default '0',
  `postcount` int(10) unsigned NOT NULL default '0',
  `topiccount` int(10) unsigned NOT NULL default '0',
  `minclassread` tinyint(3) unsigned NOT NULL default '0',
  `minclasswrite` tinyint(3) unsigned NOT NULL default '0',
  `minclasscreate` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE friends (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `friendid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
)";

$TABLE[] = "CREATE TABLE loginattempts (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `banned` enum('yes','no') NOT NULL DEFAULT 'no',
  `attempts` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)";

$TABLE[] = "CREATE TABLE messages (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `subject` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No Subject',
  `msg` text collate utf8_unicode_ci,
  `unread` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `poster` bigint(20) unsigned NOT NULL DEFAULT '0',
  `location` smallint(6) NOT NULL DEFAULT '1',
  `saved` enum('no','yes') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  PRIMARY KEY  (`id`),
  KEY `receiver` (`receiver`)
)";

$TABLE[] = "CREATE TABLE news (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
)";

$TABLE[] = "CREATE TABLE overforums (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(200) collate utf8_unicode_ci default NULL,
  `minclassview` tinyint(3) unsigned NOT NULL default '0',
  `forid` tinyint(3) unsigned NOT NULL default '1',
  `sort` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE peers (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `peer_id` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `ip` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `started` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `connectable` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `userid` int(10) unsigned NOT NULL default '0',
  `agent` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `finishedat` int(10) unsigned NOT NULL default '0',
  `downloadoffset` bigint(20) unsigned NOT NULL default '0',
  `uploadoffset` bigint(20) unsigned NOT NULL default '0',
  `passkey` varchar(32) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`),
  KEY `connectable` (`connectable`),
  KEY `userid` (`userid`),
  KEY `passkey` (`passkey`)
)";

$TABLE[] = "CREATE TABLE pmboxes (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL,
  `boxnumber` tinyint(4) NOT NULL default '2',
  `name` varchar(15) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE pollanswers (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`),
  KEY `selection` (`selection`),
  KEY `userid` (`userid`)
)";

$TABLE[] = "CREATE TABLE polls (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `question` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `option0` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option1` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option2` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option3` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option4` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option5` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option6` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option7` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option8` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option9` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option10` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option11` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option12` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option13` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option14` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option15` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option16` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option17` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option18` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option19` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `sort` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE postpollanswers (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`)
)";

$TABLE[] = "CREATE TABLE postpolls (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `question` text collate utf8_unicode_ci NOT NULL,
  `option0` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option1` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option2` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option3` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option4` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option5` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option6` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option7` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option8` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option9` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option10` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option11` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option12` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option13` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option14` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option15` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option16` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option17` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option18` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `option19` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `sort` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE posts (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topicid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `body` text collate utf8_unicode_ci,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `body` (`body`)
)";

$TABLE[] = "CREATE TABLE ratings (
  `torrent` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`torrent`,`user`),
  KEY `user` (`user`)
)";


$TABLE[] = "CREATE TABLE readposts (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  `lastpostread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`id`),
  KEY `topicid` (`topicid`)
)";

$TABLE[] = "CREATE TABLE shoutbox (
  `id` bigint(10) NOT NULL auto_increment,
  `userid` bigint(6) NOT NULL default '0',
  `to_user` int(10) NOT NULL default '0',
  `username` varchar(25) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `text_parsed` text NOT NULL,
  PRIMARY KEY  (`id`)
)";


$TABLE[] = "CREATE TABLE sitelog (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `txt` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
)";

$TABLE[] = "CREATE TABLE snatched (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `connectable` enum('yes','no') NOT NULL default 'no',
  `agent` varchar(60) NOT NULL default '',
  `peer_id` varchar(20) NOT NULL default '',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `upspeed` bigint(20) NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `downspeed` bigint(20) NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') NOT NULL default 'no',
  `seedtime` int(10) unsigned NOT NULL default '0',
  `leechtime` int(10) unsigned NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `complete_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `tr_usr` (`torrentid`,`userid`)
)";

$TABLE[] = "CREATE TABLE stats (
  `id` int(10) unsigned NOT NULL auto_increment,
  `regusers` int(10) unsigned NOT NULL default '0',
  `unconusers` int(10) unsigned NOT NULL default '0',
  `torrents` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `torrentstoday` int(10) unsigned NOT NULL default '0',
  `donors` int(10) unsigned NOT NULL default '0',
  `unconnectables` int(10) unsigned NOT NULL default '0',
  `forumtopics` int(10) unsigned NOT NULL default '0',
  `forumposts` int(10) unsigned NOT NULL default '0',
  `numactive` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE stylesheets (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uri` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `name` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
)";

$TABLE[] = "CREATE TABLE topics (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `subject` varchar(120) collate utf8_unicode_ci default NULL,
  `locked` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `forumid` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `views` int(10) unsigned NOT NULL default '0',
  `pollid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `subject` (`subject`),
  KEY `lastpost` (`lastpost`),
  KEY `locked_sticky` (`locked`,`sticky`)
)";

$TABLE[] = "CREATE TABLE torrents (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info_hash` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `filename` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `save_as` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `search_text` text collate utf8_unicode_ci NOT NULL,
  `descr` text collate utf8_unicode_ci NOT NULL,
  `ori_descr` text collate utf8_unicode_ci NOT NULL,
  `category` int(10) unsigned NOT NULL default '0',
  `size` bigint(20) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('single','multi') collate utf8_unicode_ci NOT NULL default 'single',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `comments` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `times_completed` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `visible` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `banned` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `owner` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `nfo` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
)";

$TABLE[] = "CREATE TABLE users (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `old_password` varchar(40) collate utf8_unicode_ci NOT NULL default '',
  `passhash` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `secret` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `email` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `status` enum('pending','confirmed') collate utf8_unicode_ci NOT NULL default 'pending',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `forum_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `editsecret` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `privacy` enum('strong','normal','low') collate utf8_unicode_ci NOT NULL default 'normal',
  `stylesheet` int(10) default '1',
  `info` text collate utf8_unicode_ci,
  `acceptpms` enum('yes','friends','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `class` tinyint(3) unsigned NOT NULL default '0',
  `avatar` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'images/default_avatar.gif',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `title` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `country` int(10) unsigned NOT NULL default '0',
  `notifs` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `modcomment` text collate utf8_unicode_ci NOT NULL,
  `enabled` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `avatars` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `donor` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `warned` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `warneduntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `torrentsperpage` int(3) unsigned NOT NULL default '0',
  `topicsperpage` int(3) unsigned NOT NULL default '0',
  `postsperpage` int(3) unsigned NOT NULL default '0',
  `deletepms` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `savepms` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `passkey` varchar(32) collate utf8_unicode_ci NOT NULL,
  `signatures` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `signature` varchar(225) collate utf8_unicode_ci NOT NULL default '',
  `dropmenu` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `stdmenu` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `reputation` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `last_access_numb` bigint(30) NOT NULL DEFAULT '0',
  `onlinetime` bigint(30) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status_added` (`status`,`added`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`),
  KEY `last_access` (`last_access`),
  KEY `enabled` (`enabled`),
  KEY `warned` (`warned`),
  KEY `passkey` (`passkey`)
)";

?>