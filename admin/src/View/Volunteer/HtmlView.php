<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\View\Volunteer;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

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

		ToolbarHelper::title($isNew ? Text::_('COM_VOLUNTEERTRACKER_NEW_RECORD') : Text::_('COM_VOLUNTEERTRACKER_EDIT_RECORD'), 'users');
		ToolbarHelper::apply('volunteer.apply');
		ToolbarHelper::save('volunteer.save');
		ToolbarHelper::cancel('volunteer.cancel');
	}

	private function loadAssets(): void
	{
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->useStyle('com_volunteertracker.admin');
	}
}
