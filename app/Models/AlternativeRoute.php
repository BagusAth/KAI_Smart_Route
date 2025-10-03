<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlternativeRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_station_id',
        'destination_station_id',
        'primary_train_id',
        'via_station_id',
        'alternative_train_id',
        'transfer_type',
        'total_duration_minutes',
        'notes',
    ];

    public function origin()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destination()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }

    public function primaryTrain()
    {
        return $this->belongsTo(Train::class, 'primary_train_id');
    }

    public function viaStation()
    {
        return $this->belongsTo(Station::class, 'via_station_id');
    }

    public function alternativeTrain()
    {
        return $this->belongsTo(Train::class, 'alternative_train_id');
    }
}
