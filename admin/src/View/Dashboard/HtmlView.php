<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
	protected array $items = [];
	protected array $events = [];
	protected object $stats;
	protected array $eventChart = [];
	protected array $volunteerChart = [];
	protected string $extensionVersion = '1.0.14';

	public function display($tpl = null): void
	{
		$this->items          = $this->get('Items');
		$this->events         = $this->get('Events');
		$this->stats          = $this->get('Stats');
		$this->eventChart     = $this->get('EventChart');
		$this->volunteerChart = $this->get('VolunteerChart');

		$this->addToolbar();
		$this->loadAssets();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_VOLUNTEERTRACKER_DASHBOARD'), 'users');
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
