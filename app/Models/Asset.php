<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'asset_number',
        'part',
        'parent_asset',
        'giai',
        'order_date',
        'installation_date',
        'purchase_date',
        'warranty_expiration',
        'warranty_notes',
        'description',
        'parent_id',
    ];

    public function parts()
    {
        return $this->hasOne('App\Models\ServicePart','id','part');
    }
    public function parents()
    {
        return $this->hasOne('App\Models\Asset','id','parent_asset');
    }
}
