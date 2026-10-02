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
					<input class="form-control" type="text" name="jform[volunteer_name]" value="<?php echo htmlspecialchars($this->item->volunteer_name, ENT_QUOTES, 'UTF-8'); ?>" list="vt-volunteer-options" autocomplete="off" required>
				</label>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?></span>
					<select class="form-select" name="jform[event_id]" required>
						<option value=""><?php echo Text::_('COM_VOLUNTEERTRACKER_SELECT_EVENT'); ?></option>
						<?php foreach ($this->eventOptions as $event) : ?>
							<?php $eventLabel = $event->event_name . ' - ' . HTMLHelper::_('date', $event->event_date, Text::_('DATE_FORMAT_LC4')); ?>
							<option value="<?php echo (int) $event->id; ?>" <?php echo (int) $event->id === (int) $this->item->event_id ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($eventLabel, ENT_QUOTES, 'UTF-8'); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS'); ?></span>
					<input class="form-control" type="number" name="jform[hours]" value="<?php echo htmlspecialchars((string) $this->item->hours, ENT_QUOTES, 'UTF-8'); ?>" min="0" step="0.25" placeholder="0.0">
				</label>
				<label>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_ROLE'); ?></span>
					<input class="form-control" type="text" name="jform[role]" value="<?php echo htmlspecialchars((string) $this->item->role, ENT_QUOTES, 'UTF-8'); ?>" list="vt-role-options" autocomplete="off" required>
				</label>
			</div>
			<datalist id="vt-volunteer-options">
				<?php foreach ($this->volunteerOptions as $option) : ?>
					<option value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"></option>
				<?php endforeach; ?>
			</datalist>
			<datalist id="vt-role-options">
				<?php foreach ($this->roleOptions as $option) : ?>
					<option value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"></option>
				<?php endforeach; ?>
			</datalist>
		</section>
	</div>

	<input type="hidden" name="jform[id]" value="<?php echo (int) $this->item->id; ?>">
	<input type="hidden" name="task" value="volunteer.save">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
