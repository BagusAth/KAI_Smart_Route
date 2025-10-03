(function () {
	const dateButtons = Array.from(document.querySelectorAll('[data-date-option]'));

	if (dateButtons.length === 0) {
		return;
	}

	const setActiveButton = (button) => {
		dateButtons.forEach((item) => {
			if (item === button) {
				return;
			}

			item.classList.remove('is-active');
			item.setAttribute('aria-pressed', 'false');
		});

		button.classList.add('is-active');
		button.setAttribute('aria-pressed', 'true');

		const detail = {
			date: button.dataset.dateValue || '',
			day: button.dataset.dateDay || '',
		};

		document.dispatchEvent(new CustomEvent('routes:date-change', { detail }));
	};

	let hasActiveButton = false;

	dateButtons.forEach((button) => {
		if (button.classList.contains('is-active')) {
			hasActiveButton = true;
			button.setAttribute('aria-pressed', 'true');
		}

		button.addEventListener('click', () => {
			if (button.classList.contains('is-active')) {
				return;
			}

			setActiveButton(button);
		});

		button.addEventListener('keydown', (event) => {
			if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
				return;
			}

			event.preventDefault();
			const currentIndex = dateButtons.indexOf(button);
			const nextIndex = event.key === 'ArrowRight'
				? (currentIndex + 1) % dateButtons.length
				: (currentIndex - 1 + dateButtons.length) % dateButtons.length;

			dateButtons[nextIndex].focus();
		});
	});

	if (!hasActiveButton && dateButtons[0]) {
		setActiveButton(dateButtons[0]);
	}
})();
