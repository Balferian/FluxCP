CREATE TABLE `cp_map_index` (
	`name` VARCHAR(20) NOT NULL,
	`x` SMALLINT(4) NOT NULL,
	`y` SMALLINT(4) NOT NULL,
	PRIMARY KEY (`name`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `cp_mob_spawns` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`map` VARCHAR(20) NOT NULL,
	`x` SMALLINT(4) NOT NULL,
	`y` SMALLINT(4) NOT NULL,
	`range_x` SMALLINT(4) NOT NULL,
	`range_y` SMALLINT(4) NOT NULL,
	`mob_id` SMALLINT(5) NOT NULL,
	`count` SMALLINT(4) NOT NULL,
	`name` VARCHAR(40) NOT NULL,
	`time_to` INT(11) NOT NULL,
	`time_from` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `map` (`map`),
	INDEX `mob_id` (`mob_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0
;

CREATE TABLE `cp_npcs` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`map` VARCHAR(20) NOT NULL,
	`x` SMALLINT(4) NOT NULL,
	`y` SMALLINT(4) NOT NULL,
	`name` VARCHAR(30) NOT NULL,
	`sprite` CHAR(50) NOT NULL,
	`is_shop` TINYINT(2) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0
;

CREATE TABLE `cp_shops_sells` (
	`id_shop` INT(11) NULL DEFAULT NULL,
	`item` INT(11) NULL DEFAULT NULL,
	`price` INT(11) NULL DEFAULT NULL,
	INDEX `id_shop` (`id_shop`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `cp_warps` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`map` VARCHAR(20) NOT NULL,
	`x` SMALLINT(4) NOT NULL,
	`y` SMALLINT(4) NOT NULL,
	`to` VARCHAR(20) NOT NULL,
	`tx` SMALLINT(4) NOT NULL,
	`ty` SMALLINT(4) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0
;
