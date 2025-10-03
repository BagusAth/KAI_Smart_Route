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
	const cardContainer = document.querySelector('[data-route-card-container]');
	const reservationUrl = cardContainer?.dataset.reservationUrl || '';
	const routeGroups = Array.from(cardContainer?.querySelectorAll('[data-route-group]') || []);
	const selectionStorageKey = 'kaizen:selectedRoute';
	const modeToggleButton = document.querySelector('[data-route-toggle]');
	let routeCards = [];
	const emptyMessage = document.querySelector('[data-filter-empty]');
	let isDropdownOpen = false;

	const hiddenClass = 'hidden';
	const expandedClass = 'is-expanded';
	const connectorSelector = '.route-connector';
	const eventDotSelector = '[data-route-event-dot]';
	let connectorAnimationFrame = null;

	function findTimelineColumnForConnector(connector) {
		let column = connector.previousElementSibling;

		while (column && column.classList && column.classList.contains('route-connector')) {
			column = column.previousElementSibling;
		}

		return column;
	}

	function positionRouteConnectors() {
		const connectors = Array.from(document.querySelectorAll(connectorSelector));

		connectors.forEach((connector) => {
			const column = findTimelineColumnForConnector(connector);
			if (!column || column.offsetParent === null) {
				return;
			}

			const dot = column.querySelector(eventDotSelector);
			if (!dot) {
				return;
			}

			const columnRect = column.getBoundingClientRect();
			const dotRect = dot.getBoundingClientRect();

			if (columnRect.height === 0 || dotRect.height === 0) {
				return;
			}

			const connectorRect = connector.getBoundingClientRect();
			const connectorHeight = connectorRect.height || parseFloat(window.getComputedStyle(connector).height) || 4;
			const offset = dotRect.top + dotRect.height / 2 - columnRect.top - connectorHeight / 2;

			connector.style.marginTop = `${Math.max(offset, 0)}px`;
		});
	}

	function scheduleConnectorPositioning() {
		if (connectorAnimationFrame !== null) {
			cancelAnimationFrame(connectorAnimationFrame);
		}

		connectorAnimationFrame = requestAnimationFrame(() => {
			connectorAnimationFrame = null;
			positionRouteConnectors();
		});
	}

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

		if (routeCards.length === 0) {
			emptyMessage.classList.add(hiddenClass);
			return;
		}

		const hasVisibleCard = routeCards.some((card) => !card.classList.contains(hiddenClass));
		emptyMessage.classList.toggle(hiddenClass, hasVisibleCard);
	};

	const refreshRouteCards = () => {
		const activeGroup = cardContainer?.dataset.activeGroup;
		routeCards = Array.from(cardContainer?.querySelectorAll('[data-route-group].is-active [data-route-card]') || []);

		if (!activeGroup && routeGroups.length > 0) {
			const fallbackGroup = routeGroups[0];
			cardContainer.dataset.activeGroup = fallbackGroup.dataset.routeGroup;
		}

		routeCards.forEach((card) => {
			card.classList.remove(hiddenClass);
		});

		updateEmptyState();
		scheduleConnectorPositioning();
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

	const runFilter = ({ closeDropdown: shouldClose = true } = {}) => {
		const startValue = sanitizeTime(startSelect?.value || '');
		const endValue = sanitizeTime(endSelect?.value || '');

		clearError();

		if (startValue !== null && endValue !== null && startValue > endValue) {
			showError('Jam selesai harus setelah jam mulai.');
			updateEmptyState();
			return false;
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

		if (shouldClose) {
			closeDropdown();
		}

		scheduleConnectorPositioning();

		return true;
	};

	const applyFilter = () => {
		runFilter({ closeDropdown: true });
	};

	const resetFilter = () => {
		if (startSelect) {
			startSelect.value = '';
		}

		if (endSelect) {
			endSelect.value = '';
		}

		clearError();
		refreshRouteCards();
		routeCards.forEach((card) => {
			card.classList.remove(hiddenClass);
		});
		updateEmptyState();
		scheduleConnectorPositioning();
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

	refreshRouteCards();

	const getToggleLabelForState = (mode) => {
		if (!modeToggleButton) {
			return '';
		}

		const labelDirect = modeToggleButton.dataset.labelDirect || 'Lihat kereta langsung';
		const labelMulti = modeToggleButton.dataset.labelMulti || 'Lihat koneksi multi-moda';

		return mode === 'direct' ? labelMulti : labelDirect;
	};

	const setMode = (mode) => {
		if (!cardContainer) {
			return;
		}

		const groupExists = routeGroups.some((group) => group.dataset.routeGroup === mode);

		if (!groupExists) {
			return;
		}

		const currentMode = cardContainer.dataset.activeGroup || 'direct';

		if (currentMode === mode) {
			return;
		}

		routeGroups.forEach((group) => {
			const isTarget = group.dataset.routeGroup === mode;
			group.classList.toggle('is-active', isTarget);
			group.classList.toggle(hiddenClass, !isTarget);
		});

		cardContainer.dataset.activeGroup = mode;
		refreshRouteCards();
		runFilter({ closeDropdown: false });
		scheduleConnectorPositioning();

		if (modeToggleButton) {
			modeToggleButton.textContent = getToggleLabelForState(mode);
		}
	};

	if (modeToggleButton) {
		const initialMode = cardContainer?.dataset.activeGroup || 'direct';
		modeToggleButton.textContent = getToggleLabelForState(initialMode);

		modeToggleButton.addEventListener('click', () => {
			if (!cardContainer) {
				return;
			}

			const currentMode = cardContainer.dataset.activeGroup || 'direct';
			const nextMode = currentMode === 'direct' ? 'multi' : 'direct';
			setMode(nextMode);
		});
	}


	const expandCard = (card) => {
		const details = card.querySelector('[data-route-card-details]');
		const toggle = card.querySelector('[data-route-card-toggle]');
		if (!details || !toggle) {
			return;
		}

		details.classList.remove(hiddenClass);
		card.classList.add(expandedClass);
		card.dataset.routeCardState = 'expanded';
		toggle.setAttribute('aria-expanded', 'true');
	};

	const collapseCard = (card) => {
		const details = card.querySelector('[data-route-card-details]');
		const toggle = card.querySelector('[data-route-card-toggle]');
		if (!details || !toggle) {
			return;
		}

		details.classList.add(hiddenClass);
		card.classList.remove(expandedClass);
		card.dataset.routeCardState = 'collapsed';
		toggle.setAttribute('aria-expanded', 'false');
	};

	const toggleCard = (card) => {
		if (card.dataset.routeCardState === 'expanded') {
			collapseCard(card);
			return;
		}

		expandCard(card);
	};

	const allRouteCards = Array.from(document.querySelectorAll('[data-route-card]'));

	allRouteCards.forEach((card) => {
		const toggle = card.querySelector('[data-route-card-toggle]');
		const selectButton = card.querySelector('[data-route-select]');

		if (!toggle) {
			return;
		}

		toggle.addEventListener('click', (event) => {
			event.stopPropagation();
			toggleCard(card);
		});

		toggle.addEventListener('keydown', (event) => {
			if (event.key !== 'Enter' && event.key !== ' ') {
				return;
			}

			event.preventDefault();
			toggleCard(card);
		});

		card.addEventListener('keydown', (event) => {
			if (event.target !== card) {
				return;
			}

			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				toggleCard(card);
			}
		});

		selectButton?.addEventListener('click', (event) => {
			event.preventDefault();
			event.stopPropagation();

			let payload = {};
			const payloadRaw = selectButton.dataset.routePayload;

			if (payloadRaw) {
				try {
					payload = JSON.parse(payloadRaw);
				} catch (error) {
					console.error('Gagal membaca data rute:', error);
				}
			}

			if (cardContainer?.dataset.searchSummary) {
				try {
					payload.summary = JSON.parse(cardContainer.dataset.searchSummary);
				} catch (error) {
					console.error('Gagal membaca ringkasan pencarian:', error);
				}
			}

			if (cardContainer?.dataset.passengerCount) {
				const count = Number(cardContainer.dataset.passengerCount);
				if (!Number.isNaN(count) && count > 0) {
					payload.passenger_count = count;
				}
			}

			try {
				sessionStorage.setItem(selectionStorageKey, JSON.stringify(payload));
			} catch (error) {
				console.warn('Tidak dapat menyimpan rute terpilih ke sessionStorage:', error);
			}

			document.dispatchEvent(new CustomEvent('routes:select', { detail: { card, payload } }));

			if (!reservationUrl) {
				return;
			}

			selectButton.disabled = true;
			selectButton.classList.add('is-loading');
			selectButton.dataset.originalLabel = selectButton.dataset.originalLabel || selectButton.textContent;
			selectButton.textContent = 'Memuat...';

			window.location.assign(reservationUrl);
		});
	});

	document.addEventListener('routes:date-change', scheduleConnectorPositioning);
	window.addEventListener('resize', scheduleConnectorPositioning);
	window.addEventListener('load', positionRouteConnectors);

	if (document.fonts && document.fonts.addEventListener) {
		document.fonts.addEventListener('loadingdone', scheduleConnectorPositioning);
	}
})();
