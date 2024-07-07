<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Media;

class Album extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected $table="albums";
    protected $fillable = [
        'id',
        'album_name',
        'image_cover',
        'description',
        'privacy',
    ];
    protected $hidden=[
        'deleted_at'
    ];

    public function getPrivacyAttribute($value)
    {
        return $value=='0'?'PRIVATE':'PUBLIC';
    }
    public function members()
    {
        return $this->belongsToMany(User::class, "user_album")->withPivot(["invitationStatus",'albumRole'])->withTimestamps();
    }

    public function userOwner()
    {
        return $this->belongsTo(User::class, "user_id", 'id');
    }
    public function medias()
    {
        return $this->belongsToMany(Media::class, 'album_media')->withTimestamps();
    }
}
