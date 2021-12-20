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

//Unprotected Routes
Route::group(['prefix' => 'v1'], function () {

	//Get Gateway Stanzas
	Route::get('/get/confs/stanzas', ['uses' => 'GatewayController@getConfsStanzas']);

	//Auth Login
	Route::post('/auth/login', ['uses' => 'Controller@accountLogin']);

	//Auth Register
    Route::post('/auth/register', ['uses' => 'Controller@createNewAccount']);

});