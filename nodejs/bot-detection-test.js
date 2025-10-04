/**
 * KAI Guard Bot Detection Test (Laravel integrated)
 *
 * This script drives a Puppeteer browser session through the
 * Laravel-based KAI Smart Route flow (home ➝ recommendations ➝ seat)
 * while intentionally behaving like an automated bot. The goal is to
 * exercise the behavior guard overlay and confirm that end-to-end
 * ticket searching remains protected.
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const config = {
  baseUrl: process.env.KAI_GUARD_BASE_URL || 'http://localhost:8000',
  routes: {
    home: '/',
    recommendations: '/routes/recommendations',
    reservation: process.env.KAI_GUARD_RESERVATION_PATH || '/reservasi',
    confirmation: process.env.KAI_GUARD_CONFIRMATION_PATH || '/reservasi/konfirmasi',
    payment: process.env.KAI_GUARD_PAYMENT_PATH || '/reservasi/pembayaran',
  },
  formData: {
    origin: {
      label: process.env.KAI_GUARD_ORIGIN_LABEL || 'Stasiun Gambir (Jakarta Pusat)',
      code: process.env.KAI_GUARD_ORIGIN_CODE || 'GMR',
    },
    destination: {
      label: process.env.KAI_GUARD_DESTINATION_LABEL || 'Stasiun Bandung (Bandung)',
      code: process.env.KAI_GUARD_DESTINATION_CODE || 'BD',
    },
    passengers: process.env.KAI_GUARD_PASSENGERS || '2',
    departureOffsetDays: Number(process.env.KAI_GUARD_DEPARTURE_OFFSET || 1),
  },
  selectors: {
    home: {
      form: '#search-form',
      originInput: '#origin-display',
      destinationInput: '#destination-display',
      hiddenOrigin: '#origin',
      hiddenDestination: '#destination',
      dateInput: '#departure-date',
      passengersSelect: '#passengers',
      submitButton: '#search-form button[type="submit"]',
      behaviorOverlay: '#behavior-guard-overlay',
      behaviorAction: '#behavior-guard-overlay [data-behavior-action]',
    },
    routes: {
      container: '[data-route-card-container]',
      card: '[data-route-card]',
      toggle: '[data-route-toggle]',
      multiGroupActiveCard: '[data-route-group="multi"].is-active [data-route-card]',
      directGroupActiveCard: '[data-route-group="direct"].is-active [data-route-card]',
    },
    seat: {
      primarySeatSelector: '[data-seat-state="available"]',
      fallbackSeatSelectors: [
        '[data-seat-status="available"]',
        '[data-seat]',
        '.seat.available button',
        '.seat.available',
        '[data-testid="seat-button"]',
      ],
      confirmSelectors: [
        '[data-seat-confirm]',
        'button[data-action="confirm"]',
        'button[data-action="continue"]',
        'button[data-action="submit"]',
        'button[type="submit"]',
      ],
    },
  },
  seatSelection: {
    preferredSeat: process.env.KAI_GUARD_SEAT_CODE || null,
  },
  screenshotPath: path.resolve(__dirname, 'bot-detection-result.png'),
  debug: Boolean(process.env.KAI_GUARD_DEBUG) || false,
  keepBrowserOpen: process.env.KAI_GUARD_KEEP_BROWSER_OPEN === 'true',
};

const botPatterns = {
  async fastTyping(page, selector, text) {
    await page.focus(selector);
    await page.keyboard.type(text, { delay: 2 });
  },

  async straightLineMouseMovement(page, startX, startY, endX, endY) {
    await page.mouse.move(startX, startY);

    const steps = 6;
    const xStep = (endX - startX) / steps;
    const yStep = (endY - startY) / steps;

    for (let i = 1; i <= steps; i++) {
      await page.mouse.move(startX + xStep * i, startY + yStep * i);
      await new Promise((resolve) => setTimeout(resolve, 18));
    }
  },

  async rapidClicks(page, x, y, count) {
    await page.mouse.move(x, y);
    for (let i = 0; i < count; i++) {
      await page.mouse.click(x, y);
      await new Promise((resolve) => setTimeout(resolve, 45));
    }
  },

  async noNaturalPauses() {
    return Promise.resolve();
  },
};

async function buildLaunchOptions() {
  const launchOptions = {
    headless: false,
    defaultViewport: { width: 1366, height: 768 },
  };

  const envExecutable = process.env.CHROME_PATH || process.env.PUPPETEER_EXECUTABLE_PATH;
  if (envExecutable && fs.existsSync(envExecutable)) {
    launchOptions.executablePath = envExecutable;
    return launchOptions;
  }

  try {
    const bundledPath = typeof puppeteer.executablePath === 'function' ? puppeteer.executablePath() : null;
    if (bundledPath && fs.existsSync(bundledPath)) {
      launchOptions.executablePath = bundledPath;
      return launchOptions;
    }
  } catch (error) {
    // fall back to channel/auto
  }

  if (process.env.PUPPETEER_BROWSER_CHANNEL) {
    launchOptions.channel = process.env.PUPPETEER_BROWSER_CHANNEL;
  }

  return launchOptions;
}

function resolveUrl(pathname) {
  if (!pathname) {
    return config.baseUrl;
  }

  if (/^https?:\/\//i.test(pathname)) {
    return pathname;
  }

  if (pathname.startsWith('/')) {
    return `${config.baseUrl}${pathname}`;
  }

  return `${config.baseUrl.replace(/\/$/, '')}/${pathname}`;
}

function createDateString(offsetDays = 1) {
  const date = new Date();
  date.setDate(date.getDate() + offsetDays);
  return date.toISOString().split('T')[0];
}

async function setFieldValue(page, selector, value, { triggerChange = true } = {}) {
  const elementExists = await page.$(selector);
  if (!elementExists) {
    throw new Error(`Tidak menemukan elemen: ${selector}`);
  }
  await elementExists.dispose();

  await page.evaluate(
    ({ selector, value, triggerChange }) => {
      const element = document.querySelector(selector);
      if (!element) {
        return;
      }

      element.value = value;

      if (triggerChange) {
        element.dispatchEvent(new Event('input', { bubbles: true }));
        element.dispatchEvent(new Event('change', { bubbles: true }));
      }
    },
    { selector, value, triggerChange },
  );
}

async function clickVisible(page, selector) {
  const handle = await page.evaluateHandle((sel) => {
    const elements = Array.from(document.querySelectorAll(sel));
    return elements.find((el) => {
      if (!el) return false;
      if (el.offsetParent === null) return false;
      const style = window.getComputedStyle(el);
      return style.visibility !== 'hidden' && style.display !== 'none';
    });
  }, selector);

  const element = handle.asElement();
  if (!element) {
    await handle.dispose();
    throw new Error(`Tidak ada elemen terlihat untuk selector: ${selector}`);
  }

  await element.click();
  await handle.dispose();
}

async function handleBehaviorGuard(page, context = 'interaction') {
  const { behaviorOverlay, behaviorAction } = config.selectors.home;
  const overlayHandle = await page.$(behaviorOverlay);
  if (!overlayHandle) {
    return;
  }

  const state = await page
    .$eval(behaviorOverlay, (element) => element.dataset.state || 'idle')
    .catch(() => 'idle');

  await overlayHandle.dispose();

  if (state === 'idle') {
    return;
  }

  if (state === 'challenge') {
    console.log(`[Guard] Tantangan terpicu selama ${context}. Menjawab...`);
    try {
      await clickVisible(page, behaviorAction);
      await page.waitForFunction(
        (selector) => {
          const element = document.querySelector(selector);
          return element && element.dataset.state === 'idle';
        },
        { timeout: 5000 },
        behaviorOverlay,
      );
    } catch (error) {
      console.error('[Guard] Gagal menutup overlay tantangan:', error.message);
    }
    return;
  }

  if (state === 'block') {
    throw new Error(`Behavior guard memblokir sesi selama ${context}`);
  }
}

async function performBotInteractions(page) {
  const viewport = page.viewport() || { width: 1366, height: 768 };

  await botPatterns.straightLineMouseMovement(
    page,
    Math.round(viewport.width * 0.2),
    Math.round(viewport.height * 0.25),
    Math.round(viewport.width * 0.8),
    Math.round(viewport.height * 0.3),
  );

  await botPatterns.rapidClicks(
    page,
    Math.round(viewport.width * 0.5),
    Math.round(viewport.height * 0.55),
    4,
  );

  await botPatterns.noNaturalPauses();
  await handleBehaviorGuard(page, 'simulasi awal');
}

async function fillHomeForm(page) {
  const { origin, destination, passengers, departureOffsetDays } = config.formData;
  const selectors = config.selectors.home;

  await page.waitForSelector(selectors.form, { timeout: 10000 });

  await page.click(selectors.originInput, { clickCount: 3 });
  await botPatterns.fastTyping(page, selectors.originInput, origin.label);
  await page.keyboard.press('Tab');
  await setFieldValue(page, selectors.hiddenOrigin, origin.code, { triggerChange: false });

  await page.click(selectors.destinationInput, { clickCount: 3 });
  await botPatterns.fastTyping(page, selectors.destinationInput, destination.label);
  await page.keyboard.press('Tab');
  await setFieldValue(page, selectors.hiddenDestination, destination.code, { triggerChange: false });

  const dateValue = createDateString(departureOffsetDays || 1);
  await setFieldValue(page, selectors.dateInput, dateValue);
  await page.select(selectors.passengersSelect, String(passengers));

  await handleBehaviorGuard(page, 'pengisian form');
}

async function submitSearch(page) {
  await handleBehaviorGuard(page, 'sebelum submit');

  const submitSelector = config.selectors.home.submitButton;

  const [navigationResult] = await Promise.allSettled([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 45000 }),
    clickVisible(page, submitSelector),
  ]);

  if (navigationResult.status === 'rejected') {
    const validationMessages = await page
      .$$eval('#form-errors li', (items) => items.map((item) => item.textContent.trim()).filter(Boolean))
      .catch(() => []);

    const joinedMessages = validationMessages.length ? ` | Validasi: ${validationMessages.join(' | ')}` : '';
    throw new Error(`Gagal menavigasi ke rekomendasi setelah submit: ${navigationResult.reason?.message ?? navigationResult.reason}${joinedMessages}`);
  }
}

async function verifyRecommendationsPage(page) {
  const { routes } = config.selectors;

  await page.waitForSelector(routes.container || routes.card, { timeout: 15000 });

  const snapshot = await page.evaluate(() => {
    const directGroup = document.querySelector('[data-route-group="direct"]');
    const multiGroup = document.querySelector('[data-route-group="multi"]');
    const toggle = document.querySelector('[data-route-toggle]');

    const countCards = (group) =>
      group && group.querySelectorAll('[data-route-card]').length ? group.querySelectorAll('[data-route-card]').length : 0;

    return {
      directCards: countCards(directGroup),
      multiCards: countCards(multiGroup),
      directActive: Boolean(directGroup && directGroup.classList.contains('is-active')),
      multiActive: Boolean(multiGroup && multiGroup.classList.contains('is-active')),
      hasToggle: Boolean(toggle),
    };
  });

  if (snapshot.directCards > 0 && snapshot.directActive) {
    await page.waitForSelector(routes.directGroupActiveCard || routes.card, { timeout: 10000 });
    return;
  }

  if (snapshot.directCards === 0 && snapshot.multiCards > 0 && snapshot.hasToggle) {
    console.log('Tidak ada kereta langsung, membuka koneksi multi-moda...');
    try {
      await clickVisible(page, routes.toggle || '[data-route-toggle]');
    } catch (error) {
      console.warn('Gagal mengklik tombol multi-moda:', error.message);
    }

    await page.waitForFunction(
      () => {
        const multiGroup = document.querySelector('[data-route-group="multi"]');
        return Boolean(multiGroup && multiGroup.classList.contains('is-active'));
      },
      { timeout: 10000 },
    );

    await page.waitForSelector(routes.multiGroupActiveCard || routes.card, { timeout: 10000 });
    return;
  }

  if (snapshot.directCards === 0 && snapshot.multiCards === 0) {
    throw new Error('Tidak menemukan rekomendasi rute apapun di halaman.');
  }

  await page.waitForSelector(routes.card, { timeout: 10000 });
}

async function selectRouteOption(page) {
  await handleBehaviorGuard(page, 'memilih rute');

  await page.waitForSelector(config.selectors.routes.card, { timeout: 10000 });

  const clickFirstRouteButton = async () => {
    const handle = await page.evaluateHandle(() => {
      const activeButtons = Array.from(document.querySelectorAll('[data-route-group].is-active [data-route-select]'));
      const fallback = Array.from(document.querySelectorAll('[data-route-select]'));
      const candidates = activeButtons.length ? activeButtons : fallback;

      return (
        candidates.find((button) => {
          if (!button) return false;
          if (button.offsetParent === null) return false;
          const style = window.getComputedStyle(button);
          return style.visibility !== 'hidden' && style.display !== 'none' && !button.disabled;
        }) || null
      );
    });

    const element = handle.asElement();
    if (!element) {
      await handle.dispose();
      throw new Error('Tidak menemukan tombol pilih rute yang terlihat.');
    }

    await element.click({ delay: 10 });
    await handle.dispose();
  };

  const [navigationResult, clickResult] = await Promise.allSettled([
    page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 45000 }),
    clickFirstRouteButton(),
  ]);

  if (clickResult.status === 'rejected') {
    throw new Error(`Gagal mengklik tombol pilih rute: ${clickResult.reason?.message ?? clickResult.reason}`);
  }

  if (navigationResult.status === 'rejected') {
    throw new Error(`Gagal berpindah ke halaman reservasi setelah memilih rute: ${navigationResult.reason?.message ?? navigationResult.reason}`);
  }

  await handleBehaviorGuard(page, 'setelah memilih rute');
}

async function fillReservationForm(page) {
  await page.waitForSelector('[data-reservation-form]', { timeout: 15000 });

  const fullNameInputs = await page.$$('[data-field-type="full_name"]');
  const nikInputs = await page.$$('[data-field-type="national_id"]');
  const phoneInputs = await page.$$('[data-field-type="phone_number"]');

  for (let index = 0; index < fullNameInputs.length; index += 1) {
    const nameElement = fullNameInputs[index];
    const nikElement = nikInputs[index];
    const phoneElement = phoneInputs[index];

    if (nameElement) {
      await nameElement.click({ clickCount: 3 });
      await nameElement.type(`Bot Passenger ${index + 1}`, { delay: 2 });
    }

    if (nikElement) {
      await nikElement.click({ clickCount: 3 });
      await nikElement.type(`3213213213213244${index}`, { delay: 1 });
    }

    if (phoneElement) {
      await phoneElement.click({ clickCount: 3 });
      await phoneElement.type(`081234560066${index}`, { delay: 1 });
    }
  }

  await handleBehaviorGuard(page, 'setelah isi form reservasi');

  const [navigationResult, clickResult] = await Promise.allSettled([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 45000 }),
    clickVisible(page, '[data-submit-button]'),
  ]);

  if (clickResult.status === 'rejected') {
    throw new Error(`Gagal menekan tombol submit reservasi: ${clickResult.reason?.message ?? clickResult.reason}`);
  }

  if (navigationResult.status === 'rejected') {
    const validationMessages = await page
      .$$eval('[data-error-for]', (items) => items.map((item) => item.textContent.trim()).filter(Boolean))
      .catch(() => []);

    const joined = validationMessages.length ? ` | Validasi: ${validationMessages.join(' | ')}` : '';
    throw new Error(`Gagal melanjutkan dari form reservasi: ${navigationResult.reason?.message ?? navigationResult.reason}${joined}`);
  }

  await handleBehaviorGuard(page, 'konfirmasi reservasi');
}

async function selectSeatsOnConfirmation(page) {
  await page.waitForSelector('[data-seat-open]', { timeout: 15000 });
  await clickVisible(page, '[data-seat-open]');

  await page.waitForSelector('[data-seat-modal]', { visible: true, timeout: 10000 });

  const passengerItems = await page.$$('[data-passenger-index]');

  for (let index = 0; index < passengerItems.length; index += 1) {
    const passengerItem = passengerItems[index];
    await passengerItem.click();

    const seatHandle = await page.evaluateHandle(() => {
      const buttons = Array.from(document.querySelectorAll('[data-seat-button][data-seat-status="available"]'));
      return buttons.find((button) => button && button.offsetParent !== null) || null;
    });

    const seatElement = seatHandle.asElement();
    if (seatElement) {
      await seatElement.click();
    } else {
      console.warn('Tidak menemukan kursi tersedia untuk penumpang index', index);
    }

    await seatHandle.dispose();
    await page.waitForTimeout(120);
  }

  await handleBehaviorGuard(page, 'sebelum simpan kursi');

  const seatSubmitSelector = '[data-seat-form] button[type="submit"]';
  const [navigationResult, clickResult] = await Promise.allSettled([
    page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 45000 }),
    clickVisible(page, seatSubmitSelector),
  ]);

  if (clickResult.status === 'rejected') {
    throw new Error(`Gagal menekan tombol simpan kursi: ${clickResult.reason?.message ?? clickResult.reason}`);
  }

  if (navigationResult.status === 'rejected') {
    console.warn('Pengiriman form kursi tidak memicu navigasi baru:', navigationResult.reason?.message ?? navigationResult.reason);
    await page.waitForTimeout(750);
  }

  await page.waitForSelector('[data-seat-modal]', { hidden: true, timeout: 10000 }).catch(() => undefined);
  await handleBehaviorGuard(page, 'setelah simpan kursi');
}

async function goToPayment(page) {
  await page.waitForSelector('[data-payment-button]', { timeout: 15000 });

  const [navigationResult, clickResult] = await Promise.allSettled([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 45000 }),
    clickVisible(page, '[data-payment-button]'),
  ]);

  if (clickResult.status === 'rejected') {
    throw new Error(`Gagal menekan tombol menuju pembayaran: ${clickResult.reason?.message ?? clickResult.reason}`);
  }

  if (navigationResult.status === 'rejected') {
    throw new Error(`Gagal menuju halaman pembayaran: ${navigationResult.reason?.message ?? navigationResult.reason}`);
  }

  await handleBehaviorGuard(page, 'halaman pembayaran');
}

async function spoofSuspiciousBehaviorMetrics(page) {
  await page.evaluate(() => {
    const config = window.__behaviorGuardConfig;
    if (!config || !config.endpoint || window.__kaizenSpoofedBehaviorMetrics) {
      return;
    }

    window.__kaizenSpoofedBehaviorMetrics = true;

    const suspiciousMetrics = {
      mouseSpeed: 572,
      mouseAcceleration: 0,
      mouseJitter: 0.99,
      clickFrequency: 50,
      typingSpeed: 137.5,
      typingRhythm: 0.99,
      scrollPattern: 0,
      timeOnPage: 1000,
      keyPressTime: 5,
    };

    const patchPayload = (payload) => {
      if (!payload || typeof payload !== 'object') {
        return;
      }

      const keys = Object.keys(suspiciousMetrics);
      keys.forEach((key) => {
        payload[key] = suspiciousMetrics[key];
      });

      if (payload.metrics && typeof payload.metrics === 'object') {
        keys.forEach((key) => {
          payload.metrics[key] = suspiciousMetrics[key];
        });

        payload.metrics.raw = Object.assign({}, payload.metrics.raw, {
          mouseMovements: Math.max(1, Number(payload.metrics.raw?.mouseMovements) || 1),
          clicks: Math.max(1, Number(payload.metrics.raw?.clicks) || 1),
          keyPresses: Math.max(1, Number(payload.metrics.raw?.keyPresses) || 1),
          scrollEvents: Math.max(0, Number(payload.metrics.raw?.scrollEvents) || 0),
        });
      }

      payload.reason = 'automated-bot-simulation';
      payload.context = Object.assign({}, payload.context, { anomaly: true });
      payload.extra = Object.assign({}, payload.extra, { botSpoofed: true });
    };

    const originalFetch = window.fetch ? window.fetch.bind(window) : null;

    if (originalFetch) {
      window.fetch = function spoofedFetch(input, init = {}) {
        const url = typeof input === 'string' ? input : input?.url;
        if (url && url.includes(config.endpoint) && init && typeof init.body === 'string') {
          try {
            const parsed = JSON.parse(init.body);
            patchPayload(parsed);
            init.body = JSON.stringify(parsed);
          } catch (error) {
            console.warn('BehaviorGuard spoof payload failed:', error);
          }
        }

        return originalFetch(input, init);
      };
    }
  });
}

async function evaluatePaymentTrustScore(page) {
  await page.waitForSelector('.payment-summary-button', { timeout: 15000 });

  await handleBehaviorGuard(page, 'sebelum verifikasi pembayaran');

  await page
    .evaluate(() => {
      const button = document.querySelector('.payment-summary-button');
      if (button && typeof button.scrollIntoView === 'function') {
        button.scrollIntoView({ behavior: 'instant', block: 'center' });
      }
    })
    .catch(() => undefined);

  await clickVisible(page, '.payment-summary-button');

  await page.waitForSelector('[data-trust-modal]:not(.hidden)', { timeout: 15000 });

  await handleBehaviorGuard(page, 'modal verifikasi pembayaran');

  const evaluationHandle = await page.waitForFunction(
    () => {
      const scoreElement = document.querySelector('[data-trust-score]');
      const statusElement = document.querySelector('[data-trust-status]');
      const actionElement = document.querySelector('[data-trust-action]');
      const messageElement = document.querySelector('[data-trust-message]');
      const sessionElement = document.querySelector('[data-trust-session]');

      if (!scoreElement || !statusElement || !actionElement) {
        return false;
      }

      const score = (scoreElement.textContent || '').trim();
      const status = (statusElement.textContent || '').trim();
      const action = (actionElement.textContent || '').trim();

      if (!score || score === '--%' || !status || !action || action === '—') {
        return false;
      }

      return {
        score,
        status,
        action,
        message: (messageElement?.textContent || '').trim(),
        session: (sessionElement?.textContent || '').trim(),
      };
    },
    { timeout: 20000 },
  );

  const evaluation = await evaluationHandle.jsonValue();

  const continueHandle = await page.$('[data-trust-continue]');
  const continueEnabled = continueHandle
    ? await page.evaluate((button) => !button.disabled, continueHandle)
    : false;

  if (continueHandle && continueEnabled) {
    await continueHandle.click().catch(() => undefined);
    await page.waitForTimeout(500);
  }

  if (continueHandle) {
    await continueHandle.dispose();
  }

  return evaluation;
}

function ensureScreenshotDirectory() {
  const directory = path.dirname(config.screenshotPath);
  if (!fs.existsSync(directory)) {
    fs.mkdirSync(directory, { recursive: true });
  }
}

async function runBotDetectionTest() {
  console.log('Memulai KAI Guard Bot Detection Test untuk aplikasi Laravel...');

  const launchOptions = await buildLaunchOptions();
  const browser = await puppeteer.launch(launchOptions);

  try {
    const page = await browser.newPage();

    const homeUrl = resolveUrl(config.routes.home);
    console.log(`Membuka halaman home: ${homeUrl}`);
    await page.goto(homeUrl, { waitUntil: 'networkidle2' });

    console.log('Halaman home dimuat. Menjalankan pola bot...');
    await performBotInteractions(page);

    console.log('Mengisi form pencarian di home.blade.php...');
    await fillHomeForm(page);

    console.log('Mengirim form. Menunggu rekomendasi rute...');
    await submitSearch(page);

    await verifyRecommendationsPage(page);
    console.log('Berhasil mencapai halaman routes.blade.php.');

    console.log('Memilih salah satu rekomendasi rute...');
    await selectRouteOption(page);

    console.log('Mengisi formulir reservasi pada tahap berikutnya...');
    await fillReservationForm(page);

    console.log('Membuka modal kursi dan memilih kursi yang tersedia...');
    await selectSeatsOnConfirmation(page);

    console.log('Menuju ke halaman pembayaran...');
    await goToPayment(page);

  console.log('Memaksakan metrik perilaku mencurigakan untuk pengujian...');
  await spoofSuspiciousBehaviorMetrics(page);

    console.log('Memicu verifikasi pembayaran dan membaca trust score...');
    const trustScore = await evaluatePaymentTrustScore(page);
    console.log(
      `Trust Score Bot: ${trustScore.score} | Status: ${trustScore.status} | Tindakan Sistem: ${trustScore.action}`,
    );
    if (trustScore.message) {
      console.log(`Catatan Sistem: ${trustScore.message}`);
    }
    if (trustScore.session) {
      console.log(`Behavior Guard Session ID: ${trustScore.session}`);
    }

    ensureScreenshotDirectory();
    await page.screenshot({ path: config.screenshotPath, fullPage: true });
    console.log(`Screenshot disimpan di ${config.screenshotPath}`);
  } catch (error) {
    console.error('Terjadi kesalahan selama pengujian bot:', error);
  } finally {
    if (config.keepBrowserOpen) {
      console.log('Pengujian selesai. Browser akan tetap terbuka. Tekan Enter untuk menutup browser...');
      process.stdin.resume();
      await new Promise((resolve) => process.stdin.once('data', resolve));
      process.stdin.pause();
    }

    await browser.close();
    console.log('Browser Ditutup. Pengujian selesai.');
  }
}

runBotDetectionTest();