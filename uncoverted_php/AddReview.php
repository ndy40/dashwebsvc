<?php
require_once("MySQL.inc.php");
db_connect();

function addReview()
{
	global $intLocationId, $intUserId, $strReview, $fltRating;
	
	if($intLocationId == "" || $intUserId == "")    
	{
		return 0;
	}
	
	if($strReview == "" && $fltRating == "")
	{
		return -1;
	}
	
	dbn("INSERT INTO locationreview (LocationId, UserId, DateTime, ReviewText, ReviewRating) VALUES ('".$intLocationId."', '".$intUserId."', '".Time()."', '".$strReview."', '".$fltRating."')");	
	
	if($fltRating > 0.0)
	{
		db("SELECT AVG(ReviewRating) as Average FROM locationreview WHERE LocationId = '".$intLocationId."' AND ReviewRating > 0.0");
		$avg = dbr();
		
		if($avg['Average'] != null)
		{	
			dbn("UPDATE locations SET LocationRatings = '".$avg['Average']."', LocationUseRatings = '1' WHERE LocationId = '".$intLocationId."'");
		}
	}
	
	return 1;
}
 
header('');	
 
$intLocationId = isset($_REQUEST['ld']) ? db_escape($_REQUEST['ld']) : "";
$intUserId = isset($_REQUEST['ud']) ? db_escape($_REQUEST['ud']) : "";
$strReview = isset($_REQUEST['rt']) ? db_escape($_REQUEST['rt']) : "";
$fltRating = isset($_REQUEST['rr']) ? db_escape($_REQUEST['rr']) : 3.0;
$action = db_escape($_REQUEST['a']); 
 
switch($action)
{
	case "addReview":
		$addResult = addReview();
		$outputArray = Array("success" => true, "result" => $addResult); 
        echo json_encode($outputArray);	
		break;
	default:
		$outputArray = Array("success" => false, "result" => -999); 
		echo json_encode($outputArray);	 
		break;
}

?>