<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Media;
use App\Models\Album;
use App\Models\Tag;
use App\Models\Notification;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasUuids, HasFactory, Notifiable, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc'); // 'asc' để sắp xếp tăng dần, 'desc' để sắp xếp giảm dần
        });
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    //cusstom payload
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->first_name . ' ' . $this->last_name,
            'role' => $this->role,
            'id' => $this->id,
        ];
    }
    /**
     * The attributes that are mass assignable.
     *
     */
    protected $table = 'users';
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        "avatar",
        "background",
        'password',
        'role',
        'phone',
        'email_verified_at',
        'google_id',
        'verification_token',
        "verification_token_expires_at"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function getRoleAttribute($value)
    {
        return $value == '0' ? 'ADMIN' : 'USER';
    }

    public function reactionMedia()
    {
        return $this->belongsToMany(Media::class, "reaction_media")->withPivot(["feeling_id"])->withTimestamps();
    }
    public function albums()
    {
        return $this->belongsToMany(Album::class, "user_album")->withPivot(["invitationStatus", 'albumRole'])->wherePivot("invitationStatus", "1")->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, "user_relationship", 'followee_id', 'follower_id')->withPivot(["user_status"])->withTimestamps()->wherePivot("user_status", "=", "1");
    }

    public function followees()
    {
        return $this->belongsToMany(User::class, "user_relationship", 'follower_id', 'followee_id')->withPivot(["user_status"])->withTimestamps()->wherePivot("user_status", "=", "1");
    }


    public function mediaOwner()
    {
        return $this->hasMany(Media::class, 'media_owner_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'ownerUserCreated_id', 'id');
    }

    public function reportMedias()
    {
        return $this->belongsToMany(Media::class, 'report_media');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, "receiver_id");
    }
}
