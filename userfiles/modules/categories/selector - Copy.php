<?

// d($params);
 
$rand = uniqid();
if (!isset($params['for'])) {

	$for = 'content';
} else {
	$for = $params['for'];
}


if (!isset($params['to_table'])) {
$for = 'content';
	 
} else {
	$for = $params['to_table'];
}

$is_shop = '';
$active_cats = array();

if(isset($params['data-subtype']) and $params['data-subtype'] == 'product'){
	$params['is_shop'] = 'y';
} else {
 	//$params['is_shop'] = 'n';
}


if (isset($params['is_shop'])) {
	$is_shop = '&is_shop=' . $params['is_shop'];
}

if (!isset($params['to_table_id'])) {
	$to_table_id = '';
	if (!isset($params['to_table_id'])) {
		
	}
	
	
	if (isset($params['parent-category-id'])) {
		$active_cats [] = $params['parent-category-id'];
	}
 
	
	
	
} else {
	$to_table_id = '&to_table_id=' . $params['to_table_id'];
}
?>
<?
$cats_str = array();
$cats_ids = array();
$cats__parents = array();
$is_ex1 = array();
 $for = db_get_assoc_table_name($for);
	  if($for == 'conaaaaaaatent' or $for == 'table_content'){
		  
		  
	  }
	  
	$str1 = 'table=table_taxonomy&to_table='.$for.'&data_type=category&limit=1000&parent_id=0&to_table_id=[mt][int]0';
							$is_ex = get($str1); 
 	
	if(isarr($is_ex)){
	foreach ($is_ex as $item) {
		 $cats__parents[] = $item['id'];
	}
	}
 
 

//  $cats__parents = $is_ex1;
if (empty($cats__parents)) {
	// $is_ex1 = get('limit=100&what=category&parent_id=0&for='.$for);
	foreach ($is_ex1 as $item) {
	//	$cats__parents[] = $item['parent_id'];
	}
}
 
if (isset($params['to_table_id']) and $params['to_table_id'] != 0) {

	$is_exs3 = get('limit=1000&what=category_items&to_table=' . $for .'&to_table_id=' . $params['to_table_id']);
 
	 
	if (isset($is_exs3[0])) {
		foreach ($is_exs3 as $is_exs3z) {
			$active_cats[] = $is_exs3z['parent_id'];
		}
	}
} else {

	//
}  
 
?>
<?  
 
   ?>
<script  type="text/javascript">

	$(document).ready(function(){

mw.$('#<? print $params['id'] ?> .mw-ui-check').on('click', function(e){
	// e.preventDefault(); //stop the default form action
	var names = [];
	mw.$('#<? print $params['id'] ?> .mw-ui-check-input-sel:checked').each(function() {
	names.push($(this).val());
	});
mw.log('<? print $params['id'] ?>');
	if(names.length > 0){
	mw.$('#mw_cat_selected_<? print $rand ?>').val(names.join(',')).change();
} else {
        mw.$('#mw_cat_selected_<? print $rand ?>').val('__EMPTY_CATEGORIES__').change();
	}

	//mw.log(names);

	});



   // mw_append_pages_tree_controlls(mwd.querySelector('.mw-ui-category-selector'));

	});
</script>
<? if(!empty($cats__parents)): ?>
<?
 

foreach ($cats__parents as $item1) {
 
	$tree = array();
	  $tree['include_first'] = 1;
	$tree['parent'] = $item1;
//	$tree['to_table_id'] = '[gte]0';
	
	//  $tree['debug'] = 1;
	if (isset($params['add_ids'])) {
		$tree['add_ids'] = $params['add_ids'];

	}
	if (!empty($active_cats)) {
		 $tree['active_ids'] = $active_cats;
		
	} else {
 
	if (!empty($cats_ids)) {
  $cats_ids[] = $item1;
	 $tree['active_ids'] = $cats_ids;
		
	}
	}
	
	
	 $tree['active_code'] = 'checked="checked" ';
//$tree['debug'] = 1;
	$tree['link'] = "<label class='mw-ui-check'><input type='checkbox'  {active_code} value='{id}' id='mw_cat_selector_{$rand}' class='mw-ui-check-input-sel' ><span></span><span>{title}</span></label>";
 
 	category_tree($tree);

}
?>
<? endif; ?>
<?  if(isset($params['include_global_categories']) and $params['include_global_categories'] == true  and isset($params['include_global_categories'])){
	 
						
						
					 
						$str0 = 'table=table_taxonomy&limit=1000&data_type=category&' . 'parent_id=0&to_table_id=0&to_table=table_content';
		$fors = get($str0);
					//d($fors );
					
					
					if ($fors != false and is_array($fors) and !empty($fors)) {
			foreach ($fors as $cat) {
				$cat_params =$params;
				$pt_opts = array();
	$pt_opts['link'] = "<label class='mw-ui-check'><input type='checkbox'  {active_code} value='{id}' class='mw_cat_selector_{$rand}' ><span></span><span>{title}</span></label>";
 
 
						$pt_opts['parent'] =$cat['id'];
						//$cat_params['to_table'] = 'table_content';
					//	$cat_params['to_table_id'] = ' 0 ';
					// $cat_params['for'] = 'table_content';
				 $pt_opts['include_first'] = 1;
					 //$cat_params['debug'] = 1;
					// d($cat_params);
						// category_tree($pt_opts);
			}
		}
						
				
				
				 
					
					
 
	 
 }
  
  
   ?>
<? $cats_str = implode(',', $active_cats); ?>

<input type="text" name="categories" id="mw_cat_selected_<? print $rand ?>" value="<? print $cats_str ?>" />
