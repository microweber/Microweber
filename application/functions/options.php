<?php

function create_mw_default_options() {

    $function_cache_id = __FUNCTION__;

    $cache_content = cache_get_content($function_cache_id, $cache_group = 'db');
    if (($cache_content) == '--true--') {
        return true;
    }
    $cms_db_tables = c('db_tables');

    $table = $cms_db_tables['table_options'];

    define('FORCE_SAVE', $table);
    $datas = array();



    $data = array();
    $data['option_group'] = 'website';
    $data['option_key'] = 'website_title';
    $data['option_value'] = 'My website';
    $data['position'] = '1';
    $datas[] = $data;

    $data = array();
    $data['option_group'] = 'website';
    $data['option_key'] = 'website_description';
    $data['option_value'] = 'My website\'s description';
    $data['position'] = '2';
    $datas[] = $data;

    $data = array();
    $data['option_group'] = 'website';
    $data['option_key'] = 'curent_template';
    $data['option_value'] = 'lab';
    $data['position'] = '3';
    $datas[] = $data;


    $data = array();
    $data['option_group'] = 'website';
    $data['option_key'] = 'items_pre_page';
    $data['option_value'] = '30';
    $data['position'] = '4';
    $datas[] = $data;

    $data = array();
    $data['option_group'] = 'users';
    $data['option_key'] = 'enable_user_registration';
    $data['option_value'] = '0';
    $data['position'] = '3';

    $datas[] = $data;
    $changes = false;
    foreach ($datas as $value) {
        $ch = set_default_option($value);
        if ($ch == true) {
            $changes = true;
        }
    }
    if ($changes == true) {
        //var_dump($changes);
        cache_clean_group('options/global');
    }
    cache_store_data('--true--', $function_cache_id, $cache_group = 'db');

    return true;
}

function set_default_option($data) {
    $changes = false;
    if (is_array($data)) {
        if (!isset($data['option_group'])) {
            $data['option_group'] = 'other';
        }

        if (isset($data['option_key'])) {
            $check = get_option($data['option_key'], $option_group = $data['option_group'], $return_full = false, $orderby = false);
            if ($check == false) {
                save_option($data);
            }
        }
    } else {
        error('set_default_option $data param must be array');
    }
    return $changes;
}

function option_get($key, $option_group = false, $return_full = false, $orderby = false) {
    return get_option($key, $option_group, $return_full, $orderby);
}

function get_option_groups() {
    $table = c('db_tables');
    // ->'table_options';
    $table = $table['table_options'];

    $q = "select option_group from $table where module IS NULL and option_group IS NOT NULL group by option_group order by position ASC ";
    $function_cache_id = __FUNCTION__ . crc32($q);
    $res1 = false;
    $res = db_query($q, $cache_id = $function_cache_id, $cache_group = 'options/global');
    if (is_array($res) and !empty($res)) {
        $res1 = array();
        foreach ($res as $item) {
            $res1[] = $item['option_group'];
        }
    }
    return $res1;
}

function get_options($params = '') {


    if (is_string($params)) {
        $params = parse_str($params, $params2);
        $params = $params2;
        extract($params);
    }
    if (is_array($params)) {
        $parent = 0;
        extract($params);
    }


    $data = $params;
    $table = c('db_tables');
    $table = $table['table_options'];
    //  $data['debug'] = 1000;
    $data['limit'] = 1000;
    $get = db_get($table, $data, $cache_group = 'options/global');
    return $get;
}

if (is_admin() != false) {
    api_expose('save_option');
}

function save_option($data) {
    $is_admin = is_admin();
    if ($is_admin == false or !defined('FORCE_SAVE')) {
        //error('Error: not logged in as admin.');
    }
    // p($_POST);
    $option_group = false;
    if ($data) {
        if (!isset($data['id']) or intval($data['id']) == 0) {
            if ($data['option_key'] and $data['option_group']) {
                $option_group = $data['option_group'];
                delete_option_by_key($data['option_key'], $data['option_group']);
            }
        }
        if (strval($data['option_key']) != '') {

            $cms_db_tables = c('db_tables');

            $table = $cms_db_tables['table_options'];

            // $data ['debug'] = 1;
            $save = save_data($table, $data);


//            if ($option_group != false) {
//
//                $cache_group = 'options/' . $option_group;
//                cache_clean_group($cache_group);
//            }
//
//            if (isset($data['id'])) {
//                $cache_group = 'options/' . $data['id'];
//                cache_clean_group($cache_group);
//            }
//            if (isset($data['module'])) {
//                $cache_group = 'options/' . $data['module'];
//                cache_clean_group($cache_group);
//            }
//
//
//            if (isset($data['data-option-group'])) {
//                $cache_group = 'options/' . $data['data-option-group'];
//                cache_clean_group($cache_group);
//            }
//            if (isset($data['option-group'])) {
//                $cache_group = 'options/' . $data['option-group'];
//                cache_clean_group($cache_group);
//            }
//            if (isset($data['data-module'])) {
//                $cache_group = 'options/' . $data['data-module'];
//                cache_clean_group($cache_group);
//            }
//            if (isset($data['option_key'])) {
//                $cache_group = 'options/' . $data['option_key'];
//                cache_clean_group($cache_group);
//            }


            cache_clean_group('options/global');

            return $save;
        }
    }
}

function delete_option_by_key($key, $option_group = false) {
    $key = db_escape_string($key);
    $cms_db_tables = c('db_tables');

    $table = $cms_db_tables['table_options'];


    if ($option_group != false) {
        $option_group = db_escape_string($option_group);
        $option_group_q1 = "and option_group='{$option_group}'";
    }
    // $save = $this->saveData ( $table, $data );
    $q = "delete from $table where option_key='$key' $option_group_q1 ";

    db_q($q);
    cache_clean_group('options');
    return true;
}
