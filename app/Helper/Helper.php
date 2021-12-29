<?php

function getBaseUrl() {

	if (pF() == "") {
		return "http://127.0.0.1:8000";
	}else {
		return "https://sdp.aifrruislabs.com";
	}

}

//Public Location
function pF() {

	$currentUrl = url()->current();

	$explodeUrl = explode(":8000", $currentUrl);

	if ($explodeUrl[0] == "http://127.0.0.1" || $explodeUrl[0] == "https://127.0.0.1") {	        
	        return "";
	}else {
		return "/public";
	}
	
}


function getScoreFactorsList() {
	return array(
		array('id' => 1, 'title' => 'User Credential Validation'),
		array('id' => 2, 'title' => 'MAC Address Verification'),
		array('id' => 3, 'title' => 'Geo Location Verification'),
		// array('id' => 4, 'title' => 'TPM Security Check'),
	    array('id' => 5, 'title' => 'Face Recognition Verification')
		);
}

function getFactorTitle($factorId) {
	
	return getScoreFactorsList()[$factorId - 1]['title'];
	
}