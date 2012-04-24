<?PHP
	include('../_inc/config.inc');
	
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	

	$workorder_all = "SELECT * FROM `workorders` where project_id in (select id from projects where year='2009') and status not in (1,3) and archived = '0'";
	$workorder_list = $mysql->query($workorder_all);

	if(@$workorder_list->num_rows > 0) {
		echo "<center> <b> UPDATE WORK ORDERS Count -- <b></center>".@$workorder_list->num_rows ;
		echo "<br>----------------------------------------------------------<br>";
		while($row = $workorder_list->fetch_assoc()) {
			
			$newProjectID = '';
			$new_proj_query = "select id from projects where clone_project_id = '".$row['project_id']."'";	
			$new_Proj_rec = $mysql->query($new_proj_query);

			if($row1 = $new_Proj_rec->fetch_assoc())
			{
				$newProjectID=$row1['id'];
			}
			
			$workorder_id = $row['id'];

			if($newProjectID!='' )
			{			
				$updateWOQry = "UPDATE workorders set project_id = '".$newProjectID."' WHERE ID = '".$workorder_id."'";
				$statusofUpdate = $mysql->query($updateWOQry);

				echo "<br>work Order ID : ". $workorder_id;			
				echo " | New Project ID : ". $newProjectID;
				echo " | Old Project ID : ". $row['project_id'];
				echo "<br> Query - ".$updateWOQry;
				echo " --  statusofUpdate - ".$statusofUpdate;
			}

		}
	}
	
?>