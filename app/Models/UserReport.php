<?php

namespace App\Models;

use App\Enums\Album_Media\StateReport;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasPaginateOrAll;
use App\Traits\OrderableTrait;

class UserReport extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, HasPaginateOrAll, OrderableTrait;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_report_id', 'id');
    }

    public function reasonReport()
    {
        return $this->belongsTo(ReportReason::class, 'reason_report_id', 'id');
    }
}
