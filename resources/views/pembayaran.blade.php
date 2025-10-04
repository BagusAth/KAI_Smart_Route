<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name', 'KAIzen') }} &middot; Pembayaran</title>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link
			href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
			rel="stylesheet"
		/>

		@vite(['resources/css/app.css', 'resources/js/app.js'])
		<link rel="stylesheet" href="{{ asset('css/pembayaran.css') }}">
	</head>
	<body class="payment-body min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-100 text-slate-900 antialiased">
		<div class="relative flex min-h-screen flex-col">
			<div class="pointer-events-none absolute inset-x-0 top-0 z-0 h-72 bg-gradient-to-b from-indigo-200/60 via-white to-transparent blur-2xl"></div>

			@include('header', [
				'headerClasses' => 'sticky top-0 z-50 border-b border-white/40 bg-white/80 backdrop-blur',
				'navClasses' => 'mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8 text-slate-600',
			])

			<main class="relative z-10 flex-1">
				<section class="mx-auto max-w-6xl px-6 py-12 lg:px-8 lg:py-16">
					<div class="flex flex-col gap-3">
						<h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Langkah Pembayaran</h1>
						<p class="max-w-2xl text-sm text-slate-500">
							Kami sudah menyiapkan pesanan Anda. Silakan lanjutkan ke pembayaran untuk menyelesaikan reservasi.
						</p>
					</div>

					<div class="payment-wrapper mt-10 grid gap-8 lg:grid-cols-[1fr_0.8fr]">
						<div>
							<article class="payment-card">
								<header class="payment-card-header">
									<h2 class="payment-card-title">Ringkasan Penumpang</h2>
									<p class="payment-card-subtitle">Data ini akan disertakan pada tiket elektronik Anda.</p>
								</header>

								<ul class="payment-passenger-list">
									@foreach ($passengers as $passenger)
										<li class="payment-passenger-item">
											<div class="payment-passenger-icon">#{{ str_pad($passenger['number'], 2, '0', STR_PAD_LEFT) }}</div>
											<div class="payment-passenger-info">
												<p class="payment-passenger-name">{{ $passenger['full_name'] }}</p>
												@if (!empty($passenger['seat']))
													<span class="payment-passenger-seat">{{ $passenger['seat'] }}</span>
												@endif
											</div>
										</li>
									@endforeach
								</ul>

								<footer class="payment-card-footer">
									<a class="payment-action-button" href="{{ route('reservasi.confirm') }}">Kembali ke Konfirmasi</a>
								</footer>
							</article>
						</div>

						<aside class="payment-side">
							<article class="payment-summary-card">
								<h3 class="payment-summary-title">Metode Pembayaran</h3>
								<p class="payment-summary-description">
									Pilih metode pembayaran favorit Anda untuk menyelesaikan transaksi.
								</p>

								<ul class="payment-method-list">
									<li class="payment-method-item">
										<label class="payment-method-label">
											<input type="radio" name="payment_method" value="virtual_account" checked>
											<span>Virtual Account</span>
										</label>
									</li>
									<li class="payment-method-item">
										<label class="payment-method-label">
											<input type="radio" name="payment_method" value="e_wallet">
											<span>E-Wallet</span>
										</label>
									</li>
									<li class="payment-method-item">
										<label class="payment-method-label">
											<input type="radio" name="payment_method" value="credit_card">
											<span>Kartu Kredit</span>
										</label>
									</li>
								</ul>

								<div class="payment-summary-footer">
									<button class="payment-summary-button" type="button">Lanjutkan Pembayaran</button>
								</div>
							</article>
						</aside>
					</div>
				</section>
			</main>
		</div>

		<div class="payment-verification-modal hidden" role="dialog" aria-modal="true" data-trust-modal>
			<div class="payment-verification-backdrop" data-trust-close></div>
			<div class="payment-verification-dialog">
				<header class="payment-verification-header">
					<div>
						<p class="payment-verification-label">Verifikasi Keamanan</p>
						<h2 class="payment-verification-title">Penilaian Trust Score</h2>
					</div>
					<button type="button" class="payment-verification-close" data-trust-close aria-label="Tutup verifikasi">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</header>
				<div class="payment-verification-body">
					<div class="payment-trust-score" data-trust-score>--%</div>
					<p class="payment-trust-status" data-trust-status>Menunggu evaluasi perilaku pengguna...</p>
					<p class="payment-trust-message" data-trust-message></p>
					<dl class="payment-trust-details">
						<div>
							<dt>Sesi</dt>
							<dd data-trust-session>—</dd>
						</div>
						<div>
							<dt>Tindakan Sistem</dt>
							<dd data-trust-action>—</dd>
						</div>
					</dl>
				</div>
				<footer class="payment-verification-footer">
					<button type="button" class="payment-verification-secondary" data-trust-close>Batalkan</button>
					<button type="button" class="payment-verification-primary" data-trust-continue disabled>Siap Bayar</button>
				</footer>
			</div>
		</div>

		@include('components.behavior-guard-assets', [
			'pageName' => 'pembayaran',
			'pageStage' => 'payment',
			'stageLabel' => 'Verifikasi & pembayaran',
		])

		<script src="{{ asset('js/pembayaran.js') }}" defer></script>
	</body>
</html>
