<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrustScoreTracker extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'clientId', 'trustScoreFactorId', 'percentScored',

    ];
}
