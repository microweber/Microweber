<? 
if($params['type'] != 'google_maps'){
return;	
}
 
$address = false;
if (isset($params['data-address'])) {
  
    $address = $params['data-address'];
} else {
      $address =  get_option('data-address', $params['id']);
}
if($address == false or $address == ''){
	if (isset($params['parent-module-id'])) {
  
    $address = $params['parent-module-id'];
	 $address =  get_option('data-address',$address);;
}
}


if($address == false or $address == ''){
	$address = "Sofia, Bulgaria";	

}

$address = html_entity_decode($address);
$address = strip_tags($address);
 //d($address);
$zoom = false;
if (isset($params['data-address'])) {
  
    $zoom = $params['data-zoom'];
} else {
      $zoom =  get_option('data-zoom', $params['id']);
}
if($zoom == false or $zoom == ''){
$zoom = "14";	
}

?>

<script type="text/javascript">

var T = 1;

</script>
<br />

<div class="resize-x resize-y " style="width: 100%;height: 250px">
 
 
 <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;hl=en&amp;geocode=&amp;time=&amp;date=&amp;ttype=&amp;q=<? print urlencode($address); ?>&amp;ie=UTF8&amp;om=1&amp;s=AARTsJpG68j7ib5XkPnE95ZRHLMVsa8OWg&amp;spn=0.011588,0.023174&amp;z=<? print intval($zoom); ?>&amp;output=embed"></iframe>
 
 
 
 

</div>

