<?php

namespace App\Http\Controllers;

use App\Service;
use App\TrustScorePolicy;
use Illuminate\Http\Request;

class TrustScorePolicyController extends Controller
{
	//usertrustGetAllPolicies
	public function usertrustGetAllPolicies(Request $request)
	{
		$userId = $request->header('userId');

		$policiesData = TrustScorePolicy::orderBy('created_at', 'DESC')->where([
								['userId', '=', $userId]
								])->get();

		$responseData = array();
		foreach ($policiesData as $pD) {
			$serviceData = Service::where('id', $pD->serviceId)->get()->toArray();

			$responseData[]  = array(
				'id' => $pD->id,
				'serviceTitle' => $serviceData['0']['serviceTitle'], 
				'scoreFactorId' => $pD->scoreFactorId,
				'trustScoreFactorTitle' => getFactorTitle($pD->scoreFactorId),
				'scorePercent' => $pD->scorePercent
				);
		}

		return response()->json(array('status' => true, 'data' => $responseData), 200);
	}

    //userPostTrustScorePolicy
    public function userPostTrustScorePolicy(Request $request)
    {
    	//Input Validation
    	$this->validate($request,
    				[
    					'serviceId' => 'required',
    					'scoreFactorId' => 'required',
    					'scorePercent' => 'required'
    				]);

    	$userId = $request->header('userId');

    	//Adding New Trust Score Policy
    	$newTrustScorePolicy = new TrustScorePolicy();
    	$newTrustScorePolicy->userId = $userId;
    	$newTrustScorePolicy->serviceId = $request->serviceId;
    	$newTrustScorePolicy->scoreFactorId = $request->scoreFactorId;
    	$newTrustScorePolicy->scorePercent = $request->scorePercent;

    	if ($newTrustScorePolicy->save()) {
    		return response()->json(array('status' => true), 201);
    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'SRV_ERR'), 200);
    	}
    }
}
