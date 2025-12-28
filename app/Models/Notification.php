<?php

namespace App\Models;

use App\Enums\Notifications\NotificationType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Traits\HasPaginateOrAll;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, HasPaginateOrAll;

    protected $table = "notifications";
    protected $fillable = [
        'id',
        'title',
        'content',
        "is_read",
        "link",
        'sender_id',
        'receiver_id',
        'notification_type'
    ];
    protected $hidden = [
        'deleted_at'
    ];
    protected $casts = [
        'notification_type' => NotificationType::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('notifications.created_at', 'desc');
        });
    }


    public function getNotificationTypeAttribute($value)
    {
        return NotificationType::getKey($value);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function getList($query, array $params)
    {
        if (isset($params['is_read'])) {
            $query->where('is_read', $params['is_read']);
        }

        if (isset($params['notification_type'])) {
            $query->where('notification_type', $params['notification_type']);
        }

        return $query;
    }
}
