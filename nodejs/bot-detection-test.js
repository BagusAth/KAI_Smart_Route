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
    seat: process.env.KAI_GUARD_SEAT_PATH || '/seat',
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

async function navigateToSeatPage(page) {
  const seatPath = config.routes.seat;
  if (!seatPath) {
    console.warn('Seat path tidak dikonfigurasi. Melewati navigasi ke halaman kursi.');
    return false;
  }

  const seatUrl = resolveUrl(seatPath);
  console.log(`Menuju halaman seat selection: ${seatUrl}`);

  let response;
  try {
    response = await page.goto(seatUrl, { waitUntil: 'domcontentloaded' });
  } catch (error) {
    console.warn('Gagal membuka halaman seat:', error.message);
    return false;
  }

  if (!response) {
    console.warn('Tidak ada respon saat membuka halaman seat.');
    return false;
  }

  const status = response.status();
  console.log(`Status halaman seat: ${status}`);

  if (status >= 400) {
    console.warn('Halaman seat mengembalikan status error, melewati seleksi kursi.');
    return false;
  }

  await page.waitForTimeout(750);
  return true;
}

async function findSeatHandle(page) {
  const { primarySeatSelector, fallbackSeatSelectors } = config.selectors.seat;

  if (primarySeatSelector) {
    const primary = await page.$(primarySeatSelector);
    if (primary) {
      return primary;
    }
  }

  for (const selector of fallbackSeatSelectors) {
    const handle = await page.$(selector);
    if (handle) {
      return handle;
    }
  }

  if (config.seatSelection.preferredSeat) {
    const handle = await page.evaluateHandle((seatCode) => {
      const candidates = Array.from(
        document.querySelectorAll('[data-seat-number], [data-seat], button, [role="button"]'),
      );

      return candidates.find((element) => {
        const datasetMatch = element.dataset?.seatNumber || element.dataset?.seat;
        if (datasetMatch && datasetMatch.toUpperCase() === seatCode.toUpperCase()) {
          return true;
        }

        const text = (element.textContent || '').trim();
        return text && text.toUpperCase().includes(seatCode.toUpperCase());
      });
    }, config.seatSelection.preferredSeat);

    const preferredElement = handle.asElement();
    if (preferredElement) {
      return preferredElement;
    }

    await handle.dispose();
  }

  return null;
}

async function selectSeat(page) {
  const seatHandle = await findSeatHandle(page);
  if (!seatHandle) {
    console.warn('Tidak menemukan elemen kursi yang tersedia.');
    return false;
  }

  await seatHandle.click({ delay: 5 });
  await page.waitForTimeout(250);
  await seatHandle.dispose();

  for (const selector of config.selectors.seat.confirmSelectors) {
    const confirmHandle = await page.$(selector);
    if (!confirmHandle) {
      continue;
    }

    await confirmHandle.click({ delay: 10 });
    await page.waitForTimeout(400);
    await confirmHandle.dispose();
    console.log('Kursi dipilih dan konfirmasi dikirim.');
    return true;
  }

  console.log('Kursi dipilih, namun tombol konfirmasi tidak ditemukan.');
  return true;
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

    const seatNavigated = await navigateToSeatPage(page);
    if (seatNavigated) {
      console.log('Mencoba memilih kursi di seat.blade.php...');
      await selectSeat(page);
    } else {
      console.warn('Tahap seleksi kursi dilewati karena navigasi gagal.');
    }

    ensureScreenshotDirectory();
    await page.screenshot({ path: config.screenshotPath, fullPage: true });
    console.log(`Screenshot disimpan di ${config.screenshotPath}`);
  } catch (error) {
    console.error('Terjadi kesalahan selama pengujian bot:', error);
  } finally {
    await browser.close();
    console.log('Pengujian selesai.');
  }
}

runBotDetectionTest();