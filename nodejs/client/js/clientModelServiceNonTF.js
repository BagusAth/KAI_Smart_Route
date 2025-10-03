/**
 * KAI Guard - Client-Side Non-TensorFlow Model Service
 * 
 * This is a lightweight implementation of the client-side model service that doesn't require
 * TensorFlow.js. It provides basic machine learning functionality using pure JavaScript.
 */

// clientModelServiceNonTF.js

class ClientModelServiceNonTF {
    constructor(modelPath = './model-non-tf.json', statsPath = './normalization-stats.json') {
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
            console.log('Loading client-side non-TensorFlow model...');
            
            // Load model
            const modelResponse = await fetch(this.modelPath);
            if (!modelResponse.ok) {
                throw new Error(`Failed to load model: ${modelResponse.statusText}`);
            }
            const modelData = await modelResponse.json();
            this.model = new LogisticRegressionModel();
            this.model.fromJSON(modelData);
            console.log('Client-side model loaded successfully.');
            
            // Load normalization stats
            const statsResponse = await fetch(this.statsPath);
            if (!statsResponse.ok) {
                throw new Error(`Failed to load normalization stats: ${statsResponse.statusText}`);
            }
            this.normalizationStats = await statsResponse.json();
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

        // 1. Extract and normalize features
        const features = this._extractAndNormalize(behaviorData);
        
        // 2. Make a prediction
        const trustScore = this.model.predict(features);
        
        // 3. Return the result
        return trustScore;
    }

    /**
     * Evaluates behavioral data and determines appropriate action.
     * @param {Object} behaviorData - The raw behavioral metrics.
     * @returns {Promise<Object>} - Evaluation result with trust score and recommended action.
     */
    async evaluateData(behaviorData) {
        try {
            if (!this.isReady()) {
                throw new Error('Model not ready');
            }

            // Get trust score
            const trustScore = await this.evaluate(behaviorData);
            
            // Determine action based on trust score with more tolerant thresholds
            let action, message;
            if (trustScore < 0.15) {
                action = 'BLOCK';
                message = 'Suspicious activity detected';
            } else if (trustScore < 0.4) {
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
            console.error('Error evaluating data:', error);
            // Default to challenge on error
            return {
                trustScore: 0.5,
                action: 'CHALLENGE',
                message: 'Error evaluating session'
            };
        }
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
            return 0.5; // Default to neutral value if stats are missing
        }
        // Normalize with a floor value to be more tolerant of human input
        let normalized = (value - stats.min) / (stats.max - stats.min);
        // Apply a more lenient floor value to prevent extremely low scores
        return Math.max(0.2, normalized);
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

/**
 * Simple logistic regression model implementation for client-side
 */
class LogisticRegressionModel {
    constructor(weights = null, bias = null) {
        this.weights = weights || [];
        this.bias = bias || 0;
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
     * Load model from JSON
     * @param {Object} json - Model parameters
     */
    fromJSON(json) {
        this.weights = json.weights;
        this.bias = json.bias;
    }
}

// Export for use in browser
window.ClientModelServiceNonTF = ClientModelServiceNonTF;