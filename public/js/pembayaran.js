(function () {
	const paymentButton = document.querySelector('.payment-summary-button');
	const modal = document.querySelector('[data-trust-modal]');
	if (!paymentButton || !modal) {
		return;
	}

	const guard = window.BehaviorGuard;
	if (guard && typeof guard.stageReady === 'function') {
		guard.stageReady('payment', {
			page: 'pembayaran',
			description: 'Verifikasi trust score sebelum pembayaran',
		});
	}

	const logEvent = (name, data = {}) => {
		if (guard && typeof guard.log === 'function') {
			guard.log(name, data);
		}
	};

	const scoreElement = modal.querySelector('[data-trust-score]');
	const statusElement = modal.querySelector('[data-trust-status]');
	const messageElement = modal.querySelector('[data-trust-message]');
	const sessionElement = modal.querySelector('[data-trust-session]');
	const actionElement = modal.querySelector('[data-trust-action]');
	const continueButton = modal.querySelector('[data-trust-continue]');
	const closeButtons = Array.from(modal.querySelectorAll('[data-trust-close]'));
	const cancelButton = modal.querySelector('.payment-verification-secondary');

	const setBodyScroll = (lock) => {
		if (!document.body) {
			return;
		}
		document.body.style.overflow = lock ? 'hidden' : '';
	};

	const openModal = () => {
		modal.classList.remove('hidden');
		modal.setAttribute('aria-hidden', 'false');
		setBodyScroll(true);
	};

	const closeModal = () => {
		modal.classList.add('hidden');
		modal.setAttribute('aria-hidden', 'true');
		setBodyScroll(false);
		if (paymentButton.dataset.state !== 'completed') {
			paymentButton.disabled = false;
			paymentButton.classList.remove('is-loading');
			paymentButton.textContent = 'Lanjutkan Pembayaran';
		}
	};

	const translateAction = (action) => {
		switch (action) {
			case 'ALLOW':
				return 'Diizinkan';
			case 'CHALLENGE':
				return 'Perlu verifikasi tambahan';
			case 'BLOCK':
				return 'Diblokir';
			default:
				return action || 'Tidak diketahui';
		}
	};

	const renderResult = (analysis, meta = {}) => {
		const trustScore = Math.max(0, Math.min(1, Number(analysis?.trustScore ?? 0.5)));
		const percentage = Math.round(trustScore * 100);
		const action = (analysis?.action || 'UNKNOWN').toUpperCase();
		const message = analysis?.message || 'Evaluasi perilaku berhasil dijalankan.';

		if (scoreElement) {
			scoreElement.textContent = `${percentage}%`;
		}

		if (statusElement) {
			statusElement.textContent = action === 'ALLOW'
				? 'Perilaku pengguna terdeteksi wajar.'
				: action === 'CHALLENGE'
					? 'Sesi memerlukan verifikasi lanjutan.'
					: action === 'BLOCK'
						? 'Sesi ditolak karena pola mencurigakan.'
						: 'Status verifikasi tidak diketahui.';
		}

		if (messageElement) {
			messageElement.textContent = message;
		}

		if (sessionElement && guard && typeof guard.getSessionId === 'function') {
			sessionElement.textContent = guard.getSessionId();
		}

		if (actionElement) {
			actionElement.textContent = translateAction(action);
		}

		if (continueButton) {
			if (action === 'BLOCK') {
				continueButton.disabled = true;
				continueButton.textContent = 'Hubungi Layanan Pelanggan';
				paymentButton.dataset.state = 'blocked';
				paymentButton.disabled = true;
			} else if (action === 'CHALLENGE') {
				continueButton.disabled = false;
				continueButton.textContent = 'Lanjutkan dengan Verifikasi';
				paymentButton.dataset.state = 'challenge';
			} else {
				continueButton.disabled = false;
				continueButton.textContent = 'Lanjutkan Pembayaran';
				paymentButton.dataset.state = 'ready';
			}
		}

		logEvent('payment_verification_result', {
			trustScore,
			action,
			meta,
		});
	};

	const setButtonLoading = (isLoading) => {
		if (isLoading) {
			paymentButton.disabled = true;
			paymentButton.classList.add('is-loading');
			paymentButton.textContent = 'Menganalisis perilaku...';
		} else {
			paymentButton.classList.remove('is-loading');
			paymentButton.textContent = 'Lanjutkan Pembayaran';
			if (paymentButton.dataset.state !== 'blocked') {
				paymentButton.disabled = false;
			}
		}
	};

	const evaluateTrustScore = async () => {
		setButtonLoading(true);
		openModal();
		logEvent('payment_verification_start', {});

		if (!guard || typeof guard.flush !== 'function') {
			renderResult({
				trustScore: 0.5,
				action: 'CHALLENGE',
				message: 'Sistem tidak dapat terhubung ke layanan keamanan. Lanjutkan dengan verifikasi manual.',
			});
			setButtonLoading(false);
			return;
		}

		try {
			const response = await guard.flush('payment-verification', {
				finalize: true,
				context: {
					stage: 'payment',
				},
			});
			const analysis = response?.analysis || response?.data?.analysis || guard.getLastAnalysis?.();
			renderResult(analysis, { raw: response });
		} catch (error) {
			console.error('Payment verification failed:', error);
			renderResult(
				{
					trustScore: 0.4,
					action: 'CHALLENGE',
					message: 'Terjadi kendala saat memverifikasi trust score. Silakan coba lagi.',
				},
				{ error: error?.message }
			);
		} finally {
			setButtonLoading(false);
		}
	};

	const handleContinue = () => {
		const action = paymentButton.dataset.state;
		logEvent('payment_verification_continue', { action });
		if (action === 'blocked') {
			return;
		}

		paymentButton.dataset.state = 'completed';
		paymentButton.disabled = true;
		paymentButton.classList.add('is-loading');
		paymentButton.textContent = 'Pembayaran Diproses';
		closeModal();

		setTimeout(() => {
			paymentButton.classList.remove('is-loading');
			paymentButton.textContent = 'Pembayaran Berhasil';
		}, 1500);
	};

	paymentButton.addEventListener('click', (event) => {
		event.preventDefault();
		event.stopPropagation();
		if (paymentButton.dataset.state === 'completed') {
			return;
		}
		evaluateTrustScore();
	});

	continueButton?.addEventListener('click', handleContinue);
	cancelButton?.addEventListener('click', closeModal);
	closeButtons.forEach((button) => {
		button.addEventListener('click', () => {
			logEvent('payment_verification_closed', { state: paymentButton.dataset.state });
			closeModal();
		});
	});

	modal.addEventListener('click', (event) => {
		if (event.target === modal) {
			closeModal();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
			closeModal();
		}
	});
})();
