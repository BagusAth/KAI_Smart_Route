/**
 * KAI Guard - Non-TensorFlow Model Service
 * 
 * This is a lightweight implementation of the model service that doesn't require
 * TensorFlow.js. It provides basic machine learning functionality using pure JavaScript.
 * It implements a simple logistic regression algorithm for binary classification.
 */

const fs = require('fs');
const path = require('path');

// Paths for model and stats
const MODEL_PATH = path.join(__dirname, 'model-non-tf.json');
const STATS_PATH = path.join(__dirname, '../../../normalization-stats.json');
const CLIENT_MODEL_PATH = path.join(__dirname, '../../../client/model-non-tf.json');


// Feature keys used in the model
const FEATURE_KEYS = [
    'mouseSpeed', 'mouseJitter', 'typingSpeed', 'typingRhythm',
    'clickFrequency', 'timeOnPage', 'scrollPattern', 'mouseAcceleration'
];

// Model state
let model = null;
let normalizationStats = null;
let isModelInitialized = false;

/**
 * Simple logistic regression model implementation
 */
class LogisticRegressionModel {
    constructor(weights = null, bias = null) {
        // Initialize with random weights if not provided
        this.weights = weights || Array(FEATURE_KEYS.length).fill().map(() => Math.random() * 0.1 - 0.05);
        this.bias = bias || Math.random() * 0.1 - 0.05;
        this.learningRate = 0.01;
    }

    /**
     * Sigmoid activation function
     * @param {number} z - Input value
     * @returns {number} - Output between 0 and 1
     */
    sigmoid(z) {
        return 1 / (1 + Math.exp(-z));
    }

    /**
     * Make a prediction for a single sample
     * @param {Array<number>} features - Normalized feature array
     * @returns {number} - Prediction between 0 and 1
     */
    predict(features) {
        let z = this.bias;
        for (let i = 0; i < this.weights.length; i++) {
            z += features[i] * this.weights[i];
        }
        return this.sigmoid(z);
    }

    /**
     * Train the model on a batch of data
     * @param {Array<Array<number>>} features - Array of feature arrays
     * @param {Array<number>} labels - Array of labels (0 or 1)
     * @param {number} epochs - Number of training epochs
     */
    train(features, labels, epochs = 50) {
        const m = features.length;
        
        for (let epoch = 0; epoch < epochs; epoch++) {
            let totalLoss = 0;
            
            // Shuffle the data
            const indices = Array.from(Array(m).keys());
            for (let i = indices.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [indices[i], indices[j]] = [indices[j], indices[i]];
            }
            
            // Train on each sample
            for (let idx of indices) {
                const x = features[idx];
                const y = labels[idx];
                
                // Forward pass
                const prediction = this.predict(x);
                
                // Compute loss
                const loss = -y * Math.log(prediction) - (1 - y) * Math.log(1 - prediction);
                totalLoss += loss;
                
                // Compute gradient
                const error = prediction - y;
                
                // Update weights and bias
                for (let i = 0; i < this.weights.length; i++) {
                    this.weights[i] -= this.learningRate * error * x[i];
                }
                this.bias -= this.learningRate * error;
            }
            
            // Log progress every 10 epochs
            if (epoch % 10 === 0) {
                console.log(`Epoch ${epoch}, Loss: ${totalLoss / m}`);
            }
        }
    }

    /**
     * Serialize the model to JSON
     * @returns {Object} - Model parameters
     */
    toJSON() {
        return {
            weights: this.weights,
            bias: this.bias
        };
    }

    /**
     * Load model from JSON
     * @param {Object} json - Model parameters
     */
    fromJSON(json) {
        this.weights = json.weights;
        this.bias = json.bias;
    }
}

/**
 * Initialize the ML model
 * @returns {Promise<boolean>} - Promise that resolves when initialization is complete
 */
async function initModel() {
    try {
        console.log('Initializing non-TensorFlow model service...');
        
        // Load normalization stats
        if (fs.existsSync(STATS_PATH)) {
            normalizationStats = JSON.parse(fs.readFileSync(STATS_PATH, 'utf-8'));
            console.log('Normalization stats loaded successfully.');
        } else {
            console.warn('Normalization stats not found. Using default values.');
            // Create default stats if not available
            normalizationStats = {};
            FEATURE_KEYS.forEach(key => {
                normalizationStats[key] = { min: 0, max: 1 };
            });
        }
        
        // Load model if it exists
        if (fs.existsSync(MODEL_PATH)) {
            const modelData = JSON.parse(fs.readFileSync(MODEL_PATH, 'utf-8'));
            model = new LogisticRegressionModel();
            model.fromJSON(modelData);
            console.log('Model loaded successfully.');
        } else {
            console.warn('Model not found. Creating a new one.');
            model = new LogisticRegressionModel();
        }
        
        isModelInitialized = true;
        return true;
    } catch (error) {
        console.error('Error initializing model:', error);
        return false;
    }
}

/**
 * Check if the model is ready for predictions
 * @returns {boolean} - Whether the model is ready
 */
function isModelReady() {
    return isModelInitialized && model !== null && normalizationStats !== null;
}

/**
 * Normalize a feature value
 * @param {number} value - Raw feature value
 * @param {string} key - Feature name
 * @returns {number} - Normalized value between 0 and 1
 */
function normalizeFeature(value, key) {
    if (!normalizationStats || !normalizationStats[key]) {
        return value;
    }
    
    const { min, max } = normalizationStats[key];
    if (max === min) return 0.5;
    return (value - min) / (max - min);
}

/**
 * Extract and normalize features from session data
 * @param {Object} sessionData - Raw behavioral data
 * @returns {Array<number>} - Normalized feature array
 */
function extractFeatures(sessionData) {
    return FEATURE_KEYS.map(key => {
        const value = sessionData[key] || 0;
        return normalizeFeature(value, key);
    });
}

/**
 * Evaluate a session using the trained model
 * @param {Object} sessionData - Behavioral data from the session
 * @returns {Object} - Evaluation result with trust score and recommended action
 */
async function evaluateSession(sessionData) {
    try {
        if (!isModelReady()) {
            await initModel();
        }
        
        // Extract and normalize features
        const features = extractFeatures(sessionData);
        
        // Get prediction from model
        const trustScore = model.predict(features);
        
        // Determine action based on trust score
        let action, message;
        if (trustScore < 0.3) {
            action = 'BLOCK';
            message = 'Suspicious activity detected';
        } else if (trustScore < 0.7) {
            action = 'CHALLENGE';
            message = 'Additional verification required';
        } else {
            action = 'ALLOW';
            message = 'Session appears legitimate';
        }
        
        return {
            trustScore,
            action,
            message
        };
    } catch (error) {
        console.error('Error evaluating session:', error);
        // Default to challenge on error
        return {
            trustScore: 0.5,
            action: 'CHALLENGE',
            message: 'Error evaluating session'
        };
    }
}

/**
 * Train the model with labeled data
 * @param {Array} trainingData - Array of labeled data points
 * @param {Number} epochs - Number of training epochs
 * @returns {Object} - Training result
 */
async function trainModel(trainingData, epochs = 50) {
    try {
        console.log('Training non-TensorFlow model...');
        console.log(`Total samples: ${trainingData.length}`);
        
        // Extract features and calculate normalization stats
        const stats = {};
        FEATURE_KEYS.forEach(key => {
            stats[key] = { min: Infinity, max: -Infinity };
        });
        
        // Find min/max values for normalization
        trainingData.forEach(item => {
            FEATURE_KEYS.forEach(key => {
                const value = item.data[key] || 0;
                if (value < stats[key].min) stats[key].min = value;
                if (value > stats[key].max) stats[key].max = value;
            });
        });
        
        // Save normalization stats
        normalizationStats = stats;
        fs.writeFileSync(STATS_PATH, JSON.stringify(stats, null, 2));
        console.log('Normalization stats saved.');
        
        // Extract normalized features and labels
        const features = [];
        const labels = [];
        
        trainingData.forEach(item => {
            const normalizedFeatures = FEATURE_KEYS.map(key => {
                const value = item.data[key] || 0;
                return normalizeFeature(value, key);
            });
            
            features.push(normalizedFeatures);
            labels.push(item.label);
        });
        
        // Create and train the model
        model = new LogisticRegressionModel();
        model.train(features, labels, epochs);
        
        // Save the model
        const modelJSON = model.toJSON();
        fs.writeFileSync(MODEL_PATH, JSON.stringify(modelJSON, null, 2));
        console.log('Model saved successfully.');
        
        // Save client-side model
        if (!fs.existsSync(path.dirname(CLIENT_MODEL_PATH))) {
            fs.mkdirSync(path.dirname(CLIENT_MODEL_PATH), { recursive: true });
        }
        fs.writeFileSync(CLIENT_MODEL_PATH, JSON.stringify(modelJSON, null, 2));
        console.log('Client-side model saved successfully.');
        
        isModelInitialized = true;
        
        return {
            success: true,
            message: 'Model trained successfully',
            samples: trainingData.length,
            epochs
        };
    } catch (error) {
        console.error('Error training model:', error);
        return {
            success: false,
            message: 'Error training model: ' + error.message
        };
    }
}

module.exports = {
    initModel,
    evaluateSession,
    trainModel,
    isModelReady
};