<?php

namespace App\Models;

use App\Enums\Album_Media\StateReport;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class UserReport extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $table = 'user_reports';
    protected $primaryKey = "id";
    protected $fillable = [
        'id',
        'report_state',
        'reason_report_id',
        'user_id',
        'user_report_id',
        'other_reasons',
    ];
    protected $hidden = [

    ];

    public function getStateAttribute($value)
    {
        return $value == '0' ? StateReport::getKey(0) : StateReport::getKey(1);
    }
}
