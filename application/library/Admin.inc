<?PHP
	class Admin {
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
	}
	class AdminDisplay extends Admin {
	
		public function getQuery($query, $arr = null) {
		
			/*Admin::setIniFile();
			
			$config = Admin::getIniFile();
			
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
			$data = $db->fetchAll($query, $arr);
			
			return $data;
		}

		public function executeQuery($query, $arr = null) {
			/*Admin::setIniFile();
			
			$config = Admin::getIniFile();
			
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
		
		public function getUserOptionHTML($userid='') {
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND `company`='2' ORDER BY `last_name` ASC");
			$html = '';			
			$selected = '';
			$html .= '<option value="">--Select User--</option>';
			for($i = 0; $i < sizeof($users); $i++) {
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}

				if($users[$i]['id'] == $userid) {
						$selected = " SELECTED";
				}
				else
				{
							$selected = "";
				}
				$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
					.$userName.'</option>';
			}			
			return $html;
		}
		public function getAllUserOptionHTML($userid='') {
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0'  ORDER BY `last_name` ASC");
			$html = '';			
			$selected = '';
			for($i = 0; $i < sizeof($users); $i++) {
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}

				if($users[$i]['id'] == $userid) {
						$selected = " selected='selected'";
				}
				else
				{
							$selected = "";
				}
				$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
					.$userName.'</option>';
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


		public function getUserTitleHTML($userTitle="") {
			$hiddenTitleArray = array();
			$userTitle = explode(",",$userTitle);  
			$usersTitleList = self::getQuery("SELECT * FROM `lnk_user_titles` WHERE `active`='1' AND `deleted`='0'  ORDER BY `sort` ASC");
			$usersTitleSubCategoryList = self::getQuery("SELECT * FROM `lnk_user_subtitles` WHERE `active`='1' AND `deleted`='0' ");
			for($i=0;$i<count($usersTitleSubCategoryList);$i++){
				$detailsArray['name'] = $usersTitleSubCategoryList[$i]['name'];
				$detailsArray['id'] = $usersTitleSubCategoryList[$i]['id'];
				$subCategoryArray[$usersTitleSubCategoryList[$i]['parentTitleId']][]=$detailsArray; 
			}
			$html = '';			
			$selected = '';
			$html .= '';
			for($i = 0; $i < sizeof($usersTitleList); $i++) {
				if(in_array($usersTitleList[$i]['id'], $userTitle)) {
					$selected = " SELECTED";
					$hiddenTitleArray[] = $usersTitleList[$i]['id'];
				}else{
					$selected = "";
				}
				if(count($subCategoryArray[$usersTitleList[$i]['id']])<=0){ 
					$html .= '<option value="' .$usersTitleList[$i]['id'] .'"' .$selected .'>' 
					.$usersTitleList[$i]['name'].'</option>';
				}else{
					$html .= '<optgroup label="'.$usersTitleList[$i]['name'].'">'; 
					$categoryDetails = $subCategoryArray[$usersTitleList[$i]['id']];  
					for($j = 0; $j < count($categoryDetails ); $j++) {   
						if(in_array("subcat_".$categoryDetails[$j]['id'], $userTitle)) {
							$selected = " SELECTED";$hiddenTitleArray[]="subcat_".$categoryDetails[$j]['id'];
						}else{
							$selected = "";
						}
						$html .= '<option value="subcat_' .$categoryDetails [$j]['id'] .'"' .$selected .'>' .$categoryDetails [$j]['name'] .'</option>';
					}
					$html .= "</optgroup>"; 
				}
			}
			return $html."<input type='hidden' id='previousUserTitle' value='".implode(",",$hiddenTitleArray)."'/>";
		}

		public function getUserRoleHTML($userRole='') {
			$usersRoleList = self::getQuery("SELECT * FROM  `lnk_project_phase_types` WHERE `active`='1' AND `deleted`='0' ORDER BY sort_order ASC");
			$html = '';			
			$selected = '';
			$html .= '<option value="">--Select User Role--</option>';
			for($i = 0; $i < sizeof($usersRoleList); $i++) {

				if($usersRoleList[$i]['id'] == $userRole) {
						$selected = " SELECTED";
				}
				else
				{
					$selected = "";
				}
				$html .= '<option value="' .$usersRoleList[$i]['id'] .'"' .$selected .'>' 
					.$usersRoleList[$i]['name'].'</option>';
			}			
			return $html;
		}

		public function fetchUserbyID($selectedUserID) {
			$users = self::getQuery("SELECT * FROM `users` WHERE `id` = ?",array($selectedUserID));			
			$titlesArray = self::getQuery("SELECT * FROM `user_roles` WHERE `user_id` = ?",array($selectedUserID));
			for($i=0;$i<count($titlesArray);$i++){
				if($titlesArray[$i]['flag']=="subcategory"){
					$titles[]="subcat_".$titlesArray[$i]['category_subcategory_id'];
				}else{
					$titles[]=$titlesArray[$i]['category_subcategory_id'];
				} 
			}
			if($users['user_title']!=""){
				$titles[] = $users['user_title']; 
			}
			if($titles!=""){
				$users[0]['user_title'] = implode(",",$titles);
			}  
			return $users;
		}
		
		public function fetchProjectCCList($proj_id) {
			$fetchProjectCCList = self::getQuery("SELECT * FROM `projects` WHERE `id` = ?",array($proj_id));			
			return $fetchProjectCCList;
		}

 //////18474//////
			public function fetchQCProjectCCList($proj_id) {
			$fetchProjectCCList = self::getQuery("SELECT * FROM `projects` WHERE `id` = ? ",array($proj_id));			
			return $fetchProjectCCList;
		}
		////////////
		public function fetchProjectVersions($proj_id,$version_id='') {
			if(!empty($proj_id))
			{
				$whereClause = "";
				if(!empty($version_id))
				{
					$whereClause = " AND `id`='".$version_id."'";
				}
				$proj_version = self::getQuery("SELECT * FROM `qa_project_version` WHERE `project_id` = '".$proj_id."' $whereClause");
			}
			return $proj_version;
		}
		public function fetchProjectProduct($proj_id,$version_id='') {
			if(!empty($proj_id))
			{
				$whereClause = "";
				if(!empty($version_id))
				{
					$whereClause = " AND `id`='".$version_id."'";
				}
				$proj_version = self::getQuery("SELECT * FROM `qa_project_product` WHERE `project_id` = '".$proj_id."' $whereClause");
			}
			return $proj_version;
		}
		public function fetchProjectIteration($proj_id,$version_id='') {
			if(!empty($proj_id))
			{
				$whereClause = "";
				if(!empty($version_id))
				{
					$whereClause = " AND `id`='".$version_id."'";
				}
				$proj_version = self::getQuery("SELECT * FROM `qa_project_iteration` WHERE `project_id` = '".$proj_id."' $whereClause");
			}
			return $proj_version;
		}

		public function getUserCompany($userCompany) {
			$companies = self::getQuery("SELECT * FROM  `companies` WHERE `id` = '".$userCompany."'");			
			return $companies[0]['name'];
		}

		public function getProjectName($proj_id) {
			$projects = self::getQuery("SELECT * FROM  `projects` WHERE `id` = '".$proj_id."'");
			return $projects[0]['project_code'].' - '.$projects[0]['project_name'];
		}

		public function fetchUserbyName($userFirstName,$userLastName) {
			$whereClause = "";
			if(!empty($userLastName) && !empty($userFirstName))
			{
				$whereClause = " `first_name` like '%".addslashes($userFirstName)."%' AND  `last_name` like '%".addslashes($userLastName)."%' ";
			}
			else if(!empty($userLastName))
			{
				$whereClause = " `last_name` like '%".addslashes($userLastName)."%' ";
			}
			else if(!empty($userFirstName))
			{
				$whereClause = " `first_name` like '%".addslashes($userFirstName)."%' ";
			}
			$QRY = "SELECT * FROM `users` WHERE $whereClause";
			$users = self::getQuery($QRY);
			return $users;
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

		
		
		public function getProjectOptionEditHTML($itemId) {

			$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a, `companies` com WHERE a.`company`=com.`id` AND a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1'  ORDER BY com.`name`, a.`project_code` ASC");
			$html = '';
			
			$html .= '<option value="">--Select Project--</option>';
			$companyId = false;
			for($i = 0; $i < sizeof($projects); $i++) {
				if($projects[$i]['company'] != $companyId) {
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

		
		

		public function getCompanyHTML() {
			$html = '';
			$comp = self::getQuery(QRY_COMPANIES_ASC);
			
			$html = '';
			
			for($i = 0; $i < sizeof($comp); $i++) {
				$html .= '<option value="' .$comp[$i]['id'] .'">' .$comp[$i]['name'] .'</option>';
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
				$wo = self::getQuery("SELECT * FROM `users` WHERE `id`='$user_id' LIMIT 1");
				return $wo[0]['user_img'];
			} 
			return "";
		}

		public function fetchUserName($user_id) {
			if(!empty($user_id)) {
				$wo = self::getQuery("SELECT * FROM `users` WHERE `id`='$user_id' LIMIT 1");
				return $wo[0]['last_name'].', '.$wo[0]['first_name'];
			} 
			return "";
		}

		public function getCCList($defect_id) {
			$cclist = self::getQuery("SELECT `cclist` FROM `qa_defects` WHERE `id`='$defect_id'");
			
			return '<input type="hidden" name="cclist" id="cclist" value="' .$cclist[0]['cclist'] .'" />';
		}
               
        	public function getProjectCCList($project_id) {
       		$cclist = self::getQuery("SELECT `cclist` FROM `projects` WHERE `id`='$project_id'");
			
			return '<input type="hidden" name="cclist" id="cclist" value="' .$cclist[0]['cclist'] .'" />';
		}
		public function getQCProjectCCList($project_id) {
       		$cclist = self::getQuery("SELECT `qccclist` FROM `projects` WHERE `id`='$project_id'");
			
			return '<input type="hidden" name="qccclist" id="qccclist" value="' .$cclist[0]['qccclist'] .'" />';
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
					$html .= '<option></option>';
			}
			
			return $html;
		}
		public function getUserAssignOptionEditHTML($itemId ) {
			$users = self::getQuery("SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' AND `company`='2' ORDER BY `last_name` ASC");
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
			$statuses = self::getQuery("select * from `lnk_qa_status_types` where active='1' order by name");
			$html = '';
			
			for($i = 0; $i < sizeof($statuses); $i++) {
				$html .= '<option value="' .$statuses[$i]['name'] .'">' 
					.$statuses[$i]['name'] 
				.'</option>';
			}
			
			return $html;
		}

		public function getMonths($monthSelected) {
			$startDate = strtotime(date('01/01/y'));
			$nextMonth = $startDate;
			for($i=1;$i<=12;$i++)
			{
				if($monthSelected == $i){
					$selected = " SELECTED";
				}
				else{
					$selected = "";
				}

				$html .= '<option value="' .$i .'"' .$selected .'>' 
							.date("F",$nextMonth)
						.'</option>';
				$nextMonth = strtotime("next month",$nextMonth);
			}			
			
			return $html;
		}
/* //////////////////////////////////////////////////////////////////////changes //////////////////////////////// */
		
		public function to_getMonths($monthSelected) {
			$startDate = strtotime(date('01/01/y'));
			$nextMonth = $startDate;
			$html .= '<option value="">-Select Month-</option>';
			for($i=1;$i<=12;$i++)
			{
				/*if($monthSelected == $i){
					$selected = " SELECTED";
				}
				else{
					$selected = "";
				}*/ 
					
				$html .= '<option value="' .$i .'"' .$selected .'>' 
							.date("F",$nextMonth)
						.'</option>';
				$nextMonth = strtotime("next month",$nextMonth);
			}			
			
			return $html;
		}

		public function to_getYears($yearSelected) {
		//	$startDate = strtotime(date('d/m/2010'));
		//	$nextYear = $startDate;
		$html .= '<option value="">-Select Year-</option>';
			for($i=2010;$i<=$yearSelected;$i++)
			{
				/*if($yearSelected == $i){
					$selected = " SELECTED";
				}
				else{
					$selected = "";
				}*/

				$html .= '<option value="' .$i .'"' .$selected .'>' 
							.$i
						.'</option>';
		//		$nextYear = strtotime("next year",$nextYear);
			}			
			
			return $html;
		}

/* //////////////////////////////////////////////////////////////changes///////////////////////////////////////////////////////////*/		
        public function getYears($yearSelected) {
		//	$startDate = strtotime(date('d/m/2010'));
		//	$nextYear = $startDate;
			for($i=2010;$i<=$yearSelected;$i++)
			{
				if($yearSelected == $i){
					$selected = " SELECTED";
				}
				else{
					$selected = "";
				}

				$html .= '<option value="' .$i .'"' .$selected .'>' 
							.$i
						.'</option>';
		//		$nextYear = strtotime("next year",$nextYear);
			}			
			
			return $html;
		}

		public function fetchSiteNames($site_id = '') {
			$whereClause = "";
			if(!empty($site_id))
			{
				$whereClause = " AND `field_id`='".$site_id."'";
			}
			$site_names = self::getQuery("SELECT * FROM `lnk_custom_fields_value` where field_key = 'SITE_NAME' order by `field_name`");
			return $site_names;
		}	

		public function fetchCustomFields(){
			//	3 => SITE_NAME, 4 => INFRA_TYPE, 6 => QA_CATEGORY, 8 => QA_SEVERITY, 9 => QA_OS, 10 => QA_BROWSER, 12 => QA_ORIGIN
			$field_values = self::getQuery("SELECT `field_key` FROM `lnk_custom_fields` WHERE `custom_field_id` in ('3', '4', '6', '8', '9', '10', '12') order by `field_key` ");
			return $field_values;
		}

		public function fetchFieldList($custom_name){
			$field_value_names = self::getQuery("SELECT * FROM `lnk_custom_fields_value` where field_key = '".$custom_name."' order by `field_name`");
			return $field_value_names;    
		}

		public function getUserVendorHTML($user_id){
			$user_vendor_name = self::getQuery("SELECT agency FROM `users` WHERE id='".$user_id."'");
			return $user_vendor_name[0]['agency'];
		}

		public function getUserProgramsHTML($user_id){
			$user_program = self::getQuery("SELECT program FROM `users` WHERE id='".$user_id."'");
			$html="<option value=''>--select a program--</option>";
			$programs_list = self::getQuery("SELECT id,program from lnk_programs");
			foreach($programs_list as $key=>$value){
				if($value['id'] == $user_program[0]['program']){
					$html.= '<option value="'.$value['id'].'" selected>'.$value['program'].'</option>';
				} else {
					$html.= '<option value="'.$value['id'].'">'.$value['program'].'</option>';
				}
			}
			return $html;
		}
		
		public  function getQualityGrid(){
    		$projects = self::getQuery("SELECT a.`id` id , a.`project_code` project_code, a.qa_permission, a.`project_name` project_name FROM `projects` a  INNER JOIN qa_defects qa ON (qa.project_id = a.id)  group by a.id ORDER BY a.`project_code` ASC");
    		
    		return $projects; 
 		}
 		
		
 		
 		public function upDateProjectQAPermission($projectIdArray){
 			$result = '';
 			$pid = explode(",",$projectIdArray);
 			$sql  = "UPDATE projects set qa_permission = '0' ";
			$result = self::executeQuery($sql);
 			foreach($pid as $id){
	 			try{
	 				$sql  = "UPDATE projects set qa_permission ='1' WHERE id = ? ";
					$result = self::executeQuery($sql,array($id));
					$result ='ok';
	 			}catch(Exception $e){
	 				$result = "fail";
	 			}
 			}
 			return $result;
 		}
		
		
		public function getAllCompaniesProjectOptionEditHTML($userid,$userStatus,$userCompany,$userProjectListArray) {
			$db = Zend_Registry::get('db');
			if($userStatus == "client") {
				$comp_query = " AND a.`company` = '".$userCompany ."'";
			} else {
				$comp_query = "";
			}
			$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a INNER JOIN `companies` com  ON (a.`company`=com.`id`) WHERE  a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1'   ORDER BY com.`name`, a.`project_code` ASC");
			$html = '';
					
			$companyId = false;
			//$userProjectListArray =  $db->fetchCol("SELECT project_id FROM `user_project_permissions` WHERE `user_id`='" .$userid ."' ");
					
			for($i = 0; $i < sizeof($projects); $i++) {
				if($projects[$i]['company'] != $companyId) {
//					$company = self::getQuery("SELECT * FROM `companies` WHERE `id`='" .$projects[$i]['company'] ."' LIMIT 1");
//					$html .= '<optgroup label="' .$company[0]['name'] .'">';
					$html .= '<optgroup label="' . $projects[$i]['company_name'] .'">';
				}
				if(in_array($projects[$i]['id'],$userProjectListArray) == TRUE) {
					$selected = " selected='selected'";
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
			if(count($projects) == 0){
				$html = "<option value=''>No Project Found</option>";
			}
			
			return $html;
		}
		
		function userProjectArray($userId, $userType='',$userStatus=''){
			$db = Zend_Registry::get('db');
			//$db = Zend_Registry::get('db');
			if($userStatus == "client") {
				$comp_query = " AND a.`company` = '".$userCompany ."'";
			} else {
				$comp_query = "";
			}
			//$projects = self::getQuery("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a INNER JOIN `companies` com  ON (a.`company`=com.`id`) WHERE  a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1'  $comp_query ORDER BY com.`name`, a.`project_code` ASC");

			return $userProjectListArray =  $db->fetchCol("SELECT distinct UP.project_id FROM `user_project_permissions` UP INNER JOIN `projects` a  ON (a.`id`=UP.`project_id`) WHERE UP.`user_id`='" .$userId."' AND  a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1'  ORDER BY a.`project_code` ASC ");
		
		}
		
		
		public  static function getRallyProjectOptionEditHTML() {
			$db = Zend_Registry::get('db');
			$projects = ("SELECT  rs.`project_name` AS workspace_name, r.`project_name` AS project_name, rs.project_id AS workspace_id, r.project_id AS project_id
				FROM  `rally_projects` r LEFT JOIN  `rally_projects` AS rs ON ( r.`workspace_id` = rs.`project_id` ) WHERE rs.`project_name` IS NOT NULL 
				ORDER BY rs.`project_name` ") ;
			$rs = $db->fetchAll($projects);
					
			return $rs;
		}
		

		
		public  static function maapingLhRallyProject($mappedData) {
		
			$db = Zend_Registry::get('db');
			//check if LH or RALLY project not exist in Mapped table
			$result = $db->query("SELECT id FROM rally_lh_project_mapping WHERE (lh_project_id = ? OR rally_project_id =  ? ) LIMIT 1", array($mappedData['lh_project_id'],$mappedData['rally_project_id']));
			$affectedLHRows = $result->rowCount();
			if($affectedLHRows == 0){
				$db->insert("rally_lh_project_mapping", $mappedData);
				return $db->lastInsertId();
			}
		}
		
		public  static function getLHRallyProjects($id =0) {
			
			$db = Zend_Registry::get('db');
			if($id > 0){
			
				$mappedData = "SELECT lh.project_name AS lh_project, rp.project_name AS ra_project, rl.id
							FROM rally_lh_project_mapping rl INNER JOIN projects lh ON ( lh.id = rl.lh_project_id ) 
							INNER JOIN rally_projects rp ON ( rp.project_id = rl.rally_project_id ) 
							WHERE rl.deleted =  '0' AND rl.active =  '1' AND rl.id = ".$id;
							
			}else{
				$mappedData = "SELECT lh.project_name AS lh_project, rp.project_name AS ra_project, rl.id, rl.lh_project_id,rl.rally_project_id
							FROM rally_lh_project_mapping rl INNER JOIN projects lh ON ( lh.id = rl.lh_project_id ) 
							INNER JOIN rally_projects rp ON ( rp.project_id = rl.rally_project_id ) 
							WHERE rl.deleted =  '0'
							AND rl.active =  '1'";
						}
			//echo $mappedData;
			$result = $db->fetchAll($mappedData);
			return $result;
		}
		
		public function getLHProjectOption() {
			$db = Zend_Registry::get('db');
			$projectsSql = $db->fetchAll("SELECT a.`id` id, a.`company` company, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a INNER JOIN `companies` com  ON (a.`company`=com.`id`) WHERE  a.`active` = '1' AND a.`deleted` = '0' AND a.`archived`='0' AND a.`qa_permission` = '1'  ORDER BY com.`name`, a.`project_code` ASC");
			return $projectsSql;
		}
		
		public static function deleteMaapingLhRallyProject($id){
			$db = Zend_Registry::get('db');
			
			$result = $db->query("DELETE FROM rally_lh_project_mapping WHERE id = ? LIMIT 1", array($id));
			return $affectedRows = $result->rowCount();
			//return $db->lastInsertId();
		
		}
		
	public static function deleteMaapingLhBsProject($id){
			$db = Zend_Registry::get('db');
			
			$result = $db->query("DELETE FROM `lh_basecamp_mapping` WHERE id = ? LIMIT 1", array($id));
			return $affectedRows = $result->rowCount();
			//return $db->lastInsertId();
		
		}
		
		static public function getSearchLog(){
			$db = Zend_Registry::get('db');
			$data = $db->fetchAll("SELECT l.pattern, CONCAT_WS(' ', first_name,last_name) as user_name from search_log l INNER JOIN users u ON l.user_id = u.id");
			return $data;
		
		}
		static public function getUniqueUsers(){
			$db = Zend_Registry::get('db');
			$result = $db->query("SELECT distinct user_id  FROM search_log");
			return $affectedRows = $result->rowCount();
		
		}
	static public function getLastLoggedInTime($userId){
			$db = Zend_Registry::get('db');
			$date_logged_time = '';
			$result = $db->query("SELECT  last_logged_date  FROM users_login_log WHERE user_id = $userId");
			if($result->rowCount() > 0){
				//echo "SELECT  DATE_FORMAT(last_logged_date , '%b %d %Y %I:%i:%p') as logged_time  FROM users_login_log WHERE user_id = $userId";
				$date_logged_time = $db->fetchAll("SELECT  DATE_FORMAT(last_logged_date , '%b %d %Y at %I:%i %p') as logged_time  FROM users_login_log WHERE user_id = $userId");
				return $date_logged_time[0]['logged_time'];
				
			}
			
		
		}
		
		
	public function getUserLastLoginTime(){
			$db = Zend_Registry::get('db');
			$result = $db->fetchAll("SELECT  u.*,DATE_FORMAT(ll.last_logged_date , '%b %d %Y at %I:%i %p') as logged_time  FROM users u
			LEFT JOIN users_login_log ll ON (ll.user_id = u.id) order by u.first_name ");
			return $result;
		}
		
	function safeSql($var){
			$db = Zend_Registry::get('db');
			
			$replaceValiable = '';
			//$var = trim($var);
			if(!empty($var)){
				$db->quote($var);
				return strip_tags($var);
				//return str_replace($sqlInjectionValiable,'',strip_tags($var));
			}else{
				return '';
			
			}
		
		}
		
		
			
	public function getBasecampProjectOption() {
			$db = Zend_Registry::get('db');
			$projectsSql = $db->fetchAll("SELECT a.`id` id, a.`company` company, a.bc_id, a.`project_code` project_code, a.`project_name` project_name, com.`name` company_name FROM `projects` a INNER JOIN `companies` com  ON (a.`company`=com.`id`) WHERE  a.`active` = '1' AND a.`deleted` = '0' AND a.`archived`='0' AND a.bc_id <> ''  ORDER BY com.`name`, a.`project_code` ASC");
			return $projectsSql;
		}
		
	public  static function getBasecampProjectOptionEditHTML($id = '') {
			$db = Zend_Registry::get('db');
			if($id != ''){
				$projects = ("SELECT DISTINCT b.bc_id, b.id ,p.project_name AS lh_project,CONCAT_WS(' ',u.first_name,u.last_name) as full_name , b.assigned_to FROM lh_basecamp_mapping b
				 INNER JOIN projects p on (b.bc_id = p.bc_id) INNER JOIN users u on(b.assigned_to = u.id) WHERE b.id =? ") ;
				$rs = $db->fetchAll($projects, array($id));
				
			}else{
			
				$projects = ("SELECT DISTINCT b.bc_id, b.id ,p.project_name AS lh_project,CONCAT_WS(' ',u.first_name,u.last_name) as full_name , b.assigned_to FROM lh_basecamp_mapping b
				 INNER JOIN projects p on (b.bc_id = p.bc_id) INNER JOIN users u on(b.assigned_to = u.id) ") ;
				$rs = $db->fetchAll($projects);	
			}
			
					
			return $rs;
		}
		
	public function getLHUsersObj() {
			$db = Zend_Registry::get('db');
			$userSql = $db->fetchAll("SELECT  CONCAT_WS(' ', first_name,last_name) as user_name , id from users WHERE active ='1' AND deleted ='0' ORDER BY first_name ");
			return $userSql;
		}
		
	public static function getAllApplicationCategory(){
		$db = Zend_Registry::get('db');
		$userSql = $db->fetchAll("SELECT * FROM `site_categories` WHERE active='1' AND deleted='0'");
		
		foreach($userSql as $key => $value){
			
			$html.= '<option value="'.$value['id'].'">'.$value['category_name'].'</option>';
				
		
		}
		return $html;
	
	
	}
	
	public static function getApplicationCategory(){
		$db = Zend_Registry::get('db');
		$userSql = $db->fetchAll("SELECT * FROM `site_categories`");
		
		foreach($userSql as $key => $value){
			
			$html.= '<option value="'.$value['id'].'">'.$value['category_name'].'</option>';
				
		
		}
		return $html;
	
	
	}

	
	public function getApplicationSiteName($cat_id){
		$db = Zend_Registry::get('db');
		$userSql = $db->fetchAll("SELECT f.`field_name` , f.`field_id` FROM `lnk_custom_fields_value` f
						INNER JOIN `site_categories_mapping` c ON ( c.`field_id` = f.`field_id` )
						WHERE c.category_id =?
						AND f.`field_key` = 'SITE_NAME' ORDER BY f.`field_name`",$cat_id );
		return $userSql;
	
	
	
	}
	
	public function getSiteNames(){
		$db = Zend_Registry::get('db');
		$userSql = $db->fetchAll("SELECT f.`field_name` , f.`field_id` FROM `lnk_custom_fields_value` f	WHERE  f.`field_key` = 'SITE_NAME' AND f.`field_id`
		NOT IN (SELECT field_id FROM site_categories_mapping ) ORDER BY f.`field_name`");
		return $userSql;
	
	
	}
	
	public function resetApplicationCatIds($cat_id , $application_arr = array()){
		$db = Zend_Registry::get('db');
		
		if($cat_id != ''){
			$result = $db->query("DELETE FROM `site_categories_mapping` WHERE `category_id` = ? ", array($cat_id));
			//print_r($application_arr);
			if((ISSET($application_arr)) && ($application_arr != 'null')){
				foreach( $application_arr as $application_key =>  $application_val){
					if($application_val != ''){
						$db->query("INSERT INTO `site_categories_mapping` SET `category_id` = ? , field_id = ?", array($cat_id,$application_val));
					}
				
				
				}
			}
		
		
		}
	}
		
		
		public function resetManagerResources($man_id , $resources_arr = array()){
		$db = Zend_Registry::get('db');
		
		if($man_id != ''){
			$result = $db->query("DELETE FROM `resource_manager_mapping` WHERE `manager_id` = ? ", array($man_id));
			//print_r($application_arr);
			if((ISSET($resources_arr)) && ($resources_arr != 'null')){
				$resources_arr_unique = array_unique($resources_arr);
				foreach($resources_arr_unique as $resource_key =>  $resource_val){
					if($resource_val != '' && $man_id !=  $resource_val){
					
						$db->query("INSERT INTO `resource_manager_mapping` SET `manager_id` = ? , resource_id = ?", array($man_id,$resource_val));
					}
				
				
				}
			}
		
		
		}
		
	
	}
	
	
	
	public function addCategory($dataArray){
		$db = Zend_Registry::get('db');
		
		
		$db->insert("site_categories", $dataArray);
		return $db->lastInsertId();
		
		
	}
	
	public function updateCategory($dataArray,$id){
		$db = Zend_Registry::get('db');
		
		$where[] = 'id =  '.$id;
		
		$db->update("site_categories", $dataArray, $where);
		//return $db->lastInsertId();
		
		
	}
	
	public function checkCategoryName($catName){
		$db = Zend_Registry::get('db');
		$catName = trim($catName);
		if($catName != ''){
			$userSql = $db->query("SELECT category_name FROM `site_categories` WHERE category_name = ?  ",array($catName));
			return $userSql->rowCount();
		}
		
		
		
	}
	public function getcategoryDetails($catID){
		$db = Zend_Registry::get('db');
		$catID = trim($catID);
		if($catID != ''){
			
			$userSql = $db->fetchRow("SELECT * FROM `site_categories` WHERE id = ?  ",array($catID));
			return $userSql;
			
		}
		
		
		
	}

	function getUsersList(){
		$db = Zend_Registry::get('db');
		$userSql = $db->fetchAll("SELECT u.* FROM `users` u  WHERE u.`active`='1' AND u.`deleted`='0'  AND u.`id`  NOT IN (SELECT resource_id FROM resource_manager_mapping ) ORDER BY u.last_name ASC ");
		return $userSql;
	
	}	
	
	
	public function getManagerResourceName($man_id){
		$db = Zend_Registry::get('db');
		$userSql = $db->fetchAll("SELECT u.*, c.resource_id from users u
						INNER JOIN `resource_manager_mapping` c ON ( u.`id` = c.`resource_id` )
						WHERE c.manager_id =? ",$man_id );
		return $userSql;
	
	
	
	}
	
	public function getTechLinkManagers($userid='') {
			$users = self::getQuery(QRY_TECHLINK_MANAGERS);
			$html = '';			
			$selected = '';
			for($i = 0; $i < sizeof($users); $i++) {
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}

				if($users[$i]['id'] == $userid) {
						$selected = " selected='selected'";
				}
				else
				{
							$selected = "";
				}
				$html .= '<option value="' .$users[$i]['id'] .'"' .$selected .'>' 
					.$userName.'</option>';
			}			
			return $html;
		}
		
}
?>
