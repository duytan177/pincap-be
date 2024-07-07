<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ConversationAI extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected $table="conversation_AI";
    protected $fillable = [
        'id',
        'content',
        'type_qa',
        'type_ai',
        'image_url',
        'user_id',
    ];
    protected $hidden=[
        'deleted_at'
    ];
}
