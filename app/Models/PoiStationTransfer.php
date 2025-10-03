<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoiStationTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'point_of_interest_id',
        'station_id',
        'mode',
        'duration_minutes',
        'distance_km',
        'estimated_cost',
        'description',
    ];

    protected $casts = [
        'distance_km' => 'float',
        'estimated_cost' => 'float',
    ];

    public function pointOfInterest()
    {
        return $this->belongsTo(PointOfInterest::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
