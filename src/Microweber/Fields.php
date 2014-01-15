<?php
namespace Microweber;

api_expose('fields/reorder');
api_expose('fields/delete');
api_expose('fields/make');
api_expose('fields/save');
$_mw_made_default_fields_register = array();

class Fields
{

    public $app;
    private $skip_cache = false;

    function __construct($app = null)
    {


        if (!is_object($this->app)) {

            if (is_object($app)) {
                $this->app = $app;
            } else {
                $this->app = mw('application');
            }

        }


    }

    public function get_by_id($field_id)
    {

        if ($field_id != 0) {
            $data = $this->app->db->get_by_id('table_custom_fields', $id = $field_id, $is_this_field = false);
            if (isset($data['options'])) {
                $data['options'] = $this->app->format->base64_to_array($data['options']);
            }
            return $data;
        }
    }

    public function make_default($rel, $rel_id, $fields_csv_str)
    {
        global $_mw_made_default_fields_register;
        if (!defined('SKIP_CF_ADMIN_CHECK')) {
            define('SKIP_CF_ADMIN_CHECK', 1);
        }

        // return false;
        $id = $this->app->user->is_admin();
        if ($id == false) {
            //return false;
        }

        $function_cache_id = false;

        $args = func_get_args();

        foreach ($args as $k => $v) {

            $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
        }

        $function_cache_id = 'fields_' . __FUNCTION__ . crc32($function_cache_id);


        //$is_made = $this->app->option->get($function_cache_id, 'make_default_custom_fields');


        $is_made = $this->get($rel, $rel_id, $return_full = 0, $field_for = false, $debug = 0);

        if (isset($_mw_made_default_fields_register[$function_cache_id])) {
            return;
        }


        if (is_array($is_made) and !empty($is_made)) {
            return;
        }
        $_mw_made_default_fields_register[$function_cache_id] = true;

        $table_custom_field = MW_TABLE_PREFIX . 'custom_fields';

        if (isset($rel)) {
            $rel = $this->app->db->escape_string($rel);

            $rel = $this->app->db->assoc_table_name($rel);
            $rel_id = $this->app->db->escape_string($rel_id);

            if (strstr($fields_csv_str, ',')) {
                $fields_csv_str = explode(',', $fields_csv_str);
                $fields_csv_str = array_trim($fields_csv_str);
            } else {
                $fields_csv_str = array($fields_csv_str);
            }


            $pos = 0;
            if (is_array($fields_csv_str)) {
                foreach ($fields_csv_str as $field_type) {
                    $ex = $this->get($rel, $rel_id, $return_full = 1, $field_for = false, $debug = 0, $field_type);

                    if ($ex == false or is_array($ex) == false) {
                        $make_field = array();
                        $make_field['id'] = 0;
                        $make_field['rel'] = $rel;
                        $make_field['rel_id'] = $rel_id;
                        $make_field['position'] = $pos;
                        $make_field['custom_field_name'] = ucfirst($field_type);
                        $make_field['custom_field_value'] = false;

                        $make_field['custom_field_type'] = $field_type;

                        $this->save($make_field);
                        $pos++;
                    }
                }


                if ($pos > 0) {
                    $this->app->cache->delete('custom_fields/global');

                }


                if ($rel_id != '0') {
                    $option = array();
                    $option['option_value'] = 'yes';
                    $option['option_key'] = $function_cache_id;
                    $option['option_group'] = 'make_default_custom_fields';
                    //   $this->app->option->save($option);
                }

            }

            //

        }


    }

    public function get($table, $id = 0, $return_full = false, $field_for = false, $debug = false, $field_type = false, $for_session = false)
    {
        //defined in common.php
        $params = array();

        $table_assoc_name = false;
        // $id = intval ( $id );
        if (is_string($table)) {

            $params = $params2 = parse_params($table);

            if (!is_array($params2) or (is_array($params2) and count($params2) < 2)) {

                $id = trim($id);
                $table = $this->app->db->escape_string($table);

                if ($table != false) {
                    $table_assoc_name = $this->app->db->get_table_name($table);
                } else {

                    $table_assoc_name = "MW_ANY_TABLE";
                }


                if ($table_assoc_name == false) {
                    $table_assoc_name = $this->app->db->assoc_table_name($table_assoc_name);

                }
            } else {
                $params = $params2;
            }
        }


        if (isset($params['content_id'])) {
            $params['for'] = 'content';
            $params['for_id'] = $params['content_id'];

        }

        if (isset($params['for'])) {
            $table_assoc_name = $this->app->db->assoc_table_name($params['for']);
        }
        if (isset($params['debug'])) {
            $debug = $params['debug'];
        }
        if (isset($params['for_id'])) {
            $id = $this->app->db->escape_string($params['for_id']);
        }

        if (isset($params['field_type'])) {
            $field_type = $this->app->db->escape_string($params['field_type']);
        } else if (isset($params['custom_field_type'])) {
            $field_type = $this->app->db->escape_string($params['custom_field_type']);
        }
        if (isset($params['return_full'])) {
            $return_full = $params['return_full'];
        }

        if (isset($params['is_active']) and strtolower(trim($params['is_active'])) == 'any') {

        } else if (isset($params['is_active']) and $params['is_active'] == 'n') {
            $custom_field_is_active = 'n';
        } else {
            $custom_field_is_active = 'y';

        }


        // ->'table_custom_fields';
        $table_custom_field = MW_TABLE_PREFIX . 'custom_fields';

        $the_data_with_custom_field__stuff = array();

        if (strval($table_assoc_name) != '') {

            if ($field_for != false) {
                $field_for = trim($field_for);
                $field_for = $this->app->db->escape_string($field_for);
                $field_for_q = " and  (field_for='{$field_for} OR custom_field_name='{$field_for}')'";
            } else {
                $field_for_q = " ";
            }

            if ($table_assoc_name == 'MW_ANY_TABLE') {

                $qt = '';
            } else {
                //$qt = " (rel='{$table_assoc_name}'  or rel='{$table_ass}'  ) and";

                $qt = " rel='{$table_assoc_name}'    and";
            }

            if ($return_full == true) {

                $select_what = '*';
            } else {
                $select_what = '*';
            }

            if ($field_type == false) {

                $field_type_q = ' ';
                //	$field_type_q = ' and custom_field_type!="content"  ';
            } elseif ($field_type == 'all') {

                $field_type_q = ' ';

            } else {
                $field_type = $this->app->db->escape_string($field_type);
                $field_type_q = ' and custom_field_type="' . $field_type . '"  ';
                $field_type_q .= ' and custom_field_type!=""  ';
            }

            $sidq = '';
            if (intval($id) == 0 and $for_session != false) {
                if (is_admin() != false) {
                    $sid = session_id();
                    $sidq = ' and session_id="' . $sid . '"  ';
                }
            } else {
                $sidq = '';
            }

            if ($id != 'all' and $id != 'any') {
                $id = $this->app->db->escape_string($id);

                /*	 if (intval($id) == 0){
                         if (is_admin() != false) {
                        $sid = session_id();
                        $sidq = ' and session_id="' . $sid . '"  ';
                    }
                     }
        */

                $idq1ttid = " rel_id='{$id}' ";
            } else {
                $idq1ttid = ' rel_id is not null ';
            }

            $qt_is_active = '';
            if (isset($custom_field_is_active)) {
                $custom_field_is_active = $this->app->db->escape_string($custom_field_is_active);
                $qt_is_active = " custom_field_is_active='{$custom_field_is_active}'   and ";
            }


            $q = " SELECT " . $select_what . " FROM  $table_custom_field WHERE
		{$qt}
		{$qt_is_active}
		$idq1ttid
		$field_for_q
		$field_type_q
		$sidq
		ORDER BY position ASC
		   ";

            if ($debug != false) {
                //


            }


            $crc = (crc32($q));

            $cache_id = __FUNCTION__ . '_' . $crc;
            if ($debug != false) {

            }
            // d($q);
            if ($this->skip_cache == false) {
                $q = $this->app->db->query($q, $cache_id, 'custom_fields/global');

            } else {
                $q = $this->app->db->query($q);

            }


            if (!empty($q)) {

                if ($return_full == true) {
                    $to_ret = array();
                    $i = 1;
                    foreach ($q as $it) {

                        // $it ['value'] = $it ['custom_field_value'];
                        $it['value'] = $it['custom_field_value'];

                        if (isset($it['custom_field_value']) and strtolower($it['custom_field_value']) == 'array') {
                            if (isset($it['custom_field_values']) and is_string($it['custom_field_values'])) {
                                $try = base64_decode($it['custom_field_values']);

                                if ($try != false and strlen($try) > 5) {
                                    $it['custom_field_values'] = unserialize($try);
                                }
                            }
                        }

                        //  $it['values'] = $it['custom_field_value'];

                        // $it['cssClass'] = $it['custom_field_type'];
                        $it['type'] = $it['custom_field_type'];
                        $it['position'] = $i;
                        //  $it['baseline'] = "undefined";

                        if (isset($it['options'])) {
                            $it['options'] = $this->app->format->base64_to_array($it['options']);
                        }

                        $it['title'] = $it['custom_field_name'];
                        $it['required'] = $it['custom_field_required'];

                        $to_ret[] = $it;
                        $i++;
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

                                if (strtolower($cfv) == 'array') {

                                    if (isset($q2['custom_field_values_plain']) and is_string($q2['custom_field_values_plain']) and trim($q2['custom_field_values_plain']) != '') {
                                        $cfv = $q2['custom_field_values_plain'];

                                    } else if (isset($q2['custom_field_values']) and is_string($q2['custom_field_values'])) {
                                        $try = base64_decode($q2['custom_field_values']);

                                        if ($try != false and strlen($try) > 3) {
                                            $cfv = unserialize($try);

                                        }
                                    }

                                }

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
        //$result = (array_change_key_case($result, CASE_LOWER));
        $result = $this->app->url->replace_site_url_back($result);
        //d($result);
        return $result;
    }

    public function reorder($data)
    {

        $adm = $this->app->user->is_admin();
        if ($adm == false) {
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }

        $table = MW_TABLE_PREFIX . 'custom_fields';

        foreach ($data as $value) {
            if (is_array($value)) {
                $indx = array();
                $i = 0;
                foreach ($value as $value2) {
                    $indx[$i] = $value2;
                    $i++;
                }

                $this->app->db->update_position_field($table, $indx);
                return true;
            }
        }
    }

    public function delete($id)
    {
        $uid = user_id();
        if ($uid == 0) {
            mw_error('Error: not logged in.');
        }
        $uid = $this->app->user->is_admin();
        if ($uid == false) {
            exit('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }
        if (is_array($id)) {
            extract($id);
        }
        $id = intval($id);
        if (isset($cf_id)) {
            $id = intval($cf_id);
        }

        if ($id == 0) {
            return false;
        }

        $custom_field_table = MW_TABLE_PREFIX . 'custom_fields';
        $q = "DELETE FROM $custom_field_table WHERE id='$id'";
        $this->app->db->q($q);
        $this->app->cache->delete('custom_fields');
        return true;
    }

    public function make_field($field_id = 0, $field_type = 'text', $settings = false)
    {

        $data = false;
        $form_data = array();
        if (is_array($field_id)) {

            if (!empty($field_id)) {
                $data = $field_id;
                return $this->make($field_id, false, 'y');
            }
        } else {
            if ($field_id != 0) {

                return $this->make($field_id);

                //
                // mw_error('no permission to get data');
                //  $form_data = $this->app->db->get_by_id('table_custom_fields', $id = $field_id, $is_this_field = false);
            }
        }
    }

    /**
     * make_field
     *
     * @desc make_field
     * @access      public
     * @category    forms
     * @author      Microweber
     * @link        http://microweber.com
     * @param string $field_type
     * @param string $field_id
     * @param array $settings
     */
    public function make($field_id = 0, $field_type = 'text', $settings = false)
    {

        if (is_array($field_id)) {
            if (!empty($field_id)) {
                $data = $field_id;
            }
        } else {
            if ($field_id != 0) {

                $data = $this->app->db->get_by_id('table_custom_fields', $id = $field_id, $is_this_field = false);
            }
        }
        if (isset($data['settings']) or (isset($_REQUEST['settings']) and trim($_REQUEST['settings']) == 'y')) {

            $settings = true;
        }

        if (isset($data['copy_from'])) {
            $copy_from = intval($data['copy_from']);
            if (is_admin() == true) {

                $table_custom_field = MW_TABLE_PREFIX . 'custom_fields';
                $form_data = $this->app->db->get_by_id($table_custom_field, $id = $copy_from, $is_this_field = false);
                if (is_array($form_data)) {

                    $field_type = $form_data['custom_field_type'];
                    $data['id'] = 0;
                    if (isset($data['save_on_copy'])) {

                        $cp = $form_data;
                        $cp['id'] = 0;
                        $cp['copy_of_field'] = $copy_from;
                        if (isset($data['rel'])) {
                            $cp['rel'] = ($data['rel']);
                        }
                        if (isset($data['rel_id'])) {
                            $cp['rel_id'] = ($data['rel_id']);
                        }
                        $this->save($cp);
                        $data = $cp;
                    } else {
                        $data = $form_data;
                    }

                }

            }
        } else if (isset($data['field_id'])) {
            $data = $this->app->db->get_by_id('table_custom_fields', $id = $data['field_id'], $is_this_field = false);
        }

        if (isset($data['custom_field_type'])) {
            $field_type = $data['custom_field_type'];
        }

        if (!isset($data['custom_field_required'])) {
            $data['custom_field_required'] = 'n';
        }

        if (isset($data['type'])) {
            $field_type = $data['type'];
        }

        if (isset($data['field_type'])) {
            $field_type = $data['field_type'];
        }

        if (isset($data['field_values']) and !isset($data['custom_field_value'])) {
            $data['custom_field_values'] = $data['field_values'];
        }

        $data['custom_field_type'] = $field_type;

        if (isset($data['custom_field_value']) and strtolower($data['custom_field_value']) == 'array') {
            if (isset($data['custom_field_values']) and is_string($data['custom_field_values'])) {

                $try = base64_decode($data['custom_field_values']);

                if ($try != false and strlen($try) > 5) {
                    $data['custom_field_values'] = unserialize($try);
                }
            }
        }
        if (isset($data['options']) and is_string($data['options'])) {
            $data['options'] = $this->app->format->base64_to_array($data['options']);

        }

        $data = $this->app->url->replace_site_url_back($data);


        $dir = MW_INCLUDES_DIR;
        $dir = $dir . DS . 'custom_fields' . DS;
        $field_type = str_replace('..', '', $field_type);


        if ($settings == true or isset($data['settings'])) {
            $file = $dir . $field_type . '_settings.php';
        } else {
            $file = $dir . $field_type . '.php';
        }
        if (!is_file($file)) {
            $field_type = 'text';
            if ($settings == true or isset($data['settings'])) {
                $file = $dir . $field_type . '_settings.php';
            } else {
                $file = $dir . $field_type . '.php';
            }
        }
        $file = normalize_path($file, false);

        if (is_file($file)) {

            $l = new \Microweber\View($file);

            //

            $l->assign('settings', $settings);
            if (isset($data['params'])) {
                $l->assign('params', $data['params']);
            } else {
                $l->assign('params', false);

            }
            //  $l->settings = $settings;

            if (isset($data) and !empty($data)) {
                $l->data = $data;
            } else {
                $l->data = array();
            }

            $l->assign('data', $data);


            //var_dump($data);
            $layout = $l->__toString();

            return $layout;
        }
    }

    public function save($data)
    {

        if (!defined('SKIP_CF_ADMIN_CHECK')) {
            $id = user_id();
            if ($id == 0) {
                mw_error('Error: not logged in.');
            }
            $id = $this->app->user->is_admin();
            if ($id == false) {
                mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
            }
        }
        $data_to_save = ($data);

        $table_custom_field = MW_TABLE_PREFIX . 'custom_fields';

        if (isset($data_to_save['for'])) {
            $data_to_save['rel'] = $this->app->db->assoc_table_name($data_to_save['for']);
        }
        if (isset($data_to_save['cf_id'])) {
            $data_to_save['id'] = intval($data_to_save['cf_id']);

            $table_custom_field = MW_TABLE_PREFIX . 'custom_fields';
            $form_data_from_id = $this->app->db->get_by_id($table_custom_field, $data_to_save['id'], $is_this_field = false);
            if (isset($form_data_from_id['id'])) {
                if (!isset($data_to_save['rel'])) {
                    $data_to_save['rel'] = $form_data_from_id['rel'];
                }
                if (!isset($data_to_save['rel_id'])) {
                    $data_to_save['rel_id'] = $form_data_from_id['rel_id'];
                }

                if (isset($form_data_from_id['custom_field_type']) and $form_data_from_id['custom_field_type'] != '' and (!isset($data_to_save['custom_field_type']) or ($data_to_save['custom_field_type']) == '')) {
                    $data_to_save['custom_field_type'] = $form_data_from_id['custom_field_type'];
                }


            }


            if (isset($data_to_save['copy_rel_id'])) {

                $cp = $this->app->db->copy_row_by_id($table_custom_field, $data_to_save['cf_id']);
                $data_to_save['id'] = $cp;
                $data_to_save['rel_id'] = $data_to_save['copy_rel_id'];
                //$data_to_save['id'] = intval($data_to_save['cf_id']);
            }

        }

        if (!isset($data_to_save['rel'])) {
            $data_to_save['rel'] = 'content';
        }
        $data_to_save['rel'] = $this->app->db->assoc_table_name($data_to_save['rel']);
        if (!isset($data_to_save['rel_id'])) {
            $data_to_save['rel_id'] = '0';
        }
        if (isset($data['options'])) {
            $data_to_save['options'] = $this->app->format->array_to_base64($data['options']);
        }

        $data_to_save['session_id'] = session_id();


        if (!isset($data_to_save['custom_field_type']) or trim($data_to_save['custom_field_type']) == '') {
            return array('error' => 'You must set custom_field_type');
        } else {
            if (!isset($data_to_save['custom_field_name'])) {
                return array('error' => 'You must set custom_field_name');
            }

            if (!isset($data_to_save['custom_field_value'])) {
                return array('error' => 'You must set custom_field_value');
            }

            $cf_k = $data_to_save['custom_field_name'];
            $cf_v = $data_to_save['custom_field_value'];
            if (is_array($cf_v)) {
                $cf_k_plain = $this->app->url->slug($cf_k);
                $cf_k_plain = $this->app->db->escape_string($cf_k_plain);
                $cf_k_plain = str_replace('-', '_', $cf_k_plain);
                $data_to_save['custom_field_values'] = base64_encode(serialize($cf_v));
                $val1_a = $this->app->format->array_values($cf_v);
                //   $val1_a = array_pop($val1_a);
                if (is_array($val1_a)) {
                    $val1_a = implode(', ', $val1_a);

                }


                if($val1_a != 'Array'){
                $data_to_save['custom_field_values_plain'] = $val1_a;
                }

            } else {
                if(strval($cf_v) != 'Array'){
                    $val1_a = nl2br($cf_v,1);

                $data_to_save['custom_field_values_plain'] =  ($val1_a);
                }
            }


            $data_to_save['allow_html'] = true;
            //  $data_to_save['debug'] = true;
            $this->skip_cache = true;

            $save = $this->app->db->save($table_custom_field, $data_to_save);
            $this->app->cache->delete('custom_fields');
            $this->app->cache->delete('custom_fields/global');

            return $save;
        }


        //exit
    }


    /*document_ready('test_document_ready_api');

    function test_document_ready_api($layout) {

    //   $layout = modify_html($layout, $selector = '.editor_wrapper', 'append', 'ivan');
    //$layout = modify_html2($layout, $selector = '<div class="editor_wrapper">', '');
    return $layout;
    }*/

    /**
     * names_for_table
     *
     * @desc names_for_table
     * @access      public
     * @category    forms
     * @author      Microweber
     * @link        http://microweber.com
     * @param string $table
     */


    public function names_for_table($table)
    {
        $table = $this->app->db->escape_string($table);
        $table1 = $this->app->db->assoc_table_name($table);

        $table = MW_DB_TABLE_CUSTOM_FIELDS;
        $q = false;
        $results = false;

        $q = "SELECT *, count(id) AS qty FROM $table WHERE   custom_field_type IS NOT NULL AND rel='{$table1}' AND custom_field_name!='' GROUP BY custom_field_name, custom_field_type ORDER BY qty DESC LIMIT 100";
        //d($q);
        $crc = (crc32($q));

        $cache_id = __FUNCTION__ . '_' . $crc;

        $results = $this->app->db->query($q, $cache_id, 'custom_fields/global');

        if (is_array($results)) {
            return $results;
        }

    }
}