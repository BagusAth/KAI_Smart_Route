const puppeteer = require('puppeteer');
const fs = require('fs');

async function collectHumanData(numSamples = 50) {
  const browser = await puppeteer.launch({ headless: false }); // Tampilkan browser
  const trainingData = [];

  for (let i = 0; i < numSamples; i++) {
    const page = await browser.newPage();
    await page.goto('http://localhost:3000');
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Simulasi perilaku manusia
    // Mouse movement yang natural (curved, varied speed)
    for (let j = 0; j < 5; j++) {
      const x = Math.random() * 800 + 100;
      const y = Math.random() * 600 + 100;
      await page.mouse.move(x, y, { steps: 10 + Math.random() * 20 });
      await new Promise(resolve => setTimeout(resolve, 500 + Math.random() * 1000));
    }

    // Typing dengan kecepatan manusia (varied delays)
    await page.click('#name');
    const name = 'Human User ' + i;
    for (const char of name) {
      await page.keyboard.type(char);
      await new Promise(resolve => setTimeout(resolve, 50 + Math.random() * 150));
    }

    await page.click('#nik');
    const nik = '3201234567890123';
    for (const char of nik) {
      await page.keyboard.type(char);
      await new Promise(resolve => setTimeout(resolve, 40 + Math.random() * 120));
    }

    // Natural pauses untuk "membaca"
    await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));

    await page.select('#origin', 'GMR');
    await new Promise(resolve => setTimeout(resolve, 500 + Math.random() * 1000));
    
    await page.select('#destination', 'BD');
    await new Promise(resolve => setTimeout(resolve, 3000 + Math.random() * 2000));

    // Ambil data behavior
    const behaviorData = await page.evaluate(() => {
      return window.behaviorCollector 
        ? window.behaviorCollector._calculateMetrics() 
        : null;
    });

    if (behaviorData) {
      trainingData.push({ data: behaviorData, label: 1 }); // Label 1 untuk human
      console.log(`✅ Collected human sample ${i + 1}/${numSamples}`);
    }

    await page.close();
  }

  await browser.close();
  return trainingData;
}

async function main() {
  console.log('Collecting human training data...');
  const humanData = await collectHumanData(50);

  fs.writeFileSync('training-data-human.json', JSON.stringify(humanData, null, 2));
  console.log('Human data saved to training-data-human.json');
}

main();