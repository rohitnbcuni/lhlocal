<?PHP
	/*
		Control Tower classes parent and child
	*/
	class Quality {
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
	class QaDisplay extends Quality {
		public function getQuery($query,$arr =null) {
			/*Quality::setIniFile();
			
			$config = Quality::getIniFile();
			
			$db = Zend_Db::factory('Pdo_Mysql', array(
				    'host'     => $config->database->params->host,
				    'username' => $config->database->params->username,
				    'password' => $config->database->params->password,
				    'dbname'   => $config->database->params->dbname,
				    'port'   => $config->database->params->dbport
				));
			$db->getConnection();*/
			//get DB connection from registry LH#20736
			$db = Zend_Registry::get('db');
			
			$data = $db->fetchAll($query,$arr);
			
			return $data;
		}

		public function executeQuery($query,$arr =null) {
			/*Quality::setIniFile();
			
			$config = Quality::getIniFile();
			
			$db = Zend_Db::factory('Pdo_Mysql', array(
				    'host'     => $config->database->params->host,
				    'username' => $config->database->params->username,
				    'password' => $config->database->params->password,
				    'dbname'   => $config->database->params->dbname,
				    'port'   => $config->database->params->dbport
				));
			$db->getConnection();*/
			//get DB connection from registry LH#20736
			$db = Zend_Registry::get('db');
			
			$data = $db->query($query,$arr);
			
			return $data;
		}
		
		public function getUserOptionHTML($client=false) {
		/**
		* Ticket No#18735
		* Add where condition
		* 	
		*/
		//$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND `company`='2' ORDER BY `last_name` ASC");
		/* changes for WO 25220:  
		if($_SESSION['login_status'] != "client") {
			$where=" AND `company`='2'";
		 } else {
			$where = "";
		 }
		 $users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' $where  ORDER BY `last_name` ASC");*/
		$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0'  ORDER BY `last_name` ASC");
	
		$html = '';
			if($_SESSION['login_status'] != "client") {
				$html .= '<option value="">--Select User--</option>';
			}
			if(!$client) {
				for($i = 0; $i < sizeof($users); $i++) {
					if($users[$i]['id'] == $_SESSION['user_id']) {
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
					if($_SESSION['login_status'] != "client") {
						$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
					} else {
				//		if($users[$i]['id'] == $_SESSION['user_id']) {
							$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';

						//}
					}
				}
			}
			
			return $html;
		}
		
		public function getPriorityOptionHTML() {
			$priorities = self::getQuery(QRY_WO_PRIORITY_ASC);
			$html = '';
			
			$html .= '<option value="">--Select Priority--</option>';
			for($i = 0; $i < sizeof($priorities); $i++) {
                if($priorities[$i]['name']  != 'High') {
                    if($priorities[$i]['id'] == 1) {
    					$selected = " SELECTED";
    				} else {
    					$selected = "";
    				}
    				$html .= '<option value="' .$priorities[$i]['id'] .'"' .$selected .'>' 
    					.$priorities[$i]['name']
    				.'</option>';
                }
            }
			
			return $html;
		}
		public function getTypesOptionHTML() {
			$html = '<option value="">--Select Type--</option>
				<option value="HTML Update">HTML Update</option>
				<option value="Nav Update">Nav Update</option>
				<option value="Layout Issue">Layout Issue</option>
				<option value="Change Request">Change Request</option>
				<option value="Film Evite">Film Evite</option>';
				
			return $html;
		}
		
		public function getUserOptionEditHTML($itemId='', $client=false) {
			//LH #25220
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0'  ORDER BY `last_name` ASC");
			//$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND `company`='2' ORDER BY `last_name` ASC");
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

		public function getcustomDropDown($fieldKEY,$fieldID = '', $client=false) {

			$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='".$fieldKEY."' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' and cfv.active='1' and cfv.deleted='0' order by field_name";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);
			$html = '';					

			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['field_id'] == $fieldID) {
					$selected = " selected='selected'";
				} else {;
					$selected = "";
				}
				
				if(!$client) {
					$html .= '<option value="' .$fields_list[$i]['field_id'] .'"' .$selected .'>' 
							.$fields_list[$i]['field_name'].'</option>';
				} 
				
			}

			return $html;
		}		

		public function getProjectVersionDropDown($project_id,$qa_version='') {  

			$html = '';					
			$QRY_MASTER_SELECT ="SELECT `id`,`project_id`,`version_name` FROM `qa_project_version` where `project_id` in ( '".$project_id."' ,'0') AND active ='1' AND deleted ='0'  order by project_id ='0' DESC, version_name ASC ";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);

			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['id'] == $qa_version) {
					$selected = " selected='selected'";
				} else {;
					$selected = "";
				}
				
				if(!$client) {
					$html .= '<option value="' .$fields_list[$i]['id'] .'"' .$selected .'>' 
							.$fields_list[$i]['version_name'].'</option>';
				} 					
			}
			return $html;
		}
	public function getProjectProductDropDown($project_id,$qa_version='') {  

			$html = '';					
			$QRY_MASTER_SELECT ="SELECT `id`,`project_id`,`product_name` FROM `qa_project_product` where `project_id` IN  ('".$project_id."','0') AND active='1' AND deleted ='0' order by project_id ='0' DESC, product_name ASC ";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);
			
			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['id'] == $qa_version) {
					$selected = " selected='selected'";
				} else {;
					$selected = "";
				}
				
				if(!$client) {
					$html .= '<option value="' .$fields_list[$i]['id'] .'"' .$selected .'>' 
							.$fields_list[$i]['product_name'].'</option>';
				} 					
			}
			return $html;
		}
	public function getProjectIterationDropDown($project_id,$qa_version='') {  

			$html = '';					
			$QRY_MASTER_SELECT ="SELECT `id`,`project_id`,`iteration_name` FROM `qa_project_iteration` where `project_id` IN ( '".$project_id."','0') AND active='1' AND deleted ='0' order by project_id ='0' DESC, iteration_name ASC";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);
			
			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['id'] == $qa_version) {
					$selected = " selected='selected'";
				} else {;
					$selected = "";
				}
				
				if(!$client) {
					$html .= '<option value="' .$fields_list[$i]['id'] .'"' .$selected .'>' 
							.$fields_list[$i]['iteration_name'].'</option>';
				} 					
			}
			return $html;
		}
		public function getcustom($fieldKEY,$fieldID = '', $client=false) {

			$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='".$fieldKEY."' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' order by sort_order";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);
			$html = '';					

			$html .= '<option value="_blank"></option>';
			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['field_id'] == $fieldID) {
					$selected = " selected='selected'";
				} else {;
					$selected = "";
				}
				
				if(!$client) {
					$html .= '<option value="' .$fields_list[$i]['field_id'] .'"' .$selected .'>' 
							.$fields_list[$i]['field_name'].'</option>';
				} 
				
			}

			return $html;
		}		
		
		public function getProjectOptionEditHTML($itemId,$flag) {
			if($_SESSION['login_status'] == "client") {
				$comp_query = " AND a.`company` = '".$_SESSION['company'] ."'";
				$comp_query = "";
			} else {
				$comp_query = "";
			}
			//Optomize the sql query
			//$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a, `companies` com WHERE a.`company`=com.`id` AND a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1' ORDER BY com.`name`, a.`project_code` ASC");
			$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a INNER JOIN `companies` com ON (a.`company`=com.`id`) WHERE a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1' ORDER BY com.`name`, a.`project_code` ASC");

			$html = '';
			
			$html .= '<option value="">--Select Project--</option>';
			$companyId = false;
			for($i = 0; $i < sizeof($projects); $i++) {
				$perms = self::getQuery("SELECT id FROM `user_project_permissions` WHERE `user_id`='" .$_SESSION['user_id'] ."' AND `project_id`='" .$projects[$i]['id'] ."'");
				
				if(sizeof($perms) > 0) {
					if($projects[$i]['company'] != $companyId) {
						$html .= '<optgroup label="' . $projects[$i]['company_name'] .'" title="' . $projects[$i]['company_name'] .'">';
					}
					if($projects[$i]['id'] == $itemId) {
						$selected = " SELECTED";
						if($flag!=2){
						?>
						<script type="text/javascript">
						updateCClist(<?php echo $itemId;?>);
						</script>
						<?php 
						}
					} else {
						$selected = "";
					}
					$html .= '<option title="'.$projects[$i]['project_code']
						.' - ' 
						.$projects[$i]['project_name']
					.'" value="' .$projects[$i]['id'] .'"' .$selected .'>' 
						.$projects[$i]['project_code']
						.' - ' 
						.$projects[$i]['project_name']
					.'</option>';
					$companyId = $projects[$i]['company'];
					$nxt = $i+1;
					if($projects[$nxt]['company'] != $companyId) {
						$html .= '</optgroup>';
					}
				}
			}			
			return $html;
		}
		
	public function getProjectAllOptionEditHTML($itemId,$flag) {
			if($_SESSION['login_status'] == "client") {
				$comp_query = " AND a.`company` = '".$_SESSION['company'] ."'";
				$comp_query = "";
			} else {
				$comp_query = "";
			}
			//$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a, `companies` com WHERE a.`company`=com.`id`   ORDER BY com.`name`, a.`project_code` ASC");
			$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a INNER JOIN `companies` com ON (a.`company`=com.`id`)  WHERE a.qa_permission ='1' ORDER BY com.`name`, a.`project_code` ASC");
			if(!self::projectInArray($projects, $itemId )){
			
			$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a INNER JOIN `companies` com ON (a.`company`=com.`id`)  WHERE a.qa_permission ='0' ORDER BY com.`name`, a.`project_code` ASC");

			}
			$html = '';
			
			$html .= '<option value="">--Select Project--</option>';
			$companyId = false;
			for($i = 0; $i < sizeof($projects); $i++) {
				$perms = self::getQuery("SELECT * FROM `user_project_permissions` WHERE `user_id`='" .$_SESSION['user_id'] ."' AND `project_id`='" .$projects[$i]['id'] ."'");
				
				if(sizeof($perms) > 0) {
					if($projects[$i]['company'] != $companyId) {
						$html .= '<optgroup label="' . $projects[$i]['company_name'] .'" title="' . $projects[$i]['company_name'] .'">';
					}
					if($projects[$i]['id'] == $itemId) {
						$selected = " SELECTED";
						
					} else {
						$selected = "";
					}
					$html .= '<option title="'.$projects[$i]['project_code']
						.' - ' 
						.$projects[$i]['project_name']
					.'" value="' .$projects[$i]['id'] .'"' .$selected .'>' 
						.$projects[$i]['project_code']
						.' - ' 
						.$projects[$i]['project_name']
					.'</option>';
					$companyId = $projects[$i]['company'];
					$nxt = $i+1;
					if($projects[$nxt]['company'] != $companyId) {
						$html .= '</optgroup>';
					}
				}
			}			
			return $html;
		}

		
		public static function projectInArray($parray,$itemid){
			$flag = false;
			if(is_array($parray)){
				foreach($parray as $k => $val ){
				
					if($val['id'] ==  $itemid){
						return $flag = true;	
					}
				}
			
			}
		return $flag;
		
		}
		public function getPriorityOptionEditHTML($itemId) {
			$priorities = self::getQuery(QRY_WO_PRIORITY_ASC);
			$html = '';
			$already_sel = false;
			
			$html .= '<option value="">--Select Priority--</option>';
			for($i = 0; $i < sizeof($priorities); $i++) {
                if($priorities[$i]['name']  != 'High') {
    				if($priorities[$i]['id'] == $itemId) {
    					$selected = " SELECTED";
    					$already_sel = true;
    				} else if($priorities[$i]['id'] == 1 && !$already_sel) {
    					$selected = " SELECTED";
    				} else {
    					$selected = "";
    				}
    				
    				$html .= '<option value="' .$priorities[$i]['id'] .'"' .$selected .'>' 
    					.$priorities[$i]['name']
    				.'</option>';
                }
			}
			
			return $html;
		}

		
		public function getStatusOptionEditHTML($itemId='') {
			$types = self::getQuery("SELECT id,name FROM `lnk_qa_status_types` WHERE `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC");
			$html = '';

			$html .= '<option value="">--Select Type--</option>';
			$displayStatusArray = array();
			// New-1, In Progress-2, Fixed - 3 , Rejected - 4,  Reopened - 5, Need More Info - 6 ,  Hold - 7 , Closed - 8, Feedback Provided - 10
			if($itemId=='1')
			{
				// For New status 
				$displayStatusArray = array('1'=> '1','2'=> '2','6'=>'6','7'=>'7');
			}
			if($itemId=='10')
			{
				// For Feedback Provided status 
				$displayStatusArray = array('1'=> '1','2'=> '2','6'=> '6','10'=>'10');
			}
			if($itemId=='2')
			{
				// For In Progress status 
				$displayStatusArray = array('1'=> '1','2'=> '2','3'=> '3','6'=> '6','7'=>'7');
			}
			if($itemId=='3')
			{
				// For Fixed status 
				$displayStatusArray = array('3'=> '3','8'=> '8','4'=>'4');
			}
			if($itemId=='8')
			{
				// For Closed status 
				$displayStatusArray = array('3'=> '3','8'=> '8','5'=>'5');
			}
			if($itemId=='7')
			{
				// For Hold status 
				$displayStatusArray = array('7'=> '7','6'=> '6','2'=>'2');
			}
			if($itemId=='4')
			{
				// For Rejected status 
				$displayStatusArray = array('4'=> '4','6'=> '6','2'=>'2','7'=>'7');
			}
			if($itemId=='5')
			{
				// For Reopened status 
				$displayStatusArray = array('5'=> '5','6'=> '6','2'=>'2','7'=>'7');
			}
			if($itemId=='6')
			{
				// For Need More Info status 
				$displayStatusArray = array('6'=> '6','10'=> '10','7'=>'7');
			}
			for($i = 0; $i < sizeof($types); $i++) {
				
				if($types[$i]['id'] == $itemId) {
					$selected = " SELECTED";
				} else {
					$selected = "";
				}			
				if(array_key_exists($types[$i]['id'], $displayStatusArray))
				{
					$html .= '<option value="' .$types[$i]['id'] .'"' .$selected .'>' 
						.$types[$i]['name']
					.'</option>';				
				}
				
			}			
			return $html;
		}

		public function getCompanyHTML() {
			$html = '';
			$comp = self::getQuery(QRY_COMPANIES_ASC);
			
			$html = '';
			
			for($i = 0; $i < sizeof($comp); $i++) {
				$html .= '<option value="' .$comp[$i]['id'] .'" title="'.$comp[$i]['name'] .'">' .$comp[$i]['name'] .'</option>';
			}
			
			
			return $html;
		}
		public function assignDateHTML($wo_id = "") {
			if(!empty($wo_id)) {
				$wo = self::getQuery("SELECT * FROM `workorders` WHERE `id`='$wo_id' LIMIT 1");
				$date_time_split = explode(" ", $wo[0]['assigned_date']);
				$date_split = explode("-", $date_time_split[0]);
				
				$dt = number_format($date_split[1]) ."/" .number_format($date_split[2]) ."/" .$date_split[0];
				
				return '<input type="hidden" name="assigned_date" id="assigned_date" value="' .$dt .'" />';
			} else {
				return '<input type="hidden" name="assigned_date" id="assigned_date" value="" />';
			}
		}

		public function userIMG($user_id) {
			if(!empty($user_id)) {
				$wo = self::getQuery("SELECT user_img FROM `users` WHERE `id`='$user_id' LIMIT 1");
				return $wo[0]['user_img'];
			} 
			return "";
		}

		public function fetchUserName($user_id) {
			if(!empty($user_id)) {
				$wo = self::getQuery("SELECT first_name,last_name FROM `users` WHERE `id`='$user_id' LIMIT 1");
				return $wo[0]['last_name'].', '.$wo[0]['first_name'];
			} 
			return "";
		}

		public function getCCList($defect_id) {
			$cclist = self::getQuery("SELECT `cclist` FROM `qa_defects` WHERE `id`='$defect_id'");
			
			return '<input type="hidden" name="cclist" id="cclist" value="' .$cclist[0]['cclist'] .'" />';
		}
		public function getUserAssignOptionHTML($client=false) {
			$users = self::getQuery("SELECT id,last_name,first_name FROM `users` WHERE `active`='1' AND `deleted`='0' AND `company`='2' and `id` = '97' ORDER BY `last_name` ASC");
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
					$html .= '<option></option>';
			}
			
			return $html;
		}
		public function getUserAssignOptionEditHTML($itemId ) {
			$users = self::getQuery("SELECT id,first_name,last_name FROM `users` WHERE `active`='1' AND `deleted`='0' ORDER BY `last_name` ASC");
			$html = '';
			
			$html .= '<option value="">--Select User--</option>';

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

				$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';
				
			}			
			return $html;
		}

		public function getAllStatusOptionHTML() {
			$statuses = self::getQuery("select name from `lnk_qa_status_types` where active='1' order by name");
			$html = '';
			
			for($i = 0; $i < sizeof($statuses); $i++) {
				$html .= '<option value="' .$statuses[$i]['name'] .'">' 
					.$statuses[$i]['name'] 
				.'</option>';
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
			print("hours saved ".$hour_saved);
			$html = '';
			
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
	// for fetching the qa ids first time for prev next navigation in edit screen of quality
		public function getQAIDs($client_id , $project_id , $status , $assigned_to , $severity , $column , $sortOrder){
      $status_flag = 0;
      $sort_column_order = "";
      $sort_string = "";
      $category_table = "";
      $user_table = "";
      $audit_table = "";
      $version_table = "";
      $where_last_action = "";
      if($sortOrder == '1'){
        $sort_column_order = " DESC ";
        if($column == "last_action"){
          $sort_column_order = "";
        }
      }else{
        if($column == "last_action"){
          $sort_column_order = " DESC ";
        }      
      }
      if($severity != "-1"){
      $severity_filter_sql = " AND b.`severity` = ".$severity;
      }
      if($client_id != "-1"){
      $client_filter_sql = " AND a.`company` = ".$client_id; 
      }
      if($project_id != "-1"){
      $project_filter_sql = " AND a.`id` = ".$project_id;
      }
      if($assigned_to != "-1"){
      $assigned_to_filter_sql = " AND b.`assigned_to` =".$assigned_to;
      }
      if($status != "-1"){
        $status_flag = 1;
        $status_table = " JOIN `lnk_qa_status_types` lnk ON b.`status` = lnk.`id` ";
        if($status == '99'){
          $status_filter_sql = " AND lnk.`name` NOT IN('closed')";
        }else{
          $status_filter_sql = " AND lnk.`name` = '".$status."'";      
        }
      }      

      switch($column){
      case id : $sort_string = " ,b.id";break;
      case title : $sort_string = " ,LOWER(CONVERT(b.`title` USING utf8))";break;//" ,LOWER(b.title)";break;
      case severity : $sort_string = " ,b.severity";break;
      case status : if($status_flag == 0){$status_table = " JOIN `lnk_qa_status_types` lnk ON b.`status` = lnk.`id` ";} $sort_string = " ,lnk.name";break;
      case category : $category_table = " JOIN lnk_custom_fields_value lc ON lc.field_id = b.category"; $sort_string = " ,lc.field_name";break;
      case version : $version_table = " JOIN qa_project_version qv ON b.version = qv.`id`";$sort_string = " ,qv.`version_name`";break;
      case open_date : $sort_string = " ,b.creation_date";break;
      case assigned_to : $user_table = " JOIN users u ON u.id = b.assigned_to"; $sort_string = " ,u.last_name";break;
      case detected_by : $user_table = " JOIN users u ON u.id = b.detected_by"; $sort_string = " ,u.last_name";break;
      case last_action : $audit_table = " JOIN quality_audit qa ON b.id = qa.defect_id";$sort_string = " ,qa.log_date ";$where_last_action = " AND qa.`id` IN (SELECT a.`id` FROM quality_audit a  WHERE log_date = (select max(log_date) from quality_audit b where a.defect_id =  b.defect_id)) ";break;
      }
      $qa_id_list_sql = "SELECT DISTINCT b.`id` FROM `projects` a JOIN `qa_defects` b ON a.`id`=b.`project_id` JOIN `user_project_permissions` c ON a.`id`=c.`project_id` ".$status_table.$category_table.$version_table.$user_table.$audit_table." WHERE c.`user_id`='" .$_SESSION['user_id'] ."'".$client_filter_sql.$severity_filter_sql.$project_filter_sql.$assigned_to_filter_sql.$status_filter_sql.$where_last_action." ORDER BY a.`company`, a.`project_name`".$sort_string.$sort_column_order.",b.`title` ASC,b.`id`";
    //  echo "qry=".$qa_id_list_sql; 
      $qa_id_list_result = self::getQuery($qa_id_list_sql); 
      return $qa_id_list_result;  
    }  
 	}
?>
