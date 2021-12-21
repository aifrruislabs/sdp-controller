<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'gatewayTitle', 'gatewayInfo', 'gatewayIP', 

    	'gatewayAccessToken', 'gatewayServicesList'

    ];
}
