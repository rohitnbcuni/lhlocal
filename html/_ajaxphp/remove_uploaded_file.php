<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$id = $mysql->real_escape_string(@$_GET['id']);
	
	$getEntry = "SELECT * FROM `workorder_files` WHERE `id`='$id' LIMIT 1";
	$result = $mysql->sqlordie($getEntry);
	
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$dir = $_SERVER['DOCUMENT_ROOT']  ."/files/" .$row['directory'] ."/" .$row['file_name'];
		
		unlink($dir);
		
		if(!is_file($dir)) {
			$remove_entry = "DELETE FROM `workorder_files` WHERE `id`='$id'";
			$mysql->sqlordie($remove_entry);
		}
	}
?>