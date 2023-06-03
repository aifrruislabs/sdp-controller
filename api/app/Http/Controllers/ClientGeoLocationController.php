<?php

namespace App\Http\Controllers;

use App\Client;
use App\TrustScoreWeight;
use App\TrustScoreTracker;
use App\ClientGeoLocation;
use Illuminate\Http\Request;

use App\Http\Controllers\ClientController;

class ClientGeoLocationController extends Controller
{
	//validateClientGeoLocation
	public function validateClientGeoLocation(Request $request)
	{
		//Input Validation
		$this->validate($request,
					[
						'latitude' => 'required',
						'longitude' => 'required'
					]);

		$clientId = $request->header('clientId');
		$userLatitude = $request->latitude;
		$userLongitude = $request->longitude;

		$clientData = Client::where('clientId', $clientId)->get()->toArray();

		$adminUserId = $clientData['0']['userId'];

		//Check Geo Location Data on ClientGeoLocation
		$cGeoData = ClientGeoLocation::where([
									['userId', '=', $adminUserId],
									['clientId', '=', $clientId]
									])->get()->toArray();

		if (sizeof($cGeoData) == 1) {

			$scoreFactorsUserList = TrustScoreWeight::where([
			                            ['userId', '=', $adminUserId]
			                        ])->get();

			$slatitude = $cGeoData['0']['latitude'];
			$slongitude = $cGeoData['0']['longitude'];
			$kilometreRadius = intval($cGeoData['0']['kilometreRadius']);

			//Check for Next Factor in the List
			$scoreFactorIdsList = array();

			foreach ($scoreFactorsUserList as $factorIdRes) {
			    $scoreFactorIdsList[] = $factorIdRes->scoreFactorId;
			}

			$clientController = new ClientController;

			$kmDiff = intval($this->latLongDiffDistance($userLatitude, $userLongitude, $slatitude, $slongitude));

			if ($kmDiff <= $kilometreRadius) {

				$getScorePercentQuery = TrustScoreWeight::where([
				                            ['userId', '=', $adminUserId],
				                            ['scoreFactorId', '=', 3]
				                        ])->get()->toArray();

				$scorePercent = $getScorePercentQuery['0']['scoreFactorPercent'];

				//Add Score Track in TrustScore Tracker
				$addScoreTrack = new TrustScoreTracker();
				$addScoreTrack->userId = $adminUserId;
				$addScoreTrack->clientId = $clientId;
				$addScoreTrack->trustScoreFactorId = 3;
				$addScoreTrack->percentScored = intval($scorePercent);
				$addScoreTrack->save();

				//Add New Score to User || Update Client Score
				$newClientTrustScore = intval($clientData['0']['totalTrustScore'])  
				                        + intval($scorePercent);

				//Update Client Score
				$updateClientScore = Client::find($clientData['0']['id']);
				$updateClientScore->totalTrustScore = $newClientTrustScore;
				$updateClientScore->update();

				//Remove Ids on Tracker
				$scoreFactorIdsRes = $clientController->removeIdsOnTracker($scoreFactorIdsList, $clientId);

				return response()->json(array(
						'status' => true, 
						'kmDiff' => $kmDiff,
						'scoreFactorIdsList' => $scoreFactorIdsRes), 200);
			}else {
				return response()->json(array('status' => false), 200);
			}

		}else {
			return response()->json(array('status' => false), 200);
		}
	}

	//Calculate LatlongDistance
	private function latLongDiffDistance($lat1, $lon1, $lat2, $lon2) { 
	        $pi80 = M_PI / 180; 
	        $lat1 *= $pi80; 
	        $lon1 *= $pi80; 
	        $lat2 *= $pi80; 
	        $lon2 *= $pi80; 
	        $r = 6372.797; // radius of Earth in km 6371
	        $dlat = $lat2 - $lat1; 
	        $dlon = $lon2 - $lon1; 
	        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
	        $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
	        $km = $r * $c; 
	        return round($km); 
	}

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
