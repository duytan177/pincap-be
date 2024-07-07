<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ReportReason extends Model
{
    use HasFactory,HasUuids,Notifiable,SoftDeletes;

    protected $table='reasons_report';
    protected $fillable = [
        'id',
        'title',
        'description'
    ];
    protected $hidden=[];


    public function reasonReport(){
        return $this->hasMany(MediaReport::class,'report_reason_id','id');
    }
}
