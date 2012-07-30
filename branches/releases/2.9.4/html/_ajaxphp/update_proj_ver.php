<?PHP
	include('../_inc/config.inc');  
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);  
	$OP = $mysql->real_escape_string(@$_REQUEST['OP']);	
	$proj_id = $mysql->real_escape_string(@$_REQUEST['proj_id']);
	$versionID = $mysql->real_escape_string(@$_REQUEST['versionID']);
	$versionName = trim($mysql->real_escape_string(@$_REQUEST['versionName']));

	$versionDeletedStatus = $mysql->real_escape_string(@$_REQUEST['versionDeletedStatus']);
	$versionActiveStatus = $mysql->real_escape_string(@$_REQUEST['versionActiveStatus']);
	
		if($OP == 'ADD')
		{	
			$sql ="SELECT version_name FROM `qa_project_version` where `project_id` = '$proj_id' AND  version_name ='$versionName' ";
			$checkResult = $mysql->query($sql);
			if($checkResult->num_rows == 0){
	      	$insert_version = "INSERT into `qa_project_version` (`project_id` ,`version_name`) values ('".$proj_id."','".$versionName."') ";
			$mysql->query($insert_version);
			}else{
				echo "exist";
			}
		}
		else if($OP == 'UPDATE')
		{
			$update_version = "UPDATE `qa_project_version` SET `version_name`='".$versionName."',`active`='". $versionActiveStatus."',`deleted`='". $versionDeletedStatus."' where `id`='" . $versionID ."'";
			$mysql->query($update_version);
		}
	

?>