DROP DATABASE IF EXISTS `hugcommunity`;
CREATE DATABASE `hugcommunity` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `hugcommunity`;

CREATE TABLE `groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);

INSERT INTO `groups` VALUES (NULL,'admin','Administrator');
SET @admin_group = LAST_INSERT_ID();

INSERT INTO `groups` VALUES (NULL,'social worker','Social Worker');
SET @social_worker_group = LAST_INSERT_ID();

INSERT INTO `groups` VALUES (NULL,'Little Andy','Little Andy\'s Safety Team');
SET @real_prototype_test_community_group = LAST_INSERT_ID();

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
  `mobile_alerts` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);

INSERT INTO `users` VALUES (NULL,'\0\0','awelters','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','awelters@hugmehugyou.org','',NULL,NULL,NULL,1268889823,1387334153,1,'Andrew','Welters','ADMIN','612-396-7980',1);
SET @real_admin = LAST_INSERT_ID();

INSERT INTO `users` VALUES (NULL,'\0\0','test_admin','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_admin@test.com','',NULL,NULL,NULL,1268889823,1268889823,1,'TEST','ADMIN','ADMIN','111-111-1111',0);
SET @test_admin = LAST_INSERT_ID();

INSERT INTO `users` VALUES (NULL,'\0\0','test_social_worker','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_social_worker@test.com','',NULL,NULL,NULL,1268889823,1268889823,1,'TEST','SOCIAL WORKER','SOCIAL WORKER','111-111-1111',0);
SET @test_social_worker = LAST_INSERT_ID();

INSERT INTO `users` VALUES (NULL,'\0\0','test_tester_member','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_community_member@test.com','',NULL,NULL,NULL,1268889823,1268889823,1,'TEST','COMMUNITY MEMBER','COMMUNITY MEMBER','111-111-1111',0);
SET @test_tester_member = LAST_INSERT_ID();

CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO `users_groups` VALUES (NULL,@real_admin,@admin_group);
SET @real_admin_admins = LAST_INSERT_ID();

INSERT INTO `users_groups` VALUES (NULL,@real_admin,@real_prototype_test_community_group);
SET @real_admin_test_community = LAST_INSERT_ID();

INSERT INTO `users_groups` VALUES (NULL,@test_admin,@admin_group);
SET @test_admin_admins = LAST_INSERT_ID();

INSERT INTO `users_groups` VALUES (NULL,@test_social_worker,@social_worker_group);
SET @test_social_worker_social_workers = LAST_INSERT_ID();

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
);

CREATE TABLE `companions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `emergency_alert` tinyint(1) NOT NULL DEFAULT '0',
  `curfew_alert` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_companions_name` (`name`)
);

INSERT INTO `companions` VALUES (NULL,'Andy\'s Sammy','Carver County\'s Safety Sam Prototype',0,0,NOW());
SET @real_prototype = LAST_INSERT_ID();

INSERT INTO `companions` VALUES (NULL,'Test Companion 1','Test Companion 1',0,0,NOW());
SET @test1_prototype = LAST_INSERT_ID();

INSERT INTO `companions` VALUES (NULL,'Test Companion 2','Test Companion 2',0,0,NOW());
SET @test2_prototype = LAST_INSERT_ID();

CREATE TABLE `companions_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_companions_groups` (`companion_id`,`group_id`),
  KEY `fk_companions_groups_companions1_idx` (`companion_id`),
  KEY `fk_companions_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_companions_groups_companions1` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companions_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO `companions_groups` VALUES (NULL,@real_prototype,@real_prototype_test_community_group);

CREATE TABLE `companion_says` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_message` tinyint(1) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `companion_audio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data` mediumblob NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `companion_says_audio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `companion_says_id` int(11) unsigned NOT NULL,
  `companion_audio_id` int(11) unsigned NOT NULL,
  `audio_num` smallint(3) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_companions_says_audio_num` (`companion_says_id`,`companion_audio_id`,`audio_num`),
  KEY `fk_companions_says_audio_says1_idx` (`companion_says_id`),
  KEY `fk_companions_says_audio_audio1_idx` (`companion_audio_id`),
  KEY `fk_companions_says_audio_num1_idx` (`audio_num`),
  CONSTRAINT `fk_companions_says_audio_audio1` FOREIGN KEY (`companion_audio_id`) REFERENCES `companion_audio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companions_says_audio_says1` FOREIGN KEY (`companion_says_id`) REFERENCES `companion_says` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

CREATE TABLE `companion_updates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `voltage` float(3,2) NOT NULL,
  `is_charging` tinyint(1) NOT NULL,
  `is_charging_update` tinyint(1) NOT NULL DEFAULT '0',
  `low_battery_update` tinyint(1) NOT NULL DEFAULT '0',
  `emotional_state` tinyint(1) NOT NULL,
  `emotion_update` tinyint(1) NOT NULL DEFAULT '0',
  `emergency_update` tinyint(1) NOT NULL DEFAULT '0',
  `play_message` tinyint(1) NOT NULL,
  `play_message_update` tinyint(1) NOT NULL DEFAULT '0',
  `play_message_update_by_user` tinyint(1) NOT NULL DEFAULT '0',
  `last_said_id` int(11) unsigned DEFAULT NULL,
  `last_said_update` tinyint(1) NOT NULL DEFAULT '0',
  `last_message_said_id` int(11) unsigned DEFAULT NULL,
  `last_message_said_update` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_companion_update_companions1_idx` (`companion_id`),
  KEY `fk_companion_update_last_said1_idx` (`last_said_id`),
  KEY `fk_companion_update_last_said_message1_idx` (`last_message_said_id`),
  CONSTRAINT `fk_companion_update_companions1_idx` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_update_last_said1_idx` FOREIGN KEY (`last_said_id`) REFERENCES `companion_says_audio` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_update_last_said_message1_idx` FOREIGN KEY (`last_message_said_id`) REFERENCES `companion_says_audio` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);

CREATE TABLE `companion_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `companion_id` int(11) unsigned NOT NULL,
  `companion_says_id` int(11) unsigned DEFAULT NULL,
  `is_pending` tinyint(1) NOT NULL DEFAULT '1',
  `companion_updates_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_companion_message_users1_idx` (`user_id`),
  KEY `fk_companion_message_companions1_idx` (`companion_id`),
  KEY `fk_companion_message_companion_says1_idx` (`companion_says_id`),
  KEY `fk_companion_message_companion_updates1_idx` (`companion_updates_id`),
  CONSTRAINT `fk_companion_message_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_message_companions1_idx` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_message_companion_says1_idx` FOREIGN KEY (`companion_says_id`) REFERENCES `companion_says` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_message_companion_updates1_idx` FOREIGN KEY (`companion_updates_id`) REFERENCES `companion_updates` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
