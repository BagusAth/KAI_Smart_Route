<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\Schedule;
use App\Models\Station;
use App\Models\Track;
use App\Models\Train;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            ['code' => 'GMR', 'name' => 'Gambir', 'city' => 'Jakarta Pusat'],
            ['code' => 'PSE', 'name' => 'Pasar Senen', 'city' => 'Jakarta Pusat'],
            ['code' => 'CN', 'name' => 'Cirebon', 'city' => 'Cirebon'],
            ['code' => 'SMT', 'name' => 'Semarang Tawang', 'city' => 'Semarang'],
            ['code' => 'SBI', 'name' => 'Surabaya Pasar Turi', 'city' => 'Surabaya'],
            ['code' => 'YK', 'name' => 'Yogyakarta', 'city' => 'Yogyakarta'],
            ['code' => 'PWT', 'name' => 'Purwokerto', 'city' => 'Purwokerto'],
            ['code' => 'BD', 'name' => 'Bandung', 'city' => 'Bandung'],
            ['code' => 'CKR', 'name' => 'Cikarang', 'city' => 'Cikarang'],
            ['code' => 'BKS', 'name' => 'Bekasi', 'city' => 'Bekasi'],
        ];

        $stationIds = collect($stations)
            ->mapWithKeys(fn ($station) => [
                (string) $station['code'] => Station::query()->updateOrCreate(
                    ['code' => $station['code']],
                    $station
                )->id,
            ]);

        $tracks = [
            ['station_a' => 'GMR', 'station_b' => 'CKR', 'line_name' => 'Lintas Utara & Selatan'],
            ['station_a' => 'PSE', 'station_b' => 'CKR', 'line_name' => 'Lintas Utara & Selatan'],
            ['station_a' => 'CKR', 'station_b' => 'CN', 'line_name' => 'Lintas Utara & Selatan'],
            ['station_a' => 'CN', 'station_b' => 'SMT', 'line_name' => 'Lintas Utara'],
            ['station_a' => 'SMT', 'station_b' => 'SBI', 'line_name' => 'Lintas Utara'],
            ['station_a' => 'CN', 'station_b' => 'PWT', 'line_name' => 'Lintas Selatan'],
            ['station_a' => 'PWT', 'station_b' => 'YK', 'line_name' => 'Lintas Selatan'],
            ['station_a' => 'YK', 'station_b' => 'SGU', 'line_name' => 'Lintas Selatan'],
            ['station_a' => 'CKR', 'station_b' => 'BKS', 'line_name' => 'Commuter Line'],
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
                ['line_name' => $track['line_name']]
            );
        }

        $trains = [
            ['code' => 'KA-ABA', 'name' => 'Argo Bromo Anggrek', 'class' => 'Eksekutif'],
            ['code' => 'KA-TAKA', 'name' => 'Taksaka', 'class' => 'Eksekutif & Luxury'],
            ['code' => 'KA-CL', 'name' => 'Commuter Line Cikarang', 'class' => 'KRL'],
        ];

        $trainIds = collect($trains)
            ->mapWithKeys(fn ($train) => [
                $train['code'] => Train::query()->updateOrCreate(
                    ['code' => $train['code']],
                    $train
                )->id,
            ]);

        $routes = [
            ['train' => 'KA-ABA', 'station' => 'GMR', 'order' => 1],
            ['train' => 'KA-ABA', 'station' => 'CN', 'order' => 2],
            ['train' => 'KA-ABA', 'station' => 'SMT', 'order' => 3],
            ['train' => 'KA-ABA', 'station' => 'SBI', 'order' => 4],
            ['train' => 'KA-TAKA', 'station' => 'GMR', 'order' => 1],
            ['train' => 'KA-TAKA', 'station' => 'PWT', 'order' => 2],
            ['train' => 'KA-TAKA', 'station' => 'YK', 'order' => 3],
            ['train' => 'KA-CL', 'station' => 'CKR', 'order' => 1],
            ['train' => 'KA-CL', 'station' => 'BKS', 'order' => 2],
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
                ['stop_order' => $route['order']]
            );
        }

        $schedules = [
            ['train' => 'KA-ABA', 'station' => 'GMR', 'arrival' => null, 'departure' => '08:20'],
            ['train' => 'KA-ABA', 'station' => 'CN', 'arrival' => '11:05', 'departure' => '11:10'],
            ['train' => 'KA-ABA', 'station' => 'SMT', 'arrival' => '14:25', 'departure' => '14:30'],
            ['train' => 'KA-ABA', 'station' => 'SBI', 'arrival' => '17:15', 'departure' => null],
            ['train' => 'KA-TAKA', 'station' => 'GMR', 'arrival' => null, 'departure' => '21:40'],
            ['train' => 'KA-TAKA', 'station' => 'PWT', 'arrival' => '02:30', 'departure' => '02:35'],
            ['train' => 'KA-TAKA', 'station' => 'YK', 'arrival' => '04:50', 'departure' => null],
            ['train' => 'KA-CL', 'station' => 'CKR', 'arrival' => null, 'departure' => '21:35'],
            ['train' => 'KA-CL', 'station' => 'BKS', 'arrival' => '22:05', 'departure' => null],
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
                    'arrival' => $schedule['arrival'],
                    'departure' => $schedule['departure'],
                ]
            );
        }
    }
}
