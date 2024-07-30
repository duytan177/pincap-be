<?php

namespace App\Models;

use App\Enums\Album_Media\AlbumRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Album extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

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
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "user_album")->withPivot(["invitation_status", 'album_role'])->withTimestamps()->wherePivot("album_role", "!=", AlbumRole::OWNER);
    }

    public function userOwner(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "user_album")->withPivot(["invitation_status", 'album_role'])->withTimestamps()->wherePivot("album_role", AlbumRole::OWNER);
    }
    public function medias()
    {
        return $this->belongsToMany(Media::class, 'album_media')->withTimestamps();
    }
}
