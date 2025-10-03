(function () {
	const form = document.querySelector('[data-reservation-form]');
	const selectionStorageKey = 'kaizen:selectedRoute';

	let selectionPayloadInput = null;
	if (form) {
		selectionPayloadInput = form.querySelector('[data-selection-payload-input]');
		if (!selectionPayloadInput) {
			selectionPayloadInput = document.createElement('input');
			selectionPayloadInput.type = 'hidden';
			selectionPayloadInput.name = 'selection_payload';
			selectionPayloadInput.dataset.selectionPayloadInput = 'true';
			form.appendChild(selectionPayloadInput);
		}
	}

	const setSelectionPayload = (payload) => {
		if (!selectionPayloadInput) {
			return;
		}

		if (!payload) {
			selectionPayloadInput.value = '';
			return;
		}

		try {
			selectionPayloadInput.value = JSON.stringify(payload);
		} catch (error) {
			selectionPayloadInput.value = '';
		}
	};

	const selectionCard = document.querySelector('[data-selection-card]');
	const selectionBody = selectionCard?.querySelector('[data-selection-body]');
	const selectionEmpty = selectionCard?.querySelector('[data-selection-empty]');
	const selectionTitle = selectionCard?.querySelector('[data-selection-title]');
	const selectionSubtitle = selectionCard?.querySelector('[data-selection-subtitle]');
	const selectionPrice = selectionCard?.querySelector('[data-selection-price]');
	const selectionTrain = selectionCard?.querySelector('[data-selection-train]');
	const selectionDuration = selectionCard?.querySelector('[data-selection-duration]');
	const selectionTimeline = selectionCard?.querySelector('[data-selection-timeline]');

	const formatCurrency = (value) => {
		if (typeof value !== 'number') {
			return null;
		}

		return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
	};

	const clearSelectionView = () => {
		if (selectionBody) {
			selectionBody.classList.add('hidden');
		}

		if (selectionEmpty) {
			selectionEmpty.classList.remove('hidden');
		}

		if (selectionTimeline) {
			selectionTimeline.innerHTML = '';
		}
	};

	const renderSelection = (selection) => {
		if (!selectionCard || !selection || typeof selection !== 'object') {
			clearSelectionView();
			return;
		}

		if (selectionEmpty) {
			selectionEmpty.classList.add('hidden');
		}

		if (selectionBody) {
			selectionBody.classList.remove('hidden');
		}

		if (selectionTitle) {
			selectionTitle.textContent = selection.title || selection.summary?.title || 'Rute Dipilih';
		}

		if (selectionSubtitle) {
			const subtitle = selection.summary?.title || selection.subtitle || '';
			selectionSubtitle.textContent = subtitle;
			selectionSubtitle.classList.toggle('hidden', !subtitle);
		}

		if (selectionPrice) {
			const priceLabel = selection.price_label || formatCurrency(selection.price);
			selectionPrice.textContent = priceLabel ? `Rp ${priceLabel.replace(/Rp\s?/i, '').trim()}` : 'Rp —';
		}

		if (selectionTrain) {
			selectionTrain.textContent = selection.train_name || selection.train_code || '—';
		}

		if (selectionDuration) {
			selectionDuration.textContent = selection.duration_label || '—';
		}

		if (selectionTimeline) {
			selectionTimeline.innerHTML = '';
			const timeline = Array.isArray(selection.detail_timeline) ? [...selection.detail_timeline] : [];

			if (timeline.length === 0 && Array.isArray(selection.legs)) {
				selection.legs.forEach((leg, index) => {
					timeline.push(
						{
							time: leg?.departure?.time || '',
							station: leg?.departure?.station || '',
							label: leg?.transport_name || leg?.mode_label || 'Perjalanan',
							type: 'departure',
						},
						{
							time: leg?.arrival?.time || '',
							station: leg?.arrival?.station || '',
							label: index === selection.legs.length - 1 ? 'Tiba di tujuan' : 'Transit',
							type: leg?.arrival?.type || (index === selection.legs.length - 1 ? 'arrival' : 'transfer'),
						}
					);
				});
			}

			timeline.forEach((item) => {
				const element = document.createElement('li');
				element.className = 'reservation-selection-timeline-item';

				const dot = document.createElement('div');
				dot.className = 'reservation-selection-timeline-dot';
				if (item.type) {
					dot.dataset.type = item.type;
				}

				const content = document.createElement('div');
				content.className = 'reservation-selection-timeline-content';

				const header = document.createElement('div');
				header.className = 'reservation-selection-timeline-header';

				const time = document.createElement('span');
				time.className = 'reservation-selection-time';
				time.textContent = item.time || '—';

				const station = document.createElement('span');
				station.className = 'reservation-selection-station';
				station.textContent = item.station || '';

				header.append(time, station);

				const label = document.createElement('p');
				label.className = 'reservation-selection-label-body';
				label.textContent = item.label || '';

				content.append(header, label);

				if (item.description) {
					const description = document.createElement('p');
					description.className = 'reservation-selection-description';
					description.textContent = item.description;
					content.append(description);
				}

				element.append(dot, content);
				selectionTimeline.append(element);
			});
		}
	};

	const loadSelectionFromStorage = () => {
		if (!selectionCard) {
			return;
		}

		let storedValue = null;
		try {
			storedValue = sessionStorage.getItem(selectionStorageKey);
		} catch (error) {
			console.warn('Tidak dapat membaca data rute terpilih dari sessionStorage:', error);
		}

		if (!storedValue) {
			clearSelectionView();
			setSelectionPayload(null);
			return;
		}

		try {
			const selection = JSON.parse(storedValue);
			renderSelection(selection);
			setSelectionPayload(selection);
		} catch (error) {
			console.error('Gagal memuat data rute terpilih:', error);
			clearSelectionView();
			setSelectionPayload(null);
		}
	};

	loadSelectionFromStorage();

	if (!selectionPayloadInput) {
		setSelectionPayload(null);
	}

	if (!form) {
		return;
	}

	const alertBox = document.querySelector('[data-reservation-alert]');
	const submitButton = document.querySelector('[data-submit-button]');
	const submitButtonInitialLabel = submitButton?.textContent ?? '';
	const fieldInputs = Array.from(form.querySelectorAll('[data-field]'));
	const fieldKeys = [...new Set(fieldInputs.map((input) => input.dataset.field))];

	const validators = {
		full_name(value) {
			if (!value.trim()) {
				return 'Nama lengkap wajib diisi.';
			}

			if (value.trim().length < 3) {
				return 'Nama minimal terdiri dari 3 karakter.';
			}

			return null;
		},
		national_id(value) {
			const cleaned = value.replace(/\D/g, '');

			if (!cleaned) {
				return 'NIK wajib diisi.';
			}

			if (cleaned.length !== 16) {
				return 'NIK harus terdiri dari 16 digit.';
			}

			return null;
		},
		phone_number(value) {
			const cleaned = value.replace(/[^0-9+]/g, '');

			if (!cleaned) {
				return 'Nomor handphone wajib diisi.';
			}

			if (!/^0\d{8,14}$/.test(cleaned)) {
				return 'Nomor handphone tidak valid. Gunakan format 0XXXXXXXXXX.';
			}

			return null;
		},
	};

	const getFieldElements = (fieldName) => {
		const input = form.querySelector(`[data-field="${fieldName}"]`);
		const error = form.querySelector(`[data-error-for="${fieldName}"]`);
		const wrapper = input?.closest('[data-field-wrapper]');

		return { input, error, wrapper };
	};

	const setFieldState = (fieldName, message) => {
		const { input, error, wrapper } = getFieldElements(fieldName);

		if (!input || !error || !wrapper) {
			return;
		}

		error.textContent = message ?? '';
		wrapper.classList.toggle('is-invalid', Boolean(message));
		input.setAttribute('aria-invalid', message ? 'true' : 'false');

		if (message) {
			input.setAttribute('aria-describedby', error.id || `${fieldName}-error`);
		} else {
			input.removeAttribute('aria-describedby');
		}
	};

	const validateField = (fieldName) => {
		const { input } = getFieldElements(fieldName);
		if (!input) {
			return true;
		}

		const fieldType = input.dataset.fieldType;
		const validator = validators[fieldType];

		if (!validator) {
			return true;
		}

		const result = validator(input.value ?? '');
		setFieldState(fieldName, result);

		return !result;
	};

	const validateAllFields = () => {
		let isValid = true;
		fieldKeys.forEach((field) => {
			const fieldValid = validateField(field);
			if (!fieldValid && isValid) {
				const { input } = getFieldElements(field);
				input?.focus();
			}
			isValid = isValid && fieldValid;
		});

		return isValid;
	};

	const resetAlert = () => {
		if (alertBox) {
			alertBox.classList.add('hidden');
		}
	};

	fieldInputs.forEach((input) => {
		const fieldKey = input.dataset.field;
		if (!fieldKey) {
			return;
		}

		input.addEventListener('input', () => {
			validateField(fieldKey);
			resetAlert();
		});

		input.addEventListener('blur', () => {
			validateField(fieldKey);
		});
	});

	form.addEventListener('submit', (event) => {
		const isValid = validateAllFields();

		if (!isValid) {
			event.preventDefault();
			resetAlert();

			if (submitButton) {
				submitButton.disabled = false;
				submitButton.classList.remove('is-loading');
				submitButton.textContent = submitButtonInitialLabel;
			}

			return;
		}

		if (submitButton) {
			submitButton.disabled = true;
			submitButton.classList.add('is-loading');
			submitButton.textContent = 'Memproses...';
		}
	});
})();
