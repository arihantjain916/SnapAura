<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PostImages extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "post_id",
        "image"
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
