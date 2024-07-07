<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Feeling;
use App\Models\Media;

class ReactionMedia extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $table = 'reaction_media';
    protected $fillable = [
        'id',
        'user_id',
        'media_id',
        'feeling_id'
    ];
    protected $hidden = [];

    public function reaction()
    {
        return $this->belongsTo(Feeling::class, 'feeling_id', 'id');
    }

    public function userReaction()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reactionMedia()
    {
        return $this->belongsTo(Media::class, 'media_id', 'id');
    }
}
