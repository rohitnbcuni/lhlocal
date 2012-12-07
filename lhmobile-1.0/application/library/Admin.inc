<?PHP
	/*
		Control Tower classes parent and child
	*/
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
	
		public function getQuery($query) {
		
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
			$data = $db->fetchAll($query);
			
			return $data;
		}

		public function executeQuery($query) {
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
			$data = $db->query($query);
			
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
			$users = self::getQuery("SELECT * FROM `users` WHERE `id` = '".$selectedUserID."'");			
			$titlesArray = self::getQuery("SELECT * FROM `user_roles` WHERE `user_id` = '".$selectedUserID."'");
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
			$fetchProjectCCList = self::getQuery("SELECT * FROM `projects` WHERE `id` = '".$proj_id."'");			
			return $fetchProjectCCList;
		}

 //////18474//////
			public function fetchQCProjectCCList($proj_id) {
			$fetchProjectCCList = self::getQuery("SELECT * FROM `projects` WHERE `id` = '".$proj_id."'");			
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
 			$sql  = "UPDATE projects set qa_permission ='0' ";
			$result = self::executeQuery($sql);
 			foreach($pid as $id){
	 			try{
	 				$sql  = "UPDATE projects set qa_permission ='1' WHERE id = $id";
					$result = self::executeQuery($sql);
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
		
		function userProjectArray($userId, $userType,$userStatus){
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
	}
?>
