<?php 
ini_set('max_execution_time', 0);
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
		$badwordchars=array('?','?','?','apos;',"#039;","?","?",'&#233;','&#8216;','&#8217;',
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
		'#160;','','','','','','','','',''
			
		);
		$fixedwordchars = array('','"','"',"'","'",",","'", "e","'","'",'~','~','','','_','-','-','-','','','','','','','','','','');
	    $text = str_replace($badwordchars,$fixedwordchars,$text);                         
		$text=str_replace('?',"'",$text); 
	    $text=str_replace('?',"'",$text); 
	    $text=str_replace('&amp;rsquo;',"'",$text); 
//    $text = preg_replace('/[^(\x20-\x7F)]*/','', $text );  
		$text = str_replace("&#8216;","",$text);
		//LH#    20679
		$text = str_replace(array("“","’","”"),array('"',"'",'"'),$text);
//		$text = str_replace("&","",$text);
//		$text = preg_replace('/[^\x00-\x7f]/','',$text);
		return $text;


	}
    
	public function workorders(){
		
			$mysql = self::singleton();
			$workorders = "SELECT * FROM workorders";  
			$workorders_res = $mysql->query($workorders);
							
			$doc = new DOMDocument("1.0");
			$doc->formatOutput = true; 
			$r = $doc->createElement( "add" ); 
			$doc->appendChild( $r ); 
			while($workorders_row = $workorders_res->fetch_assoc())
			//foreach( $workorders_row as $workorders_row ) 
			{//print_r($workorders_row); echo '</br>';
			$b = $doc->createElement( "doc" ); 
			$id = $doc->createElement( "id" ); 
			$id->appendChild( $doc->createTextNode( $workorders_row['id'] ) ); 
			$b->appendChild($id); 
			
			$guid = $doc->createElement( "guid" ); 
			$guid->appendChild( $doc->createTextNode( $workorders_row['project_id'] ) ); 
			$b->appendChild( $guid ); 
			
			
			$title = $doc->createElement( "title" ); 
			$title->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($workorders_row['title']), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title ); 
			
			$title_facet = $doc->createElement( "title_facet" ); 
			$title_facet->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($workorders_row['example_url'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title_facet ); 
			
			
			
			$description = $doc->createElement( "description" ); 
			$description->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes( $workorders_row['body']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $description );


			$categories = $doc->createElement( "categories" ); 
			$categories->appendChild( $doc->createCDATASection( 'workorder' ) ); 
			$b->appendChild( $categories ); 
			
				
			$author = $doc->createElement( "author" ); 
			$author->appendChild( $doc->createTextNode(htmlentities($this->escapewordquotes($workorders_row['requested_by']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $author ); 
			
			$workorders_comment = "SELECT * FROM `workorder_comments` WHERE `workorder_id`='" .$workorders_row['id'] ."'";
			$workorders_comment_res = $mysql->query($workorders_comment);
			
			
			while($workorders_comment_row = $workorders_comment_res->fetch_assoc()){
			$commentTextList = $doc->createElement( "commentTextList" ); 
			$commentTextList->appendChild( $doc->createCDATASection( htmlentities($this->escapewordquotes($workorders_comment_row['comment'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild($commentTextList); 
			
			}
			
			
			$r->appendChild($b); 
			} 
			
			$doc->saveXML(); 
			$doc->save("lh.xml");
					
			//return  $workorders_row;
		
	}
	
	public function quality(){
			$mysql = self::singleton();
			$quality = "SELECT * FROM qa_defects";  
			$quality_res = $mysql->query($quality);
			$doc = new DOMDocument("1.0");
			$doc->formatOutput = true; 
			$r = $doc->createElement( "add" ); 
			$doc->appendChild( $r );
			while($quality_row = $quality_res->fetch_assoc())
			{
			$b = $doc->createElement( "doc" ); 
			$id = $doc->createElement( "id" ); 
			$id->appendChild( $doc->createTextNode( $quality_row['id'] ) ); 
			$b->appendChild($id); 
			
			$guid = $doc->createElement( "guid" ); 
			$guid->appendChild( $doc->createTextNode( $quality_row['project_id'] ) ); 
			$b->appendChild( $guid ); 
			
			
			$title = $doc->createElement( "title" ); 
			$title->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($quality_row['title']), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title ); 
			
			$title_facet = $doc->createElement( "title_facet" ); 
			$title_facet->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes($quality_row['example_url'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild( $title_facet ); 
			
			
			
			$description = $doc->createElement( "description" ); 
			$description->appendChild( $doc->createCDATASection(htmlentities($this->escapewordquotes( $quality_row['body']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $description );


			$categories = $doc->createElement( "categories" ); 
			$categories->appendChild( $doc->createCDATASection( 'quality' ) ); 
			$b->appendChild( $categories ); 
			
				
			$author = $doc->createElement( "author" ); 
			$author->appendChild( $doc->createTextNode(htmlentities($this->escapewordquotes($quality_row['requested_by']), ENT_QUOTES, "UTF-8") ) ); 
			$b->appendChild( $author ); 
			
			$quality_comment = "SELECT * FROM `qa_comments` WHERE `defect_id`='" .$quality_row['id'] ."'";
			$quality_comment_res = $mysql->query($quality_comment);
			
			
			while($quality_comment_row = $quality_comment_res->fetch_assoc()){
			$commentTextList = $doc->createElement( "commentTextList" ); 
			$commentTextList->appendChild( $doc->createCDATASection( htmlentities($this->escapewordquotes($quality_comment_row['comment'] ), ENT_QUOTES, "UTF-8")) ); 
			$b->appendChild($commentTextList); 
			
			} 
			$r->appendChild($b); 
			} 
			
			$doc->saveXML(); 
			$doc->save("lhq.xml");
			$file= "lhxml.xml";
			$t=file_get_contents('lh.xml');
			$replace= preg_replace('</add>','',$t );
			$replace= preg_replace('/<>/','',$replace );
			$tq=file_get_contents('lhq.xml');
			
			$replaceq = preg_replace('/<add>/','',$tq );
			$strlen=strlen($replaceq);
			$replaceq =substr($replaceq,22, $strlen);
			
			$content = $replace.$replaceq; /* put array into one variable with newline as delimiter */
			file_put_contents($file, $content); /* over write original with changes made */
			
			}


	}
			require_once('../_inc/config.inc');
			$c = new createSolrXml();
			$u = new stdClass();
	    	$w = new stdClass();
	    	 print_r($c->workorders());
	    	 print_r($c->quality());
			
?>