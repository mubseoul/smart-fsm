<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePart extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'sku',
        'unit',
        'price',
        'description',
        'type',
        'parent_id',
    ];

    public function serviceTasks()
    {
        return $this->hasMany('App\Models\ServiceTask','service_id');
    }
}
