<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pool extends Model
{
    use HasFactory, HasUuids;

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
            "option" => "array",
        ];
    }

    protected $fillable = [
        "question",
        "option"
    ];

    public function votes()
    {
        return $this->hasMany(PoolVote::class, 'pool_id');
    }
}
