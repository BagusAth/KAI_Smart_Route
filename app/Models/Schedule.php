<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'train_id',
        'station_id',
        'departure_date', // Tambahkan ini
        'arrival',
        'departure',
        'platform_id',
        'price',
        'available_seats',
        'status',
        'remarks',
    ];

    protected $casts = [
        'departure_date' => 'date', // Tambahkan casting
        'price' => 'decimal:2',
        'available_seats' => 'integer',
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
