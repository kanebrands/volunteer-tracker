<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\View\Volunteer;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
	protected object $item;
	protected array $volunteerOptions = [];
	protected array $eventOptions = [];
	protected array $roleOptions = [];
	protected array $activeAssignments = [];

	public function display($tpl = null): void
	{
		$this->item             = $this->get('Item');
		$this->volunteerOptions = $this->get('VolunteerOptions');
		$this->eventOptions     = $this->get('EventOptions');
		$this->roleOptions      = $this->get('RoleOptions');
		$this->activeAssignments = $this->get('ActiveAssignments');
		$this->addToolbar();
		$this->loadAssets();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		$isNew = empty($this->item->id);

		ToolbarHelper::title($isNew ? Text::_('COM_VOLUNTEERTRACKER_NEW_RECORD') : Text::_('COM_VOLUNTEERTRACKER_EDIT_RECORD'), 'users');
		ToolbarHelper::apply('volunteer.apply');
		ToolbarHelper::save('volunteer.save');
		ToolbarHelper::cancel('volunteer.cancel');
	}

	private function loadAssets(): void
	{
		$base = Uri::root(true) . '/media/com_volunteertracker';

		$this->getDocument()->addCustomTag('<link rel="stylesheet" href="' . $base . '/css/admin.css?v=1.0.20">');
	}
}
