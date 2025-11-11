<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserSocialAccount extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_social_accounts';

    protected $fillable = [
        'user_id',
        'name',
        'avatar',
        'permalink',
        'social_id',
        'access_token',
        'access_token_expired',
        'refresh_token',
        'refresh_token_expired',
        'social_type',
    ];

    protected $casts = [
        'access_token_expired' => 'datetime',
        'refresh_token_expired' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'access_token_expired',
        'refresh_token',
        'refresh_token_expired',
    ];
    /**
    * Get the user that owns the social account.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
