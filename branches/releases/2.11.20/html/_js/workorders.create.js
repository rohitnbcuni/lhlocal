$(document).ready(function() {
	if(document.getElementById('start_date').value == "") {
		document.getElementById('start_date').value = getCurrentDate();
	}

	if(document.getElementById('start_date').value != "") {
		var wo_id = document.getElementById('workorder_id').value;
        if(wo_id !== '')
        {
           // updateEstimatedDate();
        }    
	}
	
	if($('#wo_requested_by').val()!='')
	{
		changeImage($('#wo_requested_by').val());
	}
		
	if($('#copyWO').val()!='')
	{
		getRequestType($('#REQ_TYPE').val());

		if($('#REQ_TYPE').val() == RT_PROBLEM ){
			severityChange($('#SEVERITY').val());
		}
	}

	if("932" == $('#wo_assigned_user').val()){
		$("li.rally").css({display:'block'});
	}
	
	$('#wo_assigned_user').change(function () {
		if("932" == $('#wo_assigned_user').val()){
			$("li.rally").css({display:'block'});
		}else{
			$("li.rally").css({display:'none'});
		}
	});
	updateComments();
	updateFileList();
	$('#save_buttons_dimmer').css({display:'none'});
	$('#wo_status , #wo_assigned_user').change(function() {
		$('#prompt_save').val(2);
	});
	$(document).unbind('keypress');
	$('input:text , textarea').keydown(function(event) {
		$('#prompt_save').val(2);
	});
	//document.getElementById('workorder_id').value;
	if($('#workorder_id').val() != ''){
		setInterval("showNewComment()", 5000);
		
	}
/*------ ------Lazy Load Rquestor and Project drop down------------*/
	var workorder_id = $('#workorder_id').val();
	var woRequestedByPrev = $('#woRequestedByPrev').val();
	$.ajax({
	type: "POST",
	url:"/workorders/index/requestorselect", 
	data:{wid:workorder_id,woRequestedByPrev:woRequestedByPrev},
	success: function(data){
		$('#requestor_loader').css('display','none');
		$('#wo_requested_by').html(data);
		//changeImage($('#wo_requested_by').val());
		changeImage($('#wo_requested_by').val());
		getRequestorsInfo($('#wo_requested_by').val());
		//$('#wo_requested_by').addClass('chzn-select');
		$('#requestor_loader_field').css('display','block');
		
		}
	});
	var copyWO = $('#copyWO').val();
	var hidden_projecd_id = $('#hidden_projecd_id').val();
	$.ajax({
	type: "POST",
	url:"/workorders/index/projectselect", 
	data:{wid:workorder_id,project_id:hidden_projecd_id,copyWO:copyWO},
	success: function(data){
		$('#project_loader').css('display','none');
		
		$('#wo_project').html(data);
		changeImage($('#wo_requested_by').val());
		getRequestorsInfo($('#wo_requested_by').val());
		//$('#wo_requested_by').addClass('chzn-select');
		$('#project_loader_field').css('display','block');
		
		}
	});
/*------ ------Lazy Load------------*/


	$("#related_issues_txt").ForceNumericOnly();
	$('#related_issues_txt').on("focus blur keypress keyup", function () {
	//$("#related_issues_txt").keypress(function(){
		var issue_type = $('input[name=issuse_types]:checked').val();
		var related_issues_txt = $('#related_issues_txt').val();
		//var issue_type = $('input[name=issuse_types]:checked').val();
		if(issue_type != '' && related_issues_txt != ''){
			$.ajax({
			type: "POST",
			async: true,
			cache: false,
			url:"/workorders/index/issuechecker", 
			data:{issue_type:issue_type,related_issues_id:related_issues_txt},
			success: function(val){
				var v = $.trim(val);
				
				if(val == '0'){
					if(issue_type == 'WO'){
					$('#issues_error').html("Workorder ID not found");
					}else if(issue_type == 'DF'){
					$('#issues_error').html("Defect ID not found");
					} 
					$('#issue_link').css("display","none");
					}else{
					$('#issues_error').html('');
					$('#issue_link').css("display","block");
					}
				
				}
			});
		}
	
	});
	
	
	
});
jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

function showHideTime() {
	//var checkBox = document.getElementById('time_sensitive');
	
	//if(checkBox.checked) {
	//	document.getElementById('wo_time_fade').style.display = "none";
//	} else {
//		document.getElementById('wo_time_fade').style.display = "block";
//		document.getElementById('wo_time_fade').style.backgroundColor = "#FFFFFF";
//		document.getElementById('wo_time_fade').style.opacity = '0.7';
//		document.getElementById('wo_time_fade').style.filter = 'alpha(opacity=70)';
//	}
}

function copyRequest(workorderID)
{
	var form = document.createElement("form");
    form.setAttribute("method", 'post');
    form.setAttribute("action", '/workorders/index/create/');

	 var hiddenField = document.createElement("input");
     hiddenField.setAttribute("type", "hidden");
     hiddenField.setAttribute("name", 'copyWO');
     hiddenField.setAttribute("value", workorderID);
     form.appendChild(hiddenField);

	 document.body.appendChild(form);   
     form.submit();

}

function changeImage(userID)
{
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/getUserImage.php",
		data: "userID="+userID,
		success: function(msg) {
			$('#requestor_photo').attr('src',msg);
		}
	});
}

function getRequestType(reqType) {
	if(reqType!='_blank')
	{
		$('#pageLoadHide').css({display:'block'});

		$('#wo_save').unbind('click');
		$('#wo_save').removeAttr('onclick','');
		$('#wo_save').bind('click', function() {
			 saveWorkOrder();
			 return false;
		});

		if(reqType==RT_OUTAGE)
		{
			$('.request_type_msg').css({display:'block'});
			$('#li_SEVERITY').css({display:'none'});
			$('#li_REQ_DATE').css({display:'none'});
			$('#li_INFRA_TYPE').css({display:'none'});
			$('#li_CRITICAL').css({display:'none'});
			$('#li_DRAFT').css({display:'none'});
//			document.getElementById("wo_assigned_user").options[0].value = SUPPORT_TEAM_ITOC_ID; 
//			document.getElementById("wo_assigned_user").options[0].text = SUPPORT_TEAM_ITOC_NAME;
			buildAssignedToList(WO_CREATE_OUTAGE);
			document.getElementById("SEVERITY").options[0].value = 'disable'; 
			document.getElementById("INFRA_TYPE").options[0].value = 'disable';
	
			$('#wo_save').unbind('click');
			$('#wo_save').removeAttr('onclick','');
			$('#wo_save').bind('click', function() {
				 saveWorkOrderConfirm();
				 return false;
			});
 
			severityChange(RT_OUTAGE);
		}
		else if(reqType==RT_PROBLEM)
		{
			$('#li_SEVERITY').css({display:'block'});
			$('#li_REQ_DATE').css({display:'block'});
			$('#li_INFRA_TYPE').css({display:'none'});
			$('#li_CRITICAL').css({display:'none'});
			$('#li_DRAFT').css({display:'none'});
			$('#wo_time_fade').css({display:'block'});
//			document.getElementById("wo_assigned_user").options[0].value = SUPPORT_TEAM_ID; 
//			document.getElementById("wo_assigned_user").options[0].text = SUPPORT_TEAM_NAME;
			buildAssignedToList(WO_CREATE_PROBLEM);
			document.getElementById("INFRA_TYPE").options[0].value = 'disable'; 
		}
		else if(reqType==RT_CHANGE)
		{
			$('#li_SEVERITY').css({display:'none'});
			$('#wo_time_fade').css({display:'none'});
			$('#li_REQ_DATE').css({display:'block'});
			$('#li_INFRA_TYPE').css({display:'block'});
			$('#li_CRITICAL').css({display:'block'});
			$('#li_DRAFT').css({display:'block'});
			$('#currentMinute').val('00');
//			document.getElementById("wo_assigned_user").options[0].value = MAINTENANCE_TEAM_ID; 
//			document.getElementById("wo_assigned_user").options[0].text = MAINTENANCE_TEAM_NAME;
			buildAssignedToList(WO_CREATE_CHANGE);
			document.getElementById("SEVERITY").options[0].value = 'disable'; 
		}
		else 
		{
			$('#li_SEVERITY').css({display:'block'});
			$('#li_REQ_DATE').css({display:'block'});
			$('#li_INFRA_TYPE').css({display:'block'});
		}
	}
}
function getRequestTypeNew(reqType) {
	if(reqType!='_blank')
	{
		$('#pageLoadHide').css({display:'block'});

		$('#wo_save').unbind('click');
		$('#wo_save').removeAttr('onclick','');
		$('#wo_save').bind('click', function() {
			 saveWorkOrder();
			 return false;
		});
		
		
		
		if(reqType==RT_OUTAGE)
		{
			$('.request_type_msg').css({display:'block'});
			$('#li_SEVERITY').css({display:'none'});
			$('#li_REQ_DATE').css({display:'none'});
			$('#li_INFRA_TYPE').css({display:'none'});
			$('#li_CRITICAL').css({display:'none'});
			$('#li_DRAFT').css({display:'none'});
//			document.getElementById("wo_assigned_user").options[0].value = SUPPORT_TEAM_ITOC_ID; 
//			document.getElementById("wo_assigned_user").options[0].text = SUPPORT_TEAM_ITOC_NAME;
			buildAssignedToList(WO_CREATE_OUTAGE);
			document.getElementById("SEVERITY").options[0].value = 'disable'; 
			document.getElementById("INFRA_TYPE").options[0].value = 'disable';
	
			$('#wo_save').unbind('click');
			$('#wo_save').removeAttr('onclick','');
			$('#wo_save').bind('click', function() {
				 saveWorkOrderConfirm();
				 return false;
			});
 
			severityChange(RT_OUTAGE);
		}
		else if(reqType==RT_PROBLEM)
		{
			$('#estimated_completion_date').val('');
			$('#time_sensitive_date').val('');
			$('#time_sensitive_time').val('');
			$('#ampm').val('');
			$('#li_SEVERITY').css({display:'block'});
			$('#li_REQ_DATE').css({display:'block'});
			$('#li_INFRA_TYPE').css({display:'none'});
			$('#li_CRITICAL').css({display:'none'});
			$('#li_DRAFT').css({display:'none'});
			$('#wo_time_fade').css({display:'block'});
//			document.getElementById("wo_assigned_user").options[0].value = SUPPORT_TEAM_ID; 
//			document.getElementById("wo_assigned_user").options[0].text = SUPPORT_TEAM_NAME;
			buildAssignedToList(WO_CREATE_PROBLEM);
			document.getElementById("INFRA_TYPE").options[0].value = 'disable'; 
		}
		else if(reqType==RT_CHANGE)
		{
			$('#estimated_completion_date').val('');
			$('#time_sensitive_date').val('');
			$('#time_sensitive_time option:selected').removeAttr('selected');
			$('#time_sensitive_time option').each(function() {
				  $(this).prevAll('option[value="' + this.value + '"]').remove();
				});
			//set by default 05:00 PM
			$('#time_sensitive_time').val('05:00 PM');
			$('#ampm').val('');
			$('#li_SEVERITY').css({display:'none'});
			$('#wo_time_fade').css({display:'none'});
			$('#li_REQ_DATE').css({display:'block'});
			$('#li_INFRA_TYPE').css({display:'block'});
			$('#li_CRITICAL').css({display:'block'});
			$('#li_DRAFT').css({display:'block'});
			$('#currentMinute').val('00');
//			document.getElementById("wo_assigned_user").options[0].value = MAINTENANCE_TEAM_ID; 
//			document.getElementById("wo_assigned_user").options[0].text = MAINTENANCE_TEAM_NAME;
			buildAssignedToList(WO_CREATE_CHANGE);
			document.getElementById("SEVERITY").options[0].value = 'disable'; 
		}
		else 
		{
			$('#li_SEVERITY').css({display:'block'});
			$('#li_REQ_DATE').css({display:'block'});
			$('#li_INFRA_TYPE').css({display:'block'});
		}
	}
}
function buildAssignedToList(jsonString){
	var option = '';
	if($("#workorder_id").val() == ''){
		var json = eval(jsonString);
		for (var i = 0; i < json.length; i++){
			option += '<option value="' +json[i]['id']+ '" ' +json[i]['selected']+ '>' +json[i]['name']+ '</option>';
		}
		$('#wo_assigned_user').html(option);
	}
}

function severityChange(severity) {
	if(severity!='_blank')
	{
		var currDateTime = document.getElementById('currentDateTime').value;
		if(currDateTime!='')
		{
			var splitcurrDateTime = currDateTime.split(':');
			var currDate = splitcurrDateTime[0];
			var currTime = splitcurrDateTime[1];
			var splitcurrDate = currDate.split('-');
			var splitcurrTime = currTime.split('-');			
           
		   $('#currentMinute').val(splitcurrTime[1]);
			var launchDate ='';

			if(severity==SEVERITY1) {
				$('.severity1_msg').css({display:'block'});
				launchDate=new Date(parseInt(splitcurrDate[0],10),parseInt(splitcurrDate[1],10)-1,parseInt(splitcurrDate[2],10),parseInt(splitcurrTime[0],10)+4,parseInt(splitcurrTime[1],10));
			}
			else if (severity==SEVERITY2) {
				$('.severity2_msg').css({display:'block'});
				launchDate=new Date(parseInt(splitcurrDate[0],10),parseInt(splitcurrDate[1],10)-1,parseInt(splitcurrDate[2],10)+2,parseInt(splitcurrTime[0],10),parseInt(splitcurrTime[1],10));
			}
			else if (severity==SEVERITY3) {
				$('.severity3_msg').css({display:'block'});
				launchDate=new Date(parseInt(splitcurrDate[0],10),parseInt(splitcurrDate[1],10)+5,parseInt(splitcurrDate[2],10),parseInt(splitcurrTime[0],10),parseInt(splitcurrTime[1],10));
			}
			else if(severity==RT_OUTAGE)
			{
				launchDate=new Date(parseInt(splitcurrDate[0],10),parseInt(splitcurrDate[1],10)-1,parseInt(splitcurrDate[2],10),parseInt(splitcurrTime[0],10)+2,parseInt(splitcurrTime[1],10));
			}		

			var launchDay = launchDate.getDate();
			var launchMonth = parseInt(launchDate.getMonth(),10)+1;
			if(launchMonth<10)
			{
				launchMonth = "0"+launchMonth;
			}
			if(launchDay<10)
			{
				launchDay = "0"+launchDay;
			}
			
			document.getElementById('time_sensitive_date').value = launchMonth +"/"+launchDay+"/"+launchDate.getFullYear();

			var Hrs12Frt = ((launchDate.getHours() % 12 || 12) < 10 ? '0' : '') + (launchDate.getHours() % 12 || 12); 
			
			if(parseInt(launchDate.getHours(),10)>11)
			{	var am_pm_val = "PM";
			
				document.getElementById('ampm').value =  'pm';
			}
			else
			{	var am_pm_val = "AM";
				document.getElementById('ampm').value =  'am';
			}
			
			var full_time = Hrs12Frt+":"+splitcurrTime[1]+" "+am_pm_val; 
			$('#time_sensitive_time option:selected').removeAttr('selected');
			
			
			$('#time_sensitive_time').append('<option selected="selected" value="'+full_time+'">'+full_time+'</option>');
			
		//	document.getElementById('time_sensitive_time').value =  Hrs12Frt;
			//document.getElementById('estimated_completion_date').value =  document.getElementById('time_sensitive_date').value+" "+$("#time_sensitive_time").val()+":"+splitcurrTime[1]+" "+($("#ampm").val()).toUpperCase();			
	
			var wo_ser = '';
			if($('#woesd')){
				wo_ser = $('#woesd').val();
				}
				if((wo_ser == 'undefined')||(!wo_ser)){
				
			//document.getElementById('estimated_completion_date').value =  document.getElementById('time_sensitive_date').value+" "+$("#time_sensitive_time").val()+":"+splitcurrTime[1]+" "+($("#ampm").val()).toUpperCase();			
			document.getElementById('estimated_completion_date').value =  document.getElementById('time_sensitive_date').value+" "+full_time;		
		}

	}
	}
}


function getRequestorsInfo(userId) {
	qString = '?action=entry&data=phone&user='+userId;
	qString2 = '?action=entry&data=email&user='+userId;
	qString3 = '?action=entry&data=name&user='+userId;
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString,
		success: function(msg) {
			document.getElementById('requestor_phone').value = msg;
		}
	});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString2,
		success: function(msg) {
			document.getElementById('requestor_email').value = msg;
		}
	});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString3,
		success: function(msg) {
			document.getElementById('requestor_name').value = msg;
		}
	});
	
	changeImage(userId);
}
function updateCcList() {
	var wo_id = document.getElementById('workorder_id').value;
	var qString = "?wo_id="+wo_id;
	
	//alert(qString);
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_cc_list.php"+qString,
		success: function(msg) {
			document.getElementById('cc_list').innerHTML = msg;
		}
	});
}

function saveWorkOrderConfirm()
{
	$('.message_outage_submit').css({display:'block'});
}
function getCurrentDate() {
	var currentDate = new Date();
	
	var month = currentDate.getMonth()+1;
	var day = currentDate.getDate();
	var year = currentDate.getFullYear();
	
	var dt = month+'/'+day+'/'+year;
	
	return dt;
}
function checkDateRange(startId, endId) {
	//alert('check date rage'+endId);

	var start = document.getElementById(startId).value;
	var end = document.getElementById(endId).value;
	var est = document.getElementById('estimated_completion_date');
	
	start_bk = start.split('/');
	end_bk = end.split('/');

	if(parseInt(start_bk[2],10) <= parseInt(end_bk[2],10)) {

		if(parseInt(start_bk[2],10) == parseInt(end_bk[2],10)){

		document.getElementById(endId).style.color = "#000000";
		document.getElementById(est.id).style.color = "#555555";
		if(parseInt(start_bk[0],10) <= parseInt(end_bk[0],10)) {

			document.getElementById(endId).style.color = "#000000";
			document.getElementById(est.id).style.color = "#555555";
		if(parseInt(start_bk[0],10) == parseInt(end_bk[0],10)) {
				if(parseInt(start_bk[1],10) <= parseInt(end_bk[1],10)) {
					document.getElementById(endId).style.color = "#000000";
					document.getElementById(est.id).style.color = "#555555";
				} else {

	//				document.getElementById(endId).style.color = "#FF0000";
					document.getElementById(est.id).style.color = "#FF0000";
				}
			}
		} else {
//			document.getElementById(endId).style.color = "#FF0000";
			document.getElementById(est.id).style.color = "#FF0000";
		}
	}else {
		document.getElementById(est.id).style.color = "#555555";
	}
	} else {
//		document.getElementById(endId).style.color = "#FF0000";
		document.getElementById(est.id).style.color = "#FF0000";
	}
}
function dateExists(date, month, year){
    var d = new Date(year, month, date);
    return d.getDate() === parseInt(date); //parseInt makes sure it's an integer.
}
function updateEstimatedDate() {
	var startDate = document.getElementById('start_date');
	var estDate = document.getElementById('estimated_completion_date');
	var priority = document.getElementById('wo_priority');
	//var timeSens = document.getElementById('time_sensitive');
	var launchDate = document.getElementById('time_sensitive_date');
	
	var currentDate = new Date();
	//alert($("#time_sensitive_time").val());	
	//if(timeSens.checked) {

		//estDate.value = launchDate.value+" "+$("#time_sensitive_time").val()+":"+$('#currentMinute').val()+" "+($("#ampm").val()).toUpperCase();
		estDate.value = launchDate.value+" "+$("#time_sensitive_time").val();
		checkDateRange(startDate.id, launchDate.id);

}
function saveWorkOrder(from) {
	var qString = "";
	var valid = true;

	//wo_dimmer
	//wo_dimmer_ajax
	var woId = document.getElementById('workorder_id').value;
	var dirName = document.getElementById('dirName').value;
	var requestedId = document.getElementById('wo_requested_by').value;
	var projectId = document.getElementById('wo_project').value;
	/*var woTypeId = document.getElementById('wo_type').value;*/
	var woTypeId = "";
	var priorityId = '';//document.getElementById('wo_priority').value;
	var timeSens = "true";//document.getElementById('time_sensitive').checked;
	var timeSensDate = document.getElementById('time_sensitive_date').value;
	var timeSensTime = document.getElementById('time_sensitive_time').value;
	var ampm = document.getElementById('ampm').value;
	var currmin=document.getElementById('currentMinute').value;
	var wo_draft = document.getElementById('WO_DRAFT').checked;
	var timeSensDate_draft = document.getElementById('draft_date').value;
	var timeSensTime_draft = document.getElementById('time_sensitive_time_draft').value;
	var ampm_draft = document.getElementById('ampm_draft').value;
	var woTitle = document.getElementById('wo_title').value;
	var woExampleURL = document.getElementById('wo_example_url').value;
	var woDesc = document.getElementById('wo_desc').value;
	var woAssignedTo = document.getElementById('wo_assigned_user').value;
	var woStatus = document.getElementById('wo_status').value;
	var woStartDate = document.getElementById('start_date').value;
	var woEstDate = document.getElementById('estimated_completion_date').value;

	var woREQ_TYPE = document.getElementById('REQ_TYPE').value;
	var woSEVERITY = document.getElementById('SEVERITY').value;
	var woSITE_NAME = document.getElementById('SITE_NAME').value;
	var woINFRA_TYPE = document.getElementById('INFRA_TYPE').value;
	var woCCList = document.getElementById('cclist').value;
	var woStatusIdHidden = document.getElementById('woStatusIdHidden').value;
	
	var completed_by = $('#completed_by').val();
	var wo_related_ids = $('#wo_related_ids').val();
	var df_related_ids = $('#df_related_ids').val();
	
	/*################COnfirm box if requestor change##################*/
	if($('#woRequestedByPrev').val() != ''){
		if($('#woRequestedByPrev').val() != $('#wo_requested_by').val()){
			var r = confirm("You have changed the requestor.Do you want to continue");
			if (r == false)
			{
				$('#wo_requested_by').val($('#woRequestedByPrev').val());
				getRequestorsInfo($('#woRequestedByPrev').val());
			  return ;
			}else{
				$('#woRequestedByPrev').val($('#wo_requested_by').val());
			}
		}
	}
	/*###################################*/
	$('#prompt_save').val(1);
	if(from == 'submit'){
		window.onbeforeunload = 'undifined';
	}
       
	$('.message_outage_submit').css({display:'none'});

	var woCRITICAL = "FALSE";
	if(document.getElementById('CRITICAL').checked==true)
	{
		woCRITICAL = document.getElementById('CRITICAL').value;
	}
	
	var rallyType = "";
	var rallyFlag = "";
	var rallyProject = "";

	if(document.getElementById('wo_rally_flag')){
		rallyFlag = document.getElementById('wo_rally_flag').value;
		rallyProject = document.getElementById('wo_project').value;
	}

	if(requestedId == "") {
		valid = false;
		document.getElementById('wo_requested_by_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_requested_by_label').style.color = "#000000";
	}
	if(projectId == "") {
		valid = false;
		document.getElementById('wo_project_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_project_label').style.color = "#000000";
	}
	if(woTitle == "") {
		valid = false;
		document.getElementById('wo_title_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_title_label').style.color = "#34556C";
	}
	if(woDesc == "") {
		valid = false;
		document.getElementById('wo_desc_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_desc_label').style.color = "#34556C";
	}
	
	if(woId != ""){
		if(woStatus == "") {
			valid = false;
			$("label[for='wo_status']").css({'color':'#FF0000'});		
		} else {
			$("label[for='wo_status']").css({'color':'#34556C'});
		}

		if(woAssignedTo == "") {
			valid = false;
			$("label[for='wo_assigned_user']").css({'color':'#FF0000'});		
		} else {
			$("label[for='wo_assigned_user']").css({'color':'#34556C'});
		}    
	}
	
	if(woREQ_TYPE == "_blank") {
		valid = false;
		document.getElementById('wo_request_type_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_request_type_label').style.color = "#34556C";
	}
	if(woSEVERITY == "_blank") {  // && woREQ_TYPE != "3" && woREQ_TYPE != "1"
		valid = false;
		document.getElementById('wo_severity_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_severity_label').style.color = "#34556C";
	}
	if(woSITE_NAME == "_blank") {
		valid = false;
		document.getElementById('wo_site_name_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_site_name_label').style.color = "#34556C";
	}

	/*if(woINFRA_TYPE == "_blank") {
		valid = false;
		document.getElementById('wo_infra_type_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_infra_type_label').style.color = "#34556C";
	}*/
	
	if("932" == woAssignedTo){
		$("#wo_rally_type_label").css({color:"#34556C"});
		$("#wo_rally_project_label").css({color:"#34556C"});
		if(document.getElementById('wo_rally_defect').checked){
			rallyType = $("#wo_rally_defect").val();
		}else if(document.getElementById('wo_rally_enhancement').checked){
			rallyType = $("#wo_rally_enhancement").val();
		}else{
			valid = false;
			$("#wo_rally_type_label").css({color:"#FF0000"});
		}
	}
	
	if($('#wo_status').val() == '3' || $('#wo_status').val() == '1'){
		var assigned_user = $("#wo_assigned_user").val();
		var wo_requested_by_co = $('#wo_requested_by_co').val();
		
	    wo_requested_by_array = wo_requested_by_co.split(",");
		
		if(($.inArray(assigned_user,wo_requested_by_array) != '-1') && ($('#completed_by').val() == '')){
			//$('#li_completed_by').slideDown('slow');
			valid = false;
			
			//document.getElementById('wo_site_name_label').style.color = "#FF0000";
			$("label[for='completed_by']").css({'color':'#FF0000'});
		}else{
		$("label[for='completed_by']").css({'color':'#34556C'});
		
		}
	}

	  if(timeSensDate == "") {

			valid = false;
			$("label[for='time_sensitive_date']").css({'color':'#FF0000'});
		} else {
			$("label[for='time_sensitive_date']").css({'color':'#34556C'});
		}
		if(timeSensTime == "0" || timeSensTime == "") {
			valid = false;
			$("label[for='time_sensitive_time']").css({'color':'#FF0000'});
		} else {
			$("label[for='time_sensitive_time']").css({'color':'#34556C'});
		}
		/*if(ampm == "0" || ampm == "") {
			valid = false;
			$("label[for='ampm']").css({'color':'#FF0000'});
		} else {
			$("label[for='ampm']").css({'color':'#34556C'});
		}*/
		var timeSensDateReg = /^(\d{2})\/(\d{2})\/(\d{4})$/;
		if(!timeSensDateReg.test(timeSensDate))
		{
			valid = false;
			$("label[for='time_sensitive_date']").css({'color':'#FF0000'});
		}
		if(wo_draft == true) {
		
  		  if(timeSensDate_draft == "") {
  			 valid = false;
  			 $("label[for='wo_draft']").css({'color':'#FF0000'});
  		  } else {
  			 $("label[for='wo_draft']").css({'color':'#34556C'});
  		  }
  		  if(timeSensTime_draft == "0" || timeSensTime_draft == "") {
  			 valid = false;
  			 $("label[for='time_sensitive_time_draft']").css({'color':'#FF0000'});
  		  } else {
  			 $("label[for='time_sensitive_time_draft']").css({'color':'#34556C'});
  		  }
  		 /* if(ampm_draft == "0" || ampm_draft == "") {
  			 valid = false;
  			 $("label[for='ampm_draft']").css({'color':'#FF0000'});
  		  } else {
  			 $("label[for='ampm_draft']").css({'color':'#34556C'});
  		  }*/
  		  if(!timeSensDateReg.test(timeSensDate_draft))
  		  {
  			 valid = false;
  			 $("label[for='time_sensitive_date_draft']").css({'color':'#FF0000'});
  		  }
		 }
	  else{
		  $("label[for='wo_draft']").css({'color':'#34556C'});
	  }
	  if(woStatus == 6 ) { //&& woId == "") {
		var currDateTime = document.getElementById('currentDateTime').value;
		var splitcurrDateTime = currDateTime.split(':');
		var currDate = splitcurrDateTime[0];
		var currTime = splitcurrDateTime[1];
		var splitcurrDate = currDate.split('-');
		var splitcurrTime = currTime.split('-');			

		var currentDate=new Date(parseInt(splitcurrDate[0],10),(parseInt(splitcurrDate[1],10)-1),parseInt(splitcurrDate[2],10),parseInt(splitcurrTime[0],10),parseInt(splitcurrTime[1],10));

		if(!timeSensDateReg.test(timeSensDate))
		{
			valid = false;
			$("label[for='time_sensitive_date']").css({'color':'#FF0000'});
		}
		else
		{	
			var requestDate = timeSensDate.split("/");
			//alert("timeSensTime"+timeSensTime);
			/*if(ampm != "0" && ampm != "")
			{
				if(ampm == "pm" && timeSensTime !='12'){
					timeSensTime = parseInt(timeSensTime,10)+ 12;				
				}
				if(ampm == "am" && timeSensTime =='12')
				{
					timeSensTime = 0;
				}
			}*/
			if(timeSensTime.indexOf("PM") > 0){
				timeSensTime_new = parseInt(timeSensTime,10)+ 12;
			}else{
				timeSensTime_new = timeSensTime;
			}
			//alert("timeSensTime"+timeSensTime);
			var launchDate = new Date(parseInt(requestDate[2],10),(parseInt(requestDate[0],10)-1),parseInt(requestDate[1],10),parseInt(timeSensTime_new,10),0,0); // yyyy - mm- dd hh - mm - ss
			
			
			if(launchDate < currentDate){
			
				$('.message_required p').html('Please enter current or future time for required time.');
				$('.message_required').css({display:'block'});
		        return;
			} 
			if(wo_draft == true) {

				var draftDate = timeSensDate_draft.split("/");
				/*if(ampm_draft != "0" && ampm_draft != "")
				{
					if(ampm_draft == "pm" && timeSensTime_draft !='12'){
						timeSensTime_draft = parseInt(timeSensTime_draft,10)+ 12;				
					}
					if(ampm_draft == "am" && timeSensTime_draft =='12')
					{
						timeSensTime_draft = 0;
					}
				}*/
				if(timeSensTime_draft.indexOf("PM") > 0){
					timeSensTime_draft_new = parseInt(timeSensTime_draft,10)+ 12;
				}else{
					timeSensTime_draft_new = timeSensTime_draft;
				}
				var draftDt = new Date(parseInt(draftDate[2],10),(parseInt(draftDate[0],10)-1),parseInt(draftDate[1],10),parseInt(timeSensTime_draft_new,10),0,0); // yyyy - mm- dd hh - mm - ss			
				if(draftDt < currentDate) {
					$('.message_required p').html('Please enter current or future time for draft time.');
					$('.message_required').css({display:'block'});
					return;
				}

				if(draftDt > launchDate) {
					$('.message_required p').html('Draft date/time should not be less than required date.');
					$('.message_required').css({display:'block'});
					return;
				}
			}
		}
	}
	
		
	if(valid) {
		data = {woId:woId,dirName:dirName,requestedId:requestedId,projectId:projectId,woTypeId:woTypeId,priorityId:priorityId,timeSens:timeSens,timeSensDate:timeSensDate,timeSensTime:timeSensTime,ampm:ampm,wo_draft:wo_draft,timeSensDate_draft:timeSensDate_draft,timeSensTime_draft:timeSensTime_draft,ampm_draft:ampm_draft,woTitle:woTitle,woExampleURL:woExampleURL,woDesc:woDesc,woStatus:woStatus,woAssignedTo:woAssignedTo,woStartDate:woStartDate,woEstDate:woEstDate,rallyType:rallyType,rallyProject:rallyProject,rallyFlag:rallyFlag,woREQ_TYPE:woREQ_TYPE,woSEVERITY:woSEVERITY,woSITE_NAME:woSITE_NAME,woINFRA_TYPE:woINFRA_TYPE,woCRITICAL:woCRITICAL,woCCList:woCCList,launchDate:launchDate,currmin:currmin,draftDate:draftDate,commentSubmit:from,woStatusIdHidden:woStatusIdHidden,completed_by:completed_by,wo_related_ids:wo_related_ids,df_related_ids:df_related_ids};
		//LH34096 if request type is request then estimate time will same launch date
		if(woREQ_TYPE == '3'){
			$('#estimated_completion_date').val(timeSensDate+" "+timeSensTime);

		}
		//on New ticket Onclick disabled
		if(woId == ""){
		
			$('#wo_save span').html("Please Wait...");
			$('#wo_save').unbind("click");
		}
		$('#wo_dimmer').css({display:'block'});
		$('#wo_dimmer').css({backgroundColor:'#FFFFFF'});
		$('#wo_dimmer').css({opacity:'0.7'});
		$('#wo_dimmer').css({filter:'0.7'});
		$('#wo_dimmer_ajax').css({display:'block'});
		
		if(wo_draft == true){
			var redirect_status_val = -1;
		}else{
			var redirect_status_val = 1;
		}
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/save_wo.php",
			data: data,
			success: function(msg) {
				$('#wo_dimmer').css({display:'none'});
				$('#wo_dimmer_ajax').css({display:'none'});
				var responseValue = msg.split('~');
				var res_woID = responseValue[0];
				var res_woStatusID = responseValue[1];
				var res_woAssignedID = responseValue[2];
				var res_woAssignedList = responseValue[3];
				
				if(parseInt(res_woID)) {

					$('#workorder_id').val(res_woID);
					$('#dirName').val(res_woID+"/");				

					if($('#workorder_id').val() != "") {
						$('#comment_dimmer').css({display:"none"});
					}
					
					if(woId == "") {
						Set_Cookie( "lighthouse_create_wo_data", projectId, "7", "/", "", "");
						Set_Cookie( "lighthouse_wo_data", '-1~' + projectId + '~-1~-1', "7", "/", "", "");
						window.location = '/workorders/?status='+redirect_status_val;
					} else {
						if(res_woAssignedList != ''){
							$('#wo_assigned_user').html(res_woAssignedList);
						}
						//LH#23699
						//$('#assignedToUserIdHidden').val(res_woAssignedID);
						$('#woStatusIdHidden').val(res_woStatusID);
						$('#wo_assigned_user').val(res_woAssignedID);
						$('#wo_status').val(res_woStatusID);
						if(woStatus !='6' && woStatus !='7' && woStatus !='' ){    
							$('#closeLHTicketId').css({display:'block'});
						}else{
							$('#closeLHTicketId').css({display:'none'});
						}   
						updateStatusList(woId,res_woStatusID);
						if(from != 'comment'){
							$('.message_required p').html('The work order is saved successfully.');
							$('.message_required').css({display:'block'});
						}
					}
				}
			},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("Error:" + textStatus + msg2 );
				}		
		}); 
		updateAssignedDate();
		//updateEstimatedDate();
             

	} else {
		$('.message_required p').html('Please fill in the required fields?');
		$('.message_required').css({display:'block'});
		return false;
	} 
}

function changeAssignedToUser(){
	if($('#wo_status').val() == '5' || $('#wo_status').val() == '10'){
		//default the assigned to User for Feedback status
		$("#wo_assigned_user").val('');
	}
	var assigned_user = $("#wo_assigned_user").val();
	//For fixed
	if($('#wo_status').val() == '3' || $('#wo_status').val() == '1'){
		var wo_requested_by_co = $('#wo_requested_by_co').val();
		
	    wo_requested_by_array = wo_requested_by_co.split(",");
		if($.inArray(assigned_user,wo_requested_by_array) != '-1'){
		//if(wo_requested_by_array.indexOf(assigned_user) != '-1'){
			$('#li_completed_by').slideDown('slow');
		}else{
			$('#completed_by').val('')
			$('#li_completed_by').slideUp('slow');
		
		}
			
	}else{
		$('#completed_by').val('')
		$('#li_completed_by').slideUp('slow');
	}
}
//On change of assigned to field
$(document).ready(function() {
	$('#wo_assigned_user').change(function(){
	
	var assigned_user = $("#wo_assigned_user").val();
	if($('#wo_status').val() == '3' || $('#wo_status').val() == '1'){
		var wo_requested_by_co = $('#wo_requested_by_co').val();
		
	    wo_requested_by_array = wo_requested_by_co.split(",");
		
		if($.inArray(assigned_user,wo_requested_by_array) != '-1'){
			$('#li_completed_by').slideDown('slow');
		}else{
			$('#completed_by').val('')
			$('#li_completed_by').slideUp('slow');
			
		
		}
		
	}else{
	
		$('#completed_by').val('')
		$('#li_completed_by').slideUp('slow');
	
	}
	
	
	});
	});




function updateStatusList(woId,woStatus)
{
	qString = '?status_id='+woStatus+'&responseType=json';
	jQuery.getJSON('/_ajaxphp/wo_status_list.php'+qString, function(json) {
			var select = document.getElementById("wo_status");
			select.length = 0;
			select.options[0] = new Option("--Select Type--",""); 
			for(var i=0;i<json.length;i++)
			{
				if(woStatus==json[i]['id'])
				{
					select.options[i+1] = new Option(json[i]['name'],json[i]['id'],true); 
				}
				else
				{
					select.options[i+1] = new Option(json[i]['name'],json[i]['id']); 
				}
				
			}
			$("#wo_status option[value='"+woStatus+"']").attr('selected', 'selected');

		});

}

function updateComments() {
	var qString2 = "wo_id="+document.getElementById('workorder_id').value;
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/get_num_wo_comments.php",
		data: qString2,
		success: function(msg) {
			$("#number_of_comments").html("Comments ("+msg+")");
		}
	});
}
function closeWorkOrder() {
	$('.message_close').css({display:'none'});
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/close_wo.php",
		data: "woId="+document.getElementById('workorder_id').value,
		success: function(msg) {
			document.getElementById('close_date').value=msg;
			window.location = '/workorders/';
		}
	});
}
function checkDirName(fileForm) {
	var dirName = document.getElementById('dirName');
	var woid = document.getElementById('workorder_id').value;
	var fileList = document.getElementById('file_upload_list');
	
	$('#file_upload_dimmer').css({display:'block'});
	$('#save_buttons_dimmer').css({display:'block'});
	$('#file_upload_dimmer').css({backgroundColor:'#FFFFFF'});
	$('#file_upload_dimmer').css({opacity:'0.7'});
	$('#file_upload_dimmer').css({filter:'0.7'});
	$('#save_buttons_dimmer').css({backgroundColor:'#FFFFFF'});
	$('#save_buttons_dimmer').css({opacity:'0.7'});
	$('#save_buttons_dimmer').css({filter:'0.7'});
	$('#file_upload_ajax').css({display:'block'});
	
	//if(dirName.value == "") {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/create_file_dir.php?dirName="+dirName.value,
			success: function(msg) {
				if(dirName.value == "") {
					dirName.value = msg;
				}
				//fileForm.submit();
				ajaxUploadFile(dirName.value,woid);
				fileForm.upload_file.value="";
			}
		});
	//} else {
	//	fileForm.submit();
	//	fileForm.upload_file.value="";
	//}
}
function updateFileList() {
	var dirName = document.getElementById('dirName');
	var fileList = document.getElementById('file_upload_list');
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/wo_file_listing.php?dirname="+dirName.value,
		success: function(msg) {
			fileList.innerHTML = msg;
			$('#file_upload_dimmer').css({display:'none'});
			$('#save_buttons_dimmer').css({display:'none'});
			$('#file_upload_ajax').css({display:'none'});
		}
	});
}
function submitFileForm() {
	document.file_upload_form.submit();
	updateFileList(); 
}
function removeFile(fileNum) {
	//Remove this file from the database and repopulate the list
	//$("#file_" + fileNum)
	//alert('removing file_'+fileNum);

	var html = '<div class="file_message_confirm">';
		html +=	'<p> Are you sure you would like to remove the attachment? </p>';
		html +=	'<div style="clear: both;"></div>';
		html +=	'<div class="duplicate_buttons">';
		html +=	'	<button onClick="removeFileConfirmed('+fileNum+');"><span>Yes</span></button>';
		html +=	'	<button class="cancel" onClick="$(\'.file_message_confirm\').css({display:\'none\'}); return false;"><span>No</span></button>';
		html +=	'	<div style="clear: both;"></div>';
		html +=	'</div>';
		html +=	'</div>';
	$('body').append(html);
	$('.file_message_confirm').css({display:'block'});
	
}

function removeFileConfirmed(fileNum) {

		$.ajax({
		type: "GET",
		url: "/_ajaxphp/remove_uploaded_file.php",
		data: "id="+fileNum,
		success: function(msg) {
			//alert("Data Saved: " + msg)
		}
	});
	$('.file_message_confirm').css({display:'none'});
	updateFileList();

}

function uploadFile(theForm) {
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/uploadFile.php",
		data: "file="+theForm.upload_file.value,
		success: function(msg) {
			//alert("Data Saved: " + msg)
		}
	});
	
	updateFileList();
	
	$("#upload_file").value = "";
}

/*$("#p_cclist_name:even").addclass("p_cclist_name_right");
$("#p_cclist_name:odd").addclass("p_cclist_name_left");*/

function submitComment() {
    //LH#23699
	var woId = document.getElementById('workorder_id').value;
	//var userId = document.getElementById('user_id').value;
	var comment = document.getElementById('comment').value;
	//var assignedToHidden = $('#assignedToUserIdHidden').val();
	var woStatusHidden = $('#woStatusIdHidden').val();
	var woStatus = $('#wo_status').val();
	
									
   /*
    * Private Comment
    * Select all checked users
    */
    // var p_cclist = $('#p_cclist').val();
    /*	if($('#p_comment').is(":checked") == true){
		var p_cc = new Array();
		$.each($("input[name='p_cclist[]']:checked"), function() {
			p_cc.push($(this).val());
		});
		if(p_cc.length > 0){
			$('#hidden_p_cclist').val(p_cc);
		}else{
			//$('#hidden_p_cclist').val('');
                         $('.message_private_alert').css("display","block");
			$('#p_comment').attr("checked","");
			$('#hidden_p_cclist').val('');
			return false;
		}
	}else{
		$('#hidden_p_cclist').val('');
	}
	//if Private box un checked 
	
	//Put in hidden input box
	var p_cc_users = $('#hidden_p_cclist').val();*/
    //##############
	if($.trim(comment) != "" &&  woId != "") {
		
		$('#comment_dimmer').css({display:"block"});	
		//LH#23699
		//if(comment != "" && userId != "" && woId != "") {
		var status = saveWorkOrder('comment');		
		if(status != false){
		statusChangeNotifiction();
			$.ajax({
				type: "POST",
				async: false,
				url: "/_ajaxphp/wo_save_comment.php",
				//Adding one more parameter for private users id
				//LH#23699
				//data: { woId : woId, userId : userId, comment : comment}
				data: { woId : woId, comment : comment},
				success: function(msg) {
					$('#comments_list').html(msg) ;
					document.getElementById('comment').value = "";
					$('.message_required p').html('The comment is saved successfully.');
					$('.message_required').css({display:'block'});
					updateComments();
					//$('#comment_dimmer').css({display:"none"});	
					
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					alert('Comment has not saved. Please try again..'); 
				}		
			});
		}
	}else{
		if($.trim(comment) == ''){
			alert('Please enter the comment');
		}else{
			alert('Comment has not saved. Please try again..');
		}
		
	}
	
	
	
	return false;
}

function statusChangeNotifiction(){
	var woId = document.getElementById('workorder_id').value;
	var woStatus = $('#wo_status').val();
$.ajax({
				type: "POST",
				async: false,
				url: "/workorders/index/wostatus",
				//Adding one more parameter for private users id
				//LH#23699
				//data: { woId : woId, userId : userId, comment : comment}
				data: { woId : woId, woStatus : woStatus},
				success: function(msg) {
					if(msg != ''){
						$container = $("#new_comment_notification").notify();
						create("sticky", { title:'New Comment Notification', text:msg},{ expires:false });
					}
				}		
			});

}			

function addCcUser() {
	var woId = document.getElementById('workorder_id').value;
	var cc = document.getElementById('cc_user').value;
	
	document.getElementById('cclist').value += cc +",";
	
	var cclist = document.getElementById('cclist').value;
	//alert('add cc: '+woId+":"+cc);

	$.ajax({
		type: "GET",
		url: "/_ajaxphp/wo_save_cc.php",
		data: "woId="+woId+"&cc="+cclist+"&addCC="+cc,
		success: function(msg) {
			//alert(msg);
			document.getElementById('cc_list').innerHTML = msg;
		}
	});

}
function removeCcUser(userId) {
	var woId = document.getElementById('workorder_id').value;
	//var cc = document.getElementById('cc_user').value;
	
	//document.getElementById('cclist').value += cc +",";


	var cclist = document.getElementById('cclist').value;
	if(cclist!=null)
	{
		var col_array= cclist.split(',');
		var part_num=0;
		var finalccList = "";

		while (part_num < col_array.length)
		{

			if(col_array[part_num]!=userId && col_array[part_num] !='')
			{
			  finalccList = finalccList + col_array[part_num] +',';
			}

		    part_num+=1;
		}
	}
	document.getElementById('cclist').value = finalccList;
	//alert('add cc: '+woId+":"+cc);
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/wo_save_cc.php",
		data: "woId="+woId+"&cc="+cclist+"&remove="+userId,
		success: function(msg) {
			//alert(msg);
			document.getElementById('cc_list').innerHTML = msg;
		}
	});

	return false;
}

function updateAssignedDate() {
	var woId = document.getElementById('workorder_id').value;
	
	if(woId != "") {
		qString = "wo_id="+woId;
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/update_wo_assigned_date.php",
			data: qString,
			success: function(msg) {
				//alert(msg);
				document.getElementById('assigned_date').value = msg;
				
			var wo_ser = '';
			if($('#woesd')){
			wo_ser = $('#woesd').val();
			}
			if((wo_ser == 'undefined')||(!wo_ser)){
			updateEstimatedDate();}
			}
		});
	}
}

function ajaxUploadFile(dirName, woId){
	/*
	prepareing ajax file upload
	url: the url of script file handling the uploaded files
	fileElementId: the file type of input element id and it will be the index of $_FILES Array()
	dataType: it support json, xml
	secureuri:use secure protocol
	success: call back function when the ajax complete
	error: callback function when the ajax failed

	*/
	$.ajaxFileUpload
	(
		{
			url:'/_ajaxphp/uploadFile.php',
			secureuri:false,
			fileElementId:'upload_file',
			dataType: 'text',
			data:'dirName='+dirName+'&workorder_id='+woId,
			success: function (data, status)
			{
				if(data.search("success")>='0'){
					updateFileList();
					$('.message_required p').html('File uploaded successfully.');
					$('.message_required').css({display:'block'});
				}
				else if(data.search("You")>='0'){
					updateFileList();
					$('.message_required p').html('File extention not supported.');
					$('.message_required').css({display:'block'});
				}
				else if(data.search("Exdeed")>='0'){
					updateFileList();
					$('.message_required p').html('File cant be uploaded, size is more then 10 MB.');
					$('.message_required').css({display:'block'});
				}
				else{
					$('.message_required p').html('Error in uploading the file. Please check the size of the file and upload again.');
					$('.message_required').css({display:'block'});
					$('#file_upload_dimmer').css({display:'none'});
					$('#save_buttons_dimmer').css({display:'none'});
					$('#file_upload_ajax').css({display:'none'});
				}
			},
			error: function (data, status, e)
			{
				$('.message_required p').html('Error in uploading the file. Please check the size of the file and upload again.');
				$('.message_required').css({display:'block'});
				$('#file_upload_dimmer').css({display:'none'});
				$('#save_buttons_dimmer').css({display:'none'});
				$('#file_upload_ajax').css({display:'none'});
			}
		}
	)
	return false;
}
function reOpenWorkOrder() {
	$('.message_reopen').css({display:'none'});
	//Ticket #18290
         var woid = document.getElementById('workorder_id').value;
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/reopen_wo.php",
		data: "woId="+document.getElementById('workorder_id').value,
		success: function(msg) {
			document.getElementById('close_date').value=msg;
		         //Ticket no #18290
                  window.location.href = '/workorders/index/edit/?wo_id='+woid;
		}
	});
}
function showDraftTimeField(){
//	alert("here");
	if($('#WO_DRAFT').is(':checked')){
		$('#wo_draft_time').css({display:'block'});
	}else {
    $('#wo_draft_time').css({display:'none'});
  } 
}
function openAnimated(comment_id){
	
	//$('#comment_id_li_'+comment_id).slideToggle('medium'); 
	$('html,body').animate({scrollTop: $('#comment_id_li_'+comment_id).offset().top},'slow');
}



function showNewComment() {
	//$('#tips').slideDown('slow');
	updateComments();
	var last_comment_id ='';
	var wid = $('#workorder_id').val();
	var last_wid = $('#last_comment_id').val(); 
	//alert("last_wid"+last_wid);
		//if($.trim(last_wid) != ''){
		
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/next_new_comment.php",
			data: 'wid='+wid+'&last_wid='+last_wid,
			success: function(comment_msg) {
				
					if($.trim(comment_msg) != ''){
						//alert("comment_msg"+comment_msg);
						var last_comment = comment_msg.split("##");
						last_comment_id = last_comment[0];
						last_comment_username = last_comment[1];
						current_server_time = last_comment[2];
						if($.trim(current_server_time) != '' && $.trim(last_comment_id) ==''){
							
							checkEditCommentTime(current_server_time);
							
						}
						if(($.trim(last_comment_id) !='') && ($.trim(last_comment_username) !='')){  
							checkEditCommentTimeDisable(last_comment_id);
						$('#last_comment_id').val(last_comment_id); 
						$.ajax({
							type: "POST",
							url: "/_ajaxphp/check_new_wo.php",
							data: 'wid='+wid+'&last_wid='+last_comment_id,
							success: function(msg) {
									if($.trim(msg) !=''){
									
									$('#comments_list').prepend(msg);
									//$("#new_comment_notification").css("display","none")
									$container = $("#new_comment_notification").notify();
									
									create("sticky", { title:'New Comment Notification', text:'<strong><span onclick="openAnimated('+last_comment_id+');" id="span_'+last_comment_id+'" >'+last_comment_username+' posted a comment</span></strong>'});
									//create("default", { title:'Default Notification', text:'Example of a default notification.  I will fade out after 5 seconds'});

									// $("#BeeperBox").html('<strong><span onclick="openAnimated('+last_comment_id+');" id="span_'+last_comment_id+'" >'+last_comment_username+' posted a <a title="notifications panel"  hef="javascrip:void(null);">comment</a>.');
									// showTip();
									//statusupdatNotifiction();
									
								}
								
								
							}
							});
							
						}
						
					}
					
				}
			});
			
	//}
}
function create( template, vars, opts ){
	return $container.notify("create", template, vars, opts);
}


function displayCommentBox(comment_id){
	$('#comment_id_li_msg_'+comment_id).slideUp('slow');
	$('#comment_id_li_body_'+comment_id).slideDown('slow');
	$('#comment_id_li_body_'+comment_id).css('display','inline');
}


function updateComment(comment_id){
	var newCOmment = $('#comment_id_li_textarea_'+comment_id).val();
	
	if($.trim(newCOmment) == ''){
		alert('Please enter the comment');
		return false;
	}else{
	//$('#comment_id_li_'+comment_id).slideUp('slow');
	//$('#comment_id_li_'+comment_id).remove();
	$('#comment_id_li_'+comment_id).html('<p><center><img src="/_images/ajax-loader.gif" alt="loading.." ><br/>Please wait..</center></p>');
	$.ajax({
			type: "POST",
			url: "/_ajaxphp/update_wo_comment.php",
			data: { comment : newCOmment, comment_id : comment_id},
			success: function(msg) {
					//$('#comment_id_li_'+comment_id).slideUp('slow');
					$('#comment_id_li_'+comment_id).html(msg);
					$('#comment_id_li_'+comment_id).slideDown('slow');
					//$('#comments_list').prepend(msg);
					//$('#comment_id_li_'+comment_id).load(msg);
					//$('#comment_id_li_'+comment_id).slideDown('slow');
										
				}
			});
	}		

}

function checkEditCommentTime(current_server_time){
		
		$('.comment_id_li_comment_id').each(function ()
		{
			var oDiff = new Object();
			var commt_id = $(this).val();
			var time_id = $('#comment_id_li_comment_time_'+commt_id).val();
			var t = time_id.split(/[- :]/);

			// Apply each element to the Date function
			var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			//alert(d);
			myEpoch = d.getTime();
			var ct = current_server_time.split(/[- :]/);
			//var current_time =  new Date(current_server_time*1000);
			var current_time_new = new Date(ct[0], ct[1]-1, ct[2], ct[3], ct[4], ct[5]);
			current_time = current_time_new.getTime();
			//current_time = current_time1.getTime();
			//alert(current_time_new);
			var nTotalDiff = current_time-myEpoch; // If diff comes minus value, then it is past date-time, otherwise it is future data-time
			var milliseconds = nTotalDiff;

			var seconds = milliseconds / 1000;

			var minutes = seconds / 60;

			seconds %= 60;

			var hours = minutes / 60;

			minutes %= 60;

			var days = hours / 24;

			hours %= 24;
			if(parseInt(days) > 0 || parseInt(hours) > 0 || parseInt(minutes) >=15){
				//alert("days"+days+"hours"+hours+"minutes"+minutes);
				$('#edit_pannel_'+commt_id).slideUp('slow');
				$('#edit_pannel_'+commt_id).remove();
				$('#comment_id_li_body_'+commt_id).slideUp('slow');
				$('#comment_id_li_body_'+commt_id).remove();
			
			}
				// Your code here
		});
 

}

function deleteComment(comment_id){
	var r=confirm("You are about to delete the comment. Do you want to continue?");
	if (r==true)
	  {
	  $.ajax({
			type: "POST",
			url: "/_ajaxphp/update_wo_comment.php",
			data: { del : "lhcommentdelete", comment_id : comment_id},
			success: function(msg) {
					if(msg == 'ok'){
					//alert("ddd");
						//$('#comment_id_li_msg_'+comment_id).html(msg);
						$('#comment_id_li_'+comment_id).slideUp('slow');	
					}	
					
				}
			});
	  }
	
	
}


function checkEditCommentTimeDisable(last_comment_id){
		$('.comment_id_li_comment_id').each(function ()
		{
			var commt_id = $(this).val();
			
			//alert("last_comment_id"+last_comment_id+"commt_id"+commt_id);
			if(last_comment_id != commt_id){
				//if(parseInt(days) > 0 || parseInt(hours) > 0 || parseInt(minutes) >=15){
				$('#edit_pannel_'+commt_id).slideUp('slow');
				$('#edit_pannel_'+commt_id).remove();
				$('#comment_id_li_body_'+commt_id).slideUp('slow');
				$('#comment_id_li_body_'+commt_id).remove();
			
				
			}
				// Your code here
		});
 

}

function statusupdatNotifiction(){
	var woId = $('#workorder_id').val();
	var woStatus = $('#wo_status').val();
			$.ajax({
				type: "POST",
				async: false,
				url: "/workorders/index/wostatusupdate",
				//Adding one more parameter for private users id
				//LH#23699
				//data: { woId : woId, userId : userId, comment : comment}
				data: { woId : woId, woStatus : woStatus},
				success: function(msg) {
					if(msg != ''){
						
						var responseValue = msg.split('~');
						
						var res_woStatusID = responseValue[0];
						var res_woAssignedID = responseValue[1];
						$('#woStatusIdHidden').val(res_woStatusID);
						//$('#wo_assigned_user').val(res_woAssignedID);
						//$('#wo_status').val(res_woStatusID);
						//$container = $("#new_comment_notification").notify();
						//create("sticky", { title:'New Comment Notification', text:msg},{ expires:false });
					}
				}		
			});

}


function showWOTooltip(){
	var topVar = ($('#wo_tooltip').offset().top + 58); 
	var leftVar = 175; 
	$('#wo_tooltip').css({display:'block', left: leftVar + 'px'});
	}

	function hideWOTooltip(){
	$('#wo_tooltip').css({display:'none'});
	}
	
	
function addReleatedIssue(){
		var issue_type = $('input[name=issuse_types]:checked').val();
		var related_issues_txt = $('#related_issues_txt').val();
		var workorder_id = $('#workorder_id').val();
		var wo_related_ids = $('#wo_related_ids').val();
		var df_related_ids = $('#df_related_ids').val();
		var count_wo = [];
		var count_df = [];
		var total_count  = 0;
		var duplicate_id = '-1';
		count_wo = wo_related_ids.split(',');
		count_df = df_related_ids.split(',');
		if(issue_type == 'WO'){
			
			
			if(wo_related_ids == ''){
				var new_wo_rlated_ids = related_issues_txt;
				
			}else{ 
				duplicate_id = $.inArray(related_issues_txt,wo_related_ids.split(','));
				var new_wo_rlated_ids = wo_related_ids+","+related_issues_txt;
					
				
			}
			
		}else if(issue_type == 'DF'){
			if(df_related_ids == ''){
				var new_df_rlated_ids = related_issues_txt;
			}else{
				 duplicate_id = $.inArray(related_issues_txt,df_related_ids.split(','));
				var new_df_rlated_ids = df_related_ids+","+related_issues_txt;
				
				
				
			}
		}
		
		total_count = (count_wo.length)+(count_df.length);
		
			if(duplicate_id == '-1'){
			//var issue_type = $('input[name=issuse_types]:checked').val();
			if(total_count <= 3){
				if(issue_type != '' && related_issues_txt != ''  ){
					$.ajax({
					type: "POST",
					url:"/workorders/index/addreleatedissues", 
					data:{issue_type:issue_type,related_issues_id:related_issues_txt,wid:workorder_id},
					success: function(val){
						$('#related_list').append(val);
						if(issue_type == 'WO'){
							$('#wo_related_ids').val(new_wo_rlated_ids.replace(",,",","));
						
						}else if(issue_type == 'DF'){
							//var new_df_rlated_ids = df_related_ids+","+related_issues_txt;
							$('#df_related_ids').val(new_df_rlated_ids.replace(",,",","));
						}
				
					}
					});
				}
			
			}else{
			
				$('.message_required p').html('You can add only Four Related Issues ID');
				$('.message_required').css({display:'block'});
			
			}
		}else{
			$('.message_required p').html('Related Issue ID alreadt exist');
			$('.message_required').css({display:'block'});
		
		
		}
		
	
	
	
	}
	
	function removeWoRelatedIds(issueid){
		var woId = $('#workorder_id').val();
		if(woId == ''){
			var woRelatedId = $('#wo_related_ids').val();
			if(woRelatedId!=null)
			{
				var col_array= woRelatedId.split(',');
				
				finalWoList = jQuery.grep(col_array, function(value) {
				  return value != issueid;
				});
			}
			$('#wo_related_ids').val(finalWoList);
			
		}else{
			var woRelatedId = $('#wo_related_ids').val();
			if(woRelatedId!=null)
			{
				var col_array= woRelatedId.split(',');
				
				finalWoList = jQuery.grep(col_array, function(value) {
				  return value != issueid;
				});
			}
			$('#wo_related_ids').val(finalWoList);
		//alert('add cc: '+woId+":"+cc);
			$.ajax({
			type: "POST",
			url:"/workorders/index/deletereleatedissues", 
			data:{issue_type:'WO',related_issues_id:issueid,wid:woId},
			success: function(val){
				
		
			}
			});
		}
		$('#WO-'+issueid).css('display','none');
		return false;
	
	
	}
	
	function removeDfRelatedIds(issueid){
	
	var woId = $('#workorder_id').val();
		if(woId != ''){
			var woRelatedId = $('#df_related_ids').val();
			if(woRelatedId!=null)
			{
				var col_array= woRelatedId.split(',');
				
				finalWoList = jQuery.grep(col_array, function(value) {
				  return value != issueid;
				});
			}
			$('#df_related_ids').val(finalWoList);
			
		}else{
			var woRelatedId = $('#df_related_ids').val();
			if(woRelatedId!=null)
			{
				var col_array= woRelatedId.split(',');
				
				finalWoList = jQuery.grep(col_array, function(value) {
				  return value != issueid;
				});
			}
			$('#df_related_ids').val(finalWoList);
		//alert('add cc: '+woId+":"+cc);
			$.ajax({
			type: "POST",
			url:"/workorders/index/deletereleatedissues", 
			data:{issue_type:'DF',related_issues_id:issueid,wid:woId},
			success: function(val){
			
			
				
		
			}
			});
		}
		$('#DF-'+issueid).css('display','none');
		return false;
	
	
	}


