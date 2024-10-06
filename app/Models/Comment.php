<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Comment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "user_id",
        "comment",
        "parent_id",
        "post"
    ];

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function casts()
    {
        return [
            "created_at" => "datetime:d-m-Y",
            "updated_at" => "datetime:d-m-Y",
        ];
    }
}
