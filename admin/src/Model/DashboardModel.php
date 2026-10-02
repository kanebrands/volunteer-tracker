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

	public function getEvents(): array
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('events.id'),
				$db->quoteName('events.event_name'),
				$db->quoteName('events.event_date'),
				$db->quoteName('events.event_location'),
				'COUNT(' . $db->quoteName('entries.id') . ') AS volunteer_entries',
				'COALESCE(SUM(' . $db->quoteName('entries.hours') . '), 0) AS total_hours',
			])
			->from($db->quoteName('#__volunteertracker_events', 'events'))
			->join('LEFT', $db->quoteName('#__volunteertracker_entries', 'entries') . ' ON ' . $db->quoteName('entries.event_id') . ' = ' . $db->quoteName('events.id'))
			->group([
				$db->quoteName('events.id'),
				$db->quoteName('events.event_name'),
				$db->quoteName('events.event_date'),
				$db->quoteName('events.event_location'),
			])
			->order($db->quoteName('events.event_date') . ' DESC, ' . $db->quoteName('events.event_name') . ' ASC');

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
				'SUM(' . $db->quoteName('hours') . ') AS hours',
				'AVG(' . $db->quoteName('hours') . ') AS average_hours',
			])
			->from($db->quoteName('#__volunteertracker_entries'));

		$db->setQuery($query);
		$row = $db->loadObject();

		if ($row) {
			$stats->entries       = (int) $row->entries;
			$stats->volunteers    = (int) $row->volunteers;
			$stats->hours         = (float) $row->hours;
			$stats->average_hours = (float) $row->average_hours;
		}

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__volunteertracker_events'));

		$db->setQuery($query);
		$stats->events = (int) $db->loadResult();

		return $stats;
	}

	public function getEventChart(): array
	{
		return $this->getGroupedHours('event_name');
	}

	public function getVolunteerChart(): array
	{
		return $this->getGroupedHours('volunteer_name');
	}

	private function getGroupedHours(string $column): array
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

		$db->setQuery($query);

		return $db->loadAssocList() ?: [];
	}
}
