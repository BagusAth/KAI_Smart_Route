<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointOfInterest extends Model
{
    use HasFactory;

    protected $table = 'points_of_interest';

    protected $fillable = [
        'name',
        'category',
        'city',
        'island',
        'location',
        'default_station_id',
    ];

    public function defaultStation()
    {
        return $this->belongsTo(Station::class, 'default_station_id');
    }

    public function transfers()
    {
        return $this->hasMany(PoiStationTransfer::class, 'point_of_interest_id');
    }
}
