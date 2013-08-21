<?PHP
	/*
		Control Tower classes parent and child
	*/
	class WorkOrders {
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
	class WoDisplay extends WorkOrders {
		public function getQuery($query) {
			/*WorkOrders::setIniFile();
			
			$config = WorkOrders::getIniFile();
			
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
			$data = $db->fetchAll($query);
			
			return $data;
		}

		public function executeQuery($query) {
			/*WorkOrders::setIniFile();
			
			$config = WorkOrders::getIniFile();
			
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
			
			$data = $db->query($query);
			
			return $data;
		}
		
		public function getUserOptionHTML($client=false) {
			$users = self::getQuery(QRY_USERS_LAST_ASC);
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
						if($users[$i]['id'] == $_SESSION['user_id']) {
							$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
							.$userName
						.'</option>';

						}
					}
				}
			}
			
			return $html;
		}
		public function getProjectOptionHTML($pj) {
			if($_SESSION['login_status'] == "client") {
				$comp_query = " AND a.`company` = '".$_SESSION['company'] ."'";
				$comp_query = "";
			} else {
				$comp_query = "";
			}
			//echo "SELECT * FROM `projects` a, `user_project_permissions` b WHERE a.`id`=b.`project_id` AND b.`user_id`='" .$_SESSION['user_id'] ."' AND a.`active` = '1' AND a.`deleted` = '0'$comp_query ORDER BY a.`company` ASC";

			//$projects = self::getQuery("SELECT * FROM `projects` a WHERE a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' ORDER BY a.`company` ASC");
			$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a, `companies` com WHERE a.`company`=com.`id` AND a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1' ORDER BY com.`name`, a.`project_code` ASC");

			//$projects = self::getQuery("SELECT * FROM `projects` a, `user_project_permissions` b WHERE a.`id`=b.`project_id` AND b.`user_id`='" .$_SESSION['user_id'] ."' AND a.`active` = '1' AND a.`deleted` = '0' ORDER BY a.`company` ASC");
			//$projects = self::getQuery("SELECT * FROM `projects` a, `user_project_permissions` b WHERE a.`id`=b.`project_id` AND b.`user_id`='" .$_SESSION['user_id'] ."' AND a.`active` = '1' AND a.`deleted` = '0'$comp_query ORDER BY a.`company` ASC");
			$html = '';
			
			$html .= '<option value="">--Select Project--</option>';
			$companyId = false;
			for($i = 0; $i < sizeof($projects); $i++) {
				$perms = self::getQuery("SELECT * FROM `user_project_permissions` WHERE `user_id`='" .$_SESSION['user_id'] ."' AND `project_id`='" .$projects[$i]['id'] ."'");
				
				if(sizeof($perms) > 0) {
					if($projects[$i]['company'] != $companyId) {
//						$company = self::getQuery("SELECT * FROM `companies` WHERE `id`='" .$projects[$i]['company'] ."' LIMIT 1");
//						$html .= '<optgroup label="' . $company[0]['name'] .'" title="' . $company[0]['name'] .'">';
						$html .= '<optgroup label="' . $projects[$i]['company_name'] .'" title="' . $projects[$i]['company_name'] .'">';
					}
					if($projects[$i]['id'] == $pj) {
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
		public function getStatusOptionHTML($client=false) {
			$types = self::getQuery(QRY_WO_STATUS_ASC);
			$html = '';
			
			if(!$client) {
				for($i = 0; $i < sizeof($types); $i++) {
					if($types[$i]['id'] == 6) {
						$html .= '<option value="' .$types[$i]['id'] .'"' .$selected .'>' 
							.$types[$i]['name']
						.'</option>';
					} 
				}
			}
			
			return $html;
		}
		public function getUserOptionEditHTML($itemId='', $client=false) {
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

		public function getcustomDropDown($fieldKEY,$fieldID = '', $client=false) {

			$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='".$fieldKEY."' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' and cfv.active='1' and cfv.deleted='0' order by `field_name`";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);
			$html = '';					

			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['field_id'] == $fieldID) {
					$selected = " SELECTED";
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

		public function getcustomCheckbox($fieldKEY,$fieldID = '', $client=false)
		{
			$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='".$fieldKEY."' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' order by sort_order";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);

			$selected = "";			
			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['field_id'] == $fieldID) {
					if($fields_list[$i]['field_name']=='TRUE'){
						$selected = " CHECKED";
					}
					break;
				}				
			}
			return $selected;
		}

		public function getcustom($fieldKEY,$fieldID = '', $client=false) {

			$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='".$fieldKEY."' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' order by sort_order";
			$fields_list = self::getQuery($QRY_MASTER_SELECT);
			$html = '';					

			$html .= '<option value="_blank"></option>';
			for($i = 0; $i < sizeof($fields_list); $i++) {
				if($fields_list[$i]['field_id'] == $fieldID) {
					$selected = " SELECTED";
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

		public function getProjectOptionEditHTML($itemId) {
			if($_SESSION['login_status'] == "client") {
				$comp_query = " AND a.`company` = '".$_SESSION['company'] ."'";
			} else {
				$comp_query = "";
			}
			$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a, `companies` com WHERE a.`company`=com.`id` AND a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1'  $comp_query ORDER BY com.`name`, a.`project_code` ASC");
			$html = '';
			
			$html .= '<option value="">--Select Project--</option>';
			$companyId = false;
			for($i = 0; $i < sizeof($projects); $i++) {
				if($projects[$i]['company'] != $companyId) {
//					$company = self::getQuery("SELECT * FROM `companies` WHERE `id`='" .$projects[$i]['company'] ."' LIMIT 1");
//					$html .= '<optgroup label="' .$company[0]['name'] .'">';
					$html .= '<optgroup label="' . $projects[$i]['company_name'] .'">';
				}
				if($projects[$i]['id'] == $itemId) {
					$selected = " SELECTED";
				} else {
					$selected = "";
				}
				$html .= '<option value="' .$projects[$i]['id'] .'"' .$selected .'>' 
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
			
			return $html;
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
		public function getTypesOptionEditHTML($itemId) {
			$html_selected = "";
			$nav_selected = "";
			$layout_selected = "";
			$change_selected = "";
			$film_selected = "";
			
			switch($itemId) {
				case 'HTML Update': {
					$html_selected = " SELECTED";
					break;
				}
				case 'Nav Update': {
					$nav_selected = " SELECTED";
					break;
				}
				case 'Layout Issue': {
					$layout_selected = " SELECTED";
					break;
				}
				case 'Change Request': {
					$change_selected = " SELECTED";
					break;
				}
				case 'Film Evite': {
					$film_selected = "SELECTED";
					break;
				}
			}
			
			$html = '<option value="">--Select Type--</option>
				<option value="HTML Update"' .$html_selected .'>HTML Update</option>
				<option value="Nav Update"' .$nav_selected .'>Nav Update</option>
				<option value="Layout Issue"' .$layout_selected .'>Layout Issue</option>
				<option value="Change Request"' .$change_selected .'>Change Request</option>
				<option value="Film Evite"' .$film_selected .'>Film Evite</option>';
				
			return $html;
		}
		public function getStatusOptionEditHTML($itemId, $client=false,$projectId,$isActive,$isArchived) {
			$types = self::getQuery(QRY_WO_STATUS_ASC);

			$displayStatusArray = array();

			//1-Closed, 3-Fixed,4-On Hold,5-Need More Info,6-New,7-In Progress,10-Feedback Provided,11-Rejected,12-Reopened
			if($itemId=='1')
			{
				// For Closed status 
				$displayStatusArray = array('1'=> '1','12'=> '12');
			}			
			if($itemId=='3')
			{
				// For Fixed status 
				$displayStatusArray = array('3'=> '3','1'=> '1','11'=>'11');
			}
			if($itemId=='4')
			{
				// For Hold status 
				$displayStatusArray = array('4'=> '4','5'=> '5','7'=> '7');
			}
			if($itemId=='5')
			{
				// For Need More Info status 
				$displayStatusArray = array('5'=> '5','10'=> '10','4'=>'4');
			}
			if($isActive=='0')
			{
				// For draft status 
				$displayStatusArray = array('6'=> '6');
			} else if($itemId=='6'){
 				// For New status
 				$displayStatusArray = array('4'=> '4','5'=> '5','6'=> '6','7'=>'7');     
			}
			if($itemId=='7')
			{
				// For In Progress
				$displayStatusArray = array('3'=> '3','4'=> '4','5'=> '5','7'=>'7');
			}
			if($itemId=='10')
			{
				// For Feedback Provided status 
				$displayStatusArray = array('5'=> '5','7'=> '7','10'=>'10');
			}
			if($itemId=='11')
			{
				// For Rejected status 
				$displayStatusArray = array('4'=>'4','5'=> '5','7'=> '7','11'=>'11');
			}
			if($itemId=='12')
			{
				// For Reopened status 
				$displayStatusArray = array('4'=> '4','5'=> '5','7'=>'7','12'=>'12');
			}


			$html = '';
			$projectDetails = self::getQuery("SELECT * FROM `projects` WHERE `id`='$projectId' LIMIT 1");
			if(!$client) {
				$html .= '<option value="">--Select Type--</option>';
			}
			if($isArchived == '1'){
				for($i = 0; $i < sizeof($types); $i++) {
					if($types[$i]['id'] == $itemId){
						$html = '<option value="' .$types[$i]['id'] .'"' .$selected .'>' .$types[$i]['name'].'</option>';
					}
				}
			} else {
				for($i = 0; $i < sizeof($types); $i++) {
					if($types[$i]['id'] == $itemId) {
						$selected = " SELECTED";
					} else {
						$selected = "";
					}
					
					if($client && $_SESSION['company'] != $projectDetails[0]['company']) {
						if(!empty($selected)){
							if(array_key_exists($types[$i]['id'], $displayStatusArray))
							{
								$html .= '<option value="' .$types[$i]['id'] .'"' .$selected .'>' 
										.$types[$i]['name']
								.'</option>';
							}
						}
					} else {

						if(array_key_exists($types[$i]['id'], $displayStatusArray))
						{
							$html .= '<option value="' .$types[$i]['id'] .'"' .$selected .'>' 
									.$types[$i]['name']
							.'</option>';
						}

					}

				}
				
			}
			
			return $html;
		}
		public function getCompanyHTML() {
			$html = '';
			$comp = self::getQuery(QRY_COMPANIES_ASC);
			
			$html = '';
			
			for($i = 0; $i < sizeof($comp); $i++) {
				$html .= '<option value="' .$comp[$i]['id'] .'" title="'. $comp[$i]['name'] .'">' . $comp[$i]['name'] .'</option>';
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
		public function getUserAssignOptionEditHTML($itemId, $client=false) {
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' ORDER BY `last_name` ASC");
			$html = '';
			
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

		public function getAllStatusOptionHTML() {
			$statuses = self::getQuery("select * from `lnk_workorder_status_types` where active='1' order by name");
			$html = '';
			
			//$html .= '<option value="-1">Show All</option>';
			for($i = 0; $i < sizeof($statuses); $i++) {
				$html .= '<option value="' .$statuses[$i]['name'] .'">' 
					.$statuses[$i]['name'] 
				.'</option>';
			}
			
			return $html;
		}

		public function getDailyHours($hour_saved="") {
			if($hour_saved == '')
			{
				$hour_saved = '05:00 PM';
			}
			//$hour_saved = trim($hour_saved);
			$hours_array = array(
								"12:00 AM"=>"12:00 AM",
								"01:00 AM" =>"1:00 AM",
								"02:00 AM"=>"2:00 AM",
								"03:00 AM"=>"3:00 AM",
								"04:00 AM"=>"4:00 AM",
								"05:00 AM"=>"5:00 AM",
								"06:00 AM"=>"6:00 AM",
								"07:00 AM"=>"7:00 AM",
								"08:00 AM"=>"8:00 AM",
								"09:00 AM"=>"9:00 AM",
								"10:00 AM"=>"10:00 AM",
								"11:00 AM"=>"11:00 AM",
								"12:00 PM"=>"12:00 PM",
								"01:00 PM"=>"1:00 PM",
								"02:00 PM"=>"2:00 PM",
								"03:00 PM"=>"3:00 PM",
								"04:00 PM"=>"4:00 PM",
								"05:00 PM"=>"5:00 PM",
								"06:00 PM"=>"6:00 PM",
								"07:00 PM"=>"7:00 PM",
								"08:00 PM"=>"8:00 PM",
								"09:00 PM"=>"9:00 PM",
								"10:00 PM"=>"10:00 PM",
								"11:00 PM"=>"11:00 PM"
								);
			
			
			if($hour_saved > 12){
				$hour_saved = $hour_saved - 12;
			}
			if($hour_saved != ''){
				if(array_key_exists($hour_saved, $hours_array) == false){
					$hours_array[$hour_saved] = $hour_saved;
				}
			}
			$html = '';
			
			$html .= '<option value="">--</option>';
			//for($i = 0; $i < sizeof($hours_array); $i++) {
			foreach($hours_array as $hours_array_key => $hours_array_val){
				$selected = "";
				
				if($hour_saved == $hours_array_key){
					$selected = " selected='selected'";
				}
                $html .= '<option value="' .$hours_array_key.'"' .$selected .'>' 
    					.$hours_array_val
    				.'</option>';
            }
			
			return $html;
		}
		
		static function getUserCompany($cid = null){
			$db = Zend_Registry::get('db');
			$html = '';
			$user_id = $_SESSION['user_id'];
			if($cid == ''){
				$query = "SELECT company  from users WHERE id = $user_id";
				$result = $db->fetchOne($query);
				$cid = $result['company'];
			}
			$allCompanies = $db->fetchAll("SELECT c.id, c.name FROM companies c INNER JOIN users_companies uc ON (uc.company_id = c.id) WHERE uc.user_id = ".$user_id." AND c.active ='1' AND c.deleted='0' ORDER BY c.name ASC");
			
			foreach($allCompanies as $company_key => $company_val){
				$s = '';
				if($company_val['id'] == $cid){
					$s = "selected='selected'";
					
				
				}
				$html .= "<option ".$s." value=".$company_val['id'].">".$company_val['name']."</option>";
				
			}	
			
			return $html;
		
		
		}
		
		
		
		
	}
?>
