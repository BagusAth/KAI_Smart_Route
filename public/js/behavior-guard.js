(function () {
	if (window.BehaviorGuard) {
		return;
	}

	const globalConfig = window.__behaviorGuardConfig || {};
	if (!globalConfig.enabled || !globalConfig.endpoint) {
		return;
	}

	class BehaviorCollector {
		constructor(options = {}) {
			this.options = {
				sampleRate: options.sampleRate || 120,
				maxSamples: options.maxSamples || 150,
				sessionId: options.sessionId || this._generateSessionId(),
				debug: Boolean(options.debug),
			};

			this.sessionId = this.options.sessionId;
			this.data = {
				sessionId: this.sessionId,
				timestamp: Date.now(),
				mouseMovements: [],
				clicks: [],
				keyPresses: [],
				scrollEvents: [],
				timeOnPage: 0,
				deviceInfo: this._collectDeviceInfo(),
			};

			this._isCollecting = false;
			this._startTime = null;
			this._lastMousePosition = { x: 0, y: 0 };
			this._lastMouseTime = 0;
			this._keyPressTimestamps = {};
			this._tickInterval = null;

			this._onMouseMove = this._onMouseMove.bind(this);
			this._onMouseClick = this._onMouseClick.bind(this);
			this._onKeyDown = this._onKeyDown.bind(this);
			this._onKeyUp = this._onKeyUp.bind(this);
			this._onScroll = this._onScroll.bind(this);
			this._onVisibilityChange = this._onVisibilityChange.bind(this);
		}

		start() {
			if (this._isCollecting) {
				return;
			}

			this._isCollecting = true;
			this._startTime = Date.now();

			document.addEventListener('mousemove', this._onMouseMove);
			document.addEventListener('click', this._onMouseClick);
			document.addEventListener('keydown', this._onKeyDown);
			document.addEventListener('keyup', this._onKeyUp);
			document.addEventListener('scroll', this._onScroll, { passive: true });
			document.addEventListener('visibilitychange', this._onVisibilityChange);

			this._tickInterval = window.setInterval(() => {
				this._updateTimeOnPage();
			}, Math.max(this.options.sampleRate, 250));
		}

		stop() {
			if (!this._isCollecting) {
				return;
			}

			this._isCollecting = false;
			this._updateTimeOnPage();

			document.removeEventListener('mousemove', this._onMouseMove);
			document.removeEventListener('click', this._onMouseClick);
			document.removeEventListener('keydown', this._onKeyDown);
			document.removeEventListener('keyup', this._onKeyUp);
			document.removeEventListener('scroll', this._onScroll);
			document.removeEventListener('visibilitychange', this._onVisibilityChange);

			if (this._tickInterval) {
				window.clearInterval(this._tickInterval);
				this._tickInterval = null;
			}
		}

		snapshot() {
			this._updateTimeOnPage();
			const metrics = this._calculateMetrics();
			return {
				sessionId: this.sessionId,
				timestamp: Date.now(),
				deviceInfo: this.data.deviceInfo,
				...metrics,
			};
		}

		_updateTimeOnPage() {
			if (!this._isCollecting) {
				return;
			}

			if (document.visibilityState === 'visible' && this._startTime) {
				this.data.timeOnPage += Date.now() - this._startTime;
				this._startTime = Date.now();
			}
		}

		_onMouseMove(event) {
			const now = Date.now();
			const delta = now - this._lastMouseTime;
			if (delta < this.options.sampleRate) {
				return;
			}

			const x = event.clientX;
			const y = event.clientY;
			let speed = 0;
			let acceleration = 0;

			if (this._lastMouseTime) {
				const dx = x - this._lastMousePosition.x;
				const dy = y - this._lastMousePosition.y;
				const distance = Math.sqrt(dx * dx + dy * dy);
				speed = distance / Math.max(delta, 1);

				if (this.data.mouseMovements.length) {
					const lastSpeed = this.data.mouseMovements[this.data.mouseMovements.length - 1].speed;
					acceleration = (speed - lastSpeed) / Math.max(delta, 1);
				}
			}

			this.data.mouseMovements.push({ x, y, timestamp: now, speed, acceleration });
			if (this.data.mouseMovements.length > this.options.maxSamples) {
				this.data.mouseMovements.shift();
			}

			this._lastMousePosition = { x, y };
			this._lastMouseTime = now;
		}

		_onMouseClick(event) {
			this.data.clicks.push({
				x: event.clientX,
				y: event.clientY,
				button: event.button,
				timestamp: Date.now(),
			});

			if (this.data.clicks.length > this.options.maxSamples) {
				this.data.clicks.shift();
			}
		}

		_onKeyDown(event) {
			const key = event.key.length === 1 ? 'character' : event.key;
			this._keyPressTimestamps[key] = Date.now();
		}

		_onKeyUp(event) {
			const key = event.key.length === 1 ? 'character' : event.key;
			const start = this._keyPressTimestamps[key];
			if (!start) {
				return;
			}

			const duration = Date.now() - start;
			this.data.keyPresses.push({ key, pressDuration: duration, timestamp: Date.now() });
			if (this.data.keyPresses.length > this.options.maxSamples) {
				this.data.keyPresses.shift();
			}

			delete this._keyPressTimestamps[key];
		}

		_onScroll() {
			this.data.scrollEvents.push({
				scrollX: window.scrollX,
				scrollY: window.scrollY,
				timestamp: Date.now(),
			});

			if (this.data.scrollEvents.length > this.options.maxSamples) {
				this.data.scrollEvents.shift();
			}
		}

		_onVisibilityChange() {
			if (document.visibilityState === 'visible') {
				this._startTime = Date.now();
			} else {
				this._updateTimeOnPage();
			}
		}

		_collectDeviceInfo() {
			return {
				screenResolution: `${window.screen.width}x${window.screen.height}`,
				windowSize: `${window.innerWidth}x${window.innerHeight}`,
				colorDepth: window.screen.colorDepth,
				pixelRatio: window.devicePixelRatio,
				timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
				language: navigator.language,
				platform: navigator.platform,
				userAgent: navigator.userAgent,
				touchSupport: 'ontouchstart' in window,
			};
		}

		_calculateMetrics() {
			const result = {
				mouseSpeed: 0,
				mouseAcceleration: 0,
				mouseJitter: 0,
				clickFrequency: 0,
				typingSpeed: 0,
				keyPressTime: 0,
				typingRhythm: 0,
				scrollPattern: 0,
				timeOnPage: this.data.timeOnPage,
				raw: {
					mouseMovements: this.data.mouseMovements.length,
					clicks: this.data.clicks.length,
					keyPresses: this.data.keyPresses.length,
					scrollEvents: this.data.scrollEvents.length,
				},
			};

			if (this.data.mouseMovements.length) {
				let totalSpeed = 0;
				let totalAcceleration = 0;
				let jitterSum = 0;

				for (let i = 0; i < this.data.mouseMovements.length; i += 1) {
					const movement = this.data.mouseMovements[i];
					totalSpeed += movement.speed;
					totalAcceleration += movement.acceleration;

					if (i > 1) {
						const prev = this.data.mouseMovements[i - 1];
						const prevPrev = this.data.mouseMovements[i - 2];
						const dx1 = prev.x - prevPrev.x;
						const dy1 = prev.y - prevPrev.y;
						const dx2 = movement.x - prev.x;
						const dy2 = movement.y - prev.y;

						const angle1 = Math.atan2(dy1, dx1);
						const angle2 = Math.atan2(dy2, dx2);
						let diff = Math.abs(angle2 - angle1);
						if (diff > Math.PI) {
							diff = 2 * Math.PI - diff;
						}
						jitterSum += diff;
					}
				}

				result.mouseSpeed = totalSpeed / this.data.mouseMovements.length;
				result.mouseAcceleration = totalAcceleration / this.data.mouseMovements.length;
				result.mouseJitter = this.data.mouseMovements.length > 2
					? jitterSum / (this.data.mouseMovements.length - 2)
					: 0;
			}

			const elapsedSeconds = Math.max(1, (Date.now() - this.data.timestamp) / 1000);
			result.clickFrequency = this.data.clicks.length / elapsedSeconds;

			if (this.data.keyPresses.length) {
				let totalPress = 0;
				let intervalSum = 0;
				let intervalCount = 0;

				for (let i = 0; i < this.data.keyPresses.length; i += 1) {
					const entry = this.data.keyPresses[i];
					totalPress += entry.pressDuration;

					if (i > 0) {
						const interval = entry.timestamp - this.data.keyPresses[i - 1].timestamp;
						if (interval > 0 && interval < 2000) {
							intervalSum += interval;
							intervalCount += 1;
						}
					}
				}

				result.typingSpeed = this.data.keyPresses.length / elapsedSeconds;
				result.keyPressTime = totalPress / this.data.keyPresses.length;
				result.typingRhythm = intervalCount ? Math.sqrt(intervalSum / intervalCount) : 0;
			}

			if (this.data.scrollEvents.length > 1) {
				let distance = 0;
				for (let i = 1; i < this.data.scrollEvents.length; i += 1) {
					distance += Math.abs(this.data.scrollEvents[i].scrollY - this.data.scrollEvents[i - 1].scrollY);
				}
				result.scrollPattern = distance / this.data.scrollEvents.length;
			}

			return result;
		}

		_generateSessionId() {
			return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
				const r = (Math.random() * 16) | 0;
				const v = c === 'x' ? r : (r & 0x3) | 0x8;
				return v.toString(16);
			});
		}
	}

	const storageKey = globalConfig.storageKey || 'kaizen:behaviorGuard';

	const loadStore = () => {
		try {
			const raw = sessionStorage.getItem(storageKey);
			if (!raw) {
				return null;
			}
			return JSON.parse(raw);
		} catch (error) {
			console.warn('BehaviorGuard: failed to read storage', error);
			return null;
		}
	};

	const persistStore = (store) => {
		try {
			sessionStorage.setItem(storageKey, JSON.stringify(store));
		} catch (error) {
			console.warn('BehaviorGuard: failed to persist storage', error);
		}
	};

	const initialStore = loadStore() || {
		version: 1,
		sessionId: null,
		stages: [],
		events: [],
		meta: {},
	};

	if (!initialStore.sessionId) {
		initialStore.sessionId = `session-${Date.now()}-${Math.random().toString(16).slice(2, 10)}`;
	}

	const collector = new BehaviorCollector({
		sessionId: initialStore.sessionId,
		sampleRate: 100,
		maxSamples: 200,
		debug: false,
	});

	collector.start();

	const truncateEvents = (events) => {
		const MAX_EVENTS = 120;
		if (events.length <= MAX_EVENTS) {
			return events;
		}
		return events.slice(events.length - MAX_EVENTS);
	};

	const guardState = {
		sessionId: initialStore.sessionId,
		stages: Array.isArray(initialStore.stages) ? [...initialStore.stages] : [],
		events: Array.isArray(initialStore.events) ? [...initialStore.events] : [],
		stageInfo: null,
		meta: initialStore.meta || {},
		collector,
	};

	const persist = () => {
		persistStore({
			version: 1,
			sessionId: guardState.sessionId,
			stages: guardState.stages,
			events: truncateEvents(guardState.events),
			meta: guardState.meta,
		});
	};

	const normalizeData = (value) => {
		if (value === undefined) {
			return null;
		}

		if (typeof value === 'object') {
			try {
				return JSON.parse(JSON.stringify(value));
			} catch (error) {
				return null;
			}
		}

		return value;
	};

	const buildPayload = (reason, extra = {}) => {
		const metrics = collector.snapshot();
		const now = new Date().toISOString();

		return {
			version: '1.0.0',
			sessionId: guardState.sessionId,
			reason: reason || 'manual',
			timestamp: now,
			stage: guardState.stageInfo,
			stageHistory: guardState.stages.slice(-20),
			events: truncateEvents(guardState.events),
			metrics,
			page: window.__behaviorGuardPage || null,
			context: extra.context || null,
			extra,
		};
	};

	const sendPayload = (payload, options = {}) => {
		const { keepAlive = false } = options;

		if (navigator.sendBeacon && keepAlive) {
			try {
				const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
				const success = navigator.sendBeacon(globalConfig.endpoint, blob);
				if (success) {
					return Promise.resolve({ success: true, method: 'beacon' });
				}
			} catch (error) {
				console.warn('BehaviorGuard: beacon send failed, falling back to fetch', error);
			}
		}

		return fetch(globalConfig.endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			keepalive: keepAlive,
			credentials: 'omit',
			body: JSON.stringify(payload),
		})
			.then((response) => response.json().catch(() => ({ success: false })))
			.catch((error) => {
				console.error('BehaviorGuard: failed to send payload', error);
				return { success: false, error: error?.message || 'Network error' };
			});
	};

	const BehaviorGuard = {
		stageReady(stage, meta = {}) {
			if (!stage) {
				return;
			}

			const payload = {
				stage,
				meta: normalizeData(meta),
				timestamp: new Date().toISOString(),
			};

			guardState.stageInfo = payload;
			guardState.stages.push(payload);
			persist();
		},

		log(eventName, data = {}) {
			if (!eventName) {
				return;
			}

			const entry = {
				name: String(eventName),
				data: normalizeData(data),
				stage: guardState.stageInfo?.stage || null,
				timestamp: new Date().toISOString(),
			};

			guardState.events.push(entry);
			guardState.events = truncateEvents(guardState.events);
			persist();
		},

		async flush(reason = 'manual', options = {}) {
			const payload = buildPayload(reason, options);
			const response = await sendPayload(payload, { keepAlive: Boolean(options.keepAlive) });

			if (options.finalize) {
				collector.stop();
			}

			if (response && response.analysis) {
				guardState.meta.lastAnalysis = response.analysis;
			} else if (response && response.data && response.data.analysis) {
				guardState.meta.lastAnalysis = response.data.analysis;
			}

			persist();
			return response;
		},

		getSessionId() {
			return guardState.sessionId;
		},

		getLastAnalysis() {
			return guardState.meta?.lastAnalysis || null;
		},
	};

	window.BehaviorGuard = BehaviorGuard;

	const initialPage = window.__behaviorGuardPage || {};
	if (initialPage.stage) {
		BehaviorGuard.stageReady(initialPage.stage, initialPage);
	}

	window.addEventListener('visibilitychange', () => {
		if (document.visibilityState === 'hidden') {
			BehaviorGuard.flush('visibility-hidden', { keepAlive: true, transient: true }).catch(() => undefined);
		}
	});

	window.addEventListener('pagehide', () => {
		BehaviorGuard.flush('page-hide', { keepAlive: true, transient: true }).catch(() => undefined);
	});
})();
