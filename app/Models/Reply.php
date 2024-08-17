<?php

namespace App\Models;

use App\Models\User;
use App\Models\Feeling;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

class Reply extends Model
{
    use HasFactory, HasUuids, Notifiable;
    protected $fillable = [
        'id',
        'user_id',
        'comment_id',
        'content',
        'image_url'
    ];

    public function userComment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function feelings(): BelongsToMany
    {
        return $this->belongsToMany(Feeling::class, "reaction_replies")
            ->groupBy(['feeling_id', "reply_id"])
            ->select('feeling_id as id', 'feeling_type', 'icon_url')
            ->orderByRaw('COUNT(*) DESC')
            ->take(3);
    }

    public function allFeelings(): BelongsToMany
    {
        return $this->belongsToMany(Feeling::class, "reaction_replies");
    }
}
