<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Helper;

defined('_JEXEC') or die;

use DateTimeImmutable;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

class ConfigurationHelper
{
	private const DEFAULTS = [
		'include_archived_dashboard' => '0',
		'archive_delay_days' => '30',
	];

	public static function getConfiguration(?DatabaseInterface $db = null): object
	{
		$db = $db ?: Factory::getContainer()->get(DatabaseInterface::class);
		$values = self::DEFAULTS;

		try {
			$query = $db->getQuery(true)
				->select([
					$db->quoteName('setting_key'),
					$db->quoteName('setting_value'),
				])
				->from($db->quoteName('#__volunteertracker_settings'));

			$db->setQuery($query);

			foreach ($db->loadObjectList() ?: [] as $row) {
				if (array_key_exists($row->setting_key, $values)) {
					$values[$row->setting_key] = (string) $row->setting_value;
				}
			}
		} catch (\Throwable $exception) {
			// Upgrades create the table before these values are used; defaults keep older installs readable.
		}

		return (object) [
			'include_archived_dashboard' => (int) !empty($values['include_archived_dashboard']),
			'archive_delay_days' => max(0, (int) $values['archive_delay_days']),
		];
	}

	public static function saveConfiguration(array $data, ?DatabaseInterface $db = null): void
	{
		$db = $db ?: Factory::getContainer()->get(DatabaseInterface::class);
		$values = [
			'include_archived_dashboard' => !empty($data['include_archived_dashboard']) ? '1' : '0',
			'archive_delay_days' => (string) max(0, (int) ($data['archive_delay_days'] ?? self::DEFAULTS['archive_delay_days'])),
		];

		foreach ($values as $key => $value) {
			$row = (object) [
				'setting_key' => $key,
				'setting_value' => $value,
			];
			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from($db->quoteName('#__volunteertracker_settings'))
				->where($db->quoteName('setting_key') . ' = ' . $db->quote($key));

			$db->setQuery($query);

			if ((int) $db->loadResult() > 0) {
				$db->updateObject('#__volunteertracker_settings', $row, 'setting_key');
				continue;
			}

			$db->insertObject('#__volunteertracker_settings', $row);
		}
	}

	public static function applyAutomaticArchiving(?DatabaseInterface $db = null): void
	{
		$db = $db ?: Factory::getContainer()->get(DatabaseInterface::class);
		$config = self::getConfiguration($db);
		$archiveOnOrBefore = (new DateTimeImmutable('today'))
			->modify('-' . (int) $config->archive_delay_days . ' days')
			->format('Y-m-d');

		try {
			$query = $db->getQuery(true)
				->update($db->quoteName('#__volunteertracker_events'))
				->set($db->quoteName('is_archived') . ' = 1')
				->where($db->quoteName('is_archived') . ' = 0')
				->where($db->quoteName('event_date') . ' <= ' . $db->quote($archiveOnOrBefore));

			$db->setQuery($query)->execute();
		} catch (\Throwable $exception) {
			// If an older database has not run the archive migration yet, let the page continue loading.
		}
	}
}
