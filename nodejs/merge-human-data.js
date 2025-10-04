const fs = require('fs');

// Baca file data perilaku manusia yang sudah ada
const existingHumanData = JSON.parse(fs.readFileSync('training-data-human.json', 'utf8'));

// Baca file data perilaku manusia tambahan
const additionalHumanData = JSON.parse(fs.readFileSync('additional-human-data.json', 'utf8'));

// Gabungkan kedua data
const mergedData = [...existingHumanData, ...additionalHumanData];

// Simpan hasil penggabungan ke file baru
fs.writeFileSync('merged-human-data.json', JSON.stringify(mergedData, null, 2));

console.log(`Data berhasil digabungkan!`);
console.log(`Total data: ${mergedData.length} sampel`);
console.log(`- Data asli: ${existingHumanData.length} sampel`);
console.log(`- Data tambahan: ${additionalHumanData.length} sampel`);