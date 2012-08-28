<?php 
                            /*******************************Solar Code start************************/
	//	define('HTTP_PROXY','');
// $url = 'http://snasdev9.nbcuni.com/solrsearch/api/enhancedSearch?page=1&collectionList=otsanswers&site=otsanswers&perPage=50&searchType=BOOLEAN&spell=suggest&navigatorList=site&searchString='.$_GET['s'];
$url = 'http://ec2-75-101-162-191.compute-1.amazonaws.com:8080/solr/lighthouse_active/select?q='.urlencode($_GET['s']).'%20AND%20categories:quality'; 
echo $url;		

$ch = curl_init();
		$request='<request>'.$request.'</request>';
		curl_setopt($ch, CURLOPT_URL, $url);
		if(defined('HTTP_PROXY') && HTTP_PROXY!='')
		{
			curl_setopt($ch,CURLOPT_PROXY,HTTP_PROXY);
			curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,true);
			curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
		}
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_USERPWD,BC_USER . ":" . BC_PASSWORD);
		if(preg_match('/^(http)/',$url)) curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$response = curl_exec($ch);
		curl_close($ch);
		
    $xml = (array)simplexml_load_string($response);
print_r($xml);

$r = file_get_contents('http://ec2-75-101-162-191.compute-1.amazonaws.com:8080/solr/lighthouse_active/select?q='.urlencode($_GET['s']).'%20AND%20categories:quality');	print_r($r);
	$titleDesc = array();
$desc = array();
   if(count($xml) > 0){
	foreach($xml as $key =>$val){
		if($key == 'searchResult'){
			  
				$searchResult1 =  $val->searchResultDocument;
				
				
				if(count($searchResult1) > 0){
				foreach($searchResult1 as $k => $v){
					
					$searchResult = (array) $v->searchResultFieldList;
					if(count($searchResult) > 0){
						foreach($searchResult as $sKey => $sVal){
								
								if(count($sVal) >0){
									foreach($sVal as $eKey => $eVal){
										//print "<pre>";
				                    				  if($eVal->key == 'categories'){
											$categories[] = (array)$eVal->value->fieldValue[0];
										
										}
												
										if($eVal->key == 'generic5'){
											$thumbnail[] = (array)$eVal->value->fieldValue[0];
										
										}
										if($eVal->key == 'link'){
											$link[] = (array)$eVal->value->fieldValue[0];
										
										}
										
										if($eVal->key == 'title'){
											$titleDesc[] = (array)$eVal->value->fieldValue[0];
										
										}
										if($eVal->key == 'description'){
											$desc[] = (array)$eVal->value->fieldValue[0];
										
										}
										 if($eVal->key == 'tags'){
                                                                                        $tags[] = (array)$eVal->value->fieldValue[0];

                                                                                }

												}
								
								}
						
						}
					
					}
				}
			
			}
		}
	
	}
	
	}	
	
$regular_expression1 = '<[^\>]*\ />';
	$regular_expression = '/(?:<|&lt;)\/?([a-zA-Z]+) *[^<\/]*?(?:>|&gt;)/';						
					for($i =0 ; $i<count($titleDesc); $i++){
$only_post_text = preg_replace( $regular_expression1, '' , htmlspecialchars_decode($desc[$i][0]));
				// $only_post_text1 = preg_replace( $regular_expression, '' ,  substr($only_post_text, 4, 500));
 
$only_post_text1 = preg_replace( $regular_expression, '' , $only_post_text);
 $only_post_text2 = preg_replace( '/[^0-9a-z?-????\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i', '' , $only_post_text1);
						
								$match_array = array('/#1/i', '/#2/i', '/#3/i','/#91;/i','/#93;/i','/24;/i','/#61;/i', '/#39;/i','/#124;/i','/9;/i','/borderquot;/i','/2/i','/quot;/i','/3;/i','/width/','/75%/','/cellspacing/','/cellpadding/','/rulesall/','/stylemargin/','/notoc/','/1/','/2/','/3/','/0/','/4/','/5/','/6/','/7/','/8/','/9/','/bordersolid/','/px/','/#/','/AAAAAA/','/border-collapse/','/collapsecollapseempty/','/collapseempty-cellsshowstyle/','/alignleft/','/bgcolor/','/FFFF/','/ImageInfo/','/.jpg/','/__/','/NOTOC/','/-gt;/','/lt;/','/gt;/','/Image/','/showstyle/','/collapse/','/empty/','/cells/','/aaaa/','/border/','/a a a/','/1em/','/em/','/-/','/;/','/rules/','/all style/','/margin/','/solid/','/aa/','/ffff/','/align/','/show style/','/left/','/info/' );
			
$only_post_text_final = preg_replace( $match_array, '' ,substr($only_post_text2,0, 250));
$only_post_text_final=ucfirst($only_post_text_final);


?>

<article class="answers type-answers status-publish">

<?php
if(!empty($thumbnail[$i][0])){
?>
<figure class="entry-image">
<a href="<?php echo $link[$i][0];?>" title="<?php echo $titleDesc[$i][0]; ?>" rel="bookmark"><img width="135" height="100" src="<?php echo $thumbnail[$i][0]; ?>" class="attachment-post-thumbnail wp-post-image"></a>
<figcaption></figcaption>
</figure>
<?php } ?>

<header class="entry-header">
<h3 class="entry-title"><a href="<?php echo $link[$i][0];?>"><?php echo $titleDesc[$i][0];?></a></h3>
</header><!-- .entry-header -->
<div class="entry-content native_html_style">
<p><?php echo $only_post_text_final;?> <span><a href="<?php echo $link[$i][0];?>" class="more-link">...read more</a></span></p>
</div><!-- .entry-content -->
<footer class="entry-meta">
<?php if ( $categories[$i][0]!='') { ?> 
<span class="cat-links">
<span class="entry-utility-prep entry-utility-prep-cat-links">Topic categories: </span><?php echo $categories[$i][0];?></span>
<?php }  if($tags[$i][0] !=''){ ?>
<span class="tag-links">
<span>Tags: </span><?php echo $tags[$i][0];?></span>
<?php } ?>
</footer><!-- #entry-meta -->
</article>
<?php  } 

				/*******************solar code end ******************/ ?>
