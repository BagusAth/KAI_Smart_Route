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
								method="GET"
								action="{{ route('home') }}"
								class="grid gap-4 rounded-[32px] border border-indigo-100 bg-white p-6 shadow-[0_24px_64px_rgba(79,70,229,0.16)] sm:grid-cols-2 lg:grid-cols-[repeat(4,minmax(0,1fr))_auto]"
							>
								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">From</span>
									<select
										name="from_station"
										id="from_station"
										class="w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-700 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
										required
									>
										<option value="" disabled {{ $selectedFrom ? '' : 'selected' }}>Pilih stasiun asal</option>
										@foreach ($stations as $station)
											<option value="{{ $station->id }}" @selected((int) $selectedFrom === $station->id)>
												{{ $station->name }} ({{ $station->city }})
											</option>
										@endforeach
									</select>
								</label>
								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">To</span>
									<select
										name="to_station"
										id="to_station"
										class="w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-700 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
										required
									>
										<option value="" disabled {{ $selectedTo ? '' : 'selected' }}>Pilih stasiun tujuan</option>
										@foreach ($stations as $station)
											<option value="{{ $station->id }}" @selected((int) $selectedTo === $station->id)>
												{{ $station->name }} ({{ $station->city }})
											</option>
										@endforeach
									</select>
								</label>
								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Departure Time</span>
									<select
										name="departure_time"
										id="departure_time"
										class="w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-700 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
									>
										<option value="" {{ $selectedDeparture ? '' : 'selected' }}>Semua jadwal</option>
										@foreach ($availableDepartureTimes as $time)
											<option value="{{ $time }}" @selected($selectedDeparture === $time)>{{ $time }}</option>
										@endforeach
									</select>
								</label>
								<label class="space-y-2 text-left">
									<span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Class</span>
									<select
										name="train_class"
										id="train_class"
										class="w-full rounded-2xl border-2 border-indigo-100 px-4 py-3 text-sm font-medium text-slate-700 transition focus:border-indigo-400 focus:outline-none focus:ring-4 focus:ring-indigo-200/60"
									>
										<option value="" {{ $selectedClass ? '' : 'selected' }}>Semua kelas</option>
										@foreach ($trainClasses as $class)
											<option value="{{ $class }}" @selected($selectedClass === $class)>{{ $class }}</option>
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

							@if ($sameStationSelected)
								<p class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
									Stasiun asal dan tujuan tidak boleh sama.
								</p>
							@endif

							@if ($searchPerformed)
								<div class="mt-6 space-y-4">
									<h3 class="text-lg font-semibold text-slate-800">Hasil pencarian</h3>
									@if ($searchResults->isEmpty() && ! $sameStationSelected)
										<div class="rounded-3xl border border-slate-100 bg-slate-50 px-6 py-8 text-sm text-slate-600 shadow-sm">
											Tidak ditemukan jadwal kereta sesuai filter yang dipilih.
										</div>
									@else
										<div class="grid gap-4">
											@foreach ($searchResults as $result)
												<article class="rounded-3xl border border-indigo-50 bg-white p-6 shadow-[0_12px_36px_rgba(79,70,229,0.12)] transition hover:-translate-y-[2px] hover:shadow-[0_16px_48px_rgba(79,70,229,0.16)]">
													<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
														<div>
															<h4 class="text-base font-semibold text-slate-900">{{ $result['train_name'] }} <span class="text-sm font-medium text-slate-500">({{ $result['train_code'] }})</span></h4>
															<p class="text-sm font-medium text-indigo-600">{{ $result['class'] }}</p>
														</div>
														<div class="flex flex-col gap-2 text-sm text-slate-600 md:text-base">
															<div class="flex items-center gap-3">
																<div class="rounded-2xl bg-indigo-50 px-4 py-2 text-left">
																	<p class="text-xs uppercase tracking-wider text-indigo-500">Berangkat</p>
																	<p class="font-semibold text-slate-900">{{ $result['departure_time'] ?? '—' }}</p>
																	<p class="text-xs text-slate-500">{{ $result['from_station'] ?? 'Stasiun tidak diketahui' }}</p>
																</div>
																<div class="rounded-2xl bg-slate-100 px-4 py-2 text-left">
																	<p class="text-xs uppercase tracking-wider text-slate-500">Tiba</p>
																	<p class="font-semibold text-slate-900">{{ $result['arrival_time'] ?? '—' }}</p>
																	<p class="text-xs text-slate-500">{{ $result['to_station'] ?? 'Stasiun tidak diketahui' }}</p>
																</div>
															</div>
															@if ($result['duration'])
																<p class="text-xs font-medium uppercase tracking-wider text-slate-500">Durasi perjalanan: <span class="font-semibold text-slate-800">{{ $result['duration'] }}</span></p>
															@endif
														</div>
													@if (! empty($result['route_stops']))
														<div class="mt-4 text-xs text-slate-500">
															Rute: {{ implode(' • ', $result['route_stops']) }}
														</div>
													@endif
												</article>
											@endforeach
										</div>
									@endif
								</div>
							@endif
						</div>
					</section>

					<section id="trains" class="relative py-20">
						<div class="mx-auto flex max-w-6xl flex-col items-center gap-10 px-6 text-center lg:px-8">
							<div class="space-y-4">
								<h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">TRAINS</h2>
								<p class="text-sm text-slate-600 sm:text-base">Discover the most popular train routes.</p>
							</div>
							<div class="grid w-full gap-6 sm:grid-cols-2 lg:grid-cols-5">
								@forelse ($popularTrains as $train)
									<article class="group flex flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-[0_18px_48px_rgba(15,23,42,0.12)] transition hover:-translate-y-1 hover:shadow-[0_28px_60px_rgba(15,23,42,0.16)]">
										<div class="relative h-48 w-full overflow-hidden">
											<img src="{{ $train['image'] }}" alt="{{ $train['name'] }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" />
											<span class="absolute right-3 top-3 inline-flex rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-900 shadow-sm">{{ $train['class'] }}</span>
										</div>
										<div class="flex flex-1 items-center justify-between px-4 py-4">
											<h3 class="text-sm font-semibold text-slate-800">{{ $train['name'] }}</h3>
											<span class="inline-flex h-8 min-w-[3rem] items-center justify-center rounded-full px-3 text-xs font-semibold text-white {{ $train['color'] }}">
												{{ $train['code'] }}
											</span>
										</div>
									</article>
								@empty
									<p class="col-span-full rounded-3xl border border-slate-100 bg-white px-6 py-8 text-sm text-slate-600 shadow-sm">Belum ada data kereta untuk ditampilkan.</p>
								@endforelse
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

				if (menuButton && menuPanel) {
					menuButton.addEventListener('click', () => {
						menuPanel.classList.toggle('hidden');
						menuButton.classList.toggle('bg-indigo-50');
						menuButton.classList.toggle('border-indigo-300');
					});
				}

				if (searchForm) {
					searchForm.addEventListener('submit', (event) => {
						event.preventDefault();
						const button = searchForm.querySelector('button[type="submit"]');
						if (!button) {
							return;
						}

						button.classList.add('scale-[103%]');
						button.classList.add('ring-4');
						button.classList.add('ring-indigo-200/70');

						setTimeout(() => {
							button.classList.remove('scale-[103%]');
							button.classList.remove('ring-4');
							button.classList.remove('ring-indigo-200/70');
						}, 320);
					});
				}
			});
		</script>
	</body>
</html>
