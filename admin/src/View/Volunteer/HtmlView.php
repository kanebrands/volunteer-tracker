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
	protected array $items = [];
	protected array $events = [];
	protected object $configuration;
	protected string $extensionVersion = '1.0.22';

	public function display($tpl = null): void
	{
		$this->item             = $this->get('Item');
		$this->volunteerOptions = $this->get('VolunteerOptions');
		$this->eventOptions     = $this->get('EventOptions');
		$this->roleOptions      = $this->get('RoleOptions');
		$this->activeAssignments = $this->get('ActiveAssignments');
		$this->items            = $this->get('Items');
		$this->events           = $this->get('Events');
		$this->configuration    = $this->get('Configuration');
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
		$version = rawurlencode($this->extensionVersion);
		$document = $this->getDocument();

		$document->addCustomTag('<link rel="stylesheet" href="' . $base . '/css/admin.css?v=' . $version . '">');
		$document->addCustomTag('<script src="' . $base . '/js/dashboard.js?v=' . $version . '" defer></script>');
	}
}
