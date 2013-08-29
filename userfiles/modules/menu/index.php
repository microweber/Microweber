<?php //include_once($config['path_to_module'].'functions.php'); ?>
<?php
if(isset($params['name'])){
	$params['menu-name'] = $params['name'];

} else {
$params['menu-name'] = 'header_menu';	
}
  $menu_name = mw('option')->get('menu_name', $params['id']);

if($menu_name != false){
	$params['menu-name'] = $menu_name;
}

if(isset($params['menu-name'])){

	$menu = get_menu('make_on_not_found=1&one=1&limit=1&title='.$params['menu-name']);
	if(is_array($menu)){
		
				
				$menu_filter =$params;
		
				if(!isset($params['ul_class'])){
					$menu_filter['ul_class'] = 'nav';
				}
		
				$menu_filter['menu_id'] = intval($menu['id']);
		
		
				$module_template = mw('option')->get('data-template',$params['id']);
				if($module_template == false and isset($params['template'])){
					$module_template =$params['template'];
				}
				
				
				
				
				
				if($module_template != false and trim($module_template) != '' and trim(strtolower($module_template) != 'none')){
						$template_file = module_templates( $config['module'], $module_template);
				
				} else {
						$template_file = module_templates( $config['module'], 'default');
				
				}
				
				//d($module_template );
				if(isset($template_file) and $template_file != '' and is_file($template_file) != false){
					include($template_file);
				} else {
					
						$template_file = module_templates( $config['module'], 'default');
				include($template_file);
					//print 'No default template for '.  $config['module'] .' is found';
				}

		
		

		
		
	} else {
		//mw('content')->pages_tree($params);
		   print mw('format')->lnotif("Click on settings to edit this menu");
	}

} else {
	//mw('content')->pages_tree($params);
	 print mw('format')->lnotif("Click on settings to edit this menu");
}



