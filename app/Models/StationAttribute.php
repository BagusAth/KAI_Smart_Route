<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'zone',
        'is_interchange',
        'facilities',
        'opening_hours',
        'accessibility',
        'rating',
    ];

    protected $casts = [
        'is_interchange' => 'boolean',
        'facilities' => 'array',
        'accessibility' => 'array',
        'rating' => 'float',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
