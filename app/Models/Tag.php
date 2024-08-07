<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ramsey\Uuid\Uuid;

class Tag extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected $table='tags';
    protected $fillable = [
        'id',
        'tag_name',
        'owner_user_created_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Uuid::uuid4()->toString();
            }
        });
    }

    protected $hidden=[];

    public function userOwner()
    {
        return $this->belongsTo(User::class, 'owner_user_created_id');
    }
    public function medias() : BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_tag')->withTimestamps();
    }

    public function latestMedia() : BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_tag')->withTimestamps()->latest('created_at')->take(1);
    }
}
