<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Tag extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected $table='tags';
    protected $fillable = [
        'id',
        'tag_name',
        'owner_user_created_id',
    ];
    protected $hidden=[];

    public function userOwner(){
        return $this->belongsTo(User::class,'owner_user_created_id');
    }
    public function medias(){
        return $this->belongsToMany(Media::class,'media_tag')->withTimestamps();
    }
}
