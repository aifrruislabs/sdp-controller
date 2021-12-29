<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientGeoLocation extends Model
{
    //fillables
    protected $fillable = [

    	'userId', 'clientId', 'latitude', 'longitude', 'kilometreRadius'

    ];
}
