<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class DashboardModel extends BaseDatabaseModel
{
	public function getItems(): array
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__volunteertracker_entries'))
			->order($db->quoteName('event_date') . ' DESC, ' . $db->quoteName('volunteer_name') . ' ASC');

		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
	}

	public function getStats(): object
	{
		$db = $this->getDatabase();

		$stats = (object) [
			'volunteers' => 0,
			'events' => 0,
			'hours' => 0.0,
			'entries' => 0,
			'average_hours' => 0.0,
		];

		$query = $db->getQuery(true)
			->select([
				'COUNT(*) AS entries',
				'COUNT(DISTINCT ' . $db->quoteName('volunteer_name') . ') AS volunteers',
				'COUNT(DISTINCT ' . $db->quoteName('event_name') . ') AS events',
				'SUM(' . $db->quoteName('hours') . ') AS hours',
				'AVG(' . $db->quoteName('hours') . ') AS average_hours',
			])
			->from($db->quoteName('#__volunteertracker_entries'));

		$db->setQuery($query);
		$row = $db->loadObject();

		if ($row) {
			$stats->entries       = (int) $row->entries;
			$stats->volunteers    = (int) $row->volunteers;
			$stats->events        = (int) $row->events;
			$stats->hours         = (float) $row->hours;
			$stats->average_hours = (float) $row->average_hours;
		}

		return $stats;
	}

	public function getEventChart(): array
	{
		return $this->getGroupedHours('event_name', 8);
	}

	public function getVolunteerChart(): array
	{
		return $this->getGroupedHours('volunteer_name', 8);
	}

	private function getGroupedHours(string $column, int $limit): array
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$query = $db->getQuery(true)
			->select([
				$db->quoteName($column) . ' AS label',
				'SUM(' . $db->quoteName('hours') . ') AS value',
			])
			->from($db->quoteName('#__volunteertracker_entries'))
			->group($db->quoteName($column))
			->order('value DESC');

		$db->setQuery($query, 0, $limit);

		return $db->loadAssocList() ?: [];
	}
}
