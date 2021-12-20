<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'serviceTitle', 'serviceInfo', 'servicePort', 'serviceScore',

    ];
}
