@php
	$coachConfigs = $seatConfig['coaches'] ?? [];
@endphp

<div class="seat-modal hidden" role="dialog" aria-modal="true" data-seat-modal>
	<div class="seat-modal-backdrop" data-seat-close></div>
	<div class="seat-modal-dialog">
		<form class="seat-modal-form" method="POST" action="{{ route('reservasi.seats') }}" data-seat-form>
			@csrf
			<header class="seat-modal-header">
				<div>
					<h2 class="seat-modal-title">Pemilihan Kursi</h2>
					<p class="seat-modal-subtitle">Pilih kursi sesuai kelas tiket Anda. Gunakan panel di samping untuk berpindah antar penumpang.</p>
				</div>
				<button class="seat-modal-close" type="button" data-seat-close aria-label="Tutup pemilihan kursi">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</header>

			<div class="seat-modal-body">
				<aside class="seat-passenger-panel">
					<h3>Penumpang</h3>
					<ul class="seat-passenger-list" data-seat-passenger-list>
						@foreach ($passengers as $passenger)
							@php
								$assignment = $passenger['seat_assignment'] ?? null;
								$coachValue = $assignment['coach'] ?? '';
								$seatValue = $assignment['seat'] ?? '';
								$seatLabel = $passenger['seat_label'] ?? null;
							@endphp
							<li
								class="seat-passenger-item"
								data-passenger-index="{{ $passenger['index'] }}"
								data-passenger-active="{{ $loop->first ? 'true' : 'false' }}"
							>
								<span class="seat-passenger-number">#{{ str_pad($passenger['number'], 2, '0', STR_PAD_LEFT) }}</span>
								<div class="seat-passenger-info">
									<p class="seat-passenger-name">{{ $passenger['full_name'] }}</p>
									<span
										class="seat-passenger-seat"
										data-passenger-seat-option="{{ $passenger['index'] }}"
										data-seat-empty="{{ $seatLabel ? 'false' : 'true' }}"
										data-seat-label="{{ $seatLabel ?? '' }}"
									>
										{{ $seatLabel ?? 'Belum dipilih' }}
									</span>
								</div>
								<input type="hidden" name="seats[{{ $passenger['index'] }}][coach]" value="{{ $coachValue }}" data-seat-input-coach="{{ $passenger['index'] }}">
								<input type="hidden" name="seats[{{ $passenger['index'] }}][seat]" value="{{ $seatValue }}" data-seat-input-seat="{{ $passenger['index'] }}">
							</li>
						@endforeach
					</ul>

					<div class="seat-passenger-tip">
						<p>Klik nama penumpang untuk mengatur kursinya. Kursi yang berwarna abu-abu telah terisi.</p>
					</div>
				</aside>

				<div class="seat-map-panel">
					<div class="seat-map-header">
						<div class="seat-map-class" data-seat-class-indicator>
							{{ $seatConfig['class'] ?? 'Kelas' }}
						</div>
						<div class="seat-map-legend">
							<span class="seat-map-legend-item"><span class="seat-map-legend-icon is-available"></span>Tersedia</span>
							<span class="seat-map-legend-item"><span class="seat-map-legend-icon is-active"></span>Dipilih</span>
							<span class="seat-map-legend-item"><span class="seat-map-legend-icon is-blocked"></span>Terisi</span>
						</div>
					</div>

					<nav class="seat-coach-tabs" role="tablist" data-seat-coach-tabs>
						@foreach ($coachConfigs as $coach)
							@php
								$isActiveCoach = $loop->first;
							@endphp
							<button
								type="button"
								class="seat-coach-tab"
								data-coach-tab
								data-coach-code="{{ $coach['code'] }}"
								data-coach-active="{{ $isActiveCoach ? 'true' : 'false' }}"
								role="tab"
								aria-selected="{{ $isActiveCoach ? 'true' : 'false' }}"
							>
								<span class="seat-coach-tab-label">{{ $coach['label'] }}</span>
								<span class="seat-coach-tab-meta">{{ $coach['available'] }} / {{ $coach['total'] }} kursi</span>
							</button>
						@endforeach
					</nav>

					<div class="seat-coach-panels" data-seat-coach-panels>
						@foreach ($coachConfigs as $coach)
							@php
								$isActiveCoach = $loop->first;
							@endphp
							<section
								class="seat-coach-panel"
								data-coach-panel="{{ $coach['code'] }}"
								data-coach-active="{{ $isActiveCoach ? 'true' : 'false' }}"
								role="tabpanel"
							>
								<header class="seat-coach-panel-header">
									<div>
										<h4>{{ $coach['label'] }}</h4>
										<p>Total Kursi Tersedia: {{ $coach['available'] }} / {{ $coach['total'] }}</p>
									</div>
									<span class="seat-coach-door">Pintu</span>
								</header>

								<div class="seat-map-grid" data-seat-grid role="grid">
									@foreach ($coach['rows'] as $row)
										<div class="seat-row" role="row">
											<div class="seat-row-label" role="gridcell">{{ $row['number'] }}</div>
											<div class="seat-row-seats" role="gridcell">
												@foreach ($row['seats'] as $seat)
													@php
														$status = $seat['status'];
														$ownerIndex = $seat['passenger_index'];
														$seatLabel = sprintf('%s · Kursi %s', $coach['label'], $seat['code']);
														$isBlocked = $status === 'blocked';
														$isSelected = $status === 'selected';
													@endphp
													<button
														type="button"
														class="seat-button{{ $isBlocked ? ' is-blocked' : '' }}{{ $isSelected ? ' is-selected' : '' }}"
														data-seat-button
														data-coach-code="{{ $coach['code'] }}"
														data-seat-code="{{ $seat['code'] }}"
														data-seat-base-status="{{ $status }}"
														data-seat-status="{{ $status }}"
														data-seat-owner="{{ $ownerIndex === null ? '' : $ownerIndex }}"
														data-seat-label="{{ $seatLabel }}"
														aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
														{{ $isBlocked ? 'disabled' : '' }}
													>
														<span>{{ $seat['code'] }}</span>
													</button>
												@endforeach
											</div>
										</div>
									@endforeach
								</div>
							</section>
						@endforeach
					</div>
				</div>
			</div>

			<footer class="seat-modal-footer">
				<div class="seat-modal-status" data-seat-counter>
					{{ ($selectedSeats->count() ?? 0) }} / {{ $passengers->count() }} kursi dipilih
				</div>
				<p class="seat-modal-hint" data-seat-error hidden>
					Silakan pilih kursi untuk setiap penumpang sebelum melanjutkan.
				</p>
				<div class="seat-modal-actions">
					<button class="seat-modal-secondary" type="button" data-seat-close>Batal</button>
					<button class="seat-modal-primary" type="submit">Simpan Pilihan Kursi</button>
				</div>
			</footer>
		</form>
	</div>
</div>
