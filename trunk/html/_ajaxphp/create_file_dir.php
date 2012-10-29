<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$dirName = $mysql->real_escape_string($_GET['dirName']);
	if(!empty($dirName)) {
		$dir_name = "$dirName";
	} else {
		$dir_name = "temp_" .date("m.d.Y_H.i.s") ."/";
	}
	//$dir_name = "temp_" .date("m.d.Y_H.i.s") ."/";
	//$path = WEBPATH ."files/";
	$path = $_SERVER['DOCUMENT_ROOT']."/files/";
	//User to assgin the new dir to
	$user = "nbclh";
	
	if(!is_dir($path .$dir_name)) {
		mkdir($path .$dir_name);
		
		//This file needs the proper permissions to run this function
		//chown($path, $user);
		
		echo $dir_name;
	}
?>