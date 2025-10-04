document.addEventListener('DOMContentLoaded', () => {
	const root = document.querySelector('.home-page-body');
	if (!root) {
		return;
	}

	let stationOptions = [];
	const stationsRaw = root.getAttribute('data-stations');
	if (stationsRaw) {
		try {
			const parsed = JSON.parse(stationsRaw);
			const list = Array.isArray(parsed) ? parsed : Object.values(parsed ?? {});
			stationOptions = list
				.filter(Boolean)
				.map((station) => {
					const name = station.name ?? station.label ?? station.code ?? '';
					const city = station.city ?? '';
					return {
						code: station.code ?? '',
						name,
						city,
						label:
							station.label ??
							(name && city ? `${name} (${city})` : name || city || station.code || ''),
					};
				})
				.filter((station) => station.code && station.label);
		} catch (error) {
			console.warn('Tidak dapat membaca data stasiun:', error);
		}
	}

	if (!Array.isArray(stationOptions)) {
		stationOptions = [];
	}

	stationOptions.sort((a, b) => a.label.localeCompare(b.label, 'id'));

	const menuButton = document.querySelector('[data-menu-button]');
	const menuPanel = document.querySelector('[data-menu-panel]');
	const searchForm = document.getElementById('search-form');
	const originInput = document.getElementById('origin-display');
	const destinationInput = document.getElementById('destination-display');
	const originHidden = document.getElementById('origin');
	const destinationHidden = document.getElementById('destination');
	const originSuggestions = document.querySelector('[data-suggestions="origin"]');
	const destinationSuggestions = document.querySelector('[data-suggestions="destination"]');
	const departureDateInput = document.getElementById('departure-date');
	const passengersSelect = document.getElementById('passengers');
	const formErrorsWrapper = document.getElementById('form-errors-wrapper');
	const formErrorsList = document.getElementById('form-errors');

	const guard = window.BehaviorGuard;
	if (guard && typeof guard.stageReady === 'function') {
		guard.stageReady('search', {
			page: 'home',
			description: 'Form pencarian tiket',
		});
	}

	const logEvent = (name, data = {}) => {
		if (guard && typeof guard.log === 'function') {
			guard.log(name, data);
		}
	};

	if (menuButton && menuPanel) {
		menuButton.addEventListener('click', () => {
			menuPanel.classList.toggle('hidden');
			menuButton.classList.toggle('bg-indigo-50');
			menuButton.classList.toggle('border-indigo-300');
		});
	}

	const normalize = (value) => (typeof value === 'string' ? value.trim().toLowerCase() : '');

	const findStationByLabel = (value) => {
		if (!value) {
			return null;
		}

		const normalized = normalize(value);
		const exactMatch = stationOptions.find((station) => {
			const label = normalize(station.label);
			const name = normalize(station.name);
			const code = normalize(station.code);
			const city = normalize(station.city);
			return (
				label === normalized ||
				name === normalized ||
				code === normalized ||
				`${name} (${city})` === normalized
			);
		});

		if (exactMatch) {
			return exactMatch;
		}

		return (
			stationOptions.find((station) => {
				const label = normalize(station.label);
				const name = normalize(station.name);
				const code = normalize(station.code);
				const city = normalize(station.city);
				return (
					label.includes(normalized) ||
					name.includes(normalized) ||
					city.includes(normalized) ||
					code.includes(normalized)
				);
			}) ?? null
		);
	};

	const filterStations = (query) => {
		const normalized = normalize(query);
		if (!normalized) {
			return stationOptions.slice(0, 8);
		}

		const matches = stationOptions
			.filter((station) => {
				const label = normalize(station.label);
				const name = normalize(station.name);
				const code = normalize(station.code);
				const city = normalize(station.city);
				return (
					label.includes(normalized) ||
					name.includes(normalized) ||
					city.includes(normalized) ||
					code.includes(normalized)
				);
			})
			.slice(0, 10);

		if (matches.length) {
			return matches;
		}

		return stationOptions.slice(0, 6);
	};

	const attachStationAutocomplete = (input, hidden, container, contextKey) => {
		if (!input || !hidden || !container) {
			return;
		}

		const nativeListId = input.getAttribute('list');
		if (nativeListId) {
			input.setAttribute('data-native-list', nativeListId);
			input.removeAttribute('list');
		}

		let hideTimeout = null;

		const clearHideTimeout = () => {
			if (hideTimeout) {
				window.clearTimeout(hideTimeout);
				hideTimeout = null;
			}
		};

		const closeSuggestions = () => {
			container.classList.add('hidden');
			container.innerHTML = '';
		};

		const handleSelect = (station) => {
			if (!station) {
				return;
			}

			input.value = station.label;
			hidden.value = station.code;
			input.dataset.stationCode = station.code;
			closeSuggestions();
			syncStationValue(input, hidden);
			logEvent('search_station_selected', {
				context: contextKey,
				code: station.code,
				label: station.label,
			});
		};

		const renderSuggestions = () => {
			const matches = filterStations(input.value || '');
			container.innerHTML = '';

			if (!matches.length) {
				closeSuggestions();
				return;
			}

			const fragment = document.createDocumentFragment();
			matches.forEach((station) => {
				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'station-suggestion-item';
				button.setAttribute('data-station-code', station.code);
				button.setAttribute('data-station-label', station.label);

				const meta = document.createElement('div');
				meta.className = 'station-suggestion-meta';
				const title = document.createElement('span');
				title.textContent = station.label;
				title.className = 'station-suggestion-title';
				const city = document.createElement('span');
				city.textContent = station.city;
				city.className = 'station-suggestion-city';
				meta.appendChild(title);
				meta.appendChild(city);

				const code = document.createElement('span');
				code.textContent = station.code;
				code.className = 'station-suggestion-code';

				button.appendChild(meta);
				button.appendChild(code);

				button.addEventListener('click', () => {
					handleSelect(station);
				});

				fragment.appendChild(button);
			});

			container.appendChild(fragment);
			container.classList.remove('hidden');
			logEvent('search_station_suggestions_shown', {
				context: contextKey,
				query: input.value || null,
				results: matches.length,
			});
		};

		const scheduleHide = () => {
			hideTimeout = window.setTimeout(() => {
				closeSuggestions();
			}, 180);
		};

		input.addEventListener('focus', () => {
			clearHideTimeout();
			renderSuggestions();
		});

		input.addEventListener('input', () => {
			clearHideTimeout();
			renderSuggestions();
		});

		input.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') {
				event.preventDefault();
				closeSuggestions();
			}
		});

		input.addEventListener('blur', scheduleHide);

		container.addEventListener('mousedown', (event) => {
			event.preventDefault();
			clearHideTimeout();
		});

		container.addEventListener('focusout', scheduleHide);

		document.addEventListener('click', (event) => {
			if (event.target === input || container.contains(event.target)) {
				return;
			}
			closeSuggestions();
		});
	};

	const syncStationValue = (input, hidden) => {
		if (!input || !hidden) {
			return;
		}

		const match = findStationByLabel(input.value);
		if (match) {
			hidden.value = match.code;
			input.dataset.stationCode = match.code;
			return;
		}

		if (!input.value.trim()) {
			hidden.value = '';
			delete input.dataset.stationCode;
			return;
		}

		hidden.value = '';
		delete input.dataset.stationCode;
	};

	if (originInput && originHidden) {
		['input', 'change', 'blur'].forEach((eventName) => {
			originInput.addEventListener(eventName, () => syncStationValue(originInput, originHidden));
		});
		syncStationValue(originInput, originHidden);
		attachStationAutocomplete(originInput, originHidden, originSuggestions, 'origin');

		originInput.addEventListener('blur', () => {
			logEvent('search_input_origin', {
				value: originInput.value,
				code: originHidden.value,
			});
		});
	}

	if (destinationInput && destinationHidden) {
		['input', 'change', 'blur'].forEach((eventName) => {
			destinationInput.addEventListener(eventName, () => syncStationValue(destinationInput, destinationHidden));
		});
		syncStationValue(destinationInput, destinationHidden);
		attachStationAutocomplete(destinationInput, destinationHidden, destinationSuggestions, 'destination');

		destinationInput.addEventListener('blur', () => {
			logEvent('search_input_destination', {
				value: destinationInput.value,
				code: destinationHidden.value,
			});
		});
	}

	const renderErrors = (messages) => {
		if (!formErrorsWrapper || !formErrorsList) {
			return;
		}

		formErrorsList.innerHTML = '';
		if (!messages.length) {
			formErrorsWrapper.classList.add('hidden');
			return;
		}

		messages.forEach((message) => {
			const li = document.createElement('li');
			li.textContent = message;
			formErrorsList.appendChild(li);
		});

		formErrorsWrapper.classList.remove('hidden');
	};

	if (searchForm) {
		searchForm.addEventListener('submit', (event) => {
			if (originSuggestions) {
				originSuggestions.classList.add('hidden');
				originSuggestions.innerHTML = '';
			}
			if (destinationSuggestions) {
				destinationSuggestions.classList.add('hidden');
				destinationSuggestions.innerHTML = '';
			}

			syncStationValue(originInput, originHidden);
			syncStationValue(destinationInput, destinationHidden);

			const messages = [];

			if (!originHidden?.value) {
				messages.push('Silakan pilih stasiun keberangkatan yang valid.');
			}

			if (!destinationHidden?.value) {
				messages.push('Silakan pilih stasiun tujuan yang valid.');
			}

			if (originHidden?.value && destinationHidden?.value && originHidden.value === destinationHidden.value) {
				messages.push('Stasiun keberangkatan dan tujuan tidak boleh sama.');
			}

			if (departureDateInput) {
				const minDate = departureDateInput.min;
				const selectedDate = departureDateInput.value;

				if (!selectedDate) {
					messages.push('Silakan pilih tanggal keberangkatan.');
				} else if (minDate && selectedDate < minDate) {
					messages.push('Tanggal keberangkatan tidak boleh sebelum hari ini.');
				}
			}

			if (passengersSelect && !passengersSelect.value) {
				messages.push('Silakan pilih jumlah penumpang dewasa.');
			}

			if (messages.length) {
				event.preventDefault();
				renderErrors(messages);
				logEvent('search_validation_failed', {
					messages,
				});
				return;
			}

			renderErrors([]);
			logEvent('search_validation_passed', {
				origin: originHidden?.value,
				destination: destinationHidden?.value,
				departure: departureDateInput?.value,
				passengers: passengersSelect?.value,
			});

			if (guard && typeof guard.flush === 'function') {
				guard
					.flush('search-submitted', {
						keepAlive: true,
						context: {
							origin: originHidden?.value,
							destination: destinationHidden?.value,
							departure: departureDateInput?.value,
							passengers: passengersSelect?.value,
						},
					})
					.catch(() => undefined);
			}
		});
	}

	const placeholderSelects = document.querySelectorAll('[data-placeholder-select]');

	const syncSelectPlaceholder = (select) => {
		if (!select) {
			return;
		}
		const hasValue = Boolean(select.value);
		select.classList.toggle('text-slate-700', hasValue);
		select.classList.toggle('text-slate-400', !hasValue);
	};

	placeholderSelects.forEach((select) => {
		syncSelectPlaceholder(select);
		select.addEventListener('change', () => syncSelectPlaceholder(select));
		select.addEventListener('blur', () => syncSelectPlaceholder(select));
		select.addEventListener('change', () => {
			logEvent('search_passenger_change', { passengers: select.value });
		});
	});

	if (departureDateInput) {
		const minDate = departureDateInput.min;
		const enforceMinDate = () => {
			if (!minDate) {
				return;
			}

			if (departureDateInput.value && departureDateInput.value < minDate) {
				departureDateInput.value = minDate;
			}
		};

		enforceMinDate();
		departureDateInput.addEventListener('change', enforceMinDate);
		departureDateInput.addEventListener('change', () => {
			logEvent('search_departure_change', { departure: departureDateInput.value });
		});
	}
});
