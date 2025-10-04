@php
	$config = $behaviorGuardConfig ?? null;
	$pageStage = $pageStage ?? ($stage ?? null);
	$pageName = $pageName ?? ($page ?? null);
@endphp

@if (!empty($config['enabled']) && !empty($config['endpoint']))
	<script>
		window.__behaviorGuardConfig = Object.assign({}, window.__behaviorGuardConfig || {}, {
			enabled: true,
			endpoint: @json($config['endpoint']),
			storageKey: @json($config['storage_key'] ?? 'kaizen:behaviorGuard'),
		});
		window.__behaviorGuardPage = Object.assign({}, window.__behaviorGuardPage || {}, {
			page: @json($pageName),
			stage: @json($pageStage),
			label: @json($stageLabel ?? null),
		});
	</script>
	<script src="{{ asset('js/behavior-guard.js') }}" defer></script>
@endif
