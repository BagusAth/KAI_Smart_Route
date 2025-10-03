(function () {
	if (window.BehaviorMonitorInitialized) {
		return;
	}

	window.BehaviorMonitorInitialized = true;

	class BehaviorCollector {
		constructor(options = {}) {
			this.options = Object.assign(
				{
					endpoint: '/behavior/analyze',
					sampleRate: 120,
					maxSamples: 80,
					sendInterval: 5000,
					onResult: null,
					debug: false,
				},
				options,
			);

			this.sessionId = this.options.sessionId || this.#generateSessionId();
			this.startedAt = Date.now();
			this.currentTimeAnchor = Date.now();
			this.isCollecting = false;
			this.intervalId = null;
 			this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;

			this.mouseMovements = [];
			this.clicks = [];
			this.keyPresses = [];
			this.scrollEvents = [];
			this.keyDownMap = new Map();

			this.lastMousePoint = { x: 0, y: 0 };
			this.lastMouseTime = 0;
		}

		start() {
			if (this.isCollecting) return;

			this.isCollecting = true;
			this.currentTimeAnchor = Date.now();

			document.addEventListener('mousemove', this.#onMouseMove);
			document.addEventListener('click', this.#onMouseClick);
			document.addEventListener('keydown', this.#onKeyDown);
			document.addEventListener('keyup', this.#onKeyUp);
			document.addEventListener('scroll', this.#onScroll);
			document.addEventListener('visibilitychange', this.#onVisibilityChange);

			this.intervalId = window.setInterval(() => this.#flush(false), this.options.sendInterval);

			if (this.options.debug) console.info('[BehaviorCollector] Started', this.sessionId);
		}

		stop() {
			if (!this.isCollecting) return;

			this.isCollecting = false;

			document.removeEventListener('mousemove', this.#onMouseMove);
			document.removeEventListener('click', this.#onMouseClick);
			document.removeEventListener('keydown', this.#onKeyDown);
			document.removeEventListener('keyup', this.#onKeyUp);
			document.removeEventListener('scroll', this.#onScroll);
			document.removeEventListener('visibilitychange', this.#onVisibilityChange);

			if (this.intervalId) {
				window.clearInterval(this.intervalId);
				this.intervalId = null;
			}

			this.#flush(true);
		}

		#flush(isFinal) {
			if (!this.isCollecting && !isFinal) return;

			const metrics = this.#buildMetrics();
			metrics.isFinal = isFinal;

			const headers = {
				'Content-Type': 'application/json',
				Accept: 'application/json',
			};

			if (this.csrfToken) {
				headers['X-CSRF-TOKEN'] = this.csrfToken;
			}

			fetch(this.options.endpoint, {
				method: 'POST',
				headers,
				body: JSON.stringify(metrics),
				credentials: 'same-origin',
			})
				.then((response) => response.json())
				.then((result) => {
					if (this.options.debug) console.info('[BehaviorCollector] Result', result);
					if (typeof this.options.onResult === 'function') {
						this.options.onResult(result);
					}
				})
				.catch((error) => {
					if (this.options.debug) console.error('[BehaviorCollector] Error', error);
				})
				.finally(() => {
					this.mouseMovements = [];
					this.clicks = [];
					this.keyPresses = [];
					this.scrollEvents = [];
				});
		}

		#buildMetrics() {
			const now = Date.now();
			const timeOnPage = now - this.startedAt;

			const metrics = {
				sessionId: this.sessionId,
				timestamp: now,
				timeOnPage,
				mouseSpeed: 0,
				mouseAcceleration: 0,
				mouseJitter: 0,
				clickFrequency: 0,
				typingSpeed: 0,
				keyPressTime: 0,
				typingRhythm: 0,
				scrollPattern: 0,
			};

			if (this.mouseMovements.length > 0) {
				let speedSum = 0;
				let accelSum = 0;
				let jitter = 0;

				for (let i = 0; i < this.mouseMovements.length; i++) {
					speedSum += this.mouseMovements[i].speed;
					accelSum += this.mouseMovements[i].acceleration;

					if (i > 1) {
						const prev = this.mouseMovements[i - 1];
						const older = this.mouseMovements[i - 2];
						const dx1 = prev.x - older.x;
						const dy1 = prev.y - older.y;
						const dx2 = this.mouseMovements[i].x - prev.x;
						const dy2 = this.mouseMovements[i].y - prev.y;
						const angle1 = Math.atan2(dy1, dx1);
						const angle2 = Math.atan2(dy2, dx2);
						let diff = Math.abs(angle2 - angle1);
						if (diff > Math.PI) diff = 2 * Math.PI - diff;
						jitter += diff;
					}
				}

				metrics.mouseSpeed = speedSum / this.mouseMovements.length;
				metrics.mouseAcceleration = accelSum / this.mouseMovements.length;
				metrics.mouseJitter = this.mouseMovements.length > 2 ? jitter / (this.mouseMovements.length - 2) : 0;
			}

			metrics.clickFrequency = this.clicks.length / Math.max(1, (now - this.startedAt) / 1000);

			if (this.keyPresses.length > 0) {
				let durationSum = 0;
				let intervalSum = 0;
				let intervalCount = 0;

				for (let i = 0; i < this.keyPresses.length; i++) {
					durationSum += this.keyPresses[i].pressDuration;

					if (i > 0) {
						const interval = this.keyPresses[i].timestamp - this.keyPresses[i - 1].timestamp;
						if (interval > 0 && interval < 2000) {
							intervalSum += interval;
							intervalCount += 1;
						}
					}
				}

				metrics.typingSpeed = this.keyPresses.length / Math.max(1, (now - this.startedAt) / 1000);
				metrics.keyPressTime = durationSum / this.keyPresses.length;
				metrics.typingRhythm = intervalCount > 0 ? Math.sqrt(intervalSum / intervalCount) : 0;
			}

			if (this.scrollEvents.length > 1) {
				let sum = 0;
				for (let i = 1; i < this.scrollEvents.length; i++) {
					sum += Math.abs(this.scrollEvents[i].y - this.scrollEvents[i - 1].y);
				}
				metrics.scrollPattern = sum / this.scrollEvents.length;
			}

			return metrics;
		}

		#onMouseMove = (event) => {
			const now = Date.now();
			if (now - this.lastMouseTime < this.options.sampleRate) return;

			let speed = 0;
			let acceleration = 0;

			if (this.lastMouseTime) {
				const distance = Math.hypot(event.clientX - this.lastMousePoint.x, event.clientY - this.lastMousePoint.y);
				const deltaT = now - this.lastMouseTime;
				speed = distance / deltaT;

				if (this.mouseMovements.length > 0) {
					const lastSpeed = this.mouseMovements[this.mouseMovements.length - 1].speed;
					acceleration = (speed - lastSpeed) / deltaT;
				}
			}

			this.mouseMovements.push({ x: event.clientX, y: event.clientY, timestamp: now, speed, acceleration });
			if (this.mouseMovements.length > this.options.maxSamples) this.mouseMovements.shift();

			this.lastMousePoint = { x: event.clientX, y: event.clientY };
			this.lastMouseTime = now;
		};

		#onMouseClick = (event) => {
			this.clicks.push({ x: event.clientX, y: event.clientY, timestamp: Date.now(), button: event.button });
			if (this.clicks.length > this.options.maxSamples) this.clicks.shift();
		};

		#onKeyDown = (event) => {
			if (!this.keyDownMap.has(event.key)) {
				this.keyDownMap.set(event.key, Date.now());
			}
		};

		#onKeyUp = (event) => {
			const downTime = this.keyDownMap.get(event.key);
			if (!downTime) return;

			const duration = Date.now() - downTime;
			this.keyDownMap.delete(event.key);
			this.keyPresses.push({ key: event.key, pressDuration: duration, timestamp: Date.now() });
			if (this.keyPresses.length > this.options.maxSamples) this.keyPresses.shift();
		};

		#onScroll = () => {
			this.scrollEvents.push({ x: window.scrollX, y: window.scrollY, timestamp: Date.now() });
			if (this.scrollEvents.length > this.options.maxSamples) this.scrollEvents.shift();
		};

		#onVisibilityChange = () => {
			if (document.visibilityState === 'visible') {
				this.currentTimeAnchor = Date.now();
			} else {
				this.startedAt += Date.now() - this.currentTimeAnchor;
			}
		};

		#generateSessionId() {
			return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (char) => {
				const rand = (Math.random() * 16) | 0;
				const value = char === 'x' ? rand : (rand & 0x3) | 0x8;
				return value.toString(16);
			});
		}
	}

	function initBehaviorGuard() {
		const overlay = document.getElementById('behavior-guard-overlay');
		if (!overlay) return;

		const messageEl = overlay.querySelector('[data-behavior-message]');
		const actionButton = overlay.querySelector('[data-behavior-action]');
		const protectedElements = Array.from(document.querySelectorAll('[data-behavior-protected]'));

		let currentState = 'idle';
		let collector;

		function setOverlay(state, message) {
			currentState = state;
			overlay.dataset.state = state;
			if (messageEl && message) {
				messageEl.textContent = message;
			}

			if (state === 'idle') {
				overlay.classList.remove('is-active');
				overlay.setAttribute('aria-hidden', 'true');
				overlay.hidden = true;
			} else {
				overlay.hidden = false;
				overlay.classList.add('is-active');
				overlay.setAttribute('aria-hidden', 'false');
			}
		}

		function toggleProtected(disabled) {
			protectedElements.forEach((element) => {
				if (element instanceof HTMLFormElement || element instanceof HTMLFieldSetElement) {
					Array.from(element.elements).forEach((field) => {
						if ('disabled' in field) {
							field.disabled = disabled;
						}
					});
				} else if ('disabled' in element) {
					element.disabled = disabled;
				}
			});
		}

		function handleResult(result) {
			if (!result || !result.success) return;

			const action = result.action;
			const message = result.message || 'Aktivitas mencurigakan terdeteksi.';

			switch (action) {
				case 'BLOCK':
					setOverlay('block', message);
					toggleProtected(true);
					collector?.stop();
					break;
				case 'CHALLENGE':
					if (currentState !== 'block') {
						toggleProtected(true);
						setOverlay('challenge', message);
					}
					break;
				default:
					if (currentState !== 'idle') {
						toggleProtected(false);
						setOverlay('idle');
					}
			}
		}

		collector = new BehaviorCollector({
			onResult: handleResult,
		});

		collector.start();

		window.addEventListener('beforeunload', () => collector.stop(), { once: true });

		if (actionButton) {
			actionButton.addEventListener('click', () => {
				if (currentState === 'challenge') {
					toggleProtected(false);
					setOverlay('idle');
				}
			});
		}
	}

	document.addEventListener('DOMContentLoaded', initBehaviorGuard);
})();
