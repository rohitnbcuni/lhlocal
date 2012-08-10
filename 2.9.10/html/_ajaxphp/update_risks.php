<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$type = $_GET['type'];
	$riskId = $_GET['riskId'];
	$value = $_GET['value'];
	$update = '';
	if($type == 'archive'){
		$update = " archived='1' ";
	}else if($type == 'active'){
		if($value == '1'){
			$update = " active='1', closed_date=null ";
		}else{
			$update = " active='0', closed_date=CURRENT_TIMESTAMP ";
		}
	}
	$updateSql = "UPDATE project_risks SET $update WHERE id='$riskId'";

	$mysql->query($updateSql);
	if($type == 'active' && $value == '0'){
		$closedDate = '';
		$riskResult = $mysql->query("SELECT date_format(closed_date, '%m-%d-%Y') AS closed_date FROM project_risks WHERE id='$riskId'");

		if($riskResult){
			$risk = $riskResult->fetch_assoc();
			$closedDate = $risk['closed_date'];
		}
		echo $closedDate;
	}

?>