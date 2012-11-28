<?php
//Host of the computer that holds the database. if the same computer as the web-host, leave as localhost.
//If unsure, use localhost
define("DATABASE_HOST", "mysql.serversfree.com");

//The name of the database within which SE resides
define("DATABASE", "u618791197_dash");

//The username required to access the database
define("DATABASE_USER", "u618791197_dash");

//The password required to access the databas
define("DATABASE_PASSWORD", "group2project");

/*
Function: connect to the database. Will write to the error log if cannot connect.
*/
function db_connect(){
	global $database_link;
	$database_link = @mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD) or die("No connection to the Server could be created.<p>The following error was reported:<br /><b>".mysql_error()."</b>");
	mysql_select_db(DATABASE, $database_link) or die("Unable to connect to database: ".DATABASE);
}

//send a select query to the database.
function db($string) {
	global $db_func_query, $database_link;
	$db_func_query = mysql_query($string, $database_link) or die("<b>".mysql_error()."</b>".$string);
}

//collect results of query made by db() function
function dbr() {
	global $db_func_query;
	return mysql_fetch_array($db_func_query, MYSQL_BOTH);
}

//send a select query to the database.
function db2($string) {
	global $db_func2_query, $database_link;
	$db_func_query = mysql_query($string, $database_link) or die("<b>".mysql_error()."</b>".$string);
}

//collect results of query made by db() function
function dbr2() {
	global $db_func2_query;

	return mysql_fetch_array($db_func_query, MYSQL_BOTH);
}

//send an update or insert query to the database. no select's.
function dbn($string) {
	global $database_link;	
	mysql_query($string, $database_link) or die("<b>".mysql_error()."</b>".$string);
}

//function to escape database data
function db_escape($string){
	if(get_magic_quotes_gpc()){
		$string = stripslashes($string);
	} 
	return mysql_real_escape_string($string);
}
?>