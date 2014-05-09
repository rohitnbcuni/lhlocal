<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	include('../_ajaxphp/rally_function.php');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;
	
	$defect_id = $mysql->real_escape_string($_GET['defectId']);
	$feildKEY = $mysql->real_escape_string($_GET['feildKEY']);
	$feildID = $mysql->real_escape_string($_GET['feildID']);

	if($feildKEY == 'QA_CATEGORY')
	{
		$updateColumn = " `category`='".$feildID."' ";
	}
	else if($feildKEY = 'QA_SEVERITY')
	{
		$updateColumn = " `severity`='".$feildID."' ";
	}
	
	$update_assigned = "UPDATE `qa_defects` SET $updateColumn WHERE `id`= ? ";
	@$mysql->sqlprepare($update_assigned,array($defect_id));
	
	/********************Email new Change*****************/

	$select_qa = "SELECT * FROM `qa_defects` WHERE `id`= ? ";
	$qa_res = $mysql->sqlprepare($select_qa,array($defect_id));
	$qa_row = $qa_res->fetch_assoc();
	if($feildKEY = 'QA_SEVERITY'){
		$rally_array = array();
		$rally_array['title'] = $qa_row['title'];
		$rally_array['desc'] = $qa_row['body'];
		$rally_array['status'] = $qa_row['status'];
		$rally_array['severity'] = $feildID;
		$rally_array['project_id'] = $qa_row['project_id'];
		$rally_array['detected_by'] = $qa_row['detected_by'];
		
		setNewRallyDefect($qa_row['project_id'], $defect_id,$rally_array );
	}
	
	


	$qa_custom_data = $mysql->sqlordie("SELECT * FROM `lnk_custom_fields_value` where field_id = '".$feildID."' ");
	
	$row = $qa_custom_data->fetch_assoc();
	
	echo $row['field_name'];   

?>