<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDetail extends Model
{
    use HasFactory;
    protected $fillable=[
        'client_id',
        'user_id',
        'company',
        'service_address',
        'service_city',
        'service_state',
        'service_country',
        'service_zip_code',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_zip_code',
        'parent_id',
    ];
}
