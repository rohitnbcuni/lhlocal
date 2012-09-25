<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;
	
	$defectId = $mysql->real_escape_string($_GET['defectId']);
	
	$wo_selecte_query = "SELECT * from  `qa_defects` WHERE `id`= ? and deleted='0' LIMIT 1";
	$result = $mysql->sqlprepare($wo_selecte_query,array($defectId));
	if($result->num_rows == 1){
		echo "1";
	}else{
		echo "0";
	}
	
?>