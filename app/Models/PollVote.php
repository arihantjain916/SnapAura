<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PollVote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "pool_id",
        "option"
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = auth()->user()->id;
        });
    }
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d',
            'updated_at' => 'datetime:Y-m-d',
        ];
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class, 'pool_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
