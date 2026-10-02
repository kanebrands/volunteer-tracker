CREATE TABLE IF NOT EXISTS `#__volunteertracker_entries` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`volunteer_name` varchar(255) NOT NULL,
	`event_name` varchar(255) NOT NULL,
	`event_date` date NULL DEFAULT NULL,
	`hours` decimal(10,2) NOT NULL DEFAULT 0.00,
	`notes` text NULL,
	`created` datetime NOT NULL,
	`created_by` int unsigned NOT NULL DEFAULT 0,
	`modified` datetime NOT NULL,
	`modified_by` int unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_volunteer_name` (`volunteer_name`),
	KEY `idx_event_name` (`event_name`),
	KEY `idx_event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
