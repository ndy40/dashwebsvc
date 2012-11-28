<?php
require_once("MySQL.inc.php");
db_connect();

function addCheckin()
{
	global $strLocationId, $strUserId;
	
	//some data is missing;
	if($strLocationId == "" || $strUserId == "")
	{
		return -25;
	} 
	$strTime = time();
	dbn("INSERT INTO locationcheckin (LocationId, UserId, DateTime) VALUES ('".$strLocationId."', '".$strUserId."', '".$strTime."')");
	
	db("SELECT CheckInId FROM locationcheckin WHERE LocationId = '".$strLocationId."' AND UserId = '".$strUserId."' AND DateTime = '".$strTime."'");
	$location_created = dbr();
	//location has successfully registered
	if (empty($location_created) == false)
	{ 					
		dbn("UPDATE locations SET LocationCheckins = LocationCheckins + 1 WHERE LocationId = '".$strLocationId."'");
		return $location_created['CheckInId'];									
	} 
	//location was not registered
	else 
	{					
		return -26;			
	}
}

function addLocation()
{
	global $strVenueName, $strVenueAddress, $strAPIId, $strLocationId, $strLatitude, $strLongitude;
	
	//some data is missing;
	if($strVenueName == "" || $strVenueAddress == "" || $strAPIId == "" || $strLatitude == "" || $strLongitude == "")
	{
		return 0;
	} 

	$strLocationId = "";
	
	//Check to see if the actual location exists
	db("SELECT LocationId FROM locations WHERE LocationName = '".$strVenueName."' AND LocationAddress = '".$strVenueAddress."' AND API_ID = '".$strAPIId."'");
	$location_exists = dbr();

	if (empty($location_exists) == true)
	{		
		//Check to see if the locationname is in use
		db("SELECT LocationId FROM locations WHERE API_ID = '".$strAPIId."'");
		$apiID_exists = dbr();
		
		if (empty($apiID_exists) == true)
		{
			
			//Insert our new location
			dbn("INSERT INTO locations (LocationName, LocationAddress, API_ID, LocationLat, LocationLong) VALUES ('".$strVenueName."', '".$strVenueAddress."', '".$strAPIId."', '".$strLatitude."', '".$strLongitude."')");
			
			db("SELECT LocationId FROM locations WHERE LocationName = '".$strVenueName."' AND LocationAddress = '".$strVenueAddress."' AND API_ID = '".$strAPIId."'");
			$location_created = dbr();
			//location has successfully registered
			if (empty($location_created) == false)
			{ 					
				$strLocationId = $location_created['LocationId'];									
				return $location_created['LocationId'];									
			} 
			//location was not registered
			else 
			{					
				return -1;			
			}
			
		}
		//location API_ID is already in use		
		else 
		{			
			return -2;
		}
		
	} 
	//location already exists
	else 
	{		
		$strLocationId = $location_exists['LocationId'];	
		return $location_exists['LocationId'];		
	}
}

header('');
 
$strVenueName = isset($_REQUEST['vn']) ? db_escape($_REQUEST['vn']) : "";
$strVenueAddress = isset($_REQUEST['va']) ? db_escape($_REQUEST['va']) : "";
$strAPIId = isset($_REQUEST['id']) ? db_escape($_REQUEST['id']) : "";
$strLatitude = isset($_REQUEST['la']) ? db_escape($_REQUEST['la']) : "";
$strLongitude = isset($_REQUEST['ln']) ? db_escape($_REQUEST['ln']) : "";
$strUserId = isset($_REQUEST['ud']) ? db_escape($_REQUEST['ud']) : "";
$strLocationId = isset($_REQUEST['vd']) ? db_escape($_REQUEST['vd']) : "";
$action = isset($_REQUEST['a']) ? db_escape($_REQUEST['a']) : "";
 
switch($action)
{
	case "addLocation":
		$addLoc = addLocation();
		$outputArray = Array("success" => true, "result" => $addLoc); 
        echo json_encode($outputArray);	
		break;
	case "addCheckin":
		$addCheck = addCheckin();
		$outputArray = Array("success" => true, "result" => $addCheck); 
        echo json_encode($outputArray);	
		break;
	case "LocationAndCheckin":
		$addLoc = addLocation();
		$addCheck = addCheckin();
		$outputArray = Array("success" => true, "Location" => $addLoc, "Checkin" => $addCheck); 
        echo json_encode($outputArray);			
		break;
	default:
		$outputArray = Array("success" => false, "result" => -999); 
		echo json_encode($outputArray);	 
		break;
}