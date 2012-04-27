<?PHP
	/*
		Control Tower classes parent and child
	*/
	class Events {
		protected static $configuration;
		
		public function setIniFile() {
			$this->configuration = new Zend_Config_Ini(
			    APPPATH . '/config/app.ini', 
			    APPLICATION_ENVIRONMENT
			);
		}
		
		public function getIniFile() {   
			return $this->configuration;
		}
		/*
			Work Orders Setters
		*/
		/*
			Work Orders Getters
		*/
	}
	/**
	 * 
	 * Event display
	 * 
	 *
	 */
	class EventDisplay extends Events {
		public	static $monthname = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
		public	static $monthShortname = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sept',10=>'Oct',11=>'Nov',12=>'Dec');
		public static $yearArray = array(2010,2011,2012,2013,2014,2015);
		public static $page_row=20; //pagination on listview page
		
		/**
		 * 
		 * function wrapper of fetch ALL
		 * @param string $query
		 */
		public function getQuery($query) {
			$db = Zend_Registry::get('db');
			$data = $db->fetchAll($query);
			
			return $data;
		}
		
		/**
		 * 
		 * function wrapper of query
		 * @param string $query
		 */

		public function executeQuery($query) {
			
			$db = Zend_Registry::get('db');
			
			$data = $db->query($query);
			
			return $data;
		}
		
		
		public function getUserOptionHTML($creatorUserId) {
			if(empty($creatorUserId)){
				$creatorUserId = $_SESSION['user_id'];
			}
			$users = self::getQuery(QRY_USERS_LAST_ASC);
			$html = '';
			$html .= '<option value="">--Select User--</option>';
			for($i = 0; $i < sizeof($users); $i++) {
				if($users[$i]['id'] == $creatorUserId) {
					$selected = " selected='selected'";
				} else {
					$selected = "";
				}
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}
				$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
					
					}
			return $html;
		}
		
		public function getCompanyHTML($selected) {
			$html = '';
			//$comp = self::getQuery(QRY_COMPANIES_ASC);
			//defect 5324
			$sql = "SELECT distinct c.id, c.name FROM `companies` c INNER JOIN projects p ON (p.company = c.id) WHERE p.active='1' and p.archived='0' and p.deleted='0'  ORDER BY c.`name` ASC";
			$comp = self::getQuery($sql);
			$html = '';
			$sel = '';
			for($i = 0; $i < sizeof($comp); $i++) {
				if($comp[$i]['id'] == $selected){
					$sel = ' selected ="selected"';
				}else{
					$sel = '';
				}
				$html .= '<option value="' .$comp[$i]['id'] .'" title="'. $comp[$i]['name'] .'" "'.$sel.'" >' . $comp[$i]['name'] .'</option>';
			}
			
			
			return $html;
		}
		
		public function getCCList($wo_id) {
			$cclist = self::getQuery("SELECT `cclist` FROM `workorders` WHERE `id`='$wo_id'");
			
			return '<input type="hidden" name="cclist" id="cclist" value="' .$cclist[0]['cclist'] .'" />';
		}
		public function getUserAssignOptionHTML($client=false) {
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND `company`='2' and `id` = '97' ORDER BY `last_name` ASC");
			$html = '';
			
			//$html .= '<option value="">--Select User--</option>';
			if(!$client) {
				for($i = 0; $i < sizeof($users); $i++) {
					if($users[$i]['id'] == 97) {
						$html .= '<option value="' .$users[$i]['id'] .'">' 
							.$users[$i]['last_name'] 
							.', ' 
							.$users[$i]['first_name'] 
						.'</option>';
					}
				}
			}else{
					$html .= '<option value=""></option>';
			}
			
			return $html;
		}
		
		public function getDailyHours($hour_saved="") {
			if($hour_saved=='00')
			{
				$hour_saved='12';
			}
			$hours_array = array("01","02","03","04","05","06","07","08","09","10","11","12");
			if($hour_saved > 12){
				$hour_saved = $hour_saved - 12;
			}
			//print("hours saved ".$hour_saved);
			$html = '';
			
			$html .= '<option value="">--</option>';
			for($i = 0; $i < sizeof($hours_array); $i++) {
				$selected = "";
				if($hour_saved == $hours_array[$i]){
					$selected = " selected ='selected'";
				}
                $html .= '<option value="' .$hours_array[$i] .'"' .$selected .'>' 
    					.$hours_array[$i]
    				.'</option>';
            }
			
			return $html;
		}
		
		public function getAmPm($sel){
			$ampm = array("AM" => "AM","PM" => "PM");
			$html = '';
			$html .= '<option value="">--</option>';
			foreach($ampm as $k => $v) {
				$selected = "";
				if($sel == $k){
					$selected = " selected ='selected'";
				}
                $html .= '<option value="' .$k .'"' .$selected .'>' 
    					.$v
    				.'</option>';
            }
			
			return $html;
		}
		
	public function getDailyMin($hour_saved="") {
			
			$hours_array = array("00","15","30","45","59");
			
			
			$html .= '<option value="">--</option>';
			for($i = 0; $i < sizeof($hours_array); $i++) {
				$selected = "";
				if($hour_saved == $hours_array[$i]){
					$selected = " SELECTED";
				}
                $html .= '<option value="' .$hours_array[$i] .'"' .$selected .'>' 
    					.$hours_array[$i]
    				.'</option>';
            }
			
			return $html;
		}
		
		public function getEventStatus($event_status=''){
			$html = '';
			$where = '';
			if(!isset($_GET['eId'])){
				$where = " AND id = 1";
			}
			$sql = "SELECT id, name FROM lnk_event_status_types WHERE active ='1' AND deleted = '0' $where";
			
			$status = self::getQuery($sql);
			foreach($status as $v){
				
				$selected = "";
				if($event_status == $v['id']){
					$selected = " selected = 'selected'";
				}
                $html .= '<option value="' .$v['id'] .'"' .$selected .'>' 
    					.$v['name']
    				.'</option>';
            }
			
			return $html;
			
		}
		
	public function getEventStatusList($event_status=''){
			$html = '';
			$where = '';
			
			$sql = "SELECT id, name FROM lnk_event_status_types WHERE active ='1' AND deleted = '0'";
			//echo $sql; 
						
			$status = self::getQuery($sql);
			foreach($status as $v){
				
				$selected = "";
				if($event_status == $v['id']){
					$selected = " selected = 'selected'";
				}
                $html .= '<option value="' .$v['id'] .'"' .$selected .'>' 
    					.$v['name']
    				.'</option>';
            }
			
			return $html;
			
		}
		
	public function getEventTrailStatus($event_status){
			$db = Zend_Registry::get('db');
			$sql = "SELECT id, name FROM lnk_event_audit_trial_types WHERE active ='1' AND deleted = '0' AND name = '$event_status'"; 
			$status = $db->fetchOne($sql);
			return $status;
						
		}
		public function getEventStatusType($event_status){
			$db = Zend_Registry::get('db');
			$sql = "SELECT name FROM lnk_event_status_types WHERE active ='1' AND deleted = '0' AND id = '$event_status'"; 
			$status = $db->fetchOne($sql);
			return $status;
						
		}
		
		public function getLoadTestig($sel){
			$html = '';
			$status = array('1' => "Yes - result successful",'2' => "Yes - result unsuccessful",'3' => "No - Scheduled",'4' => "No - Not scheduled");
			$selected = "";
			foreach($status as $k => $v) {
				
				if($sel == $k){
					$selected = " selected='selected'";
				}else{
					$selected = "";
				}
                $html .= '<option value="' .$k .'"' .$selected .'>' 
    					.$v
    				.'</option>';
            }
			
			return $html;
		}
		
	public function getUserAssignOptionEditHTML($itemId, $client=false) {
			//dps-support@nbcuni.com
			//dps-hosting@nbcuni.com
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND id IN (1328,1206)   ORDER BY `last_name` ASC");
			//$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND id IN (1456,1734,1932)   ORDER BY `last_name` ASC");
			$html = '';
			
			if(!$client) {
				//$html .= '<option value="">--Select User--</option>';
			}
			for($i = 0; $i < sizeof($users); $i++) {
				if($users[$i]['id'] == $itemId) {
					$selected = " selected ='selected'";
				} else {
					$selected = "";
				}
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}
				if($client) {
					if(!empty($selected)) {
						$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
					}
				} else {
					$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
				}
				
			}
			
			return $html;
		}
		
	public function getUserAssignOptionOpersionEditHTML($itemId, $client=false, $company) {
			
			//echo "SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND id IN ($company)   ORDER BY `last_name` ASC" ; die;
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND company IN ($company)   ORDER BY `last_name` ASC");
			$html = '';
			
			if(!$client) {
				$html .= '<option value="">--Select User--</option>';
			}
			for($i = 0; $i < sizeof($users); $i++) {
				if($users[$i]['id'] == $itemId) {
					$selected = " selected ='selected'";
				} else {
					$selected = "";
				}
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}
				if($client) {
					if(!empty($selected)) {
						$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
					}
				} else {
					$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
				}
				
			}
			
			return $html;
		}
		
		
	public function getUserOptionEditHTML($itemId='', $client=false) {
			//$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0'  AND id IN (1456,1734,1932)  ORDER BY `last_name` ASC");	
			$users = self::getQuery(QRY_USERS_LAST_ASC);
			$html = '';
			
			if($itemId == "") {
				$itemId = $_SESSION['user_id'];
			}
			
			if(!$client) {
				$html .= '<option value="">--Select User--</option>';
			}
			for($i = 0; $i < sizeof($users); $i++) {
				if($users[$i]['id'] == $itemId) {
					$selected = " SELECTED";
				} else {
					$selected = "";
				}
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}
				
				if($client) {
					if(!empty($selected)) {
						$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
					}
				} else {
					$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
				}
				
			}
			
			return $html;
		}
		
		public function getRequestorInfo($userid){
			$query = "SELECT id, user_name, email, CONCAT_WS(' ',last_name,first_name) as full_name,  user_img, phone_office FROM  `users` WHERE `id` = '" .$userid."'";
			$rs = self::getQuery($query);
			return $rs;
		}
		
	public function getCompanyListHTML() {
			$html = '';
			$sql = "SELECT distinct c.id, c.name FROM `companies` c INNER JOIN projects p ON (p.company = c.id) WHERE p.active='1' and p.archived='0' and p.deleted='0'  ORDER BY c.`name` ASC";
			/*if($_SESSION['login_status'] == "client") {
				$sql = "SELECT c.id, c.name FROM `companies` c INNER JOIN projects p ON (p.company = c.id and c.id='".$_SESSION['company']."') ORDER BY `name` ASC";
			} else {
				$sql = "SELECT c.id, c.name FROM `companies` c INNER JOIN projects p ON (p.company = c.id) WHERE p.active='1' and p.archived='0' and p.deleted='0'  ORDER BY `name` ASC";
				
			}*/
			$html = self::getQuery($sql);
			//$sql = "SELECT distinct c.* FROM `companies` c, projects p where p.company = c.id ORDER BY `name` ASC";

						
			
			return $html;
		}
	public function getCompanyName($id) {
	$db = Zend_Registry::get('db');
	$sql = "SELECT name FROM `companies` WHERE id='$id'"; 
	$companyName = $db->fetchOne($sql);

	return $companyName;
	}
		
		public function getTimeZone($selectTZ=''){
			$time_zone = array(''=> "Please Select", "EST" =>'Eastern Standard Time', "CST" =>'Central Standard Time', 'MST' =>"Mountain Standard Time", 'PST'=>'Pacific Standard Time');
			//P($status);
			foreach($time_zone as $k => $v) {
				$selected = "";
				
				if($selectTZ == $k){
					$selected = " selected='selected'";
				}else{
					$selected = " ";
				}
                $html .= '<option value="' .$k .'"' .$selected .'>' 
    					.$v
    				.'</option>';
            }
			
			return $html;
		}
		
		public function getEventInfo( $eid, $data){
			$eid = (int) $eid;
			//$eid = (int) $eid;
			$sql = "SELECT $data FROM  events WHERE id= $eid AND active='1' AND archived = '0' AND deleted = '0' LIMIT 1"; 
			$result = self::getQuery($sql);
			return $result;
		}
		
	public function getActiveEvent($eid){
			//$eid = (int) $eid;
			$db = Zend_Registry::get('db');
			$sql = "SELECT count(*) as count FROM  events WHERE id= $eid AND active='1' AND archived = '0' AND deleted = '0' LIMIT 1"; 
			$result =$db->fetchOne($sql);
			return $result;
		}
		
	public function getEventAuditInfo($eid, $data='*'){
			
			//$eid = (int) $eid;
			$db = Zend_Registry::get('db');
			$sql = "SELECT $data FROM  event_audit WHERE event_id = $eid ORDER BY `log_date` DESC LIMIT 1"; 
			$result =$db->fetchOne($sql);
			return $result;
		}
		
		public function updateEventCcList($eid, $cclist){
			$sql  = "UPDATE events set cclist ='$cclist' WHERE id= $eid";
			$result = self::executeQuery($sql);
		}
		
		public function insertEvent($dataArray){
			$db = Zend_Registry::get('db');
			try{
				$db->insert("events", $dataArray);
			}catch(Exception $e){
				echo "add_event".$e->getMessage();
				
			}
			$evenTrial = array();
			$evenTrial2 = array();
			$sql = "SELECT id, name FROM lnk_event_audit_trial_types WHERE active ='1' AND deleted = '0' AND name = 'New'"; 
			$trialStatusId = $db->fetchOne($sql);
			
			$sql = "SELECT id, name FROM lnk_event_audit_trial_types WHERE active ='1' AND deleted = '0' AND name = 'Assigned To'"; 
			$trialAssignedId = $db->fetchOne($sql);
			//Last Insert Id of event table
			$evenTrial['event_id'] = $db->lastInsertId();
			$evenTrial['status'] = $dataArray['status'];
			$evenTrial['audit_id'] = $trialStatusId;
			//$evenTrial['audit_id'] = $this->getEventTrailStatus("New");
			$evenTrial['log_user_id'] = $dataArray['requested_by'];
			$evenTrial['assign_user_id'] = $dataArray['assigned_to'];
			
			$evenTrial2['event_id'] = $db->lastInsertId();
			$evenTrial2['status'] = $dataArray['status'];
			$evenTrial2['audit_id'] = $trialAssignedId;
			//$evenTrial['audit_id'] = $this->getEventTrailStatus("New");
			$evenTrial2['log_user_id'] = $dataArray['requested_by'];
			$evenTrial2['assign_user_id'] = $dataArray['assigned_to'];
			//$evenTrial['assign_user_id'] = date("Y-m-d");
			try{
				$db->insert("event_audit", $evenTrial);
				$db->insert("event_audit", $evenTrial2);
			}catch(Exception $e){
				
				echo "update_event_audit".$e->getMessage();
			}
			
			// $status;
			return $evenTrial['event_id'];
			//insterting data in event audit table
			
			
		}
		
		public function updateEvent($dataArray,$eid, $preEvent,$prevAssigned){
			$db = Zend_Registry::get('db');
			$userId = $_SESSION['user_id'];
			try{
				
				$where[] = 'id =  '.$eid;
				$db->update("events", $dataArray, $where);
			}catch(Exception $e){
				echo "update_event".$e->getMessage();
				
			}
			if($preEvent  != $dataArray['status']){
				$sql = "SELECT id, name FROM lnk_event_audit_trial_types WHERE active ='1' AND deleted = '0' AND name = 'Status Change'"; 
				$trialStatusId = $db->fetchOne($sql);
				
				$evenTrial = array();
				$evenTrial['event_id'] = $eid;
				$evenTrial['status'] = $dataArray['status'];
				$evenTrial['audit_id'] = $trialStatusId;
				$evenTrial['log_user_id'] = $userId;
				$evenTrial['assign_user_id'] = $dataArray['assigned_to'];
				try{
					$db->insert("event_audit", $evenTrial);
				}catch(Exception $e){
					
					echo "event_audit_status".$e->getMessage();
				}
			}
			if($prevAssigned  != $dataArray['assigned_to']){
				$sql = "SELECT id, name FROM lnk_event_audit_trial_types WHERE active ='1' AND deleted = '0' AND name = 'Assigned To'"; 
				$trialStatusId = $db->fetchOne($sql);
				
				$evenTrial = array();
				$evenTrial['event_id'] = $eid;
				$evenTrial['status'] = $dataArray['status'];
				$evenTrial['audit_id'] = $trialStatusId;
				$evenTrial['log_user_id'] = $userId;
				$evenTrial['assign_user_id'] = $dataArray['assigned_to'];
				try{
					$db->insert("event_audit", $evenTrial);
				}catch(Exception $e){
					
					echo "event_audit_assinged_to".$e->getMessage();
				}
			}
			// $status;
			return $eid;
		}
		
		public function updateAffectedBrand($dataArray, $eid){
			$db = Zend_Registry::get('db');
			try{
				
				$where[] = 'id =  '.$eid;
				$db->update("events", $dataArray, $where);
			}catch(Exception $e){
				echo "affected_brand_event".$e->getMessage();
				
			}
		}
		
		public function deleteEvent($dataArray,$eid){
			$db = Zend_Registry::get('db');
			try{
				
				$where[] = 'id =  '.$eid;
				$db->update("events", $dataArray, $where);
				
			}catch(Exception $e){
				echo "event_delete".$e->getMessage();
				
			}
		}
		
		public function deleteEventAudit($ev_eventStatus,$ev_assigned_to , $eventId,$userId ){
				$db = Zend_Registry::get('db');
				$userId = $_SESSION['user_id'];
				$sql = "SELECT id, name FROM lnk_event_audit_trial_types WHERE active ='1' AND deleted = '0' AND name = 'Delete Event'"; 
				$trialStatusId = $db->fetchOne($sql);
				$evenTrial = array();
				$evenTrial['event_id'] = $eventId;
				$evenTrial['status'] = $ev_eventStatus;
				$evenTrial['audit_id'] = $trialStatusId;
				$evenTrial['log_user_id'] = $userId;
				$evenTrial['assign_user_id'] = $ev_assigned_to;
				try{
					$db->insert("event_audit", $evenTrial);
				}catch(Exception $e){
					
					echo "event_audit_delete".$e->getMessage();
				}
		}
		
		public function addComment($data){
			$db = Zend_Registry::get('db');
			try{
				
				//$where[] = 'id =  '.$eid;
				$db->insert("event_comments", $data);
				
			}catch(Exception $e){
				echo "event_comment".$e->getMessage();
				
			}
			return $db->lastInsertId();
		}
		
		public function addCommentAudit($event_id,$auditType, $userId,$ev_assigned_to,$ev_eventStatus){
				$db = Zend_Registry::get('db');
				$userId = $_SESSION['user_id'];
				$sql = "SELECT id, name FROM lnk_event_audit_trial_types WHERE active ='1' AND deleted = '0' AND name = '$auditType'"; 
				$trialStatusId = $db->fetchOne($sql);
				$evenTrial = array();
				$evenTrial['event_id'] = $event_id;
				$evenTrial['status'] = $ev_eventStatus;
				$evenTrial['audit_id'] = $trialStatusId;
				$evenTrial['log_user_id'] = $userId;
				$evenTrial['assign_user_id'] = $ev_assigned_to;
				try{
					$db->insert("event_audit", $evenTrial);
				}catch(Exception $e){
					
					echo "event_comment_audit".$e->getMessage();
				}
		}
		
		public function getComments($eid, $data){
			$db = Zend_Registry::get('db');
			$sql = "SELECT $data  FROM event_comments WHERE active ='1' AND deleted = '0' AND event_id  = '$eid' order by date"; 
			try{
				$rs = $db->fetchAll($sql);
			}catch(Exception $e){
				echo "Fetch_comment".$e->getMessage();	
			}
			return $rs;
		}
		
		public function getTableData($tableName, $data, $where ='1'){
			$db = Zend_Registry::get('db');
			$sql = "SELECT $data  FROM $tableName WHERE $where"; 
			try{
				$rs = $db->fetchRow($sql);
			}catch(Exception $e){
				echo "Fetch_data".$e->getMessage()." ".$sql;	
			}
			return $rs;
		}
		
		
		static function changeTimeZone($date, $timeZone){
			$newDate = '';
			$timeStmp = strtotime($date);
			
			$yr = date("Y", $timeStmp);
			$m = date("m", $timeStmp);
			$d = date("d", $timeStmp);
			$h = date("H", $timeStmp);
			$min = date("i", $timeStmp);
			$sec = date('s',$timeStmp);
			switch($timeZone){
				case'EST':{
					$newDate  = date("Y-m-d H:i:s",mktime($h, $min,$sec, $m, $d,   $yr));
					break;
				}
				case "CST":{
					$newDate  = date("Y-m-d H:i:s",mktime($h+1, $min,$sec, $m, $d,   $yr));
					break;
				}
				case "MST":{
					$newDate  = date("Y-m-d H:i:s",mktime($h+2, $min,$sec, $m, $d,   $yr));
					break;
				}
				case "PST":{
					$newDate  = date("Y-m-d H:i:s",mktime($h+3, $min,$sec, $m, $d,   $yr));
					break;
				}
				default:{
					$newDate = $date;
					break;
				}
			}
			return $newDate;
		}
			
		public function getMonthHTML($selected="") {
			$html = '';
			$sel = '';
			
			$monthname = EventDisplay::$monthname;
			
			if($selected==""):						
				$html .="<option title=\"All\" value=\"All\" selected>All</option>";
			else:
				$html .="<option title=\"All\" value=\"All\">All</option>";
			endif;
			
			for($i = 1; $i <= count($monthname); $i++) :
				if($i == $selected):
					$sel = ' selected ="selected"';
				else:
					$sel="";
				endif;
				$html .= '<option value="' .$i .'" title="'. $monthname[$i] .'" "'.$sel.'" >' . $monthname[$i] .'</option>';
			endfor;
	
			return $html;
	   }
		   
		public function getyearHTML($selected="") {
				$html = '';
				$sel = '';
				
				$db = Zend_Registry::get('db');
				$query= "SELECT max(year(est_end_datetime)) as maxYear FROM `events` where active='1' and deleted='0' LIMIT 0,1";
				$rs = $db->fetchOne($query);
				$maxYear = $rs;
				
				for($startYear =2010; $startYear <=$maxYear; $startYear++) :
					if($startYear == $selected):
						$sel = ' selected ="selected"';
					else:
						$sel="";
					endif;
					$html .= '<option value="' .$startYear .'" title="'. $startYear .'" "'.$sel.'" >' . $startYear .'</option>';
				endfor;
		
				return $html;
		   }
		   
        public function getCalyearHTML($selected="") {
                                $html = '';
                                $sel = '';

                                $db = Zend_Registry::get('db');
                                $query= "SELECT max(year(est_end_datetime)) as maxYear FROM `events` WHERE active='1' AND deleted='0' AND status =(SELECT id FROM lnk_event_status_types WHERE name='Approved') LIMIT 0,1";
                                $rs = $db->fetchOne($query);
                                $maxYear = $rs;
                                $year_array=array();
                                for($startYear =2010; $startYear <=$maxYear; $startYear++) :
                                        if($startYear == $selected):
                                                $sel = ' selected ="selected"';
                                        else:
                                                $sel="";
                                        endif;
                                        $html .= '<option value="' .$startYear .'" title="'. $startYear .'" "'.$sel.'" >' . $startYear .'</option>';
                                        $year_array[$startYear] = $startYear;
                                endfor;
                                if(!array_key_exists($selected,$year_array)):
                                $html .= '<option value="' .$selected .'" title="'. $selected .'" selected >' . $selected .'</option>';
                                endif;
                                return $html;
                   }
		   
		   ///////////////////function modify by manoj
	public function getCalviewyearHTML($selected) {
				$html = '';
				$sel = '';
				
				$db = Zend_Registry::get('db');
				$query= "SELECT max(year(est_end_datetime)) as maxYear FROM `events` WHERE active='1' AND deleted='0' and archived ='0' LIMIT 0,1";
				$rs = $db->fetchOne($query);
				$maxYear = $rs;
											
				for($startYear =2010; $startYear <=$maxYear; $startYear++) :
					if($startYear == $selected):
						$sel = ' selected ="selected"';
					else:
						$sel="";
					endif;
					$html .= '<option value="' .$startYear .'" title="'. $startYear .'" "'.$sel.'" >' . $startYear .'</option>';
				endfor;
		
				return $html;
		   }
		   /////////////////////////////////////////////////
	
		   
		function matching_row($matching_row){
			
			 $result = '';
			 $date= Date("Y-m-d",strtotime($matching_row));
			 $sql = "SELECT id as event_id ,date_format(est_start_datetime,'%Y-%m-%d') as event_start_date ,title as event_title ,est_end_datetime as event_complete_date FROM events WHERE start_date like '%$date%'";  
			 $result = self::getQuery($sql);
			return $result;
			}
			
		function print_tab($match,$array){
			$count = 0;
			foreach($array  as $k => $v){
				if(in_array($match,$array[$k]['EVENT_DAYS'])){
					$count++;
				}
			}
			return $count;
			}
		
		function event_name($match,$array){
			$event_name = array();
			$i = 0;
			foreach($array  as $k => $v){
				if(in_array($match,$array[$k]['EVENT_DAYS'])){
					//in_array($needle, $haystack)
					$event_name[$i]['EVENT_NAME'] = $array[$k]['EVENT_NAME'];
					$event_name[$i]['ST'] = $array[$k]['ST'];
					$event_name[$i]['ET'] = $array[$k]['ET'];
					$event_name[$i]['ID'] = $k;
				$i++;
				}
			}
			//print_r($event_name);
			return $event_name;
			}

	
		function check_event($event_array , $sd, $ed,$year){
			$event_cont = array();
			foreach($event_array  as $k => $v){
				if(($sd == $event_array[$k]['ST']) && ($ed == $event_array[$k]['ET'])){
					//$event_cont[] = '<li><a href=javascript:void(null); id="slider" onclick=slider("'.date("Y-m-d",strtotime($event_array[$k]['ST'])).'","'.date("Y-n",strtotime($event_array[$k]["ST"])).'","'.$event_array[$k]['ID'].'") >'.Util::escapeTitle($event_array[$k]['EVENT_NAME']).'</a></li>';
				if(date("Y-m-d",strtotime($event_array[$k]['ST'])) == date("Y-m-d",strtotime($event_array[$k]['ET']))){
					$event_cont[] = '<li><a title="'.Util::htmlEntityTitle($event_array[$k]['EVENT_NAME']).'"  href=javascript:void(null); id="slider" onclick=slider("'.date("Y-m-d",strtotime($event_array[$k]['ST'])).'","'.$year.'","'.$event_array[$k]['ID'].'","'.$year.'") >'.Util::calTitle($event_array[$k]['EVENT_NAME'],0,20).'</a></li>';
					}else{
				
					$event_cont[] = '<li><a  title="'.Util::htmlEntityTitle($event_array[$k]['EVENT_NAME']).'"  href=javascript:void(null); id="slider" onclick=slider("'.date("Y-m-d",strtotime($event_array[$k]['ST'])).'","'.$year.'","'.$event_array[$k]['ID'].'","'.$year.'") >'.Util::calTitle($event_array[$k]['EVENT_NAME'],0,20).'*</a></li>';
				}
				/*else{		
				if(date("Y",strtotime($event_array[$k]['ST'])) == $year){
				$event_cont[] = '<li><a href=javascript:void(null); id="slider" onclick=slider("'.date("Y-m-d",strtotime($event_array[$k]['ST'])).'","'.date("Y-n",strtotime($event_array[$k]["ST"])).'","'.$event_array[$k]['ID'].'","'.$year.'") >'.Util::calTitle($event_array[$k]['EVENT_NAME']).'*</a></li>';					
				}
				else{
				$event_cont[] = '<li><a href=javascript:void(null); id="slider" onclick=slider("'.date("Y-m-d",strtotime($event_array[$k]['ST'])).'","'.date("Y-n",strtotime($event_array[$k]["ET"])).'","'.$event_array[$k]['ID'].'","'.$year.'") >'.Util::calTitle($event_array[$k]['EVENT_NAME']).'*</a></li>';
					}}}*/
				}
			}
			return implode(" ",$event_cont);
			}
		
		function getYearEvents($year,$company_id){
			
			$event_array = array();
			$html = '';
			//$year = date("Y");
			$sql = "SELECT id, name FROM lnk_event_status_types  WHERE active ='1' AND deleted = '0' AND name LIKE '%Approved%'"; 
			$trialStatusId = self::getQuery($sql);
			$status=$trialStatusId[0][id];
			//$sql = "SELECT id as event_id ,start_date as event_start_date ,title as event_title ,completed_date as event_complete_date FROM EVENTS WHERE start_date BETWEEN '$year-$month-01' AND '$year-$month-31'";
			$query = "SELECT id as event_id, est_start_datetime AS ST, title AS EVENT_NAME, est_end_datetime AS ET,datediff(est_end_datetime,est_start_datetime) as datediff FROM events WHERE ((YEAR(est_start_datetime)='$year') OR (YEAR(est_end_datetime)='$year')) and status= '$status' and active='1' and deleted = '0' and archived ='0'";
			if($company_id!=-1){
			$query.=" and company_id='$company_id'";
			}
			$html = self::getQuery($query);
			//echo $query;
			$count_event_in_month = count($html);
			if($count_event_in_month > 0){
				foreach ($html as $htmls=>$htmlvalue):
					$date_diff_array = array();
					$date_diff= $htmlvalue['datediff'];
					for($i=0;$i <= $date_diff;$i++)
					{
						$date_diff_array[]=date("Y-m-d", strtotime(date("Y-m-d", strtotime($htmlvalue['ST'])) . " +".$i." day"));
						
					}
					$event_array[$htmlvalue['event_id']] =
					 array(
					 "ST" => $htmlvalue['ST'],
					"ET" => $htmlvalue['ET'],
					"EVENT_DAYS" => $date_diff_array,
					"EVENT_NAME" => $htmlvalue['EVENT_NAME'],
					 "ID" => $htmlvalue['event_id']
					 );
					 
					endforeach;
				
			}
			return $event_array;
			}
		
		
		function draw_calendar($month,$year,$event_array){
			$event_start_date_array = array();			
			$event_complete_date_array= array();
			$event_id_array = array();
			$month_tooltip_array=array();
			$month_tooltip_array=array('2','4','6','8','10','12');
			  /* table headings */
			  $headings = array('S','M','T','W','T','F','S');
			  $monthname =array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
			  if($month%4){
			  	$calendar.= '<li class="calendar_month">';
			  }else{
				  $calendar.= '<li class="calendar_month last" style="margin-bottom: 20px;">';
			  }
			  $calendar.= '<div class="calendar_content">
						  <div class="calendar_header">'.$monthname[$month].'</div>
						  <table class="table_calendar"><thead>
						  <tr>
						 								<th>'.implode('</th><th>',$headings).'</th>
													</tr>
												</thead>
												<tbody>';

			  /* days and weeks vars now ... */
			  $running_day = date('w',mktime(0,0,0,$month,1,$year));
			  $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
			  $days_in_pre_month = date('t',mktime(0,0,0,$month-1,1,$year));
			  $days_in_next_month = date('t',mktime(0,0,0,$month+1,1,$year));
			  $days_in_this_week = 1;
			  $day_counter = 0;
			  $dates_array = array();
			  $day_counter2 = array();	
			  /* row for week one */
			  $calendar.= '<tr>';
			  $pre_month_last_week = $days_in_pre_month - ($running_day-1);	
			  $pre_month_next_week = $days_in_next_month - ($running_day-1);	
			  /* print "blank" days until the first of the current week */
			  for($x = 0; $x < $running_day; $x++):
				$calendar.= '<td class="date_disabled">'.$pre_month_last_week .'</td>';
				$pre_month_last_week++;				$days_in_this_week++;
			  endfor;
			$row_counter=1;

				/* keep going with days.... */
			  	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
				 $match=date("Y-m-d",strtotime($year.'-'.$month.'-'.$list_day));
				 $class ='';
				 $single_date_class='';
				 foreach($event_array as $k => $v){
				 	 if(@in_array($match, $event_array[$k]['EVENT_DAYS'])){ 
					 	$day_counter2[$k][$match] = $i+1;
					 	$single_date_class='id="event_date" name="'.$year.'-'.$month.'-'.$list_day.'"  href=javascript:void(null); onclick = slider("'.$year.'-'.$month.'-'.$list_day.'","'.$year.'-'.$month.'","'.$event_array[$k]['ID'].'","'.$year.'")';
					 	if (strtotime($event_array[$k]['ST']) < strtotime(date("Y-m-d H:i:s")) && strtotime($event_array[$k]['ET']) < strtotime(date("Y-m-d H:i:s")))
						 { 
						 if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
					 		if(@in_array($month,$month_tooltip_array)){
					 			$class ='past_events multiple past_left_events_imgg';
					 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}else{
						 		$class ='past_events multiple past_events_imgg';
					 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}}
						 	else{
						 		$class ='';
						 		if(@in_array($month,$month_tooltip_array)){
						 		$class ='past_events multiple past_left_events_img';
						 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 		}
						 		else {					 		
						 		$class ='past_events multiple past_events_img';
						 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}}
						 	
						 if (date("Y-m-d",strtotime($event_array[$k]['ST'])) == date("Y-m-d",strtotime($event_array[$k]['ET']))){
						 	if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
						 		
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class ='past_events past_left_events_imgg';
						 	if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}else{
						 		$class ='past_events past_events_imgg';
					 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}
					 		
						 		
						 	}else{
						 		$class ='';
						 		if(@in_array($month,$month_tooltip_array)){
					 			$class ='past_events multiple past_left_events_img';
						 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}else{
						 		$class ='past_events multiple past_events_img';
					 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}					 		
						 	}
						 	
						 	}
						 	}
						 } 
					 	elseif(strtotime(date("Y-m-d",strtotime($event_array[$k]['ST']))) === strtotime(date("Y-m-d",strtotime($event_array[$k]['ET'])))){
						 	
					 		if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
					 		if(@in_array($month,$month_tooltip_array)){
					 			$class ='upcoming_events  upcoming_left_events_imgg';
					 			if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}else{
						 		$class ='upcoming_events  upcoming_events_imgg';
					 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}
					 		
						 		
						 	}else{
						 		$class ='';
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class='upcoming_events  upcoming_left_events_img';
						 	if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}else{
						 		$class='upcoming_events  upcoming_events_img';
						 	if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}}
						 	
						 	}
						 	if (date("Y-m-d") == $match){
						 	
						 	if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class ='today upcoming_events multiple upcoming_left_events_imgg';
					 		}else{
						 		$class ='today upcoming_events multiple upcoming_events_imgg';
						 	}
						 		
						 	}else{
						 		$class ='';
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class='today upcoming_events multiple upcoming_left_events_img';
						 	}else{
							$class='today upcoming_events multiple upcoming_events_img';						 	
					 		}
						 	
						 	}
						 	}
						 }
						 else if($event_array[$k]['ET'] == $match){
						 	//$class ='upcoming_events multiple upcoming_events_img';
						 if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
						 if(@in_array($month,$month_tooltip_array)){
					 			$class ='upcoming_events multiple upcoming_left_events_imgg';
						 if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}else{
						 		$class ='upcoming_events multiple upcoming_events_imgg';
					 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}
						 		
						 	}else{
						 		$class ='';
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class='upcoming_events multiple upcoming_left_events_img';
						 	if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}else{
						 		$class='upcoming_events multiple upcoming_events_img';
						 	if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}
						 	
						 	}
							 if (date("Y-m-d") == $match){
							 if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
							 if(@in_array($month,$month_tooltip_array)){
					 			$class ='today upcoming_events multiple upcoming_left_events_imgg';
					 		}else{
						 		$class ='today upcoming_events multiple upcoming_events_imgg';
						 	}
						 		
						 	}else{
						 		$class ='';
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class='today upcoming_events multiple upcoming_left_events_img';
					 		}else{
						 		$class='today upcoming_events multiple upcoming_events_img';
						 	}
						 	
						 	}	
							 }
							 }
						 else{
						  if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
						  if(@in_array($month,$month_tooltip_array)){
					 			$class ='upcoming_events multiple upcoming_left_events_imgg';
						  if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}else{
						 		$class ='upcoming_events multiple upcoming_events_imgg';
					 		if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
					 		}
						 		
						 	}else{
						 		$class ='';
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class='upcoming_events multiple upcoming_left_events_img';
						 	if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}else{
						 		$class='upcoming_events multiple upcoming_events_img';
						 	if (date("Y-m-d") == $match){
					 				$class .=' today';
					 			}
						 	}
						 	
						 	}
		 					if (date("Y-m-d") == $match){
						 	//$class ='';
						 	//$class ='today upcoming_events multiple upcoming_events_img';
		 					if(EventDisplay::print_tab($match,$event_array)== '1'){
						 		$class ='';
		 					if(@in_array($month,$month_tooltip_array)){
					 			$class ='today upcoming_events multiple upcoming_left_events_imgg';
					 		}else{
						 		$class ='today upcoming_events multiple upcoming_events_imgg';
						 	}
						 		
						 	}else{
						 		$class ='';
						 	if(@in_array($month,$month_tooltip_array)){
					 			$class='today upcoming_events multiple upcoming_left_events_img';
					 		}else{
						 		$class='today upcoming_events multiple upcoming_events_img';
						 	}
						 	
						 	}
						 	}
						 }
						 $i++;
					 }
				 }
				 

				 $event_name = EventDisplay::event_name($match,$event_array);
				// print_r($event_name);
				 $event_nameas = '';
				 $event_con = array();
				 $res = array();
				 
				   for ($i=0;$i<count($event_name); $i++)
					 { 
						if(date("Y-m-d",strtotime($event_name[$i]['ST']))!=date("Y-m-d",strtotime($event_name[$i]['ET']))):
						if(date("Y-m",strtotime($event_name[$i]['ST']))== date("Y-m",strtotime($event_name[$i]['ET']))):
					 	$key = date("M. d",strtotime($event_name[$i]['ST']))."-".date("M. d, Y",strtotime($event_name[$i]['ET']));
					 	else:
					 	if(date("Y",strtotime($event_name[$i]['ST']))== date("Y",strtotime($event_name[$i]['ET']))):
						$key = date("M. d",strtotime($event_name[$i]['ST']))."-".date("M. d, Y",strtotime($event_name[$i]['ET']));
						else:
					 	$key = date("M. d, Y",strtotime($event_name[$i]['ST']))."-".date("M. d, Y",strtotime($event_name[$i]['ET']));
						endif;
					 	endif;
					 	else:
					 	$key = date("M. d, Y",strtotime($event_name[$i]['ST']));
					 	endif;	
					 	$key = $key."###".$event_name[$i]['ID'];
					 	$res[$key]= EventDisplay::check_event($event_name, $event_name[$i]['ST'], $event_name[$i]['ET'],$year.'-'.$month);
					 }
					
				$res_uni = array_unique($res);
				ksort($res_uni);
				$a_seemore = EventDisplay::print_tab($match,$event_array);
				$match_record='';
				$a_counter='';
				foreach($res_uni as $k => $v):   
					if ($a_counter < 5 ):
					$key= explode('###',$k);
					//if($match_record==$key[0]):
					//$event_nameas .=   $v;
					//else:
					//$event_nameas .= '<br/><font class="bubble_dates" >'.$key[0].'</font>'; 
					$event_nameas .=   $v;
					//endif;
					//$match_record =$key[0];
					endif;
				$a_counter++;	
				endforeach;
				
				if(count($event_name)>0):
				$calendar.= '<td class="'.$class.' date_default "  title=\'<div class="event_bubble"><div class="bubble_header"></div><div class="bubble_content"><div class="bubble_content_header"><div class="bubble_date">'.date("M",strtotime($monthname[$month])).'. '.$list_day.', '.$year.'</div><div class="bubble_events_number" >'.EventDisplay::print_tab($match,$event_array).' events</div></div><div class="bubble_content_copy"><ul class="bubble_list_events"   style="background:url(/_images/dotted_backgroud.png) no-repeat;"><br style="display:block;margin-bottom:14px;"/> '.$event_nameas.'</ul>';
				if ($a_seemore>5)
				{$a_seemore=$a_seemore-5;
					$calendar.='<p class="see_more"><a href="/events/index/listview/date/'.$month.'-'.$list_day.'-'.$year.'">see more('.$a_seemore.')</a></p>';
				}
				$calendar.= '<p class="notes"><i>*Part of a multi-day event</i></p></div></div><div class="bubble_footer"></div>';
				if(@in_array($month,$month_tooltip_array)){
				$calendar.= '<div class="bubble_left_arrow"></div>';
				}
				else{
				$calendar.= '<div class="bubble_arrow"></div>';
				}
				$calendar.= '<a class="bubble_close" href="javascript:void(null);" onclick="hidediv();" >Close</a></div>';

				//<a class="bubble_close" href="javascript:$(\'.bubble_header\').css(\'display\',\'none\');" >Close</a></div>
		        $calendar.="'>";
		        if(strtotime($_GET['edate'])==strtotime($year.'-'.$month.'-'.$list_day)):
		        $calendar.='<div style="position:absolute;margin-left: -114px;margin-top: -120px;"><div class="default_bubble" id="form_successs">
		        <div class="bubble_header"></div>
    			<div class="bubble_content">
    			<p class="sucess_msg">Your event has been submitted for review.</p></div>
    			<div class="bubble_footer"></div>
				</div></div>';	
		        endif;
		          elseif(strtotime($_GET['edate'])==strtotime($year.'-'.$month.'-'.$list_day)):
		        
		        $calendar.= '<td class="'.$class.' date_default " id="form_successid" style="border-style:solid;border-width:2px;border-color:black;position:relative;"';
				//<a class="bubble_close" href="javascript:$(\'.bubble_header\').css(\'display\',\'none\');" >Close</a></div>
		        $calendar.="'>";
		        $calendar.='<div style="position:absolute;margin-left: -114px;margin-top: -120px;"><div class="default_bubble" id="form_successs">
		        <div class="bubble_header"></div>
    			<div class="bubble_content">
    			<p class="sucess_msg">Your event has been submitted for review.</p></div>
    			<div class="bubble_footer"></div>
				</div></div>';
		        $single_date_class='id="event_date" href="javascript:void(null)"';
		        else:
				if (date("Y-m-d") == $match):
				$calendar.='<td class="date_default today">';
				else:
		        $calendar.= '<td class="date_default">';
				endif;
		        endif;	 
		        if($single_date_class==''){$single_date_class="class='newtheme'";}
				  /* add in the day number */		        
				  //$calendar.= '<a href="javascript:void(null);" id="slider" onclick=slider("'.$year.'-'.$month.'-'.$list_day.'","'.$year.'-'.$month.'") >'.$list_day.'</a>';
					$calendar.= '<a  '.$single_date_class.'>'.$list_day.'</a>';
					
				  /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
				  //$calendar.= str_repeat('<p>&nbsp;</p>',2);
				  
				$calendar.= '</td>';
				if($running_day == 6):
				  $calendar.= '</tr>';
				  $row_counter++;
				  if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr>';
				  endif;
				  $running_day = -1;
				  $days_in_this_week = 0;
				endif;
				$days_in_this_week++; $running_day++; $day_counter++;
			  endfor;

			   $totalmissing = 1;	
			  /* finish the rest of the days in the week */
			 if($days_in_this_week < 8):
				for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				  $calendar.= '<td class="date_disabled">'.$totalmissing.'</td>';
				  $totalmissing++;
				endfor;
			 endif;
			 

			  /* final row */
			  $calendar.= '</tr>';
			  if($row_counter<6):
				//$calendar.= '<tr><td class="date_disabled" colspan="7">&nbsp;</td></tr>';
			    $calendar.= '<tr><td class="date_disabled">'.($totalmissing).'</td>
				<td class="date_disabled">'.($totalmissing+1).'</td>
				<td class="date_disabled">'.($totalmissing+2).'</td>
				<td class="date_disabled">'.($totalmissing+3).'</td>
				<td class="date_disabled">'.($totalmissing+4).'</td>
				<td class="date_disabled">'.($totalmissing+5).'</td>
				<td class="date_disabled">'.($totalmissing+6).'</td></tr>';
			endif;
				/* end the table */
			  $calendar.= '</tbody></table></div></li>';
			  
		 	   if($month%4==0)
			   {
			   for($i=$month-3;$i<=$month;$i++){			 
			   $calendar.= '<li style="margin-bottom: 20px;">';
			   $calendar.='  <div id="events_list_container'.$year.'-'.$i.'" class="events_list_calenderviewajax" style="display:none;">';
			   $calendar.=  '<div id="event_specifics'.$year.'-'.$i.'" class="event_specifics"  style="display:none;"></div></div>';
			   $calendar.= '</li>';
			   }}
			  
			  /* all done, return result */
			  return $calendar;
			}
			
		function get_upcoming_event($year,$comp_id)
			{
			if(empty($year)){
			$year= date("Y");
			}
			else{
			$year = $year;
			}						
			$html = '';
			$datetime=date("Y-m-d H:i:s");
			$sql = "SELECT id, name FROM lnk_event_status_types  WHERE active ='1' AND deleted = '0' AND name LIKE '%Approved%'"; 
			$trialStatusId = self::getQuery($sql);
			$status=$trialStatusId[0][id];
			if ($comp_id!='-1'){
			$sql = "SELECT * FROM events WHERE company_id ='$comp_id' and year(est_start_datetime) = '$year' and  status= '$status' and est_start_datetime >= NOW() and active='1' and deleted = '0' and archived ='0' limit 0,3";
			}else{
			$sql = "SELECT * FROM events WHERE year(est_start_datetime) = '$year' and status= '$status' and est_start_datetime >= NOW() and active='1' and deleted = '0' and archived ='0' limit 0,3";
			}
			 $html = self::getQuery($sql);
			 return $html;
			}			
	
			
	   	function calenderviewajax($id){
		   	$html;
		   	$db = Zend_Registry::get('db');
		   	$sql="select * from events where id='$id'";
		   	$html = $db->fetchRow($sql);
		   	return $html;
	   		}
	   
		public static function anitcipatedTraffic(){
			$status = array(1 => "Low (0 - 1,000)",2 => "Medium (1,001 - 10,000)",3 => "High (10,001+)",4 => "Don't Know");
			return $status;
			}
	
		public function getAnticipated($sel){
			$html = '';
			$status = EventDisplay::anitcipatedTraffic();
			$selected = "";
			foreach($status as $k => $v) {
				
				if($sel == $k){
					$selected = " selected='selected'";
				}else{
					$selected = '';
				}
                $html .= '<option value="' .$k .'"' .$selected .'>' 
    					.$v
    				.'</option>';
            }
			return $html;
			}
		 
		function affected_company_list($company){
	   		$db = Zend_Registry::get('db');
		 	$query = "SELECT GROUP_CONCAT(name SEPARATOR ', ') as name from companies WHERE ID IN ($company)";
	   		$result = $db->fetchOne($query);
			return $result;	   	
   	  		}	
			}
?>
