# KAIzen: Smart Multi-Modal Routing & Bot Detection System

KAIzen adalah sebuah platform web inovatif yang dirancang untuk merevolusi pengalaman perjalanan kereta api di Indonesia. Proyek ini mengatasi dua tantangan utama: kompleksitas perencanaan perjalanan multi-moda dan ancaman calo tiket yang menggunakan bot.

Dengan menggabungkan mesin pencari rute cerdas dan teknologi deteksi bot berbasis perilaku, KAIzen memastikan pengguna dapat merencanakan perjalanan mereka dengan mudah sambil menjaga keadilan dalam proses pemesanan tiket.

## Masalah yang Dipecahkan

1.  **Perencanaan Rute yang Rumit bagi Pengguna**
    - Pengguna seringkali kesulitan menemukan rute kereta yang optimal, terutama jika stasiun terdekat tidak memiliki jadwal yang sesuai dengan rentang waktu yang diinginkan.
    - Mereka harus secara manual mencari alternatif, seperti menggunakan moda transportasi lain (contoh: Commuter Line) untuk mencapai stasiun yang lebih besar yang memiliki jadwal yang cocok. Proses ini tidak efisien dan memakan waktu.

2.  **Ancaman Calo Tiket (Ticket Scalping)**
    - Pada periode puncak seperti musim liburan, calo menggunakan bot otomatis untuk memborong tiket dalam jumlah besar secepat mungkin.
    - Hal ini membuat pengguna asli (manusia) kesulitan mendapatkan tiket dan seringkali terpaksa membeli dari calo dengan harga yang jauh lebih tinggi. Praktik ini merusak citra perusahaan dan merugikan konsumen.

## Fitur Unggulan

### 1. Mesin Rekomendasi Rute Cerdas (Smart Route Engine)

Fitur ini mengubah cara pengguna mencari tiket kereta api, dari yang berbasis stasiun menjadi berbasis lokasi dan waktu.

-   **Pencarian Berbasis Lokasi & Waktu**: Pengguna dapat memasukkan lokasi awal (misalnya: "President University") dan tujuan, beserta rentang waktu keberangkatan yang fleksibel (misalnya: antara 21.00 - 23.00).
-   **Rekomendasi Multi-Moda**: Jika tidak ada kereta yang tersedia dari stasiun terdekat pada rentang waktu yang dipilih, sistem akan secara cerdas merekomendasikan rute alternatif.
    -   **Contoh**: Pengguna ingin berangkat dari Cikarang ke Semarang pada malam hari, tetapi tidak ada jadwal kereta. Sistem akan merekomendasikan untuk naik Commuter Line dari Stasiun Cikarang ke Stasiun Bekasi, lalu melanjutkan perjalanan dengan kereta antarkota dari Bekasi yang jadwalnya sesuai.
-   **Informasi Rute Detail**: Rekomendasi tidak hanya mencakup jadwal dan harga, tetapi juga informasi penting seperti **nomor peron** dan **denah gerbong kereta**, membantu pengguna merencanakan perjalanan mereka hingga ke detail terkecil.

### 2. Deteksi Bot & Calo Berbasis Perilaku (Behavior Guard)

Untuk memastikan keadilan, KAIzen mengintegrasikan sistem keamanan canggih untuk mendeteksi dan mencegah aktivitas bot.

-   **Analisis Perilaku Real-Time**: Sistem menganalisis berbagai input pengguna secara diam-diam, termasuk gerakan mouse, kecepatan dan ritme pengetikan, serta pola interaksi dengan halaman.
-   **Trust Score**: Berdasarkan analisis, setiap sesi pengguna diberi "Trust Score" yang mengindikasikan kemungkinan apakah sesi tersebut berasal dari manusia atau bot.
-   **Tindakan Adaptif**: Sesi dengan skor kepercayaan rendah (terindikasi sebagai bot) dapat secara otomatis diblokir atau diberi tantangan verifikasi tambahan, sementara pengguna asli dapat melanjutkan pemesanan tanpa gangguan.

## Teknologi yang Digunakan

-   **Backend**: Laravel (PHP)
-   **Frontend**: Blade Templates, Vite, CSS, JavaScript
-   **Bot Simulation & Testing Toolkit**: Node.js, Puppeteer, Express.js
-   **Machine Learning (untuk deteksi bot)**: TensorFlow.js (dalam toolkit `nodejs`)

## Struktur Proyek

-   `/app` & `/routes`: Logika inti aplikasi Laravel, termasuk controller dan definisi rute.
-   `/resources/views`: File Blade untuk tampilan frontend.
-   `/public`: Aset publik seperti CSS, JS, dan gambar.
-   `/database`: Skema database (migrasi) dan data awal (seeders).
-   `/nodejs`: Toolkit mandiri untuk:
    -   `bot-detection-test.js`: Simulasi bot end-to-end untuk menguji sistem.
    -   `collect-training-data.js`: Mengumpulkan data perilaku untuk melatih model.
    -   `train-model.js`: Skrip untuk melatih model deteksi bot.

## Panduan Instalasi dan Penggunaan

### Prasyarat
- PHP & Composer
- Node.js & NPM
- Database (misalnya: MySQL, PostgreSQL)

### 1. Menjalankan Aplikasi Web Laravel

```bash
# 1. Clone repositori
git clone [URL_REPO_ANDA]
cd KAI_Smart_Route

# 2. Install dependensi PHP
composer install

# 3. Salin file environment dan konfigurasi
cp .env.example .env
# Atur koneksi database Anda di file .env

# 4. Generate application key
php artisan key:generate

# 5. Jalankan migrasi database
php artisan migrate --seed

# 6. Install dependensi frontend dan build aset
npm install
npm run dev

# 7. Jalankan server pengembangan Laravel
php artisan serve
```
Aplikasi sekarang akan berjalan di `http://localhost:8000`.

### 2. Menggunakan Toolkit Deteksi Bot

Toolkit ini berada di dalam folder `nodejs`.

```bash
# 1. Masuk ke direktori nodejs
cd nodejs

# 2. Install dependensi Node.js
npm install

# 3. Jalankan server Behavior Guard (diperlukan untuk tes)
node server/server.js

# 4. (Di terminal lain) Jalankan skrip simulasi bot
node bot-detection-test.js
```
Skrip ini akan membuka browser, melakukan simulasi pemesanan seperti bot, dan melaporkan **Trust Score** yang dihasilkan di terminal.

## Alur Pencarian & Rekomendasi (Contoh Kasus)

Contoh nyata penggunaan fitur pencarian multi-moda:

> Pengguna berada di area President University (sekitar Cikarang) dan ingin menuju Stasiun Tawang Semarang dengan rentang keberangkatan antara 22.00 - 23.59.

1. Sistem mendeteksi stasiun terdekat (Cikarang) tidak memiliki jadwal kereta antarkota pada rentang waktu tersebut.
2. Mesin rute menghitung alternatif: Cikarang ➝ (Commuter Line) ➝ Bekasi ➝ (Kereta Antarkota) ➝ Semarang Tawang.
3. Ditampilkan beberapa opsi kereta dari Bekasi yang sesuai waktu, termasuk estimasi harga dan jenis layanan (ekonomi / bisnis / eksekutif).
4. Ditampilkan pula informasi pendukung: kode perjalanan, nomor peron, dan denah gerbong untuk mempermudah orientasi pengguna.

## Arsitektur Singkat

| Lapisan | Komponen | Peran |
|---------|----------|------|
| Presentasi | Blade Views, CSS, JS | Form pencarian, rekomendasi rute, reservasi, konfirmasi, pembayaran |
| Logika Aplikasi | Controller Laravel | Mengolah input, memvalidasi, menyusun struktur data rute |
| Domain Data | Model: Station, Track, Train, Route, Schedule | Menyimpan entitas perjalanan dan hubungan antar tabel |
| Keamanan Perilaku | Behavior Guard (JS + Node flush endpoint) | Mengumpulkan dan menganalisis telemetri interaksi |
| Automasi & ML | Folder `nodejs` | Bot simulator, data collection, model training |

## Dataset & Model Deteksi Bot (Konsep)

Sistem deteksi bot memanfaatkan fitur-fitur perilaku seperti:
- Kecepatan dan variasi gerakan mouse (path curvature, idle gap)
- Tempo pengetikan (interval antar keydown, kestabilan ritme)
- Pola klik (burst vs distribusi waktu)
- Korelasi fokus elemen dengan aksi berikutnya
- Rasio interaksi meaningful vs superficial (hover massal, spam fokus)

Dataset mentah dikumpulkan melalui skrip pengumpulan (`collect-training-data*.js`) lalu dinormalisasi dan dilatih menjadi model TensorFlow.js sederhana (Dense layers) untuk klasifikasi biner.

## Ide Pengembangan Lanjutan

1. Dynamic Pricing Awareness: Korelasi rekomendasi rute dengan fluktuasi harga waktu nyata.
2. Real-Time Seat Map Sync: Integrasi WebSocket untuk update ketersediaan kursi tanpa refresh.
3. Adaptive Challenge Layer: Menggabungkan puzzle ringan hanya untuk sesi borderline (trust score zona abu-abu).
4. Geo-Proximity Smart Indexing: Index spasial (mis. PostGIS) untuk pencarian stasiun terdekat berdasar lat/long.
5. Multi-Leg Optimization: Algoritma heuristik (A*) atau RAPTOR untuk rute dengan lebih dari dua segmen.
6. Progressive Web App (PWA) Mode: Offline caching untuk jadwal dan denah peron.
7. Anomaly Feedback Loop: Mengirim sesi anomali ke antrean (queue) untuk retraining otomatis berkala.
8. Accessibility Metrics: Mengukur kemudahan navigasi bagi pengguna dengan kebutuhan khusus.

## Kontribusi

Pull request, issue, dan diskusi ide baru sangat terbuka. Pastikan mengikuti gaya kode yang konsisten dan sertakan deskripsi jelas pada setiap perubahan.

## Lisensi

Proyek ini menggunakan lisensi MIT (dapat disesuaikan sesuai kebutuhan organisasi Anda).

## Kontak

Untuk kolaborasi, pertanyaan teknis, atau demo lanjutan: silakan hubungi tim pengembang internal.

---

Terima kasih telah menggunakan KAIzen. Fokus kami: perjalanan lebih cerdas, adil, dan aman dari eksploitasi calo.
