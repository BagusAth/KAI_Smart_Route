<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'city',
        'island',
    ];

    public function departures()
    {
        return $this->hasMany(Schedule::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
