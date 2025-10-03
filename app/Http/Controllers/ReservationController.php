<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservationController extends Controller
{
    private const PASSENGER_COUNT_SESSION_KEY = 'reservation.passenger_count';
    private const PASSENGER_DATA_SESSION_KEY = 'reservation.passengers';
    private const SEAT_SELECTION_SESSION_KEY = 'reservation.seats';
    private const ROUTE_SELECTION_SESSION_KEY = 'reservation.selection';

    private const SEAT_CLASS_LAYOUTS = [
        'Ekonomi Premium' => [
            'columns' => ['A', 'B', 'C', 'D'],
            'coaches' => [
                ['code' => 'EP-1', 'label' => 'Ekonomi Premium 1', 'rows' => 10, 'blocked' => ['A1', 'B1', 'C2', 'D2', 'B4', 'D4', 'A7', 'B7', 'C9', 'D9']],
                ['code' => 'EP-2', 'label' => 'Ekonomi Premium 2', 'rows' => 10, 'blocked' => ['A3', 'B3', 'C3', 'D3', 'A5', 'D5', 'A8', 'B8', 'C10', 'D10']],
                ['code' => 'EP-3', 'label' => 'Ekonomi Premium 3', 'rows' => 10, 'blocked' => ['A1', 'B1', 'C1', 'D1', 'A2', 'B2', 'D2', 'A4', 'D4', 'A6', 'B6', 'C6', 'B9', 'D9']],
                ['code' => 'EP-4', 'label' => 'Ekonomi Premium 4', 'rows' => 10, 'blocked' => ['A2', 'C2', 'A5', 'B5', 'C5', 'D5', 'B7', 'C7', 'A9', 'D9']],
            ],
        ],
        'Eksekutif' => [
            'columns' => ['A', 'B', 'C', 'D'],
            'coaches' => [
                ['code' => 'EX-1', 'label' => 'Eksekutif 1', 'rows' => 8, 'blocked' => ['A1', 'B1', 'C1', 'D1', 'A4', 'D4']],
                ['code' => 'EX-2', 'label' => 'Eksekutif 2', 'rows' => 8, 'blocked' => ['B2', 'C2', 'A5', 'C5', 'D5']],
                ['code' => 'EX-3', 'label' => 'Eksekutif 3', 'rows' => 8, 'blocked' => ['A3', 'D3', 'B6', 'C6', 'B7', 'C7']],
            ],
        ],
        'Ekonomi' => [
            'columns' => ['A', 'B', 'C', 'D'],
            'coaches' => [
                ['code' => 'EKO-1', 'label' => 'Ekonomi 1', 'rows' => 12, 'blocked' => ['A1', 'B1', 'C1', 'D1', 'A2', 'D2', 'B3', 'C3']],
                ['code' => 'EKO-2', 'label' => 'Ekonomi 2', 'rows' => 12, 'blocked' => ['A4', 'B4', 'C4', 'D4', 'A7', 'B7']],
                ['code' => 'EKO-3', 'label' => 'Ekonomi 3', 'rows' => 12, 'blocked' => ['C5', 'D5', 'A6', 'B6', 'C9', 'D9']],
                ['code' => 'EKO-4', 'label' => 'Ekonomi 4', 'rows' => 12, 'blocked' => ['A10', 'B10', 'C11', 'D11', 'A12', 'D12']],
            ],
        ],
    ];

    public function show(Request $request): View|RedirectResponse
    {
        $passengerCount = max(1, (int) $request->session()->get(self::PASSENGER_COUNT_SESSION_KEY, 1));
        $storedPassengers = $request->session()->get(self::PASSENGER_DATA_SESSION_KEY, []);

        $passengerForms = Collection::times($passengerCount, function (int $number) use ($storedPassengers) {
            $index = $number - 1;

            return [
                'index' => $index,
                'number' => $number,
                'fields' => [
                    'full_name' => [
                        'name' => "passengers[{$index}][full_name]",
                        'value' => old("passengers.{$index}.full_name") ?? data_get($storedPassengers, "{$index}.full_name"),
                        'field_key' => "passengers.{$index}.full_name",
                        'error_id' => "passengers-{$index}-full_name-error",
                    ],
                    'national_id' => [
                        'name' => "passengers[{$index}][national_id]",
                        'value' => old("passengers.{$index}.national_id") ?? data_get($storedPassengers, "{$index}.national_id"),
                        'field_key' => "passengers.{$index}.national_id",
                        'error_id' => "passengers-{$index}-national_id-error",
                    ],
                    'phone_number' => [
                        'name' => "passengers[{$index}][phone_number]",
                        'value' => old("passengers.{$index}.phone_number") ?? data_get($storedPassengers, "{$index}.phone_number"),
                        'field_key' => "passengers.{$index}.phone_number",
                        'error_id' => "passengers-{$index}-phone_number-error",
                    ],
                ],
            ];
        });

        return view('reservasi', [
            'passengerCount' => $passengerCount,
            'passengerForms' => $passengerForms,
            'noticeMessage' => session('reservation_notice'),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $passengerCount = max(1, (int) $request->session()->get(self::PASSENGER_COUNT_SESSION_KEY, 1));

        $validated = $request->validate(
            [
                'passengers' => ['required', 'array', 'size:' . $passengerCount],
                'passengers.*.full_name' => ['required', 'string', 'min:3', 'max:120'],
                'passengers.*.national_id' => ['required', 'digits:16'],
                'passengers.*.phone_number' => ['required', 'regex:/^0\d{8,14}$/'],
            ],
            [],
            [
                'passengers.*.full_name' => 'nama penumpang',
                'passengers.*.national_id' => 'NIK',
                'passengers.*.phone_number' => 'nomor handphone',
            ]
        );

        $selectionPayload = $this->decodeSelectionPayload($request->input('selection_payload'));

        $request->session()->put(self::PASSENGER_DATA_SESSION_KEY, $validated['passengers']);
        $request->session()->forget(self::SEAT_SELECTION_SESSION_KEY);

        if (!empty($selectionPayload)) {
            $request->session()->put(self::ROUTE_SELECTION_SESSION_KEY, $selectionPayload);
        }

        return redirect()->route('reservasi.confirm');
    }

    public function confirm(Request $request): View|RedirectResponse
    {
        $passengers = collect($request->session()->get(self::PASSENGER_DATA_SESSION_KEY, []));

        if ($passengers->isEmpty()) {
            return redirect()->route('reservasi.show')->with('reservation_notice', 'Silakan lengkapi data penumpang terlebih dahulu.');
        }

        $routeSelection = collect($request->session()->get(self::ROUTE_SELECTION_SESSION_KEY, []));
        $ticketClass = $this->resolveTicketClass($routeSelection);

        $storedSeats = $this->normalizeStoredSeats(collect($request->session()->get(self::SEAT_SELECTION_SESSION_KEY, [])));
        $seatConfig = $this->buildSeatConfiguration($ticketClass, $storedSeats);

        $coachLookup = collect($seatConfig['coaches'] ?? [])->keyBy(fn ($coach) => $coach['code']);
        $enhancedSeats = $storedSeats->map(function (array $assignment) use ($coachLookup) {
            $coachInfo = $coachLookup->get($assignment['coach']);

            return array_merge($assignment, [
                'coach_label' => $coachInfo['label'] ?? $assignment['coach'],
                'seat_label' => $this->formatSeatLabel($coachInfo, $assignment['seat']),
            ]);
        })->values();

        $seatsByPassenger = $enhancedSeats->keyBy('passenger_index');

        $passengerView = $passengers->values()->map(function (array $passenger, int $index) use ($seatsByPassenger) {
            $seatAssignment = $seatsByPassenger->get($index);

            return [
                'index' => $index,
                'number' => $index + 1,
                'full_name' => $passenger['full_name'] ?? '',
                'national_id' => $passenger['national_id'] ?? '',
                'phone_number' => $passenger['phone_number'] ?? '',
                'seat_assignment' => $seatAssignment,
                'seat_label' => $seatAssignment['seat_label'] ?? null,
            ];
        })->values();

        $allSeatsSelected = $passengerView->every(fn (array $passenger) => !empty($passenger['seat_assignment']));

        return view('reservasi-konfirmasi', [
            'passengers' => $passengerView,
            'seatConfig' => $seatConfig,
            'selectedSeats' => $enhancedSeats,
            'passengerCount' => $passengerView->count(),
            'allSeatsSelected' => $allSeatsSelected,
            'seatClass' => $ticketClass,
            'routeSelection' => $routeSelection,
        ]);
    }

    public function storeSeats(Request $request): RedirectResponse
    {
        $passengers = collect($request->session()->get(self::PASSENGER_DATA_SESSION_KEY, []));

        if ($passengers->isEmpty()) {
            return redirect()->route('reservasi.show')->with('reservation_notice', 'Silakan lengkapi data penumpang terlebih dahulu.');
        }

        $passengerCount = $passengers->count();
        $routeSelection = collect($request->session()->get(self::ROUTE_SELECTION_SESSION_KEY, []));
        $ticketClass = $this->resolveTicketClass($routeSelection);
        $storedSeats = $this->normalizeStoredSeats(collect($request->session()->get(self::SEAT_SELECTION_SESSION_KEY, [])));
        $seatConfig = $this->buildSeatConfiguration($ticketClass, $storedSeats);

        $coachLookup = collect($seatConfig['coaches'] ?? [])->keyBy(fn ($coach) => $coach['code']);
        $coachCodes = $coachLookup->keys()->all();

        $validated = $request->validate(
            [
                'seats' => ['required', 'array', 'size:' . $passengerCount],
                'seats.*.coach' => ['required', 'string'],
                'seats.*.seat' => ['required', 'string'],
            ],
            [],
            [
                'seats' => 'pilihan kursi',
                'seats.*.coach' => 'gerbong',
                'seats.*.seat' => 'kursi',
            ]
        );

        $seatStatusMap = [];
        foreach ($seatConfig['coaches'] as $coach) {
            $code = $coach['code'];
            foreach ($coach['rows'] as $row) {
                foreach ($row['seats'] as $seat) {
                    $seatStatusMap[$code][$seat['code']] = [
                        'status' => $seat['status'],
                        'passenger_index' => $seat['passenger_index'],
                        'coach_label' => $coach['label'],
                    ];
                }
            }
        }

        $errors = [];
        $usedSeats = [];
        $normalizedSelections = [];

        foreach ($validated['seats'] as $index => $selection) {
            $coachCode = strtoupper($selection['coach']);
            $seatCode = strtoupper($selection['seat']);

            if (!\in_array($coachCode, $coachCodes, true)) {
                $errors["seats.$index.coach"] = 'Gerbong tidak tersedia untuk kelas tiket ini.';
                continue;
            }

            if (!isset($seatStatusMap[$coachCode][$seatCode])) {
                $errors["seats.$index.seat"] = 'Kursi tidak tersedia pada gerbong terpilih.';
                continue;
            }

            $seatInfo = $seatStatusMap[$coachCode][$seatCode];

            if ($seatInfo['status'] === 'blocked') {
                $errors["seats.$index.seat"] = 'Kursi sudah tidak tersedia.';
                continue;
            }

            if ($seatInfo['status'] === 'selected' && $seatInfo['passenger_index'] !== $index) {
                $errors["seats.$index.seat"] = 'Kursi sudah dipilih oleh penumpang lain.';
                continue;
            }

            $comboKey = $coachCode . '::' . $seatCode;
            if (isset($usedSeats[$comboKey])) {
                $errors["seats.$index.seat"] = 'Kursi tidak boleh dipilih ganda.';
                continue;
            }

            $usedSeats[$comboKey] = true;

            $normalizedSelections[$index] = [
                'coach' => $coachCode,
                'coach_label' => $seatInfo['coach_label'],
                'seat' => $seatCode,
                'seat_label' => $this->formatSeatLabel([
                    'code' => $coachCode,
                    'label' => $seatInfo['coach_label'],
                ], $seatCode),
                'passenger_index' => $index,
            ];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        ksort($normalizedSelections);
        $request->session()->put(self::SEAT_SELECTION_SESSION_KEY, array_values($normalizedSelections));

        return redirect()->route('reservasi.confirm')->with('seat_success', 'Pilihan kursi berhasil disimpan.');
    }

    public function payment(Request $request): View|RedirectResponse
    {
        $passengers = collect($request->session()->get(self::PASSENGER_DATA_SESSION_KEY, []));

        if ($passengers->isEmpty()) {
            return redirect()->route('reservasi.show');
        }

        $seats = collect($request->session()->get(self::SEAT_SELECTION_SESSION_KEY, []))->values();

        $passengerView = $passengers->values()->map(function (array $passenger, int $index) use ($seats) {
            $seat = $seats->get($index);

            return [
                'number' => $index + 1,
                'full_name' => $passenger['full_name'] ?? '',
                'seat' => $seat['seat_label'] ?? ($seat['seat'] ?? null),
            ];
        });

        $hasSeatSelection = $passengerView->every(fn (array $passenger) => !empty($passenger['seat']));

        return view('pembayaran', [
            'passengers' => $passengerView,
            'hasSeatSelection' => $hasSeatSelection,
        ]);
    }

    private function decodeSelectionPayload(?string $payload): array
    {
        if ($payload === null || $payload === '') {
            return [];
        }

        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            return [];
        }

        return \is_array($decoded) ? $decoded : [];
    }

    private function resolveTicketClass(Collection $selection): string
    {
        $value = trim((string) $selection->get('ticket_class', ''));

        if ($value === '') {
            return 'Eksekutif';
        }

        $normalized = mb_strtolower($value);

        return match ($normalized) {
            'ekonomi premium', 'premium economy', 'premium' => 'Ekonomi Premium',
            'eksekutif', 'executive' => 'Eksekutif',
            'ekonomi', 'economy' => 'Ekonomi',
            'luxury', 'first', 'business' => 'Eksekutif',
            default => ucfirst($value),
        };
    }

    private function normalizeStoredSeats(Collection $seats): Collection
    {
        return $seats
            ->values()
            ->map(function ($seat, int $index) {
                if (!\is_array($seat)) {
                    return null;
                }

                $coach = strtoupper((string) ($seat['coach'] ?? ''));
                $seatCode = strtoupper((string) ($seat['seat'] ?? ($seat['seat_code'] ?? '')));

                if ($coach === '' || $seatCode === '') {
                    return null;
                }

                return [
                    'coach' => $coach,
                    'seat' => $seatCode,
                    'passenger_index' => $index,
                ];
            })
            ->filter()
            ->values();
    }

    private function buildSeatConfiguration(string $ticketClass, Collection $selectedSeats): array
    {
        $classLayout = self::SEAT_CLASS_LAYOUTS[$ticketClass] ?? self::SEAT_CLASS_LAYOUTS['Eksekutif'];
        $columns = array_map('strtoupper', $classLayout['columns'] ?? ['A', 'B', 'C', 'D']);

        $selectedKey = $selectedSeats
            ->map(fn ($item) => $item['coach'] . '::' . $item['seat'])
            ->flip();

        $seatAssignments = $selectedSeats->keyBy(fn (array $seat) => $seat['coach'] . '::' . $seat['seat']);

        $coaches = [];

        foreach ($classLayout['coaches'] as $coachLayout) {
            $coachCode = strtoupper($coachLayout['code']);
            $coachLabel = $coachLayout['label'] ?? $coachCode;
            $rows = max(1, (int) ($coachLayout['rows'] ?? 10));
            $blockedSeats = collect($coachLayout['blocked'] ?? [])->map(fn ($seat) => strtoupper($seat))->all();

            $rowCollection = [];
            $availableCount = 0;
            $totalCount = 0;

            for ($row = 1; $row <= $rows; $row++) {
                $seatRow = [];

                foreach ($columns as $column) {
                    $seatCode = $column . $row;
                    $comboKey = $coachCode . '::' . $seatCode;

                    $status = 'available';
                    $passengerIndex = null;

                    if (\in_array($seatCode, $blockedSeats, true)) {
                        $status = 'blocked';
                    } elseif ($selectedKey->has($comboKey)) {
                        $status = 'selected';
                        $passengerIndex = $seatAssignments[$comboKey]['passenger_index'] ?? null;
                    } else {
                        $availableCount++;
                    }

                    $totalCount++;

                    $seatRow[] = [
                        'code' => $seatCode,
                        'status' => $status,
                        'passenger_index' => $passengerIndex,
                    ];
                }

                $rowCollection[] = [
                    'number' => $row,
                    'seats' => $seatRow,
                ];
            }

            $coaches[] = [
                'code' => $coachCode,
                'label' => $coachLabel,
                'rows' => $rowCollection,
                'available' => $availableCount,
                'total' => $totalCount,
            ];
        }

        return [
            'class' => $ticketClass,
            'columns' => $columns,
            'coaches' => $coaches,
        ];
    }

    private function formatSeatLabel(?array $coach, string $seatCode): string
    {
        $coachName = trim((string) ($coach['label'] ?? $coach['code'] ?? 'Gerbong'));

        return sprintf('%s · Kursi %s', $coachName, strtoupper($seatCode));
    }
}
