<?php

namespace App\Http\Controllers;

use App\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    //getConfsStanzas
    public function getConfsStanzas(Request $request)
    {
        //Check Headers
        $access_token = $request->header('access_token');

        if (empty($access_token)) {
            $access_token = $request->access_token;
        }


    }

    //Get All Gateways
    public function userGatewayGetAll(Request $reques)
    {
        $gateways = Gateway::orderBy('created_at', 'DESC')->paginate(12);

        return response()->json($gateways, 200);
    }


    //userCreateGateway
    public function userCreateGateway(Request $request)
    {
    	//Inputs Validation
    	$this->validate($request,
    				[
    					'gatewayTitle' => 'required',
    					'gatewayIP' => 'required'
    				]);

    	$userId = $request->header('userId');


    	//Adding New Gateway
    	$newGateway = new Gateway();
        $newGateway->userId = $userId;
    	$newGateway->gatewayTitle = $request->gatewayTitle;
    	$newGateway->gatewayInfo = $request->gatewayInfo;
        $newGateway->gatewayIP = $request->gatewayIP;

    	if ($newGateway->save()) {

            $token = sha1($newGateway->id."--".$newGateway->IP."--".rand(1000, 10000).$newGateway->userId);

            $newGateway->gatewayAccessToken = $token;

            //Update Gateway Access Token
            $updateGatewayAccessToken = Gateway::find($newGateway->id);
            $updateGatewayAccessToken->gatewayAccessToken = $token;
            $updateGatewayAccessToken->update();

    		return response()->json(array(
    								'status' => true, 
    								'data' => $newGateway), 201);
    	}else {
            return response()->json(array(
                                    'status' => false, 
                                    'error_code' => 'SRV_ERR'), 200);
    	}
    }
}
