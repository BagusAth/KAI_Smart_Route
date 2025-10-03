(function () {
	const bodyElement = document.body;

	const safeParse = (value, fallback) => {
		if (!value) {
			return fallback;
		}

		try {
			return JSON.parse(value);
		} catch (error) {
			return fallback;
		}
	};

	const selectionStorageKey = 'kaizen:selectedRoute';

	const passengersData = safeParse(bodyElement?.dataset.passengers, []);
	const seatConfigData = safeParse(bodyElement?.dataset.seatConfig, { coaches: [], columns: [] });
	const routeSelectionFallback = safeParse(bodyElement?.dataset.routeSelection, null);
	const passengerCount = Number(bodyElement?.dataset.passengerCount || passengersData.length || 0);

	const selectionCard = document.querySelector('[data-selection-card]');
	const selectionTitle = selectionCard?.querySelector('[data-selection-title]');
	const selectionSubtitle = selectionCard?.querySelector('[data-selection-subtitle]');
	const selectionPrice = selectionCard?.querySelector('[data-selection-price]');
	const selectionTrain = selectionCard?.querySelector('[data-selection-train]');
	const selectionDuration = selectionCard?.querySelector('[data-selection-duration]');
	const selectionClass = selectionCard?.querySelector('[data-selection-class]');
	const selectionTimeline = selectionCard?.querySelector('[data-selection-timeline]');

	const formatCurrency = (value) => {
		if (typeof value !== 'number') {
			return null;
		}

		return new Intl.NumberFormat('id-ID', {
			style: 'currency',
			currency: 'IDR',
			maximumFractionDigits: 0,
		}).format(value);
	};

	const clearSelectionView = () => {
		if (!selectionCard) {
			return;
		}

		if (selectionTitle) selectionTitle.textContent = 'Rute Dipilih';
		if (selectionSubtitle) selectionSubtitle.textContent = '';
		if (selectionPrice) selectionPrice.textContent = 'Rp —';
		if (selectionTrain) selectionTrain.textContent = '—';
		if (selectionDuration) selectionDuration.textContent = '—';
		if (selectionClass) selectionClass.textContent = bodyElement?.dataset.seatClass || '—';
		if (selectionTimeline) selectionTimeline.innerHTML = '';
	};

	const renderSelection = (selection) => {
		if (!selectionCard || !selection || typeof selection !== 'object') {
			clearSelectionView();
			return;
		}

		if (selectionTitle) {
			selectionTitle.textContent = selection.title || selection.summary?.title || 'Rute Dipilih';
		}

		if (selectionSubtitle) {
			const subtitle = selection.summary?.subtitle || selection.subtitle || '';
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

		if (selectionClass) {
			selectionClass.textContent = selection.ticket_class || bodyElement?.dataset.seatClass || '—';
		}

		if (!selectionTimeline) {
			return;
		}

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
	};

	const resolveSelection = () => {
		let storedValue = null;
		try {
			storedValue = sessionStorage.getItem(selectionStorageKey);
		} catch (error) {
			storedValue = null;
		}

		if (storedValue) {
			try {
				return JSON.parse(storedValue);
			} catch (error) {
				return routeSelectionFallback;
			}
		}

		return routeSelectionFallback;
	};

	const resolvedSelection = resolveSelection();
	if (resolvedSelection) {
		renderSelection(resolvedSelection);
	} else {
		clearSelectionView();
	}

	const openSeatButton = document.querySelector('[data-seat-open]');
	const seatModal = document.querySelector('[data-seat-modal]');

	if (!seatModal) {
		return;
	}

	const closeSeatButtons = Array.from(seatModal.querySelectorAll('[data-seat-close]'));
	const seatForm = seatModal.querySelector('[data-seat-form]');
	const passengerItems = Array.from(seatModal.querySelectorAll('[data-passenger-index]'));
	const coachTabs = Array.from(seatModal.querySelectorAll('[data-coach-tab]'));
	const coachPanels = Array.from(seatModal.querySelectorAll('[data-coach-panel]'));
	const seatButtons = Array.from(seatModal.querySelectorAll('[data-seat-button]'));
	const seatCounter = seatModal.querySelector('[data-seat-counter]');
	const seatError = seatModal.querySelector('[data-seat-error]');
	const seatStatusText = document.querySelector('[data-seat-status]');
	const paymentButton = document.querySelector('[data-payment-button]');

	const seatInputsCoach = new Map();
	const seatInputsSeat = new Map();
	const modalSeatLabels = new Map();
	const summarySeatLabels = new Map();

	const makeSeatKey = (coachCode, seatCode) => `${coachCode}::${seatCode}`;
	const normaliseCode = (value) => (typeof value === 'string' ? value.trim().toUpperCase() : '');
	const getCoachMeta = (code) => seatConfigData.coaches.find((coach) => normaliseCode(coach.code) === normaliseCode(code)) || null;
	const formatSeatLabel = (coachCode, seatCode) => {
		const coach = getCoachMeta(coachCode);
		const coachLabel = coach?.label || coachCode || 'Gerbong';
		return `${coachLabel} · Kursi ${seatCode}`;
	};

	const seatButtonLookup = new Map();
	const baseSeatStatus = new Map();
	const occupancy = new Map();
	const selectedSeats = new Map();

	passengerItems.forEach((item) => {
		const index = Number(item.dataset.passengerIndex);
		const coachInput = seatModal.querySelector(`[data-seat-input-coach="${index}"]`);
		const seatInput = seatModal.querySelector(`[data-seat-input-seat="${index}"]`);
		const modalLabel = seatModal.querySelector(`[data-passenger-seat-option="${index}"]`);
		const summaryLabel = document.querySelector(`[data-passenger-seat="${index}"]`);

		if (coachInput) seatInputsCoach.set(index, coachInput);
		if (seatInput) seatInputsSeat.set(index, seatInput);
		if (modalLabel) modalSeatLabels.set(index, modalLabel);
		if (summaryLabel) summarySeatLabels.set(index, summaryLabel);

		const coachCode = normaliseCode(coachInput?.value || '');
		const seatCode = normaliseCode(seatInput?.value || '');
		if (coachCode && seatCode) {
			const key = makeSeatKey(coachCode, seatCode);
			const label = modalLabel?.dataset.seatLabel || formatSeatLabel(coachCode, seatCode);
			selectedSeats.set(index, { coach: coachCode, seat: seatCode, label });
			occupancy.set(key, index);
		}
	});

	seatButtons.forEach((button) => {
		const coachCode = normaliseCode(button.dataset.coachCode);
		const seatCode = normaliseCode(button.dataset.seatCode);
		const key = makeSeatKey(coachCode, seatCode);
		seatButtonLookup.set(key, button);
		baseSeatStatus.set(key, button.dataset.seatBaseStatus || 'available');

		const ownerAttr = button.dataset.seatOwner;
		if (ownerAttr !== undefined && ownerAttr !== '') {
			const ownerIndex = Number(ownerAttr);
			occupancy.set(key, ownerIndex);
			if (!selectedSeats.has(ownerIndex)) {
				selectedSeats.set(ownerIndex, {
					coach: coachCode,
					seat: seatCode,
					label: button.dataset.seatLabel || formatSeatLabel(coachCode, seatCode),
				});
			}
		}
	});

	const updatePassengerLabels = () => {
		modalSeatLabels.forEach((labelElement, index) => {
			const selection = selectedSeats.get(index);
			if (selection && selection.coach && selection.seat) {
				const labelText = selection.label || formatSeatLabel(selection.coach, selection.seat);
				labelElement.textContent = labelText;
				labelElement.dataset.seatEmpty = 'false';
				labelElement.dataset.seatLabel = labelText;
			} else {
				labelElement.textContent = 'Belum dipilih';
				labelElement.dataset.seatEmpty = 'true';
				labelElement.dataset.seatLabel = '';
			}
		});

		summarySeatLabels.forEach((labelElement, index) => {
			const selection = selectedSeats.get(index);
			if (selection && selection.coach && selection.seat) {
				const labelText = selection.label || formatSeatLabel(selection.coach, selection.seat);
				labelElement.textContent = labelText;
				labelElement.dataset.seatEmpty = 'false';
			} else {
				labelElement.textContent = 'Belum dipilih';
				labelElement.dataset.seatEmpty = 'true';
			}
		});
	};

	const updateSeatCounter = () => {
		const selectedCount = Array.from(selectedSeats.values()).filter((seat) => seat && seat.coach && seat.seat).length;
		if (seatCounter) {
			seatCounter.textContent = `${selectedCount} / ${passengerCount} kursi dipilih`;
		}

		const allSelected = selectedCount === passengerCount && passengerCount > 0;

		if (seatStatusText) {
			seatStatusText.textContent = allSelected
				? 'Semua penumpang telah memiliki kursi.'
				: 'Belum semua penumpang memiliki kursi. Silakan lakukan pemilihan kursi.';
		}

		if (paymentButton) {
			paymentButton.classList.toggle('is-disabled', !allSelected);
			paymentButton.setAttribute('aria-disabled', allSelected ? 'false' : 'true');
			if (allSelected) {
				const target = paymentButton.dataset.target;
				if (target) {
					paymentButton.setAttribute('href', target);
				}
			} else {
				paymentButton.setAttribute('href', 'javascript:void(0);');
			}
		}
	};

	const refreshSeatButtons = (activePassengerIndex) => {
		seatButtons.forEach((button) => {
			const coachCode = normaliseCode(button.dataset.coachCode);
			const seatCode = normaliseCode(button.dataset.seatCode);
			const key = makeSeatKey(coachCode, seatCode);
			const owner = occupancy.get(key);
			const baseStatus = baseSeatStatus.get(key) || 'available';
			const isBlocked = baseStatus === 'blocked';
			const isOwned = owner !== undefined;
			const isOwnedByActive = owner === activePassengerIndex;

			button.dataset.seatOwner = isOwned ? String(owner) : '';
			button.dataset.seatStatus = isBlocked ? 'blocked' : isOwned ? 'selected' : 'available';
			button.classList.toggle('is-blocked', isBlocked);
			button.classList.toggle('is-selected', isOwned);
			button.classList.toggle('is-active', isOwnedByActive);
			button.setAttribute('aria-pressed', isOwnedByActive ? 'true' : 'false');

			if (isBlocked) {
				button.disabled = true;
			} else if (isOwned && !isOwnedByActive) {
				button.disabled = true;
			} else {
				button.disabled = false;
			}
		});
	};

	const assignSeat = (passengerIndex, coachCode, seatCode, label) => {
		const normalizedCoach = normaliseCode(coachCode);
		const normalizedSeat = normaliseCode(seatCode);
		const key = makeSeatKey(normalizedCoach, normalizedSeat);

		const current = selectedSeats.get(passengerIndex);
		if (current) {
			const oldKey = makeSeatKey(current.coach, current.seat);
			if (oldKey === key) {
				return;
			}
			const owner = occupancy.get(oldKey);
			if (owner === passengerIndex) {
				occupancy.delete(oldKey);
			}
		}

		occupancy.set(key, passengerIndex);
		selectedSeats.set(passengerIndex, {
			coach: normalizedCoach,
			seat: normalizedSeat,
			label: label || formatSeatLabel(normalizedCoach, normalizedSeat),
		});

		const coachInput = seatInputsCoach.get(passengerIndex);
		const seatInput = seatInputsSeat.get(passengerIndex);
		if (coachInput) coachInput.value = normalizedCoach;
		if (seatInput) seatInput.value = normalizedSeat;
	};

	const clearSeat = (passengerIndex) => {
		const current = selectedSeats.get(passengerIndex);
		if (!current) {
			return;
		}

		const key = makeSeatKey(current.coach, current.seat);
		const owner = occupancy.get(key);
		if (owner === passengerIndex) {
			occupancy.delete(key);
		}
		selectedSeats.delete(passengerIndex);

		const coachInput = seatInputsCoach.get(passengerIndex);
		const seatInput = seatInputsSeat.get(passengerIndex);
		if (coachInput) coachInput.value = '';
		if (seatInput) seatInput.value = '';
	};

	const setActivePassenger = (index) => {
		passengerItems.forEach((item) => {
			const itemIndex = Number(item.dataset.passengerIndex);
			item.dataset.passengerActive = itemIndex === index ? 'true' : 'false';
		});
	};

	const setActiveCoach = (coachCode) => {
		const normalizedCoach = normaliseCode(coachCode);
		coachTabs.forEach((tab) => {
			const isActive = normaliseCode(tab.dataset.coachCode) === normalizedCoach;
			tab.dataset.coachActive = isActive ? 'true' : 'false';
			tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
			tab.classList.toggle('is-active', isActive);
		});

		coachPanels.forEach((panel) => {
			const isActive = normaliseCode(panel.dataset.coachPanel) === normalizedCoach;
			panel.dataset.coachActive = isActive ? 'true' : 'false';
			panel.hidden = !isActive;
		});
	};

	let activePassengerIndex = passengerItems.length ? Number(passengerItems[0].dataset.passengerIndex) : null;
	let activeCoachCode = coachTabs.length ? coachTabs[0].dataset.coachCode : seatConfigData.coaches[0]?.code || null;

	if (activePassengerIndex !== null) {
		const currentSelection = selectedSeats.get(activePassengerIndex);
		if (currentSelection?.coach) {
			activeCoachCode = currentSelection.coach;
		}
	}

	setActivePassenger(activePassengerIndex ?? -1);
	setActiveCoach(activeCoachCode);
	refreshSeatButtons(activePassengerIndex);
	updatePassengerLabels();
	updateSeatCounter();

	passengerItems.forEach((item) => {
		item.addEventListener('click', () => {
			const index = Number(item.dataset.passengerIndex);
			if (activePassengerIndex === index) {
				return;
			}
			activePassengerIndex = index;
			setActivePassenger(index);

			const currentSelection = selectedSeats.get(index);
			if (currentSelection?.coach) {
				activeCoachCode = currentSelection.coach;
				setActiveCoach(activeCoachCode);
			}

			refreshSeatButtons(activePassengerIndex);
		});
	});

	coachTabs.forEach((tab) => {
		tab.addEventListener('click', () => {
			const coachCode = tab.dataset.coachCode;
			if (!coachCode) {
				return;
			}
			activeCoachCode = coachCode;
			setActiveCoach(activeCoachCode);
			refreshSeatButtons(activePassengerIndex);
		});
	});

	seatButtons.forEach((button) => {
		button.addEventListener('click', () => {
			if (button.disabled || activePassengerIndex === null) {
				return;
			}

			const coachCode = normaliseCode(button.dataset.coachCode);
			const seatCode = normaliseCode(button.dataset.seatCode);
			const key = makeSeatKey(coachCode, seatCode);
			const owner = occupancy.get(key);

			if (owner !== undefined && owner !== activePassengerIndex) {
				return;
			}

			const currentSelection = selectedSeats.get(activePassengerIndex);
			if (currentSelection && currentSelection.coach === coachCode && currentSelection.seat === seatCode) {
				clearSeat(activePassengerIndex);
			} else {
				assignSeat(activePassengerIndex, coachCode, seatCode, button.dataset.seatLabel);
				activeCoachCode = coachCode;
				setActiveCoach(activeCoachCode);
			}

			refreshSeatButtons(activePassengerIndex);
			updatePassengerLabels();
			updateSeatCounter();
		});
	});

	const openModal = () => {
		seatModal.classList.remove('hidden');
		if (bodyElement) {
			bodyElement.style.overflow = 'hidden';
		}
		if (seatError) {
			seatError.hidden = true;
		}
	};

	const closeModal = () => {
		seatModal.classList.add('hidden');
		if (bodyElement) {
			bodyElement.style.overflow = '';
		}
	};

	if (openSeatButton) {
		openSeatButton.addEventListener('click', openModal);
	}

	closeSeatButtons.forEach((button) => {
		button.addEventListener('click', closeModal);
	});

	const modalBackdrop = seatModal.querySelector('.seat-modal-backdrop');
	if (modalBackdrop) {
		modalBackdrop.addEventListener('click', closeModal);
	}

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && !seatModal.classList.contains('hidden')) {
			closeModal();
		}
	});

	if (seatForm) {
		seatForm.addEventListener('submit', (event) => {
			const selectedCount = Array.from(selectedSeats.values()).filter((seat) => seat && seat.coach && seat.seat).length;
			const allSelected = selectedCount === passengerCount && passengerCount > 0;
			if (!allSelected) {
				event.preventDefault();
				if (seatError) {
					seatError.hidden = false;
					seatError.textContent = 'Silakan pilih kursi untuk setiap penumpang sebelum melanjutkan.';
				}
				return;
			}

			if (seatError) {
				seatError.hidden = true;
			}
		});
	}
})();
