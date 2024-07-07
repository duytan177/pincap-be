<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

class UserRelationship extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected $table="user_relationship";
    protected $fillable = [
        'id',
        'followee_id',
        'follower_id',
        'user_status',
    ];
    protected $hidden=[
        'deleted_at'
    ];

    public function getUserStatusAttribute($value)
    {
        return $value=='0'?'BLOCK':'FOLLOW';
    }

    public function followees()
    {
        return $this->belongsTo(User::class, 'followee_id', 'id');
    }
    public function followers()
    {
        return $this->belongsTo(User::class, 'follower_id', 'id');
    }
}
