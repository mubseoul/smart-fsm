<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WORequest extends Model
{
    use HasFactory;
    protected $fillable=[
        'request_detail',
        'client',
        'asset',
        'priority',
        'due_date',
        'status',
        'assign',
        'notes',
        'preferred_date',
        'preferred_time',
        'preferred_note',
        'parent_id',
    ];

    public static $priority=[
        'low'=>'Low',
        'medium'=>'Medium',
        'high'=>'High',
        'critical'=>'Critical',
    ];

    public static $status=[
        'pending'=>'Pending',
        'in_progress'=>'In Progress',
        'completed'=>'Completed',
        'cancel'=>'Cancel',
    ];

    public static $time=[
        'any_time'=>'Any Time',
        'morning'=>'Morning',
        'afternoon'=>'Afternoon',
        'evening'=>'Evening',
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
}
