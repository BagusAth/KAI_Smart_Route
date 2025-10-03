<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Station;
use App\Models\Train;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $stations = Station::query()->orderBy('name')->get();
        $trainClasses = Train::query()->select('class')->distinct()->pluck('class')->filter()->sort()->values();

        $selectedFrom = $request->input('from_station');
        $selectedTo = $request->input('to_station');
        $selectedDeparture = $request->input('departure_time');
        $selectedClass = $request->input('train_class');

        $availableDepartureTimes = Schedule::query()
            ->whereNotNull('departure')
            ->when($selectedFrom, fn ($query) => $query->where('station_id', $selectedFrom))
            ->orderBy('departure')
            ->pluck('departure')
            ->unique()
            ->values();

        if ($availableDepartureTimes->isEmpty()) {
            $availableDepartureTimes = Schedule::query()
                ->whereNotNull('departure')
                ->orderBy('departure')
                ->pluck('departure')
                ->unique()
                ->values();
        }

        $searchPerformed = $request->filled('from_station') && $request->filled('to_station');
        $searchResults = collect();

        if ($searchPerformed && $selectedFrom !== $selectedTo) {
            $trains = Train::query()
                ->with([
                    'routes' => fn ($query) => $query->orderBy('stop_order')->with('station'),
                    'schedules' => fn ($query) => $query->orderBy('departure')->with('station'),
                ])
                ->when($selectedClass, fn ($query) => $query->where('class', $selectedClass))
                ->get();

            $searchResults = $trains
                ->filter(function (Train $train) use ($selectedFrom, $selectedTo, $selectedDeparture) {
                    $fromRoute = $train->routes->firstWhere('station_id', (int) $selectedFrom);
                    $toRoute = $train->routes->firstWhere('station_id', (int) $selectedTo);

                    if (! $fromRoute || ! $toRoute) {
                        return false;
                    }

                    if ($fromRoute->stop_order >= $toRoute->stop_order) {
                        return false;
                    }

                    if ($selectedDeparture) {
                        $departureSchedule = $train->schedules->first(function ($schedule) use ($selectedFrom, $selectedDeparture) {
                            return $schedule->station_id === (int) $selectedFrom && $schedule->departure === $selectedDeparture;
                        });

                        if (! $departureSchedule) {
                            return false;
                        }
                    }

                    return true;
                })
                ->map(function (Train $train) use ($selectedFrom, $selectedTo) {
                    $departureSchedule = $train->schedules->firstWhere('station_id', (int) $selectedFrom);
                    $arrivalSchedule = $train->schedules->firstWhere('station_id', (int) $selectedTo);

                    $departureTime = optional($departureSchedule)->departure;
                    $arrivalTime = optional($arrivalSchedule)->arrival ?? optional($arrivalSchedule)->departure;

                    $duration = null;
                    if ($departureTime && $arrivalTime) {
                        $start = Carbon::createFromFormat('H:i', $departureTime);
                        $end = Carbon::createFromFormat('H:i', $arrivalTime);

                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }

                        $difference = $start->diff($end);
                        $hours = $difference->days * 24 + $difference->h;
                        $minutes = $difference->i;
                        $duration = sprintf('%02d j %02d m', $hours, $minutes);
                    }

                    $routeStops = $train->routes
                        ->sortBy('stop_order')
                        ->pluck('station.name')
                        ->filter()
                        ->values()
                        ->toArray();

                    return [
                        'train_name' => $train->name,
                        'train_code' => $train->code,
                        'class' => $train->class,
                        'departure_time' => $departureTime,
                        'arrival_time' => $arrivalTime,
                        'duration' => $duration,
                        'from_station' => optional($departureSchedule?->station)->name,
                        'to_station' => optional($arrivalSchedule?->station)->name,
                        'route_stops' => $routeStops,
                    ];
                })
                ->values();
        }

        $trainImageMap = [
            'KA-ABA' => 'https://images.unsplash.com/photo-1523634921619-37ce98fb2807?auto=format&fit=crop&w=700&q=80',
            'KA-TAKA' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?auto=format&fit=crop&w=700&q=80',
            'KA-CL' => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?auto=format&fit=crop&w=700&q=80',
        ];

        $popularTrains = Train::query()
            ->withCount('routes')
            ->orderByDesc('routes_count')
            ->take(5)
            ->get()
            ->map(function (Train $train) use ($trainImageMap) {
                $classColor = match ($train->class) {
                    'Eksekutif' => 'bg-indigo-500',
                    'Eksekutif & Luxury' => 'bg-purple-500',
                    'KRL' => 'bg-emerald-500',
                    default => 'bg-slate-700',
                };

                return [
                    'name' => $train->name,
                    'code' => $train->code,
                    'class' => $train->class,
                    'image' => $trainImageMap[$train->code] ?? 'https://images.unsplash.com/photo-1519677100203-a0e668c92439?auto=format&fit=crop&w=700&q=80',
                    'color' => $classColor,
                ];
            });

        $sameStationSelected = $selectedFrom && $selectedTo && (int) $selectedFrom === (int) $selectedTo;

        return view('home', [
            'stations' => $stations,
            'trainClasses' => $trainClasses,
            'availableDepartureTimes' => $availableDepartureTimes,
            'selectedFrom' => $selectedFrom,
            'selectedTo' => $selectedTo,
            'selectedDeparture' => $selectedDeparture,
            'selectedClass' => $selectedClass,
            'searchPerformed' => $searchPerformed,
            'searchResults' => $searchResults,
            'popularTrains' => $popularTrains,
            'sameStationSelected' => $sameStationSelected,
        ]);
    }
}
