<?
session_start();
include('../_inc/config.inc');
include("sessionHandler.php");
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
global $mysql;
$postingList = Array();

if($_SESSION['login_status'] == "client") {
	$client_sql = " AND a.`company`='".$_SESSION['company']."'";
} else {
	$client_sql = "";
} 


if (@$_POST["overtime"]) {
//if (@$_GET["overtime"]) {
	$projectId = $mysql->real_escape_string($_POST['projectid']);
	$hours = $mysql->real_escape_string($_POST['hours']);
	$notes = $mysql->real_escape_string($_POST['notes']);
	$userId = $mysql->real_escape_string($_POST['userid']);
	$date = $mysql->real_escape_string($_POST['date']);
	$date_part = explode("-", $date);
	$dateFormat = $date_part[2] ."/" .$date_part[0] ."/" .$date_part[1];
	
	if(empty($projectId)) {
		$projectId = "null";
	} else {
		$projectId = "'$projectId'";
	}
	
	//$projectId = $_GET['projectid'];
	//$hours = $_GET['hours'];
	//$notes = $_GET['notes'];
	//$userId = $_GET['userid'];
	//$date = $_GET['date'];
	//$date_part = explode("-", $date);
	//$dateFormat = $date_part[2] ."/" .$date_part[0] ."/" .$date_part[1];

	$sql = "SELECT * FROM resource_blocks WHERE userid='$userId' AND daypart='9' AND datestamp='$dateFormat'";
	$res = $mysql->sqlordie($sql);
	if ($res->num_rows > 0) {
		$rb = $res ->fetch_assoc();
		$id = $rb['id'];
		$sql = "UPDATE resource_blocks SET projectid=$projectId,notes='$notes',hours='$hours' WHERE id='$id'";
		//echo $sql;
		$mysql->sqlordie($sql);
	} else {
		$sql = "INSERT INTO resource_blocks (userid,projectid,daypart,datestamp,hours,notes,status) VALUES ('$userId',$projectId,'9','$dateFormat','$hours','$notes','4')";
		//echo $sql;
		$mysql->sqlordie($sql);
	}
} else {
	$newData = str_replace("\\", "", $_POST['data']);
	//print("new data ".$newData);
	$data = unserialize($newData);
	$projectID = substr(strstr($data["projectID"], '_'), 1);
	$status = $data["status"];
	$blocks = $data["blocks"];
	//echo $projectID." - ".$status."<br /><br /><br /><br />";
	//print_r($data);
	//echo "<br /><br /><br /><br />";
	//print_r($blocks);
	//echo "<br /><br /><br /><br />";
	//echo $blocks[0]['id'];/**/

	for($i=0;$i<count($blocks);$i++) {
		$user=$blocks[$i]['user'];
		$dayblock=$blocks[$i]['block'];
		$datestamp = $blocks[$i]['date'] . " 00:00:00";

		if ($status==0) {
			$sql = "DELETE FROM resource_blocks WHERE userid='$user' AND daypart='" .$dayblock['block'] ."' AND datestamp='$datestamp'";
			$res = $mysql->sqlordie($sql);
		} else {
			$sql = "SELECT * FROM resource_blocks WHERE userid='$user' AND daypart='" .$dayblock['block'] ."' AND datestamp='$datestamp'";
			$res = $mysql->sqlordie($sql);
			if ($res->num_rows > 0) {
				if ($status<5) {
					$rb = $res ->fetch_assoc();
					$id = $rb['id'];
					$sql = "UPDATE resource_blocks SET status='$status'";
					if ($projectID==0 && $status==4) {
						//$sql .= ", projectid=NULL";
					} else if ($projectID==0) {
						$sql .= ", projectid=NULL";
					} else {
						$sql .= ", projectid='".$projectID."'";
					}
					$sql .= " WHERE id='$id'";
					$mysql->sqlordie($sql);
				} else {
					$rb = $res ->fetch_assoc();
					$id = $rb['id'];
					if ($rb['projectid']) {
						$sql = "UPDATE resource_blocks SET status='4' WHERE id='$id'";
						$mysql->sqlordie($sql);
					}

				}
			} else {
				if ($status<5) {
					$sql = "INSERT INTO resource_blocks (userid,projectid,daypart,status,datestamp) VALUES ('$user',";
					if ($projectID==0) {
						$sql .= "NULL";
					} else {
						$sql .= "'".$projectID."'";
					}
					$sql .=",'" .$dayblock['block'] ."','$status','$datestamp')";
					$mysql->sqlordie($sql);
				}
			}
		}

	}

}

$jsonSettings = json_encode($postingList);

// output correct header
$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ?
  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

echo $jsonSettings;
//echo $sql."<br />\n";

?>