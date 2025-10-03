document.addEventListener('DOMContentLoaded', () => {
	const root = document.querySelector('.home-page-body');
	if (!root) {
		return;
	}

	let stationOptions = [];
	const stationsRaw = root.getAttribute('data-stations');
	if (stationsRaw) {
		try {
			stationOptions = JSON.parse(stationsRaw);
		} catch (error) {
			console.warn('Tidak dapat membaca data stasiun:', error);
		}
	}

	const menuButton = document.querySelector('[data-menu-button]');
	const menuPanel = document.querySelector('[data-menu-panel]');
	const searchForm = document.getElementById('search-form');
	const originInput = document.getElementById('origin-display');
	const destinationInput = document.getElementById('destination-display');
	const originHidden = document.getElementById('origin');
	const destinationHidden = document.getElementById('destination');
	const departureDateInput = document.getElementById('departure-date');
	const passengersSelect = document.getElementById('passengers');
	const formErrorsWrapper = document.getElementById('form-errors-wrapper');
	const formErrorsList = document.getElementById('form-errors');

	if (menuButton && menuPanel) {
		menuButton.addEventListener('click', () => {
			menuPanel.classList.toggle('hidden');
			menuButton.classList.toggle('bg-indigo-50');
			menuButton.classList.toggle('border-indigo-300');
		});
	}

	const findStationByLabel = (value) => {
		if (!value) {
			return null;
		}

		const normalized = value.trim().toLowerCase();
		return stationOptions.find((station) => {
			const label = station.label?.toLowerCase?.() ?? '';
			const name = station.name?.toLowerCase?.() ?? '';
			const code = station.code?.toLowerCase?.() ?? '';
			return label === normalized || name === normalized || code === normalized;
		});
	};

	const syncStationValue = (input, hidden) => {
		if (!input || !hidden) {
			return;
		}

		const match = findStationByLabel(input.value);
		hidden.value = match ? match.code : '';
	};

	if (originInput && originHidden) {
		['input', 'change', 'blur'].forEach((eventName) => {
			originInput.addEventListener(eventName, () => syncStationValue(originInput, originHidden));
		});
		syncStationValue(originInput, originHidden);
	}

	if (destinationInput && destinationHidden) {
		['input', 'change', 'blur'].forEach((eventName) => {
			destinationInput.addEventListener(eventName, () => syncStationValue(destinationInput, destinationHidden));
		});
		syncStationValue(destinationInput, destinationHidden);
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
				return;
			}

			renderErrors([]);
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
	}
});
