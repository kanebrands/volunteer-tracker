<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$version = htmlspecialchars($this->extensionVersion, ENT_QUOTES, 'UTF-8');

$groupHours = static function (array $items, string $property): array {
	$groups = [];

	foreach ($items as $item) {
		$label = trim((string) ($item->{$property} ?? ''));
		$hours = (float) ($item->hours ?? 0);

		if ($label === '' || $hours <= 0) {
			continue;
		}

		$groups[$label] = ($groups[$label] ?? 0) + $hours;
	}

	arsort($groups, SORT_NUMERIC);

	return array_map(
		static fn ($label, $value): array => ['label' => $label, 'value' => $value],
		array_keys($groups),
		$groups
	);
};

$normaliseRows = static function (array $rows): array {
	return array_values(array_filter(array_map(
		static fn ($row): array => [
			'label' => trim((string) ($row['label'] ?? '')),
			'value' => (float) ($row['value'] ?? 0),
		],
		$rows
	), static fn ($row): bool => $row['label'] !== '' && $row['value'] > 0));
};

$eventRows = $normaliseRows($this->eventChart);
$volunteerRows = $normaliseRows($this->volunteerChart);
$eventUsesFallback = false;
$volunteerUsesFallback = false;

if (!$eventRows) {
	$eventRows = $groupHours($this->items, 'event_name');
	$eventUsesFallback = (bool) $eventRows;
}

if (!$volunteerRows) {
	$volunteerRows = $groupHours($this->items, 'volunteer_name');
	$volunteerUsesFallback = (bool) $volunteerRows;
}

$hasRecords = (bool) $this->items;
$eventChartJson = htmlspecialchars(json_encode($eventRows), ENT_QUOTES, 'UTF-8');
$volunteerChartJson = htmlspecialchars(json_encode($volunteerRows), ENT_QUOTES, 'UTF-8');
?>
<div>
	<div class="vt-shell">
		<section class="vt-app-panel">
			<div class="vt-brand">
				<div class="vt-logo" aria-hidden="true">
					<span></span>
					<span></span>
					<span></span>
				</div>
				<div>
					<h1><?php echo Text::_('COM_VOLUNTEERTRACKER'); ?></h1>
				</div>
			</div>
			<div class="vt-app-status">
				<div>
					<strong><?php echo Text::_('COM_VOLUNTEERTRACKER_VERSION_LABEL'); ?> <?php echo $version; ?></strong>
					<span>Kane Brands LLC</span>
					<a href="https://github.com/kanebrands" target="_blank" rel="noopener noreferrer"><?php echo Text::_('COM_VOLUNTEERTRACKER_GITHUB_LABEL'); ?></a>
				</div>
			</div>
		</section>

		<div class="vt-stat-grid">
			<div class="vt-stat">
				<div>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TOTAL_VOLUNTEERS'); ?></span>
					<strong><?php echo (int) $this->stats->volunteers; ?></strong>
				</div>
				<i class="vt-card-icon vt-card-icon-people" aria-hidden="true"></i>
			</div>
			<div class="vt-stat">
				<div>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TOTAL_EVENTS'); ?></span>
					<strong><?php echo (int) $this->stats->events; ?></strong>
				</div>
				<i class="vt-card-icon vt-card-icon-calendar" aria-hidden="true"></i>
			</div>
			<div class="vt-stat">
				<div>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TOTAL_HOURS'); ?></span>
					<strong><?php echo number_format((float) $this->stats->hours, 2); ?></strong>
				</div>
				<i class="vt-card-icon vt-card-icon-clock" aria-hidden="true"></i>
			</div>
			<div class="vt-stat">
				<div>
					<span><?php echo Text::_('COM_VOLUNTEERTRACKER_AVERAGE_HOURS'); ?></span>
					<strong><?php echo number_format((float) $this->stats->average_hours, 2); ?></strong>
				</div>
				<i class="vt-card-icon vt-card-icon-trend" aria-hidden="true"></i>
			</div>
		</div>

		<div class="vt-role-card-grid">
			<section class="vt-panel vt-role-card">
				<header>
					<div>
						<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_ROLE_PEOPLE_TITLE'); ?></h2>
						<p><?php echo Text::_('COM_VOLUNTEERTRACKER_ROLE_PEOPLE_HELP'); ?></p>
					</div>
				</header>
				<div class="vt-role-card-body">
					<label>
						<span><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?></span>
						<select class="form-select" id="vt-role-coverage-event"></select>
					</label>
					<div class="vt-horizontal-chart" id="vt-role-coverage-chart" aria-live="polite"></div>
				</div>
			</section>
			<section class="vt-panel vt-role-card">
				<header>
					<div>
						<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_ROLE_COUNT_TITLE'); ?></h2>
						<p><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_ROLE_COUNT_HELP'); ?></p>
					</div>
				</header>
				<div class="vt-role-card-body">
					<label>
						<span><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_NAME'); ?></span>
						<select class="form-select" id="vt-volunteer-role-volunteer"></select>
					</label>
					<div class="vt-horizontal-chart" id="vt-volunteer-role-chart" aria-live="polite"></div>
				</div>
			</section>
		</div>

		<div class="vt-chart-grid">
			<section class="vt-panel vt-insight-panel">
				<header>
					<div>
						<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS_BY_EVENT'); ?></h2>
						<p><?php echo Text::_('COM_VOLUNTEERTRACKER_TOP_EVENTS_HELP'); ?></p>
					</div>
					<label class="vt-chart-limit">
						<span><?php echo Text::_('COM_VOLUNTEERTRACKER_CHART_LIMIT_LABEL'); ?></span>
						<select class="form-select" data-chart-limit="event">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="all"><?php echo Text::_('COM_VOLUNTEERTRACKER_CHART_LIMIT_ALL'); ?></option>
						</select>
					</label>
				</header>
				<div class="vt-meter-list">
					<?php if (!$eventRows) : ?>
						<div class="vt-panel-message">
							<strong><?php echo Text::_($hasRecords ? 'COM_VOLUNTEERTRACKER_PANEL_UNAVAILABLE_TITLE' : 'COM_VOLUNTEERTRACKER_PANEL_EMPTY_TITLE'); ?></strong>
							<p><?php echo Text::_($hasRecords ? 'COM_VOLUNTEERTRACKER_EVENT_PANEL_UNAVAILABLE' : 'COM_VOLUNTEERTRACKER_EVENT_PANEL_EMPTY'); ?></p>
						</div>
					<?php else : ?>
						<canvas class="vt-chart" width="640" height="280" data-chart="bar" data-values="<?php echo $eventChartJson; ?>"></canvas>
						<div class="vt-chart-fallback"><?php echo Text::_('COM_VOLUNTEERTRACKER_CHART_DRAW_FAILED'); ?></div>
					<?php endif; ?>
					<?php if ($eventUsesFallback) : ?>
						<div class="vt-panel-alert">
							<?php echo Text::_('COM_VOLUNTEERTRACKER_PANEL_FALLBACK_NOTICE'); ?>
						</div>
					<?php endif; ?>
				</div>
			</section>
			<section class="vt-panel vt-insight-panel">
				<header>
					<div>
						<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS_BY_VOLUNTEER'); ?></h2>
						<p><?php echo Text::_('COM_VOLUNTEERTRACKER_TOP_VOLUNTEERS_HELP'); ?></p>
					</div>
					<label class="vt-chart-limit">
						<span><?php echo Text::_('COM_VOLUNTEERTRACKER_CHART_LIMIT_LABEL'); ?></span>
						<select class="form-select" data-chart-limit="volunteer">
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="all"><?php echo Text::_('COM_VOLUNTEERTRACKER_CHART_LIMIT_ALL'); ?></option>
						</select>
					</label>
				</header>
				<div class="vt-meter-list vt-meter-list-alt">
					<?php if (!$volunteerRows) : ?>
						<div class="vt-panel-message">
							<strong><?php echo Text::_($hasRecords ? 'COM_VOLUNTEERTRACKER_PANEL_UNAVAILABLE_TITLE' : 'COM_VOLUNTEERTRACKER_PANEL_EMPTY_TITLE'); ?></strong>
							<p><?php echo Text::_($hasRecords ? 'COM_VOLUNTEERTRACKER_VOLUNTEER_PANEL_UNAVAILABLE' : 'COM_VOLUNTEERTRACKER_VOLUNTEER_PANEL_EMPTY'); ?></p>
						</div>
					<?php else : ?>
						<canvas class="vt-chart" width="640" height="280" data-chart="pie" data-values="<?php echo $volunteerChartJson; ?>"></canvas>
						<div class="vt-chart-fallback"><?php echo Text::_('COM_VOLUNTEERTRACKER_CHART_DRAW_FAILED'); ?></div>
					<?php endif; ?>
					<?php if ($volunteerUsesFallback) : ?>
						<div class="vt-panel-alert">
							<?php echo Text::_('COM_VOLUNTEERTRACKER_PANEL_FALLBACK_NOTICE'); ?>
						</div>
					<?php endif; ?>
				</div>
			</section>
		</div>

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
						<?php foreach ($this->items as $i => $item) : ?>
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

		<section class="vt-panel vt-dashboard-controls">
			<label class="vt-check">
				<input type="checkbox" id="vt-include-archived">
				<span><?php echo Text::_('COM_VOLUNTEERTRACKER_INCLUDE_ARCHIVED_EVENTS'); ?></span>
			</label>
		</section>
	</div>
</div>
