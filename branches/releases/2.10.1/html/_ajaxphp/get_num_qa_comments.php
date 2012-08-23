<?PHP
	include('../_inc/config.inc');
	
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	if(isset($_GET['defect_id'])) {
		$defect_id = $mysql->real_escape_string($_GET['defect_id']);
		
		$query = "SELECT COUNT(`id`) as total FROM `qa_comments` WHERE `defect_id`='$defect_id' AND `active`='1' AND `deleted`='0'";
		$total_res = $mysql->query($query);
		$total_row = $total_res->fetch_assoc();
		
		echo $total_row['total'];
	} else {
		echo "0";
	}
?>