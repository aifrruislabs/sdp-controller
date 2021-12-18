<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrustScoreWeight extends Model
{
    //Fillables
    protected $fillable = [

    	'userId', 'scoreWeight1', 'scoreWeight2', 'scoreWeight3',

    	'scoreWeight4', 'scoreWeight5', 'scoreWeight6', 'scoreWeight7',

    	'scoreWeight8', 'scoreWeight9', 'scoreWeight10'

    ];
}
