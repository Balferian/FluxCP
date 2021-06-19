CREATE TABLE `cp_create_master_log` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`name` VARCHAR(23) NOT NULL,
	`user_pass` VARCHAR(32) NOT NULL,
	`email` VARCHAR(39) NOT NULL,
	`reg_date` DATETIME NOT NULL,
	`reg_ip` VARCHAR(100) NOT NULL,
	`delete_date` DATETIME NULL DEFAULT NULL,
	`confirmed` TINYINT(1) NOT NULL DEFAULT '1',
	`confirm_code` VARCHAR(32) NULL DEFAULT NULL,
	`confirm_expire` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `name` (`name`),
	INDEX `user_id` (`user_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=MyISAM
AUTO_INCREMENT=0
;