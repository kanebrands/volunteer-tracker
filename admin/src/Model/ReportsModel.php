<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ReportsModel extends BaseDatabaseModel
{
	public function getEventOptions(): array
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('event_name'),
				$db->quoteName('event_date'),
				$db->quoteName('event_location'),
			])
			->from($db->quoteName('#__volunteertracker_events'))
			->order($db->quoteName('event_date') . ' DESC, ' . $db->quoteName('event_name') . ' ASC');

		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
	}

	public function getSelectedEventId(): int
	{
		$selected = Factory::getApplication()->input->getInt('event_id');
		$options = $this->getEventOptions();

		if ($selected) {
			return $selected;
		}

		return (int) ($options[0]->id ?? 0);
	}

	public function getSelectedEvent(): string
	{
		$selected = $this->getSelectedEventId();

		foreach ($this->getEventOptions() as $event) {
			if ((int) $event->id === $selected) {
				return $event->event_name . ' - ' . $event->event_date;
			}
		}

		return '';
	}

	public function getSortBy(): string
	{
		$sortBy = Factory::getApplication()->input->getCmd('sort_by', 'role');

		return in_array($sortBy, ['name', 'role'], true) ? $sortBy : 'role';
	}

	public function getItems(): array
	{
		$eventId = $this->getSelectedEventId();
		$sortBy = $this->getSortBy();

		if (!$eventId) {
			return [];
		}

		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('volunteer_name'),
				$db->quoteName('role'),
			])
			->from($db->quoteName('#__volunteertracker_entries'))
			->where($db->quoteName('event_id') . ' = ' . (int) $eventId);

		if ($sortBy === 'name') {
			$query->order($db->quoteName('volunteer_name') . ' ASC, ' . $db->quoteName('role') . ' ASC');
		} else {
			$query->order($db->quoteName('role') . ' ASC, ' . $db->quoteName('volunteer_name') . ' ASC');
		}

		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
	}
}
