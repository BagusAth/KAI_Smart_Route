<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'class',
    ];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
