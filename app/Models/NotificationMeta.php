<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NotificationMeta extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        "user_id",
        "post_id",
        "notification_id",
        "button_text",
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
