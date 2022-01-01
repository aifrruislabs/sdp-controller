<?php

namespace App\Http\Controllers;

use App\Gateway;
use App\GatewayNetworkTraffic;
use Illuminate\Http\Request;

class GatewayNetworkTrafficController extends Controller
{

    //getGatewayNetworkTrffc
    public function getGatewayNetworkTrffc(Request $request)
    {
        //Input Validation
        $this->validate($request,
                    [
                        'gatewayId' => 'required'
                    ]);

        $userId = $request->header('userId');

        $getLastInsertedLog = GatewayNetworkTraffic::where([
                                    ['userId', '=', $userId],
                                    ['gatewayId', '=', $request->gatewayId]
                                    ])->orderBy('created_at', 'DESC')
                                    ->limit(1)->get()[0];

        return response()->json(array('status' => true, 'data' => $getLastInsertedLog), 200);
    }

    //postGatewayTrafficTx
    public function postGatewayTrafficTx(Request $request)
    {
    	//Validate Inputs
    	$this->validate($request,
    			[
    				'trafficRx' => 'required',
    				'trafficTx' => 'required',
    				'cpuPercent' => 'required'
    			]);

    	$userId = $request->header('userId');
    	$gatewayId = $request->header('gatewayId');

    	$trafficRx = $request->trafficRx;
    	$trafficTx = $request->trafficTx;
    	$cpuPercent = $request->cpuPercent;

    	$newGatewayNetworkTraffic = new GatewayNetworkTraffic();
    	$newGatewayNetworkTraffic->userId = $userId;
    	$newGatewayNetworkTraffic->gatewayId = $gatewayId;
    	$newGatewayNetworkTraffic->rxCount = $trafficRx;
    	$newGatewayNetworkTraffic->txCount = $trafficTx;
    	$newGatewayNetworkTraffic->cpuPercent = $cpuPercent;

    	if ($newGatewayNetworkTraffic->save()) {
    		return response()->json(array('status' => true), 201);
    	}else {
    		return response()->json(array('status' => false), 200);
    	}
    }
}
