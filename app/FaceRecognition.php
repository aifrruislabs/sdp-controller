<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaceRecognition extends Model
{
    //Fillables
    protected $fillable = [

    	'clientId', 'attemptStatus'

    ];
}
