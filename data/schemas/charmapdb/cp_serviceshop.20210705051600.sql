CREATE TABLE IF NOT EXISTS `cp_vip_service_shop` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`category` INT(11) NULL DEFAULT NULL,
	`quantity` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`cost` INT(11) UNSIGNED NOT NULL,
	`info` TEXT NULL,
	`create_date` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM COMMENT='Service shop' AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS `cp_vip_redeemlog` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`category` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`quantity` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`cost` INT(11) UNSIGNED NOT NULL,
	`account_id` INT(11) UNSIGNED NOT NULL,
	`char_id` INT(11) UNSIGNED NULL DEFAULT NULL,
	`redeemed` TINYINT(1) UNSIGNED NOT NULL,
	`redemption_date` DATETIME NULL DEFAULT NULL,
	`purchase_date` DATETIME NOT NULL,
	`credits_before` INT(10) NOT NULL,
	`credits_after` INT(10) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `category` (`category`, `account_id`, `char_id`)
) ENGINE=MyISAM COMMENT='Log of redeemed donation services.' AUTO_INCREMENT=0;
