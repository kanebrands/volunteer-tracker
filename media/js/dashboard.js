(() => {
	const colors = ['#0f9cf7', '#1d6f63', '#d39d38', '#b84a62', '#6a7f3f', '#1f8a9c', '#8f5d2a', '#455a64'];

	const valueLabel = (value) => Number(value || 0).toLocaleString(undefined, {
		maximumFractionDigits: 2,
		minimumFractionDigits: 0,
	});

	const getRows = () => [...document.querySelectorAll('#vt-records tbody tr')]
		.filter((row) => row.querySelectorAll('td').length >= 5)
		.map((row) => [...row.querySelectorAll('td')].slice(0, 5).map((cell) => cell.textContent.trim().replace(/\s+/g, ' ')));

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

	const cellValue = (row, index) => {
		const cell = row.children[index];

		return cell?.dataset.sortValue ?? cell?.textContent.trim().replace(/\s+/g, ' ') ?? '';
	};

	const compareValues = (a, b, type) => {
		if (type === 'number') {
			return numberValue(a) - numberValue(b);
		}

		if (type === 'date') {
			const aTime = a ? Date.parse(a) : Number.MAX_SAFE_INTEGER;
			const bTime = b ? Date.parse(b) : Number.MAX_SAFE_INTEGER;

			return aTime - bTime;
		}

		return String(a).localeCompare(String(b), undefined, { numeric: true, sensitivity: 'base' });
	};

	const csvFromRows = (headers, rows, columnCount) => {
		const dataRows = rows.map((row) => [...row.querySelectorAll('td')]
			.slice(0, columnCount)
			.map((cell) => cell.textContent.trim().replace(/\s+/g, ' ')));

		return [headers, ...dataRows]
			.map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(','))
			.join('\n');
	};

	const downloadCsv = (csv, filename) => {
		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement('a');

		link.href = URL.createObjectURL(blob);
		link.download = filename;
		link.click();
		URL.revokeObjectURL(link.href);
	};

	const makeManagedTable = ({
		tableId,
		pageSizeId,
		paginationId,
		defaultSortIndex,
		defaultSortType = 'text',
		filters = [],
	}) => {
		const table = document.getElementById(tableId);
		const pageSize = document.getElementById(pageSizeId);
		const pagination = document.getElementById(paginationId);

		if (!table || !pageSize || !pagination) {
			return null;
		}

		const tbody = table.querySelector('tbody');
		const allRows = [...tbody.querySelectorAll('tr')].filter((row) => row.querySelectorAll('td').length > 1);
		const sortButtons = [...table.querySelectorAll('.vt-sort')];
		const state = {
			filteredRows: [...allRows],
			page: 1,
			sortIndex: defaultSortIndex,
			sortType: defaultSortType,
			sortDirection: 'asc',
		};

		const runFilters = () => allRows.filter((row) => filters.every((filter) => filter(row)));

		const sortRows = (rows) => {
			const multiplier = state.sortDirection === 'asc' ? 1 : -1;

			return [...rows].sort((a, b) => {
				const result = compareValues(cellValue(a, state.sortIndex), cellValue(b, state.sortIndex), state.sortType);

				return result * multiplier;
			});
		};

		const updateSortIndicators = () => {
			sortButtons.forEach((button) => {
				const indicator = button.querySelector('span');
				const active = Number(button.dataset.sortIndex) === state.sortIndex;

				button.classList.toggle('is-active', active);
				button.setAttribute('aria-sort', active ? (state.sortDirection === 'asc' ? 'ascending' : 'descending') : 'none');
				if (indicator) {
					indicator.textContent = active ? (state.sortDirection === 'asc' ? '▼' : '▲') : '';
				}
			});
		};

		const renderPagination = (totalRows, totalPages) => {
			pagination.innerHTML = '';

			const count = document.createElement('span');
			count.textContent = `${totalRows} record${totalRows === 1 ? '' : 's'}`;
			pagination.append(count);

			if (totalPages <= 1) {
				return;
			}

			const previous = document.createElement('button');
			const next = document.createElement('button');
			previous.className = 'btn btn-sm btn-outline-primary';
			next.className = 'btn btn-sm btn-outline-primary';
			previous.type = 'button';
			next.type = 'button';
			previous.textContent = 'Previous';
			next.textContent = 'Next';
			previous.disabled = state.page <= 1;
			next.disabled = state.page >= totalPages;

			const pageLabel = document.createElement('strong');
			pageLabel.textContent = `Page ${state.page} of ${totalPages}`;

			previous.addEventListener('click', () => {
				state.page = Math.max(1, state.page - 1);
				render();
			});
			next.addEventListener('click', () => {
				state.page = Math.min(totalPages, state.page + 1);
				render();
			});

			pagination.append(previous, pageLabel, next);
		};

		const render = () => {
			const limit = pageSize.value === 'all' ? Infinity : Number(pageSize.value) || 10;
			state.filteredRows = sortRows(runFilters());

			const totalRows = state.filteredRows.length;
			const totalPages = Number.isFinite(limit) ? Math.max(1, Math.ceil(totalRows / limit)) : 1;
			state.page = Math.min(state.page, totalPages);

			const start = Number.isFinite(limit) ? (state.page - 1) * limit : 0;
			const visibleRows = new Set(Number.isFinite(limit) ? state.filteredRows.slice(start, start + limit) : state.filteredRows);

			allRows.forEach((row) => {
				row.hidden = !visibleRows.has(row);
			});

			state.filteredRows.forEach((row) => tbody.append(row));
			updateSortIndicators();
			renderPagination(totalRows, totalPages);
		};

		sortButtons.forEach((button) => {
			button.addEventListener('click', () => {
				const index = Number(button.dataset.sortIndex);

				if (state.sortIndex === index) {
					state.sortDirection = state.sortDirection === 'asc' ? 'desc' : 'asc';
				} else {
					state.sortIndex = index;
					state.sortType = button.dataset.sortType || 'text';
					state.sortDirection = 'asc';
				}

				state.page = 1;
				render();
			});
		});

		pageSize.addEventListener('change', () => {
			state.page = 1;
			render();
		});

		return {
			render,
			resetPage: () => {
				state.page = 1;
				render();
			},
			filteredRows: () => state.filteredRows,
		};
	};

	let volunteerTableManager = null;
	let eventTableManager = null;

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
			const track = row.summary ? null : document.createElement('div');
			const bar = row.summary ? null : document.createElement('i');
			const value = document.createElement('strong');

			item.className = row.summary ? 'vt-horizontal-row vt-horizontal-row-summary' : 'vt-horizontal-row';
			label.className = 'vt-horizontal-label';
			value.className = 'vt-horizontal-value';

			label.textContent = row.label;
			if (!row.summary) {
				track.className = 'vt-horizontal-track';
				bar.className = 'vt-horizontal-bar';
				bar.style.width = `${Math.max(5, (Number(row.value) / max) * 100)}%`;
				bar.style.backgroundColor = colors[index % colors.length];
				track.append(bar);
				item.append(label, track, value);
			} else {
				item.append(label, value);
			}
			value.textContent = valueLabel(row.value);

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
		const rows = volunteerTableManager?.filteredRows() || [...document.querySelectorAll('#vt-records tbody tr')];
		const csv = csvFromRows(headers, rows, 5);

		downloadCsv(csv, `volunteer-tracker-${new Date().toISOString().slice(0, 10)}.csv`);
	};

	const exportEventCsv = () => {
		const table = document.getElementById('vt-event-records');

		if (!table) {
			return;
		}

		const headers = ['Event', 'Event Date', 'Event Location', 'Status', 'Volunteer Entries', 'Total Hours'];
		const rows = eventTableManager?.filteredRows() || [...table.querySelectorAll('tbody tr')];
		const csv = csvFromRows(headers, rows.filter((row) => row.querySelectorAll('td').length >= 6), 6);

		downloadCsv(csv, `volunteer-tracker-events-${new Date().toISOString().slice(0, 10)}.csv`);
	};

	const exportReportCsv = () => {
		const table = document.getElementById('vt-report-records');

		if (!table) {
			return;
		}

		const headers = ['Volunteer', 'Role'];
		const rows = [...table.querySelectorAll('tbody tr')]
			.filter((row) => row.querySelectorAll('td').length === 2)
			.map((row) => [...row.querySelectorAll('td')].map((cell) => cell.textContent.trim().replace(/\s+/g, ' ')));
		const csv = [headers, ...rows].map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
		const name = String(table.dataset.reportName || 'event').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'event';
		downloadCsv(csv, `volunteer-tracker-${name}-roles.csv`);
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

		if (roleCoverageEventSelect.value !== 'all' && eventRows.length) {
			rolePeopleRows.push({
				label: 'Total Volunteers',
				value: new Set(eventRows.map((row) => row.volunteer).filter(Boolean)).size,
				summary: true,
			});
		}

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
		const volunteerEventFilter = document.getElementById('vt-record-event-filter');
		const eventStartDate = document.getElementById('vt-event-start-date');
		const eventEndDate = document.getElementById('vt-event-end-date');
		const includeArchived = document.getElementById('vt-include-archived');
		const formatDate = (date) => [
			date.getFullYear(),
			String(date.getMonth() + 1).padStart(2, '0'),
			String(date.getDate()).padStart(2, '0'),
		].join('-');

		if (eventStartDate && !eventStartDate.value) {
			const start = new Date();
			start.setFullYear(start.getFullYear() - 1);
			eventStartDate.value = formatDate(start);
		}

		if (eventEndDate && !eventEndDate.value) {
			const end = new Date();
			end.setFullYear(end.getFullYear() + 1);
			eventEndDate.value = formatDate(end);
		}

		volunteerTableManager = makeManagedTable({
			tableId: 'vt-records',
			pageSizeId: 'vt-record-page-size',
			paginationId: 'vt-record-pagination',
			defaultSortIndex: 2,
			defaultSortType: 'date',
			filters: [
				(row) => !volunteerEventFilter || volunteerEventFilter.value === 'all' || row.dataset.eventId === volunteerEventFilter.value,
				(row) => includeArchived?.checked || row.dataset.eventArchived !== '1',
			],
		});

		eventTableManager = makeManagedTable({
			tableId: 'vt-event-records',
			pageSizeId: 'vt-event-page-size',
			paginationId: 'vt-event-pagination',
			defaultSortIndex: 1,
			defaultSortType: 'date',
			filters: [
				(row) => {
					if (!includeArchived?.checked && row.dataset.eventArchived === '1') {
						return false;
					}

					const date = row.dataset.eventDate || '';

					if (eventStartDate?.value && (!date || date < eventStartDate.value)) {
						return false;
					}

					if (eventEndDate?.value && (!date || date > eventEndDate.value)) {
						return false;
					}

					return true;
				},
			],
		});

		volunteerEventFilter?.addEventListener('change', () => volunteerTableManager?.resetPage());
		includeArchived?.addEventListener('change', () => {
			volunteerTableManager?.resetPage();
			eventTableManager?.resetPage();
		});
		eventStartDate?.addEventListener('change', () => eventTableManager?.resetPage());
		eventEndDate?.addEventListener('change', () => eventTableManager?.resetPage());
		volunteerTableManager?.render();
		eventTableManager?.render();

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
