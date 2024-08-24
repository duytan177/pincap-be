<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReactionMedia extends Model
{
    use HasFactory, HasUuids, Notifiable;

    protected $table = 'reaction_media';
    protected $fillable = [
        'id',
        'user_id',
        'media_id',
        'feeling_id'
    ];
    protected $hidden = [];

    public function userReaction(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reactionMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id', 'id');
    }
}
