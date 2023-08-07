<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrustScoreWeight extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'scoreFactorId', 'scoreFactorPercent',

    ];
}
