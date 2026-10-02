<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected array $items = [];
	protected object $stats;
	protected array $eventChart = [];
	protected array $volunteerChart = [];

	public function display($tpl = null): void
	{
		$this->items          = $this->get('Items');
		$this->stats          = $this->get('Stats');
		$this->eventChart     = $this->get('EventChart');
		$this->volunteerChart = $this->get('VolunteerChart');

		$this->addToolbar();
		$this->loadAssets();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		$user = Factory::getApplication()->getIdentity();

		ToolbarHelper::title(Text::_('COM_VOLUNTEERTRACKER_DASHBOARD'), 'users');

		if ($user->authorise('core.create', 'com_volunteertracker')) {
			ToolbarHelper::addNew('volunteer.add');
		}

		if ($user->authorise('core.delete', 'com_volunteertracker')) {
			ToolbarHelper::deleteList(Text::_('COM_VOLUNTEERTRACKER_CONFIRM_DELETE'), 'volunteer.delete');
		}
	}

	private function loadAssets(): void
	{
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->useStyle('com_volunteertracker.admin');
		$wa->useScript('com_volunteertracker.dashboard');
	}
}
