# KAI Guard - Sistem Cerdas Anti-Calo Berbasis Analisis Perilaku

## Overview

KAI Guard adalah sistem cerdas yang menggunakan teknologi analisis perilaku untuk membedakan pengguna manusia dari bot calo. Sistem ini menganalisis pola interaksi pengguna dengan halaman web secara real-time untuk memastikan proses pembelian tiket kereta api yang adil.

## Masalah

Setiap kali PT. KAI membuka penjualan tiket untuk periode puncak (contoh: Lebaran, Natal & Tahun Baru), tiket di rute-rute populer ludes hanya dalam hitungan menit. Sebagian besar masalah ini bukan disebabkan oleh tingginya animo masyarakat saja, tetapi oleh serangan bot otomatis yang dijalankan oleh calo profesional.

Hal ini menyebabkan:
- Pengguna asli (manusia) kalah cepat dengan bot
- Konsumen terpaksa membeli tiket dari calo dengan harga yang jauh lebih mahal
- Citra PT. KAI menjadi negatif
- Beban infrastruktur server yang tinggi

## Solusi

KAI Guard menggunakan pendekatan analisis perilaku (behavioral analysis) untuk membedakan pengguna manusia dari bot. Sistem ini terdiri dari tiga komponen utama:

1. **Data Collector (Frontend)**: Mengumpulkan data perilaku pengguna seperti gerakan mouse, pola pengetikan, interaksi halaman, dan informasi perangkat.

2. **Processing Engine & AI Model (Backend)**: Menganalisis data perilaku dan memberikan skor kepercayaan (trust score) untuk setiap sesi pengguna.

3. **Dashboard Monitoring**: Memvisualisasikan serangan yang berhasil diblokir dan statistik lainnya.

## Teknologi

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: Node.js, Express
- **Machine Learning**: TensorFlow.js
- **Database**: (Tidak diimplementasikan dalam demo ini)

## Fitur

- Analisis gerakan mouse (kecepatan, akselerasi, jitter)
- Analisis pola pengetikan (kecepatan, ritme)
- Analisis interaksi halaman (pola scrolling, waktu di halaman)
- Device fingerprinting (resolusi layar, browser, timezone)
- Sistem skor kepercayaan (trust score) untuk membedakan manusia dari bot
- Tantangan adaptif berdasarkan skor kepercayaan

## Cara Menjalankan

### Prasyarat

- Node.js (versi 14 atau lebih tinggi)
- NPM (versi 6 atau lebih tinggi)

### Instalasi

1. Clone repositori ini

```bash
git clone https://github.com/username/kai-guard.git
cd kai-guard
```

2. Install dependensi

```bash
npm install
```

3. Jalankan aplikasi

```bash
npm start
```

4. Buka browser dan akses `http://localhost:3000`

## Demo

Demo aplikasi ini menampilkan simulasi pembelian tiket kereta api dengan sistem anti-bot KAI Guard. Pengguna dapat mengisi form pembelian tiket dan sistem akan menganalisis perilaku pengguna secara real-time untuk menentukan apakah pengguna adalah manusia atau bot.

## Kontribusi

Kontribusi untuk proyek ini sangat diterima. Silakan fork repositori ini dan buat pull request untuk mengusulkan perubahan.

## Lisensi

Proyek ini dilisensikan di bawah lisensi MIT - lihat file LICENSE untuk detail lebih lanjut.

## Tim Pengembang

- Tim Hackathon KAI Guard

## Kontak

Untuk pertanyaan atau saran, silakan hubungi kami melalui email: example@example.com