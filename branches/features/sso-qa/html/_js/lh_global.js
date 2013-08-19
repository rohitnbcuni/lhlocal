function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		}
	}
}
function ajaxFunction(qString,action){
	//qString = "?project[desc]=test me&project[business_case]=test me";

	switch(action) {
		case 0: {			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data:qString,
				success: function(msg) {
					alert(msg);
				}
			});
			
			break;
		}
		case 1: {
			//create_company
			var comp_id = document.getElementById('create_company').value;
			qString = 'data_set=project_code&comp_id='+comp_id;
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data:qString,
				success: function(msg) {
					document.getElementById('projectCode').value = msg;
				}
			});
			
			break;
		}
		case 2: {
			//create_company
			var comp_id = document.getElementById('create_company2').value;
			qString = 'data_set=project_code&comp_id='+comp_id;
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data:qString,
				success: function(msg) {
					document.getElementById('projectCode2').value = msg;
				}
			});
			
			break;
		}
		case 'return': {
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/create_bc_project.php",
				data:qString,
				success: function(msg) {
					document.getElementById('project_id').value = msg;
					project_mode_update();
				}
			});
			
			break;
		}
		case 'update_project_desc': {
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
			var project_program = $('#project_program').val();
			var project_prog;
			if(project_program != 0){
				project_prog = '&project_program='+project_program;
			}
			qString = 'data_set=project_description&comp_id='+comp_id+'&desc='+escateQuates(FCKeditorAPI.GetInstance('descEditor').GetData())+'&section='+curSec+status+project_prog;
				$.ajax({
					type: "POST",
					url: "/_ajaxphp/update_project.php",
					data:qString,
					success: function(msg) {
						document.getElementById('ajax_loader').style.display = "none";
						if(msg == '0') {
							//alert('Changes have not been saved');
						} else if(msg == '1') {
							//alert('Changes have been saved');
						}
						changeSectionStatus();
					}
				});
			break;
		}
		case 'update_project_bcase': {
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
			qString = 'data_set=project_bcase&comp_id='+comp_id+'&desc='+escateQuates(FCKeditorAPI.GetInstance('bcaseEditor').GetData())+'&section='+curSec+status;
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data:qString,
				success: function(msg) {
					document.getElementById('ajax_loader').style.display = "none";
					if(msg == '0') {
						//alert('Changes have not been saved');
					} else if(msg == '1') {
						//alert('Changes have been saved');
					}
					changeSectionStatus();
				}
			});
			
			break;
		}
		case 'update_project_metrics': {
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
			qString = 'data_set=project_metrics&comp_id='+comp_id+'&desc='+escateQuates(FCKeditorAPI.GetInstance('metricsEditor').GetData())+'&section='+curSec+status;
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data:qString,
				success: function(msg) {
					document.getElementById('ajax_loader').style.display = "none";
					if(msg == '0') {
						//alert('Changes have not been saved');
					} else if(msg == '1') {
						//alert('Changes have been saved');
					}
					changeSectionStatus();
				}
			});
			
			break;
		}
		case 'update_project_deliverables': {
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
			qString = 'data_set=project_deliverables&comp_id='+comp_id+'&desc='+escateQuates(FCKeditorAPI.GetInstance('deliverEditor').GetData())+'&section='+curSec+status;
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data:qString,
				success: function(msg) {
					document.getElementById('ajax_loader').style.display = "none";
					if(msg == '0') {
						//alert('Changes have not been saved');
					} else if(msg == '1') {
						//alert('Changes have been saved');
					}
					changeSectionStatus();
				}
			});
			
			break;
		}
		case 'update_project_scope': {
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
			qString = 'data_set=project_scope&comp_id='+comp_id+'&desc='+escateQuates(FCKeditorAPI.GetInstance('scopeEditor').GetData())+'&section='+curSec+status;
			
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/update_project.php",
				data:qString,
				success: function(msg) {
					document.getElementById('ajax_loader').style.display = "none";
					if(msg == '0') {
						//alert('Changes have not been saved');
					} else if(msg == '1') {
						//alert('Changes have been saved');
					}
					changeSectionStatus();
				}
			});
			
			break;
		}
		case 'change_status': {
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/get_status.php",
				data:qString,
				success: function(msg) {
					if(msg != "") {
						if(uSec != '') {
							var theLi = document.getElementById(uSec);
							var liImage = theLi.getElementsByTagName("img");
							
							if(msg == "3") {
								liImage[0].src = "/_images/green_status.gif";
							} 
							else if(msg == "2") {
								liImage[0].src = "/_images/yellow_status.gif";
							} 
							else if(msg == "1") {
								liImage[0].src = "/_images/red_status.gif";
							}
							uSec = '';
						}
					}
				}
			});
			
			break;
		}
		case 'change_status_man': {
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/get_status.php",
				data:qString,
				success: function(msg) {
					if(msg != "") {
						if(uSecMan != '') {
							var theLi = document.getElementById(uSecMan);
							var liImage = theLi.getElementsByTagName("img");
							
							if(msg == "3") {
								liImage[0].src = "/_images/green_status.gif";
							} 
							else if(msg == "2") {
								liImage[0].src = "/_images/yellow_status.gif";
							} 
							else if(msg == "1") {
								liImage[0].src = "/_images/red_status.gif";
							}
							uSecMan = '';
						}
					}
				}
			});
			
			break;
		}
		case 'complete_status': {
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/complete_status.php",
				data:qString,
				success: function(msg) {
					if(msg != "") {
						if(uSec != '') {
							//alert(msg+':'+qString+":"+uSec);
							var theLi = document.getElementById(uSec);
							var liImage = theLi.getElementsByTagName("img");
							
							if(msg == 1) {
								liImage[0].src = "/_images/green_status.gif";
							}
							
							uSec = '';
						}
					}
				}
			});
			
			break;
		}
		case 'set_completeness': {
			$.ajax({
				type: "POST",
				url: "/_ajaxphp/get_completeness.php",
				data:qString,
				success: function(msg) {
					document.getElementById("progress_percent_text").innerHTML = "PROJECT brief COMPLETENESS: "+msg+"%";
					document.getElementById("progress_insider_bar").innerHTML = "<div class=\"insideBar\" style=\"width: "+msg+"%;\"></div>";
				}
			});
			
			break;
		}
		default: {
			$.ajax({
				type: "GET",
				url: "/_ajaxphp/test.php"+qString,
				success: function(msg) {
					alert(msg);
				}
			});
			
			break;
		}
	}
}
function utf8_encode (string) {
    // Encodes an ISO-8859-1 string to UTF-8  
    // 
    // version: 812.316
    // discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_utf8_encode

    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'
    string = (string+'').replace(/\r\n/g, "\n").replace(/\r/g, "\n");

    var utftext = "";
    var start, end;
    var stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128) {
            end++;
        } else if((c1 > 127) && (c1 < 2048)) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc != null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }

    if (end > start) {
        utftext += string.substring(start, string.length);
    }

    return utftext;
}
function base64_encode(data) {
    // Encodes string using MIME base64 algorithm  
    // 
    // version: 810.114
    // discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_base64_encode

    // +   original by: Tyler Akins (http://rumkin.com)
    // +   improved by: Bayron Guevara
    // +   improved by: Thunder.m
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)        
    // -    depends on: utf8_encode
    // *     example 1: base64_encode('Kevin van Zonneveld');
    // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
    // mozilla has this native
    // - but breaks in 2.0.0.12!
    //if (typeof window['atob'] == 'function') {
    //    return atob(data);
    //}
        
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = ac = 0, enc="", tmp_arr = [];
    data = utf8_encode(data);
    
    do { // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);

        bits = o1<<16 | o2<<8 | o3;

        h1 = bits>>18 & 0x3f;
        h2 = bits>>12 & 0x3f;
        h3 = bits>>6 & 0x3f;
        h4 = bits & 0x3f;

        // use hexets to index into b64, and append result to encoded string
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);
    
    enc = tmp_arr.join('');
    
    switch( data.length % 3 ){
        case 1:
            enc = enc.slice(0, -2) + '==';
        break;
        case 2:
            enc = enc.slice(0, -1) + '=';
        break;
    }

	// To handle the "+" in the encoded data
	enc = enc.replace(new RegExp( "\\+", "g" ),"%2B");
    return enc;
}

function base64_decode(data) {
    // Decodes string using MIME base64 algorithm  
    // 
    // version: 810.1317
    // discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_base64_decode

    // +   original by: Tyler Akins (http://rumkin.com)
    // +   improved by: Thunder.m
    // +      input by: Aman Gupta
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // -    depends on: utf8_decode
    // *     example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==');
    // *     returns 1: 'Kevin van Zonneveld'
    // mozilla has this native
    // - but breaks in 2.0.0.12!
    //if (typeof window['btoa'] == 'function') {
    //    return btoa(data);
    //}

    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = ac = 0, dec = "", tmp_arr = [];

    data += '';

    do {  // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));

        bits = h1<<18 | h2<<12 | h3<<6 | h4;

        o1 = bits>>16 & 0xff;
        o2 = bits>>8 & 0xff;
        o3 = bits & 0xff;

        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);
        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);

    dec = tmp_arr.join('');
    dec = utf8_decode(dec);

    return dec;
}


function showUserRisks(userID){
	$('#wo_dimmer_ajax').css({display:'block'});
	$("#user_risk_list").html('');
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/get_user_risks.php",
		data: "userID=" + userID+"&newRiskID=&"+Math.round(Math.random()*1000),
		success: function(msg) {
			$('#user_risk_list').html(msg);
			$('.user_risk_container').css({display:'block'});
			$('#wo_dimmer_ajax').css({display:'none'});
		}
	});
}
function changeRiskStatus(type, riskId){
	var statusValue = '0';
	if($('#risk_'+riskId).hasClass('complete') || $('#pp_risk_'+riskId).hasClass('complete')){
		statusValue = '1';
	}

	$('#wo_dimmer_ajax').css({display:'block'});
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/update_risks.php",
		data: "type=" + type + "&riskId=" +riskId+"&value="+statusValue+"&"+Math.round(Math.random()*1000),
		success: function(msg) {
			if('archive' == type){
				$('#risk_'+riskId).remove();
				$('#pp_risk_'+riskId).remove();
				$('.message_risk_create .risk_msg').html('The flag was deleted successfully.');
				$('.message_risk_create').css({display:"block"});
			}else if('active' == type){
				if($('#risk_'+riskId).hasClass('complete') || $('#pp_risk_'+riskId).hasClass('complete')){
					$('#risk_'+riskId).removeClass('complete');
					$('#pp_risk_'+riskId).removeClass('complete');
					$('#risk_'+riskId +' .risk_name img').attr({src: "/_images/flag_icon_red.png"});
					$('#pp_risk_'+riskId +' .risk_name img').attr({src: "/_images/flag_icon_red.png"});

					$("#pp_risk_complete_"+riskId+" INPUT[type='checkbox']").attr('checked', true);
					$("#risk_complete_"+riskId+" INPUT[type='checkbox']").attr('checked', true);

					$('.risk_done .closed_date_'+riskId).html('');
				}else{
					$('#risk_'+riskId).addClass('complete');
					$('#risk_'+riskId +' .risk_name img').attr({src: "/_images/flag_icon_green.png"});
					$('#pp_risk_'+riskId).addClass('complete');
					$('#pp_risk_'+riskId +' .risk_name img').attr({src: "/_images/flag_icon_green.png"});
					$("#pp_risk_complete_"+riskId+" INPUT[type='checkbox']").attr('checked', false);
					$("#risk_complete_"+riskId+" INPUT[type='checkbox']").attr('checked', false);
					$('.risk_done .closed_date_'+riskId).html(msg);
				}
			}
			$('#wo_dimmer_ajax').css({display:'none'});
		}
	});
}

function addComment(riskId, idPrefix){
	var topVar = ($('#'+idPrefix+'risk_comment_'+riskId).offset().top - 206);
	var leftVar = ($('#'+idPrefix+'risk_comment_'+riskId).offset().left - 72);
	$('#read_comment_block_'+riskId).css({display: 'none'});
	$('#'+idPrefix+'add_comment_block_'+riskId).css({top: topVar + 'px', left: leftVar + 'px'});
	$('#'+idPrefix+'add_comment_block_'+riskId).show("slide", { direction: "up" }, 500);
}

function pp_addComment(riskId, obj){
	var topVar = ($('#pp_risk_comment_'+riskId).offset().top - 10);
	$('#pp_read_comment_block_'+riskId).css({display: 'none'});
	$('#pp_add_comment_block_'+riskId).css({top: topVar + 'px'});
	$('#pp_add_comment_block_'+riskId).show("slide", { direction: "up" }, 500);
}
function riskComment(riskId, obj){
	var topVar = ($('#risk_comment_'+riskId).offset().top - 206);
	var leftVar = ($('#risk_comment_'+riskId).offset().left - 72);
	$('#read_comment_block_'+riskId).css({top: topVar + 'px', left: leftVar + 'px'});
	getRiskComments(riskId, '1', 'read_comment_block_'+riskId, '20');
	$('#read_comment_block_'+riskId).show("slide", { direction: "up" }, 500);
}
function pp_riskComment(riskId, obj){
	var topVar = ($('#pp_risk_comment_'+riskId).offset().top - 10);
	$('#pp_read_comment_block_'+riskId).css({top: topVar + 'px'});
	getRiskComments(riskId, '1', 'pp_read_comment_block_'+riskId, '1');
	$('#pp_read_comment_block_'+riskId).show("slide", { direction: "up" }, 500);
}
function getRiskComments(riskId, page, divID, perPage){
	$('#'+divID+' .risk_comments').fadeOut("slow");
	$.ajax({
		type: "GET",
		url: "/_ajaxphp/get_risk_comment.php",
		data: "riskId=" +riskId+"&page="+page+"&id="+divID+"&perPage="+perPage+"&"+Math.round(Math.random()*1000),
		success: function(msg) {
			$('#'+divID+' .risk_comments').html(msg);
			$('#'+divID+' .risk_comments').fadeIn("slow");
		}
	});
}
function submitComment(riskId, userId, id){
	/*id would be either pp_add_comment_content_(popup) OR add_comment_content_(project edit screen)*/
	var commentDesc = document.getElementById(id+riskId).value;
	if(commentDesc == ''){
		$('.message_risk_create .risk_msg').html('Enter a valid comment for the flag.');
		$('.message_risk_create').css({display:"block"});
	}else{
		$.ajax({
			type: "POST",
			url: "/_ajaxphp/add_risk_comment.php",
			data: { riskId : riskId, userId : userId, comment : commentDesc, randomID : Math.round(Math.random()*1000)},
			success: function(msg) {
				$('.message_risk_create .risk_msg').html('The comment was added successfully.');
				$('.message_risk_create').css({display:"block"});
				$('.risk_comment_count span.count_'+riskId).html(msg);
				$('#pp_add_comment_content_'+riskId).val('');
				$('#pp_add_comment_block_'+riskId).hide("slide", { direction: "up" }, 500);
				$('#add_comment_content_'+riskId).val('');
				$('#add_comment_block_'+riskId).hide("slide", { direction: "up" }, 500);
			}
		});
	}
}

//***********************
//** Filter Selection ***
//***********************
function Set_Cookie( name, value, expires, path, domain, secure )
{
	// set time, it's in milliseconds
	var today = new Date();
	today.setTime( today.getTime() );

	/*
	if the expires variable is set, make the correct
	expires time, the current script below will set
	it for x number of days, to make it for hours,
	delete * 24, for minutes, delete * 60 * 24
	*/
	if ( expires )
	{
	expires = expires * 1000 * 60 * 60 * 24 * 365;
	}
	var expires_date = new Date( today.getTime() + (expires) );

	document.cookie = name + "=" +escape( value ) +
	( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
	( ( path ) ? ";path=" + path : "" ) +
	( ( domain ) ? ";domain=" + domain : "" ) +
	( ( secure ) ? ";secure" : "" );
}
function getCookie(c_name)
{
	if (document.cookie.length>0)
	{
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1)
		{
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
				return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return "";
}

function escateQuates(desc)
{
	return escape(desc);
}

//***************************
//*** Cross-Browser Split ***
//***************************
var cbSplit;
// avoid running twice, which would break `cbSplit._nativeSplit`'s reference to the native `split`
if (!cbSplit) {

cbSplit = function (str, separator, limit) {
    // if `separator` is not a regex, use the native `split`
    if (Object.prototype.toString.call(separator) !== "[object RegExp]") {
        return cbSplit._nativeSplit.call(str, separator, limit);
    }

    var output = [],
        lastLastIndex = 0,
        flags = (separator.ignoreCase ? "i" : "") +
                (separator.multiline  ? "m" : "") +
                (separator.sticky     ? "y" : ""),
        separator = RegExp(separator.source, flags + "g"), // make `global` and avoid `lastIndex` issues by working with a copy
        separator2, match, lastIndex, lastLength;

    str = str + ""; // type conversion
    if (!cbSplit._compliantExecNpcg) {
        separator2 = RegExp("^" + separator.source + "$(?!\\s)", flags); // doesn't need /g or /y, but they don't hurt
    }

    /* behavior for `limit`: if it's...
    - `undefined`: no limit.
    - `NaN` or zero: return an empty array.
    - a positive number: use `Math.floor(limit)`.
    - a negative number: no limit.
    - other: type-convert, then use the above rules. */
    if (limit === undefined || +limit < 0) {
        limit = Infinity;
    } else {
        limit = Math.floor(+limit);
        if (!limit) {
            return [];
        }
    }

    while (match = separator.exec(str)) {
        lastIndex = match.index + match[0].length; // `separator.lastIndex` is not reliable cross-browser

        if (lastIndex > lastLastIndex) {
            output.push(str.slice(lastLastIndex, match.index));

            // fix browsers whose `exec` methods don't consistently return `undefined` for nonparticipating capturing groups
            if (!cbSplit._compliantExecNpcg && match.length > 1) {
                match[0].replace(separator2, function () {
                    for (var i = 1; i < arguments.length - 2; i++) {
                        if (arguments[i] === undefined) {
                            match[i] = undefined;
                        }
                    }
                });
            }

            if (match.length > 1 && match.index < str.length) {
                Array.prototype.push.apply(output, match.slice(1));
            }

            lastLength = match[0].length;
            lastLastIndex = lastIndex;

            if (output.length >= limit) {
                break;
            }
        }

        if (separator.lastIndex === match.index) {
            separator.lastIndex++; // avoid an infinite loop
        }
    }

    if (lastLastIndex === str.length) {
        if (lastLength || !separator.test("")) {
            output.push("");
        }
    } else {
        output.push(str.slice(lastLastIndex));
    }

    return output.length > limit ? output.slice(0, limit) : output;
};

cbSplit._compliantExecNpcg = /()??/.exec("")[1] === undefined; // NPCG: nonparticipating capturing group
cbSplit._nativeSplit = String.prototype.split;

} // end `if (!cbSplit)`

// Renaming the function so that it would be applicable from all the place...
String.prototype.search_split = function (separator, limit) {
    return cbSplit(this, separator, limit);
};


	 $(document).ready(function(){
		$(".top-menu-dropdown a.account").click(function(){
			var X=$(this).attr('id');if(X==1){$(".top-menu-dropdown .submenu").hide();$(this).attr('id', '0');
			}else{$(".top-menu-dropdown .submenu").show();$(this).attr('id', '1');}
		});
    //Mouse click on sub menu
	$(".submenu").mouseup(function(){return false});
    //Mouse click on my account link
	$(".account").mouseup(function(){return false});

    //Document Click
	$(document).mouseup(function(){$(".top-menu-dropdown .submenu").hide();$(".top-menu-dropdown .account").attr('id', '');});
	});
