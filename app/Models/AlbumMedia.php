<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AlbumMedia extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    //    protected $table='album_media';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'album_id',
        'media_id',
        'created_at',
        'updated_at',
        'added_by_user_id'
    ];
    protected $hidden = [
        'deleted_at'
    ];
}
