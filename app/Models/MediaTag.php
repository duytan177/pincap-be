<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MediaTag extends Model
{
    use HasApiTokens, HasFactory,HasUuids,Notifiable,SoftDeletes;
    protected $table='media_tag';

    protected $fillable = [
        'id',
        'media_id',
        'tag_id',
    ];
    protected $hidden=[];
}
