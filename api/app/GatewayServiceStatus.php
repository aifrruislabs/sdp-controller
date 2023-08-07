<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatewayServiceStatus extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'gatewayId', 'serviceId', 'serviceStatus',

    ];
}
