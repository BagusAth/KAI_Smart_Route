<?php

namespace App\Http\Controllers;

use App\Models\Route as TrainRoute;
use App\Models\Schedule;
use App\Models\Station;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RouteRecommendationController extends Controller
{
    private const SESSION_KEY = 'route_recommendations_payload';

    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('get')) {
            $payload = $request->session()->get(self::SESSION_KEY);

            if (!$payload) {
                return redirect()->route('home')->with('status', 'Silakan lakukan pencarian rute terlebih dahulu.');
            }

            return view('routes', $payload);
        }

        $validated = $request->validate(
            [
                'origin' => ['required', 'string', 'max:10'],
                'origin_label' => ['required', 'string', 'max:255'],
                'destination' => ['required', 'string', 'max:10'],
                'destination_label' => ['required', 'string', 'max:255'],
                'departure_date' => ['required', 'date', 'after_or_equal:today'],
                'passengers' => ['required', 'integer', 'min:1', 'max:10'],
            ],
            [],
            [
                'origin' => 'stasiun keberangkatan',
                'origin_label' => 'nama stasiun keberangkatan',
                'destination' => 'stasiun tujuan',
                'destination_label' => 'nama stasiun tujuan',
                'departure_date' => 'tanggal keberangkatan',
                'passengers' => 'jumlah penumpang dewasa',
            ]
        );

        $originStation = Station::query()->where('code', $validated['origin'])->first();
        $destinationStation = Station::query()->where('code', $validated['destination'])->first();

        $originName = $originStation?->name ?? $validated['origin_label'];
        $destinationName = $destinationStation?->name ?? $validated['destination_label'];

        $selectedDate = Carbon::parse($validated['departure_date'])->startOfDay();
        $today = Carbon::now()->startOfDay();
        $baseDate = $selectedDate->greaterThan($today) ? $selectedDate : $today;
        $dateOptions = $this->buildDateOptions($baseDate, $selectedDate);

        $passengerCount = (int) $validated['passengers'];

        $request->session()->put('reservation.passenger_count', $passengerCount);
        $request->session()->forget('reservation.passengers');

        $directRecommendations = $this->buildDirectRecommendations($originStation, $destinationStation);
        $multiModalRecommendations = $this->buildMultiModalRecommendations($originName, $destinationName);

        $viewData = [
            'searchSummary' => [
                'title' => sprintf('%s - %s', $originName, $destinationName),
                'passengers' => sprintf('%d Dewasa', $validated['passengers']),
                'departure_date' => $this->formatIndonesianDate($selectedDate),
            ],
            'dateOptions' => $dateOptions,
            'directRecommendations' => $directRecommendations,
            'multiModalRecommendations' => $multiModalRecommendations,
            'reservation' => [
                'passenger_count' => $passengerCount,
            ],
        ];

        $request->session()->put(self::SESSION_KEY, $viewData);

        return redirect()->route('routes.recommend.show');
    }

    /**
     * @return array<int, array{
     *     label: string,
    *     day: string,
    *     date: string,
    *     value: string,
    *     is_active: bool
     * }>
     */
    private function buildDateOptions(Carbon $startDate, Carbon $selectedDate): array
    {
        return collect(range(0, 6))
            ->map(function (int $offset) use ($startDate) {
                $date = $startDate->copy()->addDays($offset)->startOfDay();

                return [
                    'label' => $this->formatIndonesianDate($date),
                    'day' => $this->translateDay($date->format('l')),
                    'date' => sprintf('%s %s', $date->format('j'), $this->translateMonthShort($date->format('F'))),
                    'value' => $date->format('Y-m-d'),
                    'is_active' => false,
                ];
            })
            ->map(function (array $option) use ($selectedDate) {
                $option['is_active'] = $option['value'] === $selectedDate->format('Y-m-d');

                return $option;
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
    private function buildDirectRecommendations(?Station $origin, ?Station $destination): array
    {
        if (! $origin || ! $destination) {
            return [];
        }

        $originRoutes = TrainRoute::query()
            ->where('station_id', $origin->id)
            ->get()
            ->keyBy('train_id');

        if ($originRoutes->isEmpty()) {
            return [];
        }

        $destinationRoutes = TrainRoute::query()
            ->whereIn('train_id', $originRoutes->keys())
            ->where('station_id', $destination->id)
            ->get()
            ->keyBy('train_id');

        if ($destinationRoutes->isEmpty()) {
            return [];
        }

        $trainIds = $destinationRoutes->keys();

        $trains = Train::query()
            ->whereIn('id', $trainIds)
            ->get()
            ->keyBy('id');

        $originSchedules = Schedule::query()
            ->whereIn('train_id', $trainIds)
            ->where('station_id', $origin->id)
            ->get()
            ->keyBy('train_id');

        $destinationSchedules = Schedule::query()
            ->whereIn('train_id', $trainIds)
            ->where('station_id', $destination->id)
            ->get()
            ->keyBy('train_id');

        $recommendations = [];

        foreach ($destinationRoutes as $trainId => $destinationRoute) {
            $originRoute = $originRoutes[$trainId] ?? null;
            $train = $trains[$trainId] ?? null;

            if (! $originRoute || ! $train) {
                continue;
            }

            $ticketClass = $this->determinePrimaryClassForTrain($train);

            if ($originRoute->stop_order >= $destinationRoute->stop_order) {
                continue;
            }

            $originSchedule = $originSchedules[$trainId] ?? null;
            $destinationSchedule = $destinationSchedules[$trainId] ?? null;

            $departureTime = $originSchedule?->departure;
            $arrivalTime = $destinationSchedule?->arrival;
            $price = $originSchedule?->price ?? $destinationSchedule?->price ?? null;

            $durationLabel = null;

            if ($departureTime && $arrivalTime) {
                try {
                    $departureMoment = Carbon::createFromFormat('H:i', $departureTime);
                    $arrivalMoment = Carbon::createFromFormat('H:i', $arrivalTime);

                    if ($arrivalMoment->lessThanOrEqualTo($departureMoment)) {
                        $arrivalMoment = $arrivalMoment->addDay();
                    }

                    $durationMinutes = $departureMoment->diffInMinutes($arrivalMoment);
                    $hours = intdiv($durationMinutes, 60);
                    $minutes = $durationMinutes % 60;
                    $durationLabel = sprintf('%dj %02dm', $hours, $minutes);
                } catch (\Throwable $e) {
                    $durationLabel = null;
                }
            }

            $recommendations[] = [
                'title' => $train->name ?? $train->code ?? 'Kereta Langsung',
                'train_code' => $train->code ?? null,
                'train_name' => $train->name ?? null,
                'ticket_class' => $ticketClass,
                'price' => $price,
                'duration_label' => $durationLabel,
                'legs' => [[
                    'mode_icon' => 'train',
                    'mode_label' => $train->name ?? 'Kereta',
                    'transport_name' => $train->code ?? $train->name ?? 'Kereta',
                    'badge_class' => 'from-indigo-500 to-purple-500',
                    'departure' => [
                        'time' => $departureTime,
                        'station' => $origin->name,
                    ],
                    'arrival' => [
                        'time' => $arrivalTime,
                        'station' => $destination->name,
                        'type' => 'destination',
                    ],
                    'notes' => 'Perjalanan langsung tanpa transit',
                ]],
                'destination_label' => $destination->name,
            ];
        }

        return collect($recommendations)
            ->sortBy(fn (array $item) => $item['legs'][0]['departure']['time'] ?? '99:99')
            ->values()
            ->all();
    }

    private function buildMultiModalRecommendations(string $originName, string $destinationName): array
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
                'train_code' => 'KA-MTRJ',
                'train_name' => 'Matarmaja',
                'ticket_class' => 'Ekonomi',
            ],
            [
                'title' => 'Rekomendasi Rute 2',
                'price' => 520_000,
                'legs' => $secondLegs,
                'destination_label' => $destinationName,
                'train_code' => 'KA-ARPA',
                'train_name' => 'Argo Parahyangan',
                'ticket_class' => 'Ekonomi Premium',
            ],
            [
                'title' => 'Rekomendasi Rute 3',
                'price' => 430_000,
                'legs' => $thirdLegs,
                'destination_label' => $destinationName,
                'train_code' => 'KA-TAKA',
                'train_name' => 'Taksaka',
                'ticket_class' => 'Eksekutif',
            ],
        ];
    }

    private function determinePrimaryClassForTrain(Train $train): string
    {
        $class = $train->class ?? '';

        if ($class === '') {
            return 'Eksekutif';
        }

        if (str_contains($class, '&')) {
            $parts = array_map('trim', explode('&', $class));
            return $parts[0] ?: 'Eksekutif';
        }

        return trim($class) ?: 'Eksekutif';
    }
}
