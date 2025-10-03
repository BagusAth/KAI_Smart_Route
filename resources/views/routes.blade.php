<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name', 'KAIzen') }} &middot; Rekomendasi Rute</title>

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
					@php
						$timeOptions = [];
						for ($hour = 0; $hour < 24; $hour++) {
							foreach ([0, 30] as $minute) {
								$timeOptions[] = sprintf('%02d:%02d', $hour, $minute);
							}
						}
					@endphp
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

					<div class="mt-8 flex flex-wrap items-center gap-3">
						@foreach ($dateOptions as $option)
							<button
								type="button"
								class="date-option-button inline-flex items-center justify-center gap-2 rounded-full border px-5 py-2.5 text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-indigo-200/60 {{ $option['is_active'] ? 'is-active' : '' }}"
								data-date-option
								data-date-value="{{ $option['value'] }}"
								data-date-day="{{ $option['day'] }}"
								aria-pressed="{{ $option['is_active'] ? 'true' : 'false' }}"
							>
								<span>{{ $option['day'] }}</span>
								<span class="text-xs font-medium uppercase tracking-wide">{{ $option['date'] }}</span>
							</button>
						@endforeach

							<div class="route-filter relative" data-filter-root>
								<button
									type="button"
									class="route-filter-trigger inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-indigo-300 hover:text-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
									data-filter-toggle
									aria-expanded="false"
								>
									<svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M10 18h4" />
									</svg>
									<span>Filter</span>
								</button>
								<div class="route-filter-dropdown hidden" data-filter-dropdown>
									<div class="route-filter-header">
										<p class="route-filter-title">Jam</p>
										<p class="route-filter-subtitle">Pilih rentang jam keberangkatan yang diinginkan.</p>
									</div>
									<div class="route-filter-fields">
										<label class="route-filter-field">
											<span>Jam mulai</span>
											<select class="route-filter-select" data-filter-start>
												<option value="">Semua jam</option>
												@foreach ($timeOptions as $time)
													<option value="{{ $time }}">{{ $time }}</option>
												@endforeach
											</select>
										</label>
										<label class="route-filter-field">
											<span>Jam selesai</span>
											<select class="route-filter-select" data-filter-end>
												<option value="">Semua jam</option>
												@foreach ($timeOptions as $time)
													<option value="{{ $time }}">{{ $time }}</option>
												@endforeach
											</select>
										</label>
									</div>
									<p class="route-filter-error hidden" data-filter-error></p>
									<div class="route-filter-actions">
										<button type="button" class="route-filter-reset" data-filter-reset>Reset</button>
										<button type="button" class="route-filter-apply" data-filter-apply>Terapkan</button>
									</div>
								</div>
							</div>
					</div>

						@php
							$directRecommendations = $directRecommendations ?? [];
							$multiRecommendations = $multiModalRecommendations ?? ($recommendations ?? []);
							$hasDirect = !empty($directRecommendations);
							$hasMulti = !empty($multiRecommendations);
							$activeGroup = 'direct';
							$initialToggleLabel = $hasMulti ? 'Lihat koneksi multi-moda' : null;
							$directToggleLabel = $hasDirect ? 'Lihat kereta langsung' : 'Lihat info kereta langsung';
						@endphp

						<div
							class="mt-12 space-y-8"
							data-route-card-container
							data-active-group="{{ $activeGroup }}"
							data-reservation-url="{{ route('reservasi') }}"
							data-search-summary='@json($searchSummary, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
						>
							<div class="route-cards-group {{ $activeGroup === 'direct' ? 'is-active' : 'hidden' }}" data-route-group="direct">
								@if ($hasDirect)
									@foreach ($directRecommendations as $recommendation)
										@include('components.route-card', [
											'recommendation' => array_merge($recommendation, [
												'route_type' => 'direct',
												'subtitle' => $recommendation['subtitle'] ?? 'Perjalanan Langsung',
												'badge_caption' => $recommendation['badge_caption'] ?? 'Kereta langsung tanpa transit',
											]),
										])
									@endforeach
								@else
									<div class="route-empty-state" data-route-direct-empty>
										<h3 class="route-empty-state-title">Belum ada kereta langsung</h3>
										<p class="route-empty-state-description">
											Kami tidak menemukan perjalanan langsung dari {{ $searchSummary['title'] ?? 'stasiun asal' }} pada tanggal yang dipilih.
										</p>
										@if ($hasMulti)
											<p class="route-empty-state-description">Gunakan tombol di bawah untuk melihat rute multi-moda.</p>
										@endif
									</div>
								@endif
							</div>

							@if ($hasMulti)
								<div class="route-cards-group {{ $activeGroup === 'multi' ? 'is-active' : 'hidden' }}" data-route-group="multi">
									@foreach ($multiRecommendations as $recommendation)
										@include('components.route-card', [
											'recommendation' => array_merge($recommendation, [
												'route_type' => 'multi',
											]),
										])
									@endforeach
								</div>
							@endif

							<div class="route-empty-message hidden" data-filter-empty>Tidak ada jadwal untuk rentang waktu yang dipilih.</div>
						</div>

						@if ($hasMulti)
							<div class="mt-8 flex justify-center">
								<button
									type="button"
									class="route-connecting-button"
									data-route-toggle
									data-label-direct="{{ $directToggleLabel }}"
									data-label-multi="Lihat koneksi multi-moda"
								>
									{{ $initialToggleLabel }}
								</button>
							</div>
						@endif
				</section>
			</main>
		</div>
	<script src="{{ asset('js/routes.js') }}" defer></script>
	</body>
</html>
