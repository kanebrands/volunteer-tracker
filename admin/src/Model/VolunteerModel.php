<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class VolunteerModel extends BaseDatabaseModel
{
	public function getItem(int $id = 0): object
	{
		$app = Factory::getApplication();
		$id  = $id ?: $app->input->getInt('id');

		$item = (object) [
			'id' => 0,
			'volunteer_name' => '',
			'event_name' => '',
			'event_date' => '',
			'hours' => '',
			'notes' => '',
		];

		if (!$id) {
			return $item;
		}

		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__volunteertracker_entries'))
			->where($db->quoteName('id') . ' = ' . (int) $id);

		$db->setQuery($query);
		$row = $db->loadObject();

		return $row ?: $item;
	}

	public function save(array $data): int
	{
		$db  = $this->getDatabase();
		$app = Factory::getApplication();
		$now = Factory::getDate()->toSql();

		$row = (object) [
			'id' => (int) ($data['id'] ?? 0),
			'volunteer_name' => trim((string) ($data['volunteer_name'] ?? '')),
			'event_name' => trim((string) ($data['event_name'] ?? '')),
			'event_date' => trim((string) ($data['event_date'] ?? '')),
			'hours' => max(0, (float) ($data['hours'] ?? 0)),
			'notes' => trim((string) ($data['notes'] ?? '')),
			'modified' => $now,
			'modified_by' => (int) $app->getIdentity()->id,
		];

		if ($row->volunteer_name === '' || $row->event_name === '' || $row->hours <= 0) {
			return 0;
		}

		if ($row->event_date === '') {
			$row->event_date = null;
		}

		if ($row->id) {
			$db->updateObject('#__volunteertracker_entries', $row, 'id');

			return $row->id;
		}

		$row->created = $now;
		$row->created_by = (int) $app->getIdentity()->id;
		$db->insertObject('#__volunteertracker_entries', $row);

		return (int) $db->insertid();
	}

	public function delete(array $ids): void
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__volunteertracker_entries'))
			->where($db->quoteName('id') . ' IN (' . implode(',', array_map('intval', $ids)) . ')');

		$db->setQuery($query)->execute();
	}
}
