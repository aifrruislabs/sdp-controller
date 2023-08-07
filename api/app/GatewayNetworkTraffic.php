<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatewayNetworkTraffic extends Model
{
    //Fillables
    //
    //	RX -> eth0
    //
    //	TX -> eth0
    protected $fillable = [

    	'userId', 'gatewayId', 'rxCount', 'txCount', 'cpuPercent',

    ];
}
