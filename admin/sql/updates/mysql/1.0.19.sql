ALTER TABLE `#__volunteertracker_events` ADD COLUMN `is_archived` tinyint unsigned NOT NULL DEFAULT 0 AFTER `event_location`;
ALTER TABLE `#__volunteertracker_events` ADD KEY `idx_is_archived` (`is_archived`);
