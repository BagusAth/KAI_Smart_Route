/**
 * KAI Guard - Model Service Fallback
 * 
 * This is a fallback implementation of the model service that doesn't require
 * the TensorFlow.js Node.js package. It provides basic functionality to allow
 * the server to run without the native TensorFlow.js dependency.
 */

/**
 * Initialize the ML model
 * This is a fallback implementation that doesn't actually load a model
 * @returns {Promise} - Promise that resolves when initialization is complete
 */
async function initModel() {
  console.log('Using fallback model service (no TensorFlow.js Node)');
  return true;
}

/**
 * Evaluate a session using basic rules
 * @param {Object} sessionData - Behavioral data from the session
 * @returns {Object} - Evaluation result with trust score and recommended action
 */
async function evaluateSession(sessionData) {
  try {
    // In the fallback implementation, we'll use some basic heuristics
    // to determine a trust score instead of using a ML model
    
    // Extract some basic metrics from the session data
    const typingSpeed = sessionData.typingSpeed || 0;
    const mouseSpeed = sessionData.mouseSpeed || 0;
    const clickFrequency = sessionData.clickFrequency || 0;
    const timeOnPage = sessionData.timeOnPage || 0;
    
    // Calculate a simple trust score based on these metrics
    // This is a very simplified version of what the ML model would do
    let trustScore = 0.5; // Start with a neutral score
    
    // Adjust based on typing speed (too fast or too slow might be suspicious)
    if (typingSpeed > 0 && typingSpeed < 10) {
      trustScore += 0.1; // Reasonable typing speed
    } else if (typingSpeed > 20) {
      trustScore -= 0.1; // Too fast, might be automated
    }
    
    // Adjust based on mouse speed
    if (mouseSpeed > 0 && mouseSpeed < 5) {
      trustScore += 0.1; // Reasonable mouse movement
    } else if (mouseSpeed > 10) {
      trustScore -= 0.1; // Too fast, might be automated
    }
    
    // Adjust based on click frequency
    if (clickFrequency > 0 && clickFrequency < 1) {
      trustScore += 0.1; // Reasonable click rate
    } else if (clickFrequency > 2) {
      trustScore -= 0.1; // Too many clicks, might be automated
    }
    
    // Adjust based on time on page
    if (timeOnPage > 10000) { // More than 10 seconds
      trustScore += 0.1; // Spent reasonable time on page
    }
    
    // Ensure score is between 0 and 1
    trustScore = Math.max(0, Math.min(1, trustScore));
    
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
    console.error('Error evaluating session with fallback service:', error);
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
 * This is a stub implementation that doesn't actually train a model
 * @returns {Object} - Mock training history
 */
async function trainModel() {
  console.log('Training not available in fallback model service');
  return {
    success: false,
    message: 'Training not available in fallback mode'
  };
}

module.exports = {
  initModel,
  evaluateSession,
  trainModel
};