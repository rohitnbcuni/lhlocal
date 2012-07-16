var pageScroll = 645;
var currentView = 3;

var header_html = "";
var calendar_html = "";

var baseScrollMult = 1;

var lc_week_single = 0;
var lc_week_title = 0;
var lc_week_span = 0;
/****Custom Scroller****/
/*
var scrollerWidth = {
  init:   function() {
  }
}
var scroller = {
  init:   function() {

    scroller.docH = document.getElementById("content").offsetHeight;
    scroller.contH = document.getElementById("container").offsetHeight;
    scroller.scrollAreaH = document.getElementById("scrollArea").offsetHeight;
    
    scroller.scrollH = (scroller.contH * scroller.scrollAreaH) / scroller.docH;
    if(scroller.scrollH < 15) scroller.scrollH = 15;
    document.getElementById("scroller").style.height = Math.round(scroller.scrollH) + "px";
    
    scroller.scrollDist = Math.round(scroller.scrollAreaH-scroller.scrollH);
    
    Drag.init(document.getElementById("scroller"),null,0,0,-1,scroller.scrollDist);
   
    document.getElementById("scroller").onDrag = function (x,y) {
      var scrollY = parseInt(document.getElementById("scroller").style.top);
      var docY = 0 - (scrollY * (scroller.docH - scroller.contH) / scroller.scrollDist);
      document.getElementById("content").style.top = docY + "px";
	  document.getElementById("contentcol2").style.top = docY + "px";
    }
	
	scrollerWidth.docW = document.getElementById("headercontent").offsetWidth;
    scrollerWidth.contW = document.getElementById("header").offsetWidth;
    scrollerWidth.scrollAreaW = document.getElementById("scrollAreaWidth").offsetWidth;
    
    scrollerWidth.scrollW = (scrollerWidth.contW * scrollerWidth.scrollAreaW) / scrollerWidth.docW;
    if(scrollerWidth.scrollW < 15) scrollerWidth.scrollW = 15;
    document.getElementById("scrollerWidth").style.width = Math.round(scrollerWidth.scrollW) + "px";
    
    scrollerWidth.scrollDist = Math.round(scrollerWidth.scrollAreaW-scrollerWidth.scrollW);
    
    Drag.init(document.getElementById("scrollerWidth"),null,-1,scrollerWidth.scrollDist,0,0);
   
    document.getElementById("scrollerWidth").onDrag = function (x,y) {
      var scrollX = parseInt(document.getElementById("scrollerWidth").style.left);
      var docX = 0 - (scrollX * (scrollerWidth.docW - scrollerWidth.contW) / scrollerWidth.scrollDist);
      document.getElementById("contentcol2").style.left = docX + "px";
	  document.getElementById("headercontent").style.left = docX + "px";
    }
  }
}*/
var baseWidths = {
	init:   function() {		
		var header = document.getElementById('project_header');
		var calendar = document.getElementById('project_calendar');
		
		header_html = header.innerHTML;
		calendar_html = calendar.innerHTML;
		
		document.getElementById('project_header').scrollLeft = document.getElementById('current_date').offsetLeft;
		document.getElementById('project_calendar').scrollLeft = document.getElementById('current_date').offsetLeft;
	}
}
function scrollDivsLeft() {
	var current = document.getElementById('project_header').scrollLeft;
	var ajs = current - pageScroll;
	
	document.getElementById('project_header').scrollLeft = ajs;
	document.getElementById('project_calendar').scrollLeft = ajs;
	
}
function scrollDivsRight() {
	var current = document.getElementById('project_header').scrollLeft;
	var ajs = current + pageScroll;
	
	document.getElementById('project_header').scrollLeft = ajs;
	document.getElementById('project_calendar').scrollLeft = ajs;
	
}
function updateScroll() {
	document.getElementById('project_header').scrollLeft = document.getElementById('project_calendar').scrollLeft;
	document.getElementById('project_list').scrollTop = document.getElementById('project_calendar').scrollTop;
}
function lcFilter(showAll) {
	if(showAll == "") {
		showAll = false;
	}
	
	var displayCont = true;
	var client = document.getElementById('client_filter').value;
	var producer = document.getElementById('producer_filter').value
	
	var projects = document.getElementById('project_list');
	var calendar = document.getElementById('project_calendar');	
	var projects_list = projects.getElementsByTagName('li');
	var calendar_list = calendar.getElementsByTagName('li');
	
	if(showAll) {
		client = document.getElementById('client_filter');
		producer = document.getElementById('producer_filter');
		var clientOption = client.getElementsByTagName('option');
		var producerOption = producer.getElementsByTagName('option');
		
		for(var i = 0; i < clientOption.length; i++) {
			clientOption[i].selected = false;
		}
		for(var i = 0; i < producerOption.length; i++) {
			producerOption[i].selected = false;
		}
		
		for(var i = 0; i < projects_list.length; i++) {
			var elem = projects_list[i];
			
			elem.style.display = "block";
		}
		for(var i = 0; i < calendar_list.length; i++) {
			var elem2 = calendar_list[i];
			
			elem2.style.display = "block";
		}
	}else {
		for(var i = 0; i < projects_list.length; i++) {
			var elem = projects_list[i];
			
			var elemIdPart = elem.id.split('_');
			
			if(client != "" && producer != "") {
				if(elemIdPart[0] == client && elemIdPart[1] == producer) {
					elem.style.display = "block";
				} else {
					elem.style.display = "none";
				}
			} else {
				if(client != "" || producer != "") {
					if(client != "" && producer == "") {
						if(elemIdPart[0] == client)	{
							elem.style.display = "block";
						} else {
							elem.style.display = "none";
						}
					}
					else if(client == "" && producer != "") {
						if(elemIdPart[1] == producer)	{
							elem.style.display = "block";
						} else {
							elem.style.display = "none";
						}
					} else {
						elem.style.display = "none";
					}
				} else {
					elem.style.display = "block";
				}
			}
		}
		for(var i = 0; i < calendar_list.length; i++) {
			var elem2 = calendar_list[i];
			
			var elemIdPart2 = elem2.id.split('_');
			
			if(client != "" && producer != "") {
				if(elemIdPart2[0] == client && elemIdPart2[1] == producer) {
					elem2.style.display = "block";
				} else {
					elem2.style.display = "none";
				}
			} else {
				if(client != "" || producer != "") {
					if(client != "" && producer == "") {
						if(elemIdPart2[0] == client)	{
							elem2.style.display = "block";
						} else {
							elem2.style.display = "none";
						}
					}
					else if(client == "" && producer != "") {
						if(elemIdPart2[1] == producer)	{
							elem2.style.display = "block";
						} else {
							elem2.style.display = "none";
						}
					} else {
						elem2.style.display = "none";
					}
				} else {
					elem2.style.display = "block";
				}
			}
		}
	}
	
	return false;
}
function getlefts() {
	var header = document.getElementById('project_header');
	var headerElms = header.getElementsByTagName('div');
	
	var pos = "";
	//alert(header.scrollLeft);
	//alert(header.scrollLeft/2);
	for(var i = 0; i < headerElms.length; i++) {
		if(headerElms[i].className == "lc_week_single") {
			pos += (headerElms[i].offsetParent.offsetLeft) +",";
		}
	}
	
	//alert(pos);
}

function weeksView(set) {
	var header = document.getElementById('project_header');
	var calendar = document.getElementById('project_calendar');
	
	switch(set) {
		case 6: {
			var header6 = "";
			var calendar6 = "";
			
			header.innerHTML = header_html;
			calendar.innerHTML = calendar_html;
			
			if(baseScrollMult==1) {
                $('#project_calendar').scrollLeft($('#project_calendar').scrollLeft()/2);
			} else if(baseScrollMult==3) {
                $('#project_calendar').scrollLeft($('#project_calendar').scrollLeft()*1.5);
			}
			
			var headerElms = header.getElementsByTagName('div');
			var calendarElms = calendar.getElementsByTagName('li');
			
			for(var h = 0; h < headerElms.length; h++) {
				if(headerElms[h].className == "lc_weeks_container") {
					var container = headerElms[h].getElementsByTagName('div');
					header6 += "<div class=\"lc_weeks_container\" style=\"width: 13731px;\"><!-- Week -->";
					
					for(var c = 0; c < container.length; c++) {
						if(container[c].className == "lc_week_single") {
							var single = container[c].getElementsByTagName('div');
							var newSingleWidth = container[c].offsetWidth/2;
							header6 += "<div class=\"lc_week_single\" style=\"width: "+newSingleWidth+"px; padding: 0 0 0 0;\">";
							
							for(var s = 0; s < single.length; s++) {
								if(single[s].className == "lc_week_title") {
									var newWidth = single[s].offsetWidth/2
									header6 += "<div class=\"lc_week_title\" style=\"width: "+newWidth+"px;\">";
									header6 += single[s].innerHTML;
									header6 += "</div>";
								}
								if(single[s].className == "lc_days_container") {
									var days = single[s].getElementsByTagName('span');
									header6 += "<div class=\"lc_days_container\">";
									for(var d = 0; d < days.length; d++) {
										var cl = "";
										var newWidth = days[d].offsetWidth/2;
										
										if(days[d].className == "last_day") {
											cl = " class=\"last_day\""
										} else {
											cl = "";
										}
										
										header6 += "<span"+cl+" style=\"width: "+newWidth+"px; padding: 0 0 0; font-size: 7px;\">";
										header6 += days[d].innerHTML;
										header6 += "</span>";
									}
									header6+= "</div>";
								}
							}
							
							header6+= "</div>";
						}
					}
					
					header6 += "</div>";
					break;
				}
			}
			
			for(var cal = 0; cal < calendarElms.length; cal++) {	
				var newLiWidth = calendarElms[cal].offsetWidth/2;
				calendar6 += "<li id=\""+calendarElms[cal].id+"\" style=\"height: "+(calendarElms[cal].offsetHeight-1)+"px; width: "+newLiWidth+"px;\">";
				
				var dl = calendarElms[cal].getElementsByTagName('dl');
				for(var d = 0; d < dl.length; d++) {
					if(dl[d].className == "lc_timeline_container") {
						var dlDiv = dl[d].getElementsByTagName('div');
						var dlDd = dl[d].getElementsByTagName('dd');
						
						var newDlWidth = dl[d].offsetWidth/2;
						var newDlLeft = dl[d].offsetLeft/2;
						
						calendar6 += "<dl class=\"lc_timeline_container\" style=\"left: "+newDlLeft+"px; width: "+newDlWidth+"px; height: "+(dl[d].offsetHeight-1)+"px;\">";
						
						for(var dv = 0; dv < dlDiv.length; dv++) {
							if(dlDiv[dv].className == "lc_timeline_all") {
								var newDlDivWidth = dlDiv[dv].offsetWidth/2;
								calendar6 += "<div class=\"lc_timeline_all\" style=\"width: "+newDlDivWidth+"px; height: "+(dlDiv[dv].offsetHeight-1)+"px;\"></div>"
								break;
							}
						}
						for(var t = 0; t < dlDd.length; t++) {
							var ddDivs = dlDd[t].getElementsByTagName('div');
							
							calendar6 += "<dd>";
							for(var y = 0; y < ddDivs.length; y++)	{
								if(ddDivs[y].className == "lc_timeline_single") {
									var ddDivSpan = ddDivs[y].getElementsByTagName('span');
									var ddDivDiv = ddDivs[y].getElementsByTagName('div');
									var ddDivNewWidth = ddDivs[y].offsetWidth/2;
									var ddDivNewLeft = ddDivs[y].offsetLeft/2;
									var ddDivDivNewWidth = ddDivDiv[0].offsetWidth/2;
									calendar6 += "<div class=\"lc_timeline_single\" style=\"width: "+ddDivNewWidth+"px; left: "+ddDivNewLeft+"px;\">"
										calendar6 += "<span>"+ddDivSpan[0].innerHTML+"</span>";
										calendar6 += "<div class=\"lc_timeline_burn\" style=\"width: "+ddDivDivNewWidth+"px;\"></div>";
									calendar6 += "</div>";
									break;
								}
							}						
							calendar6 += "</dd>";
						}
						
						calendar6 += "</dl>";
						break;
					}
				}
				
				calendar6 += "</li>";
			}
			
			header.innerHTML = header6;
			calendar.innerHTML = calendar6;
			
			baseScrollMult = 2;
			
			break;
		}
		case 9: {
			var header6 = "";
			var calendar6 = "";
			
			header.innerHTML = header_html;
			calendar.innerHTML = calendar_html;
			
			if(baseScrollMult==1) {
                $('#project_calendar').scrollLeft($('#project_calendar').scrollLeft()/3);
			} else if(baseScrollMult==2) {
                $('#project_calendar').scrollLeft($('#project_calendar').scrollLeft()/1.5);
			}
			
			var headerElms = header.getElementsByTagName('div');
			var calendarElms = calendar.getElementsByTagName('li');
			
			for(var h = 0; h < headerElms.length; h++) {
				if(headerElms[h].className == "lc_weeks_container") {
					var container = headerElms[h].getElementsByTagName('div');
					header6 += "<div class=\"lc_weeks_container\" style=\"width: 13731px;\"><!-- Week -->";
					
					for(var c = 0; c < container.length; c++) {
						if(container[c].className == "lc_week_single") {
							var single = container[c].getElementsByTagName('div');
							var newSingleWidth = container[c].offsetWidth/3;
							header6 += "<div class=\"lc_week_single\" style=\"width: "+newSingleWidth+"px; padding: 0 0 0 0;\">";
							
							for(var s = 0; s < single.length; s++) {
								if(single[s].className == "lc_week_title") {
									var html = single[s].innerHTML;
									html = html.replace(/ /,"-");
									html = html.replace(/, /,"-");
									html = html.replace(/january/i,"1");
									html = html.replace(/february/i,"2");
									html = html.replace(/march/i,"3");
									html = html.replace(/april/i,"4");
									html = html.replace(/may/i,"5");
									html = html.replace(/june/i,"6");
									html = html.replace(/july/i,"7");
									html = html.replace(/august/i,"8");
									html = html.replace(/september/i,"9");
									html = html.replace(/october/i,"10");
									html = html.replace(/november/i,"11");
									html = html.replace(/december/i,"12");
									var newWidth = single[s].offsetWidth/3
									header6 += "<div class=\"lc_week_title\" style=\"width: "+newWidth+"px;\">";
									header6 += html;
									header6 += "</div>";
								}
								if(single[s].className == "lc_days_container") {
									var days = single[s].getElementsByTagName('span');
									header6 += "<div class=\"lc_days_container\">";
									for(var d = 0; d < days.length; d++) {
										var cl = "";
										var newWidth = days[d].offsetWidth/3;
										
										if(days[d].className == "last_day") {
											cl = " class=\"last_day\""
										} else {
											cl = "";
										}
										
										header6 += "<span"+cl+" style=\"width: "+newWidth+"px; padding: 0 0 0;\">";
										header6 += "</span>";
									}
									header6+= "</div>";
								}
							}
							
							header6+= "</div>";
						}
					}
					
					header6 += "</div>";
					break;
				}
			}
			
			for(var cal = 0; cal < calendarElms.length; cal++) {	
				var newLiWidth = calendarElms[cal].offsetWidth/3;
				calendar6 += "<li id=\""+calendarElms[cal].id+"\" style=\"height: "+(calendarElms[cal].offsetHeight-1)+"px; width: "+newLiWidth+"px;\">";
				
				var dl = calendarElms[cal].getElementsByTagName('dl');
				for(var d = 0; d < dl.length; d++) {
					if(dl[d].className == "lc_timeline_container") {
						var dlDiv = dl[d].getElementsByTagName('div');
						var dlDd = dl[d].getElementsByTagName('dd');
						
						var newDlWidth = dl[d].offsetWidth/3;
						var newDlLeft = dl[d].offsetLeft/3;
						
						calendar6 += "<dl class=\"lc_timeline_container\" style=\"left: "+newDlLeft+"px; width: "+newDlWidth+"px; height: "+(dl[d].offsetHeight-1)+"px;\">";
						
						for(var dv = 0; dv < dlDiv.length; dv++) {
							if(dlDiv[dv].className == "lc_timeline_all") {
								var newDlDivWidth = dlDiv[dv].offsetWidth/3;
								calendar6 += "<div class=\"lc_timeline_all\" style=\"width: "+newDlDivWidth+"px; height: "+(dlDiv[dv].offsetHeight-1)+"px;\"></div>"
								break;
							}
						}
						for(var t = 0; t < dlDd.length; t++) {
							var ddDivs = dlDd[t].getElementsByTagName('div');
							
							calendar6 += "<dd>";
							for(var y = 0; y < ddDivs.length; y++)	{
								if(ddDivs[y].className == "lc_timeline_single") {
									var ddDivSpan = ddDivs[y].getElementsByTagName('span');
									var ddDivDiv = ddDivs[y].getElementsByTagName('div');
									var ddDivNewWidth = ddDivs[y].offsetWidth/3;
									var ddDivNewLeft = ddDivs[y].offsetLeft/3;
									var ddDivDivNewWidth = ddDivDiv[0].offsetWidth/3;
									calendar6 += "<div class=\"lc_timeline_single\" style=\"width: "+ddDivNewWidth+"px; left: "+ddDivNewLeft+"px;\">"
										calendar6 += "<span>"+ddDivSpan[0].innerHTML+"</span>";
										calendar6 += "<div class=\"lc_timeline_burn\" style=\"width: "+ddDivDivNewWidth+"px;\"></div>";
									calendar6 += "</div>";
									break;
								}
							}						
							calendar6 += "</dd>";
						}
						
						calendar6 += "</dl>";
						break;
					}
				}
				
				calendar6 += "</li>";
			}
			
			header.innerHTML = header6;
			calendar.innerHTML = calendar6;
			
			baseScrollMult = 3;
			
			break;
		}
		default: {
			//3 weeks is default			
			header.innerHTML = header_html;
			calendar.innerHTML = calendar_html;
			
            $('#project_calendar').scrollLeft($('#project_calendar').scrollLeft()*baseScrollMult);
			
			baseScrollMult = 1;
			
			break;
		}
	}
	
	return false;
}
$(document).ready(function(){
	$('.lc_timeline_container').click(function(){
		//alert($(this).offset().left);
	});
});
onload=baseWidths.init;