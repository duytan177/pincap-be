<?php

namespace App\Models;

use App\Enums\Album_Media\AlbumRole;
use App\Enums\Album_Media\InvitationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Media;
use App\Traits\OrderableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Tymon\JWTAuth\Facades\JWTAuth;

class Album extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, OrderableTrait;
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('updated_at', 'desc'); // 'asc' để sắp xếp tăng dần, 'desc' để sắp xếp giảm dần
        });

    }
    protected $table = "albums";
    protected $fillable = [
        'id',
        'album_name',
        'image_cover',
        'description',
        'privacy',
    ];
    protected $hidden = [
        'deleted_at'
    ];

    public function getPrivacyAttribute($value)
    {
        return $value == '0' ? 'PRIVATE' : 'PUBLIC';
    }

    public function allUser(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "user_album")->withPivot(["invitation_status", 'album_role'])->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "user_album")->withPivot(["invitation_status", 'album_role'])->withTimestamps()->wherePivot("album_role", "!=", AlbumRole::OWNER)->wherePivot("invitation_status", "=", InvitationStatus::ACCEPTED);
    }

    public function userOwner(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "user_album")->withPivot(["invitation_status", 'album_role'])->withTimestamps()->wherePivot("album_role", AlbumRole::OWNER);
    }
    public function medias(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'album_media')->where("is_created", operator: true)->withTimestamps();
    }

    public function scopeOwnedBy(Builder $query, string $userId): Builder
    {
        return $query->whereHas('userOwner', function (Builder $sub) use ($userId) {
            $sub->where('user_id', $userId)
                ->where('album_role', AlbumRole::OWNER);
        });
    }

    public static function getList(array $params, string $privacy = "", bool $private = false, ?array $order = null): Builder
    {
        $albums = Album::query()
            ->when($privacy !== "", function ($query) use ($privacy) {
                $query->where('privacy', $privacy);
            })
            ->when($private, function ($query) {
                $query->whereHas("userOwner", function ($query) {
                    $query->where("user_id", JWTAuth::user()->getAttribute("id"));
                });
            });

        $albums = $albums->where(function ($query) use ($params) {
            if (!empty($params['album_name'])) {
                $query->orWhere('album_name', 'like', "%{$params['album_name']}%");
            }

            if (!empty($params['description'])) {
                $query->orWhere('description', 'like', "%{$params['description']}%");
            }

            if (!empty($params['user_id'])) {
                $query->whereHas("userOwner", function ($query) use ($params) {
                    $query->where("user_id", $params['user_id'])
                        ->where("album_role", AlbumRole::OWNER);
                    ;
                });
            }
        });

        $albums = self::scopeApplyOrder($albums, $order);

        return $albums;
    }

    public function scopeWithUserRoleAndStatus(Builder $query, ?string $role = null, ?string $status = null): Builder
    {
        return $query->whereHas("allUser", function ($q) use ($role, $status) {
            if ($role) {
                $q->wherePivot("album_role", $role);
            }

            if ($status) {
                $q->wherePivot("invitation_status", $status);
            }
        });
    }
}
