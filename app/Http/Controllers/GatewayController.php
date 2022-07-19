<?php

namespace App\Http\Controllers;

use App\Service;
use App\Gateway;
use App\GatewayServiceStatus;
use Illuminate\Http\Request;

class GatewayController extends Controller
{

    //toggleGatewayServiceStatus
    public function toggleGatewayServiceStatus(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'gatewayId' => 'required',
                    'serviceId' => 'required',
                    'serviceStatus' => 'required'
                ]);

        $userId = $request->header('userId');

        $gatewayId = $request->gatewayId;
        $serviceId = $request->serviceId;
        $serviceStatus = $request->serviceStatus;

        $toggleOperation = "FALSE";

        //Sending Request to Update Rules to Gateway
        if ($request->serviceStatus == "1") {
            $toggleOperation = $this->toggleServiceGateway('ON', $serviceId, $gatewayId);
        }else {
            $toggleOperation = $this->toggleServiceGateway('OFF', $serviceId, $gatewayId);
        }
    
        //Get Gateway Service Status Row
        $getGSRow = GatewayServiceStatus::where([
                            ['userId', '=', $userId],
                            ['serviceId', '=', $serviceId],
                            ['gatewayId', '=', $gatewayId]
                            ])->get()->toArray();

        if (sizeof($getGSRow) == 1) {
            //Update Gateway Service Status
            $updateGatewayServiceStatus = GatewayServiceStatus::find($getGSRow['0']['id']);

            if ($request->serviceStatus == "1") {
                $updateGatewayServiceStatus->serviceStatus = "OFF";
            }else {
                $updateGatewayServiceStatus->serviceStatus = "ON";
            }
            
            $updateGatewayServiceStatus->update();

        }
        
        return response()->json(array('status' => true), 201);
    
    }

    //toggleServiceGateway
    private function toggleServiceGateway($toggleStatus, $serviceId, $gatewayId) {
        // Get Service Data
        $serviceData = Service::where('id', $serviceId)->get()->toArray();
        $gatewayData = Gateway::where('id', $gatewayId)->get()->toArray();

        $gateWayData = Gateway::where('id', $gatewayId)->get()->toArray();

        if ((sizeof($serviceData) == 1) && (sizeof($gateWayData) == 1)) {
            $gatewayIP = $gateWayData['0']['gatewayIP'];
            $servicePort = $serviceData['0']['servicePort'];
            $serviceProto = $serviceData['0']['serviceProto'];

            $toggle_gateway_status_url = "";

            if ($toggleStatus == 'ON') {
                //Turn Off Service
                $toggle_gateway_status_url = "http://" . $gatewayIP . ":8000/api/v1/up/default/drop/firewall/policy";
            }else {
                //Turn On Service
                $toggle_gateway_status_url = "http://" . $gatewayIP . ":8000/api/v1/down/default/drop/firewall/policy";
            }

            if ($serviceProto == "1") {
                $serviceProto = "tcp";
            }else {
                $serviceProto = "udp";
            }

            $post = [
                'gatewayAccessToken' => $gatewayData['0']['gatewayAccessToken'],
                'serviceProto' => $serviceProto,
                'servicePort' => $servicePort
            ];

            # Send Request to Gateway Server to Turn on Default Drop Firewall Policy
            $ch = curl_init($toggle_gateway_status_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = curl_exec($ch);
            curl_close($ch);

            return response()->json(array('status' => true), 201);

        }else {
            return response()->json(array('status' => false), 200);
        }

    }   

    //userGatewayDeleteService
    public function userGatewayDeleteService(Request $request)
    {
        //Input Validation
        $this->validate($request,
                    [
                        'serviceId' => 'required',
                        'gatewayId' => 'required'
                    ]);

        //User Id Headers
        $userId = $request->header('userId');

        //Get All Services List
        $allGatewayServicesList = Gateway::where('id', $request->gatewayId)->get()->toArray();

        if (sizeof($allGatewayServicesList) == 1) {

            $currentServicesList = $allGatewayServicesList['0']['gatewayServicesList'];

            $newServiceData = Service::where('id', $request->serviceId)->get()->toArray()[0];
            
            $currentServicesList = str_replace("," . $request->serviceId, "", $currentServicesList);
            $currentServicesList = str_replace($request->serviceId . ",", "", $currentServicesList);
            $currentServicesList = str_replace($request->serviceId, "", $currentServicesList);

            //Update Data to Database
            $updateGatewayServices = Gateway::find($request->gatewayId);
            $updateGatewayServices->gatewayServicesList = $currentServicesList;
            $updateGatewayServices->update();

            //Remove Service on GatewayServiceStatusController
            $rmGS_Wh = GatewayServiceStatus::where([
                                        ['userId', '=', $userId],
                                        ['gatewayId', '=', $request->gatewayId],
                                        ['serviceId', '=', $request->serviceId]
                                        ])->get()->toArray();

            if (sizeof($rmGS_Wh) == 1) {
                //Delete Gateway Service Status
                $rmGS = GatewayServiceStatus::find($rmGS_Wh['0']['id']);
                $rmGS->delete();
            }

            return response()->json(array('status' => true, 'data' => $newServiceData), 200);            

        }else {
            return response()->json(array('status' => false, 'error_code' => 'GATEWAY_NOT_EXIST'), 200);
        }
    }

    //userGatewayAddService
    public function userGatewayAddService(Request $request)
    {
        //Input Validation
        $this->validate($request,
                        [
                            'serviceId' => 'required',
                            'gatewayId' => 'required'
                        ]);

        $userId = $request->header('userId');

        //Get All Services List
        $allGatewayServicesList = Gateway::where('id', $request->gatewayId)->get()->toArray();

        if (sizeof($allGatewayServicesList) == 1) {

            $currentServicesList = (Array) json_decode($allGatewayServicesList['0']['gatewayServicesList']);

            $newServiceData = Service::where('id', $request->serviceId)->get()->toArray()[0];
            $currentServicesList[] = $request->serviceId;

            //Update Data to Database
            $updateGatewayServices = Gateway::find($request->gatewayId);
            $updateGatewayServices->gatewayServicesList = json_encode($currentServicesList);
            $updateGatewayServices->update();

            //Add Service to GatewayServiceStatus with Off Status
            $newGatewayServiceStatus = new GatewayServiceStatus();
            $newGatewayServiceStatus->userId = $userId;
            $newGatewayServiceStatus->gatewayId = $request->gatewayId;
            $newGatewayServiceStatus->serviceId = $request->serviceId;
            $newGatewayServiceStatus->serviceStatus = "OFF";
            $newGatewayServiceStatus->save();

            return response()->json(array('status' => true, 'data' => $newServiceData), 200);

        }else {
            return response()->json(array('status' => false, 'error_code' => 'GATEWAY_NOT_EXIST'), 200);
        }
    }

    //userGetGatewayService
    public function userGetGatewayService($gatewayId, Request $request)
    {
        $userId = $request->header('userId');

        $allServices = Service::all()->toArray();

        $allServicesId = array();

        foreach ($allServices as $service) {
            $allServicesId[] = $service['id'];
        }

        $gatewayServices = Gateway::where('id', $gatewayId)->get()->toArray();

        $otherGatewayServicesList = array();
        $gatewayServicesList = (Array) json_decode($gatewayServices['0']['gatewayServicesList']);

        $allServicesIdsCopy = $allServices;

        $checkOffset = 0;
        foreach ($allServicesId as $serviceId) {
            if (in_array($serviceId, $gatewayServicesList)) {
                array_splice($allServicesIdsCopy, array_search($serviceId, $allServicesIdsCopy), 1);
            }
            $checkOffset += 1;
        }

        $otherGatewayServicesList = $allServicesIdsCopy;

        $gatewayServicesListData = array();
        foreach ($gatewayServicesList as $gatewayServiceId) {
            $serviceData = Service::where('id', $gatewayServiceId)->get()->toArray();

            $gatewayServicesListData[] = @$serviceData[0];
        }

        $gatewayServicesListDataResp = array();

        //Adding Gateway Service Status to gatewayServicesListData
        foreach ($gatewayServicesListData as $serviceData) {
            
            //Fetch Gateway Service Status
            $gatewayServiceStat = GatewayServiceStatus::where([
                                    ['userId', '=', $userId],
                                    ['gatewayId', '=', $gatewayId],
                                    ['serviceId', '=', $serviceData['id']]
                                    ])->get()->toArray();

            $serviceStatus = "0";

            if (sizeof($gatewayServiceStat) == 1) {
                if ($gatewayServiceStat['0']['serviceStatus'] == "ON") {
                    $serviceStatus = "1";
                }
            }

            $gatewayServicesListDataResp[] = array(
                                'id' => $serviceData['id'], 
                                'userId' => $userId,
                                'gatewayId' => $gatewayId,
                                'serviceId' => $serviceData['id'],
                                'serviceTitle' => $serviceData['serviceTitle'],
                                'serviceInfo' => $serviceData['serviceInfo'],
                                'serviceProto' => $serviceData['serviceInfo'],
                                'servicePort' => $serviceData['servicePort'],
                                'serviceScore' => $serviceData['serviceScore'],
                                'serviceStatus' => $serviceStatus,
                                'created_at' => $serviceData['created_at'],
                                'updated_at' => $serviceData['updated_at']
                                );
        }

        return response()->json(array('status' => 'true',  
            'gatewayServices' => $gatewayServicesListDataResp, 'otherGatewayServices' => $otherGatewayServicesList), 200);
    }

    //userUpdateGateway
    public function userUpdateGateway(Request $request)
    {
        //Validate 
        $this->validate($request,
                    [
                        'gatewayId' => 'required',
                        'gatewayTitle' => 'required',
                        'gatewayIP' => 'required',
                        'gatewayNetworkAccessibility' => 'required'
                    ]);

        //Update Gateway
        $updateGateway = Gateway::find($request->gatewayId);
        $updateGateway->gatewayTitle = $request->gatewayTitle;
        $updateGateway->gatewayInfo = $request->gatewayInfo;
        $updateGateway->gatewayIP = $request->gatewayIP;
        $updateGateway->gatewayNetworkAccessibility = $request->gatewayNetworkAccessibility;

        if ($updateGateway->update()) {
            return response()->json(array('status' => true, 'message' => 'Gateway was Updated Successfully'), 200);
        }else {
            return response()->json(array('status' => false, 'error_code' => 'SRV_ERR', 'message' => 'Failed to Update Gateway'), 200);
        }
    }

    //userGatewayDelete
    public function userGatewayDelete($gatewayId)
    {
        //Delete Gateway
        $deleteGateway = Gateway::find($gatewayId)->delete();

        return response()->json(array('status' => true, 'message' => 'Gateway was Deleted Successfully'), 200);
    }

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
        $newGateway->gatewayNetworkAccessibility = $request->gatewayNetworkAccessibility;
        $newGateway->gatewayIP = $request->gatewayIP;
        $newGateway->gatewayServicesList = "[]";

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
