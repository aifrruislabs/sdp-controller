<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GatewayLog extends Model
{
    //Fillables
    protected $fillable = [
        
        'userId', 'gatewayId', 'gatewayPcapTime', 'gatewayPcapLog'
        
    ];
}
