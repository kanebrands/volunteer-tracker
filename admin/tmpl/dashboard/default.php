<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$token = HTMLHelper::_('form.token');
$eventChart = htmlspecialchars(json_encode($this->eventChart), ENT_QUOTES, 'UTF-8');
$volunteerChart = htmlspecialchars(json_encode($this->volunteerChart), ENT_QUOTES, 'UTF-8');
?>
<form action="<?php echo Route::_('index.php?option=com_volunteertracker'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="vt-shell">
		<div class="vt-hero">
			<div>
				<p class="vt-eyebrow"><?php echo Text::_('COM_VOLUNTEERTRACKER_ADMIN_ONLY'); ?></p>
				<h1><?php echo Text::_('COM_VOLUNTEERTRACKER_DASHBOARD_TITLE'); ?></h1>
			</div>
			<button class="btn btn-primary" type="button" id="vt-export">
				<span class="icon-download" aria-hidden="true"></span>
				<?php echo Text::_('COM_VOLUNTEERTRACKER_EXPORT_CSV'); ?>
			</button>
		</div>

		<div class="vt-stat-grid">
			<div class="vt-stat">
				<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TOTAL_VOLUNTEERS'); ?></span>
				<strong><?php echo (int) $this->stats->volunteers; ?></strong>
			</div>
			<div class="vt-stat">
				<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TOTAL_EVENTS'); ?></span>
				<strong><?php echo (int) $this->stats->events; ?></strong>
			</div>
			<div class="vt-stat">
				<span><?php echo Text::_('COM_VOLUNTEERTRACKER_TOTAL_HOURS'); ?></span>
				<strong><?php echo number_format((float) $this->stats->hours, 2); ?></strong>
			</div>
			<div class="vt-stat">
				<span><?php echo Text::_('COM_VOLUNTEERTRACKER_AVERAGE_HOURS'); ?></span>
				<strong><?php echo number_format((float) $this->stats->average_hours, 2); ?></strong>
			</div>
		</div>

		<div class="vt-chart-grid">
			<section class="vt-panel">
				<header>
					<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS_BY_EVENT'); ?></h2>
				</header>
				<canvas class="vt-chart" width="640" height="260" data-chart="bar" data-values="<?php echo $eventChart; ?>"></canvas>
			</section>
			<section class="vt-panel">
				<header>
					<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS_BY_VOLUNTEER'); ?></h2>
				</header>
				<canvas class="vt-chart" width="640" height="260" data-chart="pie" data-values="<?php echo $volunteerChart; ?>"></canvas>
			</section>
		</div>

		<section class="vt-panel vt-table-panel">
			<header>
				<div>
					<h2><?php echo Text::_('COM_VOLUNTEERTRACKER_RECORDS'); ?></h2>
					<p><?php echo Text::_('COM_VOLUNTEERTRACKER_RECORDS_HELP'); ?></p>
				</div>
			</header>
			<div class="table-responsive">
				<table class="table table-striped" id="vt-records">
					<thead>
						<tr>
							<th class="w-1 text-center"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
							<th><?php echo Text::_('COM_VOLUNTEERTRACKER_VOLUNTEER_NAME'); ?></th>
							<th><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_NAME'); ?></th>
							<th><?php echo Text::_('COM_VOLUNTEERTRACKER_EVENT_DATE'); ?></th>
							<th class="text-end"><?php echo Text::_('COM_VOLUNTEERTRACKER_HOURS'); ?></th>
							<th><?php echo Text::_('COM_VOLUNTEERTRACKER_NOTES'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if (!$this->items) : ?>
							<tr>
								<td colspan="6" class="text-center"><?php echo Text::_('COM_VOLUNTEERTRACKER_NO_RECORDS'); ?></td>
							</tr>
						<?php endif; ?>
						<?php foreach ($this->items as $i => $item) : ?>
							<tr>
								<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, (int) $item->id); ?></td>
								<td>
									<a href="<?php echo Route::_('index.php?option=com_volunteertracker&task=volunteer.edit&id=' . (int) $item->id); ?>">
										<?php echo htmlspecialchars($item->volunteer_name, ENT_QUOTES, 'UTF-8'); ?>
									</a>
								</td>
								<td><?php echo htmlspecialchars($item->event_name, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo $item->event_date ? HTMLHelper::_('date', $item->event_date, Text::_('DATE_FORMAT_LC4')) : ''; ?></td>
								<td class="text-end"><?php echo number_format((float) $item->hours, 2); ?></td>
								<td><?php echo nl2br(htmlspecialchars($item->notes, ENT_QUOTES, 'UTF-8')); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>
	</div>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo $token; ?>
</form>
