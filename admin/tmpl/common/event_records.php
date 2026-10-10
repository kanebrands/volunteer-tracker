<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<section class="vt-panel vt-table-panel">
	<header>
		<div>
			<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_RECORDS'); ?></h2>
			<p><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_RECORDS_HELP'); ?></p>
		</div>
		<button class="btn btn-primary" type="button" id="vt-event-export">
			<span class="icon-download" aria-hidden="true"></span>
			<?php echo Text::_('COM_VOLUNTEERTRACKER_EXPORT_CSV'); ?>
		</button>
	</header>
	<div class="vt-table-controls">
		<label>
			<span><?php echo Text::_('COM_VOLUNTEERTRACKER_FILTER_START_DATE'); ?></span>
			<input class="form-control" type="date" id="vt-event-start-date">
		</label>
		<label>
			<span><?php echo Text::_('COM_VOLUNTEERTRACKER_FILTER_END_DATE'); ?></span>
			<input class="form-control" type="date" id="vt-event-end-date">
		</label>
		<label>
			<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TABLE_LIMIT'); ?></span>
			<select class="form-select" id="vt-event-page-size">
				<option value="10">10</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="all"><?php echo Text::_('COM_VOLUNTEERTRACKER_FILTER_ALL'); ?></option>
			</select>
		</label>
	</div>
	<div class="table-responsive">
		<table class="table table-striped" id="vt-event-records">
			<thead>
				<tr>
					<th><button type="button" class="vt-sort" data-sort-index="0"><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?><span aria-hidden="true"></span></button></th>
					<th><button type="button" class="vt-sort" data-sort-index="1" data-sort-type="date"><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_DATE'); ?><span aria-hidden="true"></span></button></th>
					<th><button type="button" class="vt-sort" data-sort-index="2"><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_LOCATION'); ?><span aria-hidden="true"></span></button></th>
					<th><button type="button" class="vt-sort" data-sort-index="3"><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_STATUS'); ?><span aria-hidden="true"></span></button></th>
					<th class="text-end"><button type="button" class="vt-sort" data-sort-index="4" data-sort-type="number"><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_ENTRIES'); ?><span aria-hidden="true"></span></button></th>
					<th class="text-end"><button type="button" class="vt-sort" data-sort-index="5" data-sort-type="number"><?php echo Text::_('COM_VOLUNTEERTRACKER_TOTAL_HOURS'); ?><span aria-hidden="true"></span></button></th>
					<th class="text-end"><?php echo Text::_('COM_VOLUNTEERTRACKER_ACTIONS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (!$this->events) : ?>
					<tr>
						<td colspan="7" class="text-center"><?php echo Text::_('COM_VOLUNTEERTRACKER_NO_EVENT_RECORDS'); ?></td>
					</tr>
				<?php endif; ?>
				<?php foreach ($this->events as $event) : ?>
					<tr data-event-date="<?php echo htmlspecialchars((string) $event->event_date, ENT_QUOTES, 'UTF-8'); ?>" data-event-archived="<?php echo !empty($event->is_archived) ? '1' : '0'; ?>">
						<td>
							<a href="<?php echo Route::_('index.php?option=com_volunteertracker&task=event.edit&id=' . (int) $event->id); ?>">
								<?php echo htmlspecialchars($event->event_name, ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</td>
						<td data-sort-value="<?php echo htmlspecialchars((string) $event->event_date, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $event->event_date ? HTMLHelper::_('date', $event->event_date, Text::_('DATE_FORMAT_LC4')) : ''; ?></td>
						<td><?php echo htmlspecialchars((string) $event->event_location, ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo Text::_(!empty($event->is_archived) ? 'COM_VOLUNTEERTRACKER_EVENT_ARCHIVED_STATUS' : 'COM_VOLUNTEERTRACKER_EVENT_ACTIVE_STATUS'); ?></td>
						<td class="text-end" data-sort-value="<?php echo (int) $event->volunteer_entries; ?>"><?php echo (int) $event->volunteer_entries; ?></td>
						<td class="text-end" data-sort-value="<?php echo htmlspecialchars((string) (float) $event->total_hours, ENT_QUOTES, 'UTF-8'); ?>"><?php echo number_format((float) $event->total_hours, 2); ?></td>
						<td class="text-end">
							<div class="vt-row-actions">
								<a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_volunteertracker&task=event.edit&id=' . (int) $event->id); ?>">
									<?php echo Text::_('COM_VOLUNTEERTRACKER_EDIT'); ?>
								</a>
								<form action="<?php echo Route::_('index.php?option=com_volunteertracker&task=event.delete'); ?>" method="post" onsubmit="return confirm('<?php echo htmlspecialchars(Text::_('COM_VOLUNTEERTRACKER_CONFIRM_EVENT_DELETE'), ENT_QUOTES, 'UTF-8'); ?>');">
									<input type="hidden" name="cid[]" value="<?php echo (int) $event->id; ?>">
									<?php echo HTMLHelper::_('form.token'); ?>
									<button class="btn btn-sm btn-outline-danger" type="submit">
										<?php echo Text::_('COM_VOLUNTEERTRACKER_DELETE'); ?>
									</button>
								</form>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="vt-pagination" id="vt-event-pagination" aria-live="polite"></div>
</section>
