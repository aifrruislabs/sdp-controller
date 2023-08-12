<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientIncident;

use App\SnortAlert;
use Illuminate\Http\Request;

class SnortAlertController extends Controller
{
    //snortReadGatewayAlerts
    public function snortReadGatewayAlerts(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'gatewayId' => 'required'
                ]);


        $gatewayId = $request->gatewayId;

        $srcIP = "";
        $dstIp = "";
        $snortFullAlert = "";
        $snortAlertCode = "";
        $snortAlertTitle = "";
        $snortAlertClassification = "";
        $snortAlertPriority = "";
        $incidentResponse = "";
        $createdAt = "";

        //Get Alerts
        $gatewaySnortAlerts = SnortAlert::where('gatewayId', $gatewayId)
                                        ->orderBy('created_at', 'DESC')
                                        ->skip(0)->take(100)->get();

        //Response Full Array
        $dataRes = array();

        //Get Top 100
        foreach ($gatewaySnortAlerts as $gateAlert) {
            $srcIP = $gateAlert->srcIP;
            $dstIp = $gateAlert->dstIp;
            $snortFullAlert = $gateAlert->snortFullAlert;
            $snortAlertCode = $gateAlert->snortAlertCode;
            $snortAlertTitle = $gateAlert->snortAlertTitle;
            $snortAlertClassification = $gateAlert->snortAlertClassification;
            $snortAlertPriority = $gateAlert->snortAlertPriority;
            $incidentResponse = $gateAlert->incidentResponseStatus;
            $createdAt = $gateAlert->created_at->diffForHumans();

            $dataRes[] = array(
                'srcIP' => $srcIP,
                'dstIp' => $dstIp,
                'snortFullAlert' => $snortFullAlert,
                'snortAlertCode' => $snortAlertCode,
                'snortAlertTitle' => $snortAlertTitle,
                'snortAlertClassification' => $snortAlertClassification,
                'snortAlertPriority' => $snortAlertPriority,
                'incidentResponse' => $incidentResponse,
                'createdAt' => $createdAt 
            );     
        }
              
        return response()->json(array('data' => $dataRes), 200);
        
    }

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

        $dstIp =  @$ipChunkAlpha[sizeof($ipChunkAlpha) - 2];
        $srcIpRes = @explode('"', $ipChunkBeta[1])[0];

        $srcIP = @explode("\\", $srcIpRes)[0];


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
            //Calculate Event According to Clients
            $this->calculateEventsAcrdngToClients($newSnortAlert);

            return response()->json(array('status' => true), 201);
        }else {
            return response()->json(array('status' => false), 200);
        }
    }

    //calculateEventsAcrdngToClients
    private function calculateEventsAcrdngToClients($snortAlert)
    {
        $snortId = $snortAlert->id;
        $userId = $snortAlert->userId;
        $gatewayId = $snortAlert->gatewayId;
        
        $snortAlertClassification = $snortAlert->snortAlertClassification;

        $srcIP = explode(":", $snortAlert->srcIP)[0];
        $dstIp = explode(":", $snortAlert->dstIp)[0];

        $networkClients = Client::all();

        $clientId = "";
        $clientPublicIP = "";

        foreach($networkClients as $networkClient) {
            if ( ($srcIP == $networkClient->publicIp) ) {
                $clientId = $networkClient->id;
                $clientPublicIP = $networkClient->publicIp;
            }

            if ( ($dstIp == $networkClient->publicIp) ) {
                $clientId = $networkClient->id;
                $clientPublicIP = $networkClient->publicIp;
            }
        }

        //Add New Incident for User
        if (!empty($clientId) && !empty($clientPublicIP)) {

            $snortClassId = "";

            foreach (getSnortClassList() as $snortClass) {
               if ($snortClass['description'] == $snortAlertClassification) {
                $snortClassId = $snortClass['id'];
               }
            } 

            //Fetch Policy for This Alert Class
            $fetchPolicy = IncidentResponsePolicy::where('incidentClassTypeId', $snortClassId)
                                ->get()->toArray();

            if (sizeof($fetchPolicy) != 0) {

                $incidentResponseId = $fetchPolicy['0']['incidentResponseId'];
                $incidentResponseDescription = $fetchPolicy['0']['incidentResponseDescription'];

                $newClientIncident = new ClientIncident();
                $newClientIncident->clientId = $clientId;
                $newClientIncident->snortAlertId = $snortId;
                $newClientIncident->snortClassId = $snortClassId;
                $newClientIncident->incidentResponseId = $incidentResponseId;
                $newClientIncident->incidentTitle = $snortAlertClassification;
                $newClientIncident->incidentResponse =  $incidentResponseDescription; 
                $newClientIncident->incidentResolved = 0;     
                $newClientIncident->save();

            }else {
                //Update No Policy for Attack
                $updateSnortAlert = SnortAlert::find($snortId);
                $updateSnortAlert->incidentResponseStatus = 999;
                $updateSnortAlert->incidentResponseId = 999;
                $updateSnortAlert->update();
            }

        }else {
            //Update No Client from Inside
            $updateSnortAlert = SnortAlert::find($snortId);
            $updateSnortAlert->incidentResponseStatus = 0;
            $updateSnortAlert->incidentResponseId = 0;
            $updateSnortAlert->update();
        }
    }

    //getSnortClassificationList
    public function getSnortClassificationList(Request $request)
    { 
        $snortClassList = getSnortClassList(); 

        return response()->json(array('data' => $snortClassList), 200);
    }
}
