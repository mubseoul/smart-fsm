<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable=[
        'client',
        'wo_id',
        'invoice_id',
        'invoice_date',
        'due_date',
        'total',
        'discount',
        'status',
        'parent_id',
        'notes',
    ];

    public static $status=[
        'unpaid'=>'Unpaid',
        'paid'=>'Paid',
    ];

    public function clients()
    {
        return $this->hasOne('App\Models\User','id','client');
    }

    public function workorders()
    {
        return $this->hasOne('App\Models\WorkOrder','id','wo_id');
    }
}
