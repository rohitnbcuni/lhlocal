/*******************************
//Main Variables
*******************************/
var selDate=new Date();
var startWeek = weekStart(1);
var monthText = new Array();
var weekDate;
var endWeek;
var lastDay;
var theId;
var filtered = false;
var resizeTimeout = null;
var content_loader = $(document.createElement('div')).css({position:'absolute', width:'100%', height:'100%', background:'#fff url(/_images/ajax-loader.gif) no-repeat center 150px', top:'0px', left:'0px'}).addClass("content_loader");
var contextMenu;
var ctrlDown;
var offY = -200;
var offX = 20;
var otId;
var status_types = {overhead: 1, outofoffice: 2, allocated: 3, convert: 4};

monthText[1] = "January";
monthText[2] = "February";
monthText[3] = "March";
monthText[4] = "April";
monthText[5] = "May";
monthText[6] = "June";
monthText[7] = "July";
monthText[8] = "August";
monthText[9] = "September";
monthText[10] = "October";
monthText[11] = "November";
monthText[12] = "December";





$(document).ready(function(){
	/** jQuery Variables **/
    contextMenu = $('.allocation_type').clone(true).addClass('context_menu');
	
	//$(window).scroll(function(){
	$('.hours ul li').mouseover(function () {
		var bottom = false;
		var height = $(document).height();
		var scrollTop = $(document).scrollTop();
		var scrollPosition = $(window).height() + $(window).scrollTop();
		
		if(height == scrollPosition) {
			$(this).find('ul').show();
			$(window).scrollTop($(window).scrollTop()+100);

			var dif = $(window).scrollTop() - scrollTop;
			
			if($(window).scrollTop() > scrollTop) {
				$(this).find('ul').css("top", (($(this).find('ul').height() - 20)*-1));
			}
		} else {
			$(this).find('ul').show();
		}
	});
	$('.hours ul li').mouseout(function () {
		$(this).find('ul').hide();
		//$(this).find('ul').css("top", "-1");
	})

	/** Global Document and Window Events **/
	//Comment this line to enable the ctrl featrue and the keyup function
	ctrlDown = true;
	$(document).click(function(e){
		if(e.button != 2){
	  		$('.context_menu').remove();
	  	}
	}).keydown(function(e){
		if(e.which == 17) { ctrlDown = true; }
	}).keyup(function(e){
		//set to false to activate the ctrl select feature
		if(e.which == 17) { ctrlDown = true; }
	}).bind("contextmenu",function(e){
		return false;
	});
	
	$(window).resize(function(){
		if(resizeTimeout != null) { clearTimeout(resizeTimeout); }
    	$(".schedules_container").append(content_loader);
		resizeTimeout = setTimeout('reloadContent()', 3000);
	});

	/** User specific functions **/
	$('#all_resources').click(function() { window.location = "/resourceplanner/"; });

	$('.month_arrows_left').click(function() {
		var oldDate = selDate;
		oldDate.setMonth(oldDate.getMonth() - 1);

		selDate = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
		changeSingleUserContent();
	});

	$('.month_arrows_right').click(function() {
		var oldDate = selDate;
		oldDate.setMonth(oldDate.getMonth() + 1);

		selDate = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
		changeSingleUserContent();
	});

	$('.single_user_date_jump').change(function () {
		var jumpDate = $(this).val();
		jumpDateSplit = jumpDate.split("/");

		selDate = new Date(parseInt(jumpDateSplit[2]), (parseInt(jumpDateSplit[0])-1), parseInt(jumpDateSplit[1]));
		changeSingleUserContent();
	});

	/** All Resources functions **/
	$('.schedules_controller').mouseup(function(e){
	  	$('.context_menu').remove();
		if(e.button == 2){
	   		$('body').append(contextMenu);
	  		$('.context_menu').css({top: e.pageY, left: e.pageX});
			selectionOptions();
			return false;
  		}
	});

	$('.allocation_expanded').removeClass('allocation_expanded').addClass('allocation_collapsed').next().slideUp("normal");

	$('.schedule li ul li').click(function(){
		if(ctrlDown) {
			$(this).toggleClass('ui-selected');
		} else {
	        $('.schedule li ul li').removeClass('ui-selected');
			$(this).toggleClass('ui-selected');
		}
	});

	/*$('.schedule li ul li').mouseover(function(ve){
		var txt = $('.slot_title', this).text();
		var splitTxt = txt.split(': ');
		if(txt) {
			$(this).mousemove(function(e){
				$('#tooltip').css({top: e.pageY, left: e.pageX+15}).html('<span>'+splitTxt[0]+'<br />'+splitTxt[1]+'</span>').show();
			});
		} else { $('#tooltip').hide(); }
		return false;
	}).mouseout(function(ue){ $('#tooltip').hide(); });*/

	$(".schedules_controller").selectable({
		autoRefresh: false,
		filter: '.schedule_day ul li.sel'
	}).mousedown(function(){return false;});

	$(".datePick input").datepicker({
		dateFormat: 'mm/dd/yy',
		showOn: 'both',
		buttonImage: '/_images/date_picker_trigger.gif',
		buttonImageOnly: true
	});

	$('.schedules_controller .overtime, .schedules_controller .cancel').click(function(){
		otId = "";
		var leftVar = ($(this).offset().left + offX);
		var topVar = ($(this).offset().top + offY);
		theId = $(this).attr('id').split("_");
		otId = theId[1];
		weekDate = theId[2];
		$('#popHours').css({display: 'block', left: leftVar + 'px', top: topVar + 'px'});
		$('#blur').css({display: 'block'});

		$('#popHours #overtime_project').val("");
		$('#popHours #overtime_hours').val("");
		$('#popHours #overtime_notes').val("");

		if(weekDate){ var useDate = weekDate; } else { var useDate = startWeek; }
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/load_overtime_data.php",
			data: "startDate="+useDate+"&userid="+theId[1],
			success: function(json){
				$('#popHours #overtime_project').val(json['project']);
				$('#popHours #overtime_hours').val(json['hours']);
				$('#popHours #overtime_notes').val(json['notes']);
			},
			dataType: "json"
		});
	});

	/*** View month click event ***/
	$('.viewmonth').click(function() {
		var viewMonth = $(this).attr("id")
		var viewMonthSplit = viewMonth.split("_");
		window.location = "/resourceplanner/?userid="+viewMonthSplit[1];
	});

	/*** Jump to click event ***/
	$('.jumptoItem').click(function() {
		var theId = $(this).text();
		$('.schedules_container')[0].scrollTop = 0;
		var pos = $('.sort_'+theId.toUpperCase()+":visible").position();
		if(pos) { $('.schedules_container')[0].scrollTop = pos.top; }
	});

	/*** Allocation Type togle event ***/
	$('.title_med').click(function() {
		if($(this).hasClass('allocation_expanded')) {
            $(this).removeClass('allocation_expanded').addClass('allocation_collapsed');
			$(this).next().slideToggle("normal");
		} else if($(this).hasClass('allocation_collapsed')) {
            $(this).removeClass('allocation_collapsed').addClass('allocation_expanded');
			$(this).next().slideToggle("normal");
		}
	});

	/*** Change on filter drop down ***/
	$('.resource_types').change(function() {
		if($(this).val() != "") {
			$('.schedules_row').css("display", "none");
			$('.title_'+$(this).val()).css("display", "block");
		} else {
			$('.schedules_row').css("display", "block");
		}
    	$(".schedules_container").append(content_loader);
		$(".jumptoItem").each(function () {
			var theId = $(this).text();
			//alert('len: '+$('.sort_'+theId.toUpperCase()+":visible").length);
			if($('.sort_'+theId.toUpperCase()+":visible").length > 0) {
				$(this).css("display", "block");
			} else {
				$(this).css("display", "none");
			}
		});
		setTimeout('reloadContent()', 500);
	});

	$('.jumptodatecal').change(function() {
		var calField = ""+$('#basics').val();
		var calParts = calField.split("/");
		//var newDate = new Date(calParts[2], (parseInt(calParts[0])-1), calParts[1]);

		selDate = new Date(parseInt(calParts[2]), (parseInt(calParts[0])-1), parseInt(calParts[1]));
		startWeek = weekStart(2); //offset by 2 for date picker compensation

		changeDateContent();
	});

	$('.arrows_left').click(function() {
		var oldDate = selDate;
		oldDate.setDate(oldDate.getDate() - 7);

		selDate = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
		startWeek = weekStart(1);

		changeDateContent();
	});

	$('.arrows_right').click(function() {
		var oldDate = selDate;
		oldDate.setDate(oldDate.getDate() + 7);

		selDate = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
		startWeek = weekStart(1);

		changeDateContent();
	});

	$('#popHours #saveBtn').click(function(){
		$('.popHours').css('display', 'none');
		$('#blur').css({display: 'none'});

		var project = $('#popHours #overtime_project').val()
		var hours = $('#popHours #overtime_hours').val()
		var notes = $('#popHours #overtime_notes').val();
		var userid = otId;
		if(weekDate){ var useDate = weekDate; } else { var useDate = startWeek; }
		if(theId.length > 2) { var otTarget = "#" + theId[0] + "_" + theId[1] + "_" + theId[2]; } else { var otTarget = "#" + theId[0] + "_" + theId[1]; }
		//alert("overtime=true&projectid="+project+"&hours="+hours+"&notes="+notes+"&userid="+otId+"&date="+startWeek);
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/resource_planner_save.php",
			data: "overtime=true&projectid="+project+"&hours="+hours+"&notes="+notes+"&userid="+otId+"&date="+useDate,
			success: function(msg){
				$(otTarget).removeClass();
				if(project != "" && hours > 0) {
					$(otTarget).addClass("cancel");
				} else {
					$(otTarget).addClass("overtime");
				}
			}
		});
	});

	$('#popHours .cancel_ot').click(function(){
        $('.popHours').css({display: 'none'});
		$('#blur').css({display: 'none'});
		return false;
	});

	$('#popHours .clear_ot').click(function(){
		$('#popHours #overtime_project').val("")
		$('#popHours #overtime_hours').val("")
		$('#popHours #overtime_notes').val("");
		return false;
	});

	/** User View Month Display **/
	$('.month_controller_display').text(monthText[selDate.getMonth()+1]+" "+selDate.getFullYear())

	/** Adds event listeners for Allocation Types ***/
	selectionOptions();

	/** Update the displays for the current week ***/
	updateWeekDisplay();

	/*** Call Update Loading on Page Load to Get the initial values ***/
	updateLoading();
});

/*************************************
//Load the current date loading percentages
**************************************/
function updateLoading(){
	var dt = ''+selDate;
	var date_split = dt.split(" ");

	//Month format for date
	//alert(date_split[1]);
	switch(date_split[1]) {
		case 'Jan': {
			date_split[1] = 1;
			break;
		}
		case 'Feb': {
			date_split[1] = 2;
			break;
		}
		case 'Mar': {
			date_split[1] = 3;
			break;
		}
		case 'Apr': {
			date_split[1] = 4;
			break;
		}
		case 'May': {
			date_split[1] = 5;
			break;
		}
		case 'Jun': {
			date_split[1] = 6;
			break;
		}
		case 'Jly': {
			date_split[1] = 7;
			break;
		}
		case 'Aug': {
			date_split[1] = 8;
			break;
		}
		case 'Sep': {
			date_split[1] = 9;
			break;
		}
		case 'Oct': {
			date_split[1] = 10;
			break;
		}
		case 'Nov': {
			date_split[1] = 11;
			break;
		}
		case 'Dec': {
			date_split[1] = 12;
			break;
		}
	}
	var passDate = date_split[3]+"/"+date_split[1]+"/"+date_split[2];
	//alert("date="+passDate+"&part=week=");

	//Ajax call to get percent calues
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_rp_load_percents.php",
		data: "date="+passDate+"",
		success: function(msg){
			//alert(msg);
			var theMsg = msg;
			var msg_split = theMsg.split("_");

			$("#week_loading").html("<span>Week loading: "+msg_split[0]+"%</span>");
			$("#quarter_loading").html("<span>Quarter loading: "+msg_split[1]+"%</span>");
			$("#year_loading").html("<span>Year loading: "+msg_split[2]+"%</span>");
		}
	});
}

/*********************************************
//Get the first day of the week the current day is in
**********************************************/
function weekStart(dayOffset) {
	//var today = new Date();
	var today = selDate;
	var day = today.getDate();
	var month = today.getMonth() + 1;
	var year = today.getYear();
	if (year < 2000)
	year = year + 1900;
	var offset = today.getDay();
	var week;

	if(offset != 0) {
		day = day - offset;
		if ( day < 1) {
			if ( month == 1) day = 31 + day;
			if (month == 2) day = 31 + day;
			if (month == 3) {
				if (( year == 00) || ( year == 04)) {
					day = 29 + day;
				}
				else {
					day = 28 + day;
				}
			}

			if (month == 4) day = 31 + day;
			if (month == 5) day = 30 + day;
			if (month == 6) day = 31 + day;
			if (month == 7) day = 30 + day;
			if (month == 8) day = 31 + day;
			if (month == 9) day = 31 + day;
			if (month == 10) day = 30 + day;
			if (month == 11) day = 31 + day;
			if (month == 12) day = 30 + day;
			if (month == 1) {
				month = 12;
				year = year - 1;
			}
			else {
				month = month - 1;
			}
		}
	}
	var newDay = parseInt(day)+parseInt(dayOffset);
	week = month + "-" + newDay + "-" + year; // i.e. 10-31-99

	return week;
}

/***********************************
//Update the week dispaly areas on page
//And end week/last day data
************************************/
function updateWeekDisplay() {
	var start_date_parts = startWeek.split("-");
	var start_week = start_date_parts[0]+"/"+start_date_parts[1]+"/"+start_date_parts[2].substring(2,4)
	var end_week = "";
	var end_day;
	var end_month;
	var end_year;
	var last_day;

	switch(parseInt(start_date_parts[0])) {
		case 1: {
			if((parseInt(start_date_parts[1])+4) > 31) {
				end_day = (parseInt(start_date_parts[1])+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 2: {
			if((parseInt(start_date_parts[1])%4) == 0) {
				if((parseInt(start_date_parts[1])+4) > 29) {
					end_day = (parseInt(start_date_parts[1])+4) - 29;
					last_day = 29;
				} else {
					end_day = parseInt(start_date_parts[1])+4;
					last_day = parseInt(start_date_parts[1])+4;
				}
			} else {
				if((parseInt(start_date_parts[1])+4) > 28) {
					end_day = (parseInt(start_date_parts[1])+4) - 28;
					last_day = 28;
				} else {
					end_day = parseInt(start_date_parts[1])+4;
					last_day = parseInt(start_date_parts[1])+4;
				}
			}
			break;
		}
		case 3: {
			if((parseInt(start_date_parts[1])+4) > 31) {
				end_day = (parseInt(start_date_parts[1])+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 4: {
			if((parseInt(start_date_parts[1])+4) > 30) {
				end_day = (parseInt(start_date_parts[1])+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 5: {
			if((parseInt(start_date_parts[1])+4) > 31) {
				end_day = (parseInt(start_date_parts[1])+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 6: {
			if((parseInt(start_date_parts[1])+4) > 30) {
				end_day = (parseInt(start_date_parts[1])+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 7: {
			if((parseInt(start_date_parts[1])+4) > 31) {
				end_day = (parseInt(start_date_parts[1])+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 8: {
			if((parseInt(start_date_parts[1])+4) > 31) {
				end_day = (parseInt(start_date_parts[1])+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 9: {
			if((parseInt(start_date_parts[1])+4) > 30) {
				end_day = (parseInt(start_date_parts[1])+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 10: {
			if((parseInt(start_date_parts[1])+4) > 31) {
				end_day = (parseInt(start_date_parts[1])+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 11: {
			if((parseInt(start_date_parts[1])+4) > 30) {
				end_day = (parseInt(start_date_parts[1])+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
		case 12: {
			if((parseInt(start_date_parts[1])+4) > 31) {
				end_day = (parseInt(start_date_parts[1])+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1])+4;
				last_day = parseInt(start_date_parts[1])+4;
			}
			break;
		}
	}

	if((parseInt(start_date_parts[0]) == 12) &&  (end_day < last_day)) {
		var yr = "" + (parseInt(start_date_parts[2])+1);
		end_week = "1"+"/"+end_day+"/"+yr.substring(2,4);
		endWeek = "1"+"-"+end_day+"-"+yr;
	} else {
		if(end_day < last_day) {
			var newMon = parseInt(start_date_parts[0])+1;
			end_week = newMon+"/"+end_day+"/"+start_date_parts[2].substring(2,4);
			endWeek = newMon+"-"+end_day+"-"+start_date_parts[2];
		} else {
			end_week = start_date_parts[0]+"/"+end_day+"/"+start_date_parts[2].substring(2,4);
			endWeek = start_date_parts[0]+"-"+end_day+"-"+start_date_parts[2];
		}
	}

	var nxtDay = parseInt(start_date_parts[1]);
	var colmon;
	var colday;
	var days = new Array();
	days[0] = "MON";
	days[1] = "TUE";
	days[2] = "WED";
	days[3] = "THU";
	days[4] = "FRI";
	for(var i = 0; i < 5; i++) {
		if(end_day < last_day) {
			if(nxtDay > last_day) {
				if(parseInt(start_date_parts[0]) == 12) {
					colmon = 1;
					colyear = parseInt(start_date_parts[2])+1;
				} else {
					colmon = parseInt(start_date_parts[0])+1;
					colyear = parseInt(start_date_parts[2]);
				}
				colday =  nxtDay - last_day;
			} else {
				colmon = start_date_parts[0];
				colyear = parseInt(start_date_parts[2]);
				colday = nxtDay;
			}
		} else {
			if(nxtDay <= last_day) {
				colmon = start_date_parts[0];
				colyear = parseInt(start_date_parts[2]);
				colday = nxtDay;
			}
		}
		var col = (i+1);

		$('.'+col+'_col:first').html(days[i]+"<br />"+colmon+"/"+colday+'<span class="days_full_date">'+colmon+"/"+colday+"/"+colyear+'</span>');

		nxtDay += 1;
	}

	$('.week_label').html("for "+start_week+" - "+end_week);

	lastDay = last_day;

}

/******************************
//Reload the selectable calculations
********************************/
function reloadContent() {
	$(".schedules_controller").selectable("refresh");
    $(".content_loader").remove();
}

/**********************************
//Change the data Ajax
************************************/
function changeDateContent() {
	$('.schedules_row').css("display", "block");
	$('.resource_types').val(0);
	$(".schedules_container").append(content_loader);
	$('.schedules_container')[0].scrollTop = 0;
	updateWeekDisplay();

	$.ajax({
		type: "POST",
		url: "/_ajaxphp/resource_planner_new_data.php",
		data: "startDate="+startWeek+"&endDate="+endWeek+"&lastDay="+lastDay,
		success: function(json){
			loadNewData(json);
		},
		dataType: "json"
	});
}

/**********************************
//
************************************/
function saveSelected(op, pid){
	var selBlocks = Array();
	$('.ui-selected').each(function(i,el){
    	var ops = "";
		$.each(el, function(k,v){
			ops += k + ": " + v + "\n";
		});
		selBlocks[i] = el;
	});

	var selectedArray = new Array();
	selectedArray['status'] = op;
	selectedArray['projectID'] = pid;
	selectedArray['hoursType'] = $('.hours_type:checked').val();
	selectedArray['blocks'] = new Array();
	
	for (t = 0; t < selBlocks.length; t++) {
		var splitStr = selBlocks[t].id.split('_');
		var date = $('.' + splitStr[2] + '_col .days_full_date').text();
		if(!date){ date = splitStr[2].split('/'); } else { date = date.split('/'); }
		var day = date[1];

		selectedArray['blocks'].push({
			id: selBlocks[t].id,
			day: day,
			block: splitStr[3],
			user: splitStr[1],
			date: date[2] + '-' + date[0] + '-' + date[1]
		});
	}
	//alert(serialize(selectedArray));
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/resource_planner_save.php",
		/*url: "/_ajaxphp/print_r.php",*/
		data: "data=" + serialize(selectedArray),
		success: function(json){
			
		},
		datatype: "json"
	});
}

/**********************************
//
************************************/
function selectionOptions(){
	$('.key_deselect').click(function(){
		$('.ui-selected').removeClass('ui-selected').addClass('ui-selectee');
		$('context_menu').remove();
	});

	$('.key_blank').click(function(){
    	saveSelected(0, $(this).attr('id'));
		$('.ui-selected .slot_label').text('');
		$('.ui-selected .slot_title').text('');
		$('.ui-selected').removeClass('allocated overhead outofoffice convert overtime unavailable ui-selected');
		$('context_menu').remove();
	});

	$('.key_overhead').click(function(){
    	saveSelected(status_types.overhead, $(this).attr('id'));
		$('.ui-selected .slot_label').text('');
		$('.ui-selected .slot_title').text('');
		$('.ui-selected').removeClass('allocated overhead outofoffice convert overtime unavailable ui-selected').addClass('overhead ui-selectee');
		$('context_menu').remove();
	});

	$('.key_outoffice').click(function(){
    	saveSelected(status_types.outofoffice, $(this).attr('id'));
		$('.ui-selected .slot_label').text('');
		$('.ui-selected .slot_title').text('');
		$('.ui-selected').removeClass('allocated overhead outofoffice convert overtime unavailable ui-selected').addClass('outofoffice ui-selectee');
		$('context_menu').remove();
	});

	$('.key_convertbillable').click(function(){
    	saveSelected(status_types.convert, $(this).attr('id'));
		$('.ui-selected').removeClass('allocated overhead outofoffice convert overtime unavailable ui-selected').addClass('convert ui-selectee');
		$('context_menu').remove();
	});

	$('.hours ul li ul li').click(function(){
		var classType = "";
		var dateNum = "";
		if($('.hours_type:checked').val() == "actual") {
			saveSelected(status_types.convert, $(this).attr('id'));
			classType = "convert";
		} else {
			saveSelected(status_types.allocated, $(this).attr('id'));
			classType = "allocated";
		}
		//var txt = $(this).attr('title');
		var txt = $(this).attr('name');
		if(txt.length > 20) { var label = txt.substr(0, 20) + '...'; } else { var label = txt; }
		//alert('<div class="slot_label">'+label+'</div><div class="slot_title">'+txt+'</div>');
		
		if($('.ui-selected:first div').hasClass('schedule_day_date')) {
			dateNum = '<div class="schedule_day_date" style="float: left; width: 10px;">'+$('.ui-selected .schedule_day_date').text()+'</div>'
		} else {
			dateNum = '';
		}
		$('.ui-selected .slot_label').text(label);
		$('.ui-selected .slot_title').text(txt);
		//.html('<div class="slot_label">'+label+'</div><div class="slot_title">'+txt+'</div>')
		$('.ui-selected').removeClass('allocated overhead outofoffice convert overtime unavailable ui-selected').addClass(classType+' ui-selectee');
	});
}

/**********************************
//Load the new data to the page
************************************/
function loadNewData(json) {
	var theId;
	var blockClass;

	for(var i = 0; i < json.length; i++) {
		theId = "#dayblock_"+json[i]['userid']+"_"+json[i]['col']+"_"+json[i]['row']+":first";
		var txt;

		if(json[i]['class'] != "") {
			blockClass = json[i]['class'];
		} else {
			blockClass = "";
		}
		
		$(theId).removeClass();
		$(theId).addClass('sel ui-selectee');
		$(theId).addClass(blockClass);
		$(theId+" .slot_label:first").text(""+json[i]['project']+"");
		$(theId+" .slot_title:first").text(""+json[i]['tooltip']+"");
		$("#overtime_"+json[i]['userid']+":first").removeClass();
		$("#overtime_"+json[i]['userid']+":first").addClass(json[i]['overtime']);
		
		txt += 'the id: '+theId+' - the label text: '+$(theId+" .slot_label:first").text()+' - the title text: '+$(theId+" .slot_title:first").text()+'\n';
	}
	//Run ajax functions update functions below
	updateLoading();
	
	//Reload content selector
	contentTimeout = setTimeout('reloadContent()', 3000);
	//reloadContent();
}

/**********************************
//
************************************/
function serialize( mixed_value ) {
    var _getType = function( inp ) {
        var type = typeof inp, match;
        var key;
        if (type == 'object' && !inp) {
            return 'null';
        }
        if (type == "object") {
            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            if (match = cons.match(/(\w+)\(/)) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = '';

    switch (type) {
        case "function":
            val = "";
            break;
        case "undefined":
            val = "N";
            break;
        case "boolean":
            val = "b:" + (mixed_value ? "1" : "0");
            break;
        case "number":
            val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
            break;
        case "string":
            val = "s:" + mixed_value.length + ":\"" + mixed_value + "\"";
            break;
        case "array":
        case "object":
            val = "a";
            var count = 0;
            var vals = "";
            var okey;
            var key;
            for (key in mixed_value) {
                ktype = _getType(mixed_value[key]);
                if (ktype == "function" && ktype == "object") {
                    continue;
                }

                okey = (key.match(/^[0-9]+$/) ? parseInt(key) : key);
                vals += serialize(okey) +
                        serialize(mixed_value[key]);
                count++;
            }
            val += ":" + count + ":{" + vals + "}";
            break;
    }
    if (type != "object" && type != "array") val += ";";
    return val;
}

/**********************************
//Change the single user mode content
************************************/
function changeSingleUserContent() {
	$('.month_controller_display').text(monthText[selDate.getMonth()+1]+" "+selDate.getFullYear());
	
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/get_single_user_change_date.php",
		data: "userid="+$("#userid").val()+"&date="+selDate.getFullYear()+"/"+(selDate.getMonth()+1)+"/"+selDate.getDate(),
		success: function(msg){
			$('.schedules_controller').html(msg+"<br/>");
			$(".schedules_controller").selectable("refresh");
		}
	});
	
	updateLoading();
}
