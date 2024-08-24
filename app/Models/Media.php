<?php

namespace App\Models;

use App\Enums\Album_Media\MediaType;
use App\Enums\Album_Media\Privacy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Album;
use App\Models\Tag;
use App\Models\Feeling;
use App\Models\Comment;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Media extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc'); // 'asc' để sắp xếp tăng dần, 'desc' để sắp xếp giảm dần
        });

        static::addGlobalScope('filterIsCreatedTrue', function (Builder $builder) {
            $builder->where('is_created', true);
        });
    }


    protected $table = 'medias';
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
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_created' => "boolean",
            "is_comment" => "boolean"
        ];
    }

    public function getTypeAttribute($value)
    {
        return $value ? MediaType::getKey($value) : $value;
    }

    public function getPrivacyAttribute($value)
    {
        return Privacy::getKey($value);
    }


    public function userComments(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comments')->withPivot(["content", 'id', "image_url"])->withTimestamps()->orderBy('created_at');
    }

    public function reactionUser()
    {
        return $this->belongsToMany(User::class, "reaction_media")->withPivot(["feeling_id"])->withTimestamps();
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_media')->withTimestamps();
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'media_tag')->withTimestamps();
    }

    public function userOwner()
    {
        return $this->belongsTo(User::class, 'mediaOwner_id', 'id');
    }

    public function mediaReported()
    {
        return $this->belongsToMany(User::class, 'report_media')->withTimestamps();
    }

    public function allFeelings(): BelongsToMany
    {
        return $this->belongsToMany(Feeling::class, "reaction_media")
            ->withPivot(["feeling_id"])
            ->groupBy(['feeling_id', "media_id"])
            ->select(
                'feeling_id as id',
                'feeling_type',
                'icon_url',
                DB::raw("COUNT(feeling_id) as total")
            )
            ->orderBy("total", "desc");
    }

    public function feelings(): BelongsToMany
    {
        return $this->belongsToMany(Feeling::class, "reaction_media")
            ->withPivot(["feeling_id"])
            ->groupBy(['feeling_id', "media_id"])
            ->select('feeling_id as id', 'feeling_type', 'icon_url')
            ->orderByRaw('COUNT(*) DESC')
            ->take(3);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'media_id', 'id');
    }
}
