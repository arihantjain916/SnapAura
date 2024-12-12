<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Tag extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'tag_id', 'post_id');
    }

}
