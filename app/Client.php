<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'clientId', 'firstName', 'middleName', 'lastName', 'username', 'password',

    	'accessToken', 'totalTrustScore', 'encryptionKey', 'hmacKey'

    ];
}
