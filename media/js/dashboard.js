(() => {
	const colors = ['#0f9cf7', '#1d6f63', '#d39d38', '#b84a62', '#6a7f3f', '#1f8a9c', '#8f5d2a', '#455a64'];

	const valueLabel = (value) => Number(value || 0).toLocaleString(undefined, {
		maximumFractionDigits: 2,
		minimumFractionDigits: 0,
	});

	const getRows = () => [...document.querySelectorAll('#vt-records tbody tr')]
		.filter((row) => row.querySelectorAll('td').length >= 5)
		.map((row) => [...row.querySelectorAll('td')].slice(0, 5).map((cell) => cell.innerText.trim().replace(/\s+/g, ' ')));

	const numberValue = (value) => Number(String(value || '0').replace(/,/g, '')) || 0;

	const tableRecords = () => getRows()
		.map((row) => ({
			volunteer: row[0],
			event: row[1],
			eventDate: row[2],
			hours: numberValue(row[3]),
			role: row[4],
		}))
		.filter((row) => row.volunteer && row.event && row.role);

	const uniqueValues = (rows, key) => [...new Set(rows.map((row) => row[key]).filter(Boolean))].sort((a, b) => a.localeCompare(b));

	const fillSelect = (select, values) => {
		if (!select) {
			return;
		}

		select.innerHTML = '';

		values.forEach((value) => {
			const option = document.createElement('option');
			option.value = value;
			option.textContent = value;
			select.append(option);
		});
	};

	const eventLabel = (row) => [row.event, row.eventDate].filter(Boolean).join(' - ');

	const countRows = (rows, key, valueFactory = () => 1) => {
		const groups = rows.reduce((result, row) => {
			const label = row[key];

			if (!label) {
				return result;
			}

			if (!result[label]) {
				result[label] = valueFactory(row);
				return result;
			}

			const value = valueFactory(row);

			if (result[label] instanceof Set && value instanceof Set) {
				value.forEach((item) => result[label].add(item));
				return result;
			}

			result[label] += value;
			return result;
		}, {});

		return Object.entries(groups)
			.map(([label, value]) => ({ label, value: value instanceof Set ? value.size : value }))
			.sort((a, b) => b.value - a.value || a.label.localeCompare(b.label));
	};

	const drawHorizontalBars = (target, rows, emptyText) => {
		if (!target) {
			return;
		}

		target.innerHTML = '';

		if (!rows.length) {
			const empty = document.createElement('p');
			empty.className = 'vt-horizontal-empty';
			empty.textContent = emptyText;
			target.append(empty);
			return;
		}

		const max = Math.max(...rows.map((row) => Number(row.value)), 1);

		rows.forEach((row, index) => {
			const item = document.createElement('div');
			const label = document.createElement('span');
			const track = document.createElement('div');
			const bar = document.createElement('i');
			const value = document.createElement('strong');

			item.className = 'vt-horizontal-row';
			label.className = 'vt-horizontal-label';
			track.className = 'vt-horizontal-track';
			bar.className = 'vt-horizontal-bar';
			value.className = 'vt-horizontal-value';

			label.textContent = row.label;
			bar.style.width = `${Math.max(5, (Number(row.value) / max) * 100)}%`;
			bar.style.backgroundColor = colors[index % colors.length];
			value.textContent = valueLabel(row.value);

			track.append(bar);
			item.append(label, track, value);
			target.append(item);
		});
	};

	const groupedTableHours = (key) => {
		const groups = tableRecords().filter((row) => row.hours > 0).reduce((result, row) => {
			result[row[key]] = (result[row[key]] || 0) + row.hours;

			return result;
		}, {});

		return Object.entries(groups)
			.map(([label, value]) => ({ label, value }))
			.sort((a, b) => b.value - a.value);
	};

	const chartRows = (canvas) => {
		try {
			const rows = JSON.parse(canvas.dataset.values || '[]');

			if (Array.isArray(rows) && rows.length) {
				return rows;
			}
		} catch (error) {
			console.warn('Volunteer Tracker chart data could not be parsed.', error);
		}

		return groupedTableHours(canvas.dataset.chart === 'pie' ? 'volunteer' : 'event');
	};

	const limitRows = (rows, limit) => {
		if (limit === 'all') {
			return rows;
		}

		return rows.slice(0, Number(limit) || 10);
	};

	const exportCsv = () => {
		const headers = ['Volunteer', 'Event', 'Event Date', 'Hours', 'Role'];
		const rows = [headers, ...getRows()];
		const csv = rows.map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement('a');

		link.href = URL.createObjectURL(blob);
		link.download = `volunteer-tracker-${new Date().toISOString().slice(0, 10)}.csv`;
		link.click();
		URL.revokeObjectURL(link.href);
	};

	const exportEventCsv = () => {
		const table = document.getElementById('vt-event-records');

		if (!table) {
			return;
		}

		const headers = ['Event', 'Event Date', 'Event Location', 'Volunteer Entries', 'Total Hours'];
		const rows = [...table.querySelectorAll('tbody tr')]
			.filter((row) => row.querySelectorAll('td').length >= 5)
			.map((row) => [...row.querySelectorAll('td')].slice(0, 5).map((cell) => cell.innerText.trim().replace(/\s+/g, ' ')));
		const csv = [headers, ...rows].map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement('a');

		link.href = URL.createObjectURL(blob);
		link.download = `volunteer-tracker-events-${new Date().toISOString().slice(0, 10)}.csv`;
		link.click();
		URL.revokeObjectURL(link.href);
	};

	const exportReportCsv = () => {
		const table = document.getElementById('vt-report-records');

		if (!table) {
			return;
		}

		const headers = ['Volunteer', 'Role'];
		const rows = [...table.querySelectorAll('tbody tr')]
			.filter((row) => row.querySelectorAll('td').length === 2)
			.map((row) => [...row.querySelectorAll('td')].map((cell) => cell.innerText.trim().replace(/\s+/g, ' ')));
		const csv = [headers, ...rows].map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
		const name = String(table.dataset.reportName || 'event').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'event';
		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement('a');

		link.href = URL.createObjectURL(blob);
		link.download = `volunteer-tracker-${name}-roles.csv`;
		link.click();
		URL.revokeObjectURL(link.href);
	};

	const emptyState = (ctx, canvas) => {
		ctx.fillStyle = '#5f6f75';
		ctx.font = '16px system-ui, sans-serif';
		ctx.textAlign = 'center';
		ctx.fillText('No data yet', canvas.width / 2, canvas.height / 2);
	};

	const drawBar = (canvas, rows) => {
		const ctx = canvas.getContext('2d');
		const pad = 40;
		const max = Math.max(...rows.map((row) => Number(row.value)), 0);

		if (!ctx) {
			return false;
		}

		ctx.clearRect(0, 0, canvas.width, canvas.height);

		if (!rows.length || !max) {
			emptyState(ctx, canvas);
			return false;
		}

		const width = (canvas.width - pad * 2) / rows.length;
		const labelEvery = rows.length > 30 ? Math.ceil(rows.length / 12) : 1;

		rows.forEach((row, index) => {
			const value = Number(row.value);
			const barHeight = ((canvas.height - pad * 2) * value) / max;
			const x = pad + index * width + width * .16;
			const y = canvas.height - pad - barHeight;

			ctx.fillStyle = colors[index % colors.length];
			ctx.fillRect(x, y, width * .68, barHeight);
			if (rows.length <= 30) {
				ctx.fillStyle = '#12343f';
				ctx.font = '13px system-ui, sans-serif';
				ctx.textAlign = 'center';
				ctx.fillText(valueLabel(value), x + width * .34, y - 8);
			}

			if (index % labelEvery === 0) {
				ctx.save();
				ctx.fillStyle = '#12343f';
				ctx.font = '12px system-ui, sans-serif';
				ctx.textAlign = 'center';
				ctx.translate(x + width * .34, canvas.height - 8);
				ctx.rotate(-Math.PI / 7);
				ctx.fillText(String(row.label).slice(0, 16), 0, 0);
				ctx.restore();
			}
		});

		return true;
	};

	const updateRoleCards = () => {
		const rows = tableRecords().filter((row) => row.role);
		const roleCoverageEventSelect = document.getElementById('vt-role-coverage-event');
		const roleCoverageChart = document.getElementById('vt-role-coverage-chart');
		const volunteerSelect = document.getElementById('vt-volunteer-role-volunteer');
		const volunteerRoleChart = document.getElementById('vt-volunteer-role-chart');
		const eventLabels = [...new Set(rows.map(eventLabel).filter(Boolean))];
		const volunteers = uniqueValues(rows, 'volunteer');

		if (!roleCoverageEventSelect || !roleCoverageChart || !volunteerSelect || !volunteerRoleChart) {
			return;
		}

		const selectedEvent = roleCoverageEventSelect.value || 'all';
		const selectedVolunteer = volunteerSelect.value || volunteers[0] || '';

		fillSelect(roleCoverageEventSelect, ['all', ...eventLabels]);
		roleCoverageEventSelect.querySelector('option[value="all"]').textContent = 'All Events';
		fillSelect(volunteerSelect, volunteers);

		roleCoverageEventSelect.value = selectedEvent === 'all' || eventLabels.includes(selectedEvent) ? selectedEvent : 'all';
		volunteerSelect.value = volunteers.includes(selectedVolunteer) ? selectedVolunteer : (volunteers[0] || '');

		const eventRows = roleCoverageEventSelect.value === 'all'
			? rows
			: rows.filter((row) => eventLabel(row) === roleCoverageEventSelect.value);
		const rolePeopleRows = countRows(eventRows, 'role', (row) => new Set([row.volunteer]));
		const volunteerRoleRows = countRows(rows.filter((row) => row.volunteer === volunteerSelect.value), 'role');

		drawHorizontalBars(roleCoverageChart, rolePeopleRows, 'No role records are available for this event yet.');
		drawHorizontalBars(volunteerRoleChart, volunteerRoleRows, 'Select a volunteer once records have been saved.');
	};

	const drawPie = (canvas, rows) => {
		const ctx = canvas.getContext('2d');
		const total = rows.reduce((sum, row) => sum + Number(row.value || 0), 0);
		const radius = Math.min(canvas.width, canvas.height) * .32;
		let start = -Math.PI / 2;

		if (!ctx) {
			return false;
		}

		ctx.clearRect(0, 0, canvas.width, canvas.height);

		if (!rows.length || !total) {
			emptyState(ctx, canvas);
			return false;
		}

		rows.forEach((row, index) => {
			const slice = (Number(row.value) / total) * Math.PI * 2;

			ctx.beginPath();
			ctx.moveTo(canvas.width * .34, canvas.height / 2);
			ctx.arc(canvas.width * .34, canvas.height / 2, radius, start, start + slice);
			ctx.closePath();
			ctx.fillStyle = colors[index % colors.length];
			ctx.fill();
			start += slice;
		});

		rows.forEach((row, index) => {
			const y = 34 + index * 25;
			ctx.fillStyle = colors[index % colors.length];
			ctx.fillRect(canvas.width * .63, y - 12, 14, 14);
			ctx.fillStyle = '#12343f';
			ctx.font = '13px system-ui, sans-serif';
			ctx.textAlign = 'left';
			ctx.fillText(`${String(row.label).slice(0, 22)} (${valueLabel(row.value)})`, canvas.width * .63 + 22, y);
		});

		return true;
	};

	const renderChart = (canvas) => {
		const key = canvas.dataset.chart === 'pie' ? 'volunteer' : 'event';
		const limit = document.querySelector(`[data-chart-limit="${key}"]`)?.value || '10';
		const rows = limitRows(chartRows(canvas), limit);
		let drawn = false;

		canvas.classList.remove('is-ready');

		if (canvas.dataset.chart === 'pie') {
			drawn = drawPie(canvas, rows);
		} else {
			drawn = drawBar(canvas, rows);
		}

		if (drawn) {
			canvas.classList.add('is-ready');
		}
	};

	const init = () => {
		document.getElementById('vt-export')?.addEventListener('click', exportCsv);
		document.getElementById('vt-event-export')?.addEventListener('click', exportEventCsv);
		document.getElementById('vt-report-export')?.addEventListener('click', exportReportCsv);
		[
			document.getElementById('vt-role-coverage-event'),
			document.getElementById('vt-volunteer-role-volunteer'),
		].forEach((select) => select?.addEventListener('change', updateRoleCards));

		updateRoleCards();
		document.querySelectorAll('[data-chart-limit]').forEach((select) => {
			select.addEventListener('change', () => {
				const type = select.dataset.chartLimit === 'volunteer' ? 'pie' : 'bar';

				document.querySelectorAll(`.vt-chart[data-chart="${type}"]`).forEach(renderChart);
			});
		});

		document.querySelectorAll('.vt-chart').forEach(renderChart);
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
		return;
	}

	init();
})();
