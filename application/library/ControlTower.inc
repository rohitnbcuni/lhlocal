<?PHP
	/*
		Control Tower classes parent and child
	*/
	class ControlTower {
		protected static $create_new;
		protected static $edit;
		protected static $configuration;
		
		protected static $project_id;
		protected static $completeness;
		protected static $section;
		protected static $company;
		protected static $desc;
		protected static $project_code;
		protected static $business_case;
		protected static $roles;
		protected static $timeline;
		protected static $scope;
		protected static $finance;
		protected static $deliverables;
		protected static $metrics_tracking;
		protected static $approvals;
		
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
			Project Setters
		*/
		//If the current project is in create new mode
		static public function setCreateNew($setVal) {
			$create_new = $setVal;
		}
		
		//Sets the current section we are adding project data in
		static public function setSection($setVal) {
			$section = $setVal;
		}
		
		//Sets the project id
		static public function setProjectId($setVal) {
			$project_id = $setval;
		}
		
		//Sets the completeness values
		static public function setCompleteness($setVal) {
			$completeness = $setVal;
		}
		
		//Sets the company
		static public function setCompany($setVal) {
			$company = $setVal;
		}
		
		//Sets the projects description
		static public function setDesc($setVal) {
			$desc = $setVal;
		}
		
		//Sets the project code
		static public function setProjectCode($setVal) {
			$project_code = $setVal;
		}
		
		//Sets the projects business case
		static public function setBusinessCase($setVal) {
			$business_case = $setVal;
		}
		
		//Sets the projects roles
		static public function setRoles($setVal) {
			$roles = $setVal;
		}
		
		//Sets the timeline
		static public function setTimeline($setVal) {
			$timeline = $setVal;
		}
		
		//Sets the project scope
		static public function setScope($setVal) {
			$scope = $setVal;
		}
		
		//Sets the project finance data
		static public function setFinance($setVal) {
			$finance = $setVal;
		}
		
		//Sets the project deliverables
		static public function setDeliverables($setVal) {
			$deliverables = $setVal;
		}
		
		//Sets the projects metrics and tracking
		static public function setMetricsTracking($setVal) {
			$metrics_tracking = $setVal;
		}
		
		//Sets the projects approval statuses
		static public function setApprovals($setVal) {
			$approvals = $setVal;
		}
		
		/*
			Project Getters
		*/
		//If the current project is in create new mode
		static public function getCreateNew() {
			return $create_new;
		}
		//gets the current section we are adding project data in
		static public function getSection() {
			return $section;
		}
		//gets the project id
		static public function getProjectId() {
			return $project_id;
		}
		//gets the completeness values
		static public function getCompleteness() {
			return $completeness;
		}
		//gets the company
		static public function getCompany() {
			return $company;
		}
		//gets the projects description
		static public function getDesc() {
			return $desc;
		}
		//gets the project code
		static public function getProjectCode() {
			return $project_code;
		}
		//gets the projects business case
		static public function getBusinessCase() {
			return $business_case;
		}
		//gets the projects roles
		static public function getRoles() {
			return $roles;
		}
		//gets the timeline
		static public function getTimeline() {
			return $timeline;
		}
		//gets the project scope
		static public function getScope() {
			return $scope;
		}
		
		//gets the project finance data
		static public function getFinance() {
			return $finance;
		}
		
		//gets the project deliverables
		static public function getDeliverables() {
			return $deliverables;
		}
		
		//gets the projects metrics and tracking
		static public function getMetricsTracking() {
			return $metrics_tracking;
		}
		
		//gets the projects approval statuses
		static public function getApprovals() {
			return $approvals;
		}
	}
	
	class CtDisplay extends ControlTower {
	
		public function getActiveProjects() {
			
		}
		
		public function getArchivedProjects() {
			
		}

		public function getConfigValue(){
			ControlTower::setIniFile();
			$config = ControlTower::getIniFile();
			return $config;
		}
		
		public function getQuery($query) {
			/*ControlTower::setIniFile();
			
			$config = ControlTower::getIniFile();
			
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
		
		public function totalActiveProjects() {
			$projects = self::getQuery("SELECT count(`id`) as total FROM `projects` WHERE `archived`='0' AND `active`='1' AND `deleted`='0'");
			
			return $projects[0]['total'];
		}
		
		/*public function financeTotal() {
			$fin = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `active`='1' AND `deleted`='0'");
			$finance_total = 0;
			
			for($i = 0; $i < sizeof($fin); $i++) {
				$proj = self::getQuery("SELECT * FROM `projects` WHERE `id`='" .$fin[$i]['project_id'] ."' AND `archived`='0' AND `active`='1' AND `deleted`='0' LIMIT 1");
				
				if(sizeof($proj) == 1) {
					$finance_total += ($fin[$i]['rate'] * $fin[$i]['hours']);
				}
			}
			return $finance_total;
		}*/
		
		public function financeTotal() {

			$total_budget =  self::getQuery("SELECT sum(total_budget) as total_budget FROM `project_budget` pb, projects p  where p.id=pb.project_id and p.archived = '0' and p.active = '1' and p.deleted = '0'");
			$finance_total = 0;

			if(sizeof($total_budget)>0)
			{
				$finance_total = $total_budget[0]['total_budget'];
			}
			return $finance_total;
		}

		public function getTotalBudget(){
			
			$total_project_rates =  self::getQuery("select project_id , phase, sum(Total) as total_amount from ( select ppf.project_id , ppf.phase, (hours * rate) as Total from  `project_phases` pp ,`project_phase_finance` ppf,`projects` p where pp.project_id = ppf.project_id and pp.phase_type = ppf.phase and p.id = pp.project_id and p.archived = '0' and p.active = '1' and p.deleted = '0' UNION select pp.project_id,pp.phase_type,sum((hours*rate)) as Total from project_sub_phase_finance pspf,`project_phases` pp,`projects` p where  pp.project_id = pspf.project_id and pp.phase_type = pspf.phase and p.id=pp.project_id and p.archived = '0' and p.active = '1' and p.deleted = '0' group by pspf.project_id , pspf.phase) tab1 group by project_id , phase ");

			return $total_project_rates;
		}
		
		public function getSectionHTML() {
			$html = '';
			//$sec = self::getSections();
			$sec = self::getQuery(QRY_PROJECT_SECTIONS_ASC);
			
			$html = "<ul id=\"create_sections\">";
			for($i = 0; $i < sizeof($sec); $i++) {
				if(($i%2) == 0) {
					$class = "alt ";
				} else {
					$class = "";
				}
				
				if($i == 0) {
					$class .= "active";
				} 
				$html .= "<li class=\"$class\" id=\"sec_" .$sec[$i]['id'] ."\" onClick=\"ctCreateSectionsSwitch('sec_" .$sec[$i]['id'] ."');\"><img src=\"/_images/red_status.gif\">" .$sec[$i]['name'] ."</li>";
			}
			
			$html .= "</ul>";
			
			return $html;
		}
/////////NEW FUNCTION ADDED BY MANOJ/////
	public function getSectionEditHTMLNEW($project_id,$project_timeline) {
			$html = '';
			//$sec = self::getSections();
			if($_SESSION['login_status'] == "client"){
				$sec = self::getQuery("SELECT * FROM `lnk_project_brief_section_types` where id not in ('5','11') ORDER BY `sort_order` ASC");
			}else{
				$sec = self::getQuery(QRY_PROJECT_SECTIONS_ASC);
			}
			//$sec = self::getQuery(QRY_PROJECT_SECTIONS_ASC);
			
			$html = "<ul id=\"create_sections\">";
			for($i = 0; $i < sizeof($sec); $i++) {
				$sec_stat = self::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='$project_id' AND `section_type`='" .$sec[$i]['id'] ."' LIMIT 1");
				$flag_img = "";
				
				if(($i%2) == 0) {
					$class = "alt ";
				} else {
					$class = "";
				}
				
				switch(@$sec_stat[0]['flag']) {
					case '2': {
						$flag_img = "/_images/yellow_status.gif";
						break;
					}
					case '3': {
						$flag_img = "/_images/green_status.gif";
						break;
					}
					default: {
						$flag_img = "/_images/red_status.gif";
						break;
					}
				}
				
				if($i == 0 && $project_timeline =='') {
					$class .= "active";
				} 
				if($project_timeline!='' && $sec[$i]['id'] == '3'){$class .= "active";}
				if($sec[$i]['id'] == '4')
				{
					$html .= "<li class=\"$class\" id=\"sec_" .$sec[$i]['id'] ."\" onClick=\"ctCreateSectionsSwitch('sec_" .$sec[$i]['id'] ."');\"><span style=\"float:left;\"><img src=\"$flag_img\">" .$sec[$i]['name'] . "</span><span class=\"risk_ps\"><img src=\"/_images/flag_icon_red.png\"></span></li>";
				}else{
					$html .= "<li class=\"$class\" id=\"sec_" .$sec[$i]['id'] ."\" onClick=\"ctCreateSectionsSwitch('sec_" .$sec[$i]['id'] ."');\"><img src=\"$flag_img\">" .$sec[$i]['name'] . "</li>";
				}
			}
			$html .= "</ul>";
			
			return $html;
		}
///////////////		
		public function getSectionEditHTML($project_id) {
			$html = '';
			//$sec = self::getSections();
			if($_SESSION['login_status'] == "client"){
				$sec = self::getQuery("SELECT * FROM `lnk_project_brief_section_types` where id not in ('5','11') ORDER BY `sort_order` ASC");
			}else{
				$sec = self::getQuery(QRY_PROJECT_SECTIONS_ASC);
			}
			//$sec = self::getQuery(QRY_PROJECT_SECTIONS_ASC);
			
			$html = "<ul id=\"create_sections\">";
			for($i = 0; $i < sizeof($sec); $i++) {
				$sec_stat = self::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='$project_id' AND `section_type`='" .$sec[$i]['id'] ."' LIMIT 1");
				$flag_img = "";
				
				if(($i%2) == 0) {
					$class = "alt ";
				} else {
					$class = "";
				}
				
				switch(@$sec_stat[0]['flag']) {
					case '2': {
						$flag_img = "/_images/yellow_status.gif";
						break;
					}
					case '3': {
						$flag_img = "/_images/green_status.gif";
						break;
					}
					default: {
						$flag_img = "/_images/red_status.gif";
						break;
					}
				}
				
				if($i == 0) {
					$class .= "active";
				} 
				if($sec[$i]['id'] == '4')
				{
					$html .= "<li class=\"$class\" id=\"sec_" .$sec[$i]['id'] ."\" onClick=\"ctCreateSectionsSwitch('sec_" .$sec[$i]['id'] ."');\"><span style=\"float:left;\"><img src=\"$flag_img\">" .$sec[$i]['name'] . "</span><span class=\"risk_ps\"><img src=\"/_images/flag_icon_red.png\"></span></li>";
				}else{
					$html .= "<li class=\"$class\" id=\"sec_" .$sec[$i]['id'] ."\" onClick=\"ctCreateSectionsSwitch('sec_" .$sec[$i]['id'] ."');\"><img src=\"$flag_img\">" .$sec[$i]['name'] . "</li>";
				}
			}
			$html .= "</ul>";
			
			return $html;
		}
		
		public function getCompanyHTML() {
			$html = '';
			
			if($_SESSION['login_status'] == "client") {
				$sql = "SELECT distinct c.* FROM `companies` c, projects p where p.company = c.id and c.id='".$_SESSION['company']."' ORDER BY `name` ASC";
			} else {
				$sql = "SELECT distinct c.* FROM `companies` c, projects p where p.company = c.id and p.active='1' and p.archived='0' and p.deleted='0'  ORDER BY `name` ASC";
				$html .= '<option value="0">All Companies</option>';
			}
			//$sql = "SELECT distinct c.* FROM `companies` c, projects p where p.company = c.id ORDER BY `name` ASC";

			$comp = self::getQuery($sql);
			
			for($i = 0; $i < sizeof($comp); $i++) {
				$html .= '<option value="' .$comp[$i]['id'] .'" title="'. $comp[$i]['name'] .'">' . $comp[$i]['name'] .'</option>';
			}
			
			
			return $html;
		}
		public function getProducerHTML() {
			$html = '';
			$producer = self::getQuery("select * from users u where u.id in (SELECT distinct user_id FROM `project_roles` WHERE `resource_type_id` = '2' and project_id in (select id from projects where active='1' and archived='0' and deleted='0')) order by u.first_name asc,last_name asc");
			$deliveryManager = self::getQuery("select * from users u where u.id in (SELECT distinct user_id FROM `project_roles` WHERE `resource_type_id` = '3' and project_id in (select id from projects where active='1' and archived='0' and deleted='0'))order by u.first_name asc,last_name asc");
			
			$html = '';
			$html .= '<optgroup label="Engagement Lead">';
			$html .= '<option value="2_0">None</option>';
			for($i = 0; $i < sizeof($producer); $i++) {
				$html .= '<option value="2_' .$producer[$i]['id'] .'">' .$producer[$i]['first_name'] .' '.$producer[$i]['last_name'] .'</option>';
			}
			$html .= '<optgroup label="Project Manager">'; // "Delivery Manager" changed to "Project Manager" for the workorder #9164
			$html .= '<option value="3_0">None</option>';
			for($i = 0; $i < sizeof($deliveryManager); $i++) {
				$html .= '<option value="3_' .$deliveryManager[$i]['id'] .'">' .$deliveryManager[$i]['first_name'] .' '.$deliveryManager[$i]['last_name'] .'</option>';
			}

			return $html;
		}
		public function getCompanyBcHTML() {
			$html = '';
			$comp = self::getQuery(QRY_COMPANIES_ASC);
			
			$html = '';
			
			for($i = 0; $i < sizeof($comp); $i++) {
				$html .= '<option value="' .$comp[$i]['id'] .'_' .$comp[$i]['bc_id'] .'">' .$comp[$i]['name'] .'</option>';
			}			
			
			return $html;
		}
		
		public function getOwnerRolesNewHTML() {
			$html = '';
			$rols = self::getQuery(QRY_ROLES_SO_ASC);
			$users = self::getQuery(QRY_DPS_USERS_ASC);
			
			
			
			$user_list = '<option value="">--Select User--</option>';
			for($i = 0; $i < sizeof($users); $i++) {
				$user_list .= '<option value="' .$users[$i]['id'] .'">' 
					.$users[$i]['first_name'] .' ' .$users[$i]['last_name'] 
					.'</option>\n';
			}
			$html .= '<li style="display: none;"><form>
					<div class="prole_name"><label for="">Client:</label></div><div class="prole_select"><select><option>Amy Shriber</option></select></div>
					<div class="prole_email"><label for="">Email:</label><input type="text"></div>
					<div class="prole_phone"><label for="">Phone:</label><input type="text"></div>
					
					<div class="dim"></div>

					<div id="" class="prole_disable"><a href="#"></a></div>
					<div style="clear: both"></div>

				</form></li>';
			for($i = 0; $i < sizeof($rols); $i++) {
				$phase_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `resource_id`='" .$rols[$i]['id'] ."' LIMIT 1");
				if($rols[$i]['editable'] == "1") {
					$html .= '<li id="role'.$rols[$i]['id'].'" onmouseover="rolesOver(\''.$rols[$i]['id'].'\');" onmouseout="rolesOut(\''.$rols[$i]['id'].'\');">';
				} else {
					$html .= '<li id="role'.$rols[$i]['id'].'">';
				}
				
				$html .= '<form action="" method="post" name="role'.$rols[$i]['id'].'" onSubmit="return false;">
							<div class="prole_name">
								<input type="hidden" name="resource_type" id="resource_type" value="' .$rols[$i]['id'] .'" />
								<input type="hidden" name="phase_type" id="phase_type" value="' .@$phase_link[0]['phase_id'] .'" />';
								
								if($rols[$i]['editable'] == "0") {
									$html .= '<input type="hidden" name="required" id="required" value="yes" />';
								} else {
									$html .= '<input type="hidden" name="required" id="required" value="no" />';
								}
				
				$html .= '<label for="user">' .$rols[$i]['name'] .':</label>
								<select name="user" id="user" onChange="changeRoleUser(this.value, role'.$rols[$i]['id'].')">'
									.$user_list
								.'</select>
							</div>
							<div class="prole_email">
								<label for="email">Email:</label>
								<input type="text" name="email" id="email" class="readonly" readonly>
							</div>
							<div class="prole_phone">
								<label for="phone">Phone:</label>
								<input type="text" name="phone" id="phone" class="readonly" readonly>
							</div>
							<div class="dim"></div>
							<div id="role'.$rols[$i]['id'].'_btn" class="prole_disable"><a href="#" onclick="confirmDisableRole(\''.$rols[$i]['id'].'\'); return false"></a></div>
							<div style="clear: both"></div>
						</form>
					</li>';
			}
			
			return $html;
		}
		public function getOwnerRolesNewEditHTML($project_id) {
			$html = '';
			$rols = self::getQuery(QRY_ROLES_SO_ASC);
			$users = self::getQuery(QRY_DPS_USERS_ASC);
			$usersAll = self::getQuery(QRY_USERS_ASC);
			
			/*$user_list = '<option value="">--Select User--</option>';
			for($i = 0; $i < sizeof($users); $i++) {
				$user_list .= '<option value="' .$users[$i]['id'] .'">' 
					.$users[$i]['first_name'] .' ' .$users[$i]['last_name'] 
					.'</option>\n';
			}*/
			$html .= '<li style="display: none;"><form>
					

				</form></li>';
			for($i = 0; $i < sizeof($rols); $i++) {
				$rolEdit = self::getQuery("SELECT * FROM `project_roles` WHERE `resource_type_id`='" .$rols[$i]['id'] ."' AND `project_id`='$project_id' LIMIT 1");
				$phase_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `resource_id`='" .$rols[$i]['id'] ."' LIMIT 1");
				echo mysql_error();
				if(sizeof($rolEdit) < 1 && $rols[$i]['editable'] == 1) {
					$display = "display: block;-moz-opacity: 0.7; filter:alpha(opacity=70);";
					$btn_class = "prole_enable";
				} else {
					$display = "";
					$btn_class = "prole_disable";
				}
				
				
				if($rols[$i]['editable'] == 1 && $_SESSION['login_status'] != "client") {
					$html .= '<li id="role'.$rols[$i]['id'].'" onmouseover="rolesOver(\''.$rols[$i]['id'].'\');" onmouseout="rolesOut(\''.$rols[$i]['id'].'\');">';
				} else {
					$html .= '<li id="role'.$rols[$i]['id'].'">';
				}
				if($rols[$i]['id'] == 1) {
					$phase_link[0]['phase_id'] = "client";
				}
				if($rols[$i]['id'] == 2) {
					$phase_link[0]['phase_id'] = "producer";
				}
				$html .= '<form action="" method="post" name="role'.$rols[$i]['id'].'" onSubmit="return false;">
							<div class="prole_name">
								<input type="hidden" name="resource_type" id="resource_type" value="' .$rols[$i]['id'] .'" />
								<input type="hidden" name="phase_type" id="phase_type" value="' .@$phase_link[0]['phase_id'] .'" />';
								
								if($rols[$i]['editable'] == "0") {
									$html .= '<input type="hidden" name="required" id="required" value="yes" />';
								} else {
									$html .= '<input type="hidden" name="required" id="required" value="no" />';
								}
						if($rols[$i]['name'] == 'Client'){							
							$html .= '<label for="user">' .$rols[$i]['name'] .':</label>
								<select name="user" id="user" onChange="changeRoleUser(this.value, role'.$rols[$i]['id'].')">
									<option value="">--Select User--</option>';
									
									for($j = 0; $j < sizeof($usersAll); $j++) {
										if(sizeof($rolEdit) == 1) {
											if($usersAll[$j]['id'] == $rolEdit[0]['user_id']) {
												$selected = " SELECTED";
											} else {
												$selected = "";
											}
										} else {
											$selected = "";
										}
										$html .= '<option value="' .$usersAll[$j]['id'] .'"' .$selected .'>' 
											.$usersAll[$j]['first_name'] .' ' .$usersAll[$j]['last_name'] 
											.'</option>\n';
									}
								$html .= '</select>';
							} else {
								
								$html .= '<label for="user">' .$rols[$i]['name'] .':</label>
								<select name="user" id="user" onChange="changeRoleUser(this.value, role'.$rols[$i]['id'].')">
									<option value="">--Select User--</option>';
									
									for($j = 0; $j < sizeof($users); $j++) {
										if(sizeof($rolEdit) == 1) {
											if($users[$j]['id'] == $rolEdit[0]['user_id']) {
												$selected = " SELECTED";
											} else {
												$selected = "";
											}
										} else {
											$selected = "";
										}
										$html .= '<option value="' .$users[$j]['id'] .'"' .$selected .'>' 
											.$users[$j]['first_name'] .' ' .$users[$j]['last_name'] 
											.'</option>\n';
									}
								$html .= '</select>';
							}

							$html .= '</div>
							<div class="prole_email">
								<label for="email">Email:</label>
								<input type="text" name="email" id="email" class="readonly" value="' .@$rolEdit[0]['email'] .'" readonly>
							</div>
							<div class="prole_phone">
								<label for="phone">Phone:</label>
								<input type="text" name="phone" id="phone" class="readonly" value="' .@$rolEdit[0]['phone'] .'" readonly>
							</div>
							<div class="dim" style="' .$display .'"></div>
							<div id="role'.$rols[$i]['id'].'_btn" class="' .$btn_class .'"><a href="#" onclick="confirmDisableRole(\''.$rols[$i]['id'].'\'); return false"></a></div>
							<div style="clear: both"></div>
						</form>
					</li>';
			}
			
			return $html;
		}
		public function getTimelineHTML() {
			$html = '';
			$phases = self::getQuery(QRY_TIMELINE_SO_ASC);
			
			$html .= '<ul class="ptimeline" id="project_timeline">'
				.'<li style="display: none;">'
					.'<form></form>'
				.'</li>';
				
			for($i = 0; $i < sizeof($phases); $i++) {
				$timeline_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `phase_id`='" .$phases[$i]['id'] ."' LIMIT 1");
				if(sizeof($timeline_link) == 1) {
					$nameIt = ' id="timeline_' .$timeline_link[0]['resource_id'] .'"';
				} else {
					$nameIt = '';
				}
				$html .= '<li' .$nameIt .'>
					<form action="" method="post">
						<input type="hidden" name="phase" id="phase" value="' .$phases[$i]['id'] .'" />
						<div class="ptimeline_phase"><label for="phase">' .$phases[$i]['name'] .'</label></div>
						<div class="ptimeline_startdate"><label for="start_date">Start Date:</label><input type="text" name="start_date" id="start_date' . $i .'" class="date_picker readonly" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')"></div>
						<div class="ptimeline_enddate"><label for="projected_date">Projected End Date:</label><input type="text" name="projected_date" id="projected_date' . $i .'" class="date_picker readonly" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')"></div>
						<div class="dim"></div>
						<div style="clear: both"></div>
					</form>
				</li>';
			}

			
			$html .= '</ul>';
			
			return $html;
		}
		public function getTimelineEditHTML($project_id) {
			$html = '';
			$phases = self::getQuery(QRY_TIMELINE_SO_ASC);
			
			$html .= '<ul class="ptimeline" id="project_timeline">'
				.'<li style="display: none;">'
					.'<form></form>'
				.'</li>';
				
			for($i = 0; $i < sizeof($phases); $i++) {
				$timeline_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `phase_id`='" .$phases[$i]['id'] ."' LIMIT 1");
				if(sizeof($timeline_link) == 1) {
					$nameIt = ' id="timeline_' .$timeline_link[0]['resource_id'] .'"';
				} else {
					$nameIt = '';
				}
				
				//get resource type link
				//check if resource type id exists
				if(is_array($timeline_link)) {
					$is_role = self::getQuery("SELECT * FROM `project_roles` WHERE `resource_type_id`='" .@$timeline_link[0]['resource_id'] ."' AND `project_id`='$project_id'");
				}
				
				if(sizeof($is_role) > 0) {
					$get_timeline = self::getQuery("SELECT * FROM `project_phases` WHERE `phase_type`='" 
					.$phases[$i]['id'] ."' AND `project_id`='$project_id'");
					
					if(sizeof($get_timeline) > 0) {
						$start_part = explode(" ", $get_timeline[0]['start_date']);
						$start_date = explode("-", $start_part[0]);
						$end_part = explode(" ", $get_timeline[0]['projected_end_date']);
						$end_date = explode("-", $end_part[0]);
						
						if($start_date[0] != '0000') {
							$start = $start_date[1] ."/" .$start_date[2] ."/" .$start_date[0];
						} else {
							$start = "";
						}
						if($end_date[0] != '0000') {
							$end = $end_date[1] ."/" .$end_date[2] ."/" .$end_date[0];
						} else {
							$end = "";
						}
					} else {
						$start = "";
						$end = "";
					}
					
					$html .= '<li' .$nameIt .'>
						<form action="" method="post">
							<input type="hidden" name="phase" id="phase" value="' .$phases[$i]['id'] .'" />
							<div class="ptimeline_phase"><label for="phase">' .$phases[$i]['name'] .'</label></div>
							<div class="ptimeline_startdate"><label for="start_date">Start Date:</label><input type="text" name="start_date" id="start_date' . $i .'" class="date_picker readonly" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')" value="' .$start .'"></div>
							<div class="ptimeline_enddate"><label for="projected_date">Projected End Date:</label><input type="text" name="projected_date" id="projected_date' . $i .'" class="date_picker readonly" value="' .$end .'" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')"></div>
							<div class="dim"></div>
							<div style="clear: both"></div>
						</form>
					</li>';
				} else {
					$get_timeline = self::getQuery("SELECT * FROM `project_phases` WHERE `phase_type`='" 
					.$phases[$i]['id'] ."' AND `project_id`='$project_id'");
					
					if(sizeof($get_timeline) > 0) {
						$start_part = explode(" ", $get_timeline[0]['start_date']);
						$start_date = explode("-", $start_part[0]);
						$end_part = explode(" ", $get_timeline[0]['projected_end_date']);
						$end_date = explode("-", $end_part[0]);
						
						if($start_date[0] != '0000') {
							$start = $start_date[1] ."/" .$start_date[2] ."/" .$start_date[0];
						} else {
							$start = "";
						}
						if($end_date[0] != '0000') {
							$end = $end_date[1] ."/" .$end_date[2] ."/" .$end_date[0];
						} else {
							$end = "";
						}
					} else {
						$start = "";
						$end = "";
					}
					
					if(sizeof($timeline_link) < 1) {
						$html .= '<li' .$nameIt .'>
							<form action="" method="post">
								<input type="hidden" name="phase" id="phase" value="' .$phases[$i]['id'] .'" />
								<div class="ptimeline_phase"><label for="phase">' .$phases[$i]['name'] .'</label></div>
								<div class="ptimeline_startdate"><label for="start_date">Start Date:</label><input type="text" name="start_date" id="start_date' . $i .'" class="date_picker readonly" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')" value="' .$start .'"></div>
								<div class="ptimeline_enddate"><label for="projected_date">Projected End Date:</label><input type="text" name="projected_date" id="projected_date' . $i .'" class="date_picker readonly" value="' .$end .'" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')"></div>
								<div class="dim"></div>
								<div style="clear: both"></div>
							</form>
						</li>';
					} else {
						$html .= '<li' .$nameIt .' style="display: none;">
						<form action="" method="post">
							<input type="hidden" name="phase" id="phase" value="' .$phases[$i]['id'] .'" />
							<div class="ptimeline_phase"><label for="phase">' .$phases[$i]['name'] .'</label></div>
							<div class="ptimeline_startdate"><label for="start_date">Start Date:</label><input type="text" name="start_date" id="start_date' . $i .'" class="date_picker readonly" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')"></div>
							<div class="ptimeline_enddate"><label for="projected_date">Projected End Date:</label><input type="text" name="projected_date" id="projected_date' . $i .'" class="date_picker readonly" onChange="checkDateRange(\'start_date' . $i .'\', \'projected_date' . $i .'\')"></div>
							<div class="dim"></div>
							<div style="clear: both"></div>
						</form>
					</li>';
					}
				}
			}
			$html .= '</ul>';
			
			return $html;
		}

		public function getPhases($project_id){
			
			$phaseSql = "SELECT lppt.id phase_id, lppt.name name, 'phase' phase_type FROM project_phase_finance ppf, lnk_project_phase_types lppt WHERE ppf.active = lppt.active AND ppf.deleted = lppt.deleted AND lppt.active = '1' AND lppt.deleted = '0' AND ppf.phase = lppt.id AND lppt.finance_flag = '1' AND ppf.project_id = '" . $project_id . "' ORDER BY sort_order ";
			$phaseResult = self::getQuery($phaseSql);
			$result = array();
			foreach($phaseResult as $phase){
				$result[$phase['phase_id']]['name'] = $phase['name'];
				$result[$phase['phase_id']]['type'] = $phase['phase_type'];
				$subPhaseSql = "SELECT lpspt.id id, lpspt.name name, 'subphase' phase_type FROM project_sub_phase_finance pspf, lnk_project_sub_phase_types lpspt WHERE pspf.active = lpspt.active AND pspf.phase = lpspt.phase_id AND pspf.sub_phase = lpspt.id AND pspf.phase = '" . $phase['phase_id'] . "' AND pspf.project_id = '" . $project_id . "'";
				$subPhaseResult = self::getQuery($subPhaseSql);

				foreach($subPhaseResult as $subphase){
					$result[$phase['phase_id']]['subphase'][$subphase['id']]['name'] = $subphase['name'];
					$result[$phase['phase_id']]['subphase'][$subphase['id']]['type'] = $subphase['phase_type'];
				}
			}
			return $result;
		}

		public function getResourceHTML($project_id) {
			$html = "";
			$users = self::getQuery("SELECT DISTINCT a.`id`, a.* FROM `users` a, `resource_blocks` b WHERE a.`id` = b.`userid` AND b.`projectid`='" .$project_id ."'");
			
//			$subPhase = self::getQuery("select a.* from lnk_project_sub_phase_types a,project_sub_phase_finance b where a.active='1' and b.project_id='" .$project_id ."' and a.id = b.sub_phase order by a.phase_id ");

			$phaseList = self::getPhases($project_id);
			for($i = 0; $i < sizeof($users); $i++) {
				$total_actual = 0;
				$total_booked = 0;
				$resources = self::getQuery("SELECT * FROM `resource_blocks` WHERE `projectid`='" .$project_id ."' AND `userid`='" .$users[$i]['id'] ."'");
				for($r = 0; $r < sizeof($resources); $r++) {
					if($resources[$r]['status'] == 4) {
						if($resources[$r]['daypart'] == 9){
							$total_actual += $resources[$r]['hours'];
						}else{
							$total_actual += 1;
						}
					}
					if($resources[$r]['status'] == 3) {
						$total_booked += 1;
					}
				}
				$phase_id = 0;
				$phase_select = '';
				$subphase_select = '';
				$phasehtml = '';
				$userRole = self::getQuery("SELECT * FROM `user_project_role` WHERE `user_id`='" .$users[$i]['id'] ."' AND `project_id`='" .$project_id ."' LIMIT 1");

				$phasehtml .= '<select name="subphase_filter" id="subphase_filter" style="' .$hideStyle .'">
							<option value="0" style="font-weight:normal;">Resource Type</option>';
				foreach($phaseList as $phase => $phaseValue){
					if($userRole[0]['flag'] == 'phase' && $userRole[0]['phase_subphase_id'] == $phase){
						$phase_select = ' SELECTED';
					}else{
						$phase_select = '';
					}
					if(array_key_exists('subphase', $phaseValue)){
						$phasehtml .= '<optgroup label="' . $phaseValue['name'] .'">';
						foreach($phaseValue['subphase'] as $subphase => $spValue){
							if($userRole[0]['flag'] == 'subphase' && $userRole[0]['phase_subphase_id'] == $subphase){
								$subphase_select = ' SELECTED';
							}else{
								$subphase_select = '';
							}
							$phasehtml .= '<option value="' . $subphase .'-'. $spValue['type'] .'" ' . $subphase_select . '>' . $spValue['name'] .' </option>';
						}
						$phasehtml .= '</optgroup>';
					}else{
						$phasehtml .= '<option value="' . $phase .'-'. $phaseValue['type'] .'"' . $phase_select . '>' . $phaseValue['name'] .' </option>';
					}
				}
				$phasehtml .= '</select>';
//				for($k = 0; $k < sizeof($subPhase); $k++) {
//					if($subPhase[$k]['phase_id'] != $phase_id){
//						$optTag = self::getQuery("SELECT name from lnk_project_phase_types WHERE id='" . $subPhase[$k]['phase_id'] . "' LIMIT 1");
//						$phase_id = $subPhase[$k]['phase_id'];
//						$subPhasehtml .= '<optgroup label="' .$optTag[0]['name'] .'">';
//					}
//					$userSubPhase = self::getQuery("SELECT * FROM `user_project_sub_phase` WHERE `sub_phase_id`='" .$subPhase[$k]['id'] ."' AND `user_id`='" .$users[$i]['id'] ."' AND `project_id`='" .$project_id ."'");
//					if(sizeof($userSubPhase) == 1) {
//						if($users[$i]['id'] == $userSubPhase[0]['user_id']) {
//							$selected = " SELECTED";
//						} else {
//							$selected = "";
//						}
//					} else {
//						$selected = "";
//					}
//					$subPhasehtml .= '<option value="' .$subPhase[$k]['id'] .'"' .$selected .'>' .$subPhase[$k]['name'] .' </option>';
//					$nxt = $k+1;
//					if($subPhase[$nxt]['phase_id'] != $phase_id) {
//						$subPhasehtml .= '</optgroup>';
//					}
//				}
//				$subPhasehtml .= '</optgroup></select>';
				$html .= '<li id="user'.$users[$i]['id'] .'">';
				$html .= '<div class="resource_row" >
					<input type="hidden" name="userid" id="userid" value="' .$users[$i]['id'] .'" />
					<div class="resource_subphase"><span>'.$phasehtml.'</span></div>
					<div class="resource_name" onClick="window.location = \'/resourceplanner/?userid=' .$users[$i]['id'] .'\';">' .ucfirst($users[$i]['first_name']) .' ' .ucfirst($users[$i]['last_name']) .'</div>
					<div class="resource_hours"><span>Actual Hours:</span>' .$total_actual .' hours</div>
					<div class="resource_hours booked_hours"><span>Scheduled Hours:</span>' .$total_booked .' hours</div>
				</div></li>';
				
			}
			
			return $html;
		}

		public function getSubPhaseHTML($fin, $subPhase, $project='', $phaseHours='', $phaseRate='', $phaseTotal=''){

			$sub_phase_list = '';
			$sp_html = '';
			$html = '';
			$totalHours = 0;
			$totalTotal = 0;
			$sql = "SELECT * FROM project_sub_phase_finance where deleted='0' AND active='1' AND phase='" . $fin['id'] . "' AND project_id='$project'";
			foreach($subPhase as $sub){
				$sp_details[$sub['id']]['name'] = $sub['name'];
			}

			$projSubPhase = self::getQuery($sql);
			foreach($projSubPhase as $phase){

				if($phase['hours'] > 0) {
					$hours = $phase['hours'];
					$totalHours += $hours;
				} else {
					$hours = "";
				}
				
				if($phase['rate'] > 0) {
					$rate = $phase['rate'];
				} else {
					$rate = "";
				}
				
				if($phase['hours'] > 0 || $phase['rate'] > 0) {
					$total = ($phase['hours'] * $phase['rate']);
					$totalTotal += $total;
				} else {
					$total = "";
				}

				$sub_phase_list .= $phase['sub_phase'] . '~';
				$onChange = 'calcSubPhaseFinance(\'' . $fin['id'] . '\', \'' . $phase['sub_phase'] . '\', fin_' . $fin['id'] . ',budget)';
				$sp_html .= '<dt id="sub_phase_' . $phase['sub_phase'] .'">
						<div class="finance_budget_name" onmouseover="$(\'#sp_remove_' . $phase['sub_phase'] .'\').css({display:\'block\'});" onmouseout="$(\'#sp_remove_' . $phase['sub_phase'] .'\').css({display:\'none\'});">
							<span> - ' . $sp_details[$phase['sub_phase']]['name'] .'</span>
							<span class="sp_remove" id="sp_remove_' . $phase['sub_phase'] .'" onClick="remove_subPhase(\'' . $phase['sub_phase'] .'\', \'' .$fin['id'] .'\', \'' . $project . '\');">Remove</span>
						</div>
						<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="sub_hours" id="sub_hours" onChange="'.$onChange.'" value="' . $hours .'"/></div>
						<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="sub_rate" id="sub_rate" onChange="'.$onChange.'" value="' . $rate .'"/></div>
						<div class="finance_budget_total"><span>Total:</span><input type="text" name="subtotal" id="subtotal" class="readonly" readonly value="' . $total .'"/></div>
						<div class="dim" style="display: block"></div>
						<div style="clear: both"></div>
					</dt>';
			}
			if('' == $sp_html){
				$phaseHrChange = 'onChange="calcFinance(fin_' . $fin['id'] .',budget);"';
				$phaseRateHTML = '<span>Rate:</span><input type="text" name="rate" id="rate" value="'.$phaseRate.'" onChange="calcFinance(fin_' . $fin['id'] .',budget);" />';
				$totalHours = $phaseHours;
				$totalTotal = $phaseTotal;
				$readonly = '';
				$readonly_class = '';
			}else{
				$phaseHrChange = '';
				$phaseRateHTML = '';
				$readonly = ' readonly="readonly" ';
				$readonly_class = ' class="readonly" ';
			}

			$html .= '<input type="hidden" name="phase" id="phase" value="' .$fin['id'] .'" />
				<div class="finance_budget_name"><span>' .$fin['name'] .':</span></div>
				<div class="finance_budget_hours"><span>' . $fin['desc'] .' Hours:</span><input type="text" name="hours" id="hours" value="'.$totalHours.'" ' . $phaseHrChange . $readonly . $readonly_class . '/></div>
				<div class="finance_budget_rate">' . $phaseRateHTML . '</div>
				<div class="finance_budget_total"><span>' . $fin['desc'] .' Total:</span><input type="text" name="total" id="total" class="readonly" readonly value="'.$totalTotal.'"/></div>
				<div class="dim" style="display: block"></div>
				<div style="clear: both"></div>';
			$html .= '<div>
					<dl id="phase_' .$fin['id'] .'" class="sub_phases_dl">';

			$html .= $sp_html;

			$sub_phase_list = substr($sub_phase_list, 0, -1);
			$html .= '<input type="hidden" name="sub_phase_list_' .$fin['id'] .'" id="sub_phase_list_' .$fin['id'] .'" value="' . $sub_phase_list .'" />';

			$html .= '	</dl>
					<div id="add_subphase_' . $fin['id'] . '" class="add_subphase" onClick="showSubPhase(\'' . $project . '\',\'' . $fin['id'] . '\');"> - Add </div>
				</div>';
			return $html;
		}
		
		public function getFinanceHTML(){
			$html = '';
			$fin = self::getQuery("SELECT * FROM `lnk_project_phase_types` WHERE `finance_flag`='1' AND `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC");
			
			$html .= '<ul class="finance_budget">
				<li style="display: none;"><form></form></li>';
			for($i = 0; $i < sizeof($fin); $i++) {
				$timeline_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `phase_id`='" .$fin[$i]['id'] ."' LIMIT 1");
				if(sizeof($timeline_link) == 1) {
					$nameIt = ' id="finance_' .$timeline_link[0]['resource_id'] .'"';
					$nameItLi = ' id="finance_li_' .$timeline_link[0]['resource_id'] .'"';
				} else {
					$nameIt = '';
					$nameItLi = '';
				}

				$subPhase = self::getQuery("SELECT * FROM lnk_project_sub_phase_types WHERE phase_id='" . $fin[$i]['id'] . "'");
				$subPhaseCount = count($subPhase);

				$html .= '<li' .$nameItLi .'>
					<div class="finance_budget"' .$nameIt .'>
					<form action="" method="post" name="fin_' .$fin[$i]['id'] .'">';

				if($subPhaseCount > 0){
					$html .= self::getSubPhaseHTML($fin[$i], $subPhase, '');
				}else{
					$html .= '<input type="hidden" name="phase" id="phase" value="' .$fin[$i]['id'] .'" />
						<div class="finance_budget_name"><span>' .$fin[$i]['name'] .':</span></div>
						<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="hours" id="hours" onChange="calcFinance(fin_' .$fin[$i]['id'] .',budget)" /></div>
						<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="rate" id="rate" onChange="calcFinance(fin_' .$fin[$i]['id'] .',budget)" /></div>
						<div class="finance_budget_total"><span>Total:</span><input type="text" name="total" id="total" class="readonly" readonly /></div>
						<div class="dim" style="display: block"></div>
						<div style="clear: both"></div>';
				}
				$html .= '</form>
				</div>
				</li>';
			}
			$html .= '</ul>';
			
			return $html;
		}

		public function getFinanceEditHTML($project_id) {
			$html = '';
			$fin = self::getQuery("SELECT * FROM `lnk_project_phase_types` WHERE `finance_flag`='1' AND `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC");
			
			$html .= '<ul class="finance_budget">
				<li style="display: none;"><form></form></li>';
			for($i = 0; $i < sizeof($fin); $i++) {
				$timeline_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `phase_id`='" .$fin[$i]['id'] ."' LIMIT 1");
				$finance_data = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `phase`='" .$fin[$i]['id'] ."' AND `project_id`='$project_id' LIMIT 1");

				$subPhase = self::getQuery("SELECT * FROM lnk_project_sub_phase_types WHERE phase_id='" . $fin[$i]['id'] . "'");
				$subPhaseCount = count($subPhase);
				
				if(sizeof($finance_data) == 1) {
					if(sizeof($timeline_link) == 1) {
						$nameIt = ' id="finance_' .$timeline_link[0]['resource_id'] .'"';
						$nameItLi = ' id="finance_li_' .$timeline_link[0]['resource_id'] .'"';
					} else {
						$nameIt = '';
						$nameItLi = '';
					}
					
					if($finance_data[0]['hours'] > 0) {
						$hours = $finance_data[0]['hours'];
					} else {
						$hours = "";
					}
					
					if($finance_data[0]['rate'] > 0) {
						$rate = $finance_data[0]['rate'];
					} else {
						$rate = "";
					}
					
					if($finance_data[0]['hours'] > 0 || $finance_data[0]['rate'] > 0) {
						$total = ($finance_data[0]['hours'] * $finance_data[0]['rate']);
					} else {
						$total = "";
					}
					
					$html .= '<li' .$nameItLi .'>
						<div class="finance_budget"' .$nameIt .'>
						<form action="" method="post" name="fin_' .$fin[$i]['id'] .'">';
					
					if($subPhaseCount > 0){
						$html .= self::getSubPhaseHTML($fin[$i], $subPhase, $project_id, $hours, $rate, $total);
					}else{
						$html .= '	<input type="hidden" name="phase" id="phase" value="' .$fin[$i]['id'] .'" />
							<div class="finance_budget_name"><span>' .$fin[$i]['name'] .':</span></div>
							<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="hours" id="hours" value="' .$hours .'" onChange="calcFinance(fin_' .$fin[$i]['id'] .',budget)" /></div>
							<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="rate" id="rate" value="' .$rate .'" onChange="calcFinance(fin_' .$fin[$i]['id'] .',budget)" /></div>
							<div class="finance_budget_total"><span>Total:</span><input type="text" name="total" id="total" value="' .$total .'" class="readonly" readonly /></div>
							<div class="dim" style="display: block"></div>
							<div style="clear: both"></div>';
					}
					$html .= '</form>
					</div>
					</li>';
				} else {
					if(sizeof($timeline_link) == 1) {
						$nameIt = ' id="finance_' .$timeline_link[0]['resource_id'] .'"';
						$nameItLi = ' id="finance_li_' .$timeline_link[0]['resource_id'] .'"';
						$display = ' style="display: none;"';
					} else {
						$nameIt = '';
						$nameItLi = '';
						$display = ' style="display: none;"';
					}
					
					$html .= '<li' .$nameItLi .$display .'>
					<div class="finance_budget"' .$nameIt .$display .'>
					<form action="" method="post" name="fin_' .$fin[$i]['id'] .'">';

					if($subPhaseCount > 0){
						$html .= self::getSubPhaseHTML($fin[$i], $subPhase, $project_id);
					}else{
						$html .= '	<input type="hidden" name="phase" id="phase" value="' .$fin[$i]['id'] .'" />
							<div class="finance_budget_name"><span>' .$fin[$i]['name'] .':</span></div>
							<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="hours" id="hours" onChange="calcFinance(fin_' .$fin[$i]['id'] .',budget)" /></div>
							<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="rate" id="rate" onChange="calcFinance(fin_' .$fin[$i]['id'] .',budget)" /></div>
							<div class="finance_budget_total"><span>Total:</span><input type="text" name="total" id="total" class="readonly" readonly /></div>
							<div class="dim" style="display: block"></div>
							<div style="clear: both"></div>';
					}
					$html .= '</form>
				</div>
				</li>';
				}
			}
			$html .= '</ul>';
			
			return $html;
		}

		public function getBudgetEditHTML($project_id) {
			$quarter1ReadOnly = "";
			$quarter2ReadOnly = "";
			$quarter3ReadOnly = "";
			$currentDate = date("n/d/Y");
			$datePart = @explode("/", $currentDate);
			if($datePart[0] > 3){
//				$quarter1ReadOnly = ' class="readonly" readonly="true" ';
				//$quarter1ReadOnly = '';
			}
			if($datePart[0] > 6){
				$quarter2ReadOnly = ' class="readonly" readonly="true" ';
			}
			if($datePart[0] > 9){
				$quarter3ReadOnly = ' class="readonly" readonly="true" ';
			}

			if($_SESSION['login_status'] == "client") {
				$quarter1ReadOnly = ' class="readonly" readonly="true" ';
				$quarter2ReadOnly = ' class="readonly" readonly="true" ';
				$quarter3ReadOnly = ' class="readonly" readonly="true" ';
				$quarter4ReadOnly = ' class="readonly" readonly="true" ';
			} else {
				$quarter1ReadOnly = "";
				$quarter2ReadOnly = "";
				$quarter3ReadOnly = "";
				$quarter4ReadOnly = "";
			}

			$html = '';
			$total_budget = 0;
			$quarter1_budget = 0;
			$quarter2_budget = 0;
			$quarter3_budget = 0;
			$quarter4_budget = 0;
			$budget_data = self::getQuery("SELECT * FROM project_budget WHERE project_id='$project_id' LIMIT 1");
			if(sizeof($budget_data) == 1){
				$total_budget = $budget_data[0]['total_budget'];
				$quarter1_budget = $budget_data[0]['quarter1_budget'];
				$quarter2_budget = $budget_data[0]['quarter2_budget'];
				$quarter3_budget = $budget_data[0]['quarter3_budget'];
				$quarter4_budget = $budget_data[0]['quarter4_budget'];
			}
			$unallocated_budget = $total_budget-($quarter1_budget+$quarter2_budget+$quarter3_budget+$quarter4_budget);
			if($total_budget > 0) {
				$quarter1_percentage = round((($quarter1_budget/$total_budget)*100)*10)/10;
				$quarter2_percentage = round((($quarter2_budget/$total_budget)*100)*10)/10;
				$quarter3_percentage = round((($quarter3_budget/$total_budget)*100)*10)/10;
				$quarter4_percentage = round((($quarter4_budget/$total_budget)*100)*10)/10;
			}else{
				$quarter1_percentage = 0;
				$quarter2_percentage = 0;
				$quarter3_percentage = 0;
				$quarter4_percentage = 0;
			}
			$html .= '<ul class="finance_complete_budget">
				<li style="display: none;"><form></form></li>';

			$html .= '<li>
			<form action="" method="post" name="budget">
				<div class="finance_budget"' .$nameIt .'>
					<div class="finance_total_budget"><span>Total Budget</span></div>
					<div class="finance_total_budget"><span>Unallocated Budget</span></div>
					<div class="finance_quarter_budget" align="right"><span>Quarter 1</span></div>
					<div class="finance_quarter_budget" align="right"><span>Quarter 2</span></div>
					<div class="finance_quarter_budget" align="right"><span>Quarter 3</span></div>
					<div class="finance_quarter_budget" align="right"><span>Quarter 4</span></div>
					<div class="dim" style="display: block"></div>
					<div style="clear: both"></div>
					
				</div>
				<div class="finance_budget"' .$nameIt .'>
					<div class="finance_quarter_budget"><span></span></div>
					<div class="finance_quarter_budget"><span></span></div>
					<div class="finance_quarter_budget" align="right"><span>%</span><input type="text" name="percentage1" id="percentage1" value="'.$quarter1_percentage.'" onChange="calcBudgetPercentage(budget)" '.$quarter1ReadOnly.'/></div>
					<div class="finance_quarter_budget" align="right"><span>%</span><input type="text" name="percentage2" id="percentage2" value="'.$quarter2_percentage.'" onChange="calcBudgetPercentage(budget)" '.$quarter2ReadOnly.'/></div>
					<div class="finance_quarter_budget" align="right"><span>%</span><input type="text" name="percentage3" id="percentage3" value="'.$quarter3_percentage.'" onChange="calcBudgetPercentage(budget)" '.$quarter3ReadOnly.'/></div>
					<div class="finance_quarter_budget" align="right"><span>%</span><input type="text" name="percentage4" id="percentage4" value="'.$quarter4_percentage.'" onChange="calcBudgetPercentage(budget)" '.$quarter4ReadOnly.' /></div>
					<div class="dim" style="display: block"></div>
					<div style="clear: both"></div>
					
				</div>
				<div class="finance_budget"' .$nameIt .'>
					<div class="finance_quarter_budget"><span>$</span><input class="readonly" readonly type="text" name="totalBudget" id="totalBudget" value="' .$total_budget .'" onChange="calcBudget(budget)" /></div>
					<div class="finance_quarter_budget"><span>$</span><input type="text" name="unallocated" id="unallocated" value="' .$unallocated_budget .'" readonly="true" class="readonly" /></div>
					<div class="finance_quarter_budget" align="right"><span>$</span><input type="text" name="quarter1" id="quarter1" value="'.$quarter1_budget.'" "'.$quarter1ReadOnly.'" onChange="calcBudget(budget)" /></div>
					<div class="finance_quarter_budget" align="right"><span>$</span><input type="text" name="quarter2" id="quarter2" value="'.$quarter2_budget.'" "'.$quarter2ReadOnly.'" onChange="calcBudget(budget)"  /></div>
					<div class="finance_quarter_budget" align="right"><span>$</span><input type="text" name="quarter3" id="quarter3" value="'.$quarter3_budget.'" "'.$quarter3ReadOnly.'" onChange="calcBudget(budget)" /></div>
					<div class="finance_quarter_budget" align="right"><span>$</span><input type="text" name="quarter4" id="quarter4" value="'.$quarter4_budget.'" onChange="calcBudget(budget)" "'.$quarter4ReadOnly.'" /></div>
					<div class="dim" style="display: block"></div>
					<div style="clear: both"></div>
					
				</div>
			</form>
			</li>';

			$html .= '</ul>';
			
			return $html;
		}

		public function getFinanceTotalEdit($project_id) {
			$finance_data = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `project_id`='$project_id'");
			$overall_total = 0.00;
			for($i = 0; $i < sizeof($finance_data); $i++) {
				$overall_total += ($finance_data[$i]['hours'] * $finance_data[$i]['rate']);
			}
			$sub_finance_data = self::getQuery("SELECT * FROM `project_sub_phase_finance` WHERE `active`='1' and `deleted`='0' AND  `project_id`='$project_id'");
			for($i = 0; $i < sizeof($sub_finance_data); $i++) {
				$overall_total += ($sub_finance_data[$i]['hours'] * $sub_finance_data[$i]['rate']);
			}
			return $overall_total;
		}
		public function getApprovalsHTML() {
			$html = '';
			$appr = self::getQuery("SELECT * FROM `lnk_project_phase_types` WHERE `approval_flag`='1' AND `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC");
			
			$html .= '<ul class="papprovals"">';
			
			for($i = 0; $i < sizeof($appr); $i++) {
				$approval_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `phase_id`='" .$appr[$i]['id'] ."' LIMIT 1");
				$html .= '<li id="approvals_' .$approval_link[0]['resource_id'] .'">
					<form action="" method="post" name="appr_' .$appr[$i]['id'] .'" id="appr_' .$appr[$i]['id'] .'">
						<input type="hidden" name="phase" id="phase" value="' .$appr[$i]['id'] .'" />
						<div class="papprovals_phase"><label for="phase">' .$appr[$i]['name'] .'</label></div>
						<div class="papprovals_name">
							<input type="text" name="user_name" id="user_name" />&nbsp;
						</div>
						<div class="papprovals_title">
							<input type="text" name="user_title" id="user_title" value="--title--" onFocus="clearOnFocus(this);" />&nbsp;
						</div>
						<div class="papprovals_phone">
							<input type="text" name="user_phone" id="user_phone" value="--phone--" onFocus="clearOnFocus(this);" />&nbsp;
						</div>
						<div class="papprovals_approved">
							<label for="approved">Approved</label>&nbsp;
							<input type="checkbox" name="approved" id="approved" />&nbsp;
						</div>
						<div class="papprovals_date">
							<input type="text" class="date_picker readonly" name="approval_date" id="approval_date_' .$appr[$i]['id'] . '" readonly />&nbsp;
						</div>
						<div style="clear: both"></div>
					</form>
				</li>';
			}
					
			return $html;
		}
		public function getApprovalsEditHTML($project_id) {
			$html = '';
			$appr = self::getQuery("SELECT * FROM `lnk_project_phase_types` WHERE `approval_flag`='1' AND `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC");
			
			$html .= '<ul class="papprovals"">';
			
			for($i = 0; $i < sizeof($appr); $i++) {
				$approval_link = self::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `phase_id`='" .$appr[$i]['id'] ."' LIMIT 1");
				$project_appr = CtDisplay::getQuery("SELECT * FROM `project_phase_approvals` WHERE `project_id`='$project_id' AND `project_phase`='" .$appr[$i]['id'] ."' LIMIT 1");
				$user_list = CtDisplay::getQuery(QRY_USERS_ASC);
				
				if(sizeof($project_appr) == 1) {
					$is_role = self::getQuery("SELECT * FROM `project_roles` WHERE `resource_type_id`='" .$approval_link[0]['resource_id'] ."' AND `project_id`='$project_id'");
					
					if(sizeof($is_role) > 0) {
						$appr_nbc_name  = $project_appr[0]['name'];
						$appr_nbc_title  = $project_appr[0]['title'];
						$appr_nbc_phone  = $project_appr[0]['phone'];
						$appr_nbc_date  = $project_appr[0]['approval_date'];
						$appr_nbc_approval  = $project_appr[0]['approved'];
						
						if(empty($appr_nbc_title)) {
							$appr_nbc_title = "--title--";
						}
						if(empty($appr_nbc_phone)) {
							$appr_nbc_phone = "--phone--";
						}
						
						$appr_date_part = explode(" ", $appr_nbc_date);
						$appr_date = explode("-", $appr_date_part[0]);
						
						if($appr_date[0] != '0000') {
							$date = $appr_date[1] . "/" .$appr_date[2] ."/" .$appr_date[0];
						} else {
							$date = "";
						}
						
						if($appr_nbc_approval != 0) {
							$checked = " CHECKED";
						} else {
							$checked = "";
						}
						if(!empty($appr_nbc_name)) {
							$preSel = "yes";
						} else {
							$preSel = "no";
						}
						
						$html .= '<li id="approvals_' .$approval_link[0]['resource_id'] .'">
							<form action="" method="post" name="appr_' .$appr[$i]['id'] .'" id="appr_' .$appr[$i]['id'] .'">
								<input type="hidden" name="phase" id="phase" value="' .$appr[$i]['id'] .'" />
								<input type="hidden" name="preselect" id="preselect" value="' .$preSel .'" />
								<div class="papprovals_phase"><label for="phase">' .$appr[$i]['name'] .'</label></div>
								<div class="papprovals_name">
									<!--<input type="text" name="user_name" id="user_name" value="' .$appr_nbc_name .'" />&nbsp;-->
									<select name="user_name" id="user_name">
										<option value="">--Select User--</option>';
										for($u = 0; $u < sizeof($user_list); $u++) {
											if($appr_nbc_name == $user_list[$u]['id']) {
												$selected = " SELECTED";
											} else {
												$selected = "";
											}
											$html .= '<option value="' .$user_list[$u]['id'] .'"' .$selected .'>' .ucfirst($user_list[$u]['first_name']) .' ' .ucfirst($user_list[$u]['last_name']) .'</option>';
										}
									$html .= '</select>&nbsp;
								</div>
								<div class="papprovals_title">
									<input type="text" name="user_title" id="user_title" value="' .$appr_nbc_title .'" onFocus="clearOnFocus(this);" />&nbsp;
								</div>
								<div class="papprovals_phone">
									<input type="text" name="user_phone" id="user_phone" value="' .$appr_nbc_phone .'" onFocus="clearOnFocus(this);" />&nbsp;
								</div>
								<div class="papprovals_approved">
									<label for="approved">Approved</label>&nbsp;
									<input type="checkbox" name="approved" id="approved"' .$checked .' />&nbsp;
								</div>
								<div class="papprovals_date">
									<input type="text" class="date_picker readonly" name="approval_date" id="approval_date_' .$appr[$i]['id'] .'" value="' .$date .'" readonly />&nbsp;
								</div>
								<div style="clear: both"></div>
							</form>
						</li>';
					} else {
						$html .= '<li id="approvals_' .$approval_link[0]['resource_id'] .'" style="display: none;">
							<form action="" method="post" name="appr_' .$appr[$i]['id'] .'" id="appr_' .$appr[$i]['id'] .'">
								<input type="hidden" name="phase" id="phase" value="' .$appr[$i]['id'] .'" />
								<div class="papprovals_phase"><label for="phase">' .$appr[$i]['name'] .'</label></div>
								<div class="papprovals_name">
									<!--<input type="text" name="user_name" id="user_name" value="" />&nbsp;-->
									<select name="user_name" id="user_name">
										<option value="">--Select User--</option>';
										for($u = 0; $u < sizeof($user_list); $u++) {
											$html .= '<option value="' .$user_list[$u]['id'] .'">' .ucfirst($user_list[$u]['first_name']) .' ' .ucfirst($user_list[$u]['last_name']) .'</option>';
										}
									$html .= '</select>&nbsp;
								</div>
								<div class="papprovals_title">
									<input type="text" name="user_title" id="user_title" value="--title--" onFocus="clearOnFocus(this);" />&nbsp;
								</div>
								<div class="papprovals_phone">
									<input type="text" name="user_phone" id="user_phone" value="--phone--" onFocus="clearOnFocus(this);" />&nbsp;
								</div>
								<div class="papprovals_approved">
									<label for="approved">Approved</label>&nbsp;
									<input type="checkbox" name="approved" id="approved" />&nbsp;
								</div>
								<div class="papprovals_date">
									<input type="text" class="date_picker readonly" name="approval_date" id="approval_date_' .$appr[$i]['id'] .'" value="" readonly />&nbsp;
								</div>
								<div style="clear: both"></div>
							</form>
						</li>';
					}
				} else {
					$html .= '<li id="approvals_' .$approval_link[0]['resource_id'] .'">
							<form action="" method="post" name="appr_' .$appr[$i]['id'] .'" id="appr_' .$appr[$i]['id'] .'">
								<input type="hidden" name="phase" id="phase" value="' .$appr[$i]['id'] .'" />
								<div class="papprovals_phase"><label for="phase">' .$appr[$i]['name'] .'</label></div>
								<div class="papprovals_name">
									<!--<input type="text" name="user_name" id="user_name" value="" />&nbsp;-->
									<select name="user_name" id="user_name">
										<option value="">--Select User--</option>';
										for($u = 0; $u < sizeof($user_list); $u++) {
											$html .= '<option value="' .$user_list[$u]['id'] .'">' .ucfirst($user_list[$u]['first_name']) .' ' .ucfirst($user_list[$u]['last_name']) .'</option>';
										}
									$html .= '</select>&nbsp;
								</div>
								<div class="papprovals_title">
									<input type="text" name="user_title" id="user_title" value="--title--" onFocus="clearOnFocus(this);" />&nbsp;
								</div>
								<div class="papprovals_phone">
									<input type="text" name="user_phone" id="user_phone" value="--phone--" onFocus="clearOnFocus(this);" />&nbsp;
								</div>
								<div class="papprovals_approved">
									<label for="approved">Approved</label>&nbsp;
									<input type="checkbox" name="approved" id="approved" />&nbsp;
								</div>
								<div class="papprovals_date">
									<input type="text" class="date_picker readonly" name="approval_date" id="approval_date_' .$appr[$i]['id'] .'" value="" readonly />&nbsp;
								</div>
								<div style="clear: both"></div>
							</form>
						</li>';
				}
			}
			$html .= "</ul>";
			
			return $html;
		}
		public function getProjectCompleteness($project_id) {
			$sections = self::getQuery("SELECT * FROM `lnk_project_brief_section_types`");
			$num_briefs = sizeof($sections);
			$num_complete = 0;
			
			for($i = 0; $i < sizeof($sections); $i++) {
				$section = self::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='$project_id' AND `flag`='3' AND `section_type`='" .$sections[$i]['id'] ."'");
				
				if(sizeof($section) == 1) {
					$num_complete++;
				}
			}
			
			if($num_briefs > 0) {
				$calc = (($num_complete/$num_briefs)*100);
			} else {
				$calc = 0;
			}
			
			return $calc;
		}
		
		public function workorderPermissions($projectId = NULL) {
			if($projectId != NULL) {
				$list = Array();
				$client = self::getQuery("SELECT a.`id`,a.`name` FROM `companies` a, `projects` b WHERE b.`id`='$projectId' AND a.`id`=b.`company` LIMIT 1");
				$companies = self::getQuery("SELECT * FROM `companies` ORDER BY `name` ASC");
				
				$html = '<ul class="wo_perms" id="perms_list">';
				
				$html .= '<li class="static_perm">
							<div class="wo_perms_company">
								' .substr($client[0]['name'], 0, 30) .'
							</div>
							<div class="wo_perms_users">
								All Users
							</div>
							<div style="clear: both;"></div>
					</li>';
					
					$get_perms = self::getQuery("SELECT * FROM `user_project_permissions` WHERE `project_id`='$projectId'");
					
					if(sizeof($get_perms) > 0) {
						for($i = 0; $i < sizeof($get_perms); $i++) {
							$get_user = self::getQuery("SELECT * FROM `users` WHERE `id`='" .$get_perms[$i]['user_id'] ."' AND `company`!='" .$client[0]['id'] ."' AND `deleted`='0' LIMIT 1");
							
							if(sizeof($get_user) > 0) {
								$list[$get_user[0]['company']][$get_user[0]['id']] = true;
							}
						}
					}
					
					$comp_keys = array_keys($list);
					for($i = 0; $i < sizeof($comp_keys); $i++) {
						$get_comp = self::getQuery("SELECT * FROM `companies` WHERE `id`='" .$comp_keys[$i]."' LIMIT 1");
						
						$html .= '<li class="static_perm">
							<div class="wo_perms_company">
								' .ucfirst($get_comp[0]['name']) .'
							</div>
							<div class="wo_perms_users">
								<select id="edit_' .$i .'" name="edit_' .$i .'[]" multiple="multiple" size="5">
									<option value=""></option>';
						
						$get_user_list = "SELECT * FROM `users` WHERE `company`='" .$comp_keys[$i] ."' AND `deleted`='0'";
						$res_user_list = self::getQuery($get_user_list);
						
						for($x = 0; $x < sizeof($res_user_list); $x++) {
							if(isset($list[$comp_keys[$i]][$res_user_list[$x]['id']])) {
								$selected = " SELECTED ";
							} else {
								$selected = "";
							}
							
							$html .= '<option value="' .$res_user_list[$x]['id'] .'" ' .$selected .'>' .ucfirst($res_user_list[$x]['first_name']) .' ' .ucfirst($res_user_list[$x]['last_name']) .'</option>';
						}
						
						$html .= '</select>
								<button class="remove_perms" name="save" id="company_' .$comp_keys[$i] .'" onClick="deletePerm(\'company_' .$comp_keys[$i] .'\');changeSectionStatusMan(\'sec_11\'); setCompleteness();return false;">&nbsp;</button>	
								</div>
								<div style="clear: both;"></div>
							</li>';
					}
					
				$html .= '</ul>
				<ul class="wo_perms">';
				
				$html .= '<li class="woPermsAdd">
							<div class="wo_perms_company">
								<select name="companyPerms" id="companyPerms">
									<option value="">--Select Company--</option>';
									
									for($i = 0; $i < sizeof($companies); $i++) {
										$html .= '<option value="' .$companies[$i]['id'] .'">' .ucfirst($companies[$i]['name']) .'</option>';
									}
									
								$html .= '</select>
							</div>
							<div class="wo_perms_users" id="wo_perms_users">
								<select id="control_3" name="control_3[]" multiple="multiple" size="5">
								</select>
							</div>
							<button class="add_perms" name="save" id="form_sec_11_save" onClick="changeSectionStatusMan(\'sec_11\'); setCompleteness();">&nbsp;</button>	
							
							<!--<div id="wo_add_perms" class="wo_perms_enable">
								<a href="#"></a>
							</div>-->
							<div style="clear: both;"></div>
					</li>';
					
				$html .='</ul>';
			} else {
				$html = '';
			}
			return $html;
		}
		public function projectPermissions($projectId = NULL) {
			if($projectId != NULL) {
				$list = Array();
				$projectPermission = self::getQuery("SELECT b.`rp_permission`,b.`wo_permission` FROM `projects` b WHERE b.`id`='$projectId' LIMIT 1");
				if($projectPermission[0]['rp_permission'] == 1){
					$rpchecked = " CHECKED";
				}else{
					$rpchecked = "";
				}
				if($projectPermission[0]['wo_permission'] == 1){
					$wochecked = " CHECKED";
				}else{
					$wochecked = "";
				}
				
				$html = '<ul class="wo_perms" id="project_perms>';
				
				$html .= '<li class="static_perm">
							<div class="project_perms">
								Display project in Resource Planner
							</div>
							<div class="wo_perms_users">
							<input type="hidden" name="currentRPPermission" id="currentRPPermission" value="' .$projectPermission[0]['rp_permission'] .'" />
							<input type="checkbox" name="projectRPPermission" id="projectRPPermission" ' .$rpchecked .'/>
							</div>
							<div style="clear: both;"></div>
					</li>';
					
					$html .= '<li class="static_perm">
							<div class="project_perms">
								Display project in Work Orders
							</div>
							<div class="wo_perms_users">
							<input type="hidden" name="currentWOPermission" id="currentWOPermission" value="' .$projectPermission[0]['wo_permission'] .'" />
							<input type="checkbox" name="projectWOPermission" id="projectWOPermission" ' .$wochecked .'/>
							</div>
							<div style="clear: both;"></div>
					</li>';
					
				$html .='</ul>';
			} else {
				$html = '';
			}
			return $html;
		}
		function getProjectRisks($projectID, $userID){
			$html = '';
			$risks = self::getQuery("SELECT * FROM project_risks WHERE project_id='" . $projectID . "' AND archived='0'");
			$numberOfRisks = sizeof($risks);
			if($numberOfRisks > 0){
				$html .= '<script>$(document).ready(function(){ $(".risk_ps").css({display: "block"})});</script>';
			}
			$html .= '<ul>';
			for($i = 0; $i < $numberOfRisks; $i++) {
				$userName = 'None';
				$ticked = "";
				$closedDate = '';
				$classComplete = '';
				$image = '/_images/flag_icon_red.png';
				$usrSql = "SELECT * FROM users WHERE id='" . $risks[$i]['assigned_to_user_id'] . "' AND active='1' LIMIT 1";
				$user = self::getQuery($usrSql);
				if(sizeof($user) == 1) {
					$userName = $user[0]['first_name'] . ' ' . $user[0]['last_name'];
				}
				$commentsSql = "SELECT * FROM risk_comments WHERE risk_id='" . $risks[$i]['id'] . "' AND archived='0'";
				$comments = self::getQuery($commentsSql);
				$totalComments = sizeof($comments);
				if($risks[$i]['active'] == '0'){
					$ticked = "checked";
					$classComplete = ' class="complete"';
					$image = '/_images/flag_icon_green.png';
					$date_time_split = explode(" ", $risks[$i]['closed_date']);
					$date_split = explode("-", $date_time_split[0]);
					
					$closedDate = number_format($date_split[1]) ."/" .number_format($date_split[2]) ."/" .$date_split[0];
				}

				if (strlen(trim($risks[$i]['title'])) > 70)
				{
					$title =  substr(trim($risks[$i]['title']), 0, 70)."...";
				}
				else
				{
					$title = trim($risks[$i]['title']);
				}
				$html .= '<li ' . $classComplete . ' id="risk_' . $risks[$i]['id'] . '">
					<div class="riskLeftCol">
						<p class="risk_name" title="' . $risks[$i]['title'] . '"><img src="' . $image . '"/>' . $title . '</p>
						<p class="risk_assigned">Assigned to: <span class="assigned">' . $userName . '</span> </p>
						<div>
							<p class="risk_done">
								<input type="checkbox" id="risk_complete_' . $risks[$i]['id'] . '" name="risk_complete" class="checkbox" onclick="changeRiskStatus(\'active\',\'' . $risks[$i]['id'] . '\');" ' .  $ticked. '/> Done <span class="closed_date_' . $risks[$i]['id'] . '">' . $closedDate . '</span>
							</p>
							<p class="risk_delete">
								<button class="status status_delete" onclick="changeRiskStatus(\'archive\',\'' . $risks[$i]['id'] . '\'); return false; "><span>Delete</span></button>
							</p>
						</div>
					</div>
					<div class="riskRightCol">
						<textarea class="risk_comment" name="risk_comment">' . $risks[$i]['description'] . '</textarea>
						<div class="risk_comments_block" id="risk_comment_' . $risks[$i]['id'] . '">
							<p class="risk_comment_count" onClick="riskComment(\'' . $risks[$i]['id'] . '\', this); return false;">Comments (<span class="count_' . $risks[$i]['id'] . '">' . $totalComments . '</span>)</p>
							<p class="risk_add_comment" onClick="addComment(\'' . $risks[$i]['id'] . '\', \'\'); return false;">Add Comments</p>
						</div>
					</div>
					<div class="risk_comment_container" id="add_comment_block_' . $risks[$i]['id'] . '" style="display:none;">
						<div class="label">New Comment:</div>
						<div class="content"><textarea class="add_comment" id="add_comment_content_' . $risks[$i]['id'] . '" name="add_comment_content_' . $risks[$i]['id'] . '"></textarea></div>
						<div class="submit">
							<button onclick="$(\'#add_comment_block_' . $risks[$i]['id'] . '\').hide(\'slide\',{direction:\'up\'},500); return false;"><span>Cancel</span></button>
							<button onclick="submitComment(\'' . $risks[$i]['id'] . '\', \'' . $userID . '\', \'add_comment_content_\'); return false;"><span>Submit Comment</span></button>
						</div>
					</div>
					<div class="uploaded_risk_comment_container" id="read_comment_block_' . $risks[$i]['id'] . '" style="display:none;">
						<div class="risk_comments">
						</div>
						<div class="submit">
							<button onclick="$(\'#read_comment_block_' . $risks[$i]['id'] . '\').hide(\'slide\', { direction: \'up\' }, 500); return false;"><span>Cancel</span></button>
						</div>
					</div>
				</li>';
			}
			$html .= '</ul>';
			return $html;
		}
		function getRiskPermsList($projectId){
			$sql = "SELECT u.id id, u.first_name first_name, u.last_name last_name FROM users u, user_project_permissions upp WHERE u.id=upp.user_id AND u.active='1' AND u.deleted='0' AND upp.active='1' AND upp.deleted='0' AND upp.project_id='$projectId' ORDER BY last_name, first_name";
			$users = self::getQuery($sql);
//			$html = $sql;
			$html = "<select class='user_list' id='user_list'>";
			$html .="<option value='-1'>select</option>";
			for($i = 0; $i < sizeof($users); $i++) {
				$html .= "<option value='" . $users[$i]['id'] . " '>" . $users[$i]['last_name'] . ", " . $users[$i]['first_name'] . "</option>";
			}
			$html .= "</select>";

			return $html;
		}
		function getProjectStatus($projectId, $status='all'){
			$html = '';
			if($status == 'all'){
				$project_status_sql = 'SELECT * FROM `lnk_project_status_types` ORDER BY `sort_order`';
				$projectStatusResult = self::getQuery($project_status_sql);
				foreach($projectStatusResult as $status){
					$class_name = str_replace(" ", "", strtolower($status['name']));
					$html .= '<li><button class="status status_' . $class_name . '" onclick="changeProjectStatus(\'' . $status['id'] . '\', \'' . $projectId . '\', \'' . $_SESSION['user_id'] . '\'); return false;"><span>' . $status['name'] . '</span></button></li>';
				}
			}else{
				$project_status_sql = 'SELECT * FROM `lnk_project_status_types` where `id`=(select IFNULL(`project_status`, 0) from `projects` where id="' . $projectId . '")';
				$projectStatusResult = self::getQuery($project_status_sql);
				foreach($projectStatusResult as $status){
					$class_name = str_replace(" ", "", strtolower($status['name']));
					$html .= '<button class="status status_' . $class_name . '" onclick="return false;"><span>' . $status['name'] . '</span></button>';
				}
				if($html == ''){
					$html = '&nbsp;';
				}
			}
			return $html;
		}
		function getProjectStatusEditHtml(){
			$html = '';
			$project_status_sql = 'SELECT * FROM `lnk_project_status_types` ORDER BY `sort_order`';
			$projectStatusResult = self::getQuery($project_status_sql);
			foreach($projectStatusResult as $status){
				$html .= '<option value="' . $status['id'] . '">' . $status['name'] . '</option>';
			}
			return $html;
		}
		function getProjectGroupsEditHtml(){
			$html = '';
			$project_groups_sql = 'SELECT * FROM `lnk_internal_groups` ORDER BY `sort_order`';
			$projectGroupsResult = self::getQuery($project_groups_sql);
			foreach($projectGroupsResult as $groups){
				$html .= '<option value="' . $groups['id'] . '">' . $groups['name'] . '</option>';
			}
			return $html;
		}

		function getAllocationType($project_id){
			$html = '';
			$internal_group = '';
			$selected = "";
			if(!empty($project_id))
			{
				$project_internal_grp = "SELECT `internal_groups` FROM `projects` where `id`='".$project_id."'";
				$project_internal_grp_Result = self::getQuery($project_internal_grp);
	 			$internal_group = $project_internal_grp_Result[0]['internal_groups'];
			}

			if(empty($internal_group))
			{
				$internal_group = DEFAULT_INTERNAL_GROUP; // default selection
			}

			$project_groups_sql = 'SELECT * FROM `lnk_internal_groups` ORDER BY `sort_order`';
			$projectGroupsResult = self::getQuery($project_groups_sql);

			foreach($projectGroupsResult as $groups){

				if($groups['id'] == $internal_group )
				{
						$selected = " SELECTED";
				} else {
						$selected = "";
				}

				$html .= '<option value="' . $groups['id'] . '" '.$selected.'>' . $groups['name'] . '</option>';
			}
			return $html;
		}


		 function getPrograms($project_id){
			$html = '';
			$program= '';
			$selected = "";
			if(!empty($project_id))
			{
				$project_program = "SELECT `program` FROM `projects` where `id`='".$project_id."'";
				$project_program_Result = self::getQuery($project_program);
				$program = $project_program_Result[0]['program'];
			}

			$project_program_sql = "SELECT * FROM `lnk_programs` where active='1' and deleted='0' ORDER BY `program`";
			$projectProgramResult = self::getQuery($project_program_sql);
			$flag = 0;
			$select_html = '';
			foreach($projectProgramResult as $prog){

				if($prog['id'] == $program )
				{
						$flag = 1;
						$selected = " SELECTED";
				} else {
						$selected = "";
				}

				$html .= '<option value="' . $prog['id'] . '" '.$selected.'>' . $prog['program'] . '</option>';
			}
			if($flag == 0){
				$select_html = '<option value = "0" selected>--Select Program--</option>';			
			}
			return $select_html.$html;
		}



		function fullProgramListHTML($savedProgram=''){
			$html = '';
			$programList = self::getQuery("SELECT id,program from lnk_programs where active='1' and deleted='0' order by program");
			foreach($programList as $key=>$value){
				if($savedProgram == $value['id']){
					$html.= '<option value="'.$value['id'].'" selected>'.$value['program'].'</option>';
				} else {
					$html.= '<option value="'.$value['id'].'">'.$value['program'].'</option>';
				}
			}
			return $html;
		}
	}
?>
