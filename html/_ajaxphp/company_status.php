<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$companyID = $mysql->real_escape_string($_GET['company_id']);
	if($companyID == '0'){
		$sql = "";
	}else{
		$sql = " AND company='$companyID'";
	}
	if('budjet' == $_GET['type']){

		$finance_total = 0;
		$projectResult = $mysql->sqlordie("SELECT * FROM `projects` WHERE `archived`='0' AND `active`='1' AND `deleted`='0' $sql");

		while($projects = $projectResult->fetch_assoc()) {
			$select_budget = "select * from project_budget where project_id= ?";
			$budget_result = $mysql->sqlprepare($select_budget, array($row['id']));
			if($budget_result->num_rows == 1){
				$result_set = $budget_result->fetch_assoc();
				$finance_total += $result_set['total_budget'];
			}else if($budget_result->num_rows == 0){
				$finResult = $mysql->sqlprepare("SELECT * FROM `project_phase_finance` WHERE `active`='1' AND `deleted`='0' AND project_id= ?", array( $projects['id']));
				while($fin = $finResult->fetch_assoc()) {
					$finance_total += ($fin['rate'] * $fin['hours']);
				}
			}
			
		}
		echo number_format($finance_total);
	}else if('status' == $_GET['type']){
		$projectCount = 0;
		$projectResult = $mysql->sqlordie("SELECT count(`id`) as total FROM `projects` WHERE `archived`='0' AND `active`='1' AND `deleted`='0' $sql");
		if($projectResult){
			$count = $projectResult->fetch_assoc();
			$projectCount = $count['total'];
		}
		echo $projectCount;
	}

?>