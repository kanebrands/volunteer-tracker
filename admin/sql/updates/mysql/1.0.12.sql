CREATE TABLE IF NOT EXISTS `#__volunteertracker_events` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`event_name` varchar(255) NOT NULL,
	`event_date` date NOT NULL,
	`event_location` varchar(255) NOT NULL DEFAULT '',
	`created` datetime NOT NULL,
	`created_by` int unsigned NOT NULL DEFAULT 0,
	`modified` datetime NOT NULL,
	`modified_by` int unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY `idx_event_name_date` (`event_name`, `event_date`),
	KEY `idx_event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__volunteertracker_entries` ADD COLUMN `event_id` int unsigned NOT NULL DEFAULT 0 AFTER `id`;
ALTER TABLE `#__volunteertracker_entries` ADD KEY `idx_event_id` (`event_id`);

INSERT IGNORE INTO `#__volunteertracker_events` (`event_name`, `event_date`, `event_location`, `created`, `created_by`, `modified`, `modified_by`)
SELECT DISTINCT `event_name`, `event_date`, '', NOW(), 0, NOW(), 0
FROM `#__volunteertracker_entries`
WHERE `event_name` <> '' AND `event_date` IS NOT NULL;

UPDATE `#__volunteertracker_entries` AS entries
INNER JOIN `#__volunteertracker_events` AS events
	ON events.`event_name` = entries.`event_name`
	AND events.`event_date` = entries.`event_date`
SET entries.`event_id` = events.`id`
WHERE entries.`event_id` = 0;
