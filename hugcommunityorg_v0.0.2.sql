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
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
);

INSERT INTO `groups` VALUES (1,'admin','Administrator');
INSERT INTO `groups` VALUES (2,'social worker','Social Worker');
INSERT INTO `groups` VALUES (3,'littleandy','Little Andy');

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
	('4',0x7f000001,'test_tester_member','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_community_member@test.com','',NULL,'1268889823','1268889823','1', 'TEST','COMMUNITY MEMBER','COMMUNITY MEMBER','111-111-1111');

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
	(4,1,3);

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
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `emergency_alert` tinyint(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY  (`id`)
);

INSERT INTO `companions` (`id`, `name`, `description`, `emergency_alert`) VALUES (1, 'Little Andys Companion', 'Little Andys Awesome Companion', 1);
INSERT INTO `companions` (`id`, `name`, `description`) VALUES (2, 'Test Companion 1', 'Test Companion 1');
INSERT INTO `companions` (`id`, `name`, `description`) VALUES (3, 'Test Companion 2', 'Test Companion 2');

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
	(1,1,3);

CREATE TABLE IF NOT EXISTS  `companion_says` (
  `id` int(11)  unsigned NOT NULL AUTO_INCREMENT,
  `is_message` tinyint(1) NOT NULL,
  `text` varchar(160) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS  `companion_audio` (
  `id` int(11)  unsigned NOT NULL AUTO_INCREMENT,
  `data` MEDIUMBLOB NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `companion_says_audio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `companion_says_id` int(11) unsigned NOT NULL,
  `companion_audio_id` int(11) unsigned NOT NULL,
  `audio_num` smallint(3) unsigned NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_companions_says_audio_says1_idx` (`companion_says_id`),
  KEY `fk_companions_says_audio_audio1_idx` (`companion_audio_id`),
  KEY `fk_companions_says_audio_num1_idx` (`audio_num`),
  CONSTRAINT `uc_companions_says_audio_num` UNIQUE (`companion_says_id`, `companion_audio_id`, `audio_num`),
  CONSTRAINT `fk_companions_says_audio_says1` FOREIGN KEY (`companion_says_id`) REFERENCES `companion_says` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companions_says_audio_audio1` FOREIGN KEY (`companion_audio_id`) REFERENCES `companion_audio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

CREATE TABLE IF NOT EXISTS  `companion_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `voltage` float(3,2) NOT NULL,
  `is_charging` tinyint(1) NOT NULL,
  `emotional_state` tinyint(1) NOT NULL,
  `quiet_time` tinyint(1) NOT NULL,
  `last_said_id` int(11) unsigned DEFAULT NULL,
  `last_message_said_id` int(11) unsigned DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `fk_companion_update_companions1_idx` (`companion_id`),
  KEY `fk_companion_update_last_said1_idx` (`last_said_id`),
  KEY `fk_companion_update_last_said_message1_idx` (`last_message_said_id`),
  CONSTRAINT `fk_companion_update_companions1_idx` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_update_last_said1_idx` FOREIGN KEY (`last_said_id`) REFERENCES `companion_says_audio` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_update_last_said_message1_idx` FOREIGN KEY (`last_message_said_id`) REFERENCES `companion_says_audio` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);

INSERT INTO `companion_updates` VALUES (1,1,4.92,0,3,0,NULL,NULL,'2013-11-27 23:55:04');
INSERT INTO `companion_updates` VALUES (2,1,3.92,0,0,0,NULL,NULL,'2013-11-27 23:55:05');
INSERT INTO `companion_updates` VALUES (3,1,5.00,0,2,1,NULL,NULL,'2013-11-27 23:55:06');
INSERT INTO `companion_updates` VALUES (4,1,2.81,1,1,0,NULL,NULL,'2013-11-27 23:55:07');

#
# Drop and create the user if the don't exist then give them permissions
#
DROP USER 'hugcommunity'@'localhost';
CREATE USER 'hugcommunity'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON hugcommunity.* TO 'hugcommunity'@'localhost';