<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $stations = Station::query()
            ->orderBy('name')
            ->get(['code', 'name', 'city'])
            ->map(fn (Station $station) => [
                'code' => $station->code,
                'name' => $station->name,
                'city' => $station->city,
                'label' => sprintf('%s (%s)', $station->name, $station->city),
            ]);

        $timeOptions = collect(range(0, 23))
            ->map(fn (int $hour) => str_pad((string) $hour, 2, '0', STR_PAD_LEFT).':00')
            ->all();

        return view('home', [
            'stationOptions' => $stations,
            'timeOptions' => $timeOptions,
        ]);
    }
}
