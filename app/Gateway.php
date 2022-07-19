<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    //Fillables
    /*
    *
    *	gatewayNetworkAccessibility (0 - Internal, 1 - External)
    *
    *
    */
    protected $fillable = [

    	'userId', 'gatewayTitle', 'gatewayInfo', 

        'gatewayNetworkAccessibility', 'gatewayIP', 

    	'gatewayAccessToken', 'gatewayServicesList'

    ];
}
