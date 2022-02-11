<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GatewayServiceStatusController extends Controller
{
    //Fillables
    protected $fillable = [

    	'userId', 'gatewayId', 'serviceId', 'serviceStatus'

    ];
}
