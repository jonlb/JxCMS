CREATE TABLE IF NOT EXISTS `capabilities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `capability` varchar(50) NOT NULL,
  `description` text,
  `module` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `capability` (`capability`)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `capabilities_roles` (
  `capability_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`capability_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `capabilities_users` (
  `capability_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`capability_id`,`user_id`)
) ENGINE=InnoDB;


ALTER TABLE  `capabilities_roles` ADD FOREIGN KEY (  `capability_id` ) REFERENCES  `capabilities` (
`id`
) ON DELETE CASCADE ;

ALTER TABLE  `capabilities_roles` ADD FOREIGN KEY (  `role_id` ) REFERENCES  `roles` (
`id`
) ON DELETE CASCADE ;

ALTER TABLE  `capabilities_users` ADD FOREIGN KEY (  `capability_id` ) REFERENCES  `capabilities` (
`id`
) ON DELETE CASCADE ;

ALTER TABLE  `capabilities_users` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `users` (
`id`
) ON DELETE CASCADE ;

