<?php

ob_start();

/**
 * Implemenation based on Cake's Sanitize logic
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 *		Copyright 2005-2007,	Cake Software Foundation, Inc.
 *		1785 E. Sahara Avenue, Suite 490-204
 *		Las Vegas, Nevada 89104
 *
 */
class Util {
	
	/**
	 * Removes any non-alphanumeric characters.
	 *
	 * @param string $string String to sanitize
	 * @return string Sanitized string
	 * @static
	 */
	public static function paranoid($string, $allowed = array()) {
		$allow = null;
		if (!empty($allowed)) {
			foreach ($allowed as $value) {
				$allow .= "\\$value";
			}
		}

		if (is_array($string)) {
			$cleaned = array();
			foreach ($string as $key => $clean) {
				$cleaned[$key] = preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $clean);
			}
		} else {
			$cleaned = preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $string);
		}
		return $cleaned;
	}
	
	/**
	 * Makes a string MySQL safe. You should use this around EVERY
	 * input from $_POST and $_GET
	 *
	 */
	public static function escape($string)
	{
		
		// this is part of the Cake framework which apparently stops
		// a known PHP exploit. Honestly, I'm not sure which one but
		// I'd rather be safe than sorry..
		if ((is_int($string) || is_float($string) || $string === '0') || (
			is_numeric($string) && strpos($string, ',') === false &&
			$data[0] != '0' && strpos($string, 'e') === false)) 
		{
			return $string;
		}
		
		if (is_numeric($string) || is_bool($string)) {
			return $string;
		}
		
		if ($string === null) {
			return 'NULL';
		}

		$engine = &Engine::getEngine();
		$string = "'" . mysqli_real_escape_string($engine->getDBHandle(), $string) . "'";

		return $string;

	}
	public static function escapewordquotes ($text) {
		$pre = chr(226).chr(128);
		$badwordchars=array('�','�','�','apos;',"#039;","�","�",'&#233;','&#8216;','&#8217;',
		'&#8230;',
		'&#8217;',
		'&#8220;',
		'&#8221;',
		'&#8212;',
		'#8212;',
		'#&8211;',
		'#8211;',
		'amp;',
		'&#160;',
		'#160;'
			
		);
		$fixedwordchars = array('','"','"',"'","'",",","'", "e","'","'",'~','~','','','_','-','-','-','','');
	    $text = str_replace($badwordchars,$fixedwordchars,$text);                         
		$text=str_replace('�',"'",$text); 
	    $text=str_replace('�',"'",$text); 
	    $text=str_replace('&amp;rsquo;',"'",$text); 
//    $text = preg_replace('/[^(\x20-\x7F)]*/','', $text );  
		$text = str_replace("&#8216;","",$text);
		//LH#    20679
		$text = str_replace(array("“","’","”"),array('"',"'",'"'),$text);
//		$text = str_replace("&","",$text);
//		$text = preg_replace('/[^\x00-\x7f]/','',$text);
		return $text;


	}

         /**
	 * remove Non prinatble characters
	 * @param desc body $str
	 * @return string of desc body
	 */
	static function nonPrintableChar($str){
		$search_str = array('�');
		$replace_str = array('');
		return str_replace($search_str, $replace_str, $str);
		
	}
		
	static function dateTimeToSql($date,$time,$ampm,$min)
			{
				$ampm = strtolower($ampm);
				if(!empty($date)) 
				{
					$dt_part = @explode("/", $date);				
		
					if(!empty($time)) 
					{
						$tm_part = @explode(":", $time);				
						if($ampm == "pm") {
							if($tm_part[0] < 12) {
								$tmAdd = 12;
							} else {
								$tmAdd = 0;
							}
						} else {
							if($ampm == "am") {
								if($tm_part[0] == 12) {
									$tmAdd = -12;
								} else {
									$tmAdd = 0;
								}
							} else {
								$tmAdd = 0;
							}
						}
					} else {
						$tm_part[0] = 0;
						$tm_part[1] = 0;
						$tm_part[2] = 0;					
						$tmAdd = 0;
					}
					$sql_date = "'" .@date("Y-n-j G:i:s", @mktime(@$tm_part[0]+$tmAdd , @$tm_part[1]+$min, @$tm_part[2], @$dt_part[0], @$dt_part[1], @$dt_part[2])) ."'";
				} 
				else 
				{
					$dt_part[0] = 0;
					$dt_part[1] = 0;
					$dt_part[2] = 0;
					
					$tm_part[0] = 0;
					$tm_part[1] = 0;
					$tm_part[2] = 0;
					
					$tmAdd = 0;
					
					$sql_date = "null";
				}
				$sql_date = @date("Y-n-j G:i:s", @mktime(@$tm_part[0]+$tmAdd , @$tm_part[1]+$min, @$tm_part[2], @$dt_part[0], @$dt_part[1], @$dt_part[2]));
				return $sql_date;
		  }
	
	 public static function escapeString($description){
            $pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            $desc =  $description;
            
            $desc= htmlentities($desc,ENT_NOQUOTES, 'UTF-8');
           
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",$desc);
            //$desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($desc,ENT_NOQUOTES, 'ISO-8859-1'));
            
            $desc_string = nl2br($desc_string);
            return $desc_string; 
       }
	 
	 public static function escapeTitle($title){
	 	$view = new Zend_View();
	 	$view->setEscape('htmlentities');
	 	return $view->escape($title);
	 }

		     
      public static function truncate ($str, $length=10, $trailing='...')
      { 
      /*
      ** $str -String to truncate
      ** $length - length to truncate
      ** $trailing - the trailing character, default: "..."
      */
      // take off chars for the trailing
   
  	   		$length-=strlen($trailing);
  
	        if (strlen($str)> $length)
	        {
	        	// string exceeded length, truncate and add trailing dots
	         	return substr($str,0,$length).$trailing;
	        }
	        else
	        {
	        	// string was already short enough, return the string
	        	$res = $str;
	        }
        	return $res;
       }
       
       public static function moreOrLessDescription($str,$length=250, $trailing='...'){
       	  	
       		$arr = array();
       		$str = Util::escapeString($str);
       		 
       		$arr['full'] = $str;
			
	        if(strlen($str) > $length)
	        {
	        	// string exceeded length, truncate and add trailing dots
	        	 $arr['show'] = true;
	        	 //$arr['short'] = substr($str,0,$length).$trailing.$more;
	        	 
	        	 if(substr($str,0,strpos($str,' ',$length))==""){
	        	 	$arr['short'] = chunk_split(substr($str,0,$length),50,"<br/>\n");
	        	 
	        	 }else{
		        	$arr['short'] = substr($str,0,strpos($str,' ',$length));
	        	 }
	        	 
	        }
	        else
	        {
	        	// string was already short enough, return the string
	        	$arr['show'] = false;
	        	$arr['short'] = $str; 
	        	
	        }
      	
        	return $arr;
       }
	public static function calTitle($str,$inl =0, $length=30){
		//$length=30;
		if(strlen($str) > $length)
	        {
	        	// string exceeded length, truncate and add trailing dots
	        	 	        	 
	        	$str = substr($str,$inl,$length);
	        	$str = $str."..";
	        }	        
	        
		return htmlentities($str,ENT_QUOTES,'UTF-8');
	}
	
	public static function htmlEntityTitle($str){
		return htmlentities($str,ENT_QUOTES,'UTF-8');
	}
	
	public static function dateDiffComment($commentDate){ 
				$dateDiff = array();
				
				

				$diff = abs(strtotime($commentDate) - time()); 

				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 

				$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 

				$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
				$dateDiff['years'] = $years;
				$dateDiff['months'] = $months;
				$dateDiff['days'] = $days;	
				$dateDiff['hours'] = $hours;
				$dateDiff['minuts'] = $minuts;
				$dateDiff['seconds'] = $seconds;		
				return $dateDiff;
						
			}
			
			
			
		public static function UpdateMileStoneComment($arrayBasecamp){
			global $mysql;
			include_once('../../application/library/Basecamp.class.php');
			$bc = new Basecamp(BASECAMP_HOST,BASECAMP_USERNAME,BASECAMP_PASSWORD,'xml');
					//createMilteStone($woAssignedTo,$getWoId,$woTitle,$woDesc,$require_date);
			if(count($arrayBasecamp) > 1){
				$us_bs = $mysql->sqlprepare("SELECT bc_id,CONCAT_WS(' ', first_name,last_name) as user_name FROM users WHERE id = ?",array($arrayBasecamp['created_by']));
				$us_bs_row = $us_bs->fetch_assoc();
				$responsible_party_id = $us_bs_row['bc_id'];
				$comment_msg = "<b>".ucfirst($us_bs_row['user_name']) ."</b> has commented ";
				//$message_id = '44850944';
				//echo "SELECT milestone_id FROM bs_milestone WHERE wid = '".$arrayBasecamp['workorder_id']."'"; die;
				//Get Milestone ID from bs_mapping
				
				$bs_milestone = $mysql->sqlprepare("SELECT milestone_id FROM bs_milestone WHERE wid = ?",array($arrayBasecamp['workorder_id']));
				//If workorder  Mapped with Basecam Milestone
				if($bs_milestone->num_rows > 0){
					$bs_milestone_row = $bs_milestone->fetch_assoc();
					$bs_milestone_id = $bs_milestone_row['milestone_id'];
					$body = $comment_msg."<br/>".str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$arrayBasecamp['comment']);
					$result = $bc->createCommentForMilestone($bs_milestone_id,$body);
				}
			
			}
			
			
			
			
		}
	//Create Milestone on basecamp
	function createMileStone($woAssignedTo,$getWoId,$woTitle,$require_date){
		global $mysql;
		//Check if assigned to exist in LH BS mapping table
		if(defined('BASECAMP_MAPPING')){
			if(BASECAMP_MAPPING == 'OPEN'){
				
				
				$bs_mapping = "SELECT * FROM lh_basecamp_mapping WHERE assigned_to = '$woAssignedTo' LIMIT 1";
				$us_bs = $mysql->sqlordie($bs_mapping);
				
				if($us_bs->num_rows > 0) {
					include_once('../../application/library/Basecamp.class.php');
					$bc = new Basecamp(BASECAMP_HOST,BASECAMP_USERNAME,BASECAMP_PASSWORD,'xml');
					$bs_mapping_row = $us_bs->fetch_assoc();
					$us_bs = $mysql->sqlprepare("SELECT bc_id FROM users WHERE id = ?",array($woAssignedTo));
					$us_bs_row = $us_bs->fetch_assoc();
					
					$project_bs_id = $bs_mapping_row['bc_id'];
					//echo "<br/>";
					$responsible_party_id = $us_bs_row['bc_id'];
					//echo "<br/>";
					$bc = new Basecamp(BASECAMP_HOST,BASECAMP_USERNAME,BASECAMP_PASSWORD,'xml');
					//createMilteStone($woAssignedTo,$getWoId,$woTitle,$woDesc,$require_date);
					$notify = false;
					$responsible_party_type = 'person';
					$woTitle = "LH[".$getWoId."] ".$woTitle;
					$basecamp_result = array();
	    			$basecamp_result = $bc->createMilestoneForProject($project_bs_id, $woTitle,$require_date, $responsible_party_type, $responsible_party_id,$notify);
					//print_r($basecamp_result);
	    			if(ISSET($basecamp_result['id'])){
	    				if($basecamp_result['id'] != ''){
	    				//echo "INSERT INTO `bs_milestone` SET milestone_id = '".$basecamp_result['id']."', wid = '".$getWoId."' , created_by = '".$woAssignedTo."', created_on ='now()'";
							$insert_basecamp_sql = $mysql->sqlordie("INSERT INTO `bs_milestone` SET milestone_id = '".$basecamp_result['id']."', wid = '".$getWoId."' , created_by = '".$woAssignedTo."', created_on ='".date('Y-m-d')."'");
	    				}
						
					}
				}
			
				
			}
			
		 
		}
	 
	}
	
	function updateMileStone($woAssignedTo,$getWoId,$woTitle,$require_date,$st_status){
		global $mysql;
		//Check if assigned to exist in LH BS mapping table
		if(defined('BASECAMP_MAPPING')){
			if(BASECAMP_MAPPING == 'OPEN'){
				
				$bs_mapping = "SELECT * FROM lh_basecamp_mapping WHERE assigned_to = '$woAssignedTo' LIMIT 1";
				$us_bs = $mysql->sqlordie($bs_mapping);
				
				if($us_bs->num_rows > 0) {
					include_once('../../application/library/Basecamp.class.php');
					$bc = new Basecamp(BASECAMP_HOST,BASECAMP_USERNAME,BASECAMP_PASSWORD,'xml');
					$bs_mapping_row = $us_bs->fetch_assoc();
					$us_bs = $mysql->sqlprepare("SELECT bc_id FROM users WHERE id = ?",array($woAssignedTo));
					$us_bs_row = $us_bs->fetch_assoc();
					
					$project_bs_id = $bs_mapping_row['bc_id'];
					//echo "<br/>";
					$responsible_party_id = $us_bs_row['bc_id'];
					//Check if Project Existing in BS_MAPPING
					$bs_milestone = $mysql->sqlordie("SELECT milestone_id FROM bs_milestone WHERE wid = $getWoId");
					//If workorder  Mapped with Basecam Milestone
					$notify = false;
					$responsible_party_type = 'person';
					$basecamp_result = array();
					if($bs_milestone->num_rows == 0){
						
						
						$bc = new Basecamp(BASECAMP_HOST,BASECAMP_USERNAME,BASECAMP_PASSWORD,'xml');
						//createMilteStone($woAssignedTo,$getWoId,$woTitle,$woDesc,$require_date);
						
						$woTitle = "LH[".$getWoId."] ".$woTitle;
		    			$basecamp_result = $bc->createMilestoneForProject($project_bs_id, $woTitle,$require_date, $responsible_party_type, $responsible_party_id,$notify);
						//print_r($basecamp_result);
		    			if(ISSET($basecamp_result['id'])){
		    				if($basecamp_result['id'] != ''){
		    				//echo "INSERT INTO `bs_milestone` SET milestone_id = '".$basecamp_result['id']."', wid = '".$getWoId."' , created_by = '".$woAssignedTo."', created_on ='now()'";
								$insert_basecamp_sql = $mysql->sqlordie("INSERT INTO `bs_milestone` SET milestone_id = '".$basecamp_result['id']."', wid = '".$getWoId."' , created_by = '".$woAssignedTo."', created_on ='".date('Y-m-d')."'");
		    				}
							
						}
					}else{
						$bc = new Basecamp(BASECAMP_HOST,BASECAMP_USERNAME,BASECAMP_PASSWORD,'xml');
						$woTitle = "LH[".$getWoId."] ".$woTitle;
						$bs_milestone_row = $bs_milestone->fetch_assoc();
						$bs_milestone_id = $bs_milestone_row['milestone_id'];
						$bc->updateMilestone($bs_milestone_id,$woTitle, $require_date, $responsible_party_type, $responsible_party_id, $notify);
						//echo "ss".$st_status;
						//Complete status array
						$complete_array = array('1','3');
						if(in_array($st_status,$complete_array)){
							
							$r = $bc->completeMilestone($bs_milestone_id);
							//print_r($r);
						}else{
						//if($st_status == '12'){
							
							$r = $bc->uncompleteMilestone($bs_milestone_id);
							//print_r($r);
						}						
						
					}
				}
			
				
			}
			
		 
		}
	 
	}
	
	function static victorOpsAlertIntegration($data_array){
		$url = 'https://alert.victorops.com/integrations/generic/20131114/alert/84b503b5-0b45-4dda-975e-60977ee2b9c0/nbcu-sandbox';
		$data_string = json_encode($data_array);
		$ch=curl_init($url);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
	
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
		$result = curl_exec($ch);

		curl_close($ch);
		
		return $result;
		
		
	}
       
       
}
?>
