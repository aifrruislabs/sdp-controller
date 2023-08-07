<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrustScorePolicy extends Model
{
    //Fillables
    protected $fillable = [

    		'userId', 'serviceId', 'scoreFactorId', 'scorePercent'

    ];
}
