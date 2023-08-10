<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncidentResponse extends Model
{
    //Fillables
    protected $fillable = [

        'userId', 'gatewayId', 'incidentClassTypeId', 'incidentClassTypeDescription', 

        'incidentResponseId', 'incidentResponseDescription'

    ];
}
