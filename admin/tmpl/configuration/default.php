<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_volunteertracker&task=configuration.save'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="vt-shell vt-edit">
		<section class="vt-panel vt-configuration-panel">
			<header>
				<div>
					<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_CONFIGURATION'); ?></h2>
					<p><?php echo Text::_('COM_VOLUNTEERTRACKER_CONFIGURATION_HELP'); ?></p>
				</div>
			</header>
			<div class="vt-form-grid">
				<label class="vt-check vt-form-wide">
					<input type="checkbox" name="jform[include_archived_dashboard]" value="1" <?php echo !empty($this->configuration->include_archived_dashboard) ? 'checked' : ''; ?>>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_INCLUDE_ARCHIVED_EVENTS'); ?></span>
				</label>
				<p class="vt-field-help vt-form-wide"><?php echo Text::_('COM_VOLUNTEERTRACKER_INCLUDE_ARCHIVED_EVENTS_HELP'); ?></p>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_ARCHIVE_DELAY_DAYS'); ?></span>
					<input class="form-control" type="number" min="0" step="1" name="jform[archive_delay_days]" value="<?php echo (int) $this->configuration->archive_delay_days; ?>">
				</label>
				<p class="vt-field-help vt-form-wide"><?php echo Text::_('COM_VOLUNTEERTRACKER_ARCHIVE_DELAY_DAYS_HELP'); ?></p>
			</div>
		</section>
	</div>

	<input type="hidden" name="task" value="configuration.save">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
