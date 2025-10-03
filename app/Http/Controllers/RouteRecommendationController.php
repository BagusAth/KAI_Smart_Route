<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RouteRecommendationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $validated = $request->validate(
            [
                'origin' => ['required', 'string', 'max:10'],
                'origin_label' => ['required', 'string', 'max:255'],
                'destination' => ['required', 'string', 'max:10'],
                'destination_label' => ['required', 'string', 'max:255'],
                'start_time' => ['required', 'string', Rule::in($this->timeOptions())],
                'end_time' => ['required', 'string', Rule::in($this->timeOptions())],
            ],
            [],
            [
                'origin' => 'stasiun keberangkatan',
                'origin_label' => 'nama stasiun keberangkatan',
                'destination' => 'stasiun tujuan',
                'destination_label' => 'nama stasiun tujuan',
                'start_time' => 'jam mulai',
                'end_time' => 'jam akhir',
            ]
        );

        $originStation = Station::query()->where('code', $validated['origin'])->first();
        $destinationStation = Station::query()->where('code', $validated['destination'])->first();

        $originName = $originStation?->name ?? $validated['origin_label'];
        $destinationName = $destinationStation?->name ?? $validated['destination_label'];

        $today = Carbon::now();
        $dateOptions = $this->buildDateOptions($today);

        $recommendations = $this->buildSampleRecommendations($originName, $destinationName);

        return view('routes', [
            'searchSummary' => [
                'title' => sprintf('%s - %s', $originName, $destinationName),
                'passengers' => '1 Dewasa',
                'time_window' => sprintf('%s - %s', $validated['start_time'], $validated['end_time']),
            ],
            'dateOptions' => $dateOptions,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function timeOptions(): array
    {
        return collect(range(0, 23))
            ->map(fn (int $hour) => str_pad((string) $hour, 2, '0', STR_PAD_LEFT).':00')
            ->all();
    }

    /**
     * @return array<int, array{
     *     label: string,
     *     day: string,
     *     date: string,
     *     is_active: bool
     * }>
     */
    private function buildDateOptions(Carbon $startDate): array
    {
        return collect(range(0, 6))
            ->map(function (int $offset) use ($startDate) {
                $date = $startDate->copy()->addDays($offset);

                return [
                    'label' => $this->formatIndonesianDate($date),
                    'day' => $this->translateDay($date->format('l')),
                    'date' => sprintf('%s %s', $date->format('j'), $this->translateMonthShort($date->format('F'))),
                    'is_active' => $offset === 0,
                ];
            })
            ->all();
    }

    private function formatIndonesianDate(Carbon $date): string
    {
        return sprintf(
            '%s, %s %s',
            $this->translateDay($date->format('l')),
            $date->format('j'),
            $this->translateMonthShort($date->format('F'))
        );
    }

    private function translateDay(string $day): string
    {
        return [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ][$day] ?? $day;
    }

    private function translateMonthShort(string $month): string
    {
        return [
            'January' => 'Jan',
            'February' => 'Feb',
            'March' => 'Mar',
            'April' => 'Apr',
            'May' => 'Mei',
            'June' => 'Jun',
            'July' => 'Jul',
            'August' => 'Agu',
            'September' => 'Sep',
            'October' => 'Okt',
            'November' => 'Nov',
            'December' => 'Des',
        ][$month] ?? $month;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildSampleRecommendations(string $originName, string $destinationName): array
    {
        $baseLegs = [
            [
                'mode_icon' => 'commuter',
                'mode_label' => 'KRL',
                'badge_class' => 'from-sky-500 to-indigo-500',
                'departure' => [
                    'time' => '21.15',
                    'station' => 'Cikarang',
                ],
                'arrival' => [
                    'time' => '22.45',
                    'station' => 'Bekasi',
                    'type' => 'transfer',
                ],
            ],
            [
                'mode_icon' => 'train',
                'mode_label' => 'KA Menoreh',
                'badge_class' => 'from-indigo-500 to-purple-500',
                'departure' => [
                    'time' => '23.10',
                    'station' => 'Bekasi',
                ],
                'arrival' => [
                    'time' => '05.35',
                    'station' => $destinationName,
                    'type' => 'destination',
                ],
            ],
        ];

        $secondLegs = [
            [
                'mode_icon' => 'commuter',
                'mode_label' => 'KRL',
                'badge_class' => 'from-sky-500 to-indigo-500',
                'departure' => [
                    'time' => '16.05',
                    'station' => 'Manggarai',
                ],
                'arrival' => [
                    'time' => '17.20',
                    'station' => 'Bekasi',
                    'type' => 'transfer',
                ],
            ],
            [
                'mode_icon' => 'train',
                'mode_label' => 'KA Argo Bromo',
                'badge_class' => 'from-purple-500 to-pink-500',
                'departure' => [
                    'time' => '17.45',
                    'station' => 'Bekasi',
                ],
                'arrival' => [
                    'time' => '23.55',
                    'station' => $destinationName,
                    'type' => 'destination',
                ],
            ],
        ];

        $thirdLegs = [
            [
                'mode_icon' => 'commuter',
                'mode_label' => 'LRT',
                'badge_class' => 'from-emerald-500 to-cyan-500',
                'departure' => [
                    'time' => '06.30',
                    'station' => $originName,
                ],
                'arrival' => [
                    'time' => '07.05',
                    'station' => 'Manggarai',
                    'type' => 'transfer',
                ],
            ],
            [
                'mode_icon' => 'train',
                'mode_label' => 'KA Taksaka',
                'badge_class' => 'from-indigo-600 to-blue-500',
                'departure' => [
                    'time' => '07.30',
                    'station' => 'Manggarai',
                ],
                'arrival' => [
                    'time' => '13.40',
                    'station' => $destinationName,
                    'type' => 'destination',
                ],
            ],
        ];

        return [
            [
                'title' => 'Rekomendasi Rute 1',
                'price' => 465_000,
                'legs' => $baseLegs,
                'destination_label' => $destinationName,
            ],
            [
                'title' => 'Rekomendasi Rute 2',
                'price' => 520_000,
                'legs' => $secondLegs,
                'destination_label' => $destinationName,
            ],
            [
                'title' => 'Rekomendasi Rute 3',
                'price' => 430_000,
                'legs' => $thirdLegs,
                'destination_label' => $destinationName,
            ],
        ];
    }
}
