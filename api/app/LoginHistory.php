<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    //Fillables
    protected $fillable = [

        'userId', 'userOs', 'userIp'

    ];
}
