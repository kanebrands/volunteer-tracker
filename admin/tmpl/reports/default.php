<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="vt-shell">
	<section class="vt-panel vt-report-panel">
		<header>
			<div>
				<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_REPORT_TITLE'); ?></h2>
				<p><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_REPORT_HELP'); ?></p>
			</div>
			<button class="btn btn-primary" type="button" id="vt-report-export">
				<span class="icon-download" aria-hidden="true"></span>
				<?php echo Text::_('COM_VOLUNTEERTRACKER_EXPORT_CSV'); ?>
			</button>
		</header>
		<form class="vt-report-filter" action="<?php echo Route::_('index.php'); ?>" method="get">
			<input type="hidden" name="option" value="com_volunteertracker">
			<input type="hidden" name="view" value="reports">
			<label>
				<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?></span>
				<select class="form-select" name="event_id">
					<?php foreach ($this->eventOptions as $event) : ?>
						<?php $eventLabel = $event->event_name . ' - ' . HTMLHelper::_('date', $event->event_date, Text::_('DATE_FORMAT_LC4')); ?>
						<option value="<?php echo (int) $event->id; ?>" <?php echo (int) $event->id === (int) $this->selectedEventId ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($eventLabel, ENT_QUOTES, 'UTF-8'); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
			<label>
				<span><?php echo Text::_('COM_VOLUNTEERTRACKER_REPORT_SORT'); ?></span>
				<select class="form-select" name="sort_by">
					<option value="role" <?php echo $this->sortBy === 'role' ? 'selected' : ''; ?>><?php echo Text::_('COM_VOLUNTEERTRACKER_REPORT_SORT_ROLE'); ?></option>
					<option value="name" <?php echo $this->sortBy === 'name' ? 'selected' : ''; ?>><?php echo Text::_('COM_VOLUNTEERTRACKER_REPORT_SORT_NAME'); ?></option>
				</select>
			</label>
			<button class="btn btn-primary" type="submit"><?php echo Text::_('COM_VOLUNTEERTRACKER_REPORT_APPLY'); ?></button>
		</form>
		<div class="table-responsive">
			<table class="table table-striped" id="vt-report-records" data-report-name="<?php echo htmlspecialchars($this->selectedEvent, ENT_QUOTES, 'UTF-8'); ?>">
				<thead>
					<tr>
						<th><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_NAME'); ?></th>
						<th><?php echo Text::_('COM_VOLUNTEERTRACKER_ROLE'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if (!$this->items) : ?>
						<tr>
							<td colspan="2" class="text-center"><?php echo Text::_('COM_VOLUNTEERTRACKER_REPORT_NO_ROWS'); ?></td>
						</tr>
					<?php endif; ?>
					<?php foreach ($this->items as $item) : ?>
						<tr>
							<td><?php echo htmlspecialchars($item->volunteer_name, ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?php echo htmlspecialchars($item->role, ENT_QUOTES, 'UTF-8'); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>
</div>
