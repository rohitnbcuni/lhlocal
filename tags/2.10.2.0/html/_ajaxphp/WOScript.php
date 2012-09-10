<?PHP
	include('../_inc/config.inc');
	
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
	echo "<center> <b> New Workorder Flow DB Script  <b></center> <br>";	
	$workorder_all = "SELECT * FROM `workorders` where creation_date >='2009-12-31 23:59:00'";
	$workorder_list = $mysql->query($workorder_all);
		echo "<br><center> <b> Total WO Workorder ".$workorder_list->num_rows." <b></center>";	
	if(@$workorder_list->num_rows > 0) {	
		echo "<br>List of Workorder : ";
		echo "<br>----------------------------------------------------------<br>";
		while($row = @$workorder_list->fetch_assoc()) {
		
		$workorder_id = $row['id'];

		$workorderCustom0InsertSql = "INSERT INTO `workorder_custom_fields` (`field_key` , `field_id`,	`workorder_id`) VALUES ('REQ_TYPE', '3', '".$workorder_id."')";
		$mysql->query($workorderCustom0InsertSql);

		$workorderCustom1InsertSql = "INSERT INTO `workorder_custom_fields` (`field_key` , `field_id`,	`workorder_id`) VALUES ('SITE_NAME', '45', '".$workorder_id."')";
		$mysql->query($workorderCustom1InsertSql);

		$workorderCustom2InsertSql = "INSERT INTO `workorder_custom_fields` (`field_key` , `field_id`,	`workorder_id`) VALUES ('INFRA_TYPE', '30', '".$workorder_id."')";
		$mysql->query($workorderCustom2InsertSql);

		$workorderCustom3InsertSql = "INSERT INTO `workorder_custom_fields` (`field_key` , `field_id`,	`workorder_id`) VALUES ('CRITICAL', '14', '".$workorder_id."')";
		$mysql->query($workorderCustom3InsertSql);
	
		echo "<br> All Custom Feilds inserted for Workorder - ".$workorder_id;

		}
	}	
?>

  	 