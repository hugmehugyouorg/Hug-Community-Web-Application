#
# TODO:  Add foriegn keys where necessary to optimize.
#

#
# Database: `hugcommunity`
#
DROP DATABASE IF EXISTS `hugcommunity`;
CREATE DATABASE `hugcommunity` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `hugcommunity`;

DROP TABLE IF EXISTS `groups`;

#
# Table structure for table 'groups'
#

CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);

#
# Dumping data for table 'groups'
#

INSERT INTO `groups` VALUES (1,'admin','Administrator');
INSERT INTO `groups` VALUES (2,'social worker','Social Worker');
INSERT INTO `groups` VALUES (3,'community member','Community Member');


DROP TABLE IF EXISTS `users`;

#
# Table structure for table 'users'
#

CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
);


#
# Dumping data for table 'users'
#

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('1',0x7f000001,'awelters','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','awelters@hugmehugyou.org','',NULL,'1268889823','1268889823','1', 'Andrew','Welters','ADMIN','0');
INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('2',0x7f000001,'test_admin','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_admin@test.com','',NULL,'1268889823','1268889823','1', 'TEST','ADMIN','ADMIN','0');
INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('3',0x7f000001,'test_social_worker','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_social_worker@test.com','',NULL,'1268889823','1268889823','1', 'TEST','SOCIAL WORKER','SOCIAL WORKER','0');
INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('4',0x7f000001,'test_community_member','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_community_member@test.com','',NULL,'1268889823','1268889823','1', 'TEST','COMMUNITY MEMBER','COMMUNITY MEMBER','0');

DROP TABLE IF EXISTS `users_groups`;

#
# Table structure for table 'users_groups'
#

CREATE TABLE `users_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(1,1,1);
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(2,2,1);
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(3,3,2);
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(4,4,3);

DROP TABLE IF EXISTS `login_attempts`;

#
# Table structure for table 'login_attempts'
#

CREATE TABLE `login_attempts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
);


DROP TABLE IF EXISTS `ci_sessions`;
#
# Table structure for table `ci_sessions`
#
CREATE TABLE IF NOT EXISTS  `ci_sessions` (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	ip_address varchar(16) DEFAULT '0' NOT NULL,
	user_agent varchar(120) NOT NULL,
	last_activity int(10) unsigned DEFAULT 0 NOT NULL,
	user_data text NOT NULL,
	PRIMARY KEY (session_id),
	KEY `last_activity_idx` (`last_activity`)
);

#
# Drop and create the user if the don't exist then give them permissions
#
DROP USER 'hugcommunity'@'localhost';
CREATE USER 'hugcommunity'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON hugcommunity.* TO 'hugcommunity'@'localhost';
