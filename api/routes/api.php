<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


//Routes Protected with Middleware
//Super Admin Middleware level
Route::group(['prefix' => 'v1', 'middleware' => 'superAdminAuthMiddleware'], function () {

	//Update Service
	Route::patch('/admin/service/update', ['uses' => 'ServiceController@adminServiceUpdateService']);

	//Delete Service
	Route::delete('/admin/service/delete/{serviceId}', ['uses' => 'ServiceController@adminServiceDelete']);

	//Get All Services
	Route::get('/admin/services/get/all', ['uses' => 'ServiceController@adminGetAllServices']);

	//Create New Service
	Route::post('/admin/create/service', ['uses' => 'ServiceController@adminCreateService']);

});


//Routes Protected with Middleware
//Admin Middleware Level
Route::group(['prefix' => 'v1', 'middleware' => 'normalAdminAuthMiddleware'], function () {

	//Does Gateway Collect Logs
	Route::get('/does/gateway/collect/logs', ['uses' => 'GatewayLogController@doesGatewayCollectLogs']);

	//Upload Log Collection for Gateway
	Route::post('/upload/log/collection/gateway', ['uses' => 'GatewayLogController@uploadLogCollection']);

	//Toggle Default Drop Firewall Policy on SDP Gateway
	Route::post('/toggle/default/drop/firewall/policy/gateway', ['uses' => 'GatewayController@toggleDefaultDropFirewallPolicy']);

	//Turn on/off Service on Gateway Machine
	Route::post('/toggle/gateway/service/status', ['uses' => 'GatewayController@toggleGatewayServiceStatus']);

	//Get Gateway Stats
	Route::get('/get/gateway/network/traffic', ['uses' => 'GatewayNetworkTrafficController@getGatewayNetworkTrffc']);

	//Delete Trust Score Policy
	Route::post('/user/delete/trust/policy', ['uses' => 'TrustScorePolicyController@userDeleteTrustPolicy']);

	//Delete Trust Score Weight
	Route::post('/user/delete/trust/score/weight', ['uses' => 'TrustScoreWeightController@usrDltTrstScoreWeight']);

	//Delete Client Geo Loc
	Route::post('/user/delete/client/geo/loc', ['uses' => 'ClientGeoLocationController@userDeleteClientGeoLoc']);

	//Delete Face Rec Data
	Route::post('/user/delete/face/rec/data', ['uses' => 'FaceRecognitionController@deleteFaceRecData']);

	//Delete Client Mac
	Route::post('/user/delete/client/mac', ['uses' => 'ClientMacController@userDeleteClientMac']);

	//Get All Clients Geo Locs
	Route::get('/user/get/all/client/geo/locs', ['uses' => 'ClientGeoLocationController@getAllClientGeoLocs']);

	//Get All Clients Mac List
	Route::get('/user/get/all/clients/macs/list', ['uses' => 'ClientMacController@getAllClientsMacList']);

	//Post Client Geo Location
	Route::post('/user/create/client/geo/location', ['uses' => 'ClientGeoLocationController@addNewGeoForClient']);

	//Post Client Mac Address
	Route::post('/user/post/client/mac', ['uses' => 'ClientMacController@createNewMacForClient']);

	//Get All Faces List
	Route::get('/user/sdp/get/all/face/rec/list', ['uses' => 'FaceRecognitionController@userSdpGetAllFacesList']);

	//Post Face for Face Recognition
	Route::post('/user/post/face/recognition/face', ['uses' => 'FaceRecognitionController@userPostNewFaceRec']);

	//Get All Trust Score Policies
	Route::get('/user/trust/policies/get/all', ['uses' => 'TrustScorePolicyController@usertrustGetAllPolicies']);

	//Create New Trust Score Policy
	Route::post('/user/post/trust/score/policy', ['uses' => 'TrustScorePolicyController@userPostTrustScorePolicy']);

	//Get All Trust Score Factor Weights
	Route::get('/user/get/all/trust/score/factor/weights', ['uses' => 'TrustScoreWeightController@getAllFactorsWeights']);

	//Add New Trust Score Factor Weight
	Route::post('/user/post/trust/score/factor/weight', ['uses' => 'TrustScoreWeightController@pstTrustScoreWghtfrFactor']);

	//Get Trust Score Factors
	Route::get('/user/get/trust/score/factors', ['uses' => 'Controller@userGetTrustScoreFactors']);

	//Add Service to Client
	Route::post('/user/gateway/client/add/service', ['uses' => 'ClientController@addGatewayClientAddService']);

	//Delete Gateway Service Access
	Route::post('/user/delete/gateway/client/service', ['uses' => 'ClientController@uDltGatewayClientAccess']);

	//Get Client Gateway Services List
	Route::get('/user/gateway/client/services/list/{clientId}', ['uses' => 'ClientController@uGtwyClientServiceList']);

	//Delete Client
	Route::delete('/user/client/delete/{clientId}', ['uses' => 'ClientController@userDeleteClient']);

	//Get All Clients
	Route::get('/user/sdp/get/all/clients', ['uses' => 'ClientController@userSdpGetAllClients']);

	//Create New Client
	Route::post('/user/sdp/create/client', ['uses' => 'ClientController@userSDPCreateClient']);

	//User Delete Service Gateway
	Route::post('/user/gateway/delete/service', ['uses' => 'GatewayController@userGatewayDeleteService']);

	//User Add Service to Gateway
	Route::post('/user/gateway/add/service', ['uses' => 'GatewayController@userGatewayAddService']);

	//Get Gateway Services
	Route::get('/user/get/gateway/service/{gatewayId}', ['uses' => 'GatewayController@userGetGatewayService']);

	//Update Gateway
	Route::patch('/user/gateway/update', ['uses' => 'GatewayController@userUpdateGateway']);

	//Delete Gateway
	Route::delete('/user/gateway/delete/{gatewayId}', ['uses' => 'GatewayController@userGatewayDelete']);

	//Get All Services
	Route::get('/user/services/get/all', ['uses' => 'ServiceController@userGetAllServices']);

	//Get All Gateways
	Route::get('/user/gateway/get/all', ['uses' => 'GatewayController@userGatewayGetAll']);

	//Create New Gateway
	Route::post('/user/gateway/create', ['uses' => 'GatewayController@userCreateGateway']);

});

//Routes Protected with Middleware
//Normal Middleware Level
Route::group(['prefix' => 'v1', 'middleware' => 'userAuthMiddleware'], function () {

	//Logout API
	Route::post('/auth/logout', ['uses' => 'Controller@accountLogout']);

});

//Routes Protected with Middleware
//Client Middleware Level
Route::group(['prefix' => 'v1', 'middleware' => 'clientAuthMiddleware'], function () {

	//Validate Geo Location Data
	Route::post('/validate/client/geo/location', ['uses' => 'ClientGeoLocationController@validateClientGeoLocation']);
		
	//Get Continue Next Page
	Route::get('/get/next/page/client/continue', ['uses' => 'ClientController@getNextPageClientContinue']);

	//Mac Address Check
	Route::post('/validate/client/mac/address', ['uses' => 'ClientController@validateClientMacAddress']);

	//Face Recognition Check
	Route::post('/client/face/recognition/verification', ['uses' => 'ClientController@clntFaceRecognitionVrfcn']);

	//Post Client Gateway for This User
	Route::post('/post/client/gateway/access/srvc', ['uses' => 'ClientController@pstClntGtwyAccss']);

	//Get Client Gateway List
	Route::get('/client/get/gateways/list', ['uses' => 'ClientController@clientGetGatewayList']);

	//Generate Encryption Key and Hmac key
	Route::post('/client/generate/encryption/hmac/keys', ['uses' => 'ClientController@gnrtEncryptionHmacKey']);

	//Logout Clear Trust Score
	Route::get('/client/logout/clear/trust/score', ['uses' => 'ClientController@logoutClearTrustScore']);

	//Pull Granted Services List
	Route::get('/client/pull/granted/services', ['uses' => 'ClientController@clientPullGrantedServices']);
	
});

//Routes Protected with Middleware
//Gateway Routes
Route::group(['prefix' => 'v1', 'middleware' => 'gatewayAuthMiddleware'], function () {

	//Post TX Gateway Stats
	Route::post('/post/gateway/network/traffic/tx', ['uses' => 'GatewayNetworkTrafficController@postGatewayTrafficTx']);

});

//Unprotected Routes
Route::group(['prefix' => 'v1'], function () {

	//validate Client Credentials
	Route::post('/validate/client/credentials', ['uses' => 'ClientController@validateClientCredentials']);

	//Get Gateway Stanzas
	Route::get('/get/confs/stanzas', ['uses' => 'GatewayController@getConfsStanzas']);

	//Auth Login
	Route::post('/auth/login', ['uses' => 'Controller@accountLogin']);

	//Auth Register
    Route::post('/auth/register', ['uses' => 'Controller@createNewAccount']);

});