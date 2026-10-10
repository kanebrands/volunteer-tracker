<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\View\Configuration;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
	protected object $configuration;
	protected string $extensionVersion = '1.0.22';

	public function display($tpl = null): void
	{
		$this->configuration = $this->get('Configuration');
		$this->addToolbar();
		$this->loadAssets();

		parent::display($tpl);
	}

	private function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_VOLUNTEERTRACKER_CONFIGURATION'), 'options');
		ToolbarHelper::apply('configuration.apply');
		ToolbarHelper::save('configuration.save');
		ToolbarHelper::cancel('configuration.cancel');
	}

	private function loadAssets(): void
	{
		$base = Uri::root(true) . '/media/com_volunteertracker';
		$version = rawurlencode($this->extensionVersion);

		$this->getDocument()->addCustomTag('<link rel="stylesheet" href="' . $base . '/css/admin.css?v=' . $version . '">');
	}
}
