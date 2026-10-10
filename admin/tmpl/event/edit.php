<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_volunteertracker&task=event.save'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="vt-shell vt-edit">
		<section class="vt-panel">
			<div class="vt-form-grid">
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?></span>
					<input class="form-control" type="text" name="jform[event_name]" value="<?php echo htmlspecialchars($this->item->event_name, ENT_QUOTES, 'UTF-8'); ?>" required>
				</label>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_DATE'); ?></span>
					<input class="form-control" type="date" name="jform[event_date]" value="<?php echo htmlspecialchars((string) $this->item->event_date, ENT_QUOTES, 'UTF-8'); ?>" required>
				</label>
				<label class="vt-form-wide">
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_LOCATION'); ?></span>
					<input class="form-control" type="text" name="jform[event_location]" value="<?php echo htmlspecialchars((string) $this->item->event_location, ENT_QUOTES, 'UTF-8'); ?>">
				</label>
				<label class="vt-check">
					<input type="checkbox" name="jform[is_archived]" value="1" <?php echo !empty($this->item->is_archived) ? 'checked' : ''; ?>>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_ARCHIVED'); ?></span>
				</label>
			</div>
		</section>
	</div>

	<input type="hidden" name="jform[id]" value="<?php echo (int) $this->item->id; ?>">
	<input type="hidden" name="task" value="event.save">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="vt-shell vt-record-shell" data-include-archived="<?php echo !empty($this->configuration->include_archived_dashboard) ? '1' : '0'; ?>">
	<?php include JPATH_COMPONENT_ADMINISTRATOR . '/tmpl/common/event_records.php'; ?>
</div>
