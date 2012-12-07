<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$status_id = $mysql->real_escape_string($_GET['status_id']);
	$responseType= $_GET['responseType'] ;
	$html = '<option value="-1">-Select-</option>';
	
	$wo_status_query = "SELECT * FROM `lnk_workorder_status_types` WHERE `active`='1' ORDER BY `name`";
	$wo_status_result = $mysql->sqlordie($wo_status_query);
	

	$displayStatusArray = array();

	//1-Closed, 3-Fixed,4-On Hold,5-Need More Info,6-New,7-In Progress,10-Feedback Provided,11-Rejected,12-Reopened
	if($status_id=='1')
	{
		// For Closed status 
		$displayStatusArray = array('1'=> '1','12'=> '12');
	}
	if($status_id=='3')
	{
		// For Fixed status 
		$displayStatusArray = array('3'=> '3','1'=> '1','11'=>'11');
	}
	if($status_id=='4')
	{
		// For Hold status 
		$displayStatusArray = array('4'=> '4','5'=> '5','7'=> '7');
	}
	if($status_id=='5')
	{
		// For Need More Info status 
		$displayStatusArray = array('5'=> '5','10'=> '10','4'=>'4');
	}
	if($status_id=='6')
	{
		// For New status 
		$displayStatusArray = array('4'=> '4','5'=> '5','6'=> '6','7'=>'7');
	}
	if($status_id=='7')
	{
		// For In Progress
		$displayStatusArray = array('3'=> '3','4'=> '4','5'=> '5','7'=>'7');
	}
	if($status_id=='10')
	{
		// For Feedback Provided status 
		$displayStatusArray = array('5'=> '5','7'=> '7','10'=>'10');
	}
	if($status_id=='11')
	{
		// For Rejected status 
		$displayStatusArray = array('4'=>'4','5'=> '5','7'=> '7','11'=>'11');
	}
	if($status_id=='12')
	{
		// For Reopened status 
		$displayStatusArray = array('4'=> '4','5'=> '5','7'=>'7','12'=>'12');
	}
	$statusList = Array();
	if($_SESSION['login_status'] == 'client'){
		while($row = $wo_status_result->fetch_assoc()) {
			if($row['id'] == $status_id)
			{
				$html .= '<option value="' .$row['id'] .'">' .$row['name'] .'</option>';
				array_push($statusList,$row);
			}
		}
	}else if($wo_status_result->num_rows > 0) {
		while($row = $wo_status_result->fetch_assoc()) {
			if(array_key_exists($row['id'], $displayStatusArray))
			{
				$html .= '<option value="' .$row['id'] .'">' .$row['name'] .'</option>';
				array_push($statusList,$row);
			}
		}
	}
	
	if($responseType=='json')
	{
		 $jsonSettings = json_encode($statusList);

		  // output correct header
		  $isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ?
		  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
		  ($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');
  	
		  echo $jsonSettings;
	}
	else
	{
		echo $html;
	}
?>