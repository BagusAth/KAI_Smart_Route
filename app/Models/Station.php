<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'city',
    'island',
        'location',
    ];

    public function departures()
    {
        return $this->hasMany(Schedule::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function platforms()
    {
        return $this->hasMany(Platform::class);
    }

    public function outgoingConnections()
    {
        return $this->hasMany(Track::class, 'station_a_id');
    }

    public function incomingConnections()
    {
        return $this->hasMany(Track::class, 'station_b_id');
    }

    public function poiTransfers()
    {
        return $this->hasMany(PoiStationTransfer::class);
    }

    public function defaultForPointsOfInterest()
    {
        return $this->hasMany(PointOfInterest::class, 'default_station_id');
    }
}
