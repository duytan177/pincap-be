<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

class UserRelationship extends Model
{
    use HasFactory,HasUuids,Notifiable;

    protected $table="user_relationship";
    protected $fillable = [
        'id',
        'followee_id',
        'follower_id',
        'user_status',
        'created_at',
        'updated_at'
    ];
    protected $hidden=[

    ];

    protected function casts()
    {
        return [
            "deleted_at" => "dates"
        ];
    }

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
