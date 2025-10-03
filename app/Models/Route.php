<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'train_id',
        'station_id',
        'stop_order',
        'platform_id',
        'arrival_offset_minutes',
        'departure_offset_minutes',
        'stop_duration_minutes',
        'notes',
    ];

    public function train()
    {
        return $this->belongsTo(Train::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
