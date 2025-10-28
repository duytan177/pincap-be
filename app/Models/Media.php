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
use App\Models\ReactionMedia;
use App\Traits\HasPaginateOrAll;
use App\Traits\OrderableTrait;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Laravel\Prompts\select;

class Media extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, OrderableTrait, HasPaginateOrAll;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('medias.created_at', 'desc'); // 'asc' để sắp xếp tăng dần, 'desc' để sắp xếp giảm dần
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
        return $value === null ? null : MediaType::getKey(value: $value);
    }

    public function getPrivacyAttribute($value)
    {
        return Privacy::getKey($value);
    }


    public function userComments(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comments')->withPivot(["content", 'id', "image_url"])->withTimestamps()->wherePivotNull("deleted_at")->orderBy('created_at');
    }

    public function reactionUser()
    {
        return $this->belongsToMany(User::class, "reaction_media")->withPivot(["feeling_id"])->withTimestamps();
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_media')->wherePivotNull("deleted_at")->withTimestamps();
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'media_tag')->wherePivotNull("deleted_at")->withTimestamps();
    }

    public function userOwner()
    {
        return $this->belongsTo(User::class, 'media_owner_id', 'id');
    }

    public function mediaReported()
    {
        return $this->belongsToMany(User::class, 'report_media')->wherePivotNull("deleted_at")->withTimestamps();
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

    public function reactions(): HasMany
    {
        return $this->hasMany(ReactionMedia::class, "media_id", "id");
    }

    public static function getList(array $params, bool $isCreated = false, string $privacy = "", bool $private = false, ?array $order = null): Builder
    {
        $medias = Media::query()
            ->when($isCreated || $params["my_media"], function ($query) use ($isCreated) {
                $query->where('is_created', $isCreated);
            })
            ->when($privacy !== "", function ($query) use ($privacy) {
                $query->where('privacy', $privacy);
            })
            ->when($private, function ($query) {
                $query->where('media_owner_id',JWTAuth::user()->getAttribute("id"));
            })
            ->when(!empty($params['user_id']), function ($query) use ($params) {
                $query->where('media_owner_id', $params['user_id']);
            })
            ->when(MediaType::hasValue($params['type'] ?? null), function ($query) use ($params) {
                $query->where('type', $params['type']);
            })
            ->where(function ($query) use ($params) {
                if (!empty($params['tag_name'])) {
                    $query->orWhereHas('tags', function ($q) use ($params) {
                        $q->where('tags.tag_name', 'like', "%{$params['tag_name']}%");
                    });
                }

                if (!empty($params['title'])) {
                    $query->orWhere('media_name', 'like', "%{$params['title']}%");
                }

                if (!empty($params['description'])) {
                    $query->orWhere('description', 'like', "%{$params['description']}%");
                }

                if (!empty($params['user_name'])) {
                    $query->orWhereIn('media_owner_id', function ($subQuery) use ($params) {
                        $subQuery->select('id')
                            ->from('users')
                            ->where('first_name', 'like', "%{$params['user_name']}%")
                            ->orWhere('last_name', 'like', "%{$params['user_name']}%");
                    });
                }
            });


        $medias = self::scopeApplyOrder($medias, $order);

        return $medias;
    }
}
