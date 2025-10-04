// /**
//  * KAI Guard - Main Server
//  * 
//  * This is the main entry point for the KAI Guard server application.
//  * It sets up the Express server, middleware, and routes.
//  */

// const express = require('express');
// const cors = require('cors');
// const morgan = require('morgan');
// const path = require('path');
// const dotenv = require('dotenv');


// // Load environment variables from .env file
// dotenv.config();

// // Import routes
// const behaviorRoutes = require('./routes/behaviorRoutes');
// const trainingRoutes = require('./routes/trainingRoutes');

// // Import ML model service (with fallback if not available)
// let modelService;
// try {
//   modelService = require('./services/ml/modelService');
// } catch (error) {
//   console.log('ML model service not available, using client-side model only');
//   modelService = {
//     initModel: () => Promise.resolve(true)
//   };
// }

// // Create Express app
// const app = express();
// const PORT = process.env.PORT || 3000;

// // Middleware
// app.use(cors());
// app.use(express.json());
// app.use(express.urlencoded({ extended: true }));


// // Logging middleware
// if (process.env.NODE_ENV === 'development') {
//   app.use(morgan('dev'));
// }

// // Serve static files from the client directory
// app.use(express.static(path.join(__dirname, '../client')));

// // API routes
// app.use('/api/behavior', behaviorRoutes);
// app.use('/api/training', trainingRoutes);


// // Serve the main HTML file for any other route
// app.get('*', (req, res) => {
//   res.sendFile(path.join(__dirname, '../client/index.html'));
// });

// // Initialize the ML model before starting the server
// modelService.initModel()
//   .then(() => {
//     // Start the server
//     app.listen(PORT, () => {
//       console.log(`KAI Guard server running on port ${PORT}`);
//       console.log(`Environment: ${process.env.NODE_ENV || 'development'}`);
//     });
//   })
//   .catch(error => {
//     console.error('Failed to initialize ML model:', error);
//     process.exit(1);
//   });

// module.exports = app; // Export for testing

const express = require('express');
const cors = require('cors');
const morgan = require('morgan');
const path = require('path');
const dotenv = require('dotenv');

dotenv.config();

// Import routes
const behaviorRoutes = require('./routes/behaviorRoutes');

// Import non-TensorFlow model service
const modelService = require('./services/ml/modelServiceNonTF');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

if (process.env.NODE_ENV === 'development') {
  app.use(morgan('dev'));
}

// Serve static files from the client directory
const clientPath = path.join(__dirname, '../client');
app.use(express.static(clientPath));

// Serve normalization stats needed by the client
app.use('/normalization-stats.json', express.static(path.join(__dirname, '../normalization-stats.json')));

// API routes
app.use('/api/behavior', behaviorRoutes);

// Serve the main HTML file for any other route
app.get('*', (req, res) => {
  res.sendFile(path.join(clientPath, 'index.html'));
});

// Initialize the ML model before starting the server
modelService.initModel()
  .then(() => {
    app.listen(PORT, () => {
      console.log(`✅ KAI Guard server running on http://localhost:${PORT}`);
    });
  })
  .catch(error => {
    console.error('❌ Failed to initialize ML model:', error);
    process.exit(1);
  });

module.exports = app;