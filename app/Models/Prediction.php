<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $location_id
 * @property \Illuminate\Support\Carbon $record_date
 * @property string|null $high_tide_time
 * @property float $high_tide_level
 * @property string|null $low_tide_time
 * @property float $low_tide_level
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 */
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