var curSec;
var changeSection = false;
var uSec;
var uSecMan;
var nxt = false;
var draft = false;
var addPerm = false;
var quarterOneinitial;
var quarterTwoinitial;
var quarterThreeinitial;
var quarterFourinitial;
var percentageOneinitial;
var percentageTwoinitial;
var percentageThreeinitial;
var percentageFourinitial;
$(document).ready(function(){
	$('.status_dropdown').click(function(){
		if($(this).hasClass('up')){
			$('.project_status_list').css({display:'none'});
			$(this).removeClass('up');
		}else{
			$('.project_status_list').css({display:'block'});
			$(this).addClass('up');
		}
	});
/*	$('.project_status_list').mouseover(function () {
		$('.project_status_list').css({display:'block'});
	});
	$('.project_status_list').mouseout(function () {
		$('.project_status_list').css({display:'none'});
	});
*/
	$('.cur_for').formatCurrency();

	$('.close_add_risk').click(function(){
		$('.add_risk').css({display:'none'});
	});

	$('.wo_perms .woPermsAdd').mouseover(function () {
		$('#wo_add_perms').css({display: "block"});
	});
	//Set to none for hover effects. CSS style will also need to change to none
	//.wo_perms_disable, .wo_perms_enable { position: absolute; right: 10px; top: 12px; display: none; }
	$('.wo_perms .woPermsAdd').mouseout(function () {
		$('#wo_add_perms').css({display: "block"});
	});
	$('#companyPerms').change(function () {
		var compId = $(this).val();
		var query = "company="+compId;
		
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/userPermsList.php",
			data: query,
			success: function(msg) {
				if(msg != "") {
					$('#wo_perms_users').html(msg);
					$("#control_3").multiSelect();
				} else {
					var newOptions = "<select id=\"control_3\" name=\"control_3[]\" multiple=\"multiple\" size=\"5\"></select>";
					$('#wo_perms_users').html(newOptions);
					$("#control_3").multiSelect();
				}
			}
		});
	});
	$('#wo_add_perms').click(function () {
		addPerm = true;
		$('#form_sec_11').submit();
		return false;
	});
	
	//$('.remove_perms').click(function () {
	//	alert('you are deleting: '+$(this).attr('id'));
	//	return false;
	//});
	
	// Default options
	//$("#control_1, #control_3, #control_4, #control_5").multiSelect();
	$("#control_3").multiSelect();
	
	// With callback
	$("#control_3").multiSelect( null, function(el) {
		$("#callbackResult").show().fadeOut();
	});
	
	// Options displayed in comma-separated list
	$("#control_3").multiSelect({ oneOrMoreSelected: '*' });
	
	// 'Select All' text changed
	//$("#control_3").multiSelect({ selectAllText: 'Pick &lsquo;em all!', oneOrMoreSelected: '% Users' });
	
	// Show test data
	$("FORM").submit( function() {
		//if(addPerm) {
		var results = $(this).serialize();
		results = decodeURI(results);
		/* Commenting out the saveWoPerms as the permissions are given from the admin tab */
		saveWoPerms(results);
		//alert('save all perms: '+results);
		//addPerm = false;
		
		return false;
	});
	quarterOneinitial = document.getElementById('quarter1').value;
	quarterTwoinitial = document.getElementById('quarter2').value;
	quarterThreeinitial = document.getElementById('quarter3').value;
	quarterFourinitial = document.getElementById('quarter4').value;
	percentageOneinitial = document.getElementById('percentage1').value;
	percentageTwoinitial = document.getElementById('percentage2').value;
	percentageThreeinitial = document.getElementById('percentage3').value;
	percentageFourinitial = document.getElementById('percentage4').value;

	updatePermsDropDowns();
	setCompleteness();
});
function saveWoPerms(theRes) {
	//alert(theRes+"&projectId="+$('#project_id').val());
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/savePermsList.php",
		data: theRes+"&projectId="+$('#project_id').val(),
		success: function(msg) {
			$('#companyPerms').val(' ');
			$('#companyPerms').change();
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/updatePermsList.php",
				data: "projectId="+$('#project_id').val(),
				success: function(msg) {
					$('#perms_list').html(msg);
					updatePermsDropDowns();
					document.getElementById('ajax_loader').style.display = "none";
				}
			});
		}
	});
}
function deletePerm(theVal) {
	//alert("projectId="+$('#project_id').val()+"&compId="+theVal);
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/deletePerm.php",
		data: "projectId="+$('#project_id').val()+"&compId="+theVal,
		success: function(msg) {
			$('#companyPerms').val(' ');
			$('#companyPerms').change();
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/updatePermsList.php",
				data: "projectId="+$('#project_id').val(),
				success: function(msg) {
					$('#perms_list').html(msg);
					updatePermsDropDowns();
					document.getElementById('ajax_loader').style.display = "none";
				}
			});
		}
	});
}
function updatePermsDropDowns() {
	$('.wo_perms_users select').each(function () {
		//$("#control_1, #control_3, #control_4, #control_5").multiSelect();
		$("#"+$(this).attr('id')).multiSelect();
		
		// With callback
		$("#"+$(this).attr('id')).multiSelect( null, function(el) {
			$("#callbackResult").show().fadeOut();
		});
		
		// Options displayed in comma-separated list
		$("#"+$(this).attr('id')).multiSelect({ oneOrMoreSelected: '*' });
	});
}
function draftStatus() {
	draft = true;
}
function clearOnFocus(elm) {
	if(elm.value == "--title--" || elm.value == "--phone--") {
		elm.value="";
	}
}
function setCurrentSection() {
	if(document.getElementById('section_menu')) {
		var secDiv = document.getElementById('section_menu');
		var ListItems = secDiv.getElementsByTagName("li");
		
		for(i = 0; i < ListItems.length; i++) {
			var elem = ListItems[i];
			
			if(elem.className == "active" || elem.className == "alt active") {
				curSec = elem.id;
			}
		}
	}
}
function changeSectionStatus() {
	/*var curListItem = document.getElementById(curSec);
	var curImageStatus = curListItem.getElementsByTagName("img");
	curImageStatus.src = "/_images/green_status.gif";*/
	
	var secDiv = document.getElementById('section_menu');
	var ListItems = secDiv.getElementsByTagName("li");
	
	for(i = 0; i < ListItems.length; i++) {
		var elem = ListItems[i];
		
		if(elem.className == "alt active" || elem.className == "active") {
			uSec = elem.id;
			
			var query = 'section='+elem.id+'&compid='+document.getElementById('project_id').value;
			ajaxFunction(query,'change_status');
		}
	}
}
function changeSectionStatusMan(section) {
	uSecMan = section;
	var query = 'section='+section+'&compid='+document.getElementById('project_id').value;
	ajaxFunction(query,'change_status_man');
}
function setCompleteness() {
	var secDiv = document.getElementById('section_menu');
	var ListItems = secDiv.getElementsByTagName("li");
	
	var query = 'project_id='+document.getElementById('project_id').value;
	ajaxFunction(query,'set_completeness');
}
function setSectionComplete() {
	var secDiv = document.getElementById('section_menu');
	var ListItems = secDiv.getElementsByTagName("li");
	
	for(i = 0; i < ListItems.length; i++) {
		var elem = ListItems[i];
		
		if(elem.className == "alt active" || elem.className == "active") {
			uSec = elem.id;
			
			var query = 'section='+elem.id+'&compid='+document.getElementById('project_id').value;
			ajaxFunction(query,'complete_status');
		}
	}
}
function updateProjectPermission(){
	var projectRPPermission = 0;
	document.getElementById('ajax_loader').style.display = "block";
	if(document.getElementById('projectRPPermission').checked){
		projectRPPermission = 1;
	}
	var currentRPPermission = document.getElementById('currentRPPermission').value;

	projectWOPermission = 0;
	if(document.getElementById('projectWOPermission').checked){
		projectWOPermission = 1;
	}
	var currentWOPermission = document.getElementById('currentWOPermission').value;
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_project_permission.php?project_id="+document.getElementById('project_id').value+"&rppermission="+projectRPPermission+"&currentRPPermission="+currentRPPermission+"&wopermission="+projectWOPermission+"&currentWOPermission="+currentWOPermission,
		success: function(msg) {
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitch(secName) {
	var secDiv = document.getElementById('section_menu');
	var ListItems = secDiv.getElementsByTagName("li");
	var qString;
	var dString;
	var changeSection;

	var lst = document.getElementById('create_sections');
	var lstElements = lst.getElementsByTagName("li");
	
	//var nmPart = split('_', secName);
	var nmPart = curSec.split('_');
	var prevElement = "form_sec_" + nmPart[1];	
	
	var comp_id = document.getElementById('project_id').value;
	//alert(curSec);
	//FCKeditorAPI.GetInstance('descEditor').GetData()
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	switch(curSec) {
		case 'sec_1': {
			var lst = document.getElementById('create_sections');
			var lstElements = lst.getElementsByTagName("li");
			changeSection = true;
			
			var nxtId = "";
			
			var comp_id = document.getElementById('project_id').value;
			//qString = '?data_set=project_description&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('descEditor').GetData())+'&section='+curSec;
			//alert(document.getElementById('descEditor').value);
			//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data: "data_set=project_description&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('descEditor').GetData())+"&section="+curSec,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_1');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_2': {			
			document.getElementById('ajax_loader').style.display = "block";
			document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
			document.getElementById('ajax_loader').style.opacity = '0.7';
			document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
			var comp_id = document.getElementById('project_id').value;
			var complete = false;
			var rolesUl = document.getElementById('project_roles');
			var rolesLi = rolesUl.getElementsByTagName('li');
			var queryStr = "?action=save&project_id="+comp_id+"&";
			var delqueryStr = "?action=delete&project_id="+comp_id+"&";
			
			for(var i = 0; i < rolesLi.length; i++) {
				if(rolesLi[i].id != "") {
					var elem = rolesLi[i];
					var disabled = false;
					
					var formElms = elem.getElementsByTagName('input');
					var formElmsSelect = elem.getElementsByTagName('select');
					var divs = elem.getElementsByTagName('div');
					
					for(var z = 0; z < divs.length; z++) {
						if(divs[z].className == "dim") {
							if(divs[z].style.display == "block") {
								disabled = true;
							} else {
								disabled = false;
							}
						}
					}
					
					if(!disabled) {
						for(var x = 0; x < formElms.length; x++) {
							var inputVal = formElms[x].value;
							if(formElms[x].value != "" && formElms[x].name != "resource_type" && formElms[x].name != "required") {
								complete = true;
							}
							switch(formElms[x].name) {
								case 'resource_type': {
									queryStr += "roles["+elem.id+"][resource_type]="+formElms[x].value+'&';
									break;
								}
								//case 'user': {
								//	queryStr += "roles["+elem.id+"][user]="+formElms[x].value+'&';
								//	break;
								//}
								case 'email': {
									queryStr += "roles["+elem.id+"][email]="+formElms[x].value+'&';
									break;
								}
								case 'phone': {
									queryStr += "roles["+elem.id+"][phone]="+formElms[x].value+'&';
									break;
								}
							}
						}
						if(complete) {
							var complete_text = "complete=1&section="+curSec+"&";
						} else {
							var complete_text = "complete=0&section="+curSec+"&";
						}
						queryStr += "roles["+elem.id+"][user]="+formElmsSelect[0].value+'&'+complete_text;
					} 
					else {
						for(var x = 0; x < formElms.length; x++) {
							var inputVal = formElms[x].value;
							
							switch(formElms[x].name) {
								case 'resource_type': {
									delqueryStr += "roles["+elem.id+"][resource_type]="+formElms[x].value+'&';
									break;
								}
								//case 'user': {
								//	queryStr += "roles["+elem.id+"][user]="+formElms[x].value+'&';
								//	break;
								//}
								case 'email': {
									delqueryStr += "roles["+elem.id+"][email]="+formElms[x].value+'&';
									break;
								}
								case 'phone': {
									delqueryStr += "roles["+elem.id+"][phone]="+formElms[x].value+'&';
									break;
								}
							}
						}
						delqueryStr += "roles["+elem.id+"][user]="+formElmsSelect[0].value+'&';
					}
				}
			}
			
			//alert(queryStr);
			//alert(delqueryStr);
			
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_roles.php"+queryStr,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/update_roles.php"+delqueryStr,
						success: function(msg) {
							changeSectionStatusMan('sec_2');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=roles&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_2');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					
					changeSectionStatusMan('sec_2');
					setCompleteness();
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_3': {
			var comp_id = document.getElementById('project_id').value;
			var query = "?action=save&project_id="+comp_id+"&";
			var delquery  = "?action=delete&project_id="+comp_id+"&";
			var complete = false;
			var cont = true;
			document.getElementById('ajax_loader').style.display = "block";
			document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
			document.getElementById('ajax_loader').style.opacity = '0.7';
			document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
			
			var timelineList = document.getElementById('project_timeline');
			var listItems = timelineList.getElementsByTagName('li');
			
			for(var i = 0; i < listItems.length; i++) {
				if(listItems[i].style.display != "none") {
					var liInputs = listItems[i].getElementsByTagName('input');
					
					for(var j = 0; j < liInputs.length; j++) {
						if(liInputs[j].value != "" && liInputs[j].name != "phase") {
							complete = true;
						}
						switch(liInputs[j].name) {
							case 'phase': {
								query += "phase["+i+"][id]=" + liInputs[j].value + "&";
								break;
							}
							case 'start_date': {
								if(liInputs[j].style.color == "rgb(255, 0, 0)") {
									cont = false;
								}
								query += "phase["+i+"][start]=" + liInputs[j].value + "&";
								break;
							}
							case 'projected_date': {
								if(liInputs[j].style.color == "rgb(255, 0, 0)") {
									cont = false;
								}
								query += "phase["+i+"][end]=" + liInputs[j].value + "&";
								break;
							}
						}
					}
				} else {
					var liInputs = listItems[i].getElementsByTagName('input');
					
					for(var j = 0; j < liInputs.length; j++) {
						if(liInputs[j].value != "" && liInputs[j].name != "phase") {
							complete = true;
						}
						switch(liInputs[j].name) {
							case 'phase': {
								delquery += "phase["+i+"][id]=" + liInputs[j].value + "&";
								break;
							}
							case 'start_date': {
								if(liInputs[j].style.color == "rgb(255, 0, 0)") {
									cont = false;
								}
								delquery += "phase["+i+"][start]=" + liInputs[j].value + "&";
								break;
							}
							case 'projected_date': {
								if(liInputs[j].style.color == "rgb(255, 0, 0)") {
									cont = false;
								}
								delquery += "phase["+i+"][end]=" + liInputs[j].value + "&";
								break;
							}
						}
					}
				}
			}
			if(complete) {
				var complete_text = "complete=1&section="+curSec+"&";
			} else {
				var complete_text = "complete=0&section="+curSec+"&";
			}
			
			//alert(query);
			//alert(delquery);
			if(!cont) {
				$('.message_timeline_date').css({display:'block'});
				document.getElementById('ajax_loader').style.display = "none";
				return false;
			} else {
				$.ajax({
					type: "GET",
					url: "/_ajaxphp/update_timeline.php"+query+complete_text,
					success: function(msg) {
						$.ajax({
							type: "GET",
							url: "/_ajaxphp/update_timeline.php"+delquery+complete_text,
							success: function(msg) {
								changeSectionStatusMan('sec_3');
								setCompleteness();
								
								document.getElementById('ajax_loader').style.display = "none";
							}
						});
						
						$.ajax({
							type: "GET",
							url: "/_ajaxphp/check_status.php?section=timeline&comp_id="+comp_id+"&cursec="+curSec,
							success: function(msg) {
								changeSectionStatusMan('sec_3');
								setCompleteness();
								
								document.getElementById('ajax_loader').style.display = "none";
							}
						});
						
						changeSectionStatusMan('sec_2');
						setCompleteness();
						changeSectionStatus();
						
						for(var i = 0; i < lstElements.length; i++) {
							var elem = lstElements[i];
							if((i%2) == 0) {
								if(elem.id == secName) {
									elem.className = "alt active";
								} else {
									elem.className = "alt";
								}
							} else {
								if(elem.id == secName) {
									elem.className = "active";
								} else {
									elem.className = "";
								}
							}
						}
						
						ctCreateDisplaySection(secName);
						setCurrentSection();
					}
				});
			}
			break;
		}
		case 'sec_4': {
			var lst = document.getElementById('create_sections');
			var lstElements = lst.getElementsByTagName("li");
			changeSection = true;
			
			var nxtId = "";
			
			var comp_id = document.getElementById('project_id').value;
			//qString = '?data_set=project_scope&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('scopeEditor').GetData())+'&section='+curSec;
			//alert(document.getElementById('descEditor').value);
			//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data: "data_set=project_scope&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('scopeEditor').GetData())+"&section="+curSec,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=bcase&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_4');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_5': {
			changeSectionStatusMan('sec_5');
			var lst = document.getElementById('create_sections');
			var lstElements = lst.getElementsByTagName("li");
			
			for(var i = 0; i < lstElements.length; i++) {
				var elem = lstElements[i];
				if((i%2) == 0) {
					if(elem.id == secName) {
						elem.className = "alt active";
					} else {
						elem.className = "alt";
					}
				} else {
					if(elem.id == secName) {
						elem.className = "active";
					} else {
						elem.className = "";
					}
				}
			}
			
			ctCreateDisplaySection(secName);
			setCurrentSection();
			document.getElementById('ajax_loader').style.display = "none";
			
			break;
		}
		case 'sec_6': {
			var comp_id = document.getElementById('project_id').value;
			var budget_code = document.getElementById('fin_budget_code').value;
			var query = "?action=save&project_id="+comp_id+"&budget_code="+budget_code+"&";
			var delquery = "?action=delete&project_id="+comp_id+"&budget_code="+budget_code+"&";
			var del = false;
			var complete = false;
			document.getElementById('ajax_loader').style.display = "block";
			document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
			document.getElementById('ajax_loader').style.opacity = '0.7';
			document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
			
			var block_finance = document.getElementById('finance_calcs');
			var block_list = block_finance.getElementsByTagName('li');
			
			for(var x = 0; x < block_list.length; x++) {
				var block_inputs = block_list[x].getElementsByTagName('input');
				var div_container = block_list[x].getElementsByTagName('div');
				
				for(var y = 0; y < div_container.length; y++) {
					if(div_container[y].className == "finance_budget") {
						if(div_container[y].style.display == "none") {
							del = true;
						} else {
							del = false;
						}
					}
				}
				
				if(!del) {
					for(var i = 0; i < block_inputs.length; i++) {
						if(block_inputs[i].value != "" && block_inputs[i].name != "phase" && block_inputs[i].name != "total") {
							complete = true;
						}
						switch(block_inputs[i].id) {
							case 'phase': {
								query += "finance["+x+"][phase]="+block_inputs[i].value+"&";
								break;
							}
							case 'hours': {
								query += "finance["+x+"][hours]="+block_inputs[i].value+"&";
								break;
							}
							case 'rate': {
								query += "finance["+x+"][rate]="+block_inputs[i].value+"&";
								break;
							}
						}
					}
				}
				else {
					for(var i = 0; i < block_inputs.length; i++) {
						switch(block_inputs[i].id) {
							case 'phase': {
								delquery += "finance["+x+"][phase]="+block_inputs[i].value+"&";
								break;
							}
							case 'hours': {
								delquery += "finance["+x+"][hours]="+block_inputs[i].value+"&";
								break;
							}
							case 'rate': {
								delquery += "finance["+x+"][rate]="+block_inputs[i].value+"&";
								break;
							}
						}
					}
				}
			}
			
			if(complete) {
				var complete_text = "complete=1&section="+curSec;
			} else {
				var complete_text = "complete=0&section="+curSec;
			}
			//alert(query+complete_text);
			//alert(delquery+complete_text);
			
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_finance.php"+query+complete_text,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/update_finance.php"+delquery+complete_text,
						success: function(msg) {
							changeSectionStatusMan('sec_6');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=roles&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_6');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					
					changeSectionStatusMan('sec_6');
					setCompleteness();
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_7': {
			var lst = document.getElementById('create_sections');
			var lstElements = lst.getElementsByTagName("li");
			changeSection = true;
			
			var nxtId = "";
			
			var comp_id = document.getElementById('project_id').value;
			//qString = '?data_set=project_deliverables&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('deliverEditor').GetData())+'&section='+curSec;
			//alert(document.getElementById('descEditor').value);
			//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data: "data_set=project_deliverables&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('deliverEditor').GetData())+"&section="+curSec,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=deliver&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_7');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_8': {
			var lst = document.getElementById('create_sections');
			var lstElements = lst.getElementsByTagName("li");
			changeSection = true;
			
			var nxtId = "";			
			var comp_id = document.getElementById('project_id').value;
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data: "data_set=project_metrics&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('metricsEditor').GetData())+"&section="+curSec,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=metrics&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_8');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_9': {
			var complete = false;
			document.getElementById('ajax_loader').style.display = "block";
			document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
			document.getElementById('ajax_loader').style.opacity = '0.7';
			document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
			
			if(draft) {
				var status = "&status=true;";
				draft = false;
			} else {
				var status = "";
			}
			
			var comp_id = document.getElementById('project_id').value;
			var query = "?action=save&project_id="+comp_id+"&";
			var delquery = "?action=delete&project_id="+comp_id+"&";
			
			var appr_block = document.getElementById('approvals');
			var apprList = appr_block.getElementsByTagName('li');
			
			for(var i = 0; i < apprList.length; i++) {
				if(apprList[i].style.display != "none") {
					var inputLst = apprList[i].getElementsByTagName('input');
					var selectLst = apprList[i].getElementsByTagName('select');
					
					for(var y = 0; y < selectLst.length; y++) {
							query += "appr["+i+"][name]="+selectLst[y].value+"&";
					}
					
					for(var x = 0; x < inputLst.length; x++) {
						if(inputLst[x].value != "" && inputLst[x].id != "phase" && inputLst[x].id != "approved") {
							complete = true;
						}
						switch(inputLst[x].name) {
							case 'phase': {
								query += "appr["+i+"][phase]="+inputLst[x].value+"&";
								break;
							}
							case 'user_name': {
								query += "appr["+i+"][name]="+inputLst[x].value+"&";
								break;
							}
							case 'user_title': {
								if(inputLst[x].value == "--title--") {
									query += "appr["+i+"][title]=&";
								} else {
									query += "appr["+i+"][title]="+inputLst[x].value+"&";
								}
								break;
							}
							case 'user_phone': {
								if(inputLst[x].value == "--phone--") {
									query += "appr["+i+"][phone]=&";
								} else {
									query += "appr["+i+"][phone]="+inputLst[x].value+"&";
								}
								break;
							}
							case 'approved': {
								if(inputLst[x].checked) {
									query += "appr["+i+"][approved]=yes&";
								} else {
									query += "appr["+i+"][approved]=no&";
								}
								break;
							}
							case 'approval_date': {
								query += "appr["+i+"][date]="+inputLst[x].value+"&";
								break;
							}
						}
						
					}
				}
				else {
					var inputLst = apprList[i].getElementsByTagName('input');
					var selectLst = apprList[i].getElementsByTagName('select');
					
					for(var y = 0; y < selectLst.length; y++) {
							delquery += "appr["+i+"][name]="+selectLst[y].value+"&";
					}
					for(var x = 0; x < inputLst.length; x++) {
						switch(inputLst[x].name) {
							case 'phase': {
								delquery += "appr["+i+"][phase]="+inputLst[x].value+"&";
								break;
							}
							case 'user_name': {
								delquery += "appr["+i+"][name]="+inputLst[x].value+"&";
								break;
							}
							case 'user_title': {
								delquery += "appr["+i+"][title]="+inputLst[x].value+"&";
								break;
							}
							case 'user_phone': {
								delquery += "appr["+i+"][phone]="+inputLst[x].value+"&";
								break;
							}
							case 'approved': {
								if(inputLst[x].checked) {
									delquery += "appr["+i+"][approved]=yes&";
								} else {
									delquery += "appr["+i+"][approved]=no&";
								}
								break;
							}
							case 'approval_date': {
								delquery += "appr["+i+"][date]="+inputLst[x].value+"&";
								break;
							}
						}
						
					}
				}
			}
			
			if(complete) {
				var complete_text = "complete=1&section="+curSec;
			} else {
				var complete_text = "complete=0&section="+curSec;
			}
			
			//alert(query+complete_text);
			//alert(delquery+complete_text);
			
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_approvals.php"+query+complete_text+status,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=roles&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_9');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					
					changeSectionStatusMan('sec_9');
					setCompleteness();
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_10': {
			var lst = document.getElementById('create_sections');
			var lstElements = lst.getElementsByTagName("li");
			changeSection = true;
			
			var nxtId = "";
			
			var comp_id = document.getElementById('project_id').value;
			//qString = '?data_set=project_bcase&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('bcaseEditor').GetData())+'&section='+curSec;
			//alert(document.getElementById('descEditor').value);
			//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data: "data_set=project_bcase&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('bcaseEditor').GetData())+"&section="+curSec,
				success: function(msg) {
					$.ajax({
						type: "GET",
						url: "/_ajaxphp/check_status.php?section=bcase&comp_id="+comp_id+"&cursec="+curSec,
						success: function(msg) {
							changeSectionStatusMan('sec_10');
							setCompleteness();
							
							document.getElementById('ajax_loader').style.display = "none";
						}
					});
					changeSectionStatus();
					
					for(var i = 0; i < lstElements.length; i++) {
						var elem = lstElements[i];
						if((i%2) == 0) {
							if(elem.id == secName) {
								elem.className = "alt active";
							} else {
								elem.className = "alt";
							}
						} else {
							if(elem.id == secName) {
								elem.className = "active";
							} else {
								elem.className = "";
							}
						}
					}
					
					ctCreateDisplaySection(secName);
					setCurrentSection();
				}
			});
			
			break;
		}
		case 'sec_11': {
			var lst = document.getElementById('create_sections');
			var lstElements = lst.getElementsByTagName("li");
			changeSection = true;
			
			var nxtId = "";
			
			var comp_id = document.getElementById('project_id').value;
			qString = 'data_set=project_bcase&comp_id='+comp_id+'&desc='+escateQuates(FCKeditorAPI.GetInstance('bcaseEditor').GetData())+'&section='+curSec;
			//alert(document.getElementById('descEditor').value);
			//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
			
			changeSectionStatusMan('sec_11');
			setCompleteness();
			
			document.getElementById('ajax_loader').style.display = "none";
			
			changeSectionStatus();
					
			for(var i = 0; i < lstElements.length; i++) {
				var elem = lstElements[i];
				if((i%2) == 0) {
					if(elem.id == secName) {
						elem.className = "alt active";
					} else {
						elem.className = "alt";
					}
				} else {
					if(elem.id == secName) {
						elem.className = "active";
					} else {
						elem.className = "";
					}
				}
			}
			
			ctCreateDisplaySection(secName);
			setCurrentSection();
			
			break;
		}
	}
	
	return false;
}
function ctCreateSectionsSwitchNext() {
	var lst = document.getElementById('create_sections');
	var lstElements = lst.getElementsByTagName("li");
	changeSection = true;
	
	changeSectionStatus();
	
	var nxtId = "";
	for(var i = 0; i < lstElements.length; i++) {
		var elem = lstElements[i];
		var nxt = i+1;
		
		if(lstElements[(lstElements.length-1)].className == "active" 
			|| lstElements[(lstElements.length-1)].className == "alt active") {
			break;
		}
		
		if((i%2) == 0) {
			if(elem.className == "alt active") {
				elem.className = "alt";
				nxtId = lstElements[nxt].id;
			}
			if(elem.id == nxtId) {
				elem.className = "alt active";
			} 
		} else {
			if(elem.className == "active") {
				elem.className = "";
				nxtId = lstElements[nxt].id;
			}
			if(elem.id == nxtId) {
				elem.className = "active";
			}
		}
	}
	//alert(nxtId);
	if(nxtId != "") {
		ctCreateDisplaySection(nxtId);
	}
	setCurrentSection();
}
function nxtFalse() {
	if(nxt) {
		nxt = false;
	}
}
function ctCreateSectionsSwitchNextDesc() {
	var lst = document.getElementById('create_sections');
	var lstElements = lst.getElementsByTagName("li");
	changeSection = true;
	
	var nxtId = "";
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var comp_id = document.getElementById('project_id').value;
	//qString = '?data_set=project_description&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('descEditor').GetData())+'&section='+curSec;
	//alert(document.getElementById('descEditor').value);
	//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_project.php",
		data: "data_set=project_description&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('descEditor').GetData())+"&section="+curSec,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_1');
						setCompleteness();
						if(!nxt) {
							nxt = true;
							
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitchNextBcase() {
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var lst = document.getElementById('create_sections');
	var lstElements = lst.getElementsByTagName("li");
	changeSection = true;
	
	var nxtId = "";
	
	var comp_id = document.getElementById('project_id').value;
	//qString = '?data_set=project_bcase&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('bcaseEditor').GetData())+'&section='+curSec;
	//alert(document.getElementById('descEditor').value);
	//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_project.php",
		data: "data_set=project_bcase&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('bcaseEditor').GetData())+"&section="+curSec,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=bcase&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_10');
						setCompleteness();
						if(!nxt) {
							nxt = true;
							
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitchNextScope() {
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var lst = document.getElementById('create_sections');
	var lstElements = lst.getElementsByTagName("li");
	changeSection = true;
	
	var nxtId = "";
	
	var comp_id = document.getElementById('project_id').value;
	//qString = '?data_set=project_scope&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('scopeEditor').GetData())+'&section='+curSec;
	//alert(document.getElementById('descEditor').value);
	//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_project.php",
		data: "data_set=project_scope&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('scopeEditor').GetData())+"&section="+curSec,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=bcase&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_4');
						setCompleteness();
						if(!nxt) {
							nxt = true;
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitchNextDeliver() {
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var lst = document.getElementById('create_sections');
	var lstElements = lst.getElementsByTagName("li");
	changeSection = true;
	
	var nxtId = "";
	
	var comp_id = document.getElementById('project_id').value;
	//qString = '?data_set=project_deliverables&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('deliverEditor').GetData())+'&section='+curSec;
	//alert(document.getElementById('descEditor').value);
	//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_project.php",
		data: "data_set=project_deliverables&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('deliverEditor').GetData())+"&section="+curSec,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=bcase&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_7');
						setCompleteness();
						if(!nxt) {
							nxt = true;
							
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitchNextMetrics() {
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var lst = document.getElementById('create_sections');
	var lstElements = lst.getElementsByTagName("li");
	changeSection = true;
	
	var nxtId = "";
	
	var comp_id = document.getElementById('project_id').value;
	//qString = '?data_set=project_metrics&comp_id='+comp_id+'&desc='+base64_encode(FCKeditorAPI.GetInstance('metricsEditor').GetData())+'&section='+curSec;
	//alert(document.getElementById('descEditor').value);
	//alert("check_status.php?section=desc&comp_id="+comp_id+"&cursec="+curSec);
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/update_project.php",
		data: "data_set=project_metrics&comp_id="+comp_id+"&desc="+escateQuates(FCKeditorAPI.GetInstance('metricsEditor').GetData())+"&section="+curSec,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=bcase&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_8');
						setCompleteness();
						if(!nxt) {
							nxt = true;
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitchNextRoles() {
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var comp_id = document.getElementById('project_id').value;
	var complete = false;
	var rolesUl = document.getElementById('project_roles');
	var rolesLi = rolesUl.getElementsByTagName('li');
	var queryStr = "?action=save&project_id="+comp_id+"&";
	var delqueryStr = "?action=delete&project_id="+comp_id+"&";
	
	for(var i = 0; i < rolesLi.length; i++) {
		if(rolesLi[i].id != "") {
			var elem = rolesLi[i];
			var disabled = false;
			
			var formElms = elem.getElementsByTagName('input');
			var formElmsSelect = elem.getElementsByTagName('select');
			var divs = elem.getElementsByTagName('div');
			
			for(var z = 0; z < divs.length; z++) {
				if(divs[z].className == "dim") {
					if(divs[z].style.display == "block") {
						disabled = true;
					} else {
						disabled = false;
					}
				}
			}
			
			if(!disabled) {
				for(var x = 0; x < formElms.length; x++) {
					var inputVal = formElms[x].value;
					if(formElms[x].value != "" && formElms[x].name != "resource_type" && formElms[x].name != "required") {
						complete = true;
					}
					switch(formElms[x].name) {
						case 'resource_type': {
							queryStr += "roles["+elem.id+"][resource_type]="+formElms[x].value+'&';
							break;
						}
						//case 'user': {
						//	queryStr += "roles["+elem.id+"][user]="+formElms[x].value+'&';
						//	break;
						//}
						case 'email': {
							queryStr += "roles["+elem.id+"][email]="+formElms[x].value+'&';
							break;
						}
						case 'phone': {
							queryStr += "roles["+elem.id+"][phone]="+formElms[x].value+'&';
							break;
						}
					}
				}
				if(complete) {
					var complete_text = "complete=1&section="+curSec+"&";
				} else {
					var complete_text = "complete=0&section="+curSec+"&";
				}
				queryStr += "roles["+elem.id+"][user]="+formElmsSelect[0].value+'&'+complete_text;
			} 
			else {
				for(var x = 0; x < formElms.length; x++) {
					var inputVal = formElms[x].value;
					
					switch(formElms[x].name) {
						case 'resource_type': {
							delqueryStr += "roles["+elem.id+"][resource_type]="+formElms[x].value+'&';
							break;
						}
						//case 'user': {
						//	queryStr += "roles["+elem.id+"][user]="+formElms[x].value+'&';
						//	break;
						//}
						case 'email': {
							delqueryStr += "roles["+elem.id+"][email]="+formElms[x].value+'&';
							break;
						}
						case 'phone': {
							delqueryStr += "roles["+elem.id+"][phone]="+formElms[x].value+'&';
							break;
						}
					}
				}
				delqueryStr += "roles["+elem.id+"][user]="+formElmsSelect[0].value+'&';
			}
		}
	}
	//alert(queryStr);
	//alert(delqueryStr);
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+queryStr,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_roles.php"+delqueryStr,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_2');
						setCompleteness();
						if(!nxt) {
							nxt = true;
						}
					}
				}
			});
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=roles&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_2');
						setCompleteness();
						if(!nxt) {
							nxt = true;
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitchNextTimeline() {
	var comp_id = document.getElementById('project_id').value;
	var query = "?action=save&project_id="+comp_id+"&";
	var delquery  = "?action=delete&project_id="+comp_id+"&";
	var complete = false;
	var cont = true;
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var timelineList = document.getElementById('project_timeline');
	var listItems = timelineList.getElementsByTagName('li');
	
	for(var i = 0; i < listItems.length; i++) {
		if(listItems[i].style.display != "none") {
			var liInputs = listItems[i].getElementsByTagName('input');
			
			for(var j = 0; j < liInputs.length; j++) {
				if(liInputs[j].value != "" && liInputs[j].name != "phase") {
					complete = true;
				}
				switch(liInputs[j].name) {
					case 'phase': {
						query += "phase["+i+"][id]=" + liInputs[j].value + "&";
						break;
					}
					case 'start_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						query += "phase["+i+"][start]=" + liInputs[j].value + "&";
						break;
					}
					case 'projected_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						query += "phase["+i+"][end]=" + liInputs[j].value + "&";
						break;
					}
				}
			}
		} else {
			var liInputs = listItems[i].getElementsByTagName('input');
			
			for(var j = 0; j < liInputs.length; j++) {
				if(liInputs[j].value != "" && liInputs[j].name != "phase") {
					complete = true;
				}
				switch(liInputs[j].name) {
					case 'phase': {
						delquery += "phase["+i+"][id]=" + liInputs[j].value + "&";
						break;
					}
					case 'start_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						delquery += "phase["+i+"][start]=" + liInputs[j].value + "&";
						break;
					}
					case 'projected_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						delquery += "phase["+i+"][end]=" + liInputs[j].value + "&";
						break;
					}
				}
			}
		}
	}
	if(complete) {
		var complete_text = "complete=1&section="+curSec+"&";
	} else {
		var complete_text = "complete=0&section="+curSec+"&";
	}
	
	//alert(query);
	//alert(delquery);
	
	if(!cont) {
		$('.message_timeline_date').css({display:'block'});
		document.getElementById('ajax_loader').style.display = "none";
		return false;
	} else {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/update_timeline.php"+query+complete_text,
			success: function(msg) {
				$.ajax({
					type: "GET",
					url: "/_ajaxphp/update_timeline.php"+delquery+complete_text,
					success: function(msg) {
						if(msg == 1) {
							changeSectionStatusMan('sec_2');
							setCompleteness();
							if(!nxt) {
								nxt = true;
							}
						}
					}
				});
				$.ajax({
					type: "GET",
					url: "/_ajaxphp/check_status.php?section=timeline&comp_id="+comp_id+"&cursec="+curSec,
					success: function(msg) {
						if(msg == 1) {
							changeSectionStatusMan('sec_2');
							setCompleteness();
							if(!nxt) {
								nxt = true;
							}
						}
					}
				});
				
				changeSectionStatus();
				ctCreateSectionsSwitchNext();
				document.getElementById('ajax_loader').style.display = "none";
			}
		});
	}
}
function ctCreateSectionsSwitchNextFinance() {
	var comp_id = document.getElementById('project_id').value;
	var budget_code = document.getElementById('fin_budget_code').value;
	var query = "?action=save&project_id="+comp_id+"&budget_code="+budget_code+"&";
	var delquery = "?action=delete&project_id="+comp_id+"&budget_code="+budget_code+"&";
	var del = false;
	var complete = false;
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var block_finance = document.getElementById('finance_calcs');
	var block_list = block_finance.getElementsByTagName('li');
	
	for(var x = 0; x < block_list.length; x++) {
		var block_inputs = block_list[x].getElementsByTagName('input');
		var div_container = block_list[x].getElementsByTagName('div');
		
		for(var y = 0; y < div_container.length; y++) {
			if(div_container[y].className == "finance_budget") {
				if(div_container[y].style.display == "none") {
					del = true;
				} else {
					del = false;
				}
			}
		}
		
		if(!del) {
			for(var i = 0; i < block_inputs.length; i++) {
				if(block_inputs[i].value != "" && block_inputs[i].name != "phase" && block_inputs[i].name != "total") {
					complete = true;
				}
				switch(block_inputs[i].id) {
					case 'phase': {
						query += "finance["+x+"][phase]="+block_inputs[i].value+"&";
						break;
					}
					case 'hours': {
						query += "finance["+x+"][hours]="+block_inputs[i].value+"&";
						break;
					}
					case 'rate': {
						query += "finance["+x+"][rate]="+block_inputs[i].value+"&";
						break;
					}
				}
			}
		}
		else {
			for(var i = 0; i < block_inputs.length; i++) {
				switch(block_inputs[i].id) {
					case 'phase': {
						delquery += "finance["+x+"][phase]="+block_inputs[i].value+"&";
						break;
					}
					case 'hours': {
						delquery += "finance["+x+"][hours]="+block_inputs[i].value+"&";
						break;
					}
					case 'rate': {
						delquery += "finance["+x+"][rate]="+block_inputs[i].value+"&";
						break;
					}
				}
			}
		}
	}
	
	if(complete) {
		var complete_text = "complete=1&section="+curSec;
	} else {
		var complete_text = "complete=0&section="+curSec;
	}
	//alert(query+complete_text);
	//alert(delquery+complete_text);
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_finance.php"+query+complete_text,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_finance.php"+delquery+complete_text,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_6');
						setCompleteness();
						if(!nxt) {
							nxt = true;
						}
					}
				}
			});
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=finance&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_6');
						setCompleteness();
						if(!nxt) {
							nxt = true;
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateSectionsSwitchNextApprovals() {
	var complete = false;
	document.getElementById('ajax_loader').style.display = 'block';
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var comp_id = document.getElementById('project_id').value;
	var query = "?action=save&project_id="+comp_id+"&";
	var delquery = "?action=delete&project_id="+comp_id+"&";
	
	var appr_block = document.getElementById('approvals');
	var apprList = appr_block.getElementsByTagName('li');
	
	for(var i = 0; i < apprList.length; i++) {
		if(apprList[i].style.display != "none") {
			var inputLst = apprList[i].getElementsByTagName('input');
			for(var x = 0; x < inputLst.length; x++) {
				if(inputLst[x].value != "" && inputLst[x].id != "phase" && inputLst[x].id != "approved") {
					complete = true;
				}
				switch(inputLst[x].name) {
					case 'phase': {
						query += "appr["+i+"][phase]="+inputLst[x].value+"&";
						break;
					}
					case 'user_name': {
						query += "appr["+i+"][name]="+inputLst[x].value+"&";
						break;
					}
					case 'user_title': {
						query += "appr["+i+"][title]="+inputLst[x].value+"&";
						break;
					}
					case 'user_phone': {
						query += "appr["+i+"][phone]="+inputLst[x].value+"&";
						break;
					}
					case 'approved': {
						if(inputLst[x].checked) {
							query += "appr["+i+"][approved]=yes&";
						} else {
							query += "appr["+i+"][approved]=no&";
						}
						break;
					}
					case 'approval_date': {
						query += "appr["+i+"][date]="+inputLst[x].value+"&";
						break;
					}
				}
				
			}
		}
		else {
			var inputLst = apprList[i].getElementsByTagName('input');
			for(var x = 0; x < inputLst.length; x++) {
				switch(inputLst[x].name) {
					case 'phase': {
						delquery += "appr["+i+"][phase]="+inputLst[x].value+"&";
						break;
					}
					case 'user_name': {
						delquery += "appr["+i+"][name]="+inputLst[x].value+"&";
						break;
					}
					case 'user_title': {
						delquery += "appr["+i+"][title]="+inputLst[x].value+"&";
						break;
					}
					case 'user_phone': {
						delquery += "appr["+i+"][phone]="+inputLst[x].value+"&";
						break;
					}
					case 'approved': {
						if(inputLst[x].checked) {
							delquery += "appr["+i+"][approved]=yes&";
						} else {
							delquery += "appr["+i+"][approved]=no&";
						}
						break;
					}
					case 'approval_date': {
						delquery += "appr["+i+"][date]="+inputLst[x].value+"&";
						break;
					}
				}
				
			}
		}
	}
	
	if(complete) {
		var complete_text = "complete=1&section="+curSec;
	} else {
		var complete_text = "complete=0&section="+curSec;
	}
	
	//alert(query+complete_text);
	//alert(delquery+complete_text);
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_approvals.php"+query+complete_text,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/check_status.php?section=finance&comp_id="+comp_id+"&cursec="+curSec,
				success: function(msg) {
					if(msg == 1) {
						changeSectionStatusMan('sec_9');
						setCompleteness();
						if(!nxt) {
							nxt = true;
						}
					}
				}
			});
			changeSectionStatus();
			ctCreateSectionsSwitchNext();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ctCreateDisplaySection(secName) {	
	var frmName = "form_"+secName;
	var divs = document.getElementById('create_columns');
	var divList = divs.getElementsByTagName("div");
	
	for(var i = 0; i < divList.length; i++) {
		var elem = divList[i];
		
		if(elem.className == "rightCol") {
			if(elem.id == frmName) {
				elem.style.display = "block";
			} else {
				elem.style.display = "none";
			}
		}
	}
}

function clearFCK(instance) {
	FCKeditorAPI.GetInstance(instance).SetData("");
}
/*Section Clear Confirmations*/
function confirmEmptyRoles() {
	$('.message_clear_roles').css({display:'block'});
}
	/*Individual Roles Disabling*/
	function confirmDisableRole(roleId) {
		if($("#role" + roleId + "_btn").hasClass('prole_disable')) {
			//fadeDimmer(roleId);
			document.getElementById('single_role').value = roleId;
			$('.message_clear_role').css({display:'block'});
		} else {
			fadeDimmer(roleId);
		}
	}
function confirmEmptyTimeline() {
	$('.message_clear_timeline').css({display:'block'});
}
function confirmEmptyFilnance() {
	$('.message_clear_finance').css({display:'block'});
}
function confirmEmptyApprovals() {
	$('.message_clear_approvals').css({display:'block'});
}
/*FCK Editor Clear Confirmations*/
function confirmEmptyDesc() {
	$('.message_clear_desc').css({display:'block'});
}
function confirmEmptyBcase() {
	$('.message_clear_bcase').css({display:'block'});
}
function confirmEmptyScope() {
	$('.message_clear_scope').css({display:'block'});
}
function confirmEmptyDeliver() {
	$('.message_clear_deliver').css({display:'block'});
}
function confirmEmptyMetrics() {
	$('.message_clear_metrics').css({display:'block'});
}
function project_mode(un,pwd) {
	//alert('function called: '+projId);
	var comp = document.getElementById('create_company');
	var code = document.getElementById('projectCode');
	var name = document.getElementById('projectName');
	
	if(comp.value != '' && code.value != '' && name.value != '') {
		comp.disabled = true;
		code.disabled = true;
		name.disabled = true;
		
		var query = 'comp='+comp.value+'&code='+code.value+'&name='+name.value;
		
		ajaxFunction(query,'return');
		setCurrentSection();
	} else {
		alert('The projects company, code, and name are all required to continue');
	}
}
function project_mode_update(theForm) {
	//alert('function called: '+projId);
		
	var divs = document.getElementById('create_columns');
	var divList = divs.getElementsByTagName("div");
	var elem = document.getElementById('createProject');
	//alert('Num of elements: ' + divList.length);
	//alert(document.getElementById('project_id').value);
	
	if(document.getElementById("new_project_dimmer")) {
		document.getElementById("new_project_dimmer").style.display = "none";
	}
	
	for(i = 0; i < divList.length; i++) {
		//if(divList[i].className == "new_project_dimmer") {
		//	divList[i].style.display = "none";
		//}
		elem.className = "inactive";
		elem.disabled = true;
	}
	var comp = document.getElementById("create_company");
	var idx = comp.selectedIndex;
	var compText = comp.options[idx].text;
	
	var comp = document.getElementById("display_company");
	var code = document.getElementById("display_code");
	var name = document.getElementById("display_name");
	
	document.getElementById('cancel_button').style.display = "none";
	document.getElementById('back_button').style.display = "block";
	
	document.getElementById("project_create").style.display = "none";
	document.getElementById("project_progress").style.display = "block";
	
	document.getElementById("createcompany").style.display = "none";
	document.getElementById("create_code").style.display = "none";
	document.getElementById("create_name").style.display = "none";
	
	//comp.innerHTML = compText;
	//code.innerHTML = document.getElementById("projectCode").value;
	//name.innerHTML = document.getElementById("projectName").value;
	
	comp.innerHTML = compText + ': ' + document.getElementById("projectCode").value + ' - ' + document.getElementById("projectName").value;
	
	comp.style.display = "block";
	//code.style.display = "block";
	//name.style.display = "block";
}
function changeRoleUser(theVal, frm) {
	var qString = '?action=entry&data=phone&user='+theVal;
	var qString2 = '?action=entry&data=email&user='+theVal;
	var qString3 = '?action=entry&data=title&user='+theVal;
	var apprGo = false;
	//alert(theVal);
	//alert(frm.resource_type.value);
	
	var apprFrmId = 'appr_'+frm.phase_type.value;
	
	if($("#"+apprFrmId+" > #preselect").val() == "no") {
		apprGo = true;
		$("#"+apprFrmId+" .papprovals_name > #user_name > option[value='"+theVal+"']").attr("selected", "selected");
	} else if($("#"+apprFrmId+" .papprovals_name > #user_name").val() == "") {
		apprGo = true;
		$("#"+apprFrmId+" .papprovals_name > #user_name > option[value='"+theVal+"']").attr("selected", "selected");
	}
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString,
		success: function(msg) {
			frm.phone.value = msg;
			if(apprGo) {
				$("#"+apprFrmId+" .papprovals_phone > #user_phone").val(msg);
			}
		}
	});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString2,
		success: function(msg) {
			frm.email.value = msg;
		}
	});
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+qString3,
		success: function(msg) {
			if(apprGo) {
				$("#"+apprFrmId+" .papprovals_title > #user_title").val(msg);
			}
		}
	});
}
function clearRoles() {
	var list = document.getElementById('project_roles');
	var listItems = list.getElementsByTagName('li');
	
	for(var i = 0; i < listItems.length; i++) {
		if(listItems[i].style.display != "none") {
			var itemInputs = listItems[i].getElementsByTagName('input');
			var itemSelect = listItems[i].getElementsByTagName('select');
			
			for(x = 0; x < itemInputs.length; x++) {
				switch(itemInputs[x].name) {
					case 'email': {
						itemInputs[x].value = "";
						break;
					}
					case 'phone': {
						itemInputs[x].value = "";
						break;
					}
				}
			}
			
			itemSelect[0].options[0].selected = true;
		}
	}
}
function saveRoles() {
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var comp_id = document.getElementById('project_id').value;
	var complete = false;
	var rolesUl = document.getElementById('project_roles');
	var rolesLi = rolesUl.getElementsByTagName('li');
	var queryStr = "?action=save&project_id="+comp_id+"&";
	var delqueryStr = "?action=delete&project_id="+comp_id+"&";
	
	if(draft) {
		var status = "&status=true;";
		draft = false;
	} else {
		var status = "";
	}
	
	for(var i = 0; i < rolesLi.length; i++) {
		if(rolesLi[i].id != "") {
			var elem = rolesLi[i];
			var disabled = false;
			
			var formElms = elem.getElementsByTagName('input');
			var formElmsSelect = elem.getElementsByTagName('select');
			var divs = elem.getElementsByTagName('div');
			
			for(var z = 0; z < divs.length; z++) {
				if(divs[z].className == "dim") {
					if(divs[z].style.display == "block") {
						disabled = true;
					} else {
						disabled = false;
					}
				}
			}
			
			if(!disabled) {
				for(var x = 0; x < formElms.length; x++) {
					var inputVal = formElms[x].value;
					if(formElms[x].name != "resource_type" && formElms[x].name != "required") {
						complete = true;
					}
					switch(formElms[x].name) {
						case 'resource_type': {
							queryStr += "roles["+elem.id+"][resource_type]="+formElms[x].value+'&';
							break;
						}
						//case 'user': {
						//	queryStr += "roles["+elem.id+"][user]="+formElms[x].value+'&';
						//	break;
						//}
						case 'email': {
							queryStr += "roles["+elem.id+"][email]="+formElms[x].value+'&';
							break;
						}
						case 'phone': {
							queryStr += "roles["+elem.id+"][phone]="+formElms[x].value+'&';
							break;
						}
					}
				}
				if(complete) {
					var complete_text = "complete=1&section="+curSec+"&";
				} else {
					var complete_text = "complete=0&section="+curSec+"&";
				}
				queryStr += "roles["+elem.id+"][user]="+formElmsSelect[0].value+'&'+complete_text;
			} 
			else {
				for(var x = 0; x < formElms.length; x++) {
					var inputVal = formElms[x].value;
					
					switch(formElms[x].name) {
						case 'resource_type': {
							delqueryStr += "roles["+elem.id+"][resource_type]="+formElms[x].value+'&';
							break;
						}
						//case 'user': {
						//	queryStr += "roles["+elem.id+"][user]="+formElms[x].value+'&';
						//	break;
						//}
						case 'email': {
							delqueryStr += "roles["+elem.id+"][email]="+formElms[x].value+'&';
							break;
						}
						case 'phone': {
							delqueryStr += "roles["+elem.id+"][phone]="+formElms[x].value+'&';
							break;
						}
					}
				}
				delqueryStr += "roles["+elem.id+"][user]="+formElmsSelect[0].value+'&';
			}
		}
	}
	//alert(queryStr);
	//alert(delqueryStr);
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_roles.php"+queryStr+status,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_roles.php"+delqueryStr+status,
				success: function(msg) {
					document.getElementById('ajax_loader').style.display = "none";
					changeSectionStatus();
				}
			});
			document.getElementById('ajax_loader').style.display = "none";
			changeSectionStatus();
		}
	});
}
function addRole(theForm) {
	var idx = theForm.resource.selectedIndex;
	alert('add a new role'+theForm.resource.value+':'+theForm.resource.options[idx].text+':'+theForm.user.value+':'+theForm.email.value+':'+theForm.phone.value);
	if(document.getElementById('add_custom_role')) {
		document.getElementById('add_custom_role').style.display = 'none';
	}
	//setAttribute("border", "0");
	var roleList = document.getElementById('project_roles');
	var roleListNum = roleList.getElementsByTagName('li');
	var listClone = document.getElementById('obj0');
	var selectClone = Object.clone(listClone.getElementsByTagName('select'));
	
	var newLi = document.createElement('li');
	newLi.id = "obj"+(roleListNum.length+1);
	
	var newName = document.createElement('div');
	newName.className = "prole_name";
		var nameInput = document.createElement('input');
		nameInput.value = theForm.resource.value;
		nameInput.name = "resource_type";
		nameInput.id = "resource_type"
		nameInput.type = "hidden";
		var nameLabel = document.createElement('label');
		var nameLabelText = document.createTextNode(theForm.resource.options[idx].text);
		nameLabel.appendChild(nameLabelText);
		newName.appendChild(nameInput);
		newName.appendChild(nameLabel);
		newName.appendChild(selectClone);
		
	var newEmail = document.createElement('div');
	newEmail.className = "prole_email";
		var newEmailLabel = document.createElement('label');
		var newEmailLabelText = document.createTextNode('Email:');
		var newEmailInput = document.createElement('input');
		newEmailInput.readOnly = true;
		newEmailInput.name = "email";
		newEmailInput.id = "email";
		newEmailInput.type = "text";
		newEmailInput.value = "test@example.com";
		newEmailLabel.appendChild(newEmailLabelText);
		newEmail.appendChild(newEmailLabel);
		newEmail.appendChild(newEmailInput);
	
	var newPhone = document.createElement('div');
	newPhone.className = "prole_email";
		var newPhoneLabel = document.createElement('label');
		var newPhoneLabelText = document.createTextNode('Phone:');
		var newPhoneInput = document.createElement('input');
		newPhoneInput.readOnly = true;
		newPhoneInput.name = "phone";
		newPhoneInput.id = "phone";
		newPhoneInput.type = "text";
		newPhoneInput.value = "1234567890";
		newPhoneLabel.appendChild(newPhoneLabelText);
		newPhone.appendChild(newPhoneLabel);
		newPhone.appendChild(newPhoneInput);
	
	newLi.appendChild(newName);
	newLi.appendChild(newEmail);
	newLi.appendChild(newPhone);
	
	roleList.innerHTML += "<li id=\"obj"+roleListNum.length+"\">"
	+"<form action=\"\" method=\"post\" name=\"obj1\" onsubmit=\"return false;\">"
	+ newLi.innerHTML
	+ "</form></li>";
	
	
	//roleList.insertAfter(newLi, roleList.getElementsByTagName('li')[roleListNum])
	
	/*
	<li id="obj1">
		<form action="" method="post" name="obj1" onsubmit="return false;">
			<div class="prole_name">
				<input name="resource_type" id="resource_type" value="3" type="hidden">
				<label>Project Manager:</label>
				<select name="user" id="user" onchange="changeRoleUser(this.value, obj1)"><option value="">--Select User--</option><option value="9">Armen Abelyan</option><option value="109">Armen Abelyan</option><option value="58">Marco Acevedo</option><option value="158">Marco Acevedo</option><option value="1">Aaron Aceves</option><option value="101">Aaron Aceves</option><option value="47">JS Admin</option><option value="147">JS Admin</option><option value="77">Rebecca Anema</option><option value="177">Rebecca Anema</option><option value="90">Steven Apple</option><option value="190">Steven Apple</option><option value="43">Javier Armendariz</option><option value="143">Javier Armendariz</option><option value="2">Aaron Bailey</option><option value="66">Michael Bailey</option><option value="102">Aaron Bailey</option><option value="166">Michael Bailey</option><option value="69">Noel Beane</option><option value="169">Noel Beane</option><option value="93">Tim Beynart</option><option value="193">Tim Beynart</option><option value="54">Laurie Boczar</option><option value="154">Laurie Boczar</option><option value="11">Barbara Boyd</option><option value="111">Barbara Boyd</option><option value="89">Steve Buccellato</option><option value="189">Steve Buccellato</option><option value="32">Evelyn Callaghan</option><option value="132">Evelyn Callaghan</option><option value="50">Kevin Campbell</option><option value="150">Kevin Campbell</option><option value="55">Leo Cardoso</option><option value="155">Leo Cardoso</option><option value="16">Chiranjeevi Chakka</option><option value="116">Chiranjeevi Chakka</option><option value="85">Sandra Cheng</option><option value="185">Sandra Cheng</option><option value="56">Luisa Ciccotto</option><option value="156">Luisa Ciccotto</option><option value="15">Charlie Concepcion</option><option value="115">Charlie Concepcion</option><option value="22">Cynthia Cortes</option><option value="122">Cynthia Cortes</option><option value="53">Lauren Dobkin</option><option value="153">Lauren Dobkin</option><option value="68">Morgan Evans</option><option value="168">Morgan Evans</option><option value="23">Darren Feher</option><option value="123">Darren Feher</option><option value="92">Teresa Finch</option><option value="192">Teresa Finch</option><option value="84">Robin Fordham</option><option value="184">Robin Fordham</option><option value="98">Vanitha Furtado</option><option value="198">Vanitha Furtado</option><option value="40">Jane Gallagher</option><option value="140">Jane Gallagher</option><option value="83">Rob Gill</option><option value="183">Rob Gill</option><option value="41">Jason Gurfink</option><option value="141">Jason Gurfink</option><option value="67">Michael Hagerman</option><option value="167">Michael Hagerman</option><option value="91">Susannah Halweg</option><option value="191">Susannah Halweg</option><option value="70">Paul Hayes</option><option value="170">Paul Hayes</option><option value="19">Chris Herring</option><option value="119">Chris Herring</option><option value="10">Austin Holt</option><option value="100">Zeb Holt</option><option value="110">Austin Holt</option><option value="200">Zeb Holt</option><option value="44">Jeffrey Hu</option><option value="144">Jeffrey Hu</option><option value="13">Brian Janko</option><option value="113">Brian Janko</option><option value="87">Sergio Jasinski</option><option value="187">Sergio Jasinski</option><option value="59">Maria Jimenez</option><option value="159">Maria Jimenez</option><option value="63">Marshall Jones</option><option value="163">Marshall Jones</option><option value="49">Kerem Kacel</option><option value="149">Kerem Kacel</option><option value="33">Eyal Kattan</option><option value="133">Eyal Kattan</option><option value="95">Time Keeper</option><option value="195">Time Keeper</option><option value="29">Edward P. Kelly</option><option value="129">Edward P. Kelly</option><option value="20">Christopher Kirchner</option><option value="120">Christopher Kirchner</option><option value="94">Tim Kirk</option><option value="194">Tim Kirk</option><option value="73">Pinguino Kolb</option><option value="173">Pinguino Kolb</option><option value="78">Rendall Koski</option><option value="178">Rendall Koski</option><option value="62">Mark Kraus</option><option value="162">Mark Kraus</option><option value="61">Mark Kusek</option><option value="161">Mark Kusek</option><option value="5">Alex Lindsay</option><option value="105">Alex Lindsay</option><option value="51">Kevin Maes</option><option value="151">Kevin Maes</option><option value="39">Ivy Mahsciao</option><option value="139">Ivy Mahsciao</option><option value="97">UXD Maintenance</option><option value="197">UXD Maintenance</option><option value="45">John Malhinha</option><option value="145">John Malhinha</option><option value="71">Paula Marmor</option><option value="171">Paula Marmor</option><option value="35">George Masters</option><option value="135">George Masters</option><option value="75">Rajan Mehta</option><option value="175">Rajan Mehta</option><option value="24">David Moon</option><option value="124">David Moon</option><option value="8">Anonny Mouse</option><option value="108">Anonny Mouse</option><option value="80">Richard Mulhern</option><option value="180">Richard Mulhern</option><option value="86">Scott Nath</option><option value="186">Scott Nath</option><option value="18">Chris Nelson</option><option value="118">Chris Nelson</option><option value="64">Matthew Newman</option><option value="164">Matthew Newman</option><option value="96">Toni Nichev</option><option value="196">Toni Nichev</option><option value="60">Mark Norris</option><option value="160">Mark Norris</option><option value="48">Junko Otsuki</option><option value="148">Junko Otsuki</option><option value="12">Brandon Otto</option><option value="112">Brandon Otto</option><option value="3">Adalberto  Pardo </option><option value="103">Adalberto  Pardo </option><option value="79">Resource Planner</option><option value="179">Resource Planner</option><option value="42">Jason Rayles</option><option value="142">Jason Rayles</option><option value="17">Chris Reardon</option><option value="117">Chris Reardon</option><option value="72">Philippa Reist</option><option value="172">Philippa Reist</option><option value="4">Adam Roberts</option><option value="104">Adam Roberts</option><option value="36">Ian Rogers</option><option value="37">Ian Rogers</option><option value="136">Ian Rogers</option><option value="137">Ian Rogers</option><option value="7">Ankit Shah</option><option value="74">Piyush Shah</option><option value="107">Ankit Shah</option><option value="174">Piyush Shah</option><option value="6">Amy Shriber</option><option value="106">Amy Shriber</option><option value="57">Marc Siry</option><option value="157">Marc Siry</option><option value="52">KimSu Theiler</option><option value="152">KimSu Theiler</option><option value="31">Elliot Thompson</option><option value="131">Elliot Thompson</option><option value="81">Rick Torres</option><option value="181">Rick Torres</option><option value="88">Smita Trivedi</option><option value="188">Smita Trivedi</option><option value="65">Michael Tsivin</option><option value="165">Michael Tsivin</option><option value="25">David Valade</option><option value="125">David Valade</option><option value="28">Donna Vaughan</option><option value="128">Donna Vaughan</option><option value="26">Deepa Vivek Vishwanathan</option><option value="126">Deepa Vivek Vishwanathan</option><option value="30">Elli Vizcaino</option><option value="130">Elli Vizcaino</option><option value="34">Garrett Vorbeck</option><option value="134">Garrett Vorbeck</option><option value="14">Brittany Wallace</option><option value="114">Brittany Wallace</option><option value="99">Ward Welch</option><option value="199">Ward Welch</option><option value="46">John Wetsell</option><option value="146">John Wetsell</option><option value="82">Rickmond Wong</option><option value="182">Rickmond Wong</option><option value="38">Isabel Woodall</option><option value="138">Isabel Woodall</option><option value="76">Ray Woods</option><option value="176">Ray Woods</option><option value="21">Cristiana Yambo</option><option value="121">Cristiana Yambo</option><option value="27">Dmitry Zak</option><option value="127">Dmitry Zak</option></select>

			</div>
			<div class="prole_email">
				<label>Email:</label>
				<input name="email" id="email" readonly="readonly" type="text">
			</div>
			<div class="prole_email">
				<label>Phone:</label>
				<input name="phone" id="phone" readonly="readonly" type="text">

			</div>
		</form>
	</li>
	*/

}
function clearTimeline() {
	var list = document.getElementById('project_timeline');
	var listItems = list.getElementsByTagName('li');
	
	for(var i = 0; i < listItems.length; i++) {
		var itemInputs = listItems[i].getElementsByTagName('input');
		
		for(x = 0; x < itemInputs.length; x++) {
			switch(itemInputs[x].name) {
				case 'start_date': {
					itemInputs[x].value = "";
					break;
				}
				case 'projected_date': {
					itemInputs[x].value = "";
					break;
				}
			}
		}
	}
}
function checkDateRange(startId, endId) {
	//alert('check date rage'+endId);
	var start = document.getElementById(startId).value;
	var end = document.getElementById(endId).value;
	start_bk = start.split('/');
	end_bk = end.split('/');
	
	document.getElementById(endId).style.color = "#000000";
	
	if(parseInt(start_bk[2]) <= parseInt(end_bk[2])) {
		document.getElementById(endId).style.color = "#000000";
		if(parseInt(start_bk[2]) == parseInt(end_bk[2])) {
			if(parseInt(start_bk[0],10) <= parseInt(end_bk[0],10)) {
				document.getElementById(endId).style.color = "#000000";
				if(parseInt(start_bk[0],10) == parseInt(end_bk[0],10)) {
					if(parseInt(start_bk[1],10) <= parseInt(end_bk[1],10)) {
						document.getElementById(endId).style.color = "#000000";
					} else {
						document.getElementById(endId).style.color = "#FF0000";
					}
				}
			} else {
				document.getElementById(endId).style.color = "#FF0000";
			}
		}
	} else {
		document.getElementById(endId).style.color = "#FF0000";
	}
}
function saveTimeline() {
	var comp_id = document.getElementById('project_id').value;
	var query = "?action=save&project_id="+comp_id+"&";
	var delquery  = "?action=delete&project_id="+comp_id+"&";
	var complete = false;
	var cont = true;
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	if(draft) {
		var status = "&status=true;";
		draft = false;
	} else {
		var status = "";
	}
	
	var timelineList = document.getElementById('project_timeline');
	var listItems = timelineList.getElementsByTagName('li');
	
	for(var i = 0; i < listItems.length; i++) {
		if(listItems[i].style.display != "none") {
			var liInputs = listItems[i].getElementsByTagName('input');
			
			for(var j = 0; j < liInputs.length; j++) {
				if(liInputs[j].value != "" && liInputs[j].name != "phase") {
					complete = true;
				}
				switch(liInputs[j].name) {
					case 'phase': {
						query += "phase["+i+"][id]=" + liInputs[j].value + "&";
						break;
					}
					case 'start_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						query += "phase["+i+"][start]=" + liInputs[j].value + "&";
						break;
					}
					case 'projected_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						query += "phase["+i+"][end]=" + liInputs[j].value + "&";
						break;
					}
				}
			}
		} else {
			var liInputs = listItems[i].getElementsByTagName('input');
			
			for(var j = 0; j < liInputs.length; j++) {
				if(liInputs[j].value != "" && liInputs[j].name != "phase") {
					complete = true;
				}
				switch(liInputs[j].name) {
					case 'phase': {
						delquery += "phase["+i+"][id]=" + liInputs[j].value + "&";
						break;
					}
					case 'start_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						delquery += "phase["+i+"][start]=" + liInputs[j].value + "&";
						break;
					}
					case 'projected_date': {
						if(liInputs[j].style.color == "rgb(255, 0, 0)") {
							cont = false;
						}
						delquery += "phase["+i+"][end]=" + liInputs[j].value + "&";
						break;
					}
				}
			}
		}
	}
	if(complete) {
		var complete_text = "complete=1&section="+curSec+"&";
	} else {
		var complete_text = "complete=0&section="+curSec+"&";
	}
	
	//alert(query+complete_text);
	//alert(delquery+complete_text);
	
	if(!cont) {
		$('.message_timeline_date').css({display:'block'});
		document.getElementById('ajax_loader').style.display = "none";
		return false;
	} else {
		$.ajax({
			type: "GET",
			url: "/_ajaxphp/update_timeline.php"+query+complete_text+status,
			success: function(msg) {
				$.ajax({
					type: "GET",
					url: "/_ajaxphp/update_timeline.php"+delquery+complete_text+status,
					success: function(msg) {
						document.getElementById('ajax_loader').style.display = "none";
						changeSectionStatus();
					}
				});
				document.getElementById('ajax_loader').style.display = "none";
				changeSectionStatus();
			}
		});
	}
}
function calcFinance(theForm,budgetForm) {
	var hours = theForm.hours.value;
	var rate = theForm.rate.value;
	
	theForm.total.value = hours*rate;
	
	var block_finance = document.getElementById('finance_calcs');
	var block_inputs = block_finance.getElementsByTagName('input');
	
	var overall_total = 0.00;
	//temp.slice(0,-2) + "." + temp.slice(-2);
	for(var i = 0; i < block_inputs.length; i++) {
		if(block_inputs[i].id == "total") {
			if(parseFloat(block_inputs[i].value)) {
				overall_total += parseFloat(block_inputs[i].value);
			}
		}
	}
	document.getElementById('totalBudget').value = overall_total;
	document.getElementById('overall_finance_total').innerHTML = "Overall Total:: <span>$"+overall_total+"</span>"
	calcBudget(budgetForm);
}
function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}
function calcBudget(theForm) {
	var totalBudget = parseInt(theForm.totalBudget.value);
	var quarter1 = 0;
	var quarter2 = 0;
	var quarter3 = 0;
	var quarter4 = 0;
	if(theForm.quarter1.value != null && theForm.quarter1.value != "" && theForm.quarter1.value != " "){
		quarter1 = parseFloat(theForm.quarter1.value);
	}else{
		theForm.quarter1.value = 0;
	}
	if(theForm.quarter2.value != null && theForm.quarter2.value != "" && theForm.quarter2.value != " "){
		quarter2 = parseFloat(theForm.quarter2.value);
	}else{
		theForm.quarter2.value = 0;
	}
	if(theForm.quarter3.value != null && theForm.quarter3.value != "" && theForm.quarter3.value != " "){
		quarter3 = parseFloat(theForm.quarter3.value);
	}else{
		theForm.quarter3.value = 0;
	}
	if(theForm.quarter4.value != null && theForm.quarter4.value != "" && theForm.quarter4.value != " "){
		quarter4 = parseFloat(theForm.quarter4.value);
	}else{
		theForm.quarter4.value = 0;
	}
		var unallocated = parseFloat(totalBudget-(quarter1+quarter2+quarter3+quarter4));
	
	if(unallocated<0){
		alert("Allocated Budget should not exceed Total Budget");
		theForm.quarter1.value = quarterOneinitial;
		theForm.quarter2.value = quarterTwoinitial;
		theForm.quarter3.value = quarterThreeinitial;
		theForm.quarter4.value = quarterFourinitial;
		return;
	}
	theForm.unallocated.value = roundNumber(totalBudget-(quarter1+quarter2+quarter3+quarter4), 2);
	if(totalBudget > 0){
		theForm.percentage1.value = roundNumber((quarter1/totalBudget)*100,1);
		theForm.percentage2.value = roundNumber((quarter2/totalBudget)*100,1);
		theForm.percentage3.value = roundNumber((quarter3/totalBudget)*100,1);
		theForm.percentage4.value = roundNumber((quarter4/totalBudget)*100,1);
	}else{
		theForm.percentage1.value = 0;
		theForm.percentage2.value = 0;
		theForm.percentage3.value = 0;
		theForm.percentage4.value = 0;
	}
	quarterOneinitial = quarter1;
	quarterTwoinitial = quarter2;
	quarterThreeinitial = quarter3;
	quarterFourinitial = quarter4;

}
function calcBudgetPercentage(theForm) {
	var totalBudget = parseInt(theForm.totalBudget.value);
	var percentage1 = 0;
	var percentage2 = 0;
	var percentage3 = 0;
	var percentage4 = 0;
	if(theForm.percentage1.value != null && theForm.percentage1.value != "" && theForm.percentage1.value != " "){
		percentage1 = parseFloat(theForm.percentage1.value);
	}else{
		theForm.percentage1.value = 0;
	}
	if(theForm.percentage2.value != null && theForm.percentage2.value != "" && theForm.percentage2.value != " "){
		percentage2 = parseFloat(theForm.percentage2.value);
	}else{
		theForm.percentage2.value = 0;
	}
	if(theForm.percentage3.value != null && theForm.percentage3.value != "" && theForm.percentage3.value != " "){
		percentage3 = parseFloat(theForm.percentage3.value);
	}else{
		theForm.percentage3.value = 0;
	}
	if(theForm.percentage4.value != null && theForm.percentage4.value != "" && theForm.percentage4.value != " "){
		percentage4 = parseFloat(theForm.percentage4.value);
	}else{
		theForm.percentage4.value = 0;
	}

	var unallocated = totalBudget-(((percentage1*totalBudget)/100)+((percentage2*totalBudget)/100)+((percentage3*totalBudget)/100)+((percentage4*totalBudget)/100));
	
	if(unallocated<0){
		alert("Allocated Budget should not exceed Total Budget");
		theForm.percentage1.value = percentageOneinitial;
		theForm.percentage2.value = percentageTwoinitial;
		theForm.percentage3.value = percentageThreeinitial;
		theForm.percentage4.value = percentageFourinitial;
		return;
	}
	quarter1 = roundNumber((percentage1*totalBudget)/100, 2);
	quarter2 = roundNumber((percentage2*totalBudget)/100, 2);
	quarter3 = roundNumber((percentage3*totalBudget)/100, 2);
	quarter4 = roundNumber((percentage4*totalBudget)/100, 2);

	theForm.unallocated.value = roundNumber(totalBudget - (quarter1 + quarter2 + quarter3 + quarter4), 2);

	theForm.quarter1.value = quarter1;
	theForm.quarter2.value = quarter2;
	theForm.quarter3.value = quarter3;
	theForm.quarter4.value = quarter4;

	percentageOneinitial = percentage1;
	percentageTwoinitial = percentage2;
	percentageThreeinitial = percentage3;
	percentageFourinitial = percentage4;

}
function clearFinance() {
	var list = document.getElementById('finance_calcs');
	var listItems = list.getElementsByTagName('li');
	
	for(var i = 0; i < listItems.length; i++) {
		var itemInputs = listItems[i].getElementsByTagName('input');
		
		for(x = 0; x < itemInputs.length; x++) {
			switch(itemInputs[x].id) {
				case 'hours': {
					itemInputs[x].value = "";
					break;
				}
				case 'rate': {
					itemInputs[x].value = "";
					break;
				}
				case 'total': {
					itemInputs[x].value = "";
					break;
				}
				case 'sub_hours': {
					itemInputs[x].value = "";
					break;
				}
				case 'sub_rate': {
					itemInputs[x].value = "";
					break;
				}
				case 'subtotal': {
					itemInputs[x].value = "";
					break;
				}

				case 'percentage1': {
					itemInputs[x].value = "";
					break;
				}
				case 'percentage2': {
					itemInputs[x].value = "";
					break;
				}
				case 'percentage3': {
					itemInputs[x].value = "";
					break;
				}
				case 'percentage4': {
					itemInputs[x].value = "";
					break;
				}
				case 'totalBudget': {
					itemInputs[x].value = "";
					break;
				}
				case 'unallocated': {
					itemInputs[x].value = "";
					break;
				}
				case 'quarter1': {
					itemInputs[x].value = "";
					break;
				}
				case 'quarter2': {
					itemInputs[x].value = "";
					break;
				}
				case 'quarter3': {
					itemInputs[x].value = "";
					break;
				}
				case 'quarter4': {
					itemInputs[x].value = "";
					break;
				}
			}
		}
	}
	document.getElementById('overall_finance_total').innerHTML = "Overall Total:: <span>$ 0</span>"
}
function saveFinance() {
	var comp_id = document.getElementById('project_id').value;
	var budget_code = document.getElementById('fin_budget_code').value;
	var query = "?action=save&project_id="+comp_id+"&budget_code="+budget_code+"&";
	var delquery = "?action=delete&project_id="+comp_id+"&budget_code="+budget_code+"&";
	var del = false;
	var complete = false;
	var total_value = 0;
	var total_budget = document.getElementById('totalBudget').value;
//	if(total_budget <= 0 && curSec == 'sec_6'){
//		alert("Total Budget cannot be zero");
//		return false;
//	}
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	if(draft) {
		var status = "&status=true;";
		draft = false;
	} else {
		var status = "";
	}
	
	var block_finance = document.getElementById('finance_calcs');
	var block_list = block_finance.getElementsByTagName('li');
	
	for(var x = 0; x < block_list.length; x++) {
		var block_inputs = block_list[x].getElementsByTagName('input');
		var div_container = block_list[x].getElementsByTagName('div');
		
		for(var y = 0; y < div_container.length; y++) {
			if(div_container[y].className == "finance_budget") {
				if(div_container[y].style.display == "none") {
					del = true;
				} else {
					del = false;
				}
			}
		}
		var sp_phase = '';
		if(!del) {
			for(var i = 0; i < block_inputs.length; i++) {
				if(block_inputs[i].value != "" && block_inputs[i].name != "phase" && block_inputs[i].name != "total") {
					complete = true;
				}
				switch(block_inputs[i].id) {
					case 'phase': {
						sp_phase = block_inputs[i].value;
						query += "finance["+x+"][phase]="+sp_phase+"&";
						if($('#sub_phase_list_'+sp_phase).length > 0 && $('#sub_phase_list_'+sp_phase).val().length > 0){
							query += "finance["+x+"][rate]=0&";
							var list = $('#sub_phase_list_'+sp_phase).val();
							sp_list = list.split('~');
							for (count in sp_list){
								sp_subPhase = sp_list[count];
								var sp_hours = $('#phase_'+sp_phase+' #sub_phase_'+sp_subPhase+' #sub_hours').val();
								var sp_rate = $('#phase_'+sp_phase+' #sub_phase_'+sp_subPhase+' #sub_rate').val();
								query += "subphase["+sp_phase+"]["+sp_subPhase+"][hours]="+sp_hours+"&";
								query += "subphase["+sp_phase+"]["+sp_subPhase+"][rate]="+sp_rate+"&";
							}
						}
						break;
					}
					case 'hours': {
						query += "finance["+x+"][hours]="+block_inputs[i].value+"&";
						break;
					}
					case 'rate': {
						query += "finance["+x+"][rate]="+block_inputs[i].value+"&";
						break;
					}
					case 'totalBudget': {
						query += "totalBudget="+block_inputs[i].value+"&";
						break;
					}
					case 'quarter1': {
						query += "quarter1="+block_inputs[i].value+"&";
						break;
					}
					case 'quarter2': {
						query += "quarter2="+block_inputs[i].value+"&";
						break;
					}
					case 'quarter3': {
						query += "quarter3="+block_inputs[i].value+"&";
						break;
					}
					case 'quarter4': {
						query += "quarter4="+block_inputs[i].value+"&";
						break;
					}
					case 'total': {
						if(parseInt(block_inputs[i].value)){
							total_value = total_value+parseInt(block_inputs[i].value);
						}
						break;
					}
				}
			}
		}
		else {
			for(var i = 0; i < block_inputs.length; i++) {
				switch(block_inputs[i].id) {
					case 'phase': {
						delquery += "finance["+x+"][phase]="+block_inputs[i].value+"&";
						break;
					}
					case 'hours': {
						delquery += "finance["+x+"][hours]="+block_inputs[i].value+"&";
						break;
					}
					case 'rate': {
						delquery += "finance["+x+"][rate]="+block_inputs[i].value+"&";
						break;
					}
				}
			}
		}
	}

//	if(total_budget != total_value && curSec == 'sec_6'){
//		alert("Overall Total should be equal to Total Budget");
//		document.getElementById('ajax_loader').style.display = "none";
//		return false;
//	}
	if(complete) {
		var complete_text = "complete=1&section="+curSec;
	} else {
		var complete_text = "complete=0&section="+curSec;
	}
	//alert(query+complete_text);
	//alert(delquery+complete_text);
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_finance.php"+query+complete_text+status,
		success: function(msg) {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_finance.php"+delquery+complete_text+status,
				success: function(msg) {
					document.getElementById('ajax_loader').style.display = "none";
					changeSectionStatus();
				}
			});
			document.getElementById('ajax_loader').style.display = "none";
			changeSectionStatus();
		}
	});
}
function clearApprovals() {
	var list = document.getElementById('approvals');
	var listItems = list.getElementsByTagName('li');
	//Defect no 3885
	for(var i = 0; i < listItems.length; i++) {
		var itemInputs = listItems[i].getElementsByTagName('select');
		for(x = 0; x < itemInputs.length; x++) {
			switch(itemInputs[x].name) {
				case 'user_name': {
					//alert(itemInputs[x]+"itemInputs[x]");
					itemInputs[x].value = "";
					break;
				}
			}
		}
	}
	//end
	for(var i = 0; i < listItems.length; i++) {
		var itemInputs = listItems[i].getElementsByTagName('input');
		
		for(x = 0; x < itemInputs.length; x++) {
			switch(itemInputs[x].name) {
				case 'user_name': {
					itemInputs[x].value = "";
					break;
				}
				case 'user_title': {
					itemInputs[x].value = "";
					break;
				}
				case 'user_phone': {
					itemInputs[x].value = "";
					break;
				}
				case 'approval_date': {
					itemInputs[x].value = "";
					break;
				}
				case 'approved': {
					itemInputs[x].checked = false;
					break;
				}
				
			}
		}
	}
}
function saveApprovals() {
	var complete = false;
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	if(draft) {
		var status = "&status=true;";
		draft = false;
	} else {
		var status = "";
	}
	
	var comp_id = document.getElementById('project_id').value;
	var query = "?action=save&project_id="+comp_id+"&";
	var delquery = "?action=delete&project_id="+comp_id+"&";
	
	var appr_block = document.getElementById('approvals');
	var apprList = appr_block.getElementsByTagName('li');
	
	for(var i = 0; i < apprList.length; i++) {
		if(apprList[i].style.display != "none") {
			var inputLst = apprList[i].getElementsByTagName('input');
			var selectLst = apprList[i].getElementsByTagName('select');
			
			for(var y = 0; y < selectLst.length; y++) {
					query += "appr["+i+"][name]="+selectLst[y].value+"&";
			}
			
			for(var x = 0; x < inputLst.length; x++) {
				if(inputLst[x].value != "" && inputLst[x].id != "phase" && inputLst[x].id != "approved") {
					complete = true;
				}
				switch(inputLst[x].name) {
					case 'phase': {
						query += "appr["+i+"][phase]="+inputLst[x].value+"&";
						break;
					}
					case 'user_name': {
						query += "appr["+i+"][name]="+inputLst[x].value+"&";
						break;
					}
					case 'user_title': {
						if(inputLst[x].value == "--title--") {
							query += "appr["+i+"][title]=&";
						} else {
							query += "appr["+i+"][title]="+inputLst[x].value+"&";
						}
						break;
					}
					case 'user_phone': {
						if(inputLst[x].value == "--phone--") {
							query += "appr["+i+"][phone]=&";
						} else {
							query += "appr["+i+"][phone]="+inputLst[x].value+"&";
						}
						break;
					}
					case 'approved': {
						if(inputLst[x].checked) {
							query += "appr["+i+"][approved]=yes&";
						} else {
							query += "appr["+i+"][approved]=no&";
						}
						break;
					}
					case 'approval_date': {
						query += "appr["+i+"][date]="+inputLst[x].value+"&";
						break;
					}
				}
				
			}
		}
		else {
			var inputLst = apprList[i].getElementsByTagName('input');
			var selectLst = apprList[i].getElementsByTagName('select');
			
			for(var y = 0; y < selectLst.length; y++) {
					delquery += "appr["+i+"][name]="+selectLst[y].value+"&";
			}
			for(var x = 0; x < inputLst.length; x++) {
				switch(inputLst[x].name) {
					case 'phase': {
						delquery += "appr["+i+"][phase]="+inputLst[x].value+"&";
						break;
					}
					case 'user_name': {
						delquery += "appr["+i+"][name]="+inputLst[x].value+"&";
						break;
					}
					case 'user_title': {
						delquery += "appr["+i+"][title]="+inputLst[x].value+"&";
						break;
					}
					case 'user_phone': {
						delquery += "appr["+i+"][phone]="+inputLst[x].value+"&";
						break;
					}
					case 'approved': {
						if(inputLst[x].checked) {
							delquery += "appr["+i+"][approved]=yes&";
						} else {
							delquery += "appr["+i+"][approved]=no&";
						}
						break;
					}
					case 'approval_date': {
						delquery += "appr["+i+"][date]="+inputLst[x].value+"&";
						break;
					}
				}
				
			}
		}
	}
	
	if(complete) {
		var complete_text = "complete=1&section="+curSec;
	} else {
		var complete_text = "complete=0&section="+curSec;
	}
	
	//alert(query+complete_text);
	//alert(delquery+complete_text);
	
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_approvals.php"+query+complete_text+status,
		success: function(msg) {
			document.getElementById('ajax_loader').style.display = "none";
			changeSectionStatus();
		}
	});
}
function rolesOver(obj) {
	$("#role" + obj).addClass("dragged1");	
}
function rolesOut(obj) {
	$("#role" + obj).removeClass("dragged1");			
}
function fadeDimmer(obj) {
	if ($("#role" + obj + " .dim").css("display") == "none") {
		$("#role" + obj + " .dim").css({ display:"block"});
		$("#role" + obj + " .dim").fadeTo("slow", 0.7);
		
		$("#timeline_" + obj).css({ display:"none"});
		//$("#timeline_" + obj + " .dim").css({ display:"block"});
		//$("#timeline_" + obj + " .dim").fadeTo("slow", 0.7);
		
		$("#finance_" + obj).css({ display:"none"});
		$("#finance_li_" + obj).css({ display:"none"});
		//$("#finance_" + obj + " .dim").css({ display:"block"});
		//$("#finance_" + obj + " .dim").fadeTo("slow", 0.7);
		
		$("#approvals_" + obj).css({ display:"none"});
		
		
		$("#role" + obj + "_btn").removeClass("prole_disable");
		$("#role" + obj + "_btn").addClass("prole_enable");
		
		saveRoles();
		saveTimeline();
		saveFinance();
		saveApprovals();
	} else {
		$("#role" + obj + "_btn").addClass("prole_disable");
		$("#role" + obj + "_btn").removeClass("prole_enable");
		
		$("#role" + obj + " .dim").fadeTo("slow", 0, function(){
				$("#role" + obj + " .dim").css({ display:"none"});
			}
		);
		
		$("#timeline_" + obj).css({ display:"block"});
		$("#finance_" + obj).css({ display:"block"});
		$("#finance_li_" + obj).css({ display:"block"});
		$("#approvals_" + obj).css({ display:"block"});
		saveRoles();
		saveTimeline();
		saveFinance();
		saveApprovals();
	}
}
function saveCreate() {
	alert('saved the data');
}
function ct_allComplete() {
	var complete = false;
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var project = document.getElementById('project_id').value;
	var qString = '?project_id='+project+"&";
	
	var ulObj = document.getElementById('create_sections');
	var ListItems = ulObj.getElementsByTagName('li');
	
	for(var i = 0; i < ListItems.length; i++) {
		qString += "sections[" + ListItems[i].id + "]&";
	}
	//alert(qString);
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/complete_all_sections.php"+qString,
		success: function(msg) {
			for(var i = 0; i < ListItems.length; i++) {
				var theLi = document.getElementById(ListItems[i].id);
				var liImage = theLi.getElementsByTagName("img");
				liImage[0].src = "/_images/green_status.gif";
			}
			setCompleteness();
			document.getElementById('ajax_loader').style.display = "none";
		}
	});
}
function ct_pdfExport() {
	var project_id = document.getElementById('project_id').value;
	var sections = document.getElementById('create_sections');
	var secLi = sections.getElementsByTagName('li');
	var qString = "?project_id="+project_id+"&";
	
	var html = "<input type=\"hidden\" name=\"project_id\" value=\""+project_id+"\" />";
	
	for(var i = 0; i < secLi.length; i++) {
		var nmPart = secLi[i].id.split('_');
		var formElement = "form_sec_" + nmPart[1];
		
		switch(nmPart[1]) {
			case "1": {
				if(FCKeditorAPI.GetInstance('descEditor')) {
					//FCKeditorAPI.GetInstance('descEditor').GetData()
					qString += "section[1]="+base64_encode(FCKeditorAPI.GetInstance('descEditor').GetData().replace("&amp;","&"))+"&";
					html += "<input type=\"hidden\" name=\"section[1]\" value=\""+base64_encode(FCKeditorAPI.GetInstance('descEditor').GetData().replace("&amp;","&"))+"\" />";
				} else {
					qString += "section[1]=&";
					html += "<input type=\"hidden\" name=\"section[1]\" value=\"\" />";
				}
				break;
			}
			case "2": {
				var rolesBlock = document.getElementById(formElement);
				var roleLi = rolesBlock.getElementsByTagName('li');
				
				for(var x = 0; x < roleLi.length; x++) {
					if(roleLi[x].style.display != "none") {
						var roleSel = roleLi[x].getElementsByTagName('select');
						var roleInp = roleLi[x].getElementsByTagName('input');
						
						qString += "section[2]["+x+"][user]="+roleSel[0].value+"&";
						html += "<input type=\"hidden\" name=\"section[2]["+x+"][user]\" value=\""+roleSel[0].value+"\" />";
						
						for(var y = 0; y < roleInp.length; y++) {
							switch(roleInp[y].id) {
								case 'resource_type':{
									qString += "section[2]["+x+"][resource_type]="+roleInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[2]["+x+"][resource_type]\" value=\""+roleInp[y].value+"\" />";
									break
								}
								case 'email':{
									qString += "section[2]["+x+"][email]="+roleInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[2]["+x+"][email]\" value=\""+roleInp[y].value+"\" />";
									break
								}
								case 'phone':{
									qString += "section[2]["+x+"][phone]="+roleInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[2]["+x+"][phone]\" value=\""+roleInp[y].value+"\" />";
									break
								}
								
							}
						}
					}
				}
				break;
			}
			case "3": {
				var timelineBlock = document.getElementById(formElement);
				var timelineLi = timelineBlock.getElementsByTagName('li');
				
				for(var x = 0; x < timelineLi.length; x++) {
					if(timelineLi[x].style.display != "none") {
						var timelineInp = timelineLi[x].getElementsByTagName('input');
						
						for(var y = 0; y < timelineInp.length; y++) {
							switch(timelineInp[y].name) {
								case 'phase': {
									qString += "section[3]["+x+"][phase]="+timelineInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[3]["+x+"][phase]\" value=\""+timelineInp[y].value+"\" />";
									break;
								}
								case 'start_date': {
									qString += "section[3]["+x+"][start_date]="+timelineInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[3]["+x+"][start_date]\" value=\""+timelineInp[y].value+"\" />";
									break;
								}
								case 'projected_date': {
									qString += "section[3]["+x+"][projected_date]="+timelineInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[3]["+x+"][projected_date]\" value=\""+timelineInp[y].value+"\" />";
									break;
								}
							}
						}
					}
				}
				break;
			}
			case "4": {
				if(FCKeditorAPI.GetInstance('scopeEditor')) {
					qString += "section[4]="+base64_encode(FCKeditorAPI.GetInstance('scopeEditor').GetData().replace("&amp;","&"))+"&";
					html += "<input type=\"hidden\" name=\"section[4]\" value=\""+base64_encode(FCKeditorAPI.GetInstance('scopeEditor').GetData().replace("&amp;","&"))+"\" />";
				} else {
					qString += "section[4]=&";
					html += "<input type=\"hidden\" name=\"section[4]\" value=\"\" />";
				}
				break;
			}
			case "5": {
				
				break;
			}
			case "6": {
				var finBlock = document.getElementById(formElement);
				var finLi = finBlock.getElementsByTagName('li');
				
				for(var x = 0; x < finLi.length; x++) {
					if(finLi[x].style.display != "none") {
						var finInp = finLi[x].getElementsByTagName('input');
						
						for(var y = 0; y < finInp.length; y++) {
							switch(finInp[y].id) {
								case 'phase': {
									qString += "section[6]["+x+"][phase]="+finInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[6]["+x+"][phase]\" value=\""+finInp[y].value+"\" />";
									break
								}
								case 'hours': {
									qString += "section[6]["+x+"][hours]="+finInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[6]["+x+"][hours]\" value=\""+finInp[y].value+"\" />";
									break
								}
								case 'rate': {
									qString += "section[6]["+x+"][rate]="+finInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[6]["+x+"][rate]\" value=\""+finInp[y].value+"\" />";
									break
								}
							}
						}
					} else {
						qString += "section[6]=&";
						html += "<input type=\"hidden\" name=\"section[6]\" value=\"\" />";
					}
				}
				break;
			}
			case "7": {
				if(FCKeditorAPI.GetInstance('deliverEditor')) {
					qString += "section[7]="+base64_encode(FCKeditorAPI.GetInstance('deliverEditor').GetData().replace("&amp;","&"))+"&";
					html += "<input type=\"hidden\" name=\"section[7]\" value=\""+base64_encode(FCKeditorAPI.GetInstance('deliverEditor').GetData().replace("&amp;","&"))+"\" />";
				} else {
					qString += "section[7]=&";
					html += "<input type=\"hidden\" name=\"section[7]\" value=\"\" />";
				}
				break;
			}
			case "8": {
				if(FCKeditorAPI.GetInstance('metricsEditor')) {
					qString += "section[8]="+base64_encode(FCKeditorAPI.GetInstance('metricsEditor').GetData().replace("&amp;","&"))+"&";
					html += "<input type=\"hidden\" name=\"section[8]\" value=\""+base64_encode(FCKeditorAPI.GetInstance('metricsEditor').GetData().replace("&amp;","&"))+"\" />";
				} else {
					qString += "section[8]=&";
					html += "<input type=\"hidden\" name=\"section[8]\" value=\"\" />";
				}
				break;
			}
			case "9": {
				var apprBlock = document.getElementById(formElement);
				var apprLi = apprBlock.getElementsByTagName('li');
				
				for(var x = 0; x < apprLi.length; x++) {
					if(apprLi[x].style.display != "none") {
						var apprInp = apprLi[x].getElementsByTagName('input');
						var selectLst = apprLi[x].getElementsByTagName('select');
						
						qString += "section[9]["+x+"][user_name]="+selectLst[0].value+"&";
						html += "<input type=\"hidden\" name=\"section[9]["+x+"][user_name]\" value=\""+selectLst[0].value+"\" />";
						
						
						for(var y = 0; y < apprInp.length; y++) {
							switch(apprInp[y].name) {
								case 'phase': {
									qString += "section[9]["+x+"][phase]="+apprInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[9]["+x+"][phase]\" value=\""+apprInp[y].value+"\" />";
									break;
								}
								case 'user_name': {
									qString += "section[9]["+x+"][user_name]="+apprInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[9]["+x+"][user_name]\" value=\""+apprInp[y].value+"\" />";
									break;
								}
								case 'user_title': {
									qString += "section[9]["+x+"][user_title]="+apprInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[9]["+x+"][user_title]\" value=\""+apprInp[y].value+"\" />";
									break;
								}
								case 'user_phone': {
									qString += "section[9]["+x+"][user_phone]="+apprInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[9]["+x+"][user_phone]\" value=\""+apprInp[y].value+"\" />";
									break;
								}
								case 'approved': {
									if(apprInp[y].checked) {
										qString += "section[9]["+x+"][approved]=yes&";
										html += "<input type=\"hidden\" name=\"section[9]["+x+"][approved]\" value=\"yes\" />";
									} else {
										qString += "section[9]["+x+"][approved]=no&";
										html += "<input type=\"hidden\" name=\"section[9]["+x+"][approved]\" value=\"no\" />";
									}
									break;
								}
								case 'approval_date': {
									qString += "section[9]["+x+"][approval_date]="+apprInp[y].value+"&";
									html += "<input type=\"hidden\" name=\"section[9]["+x+"][approval_date]\" value=\""+apprInp[y].value+"\" />";
									break;
								}
							}
						}
					} else {
						qString += "section[9]=&";
						html += "<input type=\"hidden\" name=\"section[9]\" value=\"\" />";
					}
				}
				
				break;
			}
			case "10": {
				if(FCKeditorAPI.GetInstance('bcaseEditor')) {
					qString += "section[10]="+base64_encode(FCKeditorAPI.GetInstance('bcaseEditor').GetData().replace("&amp;","&"))+"&";
					html += "<input type=\"hidden\" name=\"section[10]\" value=\""+base64_encode(FCKeditorAPI.GetInstance('bcaseEditor').GetData().replace("&amp;","&"))+"\" />";
				} else {
					qString += "section[10]=&";
					html += "<input type=\"hidden\" name=\"section[10]\" value=\"\" />";
				}
				break;
			}
		}
	}

	//alert(qString);
	//alert(html);
	$('#pdfform').html(html);
	//alert($('#pdfform').html());
	//window.open('/pdfs/export_project.php'+qString, 'Project PDF');
	document.getElementById('pdfform').submit();
	//$.post('/pdfs/export_project.php', {'section[10]': [qStringTen]});
}

/*$(document).ready(function(){
	$.ajax({
		type: "POST",
		url: "../_ajaxphp/test.php",
		data: "test=this is testget data&test2=some more",
		success: function(msg) {
			alert("Data Saved: " + msg)
		}
	});
	
	$.ajax({
		type: "GET",
		url: "../_ajaxphp/test.php",
		data: "test=this is testget data&test2=some more",
		success: function(msg) {
			alert("Data Saved: " + msg)
		}
	});
});*/

/**************Run functions for control tower*****************/
//addLoadEvent(function() {
  /* more code to run on page load */ 
//});

addLoadEvent(setCurrentSection);

function createRisk(proj, user){
	var riskTitle = document.getElementById('risk_title').value;
	var riskDesc = document.getElementById('risk_desc').value;
	var riskAssigned = document.getElementById('user_list').value;
	if(riskTitle == ''){
		$('.message_risk_create .risk_msg').html('Enter a valid title for the flag.');
		$('.message_risk_create').css({display:"block"});
	}else if(riskDesc ==''){
		$('.message_risk_create .risk_msg').html('Enter a valid description for the flag.');
		$('.message_risk_create').css({display:"block"});
	}else{
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/create_risk.php",
			data: { title : riskTitle, desc : riskDesc, createdBy : user, projectId : proj, assignedTo : riskAssigned},
			success: function(msg) {
				$('#risk_title').val('');
				$('#risk_desc').val('');
				$('#user_list').val('-1');
				$('.add_risk').css({display:'none'});
				$('.message_risk_create .risk_msg').html('The flag was added successfully.');
				$('.message_risk_create').css({display:"block"});
				$('.risk_ps').css({display: 'block'});
				$('.riskContainer ul').append(msg);
			}
		});
	}
}
function saveUserRoles() {
	document.getElementById('ajax_loader').style.display = "block";
	document.getElementById('ajax_loader').style.backgroundColor = "#FFFFFF";
	document.getElementById('ajax_loader').style.opacity = '0.7';
	document.getElementById('ajax_loader').style.filter = 'alpha(opacity=70)';
	
	var project_id = document.getElementById('project_id').value;
	var rolesUl = document.getElementById('project_subphase');
	var rolesLi = rolesUl.getElementsByTagName('li');
	var queryStr = "?action=save&project_id="+project_id+"&";
//	var delqueryStr = "?action=delete&project_id="+comp_id+"&";
	for(var i = 0; i < rolesLi.length; i++) {
		if(rolesLi[i].id != "") {
			var elem = rolesLi[i];
			var formElms = elem.getElementsByTagName('input');
			var formElmsSelect = elem.getElementsByTagName('select');
			queryStr += "roles["+elem.id+"][sub_phase_type]="+formElmsSelect[0].value+'&';
			queryStr += "roles["+elem.id+"][user]="+formElms[0].value+'&';

		}
	}
		
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_user_roles.php"+queryStr,
		success: function(msg) {
			document.getElementById('ajax_loader').style.display = "none";
			
		}
	});
}
function showSubPhase(projID, phaseID){
	var topVar = ($('#add_subphase_'+phaseID).offset().top - 148);
	var leftVar = ($('#add_subphase_'+phaseID).offset().left - 59);
	$.ajax({
			type: "GET",
			url: "/_ajaxphp/get_subphase.php",
			data: "project="+projID + "&phase=" + phaseID,
			success: function(msg) {
				$('.sub_phase_list').html(msg);
				$('.sub_phase_list').css({top: topVar + 'px', left: leftVar + 'px', display:'block'});
			}
		});
}

function add_subPhase(subPhase, phase, project){
	$.ajax({
			type: "GET",
			url: "/_ajaxphp/addEdit_subphase.php",
			data: "action=add&subPhase=" + subPhase + "&project="+ project + "&phase=" + phase,
			success: function(msg) {
				var list = $('#sub_phase_list_'+phase).val();
				if(list.length == 0){
					list += subPhase;
					$('form[name="fin_'+phase+'"] .finance_budget_rate').html('');
//					$('form[name="fin_'+phase+'"] .finance_budget_hours #hours').removeAttr('onChange');
					$('form[name="fin_'+phase+'"] .finance_budget_hours #hours').attr('readonly', 'readonly');
					$('form[name="fin_'+phase+'"] .finance_budget_hours #hours').addClass('readonly');
				}else{
					list += '~' + subPhase;
				}
				$('#sub_phase_list_'+phase).val(list);
				$('.sub_phase_list').css({display:'none'});
				$('#phase_'+phase).append(msg);
//				$('.sub_phase_list').css({top: topVar + 'px', left: leftVar + 'px', display:'block'});
			}
		});
}

function remove_subPhase(subPhase, phase, project){
	$.ajax({
			type: "GET",
			url: "/_ajaxphp/addEdit_subphase.php",
			data: "action=remove&subPhase=" + subPhase + "&project="+ project + "&phase=" + phase,
			success: function(msg) {
				var list = $('#sub_phase_list_'+phase).val();
				if(list.length == 1){
					list = list.replace(subPhase, '');
					$('form[name="fin_'+phase+'"] .finance_budget_rate').html('<span>Rate:</span><input type="text" onChange="calcFinance(fin_'+phase+')" value="" id="rate" name="rate"/>');
//					$('form[name="fin_'+phase+'"] .finance_budget_hours #hours').attr('onChange', 'calcFinance(fin_'+phase+')');
					$('form[name="fin_'+phase+'"] .finance_budget_hours #hours').removeAttr('readonly');
					$('form[name="fin_'+phase+'"] .finance_budget_hours #hours').removeClass('readonly');
				}else{
					// using replace function twice just to make sure the subPhase is removed, irrespective of it coming in the begining or end.
					list = list.replace('~'+subPhase, '');
					list = list.replace(subPhase+'~', '');
				}
				$('#sub_phase_list_'+phase).val(list);
//				$('.sub_phase_list').css({display:'none'});
				$('#sub_phase_'+subPhase).remove();
//				$('.sub_phase_list').css({top: topVar + 'px', left: leftVar + 'px', display:'block'});
			}
		});
}
function calcSubPhaseFinance(phase, subPhase, formName,theForm) {
	var hours = $('#phase_'+phase+' #sub_phase_'+subPhase+' #sub_hours').val();
	var rate = $('#phase_'+phase+' #sub_phase_'+subPhase+' #sub_rate').val();

	var total = hours * rate;
	$('#phase_'+phase+' #sub_phase_'+subPhase+' #subtotal').val(total);

	var count;
	var phaseHours = 0;
	var phaseTotal = 0;

	var list = $('#sub_phase_list_'+phase).val();
	sp_list = list.split('~');
	for (count in sp_list){
		if(parseFloat($('#phase_'+phase+' #sub_phase_'+sp_list[count]+' #sub_hours').val()))
			phaseHours += parseFloat($('#phase_'+phase+' #sub_phase_'+sp_list[count]+' #sub_hours').val());
		if(parseFloat($('#phase_'+phase+' #sub_phase_'+sp_list[count]+' #subtotal').val()))
			phaseTotal += parseFloat($('#phase_'+phase+' #sub_phase_'+sp_list[count]+' #subtotal').val());
	}

	formName.total.value = phaseTotal;
	formName.hours.value = phaseHours;

	var block_finance = document.getElementById('finance_calcs');
	var block_list = block_finance.getElementsByTagName('li');
	var overall_total = 0.00;

	for(var x = 0; x < block_list.length; x++) {
		var block_inputs = block_list[x].getElementsByTagName('input');
		if(block_list[x].style.display != "none"){
			for(var i = 0; i < block_inputs.length; i++) {
				if(block_inputs[i].id == "total") {
					if(parseFloat(block_inputs[i].value)) {
						overall_total += parseFloat(block_inputs[i].value);
					}
				}
			}
		}
	}
	document.getElementById('totalBudget').value = overall_total;
	document.getElementById('overall_finance_total').innerHTML = "Overall Total:: <span>$"+overall_total+"</span>"
	calcBudget(theForm);
}

/*	Project Status */
	function changeProjectStatus(newStatus, projectId, userid){
		$.ajax({
				type: "GET",
				url: "/_ajaxphp/update_project_status.php",
				data: "project_id="+projectId+"&project_status_id="+newStatus+"&user_id="+userid,
				success: function(msg) {
					$('.project_status_block span.project_status').html(msg);
					$('.project_status_list').css({display:'none'});
					$('.project_status_block .dropdown a').removeClass('up');
				}
			});
	}

	function escateQuates(desc)
	{
		return escape(desc);
	}
