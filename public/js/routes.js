(function () {
	const dateButtons = Array.from(document.querySelectorAll('[data-date-option]'));

	if (dateButtons.length > 0) {
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
	}

	const filterRoot = document.querySelector('[data-filter-root]');
	const dropdown = filterRoot?.querySelector('[data-filter-dropdown]');
	const toggleButton = filterRoot?.querySelector('[data-filter-toggle]');
	const startSelect = filterRoot?.querySelector('[data-filter-start]');
	const endSelect = filterRoot?.querySelector('[data-filter-end]');
	const applyButton = filterRoot?.querySelector('[data-filter-apply]');
	const resetButton = filterRoot?.querySelector('[data-filter-reset]');
	const errorMessage = filterRoot?.querySelector('[data-filter-error]');
	const routeCards = Array.from(document.querySelectorAll('[data-route-card]'));
	const emptyMessage = document.querySelector('[data-filter-empty]');
	let isDropdownOpen = false;

	const hiddenClass = 'hidden';

	const sanitizeTime = (value) => {
		if (!value) {
			return null;
		}

		const normalized = value.replace('.', ':');
		const [hours, minutes] = normalized.split(':');

		if (Number.isNaN(Number(hours)) || Number.isNaN(Number(minutes))) {
			return null;
		}

		return Number(hours) * 60 + Number(minutes);
	};

	const closeDropdown = () => {
		if (!dropdown || !toggleButton) {
			return;
		}

		dropdown.classList.add(hiddenClass);
		toggleButton.setAttribute('aria-expanded', 'false');
		isDropdownOpen = false;
	};

	const openDropdown = () => {
		if (!dropdown || !toggleButton) {
			return;
		}

		dropdown.classList.remove(hiddenClass);
		toggleButton.setAttribute('aria-expanded', 'true');
		isDropdownOpen = true;
	};

	const toggleDropdown = () => {
		if (!dropdown) {
			return;
		}

		if (isDropdownOpen) {
			closeDropdown();
		} else {
			openDropdown();
		}
	};

	const updateEmptyState = () => {
		if (!emptyMessage) {
			return;
		}

		const hasVisibleCard = routeCards.some((card) => !card.classList.contains(hiddenClass));
		emptyMessage.classList.toggle(hiddenClass, hasVisibleCard);
	};

	const clearError = () => {
		if (errorMessage) {
			errorMessage.textContent = '';
			errorMessage.classList.add(hiddenClass);
		}
	};

	const showError = (message) => {
		if (!errorMessage) {
			return;
		}

		errorMessage.textContent = message;
		errorMessage.classList.remove(hiddenClass);
	};

	const applyFilter = () => {
		const startValue = sanitizeTime(startSelect?.value || '');
		const endValue = sanitizeTime(endSelect?.value || '');

		clearError();

		if (startValue !== null && endValue !== null && startValue > endValue) {
			showError('Jam selesai harus setelah jam mulai.');
			return;
		}

		routeCards.forEach((card) => {
			const departureTime = sanitizeTime(card.dataset.departureTime || '');

			if (departureTime === null) {
				card.classList.remove(hiddenClass);
				return;
			}

			if (startValue !== null && departureTime < startValue) {
				card.classList.add(hiddenClass);
				return;
			}

			if (endValue !== null && departureTime > endValue) {
				card.classList.add(hiddenClass);
				return;
			}

			card.classList.remove(hiddenClass);
		});

		updateEmptyState();
		closeDropdown();
	};

	const resetFilter = () => {
		if (startSelect) {
			startSelect.value = '';
		}

		if (endSelect) {
			endSelect.value = '';
		}

		clearError();
		routeCards.forEach((card) => {
			card.classList.remove(hiddenClass);
		});
		updateEmptyState();
	};

	if (toggleButton && dropdown) {
		toggleButton.addEventListener('click', (event) => {
			event.stopPropagation();
			toggleDropdown();
		});
	}

	document.addEventListener('click', (event) => {
		if (!isDropdownOpen) {
			return;
		}

		if (filterRoot && !filterRoot.contains(event.target)) {
			closeDropdown();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && isDropdownOpen) {
			closeDropdown();
		}
	});

	applyButton?.addEventListener('click', applyFilter);
	resetButton?.addEventListener('click', () => {
		resetFilter();
		closeDropdown();
	});

	startSelect?.addEventListener('change', clearError);
	endSelect?.addEventListener('change', clearError);

	updateEmptyState();
})();
