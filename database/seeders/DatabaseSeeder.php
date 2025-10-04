<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Models\PointOfInterest;
use App\Models\PoiStationTransfer;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Station;
use App\Models\Track;
use App\Models\Train;
use App\Models\TrainCarriage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            // Jakarta & Jabodetabek core network
            [
                'code' => 'GMR',
                'name' => 'Stasiun Gambir',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8306, -6.1767),
            ],
            [
                'code' => 'PSE',
                'name' => 'Stasiun Pasar Senen',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8429, -6.1764),
            ],
            [
                'code' => 'JAKK',
                'name' => 'Stasiun Jakarta Kota',
                'city' => 'Jakarta Barat',
                'location' => $this->geomPoint(106.8145, -6.1376),
            ],
            [
                'code' => 'MRI',
                'name' => 'Stasiun Manggarai',
                'city' => 'Jakarta Selatan',
                'location' => $this->geomPoint(106.8505, -6.2095),
            ],
            [
                'code' => 'THB',
                'name' => 'Stasiun Tanah Abang',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8130, -6.1861),
            ],
            [
                'code' => 'SUD',
                'name' => 'Stasiun Sudirman',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8225, -6.2006),
            ],
            [
                'code' => 'JUA',
                'name' => 'Stasiun Juanda',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8290, -6.1690),
            ],
            [
                'code' => 'GDD',
                'name' => 'Stasiun Gondangdia',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8330, -6.1860),
            ],
            [
                'code' => 'CKI',
                'name' => 'Stasiun Cikini',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8415, -6.1973),
            ],
            [
                'code' => 'TEB',
                'name' => 'Stasiun Tebet',
                'city' => 'Jakarta Selatan',
                'location' => $this->geomPoint(106.8558, -6.2253),
            ],
            [
                'code' => 'JNG',
                'name' => 'Stasiun Jatinegara',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.8686, -6.2146),
            ],
            [
                'code' => 'CAW',
                'name' => 'Stasiun Cawang',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.8699, -6.2358),
            ],
            [
                'code' => 'DRN',
                'name' => 'Stasiun Duren Kalibata',
                'city' => 'Jakarta Selatan',
                'location' => $this->geomPoint(106.8553, -6.2557),
            ],
            [
                'code' => 'PSM',
                'name' => 'Stasiun Pasar Minggu',
                'city' => 'Jakarta Selatan',
                'location' => $this->geomPoint(106.8439, -6.2851),
            ],
            [
                'code' => 'TNT',
                'name' => 'Stasiun Tanjung Barat',
                'city' => 'Jakarta Selatan',
                'location' => $this->geomPoint(106.8328, -6.3099),
            ],
            [
                'code' => 'LNA',
                'name' => 'Stasiun Lenteng Agung',
                'city' => 'Jakarta Selatan',
                'location' => $this->geomPoint(106.8325, -6.3353),
            ],
            [
                'code' => 'UI',
                'name' => 'Stasiun Universitas Indonesia',
                'city' => 'Depok',
                'location' => $this->geomPoint(106.8321, -6.3628),
            ],
            [
                'code' => 'POC',
                'name' => 'Stasiun Pondok Cina',
                'city' => 'Depok',
                'location' => $this->geomPoint(106.8315, -6.3700),
            ],
            [
                'code' => 'DPB',
                'name' => 'Stasiun Depok Baru',
                'city' => 'Depok',
                'location' => $this->geomPoint(106.8229, -6.3905),
            ],
            [
                'code' => 'DP',
                'name' => 'Stasiun Depok',
                'city' => 'Depok',
                'location' => $this->geomPoint(106.8186, -6.4023),
            ],
            [
                'code' => 'CTA',
                'name' => 'Stasiun Citayam',
                'city' => 'Depok',
                'location' => $this->geomPoint(106.7747, -6.4475),
            ],
            [
                'code' => 'BJD',
                'name' => 'Stasiun Bojong Gede',
                'city' => 'Bogor',
                'location' => $this->geomPoint(106.7930, -6.4811),
            ],
            [
                'code' => 'CLT',
                'name' => 'Stasiun Cilebut',
                'city' => 'Bogor',
                'location' => $this->geomPoint(106.8014, -6.5231),
            ],
            [
                'code' => 'CKR',
                'name' => 'Stasiun Cikarang',
                'city' => 'Cikarang',
                'location' => $this->geomPoint(107.1525, -6.2611),
            ],
            [
                'code' => 'BKS',
                'name' => 'Stasiun Bekasi',
                'city' => 'Bekasi',
                'location' => $this->geomPoint(106.9959, -6.2383),
            ],
            [
                'code' => 'PWY',
                'name' => 'Stasiun Pondok Ranji',
                'city' => 'Tangerang Selatan',
                'location' => $this->geomPoint(106.7356, -6.2512),
            ],
            [
                'code' => 'SDM',
                'name' => 'Stasiun Sudimara',
                'city' => 'Tangerang Selatan',
                'location' => $this->geomPoint(106.6832, -6.2681),
            ],
            [
                'code' => 'SRP',
                'name' => 'Stasiun Serpong',
                'city' => 'Tangerang Selatan',
                'location' => $this->geomPoint(106.6640, -6.3189),
            ],
            [
                'code' => 'RJP',
                'name' => 'Stasiun Rawabuntu',
                'city' => 'Tangerang Selatan',
                'location' => $this->geomPoint(106.7051, -6.3218),
            ],
            [
                'code' => 'HLM',
                'name' => 'Stasiun Halim',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.8864, -6.2659),
            ],
            [
                'code' => 'TNG',
                'name' => 'Stasiun Tangerang',
                'city' => 'Tangerang',
                'location' => $this->geomPoint(106.6280, -6.1760),
            ],
            [
                'code' => 'KRB',
                'name' => 'Stasiun Karawang',
                'city' => 'Karawang',
                'location' => $this->geomPoint(107.2960, -6.3059),
            ],
            [
                'code' => 'CKP',
                'name' => 'Stasiun Cikampek',
                'city' => 'Karawang',
                'location' => $this->geomPoint(107.4517, -6.4119),
            ],
            [
                'code' => 'PWK',
                'name' => 'Stasiun Purwakarta',
                'city' => 'Purwakarta',
                'location' => $this->geomPoint(107.4411, -6.5560),
            ],
            [
                'code' => 'PDL',
                'name' => 'Stasiun Padalarang',
                'city' => 'Bandung Barat',
                'location' => $this->geomPoint(107.4720, -6.8364),
            ],
            [
                'code' => 'BD',
                'name' => 'Stasiun Bandung',
                'city' => 'Bandung',
                'location' => $this->geomPoint(107.6020, -6.9175),
            ],
            [
                'code' => 'KJT',
                'name' => 'Stasiun Kertajati',
                'city' => 'Majalengka',
                'location' => $this->geomPoint(108.1670, -6.6700),
            ],
            [
                'code' => 'BGR',
                'name' => 'Stasiun Bogor',
                'city' => 'Bogor',
                'location' => $this->geomPoint(106.7920, -6.5950),
            ],
            [
                'code' => 'SRG',
                'name' => 'Stasiun Serang',
                'city' => 'Serang',
                'location' => $this->geomPoint(106.1490, -6.1210),
            ],
            [
                'code' => 'CNP',
                'name' => 'Stasiun Cianjur',
                'city' => 'Cianjur',
                'location' => $this->geomPoint(107.1424, -6.8161),
            ],
            [
                'code' => 'CN',
                'name' => 'Stasiun Cirebon',
                'city' => 'Cirebon',
                'location' => $this->geomPoint(108.5479, -6.7111),
            ],
            [
                'code' => 'TGL',
                'name' => 'Stasiun Tegal',
                'city' => 'Tegal',
                'location' => $this->geomPoint(109.1500, -6.8667),
            ],
            [
                'code' => 'PML',
                'name' => 'Stasiun Pemalang',
                'city' => 'Pemalang',
                'location' => $this->geomPoint(109.3801, -6.8885),
            ],
            [
                'code' => 'SMT',
                'name' => 'Stasiun Semarang Tawang',
                'city' => 'Semarang',
                'location' => $this->geomPoint(110.4214, -6.9669),
            ],
            [
                'code' => 'SMC',
                'name' => 'Stasiun Semarang Poncol',
                'city' => 'Semarang',
                'location' => $this->geomPoint(110.4175, -6.9710),
            ],
            [
                'code' => 'WLR',
                'name' => 'Stasiun Weleri',
                'city' => 'Kendal',
                'location' => $this->geomPoint(110.0580, -6.9667),
            ],
            [
                'code' => 'KLG',
                'name' => 'Stasiun Kaliwungu',
                'city' => 'Kendal',
                'location' => $this->geomPoint(110.2753, -6.9448),
            ],
            [
                'code' => 'BRB',
                'name' => 'Stasiun Brumbung',
                'city' => 'Demak',
                'location' => $this->geomPoint(110.5244, -6.9646),
            ],
            [
                'code' => 'NBO',
                'name' => 'Stasiun Ngrombo',
                'city' => 'Grobogan',
                'location' => $this->geomPoint(110.8979, -7.1460),
            ],
            [
                'code' => 'BJR',
                'name' => 'Stasiun Bojonegoro',
                'city' => 'Bojonegoro',
                'location' => $this->geomPoint(111.8813, -7.1502),
            ],
            [
                'code' => 'SLO',
                'name' => 'Stasiun Solo Balapan',
                'city' => 'Surakarta',
                'location' => $this->geomPoint(110.8250, -7.5623),
            ],
            [
                'code' => 'YK',
                'name' => 'Stasiun Yogyakarta',
                'city' => 'Yogyakarta',
                'location' => $this->geomPoint(110.3671, -7.7895),
            ],
            [
                'code' => 'PWT',
                'name' => 'Stasiun Purwokerto',
                'city' => 'Purwokerto',
                'location' => $this->geomPoint(109.2478, -7.4211),
            ],
            [
                'code' => 'SGU',
                'name' => 'Stasiun Surabaya Gubeng',
                'city' => 'Surabaya',
                'location' => $this->geomPoint(112.7520, -7.2631),
            ],
            [
                'code' => 'SBI',
                'name' => 'Stasiun Surabaya Pasar Turi',
                'city' => 'Surabaya',
                'location' => $this->geomPoint(112.7303, -7.2458),
            ],
            [
                'code' => 'MDN',
                'name' => 'Stasiun Madiun',
                'city' => 'Madiun',
                'location' => $this->geomPoint(111.5230, -7.6172),
            ],
            [
                'code' => 'KTS',
                'name' => 'Stasiun Kertosono',
                'city' => 'Nganjuk',
                'location' => $this->geomPoint(112.1680, -7.5983),
            ],
            [
                'code' => 'MLG',
                'name' => 'Stasiun Malang',
                'city' => 'Malang',
                'location' => $this->geomPoint(112.6341, -7.9778),
            ],
            [
                'code' => 'BLT',
                'name' => 'Stasiun Blitar',
                'city' => 'Blitar',
                'location' => $this->geomPoint(112.1684, -8.0940),
            ],
            [
                'code' => 'DPS',
                'name' => 'Stasiun Denpasar',
                'city' => 'Denpasar',
                'location' => $this->geomPoint(115.2126, -8.6705),
            ],
            // Additional Jabodetabek stations (not previously listed)
            [
                'code' => 'DU',
                'name' => 'Stasiun Duri',
                'city' => 'Jakarta Barat',
                'location' => $this->geomPoint(106.8000, -6.1621),
            ],
            [
                'code' => 'PLM',
                'name' => 'Stasiun Palmerah',
                'city' => 'Jakarta Barat',
                'location' => $this->geomPoint(106.7992, -6.2100),
            ],
            [
                'code' => 'KBY',
                'name' => 'Stasiun Kebayoran',
                'city' => 'Jakarta Selatan',
                'location' => $this->geomPoint(106.7821, -6.2443),
            ],
            [
                'code' => 'JMU',
                'name' => 'Stasiun Jurangmangu',
                'city' => 'Tangerang Selatan',
                'location' => $this->geomPoint(106.7310, -6.2652),
            ],
            [
                'code' => 'KAT',
                'name' => 'Stasiun Karet',
                'city' => 'Jakarta Pusat',
                'location' => $this->geomPoint(106.8205, -6.2050),
            ],
            [
                'code' => 'CUK',
                'name' => 'Stasiun Cakung',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.9472, -6.1903),
            ],
            [
                'code' => 'KLD',
                'name' => 'Stasiun Klender',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.9053, -6.2149),
            ],
            [
                'code' => 'KLB',
                'name' => 'Stasiun Klender Baru',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.9000, -6.2147),
            ],
            [
                'code' => 'BUA',
                'name' => 'Stasiun Buaran',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.8954, -6.2195),
            ],
            [
                'code' => 'CPN',
                'name' => 'Stasiun Cipinang',
                'city' => 'Jakarta Timur',
                'location' => $this->geomPoint(106.8898, -6.2157),
            ],
            [
                'code' => 'KRI',
                'name' => 'Stasiun Kranji',
                'city' => 'Bekasi',
                'location' => $this->geomPoint(106.9597, -6.2248),
            ],
            [
                'code' => 'BKT',
                'name' => 'Stasiun Bekasi Timur',
                'city' => 'Bekasi',
                'location' => $this->geomPoint(107.0218, -6.2383),
            ],
            [
                'code' => 'CBT',
                'name' => 'Stasiun Cibitung',
                'city' => 'Bekasi',
                'location' => $this->geomPoint(107.0899, -6.2489),
            ],
        ];

        $stationIds = collect($stations)
            ->mapWithKeys(fn ($station) => [
                (string) $station['code'] => Station::query()->updateOrCreate(
                    ['code' => $station['code']],
                    $station
                )->id,
            ]);

        $platforms = [
            ['station' => 'GMR', 'code' => '3', 'name' => 'Peron 3', 'description' => 'Peron utama keberangkatan kereta jarak jauh ke Jawa Tengah.'],
            ['station' => 'GMR', 'code' => '4', 'name' => 'Peron 4', 'description' => 'Cadangan keberangkatan malam hari.'],
            ['station' => 'CKR', 'code' => '1', 'name' => 'Peron 1', 'description' => 'Lintas Commuter Line tujuan Bekasi dan Jakarta.'],
            ['station' => 'BKS', 'code' => '3', 'name' => 'Peron 3', 'description' => 'Peron layanan KRL dan pengantar.'],
            ['station' => 'BKS', 'code' => '5', 'name' => 'Peron 5', 'description' => 'Peron kereta jarak jauh lintas utara.'],
            ['station' => 'CN', 'code' => '2', 'name' => 'Peron 2', 'description' => 'Peron transit utama Stasiun Cirebon.'],
            ['station' => 'SMT', 'code' => '4', 'name' => 'Peron 4', 'description' => 'Peron kedatangan Semarang Tawang.'],
            ['station' => 'SBI', 'code' => '6', 'name' => 'Peron 6', 'description' => 'Peron tujuan akhir Surabaya Pasar Turi.'],
            ['station' => 'PWT', 'code' => '2', 'name' => 'Peron 2', 'description' => 'Lintas selatan menuju Yogyakarta.'],
            ['station' => 'YK', 'code' => '4', 'name' => 'Peron 4', 'description' => 'Peron kedatangan lintas selatan.'],
        ];

        $platformIds = collect($platforms)
            ->mapWithKeys(function ($platform) use ($stationIds) {
                if (! $stationIds->has($platform['station'])) {
                    return [];
                }

                $model = Platform::query()->updateOrCreate(
                    [
                        'station_id' => $stationIds[$platform['station']],
                        'code' => $platform['code'],
                    ],
                    [
                        'name' => $platform['name'] ?? null,
                        'description' => $platform['description'] ?? null,
                    ]
                );

                return [$platform['station'].'-'.$platform['code'] => $model->id];
            });

        $pointsOfInterest = [
            [
                'name' => 'President University',
                'category' => 'Campus',
                'city' => 'Cikarang',
                'island' => 'Jawa',
                'default_station' => 'CKR',
                'location' => $this->geomPoint(107.1769, -6.3145),
            ],
            [
                'name' => 'Kota Tua Jakarta',
                'category' => 'Tourism',
                'city' => 'Jakarta Barat',
                'island' => 'Jawa',
                'default_station' => 'PSE',
                'location' => $this->geomPoint(106.8141, -6.1352),
            ],
        ];

        $poiIds = collect($pointsOfInterest)
            ->mapWithKeys(function ($poi) use ($stationIds) {
                $model = PointOfInterest::query()->updateOrCreate(
                    ['name' => $poi['name']],
                    [
                        'category' => $poi['category'],
                        'city' => $poi['city'],
                        'island' => $poi['island'] ?? 'Jawa',
                        'location' => $poi['location'],
                        'default_station_id' => $stationIds[$poi['default_station']] ?? null,
                    ]
                );

                return [$poi['name'] => $model->id];
            });

        $poiTransfers = [
            [
                'poi' => 'President University',
                'station' => 'CKR',
                'mode' => 'Shuttle',
                'duration_minutes' => 15,
                'distance_km' => 5.80,
                'estimated_cost' => 0,
                'description' => 'Shuttle kampus tersedia setiap 30 menit menuju Stasiun Cikarang.',
            ],
            [
                'poi' => 'President University',
                'station' => 'BKS',
                'mode' => 'Taxi',
                'duration_minutes' => 35,
                'distance_km' => 22.40,
                'estimated_cost' => 120000,
                'description' => 'Pilihan taksi daring apabila jadwal di Stasiun Cikarang tidak tersedia.',
            ],
            [
                'poi' => 'Kota Tua Jakarta',
                'station' => 'PSE',
                'mode' => 'Taxi',
                'duration_minutes' => 12,
                'distance_km' => 4.30,
                'estimated_cost' => 40000,
                'description' => 'Taksi daring langsung ke Stasiun Pasar Senen.',
            ],
            [
                'poi' => 'Kota Tua Jakarta',
                'station' => 'GMR',
                'mode' => 'Bus',
                'duration_minutes' => 20,
                'distance_km' => 6.20,
                'estimated_cost' => 10000,
                'description' => 'Transjakarta koridor 1 menuju Stasiun Gambir.',
            ],
        ];

        foreach ($poiTransfers as $transfer) {
            if (! $poiIds->has($transfer['poi']) || ! $stationIds->has($transfer['station'])) {
                continue;
            }

            PoiStationTransfer::query()->updateOrCreate(
                [
                    'point_of_interest_id' => $poiIds[$transfer['poi']],
                    'station_id' => $stationIds[$transfer['station']],
                    'mode' => $transfer['mode'],
                ],
                [
                    'duration_minutes' => $transfer['duration_minutes'],
                    'distance_km' => $transfer['distance_km'] ?? null,
                    'estimated_cost' => $transfer['estimated_cost'] ?? null,
                    'description' => $transfer['description'] ?? null,
                ]
            );
        }

        $tracks = [
            [
                'station_a' => 'GMR',
                'station_b' => 'CKR',
                'line_name' => 'Lintas Utara & Selatan',
                'transport_mode' => 'Intercity',
                'distance_km' => 37.20,
                'average_duration_minutes' => 38,
                'base_fare' => 30000,
            ],
            [
                'station_a' => 'PSE',
                'station_b' => 'CKR',
                'line_name' => 'Lintas Utara & Selatan',
                'transport_mode' => 'Intercity',
                'distance_km' => 35.10,
                'average_duration_minutes' => 32,
                'base_fare' => 28000,
            ],
            [
                'station_a' => 'CKR',
                'station_b' => 'CN',
                'line_name' => 'Lintas Utara & Selatan',
                'transport_mode' => 'Intercity',
                'distance_km' => 472.80,
                'average_duration_minutes' => 260,
                'base_fare' => 280000,
            ],
            [
                'station_a' => 'CN',
                'station_b' => 'SMT',
                'line_name' => 'Lintas Utara',
                'transport_mode' => 'Intercity',
                'distance_km' => 228.40,
                'average_duration_minutes' => 135,
                'base_fare' => 180000,
            ],
            [
                'station_a' => 'SMT',
                'station_b' => 'SBI',
                'line_name' => 'Lintas Utara',
                'transport_mode' => 'Intercity',
                'distance_km' => 326.50,
                'average_duration_minutes' => 185,
                'base_fare' => 240000,
            ],
            [
                'station_a' => 'SMT',
                'station_b' => 'SGU',
                'line_name' => 'Lintas Utara',
                'transport_mode' => 'Intercity',
                'distance_km' => 328.70,
                'average_duration_minutes' => 190,
                'base_fare' => 240000,
            ],
            [
                'station_a' => 'CN',
                'station_b' => 'PWT',
                'line_name' => 'Lintas Selatan',
                'transport_mode' => 'Intercity',
                'distance_km' => 189.50,
                'average_duration_minutes' => 120,
                'base_fare' => 150000,
            ],
            [
                'station_a' => 'PWT',
                'station_b' => 'YK',
                'line_name' => 'Lintas Selatan',
                'transport_mode' => 'Intercity',
                'distance_km' => 167.20,
                'average_duration_minutes' => 110,
                'base_fare' => 130000,
            ],
            [
                'station_a' => 'YK',
                'station_b' => 'SGU',
                'line_name' => 'Lintas Selatan',
                'transport_mode' => 'Intercity',
                'distance_km' => 327.10,
                'average_duration_minutes' => 195,
                'base_fare' => 240000,
            ],
            [
                'station_a' => 'CKR',
                'station_b' => 'BKS',
                'line_name' => 'KRL Cikarang Line',
                'transport_mode' => 'Commuter',
                'distance_km' => 18.40,
                'average_duration_minutes' => 27,
                'base_fare' => 5000,
            ],
            [
                'station_a' => 'BKS',
                'station_b' => 'PSE',
                'line_name' => 'KRL Bekasi Line',
                'transport_mode' => 'Commuter',
                'distance_km' => 19.10,
                'average_duration_minutes' => 30,
                'base_fare' => 5000,
            ],
            [
                'station_a' => 'BKS',
                'station_b' => 'CN',
                'line_name' => 'Lintas Utara',
                'transport_mode' => 'Intercity',
                'distance_km' => 84.20,
                'average_duration_minutes' => 70,
                'base_fare' => 90000,
            ],
        ];

        foreach ($tracks as $track) {
            if (! $stationIds->has($track['station_a']) || ! $stationIds->has($track['station_b'])) {
                continue;
            }

            Track::query()->updateOrCreate(
                [
                    'station_a_id' => $stationIds[$track['station_a']],
                    'station_b_id' => $stationIds[$track['station_b']],
                ],
                [
                    'line_name' => $track['line_name'],
                    'transport_mode' => $track['transport_mode'],
                    'distance_km' => $track['distance_km'] ?? null,
                    'average_duration_minutes' => $track['average_duration_minutes'] ?? null,
                    'base_fare' => $track['base_fare'] ?? null,
                    'is_active' => $track['is_active'] ?? true,
                ]
            );
        }

        // Station Attributes (zones, facilities, interchange flags)
        $stationAttributes = [
            ['station' => 'GMR', 'zone' => 'Central', 'is_interchange' => true, 'facilities' => ['lounge','resto','atm','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['elevator','ramp'], 'rating' => 4.6],
            ['station' => 'PSE', 'zone' => 'Central', 'is_interchange' => true, 'facilities' => ['resto','atm','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.3],
            ['station' => 'MRI', 'zone' => 'Core', 'is_interchange' => true, 'facilities' => ['minimarket','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['elevator','ramp'], 'rating' => 4.5],
            ['station' => 'THB', 'zone' => 'Core', 'is_interchange' => true, 'facilities' => ['minimarket','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.2],
            ['station' => 'JNG', 'zone' => 'East', 'is_interchange' => true, 'facilities' => ['minimarket','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.1],
            ['station' => 'BKS', 'zone' => 'East', 'is_interchange' => true, 'facilities' => ['minimarket','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.0],
            ['station' => 'CKR', 'zone' => 'East', 'is_interchange' => false, 'facilities' => ['toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 3.9],
            ['station' => 'CN', 'zone' => 'West Java', 'is_interchange' => true, 'facilities' => ['resto','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.2],
            ['station' => 'SMT', 'zone' => 'Central Java', 'is_interchange' => true, 'facilities' => ['resto','toilet','lounge'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['elevator'], 'rating' => 4.4],
            ['station' => 'SBI', 'zone' => 'East Java', 'is_interchange' => true, 'facilities' => ['resto','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.3],
            ['station' => 'PDL', 'zone' => 'West Java', 'is_interchange' => true, 'facilities' => ['toilet'], 'opening_hours' => '05:00-22:00', 'accessibility' => ['ramp'], 'rating' => 4.0],
            ['station' => 'BD', 'zone' => 'West Java', 'is_interchange' => true, 'facilities' => ['resto','atm','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.5],
            ['station' => 'YK', 'zone' => 'Central Java', 'is_interchange' => true, 'facilities' => ['resto','toilet'], 'opening_hours' => '04:30-23:30', 'accessibility' => ['ramp'], 'rating' => 4.4],
            ['station' => 'SLO', 'zone' => 'Central Java', 'is_interchange' => true, 'facilities' => ['toilet'], 'opening_hours' => '05:00-22:00', 'accessibility' => ['ramp'], 'rating' => 4.2],
        ];

        foreach ($stationAttributes as $attr) {
            if (! $stationIds->has($attr['station'])) {
                continue;
            }
            \App\Models\StationAttribute::query()->updateOrCreate(
                ['station_id' => $stationIds[$attr['station']]],
                [
                    'zone' => $attr['zone'] ?? null,
                    'is_interchange' => $attr['is_interchange'] ?? false,
                    'facilities' => $attr['facilities'] ?? null,
                    'opening_hours' => $attr['opening_hours'] ?? null,
                    'accessibility' => $attr['accessibility'] ?? null,
                    'rating' => $attr['rating'] ?? null,
                ]
            );
        }

        // Expand commuter tracks to enrich interchange calculation
        $extraTracks = [
            ['station_a' => 'THB', 'station_b' => 'DU', 'line_name' => 'KRL Loop Line', 'transport_mode' => 'Commuter', 'distance_km' => 6.2, 'average_duration_minutes' => 12, 'base_fare' => 4000],
            ['station_a' => 'DU', 'station_b' => 'JAKK', 'line_name' => 'KRL Loop Line', 'transport_mode' => 'Commuter', 'distance_km' => 4.0, 'average_duration_minutes' => 9, 'base_fare' => 4000],
            ['station_a' => 'MRI', 'station_b' => 'SUD', 'line_name' => 'KRL Sudirman Loop', 'transport_mode' => 'Commuter', 'distance_km' => 2.1, 'average_duration_minutes' => 6, 'base_fare' => 4000],
            ['station_a' => 'PSE', 'station_b' => 'JNG', 'line_name' => 'KRL Bekasi Line', 'transport_mode' => 'Commuter', 'distance_km' => 4.5, 'average_duration_minutes' => 9, 'base_fare' => 4000],
        ];

        foreach ($extraTracks as $track) {
            if (! $stationIds->has($track['station_a']) || ! $stationIds->has($track['station_b'])) {
                continue;
            }
            Track::query()->updateOrCreate(
                [
                    'station_a_id' => $stationIds[$track['station_a']],
                    'station_b_id' => $stationIds[$track['station_b']],
                ],
                [
                    'line_name' => $track['line_name'],
                    'transport_mode' => $track['transport_mode'],
                    'distance_km' => $track['distance_km'] ?? null,
                    'average_duration_minutes' => $track['average_duration_minutes'] ?? null,
                    'base_fare' => $track['base_fare'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        // Alternative routes will be seeded after trains/routes/schedules

        $trains = [
            // Intercity - Eksekutif & Luxury
            ['code' => 'KA-ABA', 'name' => 'Argo Bromo Anggrek', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Eksekutif & Luxury'],
            ['code' => 'KA-ARLA', 'name' => 'Argo Lawu', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Eksekutif & Luxury'],
            ['code' => 'KA-ARDW', 'name' => 'Argo Dwipangga', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Eksekutif & Luxury'],
            ['code' => 'KA-GAJA', 'name' => 'Gajayana', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 11, 'class' => 'Eksekutif & Luxury'],
            ['code' => 'KA-TAKA', 'name' => 'Taksaka', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Eksekutif & Luxury'],
            
            // Intercity - Eksekutif & Ekonomi/Premium
            ['code' => 'KA-ARPA', 'name' => 'Argo Parahyangan', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Premium'],
            ['code' => 'KA-BIMA', 'name' => 'Bima', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-SMTR', 'name' => 'Sembrani', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-TUR', 'name' => 'Turangga', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Ekonomi'],

            // Commuter Line
            ['code' => 'KRL-BGR', 'name' => 'Commuter Line Bogor', 'type' => 'Commuter', 'operator' => 'KCI', 'total_carriages' => 12, 'class' => 'KRL'],
            ['code' => 'KRL-SRP', 'name' => 'Commuter Line Rangkasbitung', 'type' => 'Commuter', 'operator' => 'KCI', 'total_carriages' => 10, 'class' => 'KRL'],
            ['code' => 'KRL-CKR', 'name' => 'Commuter Line Cikarang', 'type' => 'Commuter', 'operator' => 'KCI', 'total_carriages' => 12, 'class' => 'KRL'],
            ['code' => 'KRL-TNG', 'name' => 'Commuter Line Tangerang', 'type' => 'Commuter', 'operator' => 'KCI', 'total_carriages' => 8, 'class' => 'KRL'],

            // Additional Intercity services (accurate to available stations)
            ['code' => 'KA-ARGSIN', 'name' => 'Argo Sindoro', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Eksekutif'],
            ['code' => 'KA-ARGMUR', 'name' => 'Argo Muria', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Eksekutif'],
            ['code' => 'KA-CIREX', 'name' => 'Cirebon Ekspres', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-KERT', 'name' => 'Kertajaya', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Ekonomi'],
            ['code' => 'KA-JAYB', 'name' => 'Jayabaya', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Ekonomi'],
            ['code' => 'KA-MTRJ', 'name' => 'Matarmaja', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Ekonomi'],
            ['code' => 'KA-FAJYK', 'name' => 'Fajar Utama Yogyakarta', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-SENYK', 'name' => 'Senja Utama Yogyakarta', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-BANGK', 'name' => 'Bangunkarta', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-ARWIL', 'name' => 'Argo Wilis', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Eksekutif'],
            ['code' => 'KA-HARIN', 'name' => 'Harina', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-MALAB', 'name' => 'Malabar', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Eksekutif & Ekonomi'],
            ['code' => 'KA-PASUN', 'name' => 'Pasundan', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Ekonomi'],
            ['code' => 'KA-SINGA', 'name' => 'Singasari', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Ekonomi'],

            // Commuter/regional services outside Jabodetabek using available stations
            ['code' => 'KRL-JGS', 'name' => 'KRL Yogyakarta - Solo', 'type' => 'Commuter', 'operator' => 'KCI', 'total_carriages' => 8, 'class' => 'KRL'],
            ['code' => 'KA-PRAME', 'name' => 'Prambanan Ekspres', 'type' => 'Commuter', 'operator' => 'KAI', 'total_carriages' => 6, 'class' => 'Local'],

            // High-speed service and feeder (using Halim and Padalarang present in dataset)
            ['code' => 'HSR-WHOOSH', 'name' => 'Whoosh (KCIC)', 'type' => 'High-Speed', 'operator' => 'KCIC', 'total_carriages' => 8, 'class' => 'First & Business'],
            ['code' => 'KA-FEEDPDL', 'name' => 'Feeder Whoosh Padalarang - Bandung', 'type' => 'Commuter', 'operator' => 'KAI', 'total_carriages' => 4, 'class' => 'Ekonomi'],
        ];

        $trainIds = collect($trains)
            ->mapWithKeys(fn ($train) => [
                $train['code'] => Train::query()->updateOrCreate(
                    ['code' => $train['code']],
                    $train
                )->id,
            ]);

        $carriages = [
            // Argo Bromo Anggrek (Luxury + Eksekutif)
            ['train' => 'KA-ABA', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Luxury', 'layout' => ['rows' => 6, 'seats_per_row' => 2, 'configuration' => '1-1'], 'amenities' => ['Private Entertainment', 'Lie-flat Seat', 'Snack Service']],
            ['train' => 'KA-ABA', 'car_number' => 2, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet', 'WiFi', 'Reclining Seat']],
            ['train' => 'KA-ABA', 'car_number' => 5, 'carriage_type' => 'Dining', 'class' => 'Restorasi', 'layout' => null, 'amenities' => ['Restaurant Service']],
            
            // Taksaka (Luxury + Eksekutif)
            ['train' => 'KA-TAKA', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Luxury', 'layout' => ['rows' => 6, 'seats_per_row' => 2, 'configuration' => '1-1'], 'amenities' => ['Private Entertainment', 'Lie-flat Seat', 'Snack Service']],
            ['train' => 'KA-TAKA', 'car_number' => 2, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet', 'Snack Service']],

            // Argo Parahyangan (Eksekutif + Premium)
            ['train' => 'KA-ARPA', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet', 'WiFi']],
            ['train' => 'KA-ARPA', 'car_number' => 5, 'carriage_type' => 'Seating', 'class' => 'Ekonomi Premium', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet', 'Reclining Seat']],

            // Gajayana (Luxury + Eksekutif)
            ['train' => 'KA-GAJA', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Luxury', 'layout' => ['rows' => 6, 'seats_per_row' => 2, 'configuration' => '1-1'], 'amenities' => ['Private Entertainment', 'Lie-flat Seat']],
            ['train' => 'KA-GAJA', 'car_number' => 2, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet', 'WiFi']],

            // Commuter Lines
            ['train' => 'KRL-BGR', 'car_number' => 1, 'carriage_type' => 'Standing', 'class' => 'KRL', 'layout' => ['sections' => 4, 'doors_per_side' => 3], 'amenities' => ['Handrail', 'Priority Seats', 'Air Conditioner']],
            ['train' => 'KRL-CKR', 'car_number' => 1, 'carriage_type' => 'Standing', 'class' => 'KRL', 'layout' => ['sections' => 4, 'doors_per_side' => 3], 'amenities' => ['Handrail', 'Priority Seats', 'Air Conditioner']],
            ['train' => 'KRL-SRP', 'car_number' => 1, 'carriage_type' => 'Standing', 'class' => 'KRL', 'layout' => ['sections' => 4, 'doors_per_side' => 3], 'amenities' => ['Handrail', 'Priority Seats', 'Air Conditioner']],
            ['train' => 'KRL-TNG', 'car_number' => 1, 'carriage_type' => 'Standing', 'class' => 'KRL', 'layout' => ['sections' => 4, 'doors_per_side' => 3], 'amenities' => ['Handrail', 'Priority Seats', 'Air Conditioner']],

            // New intercity carriages (sample per train)
            ['train' => 'KA-ARGSIN', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet', 'WiFi']],
            ['train' => 'KA-ARGMUR', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-CIREX', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-CIREX', 'car_number' => 4, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Reclining Seat']],
            ['train' => 'KA-KERT', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-JAYB', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-MTRJ', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Reclining Seat']],
            ['train' => 'KA-FAJYK', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-SENYK', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-BANGK', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-ARWIL', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['WiFi']],
            ['train' => 'KA-HARIN', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-HARIN', 'car_number' => 4, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Reclining Seat']],
            ['train' => 'KA-MALAB', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Eksekutif', 'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'KA-PASUN', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Reclining Seat']],
            ['train' => 'KA-SINGA', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],

            // New commuter and HSR
            ['train' => 'KRL-JGS', 'car_number' => 1, 'carriage_type' => 'Standing', 'class' => 'KRL', 'layout' => ['sections' => 4, 'doors_per_side' => 3], 'amenities' => ['Handrail', 'Priority Seats', 'Air Conditioner']],
            ['train' => 'KA-PRAME', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Local', 'layout' => ['rows' => 14, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
            ['train' => 'HSR-WHOOSH', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'First', 'layout' => ['rows' => 10, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['USB Power', 'WiFi']],
            ['train' => 'HSR-WHOOSH', 'car_number' => 2, 'carriage_type' => 'Seating', 'class' => 'Business', 'layout' => ['rows' => 12, 'seats_per_row' => 5, 'configuration' => '2-1-2'], 'amenities' => ['USB Power', 'WiFi']],
            ['train' => 'KA-FEEDPDL', 'car_number' => 1, 'carriage_type' => 'Seating', 'class' => 'Ekonomi', 'layout' => ['rows' => 15, 'seats_per_row' => 4, 'configuration' => '2-2'], 'amenities' => ['Power Outlet']],
        ];

        foreach ($carriages as $carriage) {
            if (! $trainIds->has($carriage['train'])) {
                continue;
            }

            TrainCarriage::query()->updateOrCreate(
                [
                    'train_id' => $trainIds[$carriage['train']],
                    'car_number' => $carriage['car_number'],
                ],
                [
                    'carriage_type' => $carriage['carriage_type'],
                    'class' => $carriage['class'] ?? null,
                    'layout' => $carriage['layout'] ?? null,
                    'amenities' => $carriage['amenities'] ?? null,
                ]
            );
        }

        $routes = [
            // Argo Bromo Anggrek (GMR-SBI via Lintas Utara)
            ['train' => 'KA-ABA', 'station' => 'GMR', 'order' => 1, 'platform' => 'GMR-3', 'departure_offset_minutes' => 0],
            ['train' => 'KA-ABA', 'station' => 'CN', 'order' => 2, 'platform' => 'CN-2', 'arrival_offset_minutes' => 150, 'departure_offset_minutes' => 155],
            ['train' => 'KA-ABA', 'station' => 'SMT', 'order' => 3, 'platform' => 'SMT-4', 'arrival_offset_minutes' => 295, 'departure_offset_minutes' => 300],
            ['train' => 'KA-ABA', 'station' => 'SBI', 'order' => 4, 'platform' => 'SBI-6', 'arrival_offset_minutes' => 490],
            
            // Taksaka (GMR-YK)
            ['train' => 'KA-TAKA', 'station' => 'GMR', 'order' => 1, 'platform' => 'GMR-4', 'departure_offset_minutes' => 0],
            ['train' => 'KA-TAKA', 'station' => 'PWT', 'order' => 2, 'platform' => 'PWT-2', 'arrival_offset_minutes' => 280, 'departure_offset_minutes' => 285],
            ['train' => 'KA-TAKA', 'station' => 'YK', 'order' => 3, 'platform' => 'YK-4', 'arrival_offset_minutes' => 395],
            
            // Argo Parahyangan (GMR-BD)
            ['train' => 'KA-ARPA', 'station' => 'GMR', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-ARPA', 'station' => 'BD', 'order' => 2, 'arrival_offset_minutes' => 160],
            
            // Argo Lawu (GMR-SLO)
            ['train' => 'KA-ARLA', 'station' => 'GMR', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-ARLA', 'station' => 'PWT', 'order' => 2, 'arrival_offset_minutes' => 280, 'departure_offset_minutes' => 285],
            ['train' => 'KA-ARLA', 'station' => 'YK', 'order' => 3, 'arrival_offset_minutes' => 395, 'departure_offset_minutes' => 400],
            ['train' => 'KA-ARLA', 'station' => 'SLO', 'order' => 4, 'arrival_offset_minutes' => 450],
            
            // Gajayana (GMR-MLG)
            ['train' => 'KA-GAJA', 'station' => 'GMR', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-GAJA', 'station' => 'YK', 'order' => 2, 'arrival_offset_minutes' => 400, 'departure_offset_minutes' => 405],
            ['train' => 'KA-GAJA', 'station' => 'MLG', 'order' => 3, 'arrival_offset_minutes' => 725],

            // Commuter Line Cikarang (CKR-JNG)
            ['train' => 'KRL-CKR', 'station' => 'CKR', 'order' => 1, 'platform' => 'CKR-1', 'departure_offset_minutes' => 0],
            ['train' => 'KRL-CKR', 'station' => 'BKS', 'order' => 2, 'platform' => 'BKS-3', 'arrival_offset_minutes' => 27, 'departure_offset_minutes' => 29],
            ['train' => 'KRL-CKR', 'station' => 'JNG', 'order' => 3, 'arrival_offset_minutes' => 55],

            // Commuter Line Bogor (JAKK-BGR)
            ['train' => 'KRL-BGR', 'station' => 'JAKK', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KRL-BGR', 'station' => 'MRI', 'order' => 2, 'arrival_offset_minutes' => 20, 'departure_offset_minutes' => 25],
            ['train' => 'KRL-BGR', 'station' => 'DP', 'order' => 3, 'arrival_offset_minutes' => 65, 'departure_offset_minutes' => 67],
            ['train' => 'KRL-BGR', 'station' => 'BGR', 'order' => 4, 'arrival_offset_minutes' => 95],

            // Commuter Line Rangkasbitung (THB-SRP)
            ['train' => 'KRL-SRP', 'station' => 'THB', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KRL-SRP', 'station' => 'KBY', 'order' => 2, 'arrival_offset_minutes' => 15, 'departure_offset_minutes' => 17],
            ['train' => 'KRL-SRP', 'station' => 'SRP', 'order' => 3, 'arrival_offset_minutes' => 40],

            // Commuter Line Tangerang (DU-TNG)
            ['train' => 'KRL-TNG', 'station' => 'DU', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KRL-TNG', 'station' => 'TNG', 'order' => 2, 'arrival_offset_minutes' => 25],

            // New intercity routes
            // Argo Sindoro (GMR-CN-SMT)
            ['train' => 'KA-ARGSIN', 'station' => 'GMR', 'order' => 1, 'platform' => 'GMR-3', 'departure_offset_minutes' => 0],
            ['train' => 'KA-ARGSIN', 'station' => 'CN', 'order' => 2, 'platform' => 'CN-2', 'arrival_offset_minutes' => 155, 'departure_offset_minutes' => 160],
            ['train' => 'KA-ARGSIN', 'station' => 'SMT', 'order' => 3, 'platform' => 'SMT-4', 'arrival_offset_minutes' => 300],

            // Argo Muria (GMR-SMT)
            ['train' => 'KA-ARGMUR', 'station' => 'GMR', 'order' => 1, 'platform' => 'GMR-3', 'departure_offset_minutes' => 0],
            ['train' => 'KA-ARGMUR', 'station' => 'SMT', 'order' => 2, 'platform' => 'SMT-4', 'arrival_offset_minutes' => 305],

            // Cirebon Ekspres (PSE-CN)
            ['train' => 'KA-CIREX', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-CIREX', 'station' => 'CN', 'order' => 2, 'platform' => 'CN-2', 'arrival_offset_minutes' => 180],

            // Kertajaya (PSE-CN-SMT-SBI)
            ['train' => 'KA-KERT', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-KERT', 'station' => 'CN', 'order' => 2, 'arrival_offset_minutes' => 180, 'departure_offset_minutes' => 185],
            ['train' => 'KA-KERT', 'station' => 'SMT', 'order' => 3, 'arrival_offset_minutes' => 315, 'departure_offset_minutes' => 320],
            ['train' => 'KA-KERT', 'station' => 'SBI', 'order' => 4, 'platform' => 'SBI-6', 'arrival_offset_minutes' => 500],

            // Jayabaya (PSE-CN-SMT-SBI-MLG)
            ['train' => 'KA-JAYB', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-JAYB', 'station' => 'CN', 'order' => 2, 'arrival_offset_minutes' => 170, 'departure_offset_minutes' => 175],
            ['train' => 'KA-JAYB', 'station' => 'SMT', 'order' => 3, 'arrival_offset_minutes' => 310, 'departure_offset_minutes' => 315],
            ['train' => 'KA-JAYB', 'station' => 'SBI', 'order' => 4, 'arrival_offset_minutes' => 495, 'departure_offset_minutes' => 505],
            ['train' => 'KA-JAYB', 'station' => 'MLG', 'order' => 5, 'arrival_offset_minutes' => 780],

            // Matarmaja (PSE-PWT-YK-MLG)
            ['train' => 'KA-MTRJ', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-MTRJ', 'station' => 'PWT', 'order' => 2, 'arrival_offset_minutes' => 300, 'departure_offset_minutes' => 305],
            ['train' => 'KA-MTRJ', 'station' => 'YK', 'order' => 3, 'arrival_offset_minutes' => 430, 'departure_offset_minutes' => 440],
            ['train' => 'KA-MTRJ', 'station' => 'MLG', 'order' => 4, 'arrival_offset_minutes' => 720],

            // Fajar Utama YK (PSE-PWT-YK)
            ['train' => 'KA-FAJYK', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-FAJYK', 'station' => 'PWT', 'order' => 2, 'arrival_offset_minutes' => 280, 'departure_offset_minutes' => 285],
            ['train' => 'KA-FAJYK', 'station' => 'YK', 'order' => 3, 'arrival_offset_minutes' => 400],

            // Senja Utama YK (PSE-PWT-YK)
            ['train' => 'KA-SENYK', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-SENYK', 'station' => 'PWT', 'order' => 2, 'arrival_offset_minutes' => 285, 'departure_offset_minutes' => 290],
            ['train' => 'KA-SENYK', 'station' => 'YK', 'order' => 3, 'arrival_offset_minutes' => 405],

            // Bangunkarta (PSE-MDN-SGU)
            ['train' => 'KA-BANGK', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-BANGK', 'station' => 'MDN', 'order' => 2, 'arrival_offset_minutes' => 420, 'departure_offset_minutes' => 430],
            ['train' => 'KA-BANGK', 'station' => 'SGU', 'order' => 3, 'arrival_offset_minutes' => 540],

            // Argo Wilis (BD-KTS-SGU)
            ['train' => 'KA-ARWIL', 'station' => 'BD', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-ARWIL', 'station' => 'KTS', 'order' => 2, 'arrival_offset_minutes' => 160, 'departure_offset_minutes' => 170],
            ['train' => 'KA-ARWIL', 'station' => 'SGU', 'order' => 3, 'arrival_offset_minutes' => 360],

            // Harina (BD-CN-SMT-SBI)
            ['train' => 'KA-HARIN', 'station' => 'BD', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-HARIN', 'station' => 'CN', 'order' => 2, 'arrival_offset_minutes' => 180, 'departure_offset_minutes' => 185],
            ['train' => 'KA-HARIN', 'station' => 'SMT', 'order' => 3, 'arrival_offset_minutes' => 320, 'departure_offset_minutes' => 325],
            ['train' => 'KA-HARIN', 'station' => 'SBI', 'order' => 4, 'arrival_offset_minutes' => 510],

            // Malabar (BD-SGU-MLG)
            ['train' => 'KA-MALAB', 'station' => 'BD', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-MALAB', 'station' => 'SGU', 'order' => 2, 'arrival_offset_minutes' => 360, 'departure_offset_minutes' => 370],
            ['train' => 'KA-MALAB', 'station' => 'MLG', 'order' => 3, 'arrival_offset_minutes' => 630],

            // Pasundan (BD-KTS-SGU)
            ['train' => 'KA-PASUN', 'station' => 'BD', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-PASUN', 'station' => 'KTS', 'order' => 2, 'arrival_offset_minutes' => 165, 'departure_offset_minutes' => 170],
            ['train' => 'KA-PASUN', 'station' => 'SGU', 'order' => 3, 'arrival_offset_minutes' => 370],

            // Singasari (PSE-KTS-BLT)
            ['train' => 'KA-SINGA', 'station' => 'PSE', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-SINGA', 'station' => 'KTS', 'order' => 2, 'arrival_offset_minutes' => 480, 'departure_offset_minutes' => 485],
            ['train' => 'KA-SINGA', 'station' => 'BLT', 'order' => 3, 'arrival_offset_minutes' => 560],

            // KRL Yogyakarta - Solo
            ['train' => 'KRL-JGS', 'station' => 'YK', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KRL-JGS', 'station' => 'SLO', 'order' => 2, 'arrival_offset_minutes' => 60],

            // Prambanan Ekspres
            ['train' => 'KA-PRAME', 'station' => 'YK', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-PRAME', 'station' => 'SLO', 'order' => 2, 'arrival_offset_minutes' => 70],

            // HSR Whoosh (Halim-Padalarang) and feeder (Padalarang-Bandung)
            ['train' => 'HSR-WHOOSH', 'station' => 'HLM', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'HSR-WHOOSH', 'station' => 'PDL', 'order' => 2, 'arrival_offset_minutes' => 35],
            ['train' => 'KA-FEEDPDL', 'station' => 'PDL', 'order' => 1, 'departure_offset_minutes' => 0],
            ['train' => 'KA-FEEDPDL', 'station' => 'BD', 'order' => 2, 'arrival_offset_minutes' => 20],
        ];

        foreach ($routes as $route) {
            if (! $stationIds->has($route['station']) || ! $trainIds->has($route['train'])) {
                continue;
            }

            Route::query()->updateOrCreate(
                [
                    'train_id' => $trainIds[$route['train']],
                    'station_id' => $stationIds[$route['station']],
                ],
                [
                    'stop_order' => $route['order'],
                    'platform_id' => isset($route['platform']) && isset($platformIds[$route['platform']]) ? $platformIds[$route['platform']] : null,
                    'arrival_offset_minutes' => $route['arrival_offset_minutes'] ?? null,
                    'departure_offset_minutes' => $route['departure_offset_minutes'] ?? null,
                    'stop_duration_minutes' => $route['stop_duration_minutes'] ?? null,
                    'notes' => $route['notes'] ?? null,
                ]
            );
        }

        $baseSchedules  = [
            // Argo Bromo Anggrek (GMR 08:00 -> SBI 16:10)
            ['train' => 'KA-ABA', 'station' => 'GMR', 'departure' => '08:00', 'price' => 750000, 'available_seats' => 100],
            ['train' => 'KA-ABA', 'station' => 'SBI', 'arrival' => '16:10'],
            
            // Taksaka (GMR 09:00 -> YK 15:35)
            ['train' => 'KA-TAKA', 'station' => 'GMR', 'departure' => '09:00', 'price' => 680000, 'available_seats' => 80],
            ['train' => 'KA-TAKA', 'station' => 'YK', 'arrival' => '15:35'],
            
            // Argo Parahyangan (GMR 07:30 -> BD 10:10)
            ['train' => 'KA-ARPA', 'station' => 'GMR', 'departure' => '07:30', 'price' => 200000, 'available_seats' => 150],
            ['train' => 'KA-ARPA', 'station' => 'BD', 'arrival' => '10:10'],
            
            // Argo Lawu (GMR 20:45 -> SLO 04:15)
            ['train' => 'KA-ARLA', 'station' => 'GMR', 'departure' => '20:45', 'price' => 720000, 'available_seats' => 90],
            ['train' => 'KA-ARLA', 'station' => 'SLO', 'arrival' => '04:15'],
            
            // Gajayana (GMR 18:40 -> MLG 06:45)
            ['train' => 'KA-GAJA', 'station' => 'GMR', 'departure' => '18:40', 'price' => 850000, 'available_seats' => 70],
            ['train' => 'KA-GAJA', 'station' => 'MLG', 'arrival' => '06:45'],

            // KRL Cikarang (CKR 07:00 -> JNG 07:55)
            ['train' => 'KRL-CKR', 'station' => 'CKR', 'departure' => '07:00', 'price' => 5000, 'available_seats' => 500],
            ['train' => 'KRL-CKR', 'station' => 'JNG', 'arrival' => '07:55'],

            // KRL Bogor (JAKK 08:00 -> BGR 09:35)
            ['train' => 'KRL-BGR', 'station' => 'JAKK', 'departure' => '08:00', 'price' => 6000, 'available_seats' => 500],
            ['train' => 'KRL-BGR', 'station' => 'BGR', 'arrival' => '09:35'],

            // KRL Rangkasbitung (THB 08:15 -> SRP 08:55)
            ['train' => 'KRL-SRP', 'station' => 'THB', 'departure' => '08:15', 'price' => 4000, 'available_seats' => 500],
            ['train' => 'KRL-SRP', 'station' => 'SRP', 'arrival' => '08:55'],

            // KRL Tangerang (DU 08:30 -> TNG 08:55)
            ['train' => 'KRL-TNG', 'station' => 'DU', 'departure' => '08:30', 'price' => 3000, 'available_seats' => 500],
            ['train' => 'KRL-TNG', 'station' => 'TNG', 'arrival' => '08:55'],

            // New schedules for added trains
            // Argo Sindoro (GMR 07:00 -> SMT 12:00)
            ['train' => 'KA-ARGSIN', 'station' => 'GMR', 'departure' => '07:00', 'price' => 650000, 'available_seats' => 120],
            ['train' => 'KA-ARGSIN', 'station' => 'SMT', 'arrival' => '12:00'],

            // Argo Muria (GMR 15:00 -> SMT 20:05)
            ['train' => 'KA-ARGMUR', 'station' => 'GMR', 'departure' => '15:00', 'price' => 650000, 'available_seats' => 120],
            ['train' => 'KA-ARGMUR', 'station' => 'SMT', 'arrival' => '20:05'],

            // Cirebon Ekspres (PSE 06:30 -> CN 09:30)
            ['train' => 'KA-CIREX', 'station' => 'PSE', 'departure' => '06:30', 'price' => 180000, 'available_seats' => 200],
            ['train' => 'KA-CIREX', 'station' => 'CN', 'arrival' => '09:30'],

            // Kertajaya (PSE 10:00 -> SBI 18:20)
            ['train' => 'KA-KERT', 'station' => 'PSE', 'departure' => '10:00', 'price' => 260000, 'available_seats' => 220],
            ['train' => 'KA-KERT', 'station' => 'SBI', 'arrival' => '18:20'],

            // Jayabaya (PSE 12:00 -> MLG 23:00)
            ['train' => 'KA-JAYB', 'station' => 'PSE', 'departure' => '12:00', 'price' => 300000, 'available_seats' => 220],
            ['train' => 'KA-JAYB', 'station' => 'MLG', 'arrival' => '23:00'],

            // Matarmaja (PSE 08:00 -> MLG 20:00)
            ['train' => 'KA-MTRJ', 'station' => 'PSE', 'departure' => '08:00', 'price' => 240000, 'available_seats' => 230],
            ['train' => 'KA-MTRJ', 'station' => 'MLG', 'arrival' => '20:00'],

            // Fajar Utama YK (PSE 05:30 -> YK 12:10)
            ['train' => 'KA-FAJYK', 'station' => 'PSE', 'departure' => '05:30', 'price' => 450000, 'available_seats' => 140],
            ['train' => 'KA-FAJYK', 'station' => 'YK', 'arrival' => '12:10'],

            // Senja Utama YK (PSE 21:00 -> YK 04:00)
            ['train' => 'KA-SENYK', 'station' => 'PSE', 'departure' => '21:00', 'price' => 470000, 'available_seats' => 140],
            ['train' => 'KA-SENYK', 'station' => 'YK', 'arrival' => '04:00'],

            // Bangunkarta (PSE 15:00 -> SGU 23:00)
            ['train' => 'KA-BANGK', 'station' => 'PSE', 'departure' => '15:00', 'price' => 500000, 'available_seats' => 150],
            ['train' => 'KA-BANGK', 'station' => 'SGU', 'arrival' => '23:00'],

            // Argo Wilis (BD 08:30 -> SGU 14:30)
            ['train' => 'KA-ARWIL', 'station' => 'BD', 'departure' => '08:30', 'price' => 520000, 'available_seats' => 120],
            ['train' => 'KA-ARWIL', 'station' => 'SGU', 'arrival' => '14:30'],

            // Harina (BD 20:00 -> SBI 05:00)
            ['train' => 'KA-HARIN', 'station' => 'BD', 'departure' => '20:00', 'price' => 400000, 'available_seats' => 160],
            ['train' => 'KA-HARIN', 'station' => 'SBI', 'arrival' => '05:00'],

            // Malabar (BD 17:00 -> MLG 03:30)
            ['train' => 'KA-MALAB', 'station' => 'BD', 'departure' => '17:00', 'price' => 480000, 'available_seats' => 150],
            ['train' => 'KA-MALAB', 'station' => 'MLG', 'arrival' => '03:30'],

            // Pasundan (BD 06:00 -> SGU 14:00)
            ['train' => 'KA-PASUN', 'station' => 'BD', 'departure' => '06:00', 'price' => 230000, 'available_seats' => 180],
            ['train' => 'KA-PASUN', 'station' => 'SGU', 'arrival' => '14:00'],

            // Singasari (PSE 10:30 -> BLT 19:50)
            ['train' => 'KA-SINGA', 'station' => 'PSE', 'departure' => '10:30', 'price' => 260000, 'available_seats' => 180],
            ['train' => 'KA-SINGA', 'station' => 'BLT', 'arrival' => '19:50'],

            // KRL Yogyakarta - Solo (YK 07:00 -> SLO 08:00)
            ['train' => 'KRL-JGS', 'station' => 'YK', 'departure' => '07:00', 'price' => 8000, 'available_seats' => 600],
            ['train' => 'KRL-JGS', 'station' => 'SLO', 'arrival' => '08:00'],

            // Prambanan Ekspres (YK 09:00 -> SLO 10:10)
            ['train' => 'KA-PRAME', 'station' => 'YK', 'departure' => '09:00', 'price' => 20000, 'available_seats' => 300],
            ['train' => 'KA-PRAME', 'station' => 'SLO', 'arrival' => '10:10'],

            // HSR Whoosh (HLM 08:00 -> PDL 08:35) & Feeder (PDL 08:45 -> BD 09:05)
            ['train' => 'HSR-WHOOSH', 'station' => 'HLM', 'departure' => '08:00', 'price' => 250000, 'available_seats' => 400],
            ['train' => 'HSR-WHOOSH', 'station' => 'PDL', 'arrival' => '08:35'],
            ['train' => 'KA-FEEDPDL', 'station' => 'PDL', 'departure' => '08:45', 'price' => 10000, 'available_seats' => 300],
            ['train' => 'KA-FEEDPDL', 'station' => 'BD', 'arrival' => '09:05'],
        ];

        $schedules = [];
        $startDate = Carbon::today();

        for ($day = 0; $day < 30; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            
            foreach ($baseSchedules as $schedule) {
                $schedules[] = array_merge($schedule, [
                    'departure_date' => $currentDate->format('Y-m-d')
                ]);
            }
        }

        foreach ($schedules as $schedule) {
            if (! $stationIds->has($schedule['station']) || ! $trainIds->has($schedule['train'])) {
                continue;
            }

            Schedule::query()->updateOrCreate(
                [
                    'train_id' => $trainIds[$schedule['train']],
                    'station_id' => $stationIds[$schedule['station']],
                    'departure_date' => $schedule['departure_date'], // Tambahkan ke unique key
                ],
                [
                    'arrival' => $schedule['arrival'] ?? null,
                    'departure' => $schedule['departure'] ?? null,
                    'platform_id' => isset($schedule['platform']) && isset($platformIds[$schedule['platform']]) ? $platformIds[$schedule['platform']] : null,
                    'price' => $schedule['price'] ?? null,
                    'available_seats' => $schedule['available_seats'] ?? null,
                    'status' => $schedule['status'] ?? 'Scheduled',
                    'remarks' => $schedule['remarks'] ?? null,
                ]
            );
        }

        // foreach ($schedules as $schedule) {
        //     if (! $stationIds->has($schedule['station']) || ! $trainIds->has($schedule['train'])) {
        //         continue;
        //     }

        //     Schedule::query()->updateOrCreate(
        //         [
        //             'train_id' => $trainIds[$schedule['train']],
        //             'station_id' => $stationIds[$schedule['station']],
        //         ],
        //         [
        //             'arrival' => $schedule['arrival'] ?? null,
        //             'departure' => $schedule['departure'] ?? null,
        //             'platform_id' => isset($schedule['platform']) && isset($platformIds[$schedule['platform']]) ? $platformIds[$schedule['platform']] : null,
        //             'price' => $schedule['price'] ?? null,
        //             'available_seats' => $schedule['available_seats'] ?? null,
        //             'status' => $schedule['status'] ?? 'Scheduled',
        //             'remarks' => $schedule['remarks'] ?? null,
        //         ]
        //     );
        // }
        // Alternative routes recommendations (seeded last)
        $alternativeRoutes = [
            [
                'origin' => 'GMR', 'destination' => 'MLG', 'primary_train' => 'KA-GAJA',
                'via' => 'YK', 'alternative_train' => 'KA-TAKA', 'transfer_type' => 'cross-platform',
                'total_duration_minutes' => 735, 'notes' => 'Transfer di Yogyakarta bila jadwal langsung penuh.'
            ],
            [
                'origin' => 'PSE', 'destination' => 'SBI', 'primary_train' => 'KA-KERT',
                'via' => 'SMT', 'alternative_train' => 'KA-ARGMUR', 'transfer_type' => 'same-platform',
                'total_duration_minutes' => 505, 'notes' => 'Alternatif: ganti di Semarang Tawang.'
            ],
            [
                'origin' => 'HLM', 'destination' => 'BD', 'primary_train' => 'HSR-WHOOSH',
                'via' => 'PDL', 'alternative_train' => 'KA-FEEDPDL', 'transfer_type' => 'feeder',
                'total_duration_minutes' => 65, 'notes' => 'Naik feeder di Padalarang.'
            ],
            [
                'origin' => 'JAKK', 'destination' => 'BGR', 'primary_train' => 'KRL-BGR',
                'via' => 'MRI', 'alternative_train' => 'KRL-BGR', 'transfer_type' => 'same-platform',
                'total_duration_minutes' => 95, 'notes' => 'Transit di Manggarai jika direct penuh.'
            ],
        ];

        foreach ($alternativeRoutes as $alt) {
            if (! $stationIds->has($alt['origin']) || ! $stationIds->has($alt['destination']) || ! $stationIds->has($alt['via'])) {
                continue;
            }
            if (! $trainIds->has($alt['primary_train']) || ! $trainIds->has($alt['alternative_train'])) {
                continue;
            }
            \App\Models\AlternativeRoute::query()->updateOrCreate(
                [
                    'origin_station_id' => $stationIds[$alt['origin']],
                    'destination_station_id' => $stationIds[$alt['destination']],
                    'via_station_id' => $stationIds[$alt['via']],
                ],
                [
                    'primary_train_id' => $trainIds[$alt['primary_train']],
                    'alternative_train_id' => $trainIds[$alt['alternative_train']],
                    'transfer_type' => $alt['transfer_type'] ?? null,
                    'total_duration_minutes' => $alt['total_duration_minutes'] ?? null,
                    'notes' => $alt['notes'] ?? null,
                ]
            );
        }
    }

    private function geomPoint(float $longitude, float $latitude)
    {
        return DB::raw(sprintf(
            "ST_GeomFromText('POINT(%F %F)', 4326)",
            $latitude,
            $longitude
        ));
    }
}
