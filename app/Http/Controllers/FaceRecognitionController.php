<?php

namespace App\Http\Controllers;

use Storage;
use App\Client;
use App\Face;
use App\FaceRecognition;
use Illuminate\Http\Request;

class FaceRecognitionController extends Controller
{
    //deleteFaceRecData
    public function deleteFaceRecData(Request $request)
    {
        //Input Validation
        $this->validate($request,
                    [
                        'faceId' => 'required'
                    ]);

        $faceId = $request->faceId;

        $userId = $request->header('userId');

        //Delete Face
        $deleteFace = Face::find($faceId)->delete();

        //Delete Face on Local Storage
        Storage::disk('public')->delete('FACE_RECOGNITION/FACES/face_'.$faceId.".jpg");

        return response()->json(array('status' => true), 200);
    }

    //userSdpGetAllFacesList
    public function userSdpGetAllFacesList(Request $request)
    {
        $userId = $request->header('userId');

        $allFaces = Face::where('userId', $userId)
                        ->orderBy('created_at', 'DESC')->get();

        $allFacesRet = array();
        foreach ($allFaces as $face) {

            $clientData = Client::where('id', $face->clientId)->get()->toArray();

            $allFacesRet[] = array(
                        'id' => $face->id,
                        'faceId' => $face->id,
                        'clientFirstName' => $clientData['0']['firstName'],
                        'clientLastName' => $clientData['0']['lastName'],
                        'clientUsername' => $clientData['0']['username'],
                        'clientId' => $clientData['0']['clientId'],
                        'faceLocation' => getBaseUrl() . pF() . "/storage/" . $face->faceLocation
                         );
        }

        return response()->json(array('status' => true, 'data' => $allFacesRet), 200);
    }

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
        $clientId = $request->clientId;
        $faceFile = $request->file('faceFile');

    	//Check If Already Having Face
    	$checkFaceExist = Face::where([
    						['userId', '=', $userId],
    						['clientId', '=', $clientId]
    						])->get()->toArray();

    	
    	if (sizeof($checkFaceExist) == 0) {
            //If Not Exists Add New
    		$addNewFace = new Face();
    		$addNewFace->userId = $userId;
            $addNewFace->clientId = $clientId;
            
            if ($addNewFace->save()) {
                //Uploading Face Image
                Storage::disk('public')->putFileAs('FACE_RECOGNITION/FACES/', $faceFile, 'face_'.$clientId.".jpg");

                //Update Face Location
                $updateFaceLoc = Face::find($addNewFace->id);
                $updateFaceLoc->faceLocation = "FACE_RECOGNITION/FACES/face_".$clientId.".jpg";
                $updateFaceLoc->update();

                return response()->json(array('status' => true, 'message' => 'Uploaded Successfully'), 201);
            }else {
                return response()->json(array('status' => false, 'message' => 'Server Error'), 200);
            }

    	}else {
    		//If Exists Update Face Image Data
            
            //Uploading Face Image
            Storage::disk('public')->putFileAs('FACE_RECOGNITION/FACES/', $faceFile, 'face_'.$checkFaceExist['0']['id'].".jpg");

            //Update Face Location
            $updateFaceLoc = Face::find($checkFaceExist['0']['id']);
            $updateFaceLoc->faceLocation = "FACE_RECOGNITION/FACES/face_".$checkFaceExist['0']['id'].".jpg";
            $updateFaceLoc->update();

            return response()->json(array('status' => true, 'message' => 'Exists but uploaded Image'), 200);
    	}

    }
}
