<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;
    protected $fillable=[
        'wo_id',
        'wo_detail',
        'type',
        'client',
        'asset',
        'due_date',
        'status',
        'priority',
        'notes',
        'assign',
        'preferred_date',
        'preferred_time',
        'preferred_note',
        'parent_id',
    ];

    public static $status=[
        'pending'=>'Pending',
        'approved'=>'Approved',
        'rejected'=>'Rejected',
        'on_hold'=>'On Hold',
        'cancelled'=>'Cancelled',
        'completed'=>'Completed',
    ];

    public function clients()
    {
        return $this->hasOne('App\Models\User','id','client');
    }
    public function assigned()
    {
        return $this->hasOne('App\Models\User','id','assign');
    }

    public function assets()
    {
        return $this->hasOne('App\Models\Asset','id','asset');
    }
    public function types()
    {
        return $this->hasOne('App\Models\WOType','id','type');
    }

    public function serviceParts()
    {
        return $this->hasMany('App\Models\WOServicePart','wo_id','id');
    }
    public function getWorkorderTotalAmount()
    {
        $woTotal = 0;
        foreach ($this->serviceParts as $serviceParts) {
            $woTotal += $serviceParts->amount;
        }
        return $woTotal;
    }
    public function services()
    {
        return $this->hasMany('App\Models\WOServicePart','wo_id','id')->where('type','service');
    }
    public function parts()
    {
        return $this->hasMany('App\Models\WOServicePart','wo_id','id')->where('type','part');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\WOServiceTask','wo_id','id');
    }

    public function appointments()
    {
        return $this->hasOne('App\Models\WOServiceAppointment','wo_id','id');
    }
}
