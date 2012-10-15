<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$start_date = $_POST['startDate'];
	$user_id = $_POST['userid'];
	
	//$start_date = $_GET['startDate'];
	//$user_id = $_GET['userid'];
	
	$start_date_part = explode("-", $start_date);
	$otdata = array();
	
	$select_ot = "SELECT * FROM `resource_blocks` WHERE `daypart`='9' AND `userid`='$user_id' AND `hours` > 0 AND `datestamp`='"
		.$start_date_part[2] ."/"
		.$start_date_part[0] ."/"
		.$start_date_part[1]
		."' LIMIT 1";
	$otRes = $mysql->query($select_ot);
	
	if($otRes->num_rows == 1) {
		$otRow = $otRes->fetch_assoc();
		
		$otdata ['project'] = $otRow['projectid'];
		$otdata ['hours'] = $otRow['hours'];
		$otdata ['notes'] = $otRow['notes'];
	} else {
		$otdata ['project'] = "";
		$otdata ['hours'] = "";
		$otdata ['notes'] = "";
	}
	
	$jsonSettings = json_encode($otdata);

	// output correct header
	$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 
	  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
	($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

	echo $jsonSettings;
?>