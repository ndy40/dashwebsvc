<?php
require_once("MySQL.inc.php");
db_connect();

function updateReview()
{
	global $intReviewId, $strWhichRating;
	
	if($intReviewId == "" || $strWhichRating == "")
	{
		return 0;
	}
	
	dbn("UPDATE locationreview SET ".$strWhichRating." = ".$strWhichRating." + 1 WHERE ReviewId = '".$intReviewId."'");
	
	
	return 1;
}
 
header('');	
 
$intReviewId = isset($_REQUEST['ri']) ? db_escape($_REQUEST['ri']) : "";
$strWhichRating = isset($_REQUEST['rr']) ? db_escape($_REQUEST['rr']) : "";
$action = db_escape($_REQUEST['a']); 
 
switch($action)
{
	case "updateReview":
		$update = updateReview();
		$outputArray = Array("status" => true, "result" => $update); 
        echo json_encode($outputArray);	
		break;
	default:
		$outputArray = Array("status" => false, "result" => -999); 
		echo json_encode($outputArray);	 
		break;
}
?>