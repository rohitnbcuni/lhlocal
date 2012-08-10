<?PHP
	Zend_Loader::loadClass('Zend_Controller_Action');
	include('ControlTower.inc');
	include("fckeditor.php");
	
	class Controltower_IndexController extends Zend_Controller_Action { 
		private $cur_section;
		public function indexAction() {
			
			echo '<!--=========== START: COLUMNS ===========-->
			<!--==| START: Bucket |==-->
			<div class="main_actions">
				<button onClick="window.location = \'/controltower/index/create/\'"><span>create new project</span></button>
				<!--<button><span>duplicate project</span></button>
				<button><span>archive project</span></button>
				<div class="right_actions">
					<button><span>go to project</span></button>
					<button class="trash"><span>delete project</span></button>
				</div>-->
			</div>
			<!--==| END: Bucket |==-->

			<!--==| START: Bucket |==-->
			<div class="title_med">
				<label>Company</label>
				<select id="company_filter" onchange="filterProjects()">
					<option value="0" selected="selected">All Companies</option>'
					.CtDisplay::getCompanyHTML()
				.'</select>
				<label>Project</label>
				<input type="text" value="" name="project_filter" id="project_filter" onkeyup="filterProjects()">
				<div class="right_actions_buttons">
					<button class="monitors"><span>total budget: $' .CtDisplay::financeTotal() .'</span></button>
					<button class="monitors"><span>total active projects: ' .CtDisplay::totalActiveProjects() .'</span></button>
				</div>
			</div>
			<!--==| END: Bucket |==-->

			<!--==| START: Bucket |==-->
			<div class="project_status_title"><a href="#" id="active_link" onClick = "toggleActiveList(); return false;"  class="expand_toggle <!--expand_toggle_closed-->">Active Projects</a></div>
			<div id="active_list" style="display: block;" class="<!--results_collapsed-->">
				<ul class="project_filters">
					<li class="project"><a href="#" class="down" id="project_sort" onClick="setSort(\'project\',\'active\'); return false;">project</a></li>
					<li class="actual"><a href="#" id="todate_sort" onClick="setSort(\'todate\',\'active\'); return false;">to date</a></li>
					<li class="budget"><a href="#" id="budget_sort" onClick="setSort(\'budget\',\'active\'); return false;">budget</a></li>
					<li class="completeness"><a href="#" id="complete_sort" onClick="setSort(\'complete\',\'active\'); return false;">project completeness</a></li>
				</ul>
				<div class="project_results_container">
					<dl class="project_results" id="active_project_list">

					</dl>
				</div>
			</div>
			<!--==| END: Bucket |==-->

			<!--==| START: Bucket |==-->
			<div class="project_status_title"><a href="#" id="archive_link" onClick = "loadArchiveList(); return false;" class="expand_toggle expand_toggle_closed">Archived Projects</a></div>
			<div style="display: none;" id="archive_list" class="<!--results_collapsed-->">
				<ul class="project_filters archived_projects">
					<li class="project"><a href="#" id="project_asort" onClick="setSort(\'project\',\'archive\'); return false;">project</a></li>
					<li class="actual"><a href="#" id="todate_asort" onClick="setSort(\'todate\',\'archive\'); return false;">to date</a></li>
					<li class="budget"><a href="#" id="budget_asort" onClick="setSort(\'budget\',\'archive\'); return false;">budget</a></li>
					<li class="completeness"><a href="#" id="complete_asort" onClick="setSort(\'complete\',\'archive\'); return false;">project completeness</a></li>
				</ul>
				<div class="project_results_container project_results_archived">
					<dl class="project_results archived_projects2" id="archive_project_list">

					</dl>
				</div>
			</div>
			<!--==| END: Bucket |==-->
			<!--=========== END: COLUMNS ===========-->
			
			
				<div class="duplicate_pop_up">
					<form action="/duplicate_project.php" method="POST">
					<ul>
						<input type="hidden" id="hidden_id" name="project_id"> 
						<li>
							<label>Client</label>
							<select name="project_client">
								<option value="">Client</option>
							</select>
						</li>
						<li>
							<label>Project Code</label>
							<input size=10 name="project_code" type="text">
						</li>
						<li>
							<label>Project Name</label>
							<input size=25 name="project_name" type="text">
						</li>
					</ul>
					
					<div style="clear: both;"></div>
					
					<div class="duplicate_buttons">
						<button><span>Duplicate</span></button> <button class="cancel" onClick="$(\'.duplicate_pop_up\').css({display:\'none\'}); return false;"><span>Cancel</span></button>
						<div style="clear: both;"></div>
					</div>
					</form>
				</div>

			';
		}
		public function createAction() {
			$_session = new Zend_Session_Namespace('Zend_BC_Auth');
			Zend_Session::regenerateId();
			
			$project_data = "";
			$company_data = "";
			
			if(isset($_GET['project_id'])) {
				$project_id = $_GET['project_id'];
				
				$project_data = CtDisplay::getQuery("SELECT * FROM `projects` WHERE `id`='$project_id' LIMIT 1");
				//print_r($project_data);
				$company_data = CtDisplay::getQuery("SELECT * FROM `companies` WHERE `id`='" .$project_data[0]['company'] ."' LIMIT 1");
				$proj_completeness = CtDisplay::getProjectCompleteness($project_id);
			} else {
				$project_id = "";
				$proj_completeness = "0";
			}
			
			echo '<!--=========== START: COLUMNS ===========-->
		
			<!--==| START: Bucket |==-->
			<div class="title_lrg">
				
				<div class="close_block">
					<button id="cancel_button" class="cancel" onClick="document.location = \'/controltower/\'"><span>CANCEL</span></button>
					<button id="back_button" class="back_arrow" onClick="document.location = \'/controltower/\'" style="display: none;"><span>all projects</span></button>
				</div>
				<form name="project_main" onSubmit="return false;">
				<input type="hidden" name="project_id" id="project_id" value="' .$project_id .'" />
				<div class="form_blocks" id="createcompany" style="display: ';
						if(isset($_GET['project_id'])) {	
							echo "none";
						} else {
							echo "block";
						}
					echo ';">
					<label>Select Client/Company</label>
						<select class="pClient" onChange="ajaxFunction(\'\',1); return false;" name="create_company" id="create_company">
							<option value="">--Select Company--</option>'
							.CtDisplay::getCompanyBcHTML()
						.'</select>
				</div>
				
				<div class="form_blocks" id="create_code" style="display: ';
						if(isset($_GET['project_id'])) {	
							echo "none";
						} else {
							echo "block";
						}
					echo ';">
					<label>Project Code</label>
					<input class="pCode" readonly type="text" value="" name="projectCode" id="projectCode">
				</div>
				
				<div class="form_blocks"  id="create_name" style="display: ';
						if(isset($_GET['project_id'])) {	
							echo "none";
						} else {
							echo "block";
						}
					echo ';">
					<label>Project Name</label>
					<input class="pName" type="text" value="" name="projectName" id="projectName">
				</div>
				
				<div class="form_blocks"  id="create_name">
					<h4 id="display_company" style="display: ';
						if(isset($_GET['project_id'])) {	
							echo "block";
						} else {
							echo "none";
						}
					echo ';">';
						if(isset($_GET['project_id'])) {
							echo $company_data[0]['name'] .": " .$project_data[0]['project_code'] ."&nbsp; - " .$project_data[0]['project_name'];
						}
					echo '</h4>				
					<!--<h4 id="display_code" style="display: none;">
					</h4>				
					<h4 id="display_name" style="display: none;">
					</h4>-->
				</div>

				<div class="form_blocks createProject" id="project_create" style="display: ';
						if(isset($_GET['project_id'])) {	
							echo "none";
						} else {
							echo "block";
						}
					echo ';">
					<button id="createProject" class="active" onClick="project_mode(); return false;"><span>Create Project</span></button>
				</div>
				<div class="project_complete" id="project_progress" style="display: ';
						if(isset($_GET['project_id'])) {	
							echo "block";
						} else {
							echo "none";
						}
					echo ';">
					<label id="progress_percent_text">PROJECT brief COMPLETENESS: ' .$proj_completeness .'%</label>
					<div class="progressBar" id="progress_insider_bar">
						<div class="insideBar" style="width: ' .$proj_completeness .'%;"></div>
					</div>
				</div>
				</form>
			</div>
			<!--==| END: Bucket |==-->
		
			<!--==| START: Bucket |==-->
			<div class="full_content_container_noscroll control_tower_main">
				<div class="new_project_dimmer" id="new_project_dimmer"style="display: ';
						if(isset($_GET['project_id'])) {	
							echo "none";
						} else {
							echo "block";
						}
					echo ';"></div>
				<div class="save_project_container" id="ajax_loader" style="display: none;">
					<div class="save_project_loader">
						<img src="/_images/ajax-loader.gif" alt="Loading..." />
					</div>
				</div>
				<div id="add_custom_role" style="display: none;">
					<form action="" method="post" name="addrole" onSubmit="return false;">
						<label>Resource Type</label>
						<select name="resource" onChange="alert(\'get resource name and id\');">
							<option value="">-Select Resource Type-</option>
							<option value="1">test resource</option>
						</select>
						<label>User</label>
						<select name="user" onChange="alert(\'change email and phone vals\');">
							<option value="">-Select User-</option>
							<option value="1">test user</option>
						</select>
						<label>Email</label>
						<input name="email" readonly type="text" value="" />
						<label>Phone</label>
						<input name="phone" readonly type="text" value="" />
						<input type="submit" value="Cancel" onClick="document.getElementById(\'add_custom_role\').style.display = \'none\'; return false;" />
						<input type="submit" value="OK" onClick="addRole(addrole); return false;" name="add_role" />
					</form>
				</div>
				<div class="contentCol" id="create_columns">
					<div class="leftCol" id="section_menu">';
							if(isset($_GET['project_id'])) {
								echo CtDisplay::getSectionEditHTML($_GET['project_id']);
							} else {
								echo CtDisplay::getSectionHTML();
							}
						echo '<div class="project_nav_buttons">
								<button class="status status_complete" onClick="ct_allComplete();"><span>Mark All Complete</span></button>	
								<button class="secondary" onClick="ct_pdfExport();"><span>Export to PDF</span></button>
							</div>
					</div>
					
					<div class="rightCol" id="form_sec_1" style="display: block;">
						<form action="" method="post" name="form_sec_1" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearFCK(\'descEditor\');"><span>empty</span></button>
								<button class="status status_draft" onClick="ajaxFunction(\'\',\'update_project_desc\'); changeSectionStatusMan(\'sec_1\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearFCK(\'descEditor\');"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_1_save" onClick="ajaxFunction(\'\',\'update_project_desc\'); changeSectionStatusMan(\'sec_1\'); setCompleteness();"><span>save</span></button>	
								<!--<button class="secondary" onClick="document.getElementById(\'form_sec_1_save\').click(); document.getElementById(\'form_sec_1_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>-->
								<button class="secondary" onClick="nxtFalse(); this.click(); ctCreateSectionsSwitchNextDesc();"><span>next</span></button>
							</div>
						</div>
						<div class="inside textEdit">';
							//javascript:submit(); 
							$descBasePath = $_SERVER['PHP_SELF'];
							$descBasePath = substr( $descBasePath, 0, strpos( $descBasePath, "_samples" ) );

							$descEditor = new FCKeditor('descEditor');
							$descEditor->BasePath = $descBasePath;
							$descEditor->Config['AutoDetectLanguage'] = true;
							$descEditor->Config['ToolbarCanCollapse'] = false;
							$descEditor->Config['DefaultLanguage'] = 'en';
							$descEditor->Config['EnterMode'] = 'br';
							$descEditor->ToolbarSet = "NBC";
							if(isset($_GET['project_id'])) {
								$project_description = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='1' LIMIT 1");
								$descEditor->Value = $project_description[0]['desc'];
							} else {
								$descEditor->Value = '';
							}
							$descEditor->Create();
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_2" style="display: none; height: auto;">
						<form action="" method="post" name="form_sec_2" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearRoles();"><span>empty</span></button>
								<button class="status status_draft" onClick="saveRoles(); changeSectionStatusMan(\'sec_2\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearRoles();"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_2_save" onClick="saveRoles(); changeSectionStatusMan(\'sec_2\'); setCompleteness();"><span>save</span></button>	
								<button class="secondary" onClick="document.getElementById(\'form_sec_2_save\').click();document.getElementById(\'form_sec_2_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	
							</div>
						</div>
						<!--<script>
							$(document).ready(function(){						
								$("#project_roles").sortable({
									start: function(e,ui) {
											ui.helper.addClass(\'dragged\');
										},
									beforeStop: function(e,ui) {
											ui.helper.removeClass(\'dragged\');
										},
										revert: true,
										
								});
							});
						</script>-->
						<div class="inside resources">
							<div class="proles_header">
								<h3>PROJECT OWNER & ROLES</h3>
								<div class="proles_options">
									<!--
									<select>
										<option>Select Project Type</option>
									</select>
									<button onClick="document.getElementById(\'add_custom_role\').style.display = \'block\';"><span>ADD CUSTOM ROLE</span></button>
									-->
								</div>
								<div style="clear: both;"></div>
							</div>
							
							<ul class="proles" id="project_roles">';
								if(isset($_GET['project_id'])) {
									echo CtDisplay::getOwnerRolesNewEditHTML($_GET['project_id']);
								} else {
									echo CtDisplay::getOwnerRolesNewHTML();
								}
							echo '</ul>
							</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_3" style="display: none;">
						<form action="" method="post" name="form_sec_3" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearTimeline();"><span>empty</span></button>
								<button class="status status_draft" onClick="saveTimeline();  changeSectionStatusMan(\'sec_3\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearTimeline();"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_3_save" onClick="saveTimeline(); changeSectionStatusMan(\'sec_3\'); setCompleteness();"><span>save</span></button>	
								<button class="secondary" onClick="document.getElementById(\'form_sec_3_save\').click();document.getElementById(\'form_sec_3_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	
							</div>
						</div>
						<div class="inside resources">
							<div class="proles_header">
								<h3>PROJECTED TIMELINE FOR Project</h3>

								<div style="clear: both;"></div>
							</div>';
							if(isset($_GET['project_id'])) {
								echo CtDisplay::getTimelineEditHTML($_GET['project_id']);
							} else {
								echo CtDisplay::getTimelineHTML();
							}
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_4" style="display: none;">
						<form action="" method="post" name="form_sec_4" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearFCK(\'scopeEditor\');"><span>empty</span></button>
								<button class="status status_draft" onClick="ajaxFunction(\'\',\'update_project_scope\'); changeSectionStatusMan(\'sec_4\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearFCK(\'scopeEditor\');"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_4_save" onClick="ajaxFunction(\'\',\'update_project_scope\'); changeSectionStatusMan(\'sec_4\'); setCompleteness();"><span>save</span></button>	
								<!--button class="secondary" onClick="document.getElementById(\'form_sec_4_save\').click();document.getElementById(\'form_sec_4_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->
								<button class="secondary" onClick="nxtFalse(); this.click(); ctCreateSectionsSwitchNextScope();"><span>next</span></button>
							</div>
						</div>
						<div class="inside textEdit">';
							$scopeBasePath = $_SERVER['PHP_SELF'];
							$scopeBasePath = substr( $scopeBasePath, 0, strpos( $scopeBasePath, "_samples" ) );

							$scopeEditor = new FCKeditor('scopeEditor');
							$scopeEditor->BasePath = $scopeBasePath;
							$scopeEditor->Config['AutoDetectLanguage'] = true;
							$scopeEditor->Config['ToolbarCanCollapse'] = false;
							$scopeEditor->Config['DefaultLanguage'] = 'en';
							//$approvalEditor->Config['EnterMode'] = 'br';
							$scopeEditor->ToolbarSet = "NBC";
							if(isset($_GET['project_id'])) {
								$project_scope = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='4' LIMIT 1");
								$scopeEditor->Value = $project_scope[0]['desc'];
							} else {
								$scopeEditor->Value = '';
							}
							$scopeEditor->Create();
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_5" style="display: none;">
						<form action="" method="post" name="form_sec_5" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty"><span>empty</span></button>
								<!--<button class="status status_draft" onClick="changeSectionStatusMan(\'sec_5\'); setCompleteness();"><span>draft</span></button>-->
								<button class="status status_complete" onClick="setSectionComplete();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_5_save" onClick="changeSectionStatusMan(\'sec_5\'); setCompleteness();"><span>save</span></button>	
								<button class="secondary" onClick="document.getElementById(\'form_sec_5_save\').click();document.getElementById(\'form_sec_5_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	
							</div>
						</div>
						<div class="inside textEdit">
							<div class="inside resources">
							<div class="resource_header"><h3>'.DEV_TEAM_NAME.' RESOURCES</h3></div>
							
							<div class="resource_row">
								<div class="resource_name"><span>UXD Resource:</span>Dmitry Zak</div>
								<div class="resource_hours"><span>Actual Hours:</span>9 hours</div>
								<div class="resource_hours booked_hours"><span>Booked Hours:</span>9 hours</div>
							</div>
							<div class="resource_row">
								<div class="resource_name"><span>UXD Resource:</span>Dmitry Zak</div>
								<div class="resource_hours"><span>Actual Hours:</span>9 hours</div>
								<div class="resource_hours booked_hours"><span>Booked Hours:</span>9 hours</div>
							</div>
							<div class="resource_row">
								<div class="resource_name"><span>UXD Resource:</span>Dmitry Zak</div>
								<div class="resource_hours"><span>Actual Hours:</span>9 hours</div>
								<div class="resource_hours booked_hours"><span>Booked Hours:</span>9 hours</div>
							</div>
							<div class="resource_row">
								<div class="resource_name"><span>UXD Resource:</span>Dmitry Zak</div>
								<div class="resource_hours"><span>Actual Hours:</span>9 hours</div>
								<div class="resource_hours booked_hours"><span>Booked Hours:</span>9 hours</div>
							</div>
							
						</div>';
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_6" style="display: none;">
						<form action="" method="post" name="form_sec_6" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearFinance();"><span>empty</span></button>
								<button class="status status_draft" onClick="saveFinance(); changeSectionStatusMan(\'sec_6\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearFinance();"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_6_save" onClick="saveFinance(); changeSectionStatusMan(\'sec_6\'); setCompleteness();"><span>save</span></button>	
								<button class="secondary" onClick="document.getElementById(\'form_sec_6_save\').click();document.getElementById(\'form_sec_6_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	
							</div>
						</div>
						<div class="inside textEdit" id="finance_calcs">
							<div class="finance_budget_header">
								<h3>FINANCE &amp; BUDGET</h3>
								<div class="finance_budget_options">
									<label>BUDGET CODE</label>
									<input type="text" name="fin_budget_code" id="fin_budget_code" value="';
									if(isset($_GET['project_id'])) {
										echo $project_data[0]['budget_code'];
									}
									echo '" />
									<!--<button><span>ADD ITEM</span></button>-->
								</div>
							</div>';
								if(isset($_GET['project_id'])) {
									echo CtDisplay::getFinanceEditHTML($_GET['project_id']);
								} else {
									echo CtDisplay::getFinanceHTML();
								}
							echo '<div class="finance_bar_spacer"></div>							
							<div class="finance_bar">
									<div id="overall_finance_total">Overall Total: <span>$' .CtDisplay::getFinanceTotalEdit($_GET['project_id']) .'</span></div>
							</div>';
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_7" style="display: none;">
						<form action="" method="post" name="form_sec_7" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearFCK(\'deliverEditor\');"><span>empty</span></button>
								<button class="status status_draft" onClick="ajaxFunction(\'\',\'update_project_deliverables\'); changeSectionStatusMan(\'sec_7\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearFCK(\'deliverEditor\');"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_7_save" onClick="ajaxFunction(\'\',\'update_project_deliverables\'); changeSectionStatusMan(\'sec_7\'); setCompleteness();"><span>save</span></button>	
								<!--<button class="secondary" onClick="document.getElementById(\'form_sec_7_save\').click();document.getElementById(\'form_sec_7_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->
								<button class="secondary" onClick="nxtFalse(); this.click(); ctCreateSectionsSwitchNextDeliver();"><span>next</span></button>
							</div>
						</div>
						<div class="inside textEdit">';
							$deliverBasePath = $_SERVER['PHP_SELF'] ;
							$deliverBasePath = substr( $deliverBasePath, 0, strpos( $deliverBasePath, "_samples" ) );

							$deliverEditor = new FCKeditor('deliverEditor');
							$deliverEditor->BasePath = $deliverBasePath;
							$deliverEditor->Config['AutoDetectLanguage'] = true;
							$deliverEditor->Config['ToolbarCanCollapse'] = false;
							$deliverEditor->Config['DefaultLanguage'] = 'en';
							//$metricsEditor->Config['EnterMode'] = 'br';
							$deliverEditor->ToolbarSet = "NBC";
							if(isset($_GET['project_id'])) {
								$project_deliver = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='7' LIMIT 1");
								$deliverEditor->Value = $project_deliver[0]['desc'];
							} else {
								$deliverEditor->Value = '';
							}
							$deliverEditor->Create();
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_8" style="display: none;">
						<form action="" method="post" name="form_sec_8" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearFCK(\'metricsEditor\');"><span>empty</span></button>
								<button class="status status_draft" onClick="ajaxFunction(\'\',\'update_project_metrics\'); changeSectionStatusMan(\'sec_8\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearFCK(\'metricsEditor\');"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_8_save" onClick="ajaxFunction(\'\',\'update_project_metrics\'); changeSectionStatusMan(\'sec_8\'); setCompleteness();"><span>save</span></button>	
								<!--<button class="secondary" onClick="document.getElementById(\'form_sec_8_save\').click();document.getElementById(\'form_sec_8_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->
								<button class="secondary" onClick="nxtFalse(); this.click(); ctCreateSectionsSwitchNextMetrics();"><span>next</span></button>
							</div>
						</div>
						<div class="inside textEdit">';
							$metricsBasePath = $_SERVER['PHP_SELF'] ;
							$metricsBasePath = substr( $metricsBasePath, 0, strpos( $metricsBasePath, "_samples" ) );

							$metricsEditor = new FCKeditor('metricsEditor');
							$metricsEditor->BasePath = $metricsBasePath;
							$metricsEditor->Config['AutoDetectLanguage'] = true;
							$metricsEditor->Config['ToolbarCanCollapse'] = false;
							$metricsEditor->Config['DefaultLanguage'] = 'en';
							//$metricsEditor->Config['EnterMode'] = 'br';
							$metricsEditor->ToolbarSet = "NBC";
							if(isset($_GET['project_id'])) {
								$project_metrics = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='8' LIMIT 1");
								$metricsEditor->Value = $project_metrics[0]['desc'];
							} else {
								$metricsEditor->Value = '';
							}
							$metricsEditor->Create();
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_9" style="display: none;">
						<form action="" method="post" name="form_sec_9" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearApprovals();"><span>empty</span></button>
								<button class="status status_draft" onClick="saveApprovals(); changeSectionStatusMan(\'sec_9\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearApprovals();"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_9_save" onClick="saveApprovals(); changeSectionStatusMan(\'sec_9\'); setCompleteness();"><span>save</span></button>	
								<button class="secondary" onClick="document.getElementById(\'form_sec_9_save\').click();document.getElementById(\'form_sec_9_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	
							</div>
						</div>
						<div class="inside resources" id="approvals">
							<div class="proles_header">
								<h3>PROJECT APPROVALS</h3>

								<div style="clear: both;"></div>
							</div>
							<ul class="papprovals">
								<li style="display: none;"><form></form></li>
								<li>
									<form action="" method="post">';
										$project_appr = CtDisplay::getQuery("SELECT * FROM `project_phase_approvals` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `non_phase`='nbcuxd' LIMIT 1");
										if(isset($_GET['project_id']) && is_array($project_appr)) {											
											$appr_nbc_name  = $project_appr[0]['name'];
											$appr_nbc_title  = $project_appr[0]['title'];
											$appr_nbc_phone  = $project_appr[0]['phone'];
											$appr_nbc_date  = $project_appr[0]['approval_date'];
											$appr_nbc_approval  = $project_appr[0]['approved'];
											
											$appr_date_part = explode(" ", $appr_nbc_date);
											$appr_date = explode("-", $appr_date_part[0]);
											
											$date = $appr_date[1] . "/" .$appr_date[2] ."/" .$appr_date[0];
											
											if($appr_nbc_approval == 1) {
												$checked = " CHECKED";
											} else {
												$checked = "";
											}
										} else {
											$appr_nbc_name  = "";
											$appr_nbc_title  = "";
											$appr_nbc_phone  = "";
											$appr_nbc_date  = "";
											$appr_nbc_approval  = "";
											
											$date = "";
											$checked = "";
										}
										echo '<input type="hidden" name="phase" id="phase" value="nbcux" />
										<div class="papprovals_phase"><label>'.DEV_TEAM_NAME.'</label></div>
										<div class="papprovals_name">
											<input type="text" name="user_name" id="user_name" value="' .$appr_nbc_name .'" />&nbsp;
										</div>
										<div class="papprovals_title">
											<input type="text" name="user_title" id="user_title" value="' .$appr_nbc_title .'" />&nbsp;
										</div>
										<div class="papprovals_phone">
											<input type="text" name="user_phone" id="user_phone" value="' .$appr_nbc_phone .'" />&nbsp;
										</div>
										<div class="papprovals_approved">
											<label>Approved</label>&nbsp;
											<input type="checkbox" name="approved" id="approved"' .$checked .' />&nbsp;
										</div>
										<div class="papprovals_date">
											<input type="text" name="approval_date" class="date_picker" id="approval_date_nbc" value="' .$date .'" />
										</div>
										<div style="clear: both"></div>
									</form>
								</li>
								<li>
									<form action="" method="post">';
										$project_appr = CtDisplay::getQuery("SELECT * FROM `project_phase_approvals` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `non_phase`='nbcuxd' LIMIT 1");
										if(isset($_GET['project_id']) && is_array($project_appr)) {
											
											$appr_nbc_name  = $project_appr[0]['name'];
											$appr_nbc_title  = $project_appr[0]['title'];
											$appr_nbc_phone  = $project_appr[0]['phone'];
											$appr_nbc_date  = $project_appr[0]['approval_date'];
											$appr_nbc_approval  = $project_appr[0]['approved'];
											
											$appr_date_part = explode(" ", $appr_nbc_date);
											$appr_date = explode("-", $appr_date_part[0]);
											
											$date = $appr_date[1] . "/" .$appr_date[2] ."/" .$appr_date[0];
											
											if($appr_nbc_approval == 1) {
												$checked = " CHECKED";
											} else {
												$checked = "";
											}
										} else {
											$appr_nbc_name  = "";
											$appr_nbc_title  = "";
											$appr_nbc_phone  = "";
											$appr_nbc_date  = "";
											$appr_nbc_approval  = "";
											
											$date = "";
											$checked = "";
										}
										echo '<input type="hidden" name="phase" id="phase" value="client" />
										<div class="papprovals_phase"><label>Client</label></div>
										<div class="papprovals_name">
											<input type="text" name="user_name" id="user_name" value="' .$appr_nbc_name .'" />&nbsp;
										</div>
										<div class="papprovals_title">
											<input type="text" name="user_title" id="user_title" value="' .$appr_nbc_title .'" />&nbsp;
										</div>
										<div class="papprovals_phone">
											<input type="text" name="user_phone" id="user_phone" value="' .$appr_nbc_phone .'" />&nbsp;
										</div>
										<div class="papprovals_approved">
											<label>Approved</label>&nbsp;
											<input type="checkbox" name="approved" id="approved"' .$checked .' />&nbsp;
										</div>
										<div class="papprovals_date">
											<input type="text" name="approval_date" class="date_picker" id="approval_date_client" value="' .$date .'" />
										</div>
										<div style="clear: both"></div>
									</form>

								</li>
							</ul>
							<div class="proles_header">
								<h3>STAGE APPROVALS</h3>
								<div style="clear: both;"></div>
							</div>';
							if(isset($_GET['project_id'])) {
								echo CtDisplay::getApprovalsEditHTML($_GET['project_id']);
							} else {
								echo CtDisplay::getApprovalsHTML();
							}
							
					echo '</div>
						</form>
					</div>
					
					<div class="rightCol" id="form_sec_10" style="display: none;">
						<form action="" method="post" name="form_sec_10" onSubmit="return false;">
						<div class="inside drafting_actions">
							<div class="left_actions">
								<button class="status status_empty" onClick="clearFCK(\'bcaseEditor\');"><span>empty</span></button>
								<button class="status status_draft" onClick="ajaxFunction(\'\',\'update_project_bcase\'); changeSectionStatusMan(\'sec_10\'); setCompleteness();"><span>draft</span></button>
								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
							</div>
							<div class="right_actions">
								<button class="secondary" onClick="clearFCK(\'bcaseEditor\');"><span>clear</span></button>	
								<button class="secondary" name="save" id="form_sec_10_save" onClick="ajaxFunction(\'\',\'update_project_bcase\'); changeSectionStatusMan(\'sec_10\'); setCompleteness();"><span>save</span></button>	
								<!--<button class="secondary" onClick="document.getElementById(\'form_sec_10_save\').click();document.getElementById(\'form_sec_10_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->
								<button class="secondary" onClick="nxtFalse(); this.click(); ctCreateSectionsSwitchNextBcase();"><span>next</span></button>
							</div>
						</div>
						<div class="inside textEdit">';
							$bcaseBasePath = $_SERVER['PHP_SELF'];
							$bcaseBasePath = substr( $bcaseBasePath, 0, strpos( $bcaseBasePath, "_samples" ) );

							$bcaseEditor = new FCKeditor('bcaseEditor');
							$bcaseEditor->BasePath = $bcaseBasePath;
							$bcaseEditor->Config['AutoDetectLanguage'] = true;
							$bcaseEditor->Config['ToolbarCanCollapse'] = false;
							$bcaseEditor->Config['DefaultLanguage'] = 'en';
							//$descEditor->Config['EnterMode'] = 'br';
							$bcaseEditor->ToolbarSet = "NBC";
							if(isset($_GET['project_id'])) {
								$project_bcase = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='10' LIMIT 1");
								$bcaseEditor->Value = $project_bcase[0]['desc'];
							} else {
								$bcaseEditor->Value = '';
							}
							$bcaseEditor->Create();
					echo '</div>
						</form>
					</div>
					
					<div style="clear: both;"></div>
				</div>
	
			</div>
					<script>
						$(function() {
								$(".date_picker").datepicker({ 
									showOn: "both",
									buttonImage: "/_images/date_picker_trigger.gif", 
									buttonImageOnly: true 
								});
						});
					</script>;
					
			<!--==| END: Bucket |==-->';
		}
		public function editAction() {
			$this->createAction();
		}
	}
?>