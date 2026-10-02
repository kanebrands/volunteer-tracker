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
			'event_id' => 0,
			'volunteer_name' => '',
			'event_name' => '',
			'event_date' => '',
			'hours' => '',
			'role' => '',
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
			'event_id' => (int) ($data['event_id'] ?? 0),
			'volunteer_name' => trim((string) ($data['volunteer_name'] ?? '')),
			'hours' => max(0, (float) ($data['hours'] ?? 0)),
			'role' => trim((string) ($data['role'] ?? '')),
			'modified' => $now,
			'modified_by' => (int) $app->getIdentity()->id,
		];
		$event = $this->getEventById($row->event_id);

		if ($row->volunteer_name === '' || !$event || $row->role === '') {
			return 0;
		}

		$row->event_name = $event->event_name;
		$row->event_date = $event->event_date;

		if ($row->id) {
			$db->updateObject('#__volunteertracker_entries', $row, 'id');

			return $row->id;
		}

		$row->created = $now;
		$row->created_by = (int) $app->getIdentity()->id;
		$db->insertObject('#__volunteertracker_entries', $row);

		return (int) $db->insertid();
	}

	public function getVolunteerOptions(): array
	{
		return $this->getDistinctOptions('volunteer_name');
	}

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

	public function getRoleOptions(): array
	{
		return $this->getDistinctOptions('role');
	}

	public function delete(array $ids): void
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->delete($db->quoteName('#__volunteertracker_entries'))
			->where($db->quoteName('id') . ' IN (' . implode(',', array_map('intval', $ids)) . ')');

		$db->setQuery($query)->execute();
	}

	private function getDistinctOptions(string $column): array
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select('DISTINCT ' . $db->quoteName($column))
			->from($db->quoteName('#__volunteertracker_entries'))
			->where($db->quoteName($column) . ' <> ' . $db->quote(''))
			->order($db->quoteName($column) . ' ASC');

		$db->setQuery($query);

		return array_values(array_filter(array_map('strval', $db->loadColumn() ?: [])));
	}

	private function getEventById(int $id): ?object
	{
		if (!$id) {
			return null;
		}

		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('event_name'),
				$db->quoteName('event_date'),
			])
			->from($db->quoteName('#__volunteertracker_events'))
			->where($db->quoteName('id') . ' = ' . (int) $id);

		$db->setQuery($query);
		$event = $db->loadObject();

		return $event ?: null;
	}
}
