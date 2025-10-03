const express = require('express');
const fs = require('fs');
const path = require('path');
const router = express.Router();

router.post('/save', (req, res) => {
  const { data, label } = req.body;
  const savePath = path.join(__dirname, '../../training-data.json');

  let dataset = [];
  if (fs.existsSync(savePath)) {
    dataset = JSON.parse(fs.readFileSync(savePath, 'utf-8'));
  }

  dataset.push({ data, label });
  fs.writeFileSync(savePath, JSON.stringify(dataset, null, 2));

  res.json({ success: true, count: dataset.length });
});

module.exports = router;
