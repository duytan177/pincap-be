<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Feeling extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;
    protected $fillable = [
        'id',
        'feeling_type',
        'icon_url'
    ];

    public function reactionMedia(){
        return $this->hasMany(ReactionMedia::class,'feeling_id','id');
    }
}
