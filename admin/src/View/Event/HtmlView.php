<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\View\Event;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
	protected object $item;
	protected array $events = [];
	protected object $configuration;
	protected string $extensionVersion = '1.0.22';

	public function display($tpl = null): void
	{
		$this->item = $this->get('Item');
		$this->events = $this->get('Events');
		$this->configuration = $this->get('Configuration');
		$this->addToolbar();
		$this->loadAssets();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		$isNew = empty($this->item->id);

		ToolbarHelper::title($isNew ? Text::_('COM_VOLUNTEERTRACKER_NEW_EVENT') : Text::_('COM_VOLUNTEERTRACKER_EDIT_EVENT'), 'calendar');
		ToolbarHelper::apply('event.apply');
		ToolbarHelper::save('event.save');
		ToolbarHelper::cancel('event.cancel');
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
