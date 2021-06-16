CREATE TABLE IF NOT EXISTS `cp_credits_master` (
  `master_id` int(11) unsigned NOT NULL,
  `balance` int(11) unsigned NOT NULL DEFAULT '0',
  `last_donation_date` datetime DEFAULT NULL,
  `last_donation_amount` float unsigned DEFAULT NULL,
  PRIMARY KEY (`master_id`),
  KEY `master_id` (`master_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Donation credits balance for a given account.';
