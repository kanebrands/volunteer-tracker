<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class EventController extends BaseController
{
	public function add(): void
	{
		$this->setRedirect(Route::_('index.php?option=com_volunteertracker&view=event&layout=edit', false));
	}

	public function edit(): void
	{
		$id = Factory::getApplication()->input->getInt('id');
		$this->setRedirect(Route::_('index.php?option=com_volunteertracker&view=event&layout=edit&id=' . $id, false));
	}

	public function cancel(): void
	{
		$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));
	}

	public function save(): void
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		if (!$user->authorise('core.edit', 'com_volunteertracker') && !$user->authorise('core.create', 'com_volunteertracker')) {
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));

			return;
		}

		$model = $this->getModel('Event');
		$data  = $app->input->post->get('jform', [], 'array');
		$id    = $model->save($data);

		if (!$id) {
			$app->enqueueMessage(Text::_('COM_VOLUNTEERTRACKER_EVENT_SAVE_FAILED'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_volunteertracker&view=event&layout=edit&id=' . (int) ($data['id'] ?? 0), false));

			return;
		}

		$app->enqueueMessage(Text::_('COM_VOLUNTEERTRACKER_EVENT_SAVE_SUCCESS'), 'message');

		if ($this->getTask() === 'apply') {
			$this->setRedirect(Route::_('index.php?option=com_volunteertracker&view=event&layout=edit&id=' . (int) $id, false));

			return;
		}

		$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));
	}

	public function apply(): void
	{
		$this->save();
	}

	public function delete(): void
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$app = Factory::getApplication();

		if (!$app->getIdentity()->authorise('core.delete', 'com_volunteertracker')) {
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));

			return;
		}

		$ids = $app->input->get('cid', [], 'array');
		$ids = array_values(array_filter(array_map('intval', $ids)));

		if (!$ids) {
			$app->enqueueMessage(Text::_('COM_VOLUNTEERTRACKER_NO_ROWS_SELECTED'), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));

			return;
		}

		$this->getModel('Event')->delete($ids);
		$app->enqueueMessage(Text::plural('COM_VOLUNTEERTRACKER_EVENTS_DELETED', count($ids)), 'message');
		$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));
	}
}
