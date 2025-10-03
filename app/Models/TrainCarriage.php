<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainCarriage extends Model
{
    use HasFactory;

    protected $fillable = [
        'train_id',
        'car_number',
        'carriage_type',
        'class',
        'layout',
        'amenities',
    ];

    protected $casts = [
        'layout' => 'array',
        'amenities' => 'array',
    ];

    public function train()
    {
        return $this->belongsTo(Train::class);
    }
}
