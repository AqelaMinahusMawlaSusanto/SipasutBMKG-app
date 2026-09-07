<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'latitude',
        'longitude',
        'description',
        'institution',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function tidalData()
    {
        return $this->hasMany(TidalData::class);
    }

    public function uploads()
    {
        return $this->hasMany(DataUpload::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
