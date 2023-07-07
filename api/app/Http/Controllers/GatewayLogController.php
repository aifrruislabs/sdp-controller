<?php

namespace App\Http\Controllers;

use Storage;

use App\GatewayLog;
use Illuminate\Http\Request;

class GatewayLogController extends Controller
{
    //uploadLogCollection
    public function uploadLogCollection(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'gatewayId' => 'required',
                    'gatewayPcapTime' => 'required',
                    'gatewayPcapLog' => 'required'
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
                            ->put("/public/GATEWAY_LOG/".$gatewayId."/".$pcapId.".pcap",
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
