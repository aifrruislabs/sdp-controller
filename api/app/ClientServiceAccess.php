<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientServiceAccess extends Model
{
    //Fillable
    protected $fillable = [

    	'userId', 'gatewayId', 'serviceId', 'clientId'

    ];
}
