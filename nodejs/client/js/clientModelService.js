// /**
//  * KAI Guard - Client-side Model Service
//  * 
//  * This script provides browser-based TensorFlow.js functionality for the KAI Guard system.
//  * It handles model loading, prediction, and feature extraction on the client side.
//  */

// class ClientModelService {
//   constructor() {
//     this.model = null;
//     this.isModelLoaded = false;
//     this.modelLoadPromise = null;
//   }

//   /**
//    * Initialize the model
//    * @returns {Promise} - Promise that resolves when the model is loaded
//    */
//   async initModel() {
//     if (this.modelLoadPromise) {
//       return this.modelLoadPromise;
//     }

//     this.modelLoadPromise = new Promise(async (resolve) => {
//       try {
//         // Create a simple neural network model
//         this.model = tf.sequential();
        
//         // Add layers to the model
//         this.model.add(tf.layers.dense({
//           units: 16,
//           activation: 'relu',
//           inputShape: [8] // Assuming 8 input features
//         }));
        
//         this.model.add(tf.layers.dense({
//           units: 8,
//           activation: 'relu'
//         }));
        
//         this.model.add(tf.layers.dense({
//           units: 1,
//           activation: 'sigmoid' // Output between 0-1 for trust score
//         }));
        
//         // Compile the model
//         this.model.compile({
//           optimizer: 'adam',
//           loss: 'binaryCrossentropy',
//           metrics: ['accuracy']
//         });

//         this.isModelLoaded = true;
//         console.log('Client-side model initialized');
//         resolve(true);
//       } catch (error) {
//         console.error('Error initializing client-side model:', error);
//         resolve(false);
//       }
//     });

//     return this.modelLoadPromise;
//   }

//   /**
//    * Evaluate behavioral data using the model
//    * @param {Object} behavioralData - Behavioral data to evaluate
//    * @returns {Object} - Evaluation result with trust score and recommended action
//    */
//   async evaluateData(behavioralData) {
//     try {
//       if (!this.isModelLoaded) {
//         await this.initModel();
//       }

//       // Extract features from behavioral data
//       const features = this.extractFeatures(behavioralData);
      
//       // Convert to tensor
//       const inputTensor = tf.tensor2d([features]);
      
//       // Get prediction from model
//       const prediction = this.model.predict(inputTensor);
//       const trustScore = prediction.dataSync()[0];
      
//       // Determine action based on trust score
//       let action, message;
//       if (trustScore < 0.3) {
//         action = 'BLOCK';
//         message = 'Suspicious activity detected';
//       } else if (trustScore < 0.7) {
//         action = 'CHALLENGE';
//         message = 'Additional verification required';
//       } else {
//         action = 'ALLOW';
//         message = 'Session appears legitimate';
//       }
      
//       // Clean up tensors
//       inputTensor.dispose();
//       prediction.dispose();
      
//       return {
//         trustScore,
//         action,
//         message
//       };
//     } catch (error) {
//       console.error('Error evaluating data:', error);
//       // Default to challenge on error
//       return {
//         trustScore: 0.5,
//         action: 'CHALLENGE',
//         message: 'Error evaluating session'
//       };
//     }
//   }

//   /**
//    * Extract features from behavioral data
//    * @param {Object} data - Raw behavioral data
//    * @returns {Array} - Extracted feature array
//    */
//   extractFeatures(data) {
//     // This is a simplified feature extraction
//     // In a real application, you would extract meaningful features
//     // from the behavioral data
    
//     const features = [
//       data.typingSpeed || 0,
//       data.typingRhythm || 0,
//       data.mouseSpeed || 0,
//       data.mouseJitter || 0,
//       data.clickFrequency || 0,
//       data.timeOnPage || 0,
//       data.scrollPattern || 0,
//       data.deviceInfo ? 1 : 0 // Simple device trust score
//     ];
    
//     return features;
//   }
// }

// // Export for use in browser
// window.ClientModelService = ClientModelService;

/**
 * KAI Guard - Client-Side Model Service
 * * This script loads the trained TensorFlow.js model on the client-side
 * to provide real-time behavior analysis without constant server calls.
 */
class ClientModelService {
  constructor(modelPath = './model/model.json', statsPath = './normalization-stats.json') {
    this.modelPath = modelPath;
    this.statsPath = statsPath;
    this.model = null;
    this.normalizationStats = null;
    this.featureKeys = [
        'mouseSpeed', 'mouseJitter', 'typingSpeed', 'typingRhythm',
        'clickFrequency', 'timeOnPage', 'scrollPattern', 'mouseAcceleration'
    ];
    
    // Initialize the model and stats
    this._init();
  }

  /**
   * Asynchronously loads the model and normalization stats.
   */
  async _init() {
    try {
      console.log('Loading client-side model...');
      this.model = await tf.loadLayersModel(this.modelPath);
      console.log('Client-side model loaded successfully.');
      
      const response = await fetch(this.statsPath);
      this.normalizationStats = await response.json();
      console.log('Normalization stats loaded.');
    } catch (error) {
      console.error('Failed to load client-side model or stats:', error);
      // The service will be disabled, and the system will fall back to server-side analysis.
    }
  }

  /**
   * Checks if the model is ready for predictions.
   * @returns {boolean}
   */
  isReady() {
    return this.model !== null && this.normalizationStats !== null;
  }

  /**
   * Preprocesses and evaluates behavior data to predict a trust score.
   * @param {Object} behaviorData - The raw behavioral metrics.
   * @returns {Promise<number|null>} - A trust score between 0 and 1, or null if not ready.
   */
  async evaluate(behaviorData) {
    if (!this.isReady()) {
      console.warn('Client model not ready for evaluation.');
      return null;
    }

    // Use tf.tidy to clean up intermediate tensors automatically
    return tf.tidy(() => {
      // 1. Extract and normalize features
      const features = this._extractAndNormalize(behaviorData);
      
      // 2. Convert to a tensor
      const inputTensor = tf.tensor2d([features]);
      
      // 3. Make a prediction
      const prediction = this.model.predict(inputTensor);
      
      // 4. Get the result and return it
      const trustScore = prediction.dataSync()[0];
      return trustScore;
    });
  }

  /**
   * Normalizes a single feature value.
   * @param {number} value - The raw feature value.
   * @param {string} key - The name of the feature.
   * @returns {number} - The normalized value (0-1).
   */
  _normalize(value, key) {
    const stats = this.normalizationStats[key];
    if (!stats || stats.max === stats.min) {
      return 0;
    }
    return (value - stats.min) / (stats.max - stats.min);
  }

  /**
   * Extracts features from the behavior data object and normalizes them.
   * @param {Object} behaviorData - The raw behavioral metrics.
   * @returns {Array<number>} - An array of normalized feature values.
   */
  _extractAndNormalize(behaviorData) {
    return this.featureKeys.map(key => {
      const value = behaviorData[key] || 0;
      return this._normalize(value, key);
    });
  }
}