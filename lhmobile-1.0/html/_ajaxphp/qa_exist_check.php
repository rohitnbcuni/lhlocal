<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$defectId = $mysql->real_escape_string($_GET['defectId']);
	
	$wo_selecte_query = "SELECT * from  `qa_defects` WHERE `id`='$defectId' and deleted='0' LIMIT 1";
	$result = $mysql->sqlordie($wo_selecte_query);
	if($result->num_rows == 1){
		echo "1";
	}else{
		echo "0";
	}
	
?>