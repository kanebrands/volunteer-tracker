<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_volunteertracker&task=volunteer.save'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="vt-shell vt-edit">
		<section class="vt-panel">
			<div class="vt-form-grid">
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_NAME'); ?></span>
					<input class="form-control" type="text" name="jform[volunteer_name]" value="<?php echo htmlspecialchars($this->item->volunteer_name, ENT_QUOTES, 'UTF-8'); ?>" required>
				</label>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?></span>
					<input class="form-control" type="text" name="jform[event_name]" value="<?php echo htmlspecialchars($this->item->event_name, ENT_QUOTES, 'UTF-8'); ?>" required>
				</label>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_DATE'); ?></span>
					<input class="form-control" type="date" name="jform[event_date]" value="<?php echo htmlspecialchars((string) $this->item->event_date, ENT_QUOTES, 'UTF-8'); ?>">
				</label>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS'); ?></span>
					<input class="form-control" type="number" name="jform[hours]" value="<?php echo htmlspecialchars((string) $this->item->hours, ENT_QUOTES, 'UTF-8'); ?>" min="0.01" step="0.25" required>
				</label>
				<label class="vt-full">
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_NOTES'); ?></span>
					<textarea class="form-control" name="jform[notes]" rows="5"><?php echo htmlspecialchars($this->item->notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
				</label>
			</div>
		</section>
	</div>

	<input type="hidden" name="jform[id]" value="<?php echo (int) $this->item->id; ?>">
	<input type="hidden" name="task" value="volunteer.save">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
