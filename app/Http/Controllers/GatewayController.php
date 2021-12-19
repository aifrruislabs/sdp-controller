<?php

namespace App\Http\Controllers;

use App\Service;
use App\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{

    //userGatewayDeleteService
    public function userGatewayDeleteService(Request $request)
    {
        //Input Validation
        $this->validate($request,
                    [
                        'serviceId' => 'required',
                        'gatewayId' => 'required'
                    ]);

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

            return response()->json(array('status' => true, 'data' => $newServiceData), 200);

        }else {
            return response()->json(array('status' => false, 'error_code' => 'GATEWAY_NOT_EXIST'), 200);
        }
    }

    //userGetGatewayService
    public function userGetGatewayService($gatewayId)
    {
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

            $gatewayServicesListData[] = $serviceData[0];
        }

        return response()->json(array('status' => 'true',  
            'gatewayServices' => $gatewayServicesListData, 'otherGatewayServices' => $otherGatewayServicesList), 200);
    }

    //userUpdateGateway
    public function userUpdateGateway(Request $request)
    {
        //Validate 
        $this->validate($request,
                    [
                        'gatewayId' => 'required',
                        'gatewayTitle' => 'required',
                        'gatewayIP' => 'required'
                    ]);

        //Update Gateway
        $updateGateway = Gateway::find($request->gatewayId);
        $updateGateway->gatewayTitle = $request->gatewayTitle;
        $updateGateway->gatewayInfo = $request->gatewayInfo;
        $updateGateway->gatewayIP = $request->gatewayIP;

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
