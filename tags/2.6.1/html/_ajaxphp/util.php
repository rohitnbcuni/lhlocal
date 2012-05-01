<?php
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
       
       
}
?>
