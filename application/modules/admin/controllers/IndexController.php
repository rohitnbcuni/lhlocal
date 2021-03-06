<?PHP
include('Admin.inc');
require_once('../html/_inc/config.inc');
class Admin_IndexController extends LighthouseController { 
	public function indexAction() {
			
	echo '<!--=========== START: COLUMNS ===========-->		
		<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="User Info">  					
			<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';				
					echo '<div class="adminSelect">
								   <div class="row" style="float:right;">
										<button id="adminReportbtn" onclick="generateUsers();"><span>View All Users</span></button>
									</div>
									<div >
								   
									<p>Seach Users:</p>
									<select class="field_medium" name="admin_user_select" id="admin_user_select" onChange="fetchUser(this.value);" >';
									echo AdminDisplay::getUserOptionHTML();
									echo '</select> (*Choose DPS Users from the list, others search by name)
								</div>
								
								   <div class="row">
										<p>First Name:</p>
										<input type="text" name="firstName" id="firstName" >
									</div>
									 <div class="row" >
										<p>Last Name:</p>
										<input type="text" name="lastName" id="lastName" >
									</div>
									 <div class="row2" >
										 <p>
										<button style="_margin-left:-280px;*margin-left:-280px" onclick="fetchUser();"><span>Search</span></button>
										</p>
									</div>
					</div>';
			echo '</div>					
			<div style="clear: both;"></div>';



		}
		public function createAction() {  
			

		}

		public function  projectdefaultccAction()
		{
			$proj_id = (int)$this->_request->getParam('proj_id');
			$ccList = strip_tags($this->_request->getParam('ccList'));
			if(!empty($proj_id))
			{
				$fetchProjectCCList = AdminDisplay::fetchProjectCCList($proj_id);
			}
			
			echo '<!--=========== START: COLUMNS ===========-->		
			<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="WO Default CC List">  
			<input type="hidden" name="ccList" id="ccList" value="'.$this->view->escape($ccList).'">  
			<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';				
				echo '<div class="adminSelect">
						   <div >
								<p>Project:</p>
								<select class="field_medium" name="admin_project_select" id="admin_project_select" onChange="projectDefaultCC(this.value);" >';
								echo AdminDisplay::getProjectOptionEditHTML($proj_id);
								echo '</select>
							</div>
					</div>';
			echo '<br><hr style="clear:both;margin-top:30px;">';
					if(!empty($proj_id)) {
						$this->buildProjectCCListHTML($proj_id,$fetchProjectCCList[0]['cclist']);

					} 
					else
					{
						echo '<input type="hidden" name="cclist" id="cclist" value="" />';
					}
		
			echo '</div>';

				if(!empty($proj_id)) {					
					$company = $fetchProjectCCList[0]['company'];
					if(!empty($fetchProjectCCList[0]['cclist']))
					{
						echo '<div class="row2" style="padding-left:96px;clear:both;">
						 <div><button onclick="addDefaultCompany('.$proj_id.');" id="addButton"><span>Apply to all Projects</span></button></div> for the Company <b>'.AdminDisplay::getUserCompany($company).'</b>
						</div>';
					}
				} 

				echo '<div style="clear: both;"></div>
					<div class="message_required">
					<p>
					</p>					
					<div style="clear: both;"></div>
					
					<div class="duplicate_buttons">
						<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
						<div style="clear: both;"></div>
					</div>
				</div>
				
				';

		}

		public function buildProjectCCListHTML($proj_id, $ccList){
                			echo '	<div class="admin_content_col4">
						<div class="side_bucket_container bucket_container_last">

							<div class="side_bucket_title">CC List</div>
							<div class="side_bucket_content">';
								if(isset($proj_id)) {
									echo AdminDisplay::getProjectCCList($proj_id);
								} else {
									echo '<input type="hidden" name="cclist" id="cclist" value="" />';
								}
								echo '<ul id="cc_list">';
								if(isset($proj_id)) {
										$cclist = explode(",", $ccList);

										for($lstx = 0; $lstx < sizeof($cclist); $lstx++) {
											if(!empty($cclist[$lstx])) {
												$cc_user_data = AdminDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$cclist[$lstx] ."'");
												
												echo '<li class="admincc_listli"><div class="admincclist_name">'
														.ucfirst($cc_user_data[0]['first_name']) .' ' .$cc_user_data[0]['last_name']
													.'</div>
													<button class="status admincclist_remover" onclick="removeCcUser('
														.$cclist[$lstx]
													.','.$proj_id.'); return false;"><span>remove</span></button>
												</li>';
											}
										}
									}
								echo '</ul>
								<div class="cclist_actions" id="add_cc">
									<button class="secondary" onclick="$(\'#add_cc\').css({display:\'none\'});$(\'#select_cc\').css({display:\'block\'}); return false;"><span>+ Add Person to CC List</span></button>
								</div>
								<div class="cclist_actions" id="select_cc" style="display: none;">
									<select name="cc_user" id="cc_user" style="margin-bottom:2px;">';
										if(isset($proj_id)) {
											echo AdminDisplay::getUserOptionEditHTML();
										}
									echo '</select>
									<p><button class="secondary" onclick="addCcUser('.$proj_id.'); $(\'#select_cc\').css({display:\'none\'});$(\'#add_cc\').css({display:\'block\'}); return false;"><span>Add</span></button>
									<button class="cancel" onclick="$(\'#add_cc\').css({display:\'block\'}); $(\'#select_cc\').css({display:\'none\'}); return false;"><span>Cancel</span></button>
									</p>  
								</div>
								<div class="clearer"></div>
						</div>						
				</div>';
				


		}
////////////////18474///////////////////
		public function  qcprojectdefaultccAction()
				{
					$proj_id = (int)$this->_request->getParam('proj_id');
					$ccList = strip_tags($this->_request->getParam('ccList'));
					
					if(!empty($proj_id))
					{
						$fetchProjectCCList = AdminDisplay::fetchQCProjectCCList($proj_id);
					}
					
					echo '<!--=========== START: COLUMNS ===========-->		
					<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="Quality Default CC List">  
					<input type="hidden" name="qcccList" id="qcccList" value="'.$this->view->escape($ccList).'">  
					<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';
						echo '<div class="adminSelect">
								   <div >
										<p>Project:</p>
										<select class="field_medium" name="admin_project_select" id="admin_project_select" onChange="QCprojectDefaultCC(this.value);" >';
										echo AdminDisplay::getProjectOptionEditHTML($proj_id);
										echo '</select>
									</div>
							</div>';
					echo '<br><hr style="clear:both;margin-top:30px;">';
							if(!empty($proj_id)) {
								$this->buildQCProjectCCListHTML($proj_id,$fetchProjectCCList[0]['qccclist']);
		
							} 
							else
							{
								echo '<input type="hidden" name="qccclist" id="qccclist" value="" />';
							}
				
					echo '</div>';
		
						if(!empty($proj_id)) {					
							$company = $fetchProjectCCList[0]['company'];
							if(!empty($fetchProjectCCList[0]['qccclist']))
							{
								echo '<div class="row2" style="padding-left:96px;clear:both;">
								 <div><button onclick="addQCDefaultCompany('.$proj_id.');" id="addButton"><span>Apply to all Projects</span></button></div> for the Company <b>'.AdminDisplay::getUserCompany($company).'</b>
								</div>';
							}
						} 
		
						echo '<div style="clear: both;"></div>
							<div class="message_required">
							<p>
							</p>					
							<div style="clear: both;"></div>
							
							<div class="duplicate_buttons">
								<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
								<div style="clear: both;"></div>
							</div>
						</div>
						
						';
		
				}
		public function buildQCProjectCCListHTML($proj_id, $ccList){
		                			echo '	<div class="admin_content_col4">
								<div class="side_bucket_container bucket_container_last">
		
									<div class="side_bucket_title">CC List</div>
									<div class="side_bucket_content">';
										if(isset($proj_id)) {
											echo AdminDisplay::getQCProjectCCList($proj_id);
										} else {
											echo '<input type="hidden" name="qccclist" id="qccclist" value="" />';
										}
										echo '<ul id="qccc_list">';
										if(isset($proj_id)) {
												$cclist = explode(",", $ccList);
		
												for($lstx = 0; $lstx < sizeof($cclist); $lstx++) {
													if(!empty($cclist[$lstx])) {
														$cc_user_data = AdminDisplay::getQuery("SELECT * FROM `users` WHERE `id`='" .$cclist[$lstx] ."'");
														
														echo '<li class="admincc_listli"><div class="admincclist_name">'
																.ucfirst($cc_user_data[0]['first_name']) .' ' .$cc_user_data[0]['last_name']
															.'</div>
															<button class="status admincclist_remover" onclick="removeqcCcUser('
																.$cclist[$lstx]
															.','.$proj_id.'); return false;"><span>remove</span></button>
														</li>';
													}
												}
											}
										echo '</ul>
										<div class="cclist_actions" id="add_cc">
											<button class="secondary" onclick="$(\'#add_cc\').css({display:\'none\'});$(\'#select_cc\').css({display:\'block\'}); return false;"><span>+ Add Person to CC List</span></button>
										</div>
										<div class="cclist_actions" id="select_cc" style="display: none;">
											<select name="qccc_user" id="qccc_user" style="margin-bottom:2px;width:343px;">';
												if(isset($proj_id)) {
													echo AdminDisplay::getUserOptionEditHTML();
												}
											echo '</select>
											<p><button class="secondary" onclick="addCcUserQC('.$proj_id.'); $(\'#select_cc\').css({display:\'none\'});$(\'#add_cc\').css({display:\'block\'}); return false;"><span>Add</span></button>
											<button class="cancel" onclick="$(\'#add_cc\').css({display:\'block\'}); $(\'#select_cc\').css({display:\'none\'}); return false;"><span>Cancel</span></button>
											</p>  
										</div>
										<div class="clearer"></div>
								</div>						
						</div>';
						
		
		
				}

//////////////////////////////////
		public function projectversionsAction(){

			$proj_id = (int)$this->_request->getParam('proj_id');
			$version_id = (int)$this->_request->getParam('version_id');
			if(!empty($proj_id))
			{
				$proj_version_list = AdminDisplay::fetchProjectVersions($proj_id,$version_id);
			}
			
			echo '<!--=========== START: COLUMNS ===========-->
			<div class="message_required" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
			<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="Project Versions">  
			<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';				
				echo '<div class="adminSelect">
						   <div >
								<p>Project:</p>
								<select class="field_medium" name="admin_project_select" id="admin_project_select" onChange="fetchProjVersion(this.value);" >';
								echo AdminDisplay::getProjectOptionEditHTML($proj_id);
								echo '</select>
							</div>';
							if(!empty($proj_id)){
								$addBTN = "display:block;";
							}
							else{
								$addBTN = "display:none;";
							}
						echo '<div class="row2" >
								 <div>							
							<button onclick="addNewVersion();" id="addButton" style="'.$addBTN.'"><span>Add New</span></button>
					</div>  
					</div>
			</div>';
			echo '<br><hr style="clear:both;margin-top:30px;">';
				if(sizeof($proj_version_list) =='1')
				{
					$this->buildProjectVersionHTML($proj_version_list[0]);
				}
				else if(sizeof($proj_version_list) >'1')
				{
					$this->listProjectVersionHTML($proj_version_list);
					echo '<br><hr style="clear:both;margin-top:30px;">';
				}

				if(!empty($proj_id) && sizeof($proj_version_list) !='1' )
				{
					$proj_version_list['project_id'] = $proj_id;
					$proj_version_list['active'] = '1';
					$proj_version_list['deleted'] = '0';

					$this->buildProjectVersionHTML($proj_version_list);
				}
				
			echo '</div>					
				<div style="clear: both;"></div>
				<div class="message_required">
					<p>
					</p>					
					<div style="clear: both;"></div>
					
					<div class="duplicate_buttons">
						<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
						<div style="clear: both;"></div>
					</div>
				</div>
				
				';

		}
		//LH#28522
		public function projectproductAction(){

			$proj_id = (int)$this->_request->getParam('proj_id');
			$version_id = (int)$this->_request->getParam('version_id');
			if(!empty($proj_id))
			{
				$proj_version_list = AdminDisplay::fetchProjectProduct($proj_id,$version_id);
			}
		
			echo '<!--=========== START: COLUMNS ===========-->
			<div class="message_required" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
			<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="Project Products">  
			<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';				
				echo '<div class="adminSelect">
						   <div >
								<p>Project:</p>
								<select class="field_medium" name="admin_project_select" id="admin_project_select" onChange="fetchProjProduct(this.value);" >';
								echo AdminDisplay::getProjectOptionEditHTML($proj_id);
								echo '</select>
							</div>';
							if(!empty($proj_id)){
								$addBTN = "display:block;";
							}
							else{
								$addBTN = "display:none;";
							}
						echo '<div class="row2" >
								 <div>							
							<button onclick="addNewProduct();" id="addButton" style="'.$addBTN.'"><span>Add New</span></button>
					</div>  
					</div>
			</div>';
			echo '<br><hr style="clear:both;margin-top:30px;">';
				if(sizeof($proj_version_list) =='1')
				{
					$this->buildProjectProductHTML($proj_version_list[0]);
				}
				else if(sizeof($proj_version_list) >'1')
				{
					$this->listProjectProductHTML($proj_version_list);
					echo '<br><hr style="clear:both;margin-top:30px;">';
				}

				if(!empty($proj_id) && sizeof($proj_version_list) !='1' )
				{
					$proj_version_list['project_id'] = $proj_id;
					$proj_version_list['active'] = '1';
					$proj_version_list['deleted'] = '0';

					$this->buildProjectProductHTML($proj_version_list);
				}
				
			echo '</div>					
				<div style="clear: both;"></div>
				<div class="message_required">
					<p>
					</p>					
					<div style="clear: both;"></div>
					
					<div class="duplicate_buttons">
						<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
						<div style="clear: both;"></div>
					</div>
				</div>
				
				';

		}
		public function projectiterationAction(){
			$proj_id = (int)$this->_request->getParam('proj_id');
			$version_id = (int)$this->_request->getParam('version_id');
			if(!empty($proj_id))
			{
				$proj_version_list = AdminDisplay::fetchProjectIteration($proj_id,$version_id);
			}
		
			echo '<!--=========== START: COLUMNS ===========-->
			<div class="message_required" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
			<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="Project Iterations">  
			<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';				
				echo '<div class="adminSelect">
						   <div >
								<p>Project:</p>
								<select class="field_medium" name="admin_project_select" id="admin_project_select" onChange="fetchProjIteration(this.value);" >';
								echo AdminDisplay::getProjectOptionEditHTML($proj_id);
								echo '</select>
							</div>';
							if(!empty($proj_id)){
								$addBTN = "display:block;";
							}
							else{
								$addBTN = "display:none;";
							}
						echo '<div class="row2" >
								 <div>							
							<button onclick="addNewIteration();" id="addButton" style="'.$addBTN.'"><span>Add New</span></button>
					</div>  
					</div>
			</div>';
			echo '<br><hr style="clear:both;margin-top:30px;">';
				if(sizeof($proj_version_list) =='1')
				{
					$this->buildProjectIterationHTML($proj_version_list[0]);
				}
				else if(sizeof($proj_version_list) >'1')
				{
					$this->listProjectIterationHTML($proj_version_list);
					echo '<br><hr style="clear:both;margin-top:30px;">';
				}

				if(!empty($proj_id) && sizeof($proj_version_list) !='1' )
				{
					$proj_version_list['project_id'] = $proj_id;
					$proj_version_list['active'] = '1';
					$proj_version_list['deleted'] = '0';

					$this->buildProjectIterationHTML($proj_version_list);
				}
				
			echo '</div>					
				<div style="clear: both;"></div>
				<div class="message_required">
					<p>
					</p>					
					<div style="clear: both;"></div>
					
					<div class="duplicate_buttons">
						<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
						<div style="clear: both;"></div>
					</div>
				</div>
				
				';
		}
		//end
		public function buildProjectVersionHTML($proj_version_list){

				echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label"><label>Project Name:</label></div>
									<input type="text" class="readonly" readonly name="Project_name" id="Project_name" value="'.AdminDisplay::getProjectName($proj_version_list['project_id']).'" >
						</div>
						<div class="row">
							<div class="label"><label>Version:</label></div>
								<input type="text" disabled="disabled" name="versionName" id="versionName" value="'.$proj_version_list['version_name'].'" >
								<input type="hidden" id="versionID" value="'.$proj_version_list['id'].'" >
						</div>';
						
						if($proj_version_list['active']=='1')
						{
							$activeCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Active:</label></div>
								<input type="checkBox" style="width:10px;" name="versionActiveStatus" id="versionActiveStatus" value="" '.$activeCheck.'>
						</div>';

						if($proj_version_list['deleted']=='1')
						{
							$deletedCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Deleted:</label></div>
								<input type="checkBox" style="width:10px;" name="versionDeletedStatus" id="versionDeletedStatus" value="" '.$deletedCheck.'>
							</div>';
						if(empty($proj_version_list['id']))
						{
							$editstatus = "display:none;";
							$submitStatus = "display:none;";
						}
						else
						{
							$editstatus = "display:block;";
							$submitStatus = "display:none;";
						}
						 echo'<div class="row" id="updateBTN" style="'.$editstatus.'" >
							<button onclick="updateProjectVersion(\'UPDATE\');"><span >Update</span></button>
						</div>
						<div class="row" id="submitBTN" style="'.$submitStatus.'">
							<button onclick="updateProjectVersion(\'ADD\');"><span >Submit</span></button>
						</div>
				</div>';
		}
		
		public function buildProjectProductHTML($proj_version_list){

				echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label"><label>Project Name:</label></div>
									<input type="text" class="readonly" readonly name="Project_name" id="Project_name" value="'.AdminDisplay::getProjectName($proj_version_list['project_id']).'" >
						</div>
						<div class="row">
							<div class="label"><label>Products:</label></div>
								<input type="text" disabled="disabled" name="versionName" id="versionName" value="'.$proj_version_list['product_name'].'" >
								<input type="hidden" id="versionID" value="'.$proj_version_list['id'].'" >
						</div>';
						
						if($proj_version_list['active']=='1')
						{
							$activeCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Active:</label></div>
								<input type="checkBox" style="width:10px;" name="versionActiveStatus" id="versionActiveStatus" value="" '.$activeCheck.'>
						</div>';

						if($proj_version_list['deleted']=='1')
						{
							$deletedCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Deleted:</label></div>
								<input type="checkBox" style="width:10px;" name="versionDeletedStatus" id="versionDeletedStatus" value="" '.$deletedCheck.'>
							</div>';
						if(empty($proj_version_list['id']))
						{
							$editstatus = "display:none;";
							$submitStatus = "display:none;";
						}
						else
						{
							$editstatus = "display:block;";
							$submitStatus = "display:none;";
						}
						 echo'<div class="row" id="updateBTN" style="'.$editstatus.'" >
							<button onclick="updateProjectProduct(\'UPDATE\');"><span >Update</span></button>
						</div>
						<div class="row" id="submitBTN" style="'.$submitStatus.'">
							<button onclick="updateProjectProduct(\'ADD\');"><span >Submit</span></button>
						</div>
				</div>';
		}
		public function buildProjectIterationHTML($proj_version_list){

				echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label"><label>Project Name:</label></div>
									<input type="text" class="readonly" readonly name="Project_name" id="Project_name" value="'.AdminDisplay::getProjectName($proj_version_list['project_id']).'" >
						</div>
						<div class="row">
							<div class="label"><label>Iterations:</label></div>
								<input type="text" disabled="disabled" name="versionName" id="versionName" value="'.$proj_version_list['iteration_name'].'" >
								<input type="hidden" id="versionID" value="'.$proj_version_list['id'].'" >
						</div>';
						
						if($proj_version_list['active']=='1')
						{
							$activeCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Active:</label></div>
								<input type="checkBox" style="width:10px;" name="versionActiveStatus" id="versionActiveStatus" value="" '.$activeCheck.'>
						</div>';

						if($proj_version_list['deleted']=='1')
						{
							$deletedCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Deleted:</label></div>
								<input type="checkBox" style="width:10px;" name="versionDeletedStatus" id="versionDeletedStatus" value="" '.$deletedCheck.'>
							</div>';
						if(empty($proj_version_list['id']))
						{
							$editstatus = "display:none;";
							$submitStatus = "display:none;";
						}
						else
						{
							$editstatus = "display:block;";
							$submitStatus = "display:none;";
						}
						 echo'<div class="row" id="updateBTN" style="'.$editstatus.'" >
							<button onclick="updateProjectIteration(\'UPDATE\');"><span >Update</span></button>
						</div>
						<div class="row" id="submitBTN" style="'.$submitStatus.'">
							<button onclick="updateProjectIteration(\'ADD\');"><span >Submit</span></button>
						</div>
				</div>';
		}
		public function listProjectVersionHTML($proj_version_list){
			echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label" style="width:300px;"><label>List of Versions :</label></div>
						</div>';

			for($i = 0; $i < sizeof($proj_version_list); $i++) {
				
				echo '
					 <div class="row">
							<div class="label"><label>'.($i+1).'</label></div>
							<label><a href="#" onclick="fetchProjVersion(\''.$proj_version_list[$i]['project_id'].'\',\''.$proj_version_list[$i]['id'].'\');">'.$proj_version_list[$i]['version_name'].'</a></label>
					</div>
				';
			}

			echo '</div>
				<div style="clear: both;"></div>';
		}
		public function listProjectIterationHTML($proj_version_list){
			echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label" style="width:300px;"><label>List of Iterations :</label></div>
						</div>';

			for($i = 0; $i < sizeof($proj_version_list); $i++) {
				
				echo '
					 <div class="row">
							<div class="label"><label>'.($i+1).'</label></div>
							<label><a href="#" onclick="fetchProjIteration(\''.$proj_version_list[$i]['project_id'].'\',\''.$proj_version_list[$i]['id'].'\');">'.$proj_version_list[$i]['iteration_name'].'</a></label>
					</div>
				';
			}

			echo '</div>
				<div style="clear: both;"></div>';
		}
		public function listProjectProductHTML($proj_version_list){
			echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label" style="width:300px;"><label>List of Products :</label></div>
						</div>';

			for($i = 0; $i < sizeof($proj_version_list); $i++) {
				
				echo '
					 <div class="row">
							<div class="label"><label>'.($i+1).'</label></div>
							<label><a href="#" onclick="fetchProjProduct(\''.$proj_version_list[$i]['project_id'].'\',\''.$proj_version_list[$i]['id'].'\');">'.$proj_version_list[$i]['product_name'].'</a></label>
					</div>
				';
			}

			echo '</div>
				<div style="clear: both;"></div>';
		}

		public function fetchuserAction() {  

			$selectedUserID = (int)$this->_request->getParam('selectedUserID');
			$userFirstName = trim(strip_tags($this->_request->getParam('userFirstName')));
			$userLastName = trim(strip_tags($this->_request->getParam('userLastName')));

			if(!empty($selectedUserID))
			{
				$users = AdminDisplay::fetchUserbyID($selectedUserID);				
			}
			else if(!empty($userFirstName) or !empty($userLastName)){
				$users = AdminDisplay::fetchUserbyName($userFirstName,$userLastName);				
			}
			echo '<!--=========== START: COLUMNS ===========-->		
			<div class="message_required" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
			<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';				
				echo '<div class="adminSelect">
								<div class="row" style="float:right;">
												<button id="adminReportbtn" onclick="generateUsers();"><span>View All Users</span></button>
											</div>
							   <div >
								<p>Seach Users:</p>
								<select class="field_medium" name="admin_user_select" id="admin_user_select" onChange="fetchUser(this.value);" >';
								echo AdminDisplay::getUserOptionHTML($selectedUserID);
								echo '</select> (*Choose DPS Users from the list, others search by name)
							</div>
							   <div class="row">
									<p>First Name:</p>
									<input type="text" name="firstName" id="firstName" value="'.$this->view->escape($userFirstName).'" >
								</div>
								 <div class="row" >
									<p>Last Name:</p>
									<input type="text" name="lastName" id="lastName" value="'.$this->view->escape($userLastName).'" >
								</div>
								<div class="row2" >
									 <div>
									<button onclick="fetchUser();"><span>Search</span></button>';
				if(sizeof($users) == '1')
				{	
					$editBTN = "display:block;";
				}
				else
				{
						$editBTN = "display:none;";
				}

				echo '<button onclick="editUser();" id="editButton" style="'.$editBTN.'"><span>Edit</span></button>
					</div>  
					</div>
			</div>';
			echo '<br><hr style="clear:both;margin-top:30px;">';
				if(sizeof($users) =='1')
				{
					$this->buildUserHTML($users[0]);
				}
				else
				{
					if(!empty($userFirstName) or !empty($userLastName)){
						$this->listUserHTML($users);
					}
				}

			echo '</div>					
				<div style="clear: both;"></div>
				<div class="message_required">
					<p>
					</p>					
					<div style="clear: both;"></div>
					
					<div class="duplicate_buttons">
						<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
						<div style="clear: both;"></div>
					</div>
				</div>
				
				';
		}

		public function workorderslaAction() {

			$sla_report_month = strip_tags($this->_request->getParam('sla_report_month'));
            $sla_report_year = date('Y');
			if(empty($sla_report_month))
			{
				$sla_report_month = date('m');
			}
			
			echo '<!--=========== START: COLUMNS ===========-->		
				<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="Work order SLA Report">
					<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">';				
							echo '<div class="adminslaclass" style="padding:20px;" >
                               <table width="100%">
                                    <tr>
                                    <td >
                                        <p> Assigned To:</p>';			
                                        echo '<select class="field_medium" name="admin_assign_select" id="admin_assign_select"  multiple = "multiple"  style="width:240px;">';
                                        echo AdminDisplay::getAllUserOptionHTML();
                                        echo '</select>
                                    </td>
                                     <td>   
                                     
                                        <p> Requested By:</p>';			
                                        echo '<select class="field_medium" name="admin_requested_select" id="admin_requested_select" multiple = "multiple" style="width:240px;">';
                                        echo AdminDisplay::getAllUserOptionHTML();
                                        echo '</select>
                                    </td>
                                    <td colspan="2">
                                        <p> Requested Type:</p>';			
                                        echo '<select class="field_medium" name="admin_requested_type" id="admin_requested_type" multiple = "multiple" style="width:240px;" >
                                        <option value="1">Report an Outage</option>
                                        <option value="3">Submit a Request</option>
                                         <optgroup label="Report a Problem">
                                        <option value="5">Severity 1 (4 hours)</option>
                                        <option value="6">Severity 2 (48 hours)</option>
                                        <option value="7"> Severity 3 (Best Effort)</option>
                                        </optgroup>';
                                        echo '</select>
                                    </td>
                                    
								   </tr>
                                   <tr>
                                    <td  colspan="4">
                                    &nbsp;
                                    </td>
                                </tr>
                                   <tr>
                                   <td>
									<p>From Month:</p>';			
									echo '<select class="customFields" name="admin_user_select" id="admin_user_select" >';
									echo AdminDisplay::getMonths($sla_report_month);
									echo '</select>
                                    </td>
                                    <td>
									<p>From Year:</p>';			
									echo '<select class="customFields" name="admin_year_select" id="admin_year_select" >';
									echo AdminDisplay::getYears($sla_report_year);
									echo '</select>
                                    </td>
                                    
                                    
                                    
								<td >
								<p>To Month:</p>';			
									echo '<select class="customFields" name="admin_to_select" id="admin_to_select" >';
									echo AdminDisplay::to_getMonths($sla_report_month);
									echo '</select>
								</td>
                                <td>
									<p>To Year:</p>';			
									echo '<select class="customFields" name="admin_to_year_select" id="admin_to_year_select" >';
									echo AdminDisplay::to_getYears($sla_report_year);
									echo '</select>
								</td>
                                <td>
                                &nbsp;
                                </td>
									
								</tr>
								<tr>
                                    <td  colspan="4">
                                   &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td  >
                                    <p> Application Category:</p>';	
										echo '<select class="field_medium" name="admin_category_select" id="admin_category_select" multiple = "multiple" style="width:200px;">';
                                     		echo AdminDisplay::getAllApplicationCategory();
                                        echo '</select>
                                       
                                    </td>
									 <td  colspan="3">
                                    <p> Request Completed By:</p>';	
										echo '<select class="field_medium" name="request_completed_by" id="request_completed_by" multiple = "multiple" style="width:200px;">';
                                     		echo AdminDisplay::getAllUserOptionHTML();
                                        echo '</select>
                                       
                                    </td>
                                </tr>
								
                                <tr>
                                    <td  colspan="4">
                                   &nbsp;
                                    </td>
                                </tr>
								<tr>
                                <td colspan="4">
									 <div  style="float:right;width:30%">

									<button id="adminReportbtn" onclick="generateReport(\'xls\');"><span>Generate Report</span></button>
									</div>
                                    <div style="float:right;width:30%">

									<button id="adminReportbtn" onclick="generateReport(\'chart\');"><span>Generate Chart</span></button>
									</div>
								</td></tr>
                                </table>
							</div>';
                            echo '<div  id="sla_chart">
                            </div>';
					echo '</div>						
					<div style="clear: both;"></div>';
			
		}
        
        
        

		public function listUserHTML($users)
		{
			echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label" style="width:300px;"><label>*More than one user select one:</label></div>
						</div>';

			for($i = 0; $i < sizeof($users); $i++) {
				$userName = '';
				if(!empty($users[$i]['last_name']))
				{
					$userName = $users[$i]['last_name'].', '.$users[$i]['first_name'];
				}else{
					$userName = $users[$i]['first_name'];
				}
				echo '
					 <div class="row">
							<div class="label"><label>'.($i+1).'</label></div>
							<label><a href="#" onclick="fetchUser(\''.$users[$i]['id'].'\');">'.$userName.'</a></label>
					</div>
				';
			}

			echo '</div>
				<div style="clear: both;"></div>';
		}

		public function buildUserHTML($users)
		{
			
			$employeeTypeCompany = array(2,136,141);
			if(in_array($users['company'],$employeeTypeCompany) == TRUE){
					$userStatus  = "employee";
			}else{
					$userStatus  = "client";
			}
						
			$userProjectListArray = AdminDisplay::userProjectArray($users['id']);
			if(count($userProjectListArray) > 0){
				$userProjectListStr = implode(",",$userProjectListArray);
			
			}else{
				$userProjectListStr = '';
			}
			
				
			$activeCheck = '';
			$deletedCheck = '';
			$adminCheck = '';
			//echo AdminDisplay::getAllCompaniesProjectOptionEditHTML($users['id'], $userStatus,$users['company'], $userProjectList);
				echo '<div class="admindisplayUserInfo">
						 <div class="row">
								<div class="label"><label>User ID:</label></div>
									<input type="text" class="readonly" readonly name="userID" id="userID" value="'.$this->view->escape($users['id']).'" >
						</div>
						<div class="row">
							<div class="label"><label>First Name:</label></div>
									<input type="text" class="readonly" readonly name="userID" id="userID" value="'.$this->view->escape($users['first_name']).'" >
						</div>
						<div class="row">
							<div class="label"><label>Last Name:</label></div>
									<input type="text" class="readonly" readonly name="userID" id="userID" value="'.$this->view->escape($users['last_name']).'" >
						</div>

						<div class="row">
							<div class="label"><label>Email:</label></div>
									<input type="text" class="readonly" readonly name="userID" id="userID" value="'.$this->view->escape($users['email']).'" >
						</div>
						<div class="row">
							<div class="label"><label>User Title:</label></div><div id="admin_UserTitle_fade" class="admin_UserTitle_fade"></div>
							<select class="field_medium" name="user_title_id" id="user_title_id" multiple>';
								echo AdminDisplay::getUserTitleHTML($users['user_title']);
						echo'</select></div>

						<div class="row">
							<div class="label"><label>User Role:</label></div>
							<select class="field_medium" name="user_Role_id" id="user_Role_id" >';
								echo AdminDisplay::getUserRoleHTML($users['role']);
						echo'</select></div>

						<div class="row">
							<div style="margin-top:3px;" class="label"><label>Vendor:</label></div><input type="text" value="';
							echo AdminDisplay::getUserVendorHTML($users['id']);
						echo '" id="user_vendor_name" name="user_vendor_name"></div>

						<div style="padding-top:3px;" class="row">
							<div class="label"><label>Program:</label></div>
							<select class="field_medium" name="user_program" id="user_program" >';
								echo AdminDisplay::getUserProgramsHTML($users['id']);
							echo'</select></div>

						<div class="row">
							<div class="label"><label>Company:</label></div>
									<input type="text" class="readonly" readonly name="userID" id="userID" value="'. AdminDisplay::getUserCompany($users['company']).'" >
									<input type="hidden" id="user_company" value="'.$this->view->escape($users['company']).'">
						</div>					
						<div class="row">
							<div class="label"><label>Last Login Date:</label></div>
									<input type="text" class="readonly" readonly name="user_last_logged_time" id="user_last_logged_time" value="'. AdminDisplay::getLastLoggedInTime($users['id']).'" >
									
						</div>		
						
						<div class="row">
							<div class="label"><label>Basecamp ID:</label></div>
								<input type="text" class="readonly" readonly name="userID" id="userID" value="'. $this->view->escape($users['bc_id']).'" >
						</div>';

						if($users['login_status']=='admin')
						{
							$adminCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Admin:</label></div>
								<input type="checkBox" class="adminCheckBox" DISABLED style="width:10px;" name="userAdminAccess" id="userAdminAccess" value="" '.$adminCheck.'>
						</div>';
						if($users['active']=='1')
						{
							$activeCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Active:</label></div>
								<input type="checkBox" class="adminCheckBox" DISABLED style="width:10px;" name="userActiveStatus" id="userActiveStatus" value="" '.$activeCheck.'>
						</div>';

						if($users['deleted']=='1')
						{
							$deletedCheck = " checked";
						}
						echo '<div class="row">
							<div class="label"><label>Deleted:</label></div>
								<input type="checkBox" class="adminCheckBox" DISABLED style="width:10px;" name="userDeletedStatus" id="userDeletedStatus" value="" '.$deletedCheck.'>
						</div>
						<div class="row" >
							<div class="label2"><label><u>User Project Permission:</u></label></div>
							
							<input type="hidden" name="userStatus" id="userStatus" value="'.$this->view->escape($userStatus).'">
							<div style="margin-left:-73px;">
							<select class="field_medium" name="userProjectArray" id="userProjectArray" multiple="multiple">';
								echo AdminDisplay::getAllCompaniesProjectOptionEditHTML($users['id'], $userStatus,$users['company'],$userProjectListArray);
						echo'</select></div></div>
						<div class="row">
							<div class="label2"><label><u>User Access</u></label></div>						
						</div>';
						$user_access_bits = $users['user_access'];
						global $USER_ACCESS;
						$html = "";
						echo '<script type="text/javascript">';
						echo ' var USER_ACCESS = Array(); ';
						$i = 0;
						foreach($USER_ACCESS as $controller => $access)
						{

							$user_access_bit = $user_access_bits[$i];
							$access_name = $controller."_ACCESS";
							echo "USER_ACCESS[".$i."] = '".$access_name."'; ";
							if($access == '1')
							{
								$access_check = "";
								if($user_access_bit == '1')
								{
									$access_check = " checked";
								}

								$html.= '
								<div class="row">
								<div class="label"><label>' . str_replace("_", " ", $controller) . '</label></div>
									<input type="checkBox" class="adminCheckBox" DISABLED style="width:10px;" name="'.$access_name.'" id="'.$access_name.'" value="" '.$access_check.'>
								</div>';
							}
							$i++;
						}
					echo '</script>';
						echo $html;
						 echo '<div class="row" id="updateButton">
							<button onclick="updateUser(USER_ACCESS);"><span >Update</span></button>
						</div>
				</div>';			

		}

		public function customfieldnameAction(){
			$custom_name = $this->_request->getParam('custom_name');
			$field_id = $this->_request->getParam('field_id');

			$field_list = AdminDisplay::fetchFieldList($custom_name);
			$field_names = array("REQ_TYPE" => "Required Type",
									"SEVERITY" => "Severity",
									"SITE_NAME" => "Site Name",
									"INFRA_TYPE" => "Infrastructure Type",
									"CRITICAL" => "Critical",
									"QA_CATEGORY" => "QA Category",
									"QA_STATUS" => "QA Status",
									"QA_SEVERITY" => "QA Severity",
									"QA_OS" => "QA OS",
									"QA_BROWSER" => "QA Browser",
									"QA_DETECTED_BY" => "QA Detected BY",
									"QA_ORIGIN" => "QA Origin"
			);
			$custom_fields = AdminDisplay::fetchCustomFields();
			$addBTN = 'display:block;';
			if(isset($field_names[$custom_name])){
				$screen_name = $field_names[$custom_name];
			}else{
				$screen_name = 'Custom Fields';
			}
		
			echo '
  				<!--=========== START: CUSTOM FIELD NAMES ===========-->
				<div class="message_required" style="width:3000px;height:1024px;position:fixed;background-color:#ffffff;z-index:1;margin-top:-500px;margin-left:-630px;opacity: 0.3; filter: alpha(opacity = 30); zoom:1;"></div>
  				<input type="hidden" name="adminTitlemsg" id="adminTitlemsg" value="' . $this->view->escape($screen_name). '">
					<div class="rightCol" id="form_sec_1" style="display: block;min-height:400px;">
						<div class="label" style="min-height:20px;"></div>
						<div id="customEditOrAdd">
							<label>Custom Field :</label>
							<select id="customFields" onchange="customFieldName();">
								<option value="default">-- Select Field --</option>';
								foreach($custom_fields as $values){
									$selectable = '';
									if($values['field_key'] == $custom_name){
										$selectable = 'selected';
									}
									echo '<option value="'.$values['field_key'].'" ' . $selectable .'>'.$field_names[$values['field_key']].'</option>';
								}
				echo '
							</select>
						</div>';
				if($custom_name != "default" && $custom_name != 'undefined' && !empty($custom_name)){
				$addBTN = 'display:block;';
				if(isset($field_id)){
					$addBTN = 'display:none;';
				}
				echo '
						<hr style="clear:both;margin-top:30px;">
						<div class="admindisplaySiteInfo" >
  							<button onclick="addNewSite();" id="addButton" style="'.$addBTN.'"><span>Add A New Field Value</span></button>
						</div>
			';
				if(empty($field_id)){
				$this->buildSiteNameHTML($field,$screen_name );
			}else{
					foreach($field_list as $field){
						if($field_id == $field['field_id']){
							$this->buildSiteNameHTML($field,$screen_name);
					}
				}
			}
			echo '
						<br><hr style="clear:both;margin-top:30px;">
			';

				$this->listSiteNamesHTML($field_list, $screen_name);
			echo '
					</div>
					<div style="clear: both;"></div>
					<div class="message_required">
						<p></p>
						<div style="clear: both;"></div>
						<div class="duplicate_buttons">
							<button onClick="$(\'.message_required\').css({display:\'none\'}); return false;"><span>OK</span></button>
							<div style="clear: both;"></div>
							</div>';
			}
			echo '
						</div>
				<!--=========== END  : CUSTOM FIELD NAMES ===========-->	
			';
			$this->render('create');
		}

		public function buildSiteNameHTML($site='',$screen_name){
		//	print $custom_name;
			$activeCheck = '';
			$deleteCheck = '';
			$siteName = '';
			$siteId = '';
			if(empty($site)){
				$editstatus = 'display:none;';
				$editType = 'Add';
			}else{
				$siteName = $site['field_name'];
				$siteId = $site['field_id'];
				$activeCheck =  ($site['active'] == '1') ? ' checked': '';
				$deleteCheck = ($site['deleted'] == '1') ? ' checked': '';
				$editstatus = 'display:block;';
				$editType = 'Update';
			}
			echo '
						<div id="fieldnameInfo" class="admindisplayUserInfo"  style="'.$this->view->escape($editstatus).'">
						<div class="row">
								<div class="label"><label>'.$this->view->escape($screen_name).'*:</label></div>
								<input type="text" class="" name="fieldname" id="fieldname" value="'.$this->view->escape($siteName).'" >
								<input type="hidden" class="" name="fieldid" id="fieldid" value="'.$this->view->escape($siteId).'" >
							</div>
												
							<div class="row">
								<div class="label"><label>Active:</label></div>
								<input type="checkBox" style="width:10px;" name="fieldActiveStatus" id="fieldActiveStatus" value="" '.$this->view->escape($activeCheck).'>
							</div>
							<div class="row">
								<div class="label"><label>Deleted:</label></div>
								<input type="checkBox" style="width:10px;" name="fieldDeleteStatus" id="fieldDeleteStatus" value="" '.$this->view->escape($deleteCheck).'>
							</div>
							<div class="row" id="submitBTN">
								<button onclick="updateFieldValue(\''.strtoupper($this->view->escape($editType)).'\');"><span >'.$this->view->escape($editType).'</span></button>
							</div>
						</div>
			';
		}

		public function listSiteNamesHTML($site_list, $screen_name){
			echo '
						<div class="admindisplayUserInfo">
							<div class="row">
								<div class="label" style="width:235px;"><label>List of '.$this->view->escape($screen_name).' :</label></div>
							</div>
			';

			for($i = 0; $i < sizeof($site_list); $i++) {
				echo '
							<div class="row">
								<div class="label"><label>'.($i+1).'</label></div>
								<label><a href="#" onclick="customFieldName(\''. $site_list[$i]['field_id'] .'\');">' . $site_list[$i]['field_name'] . '</a></label>
							</div>
				';
			}

			echo '
						</div>
						<div style="clear: both;"></div>
			';
		}
		
		public function qagridAction(){
			$qaGridData = AdminDisplay::getQualityGrid();
			if(count($qaGridData) > 0){
				$this->view->assign("qaGridData",$qaGridData);
			}
			
			
		}
		
		public function inserqadataAction(){
			$pid = $this->_request->getParam('pid');
			$result = AdminDisplay::upDateProjectQAPermission($pid);
			$this->view->assign('pid',$this->_request->getParam('pid'));
			$this->_helper->layout->disableLayout();
			echo $result;
		}
		
		public function rallyprojectmapAction(){
			$rallyArray = array();
			//All rally project
			$rallyProject = AdminDisplay::getRallyProjectOptionEditHTML();
			foreach($rallyProject as $r_key => $r_val){
				//if($r_val['workspace_id'] == ''){
					
					$rallyArray[$r_val['workspace_name']][] = array('name'=>$r_val['project_name'],'id'=>$r_val['project_id']);
					
					//}
			}
			$this->view->assign('rallyArray',$rallyArray);
			define("PROJECT_ID",0);
			$proj_id = PROJECT_ID;
			//All LH project
			$this->view->assign('LHProjectHtml',AdminDisplay::getLHProjectOption()); 
			//All Mapped projects
			$this->view->assign('LhRallyProjects',AdminDisplay::getLHRallyProjects($proj_id));
			
		}
		
		function maprojectlistingAction(){
			$lastInsertId = 0;
			$lh_project = AdminDisplay::safeSql($this->_request->getParam('lh'));
			$rally_project = AdminDisplay::safeSql($this->_request->getParam('rally'));
			if(!empty($lh_project) && (!empty($rally_project))){
				$mappedArray = array();
				$mappedArray = array(
					"lh_project_id" => $lh_project,
					"rally_project_id" => $rally_project,
					"active" => "1",
					"deleted" =>"0",
				);
				$lastInsertId = AdminDisplay::maapingLhRallyProject($mappedArray);
				$lastInsertId = trim($lastInsertId);
				$lastInsertRow = AdminDisplay::getLHRallyProjects($lastInsertId);
				$this->view->assign('LhRallyProjects',$lastInsertRow);
			}
			
			$this->_helper->layout->disableLayout();
			//echo $result;
		
		}
		
		function deletemaprojectlistingAction(){
			$affectedId = 0;
			$id = (int) $this->_request->getParam('id');
			
			if(!empty($id)){
				$affectedId = AdminDisplay::deleteMaapingLhRallyProject($id);
			}
			if(count($affectedId) > 0){
				echo $affectedId;
			}
			$this->_helper->layout->disableLayout();
			
		
		}
function deletebsmaprojectlistingAction(){
			$affectedId = 0;
			$id = (int) $this->_request->getParam('id');
			
			if(!empty($id)){
				$affectedId = AdminDisplay::deleteMaapingLhBsProject($id);
			}
			if(count($affectedId) > 0){
				echo $affectedId;
			}
			$this->_helper->layout->disableLayout();
			
		
		}
		
		function solrsearchlogAction(){
		
			$searchData = AdminDisplay::getSearchLog();
			$this->view->assign('searchData',$searchData);
			$uniqueUsers = AdminDisplay::getUniqueUsers();
			$this->view->assign('uniqueUsers',$uniqueUsers);
		
		}
		
		
		function exportusersAction(){
			$adminModel = new AdminDisplay();
			$resultUsers = $adminModel->getUserLastLoginTime();
			//$response = $this->getResponse();
			//$header = '';
			$i = 0;
 			$excelUsers = array();
			foreach($resultUsers as $k => $v){
				$active = ($resultUsers[$k]['active'] == '1')?'Active':'unActive';
 				$d = ($resultUsers[$k]['deleted'] == '1')?"Deleted":'Active';
 				$excelUsers[$i]['id'] = $resultUsers[$k]['id'];
 				$excelUsers[$i]['email'] = $resultUsers[$k]['email'];
 				$excelUsers[$i]['first_name'] = $resultUsers[$k]['first_name'];
 				$excelUsers[$i]['last_name'] = $resultUsers[$k]['last_name'];
 				$excelUsers[$i]['company'] = $adminModel->getUserCompany($resultUsers[$k]['company']);
 				$excelUsers[$i]['login_status'] = $resultUsers[$k]['login_status'];
 				$excelUsers[$i]['active'] = $active;
				$excelUsers[$i]['delete'] = $d;
 				$excelUsers[$i]['user_access'] = $resultUsers[$k]['user_access'];
 				$excelUsers[$i]['last_logged_date'] = $resultUsers[$k]['logged_time'];
				
				$i++;
 							
 			}
 		
			$this->view->assign('excelUsers',$excelUsers);
		
			
			
		}
		
		public function lhbasecampmappingAction(){
			$bsProject = AdminDisplay::getBasecampProjectOptionEditHTML();
			//print_r($bsProject);
			$this->view->assign('bsProjectMapping',$bsProject);
			//All LH project
			$this->view->assign('basecampProjectObj',AdminDisplay::getBasecampProjectOption()); 
			//All Mapped projects
			$this->view->assign('LHUsersHtml',AdminDisplay::getLHUsersObj());
			
		}
		
		
		
		
		public function basecampmaprojectlistingAction(){
			$db = Zend_Registry::get('db');
			$bs_project_id = (int)$this->_request->getParam('lh_basecamp');
			$assigned_to = (int)$this->_request->getParam('lh_users');
			
			if(($bs_project_id != '')&&($assigned_to != '')){
				$inser_array = array(
				'bc_id' => $bs_project_id,
				'assigned_to' => $assigned_to,
				'created_by'  => $_SESSION['user_id'],
				'created_on'  => date("Y-m-d")
				);
			
			
				$db->insert('lh_basecamp_mapping',$inser_array);
				$lastId = $db->lastInsertId()	;	
				$bsProject = AdminDisplay::getBasecampProjectOptionEditHTML($lastId);
				//$lastInsertRow = AdminDisplay::getLHRallyProjects($lastInsertId);
				$this->view->assign('bsProjectMapping',$bsProject);
				$this->_helper->layout->disableLayout();
			}
			//$this->_helper->viewRenderer->setNoRender(TRUE);
			
		}
        
        function slachartAction(){
        
            $this->_helper->layout->disableLayout();
        
        }
		function categorymappingAction(){
			
			$adminModel = new AdminDisplay();
			$allItems = $adminModel->getSiteNames();
			$this->view->assign("sitenames", $allItems);
			$this->view->assign("categories", AdminDisplay::getApplicationCategory());
        
            //$this->_helper->layout->disableLayout();
        
        }
		
		function addapplicationcatAction(){
			$app_cat_ids = array();
			$cat_id = $this->_request->getParam('cat_id');
			$app_cat_ids = $this->_request->getParam('app_cat_ids');
			$adminModel = new AdminDisplay();
			
			if((count($app_cat_ids) > 0) AND (!empty($cat_id))){
				
				$adminModel->resetApplicationCatIds($cat_id,$app_cat_ids);
				
			
			}
			echo "Category Items has been Updated";	
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		
		
		
		
		}
		
		public function categorylistAction(){
		
			
			$cat_id = $this->_request->getParam('cat_id');
			$adminModel = new AdminDisplay();
			$catItems = $adminModel->getApplicationSiteName($cat_id);
			
			
			$this->view->assign("categories", $catItems);
			$this->_helper->layout->disableLayout();
			
		
		}
		
		public function getcategorydetailsAction(){
			$cat_id = $this->_request->getParam('cat_id');
			if(!empty($cat_id)){
				$adminModel = new AdminDisplay();
				$catItems = $adminModel->getcategoryDetails($cat_id);
				echo json_encode($catItems);
			}
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
		
		
		}
		
		public function addcategoryAction(){
			$newCat = $this->_request->getParam('newCat');
		
			$adminModel = new AdminDisplay();
			if($newCat != ''){
				$checkNameCount = $adminModel->checkCategoryName($newCat);
				if($checkNameCount == 0){
					$dataArray = array("category_name" => $newCat, "active" => "1", "deleted" => "0");
					$catItems = $adminModel->addCategory($dataArray);
					echo trim(str_replace("\n",'',$catItems))."~##~".$newCat;
					
				}else{
					echo "Exist";
									
				}
			}
			
			$this->_helper->layout->disableLayout();
			//$this->_helper->viewRenderer->setNoRender(TRUE);
			
			
			
		
		
		
		}
		
		function updatecategoryAction(){
		
			$newCat = $this->_request->getParam('catId');
			$deleted = $this->_request->getParam('deleted');
			$adminModel = new AdminDisplay();
			if($newCat != ''){
					$dataArray = array( "deleted" => $deleted );
					$catItems = $adminModel->updateCategory($dataArray,$newCat);
					
					
			}
			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(TRUE);
		
		}
		
	}
	
?>


