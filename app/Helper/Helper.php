<?php

function getScoreFactorsList() {
	return array(
		array('id' => 1, 'title' => 'User Credential Validation'),
		array('id' => 2, 'title' => 'MAC Address Verification'),
		array('id' => 3, 'title' => 'Geo Location Verification'),
		array('id' => 4, 'title' => 'TPM Security Check'),
	    array('id' => 5, 'title' => 'Face Recognition Verification')
		);
}

function getFactorTitle($factorId) {
	
	return getScoreFactorsList()[$factorId - 1]['title'];
	
}