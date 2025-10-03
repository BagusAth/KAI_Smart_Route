<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'code',
        'name',
        'description',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
