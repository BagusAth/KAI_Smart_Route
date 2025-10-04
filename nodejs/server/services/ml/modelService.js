let tf;
try {
  tf = require('@tensorflow/tfjs-node');
} catch (error) {
  console.warn('TensorFlow.js native bindings not available. Falling back to non-TensorFlow model service.');
  module.exports = require('./modelServiceNonTF');
  module.exports.usesTensorFlow = false;
  return;
}

// // const tf = require('@tensorflow/tfjs-node');
// // const path = require('path');
// // const fs = require('fs');

// // // Path to the saved model
// // const MODEL_PATH = path.join(__dirname, 'model');

// // // Default model if no trained model exists yet
// // let model;

// // /**
// //  * Initialize the ML model
// //  * Loads the model from disk if it exists, otherwise creates a new one
// //  */
// // async function initModel() {
// //   try {
// //     // Check if model exists on disk
// //     if (fs.existsSync(MODEL_PATH)) {
// //       console.log('Loading existing model...');
// //       model = await tf.loadLayersModel(`file://${MODEL_PATH}/model.json`);
// //       console.log('Model loaded successfully');
// //     } else {
// //       console.log('Creating new model...');
// //       // Create a simple neural network model
// //       model = tf.sequential();
      
// //       // Add layers to the model
// //       model.add(tf.layers.dense({
// //         units: 16,
// //         activation: 'relu',
// //         inputShape: [8] // Assuming 8 input features
// //       }));
      
// //       model.add(tf.layers.dense({
// //         units: 8,
// //         activation: 'relu'
// //       }));
      
// //       model.add(tf.layers.dense({
// //         units: 1,
// //         activation: 'sigmoid' // Output between 0-1 for trust score
// //       }));
      
// //       // Compile the model
// //       model.compile({
// //         optimizer: 'adam',
// //         loss: 'binaryCrossentropy',
// //         metrics: ['accuracy']
// //       });
      
// //       console.log('New model created');
      
// //       // Create the model directory if it doesn't exist
// //       if (!fs.existsSync(MODEL_PATH)) {
// //         fs.mkdirSync(MODEL_PATH, { recursive: true });
// //       }
// //     }
    
// //     return true;
// //   } catch (error) {
// //     console.error('Error initializing model:', error);
// //     return false;
// //   }
// // }

// // /**
// //  * Evaluate a session using the trained model
// //  * @param {Object} sessionData - Behavioral data from the session
// //  * @returns {Object} - Evaluation result with trust score and recommended action
// //  */
// // async function evaluateSession(sessionData) {
// //   try {
// //     // Extract features from session data
// //     const features = extractFeatures(sessionData);
    
// //     // Convert to tensor
// //     const inputTensor = tf.tensor2d([features]);
    
// //     // Get prediction from model
// //     const prediction = model.predict(inputTensor);
// //     const trustScore = prediction.dataSync()[0];
    
// //     // Determine action based on trust score
// //     let action, message;
// //     if (trustScore < 0.3) {
// //       action = 'BLOCK';
// //       message = 'Suspicious activity detected';
// //     } else if (trustScore < 0.7) {
// //       action = 'CHALLENGE';
// //       message = 'Additional verification required';
// //     } else {
// //       action = 'ALLOW';
// //       message = 'Session appears legitimate';
// //     }
    
// //     // Clean up tensors
// //     inputTensor.dispose();
// //     prediction.dispose();
    
// //     return {
// //       trustScore,
// //       action,
// //       message
// //     };
// //   } catch (error) {
// //     console.error('Error evaluating session:', error);
// //     // Default to challenge on error
// //     return {
// //       trustScore: 0.5,
// //       action: 'CHALLENGE',
// //       message: 'Error evaluating session'
// //     };
// //   }
// // }

// // /**
// //  * Train the model with labeled data
// //  * @param {Array} trainingData - Array of labeled data points
// //  * @param {Number} epochs - Number of training epochs
// //  * @returns {Object} - Training history
// //  */
// // async function trainModel(trainingData, epochs = 10) {
// //   try {
// //     // Extract features and labels from training data
// //     const features = [];
// //     const labels = [];
    
// //     trainingData.forEach(item => {
// //       features.push(extractFeatures(item.data));
// //       labels.push(item.label); // Assuming label is 0 or 1
// //     });
    
// //     // Convert to tensors
// //     const xs = tf.tensor2d(features);
// //     const ys = tf.tensor2d(labels, [labels.length, 1]);
    
// //     // Train the model
// //     const history = await model.fit(xs, ys, {
// //       epochs,
// //       batchSize: 32,
// //       validationSplit: 0.2,
// //       callbacks: {
// //         onEpochEnd: (epoch, logs) => {
// //           console.log(`Epoch ${epoch + 1} of ${epochs} completed. Loss: ${logs.loss.toFixed(4)}`);
// //         }
// //       }
// //     });
    
// //     // Save the model
// //     await model.save(`file://${MODEL_PATH}`);
// //     console.log('Model saved successfully');
    
// //     // Clean up tensors
// //     xs.dispose();
// //     ys.dispose();
    
// //     return history;
// //   } catch (error) {
// //     console.error('Error training model:', error);
// //     throw error;
// //   }
// // }

// // /**
// //  * Extract features from session data
// //  * @param {Object} sessionData - Raw behavioral data
// //  * @returns {Array} - Extracted feature array
// //  */
// // function extractFeatures(sessionData) {
// //   // This is a simplified feature extraction
// //   // In a real application, you would extract meaningful features
// //   // from the behavioral data
  
// //   const features = [
// //     sessionData.typingSpeed || 0,
// //     sessionData.typingVariance || 0,
// //     sessionData.mousePath ? sessionData.mousePath.length : 0,
// //     sessionData.clickFrequency || 0,
// //     sessionData.timeOnPage || 0,
// //     sessionData.navigationPattern ? sessionData.navigationPattern.length : 0,
// //     sessionData.deviceTrust || 0,
// //     sessionData.ipReputation || 0
// //   ];
  
// //   return features;
// // }

// // module.exports = {
// //   initModel,
// //   evaluateSession,
// //   trainModel
// // };

// const tf = require('@tensorflow/tfjs-node');
// const path = require('path');
// const fs = require('fs');

// const MODEL_PATH = path.join(__dirname, 'model');
// const STATS_PATH = path.join(__dirname, '../../../normalization-stats.json');

// let model;
// let normalizationStats;

// async function initModel() {
//   try {
//     if (fs.existsSync(MODEL_PATH) && fs.existsSync(STATS_PATH)) {
//       console.log('Loading trained model...');
//       model = await tf.loadLayersModel(`file://${MODEL_PATH}/model.json`);
//       normalizationStats = JSON.parse(fs.readFileSync(STATS_PATH));
//       console.log('Model loaded successfully');
//     } else {
//       console.warn('No trained model found. Please run train-model.js first.');
//       return false;
//     }
    
//     return true;
//   } catch (error) {
//     console.error('Error initializing model:', error);
//     return false;
//   }
// }

// function normalizeFeature(value, min, max) {
//   if (max === min) return 0;
//   return (value - min) / (max - min);
// }

// function extractFeatures(sessionData) {
//   const raw = {
//     mouseSpeed: sessionData.mouseSpeed || 0,
//     mouseJitter: sessionData.mouseJitter || 0,
//     typingSpeed: sessionData.typingSpeed || 0,
//     typingRhythm: sessionData.typingRhythm || 0,
//     clickFrequency: sessionData.clickFrequency || 0,
//     timeOnPage: sessionData.timeOnPage || 0,
//     scrollPattern: sessionData.scrollPattern || 0,
//     mouseAcceleration: sessionData.mouseAcceleration || 0
//   };
  
//   if (!normalizationStats) {
//     console.warn('No normalization stats available');
//     return Object.values(raw);
//   }
  
//   const features = [
//     normalizeFeature(raw.mouseSpeed, normalizationStats.mouseSpeed.min, normalizationStats.mouseSpeed.max),
//     normalizeFeature(raw.mouseJitter, normalizationStats.mouseJitter.min, normalizationStats.mouseJitter.max),
//     normalizeFeature(raw.typingSpeed, normalizationStats.typingSpeed.min, normalizationStats.typingSpeed.max),
//     normalizeFeature(raw.typingRhythm, normalizationStats.typingRhythm.min, normalizationStats.typingRhythm.max),
//     normalizeFeature(raw.clickFrequency, normalizationStats.clickFrequency.min, normalizationStats.clickFrequency.max),
//     normalizeFeature(raw.timeOnPage, normalizationStats.timeOnPage.min, normalizationStats.timeOnPage.max),
//     normalizeFeature(raw.scrollPattern, normalizationStats.scrollPattern.min, normalizationStats.scrollPattern.max),
//     normalizeFeature(raw.mouseAcceleration, normalizationStats.mouseAcceleration.min, normalizationStats.mouseAcceleration.max)
//   ];
  
//   return features;
// }

// async function evaluateSession(sessionData) {
//   if (!model) {
//     throw new Error('Model not initialized');
//   }
  
//   const features = extractFeatures(sessionData);
//   const inputTensor = tf.tensor2d([features]);
//   const prediction = model.predict(inputTensor);
//   const trustScore = prediction.dataSync()[0];
  
//   let action, message;
//   if (trustScore < 0.4) {
//     action = 'BLOCK';
//     message = 'Bot terdeteksi dengan tingkat kepercayaan tinggi';
//   } else if (trustScore < 0.7) {
//     action = 'CHALLENGE';
//     message = 'Verifikasi tambahan diperlukan';
//   } else {
//     action = 'ALLOW';
//     message = 'Perilaku tampak normal';
//   }
  
//   inputTensor.dispose();
//   prediction.dispose();
  
//   return { trustScore, action, message };
// }

// module.exports = {
//   initModel,
//   evaluateSession
// };
const path = require('path');
const fs = require('fs');

const MODEL_PATH = path.join(__dirname, 'model/model.json');
const STATS_PATH = path.join(__dirname, '../../../normalization-stats.json');

let model = null;
let normalizationStats = null;

const FEATURE_KEYS = [
    'mouseSpeed', 'mouseJitter', 'typingSpeed', 'typingRhythm',
    'clickFrequency', 'timeOnPage', 'scrollPattern', 'mouseAcceleration'
];

/**
 * Loads the trained model and normalization statistics from disk.
 * @returns {Promise<void>}
 */
async function initModel() {
  if (fs.existsSync(MODEL_PATH) && fs.existsSync(STATS_PATH)) {
    console.log('Loading model and normalization stats...');
    model = await tf.loadLayersModel(`file://${MODEL_PATH}`);
    normalizationStats = JSON.parse(fs.readFileSync(STATS_PATH, 'utf-8'));
    console.log('✅ Model and stats loaded successfully.');
  } else {
    console.warn('⚠️ No trained model or stats found. Server will run without prediction capabilities.');
    console.warn("Please run 'npm run train' to build and save the model.");
  }
}

/**
 * Checks if the model is loaded and ready for use.
 * @returns {boolean}
 */
function isModelReady() {
    return model !== null && normalizationStats !== null;
}

/**
* Extracts and normalizes features from raw session data.
* @param {Object} sessionData - Raw behavioral data.
* @returns {Array<number>} - Array of normalized features.
*/
function extractAndNormalizeFeatures(sessionData) {
    if (!normalizationStats) {
        throw new Error('Normalization stats are not available.');
    }
    
    return FEATURE_KEYS.map(key => {
        const value = sessionData[key] || 0;
        const stats = normalizationStats[key];
        if (!stats || stats.max === stats.min) {
            return 0;
        }
        return (value - stats.min) / (stats.max - stats.min);
    });
}

/**
 * Evaluates a session using the loaded model.
 * @param {Object} sessionData - Behavioral data from the session.
 * @returns {Promise<Object>} - Evaluation result with trust score and action.
 */
async function evaluateSession(sessionData) {
  if (!isModelReady()) {
    throw new Error('Model is not initialized and ready for evaluation.');
  }

  return tf.tidy(() => {
    const features = extractAndNormalizeFeatures(sessionData);
    const inputTensor = tf.tensor2d([features]);
    const prediction = model.predict(inputTensor);
    const trustScore = prediction.dataSync()[0];

    let action, message;
    if (trustScore < 0.4) {
      action = 'BLOCK';
      message = 'Bot terdeteksi dengan tingkat kepercayaan tinggi.';
    } else if (trustScore < 0.7) {
      action = 'CHALLENGE';
      message = 'Verifikasi tambahan diperlukan.';
    } else {
      action = 'ALLOW';
      message = 'Perilaku tampak normal.';
    }

    return { trustScore, action, message };
  });
}

module.exports = {
  initModel,
  evaluateSession,
  isModelReady,
  usesTensorFlow: true
};