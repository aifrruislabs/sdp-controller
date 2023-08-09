<?php

namespace App\Http\Controllers;

use App\SnortAlert;
use Illuminate\Http\Request;

class SnortAlertController extends Controller
{
    //uploadGatewaySnortAlert
    public function uploadGatewaySnortAlert(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'log_chunk' => 'required'
                ]);

        $userId = $request->header('userId');
        $gatewayId = $request->header('gatewayId');

        //Get Log Chunk
        $log_chunk_data = $request->log_chunk;
        $log_chunk = json_encode($log_chunk_data);

        $classification = "";
        $priority = "";
        $snortAlertCode = "";
        $snortAlertTitle = "";
        
        //Uploading New Snort Alert
        $newSnortAlert = new SnortAlert();
        $newSnortAlert->userId = $userId;
        $newSnortAlert->gatewayId = $gatewayId;
        $newSnortAlert->snortFullAlert = $log_chunk;
        $newSnortAlert->incidentResponseStatus = 0;
        $newSnortAlert->snortAlertCode = $snortAlertCode;
        $newSnortAlert->snortAlertTitle = $snortAlertTitle;
        $newSnortAlert->snortAlertClassification = $classification;
        $newSnortAlert->snortAlertPriority = $priority;

        if ($newSnortAlert->save()) {
            return response()->json(array('status' => true), 201);
        }else {
            return response()->json(array('status' => false), 200);
        }
    }
}
