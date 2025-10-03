// /**
//  * KAI Guard - Behavior Analysis Routes
//  * 
//  * This file defines the API routes for the behavior analysis system.
//  */

// const express = require('express');
// const router = express.Router();
// const behaviorController = require('../controllers/behaviorController');

// // Route for analyzing user behavior
// router.post('/analyze', behaviorController.analyzeBehavior);

// // Route for training the ML model (protected route)
// // This would typically be protected with authentication middleware
// router.post('/train', behaviorController.trainModel);

// module.exports = router;


// behaviorRoutes.js

const express = require('express');
const router = express.Router();
const fs = require('fs');
const path = require('path');
const { evaluateSession, isModelReady, initModel } = require('../services/ml/modelServiceNonTF');

const BEHAVIOR_DATA_PATH = path.join(__dirname, '../data/behavior-data.json');

// Pastikan folder data ada
if (!fs.existsSync(path.dirname(BEHAVIOR_DATA_PATH))) {
    fs.mkdirSync(path.dirname(BEHAVIOR_DATA_PATH), { recursive: true });
}

// Middleware untuk inisialisasi model
async function ensureModel(req, res, next) {
    try {
        if (!isModelReady()) {
            await initModel();
        }
        next();
    } catch (error) {
        console.error('Error initializing model:', error);
        res.status(500).json({ success: false, message: 'Model initialization failed' });
    }
}

// POST /api/behavior/analyze
router.post('/analyze', ensureModel, async (req, res) => {
    try {
        const sessionData = req.body;
        if (!sessionData) return res.status(400).json({ success: false, message: 'No session data provided' });

        // Analisis session
        const analysis = await evaluateSession(sessionData);

        // Tambahkan timestamp
        const record = {
            timestamp: new Date().toISOString(),
            sessionData,
            analysis
        };

        // Load data lama
        let data = [];
        if (fs.existsSync(BEHAVIOR_DATA_PATH)) {
            try {
                data = JSON.parse(fs.readFileSync(BEHAVIOR_DATA_PATH, 'utf-8'));
            } catch {
                console.warn('Failed to parse existing JSON, overwrite.');
            }
        }

        data.push(record);

        // Simpan kembali
        fs.writeFileSync(BEHAVIOR_DATA_PATH, JSON.stringify(data, null, 2));

        res.json({ success: true, analysis });

    } catch (error) {
        console.error('Error analyzing behavior:', error);
        res.status(500).json({ success: false, message: 'Analysis failed', error: error.message });
    }
});

module.exports = router;
