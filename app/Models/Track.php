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
