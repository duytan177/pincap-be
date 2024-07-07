<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Reply extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;
    protected $fillable = [
        'id',
        'user_id',
        'comment_id',
        'content',
        'image_url'
    ];

    public function userReply(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function replyComment(){
        return $this->belongsTo(Comment::class, 'comment_id');
    }

}
