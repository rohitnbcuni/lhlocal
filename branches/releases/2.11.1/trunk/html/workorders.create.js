$(document).ready(function() {
	if(document.getElementById('start_date').value == "") {
		document.getElementById('start_date').value = getCurrentDate();
	}
	
	if(document.getElementById('start_date').value != "") {
		var wo_id = document.getElementById('workorder_id').value;
        if(wo_id !== '')
        {
            updateEstimatedDate();
        }    
	}

	if("932" == $('#wo_assigned_user').val()){
		$("li.rally").css({display:'block'});
	}
	
	$('#wo_assigned_user').change(function () {
		$('#wo_status').val('2');
		if("932" == $('#wo_assigned_user').val()){
			$("li.rally").css({display:'block'});
		}else{
			$("li.rally").css({display:'none'});
		}
	});
	updateComments();
	updateFileList();
	$('#save_buttons_dimmer').css({display:'none'});
});
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
function getRequestType(reqType) {
	if(reqType!='_blank')
	{
		$('#pageLoadHide').css({display:'block'});

		$('#wo_save').unbind('click');
		$('#wo_save').attr('onclick','');
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
			document.getElementById("wo_assigned_user").options[0].value = SUPPORT_TEAM_ITOC_ID; 
			document.getElementById("wo_assigned_user").options[0].text = SUPPORT_TEAM_ITOC_NAME;
			
			document.getElementById("SEVERITY").options[0].value = 'disable'; 
			document.getElementById("INFRA_TYPE").options[0].value = 'disable';
	
			$('#wo_save').unbind('click');
			$('#wo_save').attr('onclick','');
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
			$('#wo_time_fade').css({display:'block'});
			document.getElementById("wo_assigned_user").options[0].value = SUPPORT_TEAM_ID; 
			document.getElementById("wo_assigned_user").options[0].text = SUPPORT_TEAM_NAME;
			document.getElementById("INFRA_TYPE").options[0].value = 'disable'; 
		}
		else if(reqType==RT_CHANGE)
		{
			$('#li_SEVERITY').css({display:'none'});
			$('#wo_time_fade').css({display:'none'});
			$('#li_REQ_DATE').css({display:'block'});
			$('#li_INFRA_TYPE').css({display:'block'});
			$('#li_CRITICAL').css({display:'block'});
			document.getElementById("wo_assigned_user").options[0].value = MAINTENANCE_TEAM_ID; 
			document.getElementById("wo_assigned_user").options[0].text = MAINTENANCE_TEAM_NAME;

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
				launchDate=new Date(parseInt(splitcurrDate[0],10),parseInt(splitcurrDate[1],10)-1,parseInt(splitcurrDate[2],10)+3,parseInt(splitcurrTime[0],10),parseInt(splitcurrTime[1],10));
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
			{
				document.getElementById('ampm').value =  'pm';
			}
			else
			{
				document.getElementById('ampm').value =  'am';
			}
			document.getElementById('time_sensitive_time').value =  Hrs12Frt;
			document.getElementById('estimated_completion_date').value =  document.getElementById('time_sensitive_date').value;			
		}
	}
}


function getRequestorsInfo(userId) {
	qString = '?action=entry&data=phone&user='+userId;
	qString2 = '?action=entry&data=email&user='+userId;
	qString3 = '?action=entry&data=name&user='+userId;
	//alert(theVal);
	//alert(frm.resource_type.value);
	
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

	//if(timeSens.checked) {
		estDate.value = launchDate.value;
		checkDateRange(startDate.id, launchDate.id);

}
function saveWorkOrder(theForm) {
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
	
	if(woREQ_TYPE == "_blank") {
		valid = false;
		document.getElementById('wo_request_type_label').style.color = "#FF0000";
	} else {
		document.getElementById('wo_request_type_label').style.color = "#34556C";
	}
	if(woSEVERITY == "_blank") {
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
		if(ampm == "0" || ampm == "") {
			valid = false;
			$("label[for='ampm']").css({'color':'#FF0000'});
		} else {
			$("label[for='ampm']").css({'color':'#34556C'});
		}
		var timeSensDateReg = /^(\d{2})\/(\d{2})\/(\d{4})$/;
		if(!timeSensDateReg.test(timeSensDate))
		{
			valid = false;
			$("label[for='time_sensitive_date']").css({'color':'#FF0000'});
		}

	if(woStatus == 6){
		
		var currentDate = new Date();
		var month = currentDate.getMonth()+1;
		var day = currentDate.getDate();
		var year = currentDate.getFullYear();
		var hours = currentDate.getHours();
		if(!timeSensDateReg.test(timeSensDate))
		{
			valid = false;
			$("label[for='time_sensitive_date']").css({'color':'#FF0000'});
		}else{
			var requestDate = timeSensDate.split("/");

			if(requestDate[2] >= year){
				if(requestDate[2] == year){
					if(requestDate[0] >= month){
						if(requestDate[0] == month){
							if(requestDate[1] >= day){
							
							}else{
								alert("Please enter current or future day");
								return;
							}
						}

					}else{
						alert("Please enter current or future month");
						return;
					}
				}	
			}else{
				alert("Please enter current or future year");
				return;
			}
			
		}	
		if(timeSensDate != "" && timeSensTime != "0" && timeSensTime != "" && ampm != "0" && ampm != "")
			{
					if(requestDate[1] == day){
						if(ampm == "am"){
							if(hours >= timeSensTime){
								alert("Please enter future time");
								return;
							}						
						}

						if(ampm == "pm"){
							if(hours >= (timeSensTime+12)){
								alert("Please enter future time");
								return;
							}
						}
				}
			}
	
	}   
		
	if(valid) {
		data = {woId:woId,dirName:dirName,requestedId:requestedId,projectId:projectId,woTypeId:woTypeId,priorityId:priorityId,timeSens:timeSens,timeSensDate:timeSensDate,timeSensTime:timeSensTime,ampm:ampm,woTitle:woTitle,woExampleURL:woExampleURL,woDesc:woDesc,woStatus:woStatus,woAssignedTo:woAssignedTo,woStartDate:woStartDate,woEstDate:woEstDate,rallyType:rallyType,rallyProject:rallyProject,rallyFlag:rallyFlag,woREQ_TYPE:woREQ_TYPE,woSEVERITY:woSEVERITY,woSITE_NAME:woSITE_NAME,woINFRA_TYPE:woINFRA_TYPE,woCRITICAL:woCRITICAL};

		qString += "woId="+woId+"&"+"dirName="+dirName+"&"+"requestedId="+requestedId+"&"+"projectId="+projectId+"&"
		+"woTypeId="+woTypeId+"&"+"priorityId="+priorityId+"&"+"timeSens="+timeSens+"&"
		+"timeSensDate="+timeSensDate+"&"+"timeSensTime="+timeSensTime+"&"+"ampm="+ampm+"&"
		+"woTitle="+woTitle+"&"+"woExampleURL="+woExampleURL+"&"
		+"woDesc="+woDesc+"&"+"woStatus="+woStatus+"&"+"woAssignedTo="+woAssignedTo+"&"
		+"woStartDate="+woStartDate+"&"+"woEstDate="+woEstDate+"&rallyType="+rallyType+"&rallyProject="+rallyProject+"&rallyFlag="+rallyFlag;

		$('#wo_dimmer').css({display:'block'});
		$('#wo_dimmer').css({backgroundColor:'#FFFFFF'});
		$('#wo_dimmer').css({opacity:'0.7'});
		$('#wo_dimmer').css({filter:'0.7'});
		$('#wo_dimmer_ajax').css({display:'block'});
		
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/save_wo.php",
			data: data,
			success: function(msg) {
				$('#wo_dimmer').css({display:'none'});
				$('#wo_dimmer_ajax').css({display:'none'});
				data2 = {woId:msg,dirName:msg,requestedId:requestedId,projectId:projectId,woTypeId:woTypeId,priorityId:priorityId,timeSens:timeSens,timeSensDate:timeSensDate,timeSensTime:timeSensTime,ampm:ampm,woTitle:woTitle,woExampleURL:woExampleURL,woDesc:woDesc,woStatus:woStatus,woAssignedTo:woAssignedTo,woStartDate:woStartDate,woEstDate:woEstDate};
				qString2 = "woId="+msg+"&"+"dirName="+msg+"/&"+"requestedId="+requestedId+"&"+"projectId="+projectId+"&"
					+"woTypeId="+woTypeId+"&"+"priorityId="+priorityId+"&"+"timeSens="+timeSens+"&"
					+"timeSensDate="+timeSensDate+"&"+"timeSensTime="+timeSensTime+"&"+"ampm="+ampm+"&"
					+"woTitle="+woTitle+"&"+"woExampleURL="+woExampleURL+"&"
					+"woDesc="+woDesc+"&"+"woStatus="+woStatus+"&"+"woAssignedTo="+woAssignedTo+"&"
					+"woStartDate="+woStartDate+"&"+"woEstDate="+woEstDate;

				if(parseInt(msg)) {

					$('#workorder_id').val(msg);
					$('#dirName').val(msg+"/");				

					if($('#workorder_id').val() != "") {
						$('#comment_dimmer').css({display:"none"});
					}
					
					if(woId == "") {
							Set_Cookie( "lighthouse_create_wo_data", projectId, "7", "/", "", "");
							Set_Cookie( "lighthouse_wo_data", '-1~' + projectId + '~-1~-1', "7", "/", "", "");
						window.location = '/workorders/';
					} else {
						$('.message_required p').html('The work order is saved successfully.');
						$('.message_required').css({display:'block'});
					}
				}
			},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
						alert("Error:" + textStatus + msg2 );
				}		
		});
		updateAssignedDate();
		updateEstimatedDate();
	} else {
		$('.message_required p').html('Please fill in the required fields?');
		$('.message_required').css({display:'block'});
	}
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
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/remove_uploaded_file.php",
		data: "id="+fileNum,
		success: function(msg) {
			//alert("Data Saved: " + msg)
		}
	});
	
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
function submitComment() {
	var woId = document.getElementById('workorder_id').value;
	var userId = document.getElementById('user_id').value;
	var comment = document.getElementById('comment').value;
	
	if(comment != "" && userId != "" && woId != "") {
		
		$.ajax({
			type: "POST",
			async: false,
			url: "/_ajaxphp/wo_save_comment.php",
			data: { woId : woId, userId : userId, comment : comment},
			success: function(msg) {
				$('#comments_list').html(msg) ;
				document.getElementById('comment').value = "";
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
			    alert("Error:" + textStatus); 
			}		
		});
	}else{	
		alert('error. comment was not saved');
	}
	
	updateComments();
	
	return false;
}
function addCcUser() {
	var woId = document.getElementById('workorder_id').value;
	var cc = document.getElementById('cc_user').value;
	
	document.getElementById('cclist').value += cc +",";
	
	var cclist = document.getElementById('cclist').value;
	//alert('add cc: '+woId+":"+cc);
	
	if(woId != "") {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/wo_save_cc.php",
			data: "woId="+woId+"&cc="+cclist,
			success: function(msg) {
				//alert(msg);
				document.getElementById('cc_list').innerHTML = msg;
			}
		});
	}
}
function removeCcUser(userId) {
	var woId = document.getElementById('workorder_id').value;
	//var cc = document.getElementById('cc_user').value;
	
	//document.getElementById('cclist').value += cc +",";
	var cclist = document.getElementById('cclist').value;
	//alert('add cc: '+woId+":"+cc);
	
	if(woId != "") {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/wo_save_cc.php",
			data: "woId="+woId+"&cc="+cclist+"&remove="+userId,
			success: function(msg) {
				//alert(msg);
				document.getElementById('cc_list').innerHTML = msg;
			}
		});
	}
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
				updateEstimatedDate();
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
				if(data == 'success'){
					updateFileList();
					$('.message_required p').html('File uploaded successfully.');
					$('.message_required').css({display:'block'});
				}else{
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
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/reopen_wo.php",
		data: "woId="+document.getElementById('workorder_id').value,
		success: function(msg) {
			document.getElementById('close_date').value=msg;
			window.location = '/workorders/';
		}
	});
}