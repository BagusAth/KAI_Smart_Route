// /**
//  * KAI Guard - Behavior Analysis Controller
//  * 
//  * This controller handles incoming behavior data from the client,
//  * processes it through the ML model, and returns appropriate actions.
//  */

// // Import model service with fallback
// let modelService;
// try {
//   modelService = require('../services/ml/modelService');
// } catch (error) {
//   console.log('Using fallback model service');
//   modelService = require('../services/ml/modelServiceFallback');
// }

// /**
//  * Process and analyze behavioral data
//  * @param {Object} req - Express request object
//  * @param {Object} res - Express response object
//  */
// async function analyzeBehavior(req, res) {
//   try {
//     const behavioralData = req.body;
    
//     // Log incoming data if in development mode
//     if (process.env.NODE_ENV === 'development') {
//       console.log('Received behavioral data:', JSON.stringify(behavioralData, null, 2));
//     }
    
//     // Validate the request
//     if (!behavioralData || !behavioralData.sessionId) {
//       return res.status(400).json({
//         success: false,
//         message: 'Invalid request data',
//         action: 'CHALLENGE' // Default to challenge on invalid data
//       });
//     }
    
//     // Process the data through the ML model
//     const result = await modelService.evaluateSession(behavioralData);
    
//     // Log the result if in development mode
//     if (process.env.NODE_ENV === 'development') {
//       console.log(`Session ${behavioralData.sessionId} evaluated with trust score: ${result.trustScore}`);
//       console.log(`Action: ${result.action}, Message: ${result.message}`);
//     }
    
//     // Return the result to the client
//     return res.status(200).json({
//       success: true,
//       sessionId: behavioralData.sessionId,
//       trustScore: result.trustScore,
//       action: result.action,
//       message: result.message
//     });
//   } catch (error) {
//     console.error('Error analyzing behavior:', error);
    
//     // Return a generic error to the client
//     return res.status(500).json({
//       success: false,
//       message: 'An error occurred while analyzing behavior',
//       action: 'CHALLENGE' // Default to challenge on error
//     });
//   }
// }

// /**
//  * Train the ML model with labeled data
//  * This endpoint would typically be protected and only accessible by admins
//  * @param {Object} req - Express request object
//  * @param {Object} res - Express response object
//  */
// async function trainModel(req, res) {
//   try {
//     const { trainingData, epochs } = req.body;
    
//     // Validate the request
//     if (!trainingData || !Array.isArray(trainingData) || trainingData.length === 0) {
//       return res.status(400).json({
//         success: false,
//         message: 'Invalid training data'
//       });
//     }
    
//     // Train the model
//     const result = await modelService.trainModel(trainingData, epochs || 10);
    
//     // Return success response
//     return res.status(200).json({
//       success: true,
//       message: 'Model trained successfully',
//       details: {
//         epochs: epochs || 10,
//         samplesProcessed: trainingData.length,
//         finalLoss: result.history.loss[result.history.loss.length - 1]
//       }
//     });
//   } catch (error) {
//     console.error('Error training model:', error);
    
//     // Return error response
//     return res.status(500).json({
//       success: false,
//       message: 'An error occurred while training the model'
//     });
//   }
// }

// module.exports = {
//   analyzeBehavior,
//   trainModel
// };

/**
 * KAI Guard - Behavior Controller
 * Handles incoming requests for behavior analysis and model training.
 */
const modelService = require('../services/ml/modelServiceNonTF');

/**
 * Analyze user behavior data and return a trust score.
 * @param {Object} req - Express request object
 * @param {Object} res - Express response object
 */
async function analyzeBehavior(req, res) {
  try {
    const sessionData = req.body;
    
    // Check if the model is initialized
    if (!modelService.isModelReady()) {
      return res.status(503).json({ 
        message: "Model is not ready, please try again later." 
      });
    }
    
    // Evaluate the session data
    const result = await modelService.evaluateSession(sessionData);
    
    res.json(result);
  } catch (error) {
    console.error('Error in analyzeBehavior:', error);
    res.status(500).json({
      trustScore: 0.5,
      action: 'CHALLENGE',
      message: 'An error occurred during behavior analysis.'
    });
  }
}

/**
 * Placeholder for triggering model training (actual training is done via script).
 * @param {Object} req - Express request object
 * @param {Object} res - Express response object
 */
async function trainModel(req, res) {
  // In a real-world scenario, this might trigger a background training job.
  // For this project, training is handled by a dedicated script.
  res.status(202).json({ 
    message: "Model training process is initiated via the 'npm run train' script." 
  });
}

module.exports = {
  analyzeBehavior,
  trainModel
};