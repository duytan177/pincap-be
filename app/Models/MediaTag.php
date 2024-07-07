<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class MediaTag extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected $table='tags';

    protected $fillable = [
        'id',
        'media_id',
        'tag_id',
    ];
    protected $hidden=[];
}
