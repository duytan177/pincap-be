<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class UserAlbum extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $table = 'user_album';
    protected $fillable = [
        'id',
        'user_id',
        'album_id',
        'invitation_status',
        'album_role',
    ];
    protected $hidden = [];

    protected function casts()
    {
        return [
            "invitation_status" => "boolean"
        ];
    }
}
