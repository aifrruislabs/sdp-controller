<?php

function getBaseUrl() {

	if (pF() == "") {
		return "http://127.0.0.1:8000";
	}else {
		return "https://sdp.aifrruislabs.com";
	}

}


function getApiBaseUrl() {

	if (pF() == "") {
		return "http://127.0.0.1:8000";
	}else {
		return "https://sdpapi.aifrruislabs.com";
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


function getSnortICDResponseList() {
	$snortICDResponseList = array(
	            array(
	                    'id' => 1,
	                    'code' => 'TCbZC',
	                    'description' => 'Terminate Client by Zero Score' 
	                ),

	    );

	return $snortICDResponseList;
}


//Get Snort Class List
function getSnortClassList() {

		$snortClassList = array(
	                array(
	                        'id' => 1,
	                        'classType' => 'not-suspicious', 
	                        'description' => 'Not Suspicious Traffic',
	                        'priority' => 3 
	                    ),

	                array(
	                        'id' => 2,
	                        'classType' => 'unknown', 
	                        'description' => 'Unknown Traffic',
	                        'priority' => 3 
	                    ),

	                array(
	                        'id' => 3,
	                        'classType' => 'bad-unknown', 
	                        'description' => 'Potentially Bad Traffic',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 4,
	                        'classType' => 'attempted-recon', 
	                        'description' => 'Attempted Information Leak',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 5,
	                        'classType' => 'successful-recon-limited', 
	                        'description' => 'Information Leak',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 6,
	                        'classType' => 'successful-recon-largescale', 
	                        'description' => 'Large Scale Information Leak',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 7,
	                        'classType' => 'attempted-dos', 
	                        'description' => 'Attempted Denial of Service',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 8,
	                        'classType' => 'successful-dos', 
	                        'description' => 'Denial of Service',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 9,
	                        'classType' => 'attempted-user', 
	                        'description' => 'Attempted User Privilege Gain',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 10,
	                        'classType' => 'unsuccessful-user', 
	                        'description' => 'Unsuccessful User Privilege Gain',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 11,
	                        'classType' => 'succesful-user', 
	                        'description' => 'Successful User Privilege Gain',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 12,
	                        'classType' => 'attempted-admin', 
	                        'description' => 'Attempted Administrator Privilege Gain',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 13,
	                        'classType' => 'successful-admin', 
	                        'description' => 'Successful Administrator Privilege Gain',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 14,
	                        'classType' => 'rpc-portmap-decode', 
	                        'description' => 'Decode of an RPC Query',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 15,
	                        'classType' => 'shellcode-detect', 
	                        'description' => 'Executable code was detected',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 16,
	                        'classType' => 'string-detect', 
	                        'description' => 'A suspicious string was detected',
	                        'priority' => 3
	                    ),

	                array(
	                        'id' => 17,
	                        'classType' => 'suspicious-filename-detect', 
	                        'description' => 'A suspicious filename was detected',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 18,
	                        'classType' => 'suspicious-login', 
	                        'description' => 'An attempted login using a suspicious username was detected',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 19,
	                        'classType' => 'system-call-detect', 
	                        'description' => 'A system call was detected',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 20,
	                        'classType' => 'tcp-connection', 
	                        'description' => 'A TCP connection was detected',
	                        'priority' => 4
	                    ),

	                array(
	                        'id' => 21,
	                        'classType' => 'trojan-activity', 
	                        'description' => 'A Network Trojan was detected',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 22,
	                        'classType' => 'unusual-client-port-connection', 
	                        'description' => 'A client was using an unusual port',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 23,
	                        'classType' => 'network-scan', 
	                        'description' => 'Detection of a Network Scan',
	                        'priority' => 3
	                    ),

	                array(
	                        'id' => 24,
	                        'classType' => 'denial-of-service', 
	                        'description' => 'Detection of a Denial of Service Attack',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 25,
	                        'classType' => 'non-standard-protocol', 
	                        'description' => 'Detection of a non-standard protocol or event',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 26,
	                        'classType' => 'protocol-command-decode', 
	                        'description' => 'Generic Protocol Command Decode',
	                        'priority' => 3
	                    ),

	                array(
	                        'id' => 27,
	                        'classType' => 'web-application-activity', 
	                        'description' => 'Access to a potentially vulnerable web application',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 28,
	                        'classType' => 'web-application-attack', 
	                        'description' => 'Web Application Attack',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 29,
	                        'classType' => 'misc-activity', 
	                        'description' => 'Misc Activity',
	                        'priority' => 3
	                    ),

	                array(
	                        'id' => 30,
	                        'classType' => 'misc-attack', 
	                        'description' => 'Misc Attack',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 31,
	                        'classType' => 'icmp-event', 
	                        'description' => 'Generic ICMP event',
	                        'priority' => 3
	                    ),

	                array(
	                        'id' => 32,
	                        'classType' => 'inappropriate-content', 
	                        'description' => 'Inappropriate Content was Detected',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 33,
	                        'classType' => 'policy-violation', 
	                        'description' => 'Potential Corporate Privacy Violation',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 34,
	                        'classType' => 'default-login-attempt', 
	                        'description' => 'Attempt to login by a default username and password',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 35,
	                        'classType' => 'sdf', 
	                        'description' => 'Sensitive Data',
	                        'priority' => 2
	                    ),

	                array(
	                        'id' => 36,
	                        'classType' => 'file-format', 
	                        'description' => 'Known malicious file or file based exploit',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 37,
	                        'classType' => 'malware-cnc', 
	                        'description' => 'Known malware command and control traffic',
	                        'priority' => 1
	                    ),

	                array(
	                        'id' => 38,
	                        'classType' => 'client-side-exploit', 
	                        'description' => 'Known client side exploit attempt',
	                        'priority' => 1
	                    ),

	            );

	return $snortClassList;
}


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