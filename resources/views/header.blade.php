@php
	$headerClasses = $headerClasses ?? 'border-b border-slate-100 bg-white';
	$navClasses = $navClasses ?? 'mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8';
	$isHomeRoute = request()->routeIs('home');
	$homeBase = route('home');
	$homeLink = $isHomeRoute ? '#home' : $homeBase.'#home';
	$trainsLink = $isHomeRoute ? '#trains' : $homeBase.'#trains';
	$promosLink = $isHomeRoute ? '#promos' : $homeBase.'#promos';
	$contactLink = $isHomeRoute ? '#contact' : $homeBase.'#contact';
@endphp

<header class="w-full {{ $headerClasses }}">
	<nav class="{{ $navClasses }}">
		<a href="{{ $homeBase }}" class="text-2xl font-semibold tracking-tight text-indigo-600">
			KAIzen
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
			<a href="{{ $homeLink }}" class="transition hover:text-indigo-600">Home</a>
			<a href="{{ $trainsLink }}" class="transition hover:text-indigo-600">Trains</a>
			<a href="{{ $promosLink }}" class="transition hover:text-indigo-600">Promos</a>
			<a href="{{ $contactLink }}" class="transition hover:text-indigo-600">Contact Us</a>
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
				<a href="{{ $homeLink }}" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Home</a>
				<a href="{{ $trainsLink }}" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Trains</a>
				<a href="{{ $promosLink }}" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Promos</a>
				<a href="{{ $contactLink }}" class="rounded-xl px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-600">Contact Us</a>
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
