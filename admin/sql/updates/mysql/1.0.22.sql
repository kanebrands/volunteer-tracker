CREATE TABLE IF NOT EXISTS `#__volunteertracker_settings` (
	`setting_key` varchar(100) NOT NULL,
	`setting_value` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `#__volunteertracker_settings` (`setting_key`, `setting_value`) VALUES
	('include_archived_dashboard', '0'),
	('archive_delay_days', '30');
