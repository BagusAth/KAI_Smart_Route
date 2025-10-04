<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name', 'KAIzen') }} &middot; Konfirmasi Reservasi</title>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link
			href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
			rel="stylesheet"
		/>

		@vite(['resources/css/app.css', 'resources/js/app.js'])
		<link rel="stylesheet" href="{{ asset('css/reservasi-konfirmasi.css') }}">
	</head>
	<body
		class="reservation-confirm-body min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-100 text-slate-900 antialiased"
		data-passengers='@json($passengers->values())'
		data-seat-config='@json($seatConfig)'
		data-selected-seats='@json($selectedSeats->values())'
		data-route-selection='@json($routeSelection)'
		data-passenger-count="{{ $passengerCount }}"
		data-seat-class="{{ $seatClass }}"
	>
		<div class="relative flex min-h-screen flex-col">
			<div class="pointer-events-none absolute inset-x-0 top-0 z-0 h-72 bg-gradient-to-b from-indigo-200/60 via-white to-transparent blur-2xl"></div>

			@include('header', [
				'headerClasses' => 'sticky top-0 z-50 border-b border-white/40 bg-white/80 backdrop-blur',
				'navClasses' => 'mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8 text-slate-600',
			])

			<main class="relative z-10 flex-1">
				<section class="mx-auto max-w-6xl px-6 py-12 lg:px-8 lg:py-16">
					<div class="flex flex-col gap-3">
						<h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Konfirmasi Data Reservasi</h1>
						<p class="max-w-3xl text-sm text-slate-500">
							Tinjau kembali informasi penumpang di bawah ini. Pastikan seluruh data telah sesuai sebelum melanjutkan ke tahap pembayaran.
						</p>
					</div>

					<div class="confirmation-wrapper mt-10 grid gap-8 lg:grid-cols-[1fr_0.85fr]">
						<div>
							@if (session('seat_success'))
								<div class="confirmation-alert confirmation-alert--success" role="status">
									{{ session('seat_success') }}
								</div>
							@endif

							<article class="confirmation-card">
								<header class="confirmation-card-header">
									<div>
										<h2 class="confirmation-card-title">Detail Penumpang</h2>
										<p class="confirmation-card-subtitle">Periksa kembali data identitas setiap penumpang yang akan melakukan perjalanan.</p>
									</div>
									<button class="confirmation-seat-button" type="button" data-seat-open>
										<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5v14" />
										</svg>
										<span>Pilih Kursi</span>
									</button>
								</header>

								<div class="confirmation-passenger-list" data-passenger-summary>
									@foreach ($passengers as $passenger)
										<section class="confirmation-passenger-card" data-passenger-card="{{ $passenger['index'] }}">
											<header class="confirmation-passenger-header">
												<div>
													<p class="confirmation-passenger-label">Penumpang {{ $passenger['number'] }}</p>
													<p class="confirmation-passenger-name">{{ $passenger['full_name'] }}</p>
												</div>
												<span
													class="confirmation-passenger-seat"
													data-passenger-seat="{{ $passenger['index'] }}"
													data-seat-empty="{{ $passenger['seat_assignment'] ? 'false' : 'true' }}"
												>
													{{ $passenger['seat_label'] ?? 'Belum dipilih' }}
												</span>
											</header>

											<ul class="confirmation-passenger-meta">
												<li>
													<span class="confirmation-meta-label">NIK</span>
													<span class="confirmation-meta-value">{{ $passenger['national_id'] }}</span>
												</li>
												<li>
													<span class="confirmation-meta-label">No. Handphone</span>
													<span class="confirmation-meta-value">{{ $passenger['phone_number'] }}</span>
												</li>
											</ul>
										</section>
									@endforeach
								</div>

								<footer class="confirmation-card-footer">
									<div class="confirmation-card-note">
										<p>
											Data akan digunakan untuk verifikasi saat boarding. Jika ada kesalahan, klik tombol "Edit Data Penumpang" di bawah ini.
										</p>
									</div>
									<div class="confirmation-actions">
										<a class="confirmation-action-button confirmation-action-button--secondary" href="{{ route('reservasi.show') }}">
											Edit Data Penumpang
										</a>
										<a
											class="confirmation-action-button confirmation-action-button--primary {{ $allSeatsSelected ? '' : 'is-disabled' }}"
											data-payment-button
											data-target="{{ route('reservasi.payment') }}"
											href="{{ $allSeatsSelected ? route('reservasi.payment') : 'javascript:void(0);' }}"
											aria-disabled="{{ $allSeatsSelected ? 'false' : 'true' }}"
										>
											Lanjut ke Pembayaran
										</a>
									</div>
								</footer>
							</article>
						</div>

						<aside class="confirmation-side">
							<article class="confirmation-summary-card" data-selection-card>
								<header class="confirmation-summary-header">
									<p class="confirmation-summary-label">Rute yang Dipilih</p>
									<h3 class="confirmation-summary-title" data-selection-title>Rute Dipilih</h3>
									<p class="confirmation-summary-subtitle" data-selection-subtitle></p>
								</header>
								<div class="confirmation-summary-body">
									<div class="confirmation-summary-meta">
										<span class="confirmation-summary-meta-label">Nomor Kereta</span>
										<span class="confirmation-summary-meta-value" data-selection-train>—</span>
									</div>
									<div class="confirmation-summary-meta">
										<span class="confirmation-summary-meta-label">Kelas Tiket</span>
										<span class="confirmation-summary-meta-value" data-selection-class>{{ $seatClass }}</span>
									</div>
									<div class="confirmation-summary-meta">
										<span class="confirmation-summary-meta-label">Durasi</span>
										<span class="confirmation-summary-meta-value" data-selection-duration>—</span>
									</div>
									<div class="confirmation-summary-price" data-selection-price>Rp —</div>
								</div>
								<ol class="confirmation-summary-timeline" data-selection-timeline></ol>
							</article>

							<article class="confirmation-seat-status">
								<h3>Status Kursi</h3>
								<p data-seat-status>
									{{ $allSeatsSelected ? 'Semua penumpang telah memiliki kursi.' : 'Belum semua penumpang memiliki kursi. Silakan lakukan pemilihan kursi.' }}
								</p>
							</article>
						</aside>
					</div>
				</section>
			</main>
		</div>

		@include('partials.seat-modal', [
			'passengers' => $passengers,
			'seatConfig' => $seatConfig,
			'selectedSeats' => $selectedSeats,
			'seatClass' => $seatClass,
		])

		@include('components.behavior-guard-assets', [
			'pageName' => 'reservasi-konfirmasi',
			'pageStage' => 'seat-selection',
			'stageLabel' => 'Konfirmasi & pilih kursi',
		])
		<script src="{{ asset('js/reservasi-konfirmasi.js') }}" defer></script>
	</body>
</html>
