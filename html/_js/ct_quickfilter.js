//Project Name filter
function filterProjects() {
	displayActiveList();
	if (aOpen) {
		displayArchiveList();
	}
}

//Quick Filter Sort
var projectList = Array();
var archiveList = Array();
var currentSort;
var sortDir=0;
var archive_currentSort;
var archive_sortDir=0;
var aOpen = false;9
var selectedRow;
var aSelectedRow;
var projDetails; 

//var ajaxRequest;  // The variable that makes Ajax possible!

$(document).ready(function() {
	filters = $('#ct_loadFilter').val();
	if(filters != ""){
		$('#wo_dimmer_ajax').css({display:'block'});
		var filterValues = filters.split('~');
		$('#company_filter').val(filterValues[0]);
		$('#producer_filter').val(filterValues[1]);
		$('#quarter_filter').val(filterValues[2]);
		$('#approval_filter').val(filterValues[3]);
		$('#program_filter').val(filterValues[4])
		$("#company_filter option[@value=-1]").remove();
		jQuery.getJSON('/_ajaxphp/quickfilter_json.php?id='+filterValues[0]+'&producerId='+filterValues[1]+'&quarterID='+filterValues[2]+'&statusID='+filterValues[3]+'&ProgramID='+filterValues[4], function(json) {
			projectList = json;
			$('#producer_filter').val(filterValues[1]);
			sortDir = 1;
			setSort('project','active');
			$("#company_filter option[@value=-1]").remove();
			$('#wo_dimmer_ajax').css({display:'none'});
			$('#wo_dimmer_ajax').css({display:'none'});
			Set_Cookie( "lighthouse_ct_data", filterValues[0] + '~' + filterValues[1] + '~' + filterValues[2] + '~' + filterValues[3] + '~' + filterValues[4], "7", "/controltower", "", "");
		});
	}
/*	
	jQuery.getJSON('/_ajaxphp/quickfilter_json.php', function(json) {
		projectList = json;
		setSort('project','active');
	});
*/
});

function getProjects(){
	companyID = document.getElementById('company_filter').value;
	producerID = document.getElementById('producer_filter').value;
	$('#wo_dimmer_ajax').css({display:'block'});
	$("#active_project_list").html('');
	$('#quarter_filter').val('0');
	$('#approval_filter').val('0');
	$('#group_filter').val('0');
/*	$.ajax({
		type: "GET",
		url: "/_ajaxphp/company_status.php",
		data: "company_id=" + companyID + "&type=budjet",
		success: function(msg) {
			$('#totalBudgetID').html('<span>total budget: $' + msg + '</span>');
			totalBudget = msg;
		}
	});
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/company_status.php",
		data: "company_id=" + companyID + "&type=status",
		success: function(msg) {
			$('#totalActiveID').html('<span>total active projects: ' + msg + '</span>');
			totalActive = msg;
		}
	});
*/
	if (document.getElementById("archive_list").style.display == 'block') {
		$("#archive_list").hide("blind", { direction: "vertical" }, 500);
		$("#archive_link").addClass("expand_toggle_closed");
	}
	jQuery.getJSON('/_ajaxphp/quickfilter_json.php?id='+companyID, function(json) {
		projectList = json;
		sortDir = 1;
		setSort('project','active');
		$("#company_filter option[@value=-1]").remove();
		$('#wo_dimmer_ajax').css({display:'none'});
	});
	Set_Cookie( "lighthouse_ct_data", companyID + '~' + producerID + '~0~0~0', "7", "/controltower", "", "");
}
function getProducerProjects(){
	companyID = document.getElementById('company_filter').value;
	producerID = document.getElementById('producer_filter').value;
	quarterID = document.getElementById('quarter_filter').value;
	ProgramID = document.getElementById('program_filter').value;
	$('#wo_dimmer_ajax').css({display:'block'});
	$("#company_filter option[@value=-1]").remove();
	$('#quarter_filter').val('0');
	$('#approval_filter').val('0');
	$('#group_filter').val('0');
	if (document.getElementById("archive_list").style.display == 'block') {
		$("#archive_list").hide("blind", { direction: "vertical" }, 500);
		$("#archive_link").addClass("expand_toggle_closed");
	}
//	if(quarterID == '0'){
//		filterProjects();
//		$('#wo_dimmer_ajax').css({display:'none'});
//	}else{
//		if(companyID == 0 || companyID == -1){
//			query = 'producerId='+producerID;
//			companyID = 0;
//		}else{
//			query = 'id='+companyID;
//		}
//		jQuery.getJSON('/_ajaxphp/quickfilter_json.php?'+query, function(json) {
//			projectList = json;
//			sortDir = 1;
//			setSort('project','active');
//			$('#wo_dimmer_ajax').css({display:'none'});
//		});
//	}
		if(companyID == 0 || companyID == -1){
			query = 'producerId='+producerID;
			companyID = 0;
		}else{
			query = 'id='+companyID;
		}
		jQuery.getJSON('/_ajaxphp/quickfilter_json.php?'+query+'&ProgramID='+ProgramID, function(json) {
			projectList = json;
			sortDir = 1;
			setSort('project','active');
			$('#wo_dimmer_ajax').css({display:'none'});
		});
//	alert(companyID+"--"+producerID+"--"+quarterID);
	Set_Cookie( "lighthouse_ct_data", companyID + '~' + producerID + '~0~0~'+ProgramID, "7", "/controltower", "", "");
}
function getQuarterProjects(){
	companyID = document.getElementById('company_filter').value;
	producerID = document.getElementById('producer_filter').value;
	quarterID = document.getElementById('quarter_filter').value;
	statusID = document.getElementById('approval_filter').value;
	//groupID = document.getElementById('group_filter').value;
	ProgramID = document.getElementById('program_filter').value; 
	$('#wo_dimmer_ajax').css({display:'block'});
	$("#company_filter option[@value=-1]").remove();
	$('#approval_filter').val('0');
	if (document.getElementById("archive_list").style.display == 'block') {
		$("#archive_list").hide("blind", { direction: "vertical" }, 500);
		$("#archive_link").addClass("expand_toggle_closed");
	}
	var query = '?id='+companyID+'&quarterID='+quarterID+'&ProgramID='+ProgramID;
	jQuery.getJSON('/_ajaxphp/quickfilter_json.php'+query, function(json) {
			projectList = json;
			sortDir = 1;
			setSort('project','active');
			$('#wo_dimmer_ajax').css({display:'none'});
			$('#row_'+selectedRow).removeClass('active');
			$('#row_'+selectedRow+'_d').hide("blind", { direction: "vertical" }, 1000);
			selectedRow = null;
		});
	Set_Cookie( "lighthouse_ct_data", companyID + '~' + producerID + '~' + quarterID + '~0~0', "7", "/controltower", "", "");
}
function getGroupProjects(){
	companyID = document.getElementById('company_filter').value;
	producerID = document.getElementById('producer_filter').value;
	quarterID = document.getElementById('quarter_filter').value;
	statusID = document.getElementById('approval_filter').value;
	groupID = document.getElementById('group_filter').value;
	$('#wo_dimmer_ajax').css({display:'block'});
	$("#company_filter option[@value=-1]").remove();
	$('#approval_filter').val('0');
	if (document.getElementById("archive_list").style.display == 'block') {
		$("#archive_list").hide("blind", { direction: "vertical" }, 500);
		$("#archive_link").addClass("expand_toggle_closed");
	}
	var query = '?id='+companyID+'&groupID='+groupID+'&producerID='+producerID+'&quarterID='+quarterID+'&statusID='+statusID;
	jQuery.getJSON('/_ajaxphp/quickfilter_json.php'+query, function(json) {
			projectList = json;
			sortDir = 1;
			setSort('project','active');
			$('#wo_dimmer_ajax').css({display:'none'});
			$('#row_'+selectedRow).removeClass('active');
			$('#row_'+selectedRow+'_d').hide("blind", { direction: "vertical" }, 1000);
			selectedRow = null;
		});
	Set_Cookie( "lighthouse_ct_data", companyID + '~' + producerID + '~' + quarterID + '~0~0', "7", "/controltower", "", "");
}

function getProgramProjects(){
	companyID = document.getElementById('company_filter').value;
	producerID = document.getElementById('producer_filter').value;
	quarterID = document.getElementById('quarter_filter').value;
	statusID = document.getElementById('approval_filter').value;
	ProgramID = document.getElementById('program_filter').value;
	$('#wo_dimmer_ajax').css({display:'block'});
	$("#company_filter option[@value=-1]").remove();
	$('#approval_filter').val('0');
	if (document.getElementById("archive_list").style.display == 'block') {
		$("#archive_list").hide("blind", { direction: "vertical" }, 500);
		$("#archive_link").addClass("expand_toggle_closed");
	}
	var query = '?id='+companyID+'&ProgramID='+ProgramID+'&producerID='+producerID+'&quarterID='+quarterID+'&statusID='+statusID;
	jQuery.getJSON('/_ajaxphp/quickfilter_json.php'+query, function(json) {
			projectList = json;
			sortDir = 1;
			setSort('project','active');
			$('#wo_dimmer_ajax').css({display:'none'});
			$('#row_'+selectedRow).removeClass('active');
			$('#row_'+selectedRow+'_d').hide("blind", { direction: "vertical" }, 1000);
			selectedRow = null;
		});
	Set_Cookie( "lighthouse_ct_data", companyID + '~' + producerID + '~' + quarterID + '~' + statusID + '~' + ProgramID, "7", "/controltower", "", "");
}

function getApprovedProjects(){
	companyID = document.getElementById('company_filter').value;
	producerID = document.getElementById('producer_filter').value;
	quarterID = document.getElementById('quarter_filter').value;
	statusID = document.getElementById('approval_filter').value;
	//groupID = document.getElementById('group_filter').value;
	ProgramID = document.getElementById('program_filter').value; 
	$('#wo_dimmer_ajax').css({display:'block'});
	$("#company_filter option[@value=-1]").remove();
	if (document.getElementById("archive_list").style.display == 'block') {
		$("#archive_list").hide("blind", { direction: "vertical" }, 500);
		$("#archive_link").addClass("expand_toggle_closed");
	}
	var query = '?id='+companyID+'&quarterID='+quarterID+'&statusID='+statusID+'&ProgramID='+ProgramID;
	jQuery.getJSON('/_ajaxphp/quickfilter_json.php'+query, function(json) {
			projectList = json;
			sortDir = 1;
			setSort('project','active');
			$('#wo_dimmer_ajax').css({display:'none'});
			$('#row_'+selectedRow).removeClass('active');
			$('#row_'+selectedRow+'_d').hide("blind", { direction: "vertical" }, 1000);
			selectedRow = null;
		});
	Set_Cookie( "lighthouse_ct_data", companyID + '~' + producerID + '~' + quarterID + '~' + statusID + '~' + ProgramID, "7", "/controltower", "", "");
}

function duplicateProject(projID){
	$('.duplicate_pop_up').css({display:'block'});
	document.getElementById('hidden_id').value=projID;
}

function createProject() {
	$('.create_pop_up').css({display:'block'});
}

function cloneProject() {
	$('.clone_pop_up').css({display:'block'});
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/clone_projects.php",
		success: function(msg) {
			$('#clone_project_pop_up').html(msg);
		}
	});
}

function deleteProject(projID) {
	$('.message_delete').css({display:'none'});
	var tRemove = -1;
	
	for (i = 0; i < projectList.length; i++) {
		if (projectList[i]['id'] == projID) {
			tRemove = i;
		}
	}
	
	if (tRemove > -1) {
		budgetRemove = projectList[tRemove]['budget'];
		projectList.splice(tRemove, 1);
		displayActiveList();
	}
	else {
		for (i = 0; i < archiveList.length; i++) {
		
			if (archiveList[i]['id'] == projID) {
			
				tRemove = i;
				
			}
			
		}
		if (tRemove > -1) {
			budgetRemove = archiveList[tRemove]['budget'];
			archiveList.splice(tRemove, 1);
			displayArchiveList();
		}
	}
		
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/delete_project.php?project_id=" + projID,
		success: function(msg) {
			
		}
	});
	
	totalBudget = 0 + totalBudget - budgetRemove;
	totalActive = totalActive - 1;
	$('#totalActiveID').html('<span>total active projects: ' + totalActive + '</span>');
	$('#totalBudgetID').html('<span>total budget: $' + totalBudget + '</span>');
}

function deleteConfirm(projID) {
	$('.message_delete').css({display:'block'});
	document.getElementById('delete_project_confirm').value=projID;
}

function archiveConfirm(projID) {
	$('.message_archive').css({display:'block'});
	document.getElementById('archive_project_confirm').value=projID;
}

function archiveProject(projID) {
	$('.message_archive').css({display:'none'});

	selectedRow = -1;
    var tRemove = -1;

    for (i = 0; i < projectList.length; i++) {
        if (projectList[i]['id']==projID) {
            tRemove = i;
        }
    }
  

	var unarchive = 0;
	
    if (tRemove > -1) {
		totalBudget = totalBudget - projectList[tRemove]['budget'];
		totalActive = totalActive - 1;
		$('#totalActiveID').html('<span>total active projects: ' + totalActive + '</span>');
		$('#totalBudgetID').html('<span>total budget: $' + totalBudget + '</span>');
		//var cloneProject = getCloneProject(projectList[tRemove]['id']); 
		archiveList.push(projectList[tRemove]);
		
		projectList.splice(tRemove,1);
		//alert("projectList"+archiveList.toSource());
		displayActiveList();
		 $.ajax({
				type: "GET",
				url: "/_ajaxphp/archive_project.php?unarchive="+unarchive+"&&project_id="+projID,
				success: function(msg) {
					
				}
			});
    } else {
        for (i = 0; i < archiveList.length; i++) {
            if (archiveList[i]['id']==projID) {
                tRemove = i;
            }
        }
        if (tRemove > -1) {    
			unarchive = 1;
			totalBudget = totalBudget + Number(archiveList[tRemove]['budget']);
			totalActive = totalActive + 1;
			$('#totalActiveID').html('<span>total active projects: ' + totalActive + '</span>');
			$('#totalBudgetID').html('<span>total budget: $' + totalBudget + '</span>');
			$('#wo_dimmer_ajax').css({display:'block'});
			 $.ajax({
				 
					type: "GET",
					url: "/_ajaxphp/archive_project.php?unarchive="+unarchive+"&&project_id="+projID,
					success: function(clonPId) {
				 	if(projID != clonPId){
					 	companyID = document.getElementById('company_filter').value;
						producerID = document.getElementById('producer_filter').value;
						quarterID = document.getElementById('quarter_filter').value;
						statusID = document.getElementById('approval_filter').value;
						//groupID = document.getElementById('group_filter').value;
						ProgramID = document.getElementById('program_filter').value; 
						var query = '?id='+companyID+'&quarterID='+quarterID+'&statusID='+statusID+'&ProgramID='+ProgramID+'&projectID='+clonPId;
						jQuery.getJSON('/_ajaxphp/quickfilter_json.php'+query, function(json) {
							projectList = json;
									//archiveList.splice(tRemove,1);
									displayActiveList();
									var rowId = "row_"+clonPId; 
									$("#archive_project_list").find("#"+rowId).css("display","none");	
									//displayActiveList();
									$('#wo_dimmer_ajax').css({display:'none'});
								});
							}else{
					 			projectList.push(archiveList[tRemove]);
					 			archiveList.splice(tRemove,1);
								displayActiveList();
								displayArchiveList();
								$('#wo_dimmer_ajax').css({display:'none'});
								
					 		}
			 		}
				});
			
			
        } 
    }

	if (aOpen) {
		displayArchiveList();
	}
	
	 
}


function project_dup(un,pwd) {
	//alert('function called: '+projId);
	var comp = document.getElementById('create_company');
	var code = document.getElementById('projectCode');
	var name = document.getElementById('project_name');
	
	if(comp.value != '' && code.value != '' && name.value != '') {
		comp.disabled = true;
		code.disabled = true;
		name.disabled = true;
		
		var query = 'comp='+comp.value+'&code='+code.value+'&name='+name.value;
		
		ajaxFunction(query,'return');
	} else {
		alert('The projects company, code, and name are all required to continue');
	}
}

function project_create(un,pwd) {
	//alert('function called: '+projId);
	var comp = document.getElementById('create_company2');
	var code = document.getElementById('projectCode2');
	var name = document.getElementById('project_name2');
	
	if(comp.value != '' && code.value != '' && name.value != '') {
		comp.disabled = true;
		code.disabled = true;
		name.disabled = true;
		
		var query = 'comp='+comp.value+'&code='+code.value+'&name='+name.value;
		
		ajaxFunction(query,'return');
	} else {
		alert('The projects company, code, and name are all required to continue');
	}
}

//*************************
//**Show/Hide Active List**
//*************************
function toggleActiveList() {
	if (document.getElementById("active_list").style.display == 'none') {
		$("#active_link").removeClass("expand_toggle_closed");
		document.getElementById("active_list").style.display = 'block';
		$("#active_list").show("blind", { direction: "vertical" }, 500);
	} else {
		//document.getElementById("active_list").style.display = 'none';
		$("#active_list").hide("blind", { direction: "vertical" }, 500);
		$("#active_link").addClass("expand_toggle_closed");
	}
}

//**************************
//**Show/Hide Archive List**
//**************************
function loadArchiveList() {

	companyID = document.getElementById('company_filter').value;
	producerID = document.getElementById('producer_filter').value;
	statusID = document.getElementById('approval_filter').value;
	ProgramID = document.getElementById('program_filter').value;

	if (document.getElementById("archive_list").style.display == 'none') {
		$('#wo_dimmer_ajax').css({display:'block'});
		$("#archive_link").removeClass("expand_toggle_closed");

		if (aOpen == false) {
			aOpen = true;
		}
		jQuery.getJSON('/_ajaxphp/quickfilter_json.php?archive=1&id='+companyID+'&producerId='+producerID+'&statusID='+statusID+'&ProgramID='+ProgramID, function(json) {
			archiveList = json;
			setSort('project','acrhive');
			document.getElementById("archive_list").style.display = 'block';
			$("#archive_list").show("blind", { direction: "vertical" }, 500);
			$('#wo_dimmer_ajax').css({display:'none'});
		});
	
/*
		} else {	
			$("#archive_list").show("blind", { direction: "vertical" }, 500);	
		}
*/		
	} else {
		if (aOpen == true) {
			aOpen = false;
		}

		//document.getElementById("archive_list").style.display = 'none';
		$("#archive_list").hide("blind", { direction: "vertical" }, 500);
		$("#archive_link").addClass("expand_toggle_closed");
	}

}

//****************************************
//**Resort both active and archive lists**
//****************************************
function setSort(sortType, loc) {
	plist = "";
	
	if (loc == "active" ) {
		cSort = currentSort;
		tList = projectList;
		sID = "#"+cSort+"_sort";
	} else {
		cSort = archive_currentSort;
		sID = "#"+cSort+"_asort";
		tList = archiveList;
	}

	$(sID).removeClass("up");
	$(sID).removeClass("down");

	if (sortType == cSort) {
		sortDir = 1 - sortDir;
	} else {
		cSort = sortType;
		// TO make the sort to be decending by clicking on the risk column for the first time 
		if(sortType == 'risk')
			sortDir = 1;
		else
			sortDir = 0;
	}
	
	if (sortDir == 1) {
		aClass = "up";
		rClass = "down";
	} else {
		aClass = "down";
		rClass = "up";
	}
	
	if (loc == "active" ) {
		currentSort = cSort;
		sID = "#"+cSort+"_sort";
	} else {
		archive_currentSort = cSort;
		sID = "#"+cSort+"_asort";
	}
	
	switch(cSort) {
		case "project":
			tList.sort(sortByProject);
			break;
		case "complete":
			tList.sort(sortByComplete);
			break;
		case "budget":
			tList.sort(sortByBudget);
			break;
		case "todate":
			tList.sort(sortByTodate);
			break;
		case "risk":
			tList.sort(sortByRisk);
			break;
		case "status":
			tList.sort(sortByStatus);
			break;
		case "approval":
			tList.sort(sortByApproval);
			break;
		default:
	}

	if (sortDir == 1) {
		tList.reverse();
	}
	
	$(sID).removeClass(rClass);
	$(sID).addClass(aClass);
			

	if (loc == "active") {
		displayActiveList();
	} else {
		displayArchiveList();
	}

}

//********************************************
//**Set selected row and load project detail**
//********************************************
function selectRow(selRow) {
	newRow = "#row_"+selRow;
	oldR = "#row_"+selectedRow;
	oldRow = selectedRow;
	var tRemove = -1;
	var archived = 0;
	var quarterId = document.getElementById('quarter_filter').value;
	if (oldRow == selRow) {
		$(newRow).removeClass("active");
		//$(newRow + "_d").removeClass("active");
		$(oldR + "_d").hide("blind", { direction: "vertical" }, 1000);
		selRow = null;
	} else {
		$(oldR).removeClass("active");
		//$(oldR + "_d").removeClass("active");

		$(newRow).addClass("active");
		$(newRow + "_d").addClass("active");
		
	    for (i = 0; i < projectList.length; i++) {
	
	        if (projectList[i]['id']==selRow) {
	            tRemove = i;
	        }
	
	    }
		if (tRemove > -1) {
			archived = 0;
		} else {
			archived = 1;
		}

		getProjectDetails(selRow, newRow + "_d", archived,quarterId);

		$(oldR + "_d").hide("blind", { direction: "vertical" }, 1000);
	}
	
	selectedRow = selRow;
	
	

	
}

//**************************************
//**Request project detail from server**
//**************************************
function getProjectDetails(projId, ddId, archived,quarterId){	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/quickfilter_json_detail.php?archived=" + archived + "&&projID=" + projId+ "&&quarterId=" + quarterId,
		success: function(msg) {
			projDetails = eval( "(" + msg + ")" );
			$(ddId).html(projDetails);
			$(ddId).show("blind", { direction: "vertical" }, 1000);
		}
	});
}

//***************************************
//**Display active list from list array**
//***************************************
function displayActiveList() {
	plist = "";
	elem = document.getElementById("project_filter");
	fText = elem.value.toLowerCase();
	compId = document.getElementById("company_filter").value;
	leadId = document.getElementById("producer_filter").value;
	lead_array = leadId.split("_");
	producerId = lead_array[1];
	filter_role="";
	if(lead_array[0] == 2){
		filter_role = "producer_userid";
	}else if(lead_array[0] == 3){
		filter_role = "manager_userid";
	}

	var producerBudjet = 0;
	var producerProjCount = 0;
	var allBudjet = 0;
	var allProjCount = 0;
	//Add Updated ProjectList	
	//projectList = updateProjectList(); 
	tList = projectList;
	for (var i=0;i<tList.length;i++) {
		proj = tList[i];
//		pName = proj["code"] + " - " + proj["name"];
		pName = proj["name"];
		pFullName = proj["full_name"];
		if (pFullName.toLowerCase().match(fText) != null) {
			var reg = new RegExp(fText, "i");
			var splits = pName.search_split(reg,2);   

			pTemp = splits[0] + "<span style=\"color: red\">" + pName.substr(splits[0].length, fText.length) + "</span>" + pName.substr(splits[0].length + fText.length, pName.length - (splits[0].length + fText.length)) ;
			pName = pTemp;
			
			if (((compId>0) && (proj["company"] == compId)) || (compId == 0) || (compId == -1)) {
				if(proj['risk']["riskCount"] == '0'){
					pRisk = '';
					pRiskDetails = '<dd class="" style="display:none;"></dd>';
				}else{
					pRisk = "<li class=\"risk_count\" id=\"risk_count_" + proj["id"] + "\" onmouseover=\"showRisk('" + proj["id"] + "');\" onmouseout=\"hideRisk('" + proj["id"] + "');\"><img src=\"/_images/flag_icon_red.png\"><span>"+proj['risk']["riskCount"]+"</span></li>";
					if(proj['risk']["riskCount"] == '1'){
						pNextRisk = '&nbsp;';
					}else{
						pNextRisk = '<a href="javascript:getProjectRisks(\'' + proj["id"] + '\', \'2\');">Next</a>';
					}
					pRiskDetails = '<dd class="project_risk" id="row_' + proj["id"] + '_r" style="display:none;" onmouseover="showRisk(\'' + proj["id"] + '\');" onmouseout="hideRisk(\'' + proj["id"] + '\');"><div class="risk_content"><p class="risk_title">' + proj['risk']["title"] + '</p><p class="risk_assigned"> Assigned to: <span>' + proj['risk']["assigned"] + '</span></p><p class="risk_desc">' + proj['risk']["desc"] + '</p><p class="risk_paginator"><span class="prev">&nbsp;</span><span class="present">1 of ' + proj['risk']["riskCount"] + '</span><span class="next">' + pNextRisk + '</span></p></div></dd>';
				}
				pwidth = proj["complete"] > 100 ? 130 : (proj["complete"]/ 100) * 130;
				if(proj["approved"] == '1'){
					str_approved = "<img src=\"/_images/green_status.gif\"/>";
				}else{
					str_approved = "";
				}
				project_status = proj["status_name"];
				project_status = project_status.toLowerCase().replace(" ", "");
				if((leadId != '0')){
					//alert("insdide producer if ");
					if((proj[filter_role] == producerId)){
						if (proj["id"] == selectedRow) {
							plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\" class=\"active\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"status\"><div class=\"" + project_status + "\" title=\""+proj["status_name"]+"\"/></li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress " + proj["progress_class"] + "\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li>" + pRisk + "</ul></dt>";
							plist = plist + '		<dd id="row_' + proj["id"] +'_d" class=\"active\">';
							plist = plist + projDetails;
						} else {
							plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"status\"><div class=\"" + project_status + "\" title=\""+proj["status_name"]+"\"/></li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress " + proj["progress_class"] + "\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li>" + pRisk + "</ul></dt>";
							plist = plist + '		<dd id="row_' + proj["id"] +'_d">';
						}
						producerBudjet += parseInt(parseFloat(proj["budget"]));
						producerProjCount++;
					}
				}else{
					if (proj["id"] == selectedRow) {
						plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\" class=\"active\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"status\"><div class=\"" + project_status + "\" title=\""+proj["status_name"]+"\"/></li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress " + proj["progress_class"] + "\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li>" + pRisk + "</ul></dt>";
						plist = plist + '		<dd id="row_' + proj["id"] +'_d" class=\"active\">';
						plist = plist + projDetails;
					} else {
						plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"status\"><div class=\"" + project_status + "\" title=\""+proj["status_name"]+"\"/></li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress " + proj["progress_class"] + "\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li>" + pRisk + "</ul></dt>";
						plist = plist + '		<dd id="row_' + proj["id"] +'_d">';
					}
					allBudjet += parseInt(parseFloat(proj["budget"]));
					allProjCount++;
				}

				plist = plist + '		</dd>';
				plist = plist + pRiskDetails;
			}
		}
	}
	if(leadId != '0'){
		$('#totalBudgetID').html('<span>total budget: $' + addCommas(producerBudjet) + '</span>');
		$('#totalActiveID').html('<span>total active projects: ' + producerProjCount + '</span>');
		totalBudget = producerBudjet;
		totalActive = producerProjCount;
	}else{
		$('#totalBudgetID').html('<span>total budget: $' + addCommas(allBudjet) + '</span>');
		$('#totalActiveID').html('<span>total active projects: ' + allProjCount + '</span>');
		totalBudget = allBudjet;
		totalActive = allProjCount;
	}
	$("#active_project_list").html(plist);
	
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
//****************************************
//**Display archive list from list array**
//****************************************		
function displayArchiveList() {
	plist = "";
	elem = document.getElementById("project_filter");
	fText = elem.value.toLowerCase();
	compId = document.getElementById("company_filter").value;
	//loadArchiveList();
	tList = archiveList;
	leadId = document.getElementById("producer_filter").value;
	lead_array = leadId.split("_");
	producerId = lead_array[1];
	filter_role="";
	if(lead_array[0] == 2){
		filter_role = "producer_userid";
	}else if(lead_array[0] == 3){
		filter_role = "manager_userid";
	}
		for (var i=0;i<tList.length;i++) {
		proj = tList[i];
//		pName = proj["code"] + " - " + proj["name"];
		pName = proj["name"];
		pFullName = proj["full_name"];
		pStatusName = proj["status_name"].toLowerCase();

		if (pName.toLowerCase().match(fText) != null) {
            var reg = new RegExp(fText, "i");
            var splits = pName.search_split(reg,2);   
            
            pTemp = splits[0] + "<span style=\"color: red\">" + pName.substr(splits[0].length, fText.length) + "</span>" + pName.substr(splits[0].length + fText.length, pName.length - (splits[0].length + fText.length)) ;
            pName = pTemp;
            
			if (((compId>0) && (proj["company"] == compId)) || (compId == 0)) {
				pwidth = proj["complete"] > 100 ? 130 : (proj["complete"]/ 100) * 130;
				if((leadId != '0')){
					if((proj["producer_userid"] == producerId)){
						if (proj["id"] == selectedRow) {
							plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\" class=\"active\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li><li class=\"button\"><!--<button class=\"secondary\"><span>create work order</span></button>--></li></ul></dt>";
							plist = plist + '		<dd id="row_' + proj["id"] +'_d" class=\"active\">';
					
							plist = plist + projDetails;
						} else {
							plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li><li class=\"button\"><!--<button class=\"secondary\"><span>create work order</span></button>--></li></ul></dt>";
							plist = plist + '		<dd id="row_' + proj["id"] +'_d">';
						}
					}
				}else{
					if (proj["id"] == selectedRow) {
						plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\" class=\"active\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li><li class=\"button\"><!--<button class=\"secondary\"><span>create work order</span></button>--></li></ul></dt>";
						plist = plist + '		<dd id="row_' + proj["id"] +'_d" class=\"active\">';
				
						plist = plist + projDetails;
					} else {
						plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\"><ul><li class=\"project\" title='" + pFullName + "'>" + pName + "</li><li class=\"status\" title='" + pStatusName + "'><div class='"+pStatusName+"'/></li><li class=\"actual\">$" + addCommas(parseFloat(proj["todate"]).toFixed(0)) + "</li><li class=\"budget\"> $" + addCommas(parseFloat(proj["budget"]).toFixed(0)) + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li><li class=\"button\"><!--<button class=\"secondary\"><span>create work order</span></button>--></li></ul></dt>";
						plist = plist + '		<dd id="row_' + proj["id"] +'_d">';
					}

				}


				plist = plist + '		</dd>';
			}
		}
	}

	$("#archive_project_list").html(plist);
}

//*************************
//**Custom sort functions**
//*************************	
function sortByProject(a, b) {
    var x = a["code"].toLowerCase();
    var y = b["code"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByComplete(a, b) {
    var x = a["complete"];
    var y = b["complete"];
    return (x-y);
}

function sortByTodate(a, b) {
    var x = a["todate"];
    var y = b["todate"];

   return (x-y);
}

function sortByBudget(a, b) {
    var x = a["budget"];
    var y = b["budget"];
   return (x-y);
}

function sortByRisk(a, b) {
    var x = a["risk"]["riskCount"];
    var y = b["risk"]["riskCount"];
   return (x-y);
}

function sortByApproval(a, b) {
    var x = -1 * (a["approved"] - 1);
    var y = -1 * (b["approved"] - 1);
   return (x-y);
}

function sortByStatus(a, b) {
    var x = a["status"];
    var y = b["status"];
   return (x-y);
}
//******************
//**Project Risk ***
//******************
function showRisk(projId){
	var topVar = ($('#risk_count_'+projId).offset().top - 322);
	$('#row_'+projId+'_r').css({display:'block', top: topVar + 'px'});
}
function hideRisk(projId){
	$('#row_'+projId+'_r').css({display:'none'});
}
function getProjectRisks(projId, page){
	$('#row_'+projId+'_r .risk_content').fadeOut("slow");
	$.ajax({
			type: "GET",
			url: "/_ajaxphp/get_project_risk.php",
			data: "projId=" +projId+"&page="+page+"&"+Math.round(Math.random()*1000),
			success: function(msg) {
				$('#row_'+projId+'_r .risk_content').html(msg);
				$('#row_'+projId+'_r .risk_content').fadeIn("slow");
			}
		});
}
//3955#3954
function createproject_new(){
	

	/*	if($.trim($('#create_company2').val()) == ''){
		alert("Please select company name");
		return false;
	}
	if($.trim($('#projectCode2').val()) == ''){
		alert("Please enter project code");
		return false;
	}
	if($.trim($('#project_name2').val()) == ''){
		alert("Please enter project name");
		return false;
	}*/
if($.trim($('#create_company2').val()) == ''){
		alert("Please select company name");
		return false;
	}else	if($.trim($('#projectCode2').val()) == ''){
		alert("Please enter project code");
		return false;
	}else if($.trim($('#project_name2').val()) == ''){
		alert("Please enter project name");
		return false;
	}else{
		$('#create_project').submit();
	}
	
	
}//End

function generateallocationreport(){
	window.open('/_ajaxphp/allocationreport.php');
}

