<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $table = 'predictions';

    protected $fillable = [
        'location_id',
        'record_date',
        'high_tide_time',
        'high_tide_level',
        'low_tide_time',
        'low_tide_level',
        'status',
    ];

    protected $casts = [
        'record_date' => 'date',
        'high_tide_level' => 'float',
        'low_tide_level' => 'float',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
