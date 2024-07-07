<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Comment extends Model
{
    use HasFactory, HasUuids, Notifiable,SoftDeletes;
    protected $fillable = [
        'id',
        'user_id',
        'media_id',
        'content',
        'image_url'
    ];
    protected $hidden = [
        'deleted_at'
    ];

    public function userComments()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commentsMedia()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }


}
