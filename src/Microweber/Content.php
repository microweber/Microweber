<?php
namespace Microweber;


/**
 * Content class
 * Here you will find functions to get and save content in the database and much more.
 *
 * @package Content
 * @category Content
 * @desc  These functions will allow you to get and save content in the database.
 *
 */

api_expose('content/reorder');
api_expose('content/delete');
api_expose('content/set_published');
api_expose('content/set_unpublished');
api_expose('content/menu_item_delete');
api_expose('content/menu_items_reorder');
api_expose('content/menu_create');
api_expose('content/menu_delete');
api_expose('content/menu_item_save');


$mw_skip_pages_starting_with_url = array('admin', 'api', 'module');
$mw_precached_links = array();
$mw_global_content_memory = array();

class Content
{
    public $app;
    public $no_cache = false;

    function __construct($app = null)
    {

        if (!defined("MW_DB_TABLE_CONTENT")) {
            define('MW_DB_TABLE_CONTENT', MW_TABLE_PREFIX . 'content');
        }

        if (!defined("MW_DB_TABLE_CONTENT_FIELDS")) {
            define('MW_DB_TABLE_CONTENT_FIELDS', MW_TABLE_PREFIX . 'content_fields');
        }


        if (!defined("MW_DB_TABLE_CONTENT_DATA")) {
            define('MW_DB_TABLE_CONTENT_DATA', MW_TABLE_PREFIX . 'content_data');
        }

        if (!defined("MW_DB_TABLE_CONTENT_FIELDS_DRAFTS")) {
            define('MW_DB_TABLE_CONTENT_FIELDS_DRAFTS', MW_TABLE_PREFIX . 'content_fields_drafts');
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
        if (!defined("MODULE_DB_MENUS")) {
            define('MODULE_DB_MENUS', MW_TABLE_PREFIX . 'menus');
        }

        if (!defined("MW_DB_TABLE_TAXONOMY")) {
            define('MW_DB_TABLE_TAXONOMY', MW_TABLE_PREFIX . 'categories');
        }
        if (!defined("MW_DB_TABLE_TAXONOMY_ITEMS")) {
            define('MW_DB_TABLE_TAXONOMY_ITEMS', MW_TABLE_PREFIX . 'categories_items');
        }

        if (!is_object($this->app)) {
            if (is_object($app)) {
                $this->app = $app;
            } else {
                $this->app = mw('application');
            }
        }
        if (!defined("MW_DB_TABLE_CONTENT_INIT")) {
            $this->db_init();
            define('MW_DB_TABLE_CONTENT_INIT', 1);

        }


    }

    /**
     * Creates the content tables in the database.
     *
     * It is executed on install and on update
     *
     * @function mw_db_init_content_table
     * @category Content
     * @package Content
     * @subpackage  Advanced
     * @uses  $this->app->db->build_table()
     */
    public function db_init()
    {

        $function_cache_id = false;
        $args = func_get_args();
        foreach ($args as $k => $v) {

            $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
        }

        $function_cache_id = 'content_db_' . __FUNCTION__ . crc32($function_cache_id);

        $cache_content = $this->app->cache->get($function_cache_id, 'db');

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

        $fields_to_add[] = array('content', 'LONGTEXT default NULL');

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
        $fields_to_add[] = array('posted_on', 'datetime default NULL');

        $this->app->db->build_table($table_name, $fields_to_add);


        $this->app->db->add_table_index('url', $table_name, array('url(255)'));
        $this->app->db->add_table_index('title', $table_name, array('title(255)'));


        $table_name = MW_DB_TABLE_CONTENT_DATA;


        $fields_to_add = array();

        $fields_to_add[] = array('updated_on', 'datetime default NULL');
        $fields_to_add[] = array('created_on', 'datetime default NULL');
        $fields_to_add[] = array('created_by', 'int(11) default NULL');
        $fields_to_add[] = array('edited_by', 'int(11) default NULL');
        $fields_to_add[] = array('content_id', 'varchar(11) DEFAULT NULL');
        $fields_to_add[] = array('field_name', 'LONGTEXT default NULL');
        $fields_to_add[] = array('field_value', 'LONGTEXT default NULL');
        $fields_to_add[] = array('session_id', 'varchar(50) DEFAULT NULL');

        $this->app->db->build_table($table_name, $fields_to_add);


        $table_name = MW_DB_TABLE_CONTENT_FIELDS;

        $fields_to_add = array();

        $fields_to_add[] = array('updated_on', 'datetime default NULL');
        $fields_to_add[] = array('created_on', 'datetime default NULL');
        $fields_to_add[] = array('created_by', 'int(11) default NULL');
        $fields_to_add[] = array('edited_by', 'int(11) default NULL');
        $fields_to_add[] = array('rel', 'TEXT default NULL');

        $fields_to_add[] = array('rel_id', 'TEXT default NULL');
        $fields_to_add[] = array('field', 'longtext default NULL');
        $fields_to_add[] = array('value', 'LONGTEXT default NULL');
        $this->app->db->build_table($table_name, $fields_to_add);

        $this->app->db->add_table_index('rel', $table_name, array('rel(55)'));
        $this->app->db->add_table_index('rel_id', $table_name, array('rel_id(255)'));
        // $this->app->db->add_table_index('field', $table_name, array('field(55)'));

        $table_name = MW_DB_TABLE_CONTENT_FIELDS_DRAFTS;
        $fields_to_add[] = array('session_id', 'varchar(50) DEFAULT NULL');
        $fields_to_add[] = array('is_temp', "char(1) default 'y'");
        $fields_to_add[] = array('url', 'TEXT default NULL');


        $this->app->db->build_table($table_name, $fields_to_add);

        $this->app->db->add_table_index('rel', $table_name, array('rel(55)'));
        $this->app->db->add_table_index('rel_id', $table_name, array('rel_id(255)'));
        // $this->app->db->add_table_index('field', $table_name, array('field(56)'));


        $table_name = MW_DB_TABLE_MEDIA;

        $fields_to_add = array();

        $fields_to_add[] = array('updated_on', 'datetime default NULL');
        $fields_to_add[] = array('created_on', 'datetime default NULL');
        $fields_to_add[] = array('created_by', 'int(11) default NULL');
        $fields_to_add[] = array('edited_by', 'int(11) default NULL');
        $fields_to_add[] = array('session_id', 'varchar(50) DEFAULT NULL');
        $fields_to_add[] = array('rel', 'TEXT default NULL');

        $fields_to_add[] = array('rel_id', "varchar(255)  default '0'");
        $fields_to_add[] = array('media_type', 'TEXT default NULL');
        $fields_to_add[] = array('position', 'int(11) default NULL');
        $fields_to_add[] = array('title', 'longtext default NULL');
        $fields_to_add[] = array('description', 'TEXT default NULL');
        $fields_to_add[] = array('embed_code', 'TEXT default NULL');
        $fields_to_add[] = array('filename', 'TEXT default NULL');


        $this->app->db->build_table($table_name, $fields_to_add);

        $this->app->db->add_table_index('rel', $table_name, array('rel(55)'));
        $this->app->db->add_table_index('rel_id', $table_name, array('rel_id(255)'));
        $this->app->db->add_table_index('media_type', $table_name, array('media_type(55)'));

        // $this->app->db->add_table_index('url', $table_name, array('url'));
        // $this->app->db->add_table_index('title', $table_name, array('title'));


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


        $this->app->db->build_table($table_name, $fields_to_add);

        $this->app->db->add_table_index('rel', $table_name, array('rel(55)'));
        $this->app->db->add_table_index('rel_id', $table_name, array('rel_id(55)'));
        $this->app->db->add_table_index('custom_field_type', $table_name, array('custom_field_type(55)'));


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
        $this->app->db->build_table($table_name, $fields_to_add);


        $table_name = MW_DB_TABLE_TAXONOMY;

        $fields_to_add = array();

        $fields_to_add[] = array('updated_on', 'datetime default NULL');
        $fields_to_add[] = array('created_on', 'datetime default NULL');
        $fields_to_add[] = array('created_by', 'int(11) default NULL');
        $fields_to_add[] = array('edited_by', 'int(11) default NULL');
        $fields_to_add[] = array('data_type', 'TEXT default NULL');
        $fields_to_add[] = array('title', 'longtext default NULL');
        $fields_to_add[] = array('parent_id', 'int(11) default NULL');
        $fields_to_add[] = array('description', 'TEXT default NULL');
        $fields_to_add[] = array('content', 'TEXT default NULL');
        $fields_to_add[] = array('content_type', 'TEXT default NULL');
        $fields_to_add[] = array('rel', 'TEXT default NULL');

        $fields_to_add[] = array('rel_id', 'int(11) default NULL');

        $fields_to_add[] = array('position', 'int(11) default NULL');
        $fields_to_add[] = array('is_deleted', "char(1) default 'n'");
        $fields_to_add[] = array('users_can_create_subcategories', "char(1) default 'n'");
        $fields_to_add[] = array('users_can_create_content', "char(1) default 'n'");
        $fields_to_add[] = array('users_can_create_content_allowed_usergroups', 'TEXT default NULL');

        $fields_to_add[] = array('categories_content_type', 'TEXT default NULL');
        $fields_to_add[] = array('categories_silo_keywords', 'TEXT default NULL');


        $this->app->db->build_table($table_name, $fields_to_add);

        $this->app->db->add_table_index('rel', $table_name, array('rel(55)'));
        $this->app->db->add_table_index('rel_id', $table_name, array('rel_id'));
        $this->app->db->add_table_index('parent_id', $table_name, array('parent_id'));

        $table_name = MW_DB_TABLE_TAXONOMY_ITEMS;

        $fields_to_add = array();
        $fields_to_add[] = array('parent_id', 'int(11) default NULL');
        $fields_to_add[] = array('rel', 'TEXT default NULL');

        $fields_to_add[] = array('rel_id', 'int(11) default NULL');
        $fields_to_add[] = array('content_type', 'TEXT default NULL');
        $fields_to_add[] = array('data_type', 'TEXT default NULL');

        $this->app->db->build_table($table_name, $fields_to_add);

        // $this->app->db->add_table_index('rel', $table_name, array('rel(55)'));
        $this->app->db->add_table_index('rel_id', $table_name, array('rel_id'));
        $this->app->db->add_table_index('parent_id', $table_name, array('parent_id'));

        $this->app->cache->save(true, $function_cache_id, $cache_group = 'db');
        return true;

    }

    /**
     * Get single content item by id from the content_table
     *
     * @param int|string $id The id of the page or the url of a page
     * @return array The page row from the database
     * @category Content
     * @function  get_page
     *
     * @example
     * <pre>
     * Get by id
     * $page = $this->get_page(1);
     * var_dump($page);
     * </pre>
     * @example
     * <pre>
     * Get by url
     *
     * $page = $this->get_page('home');
     * var_dump($page);
     *</pre>
     */
    public function get_page($id = 0)
    {
        if ($id == false or $id == 0) {
            return false;
        }

        // $CI = get_instance ();
        if (intval($id) != 0) {
            $page = $this->get_by_id($id);

            if (empty($page)) {
                $page = $this->get_by_url($id);
            }
        } else {
            if (empty($page)) {
                $page = array();
                $page['layout_name'] = trim($id);

                $page = $this->get($page);
                $page = $page[0];
            }
        }

        return $page;

    }

    /**
     * Return the path to the layout file that will render the page
     *
     * It accepts array $page that must have  $page['id'] set
     *
     * @example
     * <code>
     *  //get the layout file for content
     *  $content = $this->get_by_id($id=1);
     *  $render_file = get_layout_for_page($content);
     *  var_dump($render_file ); //print full path to the layout file ex. /home/user/public_html/userfiles/templates/default/index.php
     * </code>
     * @package Content
     * @subpackage Advanced
     */
    public function get_layout($page = array())
    {


        $function_cache_id = '';
        if (is_array($page)) {
            ksort($page);
        }


        $function_cache_id = $function_cache_id . serialize($page);


        $cache_id = __FUNCTION__ . crc32($function_cache_id);
        $cache_group = 'content/global';
        if (!defined('ACTIVE_TEMPLATE_DIR')) {
            if (isset($page['id'])) {
                $this->define_constants($page);
            }
        }

        $cache_content = $this->app->cache->get($cache_id, $cache_group);

        if (($cache_content) != false) {
            return $cache_content;
        }


        $render_file = false;
        $look_for_post = false;
        $template_view_set_inner = false;
        $site_template_settings = $this->app->option->get('current_template', 'template');
        if (isset($page['active_site_template']) and ($page['active_site_template'] == 'default' or $page['active_site_template'] == 'mw_default')) {

            if ($site_template_settings != 'default' and $page['active_site_template'] == 'mw_default') {
                $page['active_site_template'] = 'default';
                $site_template_settings = 'default';
            }


            if ($site_template_settings != false) {
                $site_template_settings = str_replace('..', '', $site_template_settings);
                $site_template_settings_dir = TEMPLATES_DIR . $site_template_settings . DS;
                if (is_dir($site_template_settings_dir) != false) {
                    $page['active_site_template'] = $site_template_settings;
                }
            }

        }


        if (isset($page['active_site_template']) and isset($page['layout_file'])) {


            $page['layout_file'] = str_replace('___', DS, $page['layout_file']);
            $page['layout_file'] = str_replace('..', '', $page['layout_file']);
            $render_file_temp = TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'];
            $render_use_default = TEMPLATES_DIR . $page['active_site_template'] . DS . 'use_default_layouts.php';
            if (is_file($render_file_temp)) {
                $render_file = $render_file_temp;
            } elseif (is_file($render_use_default)) {
                $render_file_temp = DEFAULT_TEMPLATE_DIR . $page['layout_file'];

                if (is_file($render_file_temp)) {
                    $render_file = $render_file_temp;
                }
            }

        }
        if (isset($page['content_type'])) {
            $page['content_type'] = str_replace('..', '', $page['content_type']);

        }
        if (isset($page['subtype'])) {
            $page['subtype'] = str_replace('..', '', $page['subtype']);

        }

        if ($render_file == false and isset($page['content_type']) and isset($page['parent']) and ($page['content_type']) != 'page') {
            $get_layout_from_parent = false;
            $par = $this->get_by_id($page['parent']);


            if (isset($par['active_site_template']) and isset($par['layout_file']) and $par['layout_file'] != ''  and $par['layout_file'] != 'inherit') {
                $get_layout_from_parent = $par;
            } elseif (isset($par['is_home']) and isset($par['active_site_template']) and (!isset($par['layout_file']) or $par['layout_file'] == '')  and $par['is_home'] == 'y') {
                $par['layout_file'] = 'index.php';
                $get_layout_from_parent = $par;
            } else {
                $inh = $this->get_inherited_parent($page['parent']);

                if ($inh != false) {
                    $par = $this->get_by_id($inh);
                    if (isset($par['active_site_template']) and isset($par['layout_file']) and $par['layout_file'] != '') {
                        $get_layout_from_parent = $par;
                    } else if (isset($par['active_site_template']) and isset($par['is_home']) and $par['is_home'] == 'y' and  (!isset($par['layout_file']) or $par['layout_file'] == '')) {
                        $par['layout_file'] = 'index.php';
                        $get_layout_from_parent = $par;
                    }
                }
            }

            if (isset($get_layout_from_parent['active_site_template']) and isset($get_layout_from_parent['layout_file'])) {

                if ($get_layout_from_parent['active_site_template'] == 'default') {
                    $get_layout_from_parent['active_site_template'] = $site_template_settings;
                }

                if ($get_layout_from_parent['active_site_template'] == 'mw_default') {
                    $get_layout_from_parent['active_site_template'] = 'default';
                }


                $get_layout_from_parent['layout_file'] = str_replace('___', DS, $get_layout_from_parent['layout_file']);
                $get_layout_from_parent['layout_file'] = str_replace('..', '', $get_layout_from_parent['layout_file']);
                $render_file_temp = TEMPLATES_DIR . $get_layout_from_parent['active_site_template'] . DS . $get_layout_from_parent['layout_file'];
                $render_use_default = TEMPLATES_DIR . $get_layout_from_parent['active_site_template'] . DS . 'use_default_layouts.php';
                if (is_file($render_file_temp)) {
                    $render_file = $render_file_temp;
                } elseif (is_file($render_use_default)) {
                    $render_file_temp = DEFAULT_TEMPLATE_DIR . $get_layout_from_parent['layout_file'];
                    if (is_file($render_file_temp)) {
                        $render_file = $render_file_temp;
                    }
                }
            }
        }


        if ($render_file != false and isset($page['content_type']) and ($page['content_type']) != 'page') {

            $f1 = $render_file;
            $f2 = $render_file;

            $stringA = $f1;
            $stringB = "_inner";
            $length = strlen($stringA);
            $temp1 = substr($stringA, 0, $length - 4);
            $temp2 = substr($stringA, $length - 4, $length);
            $f1 = $temp1 . $stringB . $temp2;
            $f1 = normalize_path($f1, false);

            if (is_file($f1)) {
                $render_file = $f1;
            } else {
                $stringA = $f2;
                $stringB = '_' . $page['content_type'];
                $length = strlen($stringA);
                $temp1 = substr($stringA, 0, $length - 4);
                $temp2 = substr($stringA, $length - 4, $length);
                $f3 = $temp1 . $stringB . $temp2;
                $f3 = normalize_path($f3, false);

                if (is_file($f3)) {
                    $render_file = $f3;
                } else {
                    $found_subtype_layout = false;
                    if (isset($page['subtype'])) {
                        $stringA = $f2;
                        $stringB = '_' . $page['subtype'];
                        $length = strlen($stringA);
                        $temp1 = substr($stringA, 0, $length - 4);
                        $temp2 = substr($stringA, $length - 4, $length);
                        $f3 = $temp1 . $stringB . $temp2;
                        $f3 = normalize_path($f3, false);
                        if (is_file($f3)) {
                            $found_subtype_layout = true;
                            $render_file = $f3;
                        }
                    }


                    $check_inner = dirname($render_file);
                    if ($found_subtype_layout == false and is_dir($check_inner)) {


                        if (isset($page['subtype'])) {
                            $stringA = $check_inner;
                            $stringB = $page['subtype'] . '.php';
                            $length = strlen($stringA);
                            $f3 = $stringA . DS . $stringB;
                            $f3 = normalize_path($f3, false);

                            if (is_file($f3)) {
                                $found_subtype_layout = true;
                                $render_file = $f3;
                            }
                        }

                        if ($found_subtype_layout == false) {
                            $in_file = $check_inner . DS . 'inner.php';
                            $in_file = normalize_path($in_file, false);
                            $in_file2 = $check_inner . DS . $page['content_type'] . '.php';
                            $in_file2 = normalize_path($in_file2, false);
                            if (is_file($in_file2)) {
                                $render_file = $in_file2;
                            } elseif (is_file($in_file)) {
                                $render_file = $in_file;
                            }
                        }

                    }
                }
            }
        }

        if ($render_file == false and !isset($page['active_site_template']) and isset($page['layout_file'])) {
            $test_file = str_replace('___', DS, $page['layout_file']);
            $test_file = str_replace('..', '', $test_file);
            $render_file_temp = $test_file;
            if (is_file($render_file_temp)) {
                $render_file = $render_file_temp;
            }
        }


        if ($render_file == false and isset($page['active_site_template']) and isset($page['active_site_template']) and isset($page['layout_file']) and $page['layout_file'] != 'inherit'  and $page['layout_file'] != '') {
            $test_file = str_replace('___', DS, $page['layout_file']);
            $render_file_temp = TEMPLATES_DIR . $page['active_site_template'] . DS . $test_file;
            if (is_file($render_file_temp)) {
                $render_file = $render_file_temp;
            }
        }


        if ($render_file == false and isset($page['id']) and intval($page['id']) == 0) {
            $url_file = $this->app->url->string(1, 1);
            $test_file = str_replace('___', DS, $url_file);
            $render_file_temp = ACTIVE_TEMPLATE_DIR . DS . $test_file . '.php';
            $render_file_temp2 = ACTIVE_TEMPLATE_DIR . DS . $url_file . '.php';
            if (is_file($render_file_temp)) {
                $render_file = $render_file_temp;
            } elseif (is_file($render_file_temp2)) {
                $render_file = $render_file_temp2;
            }
        }

        if ($render_file == false and isset($page['id']) and isset($page['active_site_template']) and isset($page['layout_file']) and ($page['layout_file'] == 'inherit')) {

            /*   $inherit_from = array();
               $inh = $this->get_inherited_parent($page['id']);
               if($inh == false){

               } else {
                   $inherit_from[] =  $inh;
               }*/
            $inherit_from = $this->get_parents($page['id']);

            $found = 0;

            if (!empty($inherit_from)) {
                foreach ($inherit_from as $value) {
                    if ($found == 0 and $value != $page['id']) {
                        $par_c = $this->get_by_id($value);
                        if (isset($par_c['id']) and isset($par_c['active_site_template']) and isset($par_c['layout_file']) and $par_c['layout_file'] != 'inherit') {
                            $page['layout_file'] = $par_c['layout_file'];
                            $page['active_site_template'] = $par_c['active_site_template'];


                            if ($page['active_site_template'] == 'default') {
                                $page['active_site_template'] = $site_template_settings;
                            }

                            if ($page['active_site_template'] != 'default' and $page['active_site_template'] == 'mw_default') {
                                $page['active_site_template'] = 'default';
                            }


                            $render_file_temp = TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'];
                            $render_file_temp = normalize_path($render_file_temp, false);
                            if (is_file($render_file_temp)) {
                                $render_file = $render_file_temp;
                            } else {
                                $render_file_temp = DEFAULT_TEMPLATE_DIR . $page['layout_file'];
                                if (is_file($render_file_temp)) {
                                    $render_file = $render_file_temp;
                                }
                            }

                            $found = 1;
                        }
                    }
                }
            }
        }
        if ($render_file == false and isset($page['id']) and isset($page['active_site_template']) and isset($page['layout_file']) and ($page['layout_file'] != 'inherit')) {

            if ($page['active_site_template'] == 'default') {
                $page['active_site_template'] = $site_template_settings;
            }

            if ($page['active_site_template'] != 'default' and $page['active_site_template'] == 'mw_default') {
                $page['active_site_template'] = 'default';
            }


            $render_file_temp = TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'];
            $render_file_temp = normalize_path($render_file_temp, false);
            if (is_file($render_file_temp)) {
                $render_file = $render_file_temp;
            } else {
                $render_file_temp = DEFAULT_TEMPLATE_DIR . $page['layout_file'];
                if (is_file($render_file_temp)) {
                    $render_file = $render_file_temp;
                }
            }
        }


        if ($render_file == false and isset($page['content_type']) and $page['content_type'] != false and $page['content_type'] != '') {
            $look_for_post = $page;
            if (isset($page['parent'])) {
                $par_page = false;
                $inh_par_page = $this->get_inherited_parent($page['parent']);
                if ($inh_par_page != false) {
                    $par_page = $this->get_by_id($inh_par_page);
                } else {
                    $par_page = $this->get_by_id($page['parent']);
                }
                if (is_array($par_page)) {
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
            $simply_a_file3 = ACTIVE_TEMPLATE_DIR . 'layouts' . DS . $page['simply_a_file'];
            if ($render_file == false and  is_file($simply_a_file3) == true) {
                $render_file = $simply_a_file3;
            }

            if ($render_file == false and  is_file($simply_a_file2) == true) {
                $render_file = $simply_a_file2;
            }

            if ($render_file == false and is_file($page['simply_a_file']) == true) {
                $render_file = $page['simply_a_file'];
            }

        }
        if (!isset($page['active_site_template'])) {
            $page['active_site_template'] = ACTIVE_SITE_TEMPLATE;
        }
        if ($render_file == false and isset($page['active_site_template']) and trim($page['active_site_template']) != 'default') {
            $use_default_layouts = TEMPLATES_DIR . $page['active_site_template'] . DS . 'use_default_layouts.php';
            if (is_file($use_default_layouts)) {
                $page['active_site_template'] = 'default';
            }
        }

        if ($render_file == false and isset($page['active_site_template']) and isset($page['layout_file']) and trim($page['layout_file']) == '') {
            $use_index = TEMPLATES_DIR . $page['active_site_template'] . DS . 'index.php';
            if (is_file($use_index)) {
                $render_file = $use_index;
            }
        }

        if ($render_file == false and isset($page['active_site_template']) and isset($page['content_type']) and $render_file == false and !isset($page['layout_file'])) {
            $layouts_list = $this->app->layouts->scan('site_template=' . $page['active_site_template']);
            if (is_array($layouts_list)) {
                foreach ($layouts_list as $layout_item) {
                    if ($render_file == false and isset($layout_item['content_type']) and isset($layout_item['layout_file']) and $page['content_type'] == $layout_item['content_type']) {
                        $page['layout_file'] = $layout_item['layout_file'];
                        $render_file = TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'];
                    }
                }
            }
        }


        if ($render_file == false and isset($page['active_site_template']) and isset($page['layout_file'])) {
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
                } else {
                    if (trim($page['active_site_template']) != 'default') {
                        $use_default_layouts = TEMPLATES_DIR . $page['active_site_template'] . DS . 'use_default_layouts.php';
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

        if ($render_file == false and $template_view_set_inner != false) {

            if (isset($template_view_set_inner2)) {
                $template_view_set_inner2 = normalize_path($template_view_set_inner2, false);
                if (is_file($template_view_set_inner2) == true) {
                    $render_file = $template_view_set_inner2;
                }
            }

            $template_view_set_inner = normalize_path($template_view_set_inner, false);
            if ($render_file == false and is_file($template_view_set_inner) == true) {
                $render_file = $template_view_set_inner;
            }


        }

        if ($render_file != false  and isset($page['custom_view'])) {
            $check_custom = dirname($render_file) . DS;
            $check_custom_parent = dirname($render_file) . DS;

            $cv = trim($page['custom_view']);
            $cv = str_replace('..', '', $cv);
            $cv = str_ireplace('.php', '', $cv);
            $check_custom_f = $check_custom . $cv . '.php';

            if (is_file($check_custom_f)) {
                $render_file = $check_custom_f;
            }

        }
        if ($render_file == false and isset($page['layout_file']) and ($page['layout_file']) != false) {
            $template_view = ACTIVE_TEMPLATE_DIR . DS . $page['layout_file'];
            $template_view = normalize_path($template_view, false);

            if (is_file($template_view) == true) {
                $render_file = $template_view;
            }
        }
        $this->app->cache->save($render_file, $cache_id, $cache_group);

        return $render_file;
    }

    /**
     * Defines all constants that are needed to parse the page layout
     *
     * It accepts array or $content that must have  $content['id'] set
     *
     * @example
     * <code>
     *  Define constants for some page
     *  $ref_page = $this->get_by_id(1);
     *  $this->define_constants($ref_page);
     *  print PAGE_ID;
     *  print POST_ID;
     *  print CATEGORY_ID;
     *  print MAIN_PAGE_ID;
     *  print DEFAULT_TEMPLATE_DIR;
     *  print DEFAULT_TEMPLATE_URL;
     * </code>
     *
     * @package Content
     * @subpackage Advanced
     * @const  PAGE_ID Defines the current page id
     * @const  POST_ID Defines the current post id
     * @const  CATEGORY_ID Defines the current category id if any
     * @const  ACTIVE_PAGE_ID Same as PAGE_ID
     * @const  CONTENT_ID current post or page id
     * @const  MAIN_PAGE_ID the parent page id
     * @const DEFAULT_TEMPLATE_DIR the directory of the site's default template
     * @const DEFAULT_TEMPLATE_URL the url of the site's default template
     */
    public function define_constants($content = false)
    {


        if ($content == false) {
            if (isset($_SERVER['HTTP_REFERER'])) {

                $ref_page = $_SERVER['HTTP_REFERER'];

                if ($ref_page != '') {
                    $ref_page = $this->get_by_url($ref_page);
                    if (!empty($ref_page)) {
                        $content = $ref_page;

                    }
                }
            }
        }

//}
        $page = false;
        if (is_array($content)) {
            if (!isset($content['active_site_template']) and isset($content['id']) and $content['id'] != 0) {
                $content = $this->get_by_id($content['id']);
                $page = $content;

            } else if (isset($content['id']) and $content['id'] == 0) {
                $page = $content;
            } else if (isset($content['active_site_template'])) {
                $page = $content;
            }

            if ($page == false) {
                $page = $content;
            }

        }

        if (is_array($page)) {
            if (isset($page['content_type']) and $page['content_type'] == "post") {


                if (isset($page['id']) and $page['id'] != 0) {
                    $content = $page;


                    $current_categorys = $this->app->category->get_for_content($page['id']);
                    if (!empty($current_categorys)) {
                        $current_category = array_shift($current_categorys);
                        if (defined('CATEGORY_ID') == false and isset($current_category['id'])) {
                            define('CATEGORY_ID', $current_category['id']);
                        }


                    }

                    $page = $this->get_by_id($page['parent']);

                    if (defined('POST_ID') == false) {
                        define('POST_ID', $content['id']);
                    }

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


            if (!defined('CATEGORY_ID')) {
                //define('CATEGORY_ID', $current_category['id']);
            }

            if (defined('CATEGORY_ID') == false) {
                $cat_url = $this->app->url->param('category', $skip_ajax = true);
                if ($cat_url != false) {
                    define('CATEGORY_ID', intval($cat_url));
                }
            }
            if (!defined('CATEGORY_ID')) {
                define('CATEGORY_ID', false);
            }

            if (defined('CONTENT_ID') == false) {
                define('CONTENT_ID', $content['id']);
            }

            if (defined('PAGE_ID') == false) {
                define('PAGE_ID', $page['id']);
            }
            if (isset($page['parent'])) {


                $parent_page_check_if_inherited = $this->get_by_id($page['parent']);

                if (isset($parent_page_check_if_inherited["layout_file"]) and $parent_page_check_if_inherited["layout_file"] == 'inherit') {

                    $inherit_from_id = $this->get_inherited_parent($parent_page_check_if_inherited["id"]);

                    if (defined('MAIN_PAGE_ID') == false) {
                        define('MAIN_PAGE_ID', $inherit_from_id);
                    }

                }

                //$root_parent = $this->get_inherited_parent($page['parent']);
                //  d($root_parent);

                //  $this->get_inherited_parent($page['id']);
                // if ($par_page != false) {
                //  $par_page = $this->get_by_id($page['parent']);
                //  }
                if (defined('ROOT_PAGE_ID') == false) {

                    $root_page = $this->get_parents($page['id']);
                    if (!empty($root_page) and isset($root_page[0])) {
                        $root_page[0] = end($root_page);
                    } else {
                        $root_page[0] = $page['parent'];
                    }

                    define('ROOT_PAGE_ID', $root_page[0]);
                }

                if (defined('MAIN_PAGE_ID') == false) {
                    if ($page['parent'] == 0) {
                        define('MAIN_PAGE_ID', $page['id']);
                    } else {
                        define('MAIN_PAGE_ID', $page['parent']);
                    }

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

        if (isset($content) and isset($content['active_site_template']) and ($content['active_site_template']) != '' and strtolower($page['active_site_template']) != 'inherit' and strtolower($page['active_site_template']) != 'default') {

            $the_active_site_template = $content['active_site_template'];
        } else if (isset($page) and isset($page['active_site_template']) and ($page['active_site_template']) != '' and strtolower($page['active_site_template']) != 'default') {

            $the_active_site_template = $page['active_site_template'];
        } else if (isset($content) and isset($content['active_site_template']) and ($content['active_site_template']) != '' and strtolower($content['active_site_template']) != 'default') {

            $the_active_site_template = $content['active_site_template'];
        } else {
            $the_active_site_template = $this->app->option->get('current_template', 'template');
            //
        }

        if (isset($the_active_site_template) and $the_active_site_template != 'default' and $the_active_site_template == 'mw_default') {
            $the_active_site_template = 'default';
        }


        if ($the_active_site_template == false) {
            $the_active_site_template = 'default';
        }

        if (defined('THIS_TEMPLATE_DIR') == false and $the_active_site_template != false) {

            define('THIS_TEMPLATE_DIR', MW_TEMPLATES_DIR . $the_active_site_template . DS);

        }

        if (defined('THIS_TEMPLATE_FOLDER_NAME') == false and $the_active_site_template != false) {

            define('THIS_TEMPLATE_FOLDER_NAME', $the_active_site_template);

        }

        $the_active_site_template_dir = normalize_path(MW_TEMPLATES_DIR . $the_active_site_template . DS);

        if (defined('DEFAULT_TEMPLATE_DIR') == false) {

            define('DEFAULT_TEMPLATE_DIR', MW_TEMPLATES_DIR . 'default' . DS);
        }

        if (defined('DEFAULT_TEMPLATE_URL') == false) {

            define('DEFAULT_TEMPLATE_URL', MW_USERFILES_URL . '/' . MW_TEMPLATES_FOLDER_NAME . '/default/');
        }


        if (trim($the_active_site_template) != 'default') {

            if ((!strstr($the_active_site_template, DEFAULT_TEMPLATE_DIR))) {
                $use_default_layouts = $the_active_site_template_dir . 'use_default_layouts.php';
                if (is_file($use_default_layouts)) {
                    //$render_file = ($use_default_layouts);
                    //if()
                    //
                    //

                    if (isset($page['layout_file'])) {
                        $template_view = DEFAULT_TEMPLATE_DIR . $page['layout_file'];
                    } else {
                        $template_view = DEFAULT_TEMPLATE_DIR;
                    }
                    if (isset($page)) {
                        if (!isset($page['layout_file']) or (isset($page['layout_file']) and $page['layout_file'] == 'inherit' or $page['layout_file'] == '')) {
                            $par_page = $this->get_inherited_parent($page['id']);
                            if ($par_page != false) {
                                $par_page = $this->get_by_id($par_page);
                            }
                            if (isset($par_page['layout_file'])) {
                                $the_active_site_template = $par_page['active_site_template'];
                                $page['layout_file'] = $par_page['layout_file'];
                                $page['active_site_template'] = $par_page['active_site_template'];
                                $template_view = MW_TEMPLATES_DIR . $page['active_site_template'] . DS . $page['layout_file'];


                            }

                        }
                    }

                    if (is_file($template_view) == true) {

                        if (defined('THIS_TEMPLATE_DIR') == false) {

                            define('THIS_TEMPLATE_DIR', MW_TEMPLATES_DIR . $the_active_site_template . DS);

                        }
                        if (defined('THIS_TEMPLATE_URL') == false) {
                            $the_template_url = MW_USERFILES_URL . '/' . MW_TEMPLATES_FOLDER_NAME . '/' . $the_active_site_template;

                            $the_template_url = $the_template_url . '/';
                            if (defined('THIS_TEMPLATE_URL') == false) {
                                define("THIS_TEMPLATE_URL", $the_template_url);
                            }
                            if (defined('TEMPLATE_URL') == false) {
                                define("TEMPLATE_URL", $the_template_url);
                            }
                        }
                        $the_active_site_template = 'default';
                        $the_active_site_template_dir = DEFAULT_TEMPLATE_DIR;

                        //	d($the_active_site_template_dir);
                    }


                }
            }

        }

        if (defined('ACTIVE_TEMPLATE_DIR') == false) {

            define('ACTIVE_TEMPLATE_DIR', $the_active_site_template_dir);
        }

        if (defined('THIS_TEMPLATE_DIR') == false) {

            define('THIS_TEMPLATE_DIR', $the_active_site_template_dir);
        }

        if (defined('THIS_TEMPLATE_URL') == false) {
            $the_template_url = MW_USERFILES_URL . '/' . MW_TEMPLATES_FOLDER_NAME . '/' . $the_active_site_template;

            $the_template_url = $the_template_url . '/';
            if (defined('THIS_TEMPLATE_URL') == false) {
                define("THIS_TEMPLATE_URL", $the_template_url);
            }
        }
        if (defined('TEMPLATE_NAME') == false) {

            define('TEMPLATE_NAME', $the_active_site_template);
        }


        if (defined('TEMPLATE_DIR') == false) {

            define('TEMPLATE_DIR', $the_active_site_template_dir);
        }

        if (defined('ACTIVE_SITE_TEMPLATE') == false) {

            define('ACTIVE_SITE_TEMPLATE', $the_active_site_template);
        }

        if (defined('TEMPLATES_DIR') == false) {

            define('TEMPLATES_DIR', MW_TEMPLATES_DIR);
        }

        $the_template_url = MW_USERFILES_URL . '/' . MW_TEMPLATES_FOLDER_NAME . '/' . $the_active_site_template;

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

            $layouts_url = reduce_double_slashes($this->app->url->link_to_file($layouts_dir) . '/');

            define("LAYOUTS_URL", $layouts_url);
        }


        return true;
    }

    public function get_by_url($url = '', $no_recursive = false)
    {
        if (strval($url) == '') {

            $url = $this->app->url->string();
        }

        $u1 = $url;
        $u2 = $this->app->url->site();

        $u1 = rtrim($u1, '\\');
        $u1 = rtrim($u1, '/');

        $u2 = rtrim($u2, '\\');
        $u2 = rtrim($u2, '/');
        $u1 = str_replace($u2, '', $u1);
        $u1 = ltrim($u1, '/');
        $url = $u1;
        $table = MW_DB_TABLE_CONTENT;

        $url = $this->app->db->escape_string($url);
        $url = addslashes($url);

        $url12 = parse_url($url);
        if (isset($url12['scheme']) and isset($url12['host']) and isset($url12['path'])) {

            $u1 = $this->app->url->site();
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

        global $mw_skip_pages_starting_with_url;

        if (1 !== stripos($url, 'http://') && 1 !== stripos($url, 'https://')) {
            // $url = 'http://' . $url;
            // return false;

        }
        if (defined('MW_BACKEND')) {
            return false;

        }
        if (is_array($mw_skip_pages_starting_with_url)) {
            $segs = explode('/', $url);

            foreach ($mw_skip_pages_starting_with_url as $skip_page_url) {
                if (in_array($skip_page_url, $segs)) {
                    return false;
                }

            }

        }


        global $mw_precached_links;


        $link_hash = 'link' . crc32($url);

        if (isset($mw_precached_links[$link_hash])) {
            return $mw_precached_links[$link_hash];
        }


        $sql = "SELECT id FROM $table WHERE url='{$url}'   ORDER BY updated_on DESC LIMIT 0,1 ";

        $q = $this->app->db->query($sql, __FUNCTION__ . crc32($sql), 'content/global');

        $result = $q;

        $content = $result[0];

        if (!empty($content)) {

            $mw_precached_links[$link_hash] = $content;
            return $content;
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
                        $url = $this->get_by_url($test[0], true);
                    }
                    if (!empty($url)) {
                        $mw_precached_links[$link_hash] = $url;
                        return $url;
                    }


                }
            }
        } else {

            if (isset($content['id']) and intval($content['id']) != 0) {
                $content['id'] = ((int)$content['id']);
            }
            //$get_by_id = $this->get_by_id($content['id']);
            $mw_precached_links[$link_hash] = $content;
            return $content;
        }
        $mw_precached_links[$link_hash] = false;
        return false;
    }

    /**
     *  Get the first parent that has layout
     *
     * @category Content
     * @package Content
     * @subpackage Advanced
     * @uses $this->get_parents()
     * @uses $this->get_by_id()
     */
    public function get_inherited_parent($content_id)
    {
        $inherit_from = $this->get_parents($content_id);
        $found = 0;
        if (!empty($inherit_from)) {
            foreach ($inherit_from as $value) {
                if ($found == 0) {
                    $par_c = $this->get_by_id($value);
                    if (isset($par_c['id']) and isset($par_c['active_site_template']) and isset($par_c['layout_file']) and $par_c['layout_file'] != 'inherit') {
                        return $par_c['id'];
                        $found = 1;
                    }
                }
            }
        }

    }

    public function get_parents($id = 0, $without_main_parrent = false)
    {

        if (intval($id) == 0) {

            return FALSE;
        }

        $table = MW_DB_TABLE_CONTENT;

        $ids = array();

        $data = array();

        if (isset($without_main_parrent) and $without_main_parrent == true) {

            $with_main_parrent_q = " and parent<>0 ";
        } else {

            $with_main_parrent_q = false;
        }
        $id = intval($id);
        $q = " SELECT id, parent FROM $table WHERE id ={$id} " . $with_main_parrent_q;

        $content_parents = $this->app->db->query($q, $cache_id = __FUNCTION__ . crc32($q), $cache_group = 'content/' . $id);

        if (!empty($content_parents)) {

            foreach ($content_parents as $item) {

                if (intval($item['id']) != 0) {

                    $ids[] = $item['parent'];
                }
                if ($item['parent'] != $item['id'] and intval($item['parent'] != 0)) {
                    $next = $this->get_parents($item['parent'], $without_main_parrent);

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

    public function get_children($id = 0, $without_main_parrent = false)
    {

        if (intval($id) == 0) {

            return FALSE;
        }

        $table = MW_DB_TABLE_CONTENT;

        $ids = array();

        $data = array();

        if (isset($without_main_parrent) and $without_main_parrent == true) {

            $with_main_parrent_q = " and parent<>0 ";
        } else {

            $with_main_parrent_q = false;
        }
        $id = intval($id);
        $q = " SELECT id, parent FROM $table WHERE parent={$id} " . $with_main_parrent_q;

        $taxonomies = $this->app->db->query($q, $cache_id = __FUNCTION__ . crc32($q), $cache_group = 'content/' . $id);


        if (!empty($taxonomies)) {

            foreach ($taxonomies as $item) {

                if (intval($item['id']) != 0) {

                    $ids[] = $item['id'];
                }
                if ($item['parent'] != $item['id'] and intval($item['parent'] != 0)) {
                    $next = $this->get_children($item['id'], $without_main_parrent);

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

    public function data($content_id, $field_name = false)
    {


        $table = MW_DB_TABLE_CONTENT_DATA;


        $data = array();


        $data['table'] = $table;
        $data['cache_group'] = 'content_data';

        $data['content_id'] = intval($content_id);
        $res = array();
        $get = $this->app->db->get($data);
        if (!empty($get)) {
            foreach ($get as $item) {
                if (isset($item['field_name']) and isset($item['field_value'])) {
                    $res[$item['field_name']] = $item['field_value'];
                }
            }
        }
        if (!empty($res)) {
            return $res;
        }
        return $get;

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
     * @param $params['num'] = 5; //the numer of pages
     * @internal param $display =
     *            'default' //sets the default paging display with <ul> and </li>
     *            tags. If $display = false, the function will return the paging
     *            array which is the same as $posts_pages_links in every template
     *
     * @return string - html string with ul/li
     */
    public function paging($params)
    {
        $params = parse_params($params);

        $pages_count = 1;
        $base_url = false;
        $paging_param = 'curent_page';
        $keyword_param = 'keyword_param';
        $class = 'pagination';
        if (isset($params['num'])) {
            $pages_count = $params['num'];
        }


        if (isset($params['num'])) {
            $pages_count = $params['num'];
        }


        if (isset($params['class'])) {
            $class = $params['class'];
        }

        if (isset($params['paging_param'])) {
            $paging_param = $params['paging_param'];
        }
        $curent_page_from_url = $this->app->url->param($paging_param);

        if (isset($params['curent_page'])) {
            $curent_page_from_url = $params['curent_page'];
        }

        $data = $this->paging_links($base_url, $pages_count, $paging_param, $keyword_param);
        if (is_array($data)) {
            $to_print = "<div class='{$class}-holder' ><ul class='{$class}'>";
            foreach ($data as $key => $value) {
                $act_class = '';

                if ($curent_page_from_url != false) {
                    if (intval($curent_page_from_url) == intval($key)) {
                        $act_class = ' class="active" ';
                    }
                }
                $to_print .= "<li {$act_class} data-page-number=\"$key\">";
                $to_print .= "<a {$act_class} href=\"$value\" data-page-number=\"$key\">$key</a> ";
                $to_print .= "</li>";
            }
            $to_print .= "</ul></div>";
            return $to_print;
        }


    }

    public function paging_links($base_url = false, $pages_count, $paging_param = 'curent_page', $keyword_param = 'keyword')
    {


        if ($base_url == false) {

            if ($this->app->url->is_ajax() == false) {
                $base_url = $this->app->url->current(1);

            } else {
                if ($_SERVER['HTTP_REFERER'] != false) {
                    $base_url = $_SERVER['HTTP_REFERER'];
                }
            }


        }

        $page_links = array();


        $the_url = $base_url;

        $append_to_links = '';
        if (strpos($the_url, '?')) {
            $the_url = substr($the_url, 0, strpos($the_url, '?'));


        }
        $in_empty_url = false;
        if ($the_url == site_url()) {
            $in_empty_url = 1;
        }


        $the_url = explode('/', $the_url);


        for ($x = 1; $x <= $pages_count; $x++) {


            $new = array();

            foreach ($the_url as $itm) {

                $itm = explode(':', $itm);

                if ($itm[0] == $paging_param) {

                    $itm[1] = $x;
                }

                $new[] = implode(':', $itm);
            }

            $new_url = implode('/', $new);


            $page_links[$x] = $new_url . $append_to_links;
        }


        for ($x = 1; $x <= count($page_links); $x++) {

            if (stristr($page_links[$x], $paging_param . ':') == false) {
                if ($in_empty_url == false) {
                    $l = reduce_double_slashes($page_links[$x] . '/' . $paging_param . ':' . $x);
                } else {
                    $l = reduce_double_slashes($page_links[$x] . '?' . $paging_param . ':' . $x);

                }
                $l = str_ireplace('module/', '', $l);
                $page_links[$x] = $l . $append_to_links;
            }
        }

        return $page_links;
    }

    /**
     * Print nested tree of pages
     *
     * @example
     * <pre>
     * // Example Usage:
     * $pt_opts = array();
     * $pt_opts['link'] = "{title}";
     * $pt_opts['list_tag'] = "ol";
     * $pt_opts['list_item_tag'] = "li";
     * pages_tree($pt_opts);
     * </pre>
     *
     * @example
     * <pre>
     * // Example Usage to make <select> with <option>:
     * $pt_opts = array();
     * $pt_opts['link'] = "{title}";
     * $pt_opts['list_tag'] = " ";
     * $pt_opts['list_item_tag'] = "option";
     * $pt_opts['active_ids'] = $data['parent'];
     * $pt_opts['active_code_tag'] = '   selected="selected"  ';
     * $pt_opts['ul_class'] = 'nav';
     * $pt_opts['li_class'] = 'nav-item';
     *  pages_tree($pt_opts);
     * </pre>
     * @example
     * <pre>
     * // Other options
     * $pt_opts['parent'] = "8";
     * $pt_opts['include_first'] =  true; //includes the parent in the tree
     * $pt_opts['id_prefix'] = 'my_id';
     * </pre>
     *
     *
     *
     * @package Content
     * @param int $parent
     * @param bool $link
     * @param bool $active_ids
     * @param bool $active_code
     * @param bool $remove_ids
     * @param bool $removed_ids_code
     * @param bool $ul_class_name
     * @param bool $include_first
     * @return sting Prints the pages tree
     */
    public function pages_tree($parent = 0, $link = false, $active_ids = false, $active_code = false, $remove_ids = false, $removed_ids_code = false, $ul_class_name = false, $include_first = false)
    {

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
        if (!defined('CONTENT_ID')) {
            $this->define_constants();
        }
        $function_cache_id = false;
        $args = func_get_args();
        foreach ($args as $k => $v) {
            $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
        }
        $function_cache_id = __FUNCTION__ . crc32($function_cache_id) . PAGE_ID . $parent;
        if ($parent == 0) {
            $cache_group = 'content/global';
        } else {
            //$cache_group = 'content/' . $parent;
            $cache_group = 'categories/global';
        }
        if (isset($include_categories) and $include_categories == true) {
            $cache_group = 'categories/global';
        }


        $nest_level = 0;

        if (isset($params['nest_level'])) {
            $nest_level = $params['nest_level'];
        }

        $nest_level_orig = $nest_level;
        //

        if ($nest_level_orig == 0) {

            $cache_content = $this->app->cache->get($function_cache_id, $cache_group);
            //   $cache_content = false;


            if (isset($params['no_cache'])) {
                $cache_content = false;
            }

            if (($cache_content) != false) {

                if (isset($params['return_data'])) {
                    return $cache_content;
                } else {
                    print $cache_content;
                }

                return;
                //  return $cache_content;
            }

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
            $max_level = $params['max_level'] = $params['maxdepth'];
        } else if (isset($params['depth'])) {
            $max_level = $params['max_level'] = $params['depth'];
        }

        if ($max_level != false) {

            if (intval($nest_level) >= intval($max_level)) {
                print '';
                return;
            }
        }


        $is_shop = '';
        if (isset($params['is_shop'])) {
            $is_shop = $this->app->db->escape_string($params['is_shop']);
            $is_shop = " and is_shop='{$is_shop} '";
            $include_first = false;

        }
        $ul_class = 'pages_tree';
        if (isset($params['ul_class'])) {

            $ul_class_name = $ul_class = $params['ul_class'];
        }

        $li_class = 'pages_tree_item';
        if (isset($params['li_class'])) {

            $li_class = $params['li_class'];
        }
        if (isset($params['ul_tag'])) {

            $list_tag = $params['ul_tag'];
        }
        if (isset($params['li_tag'])) {

            $list_item_tag = $params['li_tag'];
        }
        if (isset($params['include_categories'])) {

            $include_categories = $params['include_categories'];
        }


        ob_start();


        $table = MW_DB_TABLE_CONTENT;
        $par_q = '';
        if ($parent == false) {

            $parent = (0);
        } else {
            $par_q = " parent=$parent    and  ";

        }


        if ($include_first == true) {
            $sql = "SELECT * from $table where  id={$parent}    and   is_deleted='n' and content_type='page' " . $is_shop . "  order by position desc  limit 0,1";
            //
        } else {

            //$sql = "SELECT * from $table where  parent=$parent    and content_type='page'  order by updated_on desc limit 0,1";
            $sql = "SELECT * from $table where  " . $par_q . "  content_type='page' and   is_deleted='n' $is_shop  order by position desc limit 0,100";
        }

        //$sql = "SELECT * from $table where  parent=$parent    and content_type='page'  order by updated_on desc limit 0,1000";

        $cid = __FUNCTION__ . crc32($sql);
        $cidg = 'content/' . $parent;

        //$q = $this->app->db->query($sql, $cid, $cidg);
        if (!is_array($params)) {
            $params = array();
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
        if (isset($active_ids)) {
            $active_ids = $active_ids;
        }


        if (isset($active_ids) and is_string($active_ids)) {
            $active_ids = explode(',', $active_ids);
        }
        $the_active_class = 'active';
        if (isset($params['active_class'])) {
            $the_active_class = $params['active_class'];
        }


        $params['content_type'] = 'page';

        $include_first_set = false;
        if ($include_first == true) {
            $include_first_set = 1;
            $include_first = false;
            $include_first_set = $parent;
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

        if (isset($params['is_shop']) and $params['is_shop'] == 'y') {
            if (isset($params['parent']) and $params['parent'] == 0) {
                unset($params['parent']);
            }

            if (isset($params['parent']) and $params['parent'] == 'any') {
                unset($params['parent']);

            }

        } else {

            if (isset($params['parent']) and $params['parent'] == 'any') {
                $params['parent'] = 0;

            }


        }


        $params['limit'] = 500;
        $params['orderby'] = 'position desc';

        $params['curent_page'] = 1;

        $params['is_deleted'] = 'n';
        $params['cache_group'] = false;
        $params['no_cache'] = true;
        $skip_pages_from_tree = false;
        $params2 = $params;

        if (isset($params['is_shop']) and $params['is_shop'] == 'y') {

            //$max_level = $params2['max_level'] =2;
            // $skip_pages_from_tree = 1;
            //	unset($params2['parent']);
//d($params2);


        }
        if (isset($params2['id'])) {
            unset($params2['id']);
        }
        if (isset($params2['link'])) {
            unset($params2['link']);
        }

        if ($include_first_set != false) {
            $q = $this->get("id=" . $include_first_set);
            //   $q = $this->get_by_id("id=" . $include_first_set);
        } else {
            $q = $this->get($params2);

        }


//        if (isset($params['home_first'])) {
//
//            $home_first = $params['home_first'];
//            unset($params['home_first']);
//            $hp = $this->homepage();
//
//            if($q != false and !empty($q) and !empty($hp)){
//                array_push($hp, $q);
//
//            }
//        }


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
                if (is_array($item) != false and isset($item['title']) and $item['title'] != null) {
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


                        //$content_type_li_class .=' ' .$item ['layout_file'];

                        if (isset($item ['layout_file']) and stristr($item ['layout_file'], 'blog')) {
                            $content_type_li_class = ' is_blog';

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


                        $to_pr_2 = "<{$list_item_tag} class='{$li_class} $content_type_li_class {active_class} {active_parent_class} depth-{$nest_level} item_{$iid} {exteded_classes} menu-item-id-{$item['id']}' data-page-id='{$item['id']}' value='{$item['id']}'  data-item-id='{$item['id']}'  {active_code_tag} data-parent-page-id='{$item['parent']}' {$st_str} {$st_str2} {$st_str3}  title='" . addslashes($item['title']) . "' >";

                        if ($link != false) {


                            $active_parent_class = '';
                            //if(isset($item['parent']) and intval($item['parent']) != 0){
                            if (intval($item['parent']) != 0 and intval($item['parent']) == intval(MAIN_PAGE_ID)) {
                                $active_parent_class = 'active-parent';
                            } elseif (intval($item['id']) == intval(MAIN_PAGE_ID)) {
                                $active_parent_class = 'active-parent';
                            } else {
                                $active_parent_class = '';
                            }

                            //}
                            if ($item['id'] == CONTENT_ID) {
                                $active_class = 'active';
                            } elseif (isset($active_ids) and !is_array($active_ids) and $item['id'] == $active_ids) {
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
                            if ($res_count == 0) {
                                $ext_classes .= ' first-child ';
                                $ext_classes .= ' child-' . $res_count . '';
                            } else if (!isset($result[$res_count + 1])) {
                                $ext_classes .= ' last-child';
                                $ext_classes .= ' child-' . $res_count . '';
                            } else {
                                $ext_classes .= ' child-' . $res_count . '';
                            }

                            if (isset($item['parent']) and intval($item['parent']) > 0) {
                                $ext_classes .= ' have-parent';
                            }


                            if (isset($item['subtype_value']) and intval($item['subtype_value']) != 0) {
                                $ext_classes .= ' have-category';
                            }

                            if (isset($item['is_active']) and $item['is_active'] == 'n') {

                                $ext_classes = $ext_classes . ' content-unpublished ';
                            }

                            $ext_classes = trim($ext_classes);
                            $the_active_class = $active_class;


                            $to_print = str_replace('{id}', $item['id'], $link);
                            $to_print = str_replace('{active_class}', $active_class, $to_print);
                            $to_print = str_replace('{active_parent_class}', $active_parent_class, $to_print);
                            $to_print = str_replace('{exteded_classes}', $ext_classes, $to_print);
                            $to_pr_2 = str_replace('{exteded_classes}', $ext_classes, $to_pr_2);
                            $to_pr_2 = str_replace('{active_class}', $active_class, $to_pr_2);
                            $to_pr_2 = str_replace('{active_parent_class}', $active_parent_class, $to_pr_2);


                            $to_print = str_replace('{title}', $item['title'], $to_print);

                            $to_print = str_replace('{nest_level}', 'depth-' . $nest_level, $to_print);
                            if (strstr($to_print, '{link}')) {
                                $to_print = str_replace('{link}', page_link($item['id']), $to_print);
                            }
                            $empty1 = intval($nest_level);
                            $empty = '';
                            for ($i1 = 0; $i1 < $empty1; $i1++) {
                                $empty = $empty . '&nbsp;&nbsp;';
                            }
                            $to_print = str_replace('{empty}', $empty, $to_print);


                            if (strstr($to_print, '{tn}')) {
                                $to_print = str_replace('{tn}', thumbnail($item['id'], 'original'), $to_print);
                            }
                            foreach ($item as $item_k => $item_v) {
                                $to_print = str_replace('{' . $item_k . '}', $item_v, $to_print);
                            }
                            $res_count++;
                            if (isset($active_ids) and is_array($active_ids) == true) {
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
                            } else if (isset($active_ids) and !is_array($active_ids)) {
                                if (intval($item['id']) == intval($active_ids)) {
                                    $is_there_active_ids = true;
                                    $to_print = str_ireplace('{active_code}', $active_code, $to_print);
                                    $to_print = str_ireplace('{active_class}', $the_active_class, $to_print);
                                    $to_pr_2 = str_ireplace('{active_class}', $the_active_class, $to_pr_2);
                                    $to_pr_2 = str_ireplace('{active_code_tag}', $active_code_tag, $to_pr_2);
                                }
                            }


                            $to_print = str_ireplace('{active_code}', '', $to_print);
                            $to_print = str_ireplace('{active_class}', '', $to_print);
                            $to_pr_2 = str_ireplace('{active_class}', '', $to_pr_2);
                            $to_pr_2 = str_ireplace('{active_code_tag}', '', $to_pr_2);


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
                            $to_pr_2 = str_replace('{active_parent_class}', '', $to_pr_2);


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
                                $params['ul_class'] = $params['ul_class_deep'];
                            }

                            if (isset($maxdepth)) {
                                $params['maxdepth'] = $maxdepth;
                            }


                            if (isset($params['li_class_deep'])) {
                                $params['li_class'] = $params['li_class_deep'];
                            }

                            if (isset($params['return_data'])) {
                                unset($params['return_data']);
                            }

                            if ($skip_pages_from_tree == false) {

                                $children = $this->pages_tree($params);
                            }
                        } else {
                            if ($skip_pages_from_tree == false) {

                                $children = $this->pages_tree(intval($item['id']), $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name = false);
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

                            if (isset($categores_link)) {
                                $cat_params['link'] = $categores_link;

                            } else {
                                $cat_params['link'] = $link;
                            }

                            if (isset($categories_active_ids)) {
                                $cat_params['active_ids'] = $categories_active_ids;

                            }


                            if (isset($categories_removed_ids)) {
                                $cat_params['remove_ids'] = $categories_removed_ids;

                            }

                            if (isset($active_code)) {
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


                            if ($nest_level > 1) {
                                if (isset($params['ul_class_deep'])) {
                                    $cat_params['ul_class'] = $params['ul_class_deep'];
                                }


                                if (isset($params['li_class_deep'])) {
                                    $cat_params['li_class'] = $params['li_class_deep'];
                                }

                            } else {


                                if (isset($params['ul_class'])) {
                                    $cat_params['ul_class'] = $params['ul_class'];
                                }


                                if (isset($params['li_class'])) {
                                    $cat_params['li_class'] = $params['li_class'];
                                }


                            }

                            $this->app->category->tree($cat_params);

                        }
                    }
                    print "</{$list_item_tag}>";
                }
            }


            if (trim($list_tag) != '') {
                print "</{$list_tag}>";
            }
        } else {

        }

        $content = ob_get_contents();
        if ($nest_level_orig == 0) {
            $this->app->cache->save($content, $function_cache_id, $cache_group);
        }
        ob_end_clean();

        if (isset($params['return_data'])) {
            return $content;
        } else {
            print $content;
        }
        return false;
    }

    public function get_menu($params = false)
    {

        $table = MODULE_DB_MENUS;

        $params2 = array();
        if ($params == false) {
            $params = array();
        }
        if (is_string($params)) {
            $params = parse_str($params, $params2);
            $params = $params2;
        }

        //$table = MODULE_DB_SHOP_ORDERS;
        $params['table'] = $table;
        $params['item_type'] = 'menu';
        //$params['debug'] = 'menu';
        $menus = $this->app->db->get($params);
        if (!empty($menus)) {
            return $menus;
        } else {

            if (!defined("MW_MENU_IS_ALREADY_MADE_ONCE")) {
                if (isset($params['make_on_not_found']) and ($params['make_on_not_found']) == true and isset($params['title'])) {
                    $this->menu_create('id=0&title=' . $params['title']);
                }
                define('MW_MENU_IS_ALREADY_MADE_ONCE', true);
            }


        }

    }

    public function menu_create($data_to_save)
    {
        $params2 = array();
        if ($data_to_save == false) {
            $data_to_save = array();
        }
        if (is_string($data_to_save)) {
            $params = parse_str($data_to_save, $params2);
            $data_to_save = $params2;
        }

        $id = $this->app->user->is_admin();
        if ($id == false) {
            //error('Error: not logged in as admin.'.__FILE__.__LINE__);
        } else {

            if (isset($data_to_save['menu_id'])) {
                $data_to_save['id'] = intval($data_to_save['menu_id']);
            }
            $table = MODULE_DB_MENUS;

            $data_to_save['table'] = $table;
            $data_to_save['item_type'] = 'menu';

            $save = $this->app->db->save($table, $data_to_save);

            $this->app->cache->delete('menus/global');

            return $save;
        }

    }

    public function menu_tree($menu_id, $maxdepth = false)
    {

        static $passed_ids;
        static $passed_actives;
        if (!is_array($passed_actives)) {
            $passed_actives = array();
        }
        if (!is_array($passed_ids)) {
            $passed_ids = array();
        }
        $menu_params = '';
        if (is_string($menu_id)) {
            $menu_params = parse_params($menu_id);
            if (is_array($menu_params)) {
                extract($menu_params);
            }
        }

        if (is_array($menu_id)) {
            $menu_params = $menu_id;
            extract($menu_id);
        }

        $cache_group = 'menus/global';
        $function_cache_id = false;
        $args = func_get_args();
        foreach ($args as $k => $v) {
            $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
        }


        $function_cache_id = __FUNCTION__ . crc32($function_cache_id);
        if (defined('PAGE_ID')) {
            $function_cache_id = $function_cache_id . PAGE_ID;
        }
        if (defined('CATEGORY_ID')) {
            $function_cache_id = $function_cache_id . CATEGORY_ID;
        }


        if (!isset($depth) or $depth == false) {
            $depth = 0;
        }
        $orig_depth = $depth;
        $params_o = $menu_params;

        if ($orig_depth == 0) {

            $cache_content = $this->app->cache->get($function_cache_id, $cache_group);
            if (($cache_content) != false) {
                return $cache_content;
            }

        }

        //$function_cache_id = false;


        $params = array();
        $params['item_parent'] = $menu_id;
        // $params ['item_parent<>'] = $menu_id;
        $menu_id = intval($menu_id);
        $params_order = array();
        $params_order['position'] = 'ASC';

        $menus = MODULE_DB_MENUS;

        $sql = "SELECT * FROM {$menus}
	WHERE parent_id=$menu_id
    AND   id!=$menu_id
	ORDER BY position ASC ";
        //and item_type='menu_item'
        $menu_params = array();
        $menu_params['parent_id'] = $menu_id;
        $menu_params['menu_id'] = $menu_id;

        $menu_params['table'] = $menus;
        $menu_params['orderby'] = "position ASC";

        //$q = $this->app->db->get($menu_params);

        //
        if ($depth < 2) {
            $q = $this->app->db->query($sql, 'query_' . __FUNCTION__ . crc32($sql), 'menus/global');

        } else {
            $q = $this->app->db->query($sql);
        }

        // $data = $q;
        if (empty($q)) {

            return false;
        }
        $active_class = '';
        if (!isset($ul_class)) {
            $ul_class = 'menu';
        }

        if (!isset($li_class)) {
            $li_class = 'menu_element';
        }


        if (isset($ul_class_deep)) {
            if ($depth > 0) {
                $ul_class = $ul_class_deep;
            }
        }

        if (isset($li_class_deep)) {
            if ($depth > 0) {
                $li_class = $li_class_deep;
            }
        }

        if (isset($ul_tag) == false) {
            $ul_tag = 'ul';
        }

        if (isset($li_tag) == false) {
            $li_tag = 'li';
        }

        if (isset($params['maxdepth']) != false) {
            $maxdepth = $params['maxdepth'];
        }
        if (isset($params['depth']) != false) {
            $maxdepth = $params['depth'];
        }
        if (isset($params_o['depth']) != false) {
            $maxdepth = $params_o['depth'];
        }
        if (isset($params_o['maxdepth']) != false) {
            $maxdepth = $params_o['maxdepth'];
        }


        if (!isset($link) or $link == false) {
            $link = '<a data-item-id="{id}" class="menu_element_link {active_class} {exteded_classes} {nest_level}" href="{url}">{title}</a>';
        }

        $to_print = '<' . $ul_tag . ' role="menu" class="{ul_class}' . ' menu_' . $menu_id . ' {exteded_classes}" >';

        $cur_depth = 0;
        $res_count = 0;
        foreach ($q as $item) {
            $full_item = $item;

            $title = '';
            $url = '';
            $is_active = true;
            if (intval($item['content_id']) > 0) {
                $cont = $this->get_by_id($item['content_id']);
                if (is_array($cont) and isset($cont['is_deleted']) and $cont['is_deleted'] == 'y') {
                    $is_active = false;
                    $cont = false;
                }


                if (is_array($cont)) {
                    $title = $cont['title'];
                    $url = $this->link($cont['id']);

                    if ($cont['is_active'] != 'y') {
                        $is_active = false;
                        $cont = false;
                    }

                }
            } else if (intval($item['categories_id']) > 0) {
                $cont = $this->app->category->get_by_id($item['categories_id']);
                if (is_array($cont)) {
                    $title = $cont['title'];
                    $url = $this->app->category->link($cont['id']);
                } else {
                    $this->app->db->delete_by_id($menus, $item['id']);
                    $title = false;
                    $item['title'] = false;
                }
            } else {
                $title = $item['title'];
                $url = $item['url'];
            }

            if (trim($item['url'] != '')) {
                $url = $item['url'];
            }

            if ($item['title'] == '') {
                $item['title'] = $title;
            } else {
                $title = $item['title'];
            }

            $active_class = '';
            if (trim($item['url'] != '') and intval($item['content_id']) == 0 and intval($item['categories_id']) == 0) {
                $surl = $this->app->url->site();
                $cur_url = $this->app->url->current(1);
                $item['url'] = $this->app->format->replace_once('{SITE_URL}', $surl, $item['url']);
                if ($item['url'] == $cur_url) {
                    $active_class = 'active';
                } else {
                    $active_class = '';
                }
            } else if (CONTENT_ID != 0 and $item['content_id'] == CONTENT_ID) {
                $active_class = 'active';
            } elseif (PAGE_ID != 0 and $item['content_id'] == PAGE_ID) {
                $active_class = 'active';
            } elseif (POST_ID != 0 and $item['content_id'] == POST_ID) {
                $active_class = 'active';
            } elseif (CATEGORY_ID != false and intval($item['categories_id']) != 0 and $item['categories_id'] == CATEGORY_ID) {
                $active_class = 'active';
            } elseif (isset($cont['parent']) and PAGE_ID != 0 and $cont['parent'] == PAGE_ID) {
                // $active_class = 'active';

            } elseif (isset($cont['parent']) and MAIN_PAGE_ID != 0 and $item['content_id'] == MAIN_PAGE_ID) {

                $active_class = 'active';
            } else {
                $active_class = '';
            }


            if ($is_active == false) {
                $title = '';
            }
            if ($title != '') {
                $item['url'] = $url;
                $to_print .= '<' . $li_tag . '  class="{li_class}' . ' ' . $active_class . ' {nest_level}" data-item-id="' . $item['id'] . '" >';

                $ext_classes = '';
                if (isset($item['parent']) and intval($item['parent']) > 0) {
                    $ext_classes .= ' have-parent';
                }

                if (isset($item['subtype_value']) and intval($item['subtype_value']) != 0) {
                    $ext_classes .= ' have-category';
                }

                $ext_classes = trim($ext_classes);

                $menu_link = $link;
                foreach ($item as $key => $value) {
                    $menu_link = str_replace('{' . $key . '}', $value, $menu_link);
                }
                $menu_link = str_replace('{active_class}', $active_class, $menu_link);
                $to_print .= $menu_link;

                $ext_classes = '';
                if ($res_count == 0) {
                    $ext_classes .= ' first-child';
                    $ext_classes .= ' child-' . $res_count . '';
                } else if (!isset($q[$res_count + 1])) {
                    $ext_classes .= ' last-child';
                    $ext_classes .= ' child-' . $res_count . '';
                } else {
                    $ext_classes .= ' child-' . $res_count . '';
                }

                if (in_array($item['parent_id'], $passed_ids) == false) {

                    if ($maxdepth == false) {

                        if (isset($params) and is_array($params)) {

                            $menu_params['menu_id'] = $item['id'];
                            $menu_params['link'] = $link;
                            if (isset($menu_params['item_parent'])) {
                                unset($menu_params['item_parent']);
                            }
                            if (isset($ul_class)) {
                                $menu_params['ul_class'] = $ul_class;
                            }
                            if (isset($li_class)) {
                                $menu_params['li_class'] = $li_class;
                            }

                            if (isset($maxdepth)) {
                                $menu_params['maxdepth'] = $maxdepth;
                            }

                            if (isset($li_tag)) {
                                $menu_params['li_tag'] = $li_tag;
                            }
                            if (isset($ul_tag)) {
                                $menu_params['ul_tag'] = $ul_tag;
                            }
                            if (isset($ul_class_deep)) {
                                $menu_params['ul_class_deep'] = $ul_class_deep;
                            }
                            if (isset($li_class_empty)) {
                                $menu_params['li_class_empty'] = $li_class_empty;
                            }

                            if (isset($li_class_deep)) {
                                $menu_params['li_class_deep'] = $li_class_deep;
                            }

                            if (isset($depth)) {
                                $menu_params['depth'] = $depth + 1;
                            }


                            $test1 = $this->menu_tree($menu_params);
                        } else {

                            $test1 = $this->menu_tree($item['id']);

                        }


                    } else {

                        if (($maxdepth != false) and intval($maxdepth) > 1 and ($cur_depth <= $maxdepth)) {

                            if (isset($params) and is_array($params)) {

                                $test1 = $this->menu_tree($menu_params);

                            } else {

                                $test1 = $this->menu_tree($item['id']);

                            }

                        }
                    }
                }
                if (isset($li_class_empty) and isset($test1) and trim($test1) == '') {
                    if ($depth > 0) {
                        $li_class = $li_class_empty;
                    }
                }

                $to_print = str_replace('{ul_class}', $ul_class, $to_print);
                $to_print = str_replace('{li_class}', $li_class, $to_print);
                $to_print = str_replace('{exteded_classes}', $ext_classes, $to_print);
                $to_print = str_replace('{nest_level}', 'depth-' . $depth, $to_print);

                if (isset($test1) and strval($test1) != '') {
                    $to_print .= strval($test1);
                }

                $res_count++;
                $to_print .= '</' . $li_tag . '>';

                // $passed_ids[] = $item['id'];
            }


            $cur_depth++;
        }

        $to_print .= '</' . $ul_tag . '>';
        if ($orig_depth == 0) {
            $this->app->cache->save($to_print, $function_cache_id, $cache_group);
        }
        return $to_print;
    }

    /**
     * Gets a link for given content id
     *
     * If you don't pass id parameter it will try to use the current page id
     *
     * @param int $id The $id The id of the content
     * @return string The url of the content
     * @package Content
     * @see post_link()
     * @see page_link()
     * @see content_link()
     *
     *
     * @example
     * <code>
     * print $this->link($id=1);
     * </code>
     *
     */
    public function link($id = 0)
    {
        if (is_string($id)) {
            // $link = page_link_to_layout ( $id );
        }

        if (is_array($id)) {
            extract($id);
        }


        if ($id == false or $id == 0) {
            if (defined('PAGE_ID') == true) {
                $id = PAGE_ID;
            }
        }


        if ($id == 0) {
            return $this->app->url->site();
        }

        $link = $this->get_by_id($id);


        if (!isset($link['url']) or strval($link['url']) == '') {
            $link = $this->get_by_url($id);
        }


        $surl = $this->app->url->site();
        if (!stristr($link['url'], $surl)) {
            $link = site_url($link['url']);
        } else {
            $link = ($link['url']);
        }

        return $link;
    }

    public function template_dir()
    {
        if (!defined('TEMPLATE_DIR')) {
            $this->define_constants();
        }
        if (defined('TEMPLATE_DIR')) {
            return TEMPLATE_DIR;
        }

    }

    public function template_url()
    {
        if (!defined('TEMPLATE_URL')) {
            $this->define_constants();
        }
        if (defined('TEMPLATE_URL')) {
            return TEMPLATE_URL;
        }

    }

    public function template_name()
    {

        if (!defined('TEMPLATE_NAME')) {
            $this->define_constants();
        }
        if (defined('TEMPLATE_NAME')) {
            return TEMPLATE_NAME;
        }
    }

    public function template_header($script_src)
    {
        static $mw_template_headers;
        if ($mw_template_headers == null) {
            $mw_template_headers = array();
        }

        if (is_string($script_src)) {
            if (!in_array($script_src, $mw_template_headers)) {
                $mw_template_headers[] = $script_src;
                return $mw_template_headers;
            }
        } else if (is_bool($script_src)) {
            //   return $mw_template_headers;
            $src = '';
            if (is_array($mw_template_headers)) {
                foreach ($mw_template_headers as $header) {
                    $ext = get_file_extension($header);
                    switch (strtolower($ext)) {


                        case 'css':
                            $src .= '<link rel="stylesheet" href="' . $header . '" type="text/css" media="all">' . "\n";
                            break;

                        case 'js':
                            $src .= '<script type="text/javascript" src="' . $header . '"></script>' . "\n";
                            break;


                        default:
                            $src .= $header . "\n";
                            break;
                    }
                }
            }
            return $src;
        }
    }

    /**
     * @desc  Get the template layouts info under the layouts subdir on your active template
     * @param $options
     * $options ['type'] - 'layout' is the default type if you dont define any. You can define your own types as post/form, etc in the layout.txt file
     * @return array
     * @author    Microweber Dev Team
     * @since Version 1.0
     */
    public function site_templates($options = false)
    {

        $args = func_get_args();
        $function_cache_id = '';
        foreach ($args as $k => $v) {

            $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
        }

        $cache_id = __FUNCTION__ . crc32($function_cache_id);

        $cache_group = 'templates';

        $cache_content = $this->app->cache->get($cache_id, $cache_group, 'files');

        if (($cache_content) != false) {

            return $cache_content;
        }

        $path = MW_TEMPLATES_DIR;
        $path_to_layouts = $path;
        $layout_path = $path;
        //	print $path;
        //exit;
        //$map = $this->directory_map ( $path, TRUE );
        $map = $this->directory_map($path, TRUE, TRUE);

        $to_return = array();

        foreach ($map as $dir) {

            //$filename = $path . $dir . DIRECTORY_SEPARATOR . 'layout.php';
            $filename = $path . DIRECTORY_SEPARATOR . $dir;
            $filename_location = false;
            $filename_dir = false;
            $filename = normalize_path($filename);
            $filename = rtrim($filename, '\\');


            $filename = (substr($filename, 0, 1) === '.' ? substr($filename, 1) : $filename);

            if (is_dir($filename)) {
                //


                $fn1 = normalize_path($filename, true) . 'config.php';
                $fn2 = normalize_path($filename);


                if (is_file($fn1)) {
                    $config = false;

                    include ($fn1);
                    if (!empty($config)) {
                        $c = $config;
                        $c['dir_name'] = $dir;

                        $screensshot_file = $fn2 . '/screenshot.png';
                        $screensshot_file = normalize_path($screensshot_file, false);
                        //p($screensshot_file);
                        if (is_file($screensshot_file)) {
                            $c['screenshot'] = $this->app->url->link_to_file($screensshot_file);
                        }

                        $to_return[] = $c;
                    }
                } else {
                    $filename_dir = false;
                }

                //	$path = $filename;
            }

            //p($filename);
        }
        $this->app->cache->save($to_return, $function_cache_id, $cache_group, 'files');

        return $to_return;
    }

    /**
     * Create a Directory Map
     *
     *
     * Reads the specified directory and builds an array
     * representation of it.  Sub-folders contained with the
     * directory will be mapped as well.
     *
     * @author        ExpressionEngine Dev Team
     * @link        http://codeigniter.com/user_guide/helpers/directory_helper.html
     * @access    public
     * @param    string    path to source
     * @param    int        depth of directories to traverse (0 = fully recursive, 1 = current dir, etc)
     * @return    array
     */
    function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE, $full_path = false)
    {
        if ($fp = @opendir($source_dir)) {
            $filedata = array();
            $new_depth = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if (!trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) {
                    continue;
                }

                if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir . $file)) {
                    $filedata[$file] = $this->directory_map($source_dir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden, $full_path);
                } else {
                    if ($full_path == false) {
                        $filedata[] = $file;
                    } else {
                        $filedata[] = $source_dir . $file;
                    }

                }
            }

            closedir($fp);
            return $filedata;
        }

        return FALSE;
    }

    function debug_info()
    {
        //if (c('debug_mode')) {

        return include(MW_ADMIN_VIEWS_DIR . 'debug.php');
        // }
    }

    /**
     * Get the current language of the site
     *
     * @example
     * <code>
     *  $current_lang = current_lang();
     *  print $current_lang;
     * </code>
     *
     * @package Language
     * @constant  MW_LANG defines the MW_LANG constant
     */
    public function lang_current()
    {

        if (defined('MW_LANG') and MW_LANG != false) {
            return MW_LANG;
        }


        $lang = false;


        if (!isset($lang) or $lang == false) {
            if (isset($_COOKIE['lang'])) {
                $lang = $_COOKIE['lang'];
            }
        }
        if (!isset($lang) or $lang == false) {
            $def_language = $this->app->option->get('language', 'website');
            if ($def_language != false) {
                $lang = $def_language;
            }
        }
        if (!isset($lang) or $lang == false) {
            $lang = 'en';
        }

        if (!defined('MW_LANG') and isset($lang)) {
            define('MW_LANG', $lang);
        }


        return $lang;

    }

    /**
     * Set the current language
     *
     * @example
     * <code>
     *   //sets language to Spanish
     *  set_language('es');
     * </code>
     * @package Language
     */
    function lang_set($lang = 'en')
    {
        setcookie("lang", $lang);
        return $lang;
    }

    /**
     * Gets all the language file contents
     * @internal its used via ajax in the admin panel under Settings->Language
     * @package Language
     */
    function get_language_file_content()
    {
        global $mw_language_content;

        if (!empty($mw_language_content)) {
            return $mw_language_content;
        }


        $lang = current_lang();

        $lang_file = MW_APP_PATH . 'functions' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $lang . '.php';
        $lang_file = normalize_path($lang_file, false);

        $lang_file2 = MW_APP_PATH . 'functions' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . $lang . '.php';
        $lang_file3 = MW_APP_PATH . 'functions' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . 'en.php';


        if (is_file($lang_file2)) {
            include ($lang_file2);

            if (isset($language) and is_array($language)) {
                foreach ($language as $k => $v) {
                    if (isset($mw_language_content[$k]) == false) {
                        $mw_language_content[$k] = $v;
                    }
                }
            }
        }


        if (is_file($lang_file)) {
            include ($lang_file);

            if (isset($language) and is_array($language)) {
                foreach ($language as $k => $v) {
                    if (isset($mw_language_content[$k]) == false) {

                        $mw_language_content[$k] = $v;
                    }
                }
            }
        }
        if (is_file($lang_file3)) {
            include ($lang_file3);

            if (isset($language) and is_array($language)) {
                foreach ($language as $k => $v) {
                    if (isset($mw_language_content[$k]) == false) {

                        $mw_language_content[$k] = $v;
                    }
                }
            }
        }

        return $mw_language_content;


    }

    public function add_content_to_menu($content_id, $menu_id = false)
    {
        $id = $this->app->user->is_admin();
        if ($id == false) {
            return;
        }
        $content_id = intval($content_id);
        if ($content_id == 0 or !defined('MODULE_DB_MENUS')) {
            return;
        }


        if ($menu_id != false) {
            $_REQUEST['add_content_to_menu'] = $menu_id;
        }


        $menus = MODULE_DB_MENUS;
        if (isset($_REQUEST['add_content_to_menu']) and is_array($_REQUEST['add_content_to_menu'])) {
            $add_to_menus = $_REQUEST['add_content_to_menu'];
            $add_to_menus_int = array();
            foreach ($add_to_menus as $value) {
                if ($value == 'remove_from_all') {
                    $sql = "DELETE FROM {$menus}
                    WHERE
                    item_type='menu_item'
                    AND content_id={$content_id}
				    ";

                    $this->app->cache->delete('menus');
                    $q = $this->app->db->q($sql);
                    return;
                }

                $value = intval($value);
                if ($value > 0) {
                    $add_to_menus_int[] = $value;
                }
            }

        }
        $add_under_parent_page = false;
        $content_data = false;

        if (isset($_REQUEST['add_content_to_menu_auto_parent']) and ($_REQUEST['add_content_to_menu_auto_parent']) != false) {
            $add_under_parent_page = true;
            //
            //
            $content_data = $this->get_by_id($content_id);
            if ($content_data['is_active'] != 'y') {
                return false;
            }

        }


        if (isset($add_to_menus_int) and is_array($add_to_menus_int)) {
            $add_to_menus_int_implode = implode(',', $add_to_menus_int);
            $sql = "DELETE FROM {$menus}
		WHERE parent_id NOT IN ($add_to_menus_int_implode)
		AND item_type='menu_item'
		AND content_id={$content_id}
		";

            $q = $this->app->db->q($sql);


            foreach ($add_to_menus_int as $value) {
                $check = $this->get_menu_items("limit=1&count=1&parent_id={$value}&content_id=$content_id");
                if ($check == 0) {
                    $save = array();
                    $save['item_type'] = 'menu_item';
                    //	$save['debug'] = $menus;
                    $save['parent_id'] = $value;
                    $save['position'] = 999999;
                    if ($add_under_parent_page != false and is_array($content_data) and isset($content_data['parent'])) {
                        $parent_cont = $content_data['parent'];
                        $check_par = $this->get_menu_items("limit=1&one=1&content_id=$parent_cont");
                        if (is_array($check_par) and isset($check_par['id'])) {
                            $save['parent_id'] = $check_par['id'];


                        }


                    }

                    $save['url'] = '';
                    $save['content_id'] = $content_id;
                    $new_item = $this->app->db->save($menus, $save);
                    $this->app->cache->delete('menus/' . $save['parent_id']);
                    //$this->app->cache->delete('menus/' . $save['parent_id']);

                    $this->app->cache->delete('menus/' . $value);

                }
            }

            $this->app->cache->delete('menus/global');
            $this->app->cache->delete('menus');

        }


    }

    /**
     * Saves your custom language translation
     * @internal its used via ajax in the admin panel under Settings->Language
     * @package Language
     */
    function lang_file_save($data)
    {

        if (isset($_POST) and !empty($_POST)) {
            $data = $_POST;
        }
        if (is_admin() == true) {
            if (isset($data['unicode_temp_remove'])) {
                unset($data['unicode_temp_remove']);
            }


            $lang = current_lang();

            $cust_dir = $lang_file = MW_APP_PATH . 'functions' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR;
            if (!is_dir($cust_dir)) {
                mkdir_recursive($cust_dir);
            }

            $language_content = $data;

            $lang_file = $cust_dir . $lang . '.php';

            if (is_array($language_content)) {
                $language_content = array_unique($language_content);

                $lang_file_str = '<?php ' . "\n";
                $lang_file_str .= ' $language=array();' . "\n";
                foreach ($language_content as $key => $value) {

                    $value = addslashes($value);
                    $lang_file_str .= '$language["' . $key . '"]' . "= '{$value}' ; \n";

                }
                $language_content_saved = 1;
                if (is_admin() == true) {
                    file_put_contents($lang_file, $lang_file_str);
                }
            }
            return array('success' => 'Language file [' . $lang . '] is updated');


        }


    }

    public function save($data, $delete_the_cache = true)
    {
        return $this->save_content($data, $delete_the_cache);
    }

    public function save_content($data, $delete_the_cache = true)
    {

        if (is_string($data)) {
            $data = parse_params($data);
        }


        $mw_global_content_memory = array();
        $adm = $this->app->user->is_admin();
        $table = MW_DB_TABLE_CONTENT;
        $table_data = MW_DB_TABLE_CONTENT_DATA;

        $checks = mw_var('FORCE_SAVE_CONTENT');
        $orig_data = $data;
        $stop = false;

        /* CODE MOVED TO $this->save_content_admin

         if (defined('MW_API_CALL') and $checks != $table) {

            if ($adm == false) {
                $data = $this->app->format->strip_unsafe($data);
                $stop = true;
                $author_id = user_id();
                if (isset($data['id']) and $data['id'] != 0 and $author_id != 0) {
                    $page_data_to_check_author = $this->get_by_id($data['id']);
                    if (!isset($page_data_to_check_author['created_by']) or ($page_data_to_check_author['created_by'] != $author_id)) {
                        $stop = true;
                        return array('error' => 'You dont have permission to edit this content');
                    } else if (isset($page_data_to_check_author['created_by']) and ($page_data_to_check_author['created_by'] == $author_id)) {
                        $stop = false;
                    }
                }
                if ($stop == true) {
                    if (defined('MW_API_FUNCTION_CALL') and MW_API_FUNCTION_CALL == __FUNCTION__) {

                        if (!isset($data['captcha'])) {
                            if (isset($data['error_msg'])) {
                                return array('error' => $data['error_msg']);
                            } else {
                                return array('error' => 'Please enter a captcha answer!');

                            }
                        } else {
                            $cap = $this->app->user->session_get('captcha');
                            if ($cap == false) {
                                return array('error' => 'You must load a captcha first!');
                            }
                            if ($data['captcha'] != $cap) {
                                return array('error' => 'Invalid captcha answer!');
                            }
                        }
                    }
                }


                if (isset($data['categories'])) {
                    $data['category'] = $data['categories'];
                }
                if (defined('MW_API_FUNCTION_CALL') and MW_API_FUNCTION_CALL == __FUNCTION__) {
                    if (isset($data['category'])) {
                        $cats_check = array();
                        if (is_array($data['category'])) {
                            foreach ($data['category'] as $cat) {
                                $cats_check[] = intval($cat);
                            }
                        } else {
                            $cats_check[] = intval($data['category']);
                        }
                        $check_if_user_can_publish = $this->app->category->get('ids=' . implode(',', $cats_check));
                        if (!empty($check_if_user_can_publish)) {
                            $user_cats = array();
                            foreach ($check_if_user_can_publish as $item) {
                                if (isset($item["users_can_create_content"]) and $item["users_can_create_content"] == 'y') {
                                    $user_cats[] = $item["id"];
                                    $cont_cat = $this->get('limit=1&content_type=page&subtype_value=' . $item["id"]);
                                }
                            }
                            if (!empty($user_cats)) {
                                $stop = false;
                                $data['categories'] = $user_cats;
                            }
                        }
                    }

                }
            }
        }
        */


        if ($stop == true) {
            return array('error' => 'You are not logged in as admin to save content!');
        }

        $cats_modified = false;


        if (!empty($data)) {
            if (!isset($data['id'])) {
                $data['id'] = 0;
            }
        }

        if (isset($data['content_url']) and !isset($data['url'])) {
            $data['url'] = $data['content_url'];
        }
        $data_to_save = $data;

        if (!isset($data['url']) and intval($data['id']) != 0) {

            $q = "SELECT * FROM $table WHERE id='{$data_to_save['id']}' ";

            $q = $this->app->db->query($q);

            $thetitle = $q[0]['title'];
            $q = $q[0]['url'];
            $theurl = $q;
        } else {
            if (isset($data['url'])) {
                $theurl = $data['url'];
            } else {
                $theurl = $data['title'];
            }
            $thetitle = $data['title'];
        }
        if (isset($data['title'])) {
            $data['title'] = strip_tags($data['title']);
            $data['title'] = preg_replace("/(^\s+)|(\s+$)/us", "", $data['title']);
            $data_to_save['title'] = $data['title'];
        }

        if (isset($data['id']) and intval($data['id']) == 0) {
            if (!isset($data['title']) or ($data['title']) == '') {

                $data['title'] = "New page";
                if (isset($data['content_type']) and ($data['content_type']) != 'page') {
                    $data['title'] = "New " . $data['content_type'];
                    if (isset($data['subtype']) and ($data['subtype']) != 'page' and ($data['subtype']) != 'post' and ($data['subtype']) != 'static' and ($data['subtype']) != 'dynamic') {
                        $data['title'] = "New " . $data['subtype'];
                    }
                }
                $data_to_save['title'] = $data['title'];

            }
        }


        if (isset($data['url']) == false or $data['url'] == '') {
            if (isset($data['title']) != false and intval($data ['id']) == 0) {
                $data['url'] = $this->app->url->slug($data['title']);
            }
        }
        $url_changed = false;

        if (isset($data['url']) != false) {

            $search_weird_chars = array('%E2%80%99',
                '%E2%80%99',
                '%E2%80%98',
                '%E2%80%9C',
                '%E2%80%9D'
            );
            $str = $data['url'];
            $good[] = 9; #tab
            $good[] = 10; #nl
            $good[] = 13; #cr
            for ($a = 32; $a < 127; $a++) {
                $good[] = $a;
            }
            $newstr = '';
            $len = strlen($str);
            for ($b = 0; $b < $len + 1; $b++) {
                if (isset($str[$b]) and in_array(ord($str[$b]), $good)) {
                    $newstr .= $str[$b];
                }

            }

            $newstr = str_replace('--', '-', $newstr);
            $newstr = str_replace('--', '-', $newstr);
            if ($newstr == '-' or $newstr == '--') {
                $newstr = 'post-' . date('YmdH');
            }
            $data['url'] = $newstr;

            $url_changed = true;
            $data_to_save['url'] = $data['url'];

        }


        if (isset($data['category']) or isset($data['categories'])) {
            $cats_modified = true;
        }
        $table_cats = MW_TABLE_PREFIX . 'categories';

        if (isset($data_to_save['title']) and ($data_to_save['title'] != '') and (!isset($data['url']) or trim($data['url']) == '')) {
            $data['url'] = $this->app->url->slug($data_to_save['title']);
        }

        if (isset($data['url']) and $data['url'] != false) {

            if (trim($data['url']) == '') {

                $data['url'] = $this->app->url->slug($data['title']);
            }

            $data['url'] = $this->app->db->escape_string($data['url']);


            $date123 = date("YmdHis");

            $q = "SELECT id, url FROM $table WHERE url LIKE '{$data['url']}'";

            $q = $this->app->db->query($q);

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

        }


        if (isset($data_to_save['url']) and is_string($data_to_save['url'])) {
            $data_to_save['url'] = str_replace(site_url(), '', $data_to_save['url']);
        }


        $data_to_save_options = array();

        if (isset($data_to_save['is_home']) and $data_to_save['is_home'] == 'y') {
            if ($adm == true) {
                $sql = "UPDATE $table SET is_home='n'   ";
                $q = $this->app->db->query($sql);
            } else {
                $data_to_save['is_home'] = 'n';
            }
        }

        if (isset($data_to_save['content_type']) and strval($data_to_save['content_type']) == 'post') {
            if (isset($data_to_save['subtype']) and strval($data_to_save['subtype']) == 'static') {
                $data_to_save['subtype'] = 'post';
            } else if (isset($data_to_save['subtype']) and strval($data_to_save['subtype']) == 'dynamic') {
                $data_to_save['subtype'] = 'post';
            }
        }

        if (isset($data_to_save['subtype']) and strval($data_to_save['subtype']) == 'dynamic') {
            $check_ex = false;
            if (isset($data_to_save['subtype_value']) and trim($data_to_save['subtype_value']) != '' and intval(($data_to_save['subtype_value'])) > 0) {

                $check_ex = $this->app->category->get_by_id(intval($data_to_save['subtype_value']));
            }
            if ($check_ex == false) {
                if (isset($data_to_save['id']) and intval(trim($data_to_save['id'])) > 0) {
                    $test2 = $this->app->category->get('data_type=category&rel=content&rel_id=' . intval(($data_to_save['id'])));
                    if (isset($test2[0])) {
                        $check_ex = $test2[0];
                        $data_to_save['subtype_value'] = $test2[0]['id'];
                    }
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


        $par_page = false;
        if (isset($data_to_save['content_type']) and strval($data_to_save['content_type']) == 'post') {
            if (isset($data_to_save['parent']) and intval($data_to_save['parent']) > 0) {
                $par_page = $this->get_by_id($data_to_save['parent']);
            }


            if (is_array($par_page)) {
                $change_to_dynamic = true;
                if (isset($data_to_save['is_home']) and $data_to_save['is_home'] == 'y') {
                    $change_to_dynamic = false;
                }
                if ($change_to_dynamic == true and $par_page['subtype'] == 'static') {
                    $par_page_new = array();
                    $par_page_new['id'] = $par_page['id'];
                    $par_page_new['subtype'] = 'dynamic';

                    $par_page_new = $this->app->db->save($table, $par_page_new);
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
            if (isset($data_to_save['category']) and !isset($data_to_save['categories'])) {
                $data_to_save['categories'] = $data_to_save['category'];
            }
            if (isset($data_to_save['categories']) and $par_page == false) {
                if (is_string($data_to_save['categories'])) {
                    $c1 = explode(',', $data_to_save['categories']);
                    if (is_array($c1)) {
                        foreach ($c1 as $item) {
                            $item = intval($item);
                            if ($item > 0) {
                                $cont_cat = $this->get('limit=1&content_type=page&subtype_value=' . $item);
                                if (isset($cont_cat[0]) and is_array($cont_cat[0])) {
                                    $cont_cat = $cont_cat[0];
                                    if (isset($cont_cat["subtype_value"]) and intval($cont_cat["subtype_value"]) > 0) {


                                        $data_to_save['parent'] = $cont_cat["id"];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (isset($data_to_save['content'])) {
            if (trim($data_to_save['content']) == '' or $data_to_save['content'] == false) {
                $data_to_save['content'] = null;
            } else {
                $data_to_save['content'] = mw('parser')->make_tags($data_to_save['content']);
            }
        }

        $data_to_save['updated_on'] = date("Y-m-d H:i:s");
        if (isset($data_to_save['id']) and intval($data_to_save['id']) == 0) {
            if (!isset($data_to_save['position']) or intval($data_to_save['position']) == 0) {

                $get_max_pos = "SELECT max(position) AS maxpos FROM $table  ";
                $get_max_pos = $this->app->db->query($get_max_pos);
                if (is_array($get_max_pos) and isset($get_max_pos[0]['maxpos']))


                    if (isset($data_to_save['content_type']) and strval($data_to_save['content_type']) == 'page') {
                        $data_to_save['position'] = intval($get_max_pos[0]['maxpos']) - 1;

                    } else {
                        $data_to_save['position'] = intval($get_max_pos[0]['maxpos']) + 1;

                    }

            }
            $data_to_save['posted_on'] = $data_to_save['updated_on'];

        }


        $cats_modified = true;


        if (!isset($data_to_save['id']) or intval($data_to_save['id']) == 0) {
            if (!isset($data_to_save['parent'])) {
                $data_to_save['parent'] = 0;
            }
            if ($data_to_save['parent'] == 0) {
                if (isset($data_to_save['categories'])) {
                    $first = false;
                    if (is_array($data_to_save['categories'])) {
                        $temp = $data_to_save['categories'];
                        $first = array_shift($temp);
                    } else {
                        $first = intval($data_to_save['categories']);
                    }
                    if ($first != false) {
                        $first_par_for_cat = $this->app->category->get_page($first);
                        if (!empty($first_par_for_cat) and isset($first_par_for_cat['id'])) {
                            $data_to_save['parent'] = $first_par_for_cat['id'];
                            if (!isset($data_to_save['content_type'])) {
                                $data_to_save['content_type'] = 'post';
                            }

                            if (!isset($data_to_save['subtype'])) {
                                $data_to_save['subtype'] = 'post';
                            }

                        }
                    }
                }
            }

        }


        if (isset($data_to_save['url']) and $data_to_save['url'] == $this->app->url->site()) {
            unset($data_to_save['url']);
        }


        $data_to_save['allow_html'] = true;
        $this->no_cache = true;
        $save = $this->app->db->save($table, $data_to_save);

        if (isset($data_to_save['parent']) and $data_to_save['parent'] != 0) {
            $upd_posted = array();
            $upd_posted['posted_on'] = $data_to_save['updated_on'];
            $upd_posted['id'] = $data_to_save['parent'];
            $save_posted = $this->app->db->save($table, $upd_posted);

        }


        $this->app->cache->delete('content/' . $save);

        $this->app->cache->delete('content_fields/global');
        // $this->app->cache->delete('content/global');
        if ($url_changed != false) {
            $this->app->cache->delete('menus');
            $this->app->cache->delete('categories');
        }

        $data_fields = array();
        if (!empty($orig_data)) {
            $data_str = 'data_';
            $data_str_l = strlen($data_str);
            foreach ($orig_data as $k => $v) {

                if (strlen($k) > $data_str_l) {
                    $rest = substr($k, 0, $data_str_l);
                    $left = substr($k, $data_str_l, strlen($k));
                    if ($rest == $data_str) {
                        $data_field = array();
                        $data_field["content_id"] = $save;
                        $data_field["field_name"] = $left;
                        $data_field["field_value"] = $v;
                        $data_field = $this->save_content_data_field($data_field);

                    }
                }

            }
        }


        if (isset($data_to_save['subtype']) and strval($data_to_save['subtype']) == 'dynamic') {
            $new_category = $this->app->category->get_for_content($save);

            if ($new_category == false) {
                //$new_category_id = intval($new_category);
                $new_category = array();
                $new_category["data_type"] = "category";
                $new_category["rel"] = 'content';
                $new_category["rel_id"] = $save;
                $new_category["table"] = $table_cats;
                $new_category["id"] = 0;
                $new_category["title"] = $data_to_save['title'];
                $new_category["parent_id"] = "0";
                $cats_modified = true;
                // $new_category = $this->app->category->save($new_category);
            }
        }
        $custom_field_table = MW_TABLE_PREFIX . 'custom_fields';

        $sid = session_id();

        $id = $save;

        $clean = " UPDATE $custom_field_table SET
	rel =\"content\"
	, rel_id =\"{$id}\"
	WHERE
	session_id =\"{$sid}\"
	AND (rel_id=0 OR rel_id IS NULL OR rel_id =\"0\") AND rel =\"content\"

	";


        $this->app->db->q($clean);
        $this->app->cache->delete('custom_fields');

        $media_table = MW_TABLE_PREFIX . 'media';

        $clean = " UPDATE $media_table SET

	rel_id =\"{$id}\"
	WHERE
	session_id =\"{$sid}\"
	AND rel =\"content\" AND (rel_id=0 OR rel_id IS NULL)

	";


        $this->app->cache->delete('media/global');

        $this->app->db->q($clean);

        if (isset($data_to_save['parent']) and intval($data_to_save['parent']) != 0) {
            $this->app->cache->delete('content' . DIRECTORY_SEPARATOR . intval($data_to_save['parent']));
        }
        if (isset($data_to_save['id']) and intval($data_to_save['id']) != 0) {
            $this->app->cache->delete('content' . DIRECTORY_SEPARATOR . intval($data_to_save['id']));
        }

        $this->app->cache->delete('content' . DIRECTORY_SEPARATOR . 'global');
        $this->app->cache->delete('content' . DIRECTORY_SEPARATOR . '0');
        $this->app->cache->delete('content_fields/global');
        $this->app->cache->delete('content');
        if ($cats_modified != false) {

            $this->app->cache->delete('categories/global');
            $this->app->cache->delete('categories_items/global');
            if (isset($c1) and is_array($c1)) {
                foreach ($c1 as $item) {
                    $item = intval($item);
                    if ($item > 0) {
                        $this->app->cache->delete('categories/' . $item);
                    }
                }
            }
        }
        event_trigger('mw_save_content', $save);
        return $save;
    }

    /**
     * Get single content item by id from the content_table
     *
     * @param int $id The id of the content item
     * @return array
     * @category Content
     * @function  get_content_by_id
     *
     * @example
     * <pre>
     * $content = $this->get_by_id(1);
     * var_dump($content);
     * </pre>
     *
     */
    public function get_by_id($id)
    {

        if ($id == false) {
            return false;
        }


        // ->'content';
        $table = MW_DB_TABLE_CONTENT;

        $id = intval($id);
        if ($id == 0) {
            return false;
        }

        $q = "SELECT * FROM $table WHERE id='$id'  LIMIT 0,1 ";

        $params = array();
        $params['id'] = $id;
        $params['limit'] = 1;
        $params['table'] = $table;
        //$params['debug'] = 1;
        $params['cache_group'] = 'content/' . $id;

        if ($this->no_cache == true) {

            $q = $this->app->db->query($q);

        } else {
            $q = $this->app->db->query($q, __FUNCTION__ . crc32($q), 'content/' . $id);

        }

        //$q = $this->app->db->get($params);

        //  $q = $this->app->db->get_long($table, $params, $cache_group = 'content/' . $id);
        if (is_array($q) and isset($q[0])) {
            $content = $q[0];
            if (isset($content['title'])) {
                $content['title'] = html_entity_decode($content['title']);
                $content['title'] = strip_tags($content['title']);
                $content['title'] = $this->app->format->clean_html($content['title']);

            }
        } else {

            return false;
        }

        return $content;
    }

    /**
     * Get array of content items from the database
     *
     * It accepts string or array as parameters. You can pass any db field name as parameter to filter content by it.
     * All parameter are passed to the get() function
     *
     * You can get and filter content and also order the results by criteria
     *
     *
     *
     *
     * @function get_content
     * @package Content
     *
     *
     * @desc  Get array of content items from the content DB table
     *
     * @uses get() You can use all the options of get(), such as limit, order_by, count, etc...
     *
     * @param mixed|array|bool|string $params You can pass parameters as string or as array
     * @params
     *
     * *Some parameters you can use*
     *  You can use all defined database fields as parameters
     *
     * .[params-table]
     *|-----------------------------------------------------------------------------
     *| Field Name          | Description               | Values
     *|------------------------------------------------------------------------------
     *| id                  | the id of the content     |
     *| is_active           | published or unpublished  | "y" or "n"
     *| parent              | get content with parent   | any id or 0
     *| created_by          | get by author id          | any user id
     *| created_on          | the date of creation      |
     *| updated_on          | the date of last edit     |
     *| content_type        | the type of the content   | "page" or "post", anything custom
     *| subtype             | subtype of the content    | "static","dynamic","post","product", anything custom
     *| url                 | the link to the content   |
     *| title               | Title of the content      |
     *| content             | The html content saved in the database |
     *| description         | Description used for the content list |
     *| position            | The order position        |
     *| active_site_template   | Current template for the content |
     *| layout_file         | Current layout from the template directory |
     *| is_deleted          | flag for deleted content  |  "n" or "y"
     *| is_home             | flag for homepage         |  "n" or "y"
     *| is_shop             | flag for shop page        |  "n" or "y"
     *
     *
     * @return array|bool|mixed Array of content or false if nothing is found
     * @example
     * #### Get with parameters as array
     * <code>
     *
     * $params = array();
     * $params['is_active'] = 'y'; //get only active content
     * $params['parent'] = 2; //get by parent id
     * $params['created_by'] = 1; //get by author id
     * $params['content_type'] = 'post'; //get by content type
     * $params['subtype'] = 'product'; //get by subtype
     * $params['title'] = 'my title'; //get by title
     *
     * $data = $this->get($params);
     * var_dump($data);
     *
     * </code>
     *
     * @example
     * #### Get by params as string
     * <code>
     *  $data = $this->get('is_active=y');
     *  var_dump($data);
     * </code>
     *
     * @example
     * #### Ordering and sorting
     * <code>
     *  //Order by position
     *  $data = $this->get('content_type=post&is_active=y&order_by=position desc');
     *  var_dump($data);
     *
     *  //Order by date
     *  $data = $this->get('content_type=post&is_active=y&order_by=updated_on desc');
     *  var_dump($data);
     *
     *  //Order by title
     *  $data = $this->get('content_type=post&is_active=y&order_by=title asc');
     *  var_dump($data);
     *
     *  //Get content from last week
     *  $data = $this->get('created_on=[mt]-1 week&is_active=y&order_by=title asc');
     *  var_dump($data);
     * </code>
     *
     */
    public function get($params = false)
    {

        if (defined('PAGE_ID') == false) {
            //   $this->define_constants();
        }

        $params2 = array();

        if (is_string($params)) {
            $params = parse_str($params, $params2);
            $params = $params2;
        }

        if (!is_array($params)) {
            $params = array();
            $params['is_active'] = 'y';
        }


        $cache_group = 'content/global';
        if (isset($params['cache_group'])) {
            $cache_group = $params['cache_group'];
        }
        $table = MW_DB_TABLE_CONTENT;
        if (!isset($params['is_deleted'])) {
            $params['is_deleted'] = 'n';
        }
        $params['table'] = $table;
        $params['cache_group'] = $cache_group;

        if ($this->no_cache == true) {
            $params['cache_group'] = false;
            $params['no_cache'] = true;
            $mw_global_content_memory = array();

        }

        if (isset($params['keyword'])) {

            $params['search_in_content_data_fields'] = true;

        }


        $get = $this->app->db->get($params);


        if (isset($params['count']) or isset($params['single']) or isset($params['one'])  or isset($params['data-count']) or isset($params['page_count']) or isset($params['data-page-count'])) {

            if (isset($get['url'])) {
                $get['url'] = $this->app->url->site($get['url']);
            }
            if (isset($get['title'])) {
                $get['title'] = html_entity_decode($get['title']);
                $get['title'] = strip_tags($get['title']);
                $get['title'] = $this->app->format->clean_html($get['title']);
            }


            return $get;
        }
        if (is_array($get)) {
            $data2 = array();
            foreach ($get as $item) {

                if (isset($item['url'])) {
                    $item['url'] = $this->app->url->site($item['url']);
                }
                if (isset($item['title'])) {
                    $item['title'] = html_entity_decode($item['title']);
                    $item['title'] = strip_tags($item['title']);
                    $item['title'] = $this->app->format->clean_html($item['title']);
                }


                $data2[] = $item;
            }
            $get = $data2;

            return $get;
        }

    }

    public function save_content_data_field($data, $delete_the_cache = true)
    {

        $adm = $this->app->user->is_admin();
        $table = MW_DB_TABLE_CONTENT_DATA;

        $check_force = mw_var('FORCE_SAVE_CONTENT_DATA_FIELD');


        if ($check_force == false and $adm == false) {
            return array('error' => "You must be logged in as admin to use: " . __FUNCTION__);

        }

        if (!is_array($data)) {
            $data = parse_params($data);
        }

        if (!isset($data['id'])) {

            if (!isset($data['field_name'])) {
                return array('error' => "You must set 'field' parameter");
            }
            if (!isset($data['field_value'])) {
                return array('error' => "You must set 'value' parameter");
            }

            if (!isset($data['content_id'])) {
                return array('error' => "You must set 'content_id' parameter");
            }
        }


        if (isset($data['field_name']) and isset($data['content_id'])) {
            $is_existing_data = array();
            $is_existing_data['field_name'] = $data['field_name'];
            $is_existing_data['content_id'] = intval($data['content_id']);
            $is_existing_data['one'] = true;

            $is_existing = $this->get_content_data_fields($is_existing_data);
            if (is_array($is_existing) and isset($is_existing['id'])) {
                $data['id'] = $is_existing['id'];
            }

        }


        $data['allow_html'] = true;
        // $data['debug'] = true;

        $save = $this->app->db->save($table, $data);

        $this->app->cache->delete('content_data');

        return $save;


    }


// ------------------------------------------------------------------------

    public function get_content_data_fields($data, $debug = false)
    {


        $table = MW_DB_TABLE_CONTENT_DATA;


        if (is_string($data)) {
            $data = parse_params($data);
        }

        if (!is_array($data)) {
            $data = array();
        }


        $data['table'] = $table;
        $data['cache_group'] = 'content_data';


        $get = $this->app->db->get($data);

        return $get;

    }

    public function save_edit($post_data)
    {


        $is_admin = $this->app->user->is_admin();

        if ($post_data) {
            if (isset($post_data['json_obj'])) {
                $obj = json_decode($post_data['json_obj'], true);
                $post_data = $obj;
            }
            // p($post_data);
            if (isset($post_data['mw_preview_only'])) {
                $is_no_save = true;
                unset($post_data['mw_preview_only']);
            }
            $is_no_save = false;
            $is_draft = false;
            if (isset($post_data['is_draft'])) {
                unset($post_data['is_draft']);
                $is_draft = 1;
            }
            $the_field_data_all = $post_data;
        } else {

            return array('error' => 'no POST?');

        }

        $ustr2 = $this->app->url->string(1, 1);

        if (isset($ustr2) and trim($ustr2) == 'favicon.ico') {
            return false;
        }
        $ref_page = $ref_page_url = $_SERVER['HTTP_REFERER'];
        if ($ref_page != '') {

            //removing hash from url
            if (strpos($ref_page_url, '#')) {
                $ref_page = $ref_page_url = substr($ref_page_url, 0, strpos($ref_page_url, '#'));
            }

            // $ref_page = $the_ref_page = $this->get_by_url($ref_page_url);

            $ref_page2 = $ref_page = $this->get_by_url($ref_page_url);


            if ($ref_page2 == false) {

                $ustr = $this->app->url->string(1);

                if ($this->app->module->is_installed($ustr)) {
                    $ref_page = false;
                }

            } else {
                $ref_page = $ref_page2;
            }
            if (isset($ustr) and trim($ustr) == 'favicon.ico') {
                return false;
            } elseif ($ustr2 == '' or $ustr2 == '/') {

                $ref_page = $this->homepage();

            }


            if ($ref_page == false) {


                $guess_page_data = new \Microweber\Controller();
                // $guess_page_data =  new  $this->app->controller($this->app);
                $guess_page_data->page_url = $ref_page_url;
                $guess_page_data->return_data = true;
                $guess_page_data->create_new_page = true;
                $pd = $guess_page_data->index();

                if ($is_admin == true and is_array($pd) and (isset($pd["active_site_template"]) or isset($pd["layout_file"]))) {
                    $save_page = $pd;
                    $save_page['url'] = $this->app->url->string(1);
                    $save_page['title'] = $this->app->url->slug($this->app->url->string(1));
                    $page_id = $this->save_content_admin($save_page);
                }

            } else {
                $page_id = $ref_page['id'];
                $ref_page['custom_fields'] = $this->custom_fields($page_id, false);
            }
        }


        $author_id = user_id();
        if ($is_admin == false and $page_id != 0 and $author_id != 0) {
            $page_data_to_check_author = $this->get_by_id($page_id);
            if (!isset($page_data_to_check_author['created_by']) or ($page_data_to_check_author['created_by'] != $author_id)) {
                return array('error' => 'You dont have permission to edit this content');
            }


        } else if ($is_admin == false) {
            return array('error' => 'Not logged in as admin to use ' . __FUNCTION__);

        }


        $save_as_draft = false;
        if (isset($post_data['save_draft'])) {
            $save_as_draft = true;
            unset($post_data['save_draft']);
        }

        /*

          $double_save_checksum = md5(serialize($post_data));
          $last_save_checksum = $this->app->user->session_get('mw_live_ed_checksum');

          if($double_save_checksum != $last_save_checksum){
              $this->app->user->session_set('mw_live_ed_checksum',$double_save_checksum);
          } else {
              return array('success'=>'No text is changed from the last save');
          }

        */


        $json_print = array();
        foreach ($the_field_data_all as $the_field_data) {
            $save_global = false;
            $save_layout = false;
            if (isset($page_id) and $page_id != 0 and !empty($the_field_data)) {
                $save_global = false;

                $content_id = $page_id;


                $url = $this->app->url->string(true);
                $some_mods = array();
                if (isset($the_field_data) and is_array($the_field_data) and isset($the_field_data['attributes'])) {
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
                        if (!isset($the_field_data['attributes']['rel'])) {
                            $the_field_data['attributes']['rel'] = 'content';
                        }

                        if (isset($the_field_data['attributes']['rel-id'])) {
                            $content_id = $the_field_data['attributes']['rel-id'];
                        } elseif (isset($the_field_data['attributes']['rel_id'])) {
                            $content_id = $the_field_data['attributes']['rel_id'];
                        } elseif (isset($the_field_data['attributes']['data-rel-id'])) {
                            $content_id = $the_field_data['attributes']['data-rel-id'];
                        } elseif (isset($the_field_data['attributes']['data-rel_id'])) {
                            $content_id = $the_field_data['attributes']['data-rel_id'];
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


                        if (!isset($the_field_data['attributes']['data-id'])) {
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

                            $inh = $this->get_inherited_parent($page_id);
                            if ($inh != false) {
                                $content_id_for_con_field = $content_id = $inh;

                            }

                        } else if (isset($the_field_data['attributes']['rel']) and ($the_field_data['attributes']['rel']) == 'page') {


                            $save_global = false;
                            $save_layout = false;
                            $content_id = $page_id;
                            $check_if_page = $this->get_by_id($content_id);

                            if (is_array($check_if_page)
                                and isset($check_if_page['content_type'])
                                    and isset($check_if_page['parent'])
                                        and $check_if_page['content_type'] != ''
                                            and intval($check_if_page['parent']) != 0
                                                and $check_if_page['content_type'] != 'page'
                            ) {
                                // $inh = $this->get_inherited_parent($page_id);
                                $inh = $check_if_page['parent'];
                                if ($inh != false) {
                                    $content_id_for_con_field = $content_id = $inh;

                                }

                            }


                        }


                        $save_layout = false;
                        if ($inh == false and !isset($content_id_for_con_field)) {

                            if (is_array($ref_page) and isset($ref_page['parent']) and  isset($ref_page['content_type'])  and $ref_page['content_type'] == 'post') {
                                $content_id_for_con_field = intval($ref_page['parent']);
                                // d($content_id);
                            } else {
                                $content_id_for_con_field = intval($ref_page['id']);

                            }
                        }

                        $html_to_save = $the_field_data['html'];

                        $html_to_save = $content = mw('parser')->make_tags($html_to_save);


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

                                $cont_field = array();
                                $cont_field['rel'] = 'content';
                                $cont_field['rel_id'] = $content_id_for_con_field;
                                $cont_field['value'] = $html_to_save;
                                $cont_field['field'] = $field;


                                if ($is_draft != false) {
                                    $cont_field['is_draft'] = 1;
                                    $cont_field['rel'] = $rel_ch;
                                    $cont_field['url'] = $url;

                                    $cont_field1 = $this->save_content_field($cont_field);

                                } else {
                                    if ($field != 'content') {

                                        $cont_field1 = $this->save_content_field($cont_field);
                                    }
                                }


                                $to_save = array();
                                $to_save['id'] = $content_id;


                                $is_native_fld = $this->app->db->get_fields('content');
                                if (in_array($field, $is_native_fld)) {
                                    $to_save[$field] = ($html_to_save);
                                } else {

                                    //$to_save['custom_fields'][$field] = ($html_to_save);
                                }

                                if ($is_no_save != true and $is_draft == false) {
                                    $json_print[] = $to_save;


                                    $saved = $this->save_content_admin($to_save);


                                }


                            } else if (isset($category_id)) {
                                print(__FILE__ . __LINE__ . ' category is not implemented ... not ready yet');
                            }
                        } else {

                            $cont_field = array();

                            $cont_field['rel'] = $the_field_data['attributes']['rel'];
                            $cont_field['rel_id'] = 0;
                            if (isset($the_field_data['attributes']['rel-id'])) {
                                $cont_field['rel_id'] = $the_field_data['attributes']['rel-id'];
                            } elseif (isset($the_field_data['attributes']['rel_id'])) {
                                $cont_field['rel_id'] = $the_field_data['attributes']['rel_id'];
                            } elseif (isset($the_field_data['attributes']['data-rel-id'])) {
                                $cont_field['rel_id'] = $the_field_data['attributes']['data-rel-id'];
                            } elseif ($cont_field['rel'] != 'global' and isset($the_field_data['attributes']['content-id'])) {
                                $cont_field['rel_id'] = $the_field_data['attributes']['content-id'];
                            } elseif ($cont_field['rel'] != 'global' and isset($the_field_data['attributes']['data-id'])) {
                                $cont_field['rel_id'] = $the_field_data['attributes']['data-id'];
                            } elseif (isset($the_field_data['attributes']['data-rel_id'])) {
                                $cont_field['rel_id'] = $the_field_data['attributes']['data-rel_id'];
                            }


                            $cont_field['value'] = mw('parser')->make_tags($html_to_save);

                            if ((!isset($the_field_data['attributes']['field']) or $the_field_data['attributes']['field'] == '')and isset($the_field_data['attributes']['data-field'])) {
                                $the_field_data['attributes']['field'] = $the_field_data['attributes']['data-field'];
                            }
                            $cont_field['field'] = $the_field_data['attributes']['field'];


                            if ($is_draft != false) {
                                $cont_field['is_draft'] = 1;
                                $cont_field['url'] = $this->app->url->string(true);
                                $cont_field_new = $this->save_content_field($cont_field);
                            } else {
                                $cont_field_new = $this->save_content_field($cont_field);

                            }


                            if ($save_global == true and $save_layout == false) {


                                $json_print[] = $cont_field;
                                $history_to_save = array();
                                $history_to_save['table'] = 'global';
                                // $history_to_save ['id'] = 'global';
                                $history_to_save['value'] = $cont_field['value'];
                                $history_to_save['field'] = $field;
                                $history_to_save['page_element_id'] = $page_element_id;


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
            $this->app->cache->delete('options');
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

        print $json_print;
        exit();
    }

    /**
     * Returns the homepage as array
     *
     * @category Content
     * @package Content
     */
    public function homepage()
    {

        // ->'content';
        $table = MW_DB_TABLE_CONTENT;


        $sql = "SELECT * FROM $table WHERE is_home='y' AND is_deleted='n' ORDER BY updated_on DESC LIMIT 0,1 ";

        $q = $this->app->db->query($sql, __FUNCTION__ . crc32($sql), 'content/global');
        //
        $result = $q;
        if ($result == false) {
            $sql = "SELECT * FROM $table WHERE content_type='page' AND is_deleted='n' AND url LIKE '%home%' ORDER BY updated_on DESC LIMIT 0,1 ";
            $q = $this->app->db->query($sql, __FUNCTION__ . crc32($sql), 'content/global');
            $result = $q;

        }


        if ($result != false) {
            $content = $result[0];
        }

        if (isset($content)) {
            return $content;
        }
    }

    public function save_content_admin($data, $delete_the_cache = true)
    {

        if (is_string($data)) {
            $data = parse_params($data);
        }

        $adm = $this->app->user->is_admin();

        $checks = mw_var('FORCE_SAVE_CONTENT');
        $orig_data = $data;
        $stop = false;

        if ($adm == false) {
            $data = $this->app->format->strip_unsafe($data);
            $stop = true;
            $author_id = user_id();
            if (isset($data['id']) and $data['id'] != 0 and $author_id != 0) {
                $page_data_to_check_author = $this->get_by_id($data['id']);
                if (!isset($page_data_to_check_author['created_by']) or ($page_data_to_check_author['created_by'] != $author_id)) {
                    $stop = true;
                    return array('error' => 'You dont have permission to edit this content');
                } else if (isset($page_data_to_check_author['created_by']) and ($page_data_to_check_author['created_by'] == $author_id)) {
                    $stop = false;
                }
            }
            if ($stop == true) {
                if (defined('MW_API_FUNCTION_CALL') and MW_API_FUNCTION_CALL == __FUNCTION__) {

                    if (!isset($data['captcha'])) {
                        if (isset($data['error_msg'])) {
                            return array('error' => $data['error_msg']);
                        } else {
                            return array('error' => 'Please enter a captcha answer!');

                        }
                    } else {
                        $cap = $this->app->user->session_get('captcha');
                        if ($cap == false) {
                            return array('error' => 'You must load a captcha first!');
                        }
                        if ($data['captcha'] != $cap) {
                            return array('error' => 'Invalid captcha answer!');
                        }
                    }
                }
            }


            if (isset($data['categories'])) {
                $data['category'] = $data['categories'];
            }
            if (defined('MW_API_FUNCTION_CALL') and MW_API_FUNCTION_CALL == __FUNCTION__) {
                if (isset($data['category'])) {
                    $cats_check = array();
                    if (is_array($data['category'])) {
                        foreach ($data['category'] as $cat) {
                            $cats_check[] = intval($cat);
                        }
                    } else {
                        $cats_check[] = intval($data['category']);
                    }
                    $check_if_user_can_publish = $this->app->category->get('ids=' . implode(',', $cats_check));
                    if (!empty($check_if_user_can_publish)) {
                        $user_cats = array();
                        foreach ($check_if_user_can_publish as $item) {
                            if (isset($item["users_can_create_content"]) and $item["users_can_create_content"] == 'y') {
                                $user_cats[] = $item["id"];
                                $cont_cat = $this->get('limit=1&content_type=page&subtype_value=' . $item["id"]);
                            }
                        }
                        if (!empty($user_cats)) {
                            $stop = false;
                            $data['categories'] = $user_cats;
                        }
                    }
                }
            }
        }


        if ($stop == true) {
            return array('error' => 'You are dont have permissions to save content!');
        }

        return $this->save_content($data, $delete_the_cache);

    }

    public function custom_fields($content_id, $full = true, $field_type = false)
    {

        return $this->app->fields->get('content', $content_id, $full, false, false, $field_type);


    }

    public function  save_content_field($data, $delete_the_cache = true)
    {

        $adm = $this->app->user->is_admin();
        $table = MW_DB_TABLE_CONTENT_FIELDS;
        $table_drafts = MW_DB_TABLE_CONTENT_FIELDS_DRAFTS;

        //$checks = mw_var('FORCE_SAVE_CONTENT');


        if ($adm == false) {
            return false;
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }

        if (!is_array($data)) {
            $data = array();
        }

        if (isset($data['is_draft'])) {
            $table = $table_drafts;


        }
        if (isset($data['is_draft']) and isset($data['url'])) {

            $draft_url = $this->app->db->escape_string($data['url']);
            $last_saved_date = date("Y-m-d H:i:s", strtotime("-5 minutes"));
            $last_saved_date = date("Y-m-d H:i:s", strtotime("-1 week"));

            $history_files_params = array();
            $history_files_params['order_by'] = 'id desc';
            $history_files_params['fields'] = 'id';
            $history_files_params['field'] = $data['field'];
            $history_files_params['rel'] = $data['rel'];
            $history_files_params['rel_id'] = $data['rel_id'];
            //$history_files_params['page'] = 2;

            // $history_files_params['debug'] = 1;
            $history_files_params['is_draft'] = 1;
            $history_files_params['limit'] = 20;
            $history_files_params['url'] = $draft_url;
            $history_files_params['current_page'] = 2;
            $history_files_params['created_on'] = '[lt]' . $last_saved_date;


            // $history_files_params['created_on'] = '[mt]' . $last_saved_date;
            $history_files = $this->edit_field($history_files_params);
            //
            // $history_files = $this->edit_field('order_by=id desc&fields=id&is_draft=1&all=1&limit=50&curent_page=1&url=' . $draft_url . '&created_on=[mt]' . $last_saved_date . '');
            if (is_array($history_files)) {
                $history_files_ids = $this->app->format->array_values($history_files);
            }

            if (isset($history_files_ids) and is_array($history_files_ids) and !empty($history_files_ids)) {
                $history_files_ids_impopl = implode(',', $history_files_ids);
                $del_q = "DELETE FROM {$table} WHERE id IN ($history_files_ids_impopl) ";

                $this->app->db->q($del_q);
            }


        }


        if (!isset($data['rel']) or !isset($data['rel_id'])) {
            mw_error('Error: ' . __FUNCTION__ . ' rel and rel_id is required');
        }
        //if($data['rel'] == 'global'){
        if (isset($data['field']) and !isset($data['is_draft'])) {
            $fld = $this->app->db->escape_string($data['field']);
            $fld_rel = $this->app->db->escape_string($data['rel']);
            $del_q = "DELETE FROM {$table} WHERE rel='$fld_rel' AND  field='$fld' ";
            if (isset($data['rel_id'])) {
                $i = $this->app->db->escape_string($data['rel_id']);
                $del_q .= " and  rel_id='$i' ";

            } else {
                $data['rel_id'] = 0;
            }
            $cache_group = guess_cache_group('content_fields/' . $data['rel'] . '/' . $data['rel_id']);
            $this->app->db->q($del_q);
            $this->app->cache->delete($cache_group);

            //

        }
        if (isset($fld)) {

            $this->app->cache->delete('content_fields/' . $fld);
            $this->app->cache->delete('content_fields/global/' . $fld);


        }
        $this->app->cache->delete('content_fields/global');
        if (isset($data['rel']) and isset($data['rel_id'])) {
            $cache_group = guess_cache_group('content_fields/' . $data['rel'] . '/' . $data['rel_id']);
            $this->app->cache->delete($cache_group);


            $this->app->cache->delete('content/' . $data['rel_id']);

        }
        if (isset($data['rel'])) {
            $this->app->cache->delete('content_fields/' . $data['rel']);
        }
        if (isset($data['rel']) and isset($data['rel_id'])) {
            $this->app->cache->delete('content_fields/' . $data['rel'] . '/' . $data['rel_id']);
            $this->app->cache->delete('content_fields/global/' . $data['rel'] . '/' . $data['rel_id']);
        }
        if (isset($data['field'])) {
            $this->app->cache->delete('content_fields/' . $data['field']);
        }

        $this->app->cache->delete('content_fields/global');
        //}
        $data['allow_html'] = true;

        $save = $this->app->db->save($table, $data);

        $this->app->cache->delete('content_fields');

        return $save;


    }

    public function edit_field($data, $debug = false)
    {


        $table = MW_DB_TABLE_CONTENT_FIELDS;

        $table_drafts = MW_DB_TABLE_CONTENT_FIELDS_DRAFTS;

        if (is_string($data)) {
            $data = parse_params($data);
        }

        if (!is_array($data)) {
            $data = array();
        }


        if (isset($data['is_draft'])) {
            $table = $table_drafts;
        }

        if (!isset($data['rel'])) {
            if (isset($data['rel'])) {
                if ($data['rel'] == 'content' or $data['rel'] == 'page' or $data['rel'] == 'post') {
                    $data['rel'] = 'content';
                }
                $data['rel'] = $data['rel'];
            }
        }
        if (!isset($data['rel_id'])) {
            if (isset($data['data-id'])) {
                $data['rel_id'] = $data['data-id'];
            } else {

            }
        }

        if (!isset($data['rel_id']) and !isset($data['is_draft'])) {
            $data['rel_id'] = 0;
        }

        if ((!isset($data['rel']) or !isset($data['rel_id'])) and !isset($data['is_draft'])) {
            mw_error('Error: ' . __FUNCTION__ . ' rel and rel_id is required');
        }

        if ((isset($data['rel']) and isset($data['rel_id']))) {

            $data['cache_group'] = guess_cache_group('content_fields/global/' . $data['rel'] . '/' . $data['rel_id']);
        } else {
            $data['cache_group'] = guess_cache_group('content_fields/global');

        }
        if (!isset($data['all'])) {
            $data['one'] = 1;
            $data['limit'] = 200;
        }

        $data['table'] = $table;

        $get = $this->app->db->get($data);


        if (!isset($data['full']) and isset($get['value'])) {
            return $get['value'];
        } else {
            return $get;
        }


        return false;


    }

    public function delete($data)
    {


        if (defined('MW_API_CALL')) {


            $adm = $this->app->user->is_admin();
            if ($adm == false) {
                return array('error' => 'You must be admin to delete content!');
            }
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

        $del_ids = array();
        if (isset($data['id'])) {
            $c_id = intval($data['id']);
            $del_ids[] = $c_id;
            if ($to_trash == false) {
                $this->app->db->delete_by_id('content', $c_id);
            }
        }

        if (isset($data['ids']) and is_array($data['ids'])) {
            foreach ($data['ids'] as $value) {
                $c_id = intval($value);
                $del_ids[] = $c_id;
                if ($to_trash == false) {
                    $this->app->db->delete_by_id('content', $c_id);
                }
            }

        }


        if (!empty($del_ids)) {
            $table = MW_DB_TABLE_CONTENT;

            foreach ($del_ids as $value) {
                $c_id = intval($value);
                //$q = "update $table set parent=0 where parent=$c_id ";

                if ($to_untrash == true) {
                    $q = "UPDATE $table SET is_deleted='n' WHERE id=$c_id AND  is_deleted='y' ";
                    $q = $this->app->db->query($q);
                    $q = "UPDATE $table SET is_deleted='n' WHERE parent=$c_id   AND  is_deleted='y' ";
                    $q = $this->app->db->query($q);
                    if (defined("MW_DB_TABLE_TAXONOMY")) {
                        $table1 = MW_DB_TABLE_TAXONOMY;
                        $q = "UPDATE $table1 SET is_deleted='n' WHERE rel_id=$c_id  AND  rel='content' AND  is_deleted='y' ";
                        $q = $this->app->db->query($q);
                    }

                } else if ($to_trash == false) {
                    $q = "UPDATE $table SET parent=0 WHERE parent=$c_id ";
                    $q = $this->app->db->query($q);

                    $this->app->db->delete_by_id('menus', $c_id, 'content_id');

                    if (defined("MW_DB_TABLE_MEDIA")) {
                        $table1 = MW_DB_TABLE_MEDIA;
                        $q = "DELETE FROM $table1 WHERE rel_id=$c_id  AND  rel='content'  ";
                        $q = $this->app->db->query($q);
                    }

                    if (defined("MW_DB_TABLE_TAXONOMY")) {
                        $table1 = MW_DB_TABLE_TAXONOMY;
                        $q = "DELETE FROM $table1 WHERE rel_id=$c_id  AND  rel='content'  ";
                        $q = $this->app->db->query($q);
                    }


                    if (defined("MW_DB_TABLE_TAXONOMY_ITEMS")) {
                        $table1 = MW_DB_TABLE_TAXONOMY_ITEMS;
                        $q = "DELETE FROM $table1 WHERE rel_id=$c_id  AND  rel='content'  ";
                        $q = $this->app->db->query($q);
                    }
                    if (defined("MW_DB_TABLE_CUSTOM_FIELDS")) {
                        $table1 = MW_DB_TABLE_CUSTOM_FIELDS;
                        $q = "DELETE FROM $table1 WHERE rel_id=$c_id  AND  rel='content'  ";
                        $q = $this->app->db->query($q);
                    }

                    if (defined("MW_DB_TABLE_CONTENT_DATA")) {
                        $table1 = MW_DB_TABLE_CONTENT_DATA;
                        $q = "DELETE FROM $table1 WHERE content_id=$c_id    ";
                        $q = $this->app->db->query($q);
                    }


                } else {
                    $q = "UPDATE $table SET is_deleted='y' WHERE id=$c_id ";

                    $q = $this->app->db->query($q);
                    $q = "UPDATE $table SET is_deleted='y' WHERE parent=$c_id ";
                    $q = $this->app->db->query($q);
                    if (defined("MW_DB_TABLE_TAXONOMY")) {
                        $table1 = MW_DB_TABLE_TAXONOMY;
                        $q = "UPDATE $table1 SET is_deleted='y' WHERE rel_id=$c_id  AND  rel='content' AND  is_deleted='n' ";

                        $q = $this->app->db->query($q);
                    }


                }


                $this->app->cache->delete('content/' . $c_id);
            }
            $this->app->cache->delete('menus');
            $this->app->cache->delete('content');
            $this->app->cache->delete('categories/global');


        }
        return ($del_ids);
    }

    public function edit_field_draft($data)
    {
        only_admin_access();

        $page = false;
        if (isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
            $url = explode('?', $url);
            $url = $url[0];

            if (trim($url) == '' or trim($url) == $this->app->url->site()) {
                //$page = $this->get_by_url($url);
                $page = $this->homepage();
                // var_dump($page);
            } else {

                $page = $this->get_by_url($url);
            }
        } else {
            $url = $this->app->url->string();
        }

        $this->define_constants($page);


        $table_drafts = MW_DB_TABLE_CONTENT_FIELDS_DRAFTS;


        $data = parse_params($data);

        if (isset($data['id']) and $data['id'] == 'latest_content_edit') {

            if (isset($page['id'])) {
                $page_data = $this->get_by_id($page['id']);

                $results = array();
                if (isset($page_data['title'])) {
                    $arr = array('rel' => 'content',
                        'field' => 'title',
                        'value' => $page_data['title']);
                    $results[] = $arr;
                    if (isset($page_data['content_type'])) {
                        $arr = array('rel' => $page_data['content_type'],
                            'field' => 'title',
                            'value' => $page_data['title']);
                        $results[] = $arr;
                    }
                    if (isset($page_data['subtype'])) {
                        $arr = array('rel' => $page_data['subtype'],
                            'field' => 'title',
                            'value' => $page_data['title']);
                        $results[] = $arr;
                    }
                }
                if (isset($page_data['content'])) {
                    $arr = array('rel' => 'content',
                        'field' => 'content',
                        'value' => $page_data['content']);
                    $results[] = $arr;
                    if (isset($page_data['content_type'])) {
                        $arr = array('rel' => $page_data['content_type'],
                            'field' => 'content',
                            'value' => $page_data['content']);
                        $results[] = $arr;
                    }
                    if (isset($page_data['subtype'])) {
                        $arr = array('rel' => $page_data['subtype'],
                            'field' => 'content',
                            'value' => $page_data['content']);
                        $results[] = $arr;
                    }
                }
                //$results[]

            }


        } else {
            $data['is_draft'] = 1;
            $data['full'] = 1;
            $data['all'] = 1;
            $results = $this->edit_field($data);
        }


        $ret = array();


        if ($results == false) {
            return;
        }

        $i = 0;
        foreach ($results as $item) {


            if (isset($item['value'])) {
                $field_content = htmlspecialchars_decode($item['value']);
                $field_content = $this->_decode_entities($field_content);
                $item['value'] = mw('parser')->process($field_content, $options = false);

            }

            $ret[$i] = $item;
            $i++;

        }


        return $ret;


    }

    public function _decode_entities($text)
    {

        $text = html_entity_decode($text, ENT_QUOTES, "ISO-8859-1"); #NOTE: UTF-8 does not work!
        $text = preg_replace('/&#(\d+);/me', "chr(\\1)", $text); #decimal notation
        $text = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $text); #hex notation
        return $text;
    }

    public function reorder($params)
    {
        $id = $this->app->user->is_admin();
        if ($id == false) {
            exit('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }
        $ids = $params['ids'];
        if (empty($ids)) {
            $ids = $_POST[0];
        }
        if (empty($ids)) {
            return false;
        }
        $ids = array_unique($ids);

        $ids_implode = implode(',', $ids);
        $ids_implode = $this->app->db->escape_string($ids_implode);


        $table = MW_TABLE_PREFIX . 'content';
        $maxpos = 0;
        $get_max_pos = "SELECT max(position) AS maxpos FROM $table  WHERE id IN ($ids_implode) ";
        $get_max_pos = $this->app->db->query($get_max_pos);
        if (is_array($get_max_pos) and isset($get_max_pos[0]['maxpos'])) {

            $maxpos = intval($get_max_pos[0]['maxpos']) + 1;

        }

        // $q = " SELECT id, created_on, position from $table where id IN ($ids_implode)  order by position desc  ";
        // $q = $this->app->db->query($q);
        // $max_date = $q[0]['created_on'];
        // $max_date_str = strtotime($max_date);
        $i = 1;
        foreach ($ids as $id) {
            $id = intval($id);
            $this->app->cache->delete('content/' . $id);
            //$max_date_str = $max_date_str - $i;
            //	$nw_date = date('Y-m-d H:i:s', $max_date_str);
            //$q = " UPDATE $table set created_on='$nw_date' where id = '$id'    ";
            $pox = $maxpos - $i;
            $q = " UPDATE $table SET position=$pox WHERE id=$id   ";
            //    var_dump($q);
            $q = $this->app->db->q($q);
            $i++;
        }
        //
        // var_dump($q);
        $this->app->cache->delete('content/global');
        $this->app->cache->delete('categories/global');
        return true;
    }

    /**
     * Set content to be unpublished
     *
     * Set is_active flag 'n'
     *
     * @param string|array|bool $params
     * @return string The url of the content
     * @package Content
     * @subpackage Advanced
     *
     * @uses $this->save_content()
     * @see content_set_unpublished()
     * @example
     * <code>
     * //set published the content with id 5
     * content_set_unpublished(5);
     *
     * //alternative way
     * content_set_unpublished(array('id' => 5));
     * </code>
     *
     */
    public function set_unpublished($params)
    {

        if (intval($params) > 0 and !isset($params['id'])) {
            if (!is_array($params)) {
                $id = $params;
                $params = array();
                $params['id'] = $id;
            }
        }
        $adm = $this->app->user->is_admin();
        if ($adm == false) {
            return array('error' => 'You must be admin to unpublish content!');
        }

        if (!isset($params['id'])) {
            return array('error' => 'You must provide id parameter!');
        } else {
            if (intval($params['id'] != 0)) {
                $save = array();
                $save['id'] = intval($params['id']);
                $save['is_active'] = 'n';

                $save_data = $this->save_content($save);
                return ($save_data);
            }


        }

    }

    /**
     * Set content to be published
     *
     * Set is_active flag 'y'
     *
     * @param string|array|bool $params
     * @return string The url of the content
     * @package Content
     * @subpackage Advanced
     *
     * @uses $this->save_content()
     * @see content_set_unpublished()
     * @example
     * <code>
     * //set published the content with id 5
     * api/content/set_published(5);
     *
     * //alternative way
     * api/content/set_published(array('id' => 5));
     * </code>
     *
     */
    public function set_published($params)
    {

        if (intval($params) > 0 and !isset($params['id'])) {
            if (!is_array($params)) {
                $id = $params;
                $params = array();
                $params['id'] = $id;
            }
        }
        $adm = $this->app->user->is_admin();
        if ($adm == false) {
            return array('error' => 'You must be admin to publish content!');
        }


        if (!isset($params['id'])) {
            return array('error' => 'You must provide id parameter!');
        } else {
            if (intval($params['id'] != 0)) {

                $save = array();
                $save['id'] = intval($params['id']);
                $save['is_active'] = 'y';

                $save_data = $this->save_content($save);
                return ($save_data);
            }

        }
    }

    function create_default_content($what)
    {

        if (defined("MW_NO_DEFAULT_CONTENT")) {
            return true;
        }


        switch ($what) {
            case 'shop' :
                $is_shop = $this->get('content_type=page&is_shop=y');
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
                    $find_layout = $this->app->layouts->scan();
                    if (is_array($find_layout)) {
                        foreach ($find_layout as $item) {
                            if (isset($item['layout_file']) and isset($item['is_shop'])) {
                                $add_page['layout_file'] = $item['layout_file'];
                                if (isset($item['name'])) {
                                    $add_page['title'] = $item['name'];
                                }
                            }
                        }
                    }
                    //  d($add_page);
                    $new_shop = $this->app->db->save('content', $add_page);
                    $this->app->cache->delete('content');
                    $this->app->cache->delete('categories');
                    $this->app->cache->delete('custom_fields');

                    //
                } else {

                    if (isset($is_shop[0])) {
                        $new_shop = $is_shop[0]['id'];
                    }
                }

                $posts = $this->get('content_type=post&parent=' . $new_shop);
                if ($posts == false and $new_shop != false) {
                    $add_page = array();
                    $add_page['id'] = 0;
                    $add_page['parent'] = $new_shop;
                    $add_page['title'] = "My product";
                    $add_page['url'] = "my-product";
                    $add_page['content_type'] = "post";
                    $add_page['subtype'] = "product";

                    //$new_shop = $this->save_content($add_page);
                    //$this->app->cache->delete('content');
                    //$this->app->cache->flush();
                }


                break;


            case 'blog' :
                $is_shop = $this->get('is_deleted=n&content_type=page&subtype=dynamic&is_shop=n&limit=1');
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
                    $find_layout = $this->app->layouts->scan();
                    if (is_array($find_layout)) {
                        foreach ($find_layout as $item) {
                            if (!isset($item['is_shop']) and isset($item['layout_file']) and isset($item['content_type']) and trim(strtolower($item['content_type'])) == 'dynamic') {
                                $add_page['layout_file'] = $item['layout_file'];
                                if (isset($item['name'])) {
                                    $add_page['title'] = $item['name'];
                                }
                            }
                        }

                        foreach ($find_layout as $item) {
                            if (isset($item['name']) and stristr($item['name'], 'blog') and !isset($item['is_shop']) and isset($item['layout_file']) and isset($item['content_type']) and trim(strtolower($item['content_type'])) == 'dynamic') {
                                $add_page['layout_file'] = $item['layout_file'];
                                if (isset($item['name'])) {
                                    $add_page['title'] = $item['name'];
                                }
                            }
                        }


                    }

                    $new_shop = $this->app->db->save('content', $add_page);
                    $this->app->cache->delete('content');
                    $this->app->cache->delete('categories');
                    $this->app->cache->delete('content_fields');


                    //
                } else {

                    if (isset($is_shop[0])) {
                        $new_shop = $is_shop[0]['id'];
                    }
                }


                break;

            case 'default' :
            case 'install' :
                $any = $this->get('count=1&content_type=page&limit=1');
                if (intval($any) == 0) {


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
                    $new_shop = $this->save_content($add_page);
                }

                break;

            default :
                break;
        }
    }

    public function menu_delete($id = false)
    {
        $params = parse_params($id);


        $is_admin = $this->app->user->is_admin();
        if ($is_admin == false) {
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }


        if (!isset($params['id'])) {
            mw_error('Error: id param is required.');
        }

        $id = $params['id'];

        $id = $this->app->db->escape_string($id);
        $id = htmlspecialchars_decode($id);
        $table = MODULE_DB_MENUS;

        $this->app->db->delete_by_id($table, trim($id), $field_name = 'id');

        $this->app->cache->delete('menus/global');

        return true;

    }

    public function menu_item_get($id)
    {

        $is_admin = $this->app->user->is_admin();
        if ($is_admin == false) {
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }
        $id = intval($id);

        $table = MODULE_DB_MENUS;

        return get("one=1&limit=1&table=$table&id=$id");

    }

    public function  menu_item_save($data_to_save)
    {

        $id = $this->app->user->is_admin();
        if ($id == false) {
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }

        if (isset($data_to_save['menu_id'])) {
            $data_to_save['id'] = intval($data_to_save['menu_id']);
            $this->app->cache->delete('menus/' . $data_to_save['id']);

        }

        if (!isset($data_to_save['id']) and isset($data_to_save['link_id'])) {
            $data_to_save['id'] = intval($data_to_save['link_id']);
        }

        if (isset($data_to_save['id'])) {
            $data_to_save['id'] = intval($data_to_save['id']);
            $this->app->cache->delete('menus/' . $data_to_save['id']);
        }

        if (!isset($data_to_save['id']) or intval($data_to_save['id']) == 0) {
            $data_to_save['position'] = 99999;
        }

        $url_from_content = false;
        if (isset($data_to_save['content_id']) and intval($data_to_save['content_id']) != 0) {
            $url_from_content = 1;
        }
        if (isset($data_to_save['categories_id']) and intval($data_to_save['categories_id']) != 0) {
            $url_from_content = 1;
        }
        if (isset($data_to_save['content_id']) and intval($data_to_save['content_id']) == 0) {
            unset($data_to_save['content_id']);
        }

        if (isset($data_to_save['categories_id']) and intval($data_to_save['categories_id']) == 0) {
            unset($data_to_save['categories_id']);
            //$url_from_content = 1;
        }

        if ($url_from_content != false) {
            if (isset($data_to_save['title'])) {
                $data_to_save['title'] = '';
            }
        }

        if (isset($data_to_save['categories'])) {
            unset($data_to_save['categories']);
        }

        if ($url_from_content == true and isset($data_to_save['url'])) {
            $data_to_save['url'] = '';
        }

        if (isset($data_to_save['parent_id'])) {
            $data_to_save['parent_id'] = intval($data_to_save['parent_id']);
            $this->app->cache->delete('menus/' . $data_to_save['parent_id']);
        }

        $table = MODULE_DB_MENUS;


        $data_to_save['table'] = $table;
        $data_to_save['item_type'] = 'menu_item';

        $save = $this->app->db->save($table, $data_to_save);

        $this->app->cache->delete('menus/global');

        return $save;

    }

    public function menu_item_delete($id = false)
    {

        if (is_array($id)) {
            extract($id);
        }
        if (!isset($id) or $id == false or intval($id) == 0) {
            return false;
        }

        $is_admin = $this->app->user->is_admin();
        if ($is_admin == false) {
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }

        $table = MODULE_DB_MENUS;

        $this->app->db->delete_by_id($table, intval($id), $field_name = 'id');

        $this->app->cache->delete('menus/global');

        return true;

    }

    public function menu_items_reorder($data)
    {

        $adm = $this->app->user->is_admin();
        if ($adm == false) {
            mw_error('Error: not logged in as admin.' . __FILE__ . __LINE__);
        }
        $table = MODULE_DB_MENUS;

        if (isset($data['ids_parents'])) {
            $value = $data['ids_parents'];
            if (is_array($value)) {

                foreach ($value as $value2 => $k) {
                    $k = intval($k);
                    $value2 = intval($value2);

                    $sql = "UPDATE $table SET
				parent_id=$k
				WHERE id=$value2 AND id!=$k
				AND item_type='menu_item'
				";
                    // d($sql);
                    $q = $this->app->db->q($sql);
                    $this->app->cache->delete('menus/' . $k);
                    $this->app->cache->delete('menus/' . $value2);
                }

            }
        }

        if (isset($data['ids'])) {
            $value = $data['ids'];
            if (is_array($value)) {
                $indx = array();
                $i = 0;
                foreach ($value as $value2) {
                    $indx[$i] = $value2;
                    $this->app->cache->delete('menus/' . $value2);

                    $i++;
                }

                $this->app->db->update_position_field($table, $indx);
                //return true;
            }
        }
        $this->app->cache->delete('menus/global');

        $this->app->cache->delete('menus');
        return false;
    }

    public function is_in_menu($menu_id = false, $content_id = false)
    {
        if ($menu_id == false or $content_id == false) {
            return false;
        }

        $menu_id = intval($menu_id);
        $content_id = intval($content_id);
        $check = $this->get_menu_items("limit=1&count=1&parent_id={$menu_id}&content_id=$content_id");
        $check = intval($check);
        if ($check > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_menu_items($params = false)
    {
        $table = MODULE_DB_MENUS;
        $params2 = array();
        if ($params == false) {
            $params = array();
        }
        if (is_string($params)) {
            $params = parse_str($params, $params2);
            $params = $params2;
        }
        $params['table'] = $table;
        $params['item_type'] = 'menu_item';
        return $this->app->db->get($params);
    }
}

