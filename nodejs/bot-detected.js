#!/usr/bin/env node
/**
 * Automated bot simulation across the full KAI Smart Route booking flow.
 *
 * This script intentionally behaves like a bot to exercise the behavior guard
 * integration from homepage search until the payment trust-score modal.
 */

const puppeteer = require('puppeteer');

const CONFIG = {
	baseUrl: process.env.KAI_GUARD_BASE_URL || 'http://localhost:8000',
	originLabel: process.env.KAI_GUARD_ORIGIN_LABEL || 'Stasiun Gambir (Jakarta Pusat)',
	originCode: process.env.KAI_GUARD_ORIGIN_CODE || 'GMR',
	destinationLabel: process.env.KAI_GUARD_DESTINATION_LABEL || 'Stasiun Bandung (Bandung)',
	destinationCode: process.env.KAI_GUARD_DESTINATION_CODE || 'BD',
	passengers: Number(process.env.KAI_GUARD_PASSENGERS || 2),
	departureOffset: Number(process.env.KAI_GUARD_DEPARTURE_OFFSET || 2),
	headless: process.env.HEADLESS !== 'false',
	screenshot: process.env.KAI_GUARD_SCREENSHOT || 'bot-detection-result.png',
};

const BOT_BEHAVIOR = {
	mousePath: async (page) => {
		const viewport = page.viewport() || { width: 1280, height: 720 };
		await page.mouse.move(viewport.width * 0.2, viewport.height * 0.2);
		for (let step = 0; step <= 8; step += 1) {
			await page.mouse.move(
				viewport.width * 0.2 + (viewport.width * 0.6 * step) / 8,
				viewport.height * 0.25 + (viewport.height * 0.1 * step) / 8,
			);
			await page.waitForTimeout(14);
		}
	},
	rapidClicks: async (page, selector) => {
		await page.$eval(selector, (el) => {
			el.scrollIntoView({ behavior: 'auto', block: 'center' });
		});
		const box = await page.$eval(selector, (el) => {
			const rect = el.getBoundingClientRect();
			return {
				x: rect.left + rect.width / 2,
				y: rect.top + rect.height / 2,
			};
		});
		for (let i = 0; i < 4; i += 1) {
			await page.mouse.click(box.x, box.y);
			await page.waitForTimeout(36);
		}
	},
	fastType: async (page, selector, text) => {
		await page.focus(selector);
		await page.keyboard.type(text, { delay: 2 });
	},
};

const selectFirst = async (page, selector) => page.$(selector);

const waitForNavigationIdle = (page) =>
	Promise.all([
		page.waitForNavigation({ waitUntil: 'networkidle2' }),
		page.waitForTimeout(300),
	]);

const futureDate = (offset = 1) => {
	const date = new Date();
	date.setDate(date.getDate() + offset);
	return date.toISOString().split('T')[0];
};

const resolveUrl = (pathname) => {
	if (!pathname.startsWith('http')) {
		return `${CONFIG.baseUrl.replace(/\/$/, '')}/${pathname.replace(/^\//, '')}`;
	}
	return pathname;
};

async function fillHomeForm(page) {
	await page.goto(resolveUrl('/'), { waitUntil: 'domcontentloaded' });
	await BOT_BEHAVIOR.mousePath(page);

	await BOT_BEHAVIOR.fastType(page, '#origin-display', CONFIG.originLabel);
	await page.keyboard.press('Tab');
	await page.$eval('#origin', (el, value) => {
		el.value = value;
	}, CONFIG.originCode);

	await BOT_BEHAVIOR.fastType(page, '#destination-display', CONFIG.destinationLabel);
	await page.keyboard.press('Tab');
	await page.$eval('#destination', (el, value) => {
		el.value = value;
	}, CONFIG.destinationCode);

	await page.$eval('#departure-date', (el, value) => {
		el.value = value;
	}, futureDate(CONFIG.departureOffset));

	await page.select('#passengers', String(CONFIG.passengers));

	const [navigationResult] = await Promise.allSettled([
		page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 45000 }),
		page.click('#search-form button[type="submit"]'),
	]);

	if (navigationResult.status === 'rejected') {
		const validationMessages = await page
			.$$eval('#form-errors li', (items) => items.map((item) => item.textContent.trim()).filter(Boolean))
			.catch(() => []);
		const joinedMessages = validationMessages.length ? ` | Validasi: ${validationMessages.join(' | ')}` : '';
		throw new Error(
			`Gagal berpindah dari halaman pencarian setelah submit: ${navigationResult.reason?.message ?? navigationResult.reason}${joinedMessages}`,
		);
	}
}

async function pickRoute(page) {
	await page.waitForSelector('[data-route-select]', { timeout: 10000 });
	await BOT_BEHAVIOR.rapidClicks(page, '[data-route-select]');
	await waitForNavigationIdle(page);
}

async function fillPassengerForm(page) {
	await page.waitForSelector('[data-reservation-form]', { timeout: 10000 });
	const fullNames = await page.$$('[data-field-type="full_name"]');
	const nikInputs = await page.$$('[data-field-type="national_id"]');
	const phoneInputs = await page.$$('[data-field-type="phone_number"]');

	for (let index = 0; index < fullNames.length; index += 1) {
		await fullNames[index].focus();
		await page.keyboard.type(`Bot Passenger ${index + 1}`, { delay: 2 });

		await nikInputs[index].focus();
		await page.keyboard.type(`32132132132132${index}`, { delay: 1 });

		await phoneInputs[index].focus();
		await page.keyboard.type(`0812345600${index}`, { delay: 1 });
	}

	await Promise.all([
		waitForNavigationIdle(page),
		page.click('[data-submit-button]'),
	]);
}

async function selectSeats(page) {
	await page.waitForSelector('[data-seat-open]', { timeout: 10000 });
	await page.click('[data-seat-open]');
	await page.waitForSelector('[data-seat-modal]', { visible: true });

	const passengerItems = await page.$$('[data-passenger-index]');
	for (let index = 0; index < passengerItems.length; index += 1) {
		await passengerItems[index].click();
		const seatButton = await selectFirst(page, '[data-seat-button][data-seat-status="available"]');
		if (seatButton) {
			await seatButton.click();
		}
	}

	await Promise.all([
		waitForNavigationIdle(page),
		page.click('[data-seat-form] button[type="submit"]'),
	]);
}

async function proceedToPayment(page) {
	await page.waitForSelector('[data-payment-button]', { timeout: 10000 });
	await Promise.all([
		waitForNavigationIdle(page),
		page.click('[data-payment-button]'),
	]);
}

async function verifyTrustScore(page) {
	await page.waitForSelector('.payment-summary-button', { timeout: 10000 });
	await page.click('.payment-summary-button');
	await page.waitForSelector('[data-trust-modal]:not(.hidden)', { timeout: 10000 });
	const scoreText = await page.$eval('[data-trust-score]', (el) => el.textContent.trim());
	const actionText = await page.$eval('[data-trust-action]', (el) => el.textContent.trim());
	await page.screenshot({ path: CONFIG.screenshot, fullPage: true });
	return { scoreText, actionText };
}

(async () => {
	const browser = await puppeteer.launch({
		headless: CONFIG.headless,
		defaultViewport: { width: 1280, height: 720 },
	});
	const page = await browser.newPage();

	try {
		await fillHomeForm(page);
		await pickRoute(page);
		await fillPassengerForm(page);
		await selectSeats(page);
		await proceedToPayment(page);
		const result = await verifyTrustScore(page);
		console.log('Trust score modal:', result);
	} catch (error) {
		console.error('Bot simulation failed:', error.message);
		process.exitCode = 1;
	} finally {
		await browser.close();
	}
})();
