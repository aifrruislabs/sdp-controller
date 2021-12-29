<?php

namespace App\Http\Controllers;

use App\TrustScoreWeight;
use Illuminate\Http\Request;

class TrustScoreWeightController extends Controller
{
    //usrDltTrstScoreWeight
    public function usrDltTrstScoreWeight(Request $request)
    {
        //Input Validation
        $this->validate($request,
                        [
                            'trustScoreWeightId' => 'required'
                        ]);

        $deleteTrustScoreWeight = TrustScoreWeight::find($request->trustScoreWeightId)->delete();

        return response()->json(array('status' => true), 200);
    }

	//getAllFactorsWeights
	public function getAllFactorsWeights(Request $request)
	{
		$userId = $request->header('userId');

		$trustScoreWeightData = TrustScoreWeight::where([
										['userId', '=', $userId]
										])->get();

		$responseData = array();
		foreach ($trustScoreWeightData as $trustScoreWeightD) {
			$responseData[] = array(
						'id' => $trustScoreWeightD->id,
						'scoreFactorId' => $trustScoreWeightD->scoreFactorId,
						'trustScoreFactorTitle' => getFactorTitle($trustScoreWeightD->scoreFactorId),
						'scoreFactorPercent' => $trustScoreWeightD->scoreFactorPercent
						 );
		}

		return response()->json(array('status' => true, 'data' => $responseData), 200);
	}

    //pstTrustScoreWghtfrFactor
    public function pstTrustScoreWghtfrFactor(Request $request)
    {
    	//Inputs Validation
    	$this->validate($request,
    					[
    						'trustScoreFactorId' => 'required',
    						'trustScoreWeight' => 'required'
    					]);

    	$userId = $request->header('userId');

    	//Check if Weight for Factor Exists
    	$checkIfWeightfactorExists = TrustScoreWeight::where([
    										['userId', '=', $userId],
    										['scoreFactorId', '=', $request->trustScoreFactorId]
    										])->get()->toArray();

    	if (sizeof($checkIfWeightfactorExists) == 0) {

    		//Adding New Weight
    		$newTrustScoreWeight = new TrustScoreWeight();
    		$newTrustScoreWeight->userId = $userId;
    		$newTrustScoreWeight->scoreFactorId = $request->trustScoreFactorId;
    		$newTrustScoreWeight->scoreFactorPercent = $request->trustScoreWeight;

    		if ($newTrustScoreWeight->save()) {
    			return response()->json(array('status' => true), 201);
    		}else {
    			return response()->json(array('status' => false, 'error_code' => 'SRV_ERR'), 200);
    		}

    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'DATA_EXISTS'), 200);
    	}
    }
}
