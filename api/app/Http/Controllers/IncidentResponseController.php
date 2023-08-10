<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncidentResponseController extends Controller
{
    //getSnortICDResponses
    public function getSnortICDResponses(Request $request)
    {
    	$snortICDResponseList = array(

                array(
                        'id' => 1,
                        'code' => 'TCbZC',
                        'description' => 'Terminate Client by Zero Score' 
                    ),

        );

        return response()->json(array('data' => $snortICDResponseList), 200);
    }
}
