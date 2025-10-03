<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Support\Carbon;
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

        return view('home', [
            'stationOptions' => $stations,
            'passengerOptions' => range(1, 6),
            'departureDateMin' => Carbon::now()->startOfDay()->format('Y-m-d'),
        ]);
    }
}
