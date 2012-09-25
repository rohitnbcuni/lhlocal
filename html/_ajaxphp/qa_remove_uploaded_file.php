<?PHP
	include('../_inc/config.inc');
	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;
	$id = $mysql->real_escape_string(@$_GET['id']);
	
	$getEntry = "SELECT * FROM `qa_files` WHERE `id`= ? LIMIT 1";
	$result = $mysql->sqlprepare($getEntry,array($id));
	
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$dir = $_SERVER['DOCUMENT_ROOT']  ."/qafiles/" .$row['directory'] ."/" .$row['file_name'];
		
		unlink($dir);
		
		if(!is_file($dir)) {
			$remove_entry = "DELETE FROM `qa_files` WHERE `id`= ? ";
			$mysql->sqlprepare($remove_entry,array($id));
		}
	}
?>