<?php

namespace App\Http\Controllers;

use App\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    //adminServiceUpdateService
    public function adminServiceUpdateService(Request $request)
    {
        //Input Validation
        $this->validate($request,
                        [
                            'serviceId' => 'required',
                            'serviceTitle' => 'required',
                            'servicePort' => 'required',
                            'serviceScore' => 'required'
                        ]);

        //Updating Service
        $updateService = Service::find($request->serviceId);
        $updateService->serviceTitle = $request->serviceTitle;
        $updateService->serviceInfo = $request->serviceInfo;
        $updateService->servicePort = $request->servicePort;
        $updateService->serviceScore = $request->serviceScore;

        if ($updateService->update()) {
            return response()->json(array('status' => true, 'message' => 'Service Was Updated Successfully'), 200);
        }else {
            return response()->json(array('status' => false, 'error_code' => 'SRV_ERR', 'message' => 'Failed to Update Service'), 200);
        }
    }

    //adminServiceDelete
    public function adminServiceDelete($serviceId)
    {
        $deleteService = Service::find($serviceId)->delete();

        return response()->json(array('status' => true, 'message' => 'Service was Deleted Successfully'), 200);
    }

    //adminGetAllServices
    public function adminGetAllServices()
    {
        $services = Service::orderBy('created_at', 'DESC')->get();

        return response()->json(array('status' => true, 'data' => $services), 200);
    }

    //userGetAllServices
    public function userGetAllServices() 
    {
        $services = Service::orderBy('created_at', 'DESC')->get();

        return response()->json(array('status' => true, 'data' => $services), 200);
    }

    //adminCreateService
    public function adminCreateService(Request $request)
    {
    	//Inputs Validation
    	$this->validate($request,
    					[
    						'serviceTitle' => 'required',
    						'servicePort' => 'required',
                            'serviceScore' => 'required'
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
        $newService->serviceScore = $request->serviceScore;

    	if ($newService->save()) {
    		return response()->json(array('status' => true, 'data' => $newService), 201);
    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'SRV_ERR'), 200);
    	}
    }
}
