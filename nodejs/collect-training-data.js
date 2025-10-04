const puppeteer = require('puppeteer');
const fs = require('fs');

async function collectBotData(numSamples = 50) {
  const browser = await puppeteer.launch({ headless: true });
  const trainingData = [];

  for (let i = 0; i < numSamples; i++) {
    const page = await browser.newPage();
    await page.goto('http://localhost:3000');
    await new Promise(resolve => setTimeout(resolve, 2000));
    // await page.waitForTimeout(2000);

    // Bot typing (super cepat & konsisten)
    await page.type('#name', 'Bot User', { delay: 10 });
    await page.type('#nik', '1234567890123456', { delay: 10 });

    // Mouse straight line
    await page.mouse.move(100, 100);
    await page.mouse.move(500, 500, { steps: 5 });

    await page.select('#origin', 'GMR');
    await page.select('#destination', 'BD');
    await new Promise(resolve => setTimeout(resolve, 3000));

    // Ambil data behavior
    const behaviorData = await page.evaluate(() => {
      // Access the collector instance from the page
      // In main.js, the collector is created but not assigned to window.behaviorCollector
      const collector = document.querySelector('body').__behaviorCollector;
      return collector ? collector._calculateMetrics() : null;
    });
    
    // If no data, try to inject script to expose collector
    if (!behaviorData) {
      await page.evaluate(() => {
        // Try to find the collector instance and expose it to window
        if (typeof BehaviorCollector !== 'undefined') {
          // Find the collector instance that was created in main.js
          const collectors = Object.values(document.querySelector('body'));
          const collector = collectors.find(item => item instanceof BehaviorCollector);
          if (collector) {
            window.behaviorCollector = collector;
            document.querySelector('body').__behaviorCollector = collector;
          }
        }
      });
      
      // Wait a bit for the collector to gather data
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      // Try again to get the data
      const retryData = await page.evaluate(() => {
        return window.behaviorCollector 
          ? window.behaviorCollector._calculateMetrics() 
          : null;
      });
      
      if (retryData) {
        trainingData.push({ data: retryData, label: 0 });
        console.log(`✅ Collected bot sample ${i + 1}/${numSamples}`);
        await page.close();
        continue;
      }
    }

    if (behaviorData) {
      trainingData.push({ data: behaviorData, label: 0 });
      console.log(`✅ Collected bot sample ${i + 1}/${numSamples}`);
    } else {
      console.warn(`⚠️ No data for sample ${i + 1}`);
    }

    await page.close();
  }

  await browser.close();
  return trainingData;
}

async function main() {
  console.log('Collecting bot training data...');
  const botData = await collectBotData(50);

  fs.writeFileSync('training-data-bot.json', JSON.stringify(botData, null, 2));
  console.log('Bot data saved to training-data-bot.json');
}

main();
