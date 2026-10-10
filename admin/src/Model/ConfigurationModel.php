<?php

namespace VolunteerTracker\Component\VolunteerTracker\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use VolunteerTracker\Component\VolunteerTracker\Administrator\Helper\ConfigurationHelper;

class ConfigurationModel extends BaseDatabaseModel
{
	public function getConfiguration(): object
	{
		return ConfigurationHelper::getConfiguration($this->getDatabase());
	}

	public function save(array $data): void
	{
		ConfigurationHelper::saveConfiguration($data, $this->getDatabase());
		ConfigurationHelper::applyAutomaticArchiving($this->getDatabase());
	}
}
