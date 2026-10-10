CREATE TABLE IF NOT EXISTS `#__volunteertracker_entries` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int unsigned NOT NULL DEFAULT 0,
	`volunteer_name` varchar(255) NOT NULL,
	`event_name` varchar(255) NOT NULL,
	`event_date` date NULL DEFAULT NULL,
	`hours` decimal(10,2) NOT NULL DEFAULT 0.00,
	`role` varchar(255) NOT NULL DEFAULT '',
	`notes` text NULL,
	`created` datetime NOT NULL,
	`created_by` int unsigned NOT NULL DEFAULT 0,
	`modified` datetime NOT NULL,
	`modified_by` int unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_event_id` (`event_id`),
	KEY `idx_volunteer_name` (`volunteer_name`),
	KEY `idx_event_name` (`event_name`),
	KEY `idx_event_date` (`event_date`),
	KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__volunteertracker_events` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`event_name` varchar(255) NOT NULL,
	`event_date` date NOT NULL,
	`event_location` varchar(255) NOT NULL DEFAULT '',
	`is_archived` tinyint unsigned NOT NULL DEFAULT 0,
	`created` datetime NOT NULL,
	`created_by` int unsigned NOT NULL DEFAULT 0,
	`modified` datetime NOT NULL,
	`modified_by` int unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY `idx_event_name_date` (`event_name`, `event_date`),
	KEY `idx_is_archived` (`is_archived`),
	KEY `idx_event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__volunteertracker_settings` (
	`setting_key` varchar(100) NOT NULL,
	`setting_value` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `#__volunteertracker_settings` (`setting_key`, `setting_value`) VALUES
	('include_archived_dashboard', '0'),
	('archive_delay_days', '30');
