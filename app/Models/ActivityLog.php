<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'action',
        'ip_address',
        'user_agent',
        'description',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($action, $description = null, $user = null): self
    {
        $currentUser = $user ?? auth()->user();

        return self::create([
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser?->name ?? 'Guest/System',
            'user_email' => $currentUser?->email,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
