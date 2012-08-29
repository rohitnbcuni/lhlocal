<?php 
$url = 'http://ec2-75-101-162-191.compute-1.amazonaws.com:8080/solr/lighthouse_active/select?q='.urlencode($_GET['s']).'%20AND%20categories:quality'; 
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
	$titleDesc = array();
	$desc = array();
   	if(count($xml) > 0){
	foreach($xml as $key =>$val){
		if($key == 'result'){
				$searchResult =  $val->doc;
				if(count($searchResult) > 0){
				foreach($searchResult as $k => $v){
				//	$searchResult = (array) $v->str;
					if(count($searchResult) > 0){
						foreach($searchResult as $sKey => $eVal){
							//echo $eVal->str[0]; echo '</BR>';echo  $eVal->str[1]; echo '</BR>';
							print_r($eVal);exit();	
							
				                    				 $id[] =  (array) $eVal->str[0];
										
										
												
											$project_id[] = (array) $eVal->str[1];
										
											$title[] = (array) $eVal->str[2];
										
										
											$url[] = (array) $eVal->str[3];
										
											$desc[] = (array) $eVal->str[4];
										
                                            $cat[] = (array) $eVal->str[5];
					
						}
					
					}
				}
			
			}
		}
	
	}
	
	}	
	
	for($i =0 ; $i<count($id); $i++){ exit();
?>

<article class="answers type-answers status-publish">
<?php if($cat[$i][0]=='quality'){$link='/quality/index/edit/?defect_id=';}else{$link='/workorders/index/edit/?wo_id=';}?>
<h3 class="entry-title"><a href="<?php echo $link.$id[$i][0];?>"><?php echo $title[$i][0];?></a></h3>
</header><!-- .entry-header -->
<div class="entry-content native_html_style">
<p><?php echo $desc;?> <span><a href="<?php echo $link.$id[$i][0];?>" class="more-link">...read more</a></span></p>
</div><!-- .entry-content -->
<footer class="entry-meta">
<?php if ( $cat[$i][0]!='') { ?> 
<span class="cat-links">
<span class="entry-utility-prep entry-utility-prep-cat-links">Categories: </span><?php echo $cat[$i][0];?></span>
<?php } ?>
</footer><!-- #entry-meta -->
</article>
<?php  } 

				/*******************solar code end ******************/ ?>
