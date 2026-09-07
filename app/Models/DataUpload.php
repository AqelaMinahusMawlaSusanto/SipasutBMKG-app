<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'period_month',
        'period_year',
        'status',
        'error_message',
        'total_records',
    ];

    protected $casts = [
        'period_month' => 'integer',
        'period_year' => 'integer',
        'total_records' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function tidalData()
    {
        return $this->hasMany(TidalData::class, 'upload_id');
    }
}
