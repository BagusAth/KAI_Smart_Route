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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            [
                'code' => 'GMR',
                'name' => 'Stasiun Gambir',
                'city' => 'Jakarta Pusat',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.1767 106.8306)', 4326)"),
            ],
            [
                'code' => 'PSE',
                'name' => 'Stasiun Pasar Senen',
                'city' => 'Jakarta Pusat',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.1764 106.8429)', 4326)"),
            ],
            [
                'code' => 'CN',
                'name' => 'Stasiun Cirebon',
                'city' => 'Cirebon',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.7111 108.5479)', 4326)"),
            ],
            [
                'code' => 'SMT',
                'name' => 'Stasiun Semarang Tawang',
                'city' => 'Semarang',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.9669 110.4214)', 4326)"),
            ],
            [
                'code' => 'SBI',
                'name' => 'Stasiun Surabaya Pasar Turi',
                'city' => 'Surabaya',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.2458 112.7303)', 4326)"),
            ],
            [
                'code' => 'SGU',
                'name' => 'Stasiun Surabaya Gubeng',
                'city' => 'Surabaya',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.2631 112.7520)', 4326)"),
            ],
            [
                'code' => 'YK',
                'name' => 'Stasiun Yogyakarta',
                'city' => 'Yogyakarta',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.7895 110.3671)', 4326)"),
            ],
            [
                'code' => 'PWT',
                'name' => 'Stasiun Purwokerto',
                'city' => 'Purwokerto',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.4211 109.2478)', 4326)"),
            ],
            [
                'code' => 'BD',
                'name' => 'Stasiun Bandung',
                'city' => 'Bandung',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.9175 107.6020)', 4326)"),
            ],
            [
                'code' => 'CKR',
                'name' => 'Stasiun Cikarang',
                'city' => 'Cikarang',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.2611 107.1525)', 4326)"),
            ],
            [
                'code' => 'BKS',
                'name' => 'Stasiun Bekasi',
                'city' => 'Bekasi',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.2383 106.9959)', 4326)"),
            ],
            [
                'code' => 'TGL',
                'name' => 'Stasiun Tegal',
                'city' => 'Tegal',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.8667 109.1500)', 4326)"),
            ],
            [
                'code' => 'PML',
                'name' => 'Stasiun Pemalang',
                'city' => 'Pemalang',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.8885 109.3801)', 4326)"),
            ],
            [
                'code' => 'BJR',
                'name' => 'Stasiun Bojonegoro',
                'city' => 'Bojonegoro',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.1502 111.8813)', 4326)"),
            ],
            [
                'code' => 'KRB',
                'name' => 'Stasiun Karawang',
                'city' => 'Karawang',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.3059 107.2960)', 4326)"),
            ],
            [
                'code' => 'CKP',
                'name' => 'Stasiun Cikampek',
                'city' => 'Karawang',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.4119 107.4517)', 4326)"),
            ],
            [
                'code' => 'PWK',
                'name' => 'Stasiun Purwakarta',
                'city' => 'Purwakarta',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.5560 107.4411)', 4326)"),
            ],
            [
                'code' => 'HLM',
                'name' => 'Stasiun Halim',
                'city' => 'Jakarta Timur',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.2659 106.8864)', 4326)"),
            ],
            [
                'code' => 'PDL',
                'name' => 'Stasiun Padalarang',
                'city' => 'Bandung Barat',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.8364 107.4720)', 4326)"),
            ],
            [
                'code' => 'KJT',
                'name' => 'Stasiun Kertajati',
                'city' => 'Majalengka',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.6700 108.1670)', 4326)"),
            ],
            [
                'code' => 'BGR',
                'name' => 'Stasiun Bogor',
                'city' => 'Bogor',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.5950 106.7920)', 4326)"),
            ],
            [
                'code' => 'TNG',
                'name' => 'Stasiun Tangerang',
                'city' => 'Tangerang',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.1760 106.6280)', 4326)"),
            ],
            [
                'code' => 'SRG',
                'name' => 'Stasiun Serang',
                'city' => 'Serang',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.1210 106.1490)', 4326)"),
            ],
            [
                'code' => 'CNP',
                'name' => 'Stasiun Cianjur',
                'city' => 'Cianjur',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.8161 107.1424)', 4326)"),
            ],
            [
                'code' => 'SLO',
                'name' => 'Stasiun Solo Balapan',
                'city' => 'Surakarta',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.5623 110.8250)', 4326)"),
            ],
            [
                'code' => 'MDN',
                'name' => 'Stasiun Madiun',
                'city' => 'Madiun',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.6172 111.5230)', 4326)"),
            ],
            [
                'code' => 'KTS',
                'name' => 'Stasiun Kertosono',
                'city' => 'Nganjuk',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.5983 112.1680)', 4326)"),
            ],
            [
                'code' => 'MLG',
                'name' => 'Stasiun Malang',
                'city' => 'Malang',
                'location' => DB::raw("ST_GeomFromText('POINT(-7.9778 112.6341)', 4326)"),
            ],
            [
                'code' => 'BLT',
                'name' => 'Stasiun Blitar',
                'city' => 'Blitar',
                'location' => DB::raw("ST_GeomFromText('POINT(-8.0940 112.1684)', 4326)"),
            ],
            [
                'code' => 'DPS',
                'name' => 'Stasiun Denpasar',
                'city' => 'Denpasar',
                'location' => DB::raw("ST_GeomFromText('POINT(-8.6705 115.2126)', 4326)"),
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
                'location' => DB::raw("ST_GeomFromText('POINT(-6.3145 107.1769)', 4326)"),
            ],
            [
                'name' => 'Kota Tua Jakarta',
                'category' => 'Tourism',
                'city' => 'Jakarta Barat',
                'island' => 'Jawa',
                'default_station' => 'PSE',
                'location' => DB::raw("ST_GeomFromText('POINT(-6.1352 106.8141)', 4326)"),
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

        $trains = [
            ['code' => 'KA-ABA', 'name' => 'Argo Bromo Anggrek', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 8, 'class' => 'Eksekutif'],
            ['code' => 'KA-TAKA', 'name' => 'Taksaka', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 10, 'class' => 'Eksekutif & Luxury'],
            ['code' => 'KA-CL', 'name' => 'Commuter Line Cikarang', 'type' => 'Commuter Line', 'operator' => 'KCI', 'total_carriages' => 12, 'class' => 'KRL'],
            ['code' => 'KA-SMTR', 'name' => 'Semarang Ekspres', 'type' => 'Intercity', 'operator' => 'KAI', 'total_carriages' => 9, 'class' => 'Eksekutif & Ekonomi'],
        ];

        $trainIds = collect($trains)
            ->mapWithKeys(fn ($train) => [
                $train['code'] => Train::query()->updateOrCreate(
                    ['code' => $train['code']],
                    $train
                )->id,
            ]);

        $carriages = [
            [
                'train' => 'KA-ABA',
                'car_number' => 1,
                'carriage_type' => 'Seating',
                'class' => 'Eksekutif',
                'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'],
                'amenities' => ['Power Outlet', 'WiFi'],
            ],
            [
                'train' => 'KA-ABA',
                'car_number' => 2,
                'carriage_type' => 'Seating',
                'class' => 'Eksekutif',
                'layout' => ['rows' => 16, 'seats_per_row' => 4, 'configuration' => '2-2'],
                'amenities' => ['Power Outlet'],
            ],
            [
                'train' => 'KA-TAKA',
                'car_number' => 1,
                'carriage_type' => 'Seating',
                'class' => 'Eksekutif',
                'layout' => ['rows' => 14, 'seats_per_row' => 4, 'configuration' => '2-2'],
                'amenities' => ['Power Outlet', 'Snack Service'],
            ],
            [
                'train' => 'KA-TAKA',
                'car_number' => 2,
                'carriage_type' => 'Luxury',
                'class' => 'Luxury',
                'layout' => ['suites' => 6],
                'amenities' => ['Private Entertainment', 'Lie-flat Seat'],
            ],
            [
                'train' => 'KA-CL',
                'car_number' => 1,
                'carriage_type' => 'Standing',
                'class' => 'KRL',
                'layout' => ['sections' => 4, 'doors_per_side' => 2],
                'amenities' => ['Handrail', 'Priority Seats'],
            ],
            [
                'train' => 'KA-CL',
                'car_number' => 2,
                'carriage_type' => 'Standing',
                'class' => 'KRL',
                'layout' => ['sections' => 4, 'doors_per_side' => 2],
                'amenities' => ['Handrail'],
            ],
            [
                'train' => 'KA-SMTR',
                'car_number' => 1,
                'carriage_type' => 'Seating',
                'class' => 'Eksekutif',
                'layout' => ['rows' => 12, 'seats_per_row' => 4, 'configuration' => '2-2'],
                'amenities' => ['Power Outlet', 'WiFi'],
            ],
            [
                'train' => 'KA-SMTR',
                'car_number' => 2,
                'carriage_type' => 'Seating',
                'class' => 'Ekonomi',
                'layout' => ['rows' => 20, 'seats_per_row' => 4, 'configuration' => '2-2'],
                'amenities' => ['Air Conditioner'],
            ],
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
            [
                'train' => 'KA-ABA',
                'station' => 'GMR',
                'order' => 1,
                'platform' => 'GMR-3',
                'departure_offset_minutes' => 0,
                'stop_duration_minutes' => 10,
                'notes' => 'Boarding dibuka 30 menit sebelum keberangkatan.',
            ],
            [
                'train' => 'KA-ABA',
                'station' => 'CN',
                'order' => 2,
                'platform' => 'CN-2',
                'arrival_offset_minutes' => 165,
                'departure_offset_minutes' => 170,
                'stop_duration_minutes' => 5,
            ],
            [
                'train' => 'KA-ABA',
                'station' => 'SMT',
                'order' => 3,
                'platform' => 'SMT-4',
                'arrival_offset_minutes' => 260,
                'departure_offset_minutes' => 265,
                'stop_duration_minutes' => 5,
            ],
            [
                'train' => 'KA-ABA',
                'station' => 'SBI',
                'order' => 4,
                'platform' => 'SBI-6',
                'arrival_offset_minutes' => 375,
                'notes' => 'Tujuan akhir Surabaya Pasar Turi.',
            ],
            [
                'train' => 'KA-TAKA',
                'station' => 'GMR',
                'order' => 1,
                'platform' => 'GMR-4',
                'departure_offset_minutes' => 0,
                'stop_duration_minutes' => 10,
            ],
            [
                'train' => 'KA-TAKA',
                'station' => 'PWT',
                'order' => 2,
                'platform' => 'PWT-2',
                'arrival_offset_minutes' => 300,
                'departure_offset_minutes' => 305,
                'stop_duration_minutes' => 5,
            ],
            [
                'train' => 'KA-TAKA',
                'station' => 'YK',
                'order' => 3,
                'platform' => 'YK-4',
                'arrival_offset_minutes' => 410,
                'notes' => 'Tujuan akhir Yogyakarta.',
            ],
            [
                'train' => 'KA-CL',
                'station' => 'CKR',
                'order' => 1,
                'platform' => 'CKR-1',
                'departure_offset_minutes' => 0,
                'stop_duration_minutes' => 2,
            ],
            [
                'train' => 'KA-CL',
                'station' => 'BKS',
                'order' => 2,
                'platform' => 'BKS-3',
                'arrival_offset_minutes' => 35,
                'notes' => 'Lintas Commuter Line tujuan Bekasi.',
            ],
            [
                'train' => 'KA-SMTR',
                'station' => 'BKS',
                'order' => 1,
                'platform' => 'BKS-5',
                'departure_offset_minutes' => 0,
                'stop_duration_minutes' => 7,
            ],
            [
                'train' => 'KA-SMTR',
                'station' => 'CN',
                'order' => 2,
                'platform' => 'CN-2',
                'arrival_offset_minutes' => 90,
                'departure_offset_minutes' => 95,
                'stop_duration_minutes' => 5,
            ],
            [
                'train' => 'KA-SMTR',
                'station' => 'SMT',
                'order' => 3,
                'platform' => 'SMT-4',
                'arrival_offset_minutes' => 210,
                'notes' => 'Tujuan akhir Semarang Tawang.',
            ],
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

        $schedules = [
            [
                'train' => 'KA-ABA',
                'station' => 'GMR',
                'platform' => 'GMR-3',
                'arrival' => null,
                'departure' => '20:30',
                'price' => 650000,
                'available_seats' => 110,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-ABA',
                'station' => 'CN',
                'platform' => 'CN-2',
                'arrival' => '23:15',
                'departure' => '23:20',
                'price' => 450000,
                'available_seats' => 110,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-ABA',
                'station' => 'SMT',
                'platform' => 'SMT-4',
                'arrival' => '01:25',
                'departure' => '01:30',
                'price' => 350000,
                'available_seats' => 105,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-ABA',
                'station' => 'SBI',
                'platform' => 'SBI-6',
                'arrival' => '04:15',
                'departure' => null,
                'price' => null,
                'available_seats' => null,
                'status' => 'Scheduled',
                'remarks' => 'Peron tujuan akhir.',
            ],
            [
                'train' => 'KA-TAKA',
                'station' => 'GMR',
                'platform' => 'GMR-4',
                'arrival' => null,
                'departure' => '22:15',
                'price' => 700000,
                'available_seats' => 80,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-TAKA',
                'station' => 'PWT',
                'platform' => 'PWT-2',
                'arrival' => '02:35',
                'departure' => '02:40',
                'price' => 480000,
                'available_seats' => 78,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-TAKA',
                'station' => 'YK',
                'platform' => 'YK-4',
                'arrival' => '04:55',
                'departure' => null,
                'price' => null,
                'available_seats' => null,
                'status' => 'Scheduled',
                'remarks' => 'Tujuan akhir.',
            ],
            [
                'train' => 'KA-CL',
                'station' => 'CKR',
                'platform' => 'CKR-1',
                'arrival' => null,
                'departure' => '22:05',
                'price' => 5000,
                'available_seats' => 450,
                'status' => 'Scheduled',
                'remarks' => 'Layanan KRL terakhir malam hari.',
            ],
            [
                'train' => 'KA-CL',
                'station' => 'BKS',
                'platform' => 'BKS-3',
                'arrival' => '22:40',
                'departure' => null,
                'price' => null,
                'available_seats' => null,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-SMTR',
                'station' => 'BKS',
                'platform' => 'BKS-5',
                'arrival' => null,
                'departure' => '23:30',
                'price' => 520000,
                'available_seats' => 95,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-SMTR',
                'station' => 'CN',
                'platform' => 'CN-2',
                'arrival' => '01:05',
                'departure' => '01:10',
                'price' => 380000,
                'available_seats' => 92,
                'status' => 'Scheduled',
            ],
            [
                'train' => 'KA-SMTR',
                'station' => 'SMT',
                'platform' => 'SMT-4',
                'arrival' => '03:00',
                'departure' => null,
                'price' => null,
                'available_seats' => null,
                'status' => 'Scheduled',
                'remarks' => 'Peron kedatangan akhir.',
            ],
        ];

        foreach ($schedules as $schedule) {
            if (! $stationIds->has($schedule['station']) || ! $trainIds->has($schedule['train'])) {
                continue;
            }

            Schedule::query()->updateOrCreate(
                [
                    'train_id' => $trainIds[$schedule['train']],
                    'station_id' => $stationIds[$schedule['station']],
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
    }
}
