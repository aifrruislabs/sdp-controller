<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SnortAlert extends Model
{
    //Fillables
    protected $fillable = [

        'userId', 'gatewayId', 'incidentResponseId', 'incidentResponseStatus',
        
        'snortFullAlert', 'snortAlertCode', 'snortAlertTitle', 
        
        'snortAlertClassification', 'snortAlertPriority', 

        'srcIP', 'dstIp'

    ];
}
