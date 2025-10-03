<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class BehaviorAnalysisController extends Controller
{
    private const FEATURE_KEYS = [
        'mouseSpeed',
        'mouseJitter',
        'typingSpeed',
        'typingRhythm',
        'clickFrequency',
        'timeOnPage',
        'scrollPattern',
        'mouseAcceleration',
    ];

    private array $model;
    private array $stats;

    public function __construct()
    {
        $this->model = $this->loadJson(resource_path('security/model-non-tf.json'));
        $this->stats = $this->loadJson(resource_path('security/normalization-stats.json'));
    }

    public function analyze(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (empty($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Empty payload.',
                'action' => 'CHALLENGE',
            ], 422);
        }

        $features = [];
        foreach (self::FEATURE_KEYS as $key) {
            $features[$key] = $this->normalize((float) Arr::get($payload, $key, 0), $key);
        }

        $score = $this->sigmoid($this->dotProduct($features));

        if (isset($payload['sessionId'])) {
            Log::channel('daily')->info('behavior-analysis', [
                'sessionId' => $payload['sessionId'],
                'score' => $score,
                'raw' => Arr::only($payload, self::FEATURE_KEYS),
            ]);
        }

        [$action, $message] = $this->actionForScore($score);

        return response()->json([
            'success' => true,
            'trustScore' => $score,
            'action' => $action,
            'message' => $message,
        ]);
    }

    private function dotProduct(array $normalizedFeatures): float
    {
        $sum = $this->model['bias'] ?? 0;

        foreach (self::FEATURE_KEYS as $index => $key) {
            $weight = $this->model['weights'][$index] ?? 0;
            $sum += $weight * ($normalizedFeatures[$key] ?? 0);
        }

        return $sum;
    }

    private function sigmoid(float $value): float
    {
        return 1 / (1 + exp(-$value));
    }

    private function normalize(float $value, string $key): float
    {
        $stat = $this->stats[$key] ?? null;

        if (!$stat || !isset($stat['min'], $stat['max']) || $stat['max'] == $stat['min']) {
            return $value;
        }

        return ($value - $stat['min']) / ($stat['max'] - $stat['min']);
    }

    private function loadJson(string $path): array
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Behavior model asset missing: %s', $path));
        }

        return json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array{string, string}
     */
    private function actionForScore(float $score): array
    {
        if ($score < 0.3) {
            return ['BLOCK', 'Aktivitas mencurigakan terdeteksi.'];
        }

        if ($score < 0.7) {
            return ['CHALLENGE', 'Verifikasi tambahan dibutuhkan.'];
        }

        return ['ALLOW', 'Sesi terlihat normal.'];
    }
}
