<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WOServiceTask extends Model
{
    use HasFactory;
    protected $fillable=[
        'wo_id',
        'service_part_id',
        'service_task',
        'duration',
        'description',
        'status',
    ];


    public static $status=[
        'pending'=>'Pending',
        'in_progress'=>'In Progress',
        'on_hold'=>'On Hold',
        'completed'=>'Completed',
    ];


    public function services()
    {
        return $this->hasOne('App\Models\ServicePart','id','service_part_id');
    }


}
