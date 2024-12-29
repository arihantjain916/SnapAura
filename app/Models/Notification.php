<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'message',
        'type',
        'link',
        'is_read',
        'read_at',
        'action_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
