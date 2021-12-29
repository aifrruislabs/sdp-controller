<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientMac;
use Illuminate\Http\Request;

class ClientMacController extends Controller
{
    //userDeleteClientMac
    public function userDeleteClientMac(Request $request)
    {
        $userId = $request->header('userId');
        $clientMacId = $request->clientMacId;

        $deleteClientMac = ClientMac::find($clientMacId)->delete();

        return response()->json(array('status' => true), 200);
    }

    //getAllClientsMacList
    public function getAllClientsMacList(Request $request)
    {
        $userId = $request->header('userId');

        $clientsMacList = ClientMac::where('userId', $userId)->get();

        $clientsMacListRet = array();
        foreach ($clientsMacList as $clientMacI) {
            $clientData = Client::where('clientId', $clientMacI->clientId)->get()->toArray();

            $clientsMacListRet[] = array(
                            'id' => $clientMacI->id,
                            'firstName' => $clientData['0']['firstName'],
                            'lastName' => $clientData['0']['lastName'],
                            'clientId' => $clientMacI->clientId,
                            'macAddr' => $clientMacI->macAddr
                             );
        }

        return response()->json(array('status' => true, 'data' => $clientsMacListRet), 200);
    }

    //Create New Client Mac Address
    public function createNewMacForClient(Request $request)
    {
    	//Input Validation
    	$this->validate($request,
    					[
    						'macAddr' => 'required'
    					]);

    	$userId = $request->header('userId');
    	$clientId = $request->clientId;
    	$macAddr = $request->macAddr;

    	$checkIfMacExists = ClientMac::where('clientId', $clientId)->get()->toArray();

    	if (sizeof($checkIfMacExists) == 0) {
    		//Create New
    		$newClientMac = new ClientMac();
    		$newClientMac->userId = $userId;
    		$newClientMac->clientId = $clientId;
    		$newClientMac->macAddr = $macAddr;
    		$newClientMac->save();

    		return response()->json(array('status' => true, 'message' => 'NEW_CREATED'), 201);

    	}else {
    		//Update Existing
    		$updateClientMac = ClientMac::find($checkIfMacExists['0']['id']);
    		$updateClientMac->userId = $userId;
    		$updateClientMac->clientId = $clientId;
    		$updateClientMac->macAddr = $macAddr;
    		$updateClientMac->update();

    		return response()->json(array('status' => true, 'message' => 'NEW_UPDATED'), 200);
    	}

    }
}
