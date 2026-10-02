(() => {
	const colors = ['#1d6f63', '#d39d38', '#2f6690', '#b84a62', '#6a7f3f', '#1f8a9c', '#8f5d2a', '#455a64'];

	const valueLabel = (value) => Number(value || 0).toLocaleString(undefined, {
		maximumFractionDigits: 2,
		minimumFractionDigits: 0,
	});

	const getRows = () => [...document.querySelectorAll('#vt-records tbody tr')]
		.filter((row) => row.querySelectorAll('td').length === 6)
		.map((row) => [...row.querySelectorAll('td')].slice(1).map((cell) => cell.innerText.trim().replace(/\s+/g, ' ')));

	const exportCsv = () => {
		const headers = ['Volunteer', 'Event', 'Event Date', 'Hours', 'Notes'];
		const rows = [headers, ...getRows()];
		const csv = rows.map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');
		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement('a');

		link.href = URL.createObjectURL(blob);
		link.download = `volunteer-tracker-${new Date().toISOString().slice(0, 10)}.csv`;
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
		const pad = 36;
		const max = Math.max(...rows.map((row) => Number(row.value)), 0);

		ctx.clearRect(0, 0, canvas.width, canvas.height);

		if (!rows.length || !max) {
			emptyState(ctx, canvas);
			return;
		}

		const width = (canvas.width - pad * 2) / rows.length;

		rows.forEach((row, index) => {
			const value = Number(row.value);
			const barHeight = ((canvas.height - pad * 2) * value) / max;
			const x = pad + index * width + width * .16;
			const y = canvas.height - pad - barHeight;

			ctx.fillStyle = colors[index % colors.length];
			ctx.fillRect(x, y, width * .68, barHeight);
			ctx.fillStyle = '#12343f';
			ctx.font = '13px system-ui, sans-serif';
			ctx.textAlign = 'center';
			ctx.fillText(valueLabel(value), x + width * .34, y - 8);
			ctx.save();
			ctx.translate(x + width * .34, canvas.height - 8);
			ctx.rotate(-Math.PI / 7);
			ctx.fillText(String(row.label).slice(0, 18), 0, 0);
			ctx.restore();
		});
	};

	const drawPie = (canvas, rows) => {
		const ctx = canvas.getContext('2d');
		const total = rows.reduce((sum, row) => sum + Number(row.value || 0), 0);
		const radius = Math.min(canvas.width, canvas.height) * .32;
		let start = -Math.PI / 2;

		ctx.clearRect(0, 0, canvas.width, canvas.height);

		if (!rows.length || !total) {
			emptyState(ctx, canvas);
			return;
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
	};

	document.addEventListener('DOMContentLoaded', () => {
		document.getElementById('vt-export')?.addEventListener('click', exportCsv);

		document.querySelectorAll('.vt-chart').forEach((canvas) => {
			const rows = JSON.parse(canvas.dataset.values || '[]');
			const type = canvas.dataset.chart;

			if (type === 'pie') {
				drawPie(canvas, rows);
				return;
			}

			drawBar(canvas, rows);
		});
	});
})();
