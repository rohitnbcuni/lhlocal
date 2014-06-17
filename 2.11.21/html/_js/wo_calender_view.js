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
var fromArrow = false;
var basicValue = true;

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
$('#wo_dimmer_ajax_cal').css({display:'block'});

$(document).ready(function(){
$('.wo_month_controller_display').text(monthText[selDate.getMonth()+1]+" "+selDate.getFullYear());
/*
var seltdDate = (selDate.getMonth()+1+"-"+selDate.getFullYear());
var curPHPDate = $('#currentMonthData').val();

if(seltdDate == curPHPDate){
	changeSingleUserContent();
}*/
$('.month_arrows_left').click(function() {
	var oldDate = selDate;
	oldDate.setMonth(oldDate.getMonth() - 1);
	$('#wo_dimmer_ajax_cal').css({display:'block'});
	selDate = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
	workOrdercalender();
});

$('.month_arrows_right').click(function() {
	var oldDate = selDate;
	oldDate.setMonth(oldDate.getMonth() + 1);
	$('#wo_dimmer_ajax_cal').css({display:'block'});
	selDate = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
	workOrdercalender();
});



$('.single_user_date_jump').change(function () {
	var jumpDate = $(this).val();
	jumpDateSplit = jumpDate.split("/");
	$('#wo_dimmer_ajax_cal').css({display:'block'});
	selDate = new Date(parseInt(jumpDateSplit[2], 10), (parseInt(jumpDateSplit[0], 10)-1), parseInt(jumpDateSplit[1], 10));
	workOrdercalender();
});


$('.wo_month_controller_display').click(function(){
	var oldDate = selDate;
	//alert(oldDate.getMonth());
	var selectedMonth =oldDate.getMonth()+1;
	var selectedYear =oldDate.getFullYear();
	$("#mon_cal option[value=" + selectedMonth +"]").attr("selected","selected") ;
	$("#year_cal option[value=" + selectedYear +"]").attr("selected","selected") ;
	$('.popup_cal').css("display","block");
});

$('#update_cal').click(function(){
	var mon = $('#mon_cal').val();
	var yr = $('#year_cal').val();
	
	var oldDate = selDate;
	
	oldDate.setMonth(mon-1);
	oldDate.setFullYear(yr);
	$('#wo_dimmer_ajax_cal').css({display:'block'});
	selDate = new Date(oldDate.getFullYear(), oldDate.getMonth(), oldDate.getDate());
	workOrdercalender();
	$('.popup_cal').css("display","none");
	//oldDate.setMonth(oldDate.getMonth() + 1);
	//alert(oldDate);
	
});


});


function workOrdercalender() {
        $('#wo_dimmer_ajax_cal').css({display:'block'});
	$('.wo_month_controller_display').text(monthText[selDate.getMonth()+1]+" "+selDate.getFullYear());
	var req_type = $("#requestTypeFilter").val();
	var client_filter = $('.#client_filter').val();
	$.ajax({
		type: "POST",
		url: "/_ajaxphp/wo_calendarview_all.php",
		data: "userid="+$("#userid").val()+"&date="+selDate.getFullYear()+"/"+(selDate.getMonth()+1)+"/"+selDate.getDate()+'&status='+$("#project_status_filter").val()+'&client='+$("#client_filter").val()+'&proj_id='+$("#project_filter").val()+'&status_filter='+$("#status_filter").val()+'&assigned_to='+$("#assigned_filter").val()+'&requested_by='+$("#requestedby_filter").val()+'&req_type='+req_type+'&page_num='+page_no+'&column='+column_name+'&order='+sort_order+'&start_date='+$("#start_date_hidden").val()+'&end_date='+$("#end_date_hidden").val()+"&search="+$("#search_text").val(),
		success: function(msg){
		$('#wo_dimmer_ajax_cal').css({display:'none'});	
		$('.wo_calender_view').html(msg+"<br/>");
			//$(".schedules_controller").selectable("refresh");
		}
	});
	
	  
	//jQuery.getJSON('/_ajaxphp/workorder_json.php?status='+$("#project_status_filter").val()+'&client='+$("#client_filter").val()+'&proj_id='+$("#project_filter").val()+'&status_filter='+$("#status_filter").val()+'&assigned_to='+$("#assigned_filter").val()+'&requested_by='+$("#requestedby_filter").val()+'&req_type='+req_type+'&page_num='+page_no+'&column='+column_name+'&order='+sort_order+'&start_date='+$("#start_date_hidden").val()+'&end_date='+$("#end_date_hidden").val()+"&search="+$("#search_text").val(), function(json) {  //+"&search="+$("#search_text").val()
	//jQuery.getJSON('/_ajaxphp/workorder_json.php?status='+$("#project_status_filter").val()+'&client='+$("#client_filter").val()+'&proj_id='+$("#project_filter").val()+'&status_filter='+$("#status_filter").val()+'&assigned_to='+$("#assigned_filter").val()+'&requested_by='+$("#requestedby_filter").val()+'&req_type='+req_type+'&page_num='+page_no+'&column='+column_name+'&order='+sort_order+'&start_date='+$("#start_date_hidden").val()+'&end_date='+$("#end_date_hidden").val()+"&search="+$("#search_text").val(), function(json) {  //+"&search="+$("#search_text").val()
	//updateLoading();
}
/*********************************************
//Get the first day of the week the current day is in
**********************************************/
function weekStart(dayOffset) {
	//var today = new Date();
	var today = selDate;
	var day = today.getDate();
	var date = today.getDate();
	var month = today.getMonth();
	var year = today.getFullYear();
	var offset = today.getDay();
	today_month = new Date();
	today_month.setFullYear(year, month, 1);
	var offset_month = today_month.getDay();
	var week;

	var offset_array = new Array();
	offset_array[0] = 1;
	offset_array[1] = 0;
	offset_array[2] = -1;
	offset_array[3] = -2;
	offset_array[4] = -3;
	offset_array[5] = -4;
	offset_array[6] = -5;

	var days_array = new Array();
	days_array[0] =31;
	days_array[1] =31;
	days_array[3] =31;
	days_array[4] =30;
	days_array[5] =31;
	days_array[6] =30;
	days_array[7] =31;
	days_array[8] =31;
	days_array[9] =30;
	days_array[10] =31;
	days_array[11] =30;

	if (month == 2)	{
		if((year%4) == 0) { days_array[2] = 29; }
		else { days_array[2] = 28; }
	}

	if((offset_month+date) > 7){
		month_offset = month;
		days_offset = date + offset_array[offset];
	}else{
		month_offset = month - 1;
		days_offset = days_array[month] + date + offset_array[offset];
	}

	today_date = new Date();
	today_date.setFullYear(year, month_offset, days_offset);
	month = parseInt(today_date.getMonth(), 10) + 1;
	week = month + "-" + today_date.getDate() + "-" + today_date.getFullYear();

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

	switch(parseInt(start_date_parts[0], 10)) {
		case 1: {
			if((parseInt(start_date_parts[1], 10)+4) > 31) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 2: {
			if((parseInt(start_date_parts[1], 10)%4) == 0) {
				if((parseInt(start_date_parts[1], 10)+4) > 29) {
					end_day = (parseInt(start_date_parts[1], 10)+4) - 29;
					last_day = 29;
				} else {
					end_day = parseInt(start_date_parts[1], 10)+4;
					last_day = parseInt(start_date_parts[1], 10)+4;
				}
			} else {
				if((parseInt(start_date_parts[1], 10)+4) > 28) {
					end_day = (parseInt(start_date_parts[1], 10)+4) - 28;
					last_day = 28;
				} else {
					end_day = parseInt(start_date_parts[1], 10)+4;
					last_day = parseInt(start_date_parts[1], 10)+4;
				}
			}
			break;
		}
		case 3: {
			if((parseInt(start_date_parts[1], 10)+4) > 31) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 4: {
			if((parseInt(start_date_parts[1], 10)+4) > 30) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 5: {
			if((parseInt(start_date_parts[1], 10)+4) > 31) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 6: {
			if((parseInt(start_date_parts[1], 10)+4) > 30) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 7: {
			if((parseInt(start_date_parts[1], 10)+4) > 31) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 8: {
			if((parseInt(start_date_parts[1], 10)+4) > 31) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 9: {
			if((parseInt(start_date_parts[1], 10)+4) > 30) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 10: {
			if((parseInt(start_date_parts[1], 10)+4) > 31) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 11: {
			if((parseInt(start_date_parts[1], 10)+4) > 30) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 30;
				last_day = 30;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
		case 12: {
			if((parseInt(start_date_parts[1], 10)+4) > 31) {
				end_day = (parseInt(start_date_parts[1], 10)+4) - 31;
				last_day = 31;
			} else {
				end_day = parseInt(start_date_parts[1], 10)+4;
				last_day = parseInt(start_date_parts[1], 10)+4;
			}
			break;
		}
	}

	if((parseInt(start_date_parts[0], 10) == 12) &&  (end_day < last_day)) {
		var yr = "" + (parseInt(start_date_parts[2], 10)+1);
		end_week = "1"+"/"+end_day+"/"+yr.substring(2,4);
		endWeek = "1"+"-"+end_day+"-"+yr;
	} else {
		if(end_day < last_day) {
			var newMon = parseInt(start_date_parts[0], 10)+1;
			end_week = newMon+"/"+end_day+"/"+start_date_parts[2].substring(2,4);
			endWeek = newMon+"-"+end_day+"-"+start_date_parts[2];
		} else {
			end_week = start_date_parts[0]+"/"+end_day+"/"+start_date_parts[2].substring(2,4);
			endWeek = start_date_parts[0]+"-"+end_day+"-"+start_date_parts[2];
		}
	}

	var nxtDay = parseInt(start_date_parts[1], 10);
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
				if(parseInt(start_date_parts[0], 10) == 12) {
					colmon = 1;
					colyear = parseInt(start_date_parts[2], 10)+1;
				} else {
					colmon = parseInt(start_date_parts[0], 10)+1;
					colyear = parseInt(start_date_parts[2], 10);
				}
				colday =  nxtDay - last_day;
			} else {
				colmon = start_date_parts[0];
				colyear = parseInt(start_date_parts[2], 10);
				colday = nxtDay;
			}
		} else {
			if(nxtDay <= last_day) {
				colmon = start_date_parts[0];
				colyear = parseInt(start_date_parts[2], 10);
				colday = nxtDay;
			}
		}
		var col = (i+1);

		$('.'+col+'_col:first').html(days[i]+"<br />"+colmon+"/"+colday+'<span class="days_full_date">'+colmon+"/"+colday+"/"+colyear+'</span>');

		nxtDay += 1;
	}

	//$('.week_label').html("for "+start_week+" - "+end_week);

	lastDay = last_day;

}

function call_see_more(date){
	 $('.arrow').css("display","inline");
	  $('.next').css("display","inline");
		var cur_page = $('#pagination_per_page_'+date).val();
		var total_page = $('#pagination_count_'+date).val();
		if(total_page > 0){
			for(i=1;i<=total_page;i++){
				if(i == cur_page){
					//$('.pag_1').css("display","block");
					$('.pag_'+i).css("display","block");
				}else{
					$('.pag_'+i).css("display","none")
				}
			}
		}
		if(cur_page == 1){
			  $('.arrow').css("display","none");
		}
		
	    $('.message_cancel').css("display","block");
	    $('#see_more_'+date).css("display","block");
	
}
function page_pre(date){
	
	var cur_page = $('#pagination_per_page_'+date).val();
	cur_page = eval(cur_page)-eval(1);
	var total_page = $('#pagination_count_'+date).val();
	if(total_page > 0){
		for(i=1;i<=total_page;i++){
			if(i == cur_page){
				//$('.pag_1').css("display","block");
				$('.pag_'+i).css("display","block");
			}else{
				$('.pag_'+i).css("display","none")
			}
		}
	}
	if(cur_page == 1){
		  $('.arrow').css("display","none");
		  $('.next').css("display","inline");
	}else{
		$('.arrow').css("display","inline");
		 $('.next').css("display","inline");
	}
	var pagination = $('#pagination_count').val();
	var cur_page_1 = $('#pagination_per_page_'+date).val();
	//alert('-----'+cur_page_1);
	var start_pages = eval(cur_page)*eval(pagination)-eval(pagination)+eval(1);
	var per_page_records = eval(pagination)*eval(cur_page);
	$('.counter_per_page').html(start_pages+"-"+per_page_records)
	$('#pagination_per_page_'+date).val(cur_page);
	
}
function page_next(date){
	
	var cur_page = $('#pagination_per_page_'+date).val();
	cur_page = eval(cur_page)+eval(1);
	var total_page = $('#pagination_count_'+date).val();
	if(total_page > 0){
		for(i=1;i<=total_page;i++){
			if(i == cur_page){
				//$('.pag_1').css("display","block");
				$('.pag_'+i).css("display","block");
			}else{
				$('.pag_'+i).css("display","none")
			}
		}
	}
	if(cur_page == total_page){
		  $('.next').css("display","none");
		  $('.arrow').css("display","inline");
	}else{
		$('.next').css("display","inline");
		$('.arrow').css("display","inline");
	}
	var pagination = $('#pagination_count').val();
	var cur_page_1 = $('#pagination_per_page_'+date).val();
	var start_pages = (eval(pagination)*eval(cur_page_1))+eval(1);
	if(cur_page == total_page){
		
		var per_page_records = $('#total_records_'+date).val();
	}else{
		var per_page_records = eval(pagination)*eval(cur_page);
	}
		
	$('.counter_per_page').html(start_pages+"-"+per_page_records)
	$('#pagination_per_page_'+date).val(cur_page);
	
}


