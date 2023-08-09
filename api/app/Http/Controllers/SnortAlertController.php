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

        $srcIP = "";
        $dstIp = "";
        $classification = "";
        $priority = "";
        $snortAlertCode = "";
        $snortAlertTitle = "";

        //Process for More Details
        $logChunkExploded = @explode(" ", $log_chunk);

        //Get Snort Alert Code
        $snortAlertCode = @str_replace("]", "", @$logChunkExploded[1]);
        $snortAlertCode = @str_replace("[", "", $snortAlertCode);
        
        //Get Snort Alert Title
        $titleExploded = @explode("]", $log_chunk);
        $snortAlertTitle = @str_replace("[**", "", @$titleExploded[2]);

        //Get Classification
        $classificExploded = @explode("[Classification:", $log_chunk);
        $classificationEx = @explode("]", @$classificExploded[1]);
        $classification = $classificationEx[0];

        //Get Priority
        $priorityExploded = explode("[Priority:", $log_chunk);
        $priorityEx = @explode("]", @$priorityExploded[1]);
        $priority = $priorityEx[0];

        //Get Source Destination IP
        $ipAddresses = explode("->", $log_chunk);

        $ipChunkAlpha = @explode(" ", $ipAddresses[0]);
        $ipChunkBeta = @explode(" ", $ipAddresses[1]);

        $srcIP =  @$ipChunkAlpha[sizeof($ipChunkAlpha) - 2];
        $dstIp = @explode('"', $ipChunkBeta[1])[0];


        //Uploading New Snort Alert
        $newSnortAlert = new SnortAlert();
        $newSnortAlert->userId = $userId;
        $newSnortAlert->gatewayId = $gatewayId;
        $newSnortAlert->snortFullAlert = $log_chunk;
        $newSnortAlert->incidentResponseStatus = 0;
        $newSnortAlert->snortAlertCode = $snortAlertCode;
        $newSnortAlert->snortAlertTitle = $snortAlertTitle;
        $newSnortAlert->srcIP = $srcIP;
        $newSnortAlert->dstIp = $dstIp;
        $newSnortAlert->snortAlertClassification = $classification;
        $newSnortAlert->snortAlertPriority = $priority;

        if ($newSnortAlert->save()) {
            return response()->json(array('status' => true), 201);
        }else {
            return response()->json(array('status' => false), 200);
        }
    }
}
