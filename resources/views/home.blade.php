<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{{ config('app.name', 'RailLink') }}</title>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link
			href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
			rel="stylesheet"
		/>

		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body class="min-h-screen bg-white text-slate-900 antialiased">
		<div class="flex min-h-screen flex-col">
			<header class="sticky top-0 z-50 border-b border-slate-100 bg-white">
					<nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8">
						<a href="#" class="text-2xl font-semibold tracking-tight text-indigo-600">
							RailLink
						</a>
						<div class="flex items-center gap-2 lg:hidden">
							<a
								href="#signin"
								class="rounded-full border border-indigo-200 px-4 py-1.5 text-sm font-medium text-indigo-600 transition hover:border-indigo-400 hover:text-indigo-700"
							>
								Sign In
							</a>
							<button
								type="button"
								class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-indigo-200 text-indigo-600 transition hover:border-indigo-400 hover:text-indigo-700"
								aria-label="Toggle navigation"
								data-menu-button
							>
								<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
								</svg>
							</button>
						</div>
						<div class="hidden items-center gap-10 text-sm font-medium text-slate-700 lg:flex">
							<a href="#home" class="transition hover:text-indigo-600">Home</a>
							<a href="#trains" class="transition hover:text-indigo-600">Trains</a>
							<a href="#promos" class="transition hover:text-indigo-600">Promos</a>
							<a href="#contact" class="transition hover:text-indigo-600">Contact Us</a>
						</div>
						<div class="hidden items-center gap-3 lg:flex">
							<a
								href="#signin"
								class="rounded-full border border-indigo-100 px-5 py-2 text-sm font-medium text-indigo-600 transition hover:border-indigo-300 hover:text-indigo-700"
							>
								Sign In
							</a>
							<a
								href="#register"
								class="rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 px-5 py-2 text-sm font-semibold text-white transition hover:from-indigo-600 hover:to-purple-600"
							>
								Register
							</a>
						</div>
					</nav>
					<div class="mx-auto hidden max-w-6xl px-6 pb-6 lg:hidden" id="mobile-menu" data-menu-panel>
						<div class="rounded-2xl border border-slate-100 bg-white p-6">
							<div class="grid gap-4 text-sm font-medium text-slate-700">
								<a href="#home" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Home</a>
								<a href="#trains" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Trains</a>
								<a href="#promos" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Promos</a>
								<a href="#contact" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Contact Us</a>
							</div>
							<div class="mt-6 flex flex-col gap-3">
								<a
									href="#signin"
									class="rounded-full border border-indigo-100 px-4 py-2 text-center text-sm font-medium text-indigo-600 transition hover:border-indigo-300 hover:text-indigo-700"
								>
									Sign In
								</a>
								<a
									href="#register"
									class="rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 px-4 py-2 text-center text-sm font-semibold text-white transition hover:from-indigo-600 hover:to-purple-600"
								>
									Register
								</a>
							</div>
						</div>
					</div>
				</header>

				<main class="flex-1">
					<section id="home" class="relative flex h-[calc(100vh-88px)] w-full items-end bg-white">
						<img
							src="{{ asset('img/home.png') }}"
							alt="High-speed train illustration"
							class="block h-full w-full object-contain object-bottom"
						/>
					</section>

					<section class="relative z-20 -mt-20 pb-10">
						<div class="mx-auto max-w-5xl px-6 lg:px-8">
							<form
								id="search-form"
								action="{{ route('routes.recommend') }}"
								method="POST"
								class="grid gap-4 rounded-[32px] border border-indigo-100 bg-white p-6 shadow-[0_24px_64px_rgba(79,70,229,0.16)] sm:grid-cols-2 lg:grid-cols-[repeat(4,minmax(0,1fr))_auto]"
							>
								@csrf

								@if (session('status'))
									<div class="col-span-full rounded-2xl border border-indigo-100 bg-indigo-50/70 px-4 py-3 text-sm text-indigo-700">
										{{ session('status') }}
									</div>
								@endif

								@if ($errors->any())
									<div class="col-span-full rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
										<ul class="space-y-1">
											@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
											@endforeach
										</ul>
									</div>
								@endif

								<div class="col-span-full hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600" id="form-errors-wrapper">
									<ul class="space-y-1" id="form-errors"></ul>
								</div>

								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">From</span>
									<div class="relative">
										<input
											type="text"
											name="origin_label"
											id="origin-display"
											autocomplete="off"
											list="origin-stations"
											value="{{ old('origin_label') }}"
											placeholder="Cari stasiun keberangkatan"
											class="peer w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-700 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
											required
										/>
										<input type="hidden" name="origin" id="origin" value="{{ old('origin') }}">
										<datalist id="origin-stations">
											@foreach ($stationOptions ?? [] as $station)
												<option value="{{ $station['label'] }}" data-code="{{ $station['code'] }}">{{ $station['code'] }} &middot; {{ $station['city'] }}</option>
											@endforeach
										</datalist>
										<span class="pointer-events-none absolute inset-y-0 right-4 hidden items-center text-indigo-500 peer-focus:flex">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
											</svg>
										</span>
									</div>
								</label>

								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">To</span>
									<div class="relative">
										<input
											type="text"
											name="destination_label"
											id="destination-display"
											autocomplete="off"
											list="destination-stations"
											value="{{ old('destination_label') }}"
											placeholder="Cari stasiun tujuan"
											class="peer w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-700 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
											required
										/>
										<input type="hidden" name="destination" id="destination" value="{{ old('destination') }}">
										<datalist id="destination-stations">
											@foreach ($stationOptions ?? [] as $station)
												<option value="{{ $station['label'] }}" data-code="{{ $station['code'] }}">{{ $station['code'] }} &middot; {{ $station['city'] }}</option>
											@endforeach
										</datalist>
										<span class="pointer-events-none absolute inset-y-0 right-4 hidden items-center text-indigo-500 peer-focus:flex">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
											</svg>
										</span>
									</div>
								</label>

								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Keberangkatan</span>
									<input
										type="date"
										name="departure_date"
										id="departure-date"
										min="{{ $departureDateMin ?? now()->format('Y-m-d') }}"
										value="{{ old('departure_date', $departureDateMin ?? now()->format('Y-m-d')) }}"
										class="w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-700 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
										required
									/>
								</label>

								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Penumpang Dewasa</span>
									<select
										name="passengers"
										id="passengers"
										class="w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-400 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
										data-placeholder-select
										required
									>
										<option value="" disabled {{ old('passengers') ? '' : 'selected' }}>Pilih jumlah penumpang</option>
										@foreach (($passengerOptions ?? []) as $option)
											<option value="{{ $option }}" {{ (string) old('passengers') === (string) $option ? 'selected' : '' }}>{{ $option }} Dewasa</option>
										@endforeach
									</select>
								</label>

								<button
									type="submit"
									class="hidden h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white transition hover:from-indigo-600 hover:to-purple-600 lg:flex"
									aria-label="Search trains"
								>
									<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.85-4.15a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
									</svg>
								</button>
								<button
									type="submit"
									class="col-span-full flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 px-5 py-3 text-sm font-semibold text-white transition hover:from-indigo-600 hover:to-purple-600 lg:hidden"
								>
									<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.85-4.15a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
									</svg>
									Cari Kereta
							</button>
						</form>
						</div>
					</section>

					<section id="popular-routes" class="relative z-10 bg-white py-24">
						<div class="mx-auto flex max-w-6xl flex-col gap-10 px-6 text-center lg:px-8">
							<div class="space-y-4">
								<h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Discover the most popular train routes chosen by passengers every day.</h2>
								<p class="mx-auto max-w-2xl text-sm text-slate-600 sm:text-base">
									Temukan inspirasi perjalanan dari rute terfavorit dengan jadwal terbaik dan pengalaman paling nyaman setiap hari.
								</p>
							</div>
							@php
								$popularRoutes = [
									[
										'image' => 'https://images.unsplash.com/photo-1578771792482-1e7eb2b8b447?q=80&w=1271&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
										'title' => 'Surabaya - Semarang',
										'subtitle' => '4 jam perjalanan dengan KA Argo Bromo',
										'destination' => 'Semarang Tawang',
										'destination_image' => 'https://images.unsplash.com/photo-1523268266866-5dbd36907f00?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
									],
									[
										'image' => 'https://images.unsplash.com/photo-1562923928-6078542d1ad1?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
										'title' => 'Semarang - Jakarta',
										'subtitle' => 'Jadwal fleksibel, 6 jam perjalanan nyaman setiap hari',
										'destination' => 'Gambir Jakarta',
										'destination_image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=400&q=80',
									],
									[
										'image' => 'https://plus.unsplash.com/premium_photo-1714879804862-683216104086?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
										'title' => 'Bandung - Yogyakarta',
										'subtitle' => 'Nikmati pemandangan alam dalam 7 jam perjalanan KA Lodaya',
										'destination' => 'Yogyakarta Tugu',
										'destination_image' => 'https://images.unsplash.com/photo-1553184257-604db3e574a8?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
									],
								];
							@endphp
							<div class="grid w-full gap-6 sm:grid-cols-2 xl:grid-cols-3">
								@foreach ($popularRoutes as $route)
								<article class="group relative flex min-h-[420px] overflow-hidden rounded-[32px] border border-white/20 shadow-[0_18px_48px_rgba(15,23,42,0.18)] transition duration-700 hover:-translate-y-1 hover:shadow-[0_32px_72px_rgba(15,23,42,0.26)]">
									<img src="{{ $route['image'] }}" alt="{{ $route['title'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110" />
									<div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-slate-900/10"></div>
									<div class="relative mt-auto flex w-full flex-col gap-4 p-6 text-left text-white sm:p-8">
										<div class="space-y-1">
											<p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">{{ $loop->iteration < 10 ? '0' . $loop->iteration : $loop->iteration }} Popular Route</p>
											<h3 class="text-xl font-semibold sm:text-2xl">{{ $route['title'] }}</h3>
											<p class="text-sm text-white/80 sm:text-base">{{ $route['subtitle'] }}</p>
										</div>
										<div class="flex items-center gap-3">
											<div class="h-12 w-12 overflow-hidden rounded-full border border-white/50 shadow-lg">
												<img src="{{ $route['destination_image'] }}" alt="{{ $route['destination'] }}" class="h-full w-full object-cover" />
											</div>
											<div class="flex flex-col">
												<span class="text-xs font-semibold uppercase tracking-wide text-white/70">Destinasi akhir</span>
												<span class="text-sm font-semibold text-white">{{ $route['destination'] }}</span>
											</div>
										</div>
										<div class="flex items-center justify-between">
											<div class="inline-flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-white/60">
												<span>Explore</span>
												<span class="h-1.5 w-1.5 rounded-full bg-white/60"></span>
												<span>Now</span>
											</div>
											<button type="button" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/90 text-indigo-600 shadow-lg transition hover:bg-white">
												<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
												</svg>
											</button>
										</div>
									</div>
								</article>
								@endforeach
							</div>
						</div>
					</section>

					<section id="trains" class="relative py-20">
						<div class="mx-auto flex max-w-6xl flex-col items-center gap-10 px-6 text-center lg:px-8">
							<div class="space-y-4">
								<h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">TRAINS</h2>
								<p class="text-sm text-slate-600 sm:text-base">Discover the most popular train.</p>
							</div>
							<div class="grid w-full gap-6 sm:grid-cols-2 lg:grid-cols-5">
								@php
									$trains = [
										[
											'name' => 'KA Progo',
											'image' => 'https://images.unsplash.com/photo-1527295110-5145f6b148d0?q=80&w=1131&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
											'badge' => null,
											'color' => 'bg-indigo-500'
										],
										[
											'name' => 'KA Kertajaya',
											'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1284&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
											'badge' => null,
											'color' => 'bg-blue-600'
										],
										[
											'name' => 'KA Menoreh',
											'image' => 'https://images.unsplash.com/photo-1601999007938-f584b47324ac?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
											'badge' => 'Explore',
											'color' => 'bg-slate-900'
										],
										[
											'name' => 'KA Harina',
											'image' => 'https://images.unsplash.com/photo-1514337224818-9787cf717f2a?q=80&w=1331&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
											'badge' => null,
											'color' => 'bg-orange-500'
										],
										[
											'name' => 'KA Ambarawa',
											'image' => 'https://images.unsplash.com/flagged/photo-1550719723-8602e87f2dc8?q=80&w=1025&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
											'badge' => null,
											'color' => 'bg-amber-500'
										],
									];
								@endphp

								@foreach ($trains as $train)
									<article class="group flex flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_18px_48px_rgba(15,23,42,0.12)] transition hover:-translate-y-1 hover:shadow-[0_28px_60px_rgba(15,23,42,0.16)]">
										<div class="relative h-48 w-full overflow-hidden">
											<img src="{{ $train['image'] }}" alt="{{ $train['name'] }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" />
											@if ($train['badge'])
												<span class="absolute right-3 top-3 inline-flex rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-900 shadow-sm">{{ $train['badge'] }}</span>
											@endif
										</div>
										<div class="flex flex-1 items-center justify-between px-4 py-4">
											<h3 class="text-sm font-semibold text-slate-800">{{ $train['name'] }}</h3>
											<span class="inline-flex h-8 min-w-[3rem] items-center justify-center rounded-full px-3 text-xs font-semibold text-white {{ $train['color'] }}">
												{{ explode(' ', trim($train['name']))[1] ?? 'Rail' }}
											</span>
										</div>
									</article>
								@endforeach
							</div>
						</div>
					</section>
				</main>

				<footer id="contact" class="bg-[#4D7CFF] py-14 text-white">
					<div class="mx-auto flex max-w-6xl flex-col items-center gap-10 px-6 text-center lg:px-8">
						<div class="flex flex-col items-center gap-4">
							<a href="#" class="text-2xl font-bold tracking-wide">RailLink</a>
							<div class="flex items-center gap-4 text-white/90">
								<a href="#" aria-label="Facebook" class="transition hover:text-white">
									<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
										<path d="M13.4 20.998h-2.7v-8.8H9V9.9h1.7V8.3c0-1.6 1-3.3 3.4-3.3 1 0 2 .1 2 .1v2.2h-1.1c-1.1 0-1.4.7-1.4 1.3v1.3h2.4l-.3 2.3h-2.1z" />
									</svg>
								</a>
								<a href="#" aria-label="Twitter" class="transition hover:text-white">
									<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
										<path d="M19.3 7.9c.9-.5 1.6-1.3 1.9-2.3-.9.5-1.8.8-2.8.9a4.13 4.13 0 00-7 3.8 11.7 11.7 0 01-8.5-4.3 4.1 4.1 0 001.3 5.5c-.7 0-1.4-.2-2-.5 0 2 1.4 3.8 3.4 4.2-.6.2-1.3.2-1.9.1a4.15 4.15 0 003.9 2.9A8.26 8.26 0 012 18.5a11.68 11.68 0 006.3 1.9c7.6 0 11.7-6.3 11.7-11.7v-.5a8 8 0 001.9-2.1z" />
									</svg>
								</a>
								<a href="#" aria-label="LinkedIn" class="transition hover:text-white">
									<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
										<path d="M5.1 3.5A2 2 0 113.1 5.6a2 2 0 012-2zM4 8.5h2.2V20H4zM14.6 8.2a4.4 4.4 0 00-3.2 1.3V8.5H9.3V20h2.2v-6.2a2.4 2.4 0 012.5-2.7c1.1 0 1.8.5 1.8 2.3V20H18v-6.7c0-3-1.6-5.1-3.4-5.1z" />
									</svg>
								</a>
								<a href="#" aria-label="Instagram" class="transition hover:text-white">
									<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
										<path d="M16.7 3H7.3A4.3 4.3 0 003 7.3v9.4A4.3 4.3 0 007.3 21h9.4a4.3 4.3 0 004.3-4.3V7.3A4.3 4.3 0 0016.7 3zm2.4 14.7a2.4 2.4 0 01-2.4 2.4H7.3a2.4 2.4 0 01-2.4-2.4V7.3a2.4 2.4 0 012.4-2.4h9.4a2.4 2.4 0 012.4 2.4z" />
										<path d="M12 8.3A3.7 3.7 0 108.3 12 3.7 3.7 0 0012 8.3zm0 5.8a2.1 2.1 0 112.1-2.1 2.1 2.1 0 01-2.1 2.1zM17.2 7a.9.9 0 11-.9-.9.9.9 0 01.9.9z" />
									</svg>
								</a>
							</div>
						</div>
						<nav class="flex flex-wrap justify-center gap-6 text-sm font-semibold text-white/90">
							<a href="#home" class="transition hover:text-white">Home</a>
							<a href="#trains" class="transition hover:text-white">Service</a>
							<a href="#promos" class="transition hover:text-white">Resource</a>
							<a href="#contact" class="transition hover:text-white">Contact</a>
							<a href="#about" class="transition hover:text-white">About</a>
						</nav>
						<p class="max-w-3xl text-sm text-white/80">
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sed purus feugiat, fermentum justo vitae, accumsan enim.
						</p>
						<p class="text-xs text-white/70">&copy; {{ now()->year }} Mangcoding &middot; Powered by mangcoding</p>
					</div>
				</footer>
			</div>
		</div>

		<script>
			document.addEventListener('DOMContentLoaded', () => {
				const menuButton = document.querySelector('[data-menu-button]');
				const menuPanel = document.querySelector('[data-menu-panel]');
				const searchForm = document.getElementById('search-form');
				const originInput = document.getElementById('origin-display');
				const destinationInput = document.getElementById('destination-display');
				const originHidden = document.getElementById('origin');
				const destinationHidden = document.getElementById('destination');
				const departureDateInput = document.getElementById('departure-date');
				const passengersSelect = document.getElementById('passengers');
				const stationOptions = @json($stationOptions ?? []);
				const formErrorsWrapper = document.getElementById('form-errors-wrapper');
				const formErrorsList = document.getElementById('form-errors');

				if (menuButton && menuPanel) {
					menuButton.addEventListener('click', () => {
						menuPanel.classList.toggle('hidden');
						menuButton.classList.toggle('bg-indigo-50');
						menuButton.classList.toggle('border-indigo-300');
					});
				}

				const findStationByLabel = (value) => {
					if (!value) {
						return null;
					}

					const normalized = value.trim().toLowerCase();
					return stationOptions.find((station) => {
						return station.label.toLowerCase() === normalized
							|| station.name.toLowerCase() === normalized
							|| station.code.toLowerCase() === normalized;
					});
				};

				const syncStationValue = (input, hidden) => {
					const match = findStationByLabel(input.value);
					hidden.value = match ? match.code : '';
				};

				if (originInput && originHidden) {
					['input', 'change', 'blur'].forEach((eventName) => {
						originInput.addEventListener(eventName, () => syncStationValue(originInput, originHidden));
					});
					syncStationValue(originInput, originHidden);
				}

				if (destinationInput && destinationHidden) {
					['input', 'change', 'blur'].forEach((eventName) => {
						destinationInput.addEventListener(eventName, () => syncStationValue(destinationInput, destinationHidden));
					});
					syncStationValue(destinationInput, destinationHidden);
				}

				const renderErrors = (messages) => {
					if (!formErrorsWrapper || !formErrorsList) {
						return;
					}

					formErrorsList.innerHTML = '';
					if (!messages.length) {
						formErrorsWrapper.classList.add('hidden');
						return;
					}

					messages.forEach((message) => {
						const li = document.createElement('li');
						li.textContent = message;
						formErrorsList.appendChild(li);
					});

					formErrorsWrapper.classList.remove('hidden');
				};

				if (searchForm) {
					searchForm.addEventListener('submit', (event) => {
						syncStationValue(originInput, originHidden);
						syncStationValue(destinationInput, destinationHidden);

						const messages = [];

						if (!originHidden.value) {
							messages.push('Silakan pilih stasiun keberangkatan yang valid.');
						}

						if (!destinationHidden.value) {
							messages.push('Silakan pilih stasiun tujuan yang valid.');
						}

						if (originHidden.value && destinationHidden.value && originHidden.value === destinationHidden.value) {
							messages.push('Stasiun keberangkatan dan tujuan tidak boleh sama.');
						}

						if (departureDateInput) {
							const minDate = departureDateInput.min;
							const selectedDate = departureDateInput.value;

							if (!selectedDate) {
								messages.push('Silakan pilih tanggal keberangkatan.');
							} else if (minDate && selectedDate < minDate) {
								messages.push('Tanggal keberangkatan tidak boleh sebelum hari ini.');
							}
						}

						if (passengersSelect && !passengersSelect.value) {
							messages.push('Silakan pilih jumlah penumpang dewasa.');
						}

						if (messages.length) {
							event.preventDefault();
							renderErrors(messages);
							return;
						}

						renderErrors([]);
					});
				}

				const placeholderSelects = document.querySelectorAll('[data-placeholder-select]');

				const syncSelectPlaceholder = (select) => {
					const hasValue = Boolean(select.value);
					select.classList.toggle('text-slate-700', hasValue);
					select.classList.toggle('text-slate-400', !hasValue);
				};

				placeholderSelects.forEach((select) => {
					syncSelectPlaceholder(select);
					select.addEventListener('change', () => syncSelectPlaceholder(select));
					select.addEventListener('blur', () => syncSelectPlaceholder(select));
				});

				if (departureDateInput) {
					const minDate = departureDateInput.min;
					const enforceMinDate = () => {
						if (!minDate) {
							return;
						}

						if (departureDateInput.value && departureDateInput.value < minDate) {
							departureDateInput.value = minDate;
						}
					};

					enforceMinDate();
					departureDateInput.addEventListener('change', enforceMinDate);
				}
			});
		</script>
	</body>
</html>
