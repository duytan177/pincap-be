<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prompt extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = "prompts";
    protected $fillable = [
        'id',
        'key',
        'system_prompt',
        'user_prompt',
        'created_at',
        "updated_at"
    ];
    protected $hidden = [
        'deleted_at'
    ];
}
