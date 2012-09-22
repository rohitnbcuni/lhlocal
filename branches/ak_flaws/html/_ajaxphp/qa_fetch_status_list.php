<?PHP
	include('../_inc/config.inc');
	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;
	$defectID= $_GET['defectID'] ;
	$qaStatus= $_GET['qaStatus'] ;



	$QRY_STATUS_SELECT ="SELECT id, name FROM `lnk_qa_status_types` WHERE `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC";

	$result = $mysql->sqlordie();	
	$statusList = Array();

	if($result->num_rows > 0) {
		$i = '0';
		$displayStatusArray = array();
		// New-1, In Progress-2, Fixed - 3 , Rejected - 4,  Reopened - 5, Need More Info - 6 ,  Hold - 7 , Closed - 8, Feedback Provided - 10
		if($qaStatus=='1')
		{
			// For New status 
			$displayStatusArray = array('1'=> '1','2'=> '2','6'=>'6','7'=>'7');
		}
		if($qaStatus=='10')
		{
			// For Feedback Provided status 
			$displayStatusArray = array('1'=> '1','2'=> '2','6'=> '6','10'=>'10');
		}
		if($qaStatus=='2')
		{
			// For In Progress status 
			$displayStatusArray = array('1'=> '1','2'=> '2','3'=> '3','6'=> '6','7'=>'7');
		}
		if($qaStatus=='3')
		{
			// For Fixed status 
			$displayStatusArray = array('3'=> '3','8'=> '8','4'=>'4');
		}
		if($qaStatus=='8')
		{
			// For Closed status 
			$displayStatusArray = array('3'=> '3','8'=> '8','5'=>'5');
		}
		if($qaStatus=='7')
		{
			// For Hold status 
			$displayStatusArray = array('7'=> '7','6'=> '6','2'=>'2');
		}
		if($qaStatus=='4')
		{
			// For Rejected status 
			$displayStatusArray = array('4'=> '4','6'=> '6','2'=>'2','7'=>'7');
		}
		if($qaStatus=='5')
		{
			// For Reopened status 
			$displayStatusArray = array('5'=> '5','6'=> '6','2'=>'2','7'=>'7');
		}
		if($qaStatus=='6')
		{
			// For Need More Info status 
			$displayStatusArray = array('6'=> '6','10'=> '10','7'=>'7');
		}

		while($row = $result->fetch_assoc()) {
			if(array_key_exists($row['id'], $displayStatusArray))
			{
				array_push($statusList,$row);
			}
			$i++;
		}

		  $jsonSettings = json_encode($statusList);

		  // output correct header
		  $isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ?
		  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
		  ($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');
  	
		  echo $jsonSettings;

	}
	
?>