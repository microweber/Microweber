<? //include_once($config['path_to_module'].'functions.php'); ?>

<? 

  $menu_name = get_option('menu_name', $params['id']);  

if($menu_name != false){
	$params['menu-name'] = $menu_name;
}

if(isset($params['menu-name'])){
	
	$menu = get_menu('one=1&limit=1&title='.$params['menu-name']);
	if(isarr($menu)){
		$mt =  menu_tree($menu['id']);
		if($mt != false){
			print ($mt);
		} else {
			pages_tree();
			//print "There are no items in the menu <b>".$params['menu-name']. '</b>';	
		}
	} else {
		pages_tree();
		//print "Click on settings to edit this menu";	
	}
	
} else {
	pages_tree();
	//print "Click on settings to edit this menu";	
}


 ?>