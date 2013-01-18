<?PHP
	include('/var/www/lighthouse-uxd/lighthouse/html/_inc/config.inc');
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$selectSql = 'SELECT id, IFNULL(project_status, "0") AS Status FROM projects where archived="0"';
	$selectResult = $mysql->query($selectSql);
	if($selectResult->num_rows > 0) {
		while($selectRow = $selectResult->fetch_assoc()){
			if($selectRow['Status'] == "0"){
				$updateSql = 'UPDATE `projects` SET `project_status`="1" WHERE `id`="' . $selectRow['id'] . '"';
				$mysql->query($updateSql);
				$insertSql = 'INSERT INTO `project_status` (`project_id`, `status_id`, `created_user`, `created_date`) VALUES ("' . $selectRow['id'] . '", "1", "83", NOW())';
				$mysql->query($insertSql);
			}
		}
	}

	$selectSql = 'SELECT id, IFNULL(project_status, "0") AS Status FROM projects where archived="1"';
	$selectResult = $mysql->query($selectSql);
	if($selectResult->num_rows > 0) {
		while($selectRow = $selectResult->fetch_assoc()){
			if($selectRow['Status'] == "0"){
				$updateSql = 'UPDATE `projects` SET `project_status`="6" WHERE `id`="' . $selectRow['id'] . '"';
				$mysql->query($updateSql);
				$insertSql = 'INSERT INTO `project_status` (`project_id`, `status_id`, `created_user`, `created_date`) VALUES ("' . $selectRow['id'] . '", "6", "83", NOW())';
				$mysql->query($insertSql);
			}
		}
	}
?>
