<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientGeoLocation;
use Illuminate\Http\Request;

class ClientGeoLocationController extends Controller
{
	//userDeleteClientGeoLoc
	public function userDeleteClientGeoLoc(Request $request)
	{
		//Input Validation
		$this->validate($request,
					[
						'geoLocId' => 'required'
					]);

		//Delete Geo Location
		$deleteGeoLoc = ClientGeoLocation::find($request->geoLocId)->delete();

		return response()->json(array('status' => true), 200);
	}

	//getAllClientGeoLocs
	public function getAllClientGeoLocs(Request $request)
	{
		$userId = $request->header('userId');

		$clientsGeoLocsList = ClientGeoLocation::where('userId', $userId)->get();

		$clientsGeoLocsListRet = array();
		foreach ($clientsGeoLocsList as $clientGeo) {
			$clientData = Client::where('clientId', $clientGeo->clientId)->get()->toArray();

			$clientsGeoLocsListRet[] = array(
								'id' => $clientGeo->id,
								'firstName' => $clientData['0']['firstName'],
								'lastName' => $clientData['0']['lastName'],
								'clientId' => $clientGeo->clientId,
								'latitude' => $clientGeo->latitude,
								'longitude' => $clientGeo->longitude,
								'kilometreRadius' => $clientGeo->kilometreRadius
								);
		}

		return response()->json(array('status' => true, 'data' => $clientsGeoLocsListRet), 200);
	}

    //addNewGeoForClient
	public function addNewGeoForClient(Request $request)
	{
		//Input Verification
		$this->validate($request,
				[
					'latitude' => 'required',
					'longitude' => 'required',
					'clientId' => 'required',
					'kilometreRadius' => 'required'
				]);

		$userId = $request->header('userId');
		$clientId = $request->clientId;

		//Check if Client Has Latitude and Longitude
		$checkLatLongForClient = ClientGeoLocation::where('clientId', $clientId)->get()->toArray();

		if (sizeof($checkLatLongForClient) == 0) {
			//Add New
			$newClientLatLong = new ClientGeoLocation();
			$newClientLatLong->userId = $userId;
			$newClientLatLong->clientId = $clientId;
			$newClientLatLong->latitude = $request->latitude;
			$newClientLatLong->longitude = $request->longitude;
			$newClientLatLong->kilometreRadius = $request->kilometreRadius;
			$newClientLatLong->save();

			return response()->json(array('status' => true, 'message' => 'NEW_CREATED'), 201);
		}else {
			//Update Existing
			$updateClientLatLong = ClientGeoLocation::find($checkLatLongForClient['0']['id']);
			$updateClientLatLong->latitude = $request->latitude;
			$updateClientLatLong->longitude = $request->longitude;
			$updateClientLatLong->kilometreRadius = $request->kilometreRadius;
			$updateClientLatLong->update();

			return response()->json(array('status' => true, 'message' => 'NEW_UPDATED'), 200);
		}
	}
}
