<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reply extends Model
{
    use HasFactory,HasUuids,Notifiable;
    protected $fillable = [
        'id',
        'user_id',
        'comment_id',
        'content',
        'image_url'
    ];
}
