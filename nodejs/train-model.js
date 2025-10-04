// const fs = require('fs');
// const path = require('path');
// const modelService = require('./server/services/ml/modelServiceNonTF');

// /**
//  * KAI Guard - Model Training Script (Non-TensorFlow Version)
//  * 
//  * This script trains a logistic regression model using pure JavaScript
//  * without requiring TensorFlow.js. It loads training data, processes it,
//  * and trains the model using the modelServiceNonTF implementation.
//  */

// /**
//  * Load training data from bot and human JSON files
//  * @returns {Array} Combined training data
//  */
// function loadTrainingData() {
//   console.log('Loading training data...');
//   let botData = [];
//   let humanData = [];
  
//   try {
//     if (fs.existsSync('training-data-bot.json')) {
//       botData = JSON.parse(fs.readFileSync('training-data-bot.json'));
//       console.log(`Loaded ${botData.length} bot samples`);
//     } else {
//       console.warn('Bot training data file not found');
//     }
    
//     if (fs.existsSync('training-data-human.json')) {
//       humanData = JSON.parse(fs.readFileSync('training-data-human.json'));
//       console.log(`Loaded ${humanData.length} human samples`);
//     } else {
//       console.warn('Human training data file not found');
//     }
//   } catch (error) {
//     console.error('Error loading training data:', error);
//   }
  
//   return [...botData, ...humanData];
// }

// /**
//  * Main training function
//  */
// async function trainModel() {
//   console.log('Starting non-TensorFlow model training...');
//   const rawData = loadTrainingData();
  
//   if (rawData.length === 0) {
//     console.error('No training data available. Please collect training data first.');
//     return;
//   }
  
//   console.log(`Total samples: ${rawData.length}`);
//   console.log(`Bot samples: ${rawData.filter(d => d.label === 0).length}`);
//   console.log(`Human samples: ${rawData.filter(d => d.label === 1).length}`);
  
//   try {
//     // Train the model using our non-TensorFlow implementation
//     const result = await modelService.trainModel(rawData, 50);
    
//     if (result.success) {
//       console.log('✅ Model training completed successfully!');
//       console.log(`Trained with ${result.samples} samples over ${result.epochs} epochs`);
//     } else {
//       console.error('❌ Model training failed:', result.message);
//     }
//   } catch (error) {
//     console.error('Error during model training:', error);
//   }
// }

// // Run the training
// trainModel().catch(error => {
//   console.error('Unhandled error during training:', error);
//   process.exit(1);
// });

const fs = require('fs');
const path = require('path');
const modelService = require('./server/services/ml/modelServiceNonTF');

/**
 * Mapping dari nama fitur JSON ke nama fitur model
 */
const FEATURE_MAPPING = {
  mouseSpeed: 'mouseMovementSpeed',
  mouseJitter: 'mouseStraightness',
  typingSpeed: 'typingSpeed',
  typingRhythm: 'typingConsistency',
  clickFrequency: 'pauseDurations',       // akan dihitung rata-rata
  timeOnPage: 'formCompletionTime',
  scrollPattern: null,                     // belum ada → default 0
  mouseAcceleration: null                  // belum ada → default 0
};

const FEATURE_NAMES = Object.keys(FEATURE_MAPPING);

/**
 * Load training data from bot and human JSON files
 */
function loadTrainingData() {
  console.log('Loading training data...');
  let botData = [];
  let humanData = [];

  try {
    if (fs.existsSync('training-data-bot.json')) {
      botData = JSON.parse(fs.readFileSync('training-data-bot.json'));
      console.log(`Loaded ${botData.length} bot samples`);
    } else {
      console.warn('Bot training data file not found');
    }

    if (fs.existsSync('training-data-human.json')) {
      humanData = JSON.parse(fs.readFileSync('training-data-human.json'));
      console.log(`Loaded ${humanData.length} human samples`);
    } else {
      console.warn('Human training data file not found');
    }
  } catch (error) {
    console.error('Error loading training data:', error);
  }

  return [...botData, ...humanData];
}

/**
 * Flatten dan mapping fitur agar sesuai model
 */
// function prepareData(rawData) {
//   return rawData.map(sample => {
//     const dat = sample.data || {};
//     const mapped = {};

//     FEATURE_NAMES.forEach(f => {
//       const source = FEATURE_MAPPING[f];

//       if (!source) {
//         mapped[f] = 0; // default untuk fitur kosong
//       } else if (source === 'pauseDurations' && Array.isArray(dat[source])) {
//         // hitung rata-rata klik dari pauseDurations
//         const arr = dat[source];
//         mapped[f] = arr.reduce((sum, val) => sum + val, 0) / arr.length;
//       } else {
//         mapped[f] = dat[source] ?? 0;
//       }
//     });

//     mapped.label = sample.label ?? 0;
//     return mapped;
//   });
// }

function prepareData(rawData) {
  return rawData.map(sample => {
    const dat = sample.data || {};
    const mapped = {};

    FEATURE_NAMES.forEach(f => {
      const source = FEATURE_MAPPING[f];
      if (!source) {
        mapped[f] = 0;
      } else if (source === 'pauseDurations' && Array.isArray(dat[source])) {
        mapped[f] = dat[source].reduce((sum, val) => sum + val, 0) / dat[source].length;
      } else {
        mapped[f] = dat[source] ?? 0;
      }
    });

    return { data: mapped, label: sample.label ?? 0 }; // tetap di-wrap di .data
  });
}

/**
 * Main training function
 */
async function trainModel() {
  console.log('Starting non-TensorFlow model training...');
  const rawData = loadTrainingData();

  if (rawData.length === 0) {
    console.error('No training data available. Please collect training data first.');
    return;
  }

  const preparedData = prepareData(rawData);

  console.log(`Total samples: ${preparedData.length}`);
  console.log(`Bot samples: ${preparedData.filter(d => d.label === 0).length}`);
  console.log(`Human samples: ${preparedData.filter(d => d.label === 1).length}`);

  // Debug normalization stats
  const stats = {};
  FEATURE_NAMES.forEach(f => {
    const values = preparedData.map(d => d[f]);
    stats[f] = {
      min: Math.min(...values),
      max: Math.max(...values)
    };
  });
  console.log('Normalization stats:', JSON.stringify(stats, null, 2));

  try {
    // Train the model using our non-TensorFlow implementation
    const result = await modelService.trainModel(preparedData, 50);

    if (result.success) {
      console.log('✅ Model training completed successfully!');
      console.log(`Trained with ${result.samples} samples over ${result.epochs} epochs`);
    } else {
      console.error('❌ Model training failed:', result.message);
    }
  } catch (error) {
    console.error('Error during model training:', error);
  }
}

// Run the training
trainModel().catch(error => {
  console.error('Unhandled error during training:', error);
  process.exit(1);
});
