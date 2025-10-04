// /**
//  * KAI Guard - Behavior Collector
//  * 
//  * This script collects user behavior data to help distinguish between humans and bots.
//  * It tracks mouse movements, keyboard patterns, page interactions, and device information.
//  * The collected data is sent to the server for analysis by the ML model.
//  */

// class BehaviorCollector {
//   constructor(options = {}) {
//     // Configuration options
//     this.options = {
//       sampleRate: options.sampleRate || 100, // ms between samples
//       maxSamples: options.maxSamples || 100, // max number of samples to collect
//       sendInterval: options.sendInterval || 2000, // ms between sending data to server
//       endpoint: options.endpoint || '/api/behavior/analyze', // server endpoint
//       sessionId: options.sessionId || this._generateSessionId(),
//       debug: options.debug || false
//     };
    
//     // Data storage
//     this.data = {
//       sessionId: this.options.sessionId,
//       timestamp: Date.now(),
//       mouseMovements: [],
//       clicks: [],
//       keyPresses: [],
//       scrollEvents: [],
//       timeOnPage: 0,
//       deviceInfo: this._collectDeviceInfo()
//     };
    
//     // Internal state
//     this._isCollecting = false;
//     this._startTime = null;
//     this._lastMousePosition = { x: 0, y: 0 };
//     this._lastMouseTime = 0;
//     this._keyPressTimestamps = {};
//     this._sendInterval = null;
    
//     // Bind event handlers
//     this._onMouseMove = this._onMouseMove.bind(this);
//     this._onMouseClick = this._onMouseClick.bind(this);
//     this._onKeyDown = this._onKeyDown.bind(this);
//     this._onKeyUp = this._onKeyUp.bind(this);
//     this._onScroll = this._onScroll.bind(this);
//     this._onVisibilityChange = this._onVisibilityChange.bind(this);
    
//     // Debug logging
//     if (this.options.debug) {
//       console.log('BehaviorCollector initialized with options:', this.options);
//     }
//   }
  
//   /**
//    * Start collecting behavior data
//    */
//   start() {
//     if (this._isCollecting) return;
    
//     this._isCollecting = true;
//     this._startTime = Date.now();
    
//     // Add event listeners
//     document.addEventListener('mousemove', this._onMouseMove);
//     document.addEventListener('click', this._onMouseClick);
//     document.addEventListener('keydown', this._onKeyDown);
//     document.addEventListener('keyup', this._onKeyUp);
//     document.addEventListener('scroll', this._onScroll);
//     document.addEventListener('visibilitychange', this._onVisibilityChange);
    
//     // Set up periodic sending of data
//     this._sendInterval = setInterval(() => {
//       this._processAndSendData();
//     }, this.options.sendInterval);
    
//     if (this.options.debug) {
//       console.log('BehaviorCollector started');
//     }
//   }
  
//   /**
//    * Stop collecting behavior data
//    */
//   stop() {
//     if (!this._isCollecting) return;
    
//     this._isCollecting = false;
    
//     // Remove event listeners
//     document.removeEventListener('mousemove', this._onMouseMove);
//     document.removeEventListener('click', this._onMouseClick);
//     document.removeEventListener('keydown', this._onKeyDown);
//     document.removeEventListener('keyup', this._onKeyUp);
//     document.removeEventListener('scroll', this._onScroll);
//     document.removeEventListener('visibilitychange', this._onVisibilityChange);
    
//     // Clear sending interval
//     if (this._sendInterval) {
//       clearInterval(this._sendInterval);
//       this._sendInterval = null;
//     }
    
//     // Send final data
//     this._processAndSendData(true);
    
//     if (this.options.debug) {
//       console.log('BehaviorCollector stopped');
//     }
//   }
  
//   /**
//    * Handle mouse movement events
//    * @param {MouseEvent} event - Mouse event
//    */
//   _onMouseMove(event) {
//     const now = Date.now();
//     const timeDiff = now - this._lastMouseTime;
    
//     // Only sample at the specified rate
//     if (timeDiff < this.options.sampleRate) return;
    
//     const x = event.clientX;
//     const y = event.clientY;
    
//     // Calculate speed and acceleration if we have previous position
//     let speed = 0;
//     let acceleration = 0;
    
//     if (this._lastMouseTime > 0) {
//       const dx = x - this._lastMousePosition.x;
//       const dy = y - this._lastMousePosition.y;
//       const distance = Math.sqrt(dx * dx + dy * dy);
      
//       speed = distance / timeDiff; // pixels per ms
      
//       // If we have at least two movements, calculate acceleration
//       if (this.data.mouseMovements.length > 0) {
//         const lastSpeed = this.data.mouseMovements[this.data.mouseMovements.length - 1].speed;
//         acceleration = (speed - lastSpeed) / timeDiff;
//       }
//     }
    
//     // Store the movement data
//     this.data.mouseMovements.push({
//       x,
//       y,
//       timestamp: now,
//       speed,
//       acceleration
//     });
    
//     // Limit the number of samples
//     if (this.data.mouseMovements.length > this.options.maxSamples) {
//       this.data.mouseMovements.shift();
//     }
    
//     // Update last position and time
//     this._lastMousePosition = { x, y };
//     this._lastMouseTime = now;
//   }
  
//   /**
//    * Handle mouse click events
//    * @param {MouseEvent} event - Mouse event
//    */
//   _onMouseClick(event) {
//     this.data.clicks.push({
//       x: event.clientX,
//       y: event.clientY,
//       button: event.button,
//       timestamp: Date.now()
//     });
    
//     // Limit the number of samples
//     if (this.data.clicks.length > this.options.maxSamples) {
//       this.data.clicks.shift();
//     }
//   }
  
//   /**
//    * Handle key down events
//    * @param {KeyboardEvent} event - Keyboard event
//    */
//   _onKeyDown(event) {
//     // Don't log actual key values for privacy/security reasons
//     // Just store the fact that a key was pressed and timing information
//     const key = event.key.length === 1 ? 'character' : event.key;
//     this._keyPressTimestamps[key] = Date.now();
//   }
  
//   /**
//    * Handle key up events
//    * @param {KeyboardEvent} event - Keyboard event
//    */
//   _onKeyUp(event) {
//     const key = event.key.length === 1 ? 'character' : event.key;
//     const downTime = this._keyPressTimestamps[key];
    
//     if (downTime) {
//       const upTime = Date.now();
//       const pressDuration = upTime - downTime;
      
//       this.data.keyPresses.push({
//         key,
//         pressDuration,
//         timestamp: upTime
//       });
      
//       // Limit the number of samples
//       if (this.data.keyPresses.length > this.options.maxSamples) {
//         this.data.keyPresses.shift();
//       }
      
//       // Clean up
//       delete this._keyPressTimestamps[key];
//     }
//   }
  
//   /**
//    * Handle scroll events
//    * @param {Event} event - Scroll event
//    */
//   _onScroll() {
//     this.data.scrollEvents.push({
//       scrollX: window.scrollX,
//       scrollY: window.scrollY,
//       timestamp: Date.now()
//     });
    
//     // Limit the number of samples
//     if (this.data.scrollEvents.length > this.options.maxSamples) {
//       this.data.scrollEvents.shift();
//     }
//   }
  
//   /**
//    * Handle visibility change events (tab switching)
//    */
//   _onVisibilityChange() {
//     if (document.visibilityState === 'visible') {
//       // User returned to the page
//       this._startTime = Date.now();
//     } else {
//       // User left the page, update time on page
//       this.data.timeOnPage += Date.now() - this._startTime;
//     }
//   }
  
//   /**
//    * Collect device information
//    * @returns {Object} - Device information
//    */
//   _collectDeviceInfo() {
//     return {
//       screenResolution: `${window.screen.width}x${window.screen.height}`,
//       windowSize: `${window.innerWidth}x${window.innerHeight}`,
//       colorDepth: window.screen.colorDepth,
//       pixelRatio: window.devicePixelRatio,
//       timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
//       language: navigator.language,
//       platform: navigator.platform,
//       userAgent: navigator.userAgent,
//       touchSupport: 'ontouchstart' in window
//     };
//   }
  
//   /**
//    * Generate a unique session ID
//    * @returns {String} - Session ID
//    */
//   _generateSessionId() {
//     return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
//       const r = Math.random() * 16 | 0;
//       const v = c === 'x' ? r : (r & 0x3 | 0x8);
//       return v.toString(16);
//     });
//   }
  
//   /**
//    * Process collected data and send to server
//    * @param {Boolean} isFinal - Whether this is the final data send before stopping
//    */
//   _processAndSendData(isFinal = false) {
//     // Update time on page if collector is still running
//     if (this._isCollecting && document.visibilityState === 'visible') {
//       this.data.timeOnPage += Date.now() - this._startTime;
//       this._startTime = Date.now();
//     }
    
//     // Calculate derived metrics
//     const processedData = this._calculateMetrics();
    
//     // Send data to server
//     this._sendToServer(processedData, isFinal);
//   }
  
//   /**
//    * Calculate derived metrics from raw data
//    * @returns {Object} - Processed behavioral data
//    */
//   _calculateMetrics() {
//     // Create a copy of the data to avoid modifying the original
//     const processedData = {
//       sessionId: this.data.sessionId,
//       timestamp: Date.now(),
//       deviceInfo: this.data.deviceInfo,
//       timeOnPage: this.data.timeOnPage
//     };
    
//     // Mouse movement metrics
//     if (this.data.mouseMovements.length > 0) {
//       // Calculate average speed and acceleration
//       let totalSpeed = 0;
//       let totalAcceleration = 0;
//       let jitterSum = 0;
      
//       for (let i = 0; i < this.data.mouseMovements.length; i++) {
//         totalSpeed += this.data.mouseMovements[i].speed;
//         totalAcceleration += this.data.mouseMovements[i].acceleration;
        
//         // Calculate jitter (changes in direction)
//         if (i > 1) {
//           const prev = this.data.mouseMovements[i-1];
//           const current = this.data.mouseMovements[i];
          
//           // Calculate angle change in movement direction
//           const dx1 = prev.x - this.data.mouseMovements[i-2].x;
//           const dy1 = prev.y - this.data.mouseMovements[i-2].y;
//           const dx2 = current.x - prev.x;
//           const dy2 = current.y - prev.y;
          
//           // Avoid division by zero
//           if (dx1 !== 0 && dy1 !== 0 && dx2 !== 0 && dy2 !== 0) {
//             const angle1 = Math.atan2(dy1, dx1);
//             const angle2 = Math.atan2(dy2, dx2);
//             let angleDiff = Math.abs(angle2 - angle1);
            
//             // Normalize angle difference
//             if (angleDiff > Math.PI) {
//               angleDiff = 2 * Math.PI - angleDiff;
//             }
            
//             jitterSum += angleDiff;
//           }
//         }
//       }
      
//       processedData.mouseSpeed = totalSpeed / this.data.mouseMovements.length;
//       processedData.mouseAcceleration = totalAcceleration / this.data.mouseMovements.length;
//       processedData.mouseJitter = this.data.mouseMovements.length > 2 ? 
//         jitterSum / (this.data.mouseMovements.length - 2) : 0;
//     } else {
//       processedData.mouseSpeed = 0;
//       processedData.mouseAcceleration = 0;
//       processedData.mouseJitter = 0;
//     }
    
//     // Click frequency
//     processedData.clickFrequency = this.data.clicks.length / 
//       (Math.max(1, (Date.now() - this.data.timestamp) / 1000)); // clicks per second
    
//     // Keyboard metrics
//     if (this.data.keyPresses.length > 0) {
//       // Calculate typing speed and rhythm
//       let totalPressDuration = 0;
//       let intervalSum = 0;
//       let intervalCount = 0;
      
//       for (let i = 0; i < this.data.keyPresses.length; i++) {
//         totalPressDuration += this.data.keyPresses[i].pressDuration;
        
//         // Calculate intervals between key presses
//         if (i > 0) {
//           const interval = this.data.keyPresses[i].timestamp - 
//             this.data.keyPresses[i-1].timestamp;
          
//           // Only count reasonable intervals (< 2 seconds)
//           if (interval < 2000) {
//             intervalSum += interval;
//             intervalCount++;
//           }
//         }
//       }
      
//       processedData.typingSpeed = this.data.keyPresses.length / 
//         (Math.max(1, (Date.now() - this.data.timestamp) / 1000)); // keys per second
//       processedData.keyPressTime = totalPressDuration / this.data.keyPresses.length;
//       processedData.typingRhythm = intervalCount > 0 ? 
//         Math.sqrt(intervalSum / intervalCount) : 0; // standard deviation of intervals
//     } else {
//       processedData.typingSpeed = 0;
//       processedData.keyPressTime = 0;
//       processedData.typingRhythm = 0;
//     }
    
//     // Scroll pattern
//     if (this.data.scrollEvents.length > 0) {
//       let scrollDistanceSum = 0;
      
//       for (let i = 1; i < this.data.scrollEvents.length; i++) {
//         const dy = this.data.scrollEvents[i].scrollY - this.data.scrollEvents[i-1].scrollY;
//         scrollDistanceSum += Math.abs(dy);
//       }
      
//       processedData.scrollPattern = scrollDistanceSum / this.data.scrollEvents.length;
//     } else {
//       processedData.scrollPattern = 0;
//     }
    
//     return processedData;
//   }
  
//   /**
//    * Process data using client-side model or send to server
//    * @param {Object} data - Processed behavioral data
//    * @param {Boolean} isFinal - Whether this is the final data send
//    */
//   _sendToServer(data, isFinal) {
//     // Add flag for final data
//     data.isFinal = isFinal;
    
//     // Check if we have the client-side model service available
//     if (window.ClientModelService) {
//       // Use client-side model if available
//       if (!this.clientModel) {
//         this.clientModel = new ClientModelService();
//       }
      
//       // Evaluate data using the client-side model
//       this.clientModel.evaluateData(data)
//         .then(result => {
//           if (this.options.debug) {
//             console.log('Client-side model result:', result);
//           }
          
//           // If the model indicates the user is a bot, trigger the callback
//           if (result && result.action === 'BLOCK' && typeof this.options.onBotDetected === 'function') {
//             this.options.onBotDetected(result);
//           }
          
//           // If the model requires a challenge, trigger the callback
//           if (result && result.action === 'CHALLENGE' && typeof this.options.onChallengeRequired === 'function') {
//             this.options.onChallengeRequired(result);
//           }
//         })
//         .catch(error => {
//           if (this.options.debug) {
//             console.error('Error evaluating data with client-side model:', error);
//           }
//           // Fall back to server if client-side model fails
//           this._sendToServerFallback(data);
//         });
//     } else {
//       // Fall back to server if client-side model is not available
//       this._sendToServerFallback(data);
//     }
//   }
  
//   /**
//    * Fallback method to send data to server
//    * @param {Object} data - Processed behavioral data
//    */
//   _sendToServerFallback(data) {
//     fetch(this.options.endpoint, {
//       method: 'POST',
//       headers: {
//         'Content-Type': 'application/json'
//       },
//       body: JSON.stringify(data)
//     })
//     .then(response => response.json())
//     .then(result => {
//       if (this.options.debug) {
//         console.log('Server response:', result);
//       }
      
//       // If the server indicates the user is a bot, trigger the callback
//       if (result && result.action === 'BLOCK' && typeof this.options.onBotDetected === 'function') {
//         this.options.onBotDetected(result);
//       }
      
//       // If the server requires a challenge, trigger the callback
//       if (result && result.action === 'CHALLENGE' && typeof this.options.onChallengeRequired === 'function') {
//         this.options.onChallengeRequired(result);
//       }
//     })
//     .catch(error => {
//       if (this.options.debug) {
//         console.error('Error sending data to server:', error);
//       }
//     });
//   }
//   }


// // Export for use in browser and Node.js environments
// if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
//   module.exports = BehaviorCollector;
// } else {
//   window.BehaviorCollector = BehaviorCollector;
// }

/**
 * KAI Guard - Optimized Behavior Collector
 * 
 * Collects user behavior and computes metrics in real-time.
 * Sends only final JSON metrics to server/client model.
 */

/**
 * BehaviorCollector - Client-side behavioral data collection
 * Sends processed metrics periodically to server to speed up analysis.
 */
class BehaviorCollector {
  constructor(options = {}) {
    this.options = {
      sampleRate: options.sampleRate || 100,      // ms between samples
      maxSamples: options.maxSamples || 100,      // max samples stored per type
      sendInterval: options.sendInterval || 2000, // ms between sends
      endpoint: options.endpoint || '/api/behavior/analyze',
      sessionId: options.sessionId || this._generateSessionId(),
      debug: options.debug || false
    };

    this.data = {
      sessionId: this.options.sessionId,
      timestamp: Date.now(),
      mouseMovements: [],
      clicks: [],
      keyPresses: [],
      scrollEvents: [],
      timeOnPage: 0,
      deviceInfo: this._collectDeviceInfo()
    };

    this._isCollecting = false;
    this._startTime = null;
    this._lastMousePosition = { x: 0, y: 0 };
    this._lastMouseTime = 0;
    this._keyPressTimestamps = {};
    this._sendInterval = null;

    // Bind handlers
    this._onMouseMove = this._onMouseMove.bind(this);
    this._onMouseClick = this._onMouseClick.bind(this);
    this._onKeyDown = this._onKeyDown.bind(this);
    this._onKeyUp = this._onKeyUp.bind(this);
    this._onScroll = this._onScroll.bind(this);
    this._onVisibilityChange = this._onVisibilityChange.bind(this);

    if (this.options.debug) console.log('BehaviorCollector initialized', this.options);
  }

  start() {
    if (this._isCollecting) return;

    this._isCollecting = true;
    this._startTime = Date.now();

    document.addEventListener('mousemove', this._onMouseMove);
    document.addEventListener('click', this._onMouseClick);
    document.addEventListener('keydown', this._onKeyDown);
    document.addEventListener('keyup', this._onKeyUp);
    document.addEventListener('scroll', this._onScroll);
    document.addEventListener('visibilitychange', this._onVisibilityChange);

    // Send processed metrics periodically
    this._sendInterval = setInterval(() => {
      this._sendMetrics(false);
    }, this.options.sendInterval);

    if (this.options.debug) console.log('BehaviorCollector started');
  }

  stop() {
    if (!this._isCollecting) return;

    this._isCollecting = false;

    document.removeEventListener('mousemove', this._onMouseMove);
    document.removeEventListener('click', this._onMouseClick);
    document.removeEventListener('keydown', this._onKeyDown);
    document.removeEventListener('keyup', this._onKeyUp);
    document.removeEventListener('scroll', this._onScroll);
    document.removeEventListener('visibilitychange', this._onVisibilityChange);

    if (this._sendInterval) clearInterval(this._sendInterval);

    // Send final metrics
    this._sendMetrics(true);

    if (this.options.debug) console.log('BehaviorCollector stopped');
  }

  _onMouseMove(e) {
    const now = Date.now();
    if (now - this._lastMouseTime < this.options.sampleRate) return;

    const x = e.clientX;
    const y = e.clientY;
    let speed = 0, acceleration = 0;

    if (this._lastMouseTime > 0) {
      const dx = x - this._lastMousePosition.x;
      const dy = y - this._lastMousePosition.y;
      const dist = Math.sqrt(dx * dx + dy * dy);
      speed = dist / (now - this._lastMouseTime);
      if (this.data.mouseMovements.length > 0) {
        const lastSpeed = this.data.mouseMovements[this.data.mouseMovements.length - 1].speed;
        acceleration = (speed - lastSpeed) / (now - this._lastMouseTime);
      }
    }

    this.data.mouseMovements.push({ x, y, timestamp: now, speed, acceleration });
    if (this.data.mouseMovements.length > this.options.maxSamples) this.data.mouseMovements.shift();

    this._lastMousePosition = { x, y };
    this._lastMouseTime = now;
  }

  _onMouseClick(e) {
    this.data.clicks.push({ x: e.clientX, y: e.clientY, button: e.button, timestamp: Date.now() });
    if (this.data.clicks.length > this.options.maxSamples) this.data.clicks.shift();
  }

  _onKeyDown(e) { this._keyPressTimestamps[e.key] = Date.now(); }

  _onKeyUp(e) {
    const downTime = this._keyPressTimestamps[e.key];
    if (downTime) {
      const duration = Date.now() - downTime;
      this.data.keyPresses.push({ key: e.key, pressDuration: duration, timestamp: Date.now() });
      if (this.data.keyPresses.length > this.options.maxSamples) this.data.keyPresses.shift();
      delete this._keyPressTimestamps[e.key];
    }
  }

  _onScroll() {
    this.data.scrollEvents.push({ scrollX: window.scrollX, scrollY: window.scrollY, timestamp: Date.now() });
    if (this.data.scrollEvents.length > this.options.maxSamples) this.data.scrollEvents.shift();
  }

  _onVisibilityChange() {
    if (document.visibilityState === 'visible') this._startTime = Date.now();
    else this.data.timeOnPage += Date.now() - this._startTime;
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
      touchSupport: 'ontouchstart' in window
    };
  }

  _generateSessionId() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
      const r = Math.random() * 16 | 0;
      const v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  /**
   * Calculate metrics from collected raw data
   */
  _calculateMetrics() {
    const processed = {
      sessionId: this.data.sessionId,
      timestamp: Date.now(),
      deviceInfo: this.data.deviceInfo,
      timeOnPage: this.data.timeOnPage
    };

    // Mouse
    if (this.data.mouseMovements.length > 0) {
      let totalSpeed = 0, totalAcc = 0, jitterSum = 0;
      for (let i = 0; i < this.data.mouseMovements.length; i++) {
        totalSpeed += this.data.mouseMovements[i].speed;
        totalAcc += this.data.mouseMovements[i].acceleration;

        if (i > 1) {
          const prev = this.data.mouseMovements[i-1];
          const curr = this.data.mouseMovements[i];
          const dx1 = prev.x - this.data.mouseMovements[i-2].x;
          const dy1 = prev.y - this.data.mouseMovements[i-2].y;
          const dx2 = curr.x - prev.x;
          const dy2 = curr.y - prev.y;
          const angle1 = Math.atan2(dy1, dx1);
          const angle2 = Math.atan2(dy2, dx2);
          let diff = Math.abs(angle2 - angle1);
          if (diff > Math.PI) diff = 2 * Math.PI - diff;
          jitterSum += diff;
        }
      }
      processed.mouseSpeed = totalSpeed / this.data.mouseMovements.length;
      processed.mouseAcceleration = totalAcc / this.data.mouseMovements.length;
      processed.mouseJitter = this.data.mouseMovements.length > 2 ? jitterSum / (this.data.mouseMovements.length - 2) : 0;
    } else {
      processed.mouseSpeed = 0;
      processed.mouseAcceleration = 0;
      processed.mouseJitter = 0;
    }

    // Clicks
    processed.clickFrequency = this.data.clicks.length / Math.max(1, (Date.now() - this.data.timestamp)/1000);

    // Typing
    if (this.data.keyPresses.length > 0) {
      let totalPress = 0, intervalSum = 0, intervalCount = 0;
      for (let i = 0; i < this.data.keyPresses.length; i++) {
        totalPress += this.data.keyPresses[i].pressDuration;
        if (i > 0) {
          const interval = this.data.keyPresses[i].timestamp - this.data.keyPresses[i-1].timestamp;
          if (interval < 2000) { intervalSum += interval; intervalCount++; }
        }
      }
      processed.typingSpeed = this.data.keyPresses.length / Math.max(1, (Date.now() - this.data.timestamp)/1000);
      processed.keyPressTime = totalPress / this.data.keyPresses.length;
      processed.typingRhythm = intervalCount > 0 ? Math.sqrt(intervalSum / intervalCount) : 0;
    } else {
      processed.typingSpeed = 0;
      processed.keyPressTime = 0;
      processed.typingRhythm = 0;
    }

    // Scroll
    if (this.data.scrollEvents.length > 1) {
      let scrollSum = 0;
      for (let i = 1; i < this.data.scrollEvents.length; i++) {
        scrollSum += Math.abs(this.data.scrollEvents[i].scrollY - this.data.scrollEvents[i-1].scrollY);
      }
      processed.scrollPattern = scrollSum / this.data.scrollEvents.length;
    } else {
      processed.scrollPattern = 0;
    }

    return processed;
  }

  /**
   * Send metrics to server
   */
  _sendMetrics(isFinal = false) {
    if (this._isCollecting && document.visibilityState === 'visible') {
      this.data.timeOnPage += Date.now() - this._startTime;
      this._startTime = Date.now();
    }

    const metrics = this._calculateMetrics();
    metrics.isFinal = isFinal;

    fetch(this.options.endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(metrics)
    }).catch(err => { if (this.options.debug) console.error('Send metrics failed', err); });

    if (this.options.debug) console.log('Metrics sent', metrics);
  }
}

// Export
if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
  module.exports = BehaviorCollector;
} else {
  window.BehaviorCollector = BehaviorCollector;
}
