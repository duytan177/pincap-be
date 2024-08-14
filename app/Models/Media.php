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
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        return MediaType::getKey($value);
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

    public function reactionCounts()
    {
        return DB::table('reaction_media')
        ->select('feeling_id', DB::raw('count(*) as count'))
        ->where('media_id', $this->id)
        ->groupBy('feeling_id')
        ->get();
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

    public function feelings()
    {
        return $this->belongsTo(Feeling::class, 'feeling_id', 'id');
    }
}
