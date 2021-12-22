<?php

namespace App\Http\Controllers;

use App\Client;
use App\Gateway;
use App\Service;
use App\TrustScoreWeight;
use App\ClientServiceAccess;

use App\TrustScorePolicy;
use App\TrustScoreTracker;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    //clientGetGatewayList
    public function clientGetGatewayList(Request $request)
    {
        $clientId = $request->header('clientId');

        $gatewaysWithAccess = ClientServiceAccess::where('clientId', $clientId)
                                ->get()->toArray();

        if (sizeof($gatewaysWithAccess) != 0) {

            $gatewaysArrlist = array();
            $gatewaysDatalist = array();

            foreach ($gatewaysWithAccess as $gate) {
                if (!in_array($gate->gatewayId, $gatewaysArrlist)) {
                    $gatewaysArrlist = $gate->gatewayId;
                }
            }

            foreach ($gatewaysArrlist as $gateId) {
                $gatewaysDatalist[] = Gateway::where('id', $gateId)->get()->toArray()[0];
            }

            return response()->json(array('status' => true, 'data' => $gatewaysDatalist), 200);

        }else {
            return response()->json(array('status' => false, 'error_code' => 'NO_GATEWAY'), 200);
        }
    }

    //gnrtEncryptionHmacKey
    public function gnrtEncryptionHmacKey(Request $request)
    {
        $clientId = $request->header('clientId');

        //Get Client Data
        $clientData = Client::where('clientId', $clientId)->get()->toArray();

        if (sizeof($clientData) == 1) {

            //Get Client Granted Services
            $grantedServicesList = $this->clientPullGrantedServices($request)->original['data'];

            //Generate fwknop command from services list
            $clientPublicIp = $request->client_public_ip;
            $gatewayServerIP = "";
            $servicesProtosPortsList = "";

            $fwknopCmd = "fwknop -A ".$servicesProtosPortsList." -a ".$clientPublicIp.
                            " -D ".$gatewayServerIP." --key-gen --use-hmac";

            $encryptionHmacKeys = exec($fwknopCmd);

            // $encryptionKey = exec("grep KEY_BASE64 ". $encryptionHmacKeys);
            // $hmacKey = exec("grep HMAC_KEY_BASE64 ". $encryptionHmacKeys);

            return response()->json(array('grantedServicesList' => $grantedServicesList), 200);

        }else {
            return response()->json(array('status' => false, 'error_code' => 'CLIENT_NOT_EXIST'), 200);
        }
    }

    //logoutClearTrustScore
    public function logoutClearTrustScore(Request $request)
    {
        $clientId = $request->header('clientId');

        //Clear Client Trust Score to Zero
        $clientData = Client::where('clientId', $clientId)->get()->toArray();

        if (sizeof($clientData) == 1) {
            $updateClientTrustScore = Client::find($clientData['0']['id']);
            $updateClientTrustScore->accessToken = "";
            $updateClientTrustScore->totalTrustScore = "0";
            $updateClientTrustScore->update();

            $clientAdminUserId = $clientData['0']['userId'];

            //Clear Tracks
            $getTracksToClear = TrustScoreTracker::where([
                                    ['userId', '=', $clientAdminUserId],
                                    ['clientId', '=', $clientId]
                                    ])->get();

            foreach ($getTracksToClear as $track) {
                TrustScoreTracker::find($track->id)->delete();
            }

            return response()->json(array('status' => true), 200);

        }else {
            return response()->json(array('status' => false), 200);
        }
    }

    //clientPullGrantedServices
    public function clientPullGrantedServices(Request $request)
    {
        $clientId = $request->header('clientId');

        $grantedServicesList = array();

        //Check Client Trust Score
        $clientTrustScoreQ = Client::where('clientId', $clientId)->get()->toArray();

        if (sizeof($clientTrustScoreQ) == 1) {

            $trustScoreCount = intval($clientTrustScoreQ['0']['totalTrustScore']);
            $clientAdminUserId = $clientTrustScoreQ['0']['userId'];
            $clientTrustScore = $clientTrustScoreQ['0']['totalTrustScore'];

            if ($trustScoreCount != 0) {

                //Check on Trust Score Policies for Allowed Services in 
                //this Score Granted 
                foreach (getScoreFactorsList() as $factorRes) {
                    $policiesOnUserCredentialV = TrustScorePolicy::where([
                                                    ['userId', '=', $clientAdminUserId],
                                                    ['scoreFactorId', '=', $factorRes['id']],
                                                    ['scorePercent', '<=', $trustScoreCount]
                                                    ])->get();

                    foreach ($policiesOnUserCredentialV as $policyServ) {
                        $grantedServicesList[] = Service::where('id', $policyServ->serviceId)
                                                        ->get()
                                                        ->toArray()[0];    
                    }
            
                }

                return response()->json(array(
                            'status' => true, 
                            'clientTrustScore' => $clientTrustScore,
                            'data' => $grantedServicesList), 200);    
            }else {
                return response()->json(array('status' => false, 'data' => $grantedServicesList), 200);    
            }

        }else {
            return response()->json(array('status' => false, 'data' => $grantedServicesList), 200);
        }
    }

    //validateClientCredentials
    public function validateClientCredentials(Request $request)
    {
        //Inputs Validation
        $this->validate($request,
                    [
                        'username' => 'required',
                        'password' => 'required',
                        'client_public_ip' => 'required'
                    ]);

        //Check if Client Id or username Exists
        $checkClientIdUsernameQuery = Client::where([
                                    ['username', '=', $request->username]
                                    ])->orWhere([
                                    ['clientId', '=', $request->username]
                                    ])->get()->toArray();

        if (sizeof($checkClientIdUsernameQuery) == 1) {

            //Verify Password
            if (Hash::check($request->password, $checkClientIdUsernameQuery['0']['password'])) {
                //If Passed Grant Trust Score to Client for this Step
                $clientId = $checkClientIdUsernameQuery['0']['clientId'];
                $clientAdminUserId = $checkClientIdUsernameQuery['0']['userId'];

                //Generate Client Access Token
                $accessToken = sha1($clientId.$checkClientIdUsernameQuery['0']['updated_at'].rand(100, 10000));

                //Update Access Token to Database
                $updateAccessToken = Client::find($checkClientIdUsernameQuery['0']['id']);
                $updateAccessToken->accessToken = $accessToken;
                $updateAccessToken->update();

                $scoreFactorsUserList = TrustScoreWeight::where([
                                            ['userId', '=', $clientAdminUserId]
                                        ])->get();

                $getScorePercentQuery = TrustScoreWeight::where([
                                            ['userId', '=', $clientAdminUserId],
                                            ['scoreFactorId', '=', 1]
                                        ])->get()->toArray();

                if (sizeof($getScorePercentQuery) == 1) {

                    $scorePercent = $getScorePercentQuery['0']['scoreFactorPercent'];

                    //Check Factor in Trust Score Tracker
                    $checkFactorInTrustTracker = TrustScoreTracker::where([
                                                    ['clientId', '=', $clientId],
                                                    ['trustScoreFactorId', '=', 1]
                                                ])->get();

                    if (sizeof($checkFactorInTrustTracker) == 0) {
                        //Add Score Track in TrustScore Tracker
                        $addScoreTrack = new TrustScoreTracker();
                        $addScoreTrack->userId = $clientAdminUserId;
                        $addScoreTrack->clientId = $clientId;
                        $addScoreTrack->trustScoreFactorId = 1;
                        $addScoreTrack->percentScored = intval($scorePercent);
                        $addScoreTrack->save();

                        //Add New Score to User || Update Client Score
                        $newClientTrustScore = intval($checkClientIdUsernameQuery['0']['totalTrustScore'])  
                                                + intval($scorePercent);

                        //Update Client Score
                        $updateClientScore = Client::find($checkClientIdUsernameQuery['0']['id']);
                        $updateClientScore->totalTrustScore = $newClientTrustScore;
                        $updateClientScore->update();

                    }

                }
                
                //Check for Next Factor in the List
                $scoreFactorIdsList = array();

                foreach ($scoreFactorsUserList as $factorIdRes) {
                    $scoreFactorIdsList[] = $factorIdRes->scoreFactorId;
                }

                //Remove Ids on Tracker
                $scoreFactorIdsRes = $this->removeIdsOnTracker($scoreFactorIdsList, $clientId);

                return response()->json(array(
                                    'status' => true, 
                                    'clientId' => $clientId,
                                    'clientToken' => $accessToken,
                                    'scoreFactorIdsList' => $scoreFactorIdsRes), 200);
                
            }else {
                return response()->json(array('status' => false, 'error_code' => 'WRONG_PWD'), 200);
            }

        }else {
            return response()->json(array('status' => false, 'error_code' => 'USER_NOT_EXISTS'), 200);
        }
    }

    //removeIdsOnTracker
    private function removeIdsOnTracker($scoreFactorIdsList, $clientId)
    {
        $responseArr = array();
        $trackerIdsL = array();
        $trackerIdsList = TrustScoreTracker::where('clientId', $clientId)->get();
    
        foreach ($trackerIdsList as $trackerId) {
            $trackerIdsL[] = $trackerId->trustScoreFactorId;
        }

        foreach ($scoreFactorIdsList as $factorId) {
            
            $addTo = 0;
            if (in_array($factorId, $trackerIdsL)) {
                $addTo = 1;
            }

            if ($addTo == 0) {
                $responseArr[] = $factorId;
            }

        }

        return $responseArr;
    }


    //addGatewayClientAddService
    public function addGatewayClientAddService(Request $request)
    {
        //Input Validation
        $this->validate($request,
                [
                    'clientId' => 'required',
                    'serviceId' => 'required',
                    'gatewayId' => 'required'
                ]);

        $userId = $request->header('userId');

        //Get Client Single Id
        $clientSingleIdQuery = Client::where('clientId', $request->clientId)->get()->toArray();
        $clientSingleId = $clientSingleIdQuery['0']['id'];

        //Check If Service Access Not Granted Already
        $checkServiceGrantedAlreadyQuery = ClientServiceAccess::where([
                                        ['gatewayId', '=', $request->gatewayId],
                                        ['serviceId', '=', $request->serviceId],
                                        ['clientId', '=', $clientSingleId]
                                        ])->get();
        if (sizeof($checkServiceGrantedAlreadyQuery) == 0) {

            //Adding Service
            $newClientServiceAccess = new ClientServiceAccess();
            $newClientServiceAccess->userId = $userId;
            $newClientServiceAccess->gatewayId = $request->gatewayId;
            $newClientServiceAccess->serviceId = $request->serviceId;
            $newClientServiceAccess->clientId = $clientSingleId;

            if ($newClientServiceAccess->save()) {
                
                return response()->json(array(
                    'status' => true, 
                    'message' => 'Client Service was Added Successfully'), 200);
            }else {
                return response()->json(array(
                    'status' => false, 
                    'error_code' => 'SRV_ERR'), 200);
            }

        }else {
            return response()->json(array('status' => true), 200);
        }
    }

    //uDltGatewayClientAccess
    public function uDltGatewayClientAccess(Request $request)
    {
        //Input Validation
        $this->validate($request,
                    [
                        'csaId' => 'required',

                        'gatewayId' => 'required',

                        'clientId' => 'required'
                    ]);

        //Deleting Access
        $deleteAcces = ClientServiceAccess::find($request->csaId)->delete();

        return response()->json(array('status' => true, 'message' => 'Access was Removed Successfully'), 200);
    }

    //uGtwyClientServiceList
    public function uGtwyClientServiceList($clientId, Request $request)
    {
        $userId = $request->header('userId');

        $gatewayServiceAccessList = array();
        $gSAL_Services_Nums = array();

        $gatewayServiceNoAccessList = array();

        $servicesListQuery = ClientServiceAccess::where('clientId', $clientId)->get();

        //Working on Gateway Service Access List
        if (sizeof($servicesListQuery) != 0) {

            foreach ($servicesListQuery as $serviceL) {
                $gSAL_Services_Nums[] = $serviceL->serviceId;

                $serviceId = $serviceL->serviceId;
                $gatewayId = $serviceL->gatewayId;

                $clientInfo = Client::where('id', $clientId)->get()->toArray();
                $gatewayInfo = Gateway::where('id', $gatewayId)->get()->toArray();
                $serviceInfo = Service::where('id', $serviceId)->get()->toArray();

                $gatewayServiceAccessList[] = array(
                                'csaId' => $serviceL->id,
                                'gatewayTitle' => $gatewayInfo['0']['gatewayTitle'],
                                'clientId' => $clientInfo['0']['clientId'],
                                'gatewayId' => $gatewayInfo['0']['id'],
                                'serviceId' => $serviceInfo['0']['id'],
                                'serviceTitle' => $serviceInfo['0']['serviceTitle'],
                                'servicePort' => $serviceInfo['0']['servicePort'],
                                'serviceScore' => $serviceInfo['0']['serviceScore'] );
            }

        }


        //Working on Gateway Services No Access list
        $gatewayServices = Gateway::where('userId', $userId)->get();

        foreach ($gatewayServices as $gateway) {
            
            $servicesListG = (Array) json_decode($gateway->gatewayServicesList);

            
            foreach ($servicesListG as $gSAL_ServiceId) {
                
                $addInList = 0;

                foreach ($gatewayServiceAccessList as $serviceInId) {
                    
                    if ($gSAL_ServiceId == $serviceInId['serviceId']) {
                        $addInList = 1;
                    }
                }
            
                if ($addInList != 1) {
                    $clientInfo = Client::where('id', $clientId)->get()->toArray();
                    $gatewayInfo = Gateway::where('id', $gateway->id)->get()->toArray();
                    $serviceInfo = Service::where('id', $gSAL_ServiceId)->get()->toArray();

                    $gatewayServiceNoAccessList[] = array(
                        'gatewayTitle' => $gatewayInfo['0']['gatewayTitle'],
                        'clientId' => $clientInfo['0']['clientId'],
                        'gatewayId' => $gatewayInfo['0']['id'],
                        'serviceId' => $serviceInfo['0']['id'],
                        'serviceTitle' => $serviceInfo['0']['serviceTitle'],
                        'servicePort' => $serviceInfo['0']['servicePort'],
                        'serviceScore' => $serviceInfo['0']['serviceScore'] );    
                }
                
            }
        }

        return response()->json(array(
            'status' => true, 
            'gatewayServiceAccessList' => $gatewayServiceAccessList,
            'gatewayServiceNoAccessList' => $gatewayServiceNoAccessList), 200);

    }

    //userDeleteClient
    public function userDeleteClient($clientId)
    {
        $deleteClient = Client::find($clientId)->delete();

        return response()->json(array('status' => true, 'message' => 'Client was Deleted Successfully'), 200);
    }

    //userSdpGetAllClients
    public function userSdpGetAllClients(Request $request)
    {
        $userId = $request->header('userId');

        $clients = Client::where([
                                ['userId', '=', $userId]
                                ])->orderBy('created_at', 'DESC')->get();

        return response()->json(array('status' => true, 'data' => $clients), 200);
    }

    //userSDPCreateClient
    public function userSDPCreateClient(Request $request)
    {
    	//Input Validation
    	$this->validate($request,
    				[
    					'clientUsername' => 'required',
    					'clientPassword' => 'required'
    				]);

    	$userId = $request->header('userId');

    	//Adding New Client
    	$newClient = new Client();
    	$newClient->userId = $userId;
    	$newClient->firstName = $request->clientFirstName;
    	$newClient->middleName = $request->clientMiddleName;
    	$newClient->lastName = $request->clientLastName;
    	$newClient->username = $request->clientUsername;
        $newClient->totalTrustScore = "0";
    	$newClient->password = bcrypt($request->clientPassword);

    	if ($newClient->save()) {

    		//Update Client ID
    		$updateClientId = Client::find($newClient->id);
    		$updateClientId->clientId = "FRU-".rand(0,1000)."-".$newClient->id;

    		if ($updateClientId->update()) {
    			return response()->json(array('status' => true, 'message' => 'Client was Created Successfully'), 201);
    		}else {
    			return response()->json(array('status' => true, 'error_code' => 'SRV_ERR'), 200);
    		}

    	}else {
    		return response()->json(array('status' => false, 'error_code' => 'SRV_ERR'), 200);
    	}

    }
}
