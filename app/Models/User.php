<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasUuids;
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        "remember_token",
        "profile",
        "username",
        'provider',
        'provider_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        "created_at",
        "updated_at",
        'provider',
        'provider_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:Y-m-d',
            'password' => 'hashed',
            'created_at' => 'datetime:Y-m-d',
            'updated_at' => 'datetime:Y-m-d',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
    }

    public function followRequestsSent()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    // Follow requests received by the user
    public function followRequestsReceived()
    {
        return $this->hasMany(Follow::class, 'followed_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function postsLike()
    {
        return $this->hasMany(PostLike::class);
    }
    public function notifications(){
        return $this->hasMany(Notification::class);
    }
}
