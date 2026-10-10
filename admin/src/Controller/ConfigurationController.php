<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class ConfigurationController extends BaseController
{
	public function save(): void
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$app = Factory::getApplication();

		if (!$app->getIdentity()->authorise('core.manage', 'com_volunteertracker')) {
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));

			return;
		}

		$data = $app->input->post->get('jform', [], 'array');
		$this->getModel('Configuration')->save($data);
		$app->enqueueMessage(Text::_('COM_VOLUNTEERTRACKER_CONFIGURATION_SAVE_SUCCESS'), 'message');

		if ($this->getTask() === 'apply') {
			$this->setRedirect(Route::_('index.php?option=com_volunteertracker&view=configuration', false));

			return;
		}

		$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));
	}

	public function apply(): void
	{
		$this->save();
	}

	public function cancel(): void
	{
		$this->setRedirect(Route::_('index.php?option=com_volunteertracker', false));
	}
}
