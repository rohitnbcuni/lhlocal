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
var aOpen = false;
var selectedRow;
var aSelectedRow;
var projDetails; 

var ajaxRequest;  // The variable that makes Ajax possible!
ajaxRequest = new XMLHttpRequest();

ajaxRequest.onreadystatechange = function(){
	if(ajaxRequest.readyState == 4){
		projectList = eval( "(" + ajaxRequest.responseText + ")" );
		setSort('project','active');
	}
}
ajaxRequest.open("GET", "_ajaxphp/quickfilter_json.php", true);
ajaxRequest.send(null);

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
	
	if (document.getElementById("archive_list").style.display == 'none') {
		$("#archive_link").removeClass("expand_toggle_closed");

		if (aOpen == false) {
			aOpen = true;
			var ajaxRequest1; // The variable that makes Ajax possible!
			ajaxRequest1 = new XMLHttpRequest();
			
			ajaxRequest1.onreadystatechange = function(){
				if (ajaxRequest1.readyState == 4) {
					archiveList = eval("(" + ajaxRequest1.responseText + ")");
					setSort('project','acrhive');
					document.getElementById("archive_list").style.display = 'block';
					$("#archive_list").show("blind", { direction: "vertical" }, 500);
				}
			}
			ajaxRequest1.open("GET", "_ajaxphp/quickfilter_json.php?archive=1", true);
			ajaxRequest1.send(null);
		} else {	
			$("#archive_list").show("blind", { direction: "vertical" }, 500);	
		}
		
	} else {
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
			tList.sort(sortByTodate );
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
		
		getProjectDetails(selRow, newRow + "_d");
		
		$(oldR + "_d").hide("blind", { direction: "vertical" }, 1000);
	}
	
	selectedRow = selRow;
	
	

	
}

//**************************************
//**Request project detail from server**
//**************************************
function getProjectDetails(projId, ddId){
	var ajRequest = new XMLHttpRequest();
	
	ajRequest.onreadystatechange = function(){
		if(ajRequest.readyState == 4){
			projDetails = eval( "(" + ajRequest.responseText + ")" );
			$(ddId).html(projDetails);
			$(ddId).show("blind", { direction: "vertical" }, 1000);
		}
	}
	ajRequest.open("GET", "_ajaxphp/quickfilter_json_detail.php?project_id="+projId, true);
	ajRequest.send(null);
}

//***************************************
//**Display active list from list array**
//***************************************
function displayActiveList() {
	plist = "";
	elem = document.getElementById("project_filter");
	fText = elem.value.toLowerCase();
	compId = document.getElementById("company_filter").value;
	
	tList = projectList;
	for (var i=0;i<tList.length;i++) {
		proj = tList[i];
		if ((proj["name"].toLowerCase().match(fText) != null) || (proj["code"].toLowerCase().match(fText) != null)) {
			if (((compId>0) && (proj["company"] == compId)) || (compId == 0)) {
				pwidth = (proj["complete"] / 100) * 130;
				if (proj["id"] == selectedRow) {
					plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\" class=\"active\"><ul><li class=\"project\">" + proj["code"] + " - " + proj["name"] + "</li><li class=\"actual\">$" + proj["todate"] + "</li><li class=\"budget\"> $" + proj["budget"] + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li></ul></dt>";
					plist = plist + '		<dd id="row_' + proj["id"] +'_d" class=\"active\">';
					plist = plist + projDetails;
				} else {
					plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\"><ul><li class=\"project\">" + proj["code"] + " - " + proj["name"] + "</li><li class=\"actual\">$" + proj["todate"] + "</li><li class=\"budget\"> $" + proj["budget"] + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li></ul></dt>";
					plist = plist + '		<dd id="row_' + proj["id"] +'_d">';
				}

				plist = plist + '		</dd>';
			}
		}
	}
	
	$("#active_project_list").html(plist);
	
}

			
//****************************************
//**Display archive list from list array**
//****************************************		
function displayArchiveList() {
	plist = "";
	elem = document.getElementById("project_filter");
	fText = elem.value.toLowerCase();
	compId = document.getElementById("company_filter").value;
	
	tList = archiveList;
		for (var i=0;i<tList.length;i++) {
		proj = tList[i];
		if ((proj["name"].toLowerCase().match(fText) != null) || (proj["code"].toLowerCase().match(fText) != null)) {
			if (((compId>0) && (proj["company"] == compId)) || (compId == 0)) {
				pwidth = (proj["complete"] / 100) * 130;
				if (proj["id"] == selectedRow) {
					plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\" class=\"active\"><ul><li class=\"project\">" + proj["code"] + " - " + proj["name"] + "</li><li class=\"actual\">$" + proj["todate"] + "</li><li class=\"budget\"> $" + proj["budget"] + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li><li class=\"button\"><button class=\"secondary\"><span>create work order</span></button></li></ul></dt>";
					plist = plist + '		<dd id="row_' + proj["id"] +'_d" class=\"active\">';
				
					plist = plist + projDetails;
				} else {
				plist = plist + "<dt onClick=\"selectRow('" + proj["id"] + "');\" id=\"row_" + proj["id"] + "\"><ul><li class=\"project\">" + proj["code"] + " - " + proj["name"] + "</li><li class=\"actual\">$" + proj["todate"] + "</li><li class=\"budget\"> $" + proj["budget"] + "</li><li class=\"completeness\"><div class=\"project_progress\" style=\"width: " + pwidth + "px;\"></div><div class=\"percentage\">" + proj["complete"] + "%</div></li><li class=\"button\"><button class=\"secondary\"><span>create work order</span></button></li></ul></dt>";
				plist = plist + '		<dd id="row_' + proj["id"] +'_d">';
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
    var x = a["todate"].toLowerCase();
    var y = b["todate"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

function sortByBudget(a, b) {
    var x = a["budget"].toLowerCase();
    var y = b["budget"].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}
