<?php
require_once "loxberry_log.php";

// check parameter "petname"
if(empty($_GET['petname'])){
	die("Usage: ".$_SERVER['PHP_SELF']."?petname=[...]&location=[1|2] or [in|out]\n");
}

// check parameter "location"
switch($_GET['location']){
	case "0":
	case "in":
		$location = 1;
		$location_str = "inside";
		break;
	case "1":
	case "out":
		$location = 2;
		$location_str = "outside";
		break;
	default:
		die("Usage: ".$_SERVER['PHP_SELF']."?petname=[...]&location=[0|1] or [in|out]\n");
}

$params = [
    "name" => "Daemon",
    "filename" => "$lbplogdir/sureflap.log",
    "append" => 1
];
$log = LBLog::newLog ($params);

LOGSTART("SureFlap HTTP setPetLocation.php started");
LOGDEB("setPetLocation: ".$location_str." for ".$_GET['petname']);

// get new data - no output
$background = true;
include 'getData.php';

// Check if pet match
if($petname != $_GET['petname']) {
	LOGERR("Pet does not match!");
	die("Pet does not match!");
}

if($curr_location_id == $location) {
	print "Location for \"$petname\" is \"$location_str\". No change necessary.<br>";
	LOGINF("Location for \"$petname\" is \"$location_str\". No change necessary.");
} else {
	// Set Timezone to UTC
	date_default_timezone_set('UTC');
	$json = json_encode(array("where" => $location, "since" => date("Y-m-d H:i:s")));
	// reset timezone
	date_default_timezone_set($server_timezone);

	LOGDEB("Starting request...");
	$ch = curl_init($endpoint."/api/pet/$petid/position");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Content-Length: ".strlen($json),"Authorization: Bearer $token"));
	$result = json_decode(curl_exec($ch),true) or die("Curl Failed\n");
	LOGDEB("Request received with code: ".curl_getinfo($ch, CURLINFO_HTTP_CODE));

	if($result['data']['where'] == $location) {
		print "Successfully set pet location for \"$petname\" to \"$location_str\"<br><br>";
		LOGINF("Successfully set pet location for \"$petname\" to \"$location_str\"");
	} else {
		print "Set Location Failed!<br>";
		LOGERR("Set Location Failed!");
	}
	
	if($config_http_send == 1) {
		// Build data to responce
		$pets = array(array("id" => $petid, "name" => $petname, "position" => $result['data']));
		include 'getPets.php';
		// Responce to virutal input
		LOGDEB("Starting Response to miniserver...");
		include_once 'sendResponces.php';
	}
}

LOGEND("SureFlap HTTP setPedLocations.php stopped");/**/
?>

