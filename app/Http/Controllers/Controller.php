<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //accountLogout
    public function accountLogout(Request $request)
    {
        $userId = $request->header('userId');

        //Clear Auth Token
        $removeAuthToken = User::find($userId);
        $removeAuthToken->authToken = "";

        if ($removeAuthToken->update()) {
            $responseData = array(
                                'status' => true, 
                                'message' => 'Logged Out Successfully');

            return response()->json(array('data' => $responseData), 200);
        }else {
            $responseData = array('status' => true, 
                                'error_code' => 'SRV_ERR', 
                                'message' => 'Failed to Log Out');

            return response()->json(array('data' => $responseData), 200);
        }
    }

    //accountLogin
    public function accountLogin(Request $request)
    {
    	//Inputs Validation
    	$this->validate($request,
    				[
    					'email' => 'required',
    					'password' => 'required'
    				]);

    	//Account Auth
    	$checkEmail = User::where('email', $request->email)->get();

    	if (sizeof($checkEmail) == 1) {

    		//Verifying Password
    		if (Hash::check($request->password, $checkEmail['0']['password'])) {

    			//Generate Token for the User
    			$token = $this->generateAuthToken($checkEmail['0']['id']);

                $responseData = $checkEmail['0'];

                $responseData->authToken = $token;

    			return response()->json(array('status' => true, 'data' => new UserResource($responseData)), 200);

    		}else {
    			return response()->json(array('status' => false, 'error_code' => 'WRONG_PWD'), 200);
    		}


    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'WRONG_EMAIL'), 200);
    	}
    }

    //Create New Account API
    public function createNewAccount(Request $request)
    {
    	//Inputs Validation
    	$this->validate($request,
    				[
    					'firstName' => 'required',
    					'lastName' => 'required',
    					'email' => 'required|unique:users',
    					'password' => 'required'
    				]);

    	//Creating New Account
    	$newAccount = new User();
    	$newAccount->firstName = $request->firstName;
    	$newAccount->lastName = $request->lastName;
    	$newAccount->email = $request->email;
    	$newAccount->level = 2;
    	$newAccount->password = bcrypt($request->password);

    	if ($newAccount->save()) {
    		$token = $this->generateAuthToken($newAccount->id);

    		$newAccount->token = $token;

    		return response()->json(array('status' => true, 'data' => new UserResource($newAccount)), 200);
    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'SRV_ERR'), 200);
    	}

    }

    //Generate Authentication Token
    private function generateAuthToken($userId) {
    	$userData = User::where('id', $userId)->get();

    	if (sizeof($userData) == 1) {
    		$token = md5($userId."-".$userData['0']['updated_at'].rand(1000, 9000).$userData['0']['lastName']);

    		//Update Token in Database
    		$updateUserToken = User::find($userData['0']['id']);
    		$updateUserToken->authToken = $token;
    		$updateUserToken->update();

    		return $token;
    	}else {
    		return "";
    	}
    }

}



