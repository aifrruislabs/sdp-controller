<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientIncident extends Model
{
    //Fillables
    protected $fillable = [

    	'clientId', 'snortAlertId', 'snortClassId', 'incidentResponseId',

    	'incidentTitle', 'incidentResponse', 'incidentResolved'

    ];
}
