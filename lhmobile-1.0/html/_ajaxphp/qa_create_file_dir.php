<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;

	$dirName = $mysql->real_escape_string($_GET['dirName']);
	if(!empty($dirName)) {
		$dir_name = "$dirName";
	} else {
		$dir_name = "temp_" .date("m.d.Y_H.i.s") ."/";
	}
	$path = $_SERVER['DOCUMENT_ROOT']."/qafiles/";
	//User to assgin the new dir to
	$user = "nbclh";
	
	if(!is_dir($path .$dir_name)) {
		mkdir($path .$dir_name);		
		echo $dir_name;
	}
?>