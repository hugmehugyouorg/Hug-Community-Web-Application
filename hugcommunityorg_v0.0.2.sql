#
# TODO:  Add foriegn keys where necessary to optimize.
#

#
# Database: `hugcommunity`
#
DROP DATABASE IF EXISTS `hugcommunity`;
CREATE DATABASE `hugcommunity` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `hugcommunity`;

#
# Table structure for table 'groups'
#

CREATE TABLE `groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `groups` VALUES (1,'admin','Administrator');
INSERT INTO `groups` VALUES (2,'social worker','Social Worker');
INSERT INTO `groups` VALUES (3,'community member','Community Member');
INSERT INTO `groups` VALUES (4,'testers','Testers');

#
# Table structure for table 'users'
#

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE (`email`)
);


INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('1',0x7f000001,'awelters','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','awelters@hugmehugyou.org','',NULL,'1268889823','1268889823','1', 'Andrew','Welters','ADMIN','111-111-1111');
INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('2',0x7f000001,'test_admin','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_admin@test.com','',NULL,'1268889823','1268889823','1', 'TEST','ADMIN','ADMIN','111-111-1111');
INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('3',0x7f000001,'test_social_worker','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_social_worker@test.com','',NULL,'1268889823','1268889823','1', 'TEST','SOCIAL WORKER','SOCIAL WORKER','111-111-1111');
INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
	('4',0x7f000001,'test_community_member','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_community_member@test.com','',NULL,'1268889823','1268889823','1', 'TEST','COMMUNITY MEMBER','COMMUNITY MEMBER','111-111-1111');

#
# Table structure for table 'users_groups'
#

CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `uc_users_groups` UNIQUE (`user_id`, `group_id`),
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(1,1,1);
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(2,2,1);
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(3,3,2);
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(4,4,3);
INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(5,1,4);

#
# Table structure for table 'login_attempts'
#

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
);


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
# Table structure for table `companions`
#

CREATE TABLE IF NOT EXISTS  `companions` (
  `id` int(11)  unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY  (`id`)
);

INSERT INTO `companions` (`id`) VALUES (1);

CREATE TABLE `companions_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_companions_groups_companions1_idx` (`companion_id`),
  KEY `fk_companions_groups_groups1_idx` (`group_id`),
  CONSTRAINT `uc_companions_groups` UNIQUE (`companion_id`, `group_id`),
  CONSTRAINT `fk_companions_groups_companions1` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companions_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO `companions_groups` (`id`, `companion_id`, `group_id`) VALUES
	(1,1,4);

CREATE TABLE IF NOT EXISTS  `companion_messages` (
  `id` int(11)  unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `message` varchar(160) NOT NULL,
  PRIMARY KEY  (`id`)
);

#
# Table structure for table `companion_update`
#
# NOTE:  When making an insert/update make sure you set both 'created_at' and 'updated_at' to NULL
# SEE http://jasonbos.co/two-timestamp-columns-in-mysql/
#

CREATE TABLE IF NOT EXISTS  `companion_update` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `emotional_state` bit(3) NOT NULL,
  `quiet_time` bit(1) NOT NULL,
  `last_said` int(11) unsigned NOT NULL,
  `last_message_said` int(11) unsigned NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
);

#
# Drop and create the user if the don't exist then give them permissions
#
DROP USER 'hugcommunity'@'localhost';
CREATE USER 'hugcommunity'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON hugcommunity.* TO 'hugcommunity'@'localhost';