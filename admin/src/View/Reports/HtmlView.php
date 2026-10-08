<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\View\Reports;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
	protected array $items = [];
	protected array $eventOptions = [];
	protected string $selectedEvent = '';
	protected int $selectedEventId = 0;
	protected string $sortBy = 'role';

	public function display($tpl = null): void
	{
		$this->items           = $this->get('Items');
		$this->eventOptions    = $this->get('EventOptions');
		$this->selectedEvent   = $this->get('SelectedEvent');
		$this->selectedEventId = $this->get('SelectedEventId');
		$this->sortBy          = $this->get('SortBy');

		$this->addToolbar();
		$this->loadAssets();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_VOLUNTEERTRACKER_REPORTS'), 'users');
	}

	private function loadAssets(): void
	{
		$base = Uri::root(true) . '/media/com_volunteertracker';

		$this->getDocument()->addCustomTag('<link rel="stylesheet" href="' . $base . '/css/admin.css?v=1.0.21">');
		$this->getDocument()->addCustomTag('<script src="' . $base . '/js/dashboard.js?v=1.0.21" defer></script>');
	}
}
