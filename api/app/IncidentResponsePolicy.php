<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncidentResponsePolicy extends Model
{

	//Change Table Name
	protected $table = 'incident_response_policies';

    //Fillables
    protected $fillable = [

        'userId', 'gatewayId', 'incidentClassTypeId', 'incidentClassTypeDescription', 

        'incidentResponseId', 'incidentResponseDescription'

    ];
}
