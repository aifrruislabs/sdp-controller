<?php

namespace App\Http\Controllers;

use Storage;

use App\Gateway;
use App\GatewayLog;
use Illuminate\Http\Request;

class GatewayLogController extends Controller
{
    //doesGatewayCollectLogs
    public function doesGatewayCollectLogs(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'gatewayId' => 'required'
                ]);
                
        $userId = $request->header('userId');

        $gatewayData = Gateway::where('id', $request->gatewayId)->get()->toArray();

        if (sizeof($gatewayData) == 1) {

            if ($gatewayData['0']['userId'] == $userId) {
                return response()->json(array(
                    'status' => true, 
                    'does' => $gatewayData['0']['collectLogs'],
                    'period' => $gatewayData['0']['logsCollectionPeriod']
                ), 200);
            }else {
                return response()->json(array('status' => false, 'message' => 'Un-Authorized'), 200);
            }

        }else {
            return response()->json(array('status' => true), 200);
        }
        
    }

    private function pcap_analyzer()
    {

        // Load the PCAP file.
        $pcap = file_get_contents('sample.pcap');

        // Analyze the PCAP file for malware.
        $malware = analyze_pcap($pcap);

        // Return the response in JSON format.
        echo json_encode($malware);

        function analyze_pcap($pcap) {
        // TODO: Implement the malware analysis logic here.
        }
    }

    //uploadLogCollection
    public function uploadLogCollection(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'gatewayId' => 'required',
                    'gatewayPcapTime' => 'required'
                ]);

        $userId = $request->header('userId');
        
        $gatewayId = $request->gatewayId;
        $gatewayPcapTime = $request->gatewayPcapTime;

        //Add New Pcap File
        $newGatewayPcapLog = new GatewayLog();
        $newGatewayPcapLog->userId = $userId;
        $newGatewayPcapLog->gatewayId = $gatewayId;
        $newGatewayPcapLog->gatewayPcapTime = $gatewayPcapTime;
        $newGatewayPcapLog->gatewayPcapLog = 0;
        $newGatewayPcapLog->pcapAnalysis = 0;

        if ($newGatewayPcapLog->save()) {
            $pcapId = $newGatewayPcapLog->id;

            //Uploading File
            $storePcap = Storage::disk('local')
                            ->put("/public/GATEWAY_LOG/".$gatewayId."/".$pcapId.".pcap",
                                 $request->gatewayPcapLog, 'public');

            //Analyse Pcap File
            $analyse_command = "";

            $resultAnalysis = array();

            //Update Gateway Pcap Status
            $updateGatewayPcapStatus = GatewayLog::find($pcapId);
            $updateGatewayPcapStatus->gatewayPcapLog = 1;
            $updateGatewayPcapStatus->pcapAnalysis = 1;
            $updateGatewayPcapStatus->resultAnalysis = json_encode($resultAnalysis);
            $updateGatewayPcapStatus->update();

            //Return 201 Response Status
            return response()->json(array('status' => true), 201);

        }else {
            return response()->json(array('status' => false), 200);
        }

    }
}
