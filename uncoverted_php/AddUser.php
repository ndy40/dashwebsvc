<?php
require_once("MySQL.inc.php");
db_connect();

function addRunner()
{
	global $strUserName, $strPassword, $strFirstName, $strSurName, $strEmail;
	
	//"ome data is missing;
	if($strUserName == "" || $strPassword == "" || $strFirstName == "" || $strSurName == "" || $strEmail == ""){		
		return 0;
	} 

	//Check to see if the actual user exists
	db("SELECT User_id FROM users WHERE UserName = '".$strUserName."' AND Firstname = '".$strFirstName."' AND Surname = '".$strSurName."' AND Email = '".$strEmail."'");
	$user_exists = dbr();

	if (empty($user_exists) == true)
	{		
		//Check to see if the username is in use
		db("SELECT User_id FROM users WHERE UserName = '".$strUserName."'");
		$username_exists = dbr();
		
		if (empty($username_exists) == true)
		{
			
			//Check to see if the email address is in use
			db("SELECT User_id FROM users WHERE Email = '".$strEmail."'");
			$Email_exists = dbr();
			
			if (empty($Email_exists) == true)
			{
				//Insert our new user
				dbn("INSERT INTO users (UserName, Password, Firstname, Surname, Email) VALUES ('".$strUserName."', '".$strPassword."', '".$strFirstName."', '".$strSurName."', '".$strEmail."')");
				
				db("SELECT User_id FROM users WHERE UserName = '".$strUserName."' AND Firstname = '".$strFirstName."' AND Surname = '".$strSurName."' AND Email = '".$strEmail."'");
				$user_registered = dbr();
				//User has successfully registered
				if (empty($user_registered) == false)
				{ 					
					return $user_registered['User_id'];									
				} 
				//User was not registered
				else 
				{					
					return -1;			
				}

			}
			//Email Address is already in use
			else 
			{				
				return -2;
			}
			
		}
		//Username is already in use		
		else 
		{			
			return -3;
		}
		
	} 
	//User already exists
	else 
	{		
		return -4;
	}
}

header('');
 
if(!isset($_POST['SecuriKey']) || $_POST['SecuriKey'] != "C93gNIbIRr8lzdYjjiw0")
{
    die('Unauthorised access to script.');
}
 
$strUserName = isset($_POST['u']) ? db_escape($_POST['u']) : "";
$strPassword = isset($_POST['p']) ? db_escape($_POST['p']) : "";
$strFirstName = isset($_POST['f']) ? db_escape($_POST['f']) : "";
$strSurName = isset($_POST['s']) ? db_escape($_POST['s']) : "";
$strEmail = isset($_POST['e']) ? db_escape($_POST['e']) : "";
$action = db_escape($_POST['a']); 
 
switch($action)
{
	case "addRunner":
		$addRun = addRunner();
		$outputArray = Array("success" => true, "result" =>$addRun); 
        echo json_encode($outputArray);	
	default:
		$outputArray = Array("success" => false, "result" => -5; 
        echo json_encode($outputArray);	 
}

?>