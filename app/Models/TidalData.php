<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TidalData extends Model
{
    use HasFactory;

    protected $table = 'tidal_data';

    protected $fillable = [
        'location_id',
        'upload_id',
        'record_date',
        'hour',
        'water_level',
    ];

    protected $casts = [
        'record_date' => 'date',
        'hour' => 'integer',
        'water_level' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function upload()
    {
        return $this->belongsTo(DataUpload::class, 'upload_id');
    }
}
