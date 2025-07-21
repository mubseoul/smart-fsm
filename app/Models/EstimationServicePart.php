<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimationServicePart extends Model
{
    use HasFactory;
    protected $fillable=[
        'estimation_id',
        'service_part_id',
        'quantity',
        'amount',
        'type',
        'description',
    ];

    public function serviceParts()
    {
        return $this->hasOne('App\Models\ServicePart','id','service_part_id');
    }
}
