<?php

namespace App\Models;

use App\Enums\Album_Media\Privacy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Media extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected static function booted()
    {
        // Sắp xếp theo created_at mới nhất
        static::addGlobalScope(function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }

    protected $table='medias';
    protected $fillable = [
        'id',
        'media_name',
        'media_url',
        'description',
        'type',
        'privacy',
        'is_created',
        'is_comment',
        'media_owner_id',
    ];
    protected $hidden=[
        'deleted_at'
    ];


    public function getTypeAttribute($value){
        return $value=='0'?'IMAGE':'VIDEO';
    }
    public function getPrivacyAttribute($value){
        return $value=='0'?'PRIVATE':'PUBLIC';
    }

    public function  userComments(){
        return $this->belongsToMany(User::class,'comments')->withPivot(["content",'id'])->withTimestamps();
    }
    public function reactionUser(){
        return $this->belongsToMany(User::class,"reaction_media")->withPivot(["feeling_id"])->withTimestamps();
    }

    public function albums(){
        return $this->belongsToMany(Album::class,'album_media')->withTimestamps();
    }
    public function tags(){
        return $this->belongsToMany(Tag::class,'media_tag')->withTimestamps();
    }

    public function userOwner(){
        return $this->belongsTo(User::class,'mediaOwner_id','id');
    }

    public function mediaReported(){
        return $this->belongsToMany(User::class,'report_media')->withTimestamps();
    }

}
