(function () {
	const paymentButton = document.querySelector('.payment-summary-button');

	if (!paymentButton) {
		return;
	}

	paymentButton.addEventListener('click', () => {
		paymentButton.disabled = true;
		paymentButton.classList.add('is-loading');
		paymentButton.textContent = 'Memproses...';

		setTimeout(() => {
			paymentButton.textContent = 'Pembayaran Diproses';
		}, 2000);
	});
})();
