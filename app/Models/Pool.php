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
            $model->created_by = auth()->user()->id;
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
        "options"
    ];

    public function votes()
    {
        return $this->hasMany(PoolVote::class, 'pool_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
