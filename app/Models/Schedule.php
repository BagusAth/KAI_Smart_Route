<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'train_id',
        'station_id',
        'platform_id',
        'arrival',
        'departure',
        'price',
        'available_seats',
        'status',
        'remarks',
    ];

    protected $casts = [
        'price' => 'float',
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
