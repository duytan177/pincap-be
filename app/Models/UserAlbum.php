<?php

namespace App\Models;

use App\Enums\Album_Media\InvitationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class UserAlbum extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $table = 'user_album';
    protected $fillable = [
        'id',
        'user_id',
        'invitation_status',
        'albumRole',
    ];
    protected $hidden = [];

    public function getInvitationStatusAttribute($value)
    {
        $value = (int)$value;
        switch ($value) {
            case 0:
                return InvitationStatus::getKey($value);
            case 1:
                return InvitationStatus::getKey($value);
            case 2:
                return InvitationStatus::getKey($value);
        }
    }
}
