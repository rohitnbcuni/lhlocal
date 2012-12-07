<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	if(isset($_GET['date'])) {
		$date=@$_GET['date'];
	} else {
		$date='';
	}
	if(isset($_REQUEST['program_type']) && !empty($_REQUEST['program_type'])){
		$program_filter = " program = '".$_REQUEST['program_type']."' AND ";
	} else {
		$program_filter = '';
	}
	if(isset($_GET['part'])) {
		$part=@$_GET['part'];
	} else {
		$part='';
	}
	$character = $_GET['showUser'];
	$role = $_GET['role'];

	if(!empty($_REQUEST['role']) || !empty($_REQUEST['program_type'] )){
		$character = 'all';
	}

	if('all' == strtolower($character)){
		$charLimit = "";
	}else{
		$charLimit = " AND u.`last_name` like '$character%' ";
	}
	

	if($role == ''){
		$sql_user = "";
	}else{
		$sql_user = " AND u.`user_title`='$role' ";
	}

	if(!empty($date)) {
		$time=time();
		if($date!='')
		{
			$t=strtotime($date);
			if($t) $time=$t;
		}
		$d=getdate($time);
		$quarter=floor($d['yday']/91.25); //starts at zero
		$start=strtotime($d['year'].'-01-01');

		$qstart=date('Y-m-d',strtotime('+'.round($quarter*91.25).' day',$start));
		$qend=date('Y-m-d',strtotime('+'.round((($quarter+1)*91.25)-1).' day',$start));

		$start=strtotime($d['year'].'-'.$d['mon'].'-'.$d['mday']);
		$wstart=date('Y-m-d',strtotime('-'.$d['wday'].' day',$start));
		$wend=date('Y-m-d',strtotime('+'.(6-$d['wday']).' day',$start));

		$ystart=$d['year'].'-01-01';
		$yend=$d['year'].'-12-31';
		
		$y_res = $mysql->sqlordie("SELECT COUNT(rb.Daypart), COUNT(DISTINCT rb.UserID) FROM resource_blocks rb, users u WHERE $program_filter u.id=rb.userid " . $charLimit . $sql_user . " AND rb.Datestamp>='$ystart' AND rb.Datestamp<='$yend' AND Status!=0");
		$q_res = $mysql->sqlordie("SELECT COUNT(rb.Daypart), COUNT(DISTINCT rb.UserID) FROM resource_blocks rb, users u WHERE $program_filter u.id=rb.userid " . $charLimit . $sql_user . " AND rb.Datestamp>='$qstart' AND rb.Datestamp<='$qend' AND Status!=0");
		$w_res = $mysql->sqlordie("SELECT COUNT(rb.Daypart), COUNT(DISTINCT rb.UserID) FROM resource_blocks rb, users u WHERE $program_filter u.id=rb.userid " . $charLimit . $sql_user . " AND rb.Datestamp>='$wstart' AND rb.Datestamp<='$wend' AND Status!=0");
		
		//list($y_booked,$y_uid)=$this->db->query("SELECT COUNT(Daypart), COUNT(DISTINCT UserID) FROM resource_block WHERE Datestamp>='$ystart' AND Datestamp<='$yend' AND Status!=0")->fetch(PDO::FETCH_NUM);
		//list($q_booked,$q_uid)=$this->db->query("SELECT COUNT(Daypart), COUNT(DISTINCT UserID) FROM resource_block WHERE Datestamp>='$qstart' AND Datestamp<='$qend' AND Status!=0")->fetch(PDO::FETCH_NUM);
		//list($w_booked,$w_uid)=$this->db->query("SELECT COUNT(Daypart), COUNT(DISTINCT UserID) FROM resource_block WHERE Datestamp>='$wstart' AND Datestamp<='$wend' AND Status!=0")->fetch(PDO::FETCH_NUM);
		//echo $mysql->error;
		list($y_booked,$y_uid)=$y_res->fetch_row();
		list($q_booked,$q_uid)=$q_res->fetch_row();
		list($w_booked,$w_uid)=$w_res->fetch_row();
		
		
		
		$w_total=$w_uid*4*5;
		$y_total=$y_uid*4*365;
		$q_total=floor($q_uid*4*91.25);
		
		if($w_total > 0) {
			$w_load=sprintf('%01.2f',($w_booked/$w_total)*100);
		} else {
			$w_load=0;
		}
		if($w_total > 0) {
			$q_load=sprintf('%01.2f',($q_booked/$q_total)*100);
		} else {
			$q_load=0;
		}
		if($w_total > 0) {
			$y_load=sprintf('%01.2f',($y_booked/$y_total)*100);
		} else {
			$y_load=0;
		}
		$half_load=array();
		
		for($i=0;$i<27;$i++)
		{
			$query="SELECT COUNT(rb.Daypart), DATE(DATE_SUB(NOW() ,INTERVAL ".$i." WEEK)), COUNT(DISTINCT rb.UserID) 
			FROM resource_blocks rb, users u 
			WHERE $program_filter u.id=rb.userid " . $charLimit . $sql_user . " AND rb.Datestamp <= DATE_SUB( NOW( ) , INTERVAL ".$i." WEEK )
			AND rb.Datestamp > DATE_SUB( NOW( ) , INTERVAL ".($i+1)." WEEK )";
			$hlf = $mysql->sqlordie($query);
			$row=$hlf->fetch_row();
			if($row[2]!=0)
			{
				$half_load[strtotime($row[1])]=array(100*($row[0]/($row[2]*5*4)),$row[1]);
			}
			else
			{
				$half_load[strtotime($row[1])]=array(0,$row[1]);;
			}
		}
		ksort($half_load); 
		switch($part) {
			case 'year': {
				echo $y_load;
				break;
			}
			case 'quarter': {
				echo $q_load;
				break;
			}
			case 'week': {
				echo $w_load;
				break;
			}
			default: {
				echo $w_load ."_" .$q_load ."_" .$y_load;
				break;
			}
		}
	} else {
		echo '';
	}
?>