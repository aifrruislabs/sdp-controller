<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'gatewayId', 'clientId', 'publicIp', 'firstName', 'middleName', 'lastName', 

    	'username', 'password', 'accessToken', 'totalTrustScore', 'encryptionKey', 'hmacKey',

    ];
}
