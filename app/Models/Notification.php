<?php

namespace App\Models;

use App\Enums\Notifications\NotificationType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

class Notification extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

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
}
