<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_a_id',
        'station_b_id',
        'line_name',
        'transport_mode',
        'distance_km',
        'average_duration_minutes',
        'base_fare',
        'is_active',
    ];

    protected $casts = [
        'distance_km' => 'float',
        'average_duration_minutes' => 'integer',
        'base_fare' => 'float',
        'is_active' => 'boolean',
    ];

    public function stationA()
    {
        return $this->belongsTo(Station::class, 'station_a_id');
    }

    public function stationB()
    {
        return $this->belongsTo(Station::class, 'station_b_id');
    }
}
