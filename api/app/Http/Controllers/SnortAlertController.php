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
        $log_chunk = $request->log_chunk;

        //Uploading New Snort Alert
        $newSnortAlert = new SnortAlert();
        $newSnortAlert->userId = $userId;
        $newSnortAlert->gatewayId = $gatewayId;
        $newSnortAlert->snortFullAlert = $log_chunk;
        $newSnortAlert->incidentResponseStatus = 0;
        $newSnortAlert->snortAlertCode = "";
        $newSnortAlert->snortAlertTitle = "";
        $newSnortAlert->snortAlertClassification = "";
        $newSnortAlert->snortAlertPriority = "";

        if ($newSnortAlert->save()) {
            return response()->json(array('status' => true), 201);
        }else {
            return response()->json(array('status' => false), 200);
        }
    }
}
