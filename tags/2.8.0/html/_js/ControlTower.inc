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
		
		public function getQuery($query) {
			ControlTower::setIniFile();
			
			$config = ControlTower::getIniFile();
			
			$db = Zend_Db::factory('Pdo_Mysql', array(
					'host'     => $config->database->params->host,
				    'username' => $config->database->params->username,
				    'password' => $config->database->params->password,
				    'dbname'   => $config->database->params->dbname
				));
			$db->getConnection();
			
			$data = $db->fetchAll($query);
			
			return $data;
		}
		
		public function totalActiveProjects() {
			$projects = self::getQuery("SELECT count(`id`) as total FROM `projects` WHERE `archived`='0' AND `active`='1' AND `deleted`='0'");
			
			return $projects[0]['total'];
		}
		
		public function financeTotal() {
			$fin = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `active`='1' AND `deleted`='0'");
			$finance_total = 0;
			
			for($i = 0; $i < sizeof($fin); $i++) {
				$proj = self::getQuery("SELECT * FROM `projects` WHERE `id`='" .$fin[$i]['project_id'] ."' AND `archived`='0' AND `active`='1' AND `deleted`='0' LIMIT 1");
				
				if(sizeof($proj) == 1) {
					$finance_total += ($fin[$i]['rate'] * $fin[$i]['hours']);
				}
			}
			return $finance_total;
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
		
		public function getSectionEditHTML($project_id) {
			$html = '';
			//$sec = self::getSections();
			$sec = self::getQuery(QRY_PROJECT_SECTIONS_ASC);
			
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
				$html .= "<li class=\"$class\" id=\"sec_" .$sec[$i]['id'] ."\" onClick=\"ctCreateSectionsSwitch('sec_" .$sec[$i]['id'] ."');\"><img src=\"$flag_img\">" .$sec[$i]['name'] ."</li>";
			}
			$html .= "</ul>";
			
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
			$users = self::getQuery(QRY_USERS_ASC);
			
			
			
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
			$users = self::getQuery(QRY_USERS_ASC);
			
			/*$user_list = '<option value="">--Select User--</option>';
			for($i = 0; $i < sizeof($users); $i++) {
				$user_list .= '<option value="' .$users[$i]['id'] .'">' 
					.$users[$i]['first_name'] .' ' .$users[$i]['last_name'] 
					.'</option>\n';
			}*/
			$html .= '<li style="display: none;"><form>
					
					<div style="clear: both"></div>

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
				
				
				if($rols[$i]['editable'] == 1) {
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
								$html .= '</select>
							</div>
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
		public function getResourceHTML($project_id) {
			$html = "";
			$users = self::getQuery("SELECT DISTINCT a.`id`, a.* FROM `users` a, `resource_blocks` b WHERE a.`id` = b.`userid` AND b.`projectid`='" .$project_id ."'");
			
			for($i = 0; $i < sizeof($users); $i++) {
				$total_actual = 0;
				$total_booked = 0;
				$resources = self::getQuery("SELECT * FROM `resource_blocks` WHERE `projectid`='" .$project_id ."' AND `userid`='" .$users[$i]['id'] ."'");
				
				for($r = 0; $r < sizeof($resources); $r++) {
					if($resources[$r]['status'] == 4) {
						$total_actual += 2;
					}
					if($resources[$r]['status'] == 3) {
						$total_booked += 2;
					}
				}
				
				$html .= '<div class="resource_row" onClick="window.location = \'/resourceplanner/?userid=' .$users[$i]['id'] .'\';">
					<div class="resource_name"><span>UXD Resource:</span>' .ucfirst($users[$i]['first_name']) .' ' .ucfirst($users[$i]['last_name']) .'</div>
					<div class="resource_hours"><span>Actual Hours:</span>' .$total_actual .' hours</div>
					<div class="resource_hours booked_hours"><span>Scheduled Hours:</span>' .$total_booked .' hours</div>
				</div>';
			}
			
			return $html;
		}
		public function getFinanceHTML() {
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
				$html .= '<li' .$nameItLi .'>
					<div class="finance_budget"' .$nameIt .'>
					<form action="" method="post" name="fin_' .$fin[$i]['id'] .'">
						<input type="hidden" name="phase" id="phase" value="' .$fin[$i]['id'] .'" />
						<div class="finance_budget_name"><span>' .$fin[$i]['name'] .':</span></div>
						<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="hours" id="hours" onChange="calcFinance(fin_' .$fin[$i]['id'] .')" /></div>
						<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="rate" id="rate" onChange="calcFinance(fin_' .$fin[$i]['id'] .')" /></div>
						<div class="finance_budget_total"><span>Total:</span><input type="text" name="total" id="total" class="readonly" readonly /></div>
						<div class="dim" style="display: block"></div>
						<div style="clear: both"></div>
					</form>
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
						<form action="" method="post" name="fin_' .$fin[$i]['id'] .'">
							<input type="hidden" name="phase" id="phase" value="' .$fin[$i]['id'] .'" />
							<div class="finance_budget_name"><span>' .$fin[$i]['name'] .':</span></div>
							<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="hours" id="hours" value="' .$hours .'" onChange="calcFinance(fin_' .$fin[$i]['id'] .')" /></div>
							<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="rate" id="rate" value="' .$rate .'" onChange="calcFinance(fin_' .$fin[$i]['id'] .')" /></div>
							<div class="finance_budget_total"><span>Total:</span><input type="text" name="total" id="total" value="' .$total .'" class="readonly" readonly /></div>
							<div class="dim" style="display: block"></div>
							<div style="clear: both"></div>
						</form>
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
					<form action="" method="post" name="fin_' .$fin[$i]['id'] .'">
						<input type="hidden" name="phase" id="phase" value="' .$fin[$i]['id'] .'" />
						<div class="finance_budget_name"><span>' .$fin[$i]['name'] .':</span></div>
						<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="hours" id="hours" onChange="calcFinance(fin_' .$fin[$i]['id'] .')" /></div>
						<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="rate" id="rate" onChange="calcFinance(fin_' .$fin[$i]['id'] .')" /></div>
						<div class="finance_budget_total"><span>Total:</span><input type="text" name="total" id="total" class="readonly" readonly /></div>
						<div class="dim" style="display: block"></div>
						<div style="clear: both"></div>
					</form>
				</div>
				</li>';
				}
			}
			$html .= '</ul>';
			
			return $html;
		}
		public function getFinanceTotalEdit($project_id) {
			$finance_data = self::getQuery("SELECT * FROM `project_phase_finance` WHERE `project_id`='$project_id'");
			$overall_total = 0.00;
			for($i = 0; $i < sizeof($finance_data); $i++) {
				$overall_total += ($finance_data[$i]['hours'] * $finance_data[$i]['rate']);
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
				$client = self::getQuery("SELECT a.`id`,a.`name` FROM `companies` a, `projects` b WHERE b.`id`='$projectId' AND a.`id`=b.`company` LIMIT 1");
				$companies = self::getQuery("SELECT * FROM `companies` ORDER BY `name` ASC");
				
				$html = '<ul class="wo_perms" id="perms_list">';
				
				$html .= '<li class="static_perm">
							<div class="wo_perms_company">
								' .$client[0]['name'] .'
							</div>
							<div class="wo_perms_users">
								All Users
							</div>
							<div style="clear: both;"></div>
					</li>
				</ul>
				<ul class="wo_perms">';
				
				$html .= '<li class="woPermsAdd">
						<form action="" method="post" name="" onsubmit="return false;">
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
								<div id="wo_add_perms" class="wo_perms_enable">
									<a href="#"></a>
								</div>
								<div style="clear: both;"></div>
						</form>
					</li>';
					
				$html .='</ul>';
			} else {
				$html = '';
			}
			return $html;
		}
	}
?>