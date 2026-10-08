<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$activeAssignments = array_map(static function ($assignment) {
	$eventDate = (string) ($assignment->event_date ?? '');

	return [
		'id' => (int) $assignment->id,
		'eventId' => (int) $assignment->event_id,
		'name' => (string) $assignment->volunteer_name,
		'eventLabel' => trim((string) $assignment->event_name . ($eventDate !== '' ? ' - ' . HTMLHelper::_('date', $eventDate, Text::_('DATE_FORMAT_LC4')) : '')),
	];
}, $this->activeAssignments);

?>
<form action="<?php echo Route::_('index.php?option=com_volunteertracker&task=volunteer.save'); ?>" method="post" name="adminForm" id="adminForm" data-active-assignments="<?php echo htmlspecialchars(json_encode($activeAssignments), ENT_QUOTES, 'UTF-8'); ?>">
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
						<?php if (empty($this->item->id)) : ?>
							<option value="all_active"><?php echo Text::_('COM_VOLUNTEERTRACKER_ALL_ACTIVE_EVENTS'); ?></option>
						<?php endif; ?>
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
	<input type="hidden" name="jform[duplicate_action]" value="">
	<input type="hidden" name="task" value="volunteer.save">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<script>
(() => {
	const form = document.getElementById('adminForm');

	if (!form) {
		return;
	}

	const normalize = (value) => String(value || '').trim().replace(/\s+/g, ' ').toLowerCase();
	const assignments = JSON.parse(form.dataset.activeAssignments || '[]');
	const volunteer = form.querySelector('[name="jform[volunteer_name]"]');
	const event = form.querySelector('[name="jform[event_id]"]');
	const id = form.querySelector('[name="jform[id]"]');
	const duplicateAction = form.querySelector('[name="jform[duplicate_action]"]');
	const task = form.querySelector('[name="task"]');
	const originalSubmitButton = Joomla.submitbutton;
	let duplicateChoiceMade = false;

	const duplicateAssignments = () => {
		const name = normalize(volunteer?.value);
		const eventId = event?.value || '';
		const currentId = Number(id?.value || 0);

		if (!name || !eventId) {
			return [];
		}

		return assignments.filter((assignment) => {
			const samePerson = normalize(assignment.name) === name;
			const sameEvent = eventId === 'all_active' || Number(assignment.eventId) === Number(eventId);
			const sameRecord = currentId && Number(assignment.id) === currentId;

			return samePerson && sameEvent && !sameRecord;
		});
	};

	const confirmDuplicateAction = () => {
		const duplicates = duplicateAssignments();

		if (!duplicates.length) {
			duplicateAction.value = '';

			return true;
		}

		const eventList = duplicates.map((assignment) => assignment.eventLabel).filter(Boolean).join('\n- ');
		const message = eventList
			? '<?php echo addslashes(Text::_('COM_VOLUNTEERTRACKER_DUPLICATE_VOLUNTEER_CONFIRM')); ?>' + '\n\n- ' + eventList
			: '<?php echo addslashes(Text::_('COM_VOLUNTEERTRACKER_DUPLICATE_VOLUNTEER_CONFIRM')); ?>';

		duplicateAction.value = window.confirm(message) ? 'overwrite' : 'skip';
		duplicateChoiceMade = true;

		return true;
	};

	form.addEventListener('submit', () => {
		if (duplicateChoiceMade) {
			duplicateChoiceMade = false;

			return true;
		}

		return confirmDuplicateAction();
	});

	Joomla.submitbutton = (submitTask) => {
		if ((submitTask === 'volunteer.save' || submitTask === 'volunteer.apply') && !confirmDuplicateAction()) {
			return false;
		}

		if (task && submitTask) {
			task.value = submitTask;
		}

		if (originalSubmitButton) {
			return originalSubmitButton(submitTask);
		}

		form.submit();

		return true;
	};
})();
</script>
