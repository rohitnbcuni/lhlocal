<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	include('../_ajaxphp/sendEmail.php');

	if(isset($_SESSION['user_id'])) {
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

		$workorderList =explode(",", @$_GET['id']);
		$woStr = '';
		$flag = 0;
		$woStatus = '6'; // Status - New 
		foreach($workorderList as $wo)
		{ 
			if(!empty($wo))
			{
				$archive_query = "UPDATE `workorders` SET `active`='1' WHERE `id` = ?"; 
				@$mysql->sqlprepare($archive_query,array($wo));

				$draftWO_query = "select * from `workorders` WHERE `id` =?";
				
				$draftWORes = @$mysql->sqlprepare($draftWO_query,array($wo));
				if($draftWORes->num_rows > 0) {
					$draftRow = $draftWORes->fetch_assoc();
					insertWorkorderAudit($mysql,$draftRow['id'], '1',$draftRow['requested_by'] ,$draftRow['assigned_to'],$woStatus);
					sendEmail_newRequest($mysql, $draftRow);
				}
			}
		} 		
	}

	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->sqlordie($insert_custom_feild);
	}
?>
