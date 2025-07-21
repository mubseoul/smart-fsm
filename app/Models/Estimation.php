<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    use HasFactory;
    protected $fillable=[
        'estimation_id',
        'client',
        'asset',
        'due_date',
        'status',
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

    public function assets()
    {
        return $this->hasOne('App\Models\Asset','id','asset');
    }

    public function services()
    {
        return $this->hasMany('App\Models\EstimationServicePart','estimation_id','id')->where('type','service');
    }
    public function parts()
    {
        return $this->hasMany('App\Models\EstimationServicePart','estimation_id','id')->where('type','part');
    }

    public function serviceParts()
    {
        return $this->hasMany('App\Models\EstimationServicePart','estimation_id','id');
    }
    public function getEstimationSubTotalAmount()
    {
        $estimationSubTotal = 0;
        foreach ($this->serviceParts as $serviceParts) {
            $estimationSubTotal += $serviceParts->amount;
        }
        return $estimationSubTotal;
    }
}
