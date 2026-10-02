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

	public function display($tpl = null): void
	{
		$this->item = $this->get('Item');
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

		$this->getDocument()->addCustomTag('<link rel="stylesheet" href="' . $base . '/css/admin.css?v=1.0.18">');
	}
}
