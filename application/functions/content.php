<?php

function define_constants($content = false) {
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

    if (isset($page) and ($page['active_site_template']) != '' and strtolower($page['active_site_template']) != 'default') {
        $the_active_site_template = $page['active_site_template'];
    } else {
        $the_active_site_template = get_option('curent_template');
    }
    $the_active_site_template_dir = normalize_path(TEMPLATEFILES . $the_active_site_template . '/');

    if (defined('ACTIVE_TEMPLATE_DIR') == false) {

        define('ACTIVE_TEMPLATE_DIR', $the_active_site_template_dir);
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
    // d($the_template_url);
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

    // d ( $the_active_site_template_dir );

    return true;
}

function get_layout_for_page($page = array()) {
    $render_file = false;
    $look_for_post = false;
    if (isset($page['content_type']) and $page['content_type'] == 'post') {
        $look_for_post = $page;
        if (isset($page['parent'])) {

            $par_page = get_content_by_id($page['parent']);
            if (isarr($par_page)) {
                $page = $par_page;
            }
        }
    }

    if (isset($page['simply_a_file'])) {

        if (is_file($page['simply_a_file']) == true) {
            $render_file = $page['simply_a_file'];
        }
    }

    if (isset($page['active_site_template']) and $render_file == false and isset($page['layout_file'])) {

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

    if ($render_file == false and ($page['layout_file']) != false) {
        $template_view = ACTIVE_TEMPLATE_DIR . DS . $page['layout_file'];
        $template_view = normalize_path($template_view, false);

        if (is_file($template_view) == true) {
            $render_file = $template_view;
        } else {

        }
    }

    return $render_file;
}

function get_homepage() {
    $table = c('db_tables');
    // ->'table_content';
    $table = $table['table_content'];

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

    $table = c('db_tables');
    // ->'table_content';
    $table = $table['table_content'];

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
        $content['id'] = ((int) $content['id']);
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




    $table = c('db_tables');
    // ->'table_content';
    $table = $table['table_content'];

    $id = intval($id);
    if ($id == 0) {
        return false;
    }



    $q = "SELECT * from $table where id='$id'  limit 0,1 ";

    $params = array();
    $params['id'] = $id;
    $params['limit'] = 1;
    $params['table'] = $table;

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

api_expose('get_content_admin');

function get_content_admin($params) {
    if (is_admin() == false) {
        return false;
    }

    return get_content($params);
}

/**
 * Function to get single content item by id from the content_table
 *
 * @return array
 * @author Peter Ivanov
 *
 */
function get_content($params) {
    $params2 = array();

    if (is_string($params)) {
        $params = parse_str($params, $params2);
        $params = $params2;
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

        $table = c('db_tables');
        $table = $table['table_content'];
        // $params['orderby'];
        if (isset($params['orderby'])) {
            $orderby = $params['orderby'];
        }
        if (isset($orderby) == false) {
            $orderby = array();
            $orderby[0] = 'created_on';

            $orderby[1] = 'DESC';
        }

        if (isset($params['limit'])) {
            $limit = $params['limit'];
        } else {
            $limit = array();
            $limit[0] = '0';

            $limit[1] = '30';
        }
        //$params['no_cache'] = 1;
        //d($params);
        $get = db_get($table, $params, $cache_group = 'content/global');
        if (isset($params['count']) or isset($params['data-count']) or isset($params['page_count']) or isset($params['data-page-count'])) {
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
            //  cache_store_data($get, $function_cache_id, $cache_group = 'content/global');

            return $get;
        } else {
            // cache_store_data('--false--', $function_cache_id, $cache_group = 'content/global');

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
        if (PAGE_ID != false and CATEGORY_ID == false) {
            $base_url = page_link(PAGE_ID);

            // p($base_url);
        } elseif (PAGE_ID != false and CATEGORY_ID != false) {
            $base_url = category_link(CATEGORY_ID);
        } else {
            if (isAjax() == false) {
                $base_url = url_string();
            } else {
                if ($_SERVER ['HTTP_REFERER'] != false) {
                    $base_url = $_SERVER ['HTTP_REFERER'];
                }
            }
            // $base_url =  full_url(true);
        }
    }

    // print $base_url;

    $page_links = array();

    $the_url = parse_url($base_url, PHP_URL_QUERY);
//$the_url = parse_url($base_url);
    $the_url = $base_url;
    //
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

        $page_links[$x] = $new_url;
    }

    for ($x = 1; $x <= count($page_links); $x++) {

        if (stristr($page_links[$x], $paging_param . ':') == false) {

            $l = reduce_double_slashes($page_links[$x] . '/' . $paging_param . ':' . $x);
            $l = str_ireplace('module/', '', $l);
            $page_links[$x] = $l;
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
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 * @param $display =
 *        	'default' //sets the default paging display with <ul> and </li>
 *        	tags. If $display = false, the function will return the paging
 *        	array which is the same as $posts_pages_links in every template
 *
 *
 */
function paging($display = 'default', $data = false) {
    print "todo: paging() function";
    return true;
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
    $more = get_custom_fields('table_content', $content_id, $full, false, false, $field_type);






    return $more;
}

function get_custom_fields($table, $id = 0, $return_full = false, $field_for = false, $debug = false, $field_type = false) {

    // $id = intval ( $id );
    if ($id == 0) {

        // return false;
    }


    $id = intval($id);
    $table = db_escape_string($table);
    $table_assoc_name = false;
    if ($table != false) {
        $table_assoc_name = db_get_table_name($table);
    } else {

        $table_assoc_name = "MW_ANY_TABLE";
    }

    if ((int) $table_assoc_name == 0) {
        $table_assoc_name = guess_table_name($table);
    }
    $table_assoc_name = db_get_assoc_table_name($table_assoc_name);

    $table_custom_field = c('db_tables');
    // ->'table_custom_fields';
    $table_custom_field = $table_custom_field['table_custom_fields'];

    $the_data_with_custom_field__stuff = array();

    if (strval($table_assoc_name) != '') {

        if ($field_for != false) {
            $field_for = trim($field_for);
            $field_for_q = " and  (field_for='{$field_for} OR custom_field_name='{$field_for}')'";
        } else {
            $field_for_q = " ";
        }

        if ($table_assoc_name == 'MW_ANY_TABLE') {

            $qt = '';
        } else {
            //$qt = " (to_table='{$table_assoc_name}'  or to_table='{$table_ass}'  ) and";

            $qt = " to_table='{$table_assoc_name}'    and";
        }

        if ($return_full == true) {

            $select_what = '*';
        } else {
            $select_what = '*';
        }


        if ($field_type == false) {

            $field_type_q = ' ';
        } else {
            $field_type = db_escape_string($field_type);
            $field_type_q = ' and custom_field_type="' . $field_type . '"  ';
        }





        $q = " SELECT
		{$select_what} from  $table_custom_field where
		{$qt}
		to_table_id='{$id}'
		$field_for_q
		$field_type_q
		order by field_order asc  ";

        if ($debug != false) {
            d($q);
        }

        // $crc = crc32 ( $q );

        $crc = (crc32($q));


        $cache_id = __FUNCTION__ . '_' . $crc;

        $q = db_query($q, $cache_id, 'custom_fields/global');

        if (!empty($q)) {

            if ($return_full == true) {
                $to_ret = array();
                foreach ($q as $it) {

                    // $it ['value'] = $it ['custom_field_value'];
                    $it['value'] = $it['custom_field_value'];
                    if (isset($it['custom_field_value']) and strtolower($it['custom_field_value']) == 'array') {
        if (isset($it['custom_field_values']) and is_string($it['custom_field_values'])) {
            $try = base64_decode($it['custom_field_values']);
            if ($try != false) {
                $it['custom_field_values'] = unserialize($try);
            }
        }
    }

                  //  $it['values'] = $it['custom_field_value'];

                   // $it['cssClass'] = $it['custom_field_type'];
                    $it['type'] = $it['custom_field_type'];

                  //  $it['baseline'] = "undefined";

                    $it['title'] = $it['custom_field_name'];
                    $it['required'] = $it['custom_field_required'];

                    $to_ret[] = $it;
                }
                return $to_ret;
            }

            $append_this = array();
            if (is_array($q) and !empty($q)) {
                foreach ($q as $q2) {

                    $i = 0;

                    $the_name = false;

                    $the_val = false;

                    foreach ($q2 as $cfk => $cfv) {

                        if ($cfk == 'custom_field_name') {

                            $the_name = $cfv;
                        }

                        if ($cfk == 'custom_field_value') {

                            $the_val = $cfv;
                        }

                        $i++;
                    }

                    if ($the_name != false and $the_val != false) {
                        if ($return_full == false) {

                            $the_data_with_custom_field__stuff[$the_name] = $the_val;
                        } else {

                            $the_data_with_custom_field__stuff[$the_name] = $q2;
                        }
                    }
                }
            }
        }
    }

    $result = $the_data_with_custom_field__stuff;
    $result = (array_change_key_case($result, CASE_LOWER));
    $result = remove_slashes_from_array($result);
    $result = replace_site_vars_back($result);
    return $result;
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
    $ref_page = $_SERVER['HTTP_REFERER'];
    if ($ref_page != '') {
        $ref_page = $the_ref_page = get_content_by_url($ref_page);
        $page_id = $ref_page['id'];
        $ref_page['custom_fields'] = get_custom_fields_for_content($page_id, false);
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
                    }

                    if (isset($the_field_data['attributes']['data-field'])) {
                        $field = trim($the_field_data['attributes']['data-field']);
                    }

                    if ($field == false) {
                        if (isset($the_field_data['attributes']['id'])) {
                            $the_field_data['attributes']['field'] = $field = $the_field_data['attributes']['id'];
                        }
                    }

                    if (($field != false)) {
                        $page_element_id = $field;
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

                    if (isset($the_field_data['attributes']['rel']) and trim($the_field_data['attributes']['rel']) == 'content' and isset($the_field_data['attributes']['data-id'])) {
                        $save_global = false;
                        $save_layout = false;
                        $content_id = $the_field_data['attributes']['data-id'];
                    }
                    $save_layout = false;

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
                            $history_to_save['table'] = 'table_content';
                            $history_to_save['id'] = $content_id;
                            $history_to_save['value'] = $old;
                            $history_to_save['field'] = $field;
                            // p ( $history_to_save );
                            if ($is_no_save != true) {
                                save_history($history_to_save);
                            }

                            $to_save = array();
                            $to_save['id'] = $content_id;

                            //$to_save['debug'] = $content_id;

                            $to_save['page_element_id'] = $page_element_id;

                            $is_native_fld = db_get_table_fields('table_content');
                            if (in_array($field, $is_native_fld)) {
                                $to_save[$field] = ($html_to_save);
                            } else {
                                $to_save['custom_fields'][$field] = ($html_to_save);
                            }
                            // d($to_save);
                            //  exit;

                            if ($is_no_save != true) {
                                $json_print[] = $to_save;

                                $saved = save_content($to_save);
                            }
                        } else if (isset($category_id)) {
                            print(__FILE__ . __LINE__ . ' category is not implemented not ready yet');
                        }
                    } else {
                        if ($save_global == true and $save_layout == false) {

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
                            $json_print[] = $to_save;
                            $history_to_save = array();
                            $history_to_save['table'] = 'global';
                            // $history_to_save ['id'] = 'global';
                            $history_to_save['value'] = $field_content['option_value'];
                            $history_to_save['field'] = $field;
                            if ($is_no_save != true) {
                                //  $this->core_model->saveHistory($history_to_save);
                            }
                        }
                        if ($save_global == false and $save_layout == true) {

                            $d = TEMPLATE_DIR . 'layouts' . DIRECTORY_SEPARATOR . 'editabe' . DIRECTORY_SEPARATOR;
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

    if (isset($data['id'])) {
        $c_id = intval($data['id']);
        db_delete_by_id('table_content', $c_id);

        //d($c_id);
    }
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
    if ($adm == false) {
        error('Error: not logged in as admin.');
    }
    $cats_modified = false;
    $cms_db_tables = c('db_tables');

    $table = $cms_db_tables['table_content'];

    if (empty($data)) {

        return false;
    }
    $data_to_save = $data;

    $more_categories_to_delete = array();
    if (intval($data['id']) != 0) {

        $q = "SELECT * from $table where id='{$data_to_save['id']}' ";

        $q = db_query($q);

        $thetitle = $q[0]['title'];

        $q = $q[0]['url'];

        $theurl = $q;

        $more_categories_to_delete = get_categories_for_content($data['id'], 'categories');
    } else {

        $theurl = $data['url'];

        $thetitle = $data['title'];
    }

    if (isset($theurl) != false) {

        if (isset($data['url']) and $data['url'] == $theurl) {

            // print 'asd2';

            $data['url'] = $theurl;
        } else {

        }

        if (!isset($data['url']) or strval($data['url']) == '') {

            // print 'asd1';

            $data['url'] = $theurl;
        }

        if ((strval($data['url']) == '') and (strval($theurl) == '')) {

            // print 'asd';

            $data['url'] = url_title($thetitle);
        }
    } else {
        if ($thetitle != false) {
            $data['url'] = url_title($thetitle);
        }
    }
    if (isset($item['title'])) {
        $item['title'] = htmlspecialchars_decode($item['title'], ENT_QUOTES);

        $item['title'] = strip_tags($item['title']);
    }
    if ($data['url'] != false) {
        // if (intval ( $data ['id'] ) == 0) {
        $data_to_save['url'] = $data['url'];

        // }
    }

    if (isset($data['category']) or isset($data['categories'])) {
        $cats_modified = true;
    }

    if ($data['url'] != false) {
        $data['url'] = url_title($data['url']);

        if (strval($data['url']) == '') {

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

        // $data_to_save ['url_md5'] = md5 ( $data_to_save
        // ['url'] );
    }

    $data_to_save_options = array();

    if (isset($data_to_save['is_home']) and $data_to_save['is_home'] == 'y') {
        $sql = "UPDATE $table set is_home='n'   ";
        $q = db_query($sql);
    }

    if (isset($data_to_save['subtype']) and strval($data_to_save['subtype']) == 'dynamic') {

        if (isset($data_to_save['subtype_value']) and intval(trim($data_to_save['subtype_value'])) > 0) {

            $check_ex = get_category_by_id(intval($data_to_save['subtype_value']));
            if ($check_ex == false) {
                unset($data_to_save['subtype_value']);
            }
        }

        if (!isset($data_to_save['subtype_value']) or trim($data_to_save['subtype_value']) == '') {

            if (!isset($data_to_save['subtype_value_new'])) {
                if (isset($data_to_save['title'])) {
                    $cats_modified = true;
                    $data_to_save['subtype_value_new'] = $data_to_save['title'];
                }
            }
        }
    }

    if (isset($data_to_save['subtype_value_new']) and strval($data_to_save['subtype_value_new']) != '') {

        if ($data_to_save['subtype_value_new'] != '') {

            if ($adm == true) {

                $new_category = array();
                $new_category["data_type"] = "category";
                $new_category["to_table"] = "table_content";
                $new_category["title"] = $data_to_save['subtype_value_new'];
                $new_category["parent_id"] = "0";
                $cats_modified = true;
                $new_category = save_category($new_category);
                //d($new_category);
                $data_to_save['subtype_value'] = $new_category;
                $data_to_save['subtype'] = 'dynamic';
            }
        }

        if (isset($data_to_save['taxonomy_categories_str']) and !empty($data_to_save['taxonomy_categories_str'])) {
            $data_to_save['subtype_value_auto_create'] = $data_to_save['taxonomy_categories_str'];

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
                        $new_scategory["parent_id"] = intval($new_category);
                        $cats_modified = true;
                        $new_scategory = save_category($new_scategory);
                    }
                }
            }
        }
    }
    $par_page = false;
    if (isset($data_to_save['content_type']) and strval($data_to_save['content_type']) == 'post') {
        if (isset($data_to_save['parent']) and intval($data_to_save['parent']) > 0) {
            $par_page = get_content_by_id($data_to_save['parent']);
        }

        if (is_array($par_page)) {
            if (!isset($data_to_save['categories'])) {
                $data_to_save['categories'] = '';
            }
            if (is_string($data_to_save['categories'])) {
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
                            if (isset($cont_cat[0]) and isarr($cont_cat[0])) {
                                $cont_cat = $cont_cat[0];
                                if (isset($cont_cat["subtype_value"]) and intval($cont_cat["subtype_value"]) > 0) {
                                    // d($cont_cat);
                                    $data_to_save['parent'] = $cont_cat["id"];
                                    break;
                                }
                            }
                            //
                        }
                    }
                }
                // d($c1);
            }
        }
    }

    //d($data_to_save);

    $save = save_data($table, $data_to_save);

    $custom_field_table = $cms_db_tables['table_custom_fields'];

    $sid = session_id();

    $id = $save;

    $clean = " update $custom_field_table set
				to_table =\"table_content\"
				, to_table_id =\"{$id}\"
				where
				session_id =\"{$sid}\"
and (to_table_id=0 or to_table_id IS NULL)

				";
    //d($clean);

    db_q($clean);
    cache_clean_group('custom_fields');

    $media_table = $cms_db_tables['table_media'];

    $clean = " update $media_table set

				  to_table_id =\"{$id}\"
				where
				session_id =\"{$sid}\"
and to_table =\"table_content\" and (to_table_id=0 or to_table_id IS NULL)

				";
    //   d($clean);
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

    if ($cats_modified != false) {

        cache_clean_group('taxonomy/global');
        cache_clean_group('taxonomy_items/global');
        if (isset($c1) and isarr($c1)) {
            foreach ($c1 as $item) {
                $item = intval($item);
                if ($item > 0) {
                    cache_clean_group('taxonomy/' . $item);
                }
            }
        }
    }
    return $save;
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
	// if (!empty($data_to_save['taxonomy_categories'])) {
    // foreach ($data_to_save ['taxonomy_categories'] as $cat) {
    //
	// cache_clean_group('taxonomy' . DIRECTORY_SEPARATOR . intval($cat));
    // }
    // // cache_clean_group ( 'taxonomy' . DIRECTORY_SEPARATOR . '0' );
    // cache_clean_group('taxonomy' . DIRECTORY_SEPARATOR . 'global');
    // cache_clean_group('taxonomy' . DIRECTORY_SEPARATOR . 'items');
    // }
    //
	// if (!empty($more_categories_to_delete)) {
    // foreach ($more_categories_to_delete as $cat) {
    // cache_clean_group('taxonomy' . DIRECTORY_SEPARATOR . intval($cat));
    // }
    // }
    // }
    // return $save;
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
 * $pt_opts['actve_ids'] = $data['parent'];
 * $pt_opts['active_code_tag'] = '   selected="selected"  ';
 *  pages_tree($pt_opts);
 *
 * 	Other options
 * $pt_opts['parent'] = "8"; //
 * $pt_opts['include_first'] =  true; //includes the parent in the tree
 * $pt_opts['id_prefix'] = 'my_id';
 */

function pages_tree($parent = 0, $link = false, $actve_ids = false, $active_code = false, $remove_ids = false, $removed_ids_code = false, $ul_class_name = false, $include_first = false) {


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
    $cache_content = cache_get_content($function_cache_id, $cache_group);

    if (($cache_content) != false) {
        print $cache_content;
        return;
        //  return $cache_content;
    }

    $nest_level = 0;

    ob_start();

    //d($params);

    if (isset($params['nest_level'])) {
        $nest_level = $params['nest_level'];
    }

    $cms_db_tables = c('db_tables');

    $table = $cms_db_tables['table_content'];

    if ($parent == false) {

        $parent = (0);
    }

    if ($include_first == true) {
        $sql = "SELECT * from $table where  id=$parent    and content_type='page'  order by updated_on desc limit 0,1";
        //
    } else {

        //$sql = "SELECT * from $table where  parent=$parent    and content_type='page'  order by updated_on desc limit 0,1";
        $sql = "SELECT * from $table where  parent=$parent    and content_type='page'  order by updated_on desc limit 0,100";
    }
    //d($sql);
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
        $link = '<span data-page-id="{id}" class="pages_tree_link {nest_level}" href="{link}' . $append_to_link . '">{title}</span>';
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

    if (isset($actve_ids) and is_string($actve_ids)) {
        $actve_ids = explode(',', $actve_ids);
    }

    //	$params['debug'] = $parent;
    $params['content_type'] = 'page';
    if ($include_first == true) {
        $include_first = false;
        $params['id'] = $parent;
        if (isset($params['include_first'])) {
            unset($params['include_first']);
        }
        if (isset($params['parent'])) {
            unset($params['parent']);
        }
    } else {
        $params['parent'] = $parent;
    }
    $params['limit'] = 50;
    $params['curent_page'] = 1;
    $q = get_content($params);

    $result = $q;

    if (is_array($result) and !empty($result)) {
        $nest_level++;

        if ($ul_class_name == false) {
            print "<{$list_tag} class='pages_tree depth-{$nest_level}'>";
        } else {
            print "<{$list_tag} class='{$ul_class_name} depth-{$nest_level}'>";
        }

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
                $to_pr_2 = "<{$list_item_tag} class='$content_type_li_class {active_class} depth-{$nest_level} item_{$iid}' data-page-id='{$item['id']}' value='{$item['id']}'  data-item-id='{$item['id']}' {active_code_tag} data-parent-page-id='{$item['parent']}' {$st_str} {$st_str2} {$st_str3}  >";

                if ($link != false) {

                    $to_print = str_ireplace('{id}', $item['id'], $link);

                    $to_print = str_ireplace('{title}', $item['title'], $to_print);

                    $to_print = str_ireplace('{nest_level}', 'depth-' . $nest_level, $to_print);

                    $to_print = str_ireplace('{link}', page_link($item['id']), $to_print);

                    if (strstr($to_print, '{tn}')) {
                        $to_print = str_ireplace('{tn}', thumbnail($item['id'], 'original'), $to_print);
                    }
                    foreach ($item as $item_k => $item_v) {
                        $to_print = str_ireplace('{' . $item_k . '}', $item_v, $to_print);
                    }

                    if (is_array($actve_ids) == true) {

                        $is_there_active_ids = false;

                        foreach ($actve_ids as $active_id) {

                            if (strval($item['id']) == strval($active_id)) {

                                $is_there_active_ids = true;

                                $to_print = str_ireplace('{active_code}', $active_code, $to_print);
                                $to_print = str_ireplace('{active_class}', 'active', $to_print);
                                $to_pr_2 = str_ireplace('{active_class}', 'active', $to_pr_2);
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

                    if (is_array($remove_ids) == true) {

                        if (in_array($item['id'], $remove_ids)) {

                            if ($removed_ids_code == false) {

                                $to_print = false;
                            } else {

                                $to_print = str_ireplace('{removed_ids_code}', $removed_ids_code, $to_print);
                                //$to_pr_2 = str_ireplace('{removed_ids_code}', $removed_ids_code, $to_pr_2);
                            }
                        } else {

                            $to_print = str_ireplace('{removed_ids_code}', '', $to_print);
                            //$to_pr_2 = str_ireplace('{removed_ids_code}', $removed_ids_code, $to_pr_2);
                        }
                    }
                    $to_pr_2 = str_ireplace('{active_class}', '', $to_pr_2);

                    print $to_pr_2;
                    $to_pr_2 = false;
                    print $to_print;
                } else {
                    $to_pr_2 = str_ireplace('{active_class}', '', $to_pr_2);
                    print $to_pr_2;
                    $to_pr_2 = false;
                    print $item['title'];
                }

                if (is_array($params)) {
                    $params['parent'] = $item['id'];

                    //   $nest_level++;
                    $params['nest_level'] = $nest_level;
                    $children = pages_tree($params);
                } else {
                    $children = pages_tree(intval($item['id']), $link, $actve_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name);
                }

                if (isset($include_categories) and $include_categories == true) {
                    if (isset($item['subtype_value']) and intval($item['subtype_value']) == true) {
                        $cat_params = array();
                        $cat_params['subtype_value'] = $item['subtype_value'];
                        $cat_params['include_first'] = 1;
                        $cat_params['nest_level'] = $nest_level;
                        //  d($cat_params);
                        category_tree($cat_params);
                    }
                }
            }
            print "</{$list_item_tag}>";
        }

        print "</{$list_tag}>";
    } else {

    }

    $content = ob_get_contents();
    cache_store_data($content, $function_cache_id, $cache_group);

    ob_end_clean();
    print $content;
    return;
}

function mw_create_default_content($what) {

    switch ($what) {
        case 'shop' :
            $is_shop = get_content('content_type=page&is_shop=y');
            if ($is_shop == false) {
                $add_page = array();
                $add_page['id'] = 0;
                $add_page['parent'] = 0;

                $add_page['title'] = "Online shop";
                $add_page['url'] = "online-shop";
                $add_page['content_type'] = "page";
                $add_page['subtype'] = 'dynamic';
                $add_page['is_shop'] = 'y';
                $add_page['active_site_template'] = 'default';
                $find_layout = layouts_list();
                foreach ($find_layout as $item) {
                    if (isset($item['layout_file']) and isset($item['is_shop']) and $item['is_shop'] == 'yes') {
                        $add_page['layout_file'] = $item['layout_file'];
                        if (isset($item['name'])) {
                            $add_page['title'] = $item['name'];
                        }
                    }
                }
                $new_shop = save_content($add_page);
                clearcache();
                //
            } else {
                //d($is_shop);

                $new_shop = $is_shop[0]['id'];
            }

            $posts = get_content('content_type=post&parent=' . $new_shop);
            if ($posts == false) {
                $add_page = array();
                $add_page['id'] = 0;
                $add_page['parent'] = $new_shop;
                $add_page['title'] = "My product";
                $add_page['url'] = "my-product";
                $add_page['content_type'] = "post";
                $add_page['subtype'] = "product";

                $new_shop = save_content($add_page);
                clearcache();
            }
            //d($posts);

            break;

        default :
            break;
    }
}

