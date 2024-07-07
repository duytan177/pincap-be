<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ReactionComment extends Model
{
    use HasFactory,HasUuids,Notifiable;
    protected $table='reaction_comments';
    protected $fillable = [
        'id',
        'user_id',
        'comment_id',
        'feeling_id'
    ];
    public function Reaction(){
        return $this->belongsTo(Feeling::class,'feeling_id','id');
    }

    public function userReaction(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function reactionComment(){
        return $this->belongsTo(Comment::class,'comment_id','id');
    }

}
