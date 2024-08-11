<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory, HasUuids, Notifiable;

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

    public function userComments()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commentsMedia()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
