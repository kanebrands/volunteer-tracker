ALTER TABLE `#__volunteertracker_entries` ADD COLUMN `role` varchar(255) NOT NULL DEFAULT '' AFTER `hours`;
ALTER TABLE `#__volunteertracker_entries` ADD KEY `idx_role` (`role`);
UPDATE `#__volunteertracker_entries` SET `role` = LEFT(TRIM(COALESCE(`notes`, '')), 255) WHERE `role` = '' AND COALESCE(`notes`, '') <> '';
