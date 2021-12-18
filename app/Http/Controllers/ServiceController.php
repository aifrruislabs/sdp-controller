<?php

namespace App\Http\Controllers;

use App\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    //adminGetAllServices
    public function adminGetAllServices()
    {
        $services = Service::orderBy('servicePort', 'ASC')->get();

        return response()->json(array('status' => true, 'data' => $services), 200);
    }

    //userGetAllServices
    public function userGetAllServices() 
    {
        $services = Service::orderBy('servicePort', 'ASC')->get();

        return response()->json(array('status' => true, 'data' => $services), 200);
    }

    //adminCreateService
    public function adminCreateService(Request $request)
    {
    	//Inputs Validation
    	$this->validate($request,
    					[
    						'serviceTitle' => 'required',
    						'servicePort' => 'required'
    					]);

    	$userId = $request->header('userId');

    	if (empty($userId)) {
    		$userId = $request->userId;
    	}

    	//Adding New Service
    	$newService = new Service();
    	$newService->userId = $userId;
    	$newService->serviceTitle = $request->serviceTitle;
    	$newService->serviceInfo = $request->serviceInfo;
    	$newService->servicePort = $request->servicePort;

    	if ($newService->save()) {
    		return response()->json(array('status' => true, 'data' => $newService), 201);
    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'SRV_ERR'), 200);
    	}
    }
}
