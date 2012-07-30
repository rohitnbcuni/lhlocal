<?PHP
	session_start();
	include('../_inc/config.inc');
	include('../_ajaxphp/sendEmail.php');
	if(isset($_SESSION['user_id'])) {
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		
		$wo_id = $mysql->real_escape_string($_GET['id']);
		$assigned_id = $mysql->real_escape_string($_GET['user_id']);
		
		$select_wo_old = "SELECT * FROM `workorders` WHERE `id`='" .$wo_id ."'";
		$wo_old_res = $mysql->query($select_wo_old);
		$wo_old_row = $wo_old_res->fetch_assoc();	 
		
		if($wo_old_row['assigned_to'] != $assigned_id) {
			$assigned_query = ",`assigned_date`=NOW(),`status`='6'";
		} else {
			$assigned_query = "";
		}
		
		$update_assigned = "UPDATE `workorders` SET `assigned_to`='$assigned_id'$assigned_query WHERE `id`='$wo_id'";
		@$mysql->query($update_assigned);
		
		/********************Email new Change*****************/
		
		$select_wo = "SELECT * FROM `workorders` WHERE `id`='" .$wo_id ."'";
		$wo_res = $mysql->query($select_wo);
		$wo_row = $wo_res->fetch_assoc();

		insertWorkorderAudit($mysql,$wo_id, '2', $_SESSION['user_id'],$wo_row['assigned_to'],$wo_row['status']);
		sendEmail_assignedTO($mysql,$wo_row);

	}

	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->query($insert_custom_feild);
	}
?>