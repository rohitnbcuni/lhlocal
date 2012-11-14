<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$project_id = $mysql->real_escape_string(@$_GET['project_id']);
	$unarchive = $mysql->real_escape_string(@$_GET['unarchive']);
	
	/*if($unarchive == 0) {
		$set_archive_query = "UPDATE `projects` SET `archived`='1', rp_permission='0', wo_permission='0' WHERE `id`='$project_id'";
		@$mysql->query($set_archive_query);
	} else if($unarchive == 1) {
		$set_archive_query = "UPDATE `projects` SET `archived`='0', rp_permission='1', wo_permission='1' WHERE `id`='$project_id'";
		@$mysql->query($set_archive_query);
	}*/
	
	
	if($unarchive == 0) {
		$set_archive_query = "UPDATE `projects` SET `archived`='1', rp_permission='0', wo_permission='0' WHERE `id`='$project_id'";
		@$mysql->sqlordie($set_archive_query);
	} else if($unarchive == 1) {
		$un_archive_query = "SELECT id FROM `projects` WHERE `clone_project_id` = '$project_id'";
		$project_list = $mysql->sqlordie($un_archive_query);
		if($project_list->num_rows > 0) {
			$project_new_list = $project_list->fetch_assoc();
			$project_new_id = $project_new_list['id'];
			/*$set_archive_query = "UPDATE `projects` SET `deleted`='1', rp_permission='0', wo_permission='0' WHERE `id`='$project_id'";
			@$mysql->query($set_archive_query);*/
			$un_archive_2012_query = "UPDATE `projects` SET `archived`='0', `deleted`='0',rp_permission='1', wo_permission='1' WHERE `id` = '$project_new_id'";
			@$mysql->sqlordie($un_archive_2012_query);
			echo $project_new_list['id'];
		}else{
			$set_archive_query = "UPDATE `projects` SET `archived`='0', rp_permission='1', wo_permission='1' WHERE `id`='$project_id'";
			@$mysql->sqlordie($set_archive_query);
			echo $project_id;
		}
	}
?>