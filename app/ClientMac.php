<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientMac extends Model
{
    //fillables
    protected $fillable = [

    	'userId', 'clientId', 'macAddr'

    ];
}
