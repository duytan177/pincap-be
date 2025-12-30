<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\MediaReport;
use App\Models\UserReport;
use App\Traits\HasPaginateOrAll;
use App\Traits\OrderableTrait;

class ReportReason extends Model
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, HasPaginateOrAll, OrderableTrait;

    protected $table = 'reasons_report';
    protected $fillable = [
        'id',
        'title',
        'description'
    ];
    protected $hidden = [];

    public function mediaReports()
    {
        return $this->hasMany(MediaReport::class, 'reason_report_id', 'id');
    }

    public function userReports()
    {
        return $this->hasMany(UserReport::class, 'reason_report_id', 'id');
    }
}
