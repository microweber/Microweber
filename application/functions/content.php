<?php


if (!defined("MW_DB_TABLE_CONTENT")) {
	define('MW_DB_TABLE_CONTENT', MW_TABLE_PREFIX . 'content');
}

if (!defined("MW_DB_TABLE_CONTENT_FIELDS")) {
	define('MW_DB_TABLE_CONTENT_FIELDS', MW_TABLE_PREFIX . 'content_fields');
}

if (!defined("MW_DB_TABLE_MEDIA")) {
	define('MW_DB_TABLE_MEDIA', MW_TABLE_PREFIX . 'media');
}

if (!defined("MW_DB_TABLE_CUSTOM_FIELDS")) {
	define('MW_DB_TABLE_CUSTOM_FIELDS', MW_TABLE_PREFIX . 'custom_fields');
}
if (!defined("MW_DB_TABLE_MENUS")) {
	define('MW_DB_TABLE_MENUS', MW_TABLE_PREFIX . 'menus');
}


action_hook('mw_db_init_default', 'mw_db_init_content_table');
 // action_hook('mw_db_init', 'mw_db_init_content_table');

function mw_db_init_content_table() {
	$function_cache_id = false;

	$args = func_get_args();

	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	}

	$function_cache_id = __FUNCTION__ . crc32($function_cache_id);

	$cache_content = cache_get_content($function_cache_id, 'db');

	if (($cache_content) != false) {

		return $cache_content;
	}

	$table_name = MW_DB_TABLE_CONTENT;

	$fields_to_add = array();

	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('expires_on', 'datetime default NULL');

	$fields_to_add[] = array('created_by', 'int(11) default NULL');

	$fields_to_add[] = array('edited_by', 'int(11) default NULL');


	$fields_to_add[] = array('content_type', 'TEXT default NULL');
	$fields_to_add[] = array('url', 'longtext default NULL');
	$fields_to_add[] = array('content_filename', 'TEXT default NULL');
	$fields_to_add[] = array('title', 'longtext default NULL');
	$fields_to_add[] = array('parent', 'int(11) default NULL');
	$fields_to_add[] = array('description', 'TEXT default NULL');
	 $fields_to_add[] = array('content_meta_title', 'TEXT default NULL');

	$fields_to_add[] = array('content_meta_keywords', 'TEXT default NULL');
	$fields_to_add[] = array('position', 'int(11) default 1');

	$fields_to_add[] = array('content', 'TEXT default NULL');

	$fields_to_add[] = array('is_active', "char(1) default 'y'");
	$fields_to_add[] = array('is_home', "char(1) default 'n'");
	$fields_to_add[] = array('is_pinged', "char(1) default 'n'");
	$fields_to_add[] = array('is_shop', "char(1) default 'n'");
	$fields_to_add[] = array('is_deleted', "char(1) default 'n'");
	$fields_to_add[] = array('draft_of', 'int(11) default NULL');

	$fields_to_add[] = array('require_login', "char(1) default 'n'");



	$fields_to_add[] = array('subtype', 'TEXT default NULL');
	$fields_to_add[] = array('subtype_value', 'TEXT default NULL');
	$fields_to_add[] = array('original_link', 'TEXT default NULL');
	$fields_to_add[] = array('layout_file', 'TEXT default NULL');
	$fields_to_add[] = array('layout_name', 'TEXT default NULL');
	$fields_to_add[] = array('layout_style', 'TEXT default NULL');
	$fields_to_add[] = array('active_site_template', 'TEXT default NULL');
	$fields_to_add[] = array('session_id', 'varchar(255)  default NULL ');
	set_db_table($table_name, $fields_to_add);


	db_add_table_index('url', $table_name, array('url(255)'));
	db_add_table_index('title', $table_name, array('title(255)'));






	$table_name = MW_DB_TABLE_CONTENT_FIELDS;

	$fields_to_add = array();

	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('created_by', 'int(11) default NULL');
	$fields_to_add[] = array('edited_by', 'int(11) default NULL');
	$fields_to_add[] = array('rel', 'TEXT default NULL');

	$fields_to_add[] = array('rel_id', 'TEXT default NULL');
	$fields_to_add[] = array('position', 'int(11) default NULL');
	$fields_to_add[] = array('field', 'longtext default NULL');
	$fields_to_add[] = array('value', 'TEXT default NULL');




	set_db_table($table_name, $fields_to_add);

	db_add_table_index('rel', $table_name, array('rel(55)'));
	db_add_table_index('rel_id', $table_name, array('rel_id(255)'));
	db_add_table_index('field', $table_name, array('field(55)'));





	$table_name = MW_DB_TABLE_MEDIA;

	$fields_to_add = array();

	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('created_by', 'int(11) default NULL');
	$fields_to_add[] = array('edited_by', 'int(11) default NULL');
	$fields_to_add[] = array('session_id', 'varchar(50) DEFAULT NULL');
	$fields_to_add[] = array('rel', 'TEXT default NULL');

	$fields_to_add[] = array('rel_id', 'TEXT default NULL');
	$fields_to_add[] = array('media_type', 'TEXT default NULL');
	$fields_to_add[] = array('position', 'int(11) default NULL');
	$fields_to_add[] = array('title', 'longtext default NULL');
	$fields_to_add[] = array('description', 'TEXT default NULL');
	$fields_to_add[] = array('embed_code', 'TEXT default NULL');
	$fields_to_add[] = array('filename', 'TEXT default NULL');



	set_db_table($table_name, $fields_to_add);

	db_add_table_index('rel', $table_name, array('rel(55)'));
	db_add_table_index('rel_id', $table_name, array('rel_id(255)'));
	db_add_table_index('media_type', $table_name, array('media_type(55)'));

	 //db_add_table_index('url', $table_name, array('url'));
	 //db_add_table_index('title', $table_name, array('title'));





	$table_name = MW_DB_TABLE_CUSTOM_FIELDS;

	$fields_to_add = array();
	$fields_to_add[] = array('rel', 'TEXT default NULL');

	$fields_to_add[] = array('rel_id', 'TEXT default NULL');
	$fields_to_add[] = array('session_id', 'varchar(50) DEFAULT NULL');
	$fields_to_add[] = array('position', 'int(11) default NULL');


	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('created_by', 'int(11) default NULL');
	$fields_to_add[] = array('edited_by', 'int(11) default NULL');

	$fields_to_add[] = array('custom_field_name', 'TEXT default NULL');
	$fields_to_add[] = array('custom_field_name_plain', 'longtext default NULL');


	$fields_to_add[] = array('custom_field_value', 'TEXT default NULL');



	$fields_to_add[] = array('custom_field_type', 'TEXT default NULL');
	$fields_to_add[] = array('custom_field_values', 'longtext default NULL');
	$fields_to_add[] = array('custom_field_values_plain', 'longtext default NULL');

	$fields_to_add[] = array('field_for', 'TEXT default NULL');
	$fields_to_add[] = array('custom_field_field_for', 'TEXT default NULL');
	$fields_to_add[] = array('custom_field_help_text', 'TEXT default NULL');
	$fields_to_add[] = array('options', 'TEXT default NULL');


	$fields_to_add[] = array('custom_field_is_active', "char(1) default 'y'");
	$fields_to_add[] = array('custom_field_required', "char(1) default 'n'");
	$fields_to_add[] = array('copy_of_field', 'int(11) default NULL');




	set_db_table($table_name, $fields_to_add);

	db_add_table_index('rel', $table_name, array('rel(55)'));
	db_add_table_index('rel_id', $table_name, array('rel_id(55)'));
	db_add_table_index('custom_field_type', $table_name, array('custom_field_type(55)'));




	$table_name = MW_DB_TABLE_MENUS;

	$fields_to_add = array();
	$fields_to_add[] = array('title', 'TEXT default NULL');
	$fields_to_add[] = array('item_type', 'varchar(33) default NULL');
	$fields_to_add[] = array('parent_id', 'int(11) default NULL');
	$fields_to_add[] = array('content_id', 'int(11) default NULL');
	$fields_to_add[] = array('categories_id', 'int(11) default NULL');
	$fields_to_add[] = array('position', 'int(11) default NULL');
	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('is_active', "char(1) default 'y'");
	$fields_to_add[] = array('description', 'TEXT default NULL');
	$fields_to_add[] = array('url', 'TEXT default NULL');
	set_db_table($table_name, $fields_to_add);



	cache_save(true, $function_cache_id, $cache_group = 'db');
	return true;

}
action_hook('mw_db_init', 'create_mw_default_pages_in_not_exist');
function create_mw_default_pages_in_not_exist() {
	mw_create_default_content('default');

}
function define_constants($content = false) {

	if($content == false){
		if (isset($_SERVER['HTTP_REFERER'])) {
			$ref_page = $_SERVER['HTTP_REFERER'];
			if ($ref_page != '') {
				$ref_page = get_content_by_url($ref_page);
				if(!empty($ref_page)){
					$content = $ref_page;
				}
			}
		}
	}
 //d($content);


	$page_data = false;
	if (is_array($content)) {
		if (isset($content['id'])) {

			$page = $content;
		}
	}

	if (isset($page)) {
		if ($page['content_type'] == "post") {
			$content = $page;

			$page = get_content_by_id($page['parent']);
			if (defined('POST_ID') == false) {
				define('POST_ID', $content['id']);
			}
		} else {
			$content = $page;
			if (defined('POST_ID') == false) {
				define('POST_ID', false);
			}
		}

		if (defined('ACTIVE_PAGE_ID') == false) {

			define('ACTIVE_PAGE_ID', $page['id']);
		}

		if (defined('CATEGORY_ID') == false) {
			define('CATEGORY_ID', false);
		}

		if (defined('CONTENT_ID') == false) {
			define('CONTENT_ID', $content['id']);
		}

		if (defined('PAGE_ID') == false) {
			define('PAGE_ID', $page['id']);
		}
		if (isset($page['parent'])) {
			if (defined('MAIN_PAGE_ID') == false) {
				define('MAIN_PAGE_ID', $page['parent']);
			}

			if (defined('PARENT_PAGE_ID') == false) {
				define('PARENT_PAGE_ID', $page['parent']);
			}
		}
	}

	if (defined('ACTIVE_PAGE_ID') == false) {

		define('ACTIVE_PAGE_ID', false);
	}

	if (defined('CATEGORY_ID') == false) {
		define('CATEGORY_ID', false);
	}

	if (defined('CONTENT_ID') == false) {
		define('CONTENT_ID', false);
	}

	if (defined('POST_ID') == false) {
		define('POST_ID', false);
	}
	if (defined('PAGE_ID') == false) {
		define('PAGE_ID', false);
	}

	if (defined('MAIN_PAGE_ID') == false) {
		define('MAIN_PAGE_ID', false);
	}

	if (isset($page) and isset($page['active_site_template']) and ($page['active_site_template']) != '' and strtolower($page['active_site_template']) != 'default') {

		$the_active_site_template = $page['active_site_template'];
	} else {
		$the_active_site_template = get_option('curent_template');
		// d($the_active_site_template );
	}

	$the_active_site_template_dir = normalize_path(TEMPLATEFILES . $the_active_site_template . DS);

	if (defined('DEFAULT_TEMPLATE_DIR') == false) {

		define('DEFAULT_TEMPLATE_DIR', TEMPLATEFILES.'default'.DS);
	}

	if (defined('DEFAULT_TEMPLATE_URL') == false) {

		define('DEFAULT_TEMPLATE_URL', site_url('userfiles/' . TEMPLATEFILES_DIRNAME . '/' . 'default/'));
	}


	if (trim($the_active_site_template) != 'default') {

		if ((!strstr($the_active_site_template, DEFAULT_TEMPLATE_DIR))) {
			$use_default_layouts = $the_active_site_template_dir . 'use_default_layouts.php';
			if (is_file($use_default_layouts)) {
											//$render_file = ($use_default_layouts);
											//if()
											//
											//

				if(isset($page['layout_file'])){
					$template_view = DEFAULT_TEMPLATE_DIR .  $page['layout_file'];
				} else {
					$template_view = DEFAULT_TEMPLATE_DIR ;
				}
				if(isset($page)){
					if(!isset($page['layout_file']) or (isset($page['layout_file']) and $page['layout_file'] == 'inherit' or $page['layout_file'] == '')){
						$par_page = content_get_inherited_parent($page['id']);
						if($par_page != false){
							$par_page = get_content_by_id($par_page);
						}
						if(isset($par_page['layout_file'])){
							$the_active_site_template = $par_page['active_site_template'];
							$page['layout_file'] = $par_page['layout_file'];
							$page['active_site_template'] = $par_page['active_site_template'];
							$template_view = TEMPLATEFILES.$page['active_site_template'].DS .  $page['layout_file'];


						}

					}
				}

				if (is_file($template_view) == true) {

					if (defined('THIS_TEMPLATE_DIR') == false) {

						define('THIS_TEMPLATE_DIR', TEMPLATEFILES.$the_active_site_template.DS );

					}
					if (defined('THIS_TEMPLATE_URL') == false) {
						$the_template_url = site_url('userfiles/' . TEMPLATEFILES_DIRNAME . '/' . $the_active_site_template);

						$the_template_url = $the_template_url . '/';
						if (defined('THIS_TEMPLATE_URL') == false) {
							define("THIS_TEMPLATE_URL", $the_template_url);
						}
					}
					$the_active_site_template = 'default';
					$the_active_site_template_dir = DEFAULT_TEMPLATE_DIR;

														//	d($the_active_site_template_dir);
				}


			}
		}

	} else {
		//d($the_active_site_template);
	}






	if (defined('ACTIVE_TEMPLATE_DIR') == false) {

		define('ACTIVE_TEMPLATE_DIR', $the_active_site_template_dir);
	}

	if (defined('THIS_TEMPLATE_DIR') == false) {

		define('THIS_TEMPLATE_DIR', $the_active_site_template_dir);
	}

	if (defined('THIS_TEMPLATE_URL') == false) {
		$the_template_url = site_url('userfiles/' . TEMPLATEFILES_DIRNAME . '/' . $the_active_site_template);

		$the_template_url = $the_template_url . '/';
		if (defined('THIS_TEMPLATE_URL') == false) {
			define("THIS_TEMPLATE_URL", $the_template_url);
		}
	}




	if (defined('TEMPLATE_DIR') == false) {

		define('TEMPLATE_DIR', $the_active_site_template_dir);
	}

	if (defined('ACTIVE_SITE_TEMPLATE') == false) {

		define('ACTIVE_SITE_TEMPLATE', $the_active_site_template);
	}

	if (defined('TEMPLATES_DIR') == false) {

		define('TEMPLATES_DIR', TEMPLATEFILES);
	}

	$the_template_url = site_url('userfiles/' . TEMPLATEFILES_DIRNAME . '/' . $the_active_site_template);

	$the_template_url = $the_template_url . '/';
	if (defined('TEMPLATE_URL') == false) {
		define("TEMPLATE_URL", $the_template_url);
	}





	if (defined('LAYOUTS_DIR') == false) {

		$layouts_dir = TEMPLATE_DIR . 'layouts/';

		define("LAYOUTS_DIR", $layouts_dir);
	} else {

		$layouts_dir = LAYOUTS_DIR;
	}

	if (defined('LAYOUTS_URL') == false) {

		$layouts_url = reduce_double_slashes(dirToURL($layouts_dir) . '/');

		define("LAYOUTS_URL", $layouts_url);
	} else {

		$layouts_url = LAYOUTS_URL;
	}



	return true;
}

function get_layout_for_page($page = array()) {






	$args = func_get_args();
	$function_cache_id = '';
	 if(is_array($page)){
	 	ksort($page);
	 }
	$function_cache_id = $function_cache_id . serialize($page) ;


	$cache_id = __FUNCTION__ . crc32($function_cache_id);
	if (isset($page['id']) and intval($page['id']) != 0){
		$cache_group = 'content/'.$page['id'];
	} else {
		$cache_group = 'content/global';
	}


	$cache_content = cache_get_content($cache_id, $cache_group);

	if (($cache_content) != false) {

	 return $cache_content;
	}


	$render_file = false;
	$look_for_post = false;
	$template_view_set_inner = false;

	if (isset($page['active_site_template']) and isset($page['active_site_template']) and isset($page['layout_file']) and $page['layout_file'] != 'inherit'  and $page['layout_file'] != '') {
		$test_file = str_replace('___',DS,$page['layout_file']);
								$render_file_temp = TEMPLATES_DIR . $page['active_site_template'] . DS . $test_file ;

						if(is_file($render_file_temp)){
							$render_file = $render_file_temp;
						}
	}



	if ($render_file == false and isset($page['id']) and isset($page['active_site_template']) and isset($page['layout_file']) and $page['layout_file'] == 'inherit') {

		$inherit_from = get_content_parents($page['id']);

		$found = 0;
		if(!empty( $inherit_from )){
			foreach ( $inherit_from   as $value) {
				if($found  == 0 and $value !=$page['id']){
					$par_c = get_content_by_id($value);
					if (isset($par_c['id']) and isset($par_c['active_site_template']) and isset($par_c['layout_file']) and $par_c['layout_file'] != 'inherit') {

						$page['layout_file'] = $par_c['layout_file'];
						$page['active_site_template'] = $par_c['active_site_template'];
						$render_file_temp = TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'] ;

						if(is_file($render_file_temp)){
							$render_file = $render_file_temp;
						} else {

							$render_file_temp = DEFAULT_TEMPLATE_DIR. $page['layout_file'] ;
							if(is_file($render_file_temp)){
								$render_file = $render_file_temp;

								//d(THIS_TEMPLATE_DIR);

							}
						}

						//$page = $par_c;
						//
						$found = 1;
					}
				}
			}
		}
		 //d($inherit_from);
	}




	if ($render_file == false and isset($page['content_type']) and $page['content_type'] == 'post') {
		$look_for_post = $page;
		if (isset($page['parent'])) {

			$par_page = get_content_by_id($page['parent']);

			if (isarr($par_page)) {
				$page = $par_page;
			} else {
				$template_view_set_inner = ACTIVE_TEMPLATE_DIR . DS . 'inner.php';
								$template_view_set_inner2 = ACTIVE_TEMPLATE_DIR . DS . 'layouts/inner.php';


			}
		} else {
			$template_view_set_inner = ACTIVE_TEMPLATE_DIR . DS . 'inner.php';
		   $template_view_set_inner2 = ACTIVE_TEMPLATE_DIR . DS . 'layouts/inner.php';



		}
	}

	if ($render_file == false and isset($page['simply_a_file'])) {



		$simply_a_file2 = ACTIVE_TEMPLATE_DIR . $page['simply_a_file'];
		$simply_a_file3 = ACTIVE_TEMPLATE_DIR . 'layouts'.DS.$page['simply_a_file'];

		if ($render_file == false and  is_file($simply_a_file3) == true) {
			$render_file = $simply_a_file3;

		}


		if ($render_file == false and  is_file($simply_a_file2) == true) {
			$render_file = $simply_a_file2;

		}


		if ($render_file == false and is_file($page['simply_a_file']) == true) {
			$render_file = $page['simply_a_file'];
		}
		//



	}
	if (!isset($page['active_site_template'])){
		$page['active_site_template'] = ACTIVE_SITE_TEMPLATE;
	}
	if ($render_file == false and isset($page['active_site_template']) and trim($page['active_site_template']) != 'default') {

		$use_default_layouts = TEMPLATES_DIR . $page['active_site_template'].DS.'use_default_layouts.php';

		if (is_file($use_default_layouts)) {
			$page['active_site_template']  = 'default';
		}
	}
	if ($render_file == false and isset($page['active_site_template']) and isset($page['content_type']) and $render_file == false and !isset($page['layout_file'])) {
		$layouts_list  = layouts_list('site_template='.$page['active_site_template']);

		if(isarr($layouts_list)){
			foreach ($layouts_list as $layout_item) {
				if($render_file == false and isset($layout_item['content_type']) and isset($layout_item['layout_file']) and $page['content_type'] ==$layout_item['content_type']){

					$page['layout_file']  = $layout_item['layout_file'];
					$render_file = TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'] ;
				}

			}
		}



	}


	if ($render_file == false and isset($page['active_site_template']) and $render_file == false and isset($page['layout_file'])) {
		if ($look_for_post != false) {
			$f1 = $page['layout_file'];


			$stringA = $f1;
			$stringB = "_inner";
			$length = strlen($stringA);
			$temp1 = substr($stringA, 0, $length - 4);
			$temp2 = substr($stringA, $length - 4, $length);
			$f1 = $temp1 . $stringB . $temp2;





			if (strtolower($page['active_site_template']) == 'default') {
				$template_view = ACTIVE_TEMPLATE_DIR . DS . $f1;
			} else {

				$template_view = TEMPLATES_DIR . $page['active_site_template'] . DS . $f1;
			}



//.


			if (is_file($template_view) == true) {

				$render_file = $template_view;
			} else {






				$dn = dirname($template_view);
				$dn1 = $dn . DS;
				$f1 = $dn1 . 'inner.php';

				if (is_file($f1) == true) {
					$render_file = $f1;
				} else {
					$dn = dirname($dn);
					$dn1 = $dn . DS;
					$f1 = $dn1 . 'inner.php';

					if (is_file($f1) == true) {
						$render_file = $f1;
					} else {
						$dn = dirname($dn);
						$dn1 = $dn . DS;
						$f1 = $dn1 . 'inner.php';

						if (is_file($f1) == true) {
							$render_file = $f1;
						}
					}
				}



			}
		}



		if ($render_file == false) {
			if (strtolower($page['active_site_template']) == 'default') {
				$template_view = ACTIVE_TEMPLATE_DIR . DS . $page['layout_file'];
			} else {
				$template_view = TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'];
			}

			if (is_file($template_view) == true) {
				$render_file = $template_view;
			} else {


				if (trim($page['active_site_template']) != 'default') {
					$use_default_layouts = TEMPLATES_DIR . $page['active_site_template'] .DS  . 'use_default_layouts.php';


					if (is_file($use_default_layouts)) {
						$page['active_site_template'] = 'default';

					}


				}


			}
		}

	}




	if (isset($page['active_site_template']) and $render_file == false and strtolower($page['active_site_template']) == 'default') {
		$template_view = ACTIVE_TEMPLATE_DIR . 'index.php';
		if (is_file($template_view) == true) {
			$render_file = $template_view;
		}
	}

	if (isset($page['active_site_template']) and $render_file == false and strtolower($page['active_site_template']) != 'default') {
		$template_view = ACTIVE_TEMPLATE_DIR . 'index.php';
		if (is_file($template_view) == true) {
			$render_file = $template_view;
		}
	}
	if (isset($page['active_site_template']) and $render_file == false and strtolower($page['active_site_template']) != 'default') {
		$template_view = ACTIVE_TEMPLATE_DIR . 'index.html';
		if (is_file($template_view) == true) {
			$render_file = $template_view;
		}
	}

	if (isset($page['active_site_template']) and $render_file == false and strtolower($page['active_site_template']) != 'default') {
		$template_view = ACTIVE_TEMPLATE_DIR . 'index.htm';
		if (is_file($template_view) == true) {
			$render_file = $template_view;
		}
	}

	if($render_file == false and $template_view_set_inner != false){

		if(isset($template_view_set_inner2)){
		$template_view_set_inner2 = normalize_path($template_view_set_inner2, false);
		if (is_file($template_view_set_inner2) == true) {
			$render_file = $template_view_set_inner2;
		}
		}

		$template_view_set_inner = normalize_path($template_view_set_inner, false);
		if ($render_file == false and is_file($template_view_set_inner) == true) {
			$render_file = $template_view_set_inner;
		}


	//d($template_view_set_inner);
	}
	//d($render_file);

	//    if (trim($page['layout_name']) != '') {
	//        $template_view = ACTIVE_TEMPLATE_DIR . 'layouts' . DS . $page['layout_name'] . DS . 'index.php';
	//        // d($template_view);
	//        if (is_file($template_view) == true) {
	//            $render_file = $template_view;
	//        }
	//    }
	//
	//    if ($render_file == false and strtolower($page['active_site_template']) == 'default') {
	//        $template_view = ACTIVE_TEMPLATE_DIR . 'index.php';
	//        if (is_file($template_view) == true) {
	//            $render_file = $template_view;
	//        }
	//    }
	//
	//    if ($render_file == false and strtolower($page['active_site_template']) == 'default') {
	//        $template_view = ACTIVE_TEMPLATE_DIR . 'index.php';
	//        if (is_file($template_view) == true) {
	//            $render_file = $template_view;
	//        }
	//    }
	//
	//    if ($render_file == false and ($page['layout_name']) == false and ($page['layout_style']) == false) {
	//        $template_view = ACTIVE_TEMPLATE_DIR . 'index.php';
	//        if (is_file($template_view) == true) {
	//            $render_file = $template_view;
	//        }
	//    }
	if (isset($page['custom_view']) and isset($render_file)) {
		$check_custom = dirname($render_file) . DS;
		$cv = trim($page['custom_view']);
		$cv = str_replace('..', '', $cv);
		$cv = str_ireplace('.php', '', $cv);
		$check_custom_f = $check_custom . $cv . '.php';
		if (is_file($check_custom_f)) {
			$render_file = $check_custom_f;
		}
		//d($check_custom_f);

	}
	if ($render_file == false and ($page['layout_file']) != false) {
		$template_view = ACTIVE_TEMPLATE_DIR . DS . $page['layout_file'];
		$template_view = normalize_path($template_view, false);

		if (is_file($template_view) == true) {
			$render_file = $template_view;
		} else {

		}
	}
//	$render_file = reduce_double_slashes($render_file);
	cache_save($render_file, $cache_id, $cache_group);

	return $render_file;
}

function homepage_link() {
	$hp = get_homepage();
	return content_link($hp['id']);
}

function get_homepage() {

	// ->'content';
	$table = MW_TABLE_PREFIX . 'content';

	$sql = "SELECT * from $table where is_home='y'  order by updated_on desc limit 0,1 ";

	$q = db_query($sql, __FUNCTION__ . crc32($sql), 'content/global');
	// var_dump($q);
	$result = $q;

	$content = $result[0];

	return $content;
}

function get_content_by_url($url = '', $no_recursive = false) {
	return get_page_by_url($url, $no_recursive);
}

function get_page_by_url($url = '', $no_recursive = false) {
	if (strval($url) == '') {

		$url = url_string();
	}

	$u1 = $url;
	$u2 = site_url();

	$u1 = rtrim($u1, '\\');
	$u1 = rtrim($u1, '/');

	$u2 = rtrim($u2, '\\');
	$u2 = rtrim($u2, '/');
	$u1 = str_replace($u2, '', $u1);
	$u1 = ltrim($u1, '/');
	$url = $u1;
//d($url);
	// ->'content';
	$table = MW_TABLE_PREFIX . 'content';

	// $url = strtolower($url);
	//  $url = string_clean($url);
	$url = db_escape_string($url);
	$url = addslashes($url);

	$url12 = parse_url($url);
	if (isset($url12['scheme']) and isset($url12['host']) and isset($url12['path'])) {

		$u1 = site_url();
		$u2 = str_replace($u1, '', $url);
		$current_url = explode('?', $u2);
		$u2 = $current_url[0];
		$url = ($u2);
	} else {
		$current_url = explode('?', $url);
		$u2 = $current_url[0];
		$url = ($u2);
	}
	$url = rtrim($url, '?');
	$url = rtrim($url, '#');
	$sql = "SELECT id,url from $table where url='{$url}'   order by updated_on desc limit 0,1 ";
  //d($sql);
	$q = db_query($sql, __FUNCTION__ . crc32($sql), 'content/global');

	$result = $q;

	$content = $result[0];

	if (!empty($content)) {

		$get_by_id = get_content_by_id($content['id']);

		return $get_by_id;
	}

	if ($no_recursive == false) {

		if (empty($content) == true) {

			// /var_dump ( $url );

			$segs = explode('/', $url);

			$segs_qty = count($segs);

			for ($counter = 0; $counter <= $segs_qty; $counter += 1) {

				$test = array_slice($segs, 0, $segs_qty - $counter);

				$test = array_reverse($test);

				if (isset($test[0])) {
					$url = get_page_by_url($test[0], true);
				}
				if (!empty($url)) {

					return $url;
				}


			}
		}
	} else {
		$content['id'] = ((int)$content['id']);

		$get_by_id = get_content_by_id($content['id']);

		return $get_by_id;
	}
}

/**
 * Function to get single content item by id from the content_table
 *
 * @param
 *        	int
 * @return array
 * @author Peter Ivanov
 *
 */
function get_content_by_id($id) {


	// ->'content';
	$table = MW_TABLE_PREFIX . 'content';

	$id = intval($id);
	if ($id == 0) {
		return false;
	}

	$q = "SELECT * from $table where id='$id'  limit 0,1 ";

	$params = array();
	$params['id'] = $id;
	$params['limit'] = 1;
	$params['table'] = $table;
	//$params['debug'] = 1;
	$params['cache_group'] = 'content/'.$id;



	 $q = get($params);

	//  $q = db_get($table, $params, $cache_group = 'content/' . $id);
	 //  $q = db_query($q, __FUNCTION__ . crc32($q), 'content/' . $id);
	if (isset($q[0])) {
		$content = $q[0];
	} else {
		return false;
	}
	return $content;
}

function get_page($id = false) {
	if ($id == false) {
		return false;
	}

	// $CI = get_instance ();
	if (intval($id) != 0) {
		$page = get_content_by_id($id);

		if (empty($page)) {
			$page = get_content_by_url($id);
		}
	} else {
		if (empty($page)) {
			$page = array();
			$page['layout_name'] = trim($id);

			$page = get_pages($page);
			$page = $page[0];
		}
	}

	return $page;

	// $link = get_instance()->content_model->getContentURLByIdAndCache (
	// $link['id'] );
}

api_expose('reorder_content');
function reorder_content()
{
	$id = is_admin();
	if ($id == false) {
		exit('Error: not logged in as admin.');
	}
	$ids = $_POST['ids'];
	if (empty($ids)) {
		$ids = $_POST[0];
	}
	if (empty($ids)) {
		exit();
	}
	$ids = array_unique($ids);
	$ids_implode = implode(',', $ids);




	$table = MW_TABLE_PREFIX . 'content';



	// $q = " SELECT id, created_on, position from $table where id IN ($ids_implode)  order by position asc  ";
	// $q = db_query($q);
	// $max_date = $q[0]['created_on'];
	// $max_date_str = strtotime($max_date);
	$i = 1;
	foreach ($ids as $id) {
		$id = intval($id);
		cache_clean_group('content/'.$id);
		//$max_date_str = $max_date_str - $i;
	//	$nw_date = date('Y-m-d H:i:s', $max_date_str);
		//$q = " UPDATE $table set created_on='$nw_date' where id = '$id'    ";

		$q = " UPDATE $table set position=$i where id=$id   ";
       //  var_dump($q);
		$q = db_q($q);
		$i++;
	}
       //
        // var_dump($q);
	cache_clean_group('content/global');
	cache_clean_group('categories/global');
	exit();
}

api_expose('get_content_admin');

function get_content_admin($params) {
	if (is_admin() == false) {
		return false;
	}

	return get_content($params);
}



/**
 *
 * Function to get single content item by id from the content_table
 *
 * @access public
 * @package content
 *
 * @author Peter Ivanov
 * @version 1.0
 *
 *
 * @see db#get
 * @since 0.320
 * @return mixed Array with posts or false
 * @param array $params parameters for the DB
 *

Example:
$params = array();
$params['is_active'] = 'y'; //get only active content
$params['parent'] = 2; //get by parent id
$params['created_by'] = 1; //get by author id
$params['content_type'] = 'post'; //get by content type
$params['subtype'] = 'product'; //get by subtype

$params['title'] = 'my title'; //get by title



 $data = get_content($params);


 */


api_expose('get_content');

function get_content($params = false) {

	if(defined('MW_API_CALL')){
		if (isset($_REQUEST['api_key']) and is_admin() == 0) {
			api_login($_REQUEST['api_key']);
			if(is_admin() == 0){
				return false;
			}
		}

	}

	if (defined('PAGE_ID') == false) {
				define_constants();
			}


	$params2 = array();

	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}

	if(!is_array($params)){
		$params = array();
		$params['is_active'] = 'y';
	}





 	$function_cache_id = false;

	$args = func_get_args();

	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($params);
	}

	$function_cache_id = __FUNCTION__ . crc32($function_cache_id);
	$cache_content = false;
	// $cache_content = cache_get_content($function_cache_id, $cache_group = 'content/global');
	if (($cache_content) == '--false--') {
		//return false;
	}
	// $cache_content = false;
	if (($cache_content) != false) {

		//	return $cache_content;
	} else {

		// $params['orderby'];
		if (isset($params['orderby'])) {
			$orderby = $params['orderby'];
		}
		if (isset($orderby) == false) {
			$orderby = array();
			//$orderby[0] = 'created_on';

			//$orderby[1] = 'DESC';

			$orderby[0] = 'position';

			$orderby[1] = 'ASC';


		}
		$cache_group = 'content/global';
		if (isset($params['cache_group'])) {
			$cache_group = $params['cache_group'];
		}


		if (isset($params['limit'])) {
			$limit = $params['limit'];
		} else {
			$limit = array();
			$limit[0] = '0';

			$limit[1] = '30';
		}
		//  $params['debug'] = 1;
		// d($table);


		$table = MW_TABLE_PREFIX . 'content';
		if(!isset($params['is_deleted'])){
			$params['is_deleted'] = 'n';
		}
		$params['table'] = $table;
		$params['cache_group'] = $cache_group;
		$get = get($params);

		//$get = db_get($table, $params, $cache_group );
		if (isset($params['count']) or isset($params['single']) or isset($params['one'])  or isset($params['data-count']) or isset($params['page_count']) or isset($params['data-page-count'])) {
				 if (isset($get['url'])) {
 					$get['url'] = site_url($get['url']);
				}
			return $get;
		}
		if (!empty($get)) {
			$data2 = array();
			foreach ($get as $item) {
				if (isset($item['url'])) {
					//$item['url'] = page_link($item['id']);
					$item['url'] = site_url($item['url']);
				}
				$data2[] = $item;
			}
			$get = $data2;
			//  cache_save($get, $function_cache_id, $cache_group = 'content/global');

			return $get;
		} else {
			// cache_save('--false--', $function_cache_id, $cache_group = 'content/global');

			return FALSE;
		}
	}
}

function post_link($id = false) {
	if (is_string($id)) {
		// $link = page_link_to_layout ( $id );
	}
	if ($id == false) {
		if (defined('PAGE_ID') == true) {
			$id = PAGE_ID;
		}
	}

	$link = get_content_by_id($id);
	if (strval($link) == '') {
		$link = get_page_by_url($id);
	}
	$link = site_url($link['url']);
	return $link;
}

api_expose('content_link');

function content_link($id = false) {
	if (is_string($id)) {
		// $link = page_link_to_layout ( $id );
	}
	if ($id == false) {
		if (defined('PAGE_ID') == true) {
			$id = PAGE_ID;
		}
	}
	if($id == 0){
		return site_url();
	}

	$link = get_content_by_id($id);
	if (strval($link['url']) == '') {
		$link = get_page_by_url($id);
	}
	$link = site_url($link['url']);
	return $link;
}

function page_link($id = false) {
	if (is_string($id)) {
		// $link = page_link_to_layout ( $id );
	}
	if ($id == false) {
		if (defined('PAGE_ID') == true) {
			$id = PAGE_ID;
		}
	}

	$link = get_content_by_id($id);
	if (isset($link['url'])) {
		if (strval($link['url']) == '') {
			$link = get_page_by_url($id);
		}
		$link = site_url($link['url']);
		return $link;
	} else {
		$link = site_url();
		return $link;
	}
}

/**
 * get_posts
 *
 * get_posts is used to get content by parameters
 *
 * @category posts
 *
 *
 */

function get_posts($params = false) {
	$params2 = array();

	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $params2;
	}

	// $params
	return get_content($params);
}

function paging_links($base_url = false, $pages_count, $paging_param = 'curent_page', $keyword_param = 'keyword') {

	// getCurentURL()
	if ($base_url == false) {
		/*if (PAGE_ID != false and CATEGORY_ID == false) {
			$base_url = page_link(PAGE_ID);

			// p($base_url);
		} elseif (PAGE_ID != false and CATEGORY_ID != false) {
			$base_url = category_link(CATEGORY_ID);
		} else {

			// $base_url =  full_url(true);
		}*/

		if (isAjax() == false) {
			$base_url = curent_url(1);

		} else {
			if ($_SERVER['HTTP_REFERER'] != false) {
				$base_url = $_SERVER['HTTP_REFERER'];
			}
		}


	}

	// print $base_url;

	$page_links = array();

// $the_url = parse_url($base_url, PHP_URL_QUERY);

	//$the_url = parse_url($base_url);
	$the_url = $base_url;

	$append_to_links  = '';
	if (strpos($the_url, '?')) {
		$the_url= substr($the_url, 0, strpos($the_url, '?'));
//$append_to_links = str_replace($the_url, '', $base_url);


	}

	//
	//
	//
	//
	if(isset($_GET) and !empty($_GET)){
		 // $append_to_links  = '?'.  http_build_query($_GET);
	}


	$the_url = explode('/', $the_url);

	// var_dump ( $the_url );

	for ($x = 1; $x <= $pages_count; $x++) {

		$new_url = array();

		$new = array();

		foreach ($the_url as $itm) {

			$itm = explode(':', $itm);

			if ($itm[0] == $paging_param) {

				$itm[1] = $x;
			}

			$new[] = implode(':', $itm);
		}

		$new_url = implode('/', $new);

		// var_dump ( $new_url);

		$page_links[$x] = $new_url.$append_to_links;
	}

	for ($x = 1; $x <= count($page_links); $x++) {

		if (stristr($page_links[$x], $paging_param . ':') == false) {

			$l = reduce_double_slashes($page_links[$x] . '/' . $paging_param . ':' . $x);
			$l = str_ireplace('module/', '', $l);
			$page_links[$x] = $l.$append_to_links;
		}
	}

	return $page_links;
}

/**
 * paging
 *
 * paging
 *
 * @access public
 * @category posts
 * @author Microweber
 * @link
 *
 * @param $display =
 *        	'default' //sets the default paging display with <ul> and </li>
 *        	tags. If $display = false, the function will return the paging
 *        	array which is the same as $posts_pages_links in every template
 *
 * @param $params['paging_param'] = 'current_page'; //the url param used for paging
 * @param $params['num'] = 5; //the numer of pages
 */
function paging($params) {
	$params = parse_params($params);
	$pages_count = 1;
	$base_url = false;
	$paging_param = 'curent_page';
	$keyword_param = 'keyword_param';
	$class = 'pagination';
	if(isset($params['num'])){
		$pages_count = $params['num'];
	}


	if(isset($params['num'])){
		$pages_count = $params['num'];
	}

	if(isset($params['class'])){
		$class = $params['class'];
	}

	if(isset($params['paging_param'])){
		$paging_param = $params['paging_param'];
	}
	 $curent_page_from_url = url_param($paging_param);

	 	if(isset($params['curent_page'])){
		$curent_page_from_url = $params['curent_page'];
	}

	$data = paging_links($base_url, $pages_count, $paging_param,$keyword_param);
	if(isarr($data)){
		$to_print = "<div class='{$class}'><ul>";
		foreach ($data as $key => $value) {
			$act_class = '';

			if( $curent_page_from_url != false){
				if(intval( $curent_page_from_url) == intval($key)){
					$act_class = ' class="active" ';
				}
			}
			$to_print .= "<li {$act_class}>";
			$to_print .= "<a {$act_class} href=\"$value\">$key</a> ";
			$to_print .= "</li>";
		}
		$to_print .= "</ul></div>";
		return  $to_print;
	}


}

/**
 * cf_val
 *
 * Returns custom field value
 *
 * @return string or array
 * @author Peter Ivanov
 */
function custom_field_value($content_id, $field_name, $use_vals_array = true) {
	$fields = get_custom_fields_for_content($content_id);
	if (empty($fields)) {
		return false;
	}
	// p($fields);
	foreach ($fields as $field) {
		if ((strtolower($field_name)) == strtolower($field['custom_field_name'])) {

			if (!empty($field['custom_field_values']) and $use_vals_array == true) {
				return $field['custom_field_values'];
			}

			if ($field['custom_field_value'] != 'Array' and $field['custom_field_value'] != '') {
				return $field['custom_field_value'];
			} else {

				if ($field['custom_field_values']) {
					return $field['custom_field_values'];
				}
			}

			// p ( $field );
		}
	}
}

function custom_fields_content($content_id, $field_type = false, $full = true) {
	return get_custom_fields_for_content($content_id, $full, $field_type);
}

function get_custom_fields_for_content($content_id, $full = true, $field_type = false) {
	$more = false;
	$more = get_custom_fields('content', $content_id, $full, false, false, $field_type);

	return $more;
}



function save_edit($post_data) {
	$id = is_admin();
	if ($id == false) {
		exit('Error: not logged in as admin.');
	}
	if ($post_data) {
		if (isset($post_data['json_obj'])) {
			$obj = json_decode($post_data['json_obj'], true);
			$post_data = $obj;
		}
		// p($post_data);
		if (isset($post_data['mw_preview_only'])) {
			$is_no_save = true;
			unset($the_field_data_all['mw_preview_only']);
		}
		$is_no_save = false;
		$the_field_data_all = $post_data;
	} else {
		exit('Error: no POST?');
	}
	$ref_page = $ref_page_url= $_SERVER['HTTP_REFERER'];
	if ($ref_page != '') {
		$ref_page = $the_ref_page = get_content_by_url($ref_page_url);
 		$ref_page2 = get_content_by_url($ref_page_url, true);


 		if($ref_page2 == false){

 				$ustr = url_string(1);;
			 if(is_module_installed($ustr)){
			 	$ref_page = false;
			 }

		}


		if($ref_page == false){


			$guess_page_data = new MwController();
			$guess_page_data->page_url =  $ref_page_url;
			$guess_page_data->return_data = true;
			$guess_page_data->create_new_page = true;
			$pd = $guess_page_data->index();
			if(isarr($pd) and isset($pd["active_site_template"])){
				$save_page = $pd;
				$save_page['url'] = url_string(1);
				$save_page['title'] = url_title(url_string(1));
				$page_id = save_content($save_page);
			//	d($save_page);
			//	d($save_page);
			}
			//d($pd);

			// d($ref_page_url);
		} else {
			$page_id = $ref_page['id'];
			$ref_page['custom_fields'] = get_custom_fields_for_content($page_id, false);
		}
	}

	$json_print = array();
	foreach ($the_field_data_all as $the_field_data) {
		$save_global = false;
		$save_layout = false;
		if (!empty($the_field_data)) {
			$save_global = false;
			if ($the_field_data['attributes']) {
				// $the_field_data ['attributes'] = json_decode($the_field_data
				// ['attributes']);
				// var_dump($the_field_data ['attributes']);
			}
			$content_id = $page_id;

			/*
			 * if (intval ( $the_field_data ['attributes'] ['page'] ) != 0) {
			 * $page_id = intval ( $the_field_data ['attributes'] ['page'] );
			 * $the_ref_page = get_page ( $page_id ); } if (intval (
			 * $the_field_data ['attributes'] ['post'] ) != 0) { $post_id =
			 * intval ( $the_field_data ['attributes'] ['post'] ); $content_id =
			 * $post_id; $the_ref_post = get_content_by_id ( $post_id ); } if
			 * (intval ( $the_field_data ['attributes'] ['category'] ) != 0) {
			 * $category_id = intval ( $the_field_data ['attributes']
			 * ['category'] ); } $page_element_id = false; if (strval (
			 * $the_field_data ['attributes'] ['id'] ) != '') { $page_element_id
			 * = ($the_field_data ['attributes'] ['id']); } if (($the_field_data
			 * ['attributes'] ['global']) != false) { $save_global = true; } if
			 * (($the_field_data ['attributes'] ['rel']) == 'global') {
			 * $save_global = true; $save_layout = false; } if (trim (
			 * $the_field_data ['attributes'] ['rel'] ) == 'layout') {
			 * $save_global = false; $save_layout = true; // p($the_field_data
			 * ['attributes'] ['rel']); } if (($the_field_data ['attributes']
			 * ['rel']) == 'post') { if ($ref_page != '') { $save_global =
			 * false; $ref_post = $the_ref_post = get_ref_post (); // p (
			 * $ref_post ); $post_id = $ref_post ['id']; $page_id = $ref_page
			 * ['id']; $content_id = $post_id; } } if (($the_field_data
			 * ['attributes'] ['rel']) == 'page') { p ( $_SERVER ); if
			 * ($ref_page != '') { $save_global = false; $ref_page =
			 * $the_ref_page = get_ref_page (); $page_id = $ref_page ['id'];
			 * $content_id = $page_id; } } if (($the_field_data ['attributes']
			 * ['rel']) == 'PAGE_ID') { // p ( $_SERVER ); if ($ref_page != '')
			 * { $save_global = false; $ref_page = $the_ref_page = get_ref_page
			 * (); $page_id = $ref_page ['id']; $content_id = $page_id; } } if
			 * (($the_field_data ['attributes'] ['rel']) == 'POST_ID') { // p (
			 * $_SERVER ); if ($ref_page != '') { $save_global = false;
			 * $ref_page = $the_ref_page = get_ref_page (); $page_id = $ref_page
			 * ['id']; $content_id = $page_id; } }
			 */
			$some_mods = array();
			if (($the_field_data['attributes'])) {
				if (($the_field_data['html']) != '') {
					$field = false;
					if (isset($the_field_data['attributes']['field'])) {
						$field = trim($the_field_data['attributes']['field']);
						//$the_field_data['attributes']['rel'] = $field;


					}

					if (isset($the_field_data['attributes']['data-field'])) {
						$field = $the_field_data['attributes']['field'] = trim($the_field_data['attributes']['data-field']);
					}

					if ($field == false) {
						if (isset($the_field_data['attributes']['id'])) {
						//	$the_field_data['attributes']['field'] = $field = $the_field_data['attributes']['id'];
						}
					}

					if (($field != false)) {
						$page_element_id = $field;
					}
					if(!isset($the_field_data['attributes']['rel'])){
					$the_field_data['attributes']['rel'] = 'content';
					}
					$save_global = false;
					if (isset($the_field_data['attributes']['rel']) and (trim($the_field_data['attributes']['rel']) == 'global' or trim($the_field_data['attributes']['rel'])) == 'module') {
						$save_global = true;
						// p($the_field_data ['attributes'] ['rel']);
					} else {
						$save_global = false;
					}
					if (isset($the_field_data['attributes']['rel']) and trim($the_field_data['attributes']['rel']) == 'layout') {
						$save_global = false;
						$save_layout = true;
					} else {
						$save_layout = false;
					}




					if(!isset($the_field_data['attributes']['data-id'])){
						$the_field_data['attributes']['data-id'] = $content_id;
					}

					$save_global = 1;

					if (isset($the_field_data['attributes']['rel']) and isset($the_field_data['attributes']['data-id'])) {



						$rel_ch = trim($the_field_data['attributes']['rel']);
						switch ($rel_ch) {
							case 'content':

							$save_global = false;
							$save_layout = false;
							$content_id_for_con_field = $content_id = $the_field_data['attributes']['data-id'];
							break;
							case 'page':
							case 'post':
							$save_global = false;
							$save_layout = false;
							$content_id_for_con_field = $content_id = $page_id;
							break;


							default:

							break;
						}



					}
					$inh = false;
					if (isset($the_field_data['attributes']['rel']) and ($the_field_data['attributes']['rel']) == 'inherit') {


						$save_global = false;
						$save_layout = false;
						$content_id = $page_id;

						$inh = content_get_inherited_parent($page_id);
						if($inh != false){
							$content_id_for_con_field = $content_id = $inh;

						}

					}








					$save_layout = false;
					if($inh  == false and !isset($content_id_for_con_field)){

						if(isarr($ref_page) and isset($ref_page['parent']) and  isset($ref_page['content_type'])  and $ref_page['content_type'] == 'post'){
							$content_id_for_con_field = intval($ref_page['parent']);
						// d($content_id);
						} else {
							$content_id_for_con_field = intval($ref_page['id']);

						}
					}
					$html_to_save = $the_field_data['html'];
					$html_to_save = $content = make_microweber_tags($html_to_save);
					if ($save_global == false and $save_layout == false) {
						if ($content_id) {

							$for_histroy = $ref_page;
							$old = false;
							$field123 = str_ireplace('custom_field_', '', $field);

							if (stristr($field, 'custom_field_')) {

								$old = $for_histroy['custom_fields'][$field123];
							} else {

								if (isset($for_histroy['custom_fields'][$field123])) {
									$old = $for_histroy['custom_fields'][$field123];
								} elseif (isset($for_histroy[$field])) {
									$old = $for_histroy[$field];
								}
							}
							$history_to_save = array();
							$history_to_save['table'] = 'content';
							$history_to_save['id'] = $content_id;
							$history_to_save['value'] = $old;
							$history_to_save['field'] = $field;
							// p ( $history_to_save );
							if ($is_no_save != true) {
							//	save_history($history_to_save);
							}
							$cont_field = array();
							$cont_field['rel'] = 'content';
							$cont_field['rel_id'] = $content_id_for_con_field;
							$cont_field['value'] = $html_to_save;
							$cont_field['field'] = $field;
							if($field != 'content'){

								$cont_field1 = save_content_field($cont_field);
							}
							$to_save = array();
							$to_save['id'] = $content_id;

						// $to_save['debug'] = $content_id;

							$to_save['page_element_id'] = $page_element_id;

							$is_native_fld = db_get_table_fields('content');
							if (in_array($field, $is_native_fld)) {
								$to_save[$field] = ($html_to_save);
							} else {

								//$to_save['custom_fields'][$field] = ($html_to_save);
							}


							if ($is_no_save != true) {
								$json_print[] = $to_save;

								$saved = save_content($to_save);
							}
						} else if (isset($category_id)) {
							print(__FILE__ . __LINE__ . ' category is not implemented ... not ready yet');
						}
					} else {

						$cont_field = array();

						$cont_field['rel'] = $the_field_data['attributes']['rel'];
						$cont_field['rel_id'] = 0;
						if($cont_field['rel'] != 'global' and isset($the_field_data['attributes']['content-id'])){
							$cont_field['rel_id'] = $the_field_data['attributes']['content-id'];
						} else 	if($cont_field['rel'] != 'global' and isset($the_field_data['attributes']['data-id'])){
							$cont_field['rel_id'] = $the_field_data['attributes']['data-id'];
						}


						$cont_field['value'] = make_microweber_tags($html_to_save);;
						if((!isset($the_field_data['attributes']['field']) or $the_field_data['attributes']['field'] == '' )and isset($the_field_data['attributes']['data-field'])){
							$the_field_data['attributes']['field'] = $the_field_data['attributes']['data-field'];
						}
						$cont_field['field'] = $the_field_data['attributes']['field'];


							 	 // d($cont_field);


						//if($field != 'content'){

							$cont_field_new = save_content_field($cont_field);

						//}




						if ($save_global == true and $save_layout == false) {

							/*
							if (isset($the_field_data['attributes']['data-option_group'])) {
															$og = $the_field_data['attributes']['data-option_group'];
														} else {
															$og = 'editable_region';
														}

														$field_content = get_option($the_field_data['attributes']['field'], $og, $return_full = true, $orderby = false);
														$html_to_save = make_microweber_tags($html_to_save);
														// p($html_to_save,1);
														$to_save = $field_content;
														$to_save['option_key'] = $the_field_data['attributes']['field'];
														$to_save['option_value'] = $html_to_save;
														//  $to_save['option_key2'] = 'editable_region';
														$to_save['option_group'] = $og;
														$to_save['page_element_id'] = $page_element_id;

														if (isset($the_field_data['attributes']['data-module'])) {
															$to_save['module'] = $the_field_data['attributes']['data-module'];
														}

														$opts_saved = true;


							if ($is_no_save != true) {
								save_option($to_save);
							}
							 * */
							$json_print[] = $cont_field;
							$history_to_save = array();
							$history_to_save['table'] = 'global';
							// $history_to_save ['id'] = 'global';
							$history_to_save['value'] = $cont_field['value'] ;
							$history_to_save['field'] = $field;
							$history_to_save['page_element_id'] = $page_element_id;

							if ($is_no_save != true) {
							//	save_history($history_to_save);
 							}
						}
						if ($save_global == false and $save_layout == true) {

							$d = TEMPLATE_DIR . 'layouts' . DIRECTORY_SEPARATOR . 'editable' . DIRECTORY_SEPARATOR;
							$f = $d . $ref_page['id'] . '.php';
							if (!is_dir($d)) {
								mkdir_recursive($d);
							}

							file_put_contents($f, $html_to_save);
						}
					}
				}
			} else {

			}
		}
	}
	if (isset($opts_saved)) {
		cache_clean_group('options');
	}
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');

	$json_print = json_encode($json_print);

	$history_to_save = array();
	$history_to_save['table'] = 'edit';
	$history_to_save['id'] = (parse_url(strtolower($_SERVER['HTTP_REFERER']), PHP_URL_PATH));
	$history_to_save['value'] = $json_print;
	$history_to_save['field'] = 'html_content';
	save_history($history_to_save);
	// }
	print $json_print;
	//cache_clean_group('global/blocks');
	exit();
}

api_expose('delete_content');

function delete_content($data) {

	$adm = is_admin();
	if ($adm == false) {
		error('Error: not logged in as admin.');
	}

	$to_trash = true;
	$to_untrash = false;
	if (isset($data['forever']) or isset($data['delete_forever'])) {

			$to_trash = false;
	}
	if (isset($data['undelete'])) {

			$to_trash = true;
			$to_untrash = true;

	}

$del_ids =array();
	if (isset($data['id'])) {
		$c_id = intval($data['id']);
		$del_ids[] = $c_id;
		if ($to_trash == false) {
		db_delete_by_id('content', $c_id);
		}
	}

	if (isset($data['ids']) and isarr($data['ids'])) {
		foreach ($data['ids'] as   $value) {
			$c_id = intval($value);
			$del_ids[] = $c_id;
			if ($to_trash == false) {
			db_delete_by_id('content', $c_id);
			}
		}

	}



	if(!empty($del_ids)){
			$table = MW_DB_TABLE_CONTENT;

			foreach ($del_ids as   $value) {
			$c_id = intval($value);
				//$q = "update $table set parent=0 where parent=$c_id ";

				if($to_untrash == true){
					$q = "update $table set is_deleted='n' where id=$c_id ";
					$q = db_query($q);
					$q = "update $table set is_deleted='n' where parent=$c_id ";
					$q = db_query($q);

				}	else if ($to_trash == false) {
					$q = "update $table set parent=0 where parent=$c_id ";
					$q = db_query($q);
				} else {
					$q = "update $table set is_deleted='y' where id=$c_id ";
					$q = db_query($q);
					$q = "update $table set is_deleted='y' where parent=$c_id ";
					$q = db_query($q);

				}


				cache_clean_group('content/'.$c_id);
			}


				cache_clean_group('content/global');



	}
		return($del_ids);
}

/**
 * Function to save content into the content_table
 *
 * @param
 *        	array
 *
 * @param
 *        	boolean
 *
 * @return string | the id saved
 *
 * @author Peter Ivanov
 *
 * @version 1.0
 *
 * @since Version 1.0
 *
 */
api_expose('save_content');

function save_content($data, $delete_the_cache = true) {

	$adm = is_admin();
	$table = MW_DB_TABLE_CONTENT;
	$checks = mw_var('FORCE_SAVE_CONTENT');

	if($checks != $table){
		if ($adm == false) {
			error('Error: not logged in as admin.');
		}
	}
	$cats_modified = false;




	if (empty($data) or !isset($data['id'])) {

		return false;
	}

	if(isset($data['content_url']) and !isset($data['url'])){
		$data['url'] = $data['content_url'];
	}
	$data_to_save = $data;

	$more_categories_to_delete = array();
	if (!isset($data['url']) and intval($data['id']) != 0) {

		$q = "SELECT * from $table where id='{$data_to_save['id']}' ";

		$q = db_query($q);

		$thetitle = $q[0]['title'];

		$q = $q[0]['url'];

		$theurl = $q;

		$more_categories_to_delete = get_categories_for_content($data['id'], 'categories');
	} else {
		if(isset($data['url'])){
			$theurl = $data['url'];
		} else {
			$theurl = $data['title'];
		}
		$thetitle = $data['title'];
	}



	if (isset($data['url']) and (strval($data['url']) == '')) {
		//$data['url'] = ($thetitle);
	}

	if (isset($data['url']) and (strval($data['url']) != '')) {
		//$data['url'] = ($data['url']);
	}



	if (isset($data['title'])) {
		$data['title'] = htmlspecialchars_decode($data['title'], ENT_QUOTES);

		$data['title'] = strip_tags($data['title']);
	}

	if (isset($data['url']) == false or $data['url'] == '') {
		if (isset($data['title']) != false and intval ( $data ['id'] ) == 0) {
			$data['url'] = url_title($data['title']);


		}
	}



	if (isset($data['url']) != false) {
		// if (intval ( $data ['id'] ) == 0) {
		$data_to_save['url'] = $data['url'];

		// }
	}

	if (isset($data['category']) or isset($data['categories'])) {
		$cats_modified = true;
	}
	$table_cats = MW_TABLE_PREFIX . 'categories';

	if (isset($data['url']) and $data['url'] != false) {
		//$data['url'] = url_title($data['url']);

		if (trim($data['url']) == '') {

			$data['url'] = url_title($data['title']);
		}

		$date123 = date("YmdHis");

		$q = "select id, url from $table where url LIKE '{$data ['url']}'";

		$q = db_query($q);

		if (!empty($q)) {

			$q = $q[0];

			if ($data['id'] != $q['id']) {

				$data['url'] = $data['url'] . '-' . $date123;
				$data_to_save['url'] = $data['url'];
			}
		}

		if (isset($data_to_save['url']) and strval($data_to_save['url']) == '' and (isset($data_to_save['quick_save']) == false)) {

			$data_to_save['url'] = $data_to_save['url'] . '-' . $date123;
		}

		if (isset($data_to_save['title']) and strval($data_to_save['title']) == '' and (isset($data_to_save['quick_save']) == false)) {

			$data_to_save['title'] = 'post-' . $date123;
		}
		if (isset($data_to_save['url']) and strval($data_to_save['url']) == '' and (isset($data_to_save['quick_save']) == false)) {
			$data_to_save['url'] = strtolower(reduce_double_slashes($data['url']));
		}


		if (trim($data_to_save['url']) != '') {

			$data_to_save['url'] = url_title($data_to_save['title']);
		}
		// $data_to_save ['url_md5'] = md5 ( $data_to_save
		// ['url'] );
	}

	$data_to_save_options = array();

	if (isset($data_to_save['is_home']) and $data_to_save['is_home'] == 'y') {
		$sql = "UPDATE $table set is_home='n'   ";
		$q = db_query($sql);
	}

	if (isset($data_to_save['subtype']) and strval($data_to_save['subtype']) == 'dynamic') {
		$check_ex = false;
		if (isset($data_to_save['subtype_value']) and trim($data_to_save['subtype_value']) != '' and intval(($data_to_save['subtype_value'])) > 0) {

			$check_ex = get_category_by_id(intval($data_to_save['subtype_value']));
		}
		if ($check_ex == false) {
			if (isset($data_to_save['id']) and intval(trim($data_to_save['id'])) > 0) {
				$test2 = get_categories('data_type=category&rel=content&rel_id='.intval(($data_to_save['id'])));

				if(isset($test2[0])){
					$check_ex = $test2[0];
					$data_to_save['subtype_value'] =  $test2[0]['id'];
				}


			}

			if ($check_ex == false) {

			}

			unset($data_to_save['subtype_value']);
		}


		if (isset($check_ex) and $check_ex == false) {

			if (!isset($data_to_save['subtype_value_new'])) {
				if (isset($data_to_save['title'])) {
					//$cats_modified = true;
					//$data_to_save['subtype_value_new'] = $data_to_save['title'];
				}
			}
		}
	}

/*
	if (isset($data_to_save['subtype_value_new']) and strval($data_to_save['subtype_value_new']) != '') {


		if ($data_to_save['subtype_value_new'] != '') {

			if ($adm == true) {

				$new_category = array();
				$new_category["data_type"] = "category";
				$new_category["rel"] = "content";
				$new_category["table" ] = $table_cats;
				//$new_category["debug" ] = $table_cats;
					if (isset($data_to_save['id']) and intval(($data_to_save['id'])) > 0) {
					$new_category["rel_id"] = intval(($data_to_save['id']));
				}
				$new_category["title"] = $data_to_save['subtype_value_new'];
				$new_category["parent_id"] = "0";
				$cats_modified = true;
				//@todo remove code here and around
				//$new_category = save_category($new_category);

				$data_to_save['subtype_value'] = $new_category;
				$data_to_save['subtype'] = 'dynamic';
			}
		}

		if (isset($data_to_save['categories_categories_str']) and !empty($data_to_save['categories_categories_str'])) {
			$data_to_save['subtype_value_auto_create'] = $data_to_save['categories_categories_str'];

			if ($adm == true) {
				if (!is_array($original_data['subtype_value_auto_create'])) {

					$scats = explode(',', $data_to_save['subtype_value_auto_create']);
				} else {

					$scats = explode(',', $data_to_save['subtype_value_auto_create']);
				}
				if (!empty($scats)) {
					foreach ($scats as $sc) {
						$new_scategory = array();
						$new_scategory["data_type"] = "category";
						$new_scategory["title"] = $sc;
						$new_scategory["rel"] = "content";
				$new_scategory["table" ] = $table_cats;
						$new_scategory["parent_id"] = intval($new_category);
						$cats_modified = true;
						//@todo remove code here and around
					//	$new_scategory = save_category($new_scategory);
					}
				}
			}
		}
	}*/

	$par_page = false;
	if (isset($data_to_save['content_type']) and strval($data_to_save['content_type']) == 'post') {
		if (isset($data_to_save['parent']) and intval($data_to_save['parent']) > 0) {
			$par_page = get_content_by_id($data_to_save['parent']);
		}





		if (is_array($par_page)) {



			if($par_page['subtype'] == 'static'){
				$par_page_new = array();
				$par_page_new['id'] = $par_page['id'];
				$par_page_new['subtype'] = 'dynamic';

				$par_page_new = save_data($table, $par_page_new);
				$cats_modified = true;
			}
			if (!isset($data_to_save['categories'])) {
				$data_to_save['categories'] = '';
			}
			if (is_string($data_to_save['categories']) and isset($par_page['subtype_value']) and $par_page['subtype_value'] != '') {
				$data_to_save['categories'] = $data_to_save['categories'] . ', ' . $par_page['subtype_value'];
			}
		}
		$c1 = false;
		if (isset($data_to_save['categories']) and $par_page == false) {
			if (is_string($data_to_save['categories'])) {
				$c1 = explode(',', $data_to_save['categories']);
				if (isarr($c1)) {
					foreach ($c1 as $item) {
						$item = intval($item);
						if ($item > 0) {
							$cont_cat = get_content('limit=1&content_type=page&subtype=dynamic&subtype_value=' . $item);
						//	d($cont_cat);
							if (isset($cont_cat[0]) and isarr($cont_cat[0])) {
								$cont_cat = $cont_cat[0];
								if (isset($cont_cat["subtype_value"]) and intval($cont_cat["subtype_value"]) > 0) {


									$data_to_save['parent'] = $cont_cat["id"];
									break;
								}
							}
							//
						}
					}
				}


			}
		}
	}

	if (isset($data_to_save['content'])) {
		if(trim($data_to_save['content']) == '' or $data_to_save['content'] == false){
			unset($data_to_save['content']);
	//
		} else {
			$data_to_save['content'] = make_microweber_tags($data_to_save['content']);
		}
	}

 //$data_to_save['debug'] = 1;
	$cats_modified = true;
	$save = save_data($table, $data_to_save);

	if (isset($data_to_save['subtype']) and strval($data_to_save['subtype']) == 'dynamic') {
		$new_category = get_categories_for_content($save);

		if ($new_category == false ) {
		//$new_category_id = intval($new_category);
			$new_category = array();
			$new_category["data_type"] = "category";
			$new_category["rel"] = 'content';
			$new_category["rel_id"] = $save;
			$new_category["table" ] = $table_cats;
			$new_category["id" ] = 0;
			$new_category["title"] = $data_to_save['title'];
			$new_category["parent_id"] = "0";
			$cats_modified = true;
					//	 d($new_category);
			$new_category = save_category($new_category);


		}
	}
	$custom_field_table = MW_TABLE_PREFIX . 'custom_fields';

	$sid = session_id();

	$id = $save;

	$clean = " update $custom_field_table set
	rel =\"content\"
	, rel_id =\"{$id}\"
	where
	session_id =\"{$sid}\"
	and (rel_id=0 or rel_id IS NULL) and rel =\"content\"

	";


	db_q($clean);
	cache_clean_group('custom_fields');

	$media_table =  MW_TABLE_PREFIX . 'media';

	$clean = " update $media_table set

	rel_id =\"{$id}\"
	where
	session_id =\"{$sid}\"
	and rel =\"content\" and (rel_id=0 or rel_id IS NULL)

	";


	cache_clean_group('media');

	db_q($clean);

	if (isset($data_to_save['parent']) and intval($data_to_save['parent']) != 0) {
		cache_clean_group('content' . DIRECTORY_SEPARATOR . intval($data_to_save['parent']));
	}
	if (isset($data_to_save['id']) and intval($data_to_save['id']) != 0) {
		cache_clean_group('content' . DIRECTORY_SEPARATOR . intval($data_to_save['id']));
	}
	cache_clean_group('content' . DIRECTORY_SEPARATOR . 'global');
	cache_clean_group('content' . DIRECTORY_SEPARATOR . '0');
	cache_clean_group('content_fields/global');

	if ($cats_modified != false) {

		cache_clean_group('categories/global');
		cache_clean_group('categories_items/global');
		if (isset($c1) and isarr($c1)) {
			foreach ($c1 as $item) {
				$item = intval($item);
				if ($item > 0) {
					cache_clean_group('categories/' . $item);
				}
			}
		}
	}
	return $save;
	//exit();
	// if ($data_to_save ['content_type'] == 'page') {
	// if (!empty($data_to_save['menus'])) {
	//
	// // housekeep
	//
	// $this -> removeContentFromUnusedMenus($save, $data_to_save['menus']);
	//
	// foreach ($data_to_save ['menus'] as $menu_item) {
	//
	// $to_save = array();
	//
	// $to_save['item_type'] = 'menu_item';
	//
	// $to_save['item_parent'] = $menu_item;
	//
	// $to_save['content_id'] = intval($save);
	//
	// $to_save['item_title'] = $data_to_save['title'];
	//
	// $this -> saveMenu($to_save);
	//
	// $this -> core_model -> cleanCacheGroup('menus');
	// }
	// }
	//
	// // }
	// // $this->core_model->cacheDeleteAll ();
	//
	// if ($data_to_save['preserve_cache'] == false) {
	// if (intval($data_to_save['parent']) != 0) {
	// cache_clean_group('content' . DIRECTORY_SEPARATOR . intval($data_to_save['parent']));
	// }
	// cache_clean_group('content' . DIRECTORY_SEPARATOR . $id);
	// // cache_clean_group ( 'content' . DIRECTORY_SEPARATOR . '0' );
	// cache_clean_group('content' . DIRECTORY_SEPARATOR . 'global');
	//
	// if (!empty($data_to_save['categories_categories'])) {
	// foreach ($data_to_save ['categories_categories'] as $cat) {
	//
	// cache_clean_group('categories' . DIRECTORY_SEPARATOR . intval($cat));
	// }
	// // cache_clean_group ( 'categories' . DIRECTORY_SEPARATOR . '0' );
	// cache_clean_group('categories' . DIRECTORY_SEPARATOR . 'global');
	// cache_clean_group('categories' . DIRECTORY_SEPARATOR . 'items');
	// }
	//
	// if (!empty($more_categories_to_delete)) {
	// foreach ($more_categories_to_delete as $cat) {
	// cache_clean_group('categories' . DIRECTORY_SEPARATOR . intval($cat));
	// }
	// }
	// }
	// return $save;
}


	//api_expose('save_content_field');

function save_content_field($data, $delete_the_cache = true) {

	$adm = is_admin();
	$table = MW_DB_TABLE_CONTENT_FIELDS;
	//$checks = mw_var('FORCE_SAVE_CONTENT');


	if ($adm == false) {
		error('Error: not logged in as admin.');
	}

	if(!is_array($data)){
		$data = array();
	}
	if(!isset($data['rel']) or !isset($data['rel_id'])){
		error('Error: '.__FUNCTION__.' rel and rel_id is required');
	}
	//if($data['rel'] == 'global'){
	if(isset($data['field'])){
		$fld = db_escape_string($data['field']);
		$fld_rel = db_escape_string($data['rel']);
		$del_q = "delete from {$table} where rel='$fld_rel' and  field='$fld' ";
		if(isset($data['rel_id'])){
			$i = db_escape_string($data['rel_id']);
			$del_q .= " and  rel_id='$i' ";

		} else {
			$data['rel_id'] = 0;
		}
		$cache_group = guess_cache_group('content_fields/'.$data['rel'].'/'.$data['rel_id']);
		db_q($del_q);
		cache_clean_group($cache_group);

cache_clean_group('content_fields/global');

	}
	//}

	$save = save_data($table, $data);


	return $save;



}
function get_content_field($data, $debug = false) {


	$table = MW_DB_TABLE_CONTENT_FIELDS;


	if(is_string($data)){
		$data = parse_params($data);
	}

	if(!is_array($data)){
		$data = array();
	}
	// d($data);



	if(!isset($data['rel'])){
		if(isset($data['rel'])){
			if($data['rel'] == 'content' or $data['rel'] == 'page' or $data['rel'] == 'post'){
				$data['rel']  = 'content';
			}
			$data['rel'] = $data['rel'];
		}
	}
	if(!isset($data['rel_id'])){
		if(isset($data['data-id'])){
			$data['rel_id'] = $data['data-id'];
		} else {

		}
	}
	if(!isset($data['rel_id'])){
		$data['rel_id'] = 0;
	}

	if(!isset($data['rel']) or !isset($data['rel_id'])){
		error('Error: '.__FUNCTION__.' rel and rel_id is required');
	}
	//if($data['rel'] == 'global'){
	if(isset($data['field'])){

		$data['limit'] = 1;
		$data['cache_group'] = guess_cache_group('content_fields/'.$data['rel'].'/'.$data['rel_id']);

		$data['one'] = 1;
		$data['table'] = $table;
		if($debug!=false){
			$data['debug'] = 1;
		}
		//
		$get = get($data);
//	d($get);
		if(isset($get['value'])){
			return $get['value'];
		}
	}
	//}

	return false;



}
/*
 *
 *
 *
 * Example Usage:
 * pt_opts = array();
 * $pt_opts['link'] = "{title}";
 * $pt_opts['list_tag'] = "ol";
 * $pt_opts['list_item_tag'] = "li";
 *
 *
 * pages_tree($pt_opts);
 *
 *
 *
 *
 * Example Usage to make options for <select>:
 * $pt_opts = array();
 * $pt_opts['link'] = "{title}";
 * $pt_opts['list_tag'] = " ";
 * $pt_opts['list_item_tag'] = "option";
 * $pt_opts['active_ids'] = $data['parent'];
 * $pt_opts['active_code_tag'] = '   selected="selected"  ';
 * $pt_opts['ul_class'] = 'nav';
 * $pt_opts['li_class'] = 'nav-item';
 *  pages_tree($pt_opts);
 *
 * 	Other options
 * $pt_opts['parent'] = "8"; //
 * $pt_opts['include_first'] =  true; //includes the parent in the tree
 * $pt_opts['id_prefix'] = 'my_id';
 *
 *
 *
 *
 *
 *
 *
 *
 */

function pages_tree($parent = 0, $link = false, $active_ids = false, $active_code = false, $remove_ids = false, $removed_ids_code = false, $ul_class_name = false, $include_first = false) {

	$params2 = array();
	$params = false;
	$output = '';
	if (is_integer($parent)) {

	} else {
		$params = $parent;
		if (is_string($params)) {
			$params = parse_str($params, $params2);
			$params = $params2;
			extract($params);
		}
		if (is_array($params)) {
			$parent = 0;
			extract($params);
		}
	}

	$function_cache_id = false;
	$args = func_get_args();
	foreach ($args as $k => $v) {
		$function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	}
	$function_cache_id = __FUNCTION__ . crc32($function_cache_id);
	if ($parent == 0) {
		$cache_group = 'content/global';
	} else {
		$cache_group = 'content/' . $parent;
	}
	if (isset($include_categories) and $include_categories == true) {
		$cache_group = 'categories/global';
	}



	//

	$cache_content = cache_get_content($function_cache_id, $cache_group);
	$cache_content = false;
//	if (!isset($_GET['debug'])) {
	if (($cache_content) != false) {

			if (isset($params['return_data'])) {
			return $cache_content;
	}  else {
		print $cache_content;
	}

		return;
			//  return $cache_content;
	}
	//}
	$nest_level = 0;

	if (isset($params['nest_level'])) {
		$nest_level = $params['nest_level'];
	}
	$max_level = false;
	if (isset($params['max_level'])) {
		$max_level = $params['max_level'];
	} else if (isset($params['maxdepth'])) {
		$max_level =  $params['max_level'] = $params['maxdepth'];
	}

 	if ($max_level != false) {

		if (intval($nest_level) >= intval($max_level)) {
			print '';
			return;
		}
	}



	$is_shop = '';
	if (isset($params['is_shop'])) {
		$is_shop = db_escape_string($params['is_shop']);
		$is_shop = " and is_shop='{$is_shop} '";
		$include_first = false;

	}
	$ul_class = 'pages_tree';
	if (isset($params['ul_class'])) {

		$ul_class_name = $ul_class = $params['ul_class'] ;
	}

	$li_class = 'pages_tree_item';
	if (isset($params['li_class'])) {

		$li_class =   $params['li_class'] ;
	}


if (isset($params['include_categories'])) {

		$include_categories =   $params['include_categories'] ;
	}


	ob_start();






	$table = MW_TABLE_PREFIX . 'content';
	$par_q = '';
	if ($parent == false) {

		$parent = (0);
	} else {
		$par_q = " parent=$parent    and  ";

	}

	if ($include_first == true) {
		$sql = "SELECT * from $table where  id=$parent    and   is_deleted='n' and content_type='page' $is_shop order by position asc  limit 0,1";
		//
	} else {

		//$sql = "SELECT * from $table where  parent=$parent    and content_type='page'  order by updated_on desc limit 0,1";
		$sql = "SELECT * from $table where  $par_q  content_type='page' and   is_deleted='n' $is_shop  order by position asc limit 0,100";
	}

	//$sql = "SELECT * from $table where  parent=$parent    and content_type='page'  order by updated_on desc limit 0,1000";

	$cid = __FUNCTION__ . crc32($sql);
	$cidg = 'content/' . $parent;

	//$q = db_query($sql, $cid, $cidg);
	if (!isarr($params)) {
		$params = array();
	}
	if (isset($params['id'])) {
		unset($params['id']);
	}
	if (isset($append_to_link) == false) {
		$append_to_link = '';
	}
	if (isset($id_prefix) == false) {
		$id_prefix = '';
	}

	if (isset($link) == false) {
		$link = '<span data-page-id="{id}" class="pages_tree_link {nest_level} {active_class} {active_parent_class}" href="{link}' . $append_to_link . '">{title}</span>';
	}

	if (isset($list_tag) == false) {
		$list_tag = 'ul';
	}

	if (isset($active_code_tag) == false) {
		$active_code_tag = '';
	}

	if (isset($list_item_tag) == false) {
		$list_item_tag = 'li';
	}

	if (isset($remove_ids) and is_string($remove_ids)) {
		$remove_ids = explode(',', $remove_ids);
	}
	if (isset($active_ids)){
		$active_ids = $active_ids;
	}


	if (isset($active_ids) and is_string($active_ids)) {
		$active_ids = explode(',', $active_ids);
	}
	$the_active_class='active';
	if (isset($params['active_class'])) {
		$the_active_class = $params['active_class'];
	}


	//	$params['debug'] = $parent;
	//
	$params['content_type'] = 'page';

	$include_first_set = false;
	if ($include_first == true) {
		$include_first_set = 1;
		$include_first = false;
		$params['id'] = $parent;
		if (isset($params['include_first'])) {
			unset($params['include_first']);
		}
		if (isset($params['parent'])) {
			//unset($params['parent']);
		}





	} else {
		// if($parent != 0){
		$params['parent'] = $parent;
		// }
	}

	if(isset($params['is_shop']) and $params['is_shop'] == 'y'){
		if(isset($params['parent']) and $params['parent'] == 0){
			unset($params['parent']);
		}

		if(isset($params['parent']) and $params['parent'] == 'any'){
			unset($params['parent']);

		}

	} else {

		if(isset($params['parent']) and $params['parent'] == 'any'){
			$params['parent'] = 0;

		}


	}


	$params['limit'] = 50;
	$params['orderby'] = 'position asc';

	$params['curent_page'] = 1;


 //$params['debug'] = 1;

	$skip_pages_from_tree = false;
	$params2 = $params;
	if(isset($params['is_shop']) and $params['is_shop'] == 'y'){

 //$max_level = $params2['max_level'] =2;
 // $skip_pages_from_tree = 1;
	//	unset($params2['parent']);
//d($params2);


	}

	$q = get_content($params2);

	$result = $q;

	if (is_array($result) and !empty($result)) {
		$nest_level++;
		if (trim($list_tag) != '') {
			if ($ul_class_name == false) {
				print "<{$list_tag} class='pages_tree depth-{$nest_level}'>";
			} else {
				print "<{$list_tag} class='{$ul_class_name} depth-{$nest_level}'>";
			}
		}
		$res_count = 0;
		foreach ($result as $item) {
			$skip_me_cause_iam_removed = false;
			if (is_array($remove_ids) == true) {

				if (in_array($item['id'], $remove_ids)) {

					$skip_me_cause_iam_removed = true;
				}
			}

			if ($skip_me_cause_iam_removed == false) {

				$output = $output . $item['title'];

				$content_type_li_class = false;

				switch ($item ['subtype']) {

					case 'dynamic' :
					$content_type_li_class = 'have_category';

					break;

					case 'module' :
					$content_type_li_class = 'is_module';

					break;

					default :
					$content_type_li_class = 'is_page';

					break;
				}

				if ($item['is_home'] != 'y') {

				} else {

					$content_type_li_class .= ' is_home';
				}
				$st_str = '';
				$st_str2 = '';
				$st_str3 = '';
				if (isset($item['subtype']) and trim($item['subtype']) != '') {
					$st_str = " data-subtype='{$item['subtype']}' ";
				}

				if (isset($item['subtype_value']) and trim($item['subtype_value']) != '') {
					$st_str2 = " data-subtype-value='{$item['subtype_value']}' ";
				}

				if (isset($item['is_shop']) and trim($item['is_shop']) == 'y') {
					$st_str3 = " data-is-shop=true ";
					$content_type_li_class .= ' is_shop';
				}
				$iid = $item['id'];





				$to_pr_2 = "<{$list_item_tag} class='{$li_class} $content_type_li_class {active_class} {active_parent_class} depth-{$nest_level} item_{$iid} {exteded_classes}' data-page-id='{$item['id']}' value='{$item['id']}'  data-item-id='{$item['id']}'  {active_code_tag} data-parent-page-id='{$item['parent']}' {$st_str} {$st_str2} {$st_str3}  title='".addslashes($item['title'])."' >";

				if ($link != false) {


					$active_parent_class = '';
					//if(isset($item['parent']) and intval($item['parent']) != 0){
					if (intval($item['parent']) != 0 and intval($item['parent']) == intval(MAIN_PAGE_ID)) {
						$active_parent_class = 'active-parent';
					} 	elseif (intval($item['id']) == intval(MAIN_PAGE_ID)) {
						$active_parent_class = 'active-parent';
					}  else {
						$active_parent_class = '';
				 }

		//}
		if ($item['id'] == CONTENT_ID) {
			$active_class = 'active';
		} elseif ($item['id'] == PAGE_ID) {
			$active_class = 'active';
		} elseif ($item['id'] == POST_ID) {
			$active_class = 'active';
		} elseif (CATEGORY_ID != false and intval($item['subtype_value']) != 0 and $item['subtype_value'] == CATEGORY_ID) {
			$active_class = 'active';
		} else {
			$active_class = '';
		}


		$ext_classes = '';
		if($res_count == 0){
			$ext_classes .= ' first-child ';
			$ext_classes .= ' child-'.$res_count.'';
		} else if(!isset($result[$res_count +1])){
			$ext_classes .= ' last-child' ;
			$ext_classes .= ' child-'.$res_count.'';
		} else {
			$ext_classes .= ' child-'.$res_count.'';
		}

		if(isset($item['parent']) and intval($item['parent']) > 0){
			$ext_classes .= ' have-parent';
		}


		if (isset($item['subtype_value']) and intval($item['subtype_value']) != 0) {
			$ext_classes .= ' have-category';
		}

		if(isset($item['is_active']) and $item['is_active'] == 'n'){

					$ext_classes =$ext_classes. ' content-unpublished ';
				}

		$ext_classes = trim($ext_classes);
				$the_active_class = $active_class;




					$to_print = str_replace('{id}', $item['id'], $link);
					$to_print = str_replace('{active_class}', $active_class, $to_print);
					$to_print = str_replace('{active_parent_class}', $active_parent_class, $to_print);
					$to_print = str_replace('{exteded_classes}', $ext_classes, $to_print);
					$to_pr_2= str_replace('{exteded_classes}', $ext_classes, $to_pr_2);
					$to_pr_2= str_replace('{active_class}', $active_class, $to_pr_2);
					$to_pr_2= str_replace('{active_parent_class}', $active_parent_class, $to_pr_2);


					$to_print = str_replace('{title}', $item['title'], $to_print);

					$to_print = str_replace('{nest_level}', 'depth-' . $nest_level, $to_print);
					if (strstr($to_print, '{link}')) {
						$to_print = str_replace('{link}', page_link($item['id']), $to_print);
					}
					$empty1 =  intval($nest_level);
					$empty = '';
					for ($i1=0; $i1 < $empty1; $i1++) {
						$empty = $empty.'&nbsp;&nbsp;';
					}
					$to_print = str_replace('{empty}', $empty, $to_print);


					if (strstr($to_print, '{tn}')) {
						$to_print = str_replace('{tn}', thumbnail($item['id'], 'original'), $to_print);
					}
					foreach ($item as $item_k => $item_v) {
						$to_print = str_replace('{' . $item_k . '}', $item_v, $to_print);
					}
					$res_count++;
					if (is_array($active_ids) == true) {

						$is_there_active_ids = false;

						foreach ($active_ids as $active_id) {

							if (intval($item['id']) == intval($active_id)) {

								$is_there_active_ids = true;

								$to_print = str_ireplace('{active_code}', $active_code, $to_print);
								$to_print = str_ireplace('{active_class}', $the_active_class, $to_print);
								$to_pr_2 = str_ireplace('{active_class}', $the_active_class, $to_pr_2);
								$to_pr_2 = str_ireplace('{active_code_tag}', $active_code_tag, $to_pr_2);
							}
						}

						if ($is_there_active_ids == false) {

							$to_print = str_ireplace('{active_code}', '', $to_print);
							$to_print = str_ireplace('{active_class}', '', $to_print);
							$to_pr_2 = str_ireplace('{active_class}', '', $to_pr_2);
							$to_pr_2 = str_ireplace('{active_code_tag}', '', $to_pr_2);
						}
					} else {

						$to_print = str_ireplace('{active_code}', '', $to_print);
						$to_pr_2 = str_ireplace('{active_class}', '', $to_pr_2);
						$to_pr_2 = str_ireplace('{active_code_tag}', '', $to_pr_2);
					}
					$to_print = str_replace('{exteded_classes}', '', $to_print);

					if (is_array($remove_ids) == true) {

						if (in_array($item['id'], $remove_ids)) {

							if ($removed_ids_code == false) {

								$to_print = false;
							} else {
								$remove_ids[] = $item['id'];
								$to_print = str_ireplace('{removed_ids_code}', $removed_ids_code, $to_print);
								//$to_pr_2 = str_ireplace('{removed_ids_code}', $removed_ids_code, $to_pr_2);
							}
						} else {

							$to_print = str_ireplace('{removed_ids_code}', '', $to_print);
							//$to_pr_2 = str_ireplace('{removed_ids_code}', $removed_ids_code, $to_pr_2);
						}
					}
					$to_pr_2 = str_replace('{active_class}', '', $to_pr_2);
$to_pr_2 = str_replace('{exteded_classes}', '', $to_pr_2);

					print $to_pr_2;
					$to_pr_2 = false;
					print $to_print;
				} else {
					$to_pr_2 = str_ireplace('{active_class}', '', $to_pr_2);
					$to_pr_2 = str_replace('{exteded_classes}', '', $to_pr_2);
					$to_pr_2= str_replace('{active_parent_class}', '', $to_pr_2);


					print $to_pr_2;
					$to_pr_2 = false;
					print $item['title'];
				}

				if (is_array($params)) {
					$params['parent'] = $item['id'];
					if ($max_level != false) {
						$params['max_level'] = $max_level;
					}
					if (isset($params['is_shop'])) {
						unset($params['is_shop']);
					}

					//   $nest_level++;
					$params['nest_level'] = $nest_level;
					$params['ul_class_name'] = false;
					$params['ul_class'] = false;

					if (isset($params['ul_class_deep'])) {
						 $params['ul_class']= $params['ul_class_deep'];
					}

					if (isset($maxdepth)) {
							$params['maxdepth'] = $maxdepth;
						}




					if (isset($params['li_class_deep'])) {
						 $params['li_class']= $params['li_class_deep'];
					}

						if (isset($params['return_data'])) {
						unset( $params['return_data']);
					}

					if( $skip_pages_from_tree  == false){

						$children = pages_tree($params);
					}
				} else {
					if( $skip_pages_from_tree  == false){
					//	$children = pages_tree(intval($item['id']), $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name);

						$children = pages_tree(intval($item['id']), $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name = false);
					}
				}

				if (isset($include_categories) and $include_categories == true) {

					$content_cats = array();
					if (isset($item['subtype_value']) and intval($item['subtype_value']) == true) {

					}




					$cat_params = array();
					if (isset($item['subtype_value']) and intval($item['subtype_value']) != 0) {
						//$cat_params['subtype_value'] = $item['subtype_value'];
					}
						//$cat_params['try_rel_id'] = $item['id'];

					if(isset($categores_link)){
						$cat_params['link'] = $categores_link;

					} else {
						$cat_params['link'] =$link;
					}

					if(isset($categories_active_ids)){
						$cat_params['active_ids'] = $categories_active_ids;

					}

					if(isset($active_code)){
						$cat_params['active_code'] = $active_code;

					}




						//$cat_params['for'] = 'content';
					$cat_params['list_tag'] = $list_tag;
					$cat_params['list_item_tag'] = $list_item_tag;
					$cat_params['rel'] = 'content';
					$cat_params['rel_id'] = $item['id'];

					$cat_params['include_first'] = 1;
					$cat_params['nest_level'] = $nest_level;
					if ($max_level != false) {
						$cat_params['max_level'] = $max_level;
					}



					if($nest_level > 1){
						if (isset($params['ul_class_deep'])) {
							 $cat_params['ul_class']= $params['ul_class_deep'];
						}


						if (isset($params['li_class_deep'])) {
							 $cat_params['li_class']= $params['li_class_deep'];
						}

					} else {


						if (isset($params['ul_class'])) {
							 $cat_params['ul_class']= $params['ul_class'];
						}


						if (isset($params['li_class'])) {
							 $cat_params['li_class']= $params['li_class'];
						}


					}

					if (isset($debug)) {

					}
					//d($cat_params);
						 //d($cat_params);
					category_tree($cat_params);

				}
			}
			print "</{$list_item_tag}>";
		}


		if (trim($list_tag) != '') {
			print "</{$list_tag}>";
		}
	} else {

	}

	$content = ob_get_contents();
//	if (!isset($_GET['debug'])) {
	cache_save($content, $function_cache_id, $cache_group);
	//}
	ob_end_clean();

	if (isset($params['return_data'])) {
			return $content;
	}  else {
		print $content;
	}




	return;
}

function mw_create_default_content($what) {

	switch ($what) {
		case 'shop' :
		$is_shop = get_content('content_type=page&is_shop=y');
			//$is_shop = false;
		$new_shop = false;
		if ($is_shop == false) {
			$add_page = array();
			$add_page['id'] = 0;
			$add_page['parent'] = 0;

			$add_page['title'] = "Online shop";
			$add_page['url'] = "shop";
			$add_page['content_type'] = "page";
			$add_page['subtype'] = 'dynamic';
			$add_page['is_shop'] = 'y';
			$add_page['active_site_template'] = 'default';
			$find_layout = layouts_list();
			if(isarr($find_layout)){
				foreach ($find_layout as $item) {
					if (isset($item['layout_file']) and isset($item['is_shop']) ) {
						$add_page['layout_file'] = $item['layout_file'];
						if (isset($item['name'])) {
							$add_page['title'] = $item['name'];
						}
					}
				}
			}
				//  d($add_page);
			$new_shop = save_data('content',$add_page);
			clearcache();
				//
		} else {

			if(isset($is_shop[0])){
				$new_shop = $is_shop[0]['id'];
			}
		}

		$posts = get_content('content_type=post&parent=' . $new_shop);
		if ($posts == false and $new_shop != false) {
			$add_page = array();
			$add_page['id'] = 0;
			$add_page['parent'] = $new_shop;
			$add_page['title'] = "My product";
			$add_page['url'] = "my-product";
			$add_page['content_type'] = "post";
			$add_page['subtype'] = "product";

			$new_shop = save_content($add_page);
			 cache_clean_group('content');
			//clearcache();
		}


		break;



		case 'blog' :
		$is_shop = get_content('content_type=page&subtype=dynamic&is_shop=n&limit=1');
			//$is_shop = false;
		$new_shop = false;
		if ($is_shop == false) {
			$add_page = array();
			$add_page['id'] = 0;
			$add_page['parent'] = 0;

			$add_page['title'] = "Blog";
			$add_page['url'] = "blog";
			$add_page['content_type'] = "page";
			$add_page['subtype'] = 'dynamic';
			$add_page['is_shop'] = 'n';
			$add_page['active_site_template'] = 'default';
			$find_layout = layouts_list();
			if(isarr($find_layout)){
				foreach ($find_layout as $item) {
					if (!isset($item['is_shop']) and isset($item['layout_file']) and isset($item['content_type']) and trim(strtolower($item['content_type'])) =='dynamic' ) {
						$add_page['layout_file'] = $item['layout_file'];
						if (isset($item['name'])) {
							$add_page['title'] = $item['name'];
						}
					}
				}

				foreach ($find_layout as $item) {
					if (isset($item['name']) and stristr($item['name'], 'blog') and !isset($item['is_shop']) and isset($item['layout_file']) and isset($item['content_type']) and trim(strtolower($item['content_type'])) =='dynamic' ) {
						$add_page['layout_file'] = $item['layout_file'];
						if (isset($item['name'])) {
							$add_page['title'] = $item['name'];
						}
					}
				}


			}
				//  d($add_page);
			$new_shop = save_data('content',$add_page);
			 cache_clean_group('content');
				//
		} else {

			if(isset($is_shop[0])){
				$new_shop = $is_shop[0]['id'];
			}
		}




		break;

		case 'default' :
		case 'install' :
		$any = get_content('count=1&content_type=page&limit=1');
		if(intval($any) == 0){


			$table = MW_TABLE_PREFIX . 'content';
			mw_var('FORCE_SAVE_CONTENT', $table);
			mw_var('FORCE_SAVE', $table);

			$add_page = array();
			$add_page['id'] = 0;
			$add_page['parent'] = 0;
			$add_page['title'] = "Home";
			$add_page['url'] = "home";
			$add_page['content_type'] = "page";
			$add_page['subtype'] = 'static';
			$add_page['is_shop'] = 'n';
			//$add_page['debug'] = 1;
			$add_page['is_home'] = 'y';
			$add_page['active_site_template'] = 'default';
			$new_shop = save_content($add_page);
		}

		break;

		default :
		break;
	}
}


api_expose('content_set_published');
function content_set_published($params){
	if(!isset($params['id'])){
	return array('error' => 'You must provide id parameter!');
	} else {
		if(is_admin() == false){
		return array('error' => 'You must be admin!');

		} else {
			$save = array();
			$save['id'] = intval($params['id']);
			$save['is_active'] = 'y';
			$save_data = save_content($save);
			return ($save_data);
		}

	}

}
api_expose('content_set_unpublished');

function content_set_unpublished($params){
	if(!isset($params['id'])){
	return array('error' => 'You must provide id parameter!');
	} else {
		if(is_admin() == false){
		return array('error' => 'You must be admin!');

		} else {
			$save = array();
			$save['id'] = intval($params['id']);
			$save['is_active'] = 'n';
			$save_data = save_content($save);
			return ($save_data);
		}

	}

}

function get_content_parents($id = 0, $without_main_parrent = false, $data_type = 'category') {

	if (intval($id) == 0) {

		return FALSE;
	}

	$table =MW_DB_TABLE_CONTENT;

	$ids = array();

	$data = array();

	if (isset($without_main_parrent) and $without_main_parrent == true) {

		$with_main_parrent_q = " and parent<>0 ";
	} else {

		$with_main_parrent_q = false;
	}
	$id = intval($id);
	$q = " select id, parent  from $table where id = $id   $with_main_parrent_q ";

	$taxonomies = db_query($q, $cache_id = __FUNCTION__ . crc32($q), $cache_group = 'content/' . $id);

	//var_dump($q);
	//  var_dump($taxonomies);
	//  exit;

	if (!empty($taxonomies)) {

		foreach ($taxonomies as $item) {

			if (intval($item['id']) != 0) {

				$ids[] = $item['parent'];
			}
			if ($item['parent'] != $item['id'] and intval($item['parent'] != 0)) {
				$next = get_content_parents($item['parent'], $without_main_parrent);

				if (!empty($next)) {

					foreach ($next as $n) {

						if ($n != '' and $n != 0) {

							$ids[] = $n;
						}
					}
				}
			}
		}
	}

	if (!empty($ids)) {

		$ids = array_unique($ids);

		return $ids;
	} else {

		return false;
	}
}

function format_date($date, $date_format = false){
	if($date_format == false){
		 $date_format = get_option('date_format','website');
		if($date_format == false){
		$date_format = "Y-m-d H:i:s";
		}
	}

	$date =  date($date_format, strtotime($date));
	return $date;
}

function content_get_inherited_parent($content_id){



	$inherit_from = get_content_parents($content_id);

	$found = 0;
	if(!empty( $inherit_from )){
		foreach ( $inherit_from   as $value) {
			if($found  == 0){
				$par_c = get_content_by_id($value);
				if (isset($par_c['id']) and isset($par_c['active_site_template']) and isset($par_c['layout_file']) and $par_c['layout_file'] != 'inherit') {
					return $par_c['id'];
					$found = 1;
				}
			}
		}
	}

}
