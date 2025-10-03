@php
    $recommendation = $recommendation ?? [];
    $legs = $recommendation['legs'] ?? [];
    $destinationLabel = $recommendation['destination_label'] ?? 'Tujuan Akhir';
    $cardType = $recommendation['route_type'] ?? (count($legs) === 1 ? 'direct' : 'multi');

    $segmentsNodes = collect($legs)
        ->map(fn ($leg) => [
            'icon' => $leg['mode_icon'] ?? 'train',
            'label' => $leg['mode_label'] ?? 'Perjalanan',
            'badge' => $leg['badge_class'] ?? 'from-indigo-500 to-purple-500',
        ])
        ->push([
            'icon' => 'destination',
            'label' => 'Tujuan',
            'badge' => 'destination',
        ])
        ->all();

    $events = [];

    if (! empty($legs)) {
        $firstLeg = $legs[0];
        $events[] = [
            'time' => $firstLeg['departure']['time'] ?? '',
            'station' => $firstLeg['departure']['station'] ?? '',
            'type' => 'origin',
        ];

        foreach ($legs as $index => $leg) {
            $events[] = [
                'time' => $leg['arrival']['time'] ?? '',
                'station' => $leg['arrival']['station'] ?? '',
                'type' => $leg['arrival']['type'] ?? ($index === array_key_last($legs) ? 'destination' : 'transfer'),
            ];
        }
    }

    if (empty($segmentsNodes) && ! empty($destinationLabel)) {
        $segmentsNodes = [
            [
                'icon' => 'destination',
                'label' => 'Tujuan',
                'badge' => 'destination',
            ],
        ];
    }

    $timelineItems = collect($segmentsNodes)
        ->map(function ($item, $index) use ($events) {
            $event = $events[$index] ?? null;
            $isDestination = ($item['icon'] ?? '') === 'destination';
            $badgeClasses = $isDestination
                ? 'border border-indigo-100 bg-white text-indigo-600'
                : 'bg-gradient-to-br '.($item['badge'] ?? 'from-indigo-500 to-purple-500').' text-white shadow-lg';

            $eventBulletClass = match ($event['type'] ?? null) {
                'destination' => 'border-indigo-500 bg-indigo-500',
                'origin' => 'border-indigo-400 bg-indigo-400',
                default => 'border-indigo-200 bg-white',
            };

            return [
                'icon' => $item['icon'],
                'label' => $item['label'] ?? '',
                'badge_classes' => $badgeClasses,
                'event' => $event,
                'event_bullet_class' => $eventBulletClass,
                'is_destination' => $isDestination,
            ];
        })
        ->values()
        ->all();

    $detailTimeline = collect($legs)
        ->flatMap(function ($leg, $index) use ($legs) {
            $segments = [];

            $segments[] = [
                'time' => $leg['departure']['time'] ?? '',
                'station' => $leg['departure']['station'] ?? '',
                'label' => $leg['transport_name'] ?? $leg['mode_label'] ?? 'Perjalanan',
                'description' => $leg['notes'] ?? $leg['departure']['description'] ?? null,
                'type' => 'departure',
            ];

            $segments[] = [
                'time' => $leg['arrival']['time'] ?? '',
                'station' => $leg['arrival']['station'] ?? '',
                'label' => $index === array_key_last($legs) ? 'Tiba di tujuan' : 'Transit',
                'description' => $leg['arrival']['notes'] ?? null,
                'type' => $leg['arrival']['type'] ?? ($index === array_key_last($legs) ? 'arrival' : 'transfer'),
            ];

            return $segments;
        })
        ->values()
    ->all();

    $firstDeparture = $legs[0]['departure']['time'] ?? null;
    $price = $recommendation['price'] ?? null;
    $priceLabel = $price !== null ? number_format($price, 0, ',', '.') : '—';
    $durationLabel = $recommendation['duration_label'] ?? null;

    $selectionPayload = [
        'title' => $recommendation['title'] ?? 'Rekomendasi Rute',
        'subtitle' => $recommendation['subtitle'] ?? 'Perjalanan Multi-Moda',
        'type' => $cardType,
        'train_code' => $recommendation['train_code'] ?? null,
        'train_name' => $recommendation['train_name'] ?? null,
        'ticket_class' => $recommendation['ticket_class'] ?? null,
        'price' => $price,
        'price_label' => $priceLabel,
        'duration_label' => $durationLabel,
        'legs' => array_values($legs),
        'detail_timeline' => $detailTimeline,
        'destination_label' => $destinationLabel,
    ];
@endphp

<article
    class="route-card border border-indigo-100 bg-white/90 p-6 backdrop-blur transition duration-500"
    data-route-card
    data-route-type="{{ $cardType }}"
    data-departure-time="{{ $firstDeparture }}"
    data-route-card-state="collapsed"
>
    <div class="route-card-summary" data-route-card-toggle role="button" tabindex="0" aria-expanded="false">
        <div class="flex flex-wrap items-start justify-between gap-6">
            <div>
                <p class="route-card-label text-sm font-semibold uppercase text-indigo-500">{{ $recommendation['title'] ?? 'Rekomendasi Rute' }}</p>
                <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $recommendation['subtitle'] ?? 'Perjalanan Multi-Moda' }}</h2>
                @if ($durationLabel)
                    <p class="mt-1 text-sm text-slate-500">Estimasi durasi {{ $durationLabel }}</p>
                @endif
            </div>
            <div class="flex items-start gap-4">
                <div class="text-right">
                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Harga</span>
                    <p class="mt-1 text-2xl font-bold text-slate-900">Rp {{ $priceLabel }}</p>
                </div>
                <span class="route-card-toggle-icon" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                    </svg>
                </span>
            </div>
        </div>

        <div class="mt-6 space-y-6">
            <div class="flex w-full flex-col gap-6 sm:flex-row sm:items-start sm:gap-6">
                @foreach ($timelineItems as $item)
                    <div class="flex w-full items-start gap-4 sm:flex-1">
                        <div class="flex flex-col items-center text-center">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full {{ $item['badge_classes'] }}">
                                @if (($item['icon'] ?? '') === 'commuter')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 3h7a3.5 3.5 0 013.5 3.5v5.75a3.5 3.5 0 01-3.5 3.5h-7a3.5 3.5 0 01-3.5-3.5V6.5A3.5 3.5 0 018.5 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 17.25h12M7.5 20h2m5 0h2" />
                                    </svg>
                                @elseif (($item['icon'] ?? '') === 'train')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 21h6m-9-4h12a2 2 0 002-2V6a5 5 0 00-5-5H8a5 5 0 00-5 5v9a2 2 0 002 2zm0-9h12" />
                                    </svg>
                                @elseif (($item['icon'] ?? '') === 'destination')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-4.686-6-10a6 6 0 1112 0c0 5.314-6 10-6 10z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-4.686-6-10a6 6 0 1112 0c0 5.314-6 10-6 10z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                    </svg>
                                @endif
                            </span>
                            <span class="mt-2 text-[10px] font-semibold uppercase tracking-wide {{ $item['is_destination'] ? 'text-indigo-500' : 'text-indigo-400' }}">
                                {{ $item['label'] ?? '' }}
                            </span>
                            @if ($item['event'])
                                <div class="mt-6 flex flex-col items-center gap-1">
                                    <span class="inline-flex h-3 w-3 items-center justify-center rounded-full border-2 {{ $item['event_bullet_class'] }}" data-route-event-dot></span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $item['event']['time'] }}</span>
                                    <span class="text-xs text-slate-500">{{ $item['event']['station'] }}</span>
                                </div>
                            @endif
                        </div>
                        @if (! $loop->last)
                            <div class="route-connector route-connector--full route-connector--desktop flex-1" aria-hidden="true"></div>
                            <div class="route-connector route-connector--short route-connector--mobile" aria-hidden="true"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="route-card-footer mt-6 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-500">
        <div class="inline-flex items-center gap-2 rounded-full bg-indigo-50/70 px-4 py-2 text-indigo-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 4h8m-8 4h5" />
            </svg>
            <span>{{ $recommendation['badge_caption'] ?? 'Layanan onboard nyaman & wifi gratis' }}</span>
        </div>
        <button
            type="button"
            class="route-card-select"
            data-route-select
            data-route-payload='@json($selectionPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
        >Pilih</button>
    </div>

    <div class="route-card-details hidden" data-route-card-details>
    @if (count($detailTimeline) > 0)
            <ol class="route-card-detail-timeline">
        @foreach ($detailTimeline as $detail)
                    <li class="route-card-detail-item">
                        <div class="route-card-detail-dot" data-type="{{ $detail['type'] ?? 'transfer' }}"></div>
                        <div class="route-card-detail-content">
                            <div class="route-card-detail-header">
                                <span class="route-card-detail-time">{{ $detail['time'] }}</span>
                                <span class="route-card-detail-station">{{ $detail['station'] }}</span>
                            </div>
                            <p class="route-card-detail-label">{{ $detail['label'] }}</p>
                            @if (! empty($detail['description']))
                                <p class="route-card-detail-description">{{ $detail['description'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @else
            <p class="text-sm text-slate-500">Detail perjalanan akan tersedia setelah jadwal final dikonfirmasi.</p>
        @endif
    </div>
</article>
