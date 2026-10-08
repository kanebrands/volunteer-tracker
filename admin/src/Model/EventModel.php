<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class EventModel extends BaseDatabaseModel
{
	public function getItem(int $id = 0): object
	{
		$app = Factory::getApplication();
		$id  = $id ?: $app->input->getInt('id');

		$item = (object) [
			'id' => 0,
			'event_name' => '',
			'event_date' => '',
			'event_location' => '',
			'is_archived' => 0,
		];

		if (!$id) {
			return $item;
		}

		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__volunteertracker_events'))
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
			'event_name' => trim((string) ($data['event_name'] ?? '')),
			'event_date' => trim((string) ($data['event_date'] ?? '')),
			'event_location' => trim((string) ($data['event_location'] ?? '')),
			'is_archived' => !empty($data['is_archived']) ? 1 : 0,
			'modified' => $now,
			'modified_by' => (int) $app->getIdentity()->id,
		];

		if ($row->event_name === '' || $row->event_date === '' || $this->isDuplicateEvent($row)) {
			return 0;
		}

		if ($row->id) {
			$db->updateObject('#__volunteertracker_events', $row, 'id');
			$this->syncEntries($row);

			return $row->id;
		}

		$row->created = $now;
		$row->created_by = (int) $app->getIdentity()->id;
		$db->insertObject('#__volunteertracker_events', $row);

		return (int) $db->insertid();
	}

	public function delete(array $ids): void
	{
		$ids = array_values(array_filter(array_map('intval', $ids)));

		if (!$ids) {
			return;
		}

		$db = $this->getDatabase();
		$idList = implode(',', $ids);

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__volunteertracker_entries'))
			->where($db->quoteName('event_id') . ' IN (' . $idList . ')');

		$db->setQuery($query)->execute();

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__volunteertracker_events'))
			->where($db->quoteName('id') . ' IN (' . $idList . ')');

		$db->setQuery($query)->execute();
	}

	private function isDuplicateEvent(object $row): bool
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__volunteertracker_events'))
			->where($db->quoteName('event_name') . ' = ' . $db->quote($row->event_name))
			->where($db->quoteName('event_date') . ' = ' . $db->quote($row->event_date));

		if ($row->id) {
			$query->where($db->quoteName('id') . ' <> ' . (int) $row->id);
		}

		$db->setQuery($query);

		return (int) $db->loadResult() > 0;
	}

	private function syncEntries(object $row): void
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->update($db->quoteName('#__volunteertracker_entries'))
			->set($db->quoteName('event_name') . ' = ' . $db->quote($row->event_name))
			->set($db->quoteName('event_date') . ' = ' . $db->quote($row->event_date))
			->where($db->quoteName('event_id') . ' = ' . (int) $row->id);

		$db->setQuery($query)->execute();
	}
}
