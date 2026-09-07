<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'location_id',
        'is_active',
        'threshold_value',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold_value' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
