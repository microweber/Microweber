<?php

if (!defined("MW_DB_TABLE_MODULES")) {
	define('MW_DB_TABLE_MODULES', MW_TABLE_PREFIX . 'modules');
}

if (!defined("MW_DB_TABLE_ELEMENTS")) {
	define('MW_DB_TABLE_ELEMENTS', MW_TABLE_PREFIX . 'elements');
}

api_expose('reorder_modules');

function reorder_modules($data) {

	$adm = is_admin();
	if ($adm == false) {
		error('Error: not logged in as admin.');
	}

	$table = MW_TABLE_PREFIX . 'modules';
	foreach ($data as $value) {
		if (is_arr($value)) {
			$indx = array();
			$i = 0;
			foreach ($value as $value2) {
				$indx[$i] = $value2;
				$i++;
			}

			db_update_position($table, $indx);
			return true;
			// d($indx);
		}
	}
}

action_hook('mw_db_init_modules', 'mw_db_init_modules_table');

function mw_db_init_modules_table() {
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

	$table_name = MW_DB_TABLE_MODULES;
	$table_name2 = MW_DB_TABLE_ELEMENTS;

	$fields_to_add = array();

	$fields_to_add[] = array('updated_on', 'datetime default NULL');
	$fields_to_add[] = array('created_on', 'datetime default NULL');
	$fields_to_add[] = array('expires_on', 'datetime default NULL');

	$fields_to_add[] = array('created_by', 'int(11) default NULL');

	$fields_to_add[] = array('edited_by', 'int(11) default NULL');

	$fields_to_add[] = array('name', 'TEXT default NULL');
	$fields_to_add[] = array('parent_id', 'int(11) default NULL');
	$fields_to_add[] = array('module_id', 'TEXT default NULL');

	$fields_to_add[] = array('module', 'TEXT default NULL');
	$fields_to_add[] = array('description', 'TEXT default NULL');
	$fields_to_add[] = array('icon', 'TEXT default NULL');
	$fields_to_add[] = array('author', 'TEXT default NULL');
	$fields_to_add[] = array('website', 'TEXT default NULL');
	$fields_to_add[] = array('help', 'TEXT default NULL');

	$fields_to_add[] = array('installed', 'int(11) default NULL');
	$fields_to_add[] = array('ui', 'int(11) default 0');
	$fields_to_add[] = array('position', 'int(11) default NULL');
	$fields_to_add[] = array('as_element', 'int(11) default 0');
	$fields_to_add[] = array('ui_admin', 'int(11) default 0');
	$fields_to_add[] = array('notifications', 'int(11) default 0');

	set_db_table($table_name, $fields_to_add);

	db_add_table_index('module', $table_name, array('module(255)'));
	db_add_table_index('module_id', $table_name, array('module_id(255)'));

	set_db_table($table_name2, $fields_to_add);

	db_add_table_index('module', $table_name2, array('module(255)'));
	db_add_table_index('module_id', $table_name2, array('module_id(255)'));

	cache_store_data(true, $function_cache_id, $cache_group = 'db');
	// $fields = (array_change_key_case ( $fields, CASE_LOWER ));
	return true;

	//print '<li'.$cls.'><a href="'.admin_url().'view:settings">newsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl etenewsl eter</a></li>';
}

/**
 *
 * Modules functions API
 *
 * @package		modules
 * @since		Version 0.1
 */

// ------------------------------------------------------------------------

define("EMPTY_MOD_STR", "<div class='mw-empty-module '>{module_title} {type}</div>");

/**
 * module_templates
 *
 * Gets all templates for a module
 *
 * @package		modules
 * @subpackage	functions
 * @category	modules api
 */
function module_templates($module_name, $template_name = false) {

	$module_name_l = locate_module($module_name);

	$module_name_l = dirname($module_name_l) . DS . 'templates' . DS;
	if (!is_dir($module_name_l)) {
		return false;
	} else {
		if ($template_name == false) {
			$options = array();
			$options['no_cache'] = 1;
			$options['path'] = $module_name_l;
			$module_name_l = layouts_list($options);
			return $module_name_l;
		} else {
			$tf = $module_name_l . $template_name;
			if (is_file($tf)) {
				return $tf;
			} else {
				return false;
			}
		}

		// d($module_name_l);
	}
}

function module_name_decode($module_name) {
	$module_name = str_replace('__', '/', $module_name);
	return $module_name;
	//$module_name = str_replace('%20', '___', $module_name);

	//$module_name = str_replace('/', '___', $module_name);
}

function module_name_encode($module_name) {
	$module_name = str_replace('/', '__', $module_name);
	$module_name = str_replace('\\', '__', $module_name);
	return $module_name;
	//$module_name = str_replace('%20', '___', $module_name);

	//$module_name = str_replace('/', '___', $module_name);
}

function module_data($params) {
	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $options = $params2;
	}

	$params['display'] = 'custom';

	return module($params);

}

function module($params) {

	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $options = $params2;
	}
	$tags = '';
	$em = EMPTY_MOD_STR;
	foreach ($params as $k => $v) {

		if ($k == 'type') {
			$module_name = $v;
		}

		if ($k == 'module') {
			$module_name = $v;
		}

		if ($k == 'data-type') {
			$module_name = $v;
		}

		if ($k != 'display') {

			if (is_array($v)) {
				$v1 = encode_var($v);
				$tags .= "{$k}=\"$v1\" ";
			} else {

				$em = str_ireplace("{" . $k . "}", $v, $em);

				$tags .= "{$k}=\"$v\" ";
			}
		}
	}

	//$tags = "<div class='module' {$tags} data-type='{$module_name}'  data-view='empty'>" . $em . "</div>";

	$res = load_module($module_name, $params);
	if (is_array($res)) {
		// $res['edit'] = $tags;
	}

	if (isset($params['wrap']) or isset($params['data-wrap'])) {
		$module_cl = module_css_class($module_name);
		$res = "<div class='module {$module_cl}' {$tags} data-type='{$module_name}'>" . $res . "</div>";
	}

	return $res;
}

function get_all_functions_files_for_modules($options = false) {
	$args = func_get_args();
	$function_cache_id = '';

	$function_cache_id = serialize($options);

	$cache_id = $function_cache_id = __FUNCTION__ . crc32($function_cache_id);

	$cache_group = 'modules/functions';

	$cache_content = cache_get_content($cache_id, $cache_group);

	if (($cache_content) != false) {

		return $cache_content;
	}

	//d($uninstall_lock);
	if (isset($options['glob'])) {
		$glob_patern = $options['glob'];
	} else {
		$glob_patern = '*functions.php';
	}

	if (isset($options['dir_name'])) {
		$dir_name = $options['dir_name'];
	} else {
		$dir_name = normalize_path(MODULES_DIR);
	}

	$disabled_files = array();

	$uninstall_lock = get_modules_from_db('ui=any&installed=[int]0');

	if (is_array($uninstall_lock) and !empty($uninstall_lock)) {
		foreach ($uninstall_lock as $value) {
			$value1 = normalize_path($dir_name . $value['module'] . DS . 'functions.php', false);
			$disabled_files[] = $value1;
		}
	}

	$dir = rglob($glob_patern, 0, $dir_name);

	if (!empty($dir)) {
		$configs = array();
		foreach ($dir as $key => $value) {

			if (is_string($value)) {
				$value = normalize_path($value, false);

				$found = false;
				foreach ($disabled_files as $disabled_file) {
					//d($disabled_file);
					if (strtolower($value) == strtolower($disabled_file)) {
						$found = 1;
					}
				}
				if ($found == false) {
					$configs[] = $value;
				}
			}
			//d($value);
			//if ($disabled_files !== null and !in_array($value, $disabled_files,1)) {
			//
			//}
		}

		cache_save($configs, $function_cache_id, $cache_group);

		return $configs;
	} else {
		return false;
	}
}

function get_elements_from_db($params = false) {

	$table = MW_TABLE_PREFIX . 'elements';
	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $options = $params2;
	}
	$params['table'] = $table;
	$params['orderby'] = 'position asc';

	$params['cache_group'] = 'elements/global';
	if (isset($params['id'])) {
		$params['limit'] = 1;
	} else {
		$params['limit'] = 1000;
	}

	if (!isset($params['ui'])) {
		//   $params['ui'] = 1;
	}
	$s = get($params);
	// d($params); d( $s);
	return $s;
}

function get_modules_from_db($params = false) {

	$table = MW_TABLE_PREFIX . 'modules';
	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $options = $params2;
	}
	$params['table'] = $table;

	$params['orderby'] = 'position asc';
	$params['cache_group'] = 'modules/global';
	if (isset($params['id'])) {
		$params['limit'] = 1;
	} else {
		$params['limit'] = 1000;
	}

	if (!isset($params['ui'])) {
		$params['ui'] = 1;
	}

	if (isset($params['ui']) and $params['ui'] == 'any') {
		// d($params);
		unset($params['ui']);
	}

	return get($params);
}

api_expose('save_settings_el');

function save_settings_el($data_to_save) {
	return save_element_to_db($data_to_save);
}

api_expose('save_settings_md');

function save_settings_md($data_to_save) {
	return save_module_to_db($data_to_save);
}

function save_element_to_db($data_to_save) {

	if (is_admin() == false) {
		return false;
	}
	if (isset($data_to_save['is_element']) and $data_to_save['is_element'] == true) {
		exit(d($data_to_save));
	}

	$table = MW_TABLE_PREFIX . 'elements';
	$save = false;
	// d($table);
	//d($data_to_save);

	if (!empty($data_to_save)) {
		$s = $data_to_save;
		// $s["module_name"] = $data_to_save["name"];
		// $s["module_name"] = $data_to_save["name"];
		if (!isset($s["parent_id"])) {
			$s["parent_id"] = 0;
		}
		if (!isset($s["id"]) and isset($s["module"])) {
			$s["module"] = $data_to_save["module"];
			if (!isset($s["module_id"])) {
				$save = get_elements_from_db('limit=1&module=' . $s["module"]);
				if ($save != false and isset($save[0]) and is_array($save[0])) {
					$s["id"] = $save[0]["id"];
				} else {
					$save = save_data($table, $s);
				}
			}
		} else {
			$save = save_data($table, $s);
		}

		//
		//d($s);
	}

	if ($save != false) {

		cache_clean_group('elements' . DIRECTORY_SEPARATOR . '');
		cache_clean_group('elements' . DIRECTORY_SEPARATOR . 'global');
	}
	return $save;
}

function delete_elements_from_db() {
	if (is_admin() == false) {
		return false;
	} else {

		$table = MW_TABLE_PREFIX . 'elements';

		$table_taxonomy = MW_TABLE_PREFIX . 'taxonomy';
		$table_taxonomy_items = MW_TABLE_PREFIX . 'taxonomy_items';

		$q = "delete from $table ";
		//   d($q);
		db_q($q);

		$q = "delete from $table_taxonomy where to_table='table_elements' and data_type='category' ";
		// d($q);
		db_q($q);

		$q = "delete from $table_taxonomy_items where to_table='table_elements' and data_type='category_item' ";
		// d($q);
		db_q($q);
		cache_clean_group('taxonomy' . DIRECTORY_SEPARATOR . '');
		cache_clean_group('taxonomy_items' . DIRECTORY_SEPARATOR . '');

		cache_clean_group('elements' . DIRECTORY_SEPARATOR . '');
	}
}

function delete_module_by_id($id) {
	if (is_admin() == false) {
		return false;
	}
	$id = intval($id);

	$table = MW_TABLE_PREFIX . 'modules';
	$table_taxonomy = MW_TABLE_PREFIX . 'taxonomy';
	$table_taxonomy_items = MW_TABLE_PREFIX . 'taxonomy_items';

	$q = "delete from $table where id={$id}";
	db_q($q);

	$q = "delete from $table_taxonomy_items where to_table='table_modules' and data_type='category_item' and to_table_id={$id}";
	db_q($q);
	cache_clean_group('taxonomy' . DIRECTORY_SEPARATOR . '');
	// cache_clean_group('taxonomy_items' . DIRECTORY_SEPARATOR . '');

	cache_clean_group('modules' . DIRECTORY_SEPARATOR . '');
}

function delete_modules_from_db() {
	if (is_admin() == false) {
		return false;
	} else {

		$table = MW_TABLE_PREFIX . 'modules';
		$table_taxonomy = MW_TABLE_PREFIX . 'taxonomy';
		$table_taxonomy_items = MW_TABLE_PREFIX . 'taxonomy_items';

		$q = "delete from $table ";
		db_q($q);

		$q = "delete from $table_taxonomy where to_table='table_modules' and data_type='category' ";
		db_q($q);

		$q = "delete from $table_taxonomy_items where to_table='table_modules' and data_type='category_item' ";
		db_q($q);
		cache_clean_group('taxonomy' . DIRECTORY_SEPARATOR . '');
		cache_clean_group('taxonomy_items' . DIRECTORY_SEPARATOR . '');

		cache_clean_group('modules' . DIRECTORY_SEPARATOR . '');
	}
}

function is_module_installed($module_name) {

	$module_name = trim($module_name);

	$module_namei = $module_name;
	if (strstr($module_name, 'admin')) {

		$module_namei = str_ireplace('\\admin', '', $module_namei);
		$module_namei = str_ireplace('/admin', '', $module_namei);
	}

	//$module_namei = str_ireplace($search, $replace, $subject)e

	$uninstall_lock = get_modules_from_db('one=1&ui=any&module=' . $module_namei);

	if (isset($uninstall_lock["installed"]) and $uninstall_lock["installed"] != '' and intval($uninstall_lock["installed"]) != 1) {
		return false;
	} else {
		return true;
	}
}

function is_module($module_name) {
	if (!is_string($module_name)) {
		return false;
	}
	if (trim($module_name) == '') {
		return false;
	}
	$checked = array();

	if (!isset($checked[$module_name])) {
		$ch = locate_module($module_name, $custom_view = false);
		if ($ch != false) {
			$checked[$module_name] = true;
		} else {
			$checked[$module_name] = false;
		}
	}

	return $checked[$module_name];
}

function module_dir($module_name) {
	if (!is_string($module_name)) {
		return false;
	}

	$args = func_get_args();
	$function_cache_id = '';
	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	}

	$cache_id = $function_cache_id = __FUNCTION__ . crc32($function_cache_id);

	$cache_group = 'modules/global';

	$cache_content = cache_get_content($cache_id, $cache_group);

	if (($cache_content) != false) {

		return $cache_content;
	}

	$checked = array();

	if (!isset($checked[$module_name])) {
		$ch = locate_module($module_name, $custom_view = false);

		if ($ch != false) {
			$ch = dirname($ch);
			//$ch = dir2url($ch);
			$ch = normalize_path($ch, 1);
			//	$ch = trim($ch,'\//');

			$checked[$module_name] = $ch;
		} else {
			$checked[$module_name] = false;
		}
	}

	cache_save($checked[$module_name], $function_cache_id, $cache_group);

	return $checked[$module_name];

}

function module_url($module_name) {
	if (!is_string($module_name)) {
		return false;
	}

	$args = func_get_args();
	$function_cache_id = '';
	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	}

	$cache_id = $function_cache_id = __FUNCTION__ . crc32($function_cache_id);

	$cache_group = 'modules/global';

	$cache_content = cache_get_content($cache_id, $cache_group);

	if (($cache_content) != false) {

		return $cache_content;
	}

	$checked = array();

	if (!isset($checked[$module_name])) {
		$ch = locate_module($module_name, $custom_view = false);

		if ($ch != false) {
			$ch = dirname($ch);
			$ch = dir2url($ch);
			$ch = $ch . '/';
			//	$ch = trim($ch,'\//');

			$checked[$module_name] = $ch;
		} else {
			$checked[$module_name] = false;
		}
	}

	cache_save($checked[$module_name], $function_cache_id, $cache_group);

	return $checked[$module_name];

}

function locate_module($module_name, $custom_view = false, $no_fallback_to_view = false) {

	if (!defined("ACTIVE_TEMPLATE_DIR")) {
		define_constants();
	}

	$module_name = trim($module_name);
	$module_name = str_replace('\\', '/', $module_name);
	$module_name = str_replace('..', '', $module_name);
	// prevent hack of the directory
	$module_name = reduce_double_slashes($module_name);

	$module_in_template_dir = ACTIVE_TEMPLATE_DIR . 'modules/' . $module_name . '';

	$module_in_template_dir = normalize_path($module_in_template_dir, 1);
	$module_in_template_file = ACTIVE_TEMPLATE_DIR . 'modules/' . $module_name . '.php';
	$module_in_template_file = normalize_path($module_in_template_file, false);
	//d($module_in_template_dir);
	$module_in_default_file12 = MODULES_DIR . $module_name . '.php';

	$try_file1 = false;

	if (is_dir($module_in_template_dir)) {
		$mod_d = $module_in_template_dir;
		$mod_d1 = normalize_path($mod_d, 1);
		$try_file1 = $mod_d1 . 'index.php';
	} elseif (is_file($module_in_template_file)) {
		$try_file1 = $module_in_template_file;
		//d($try_file1);
	} elseif (is_file($module_in_default_file12) and $custom_view == false) {
		$try_file1 = $module_in_default_file12;
		//	d($try_file1);
	} else {

		$module_in_default_dir = MODULES_DIR . $module_name . '';
		$module_in_default_dir = normalize_path($module_in_default_dir, 1);
		//  d($module_in_default_dir);
		$module_in_default_file = MODULES_DIR . $module_name . '.php';
		$module_in_default_file_custom_view = MODULES_DIR . $module_name . '_' . $custom_view . '.php';

		$element_in_default_file = ELEMENTS_DIR . $module_name . '.php';
		$element_in_default_file = normalize_path($element_in_default_file, false);

		//
		$module_in_default_file = normalize_path($module_in_default_file, false);

		if (is_file($module_in_default_file)) {

			if ($custom_view == true and is_file($module_in_default_file_custom_view)) {
				$try_file1 = $module_in_default_file_custom_view;
				if ($no_fallback_to_view == true) {
					return $try_file1;
				}

			} else {

				//  $try_file1 = $module_in_default_file;
			}

		} else {
			if (is_dir($module_in_default_dir)) {

				$mod_d1 = normalize_path($module_in_default_dir, 1);

				if ($custom_view == true) {

					$try_file1 = $mod_d1 . trim($custom_view) . '.php';
					if ($no_fallback_to_view == true) {
						return $try_file1;
					}
				} else {
					if ($no_fallback_to_view == true) {
						return false;
					}

					//temp
					$try_file1 = $mod_d1 . 'index.php';
				}
			} elseif (is_file($element_in_default_file)) {

				$is_element = true;

				$try_file1 = $element_in_default_file;
			}
		}
	}

	$try_file1 = normalize_path($try_file1, false);
	return $try_file1;
}

api_expose('uninstall_module');

function uninstall_module($params) {

	if (is_admin() == false) {
		return false;
	}
	if (isset($params['id'])) {
		$id = intval($params['id']);
		$this_module = get_modules_from_db('ui=any&one=1&id=' . $id);
		if ($this_module != false and is_array($this_module) and isset($this_module['id'])) {
			$module_name = $this_module['module'];
			if (is_admin() == false) {
				return false;
			}

			if (trim($module_name) == '') {
				return false;
			}
			//            $uninstall_lock = DBPATH_FULL . 'disabled_modules' . DS;
			//            if (!is_dir($uninstall_lock)) {
			//                mkdir_recursive($uninstall_lock);
			//            }
			//            $unistall_file = url_title($module_name);
			//            $unistall_file = $uninstall_lock . $unistall_file . '.php';
			//            touch($unistall_file);
			//  d($unistall_file);
			$loc_of_config = locate_module($module_name, 'config');
			$res = array();
			$loc_of_functions = locate_module($module_name, 'functions');
			$cfg = false;
			if (is_file($loc_of_config)) {
				include ($loc_of_config);
				if (isset($config)) {
					$cfg = $config;
				}

				if (is_array($cfg) and !empty($cfg)) {

					if (isset($cfg['on_uninstall'])) {

						$func = $cfg['on_uninstall'];

						if (!function_exists($func)) {
							if (is_file($loc_of_functions)) {
								include_once ($loc_of_functions);
							}
						}

						if (function_exists($func)) {

							$res = $func();
							// return $res;
						}
					} else {
						//  return true;
					}
				}

				// d($loc_of_config);
			}
			$to_save = array();
			$to_save['id'] = $id;
			$to_save['installed'] = '0';
			//  $to_save['keep_cache'] = '1';
			//   $to_save['module'] = $module_name;

			//d($to_save);
			save_module_to_db($to_save);
			// delete_module_by_id($id);
		}
	}
	cache_clean_group('modules' . DIRECTORY_SEPARATOR . '');

	// d($params);
}

api_expose('install_module');

function install_module($params) {

	if (is_admin() == false) {
		return false;
	}

	if (isset($params['for_module'])) {
		$module_name = $params['for_module'];

		if (trim($module_name) == '') {
			return false;
		}
	}

	if (isset($params['module'])) {
		$module_name = $params['module'];

		if (trim($module_name) == '') {
			return false;
		}
	}

	$loc_of_config = locate_module($module_name, 'config', 1);
	//d($loc_of_config);
	$res = array();
	$loc_of_functions = locate_module($module_name, 'functions', 1);
	$cfg = false;
	if ($loc_of_config != false and is_file($loc_of_config)) {
		include ($loc_of_config);
		if (isset($config)) {
			$cfg = $config;
		}

	}

	//    $uninstall_lock = DBPATH_FULL . 'disabled_modules' . DS;
	//    if (!is_dir($uninstall_lock)) {
	//        mkdir_recursive($uninstall_lock);
	//    }
	//    $unistall_file = url_title($module_name);
	//    $unistall_file = $uninstall_lock . $unistall_file . '.php';
	//    // d($unistall_file);
	//    if (is_file($unistall_file)) {
	//        unlink($unistall_file);
	//    }

	$this_module = get_modules_from_db('no_cache=1&ui=any&one=1&module=' . $module_name);
	//d($this_module);
	if ($this_module != false and is_array($this_module) and isset($this_module['id'])) {
		$to_save = array();
		$to_save['id'] = $this_module['id'];
		if (isset($params['installed']) and $params['installed'] == 'auto') {
			if (isset($this_module['installed']) and $this_module['installed'] == '') {
				$to_save['installed'] = '1';
			} else if (isset($this_module['installed']) and $this_module['installed'] != '') {
				$to_save['installed'] = $this_module['installed'];
			}

		} else {
			$to_save['installed'] = '1';

		}
		if ($to_save['installed'] == '1') {
			if (isset($config)) {
				if (isset($config['tables']) and is_arr($config['tables'])) {
					$tabl = $config['tables'];
					foreach ($tabl as $key => $value) {
						$table = db_get_real_table_name($key);
						set_db_table($table, $fields_to_add);
					}
				}
				if (is_array($config) and !empty($config)) {

					if (isset($config['on_install'])) {

						$func = $config['on_install'];

						if (!function_exists($func)) {
							if (is_file($loc_of_functions)) {
								include_once ($loc_of_functions);
							}
						}

						if (function_exists($func)) {

							$res = $func();
							//	return $res;
						}
					} else {
						//return true;
					}
				}
				if (isset($config['options']) and is_arr($config['options'])) {
					$changes = false;
					$tabl = $config['options'];
					foreach ($tabl as $key => $value) {
						//$table = db_get_real_table_name($key);
						//d($value);
						$value['module'] = $module_name;
						$ch = set_default_option($value);
						//	d($ch);
						if ($ch == true) {
							$changes = true;
						}
					}

					if ($changes == true) {

						cache_clean_group('options/global');
					}
				}

				//
			}
		}
		$to_save['keep_cache'] = '1';
		//   $to_save['module'] = $module_name;

		save_module_to_db($to_save);
	}

	// d($loc_of_functions);
}

function save_module_to_db($data_to_save) {

	if (is_admin() == false) {
		return false;
	}
	if (isset($data_to_save['is_element']) and $data_to_save['is_element'] == true) {
		exit(d($data_to_save));
	}

	$table = MW_TABLE_PREFIX . 'modules';
	$save = false;
	// d($table);

	//d($data_to_save);

	if (!empty($data_to_save)) {
		$s = $data_to_save;
		// $s["module_name"] = $data_to_save["name"];
		// $s["debug"] = 1;
		if (!isset($s["parent_id"])) {
			$s["parent_id"] = 0;
		}
		if (!isset($s["id"]) and isset($s["module"])) {
			$s["module"] = $data_to_save["module"];
			if (!isset($s["module_id"])) {
				$save = get_modules_from_db('no_cache=1&ui=any&limit=1&module=' . $s["module"]);
				//  d($s);
				//
				if ($save != false and isset($save[0]) and is_array($save[0])) {
					$s["id"] = $save[0]["id"];

					$save = save_data($table, $s);
				} else {
					$save = save_data($table, $s);
				}
			}
		} else {
			$save = save_data($table, $s);
		}

		//
		//d($s);
	}
	cache_clean_group('modules' . DIRECTORY_SEPARATOR . 'functions');
	if (!isset($data_to_save['keep_cache'])) {
		if ($save != false) {
			//   cache_clean_group('modules' . DIRECTORY_SEPARATOR . intval($save));
			// cache_clean_group('modules' . DIRECTORY_SEPARATOR . 'global');
			//cache_clean_group('modules' . DIRECTORY_SEPARATOR . '');
		}
	}
	return $save;
}

function modules_list($options = false) {

	return scan_for_modules($options);
}

action_hook('mw_scan_for_modules', 'scan_for_modules');
function scan_for_modules($options = false) {
	ini_set("memory_limit", "160M");
	if (!ini_get('safe_mode')) {
		set_time_limit(250);
	}
	$params = $options;
	if (is_string($params)) {
		$params = parse_str($params, $params2);
		$params = $options = $params2;
	}

	$args = func_get_args();
	$function_cache_id = '';
	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($k) . serialize($v) . serialize($params);
	}
	$list_as_element = false;
	$cache_id = $function_cache_id = __FUNCTION__ . crc32($function_cache_id);
	if (isset($options['dir_name'])) {
		$dir_name = $options['dir_name'];
		//$list_as_element = true;
		$cache_group = 'elements/global';
		//
	} else {
		$dir_name = normalize_path(MODULES_DIR);
		$list_as_element = false;
		$cache_group = 'modules/global';
	}

	if (isset($options['is_elements'])) {
		$list_as_element = true;

	}

	if (isset($options['cache_group'])) {
		$cache_group = $cache_group;
	}

	if (isset($options['reload_modules']) == true) {
		//
	}

	if (isset($options['cleanup_db']) == true) {

		if (is_admin() == true) {
			if ($cache_group == 'modules') {
				//	delete_modules_from_db();
			}

			if ($cache_group == 'elements') {
				//	delete_elements_from_db();
			}

			cache_clean_group('taxonomy');
			cache_clean_group('taxonomy_items');
		}
	}

	if (isset($options['skip_cache']) == false) {

		$cache_content = cache_get_content($cache_id, $cache_group);

		if (($cache_content) != false) {

			return $cache_content;
		}
	}
	if (isset($options['glob'])) {
		$glob_patern = $options['glob'];
	} else {
		$glob_patern = '*config.php';
	}

	//clearcache();
	//clearstatcache();
	$modules_remove_old = false;
	$dir = rglob($glob_patern, 0, $dir_name);
	$dir_name_mods = MODULES_DIR;
	$dir_name_mods2 = ELEMENTS_DIR;

	if (!empty($dir)) {
		$configs = array();
		foreach ($dir as $key => $value) {
			$skip_module = false;
			if (isset($options['skip_admin']) and $options['skip_admin'] == true) {
				// p($value);
				if (strstr($value, 'admin')) {
					$skip_module = true;
				}
			}

			if ($skip_module == false) {

				$config = array();
				$value = normalize_path($value, false);
				//
				$value_fn = $mod_name = str_replace('_config.php', '', $value);
				$value_fn = $mod_name = str_replace('config.php', '', $value_fn);
				$value_fn = $mod_name = str_replace('index.php', '', $value_fn);

				//  d( $value_fn);

				$value_fn = $mod_name_dir = str_replace($dir_name_mods, '', $value_fn);
				$value_fn = $mod_name_dir = str_replace($dir_name_mods2, '', $value_fn);

				//d( $value_fn);
				//  $value_fn = reduce_double_slashes($value_fn);

				$def_icon = MODULES_DIR . 'default.png';

				ob_start();

				include ($value);

				$content = ob_get_contents();
				ob_end_clean();

				$value_fn = rtrim($value_fn, '\\');
				$value_fn = rtrim($value_fn, '/');
				$value_fn = str_replace('\\', '/', $value_fn);
				$config['module'] = $value_fn . '';
				$config['module'] = rtrim($config['module'], '\\');
				$config['module'] = rtrim($config['module'], '/');

				$config['module_base'] = str_replace('admin/', '', $value_fn);
				if (is_dir($mod_name)) {
					$t1 = ($mod_name) . $value_fn;

					$try_icon = $t1 . '.png';

				} else {
					$try_icon = $mod_name . '.png';
				}
				$try_icon = normalize_path($try_icon, false);
				if (is_file($try_icon)) {

					$config['icon'] = pathToURL($try_icon);
				} else {
					$config['icon'] = pathToURL($def_icon);
				}
				//   $config ['installed'] = install_module($config ['module']);
				// $mmd5 = url_title($config ['module']);
				//   $check_if_uninstalled = MODULES_DIR . '_system/' . $mmd5 . '.php';
				//                if (is_file($check_if_uninstalled)) {
				//                    $config ['uninstalled'] = true;
				//                    $config ['installed'] = false;
				//                } else {
				//                    $config ['uninstalled'] = false;
				//                    $config ['installed'] = true;
				//                }
				//                if (isset($options ['ui']) and $options ['ui'] == true) {
				//                    if ($config ['ui'] == false) {
				//                       // $skip_module = true;
				//                        $config ['ui'] = 0;
				//                    }
				//                }

				if ($skip_module == false) {
					if (trim($config['module']) != '') {

						$configs[] = $config;

						if ($list_as_element == true) {

							save_element_to_db($config);
						} else {
							//d($config);
							//if (isset($options['dir_name'])) {
							save_module_to_db($config);
							$modules_remove_old = true;
							$config['installed'] = 'auto';
							install_module($config);
							//}
						}
					}
				}
			}
		}
		$cfg_ordered = array();
		$cfg_ordered2 = array();
		$cfg = $configs;
		foreach ($cfg as $k => $item) {
			if (isset($item['position'])) {
				$cfg_ordered2[$item['position']][] = $item;
				unset($cfg[$k]);
			}
		}
		ksort($cfg_ordered2);
		foreach ($cfg_ordered2 as $k => $item) {
			foreach ($item as $ite) {
				$cfg_ordered[] = $ite;
			}
		}

		if ($modules_remove_old == true) {

			$table = MW_DB_TABLE_OPTIONS;
			$uninstall_lock = get_modules_from_db('ui=any');
			if (!empty($uninstall_lock)) {
				foreach ($uninstall_lock as $value) {
					$ism = is_module($value['module']);
					if ($ism == false) {
						delete_module_by_id($value['id']);
						$mn = $value['module'];
						$q = "delete from $table where option_group='{$mn}'  ";

						db_q($q);
					}
					//	d($ism);
				}
			}
		}

		$c2 = array_merge($cfg_ordered, $cfg);

		cache_save($c2, $cache_id, $cache_group);

		return $c2;
	}
}

action_hook('mw_scan_for_modules', 'get_elements');

function get_elements($options = array()) {

	if (is_string($options)) {
		$params = parse_str($options, $params2);
		$options = $params2;
	}

	$options['is_elements'] = 1;
	$options['dir_name'] = normalize_path(ELEMENTS_DIR);

	return scan_for_modules($options);

	$args = func_get_args();
	$function_cache_id = '';
	foreach ($args as $k => $v) {

		$function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	}

	$cache_id = $function_cache_id = __FUNCTION__ . crc32($function_cache_id);

	$cache_group = 'elements';

	$cache_content = cache_get_content($cache_id, $cache_group);

	if (($cache_content) != false) {

		return $cache_content;
	}

	$dir_name = normalize_path(ELEMENTS_DIR);
	$a1 = array();
	$dirs = array_filter(glob($dir_name . '*'), 'is_dir');
	foreach ($dirs as $dir) {
		$a1[] = basename($dir);
	}
	cache_save($a1, $function_cache_id, $cache_group);

	return $a1;
}

function load_all_lic() {
	$h = site_hostname();
	$cache_id = __FUNCTION__ . $h;
	$cache_group = 'updates';

	$cache_content = cache_get_content($cache_id, $cache_group);

	if (($cache_content) != false) {

		return $cache_content;
	}

	static $u1;
	if ($u1 == false) {
		//  $dir_name = DBPATH_FULL . 'lic' . DS;

		$dir_name = DBPATH_FULL . 'lic' . DS . $h . DS;
		$glob_patern = '*.php';
		if (is_dir($dir_name)) {
			$dir = rglob($glob_patern, 0, $dir_name);

			if (!empty($dir)) {
				$lic_files = array();
				foreach ($dir as $key => $value) {
					$lic_files[] = normalize_path($value, false);
				}
			}
		}

		//  $u1 = $module_name;
		// return $u1;
		if (isset($lic_files)) {
			$u1 = $lic_files;
		}
	}
	cache_save($u1, $cache_id, $cache_group);

	return $u1;
}

function load_module_lic($module_name = false) {
	static $u1;

	if ($u1 == false) {
		$u1 = array();
	}

	if (isset($u1[$module_name]) == false) {
		$all_lic = load_all_lic();
		$h = site_hostname();
		$dir_name = DBPATH_FULL . 'lic' . DS . $h . DS;
		$m_name = $dir_name . $module_name;
		$m_name = normalize_path($m_name, false);
		$m_name = $m_name . '.php';
		if (isset($all_lic) and is_array($all_lic)) {
			foreach ($all_lic as $itemz) {
				if ($itemz == $m_name) {
					//if (is_file($itemz)) {
					$lic = file_get_contents($m_name);
					$search = CACHE_CONTENT_PREPEND;

					$replace = '';

					$count = 1;

					$cache = str_replace($search, $replace, $lic, $count);
					$lic = decrypt_var($cache, $h);
					if ($lic == false) {
						$u1[$module_name] = array("error" => "no_license_found", "module" => $module_name, "license_decoding_error" => $module_name);
					} else {
						$u1[$module_name] = $lic;
					}
					// }
				}
			}
		}
		//$u1 = $module_name;
		// d($all_lic);
		// return $u1;
	}
	if (isset($u1[$module_name]) != false) {

		return $u1[$module_name];
	}
	return false;
}

function load_module($module_name, $attrs = array()) {
	$function_cache_id = false;
	//if (defined('PAGE_ID') == false) {
	// define_constants();
	// }
	//    $args = func_get_args();
	//    foreach ($args as $k => $v) {
	//        $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
	//    }
	//    $function_cache_id = __FUNCTION__ . crc32($function_cache_id);
	//
	//    d($function_cache_id);
	//    $cache_content = 'CACHE_LOAD_MODULE_' . $function_cache_id;
	// if (!defined($cache_content)) {
	//
	// } else {
	//
	// // p((constant($cache_content)));
	// return (constant($cache_content));
	// }

	//d($uninstall_lock);

	$is_element = false;
	$custom_view = false;
	if (isset($attrs['view'])) {

		$custom_view = $attrs['view'];
		$custom_view = trim($custom_view);
		$custom_view = str_replace('\\', '/', $custom_view);
		$custom_view = str_replace('..', '', $custom_view);
	}

	if ($custom_view != false and strtolower($custom_view) == 'admin') {
		if (is_admin() == false) {
			error('Not logged in as admin');
		}
	}
	// $CI = get_instance();
	$module_name = trim($module_name);
	$module_name = str_replace('\\', '/', $module_name);
	$module_name = str_replace('..', '', $module_name);
	// prevent hack of the directory
	$module_name = reduce_double_slashes($module_name);

	$module_namei = $module_name;
	if (strstr($module_name, 'admin')) {

		$module_namei = str_ireplace('\\admin', '', $module_namei);
		$module_namei = str_ireplace('/admin', '', $module_namei);
	}

	//$module_namei = str_ireplace($search, $replace, $subject)e

	$uninstall_lock = get_modules_from_db('one=1&&ui=any&module=' . $module_namei);

	if (isset($uninstall_lock["installed"]) and $uninstall_lock["installed"] != '' and intval($uninstall_lock["installed"]) != 1) {
		return '';
	}

	if (!defined('ACTIVE_TEMPLATE_DIR')) {
		define_constants();
	}

	$module_in_template_dir = ACTIVE_TEMPLATE_DIR . 'modules/' . $module_name . '';
	$module_in_template_dir = normalize_path($module_in_template_dir, 1);
	$module_in_template_file = ACTIVE_TEMPLATE_DIR . 'modules/' . $module_name . '.php';
	$module_in_template_file = normalize_path($module_in_template_file, false);

	$try_file1 = false;

	if (is_dir($module_in_template_dir)) {
		$mod_d = $module_in_template_dir;
		$mod_d1 = normalize_path($mod_d, 1);
		$try_file1 = $mod_d1 . 'index.php';
	} elseif (is_file($module_in_template_file)) {
		$try_file1 = $module_in_template_file;
	} else {

		$module_in_default_dir = MODULES_DIR . $module_name . '';
		$module_in_default_dir = normalize_path($module_in_default_dir, 1);
		// d($module_in_default_dir);
		$module_in_default_file = MODULES_DIR . $module_name . '.php';
		$module_in_default_file_custom_view = MODULES_DIR . $module_name . '_' . $custom_view . '.php';

		$element_in_default_file = ELEMENTS_DIR . $module_name . '.php';
		$element_in_default_file = normalize_path($element_in_default_file, false);

		//
		$module_in_default_file = normalize_path($module_in_default_file, false);

		if (is_file($module_in_default_file)) {

			if ($custom_view == true and is_file($module_in_default_file_custom_view)) {
				$try_file1 = $module_in_default_file_custom_view;
			} else {

				$try_file1 = $module_in_default_file;
			}
		} else {
			if (is_dir($module_in_default_dir)) {

				$mod_d1 = normalize_path($module_in_default_dir, 1);

				if ($custom_view == true) {

					$try_file1 = $mod_d1 . trim($custom_view) . '.php';
				} else {
					$try_file1 = $mod_d1 . 'index.php';
				}
			} elseif (is_file($element_in_default_file)) {

				$is_element = true;

				$try_file1 = $element_in_default_file;
			}
		}
	}
	//

	if (isset($try_file1) != false and $try_file1 != false and is_file($try_file1)) {

		if (isset($attrs) and is_array($attrs) and !empty($attrs)) {
			$attrs2 = array();
			foreach ($attrs as $attrs_k => $attrs_v) {
				$attrs_k2 = substr($attrs_k, 0, 5);
				//d($attrs_k2);
				if (strtolower($attrs_k2) == 'data-') {
					$attrs_k21 = substr($attrs_k, 5);
					$attrs2[$attrs_k21] = $attrs_v;
					//d($attrs_k21);
				}

				$attrs2[$attrs_k] = $attrs_v;
			}
			$attrs = $attrs2;
		}

		$config['path_to_module'] = $config['mp'] = normalize_path((dirname($try_file1)) . '/', true);
		$config['the_module'] = $module_name;
		$config['module'] = $module_name;

		$config['module_name_url_safe'] = module_name_encode($module_name);

		$find_base_url = curent_url(1);
		if ($pos = strpos($find_base_url, ':' . $module_name) or $pos = strpos($find_base_url, ':' . $config['module_name_url_safe'])) {
			//	d($pos);
			$find_base_url = substr($find_base_url, 0, $pos) . ':' . $config['module_name_url_safe'];
		}
		$config['url'] = $find_base_url;

		$config['module_api'] = site_url('m/' . $module_name);
		$config['module_view'] = site_url('module/' . $module_name);
		$config['ns'] = str_replace('/', '\\', $module_name);

		$config['module_class'] = module_css_class($module_name);

		$config['url_to_module'] = pathToURL($config['path_to_module']);
		//$config['url_to_module'] = rtrim($config['url_to_module'], '///');
		$lic = load_module_lic($module_name);
		//  $lic = 'valid';
		if ($lic != false) {
			$config['license'] = $lic;
		}

		if (!isset($attrs['id'])) {

			$attrs1 = crc32(serialize($attrs));
			$s1 = url_segment(0);
			if ($s1 != false and trim($s1) != '') {
				$attrs1 = $attrs1 . '-' . $s1;
			} else if (defined('PAGE_ID') and PAGE_ID != false) {
				$attrs1 = $attrs1 . '-' . PAGE_ID;
			}

			$attrs['id'] = ($config['module_class'] . '-' . $attrs1);

		}
		if (isset($attrs['id']) and strstr($attrs['id'], '__MODULE_CLASS_NAME__')) {
			$attrs['id'] = str_replace('__MODULE_CLASS_NAME__', $config['module_class'], $attrs['id']);

			//$attrs['id'] = ('__MODULE_CLASS__' . '-' . $attrs1);
		}

		//print(file_get_contents($try_file1));
		$l1 = new MwView($try_file1);
		$l1 -> config = $config;
		if (!empty($config)) {
			foreach ($config as $key1 => $value1) {
				template_var($key1, $value1);
			}
		}

		$l1 -> params = $attrs;
		if (isset($attrs['view']) && (trim($attrs['view']) == 'empty')) {

			$module_file = EMPTY_MOD_STR;
		} elseif (isset($attrs['view']) && (trim($attrs['view']) == 'admin')) {

			$module_file = $l1 -> __toString();
		} else {

			if (isset($attrs['display']) && (trim($attrs['display']) == 'custom')) {
				$module_file = $l1 -> __get_vars();
				return $module_file;
			} else if (isset($attrs['format']) && (trim($attrs['format']) == 'json')) {
				$module_file = $l1 -> __get_vars();
				header("Content-type: application/json");
				exit(json_encode($module_file));
			} else {
				$module_file = $l1 -> __toString();
			}
		}
		$l1 = null;

		if ($lic != false and isset($lic["error"]) and ($lic["error"] == 'no_license_found')) {
			$lic_l1_try_file1 = ADMIN_VIEWS_PATH . 'activate_license.php';
			$lic_l1 = new MwView($lic_l1_try_file1);

			$lic_l1 -> config = $config;
			$lic_l1 -> params = $attrs;

			$lic_l1e_file = $lic_l1 -> __toString();

			$lic_l1 = null;
			return $lic_l1e_file . $module_file;
		}
		return $module_file;
	} else {
		//define($cache_content, FALSE);

		return false;
	}
}

function module_css_class($module_name) {
	static $defined = array();

	if (isset($defined[$module_name]) != false) {
		return $defined[$module_name];
	} else {

		$module_class = str_replace('/', '-', $module_name);
		$module_class = str_replace('\\', '-', $module_class);
		$module_class = str_replace(' ', '-', $module_class);
		$module_class = str_replace('%20', '-', $module_class);
		$module_class = str_replace('_', '-', $module_class);
		$module_class = 'module-' . $module_class;

		$defined[$module_name] = $module_class;
		return $module_class;
	}
}

action_hook('mw_cron', 'mw_cron');
function mw_cron() {
	$file_loc = CACHEDIR_ROOT . "cron" . DS;

	$some_hour = date('Ymd');
	$file_loc_hour = $file_loc . 'cron_lock' . $some_hour . '.php';
	if (is_file($file_loc_hour)) {
		return true;
	} else {

		$opts = get_options("option_key2=cronjob");
		if ($opts != false) {

			 //d($file_loc);
			if (!is_dir($file_loc)) {
				if (!mkdir($file_loc)) {
					return false;
				}
			}

			if (!defined('MW_CRON_EXEC')) {
				define('MW_CRON_EXEC', true);
			}

			foreach ($opts as $item) {
				if (isset($item['module']) and $item['module'] != '' and is_module_installed($item['module'])) {
					if (isset($item['option_value']) and $item['option_value'] != 'n') {
						$when = strtotime($item['option_value']);
						if ($when != false) {
							$when_date = date('Ymd', $when);
							$file_loc_date = $file_loc . '' . $item['option_key'] . $item['id'] . $when_date . '.php';
							if (!is_file($file_loc_date)) {
								touch($file_loc_date);
								$md = module_data('module=' . $item['module'] . '/cron');
							}
						}
					}
				}
			}
			touch($file_loc_hour);
		}
	}
}
