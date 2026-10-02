<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class DisplayController extends BaseController
{
	protected $default_view = 'dashboard';

	public function display($cachable = false, $urlparams = []): self
	{
		if (!Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_volunteertracker')) {
			Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$this->setRedirect(Route::_('index.php', false));

			return $this;
		}

		return parent::display($cachable, $urlparams);
	}
}
