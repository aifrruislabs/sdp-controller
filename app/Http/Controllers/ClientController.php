<?php

namespace App\Http\Controllers;

use App\Client;
use App\Gateway;
use App\Service;
use App\ClientServiceAccess;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    //addGatewayClientAddService
    public function addGatewayClientAddService(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'clientId' => 'required',
                    'serviceId' => 'required',
                    'gatewayId' => 'required'
                ]);

        $userId = $request->header('userId');

        //Get Client Single Id
        $clientSingleIdQuery = Client::where('clientId', $request->clientId)->get()->toArray();
        $clientSingleId = $clientSingleIdQuery['0']['id'];

        //Check If Service Access Not Granted Already
        $checkServiceGrantedAlreadyQuery = ClientServiceAccess::where([
                                        ['gatewayId', '=', $request->gatewayId],
                                        ['serviceId', '=', $request->serviceId],
                                        ['clientId', '=', $clientSingleId]
                                        ])->get();
        if (sizeof($checkServiceGrantedAlreadyQuery) == 0) {

            //Adding Service
            $newClientServiceAccess = new ClientServiceAccess();
            $newClientServiceAccess->userId = $userId;
            $newClientServiceAccess->gatewayId = $request->gatewayId;
            $newClientServiceAccess->serviceId = $request->serviceId;
            $newClientServiceAccess->clientId = $clientSingleId;

            if ($newClientServiceAccess->save()) {
                //Update Total Access Score
                $getAllDataGatClnt = ClientServiceAccess::where([
                                        ['gatewayId', '=', $request->gatewayId],
                                        ['clientId', '=', $clientSingleId]
                                        ])->get();

                $totalTAS = 0;
                foreach ($getAllDataGatClnt as $GatClnt) {
                    $serviceData = Service::where('id', $GatClnt->serviceId)->get()->toArray();

                    $totalTAS += intval($serviceData['0']['serviceScore']);
                }

                //Update Client Score
                $updateClientTAS = Client::find($clientSingleId);
                $updateClientTAS->totalAccessScore = $totalTAS;
                $updateClientTAS->update();

                return response()->json(array(
                    'status' => true, 
                    'message' => 'Client Service was Added Successfully'), 200);
            }else {
                return response()->json(array(
                    'status' => false, 
                    'error_code' => 'SRV_ERR'), 200);
            }

        }else {
            return response()->json(array('status' => true), 200);
        }
    }

    //uDltGatewayClientAccess
    public function uDltGatewayClientAccess(Request $request)
    {
        //Input Validation
        $this->validate($request,
                    [
                        'csaId' => 'required',

                        'gatewayId' => 'required',

                        'clientId' => 'required'
                    ]);

        //Deleting Access
        $deleteAcces = ClientServiceAccess::find($request->csaId)->delete();

        //Get Client Single Id
        $clientSingleIdQuery = Client::where('clientId', $request->clientId)->get()->toArray();
        $clientSingleId = $clientSingleIdQuery['0']['id'];

        //Update Total Access Score
        $getAllDataGatClnt = ClientServiceAccess::where([
                                ['gatewayId', '=', $request->gatewayId],
                                ['clientId', '=', $clientSingleId]
                                ])->get();

        $totalTAS = 0;
        foreach ($getAllDataGatClnt as $GatClnt) {
            $serviceData = Service::where('id', $GatClnt->serviceId)->get()->toArray();

            $totalTAS += intval($serviceData['0']['serviceScore']);
        }

        //Update Client Score
        $updateClientTAS = Client::find($clientSingleId);
        $updateClientTAS->totalAccessScore = $totalTAS;
        $updateClientTAS->update();

        return response()->json(array('status' => true, 'message' => 'Access was Removed Successfully'), 200);
    }

    //uGtwyClientServiceList
    public function uGtwyClientServiceList($clientId, Request $request)
    {
        $userId = $request->header('userId');

        $gatewayServiceAccessList = array();
        $gSAL_Services_Nums = array();

        $gatewayServiceNoAccessList = array();

        $servicesListQuery = ClientServiceAccess::where('clientId', $clientId)->get();

        //Working on Gateway Service Access List
        if (sizeof($servicesListQuery) != 0) {

            foreach ($servicesListQuery as $serviceL) {
                $gSAL_Services_Nums[] = $serviceL->serviceId;

                $serviceId = $serviceL->serviceId;
                $gatewayId = $serviceL->gatewayId;

                $clientInfo = Client::where('id', $clientId)->get()->toArray();
                $gatewayInfo = Gateway::where('id', $gatewayId)->get()->toArray();
                $serviceInfo = Service::where('id', $serviceId)->get()->toArray();

                $gatewayServiceAccessList[] = array(
                                'csaId' => $serviceL->id,
                                'gatewayTitle' => $gatewayInfo['0']['gatewayTitle'],
                                'clientId' => $clientInfo['0']['clientId'],
                                'gatewayId' => $gatewayInfo['0']['id'],
                                'serviceId' => $serviceInfo['0']['id'],
                                'serviceTitle' => $serviceInfo['0']['serviceTitle'],
                                'servicePort' => $serviceInfo['0']['servicePort'],
                                'serviceScore' => $serviceInfo['0']['serviceScore'] );
            }

        }


        //Working on Gateway Services No Access list
        $gatewayServices = Gateway::where('userId', $userId)->get();

        foreach ($gatewayServices as $gateway) {
            
            $servicesListG = (Array) json_decode($gateway->gatewayServicesList);

            
            foreach ($servicesListG as $gSAL_ServiceId) {
                
                $addInList = 0;

                foreach ($gatewayServiceAccessList as $serviceInId) {
                    
                    if ($gSAL_ServiceId == $serviceInId['serviceId']) {
                        $addInList = 1;
                    }
                }
            
                if ($addInList != 1) {
                    $clientInfo = Client::where('id', $clientId)->get()->toArray();
                    $gatewayInfo = Gateway::where('id', $gateway->id)->get()->toArray();
                    $serviceInfo = Service::where('id', $gSAL_ServiceId)->get()->toArray();

                    $gatewayServiceNoAccessList[] = array(
                        'gatewayTitle' => $gatewayInfo['0']['gatewayTitle'],
                        'clientId' => $clientInfo['0']['clientId'],
                        'gatewayId' => $gatewayInfo['0']['id'],
                        'serviceId' => $serviceInfo['0']['id'],
                        'serviceTitle' => $serviceInfo['0']['serviceTitle'],
                        'servicePort' => $serviceInfo['0']['servicePort'],
                        'serviceScore' => $serviceInfo['0']['serviceScore'] );    
                }
                
            }
        }

        return response()->json(array(
            'status' => true, 
            'gatewayServiceAccessList' => $gatewayServiceAccessList,
            'gatewayServiceNoAccessList' => $gatewayServiceNoAccessList), 200);

    }

    //userDeleteClient
    public function userDeleteClient($clientId)
    {
        $deleteClient = Client::find($clientId)->delete();

        return response()->json(array('status' => true, 'message' => 'Client was Deleted Successfully'), 200);
    }

    //userSdpGetAllClients
    public function userSdpGetAllClients(Request $request)
    {
        $userId = $request->header('userId');

        $clients = Client::where([
                                ['userId', '=', $userId]
                                ])->orderBy('created_at', 'DESC')->get();

        return response()->json(array('status' => true, 'data' => $clients), 200);
    }

    //userSDPCreateClient
    public function userSDPCreateClient(Request $request)
    {
    	//Input Validation
    	$this->validate($request,
    				[
    					'clientUsername' => 'required',
    					'clientPassword' => 'required'
    				]);

    	$userId = $request->header('userId');

    	//Adding New Client
    	$newClient = new Client();
    	$newClient->userId = $userId;
    	$newClient->firstName = $request->clientFirstName;
    	$newClient->middleName = $request->clientMiddleName;
    	$newClient->lastName = $request->clientLastName;
    	$newClient->username = $request->clientUsername;
        $newClient->totalTrustScore = "0";
    	$newClient->password = bcrypt($request->clientPassword);

    	if ($newClient->save()) {

    		//Update Client ID
    		$updateClientId = Client::find($newClient->id);
    		$updateClientId->clientId = "FRU-".rand(0,1000)."-".$newClient->id;

    		if ($updateClientId->update()) {
    			return response()->json(array('status' => true, 'message' => 'Client was Created Successfully'), 201);
    		}else {
    			return response()->json(array('status' => true, 'error_code' => 'SRV_ERR'), 200);
    		}

    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'SRV_ERR'), 200);
    	}

    }
}
