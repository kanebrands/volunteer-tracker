<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<section class="vt-panel vt-table-panel">
	<header>
		<div>
			<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_RECORDS'); ?></h2>
			<p><?php echo Text::_('COM_VOLUNTEERTRACKER_RECORDS_HELP'); ?></p>
		</div>
		<button class="btn btn-primary" type="button" id="vt-export">
			<span class="icon-download" aria-hidden="true"></span>
			<?php echo Text::_('COM_VOLUNTEERTRACKER_EXPORT_CSV'); ?>
		</button>
	</header>
	<div class="vt-table-controls">
		<label>
			<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?></span>
			<select class="form-select" id="vt-record-event-filter">
				<option value="all"><?php echo Text::_('COM_VOLUNTEERTRACKER_FILTER_ALL'); ?></option>
				<?php foreach ($this->events as $event) : ?>
					<?php $eventLabel = $event->event_name . ($event->event_date ? ' - ' . HTMLHelper::_('date', $event->event_date, Text::_('DATE_FORMAT_LC4')) : ''); ?>
					<option value="<?php echo (int) $event->id; ?>">
						<?php echo htmlspecialchars($eventLabel, ENT_QUOTES, 'UTF-8'); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
		<label>
			<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TABLE_LIMIT'); ?></span>
			<select class="form-select" id="vt-record-page-size">
				<option value="10">10</option>
				<option value="50" selected>50</option>
				<option value="100">100</option>
				<option value="all"><?php echo Text::_('COM_VOLUNTEERTRACKER_FILTER_ALL'); ?></option>
			</select>
		</label>
	</div>
	<div class="table-responsive">
		<table class="table table-striped" id="vt-records">
			<thead>
				<tr>
					<th><button type="button" class="vt-sort" data-sort-index="0"><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_NAME'); ?><span aria-hidden="true"></span></button></th>
					<th><button type="button" class="vt-sort" data-sort-index="1"><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?><span aria-hidden="true"></span></button></th>
					<th><button type="button" class="vt-sort" data-sort-index="2" data-sort-type="date"><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_DATE'); ?><span aria-hidden="true"></span></button></th>
					<th class="text-end"><button type="button" class="vt-sort" data-sort-index="3" data-sort-type="number"><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS'); ?><span aria-hidden="true"></span></button></th>
					<th><button type="button" class="vt-sort" data-sort-index="4"><?php echo Text::_('COM_VOLUNTEERTRACKER_ROLE'); ?><span aria-hidden="true"></span></button></th>
					<th class="text-end"><?php echo Text::_('COM_VOLUNTEERTRACKER_ACTIONS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (!$this->items) : ?>
					<tr>
						<td colspan="6" class="text-center"><?php echo Text::_('COM_VOLUNTEERTRACKER_NO_RECORDS'); ?></td>
					</tr>
				<?php endif; ?>
				<?php foreach ($this->items as $item) : ?>
					<tr data-event-id="<?php echo (int) $item->event_id; ?>" data-event-archived="<?php echo !empty($item->event_archived) ? '1' : '0'; ?>">
						<td>
							<a href="<?php echo Route::_('index.php?option=com_volunteertracker&task=volunteer.edit&id=' . (int) $item->id); ?>">
								<?php echo htmlspecialchars($item->volunteer_name, ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</td>
						<td><?php echo htmlspecialchars($item->event_name, ENT_QUOTES, 'UTF-8'); ?></td>
						<td data-sort-value="<?php echo htmlspecialchars((string) $item->event_date, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $item->event_date ? HTMLHelper::_('date', $item->event_date, Text::_('DATE_FORMAT_LC4')) : ''; ?></td>
						<td class="text-end" data-sort-value="<?php echo htmlspecialchars((string) (float) $item->hours, ENT_QUOTES, 'UTF-8'); ?>"><?php echo number_format((float) $item->hours, 2); ?></td>
						<td><?php echo htmlspecialchars((string) $item->role, ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="text-end">
							<div class="vt-row-actions">
								<a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_volunteertracker&task=volunteer.edit&id=' . (int) $item->id); ?>">
									<?php echo Text::_('COM_VOLUNTEERTRACKER_EDIT'); ?>
								</a>
								<form action="<?php echo Route::_('index.php?option=com_volunteertracker&task=volunteer.delete'); ?>" method="post" onsubmit="return confirm('<?php echo htmlspecialchars(Text::_('COM_VOLUNTEERTRACKER_CONFIRM_DELETE'), ENT_QUOTES, 'UTF-8'); ?>');">
									<input type="hidden" name="cid[]" value="<?php echo (int) $item->id; ?>">
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
	<div class="vt-pagination" id="vt-record-pagination" aria-live="polite"></div>
</section>
