<?
error_reporting(E_ERROR);

session_start();
include('../_inc/config.inc');
include("sessionHandler.php");	
$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

$blocks = Array();
			
if($_SESSION['login_status'] == "client") {
	$client_sql = " AND a.`company`='".$_SESSION['company']."'";
} else {
	$client_sql = "";
}
			
$start = $_POST["startDate"] . " 00:00:00";
$end = $_POST["endDate"] ." 23:59:59";

if ($_POST["userid"]) {
	$userid=$_POST["userid"];
	$sql = "SELECT a.userid as userid, a.notes as notes, a.hours as hours, a.daypart as daypart, a.status as status, a.datestamp as datestamp, b.id as project_id, b.project_name as project_name, b.project_code as project_code FROM resource_blocks a LEFT JOIN projects b ON a.projectid=b.id WHERE a.userid='$userid' AND a.datestamp>='$start' AND a.datestamp<='$end'";
} else {
	$sql = "SELECT a.userid as userid, a.notes as notes, a.hours as hours, a.daypart as daypart, a.status as status, a.datestamp as datestamp, b.id as project_id, b.project_name as project_name, b.project_code as project_code FROM resource_blocks a LEFT JOIN projects b ON a.projectid=b.id WHERE  a.datestamp>='$start' AND a.datestamp<='$end'";	
}

$res = $mysql->query($sql);
	
if ($res->num_rows > 0) {
	while ($row = $res ->fetch_assoc()) {
		array_push($blocks,$row);
	}
}

$jsonSettings = json_encode($blocks);

// output correct header
$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 
  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

echo $jsonSettings;

?>