<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Media;
use App\Models\Feeling;
use App\Traits\HasPaginateOrAll;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comment extends Model
{
    use HasFactory, HasUuids, Notifiable, HasPaginateOrAll;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    protected $fillable = [
        'id',
        'user_id',
        'media_id',
        'content',
        'image_url'
    ];
    protected $hidden = [
        'deleted_at'
    ];

    public function userComment() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commentsMedia()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function replies(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'replies')->withPivot(["content", 'id', "image_url"])->withTimestamps()->orderBy('created_at');
    }

    public function feelings(): BelongsToMany
    {
        return $this->belongsToMany(Feeling::class, "reaction_comments")
                ->groupBy(['feeling_id', "comment_id"])
                ->select('feeling_id as id', 'feeling_type', 'icon_url')
                ->orderByRaw('COUNT(*) DESC')
                ->take(3);
    }

    public function allFeelings(): BelongsToMany
    {
        return $this->belongsToMany(Feeling::class, "reaction_comments");
    }
}
