<?

/**
 * Simple file browser
 *
 * Gets all files from dir and output them in a template
 *
 * @package		modules
 * @subpackage	files
 * @category	modules
 */
 ?>
<?php
  // Explore the files via a web interface.
  $script = $config['url']; // the name of this script
  $path   =   MEDIAFILES; // the path the script should access
 if(isset($params['path'])){

	  $path   =   $params['path']; // the path the script should access
 }

 $path = urldecode($path);
  
 //$data = rglob($path);
  $params_get_files = array();
   $params_get_files['directory']  =  $path;  
   
   if (isset($params['search'])) {
		   $params_get_files['search']  =  $params['search'];  
	}

	  if (isset($params['sort_by'])) {
		   $params_get_files['sort_by']  =  $params['sort_by'];
	}
	  if (isset($params['sort_order'])) {
		   $params_get_files['sort_order']  =  $params['sort_order'];
	}

  //  $params['keyword']  
 $data = get_files($params_get_files);

 $path_nav = explode(DS,$path);

?>


<script>

PreviousFolder = [];

</script>


<div class="mw-o-box mw-file-browser">
<div class="mw-o-box-header">
<a href="javascript:;" onclick="mw.url.windowHashParam('path', PreviousFolder);" class="mw-ui-btn mw-ui-btn-small right"><span class="backico"></span><?php _e("Back"); ?></a>
<span class="ico ireport"></span>
    <span class="mw-browser-uploader-path">
<? if(isarr($path_nav )): ?>
<?

$path_nav_pop = false;
foreach($path_nav  as $item): ?>
<?

if($path_nav_pop  == false){
	$path_nav_pop = $item;
} else {

$path_nav_pop = $path_nav_pop.DS.$item;

}
 if(strlen($item)>0):
 ?>
 <script>PreviousFolder.push('<? print urlencode($path_nav_pop) ?>');</script>
 <a href="#path=<? print urlencode($path_nav_pop) ?>"><span class="<? print $config['module_class']; ?> path-item"><? print ($item) ?></span></a>&raquo;<? endif; endforeach ; ?>
<? endif; ?>
    </span>
</div>
 <script>
    PreviousFolder.length > 1 ? PreviousFolder.pop() : '';
    PreviousFolder = PreviousFolder.length > 1 ? PreviousFolder[PreviousFolder.length-1] : PreviousFolder[0];
 </script>
 <div class="mw-o-box-content">

<? if(isset($data['dirs'] )): ?>
<ul class="mw-browser-list">
  <? foreach($data['dirs']  as $item): ?>
  <li> <a title="<? print basename($item).'&#10;'.dirname($item); ?>" href="#path=<? print urlencode($item); ?>"><span class="ico icategory"></span><span><? print basename($item); ?></span></a> </li>
  <? endforeach ; ?>
</ul>
<div class="vSpace"></div>
<div class="mw-o-box-hr"></div>
<div class="vSpace"></div>
<? endif; ?>
<? if(isset($data['files'] )): ?>
<ul class="mw-browser-list">
  <? foreach($data['files']  as $item): ?>
  <li> <a title="<? print basename($item).'&#10;'.dirname($item); ?>" class="mw-browser-list-file mw-browser-list-<?php print substr(strrchr($item,'.'),1); ?>" href="#select-file=<? print dir2url($item) ?>"><span class="ico ipage"></span><span><? print basename($item) ?></span></a> </li>
  <? endforeach ; ?>
</ul>
<? endif; ?>



</div>

</div>
