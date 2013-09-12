<?php

$TABLE[] = "CREATE TABLE announcement_main (
`main_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`owner_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
`created` datetime NOT NULL default '0000-00-00 00:00:00',
`expires` datetime NOT NULL default '0000-00-00 00:00:00',
`sql_query` text NOT NULL,
`subject` text NOT NULL,
`body` text NOT NULL,
PRIMARY KEY (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE announcement_process (
`process_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`main_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
`status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`process_id`),
KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE attachmentdownloads (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fileid` int(10) NOT NULL default '0',
  `username` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `userid` int(10) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `downloads` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fileid_userid` (`fileid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE attachments (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE avps (
  `arg` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `value_s` text collate utf8_unicode_ci NOT NULL,
  `value_i` int(11) NOT NULL default '0',
  `value_u` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE bans (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `first` int(11) default NULL,
  `last` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE blocks (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL default '0',
  `blockid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`blockid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE categories (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `image` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE comments (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE comments_offer (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`user` int(10) unsigned NOT NULL default '0',
`offer` int(10) unsigned NOT NULL default '0',
`added` datetime NOT NULL default '0000-00-00 00:00:00',
`text` text collate utf8_unicode_ci NOT NULL,
`ori_text` text collate utf8_unicode_ci NOT NULL,
`editedby` int(10) unsigned NOT NULL default '0',
`editedat` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY (`id`),
KEY `user` (`user`),
KEY `torrent` (`offer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE comments_request (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`user` int(10) unsigned NOT NULL default '0',
`request` int(10) unsigned NOT NULL default '0',
`added` datetime NOT NULL default '0000-00-00 00:00:00',
`text` text collate utf8_unicode_ci NOT NULL,
`ori_text` text collate utf8_unicode_ci NOT NULL,
`editedby` int(10) unsigned NOT NULL default '0',
`editedat` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY (`id`),
KEY `user` (`user`),
KEY `torrent` (`request`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE config (
  `mysql_host` varchar(255) NOT NULL default '',
  `mysql_db` varchar(255) NOT NULL default '',
  `mysql_user` varchar(255) NOT NULL default '',
  `mysql_pass` varchar(255) NOT NULL default '',
  `site_url` varchar(255) NOT NULL default '',
  `announce_url` varchar(255) NOT NULL default '',
  `site_online` varchar(255) NOT NULL default '',
  `members_only` varchar(255) NOT NULL default '',
  `site_mail` varchar(255) NOT NULL default '',
  `email_confirm` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'true',
  `site_name` varchar(255) NOT NULL default '',
  `image_dic` varchar(255) NOT NULL default '',
  `torrent_dic` varchar(255) NOT NULL default '',
  `peer_limit` varchar(255) NOT NULL default '',
  `max_members` varchar(255) NOT NULL default '',
  `max_users_then_invite` int(10) UNSIGNED DEFAULT '5000' NOT NULL,
  `invites` int(10) UNSIGNED DEFAULT '2500' NOT NULL,
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
  `site_reputation` enum('true','false') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'true',
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE controlpanel (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) collate utf8_unicode_ci default NULL,
`url` varchar(50) collate utf8_unicode_ci default NULL,
`image` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
`status` tinyint(3) unsigned DEFAULT NULL,
`max_class` tinyint(3) unsigned DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE countries (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) collate utf8_unicode_ci default NULL,
  `flagpic` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE files (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrent` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE forums (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE friends (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL default '0',
  `friendid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE helpdesk (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `msg_problem` text,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `solved_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `solved` enum ('no','yes','ignored') NOT NULL default 'no',
  `added_by` int(10) unsigned NOT NULL default '0',
  `solved_by` int(10) unsigned NOT NULL default '0',
  `msg_answer` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE invite_codes (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender` int(10) unsigned NOT NULL DEFAULT '0',
  `receiver` varchar(32) NOT NULL DEFAULT '0',
  `code` varchar(32) NOT NULL DEFAULT '',
  `invite_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('Pending','Confirmed') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `sender` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE loginattempts (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `banned` enum('yes','no') NOT NULL DEFAULT 'no',
  `attempts` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE messages (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE modscredits (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
`category` enum('Addon','Forum','Message/Email','Display/Style','Staff/Tools','Browse/Torrent/Details','Misc') collate utf8_unicode_ci NOT NULL default 'Misc',
`status` enum('Complete','In-Progress') collate utf8_unicode_ci NOT NULL default 'Complete',
`mod_link` varchar(255) collate utf8_unicode_ci NOT NULL default '',
`credit` varchar(255) collate utf8_unicode_ci NOT NULL default '',
`modified` varchar(255) collate utf8_unicode_ci NOT NULL default '',
`description` varchar(255) collate utf8_unicode_ci NOT NULL default '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE news (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE offers (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`offer_name` varchar(120) default NULL,
`image` varchar(180) default NULL,
`description` text NOT NULL,
`category` int(10) unsigned NOT NULL default '0',
`added` int(11) NOT NULL default '0',
`offered_by_user_id` int(10) unsigned NOT NULL default '0',
`filled_torrent_id` int(10) NOT NULL default '0',
`vote_yes_count` int(10) unsigned NOT NULL default '0',
`vote_no_count` int(10) unsigned NOT NULL default '0',
`comments` int(10) unsigned NOT NULL default '0',
`link` varchar(240) default NULL,
`status` enum('approved','pending','denied') NOT NULL default 'pending',
PRIMARY KEY (`id`),
KEY `id_added` (`id`,`added`),
KEY `offered_by_name` (`offer_name`,`offered_by_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE offer_votes (
`id` int(10) unsigned NOT NULL auto_increment,
`offer_id` int(10) unsigned NOT NULL default '0',
`user_id` int(10) unsigned NOT NULL default '0',
`vote` enum('yes','no') NOT NULL default 'yes',
PRIMARY KEY (`id`),
KEY `user_offer` (`offer_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE overforums (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(200) collate utf8_unicode_ci default NULL,
  `minclassview` tinyint(3) unsigned NOT NULL default '0',
  `forid` tinyint(3) unsigned NOT NULL default '1',
  `sort` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE peers (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE pmboxes (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `boxnumber` tinyint(4) NOT NULL default '2',
  `name` varchar(15) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE pollanswers (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`),
  KEY `selection` (`selection`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE polls (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE postpollanswers (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE postpolls (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE posts (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topicid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `added` datetime DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `editedby` int(10) unsigned NOT NULL DEFAULT '0',
  `editedat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `topicid` (`topicid`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE ratings (
  `torrent` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`torrent`,`user`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE readposts (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  `lastpostread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`id`),
  KEY `topicid` (`topicid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE reports (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`reported_by` int(10) unsigned NOT NULL default '0',
`reporting_what` int(10) unsigned NOT NULL default '0',
`reporting_type` enum('User','Comment','Request_Comment','Offer_Comment','Request','Offer','Torrent','Hit_And_Run','Post') NOT NULL default 'Torrent',
`reason` text NOT NULL,
`who_delt_with_it` int(10) unsigned NOT NULL default '0',
`delt_with` tinyint(1) NOT NULL default '0',
`added` datetime NOT NULL default '0000-00-00 00:00:00',
`how_delt_with` text NOT NULL,
`2nd_value` int(10) unsigned NOT NULL default '0',
`when_delt_with` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE requests (
`id` int(10) unsigned NOT NULL auto_increment,
`request_name` varchar(120) default NULL,
`image` varchar(180) default NULL,
`description` text NOT NULL,
`category` int(10) unsigned NOT NULL default '0',
`added` int(11) NOT NULL default '0',
`requested_by_user_id` int(10) unsigned NOT NULL default '0',
`filled_by_user_id` int(10) unsigned NOT NULL default '0',
`filled_by_username` varchar(40) collate utf8_unicode_ci NOT NULL default '',
`filled_torrent_id` int(10) NOT NULL default '0',
`vote_yes_count` int(10) unsigned NOT NULL default '0',
`vote_no_count` int(10) unsigned NOT NULL default '0',
`comments` int(10) unsigned NOT NULL default '0',
`link` varchar(240) default NULL,
PRIMARY KEY (`id`),
KEY `id_added` (`id`,`added`),
KEY `requested_by_name` (`request_name`,`requested_by_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE request_votes (
`id` int(10) unsigned NOT NULL auto_increment,
`request_id` int(10) unsigned NOT NULL default '0',
`user_id` int(10) unsigned NOT NULL default '0',
`vote` enum('yes','no') NOT NULL default 'yes',
PRIMARY KEY (`id`),
KEY `user_request` (`request_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE shoutbox (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `userid` bigint(6) NOT NULL default '0',
  `to_user` int(10) NOT NULL default '0',
  `username` varchar(25) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `text_parsed` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE sitelog (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `added` datetime default NULL,
  `txt` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE snatched (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE stafflog (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `added` datetime default NULL,
  `txt` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE staffnews (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`userid` int(11) NOT NULL default '0',
`added` datetime NOT NULL default '0000-00-00 00:00:00',
`body` text collate utf8_unicode_ci NOT NULL,
PRIMARY KEY (`id`),
KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE stats (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `Users` int(10) unsigned NOT NULL default '0',
  `Poweruser` int(10) unsigned NOT NULL default '0',
  `Vip` int(10) unsigned NOT NULL default '0',
  `Uploaders` int(10) unsigned NOT NULL default '0',
  `Moderator` int(10) unsigned NOT NULL default '0',
  `Adminisitrator` int(10) unsigned NOT NULL default '0',
  `Sysop` int(10) unsigned NOT NULL default '0',
  `Manager` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE support_lang (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE stylesheets (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `name` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE thanks (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `torrentid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE topics (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE torrents (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `poster` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `sticky` enum('yes','no') NOT NULL DEFAULT 'no',
  `anonymous` enum('yes','no') DEFAULT 'no' NOT NULL,
  `offer` int(10) unsigned NOT NULL DEFAULT '0',
  `request` int(10) unsigned NOT NULL DEFAULT '0',
  `freeleech` enum('yes','no') DEFAULT 'no' NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

$TABLE[] = "CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `old_password` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `passhash` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `secret` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` enum('pending','confirmed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `curr_ann_last_check` int(10) unsigned NOT NULL DEFAULT '0',
  `curr_ann_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editsecret` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `privacy` enum('strong','normal','low') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `stylesheet` int(10) DEFAULT '1',
  `info` text COLLATE utf8_unicode_ci,
  `acceptpms` enum('yes','friends','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `class` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `override_class` tinyint(3) unsigned NOT NULL DEFAULT '255',
  `avatar` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'images/default_avatar.gif',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country` int(10) unsigned NOT NULL DEFAULT '0',
  `notifs` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modcomment` text COLLATE utf8_unicode_ci NOT NULL,
  `enabled` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `avatars` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `donor` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `warned` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `warneduntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `torrentsperpage` int(3) unsigned NOT NULL DEFAULT '0',
  `topicsperpage` int(3) unsigned NOT NULL DEFAULT '0',
  `postsperpage` int(3) unsigned NOT NULL DEFAULT '0',
  `deletepms` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `savepms` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `passkey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `signatures` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `signature` varchar(225) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `reputation` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `last_access_numb` bigint(30) NOT NULL DEFAULT '0',
  `onlinetime` bigint(30) NOT NULL DEFAULT '0',
  `pcoff` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `parked` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `last_browse` int(11) NOT NULL DEFAULT '0',
  `menu` int(10) DEFAULT '1',
  `uploadpos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `uploadposuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `downloadpos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `downloadposuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `forumpos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `forumposuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `shoutboxpos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `shoutboxposuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `torrcompos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `torrcomposuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `offercompos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `offercomposuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requestcompos` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `requestcomposuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `support` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `supportfor` text COLLATE utf8_unicode_ci NOT NULL,
  `support_lang` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `invites` int(10) unsigned NOT NULL DEFAULT '1',
  `invitedby` int(10) unsigned NOT NULL DEFAULT '0',
  `invite_rights` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

?>