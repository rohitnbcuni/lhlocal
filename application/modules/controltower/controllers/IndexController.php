<?PHP	
 include('library/ControlTower.inc');	
 include("_fckeditor/fckeditor.php");		
class Controltower_IndexController extends LighthouseController 
{ 		
private $cur_section;		
public function indexAction() 
{			
$totalBudget = CtDisplay::financeTotal();
$totalActiveProjects = CtDisplay::totalActiveProjects();	
$htmlCompany = CtDisplay::getCompanyBcHTML();	
if($_SESSION['login_status'] == "client")
{	
$hideStyle = "display: none;";			
} 
else
{			
$hideStyle = "";	
}
echo '<script language="javascript">	
totalBudget = "'.$totalBudget.'";			
totalActive = "'.$totalActiveProjects.'";	
</script>';		
if(isset($_COOKIE["lighthouse_ct_data"])){	
echo '<input type="hidden" id="ct_loadFilter" name="ct_loadFilter" value="' . urldecode($_COOKIE["lighthouse_ct_data"]) . '">';			
}else
{				
echo '<input type="hidden" id="ct_loadFilter" name="ct_loadFilter" value="">';	
}		
echo '			<!--=========== START: COLUMNS ===========-->			
<!--==| START: Bucket |==-->

<div class=" create_pop_up " style="width:3000px;height:1024px;position:fixed;background-color:#fffff;z-index:1.0;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:2.0; z-index:1;"></div>
<div class="main_actions" style="display">				
<!--<button onClick="window.location = \'/controltower/index/create/\'"><span>create new project</span></button>-->	
<button onClick="createProject();"><span>create new project</span></button><button style="float:right;" onclick="return generateallocationreport();"><span>Allocation Report</span></button>	
	<p class="message">Select a company or lead to list the projects.</p>';  
		/*				if($_SESSION['login_status'] == "admin") {					echo '<div class="right_actions">						<button onClick="cloneProject();"><span>CLONE PROJECTS</span></button>					</div>					<div class="clone_pop_up">								<div id="clone_project_pop_up" style="margin-left:30px;" >		</div>						<div style="clear: both;"></div>											<div class="duplicate_buttons" style="float:left;margin-left:100px;">							<button class="cancel" onClick="$(\'.clone_pop_up\').css({display:\'none\'}); return false;"><span>Cancel</span></button>							<div style="clear: both;"></div>						</div>														</div>';							} */
					echo '				<!--<button><span>archive project</span></button>	
					<div class="right_actions">					
					<button><span>go to project</span></button>	
					<button class="trash"><span>delete project</span></button>
					</div>-->			
					</div>			
					<!--==| END: Bucket |==-->			
					<!--==| START: Bucket |==-->	
						
					<div class="title_med_ct">			
				<label for="company_filter" style="">Company</label>
				<select name="company_filter" id="company_filter" onchange="getProjects()" style="">	
				<option value="-1" selected="selected">Select</option>'.CtDisplay::getCompanyHTML().'</select>	
				<label for="producer_filter" style="' .$hideStyle .'">Lead</label>		
				<select name="producer_filter" id="producer_filter" onchange="getProducerProjects()" style="' .$hideStyle .'">		
				<option value="0" selected="selected">All</option>'					.CtDisplay::getProducerHTML().'</select>					
				<div class="right_actions_buttons" style="' .$hideStyle .'">					
		<button id="totalBudgetID" class="monitors">
		<span>total budget: $' . number_format($totalBudget) .'</span></button>	
			<button id="totalActiveID" class="monitors"><span>total active projects: ' .$totalActiveProjects .'</span></button>			
			</div>			
			<div class="ct_search_title">	
				<label for="project_filter" style="">Search Project Title</label>		
		<input type="text" value="" name="project_filter" id="project_filter" onkeyup="filterProjects()" style="">	
				</div>				
				<label for="producer_filter" style="' .$hideStyle .'">Quarter</label>				
				<select name="quarter_filter" id="quarter_filter" class="small" onchange="getQuarterProjects()" style="' .$hideStyle .'">					
          <option value="0" selected="selected">Annual</option>					<option value="1">Q1</option>	
						<option value="2">Q2</option>	
						<option value="3">Q3</option>				
						<option value="4">Q4</option>			
					</select>			
			<label for="approval_filter" style="' .$hideStyle .'">Project Status</label>	
				<select name="approval_filter" id="approval_filter" class="small" onchange="getApprovedProjects()" style="' .$hideStyle .'">			
	<option value="0" selected="selected">All</option>					' . CtDisplay::getProjectStatusEditHtml() . '			
					</select>				
				<!--<label for="group_filter" style="' .$hideStyle .'">Allocation Type</label>				
	<select name="group_filter" id="group_filter" class="medium" onchange="getGroupProjects()" style="' .$hideStyle .'">		
	<option value="0" selected="selected">All</option>					' . CtDisplay::getProjectGroupsEditHtml() . '				
	</select>-->		

		<label for="program_filter" style="' .$hideStyle .'">Program</label>				
	<select name="program_filter" id="program_filter" class="medium" onchange="getProgramProjects()" style="' .$hideStyle .'">		
	<option value="0" selected="selected">All</option><option value="99" >None</option>					' . CtDisplay::fullProgramListHTML() . '				
	</select>

		</div>		
			<!--==| END: Bucket |==-->			
	<!--==| START: Bucket |==-->	
		
		<div class="project_status_title">
		<a href="#" id="active_link" onClick = "toggleActiveList(); return false;"  class="expand_toggle 
				<!--expand_toggle_closed-->">Active Projects</a></div>			
	<div id="active_list" style="display: block;" class="<!--results_collapsed-->">		
				<ul class="project_filters">	
<li class="project"><a href="#" class="down" id="project_sort" onClick="setSort(\'project\',\'active\'); return false;">project</a></li>	
<li class="status" style="' .$hideStyle .'"><a href="#" id="status_sort" onClick="setSort(\'status\',\'active\'); return false;">status</a></li>	
<li class="actual" style="' .$hideStyle .'"><a href="#" id="todate_sort" onClick="setSort(\'todate\',\'active\'); return false;">to date</a></li>		
<li class="budget"><a href="#" id="budget_sort" onClick="setSort(\'budget\',\'active\'); return false;">budget</a></li>		
<li class="completeness" style="' .$hideStyle .'"><a href="#" id="complete_sort" onClick="setSort(\'complete\',\'active\'); return false;">% of budget spent</a></li>		
<li class="risk" style="' .$hideStyle .'"><a href="#" id="risk_sort" onClick="setSort(\'risk\',\'active\'); return false;">Risk</a></li>				
</ul>				
<div class="project_results_container">	
<dl class="project_results" id="active_project_list">	
		</dl>
		</div>	
<!-- ==| START: calendar view type Botton Divison#22669 |== -->

		          <div style="float: right; margin: 0pt; position: absolute; right: 12px; top: 78px;padding-right:11px;" id="list_view" >
		         	<a href="/controltower/index/calendarview" title="List View"><img src="/_images/cal_active.png" alt="Calendar view" title="Calendar view" width="23" height="23" style="padding-right:6px;" />
                      </a><img src="/_images/list_dis_05.png" alt="List View" width="23" height="23" >
		         </div>
		         <!-- ==| END: calendar view type Botton Divison#22669 |== -->	
		</div>
		<!--==| END: Bucket |==-->		
<!--==| START: Bucket |==-->
			
<div class=" duplicate_pop_up message_archive message_delete " style="width:3000px;height:1024px;position:fixed;background-color:#fffff;z-index:1.0;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:1.0; z-index:1"></div>
<div class="project_status_title"><a href="#" id="archive_link" onClick = "loadArchiveList(); return false;" class="expand_toggle expand_toggle_closed">Archived Projects</a></div>	
<div style="display: none;" id="archive_list" class="<!--results_collapsed-->">	
<ul class="project_filters archived_projects">			
<li class="project"><a href="#" id="project_asort" onClick="setSort(\'project\',\'archive\'); return false;">project</a></li>		
<li class="status" >Status</li>		
<li class="actual"><a href="#" id="todate_asort" onClick="setSort(\'todate\',\'archive\'); return false;">to date</a></li>	
<li class="budget"><a href="#" id="budget_asort" onClick="setSort(\'budget\',\'archive\'); return false;">budget</a></li>
<li class="completeness"><a href="#" id="complete_asort" onClick="setSort(\'complete\',\'archive\'); return false;">% of budget spent</a></li>		
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
				<label for="project_client">Client</label>    
				<select class="pClient" onChange="ajaxFunction(\'\',1); return false;" name="project_client" id="create_company">    
				<option value="">--Select Company--</option>'.$htmlCompany.'</select>			
				</li>		
				<li>		
				<label for="project_code">Project Code</label>	
				<input size=10 name="project_code" id="projectCode" type="text" />	
				</li>					
				<li>							
				<label for="project_name">Project Name</label>
				<input size=20 name="project_name" id="project_name" type="text" />	
				</li>					
				</ul>									
				<div style="clear: both;"></div>	
				<div class="duplicate_buttons">			
				<button><span>Duplicate</span></button> 	
				<button class="cancel" onClick="$(\'.duplicate_pop_up\').css({display:\'none\'}); return false;"><span>Cancel</span></button>
				<div style="clear: both;"></div>	
				</div>								
				</form>			
				</div>	
				<div class="create_pop_up" style=" z-index:2; ">		
				<form action="' . BASE_URL . '/create_project.php" name="create_project" id="create_project" method="POST">	
				<ul>		
				<input type="hidden" id="hidden_id" name="project_id"> 		
				<li>		
				<label for="project_client">Client</label>     
				<select class="pClient" onChange="ajaxFunction(\'\',2); return false;" name="project_client" id="create_company2">     
				<option value="">--Select Company--</option>'.$htmlCompany.'</select>	
				</li>
				<li>
				<label for="project_code2">Project Code</label>	
				<input size=10 name="project_code" id="projectCode2" type="text" />	
				</li>				
				<li>			
				<label for="project_name">Project Name</label>
				<input size=20 name="project_name" id="project_name2" type="text" />	
				</li>			
				<li>	
				<label for="project_name">Year</label>	
				<input size=10 name="project_year" id="project_year" type="text" readonly="true" value="'.date("Y").'"/>
				</li>			
				</ul>			
				<div style="clear: both;"></div>
				<div class="duplicate_buttons">	
				<button onClick="return createproject_new();"><span>Create Project</span></button>	
				<button class="cancel" onClick="$(\'.create_pop_up\').css({display:\'none\'}); return false;"><span>Cancel</span></button>	
				<div style="clear: both;"></div>	
				</div>								
				</form>		
				</div>		
				<div class="message_archive">	
				<p>					
				You are about to archive this project.<br />Do you want to continue?	
				</p>	
				<input type="hidden" name="archive_project_confirm" id="archive_project_confirm" value="" />
				<div style="clear: both;"></div>	
				<div class="duplicate_buttons">	
				<button onClick="archiveProject(document.getElementById(\'archive_project_confirm\').value); return false;"><span>Yes</span></button> 	
				<button class="cancel" onClick="$(\'.message_archive\').css({display:\'none\'}); return false;"><span>No</span></button>	
				<div style="clear: both;"></div>	
				</div>		
				</div>		
				<div class="message_delete">	
				<p>				
				You are about to delete this project.<br />Do you want to continue?		
				</p>	
				<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
				<div style="clear: both;"></div>		
				<div class="duplicate_buttons">	
				<button onClick="deleteProject(document.getElementById(\'delete_project_confirm\').value); return false;"><span>Yes</span></button> 	
				<button class="cancel" onClick="$(\'.message_delete\').css({display:\'none\'}); return false;"><span>No</span></button>	
				<div style="clear: both;"></div>
				</div>			
				</div>		
				<div style="display: none;" id="wo_dimmer_ajax" class="wo_save_box">
				<img alt="ajax-loader" src="/_images/ajax-loader.gif"/>	
				</div>			
			';
		}	

	public function createAction() 
		{			
		$_session = new Zend_Session_Namespace('Zend_BC_Auth');
		Zend_Session::regenerateId();		
		$config = CtDisplay::getConfigValue();	
		$project_data = "";		
		$company_data = "";
		
        echo '<input type="hidden" id="ct_user_id" value="'.$_SESSION['user_id'].'"></input>';
		echo '<input type="hidden" id="ct_section" value="'.strtolower($_GET['section']).'"></input>';

        if(isset($_GET['project_id']))
			{				
		    $project_id = $_GET['project_id'];
			/* LH fixes
			 * LH#21355
			 */
			if(!is_numeric($project_id )){
				$this->_redirect("controltower/index/");
			}	
			$project_data = CtDisplay::getQuery("SELECT * FROM `projects` WHERE `id`='$project_id' LIMIT 1");
			/* LH fixes
			 * LH#21355
			 */
			if(count($project_data) == 0){
				$this->_redirect("controltower/index/");
			}	
			//print_r($project_data);
			$company_data = CtDisplay::getQuery("SELECT * FROM `companies` WHERE `id`='" .$project_data[0]['company'] ."' LIMIT 1");
			$proj_completeness = CtDisplay::getProjectCompleteness($project_id);		
			} 
		   else 
			{				
			$project_id = "";	
			$proj_completeness = "0";	
			}						
			$strAddFlag = '	<div class="center_actions">	
			<button class="status status_flag" onClick="$(\'.add_risk\').css({display:\'block\'}); return false;" style="display:none;"><span>Add Flag</span></button>		
			</div>';	
			echo '<!--=========== START: COLUMNS ===========-->		
			
		<!--==| START: Bucket |==-->
			  
			<div class="title_lrg">	
			<div class="close_block">	
			<button id="cancel_button" class="cancel" onClick="document.location = \'/controltower/\'" style="display: ';	
			if(isset($_GET['project_id']))
			{	
			echo "none";	
			} 
			else 
			{
			echo "block";
			}				
		echo ';"><span>CANCEL</span></button>
		<button id="back_button" class="back_arrow" onClick="document.location = \'/controltower/\'" style="display: ';
		if(isset($_GET['project_id']))
		{							
		echo "block";		
		}
	 else
	{
	echo "none";
	}
echo ';"><span>all projects</span></button>	
</div>		
<form name="project_main" onSubmit="return false;">		
<input type="hidden" name="project_id" id="project_id" value="' .$project_id .'" />			
<div class="form_blocks" id="createcompany" style="display: ';	
if(isset($_GET['project_id']))
{	
echo "none";
}
else
{				
echo "block";
}			
echo ';">	
<label for="create_company">Select Client/Company</label>
	<select class="pClient" onChange="ajaxFunction(\'\',1); return false;" name="create_company" id="create_company">
	<option value="">--Select Company--</option>'.CtDisplay::getCompanyBcHTML().'</select>	
	</div>	
	<div class="form_blocks" id="create_code" style="display: ';
if(isset($_GET['project_id'])) 
	{						
	echo "none";		
	}
	else
	{
	echo "block";	
	}				
	echo ';">	
	<label for="projectCode">Project Code</label>	
		<input class="pCode" type="text" value="" name="projectCode" id="projectCode">	
		</div>	
		<div class="form_blocks"  id="create_name" style="display: ';	
		if(isset($_GET['project_id']))
		{			
			echo "none";
		} 
		else
		{
		echo "block";
		}		
		echo ';">	
		<label for="projectName">Project Name</label>
			<input class="pName" type="text" value="" name="projectName" id="projectName">	
			</div>								<div class="form_blocks"  id="create_name">	
			<h4 id="display_company" style="display: ';	
		if(isset($_GET['project_id']))
		{	
		echo "block";	
		}
		else
		{	
		echo "none";
	}	
	echo ';">';	
	if(isset($_GET['project_id']))
{		
	$projectNameCode = $company_data[0]['name'] .": " .$project_data[0]['project_code'] ."&nbsp; - " .$project_data[0]['project_name'];	
	echo ' <a href="https://' . $config->basecamp->host. '/projects/' . $project_data[0]['bc_id'] . '/log" target="_blank" title="Open in Basecamp"> ';	
	echo substr($projectNameCode, 0, 65);	
	if(strlen($projectNameCode) > 65)
{		
echo "...";	
}		
echo "</a>";
}		
echo '</h4>		
<!--<h4 id="display_code" style="display: none;">	
	</h4>		
	<h4 id="display_name" style="display: none;">		
	</h4>-->	
	</div>	
	<div class="form_blocks createProject" id="project_create" style="display: ';
if(isset($_GET['project_id']))
{	
echo "none";	
}
else
	{
	echo "block";
	}	
	echo ';">
	<button id="createProject" class="active" onClick="project_mode(); return false;"><span>Create Project</span></button>
	</div>		
	<div style="display: none;" class="project_complete" id="project_progress" style="display: ';	
if(isset($_GET['project_id'])) 
{ 
echo "block";
} 
else
{ 
echo "none";
} 
echo ';"> 
<label id="progress_percent_text" for="">PROJECT brief COMPLETENESS: ' .$proj_completeness .'%</label>
<div class="progressBar" id="progress_insider_bar">	
<div class="insideBar" style="width: ' .$proj_completeness .'%;"></div>	
</div>	
</div>	
</form>		
</div>		
<!--==| END: Bucket |==-->	
<!--==| START: Bucket |==-->
	<div  class="message_clear_desc message_clear_scope message_clear_deliver message_clear_metrics message_clear_bcase message_timeline_date message_clear_finance message_clear_roles message_clear_role message_clear_timeline message_clear_approvals message_risk_create project_history_note timeline_history_note budget_save" style="width:3000px;height:1024px;position:fixed;background-color:#fffff;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:1.0;z-index:1;"></div>
		
	
<div class="full_content_container_noscroll control_tower_main">	
<div class="new_project_dimmer" id="new_project_dimmer"style="display: ';
if(isset($_GET['project_id'])) 
	{		
	echo "none";
	} 
	else
		{
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
			<label for="resource">Resource Type</label>	
			<select name="resource" onChange="alert(\'get resource name and id\');">	
			<option value="">-Select Resource Type-</option>	
			<option value="1">test resource</option>
			</select>	
			<label for="user">User</label>	
			<select name="user" onChange="alert(\'change email and phone vals\');">	
			<option value="">-Select User-</option>	
			<option value="1">test user</option>	
			</select>			
			<label for="email">Email</label>	
			<input name="email" readonly type="text" value="" />
			<label for="phone">Phone</label>			
			<input name="phone" readonly type="text" value="" />	
			<input type="submit" value="Cancel" onClick="document.getElementById(\'add_custom_role\').style.display = \'none\'; return false;" />	
			<input type="submit" value="OK" onClick="addRole(addrole); return false;" 
		name="add_role" />	
		</form>		
		</div>	
		<div class="contentCol" id="create_columns">	
		<div class="leftCol" id="section_menu">';	
	if(isset($_GET['project_id'])) 
		{		
		if (isset($_GET['project_timeline'])){
		echo CtDisplay::getSectionEditHTMLNew($_GET['project_id'],1);
		}
		else{	
		echo CtDisplay::getSectionEditHTMLDisplay($_GET['project_id'], $_GET['section']);	
		}}
		else
			{
			echo CtDisplay::getSectionHTML();
			}			
		echo '<div class="project_nav_buttons">	
			<button class="status status_complete" onClick="ct_allComplete();"><span>Mark All Complete</span></button>
				<button class="secondary" onClick="ct_pdfExport();"><span>Export to PDF</span></button>	
				</div>	
				</div>';
		
			if(isset($_GET['project_timeline'])){
				echo '<div class="rightCol" id="form_sec_33" style="display: block;">
<form action="" method="post" name="form_sec_3" onSubmit="return false;">
<div class="inside drafting_actions">	
<div class="left_actions">
<button class="status status_empty" onClick="confirmEmptyTimeline();"><span>empty</span></button>
<button class="status status_draft" onClick="draftStatus(); saveTimeline();  changeSectionStatusMan(\'sec_3\'); setCompleteness();"><span>draft</span></button>
<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
</div>		
<div class="right_actions">	
<button class="secondary" onClick="confirmEmptyTimeline();"><span>clear</span></button>		
<button class="secondary" name="save" id="form_sec_3_save" onClick="saveTimeline(); changeSectionStatusMan(\'sec_3\'); setCompleteness();"><span>save</span></button>									<button class="secondary" onClick="nxtFalse(); ctCreateSectionsSwitchNextTimeline();"><span>next</span></button>	
</div>	' . $strAddFlag . '	</div>		
<div class="inside resources">
<div class="proles_header">
<h3>PROJECTED TIMELINE FOR Project</h3>
<div style="clear: both;"></div>	
</div>';
	
if(isset($_GET['project_id']))
	{			
	echo CtDisplay::getTimelineEditHTML($_GET['project_id']);	
	}
	else 
		{	
		echo CtDisplay::getTimelineHTML();		
		}			
		echo '</div>	
		</form>			
			</div>';}
		
				if(isset($_GET['section']) && $_GET['section'] == 'Description'){
					echo '<div class="rightCol" id="form_sec_1" style="display:block;">';
				echo '<form action="" method="post" name="form_sec_1" onSubmit="return false;">
				<div class="inside drafting_actions">
					
					<div class="right_actions">		
						<button class="secondary" onClick="confirmEmptyDesc();"><span>clear</span></button>	
						<button class="secondary" name="save" id="form_sec_1_save" onClick="ajaxFunction(\'\',\'update_project_desc\');return false;"><span>save</span></button>									<!--<button class="secondary" onClick="document.getElementById(\'form_sec_1_save\').click(); document.getElementById(\'form_sec_1_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>-->
						<button class="secondary" onClick="nxtFalse(); ctCreateDisplaySection(\'sec_2\');">
						<span>next</span></button>
					</div>
					<div class = "left_actions_project_status">
						<div class="project_status_program">
							<div class="left_actions">
								<div class="project_status_container">	
									<label id="project_status_label" for="project_status">Project Status: </label>
									<div class="project_status_block">
										<span class="project_status">
										' . CtDisplay::getProjectStatus($_GET['project_id'], '') . '</span>		
										<span class="dropdown">		
											<a class="status_dropdown">&nbsp;</a>
										</span>	
									</div>		
								</div>		
							</div>	
								
							<div class="ct_prj_internal_grp">	
								<div class="internal_grp_container">	
									<!--<label id="project_status_label" for="project_status">Allocation Type: </label>	
									<select name="allocationType" id="allocationType" class="medium">
									' . CtDisplay::getAllocationType($_GET['project_id']) . '</select>-->

									<label id="project_status_label" for="project_status">Program: </label>	
									<select name="project_program" id="project_program" class="medium">
									' . CtDisplay::getPrograms($_GET['project_id']) . '</select>
								</div>				
							</div>
						</div>';

					if($_GET["project_id"] != "") {
						$project_charter_scope = CtDisplay::getQuery('SELECT project_scope, project_charter FROM projects WHERE id='.$_GET["project_id"]);	
					}
						
					echo '<div class = "project_charter" style="clear: both;"><label id="project_charter_label" for="project_charter">Project Charter: </label><input type="text" name="project_charter_text" id="project_charter_text" value = "'.$project_charter_scope[0]['project_charter'].'"/></div>';

					echo '<div class = "project_description_scope"><label id="project_description_scope_label" for="project_description_scope">Scope: </label><input type="text" name="project_description_scope_text" id="project_description_scope_text" value = "'.$project_charter_scope[0]['project_scope'].'"/>
					</div>' . $strAddFlag . '</div></div>

				<div class = "project_history_title"><h3>Project History</h3></div>
				<div class = "project_history"><ul>';
				$project_history = CtDisplay::getQuery('SELECT * FROM project_status WHERE project_id='.$_GET["project_id"].' order by id DESC');
				
				if(count($project_history) == 1) {
					echo "<div class = 'note_nohistorty'><label>No history available.</label></div>";
				}

				$history_flag = true;
				$history_count = sizeof($project_history);

				for ($i = 0; $i<$history_count; $i++) {
				  $user = CtDisplay::getQuery('SELECT * FROM users WHERE id='.$project_history[$i]["created_user"]);
				  $status = CtDisplay::getQuery('SELECT * FROM lnk_project_status_types WHERE id='.$project_history[$i]["status_id"]);
				  $style = '';
				  if(($history_count-1) == $i) {
					$style = 'style=display:none;';
					$history_flag = false;
				  }
				  $date = date("m/d/Y", strtotime($project_history[$i]['created_date']));
				  echo '<li '.$style.'>';
				  echo "<div class = 'project_history_status'>".$status[0]['name']."</div>";
				   echo '<div class = "textarea_note"><textarea maxlength="100" readonly rows="2" cols="25" >'.$project_history[$i]['note'].'</textarea></div>';
				    echo "<div class = 'note_updated_by'>Updated by ".$user[0]['first_name'].' '.$user[0]['last_name']." on ".$date."</div>";
				  echo '</li>';
				}
				echo '</ul></div>	
				<div class="inside textEdit">';
			//javascript:submit(); 		
			$descBasePath = $_SERVER['PHP_SELF'];
			$descBasePath = substr( $descBasePath, 0, strpos( $descBasePath, "_samples" ) );	
			$descEditor = new FCKeditor('descEditor');	
			$descEditor->BasePath = $descBasePath;		
			$descEditor->Config['AutoDetectLanguage'] = true;	
			$descEditor->Config['ToolbarCanCollapse'] = false;	
			$descEditor->Config['DefaultLanguage'] = 'en';		
			//$descEditor->Config['EnterMode'] = 'br';
			$descEditor->ToolbarSet = "NBC";		
			$descEditor->Height = 300;
					
			if(isset($_GET['project_id'])) 
		{	
		$project_description = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='1' LIMIT 1");
		if(@$project_description[0]['desc'] != "null")
			{	
			$descEditor->Value = @$project_description[0]['desc'];
			}
			else 
			{	
			$descEditor->Value = '';	
			}	
			}
			else
			{	
			$descEditor->Value = '';
			}
			$descEditor->Create();
			echo '</div>
			</form>	
			<div class="fck_editor_overlay">
			</div>
			</div>';
				}
			if (isset($_GET['section']) && $_GET['section'] == 'Roles') {
				echo '<div class="rightCol" id="form_sec_2" style="display:block;">';
			echo '<form action="" method="post" name="form_sec_2" onSubmit="return false;">		
			<div class="inside drafting_actions">
			<div class="left_actions">
			<button class="status status_empty" onClick="confirmEmptyRoles();"><span>empty</span></button>	
			<button class="status status_draft" onClick="draftStatus(); saveRoles(); changeSectionStatusMan(\'sec_2\'); setCompleteness();"><span>draft</span></button>							
			<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>								
			</div>	
			<div class="right_actions">	
			<button class="secondary" onClick="confirmEmptyRoles();"><span>clear</span></button>	
			<button class="secondary" name="save" id="form_sec_2_save" onClick="saveRoles(); changeSectionStatusMan(\'sec_2\'); setCompleteness();"><span>save</span></button>		
			<button class="secondary" onClick="nxtFalse(); ctCreateDisplaySection(\'sec_3\');"><span>next</span></button>
				</div>' . $strAddFlag . '</div>	
		<!--<script>		
				$(document).ready(function()
				{		
				$("#project_roles").sortable(
					{		
					start: function(e,ui)
						{
						ui.helper.addClass(\'dragged\');},
							beforeStop: function(e,ui) {
							ui.helper.removeClass(\'dragged\');		
							},		
						revert: true,	
						});		
					});			
		</script>-->	
<div class="inside ownerroles">	
<div class="proles_header">		
<h3>PROJECT OWNER & ROLES</h3>	
<div class="proles_options">	
<!--<select>
<option>Select Project Type</option>	
</select>
<button onClick="document.getElementById(\'add_custom_role\').style.display = \'block\';"><span>ADD CUSTOM ROLE</span></button>									-->		
</div>			
<div style="clear: both;"></div>	
</div>		
<ul class="proles" id="project_roles">';	
if(isset($_GET['project_id'])) 
{			
echo CtDisplay::getOwnerRolesNewEditHTML($_GET['project_id']);	
}
else
{
echo CtDisplay::getOwnerRolesNewHTML();		
}			
echo '</ul>	
</div>		
</form>		
</div>';
			}
	if (isset($_GET['section'])) {
	if ($_GET['section'] == 'Timeline') 
		echo '<div class="rightCol" id="form_sec_3" style="display:block;">';
	else 
		echo '<div class="rightCol" id="form_sec_3" style="display:none;">';
echo '<form action="" method="post" name="form_sec_3" onSubmit="return false;">
<div class="inside drafting_actions">	
<div class="left_actions">
<button class="status status_empty" onClick="confirmEmptyTimeline();"><span>empty</span></button>
<button class="status status_draft" onClick="draftStatus(); saveTimeline();  changeSectionStatusMan(\'sec_3\'); setCompleteness();"><span>draft</span></button>
<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
</div>		
<div class="right_actions">	
<button class="secondary" onClick="confirmEmptyTimeline();"><span>clear</span></button>		
<button class="secondary" name="save" id="form_sec_3_save" onClick="saveTimeline(); changeSectionStatusMan(\'sec_3\'); setCompleteness();"><span>save</span></button>									<button class="secondary" onClick="nxtFalse(); ctCreateDisplaySection(\'sec_5\');"><span>next</span></button>	
</div>	' . $strAddFlag . '	</div>
<div class = "timeline_history_title"><h3>Timeline History</h3></div>
<div class = "timeline_history">';
     $project_id = $_GET['project_id'];
	 $phase_array = array();
	 echo "<ul>";
     $phases = CtDisplay::getQuery("SELECT * FROM `project_phases` WHERE `project_id`='".$project_id ."' order by id DESC");
	 $phasescount = CtDisplay::getQuery("SELECT * FROM `project_phases` WHERE `project_id`='".$project_id ."' group by phase_type having count(*) > 1");
	 if(!count($phasescount)) {
	   echo "<div class = 'timeline_note_nohistorty'><label>No history available.</label></div>";
	 }
	 foreach($phases as $phase) {
	 $get_timeline = CtDisplay::getQuery("SELECT * FROM `project_phases` WHERE `phase_type`='".$phase['phase_type'] ."' AND `project_id`='$project_id' order by id DESC");//echo "<pre>";print_r($get_timeline);
       if(sizeof($get_timeline) > 1) {
          $phase_array[$phase['phase_type']] += 1;
		  if (sizeof($get_timeline) != $phase_array[$phase['phase_type']]) {
			  $start = date("m/d/Y",strtotime($get_timeline[$phase_array[$phase['phase_type']]]['start_date']));
			  $end = date("m/d/Y",strtotime($get_timeline[$phase_array[$phase['phase_type']]]['projected_end_date']));
			  $user = CtDisplay::getQuery("SELECT first_name,last_name FROM `users` WHERE `id`='".$get_timeline[$phase_array[$phase['phase_type']]-1]['updated_by'] ."'");
			  $phase_name= CtDisplay::getQuery("SELECT name FROM `lnk_project_phase_types` WHERE `id`='".$phase['phase_type']."'");
			  $timeline_start_date = date("m/d/Y", strtotime($get_timeline[$phase_array[$phase['phase_type']]-1]['start_date']));
			  $timeline_end_date = date("m/d/Y", strtotime($get_timeline[$phase_array[$phase['phase_type']]-1]['projected_end_date']));
			  if ($get_timeline[$phase_array[$phase['phase_type']]]['start_date'] == '0000-00-00 00:00:00') $start = '';
			  if ($get_timeline[$phase_array[$phase['phase_type']]]['projected_end_date'] == '0000-00-00 00:00:00') $end = '';
			  if ($get_timeline[$phase_array[$phase['phase_type']]-1]['start_date'] == '0000-00-00 00:00:00') $timeline_start_date = '';
			  if ($get_timeline[$phase_array[$phase['phase_type']]-1]['projected_end_date'] == '0000-00-00 00:00:00') $timeline_end_date = '';
			  echo "<li><div class = 'projected_timeline'><label>".$phase_name[0]['name']."</label></div>";
			  echo "<div class = 'original_dates'>Original Dates:". $start."-".$end."</div>";
			  echo "<div class = 'revised_dates'>Revised Dates:".$timeline_start_date."-".$timeline_end_date."</div>";
			  echo "<div class = 'note_timeline_textarea'><textarea rows = '2' cols = '25' readonly>".$get_timeline[$phase_array[$phase['phase_type']]-1]['note']."</textarea></div>";
			  echo "<div>Updated by ".$user[0]['first_name'].' '.$user[0]['last_name']." on ".date("m/d/Y", strtotime($get_timeline[$phase_array[$phase['phase_type']]-1]['updated_on']))."</div></li>";
		  }
       }



	 }
	 echo "</ul>";
	echo '</div>
<div class="inside resources">
<div class="proles_header">
<h3>PROJECTED TIMELINE FOR Project</h3>
<div style="clear: both;"></div>	
</div>';	
if(isset($_GET['project_id']))
	{			
	echo CtDisplay::getTimelineEditHTMLNEW($_GET['project_id']);	
	}
	else 
		{	
		echo CtDisplay::getTimelineHTML();		
		}			
		echo '</div>	
		</form>			
			</div>';}
	
		/*	echo '<div class="rightCol" id="form_sec_4" style="display: none;">	
			<form action="" method="post" name="form_sec_4" onSubmit="return false;">		
			<div class="inside drafting_actions">		
			<div class="left_actions">	
			<button class="status status_empty" onClick="confirmEmptyScope();"><span>empty</span></button>	
			<button class="status status_draft" onClick="draftStatus(); ajaxFunction(\'\',\'update_project_scope\'); changeSectionStatusMan(\'sec_4\'); setCompleteness();"><span>draft</span></button>								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
			</div>		
			<div class="right_actions">	
			<button class="secondary" onClick="confirmEmptyScope();"><span>clear</span></button>	
			<button class="secondary" name="save" id="form_sec_4_save" onClick="ajaxFunction(\'\',\'update_project_scope\'); changeSectionStatusMan(\'sec_4\'); setCompleteness();"><span>save</span></button>									<!--button class="secondary" onClick="document.getElementById(\'form_sec_4_save\').click();document.getElementById(\'form_sec_4_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->								<button class="secondary" onClick="nxtFalse(); ctCreateSectionsSwitchNextScope();"><span>next</span></button>							</div>						' . $strAddFlag . '						</div>						<div class="riskContainer">';							echo CtDisplay::getProjectRisks($_GET['project_id'], $_SESSION['user_id']); 					echo '</div>						<div class="inside textEdit">';							$scopeBasePath = $_SERVER['PHP_SELF'];		
		$scopeBasePath = substr( $scopeBasePath, 0, strpos( $scopeBasePath, "_samples" ) );	
		$scopeEditor = new FCKeditor('scopeEditor');		
		$scopeEditor->BasePath = $scopeBasePath;	
		$scopeEditor->Config['AutoDetectLanguage'] = true;		
		$scopeEditor->Config['ToolbarCanCollapse'] = false;	
		$scopeEditor->Config['DefaultLanguage'] = 'en';		
		//$approvalEditor->Config['EnterMode'] = 'br';	
		$scopeEditor->ToolbarSet = "NBC";		
		$scopeEditor->Height = 300;			
if(isset($_GET['project_id']))
	{			
	$project_scope = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='4' LIMIT 1");
	if(@$project_scope[0]['desc'] != "null")
	{
	$scopeEditor->Value = @$project_scope[0]['desc'];
	} 
	else
	{
	$scopeEditor->Value = '';
	}		
} 
else
{
$scopeEditor->Value = '';	
}	
$scopeEditor->Create();	
echo '</div>	
</form>
<div class="fck_editor_overlay">	
</div>		
</div>';*/
if (isset($_GET['section']) && $_GET['section'] == 'Resources') {
	echo '<div class="rightCol" id="form_sec_5" style="display: block;">';	

echo '<form action="" method="post" name="form_sec_5" onSubmit="return false;">	
<div class="inside drafting_actions">			
<div class="left_actions">
<button class="status status_empty"><span>empty</span></button>		
<!--<button class="status status_draft" onClick="changeSectionStatusMan(\'sec_5\'); setCompleteness();"><span>draft</span></button>-->	
<button class="status status_complete" onClick="setSectionComplete();"><span>complete</span></button>	
</div>			
<div class="right_actions">		
<!--<button class="secondary"><span>clear</span></button>-->	
<button class="secondary" name="save" id="form_sec_5_save" onClick="saveUserRoles(); return false;"><span>save</span></button>	
<button class="secondary" onClick="nxtFalse(); ctCreateDisplaySection(\'sec_6\');"><span>next</span></button>
</div>' . $strAddFlag . '</div>	
<div class="inside textEdit">	
<div class="inside resources">		
<div class="resource_header"><h3>'.DEV_TEAM_NAME.' RESOURCES</h3></div>		
<ul class="proles" id="project_subphase">';	
if(isset($_GET['project_id'])) 
{						
echo CtDisplay::getResourceHTML($_GET['project_id']);
} 
echo '</ul>	
</div>';
echo '</div>
</form>	
</div>';
}
if (isset($_GET['section'])) {
if ($_GET['section'] == 'Finance') 
   echo '<div class="rightCol" id="form_sec_6" style="display:block;">';
 else echo '<div class="rightCol" id="form_sec_6" style="display:none;">';
echo '<form action="" method="post" name="form_sec_6" onSubmit="return false;" style="float: left;">
<div class="inside drafting_actions" style="width: 734px;">	
<div class="left_actions">		
<button class="status status_empty" onClick="confirmEmptyFilnance();"><span>empty</span></button>
<button class="status status_draft" onClick="draftStatus(); saveFinance(); return false; changeSectionStatusMan(\'sec_6\'); setCompleteness();"><span>draft</span></button>			
<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
</div>			
<div class="right_actions">		
<button class="secondary" onClick="confirmEmptyFilnance(); return false;"><span>clear</span></button>
<button class="secondary" name="save" id="form_sec_6_save" onClick="saveFinance(); return false; changeSectionStatusMan(\'sec_6\'); setCompleteness();"><span>save</span></button>	
<button class="secondary" onClick="nxtFalse(); ctCreateDisplaySection(\'sec_11\');"><span>next</span></button>	
</div>' . $strAddFlag . '</div>	
<div class="inside textEdit" id="finance_calcs" style="width: 736px;">';	

	if(isset($_GET['project_id'])) 
	{	
	echo CtDisplay::getBudgetEditHTML($_GET['project_id']);	
	}		
	if(isset($_GET['project_id'])) 
		{
		echo CtDisplay::getFinanceEditHTML($_GET['project_id']);
		}
		else 
			{	
			echo CtDisplay::getFinanceHTML();	
			}
		echo '<div class="finance_bar_spacer"></div>
		<div class="finance_bar" style="width: 736px;">
			<input type="hidden" id="ct_overall_total" value="'.@CtDisplay::getFinanceTotalEdit($_GET['project_id']).'"/>
	 <div id="overall_finance_total">Overall Total: <span class="cur_for">$' .@CtDisplay::getFinanceTotalEdit($_GET['project_id']) .'</span></div>	
		</div>';
		echo '</div>';
		
		echo '<div id="budget_history" style="border: 1px solid gray; left: 8px; top: 2px; width: 210px; position: relative;float: left;">';		
		echo CtDisplay::getBudgetHistory($_GET['project_id']);
		echo "</div>";

		echo '</form>		
	</div>';
}

	/*echo '<div class="rightCol" id="form_sec_7" style="display: none;">	
	<form action="" method="post" name="form_sec_7" onSubmit="return false;">	
	<div class="inside drafting_actions">
	<div class="left_actions">		
	<button class="status status_empty" onClick="confirmEmptyDeliver();"><span>empty</span></button>
	<button class="status status_draft" onClick="draftStatus(); ajaxFunction(\'\',\'update_project_deliverables\'); changeSectionStatusMan(\'sec_7\'); setCompleteness();"><span>draft</span></button>	
	<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>								</div>							<div class="right_actions">	
	<button class="secondary" onClick="confirmEmptyDeliver();"><span>clear</span></button>	
	<button class="secondary" name="save" id="form_sec_7_save" onClick="ajaxFunction(\'\',\'update_project_deliverables\'); changeSectionStatusMan(\'sec_7\'); setCompleteness();"><span>save</span></button>	
	<!--<button class="secondary" onClick="document.getElementById(\'form_sec_7_save\').click();document.getElementById(\'form_sec_7_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->			
	<button class="secondary" onClick="nxtFalse(); ctCreateSectionsSwitchNextDeliver();"><span>next</span></button>
	</div>' . $strAddFlag . '</div>	
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
	$deliverEditor->Height = 300;	
	if(isset($_GET['project_id']))
		{			
		$project_deliver = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='7' LIMIT 1");
		if(@$project_deliver[0]['desc'] != "null") 
			{								
			$deliverEditor->Value = @$project_deliver[0]['desc'];	
			} else 
			{							
				$deliverEditor->Value = '';	
			}				
			}
		else
		{		
			$deliverEditor->Value = '';		
			}		
			$deliverEditor->Create();
			echo '</div>	
			</form>		
	<div class="fck_editor_overlay">	
</div>	
</div>		
<div class="rightCol" id="form_sec_8" style="display: none;">	
<form action="" method="post" name="form_sec_8" onSubmit="return false;">	
<div class="inside drafting_actions">
<div class="left_actions">		
<button class="status status_empty" onClick="confirmEmptyMetrics();"><span>empty</span></button>	
<button class="status status_draft" onClick="draftStatus(); ajaxFunction(\'\',\'update_project_metrics\'); changeSectionStatusMan(\'sec_8\'); setCompleteness();"><span>draft</span></button>	
<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>
</div>		
<div class="right_actions">		
<button class="secondary" onClick="confirmEmptyMetrics();"><span>clear</span></button>	
<button class="secondary" name="save" id="form_sec_8_save" onClick="ajaxFunction(\'\',\'update_project_metrics\'); changeSectionStatusMan(\'sec_8\'); setCompleteness();"><span>save</span></button>	
<!--<button class="secondary" onClick="document.getElementById(\'form_sec_8_save\').click();document.getElementById(\'form_sec_8_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->	
<button class="secondary" onClick="nxtFalse(); ctCreateSectionsSwitchNextMetrics();"><span>next</span></button>	
</div>' . $strAddFlag . '</div>	
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
$metricsEditor->Height = 300;		
if(isset($_GET['project_id'])) 
{			
$project_metrics = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='8' LIMIT 1");	
if(@$project_metrics[0]['desc'] != "null") 
{	
$metricsEditor->Value = @$project_metrics[0]['desc'];	
} 
else
{	
$metricsEditor->Value = '';	
}			
} 
else
{	
$metricsEditor->Value = '';	
}		
$metricsEditor->Create();
echo '</div>	
<div class="fck_editor_overlay">
</div>
</form>
</div>	
<div class="rightCol" id="form_sec_9" style="display: none;">
<form action="" method="post" name="form_sec_9" onSubmit="return false;">	
<div class="inside drafting_actions">
<div class="left_actions">	
<button class="status status_empty" onClick="confirmEmptyApprovals();"><span>empty</span></button>	
<button class="status status_draft" onClick="draftStatus(); saveApprovals(); changeSectionStatusMan(\'sec_9\'); setCompleteness();"><span>draft</span></button>							
<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>								
</div>
<div class="right_actions">		
<button class="secondary" onClick="confirmEmptyApprovals();"><span>clear</span></button>
<button class="secondary" name="save" id="form_sec_9_save" onClick="saveApprovals(); changeSectionStatusMan(\'sec_9\'); setCompleteness();"><span>save</span></button>		
<button class="secondary" onClick="nxtFalse(); ctCreateSectionsSwitchNextApprovals();"><span>next</span></button>
</div>' . $strAddFlag . '</div>		
<div class="inside resources" id="approvals">		
<div class="proles_header">	
<h3>PROJECT APPROVALS</h3>		
<div style="clear: both;"></div>		
</div>			
<ul class="papprovals">		
<li style="display: none;"><form></form></li>
<li>	
<form action="" method="post" name="appr_producer" id="appr_producer">';	
if(isset($_GET['project_id']))
{		
$project_appr = CtDisplay::getQuery("SELECT * FROM `project_phase_approvals` WHERE `project_id`='" .@$project_data[0]['id'] ."' AND `non_phase`='nbcuxd' LIMIT 1");	
$user_list = CtDisplay::getQuery(QRY_USERS_ASC);		
if(is_array($project_appr))
	{	
	$appr_nbc_name  = @$project_appr[0]['name'];
	$appr_nbc_title  = @$project_appr[0]['title'];	
	$appr_nbc_phone  = @$project_appr[0]['phone'];	
	$appr_nbc_date  = @$project_appr[0]['approval_date'];	
	$appr_nbc_approval  = @$project_appr[0]['approved'];	
	if(empty($appr_nbc_title)) 
	{				
	$appr_nbc_title = "--title--";		
	}					
	if(empty($appr_nbc_phone)) 
	{				
	$appr_nbc_phone = "--phone--";	
	}		
	$appr_date_part = explode(" ", $appr_nbc_date);	
	$appr_date = explode("-", $appr_date_part[0]);	
	if(@$appr_date[0] != '0000')
	{		
	$date = @$appr_date[1] . "/" .@$appr_date[2] ."/" .@$appr_date[0];
	}
	else 
	{	
	$date = "";		
	}	
if($appr_nbc_approval == 1) 
	{	
	$checked = " CHECKED";	
	} 
	else 
{	
$checked = "";	
}		
}
else 
	{
	$appr_nbc_name  = "";
	$appr_nbc_title  = "";
	$appr_nbc_phone  = "";
	$appr_nbc_date  = "";		
	$appr_nbc_approval  = "";	
	$date = "";			
	$checked = "";	
	}				
	} 
	else 
		{
		$appr_nbc_name  = "";		
		$appr_nbc_title  = "";		
		$appr_nbc_phone  = "";		
		$appr_nbc_date  = "";		
		$appr_nbc_approval  = "";		
		$date = "";		
		$checked = "";	
		}		
		if(!empty($appr_nbc_name))
			{	
			$preSel = "yes";
			}else
				{	
				$preSel = "no";
				}		
				echo '<input type="hidden" name="phase" id="phase" value="nbcux" />
				<input type="hidden" name="preselect" id="preselect" value="' .$preSel .'" />
					<div class="papprovals_phase"><label for="phase">'.DEV_TEAM_NAME.'</label></div>
					<div class="papprovals_name">	
					<!--<input type="text" name="user_name" id="user_name" value="' .$appr_nbc_name .'" />&nbsp;-->	
					<select name="user_name" id="user_name">	
					<option value="">--Select User--</option>';	
				for($u = 0; $u < sizeof($user_list); $u++)
					{	
					if($appr_nbc_name == $user_list[$u]['id'])
						{			
						$selected = " SELECTED";
						} 
						else
						{
						$selected = "";		
						}	
						echo '<option value="' .$user_list[$u]['id'] .'"' .$selected .'>' .ucfirst($user_list[$u]['first_name']) .' ' .ucfirst($user_list[$u]['last_name']) .'</option>';
						}	
						echo '</select>&nbsp;		
						</div>			
						<div class="papprovals_title">	
						<input type="text" name="user_title" id="user_title" value="' .$appr_nbc_title .'" onFocus="clearOnFocus(this);" />&nbsp;									
						</div>	
						<div class="papprovals_phone">	
						<input type="text" name="user_phone" id="user_phone" value="' .$appr_nbc_phone .'" onFocus="clearOnFocus(this);" />&nbsp;										</div>										<div class="papprovals_approved">		
						<label for="approved">Approved</label>&nbsp;	
						<input type="checkbox" name="approved" id="approved"' .$checked .' />&nbsp;	
						</div>			
						<div class="papprovals_date">	
						<input type="text" name="approval_date" class="date_picker readonly" id="approval_date_nbc" value="' .$date .'" readonly />										</div>										<div style="clear: both">
					</div>	
					</form>	
					</li>	
					<li>	
					<form action="" method="post" name="appr_client" id="appr_client">';	
						if(isset($_GET['project_id'])) 
							{							
							$project_appr = CtDisplay::getQuery("SELECT * FROM `project_phase_approvals` WHERE `project_id`='" .@$project_data[0]['id'] ."' AND `non_phase`='client' LIMIT 1");																						if(is_array($project_appr)) {	
			$appr_nbc_name  = @$project_appr[0]['name'];
			$appr_nbc_title  = @$project_appr[0]['title'];	
			$appr_nbc_phone  = @$project_appr[0]['phone'];	
			$appr_nbc_date  = @$project_appr[0]['approval_date'];
			$appr_nbc_approval  = @$project_appr[0]['approved'];
			if(empty($appr_nbc_title)) 
		   {		
		$appr_nbc_title = "--title--";		
		}					
		if(empty($appr_nbc_phone)) {
			$appr_nbc_phone = "--phone--";	
			}	
			$appr_date_part = explode(" ", $appr_nbc_date);	
			$appr_date = explode("-", @$appr_date_part[0]);		
if(@$appr_date[0] != '0000')
	{	
	$date = @$appr_date[1] . "/" .@$appr_date[2] ."/" .@$appr_date[0];	
	} 
	else
		{		
		$date = "";	
		}	
		if($appr_nbc_approval == 1)
			{	
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
				} else {
					$appr_nbc_name  = "";
					$appr_nbc_title  = "";	
					$appr_nbc_phone  = "";	
					$appr_nbc_date  = "";	
					$appr_nbc_approval  = "";
					$date = "";		
					$checked = "";		
					}		
					if(!empty($appr_nbc_name)) 
						{	
						$preSel = "yes";	
						} else {	
							$preSel = "no";	
							}		
echo '<input type="hidden" name="phase" id="phase" value="client" />	
<input type="hidden" name="preselect" id="preselect" value="' .$preSel .'" />
	<div class="papprovals_phase"><label for="phase">Client</label></div>	
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
				echo '<option value="' .$user_list[$u]['id'] .'"' .$selected .'>' .ucfirst($user_list[$u]['first_name']) .' ' .ucfirst($user_list[$u]['last_name']) .'</option>';	
				}							
				echo '</select>&nbsp;	
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
					<input type="text" name="approval_date" class="date_picker readonly" id="approval_date_client" value="' .$date .'" readonly />
					</div>
					<div style="clear: both"></div>
					</form>
					</li>
					</ul>
					<div class="proles_header">
					<h3>STAGE APPROVALS</h3>	
					<div style="clear: both;"></div>
					</div>';
				if(isset($_GET['project_id']))
					{
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
	<button class="status status_empty" onClick="confirmEmptyBcase();"><span>empty</span></button>	
	<button class="status status_draft" onClick="draftStatus(); ajaxFunction(\'\',\'update_project_bcase\'); changeSectionStatusMan(\'sec_10\'); setCompleteness();"><span>draft</span></button>								<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>	
	</div>	
	<div class="right_actions">	
	<button class="secondary" onClick="confirmEmptyBcase();"><span>clear</span></button>
	<button class="secondary" name="save" id="form_sec_10_save" onClick="ajaxFunction(\'\',\'update_project_bcase\'); changeSectionStatusMan(\'sec_10\'); setCompleteness();"><span>save</span></button>	
	<!--<button class="secondary" onClick="document.getElementById(\'form_sec_10_save\').click();document.getElementById(\'form_sec_10_save\').click(); ctCreateSectionsSwitchNext();"><span>next</span></button>	-->								<button class="secondary" onClick="nxtFalse(); ctCreateSectionsSwitchNextBcase();"><span>next</span></button>	
	</div>' . $strAddFlag . '</div>		
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
	$bcaseEditor->Height = 300;		
	if(isset($_GET['project_id'])) 
	{				
	$project_bcase = CtDisplay::getQuery("SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$project_data[0]['id'] ."' AND `section_type`='10' LIMIT 1");	
	if(@$project_bcase[0]['desc'] != "null")
		{
		$bcaseEditor->Value = @$project_bcase[0]['desc'];
		} else {
			$bcaseEditor->Value = '';
			}			
			} else {
				$bcaseEditor->Value = '';
				}	
				$bcaseEditor->Create();		
				echo '</div>	
				</form>	
				<div class="fck_editor_overlay">
			</div>		
			</div>';*/
			if (!isset($_GET['section'])) {
				echo '<div class="rightCol" id="form_sec_12" style="display: block;">';
			
				echo '<div class = "wrapper_sections wrapper_sections_1">';
				echo '
					<h3>Project Description</h3><ul class = "sections_project_description">';
					echo '<li class = "project_description_details_labels"><ul class = "project_description_details">';
							echo '<li class = "project_status">Project Status: </li>';
							echo '<li class = "project_program">Program:</li>';
							echo '<li class = "project_charter">Project Charter: </li>';
							echo '<li class = "project_scope">Scope: </li></ul></li>';
							echo '<li class = "project_description_details_values"><ul class = "project_description_results">';
								$project_status_sql = 'SELECT * FROM `lnk_project_status_types` where `id`=(select IFNULL(`project_status`, 0) from `projects` where id="' . $_GET['project_id'] . '")';
								$projectStatusResult = CtDisplay::getQuery($project_status_sql);
								foreach($projectStatusResult as $status){
									$project_status = $status['name'];
								}

							echo '<li>'.$project_status.'</li>';	
							if(!empty($_GET['project_id']))
							{
								$project_program_Result = CtDisplay::getQuery("SELECT * FROM `projects` where `id`='".$_GET['project_id']."'");
								$program = $project_program_Result[0]['program'];
							}

							$project_program_sql = "SELECT * FROM `lnk_programs` where active='1' and deleted='0' and id='".$program."'";
							$projectProgramResult = CtDisplay::getQuery($project_program_sql);
							foreach($projectProgramResult as $prog){
								$program = $prog['program'];
							}
							echo '<li>'.$program.'</li><li>'.$project_program_Result[0]["project_charter"].'</li>	
								<li>'.$project_program_Result[0]["project_scope"].'</li></ul></li>	
						</ul><button class="secondary" onclick=ctCreateDisplaySection("sec_1")><span>Edit</span></button>
					
				</div>

				<div class = "wrapper_sections wrapper_sections_2">
					<h3>Project Owner & Roles</h3><ul class = "sections_project_roles">';
					$roles = CtDisplay::getQuery("SELECT *FROM `project_roles` where project_id='".$_GET['project_id']."'");
					$role_count = 0;
					foreach ($roles as $role) {
					  if ($role['email'] != '') {
						  echo '<li class="sections_project_roles_results_'.$role_count.'"><ul>';
						  $res_type =  CtDisplay::getQuery("SELECT name FROM `resource_types` where id='".$role['resource_type_id']."'");
						  $user =  CtDisplay::getQuery("SELECT first_name, last_name FROM `users` where id='".$role['user_id']."'");
						  echo '<li>User:'.$user[0]['first_name'].' '.$user[0]['last_name'].'</li>';
						  echo '<li>Resource Type:'.$res_type[0]['name'].'</li>';
						  echo '<li>Email:'.$role['email'].'</li>';
						  echo '<li>Phone:'.$role['phone'].'</li></ul></li>';
						  $role_count++;
					  }
					  if ($role_count == 3) break;
					}
				echo '</ul><button class="secondary" onclick=ctCreateDisplaySection("sec_2")><span>Edit</span></button></div>
					<div class = "wrapper_sections wrapper_sections_3">
					<h3>Project Timeline</h3>';
					$phases = CtDisplay::getQuery(QRY_TIMELINE_SO_ASC);
					$project_id = $_GET['project_id'];
					$proj_time_count = 0;
					for($i = 0; $i < sizeof($phases); $i++) {
						$timeline_link = CtDisplay::getQuery("SELECT * FROM `lnk_resource_stage` WHERE `phase_id`='" .$phases[$i]['id'] ."' LIMIT 1");
						if(is_array($timeline_link)) {
							$is_role = CtDisplay::getQuery("SELECT * FROM `project_roles` WHERE `resource_type_id`='" .@$timeline_link[0]['resource_id'] ."' AND `project_id`='$project_id'");
						}
						if(sizeof($is_role) > 0) {
							$get_timeline = CtDisplay::getQuery("SELECT * FROM `project_phases` WHERE `phase_type`='".$phases[$i]['id'] ."' AND `project_id`='$project_id' order by ID DESC LIMIT 1");
							if(sizeof($get_timeline) > 0) {
								$start = date("m/d/Y", strtotime($get_timeline[0]['start_date']));
								$end = date("m/d/Y", strtotime($get_timeline[0]['projected_end_date']));
							
								if ($start != '' && $get_timeline[0]['start_date'] != '0000-00-00 00:00:00') {
									$proj_time_count++;
									echo '<ul class = "sections_project_timeline_'.$proj_time_count.'">';
									echo '<li>Phase:'.$phases[$i]['name'].'</li>';
									echo '<li>Start Date:'.$start.'</li>';
									echo '<li>Projected End Date:'.$end.'</li></ul>';
								}
							}
						}
						else {
						  $get_timeline = CtDisplay::getQuery("SELECT * FROM `project_phases` WHERE `phase_type`='".$phases[$i]['id'] ."' AND `project_id`='$project_id' order by id DESC LIMIT 1");
							if(sizeof($get_timeline) > 0) {
								$start = date("m/d/Y", strtotime($get_timeline[0]['start_date']));
								$end = date("m/d/Y", strtotime($get_timeline[0]['projected_end_date']));
							
								if(sizeof($timeline_link) < 1 && $start != '' && $get_timeline[0]['start_date'] != '0000-00-00 00:00:00') {
									$proj_time_count++;
									echo '<ul class = "sections_project_timeline_'.$proj_time_count.'">';
									echo '<li>Phase:'.$phases[$i]['name'].'</li>';
									echo '<li>Start Date:'.$start.'</li>';
									echo '<li>Projected End Date:'.$end.'</li></ul>';
								}
							}
						}
						if ($proj_time_count == 3) break;
					}
				echo '<button class="secondary" onclick=ctCreateDisplaySection("sec_3")><span>Edit</span></button></div>
				<div class = "wrapper_sections wrapper_sections_5">
					<h3>Resources</h3>';
					$project_id = $_GET['project_id'];
					$users = CtDisplay::getQuery("SELECT DISTINCT a.`id`, a.* FROM `users` a, `resource_blocks` b WHERE a.`id` = b.`userid` AND b.`projectid`='" .$project_id ."'");
					$phaseList = CtDisplay::getPhases($project_id);
					$resource_count = 0;
					for($i = 0; $i < sizeof($users); $i++) {
						$total_actual = 0;
						$total_booked = 0;
						$resources = CtDisplay::getQuery("SELECT * FROM `resource_blocks` WHERE `projectid`='" .$project_id ."' AND `userid`='" .$users[$i]['id'] ."'");
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
						$userRole = CtDisplay::getQuery("SELECT * FROM `user_project_role` WHERE `user_id`='" .$users[$i]['id'] ."' AND `project_id`='" .$project_id ."' LIMIT 1");
						$phase_val = '';
						foreach($phaseList as $phase => $phaseValue){
							if($userRole[0]['flag'] == 'phase' && $userRole[0]['phase_subphase_id'] == $phase){
								$phase_val = $phaseValue['name'];
							}
							if(array_key_exists('subphase', $phaseValue)){
								foreach($phaseValue['subphase'] as $subphase => $spValue){
									if($userRole[0]['flag'] == 'subphase' && $userRole[0]['phase_subphase_id'] == $subphase){
										$phase_val = $spValue['name'];
									}
								}
							}
						}
						echo '<ul class = "sections_project_resources_'.$resource_count.'">';
						echo '<li>User:'.$users[$i]['first_name'].' '.$users[$i]['last_name'].'</li>';
						echo '<li>Resource Type:'.$phase_val.'</li>';
						echo '<li>Actual Hours:'.$total_actual.'</li>';
						echo '<li>Scheduled Hours:'.$total_booked.'</li></ul>';
						$resource_count++;
						if ($resource_count == 3) break;
					}
				echo '<button class="secondary" onclick=ctCreateDisplaySection("sec_5")><span>Edit</span></button></div>
				<div class = "wrapper_sections wrapper_sections_6">
					<h3>Finance & Budget</h3>';
				$phaseToDate = CtDisplay::calculateDate($_GET['project_id']);			
	
				$project_details_sql = "SELECT bc_id from `projects` WHERE id='" . $_GET['project_id'] . "'";
				$project_details_result = CtDisplay::getQuery($project_details_sql);
				$project_details_row = $project_details_result[0];


				$select_project_phases = "SELECT ppf.phase phase, ppf.rate rate, ppf.hours hours FROM project_phase_finance ppf, lnk_project_phase_types lppt where ppf.phase = lppt.id and ppf.project_id = '" .$_GET['project_id'] ."' order by lppt.sort_order";

				$result_phases = CtDisplay::getQuery($select_project_phases);
				$row = 0;
				$todate = 0;
				foreach($result_phases as $row_phases) {
					$total_finance = 0;
					$select_phase_data = "SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$row_phases['phase'] ."' LIMIT 1";
					$phase_data_res = CtDisplay::getQuery($select_phase_data);
					$phase_data_row = $phase_data_res[0];
					$timeline_query = "SELECT * FROM `project_phases` WHERE `project_id`='" .$_GET['project_id']."' AND `phase_type`='" .$row_phases['phase'] ."' order by id DESC LIMIT 1";
					$timeline_res = CtDisplay::getQuery($timeline_query);
					$timeline_row = $timeline_res[0];
					if(array_key_exists($row_phases['phase'], $phaseToDate)){
						$todate = $phaseToDate[$row_phases['phase']];
					}else{
						$todate = 0;
					}

					$sub_phase_select = "select * from project_sub_phase_finance where phase='" .$row_phases['phase'] ."' and project_id='" .$_GET['project_id'] ."' and active='1'";
					$result_sub_phase_select = CtDisplay::getQuery($sub_phase_select);
					if(count($result_sub_phase_select) > 0){
						foreach($result_sub_phase_select as $project_subphase_row){
							$total_finance += $project_subphase_row['hours'] * $project_subphase_row['rate'];
						}
					}else{
						$total_finance += $row_phases['hours'] * $row_phases['rate'];
					}


					$start_date_time = explode(" ", $timeline_row['start_date']);
					$start_date = explode("-", $start_date_time[0]);
					$end_date_time = explode(" ", $timeline_row['projected_end_date']);
					$end_date = explode("-", $end_date_time[0]);
					$display = '1';
					if($row_phases['phase'] == UNASSIGNED_PHASE)
					{
						if($todate!='0'){
							$display = '1';
						}
						else
						{
							$display = '0';
						}
					}
					if ($display == '1') {
					  $projectDetail = '<!-- DATA -->
							<ul class = "sections_project_finance_'.$row.'">
								<li class="project_detail">' .$phase_data_row['name'] .'</li>
								<li class="dates">Dates:' .CtDisplay::checkPhaseDate($start_date[1]) ."/" .CtDisplay::checkPhaseDate($start_date[2]) ."/".CtDisplay::checkPhaseDate($start_date[0]).' - ' .CtDisplay::checkPhaseDate($end_date[1]) ."/" .CtDisplay::checkPhaseDate($end_date[2]) ."/".CtDisplay::checkPhaseDate($end_date[0]).'</li>
								<li class="actual">To Date:$' .number_format($todate, 0, '.', ',') .'</li>
								<li class="budget">Budget:$' .number_format($total_finance, 0, '.', ',') .'</li>
							</ul>';
					}
					echo  $projectDetail;
					$row++;
					if ($row == 3) break;
				}
				echo '<button class="secondary" onclick=ctCreateDisplaySection("sec_6")><span>Edit</span></button></div>
				<div class = "wrapper_sections wrapper_sections_11">
					<h3>Project Permissions</h3>';
					$projectPermission = CtDisplay::getQuery("SELECT b.`rp_permission`,b.`wo_permission` FROM `projects` b WHERE b.`id`='$project_id' LIMIT 1");
					echo "<ul><li class = 'sections_project_permissions_labels'><ul>";
					if($projectPermission[0]['rp_permission'] == 1) echo "<li>Resource Planner:</li>";
					if($projectPermission[0]['wo_permission'] == 1) echo "<li>Work Orders:</li>";echo '</ul></li><li><ul>';
					if($projectPermission[0]['rp_permission'] == 1)
						echo '<li>'.str_replace('http://', '', BASE_URL).'/resourceplanner</li>';
					if($projectPermission[0]['wo_permission'] == 1)
						echo '<li>'.str_replace('http://', '', BASE_URL).'/workorders</li>';
					echo "</ul></li></ul>";
				echo '<button class="secondary" onclick=ctCreateDisplaySection("sec_11")><span>Edit</span></button></div>
			</div>';
			}
			$section_array = array("Description", "Roles", "Timeline", "Resources", "Finance", "Permissions");

			if (isset($_GET['section'])) {
			  if (!in_array(trim($_GET['section']), $section_array)) {
			    header("Location: ".BASE_URL."/noaccess/index/error/");
			  }
			}

			if (isset($_GET['section']) && $_GET['section'] == 'Permissions') {
				echo '<div class="rightCol" id="form_sec_11" style="display:block;">';
			
			echo '<form action="" method="post" name="form_sec_11" id="form_sec_11" onSubmit="return false;">	
			<div class="inside drafting_actions">	
			<div class="left_actions">	
			<!--<button class="status status_empty" onClick="confirmEmptyTimeline();"><span>empty</span></button>-->	
			<button class="status status_draft" onClick="draftStatus(); saveTimeline();  changeSectionStatusMan(\'sec_11\'); setCompleteness();"><span>draft</span></button>	
			<button class="status status_complete" onClick="setSectionComplete(); setCompleteness();"><span>complete</span></button>						
			</div>				
			<div class="right_actions">					
			<button class="secondary" name="save" id="form_sec_11_save" onClick="changeSectionStatusMan(\'sec_11\'); setCompleteness(); updateProjectPermission();"><span>save</span></button>	
					</div>						' . $strAddFlag . '						</div>
			<div class="inside permission">
			<div class="proles_header">		
			<h3>Project Permissions</h3>		
			<div style="clear: both;"></div>	
			</div>';		
			if(isset($_GET['project_id'])) 
				{				
				echo CtDisplay::projectPermissions($_GET['project_id']);	
				} else {	
					echo CtDisplay::projectPermissions();
					}		
					echo '</div>
						<div class="inside resources">		
						<div class="proles_header">	
						<h3>Work Order Permissions</h3>	
						<div style="clear: both;"></div>	
						</div>';							
					if(isset($_GET['project_id'])) {	
						echo CtDisplay::workorderPermissions($_GET['project_id']);
						} else {		
	echo CtDisplay::workorderPermissions();	
	}				
	echo '</div>
	</form>		
	</div>';
			}
			
	echo '<div style="clear: both;"></div>
	</div>		
	</div>			
	<form method="post" action="' .BASE_URL .'/pdfs/export_project.php" target="_blank" id="pdfform" name="pdfform">	</form>
	<form action="" name="add_flag" id="add_flag" onSubmit="return false;">		
		<div class="add_risk">	
		<div class="close_add_risk">X</div>		
		<div class="add_risk_content">	
<label>Short description of issue (one line):</label>		
		<input type="text" name="risk_title" id="risk_title"/>	
		<label>More detail:</label>			
		<textarea name="risk_desc" id="risk_desc"></textarea>	
		<ul>					
		<li class="label_name">Assign to:<span class="optional">optional</span></li>
		<li class="label_value"> ' . CtDisplay::getRiskPermsList($_GET['project_id']) . '</li>
		</ul>			
		<div class="add">	
		<button class="status status_flag" onClick="createRisk(\''. $_GET['project_id'] .'\', \''. $_SESSION['user_id'] .'\'); return false;"><span>Add Flag</span></button>				
		</div>			
		</div>			
		</div>	
		</form>		
		<!--==| Project Status ==|-->
		<div class="project_status_list" style="display:none;">		
		<ul>' . CtDisplay::getProjectStatusList($_GET['project_id']) . '	</ul>
		</div>		
		<!--==| Section Clear Messages ==|-->	
		<div class="message_clear_roles">	
		<p>		
		You are about to clear this sections data.<br />Do you want to continue?	
		</p>		
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">	
		<button onClick="clearRoles(); $(\'.message_clear_roles\').css({display:\'none\'}); return false;"><span>Yes</span></button> 					
		<button class="cancel" onClick="$(\'.message_clear_roles\').css({display:\'none\'}); return false;"><span>No</span></button>	
		<div style="clear: both;"></div>	
		</div>		
		</div>			
		<div class="message_clear_role">	
		<p>					
		You are about to disable this role.<br />This will will also remove roles in other sections.<br />	Do you want to continue?					
		</p>					
		<input type="hidden" name="single_role" id="single_role" value="" />	
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">		
		<button onClick="fadeDimmer(document.getElementById(\'single_role\').value); $(\'.message_clear_role\').css({display:\'none\'}); return false;"><span>Yes</span></button> 					
		<button class="cancel" onClick="$(\'.message_clear_role\').css({display:\'none\'}); return false;"><span>No</span></button>	
		<div style="clear: both;"></div>	
		</div>			
		</div>		
		<div class="message_clear_timeline">
		<p>			
		You are about to clear this sections data.<br />Do you want to continue?	
		</p>				
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">				
		<button onClick="clearTimeline(); $(\'.message_clear_timeline\').css({display:\'none\'}); return false;"><span>Yes</span></button> 
		<button class="cancel" onClick="$(\'.message_clear_timeline\').css({display:\'none\'}); return false;"><span>No</span></button>		
		<div style="clear: both;">
		</div>			
		</div>			
		</div>		
		<div class="message_clear_finance">	
		<p>			
		You are about to clear this sections data.<br />Do you want to continue?			
		</p>		
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">		
		<button onClick="clearFinance(); $(\'.message_clear_finance\').css({display:\'none\'}); return false;"><span>Yes</span></button> 	
		<button class="cancel" onClick="$(\'.message_clear_finance\').css({display:\'none\'}); return false;"><span>No</span></button>	
		<div style="clear: both;"></div>	
		</div>		
		</div>	
			
		<div class="budget_save" style="text-align: center; padding: 10px; width: 228px;">
			<input type="hidden" id="ct_user_note_id" value="0"/>
			<p style="font-size: 11pt">Leave a note (optional)</p>
			<div class="project_note_save" style="text-align: left;padding-top: 5px"><textarea maxlength="100" rows="2" cols="25"></textarea></div>
			<div style="clear: both;"></div>	
			<div class="duplicate_buttons">			
			<button onClick="ajaxFunction(\'\', \'budget_save\'); $(\'.budget_save\').css({display:\'none\'}); return false;"><span>Save</span></button>
			<div style="clear: both;"></div>	
			</div>		
		</div>
    	
		<div class="message_clear_approvals">	
		<p>					
		You are about to clear this sections data.<br />Do you want to continue?
		</p>
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">	
		<button onClick="clearApprovals(); $(\'.message_clear_approvals\').css({display:\'none\'}); return false;"><span>Yes</span></button> 			
		<button class="cancel" onClick="$(\'.message_clear_approvals\').css({display:\'none\'}); return false;"><span>No</span></button>			
		<div style="clear: both;"></div>	
		</div>		
		</div>		
		<!--==| FCK Editor Clear Messages ==|-->	
		<div class="message_clear_desc">		
		<p>			
		You are about to clear this sections data.<br />Do you want to continue?	
		</p>		
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">			
		<button onClick="clearFCK(\'descEditor\'); $(\'.message_clear_desc\').css({display:\'none\'}); return false;"><span>Yes</span></button> 
		<button class="cancel" onClick="$(\'.message_clear_desc\').css({display:\'none\'}); return false;"><span>No</span></button>			
		<div style="clear: both;"></div>	
		</div>		
		</div>
		<div class="project_history_note">	
			<p>Leave a note (optional)</p>
			<div class="project_note_save"><textarea maxlength="100" rows="2" cols="25"></textarea></div>
			<div style="clear: both;"></div>	
			<div class="duplicate_buttons">			
			<button onClick="ajaxFunction(\'\', \'project_history_note_save\'); $(\'.project_history_note\').css({display:\'none\'}); return false;"><span>Save</span></button> 			
			<div style="clear: both;"></div>	
			</div>		
		</div>
		<div class="timeline_history_note">	
			<p>Leave a note (optional)</p>
			<div><textarea maxlength="100" cols = "25" rows = "2"></textarea></div>
			<div style="clear: both;"></div>	
			<div class="duplicate_buttons">			
			<button onClick="ajaxFunction(\'\', \'timeline_history_note_save\'); $(\'.timeline_history_note\').css({display:\'none\'}); return false;"><span>Save</span></button> 			
			<div style="clear: both;"></div>	
			</div>		
		</div>
		<div class="message_clear_scope">	
		<p>				
		You are about to clear this sections data.<br />Do you want to continue?	
		</p>			
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">		
		<button onClick="clearFCK(\'scopeEditor\'); $(\'.message_clear_scope\').css({display:\'none\'}); return false;"><span>Yes</span></button> 					
		<button class="cancel" onClick="$(\'.message_clear_scope\').css({display:\'none\'}); return false;"><span>No</span></button>	
		<div style="clear: both;"></div>	
		</div>		
		</div>		
		<div class="message_clear_deliver">	
		<p>		
		You are about to clear this sections data. Do you want to continue?		
		</p>		
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>
		<div class="duplicate_buttons">		
		<button onClick="clearFCK(\'deliverEditor\'); $(\'.message_clear_deliver\').css({display:\'none\'}); return false;"><span>Yes</span></button> 					
		<button class="cancel" onClick="$(\'.message_clear_deliver\').css({display:\'none\'}); return false;"><span>No</span></button>		
		<div style="clear: both;"></div>	
		</div>		
		</div>		
		<div class="message_clear_metrics">	
		<p>					
		You are about to clear this sections data.<br />Do you want to continue?	
		</p>	
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>		
		<div class="duplicate_buttons">			
		<button onClick="clearFCK(\'metricsEditor\'); $(\'.message_clear_metrics\').css({display:\'none\'}); return false;"><span>Yes</span></button> 					
		<button class="cancel" onClick="$(\'.message_clear_metrics\').css({display:\'none\'}); return false;"><span>No</span></button>
		<div style="clear: both;"></div>	
		</div>			
		</div>			
		<div class="message_clear_bcase">
		<p>					
		You are about to clear this sections data.<br />Do you want to continue?
		</p>	
		<input type="hidden" name="delete_project_confirm" id="delete_project_confirm" value="" />	
		<div style="clear: both;"></div>		
		<div class="duplicate_buttons">		
		<button onClick="clearFCK(\'bcaseEditor\'); $(\'.message_clear_bcase\').css({display:\'none\'}); return false;"><span>Yes</span></button> 					
		<button class="cancel" onClick="$(\'.message_clear_bcase\').css({display:\'none\'}); return false;"><span>No</span></button>					
		<div style="clear: both;"></div>	
		</div>		
		</div>			
		<div class="message_timeline_date">	
		<p>				
		An invalid date has been entered.<br />Please fix this issue to continue?
		</p>						
		<div style="clear: both;"></div>	
		<div class="duplicate_buttons">		
		<button onClick="$(\'.message_timeline_date\').css({display:\'none\'}); return false;"><span>OK</span></button> 	
		<div style="clear: both;"></div>
		</div>		
		</div>			
		<div class="sub_phase_list" onmouseover="$(\'.sub_phase_list\').css({display:\'block\'}); return false;" onmouseout="$(\'.sub_phase_list\').css({display:\'none\'}); return false;">
		</div>	
		<script>	
		$(function() 
		{			
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
	 /**
	  * LH22669
	  * calendar view control tower
	  */
	function calendarviewAction()
		{
			$actionName = $this->getRequest()->getActionName();
			echo '<input type="hidden" name="curControllerName" id ="curControllerName" value="'.$actionName.'">';
			$totalBudget = CtDisplay::financeTotal();
			$totalActiveProjects = CtDisplay::totalActiveProjects();	
			$htmlCompany = CtDisplay::getCompanyBcHTML();	
			if($_SESSION['login_status'] == "client")
			{	
			$hideStyle = "display: none;";			
			} 
			else
			{			
			$hideStyle = "";	
			}
			echo '<script language="javascript">	
			totalBudget = "'.$totalBudget.'";			
			totalActive = "'.$totalActiveProjects.'";	
			</script>';		
			if(isset($_COOKIE["lighthouse_ct_data"])){	
			echo '<input type="hidden" id="ct_loadFilter" name="ct_loadFilter" value="' . urldecode($_COOKIE["lighthouse_ct_data"]) . '">';			
			}else
			{				
			echo '<input type="hidden" id="ct_loadFilter" name="ct_loadFilter" value="">';	
			}		
			echo '			<!--=========== START: COLUMNS ===========-->			
			<!--==| START: Bucket |==-->
			
			<div class=" create_pop_up " style="width:3000px;height:1024px;position:fixed;background-color:#fffff;z-index:1.0;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:2.0; z-index:1;"></div>
			<div class="main_actions" style="display">				
			<!--<button onClick="window.location = \'/controltower/index/create/\'"><span>create new project</span></button>-->	
			<button onClick="createProject();"><span>create new project</span></button>	
				<p class="message">Select a company or lead to list the projects.</p>';  
					/*				if($_SESSION['login_status'] == "admin") {					echo '<div class="right_actions">						<button onClick="cloneProject();"><span>CLONE PROJECTS</span></button>					</div>					<div class="clone_pop_up">								<div id="clone_project_pop_up" style="margin-left:30px;" >		</div>						<div style="clear: both;"></div>											<div class="duplicate_buttons" style="float:left;margin-left:100px;">							<button class="cancel" onClick="$(\'.clone_pop_up\').css({display:\'none\'}); return false;"><span>Cancel</span></button>							<div style="clear: both;"></div>						</div>														</div>';							} */
								echo '<!--<button><span>archive project</span></button>	
								<div class="right_actions">					
								<button><span>go to project</span></button>	
								<button class="trash"><span>delete project</span></button>
								</div>-->			
								</div>			
								<!--==| END: Bucket |==-->			
								<!--==| START: Bucket |==-->	
									
								<div class="title_med_ct">			
							<label for="company_filter" style="">Company</label>
							<select name="company_filter" id="company_filter" onchange="getProjects()" style="">	
							<option value="-1" selected="selected">Select</option>'.CtDisplay::getCompanyHTML().'</select>	
							<label for="producer_filter" style="' .$hideStyle .'">Lead</label>		
							<select name="producer_filter" id="producer_filter" onchange="getProducerProjects()" style="' .$hideStyle .'">		
							<option value="0" selected="selected">All</option>'					.CtDisplay::getProducerHTML().'</select>					
							<div class="right_actions_buttons" style="' .$hideStyle .'">					
					<button id="totalBudgetID" class="monitors">
					<span>total budget: $' . number_format($totalBudget) .'</span></button>	
						<button id="totalActiveID" class="monitors"><span>total active projects: ' .$totalActiveProjects .'</span></button>			
						</div>			
						<div class="ct_search_title">	
							<label for="project_filter" style="">Search Project Title</label>		
					<input type="text" value="" name="project_filter" id="project_filter" onblur="filterProjects()" style="">	
							</div>				
							<label for="producer_filter" style="display:none;">Quarter</label>				
							<select name="quarter_filter" id="quarter_filter" class="small" onchange="getQuarterProjects()" style="display:none;">					
			          <option value="0" selected="selected">Annual</option>					<option value="1">Q1</option>	
									<option value="2">Q2</option>	
									<option value="3">Q3</option>				
									<option value="4">Q4</option>			
								</select>			
						<label for="approval_filter" style="' .$hideStyle .'">Project Status</label>	
							<select name="approval_filter" id="approval_filter" class="small" onchange="getApprovedProjects()" style="' .$hideStyle .'">			
				<option value="0" selected="selected">All</option>					' . CtDisplay::getProjectStatusEditHtml() . '			
								</select>				
							<!--<label for="group_filter" style="' .$hideStyle .'">Allocation Type</label>				
				<select name="group_filter" id="group_filter" class="medium" onchange="getGroupProjects()" style="' .$hideStyle .'">		
				<option value="0" selected="selected">All</option>					' . CtDisplay::getProjectGroupsEditHtml() . '				
				</select>-->		
			
					<label for="program_filter" style="' .$hideStyle .'">Program</label>				
				<select name="program_filter" id="program_filter" class="medium" onchange="getProgramProjects()" style="' .$hideStyle .'">		
				<option value="0" selected="selected">All</option><option value="99" >None</option>					' . CtDisplay::fullProgramListHTML() . '				
				</select>
					<!-- ==| START: view type Botton Divison#7927 |== -->

		          <div style="float: right; margin: 0pt; position: absolute; right: 12px; top: 37px;padding-right:11px;" id="list_view" >
		         	<img src="/_images/cal_dis_03.png" alt="Calendar view" title="Calendar view" width="23" height="23" style="padding-right:6px;" />
                           <a href="/controltower/index" title="List View"><img src="/_images/list_active.png" alt="List View" width="23" height="23" ></a>
		         </div>
		         <!-- ==| END: view type Botton Divison#7927 |== -->
					</div>	
			        <div class="message_cancel see_more_overlay popup_cal" style="width:3000px;height:1024px;position:fixed;background-color:#fffff;z-index:1.0;margin-top:-500px;margin-left:-630px;opacity: 0.3;filter:alpha(opacity=30);zoom:1.0;"></div>
			        <!--==| START: Bucket |==-->
					<div class="month_controller_container">
						<ul class="month_controller" style="margin-left:175px;text-transform:none;">
							<li class="month_arrows"><button class="month_arrows month_arrows_left"></button></li>
							<li class="wo_month_controller_display" style="text-align:center;"> </li>
							<li class="month_arrows"><button class="month_arrows month_arrows_right"></button></li>
						</ul>
					</div>
					
			        <!--==| START: Bucket |==-->
									<div class="resources_controller resources_controller_wide">
										<ul class="wo_days_container wo_days_container_wide">
											<li>Monday</li>
											<li>Tuesday</li>
											<li>Wednesday</li>
											<li>Thursday</li>
											<li>Friday</li>
											<li>Saturday</li>
											<li  style="background:none;">Sunday</li>
										</ul>
									</div>
															
								<!--=========== COLUMN BREAK ===========-->';
			        echo '<div class="popup_cal">
			        <h4>Change Calendar Display to <a href="#" onclick="$(\'.popup_cal\').css({display:\'none\'}); return false;"><img class="close" src="/_images/close_btn.gif" width="15" height="14" alt="close" /></a></h4>
			  			<p><span class="title"> Month </span><select name="mon" id="mon_cal">
			  					  '.$this->getMonth().'
								  </select>	
			  			<span class="title"> Year</span> <select name="year_cal" id="year_cal">
			  					  '.$this->getYear().'
								  </select>
			  			
			  			</p>
			  			<div class="duplicate_buttons">
				  			<button  class="cancel" onclick="$(\'.popup_cal\').css({display:\'none\'}); return false;">
				  			<span>Cancel</span>
				  			</button>
				  			<button id="update_cal" >
				  			<span>Update</span>
				  			</button>
			  			
			  			</div>
			        </div>';
			        echo '<div id="ct_calender_view" class="wo_calender_view"></div>
			        	<div  id="wo_dimmer_ajax_cal" class="wo_save_box" style="display:none;">
							<img alt="ajax-loader" src="/_images/ajax-loader.gif"/>
						</div>
						<br/>
						<div class="color_block" >
							<div class="color_status"><strong>STATUS</strong></div>
						 	
						 	
						 	<div class="color_blk" style="background:#FCFBF4;"></div>
						 	<div class="color_blk2" ><strong> Project Launch date</strong></div>
						</div>
			        ';
        }
        
        function getMonth(){
        	$output = "";
        	$monthText[1] = "January";
			$monthText[2] = "February";
			$monthText[3] = "March";
			$monthText[4] = "April";
			$monthText[5] = "May";
			$monthText[6] = "June";
			$monthText[7] = "July";
			$monthText[8] = "August";
			$monthText[9] = "September";
			$monthText[10] = "October";
			$monthText[11] = "November";
			$monthText[12] = "December";
        	foreach($monthText as $month_key => $month_val){
        		$output .= "<option value=$month_key>".$month_val."</option>";
        	}
        	 return $output;
        }
        
	 function getYear(){
        	$output = "";
        	$yearText = array();
        	$futureYear = date("Y")+9;
        	for($i=2009; $i<=$futureYear ; $i++){
        		$yearText[$i] = $i;
        	}
        	
        	
        	foreach($yearText as $year_key => $year_val){
        		$output .= "<option value=$year_key>".$year_val."</option>";
        	}
        	 return $output;
        }
}?>
