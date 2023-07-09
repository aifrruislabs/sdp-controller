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

        if ($newGatewayPcapLog->save()) {
            $pcapId = $newGatewayPcapLog->id;

            //Uploading File
            $storePcap = Storage::disk('local')
                            ->put("/public/GATEWAY_LOG/".$gatewayId."/".$pcapId,
                                 $request->gatewayPcapLog, 'public');

            //Update Gateway Pcap Status
            $updateGatewayPcapStatus = GatewayLog::find($pcapId);
            $updateGatewayPcapStatus->gatewayPcapLog = 1;
            $updateGatewayPcapStatus->update();

            //Return 201 Response Status
            return response()->json(array('status' => true), 201);

        }else {
            return response()->json(array('status' => false), 200);
        }

    }
}
