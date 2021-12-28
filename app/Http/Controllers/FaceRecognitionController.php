<?php

namespace App\Http\Controllers;

use Storage;
use App\Face;
use App\FaceRecognition;
use Illuminate\Http\Request;

class FaceRecognitionController extends Controller
{
    //userPostNewFaceRec
    public function userPostNewFaceRec(Request $request)
    {
    	//Validate Inputs
    	$this->validate($request,
    			[
    				'clientId' => 'required',
    				'faceFile' => 'required'
    			]);

    	$userId = $request->header('userId');

    	//Check If Already Having Face
    	$checkFaceExist = Face::where([
    						['userId', '=', $userId],
    						['clientId', '=', $request->clientId]
    						])->get()->toArray();

    	
    	if (sizeof($checkFaceExist) == 0) {
    		//If Not Exists Add New
    		$addNewFace = new Face();
    		$addNewFace->userId = 
    	}else {
    		//If Exists Update
    			
    	}

    	
    }
}
