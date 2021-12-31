<?php

namespace App\Http\Controllers;

use App\Gateway;
use App\GatewayNetworkTraffic;
use Illuminate\Http\Request;

class GatewayNetworkTrafficController extends Controller
{
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
