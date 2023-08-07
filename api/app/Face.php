<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Face extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'clientId', 'faceLocation'

    ];
}
