<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name', 'RailLink') }} &middot; Rekomendasi Rute</title>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link
			href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
			rel="stylesheet"
		/>

		@vite(['resources/css/app.css', 'resources/js/app.js'])
		<link rel="stylesheet" href="{{ asset('css/routes.css') }}">
	</head>
	<body class="routes-page-body min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-100 text-slate-900 antialiased">
		<div class="relative flex min-h-screen flex-col">
			<div class="pointer-events-none absolute inset-x-0 top-0 z-0 h-72 bg-gradient-to-b from-indigo-200/60 via-white to-transparent blur-2xl"></div>
			@include('header', [
				'headerClasses' => 'sticky top-0 z-50 border-b border-white/40 bg-white/80 backdrop-blur',
				'navClasses' => 'mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8 text-slate-600',
			])

			<main class="relative z-10 flex-1">
				<section class="mx-auto max-w-6xl px-6 py-12 lg:px-8 lg:py-16">
					<div class="flex flex-col gap-3">
						<h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
							{{ $searchSummary['title'] ?? 'Rute Terpilih' }}
						</h1>
						<div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
							<span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 font-medium text-slate-600 shadow-sm">
								<svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 4.5v-1.5m10.5 1.5v-1.5M4.5 7.5h15m-13.5 3h12a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5h-12a1.5 1.5 0 01-1.5-1.5V12a1.5 1.5 0 011.5-1.5z" />
								</svg>
								{{ $searchSummary['departure_date'] ?? '' }}
							</span>
							<span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 font-medium text-slate-600 shadow-sm">
								<svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M5.5 21h13M5.75 7.5h12.5M4.5 3h15m-1.75 4.5L16.5 21m-9-13.5L7.5 21" />
								</svg>
								{{ $searchSummary['passengers'] ?? '1 Dewasa' }}
							</span>
						</div>
					</div>

					<div class="mt-8 flex flex-wrap gap-3">
						@foreach ($dateOptions as $option)
							<button
								type="button"
								class="date-option-button inline-flex items-center justify-center gap-2 rounded-full border px-5 py-2.5 text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-indigo-200/60 {{ $option['is_active'] ? 'is-active' : '' }}"
								data-date-option
								data-date-value="{{ $option['date'] }}"
								data-date-day="{{ $option['day'] }}"
								aria-pressed="{{ $option['is_active'] ? 'true' : 'false' }}"
							>
								<span>{{ $option['day'] }}</span>
								<span class="text-xs font-medium uppercase tracking-wide">{{ $option['date'] }}</span>
							</button>
						@endforeach
					</div>

					<div class="mt-12 space-y-8">
						@foreach ($recommendations as $recommendation)
							@php
								$legs = $recommendation['legs'] ?? [];
								$destinationLabel = $recommendation['destination_label'] ?? 'Tujuan Akhir';
								$segmentsNodes = collect($legs)
									->map(fn ($leg) => [
										'icon' => $leg['mode_icon'] ?? 'train',
										'label' => $leg['mode_label'] ?? 'Leg',
										'badge' => $leg['badge_class'] ?? 'from-indigo-500 to-purple-500',
									])
									->push([
										'icon' => 'destination',
										'label' => 'Tujuan',
										'badge' => 'destination',
									])
									->all();

								$events = [];
								if (!empty($legs)) {
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

								$timelineItems = collect($segmentsNodes)
									->map(function ($node, $index) use ($events) {
										$node['event'] = $events[$index] ?? null;
										return $node;
									})
									->all();
							@endphp

							<article class="route-card border border-indigo-100 bg-white/90 p-8 backdrop-blur transition duration-500">
								<div class="flex flex-wrap items-start justify-between gap-6">
									<div>
										<p class="route-card-label text-sm font-semibold uppercase text-indigo-500">{{ $recommendation['title'] ?? 'Rekomendasi Rute' }}</p>
										<h2 class="mt-2 text-xl font-semibold text-slate-900">Perjalanan Multi-Moda</h2>
									</div>
									<div class="text-right">
										<span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Harga</span>
										<p class="mt-1 text-2xl font-bold text-slate-900">Rp {{ number_format($recommendation['price'] ?? 0, 0, ',', '.') }}</p>
									</div>
								</div>

								<div class="mt-8 space-y-6">
									<div class="flex w-full flex-col gap-6 sm:flex-row sm:items-start sm:gap-6">
										@foreach ($timelineItems as $item)
											@php
												$isDestination = ($item['icon'] ?? '') === 'destination';
												$badgeClasses = $isDestination
													? 'border border-indigo-100 bg-white text-indigo-600'
													: 'bg-gradient-to-br '.($item['badge'] ?? 'from-indigo-500 to-purple-500').' text-white shadow-lg';
												$event = $item['event'] ?? null;
												$eventBulletClass = match ($event['type'] ?? null) {
													'destination' => 'border-indigo-500 bg-indigo-500',
													'origin' => 'border-indigo-400 bg-indigo-400',
													default => 'border-indigo-200 bg-white',
												};
											@endphp
											<div class="flex w-full items-start gap-4 sm:flex-1">
												<div class="flex flex-col items-center text-center">
													<span class="flex h-12 w-12 items-center justify-center rounded-full {{ $badgeClasses }}">
														@if (($item['icon'] ?? '') === 'commuter')
															<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" d="M8.5 3h7a3.5 3.5 0 013.5 3.5v5.75a3.5 3.5 0 01-3.5 3.5h-7a3.5 3.5 0 01-3.5-3.5V6.5A3.5 3.5 0 018.5 3z" />
																<path stroke-linecap="round" stroke-linejoin="round" d="M6 17.25h12M7.5 20h2m5 0h2" />
															</svg>
														@elseif (($item['icon'] ?? '') === 'train')
															<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" d="M9 21h6m-9-4h12a2 2 0 002-2V6a5 5 0 00-5-5H8a5 5 0 00-5 5v9a2 2 0 002 2zm0-9h12" />
															</svg>
														@else
															<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-4.686-6-10a6 6 0 1112 0c0 5.314-6 10-6 10z" />
																<path stroke-linecap="round" stroke-linejoin="round" d="M12 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
															</svg>
														@endif
													</span>
													<span class="mt-2 text-[10px] font-semibold uppercase tracking-wide {{ $isDestination ? 'text-indigo-500' : 'text-indigo-400' }}">
														{{ $item['label'] ?? '' }}
													</span>
													@if ($event)
														<div class="mt-6 flex flex-col items-center gap-1">
															<span class="inline-flex h-3 w-3 items-center justify-center rounded-full border-2 {{ $eventBulletClass }}"></span>
															<span class="text-sm font-semibold text-slate-900">{{ $event['time'] }}</span>
															<span class="text-xs text-slate-500">{{ $event['station'] }}</span>
														</div>
													@endif
												</div>
												@if (!$loop->last)
													<div class="hidden route-connector route-connector--full flex-1 self-center sm:block"></div>
													<div class="block route-connector route-connector--short self-center sm:hidden"></div>
												@endif
											</div>
										@endforeach
									</div>
								</div>

								<div class="mt-6 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-500">
									<div class="inline-flex items-center gap-2 rounded-full bg-indigo-50/70 px-4 py-2 text-indigo-600">
										<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 4h8m-8 4h5" />
										</svg>
										<span>Layanan onboard nyaman & wifi gratis</span>
									</div>
									<a href="#" class="inline-flex items-center gap-2 text-indigo-600 transition hover:text-indigo-700">
										<span>Detail perjalanan</span>
										<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
										</svg>
									</a>
								</div>
							</article>
						@endforeach
					</div>
				</section>
			</main>
		</div>
	<script src="{{ asset('js/routes.js') }}" defer></script>
	</body>
</html>
