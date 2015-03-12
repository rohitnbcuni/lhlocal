<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	if(isset($_SESSION['user_id'])) {

		$workorderList =explode(",", $mysql->real_escape_string($_GET['id']));
		$woStr = '';
		$flag = 0;
		foreach($workorderList as $wo)
		{ 
			if(!empty($wo))
			{
				if($flag==0)
					$woStr = "'".$wo."'";
				else
					$woStr = $woStr.",'".$wo."'";
				$flag=1;
			}

		} 

		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		//$wo_id = $mysql->real_escape_string($_GET['id']);
		
		$wo_before_archive = "SELECT `id`,`status`,`assigned_to` from `workorders` where `id` in ($woStr)";
		$wo_status_res = $mysql->sqlordie($wo_before_archive);
		if($wo_status_res->num_rows == 1) {
			while($row = $wo_status_res->fetch_assoc()) {
				if($row['status'] != '1'){
					$audit_insert_query = "INSERT INTO  `workorder_audit` (`id`,`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('','".$row['id']."','3','".$_SESSION['user_id']."','".$row['assigned_to']."','1',NOW())";
					//echo "qry=".$audit_insert_query;
					$mysql->sqlordie($audit_insert_query);
				}
			}
		}
		$archive_query = "UPDATE `workorders` SET `archived`='1',`status`='1',`closed_date`=NOW() WHERE `id` in  ($woStr)"; 

		@$mysql->sqlordie($archive_query);
	}
?>