<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name', 'KAIzen') }} &middot; Reservasi</title>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link
			href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
			rel="stylesheet"
		/>

		@vite(['resources/css/app.css', 'resources/js/app.js'])
		<link rel="stylesheet" href="{{ asset('css/reservasi.css') }}">
	</head>
	<body class="reservation-body min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-100 text-slate-900 antialiased">
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
							Formulir Reservasi
						</h1>
						<p class="max-w-2xl text-sm text-slate-500">
							Lengkapi data pemesanan Anda untuk melanjutkan proses reservasi. Pastikan seluruh informasi yang
							diberikan sesuai dengan identitas resmi penumpang.
						</p>
					</div>

					<div class="reservation-wrapper mt-10 grid gap-8 lg:grid-cols-[1fr_0.85fr]">
						<div>
							<article class="reservation-card" data-reservation-card>
								<div class="reservation-card-header">
									<h2 class="reservation-card-title">Data Penumpang</h2>
									<p class="reservation-card-subtitle">
										Informasi ini akan digunakan untuk pencetakan tiket dan verifikasi identitas.
									</p>
								</div>

								<form method="POST" action="#" class="reservation-form" data-reservation-form novalidate>
									@csrf
									<div class="reservation-form-group">
										<label for="full_name" class="reservation-label">Nama Lengkap</label>
										<div class="reservation-input-wrapper" data-field-wrapper>
											<input
												type="text"
												name="full_name"
												id="full_name"
												class="reservation-input"
												placeholder="Nama sesuai KTP"
												autocomplete="name"
												required
												data-field="full_name"
												maxlength="120"
											>
											<span class="reservation-input-icon" aria-hidden="true">
												<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a9.76 9.76 0 0115 0" />
												</svg>
											</span>
										</div>
										<p class="reservation-error" id="full_name-error" data-error-for="full_name"></p>
									</div>

									<div class="reservation-form-group">
										<label for="national_id" class="reservation-label">NIK</label>
										<div class="reservation-input-wrapper" data-field-wrapper>
											<input
												type="text"
												name="national_id"
												id="national_id"
												class="reservation-input"
												placeholder="16 digit NIK"
												inputmode="numeric"
												pattern="[0-9]*"
												required
												data-field="national_id"
												maxlength="16"
											>
											<span class="reservation-input-icon" aria-hidden="true">
												<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75A2.25 2.25 0 0014.25 4.5h-9a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 005.25 19.5h9a2.25 2.25 0 002.25-2.25V13.5" />
													<path stroke-linecap="round" stroke-linejoin="round" d="M18 15l3-3m0 0l-3-3m3 3H9" />
												</svg>
											</span>
										</div>
										<p class="reservation-error" id="national_id-error" data-error-for="national_id"></p>
									</div>

									<div class="reservation-form-group">
										<label for="phone_number" class="reservation-label">Nomor Handphone</label>
										<div class="reservation-input-wrapper" data-field-wrapper>
											<input
												type="tel"
												name="phone_number"
												id="phone_number"
												class="reservation-input"
												placeholder="Contoh: 081234567890"
												inputmode="tel"
												required
												data-field="phone_number"
												maxlength="15"
											>
											<span class="reservation-input-icon" aria-hidden="true">
												<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 4.5l1.4-1.4a2.121 2.121 0 013 0l2.1 2.1c.828.828.828 2.172 0 3l-.42.42a2.25 2.25 0 000 3.182l6.348 6.348a2.25 2.25 0 003.182 0l.42-.42c.828-.828 2.172-.828 3 0l2.1 2.1a2.121 2.121 0 010 3l-1.4 1.4a3.75 3.75 0 01-4.95.22l-1.58-1.184a41.351 41.351 0 01-9.574-9.574L3.47 9.45a3.75 3.75 0 01.22-4.95z" />
												</svg>
											</span>
										</div>
										<p class="reservation-error" id="phone_number-error" data-error-for="phone_number"></p>
									</div>

									<div class="reservation-form-actions">
										<button type="submit" class="reservation-submit" data-submit-button>
											Simpan dan Lanjutkan
										</button>
									</div>
								</form>
								<div class="reservation-alert hidden" role="status" data-reservation-alert>
									Data reservasi berhasil disimpan. Kami akan menghubungi Anda melalui nomor yang terdaftar.
								</div>
							</article>
						</div>

						<aside class="reservation-side">
							<article class="reservation-selection-card" data-selection-card>
								<div class="reservation-selection-empty" data-selection-empty>
									<h3>Belum ada rute dipilih</h3>
									<p>Pilih rute pada halaman rekomendasi untuk melihat detail perjalanan di sini.</p>
									<a class="reservation-selection-link" href="{{ route('routes.recommend.show') }}">Kembali ke halaman rute</a>
								</div>
								<div class="reservation-selection-body hidden" data-selection-body>
									<header class="reservation-selection-header">
										<div>
											<p class="reservation-selection-label">Rute yang dipilih</p>
											<h3 class="reservation-selection-title" data-selection-title></h3>
											<p class="reservation-selection-subtitle" data-selection-subtitle></p>
										</div>
										<div class="reservation-selection-price" data-selection-price></div>
									</header>
									<div class="reservation-selection-summary">
										<div class="reservation-selection-meta">
											<span class="reservation-selection-meta-label">Nomor Kereta</span>
											<span class="reservation-selection-meta-value" data-selection-train></span>
										</div>
										<div class="reservation-selection-meta">
											<span class="reservation-selection-meta-label">Durasi</span>
											<span class="reservation-selection-meta-value" data-selection-duration></span>
										</div>
									</div>
									<ol class="reservation-selection-timeline" data-selection-timeline></ol>
								</div>
							</article>
						</aside>
					</div>
				</section>
			</main>
		</div>

		<script src="{{ asset('js/reservasi.js') }}" defer></script>
	</body>
</html>
