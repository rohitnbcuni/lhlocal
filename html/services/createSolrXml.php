<?php 

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');
class createSolrXml{

	private static $instance;
	private $count = 0;
	public $config;
	public static function singleton()
    {
        if (!isset(self::$instance)) {
            //echo 'Creating new instance.';
            $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
            self::$instance = $mysql;
        }
        return self::$instance;
    }
	
    public function escapewordquotes ($text) {
		$pre = chr(226).chr(128);
		$badwordchars=array('?','?','?','apos;',"#039;","?","?",'&#233;','&#8216;','&#8217;','&#8230;','&#8217;','&#8220;','&#8221;','&#8212;','#8212;','#&8211;','#8211;','amp;','&#160;','#160;','','','','','','','','','','','','','','','','','','','','','','','','');
		$fixedwordchars = array('','"','"',"'","'",",","'", "e","'","'",'~','~','','','_','-','-','-','','','','','','','','','','','','','','','','','','','','','','','','','');
	    $text = str_replace($badwordchars,$fixedwordchars,$text);                         
		$text=str_replace('?',"'",$text); 
	    $text=str_replace('?',"'",$text); 
	    $text=str_replace('&amp;rsquo;',"'",$text); 
//    $text = preg_replace('/[^(\x20-\x7F)]*/','', $text );  
		$text = str_replace("&#8216;","",$text);
		//LH#    20679
		$text = str_replace(array("�","�","�"),array('"',"'",'"'),$text);
//		$text = str_replace("&","",$text);
//		$text = preg_replace('/[^\x00-\x7f]/','',$text);
		return $text;


	}
    
	public function workorders($path){
		
			$mysql = self::singleton();
			$workorders = "SELECT id,project_id,title,example_url,body,requested_by,creation_date FROM workorders where id!='28317'  ORDER BY id DESC limit 0, 1000";  
			$workorders_res = $mysql->query($workorders);
							
			$doc = new DOMDocument("1.0");
			$doc->formatOutput = true; 
			$r = $doc->createElement( "add" ); 
			$doc->appendChild( $r ); 
			while($workorders_row = $workorders_res->fetch_assoc())
			{
			$b = $doc->createElement( "doc" ); 
			$id = $doc->createElement( "field" );
			$id->setAttribute('name', 'id');
			$id->appendChild( $doc->createCDATASection('WO'.$workorders_row['id'] ) );
			$b->appendChild($id); 
			
			$docid = $doc->createElement( "field" );
                        $docid->setAttribute('name', 'docid');
                        $docid->appendChild( $doc->createTextNode($workorders_row['id'] ) );
                        $b->appendChild($docid);
	
			$guid = $doc->createElement( "field" ); 
			$guid->setAttribute('name', 'guid');
			$guid->appendChild( $doc->createTextNode( $workorders_row['project_id'] ) ); 
			$b->appendChild( $guid ); 
			
			
			$title = $doc->createElement( "field" ); 
			$title->setAttribute('name', 'title');
			$title->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($workorders_row['title']), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title ); 
			
			$title_facet = $doc->createElement( "field" ); 
			$title_facet->setAttribute('name', 'title_facet');
			$title_facet->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($workorders_row['example_url'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title_facet ); 
			
			
			
			$description = $doc->createElement( "field" );
			$description->setAttribute('name', 'description');			
			$description->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes( $workorders_row['body']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $description );


			$categories = $doc->createElement( "field" ); 
			$categories->setAttribute('name', 'categories');
			$categories->appendChild( $doc->createCDATASection( 'workorder' ) ); 
			$b->appendChild( $categories ); 
			
				
			$author = $doc->createElement( "field" );
			$author->setAttribute('name', 'author');
			$author->appendChild( $doc->createTextNode(htmlentities($this->escapewordquotes($workorders_row['requested_by']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $author ); 
			
			$createdDate = $doc->createElement( "field" );
                        $createdDate->setAttribute('name', 'createdDate');
                        $createdDate->appendChild( $doc->createTextNode( date('Y-m-d\TH:i:s\Z', strtotime($workorders_row['creation_date'])) ) );
                        $b->appendChild( $createdDate );
			
			$workorders_comment = "SELECT comment,date FROM `workorder_comments` WHERE `workorder_id`='" .$workorders_row['id'] ."'";
			$workorders_comment_res = $mysql->query($workorders_comment);
			
			
			while($workorders_comment_row = $workorders_comment_res->fetch_assoc()){
			$commentTextList = $doc->createElement( "field" );
			$commentTextList->setAttribute('name', 'commentTextList');
			$commentTextList->appendChild( $doc->createCDATASection( htmlentities($this->escapewordquotes($workorders_comment_row['comment'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild($commentTextList); 
			
			}
			
			$workorders_date_comment = "SELECT date FROM `workorder_comments` WHERE `workorder_id`='" .$workorders_row['id'] ."'order by id DESC limit 1";
                        $workorders_comment_date_res = $mysql->query($workorders_date_comment);

			while($workorders_comment_date_value = $workorders_comment_date_res->fetch_row()){
                        $commentLastUpdatedDate = $doc->createElement( "field" );
                        $commentLastUpdatedDate->setAttribute('name', 'commentLastUpdatedDate');
                        $commentLastUpdatedDate->appendChild( $doc->createCDATASection( date('Y-m-d\TH:i:s\Z', strtotime($workorders_comment_date_value[0]))));
                        $b->appendChild($commentLastUpdatedDate);
			}

			$r->appendChild($b); 
			} 
			
			$doc-> saveXML(); 
			$doc->save($path."workorders.xml");
					
			//return  $workorders_row;
		
	}
	
	public function quality($path){
			$mysql = self::singleton();
			$quality = "SELECT id,project_id,title,example_url,body,requested_by,creation_date FROM qa_defects ORDER BY id DESC limit 0,1000";  
			$quality_res = $mysql->query($quality);
			$doc = new DOMDocument("1.0");
			$doc->formatOutput = true; 
			$r = $doc->createElement( "add" ); 
			$doc->appendChild( $r );
			while($quality_row = $quality_res->fetch_assoc())
			{
			$b = $doc->createElement( "doc" ); 
			
			$id = $doc->createElement( "field" );
			$id->setAttribute('name', 'id');			
			$id->appendChild( $doc->createCDATASection('QA'.$quality_row['id'] ) ); 
			$b->appendChild($id); 
			
			$docid = $doc->createElement( "field" );
                        $docid->setAttribute('name', 'docid');
                        $docid->appendChild( $doc->createTextNode($quality_row['id'] ) );
                        $b->appendChild($docid);

			$guid = $doc->createElement( "field" ); 
			$guid->setAttribute('name', 'guid');
			$guid->appendChild( $doc->createTextNode( $quality_row['project_id'] ) ); 
			$b->appendChild( $guid ); 
			
			
			$title = $doc->createElement( "field" ); 
			$title->setAttribute('name', 'title');
			$title->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($quality_row['title']), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title ); 
			
			$title_facet = $doc->createElement( "field" ); 
			$title_facet->setAttribute('name', 'title_facet');
			$title_facet->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($quality_row['example_url'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title_facet ); 
			
			
			
			$description = $doc->createElement( "field" );
			$description->setAttribute('name', 'description');
			$description->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes( $quality_row['body']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $description );


			$categories = $doc->createElement( "field" ); 
			$categories->setAttribute('name', 'categories');
			$categories->appendChild( $doc->createCDATASection( 'quality' ) ); 
			$b->appendChild( $categories ); 
			
				
			$author = $doc->createElement( "field" );
			$author->setAttribute('name', 'author'); 
			$author->appendChild( $doc->createTextNode(htmlentities($this->escapewordquotes($quality_row['requested_by']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $author ); 
			
			 $createdDate = $doc->createElement( "field" );
                        $createdDate->setAttribute('name', 'createdDate');
                        $createdDate->appendChild( $doc->createTextNode(date('Y-m-d\TH:i:s\Z', strtotime($quality_row['creation_date']))) );
                        $b->appendChild( $createdDate );
			
			$quality_comment = "SELECT comment FROM `qa_comments` WHERE `defect_id`='" .$quality_row['id'] ."'";
			$quality_comment_res = $mysql->query($quality_comment);
			
			
			while($quality_comment_row = $quality_comment_res->fetch_assoc()){
			$commentTextList = $doc->createElement( "field" );
			$commentTextList->setAttribute('name', 'commentTextList');
			$commentTextList->appendChild( $doc->createCDATASection( htmlentities($this->escapewordquotes($quality_comment_row['comment'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild($commentTextList); 
			
			}
			
			$quality_comment_date = "SELECT date FROM `qa_comments` WHERE `defect_id`='" .$quality_row['id'] ."' order by id DESC limit 0";
                        $quality_comment_date_res = $mysql->query($quality_comment_date);
			
			while($quality_comment_date_value =  $quality_comment_date_res->fetch_row()){
			$commentLastUpdatedDate = $doc->createElement( "field" );
                        $commentLastUpdatedDate->setAttribute('name', 'commentLastUpdatedDate');
                        $commentLastUpdatedDate->appendChild( $doc->createCDATASection( date('Y-m-d\TH:i:s\Z', strtotime($quality_comment_date_value[0]))) );
                        $b->appendChild($commentLastUpdatedDate);
			}
 
			$r->appendChild($b); 
			} 
			
			$doc->saveXML(); 
			$doc->save($path."quality.xml");
			}


	}
			
			//require_once('/var/www/lighthouse-uxd/dev2/current/html/_inc/config.inc');
			//require_once('/var/www/lighthouse-uxd/dev2/current/html/_ajaxphp/util.php');
			//$path = '/var/www/lighthouse-uxd/dev2/current/Solarxml/';
			$config_path = str_replace("/html/services","",dirname(__FILE__));
			require_once($config_path.'/html/_inc/config.inc');
			require_once($config_path.'/html/_ajaxphp/util.php');
			$path = $config_path.'/Solarxml/';
			$c = new createSolrXml();
			$u = new stdClass();
	    		$w = new stdClass();
	    	 	$c->workorders($path);
	    		$c->quality($path);
		        echo 'XML Created';
			 
			
?>
