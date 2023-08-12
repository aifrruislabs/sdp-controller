<?php

namespace App\Http\Controllers;

use App\IncidentResponsePolicy;
use Illuminate\Http\Request;

class IncidentResponsePolicyController extends Controller
{
    //Get Policies List
    public function getPoliciesList(Request $request)
    {   
        $userId = $request->header('userId');

        //Get Policies List
        $policiesList = IncidentResponsePolicy::where('userId', $userId)
                            ->orderBy('created_at', 'DESC')->get();

        return response()->json(array('data' => $policiesList), 200);
    }

    //Add New Incident Response Policy
    public function newIcdResponsePolicy(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'gateway' => 'required',
                    'snort_class' => 'required',
                    'icd_response' => 'required'
                ]);

        $userId = $request->header('userId');

        $gateway = $request->gateway;
        $snort_class = $request->snort_class;
        $icd_response = $request->icd_response;

        $newIcdPolicy = new IncidentResponsePolicy();
        $newIcdPolicy->userId = $userId;
        $newIcdPolicy->gatewayId = $gateway;
        $newIcdPolicy->incidentClassTypeId = $snort_class;
        $newIcdPolicy->incidentClassTypeDescription = getSnortClassList()[$snort_class-1]['description'];
        $newIcdPolicy->incidentResponseId = $icd_response;
        $newIcdPolicy->incidentResponseDescription = getSnortICDResponseList()[$icd_response-1]['description'];
        
        if ($newIcdPolicy->save()) {
            return response()->json(array('status' => true, 'data' => $newIcdPolicy), 201);
        }else {
            return response()->json(array('status' => false), 200);
        }
    }

    //getSnortICDResponses
    public function getSnortICDResponses(Request $request)
    {
    	$snortICDResponseList = getSnortICDResponseList();

        return response()->json(array('data' => $snortICDResponseList), 200);
    }
}
