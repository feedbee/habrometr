CREATE TABLE `karmalog` (
  `user_id` int(6) unsigned NOT NULL,
  `karma_value` float NOT NULL,
  `habraforce` float NOT NULL,
  `rate_position` mediumint(6) NOT NULL,
  `log_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`user_id`,`log_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_code` varchar(50) NOT NULL,
  `user_email` varchar(100) NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_code` (`user_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
