<?php


namespace Microweber;
// magic quotes fix
// http://php.net/manual/en/function.get-magic-quotes-gpc.php
// http://stackoverflow.com/questions/3117512/prevent-automatic-add-slashes-while-using-parse-str
if (function_exists('get_magic_quotes_runtime') and function_exists('set_magic_quotes_runtime') and get_magic_quotes_runtime()) {
    @set_magic_quotes_runtime(0);
}
function params_stripslashes_array_walk($array)
{
    if (is_array($array)) {
        foreach ($array as $k => $v) {
            //$new_key =  stripslashes($array);
            if (is_string($v)) {
                $array[$k] = stripslashes($v);
            } elseif (is_array($v)) {
                $array[$k] = params_stripslashes_array_walk($v);
            }
        }
    }
    return $array;
}

function params_stripslashes_array($array)
{
    return is_array($array) ? array_map('params_stripslashes_array_walk', $array) : stripslashes($array);
}

if (function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc()) {


    $_GET = params_stripslashes_array($_GET);
    $_POST = params_stripslashes_array($_POST);
    $_COOKIE = params_stripslashes_array($_COOKIE);
    $_REQUEST = params_stripslashes_array($_REQUEST);

}


// Controller Class
class Controller
{
    public $return_data = false;
    public $page_url = false;
    public $create_new_page = false;
    public $render_this_url = false;
    public $isolate_by_html_id = false;
    public $functions = array();
    public $vars = array();
    public $app;

    public function __construct($app = null)
    {
        if (!is_object($this->app)) {

            if (is_object($app)) {
                $this->app = $app;
            } else {
                $this->app = mw('application');
            }

        }


        if (!defined('MW_IS_INSTALLED')) {
            // Check if installed
            $installed = $this->app->config('installed');

            if (strval($installed) != 'yes') {
                define('MW_IS_INSTALLED', false);
                $this->install();
                exit();
            } else {
                define('MW_IS_INSTALLED', true);
            }
        }

    }

    public function index()
    {

        if ($this->render_this_url == false and $this->app->url->is_ajax() == FALSE) {
            $page_url = $this->app->url->string();
        } else {
            $page_url = $this->render_this_url;
            $this->render_this_url = false;
        }
        if ($this->page_url != false) {
            $page_url = $this->page_url;
        }

        if (strtolower($page_url) == 'index.php') {
            $page_url = '';
        }

        $page = false;
        $page_url = rtrim($page_url, '/');
        $is_admin = $this->app->user->is_admin();

        $simply_a_file = false;
        // if this is a file path it will load it
        if (isset($_GET['view'])) {
            $is_custom_view = $_GET['view'];
        } else {
            $is_custom_view = $this->app->url->param('view');
            if ($is_custom_view and $is_custom_view != false) {

                $page_url = $this->app->url->param_unset('view', $page_url);

            }

        }

        $is_editmode = $this->app->url->param('editmode');
        $is_no_editmode = $this->app->url->param('no_editmode');


        if (isset($_SESSION) and $is_editmode and $is_no_editmode == false) {

            if ($is_editmode == 'n') {
                $is_editmode = false;
                $page_url = $this->app->url->param_unset('editmode', $page_url);

                $this->app->user->session_set('back_to_editmode', true);
                $this->app->user->session_set('editmode', false);
                //sleep(1);


                //$this->app->url->redirect($this->app->url->site_url($page_url));
                //exit();
            } else {

                $editmode_sess = $this->app->user->session_get('editmode');

                $page_url = $this->app->url->param_unset('editmode', $page_url);
                if ($is_admin == true) {
                    if ($editmode_sess == false) {
                        $this->app->user->session_set('editmode', true);
                        $this->app->user->session_set('back_to_editmode', false);
                        $is_editmode = false;

                    }
                    $this->app->url->redirect($this->app->url->site_url($page_url));
                    exit();
                } else {
                    $is_editmode = false;
                }
            }
        } else {

        }

        if (isset($_SESSION) and !$is_no_editmode) {
            $is_editmode = $this->app->user->session_get('editmode');

        } else {
            $is_editmode = false;
            $page_url = $this->app->url->param_unset('no_editmode', $page_url);

        }

        $is_preview_template = $this->app->url->param('preview_template');
        if (!$is_preview_template) {
            $is_preview_template = false;
        } else {

            $page_url = $this->app->url->param_unset('preview_template', $page_url);
        }

        $preview_module = false;
        $preview_module_template = false;
        $preview_module_id = false;
        $is_preview_module = $this->app->url->param('preview_module');

        if ($is_preview_module != false) {
            if ($this->app->user->is_admin()) {
                $is_preview_module = module_name_decode($is_preview_module);
                if (is_module($is_preview_module)) {

                    $is_preview_module_skin = $this->app->url->param('preview_module_template');
                    $preview_module_id = $this->app->url->param('preview_module_id');
                    $preview_module = $is_preview_module;
                    if ($is_preview_module_skin != false) {
                        $preview_module_template = module_name_decode($is_preview_module_skin);
                        $is_editmode = false;
                    }
                }
            }
            // d($is_preview_module);
        }

        $is_layout_file = $this->app->url->param('preview_layout');
        if (!$is_layout_file) {
            $is_layout_file = false;
        } else {

            $page_url = $this->app->url->param_unset('preview_layout', $page_url);
        }

        if ($is_preview_template == true or isset($_REQUEST['isolate_content_field']) or $this->create_new_page == true) {

            if (isset($_REQUEST['content_id']) and intval($_REQUEST['content_id']) != 0) {
                $page = $this->app->content->get_by_id($_REQUEST['content_id']);

            } else {

                $page['id'] = 0;
                $page['content_type'] = 'page';
                if (isset($_GET['content_type'])) {
                    $page['content_type'] = $this->app->db->escape_string($_GET['content_type']);
                }


                template_var('new_content_type', $page['content_type']);
                $page['parent'] = '0';

                if (isset($_GET['parent_id']) and $_GET['parent_id'] != 0) {
                    $page['parent'] = intval($_GET['parent_id']);
                }

                //$page['url'] = $this->app->url->string();
                if (isset($is_preview_template) and $is_preview_template != false) {
                    $page['active_site_template'] = $is_preview_template;
                } else {

                }
                if (isset($is_layout_file) and $is_layout_file != false) {
                    $page['layout_file'] = $is_layout_file;
                }

                if (isset($_GET['inherit_template_from']) and $_GET['inherit_template_from'] != 0) {
                    $page['parent'] = intval($_GET['inherit_template_from']);
                    $inherit_from = $this->app->content->get_by_id($_GET["inherit_template_from"]);

                    //$page['parent'] =  $inherit_from ;
                    if (isset($inherit_from["layout_file"]) and $inherit_from["layout_file"] == 'inherit') {

                        $inherit_from_id = $this->app->content->get_inherited_parent($inherit_from["id"]);
                        $inherit_from = $this->app->content->get_by_id($inherit_from_id);
                    }


                    if (is_array($inherit_from) and isset($inherit_from['active_site_template'])) {
                        $page['active_site_template'] = $inherit_from['active_site_template'];
                        $is_layout_file = $page['layout_file'] = $inherit_from['layout_file'];

                    }
                }

                //$page['active_site_template'] = $page_url_segment_1;
                //$page['layout_file'] = $the_new_page_file;
                //$page['simply_a_file'] = $simply_a_file;

                template_var('new_page', $page);
            }
        }

        if (isset($is_preview_template) and $is_preview_template != false) {

            if (!defined('MW_NO_SESSION')) {
                define('MW_NO_SESSION', true);
            }
        }


        if ($page == false or $this->create_new_page == true) {
            if (trim($page_url) == '' and $preview_module == false) {
                //

                $page = $this->app->content->homepage();

            } else {
                $found_mod = false;
                $page = $this->app->content->get_by_url($page_url);
                $page_exact = $this->app->content->get_by_url($page_url, true);

                $the_active_site_template = $this->app->option->get('curent_template', 'template');
                $page_url_segment_1 = $this->app->url->segment(0, $page_url);

                if ($preview_module != false) {
                    $page_url = $preview_module;
                }

                if ($the_active_site_template == false or $the_active_site_template == '') {
                    $the_active_site_template = 'default';
                }
                if ($page_exact == false and $found_mod == false and $this->app->module->is_installed($page_url)) {

                    $found_mod = true;
                    $page['id'] = 0;
                    $page['content_type'] = 'page';
                    $page['parent'] = '0';
                    $page['url'] = $this->app->url->string();
                    $page['active_site_template'] = $the_active_site_template;
                    $mod_params = '';
                    if ($preview_module_template != false) {
                        $mod_params = $mod_params . " template='{$preview_module_template}' ";
                    }
                    if ($preview_module_id != false) {
                        $mod_params = $mod_params . " id='{$preview_module_id}' ";
                    }

                    $page['content'] = '<microweber module="' . $page_url . '" ' . $mod_params . '  />';
                    $page['simply_a_file'] = 'clean.php';
                    $page['layout_file'] = 'clean.php';
                    template_var('content', $page['content']);

                    template_var('new_page', $page);
                }

                if ($found_mod == false) {

                    if (empty($page)) {
                        $the_new_page_file = false;
                        $page_url_segment_1 = $this->app->url->segment(0, $page_url);
                        $td = MW_TEMPLATES_DIR . $page_url_segment_1;
                        $td_base = $td;

                        $page_url_segment_2 = $this->app->url->segment(1, $page_url);
                        $directly_to_file = false;
                        $page_url_segment_3 = $this->app->url->segment(-1, $page_url);

                        if (!is_dir($td_base)) {
                            $page_url_segment_1 = $the_active_site_template = $this->app->option->get('curent_template', 'template');
                            $td_base = MW_TEMPLATES_DIR . $the_active_site_template . DS;
                        } else {
                            array_shift($page_url_segment_3);
                            //d($page_url_segment_3);
                        }

                        $page_url_segment_3_str = implode(DS, $page_url_segment_3);

                        if ($page_url_segment_3_str != '') {
                            $page_url_segment_3_str = rtrim($page_url_segment_3_str, DS);
                            $page_url_segment_3_str = rtrim($page_url_segment_3_str, '\\');
                            //d($page_url_segment_3_str);
                            $page_url_segment_3_str_copy = $page_url_segment_3_str;

                            $is_ext = get_file_extension($page_url_segment_3_str);
                            if ($is_ext == false or $is_ext != 'php') {
                                $page_url_segment_3_str = $page_url_segment_3_str . '.php';
                            }

                            $td_f = $td_base . DS . $page_url_segment_3_str;
                            $td_fd = $td_base . DS . $page_url_segment_3_str_copy;
                            //  d($td_f);
                            if (is_file($td_f)) {
                                $the_new_page_file = $page_url_segment_3_str;
                                $simply_a_file = $directly_to_file = $td_f;
                            } else {
                                if (is_dir($td_fd)) {
                                    $td_fd_index = $td_fd . DS . 'index.php';
                                    if (is_file($td_fd_index)) {
                                        $the_new_page_file = $td_fd_index;
                                        $simply_a_file = $directly_to_file = $td_fd_index;
                                    }
                                } else {
                                    $is_ext = get_file_extension($td_fd);
                                    if ($is_ext == false or $is_ext != 'php') {
                                        $td_fd = $td_fd . '.php';
                                    }
                                    if (is_file($td_fd)) {
                                        $the_new_page_file = $td_fd;
                                        $simply_a_file = $directly_to_file = $td_fd;
                                    } else {
                                        $td_basedef = MW_TEMPLATES_DIR . 'default' . DS . $page_url_segment_3_str;
                                        if (is_file($td_basedef)) {
                                            $the_new_page_file = $td_basedef;
                                            $simply_a_file = $directly_to_file = $td_basedef;
                                        }
                                        // d($simply_a_file);
                                    }

                                }

                            }

                        }

                        $fname1 = 'index.php';
                        $fname2 = $page_url_segment_2 . '.php';
                        $fname3 = $page_url_segment_2;

                        $tf1 = $td . DS . $fname1;
                        $tf2 = $td . DS . $fname2;
                        $tf3 = $td . DS . $fname3;

                        if ($directly_to_file == false and is_dir($td)) {
                            if (is_file($tf1)) {
                                $simply_a_file = $tf1;
                                $the_new_page_file = $fname1;
                            }

                            if (is_file($tf2)) {
                                $simply_a_file = $tf2;
                                $the_new_page_file = $fname2;
                            }
                            if (is_file($tf3)) {
                                $simply_a_file = $tf3;
                                $the_new_page_file = $fname3;
                            }

                            if (($simply_a_file) != false) {
                                $simply_a_file = str_replace('..', '', $simply_a_file);
                            }
                        }

                        if ($simply_a_file == false) {
                            //$page = $this->app->content->homepage();
                            $page = false;
                            if (!is_array($page)) {
                                $page = array();

                                $page['id'] = 0;
                                $page['content_type'] = 'page';
                                $page['parent'] = '0';
                                $page['url'] = $this->app->url->string();
                                //  $page['active_site_template'] = $page_url_segment_1;
                                $page['simply_a_file'] = 'clean.php';
                                $page['layout_file'] = 'clean.php';


                            }


                            if (is_array($page_url_segment_3)) {

                                foreach ($page_url_segment_3 as $mvalue) {
                                    if ($found_mod == false and $this->app->module->is_installed($mvalue)) {
                                        //d($mvalue);
                                        $found_mod = true;
                                        $page['id'] = 0;
                                        $page['content_type'] = 'page';
                                        $page['parent'] = '0';
                                        $page['url'] = $this->app->url->string();
                                        $page['active_site_template'] = $page_url_segment_1;
                                        $page['content'] = '<module type="' . $mvalue . '" />';
                                        $page['simply_a_file'] = 'clean.php';
                                        $page['layout_file'] = 'clean.php';
                                        template_var('content', $page['content']);

                                        template_var('new_page', $page);
                                    }
                                }
                            }

                        } else {
                            if (!is_array($page)) {
                                $page = array();
                            }
                            $page['id'] = 0;
                            $page['content_type'] = 'page';
                            $page['parent'] = '0';
                            $page['url'] = $this->app->url->string();


                            $page['active_site_template'] = $page_url_segment_1;

                            $page['layout_file'] = $the_new_page_file;
                            $page['simply_a_file'] = $simply_a_file;

                            template_var('new_page', $page);
                            //template_var('new_page');
                        }

                    }

                    //
                }
            }
        }

        //


        if ($page['id'] != 0) {
            $page = $this->app->content->get_by_id($page['id']);


            if ($page['content_type'] == "post" and isset($page['parent'])) {
                $content = $page;
                $page = $this->app->content->get_by_id($page['parent']);
            } else {
                $content = $page;
            }
        } else {
            $content = $page;
        }


        if ($is_preview_template != false and $is_admin == true) {
            $is_preview_template = str_replace('____', DS, $is_preview_template);
            $content['active_site_template'] = $is_preview_template;
        }


        if ($is_layout_file != false and $is_admin == true) {
            $is_layout_file = str_replace('____', DS, $is_layout_file);
            if ($is_layout_file == 'inherit') {
                if (isset($_REQUEST['inherit_template_from']) and intval($_REQUEST['inherit_template_from']) != 0) {
                    $inherit_layout_from_this_page = $this->app->content->get_by_id($_REQUEST['inherit_template_from']);

                    if (isset($inherit_layout_from_this_page['layout_file']) and $inherit_layout_from_this_page['layout_file'] != 'inherit') {
                        $is_layout_file = $inherit_layout_from_this_page['layout_file'];
                    }

                    if (isset($inherit_layout_from_this_page['layout_file']) and $inherit_layout_from_this_page['layout_file'] != 'inherit') {
                        $is_layout_file = $inherit_layout_from_this_page['layout_file'];
                    }
                }
            }
            $content['layout_file'] = $is_layout_file;
        }
        if ($is_custom_view and $is_custom_view != false) {
            $content['custom_view'] = $is_custom_view;
        }

        if (isset($content['is_active']) and $content['is_active'] == 'n') {

            if ($this->app->user->is_admin() == false) {
                $page_non_active = array();
                $page_non_active['id'] = 0;
                $page_non_active['content_type'] = 'page';
                $page_non_active['parent'] = '0';
                $page_non_active['url'] = $this->app->url->string();
                $page_non_active['content'] = 'This page is not published!';
                $page_non_active['simply_a_file'] = 'clean.php';
                $page_non_active['layout_file'] = 'clean.php';
                template_var('content', $page_non_active['content']);
                $content = $page_non_active;
            }

        } else if (isset($content['is_deleted']) and $content['is_deleted'] == 'y') {
            if ($this->app->user->is_admin() == false) {
                $page_non_active = array();
                $page_non_active['id'] = 0;
                $page_non_active['content_type'] = 'page';
                $page_non_active['parent'] = '0';
                $page_non_active['url'] = $this->app->url->string();
                $page_non_active['content'] = 'This page is deleted!';
                $page_non_active['simply_a_file'] = 'clean.php';
                $page_non_active['layout_file'] = 'clean.php';
                template_var('content', $page_non_active['content']);
                $content = $page_non_active;
            }
        }

//
        $this->app->content->define_constants($content);

        //$page_data = $this->app->content->get_by_id(PAGE_ID);
//.. d($content);
        $render_file = $this->app->content->get_layout($content);

      // d($render_file);
        $content['render_file'] = $render_file;

        if ($this->return_data != false) {
            return $content;
        }

        if ($render_file) {


            //$l = new $this->app->view($render_file);
            $l = new $this->app->view($render_file);
            $l->page_id = PAGE_ID;
            $l->content_id = CONTENT_ID;
            $l->post_id = PAGE_ID;
            $l->category_id = CATEGORY_ID;
            $l->content = $content;
            $l->page = $page;
            $l->application = $this->app;

            // $l->assign('application', $this->app);


            // $l->set($l);


            $l = $l->__toString();


            if (isset($_REQUEST['isolate_content_field'])) {
                //d($_REQUEST);

                require_once (MW_APP_PATH . 'Utils' . DIRECTORY_SEPARATOR . 'phpQuery.php');
                $pq = \phpQuery::newDocument($l);

                $isolated_head = pq('head')->eq(0)->html();

                // d($isolated_head);
                $found_field = false;
                if (isset($_REQUEST['isolate_content_field'])) {
                    foreach ($pq ['[field=content]'] as $elem) {
                        //d($elem);
                        $isolated_el = $l = pq($elem)->htmlOuter();
                    }
                }

                $is_admin = $this->app->user->is_admin();
                if ($is_admin == true and isset($isolated_el) != false) {

                    $tb = MW_INCLUDES_DIR . DS . 'toolbar' . DS . 'editor_tools' . DS . 'wysiwyg' . DS . 'index.php';
                    //$layout_toolbar = file_get_contents($filename);
                    $layout_toolbar = new \Microweber\View($tb);
                    $layout_toolbar = $layout_toolbar->__toString();
                    if ($layout_toolbar != '') {

                        if (strstr($layout_toolbar, '{head}')) {
                            if ($isolated_head != false) {
                                //	d($isolated_head);
                                $layout_toolbar = str_replace('{head}', $isolated_head, $layout_toolbar);
                            }
                        }

                        if (strpos($layout_toolbar, '{content}')) {

                            $l = str_replace('{content}', $l, $layout_toolbar);

                        }
                        //$layout_toolbar = mw('parser')->process($layout_toolbar, $options = array('no_apc' => 1));

                    }
                }

            }
            event_trigger('on_load', $content);

            //$this->app->content->debug_info();
            $l = $this->app->parser->process($l, $options = false);

            if ($preview_module_id != false) {
                $_REQUEST['embed_id'] = $preview_module_id;
            }
            if (isset($_REQUEST['embed_id'])) {
                $find_embed_id = trim($_REQUEST['embed_id']);
                $l = $this->app->parser->get_by_id($find_embed_id, $l);
            }

            //mw_var('get_module_template_settings_from_options', 1);
            //	mw_var('get_module_template_settings_from_options', 0);
            $apijs_loaded = $this->app->url->site('apijs');
            $apijs_loaded = $this->app->url->site('apijs') . '?id=' . CONTENT_ID;

            $is_admin = $this->app->user->is_admin();
            $default_css = '<link rel="stylesheet" href="' . MW_INCLUDES_URL . 'default.css" type="text/css" />';
            event_trigger('site_header', TEMPLATE_NAME);


            if (function_exists('template_headers_src')) {
                $template_headers_src = template_headers_src();
                if ($template_headers_src != false and $template_headers_src != '') {
                    $l = str_ireplace('</head>', $template_headers_src . '</head>', $l);

                }
            }

            // $l = str_ireplace('</head>', $default_css . '</head>', $l);
            $l = str_ireplace('<head>', '<head>' . $default_css, $l);
            if (!stristr($l, $apijs_loaded)) {

                //$apijs_loaded = $apijs_loaded.'?id='.$content['id'];

                $default_css = '<script src="' . $apijs_loaded . '"></script>' . "\r\n";
                 $default_css.='<script src="' . MW_INCLUDES_URL . 'js/jquery-1.10.2.min.js"></script>' . "\r\n";
 
 
                //as of aug 28
                $l = str_ireplace('<head>', '<head>' . $default_css, $l);
            }


            if (isset($content['active_site_template']) and trim($content['active_site_template']) != '') {

                if (!defined('CONTENT_TEMPLATE')) {
                    define('CONTENT_TEMPLATE', $content['active_site_template']);
                }


                $custom_live_edit = TEMPLATES_DIR . DS . $content['active_site_template'] . DS . 'live_edit.css';
            } else {
                $custom_live_edit = TEMPLATE_DIR . DS . 'live_edit.css';
            }


            $custom_live_edit = normalize_path($custom_live_edit, false);
            //d($custom_live_edit);
            if (is_file($custom_live_edit)) {
                $liv_ed_css = '<link rel="stylesheet" href="' . TEMPLATE_URL . 'live_edit.css" type="text/css" />';

                $l = str_ireplace('</head>', $liv_ed_css . '</head>', $l);
            }


            if ($is_editmode == true and $this->isolate_by_html_id == false and !isset($_REQUEST['isolate_content_field'])) {

                if ($is_admin == true) {

                    $tb = MW_INCLUDES_DIR . DS . 'toolbar' . DS . 'toolbar.php';

                    $layout_toolbar = new $this->app->view($tb);
                    $is_editmode_basic = false;
                    $user_data = $this->app->user->get();
                    if (isset($user_data['basic_mode']) and trim($user_data['basic_mode'] == 'y')) {
                        $is_editmode_basic = true;
                    }

                    if (isset($is_editmode_basic) and $is_editmode_basic == true) {
                        $layout_toolbar->assign('basic_mode', true);
                    } else {
                        $layout_toolbar->assign('basic_mode', false);

                    }

                    $layout_toolbar = $layout_toolbar->__toString();
                    if ($layout_toolbar != '') {
                        $layout_toolbar = $this->app->parser->process($layout_toolbar, $options = array('no_apc' => 1));

                        $c = 1;
                        $l = str_ireplace('</body>', $layout_toolbar . '</body>', $l, $c);
                    }


                    $custom_live_edit = TEMPLATES_DIR . DS . TEMPLATE_NAME . DS . 'live_edit.php';
                    $custom_live_edit = normalize_path($custom_live_edit, false);
                    if (is_file($custom_live_edit)) {
                        $layout_live_edit = new $this->app->view($custom_live_edit);
                        $layout_live_edit = $layout_live_edit->__toString();
                        if ($layout_live_edit != '') {
                            //$layout_live_edit = $this->app->parser->process($layout_live_edit, $options = array('no_apc' => 1));
                            $l = str_ireplace('</body>', $layout_live_edit . '</body>', $l, $c);
                        }

                    }


                }
            } else if ($is_editmode == false and $is_admin == true and isset($_SESSION) and !empty($_SESSION) and isset($_SESSION['back_to_editmode'])) {
                if (!isset($_GET['isolate_content_field']) and !isset($_GET['content_id'])) {
                    //d($_REQUEST);
                    $back_to_editmode = $this->app->user->session_get('back_to_editmode');
                    if ($back_to_editmode == true) {
                        $tb = MW_INCLUDES_DIR . DS . 'toolbar' . DS . 'toolbar_back.php';

                        $layout_toolbar = new $this->app->view($tb);
                        $layout_toolbar = $layout_toolbar->__toString();
                        if ($layout_toolbar != '') {
                            $layout_toolbar = $this->app->parser->process($layout_toolbar, $options = array('no_apc' => 1));
                            $c = 1;
                            $l = str_ireplace('</body>', $layout_toolbar . '</body>', $l, $c);
                        }
                    }
                }
            }


            $l = str_replace('{TEMPLATE_URL}', TEMPLATE_URL, $l);
            $l = str_replace('{THIS_TEMPLATE_URL}', THIS_TEMPLATE_URL, $l);
            $l = str_replace('{DEFAULT_TEMPLATE_URL}', DEFAULT_TEMPLATE_URL, $l);

            $l = str_replace('%7BTEMPLATE_URL%7D', TEMPLATE_URL, $l);
            $l = str_replace('%7BTHIS_TEMPLATE_URL%7D', THIS_TEMPLATE_URL, $l);
            $l = str_replace('%7BDEFAULT_TEMPLATE_URL%7D', DEFAULT_TEMPLATE_URL, $l);
            $meta = array();
            $meta['content_image'] = '';
            $meta['content_url'] = $this->app->url->current(1);
            $meta['og_description'] = $this->app->option->get('website_description', 'website');
            $meta['og_type'] = 'website';

            if (CONTENT_ID > 0) {
                $meta = $this->app->content->get_by_id(CONTENT_ID);
                $meta['content_image'] = $this->app->media->get_picture(CONTENT_ID);
                $meta['content_url'] = $this->app->content->link(CONTENT_ID);
                $meta['og_type'] = $meta['content_type'];
                if ($meta['og_type'] != 'page' and trim($meta['subtype']) != '') {
                    $meta['og_type'] = $meta['subtype'];

                }

                if (isset($meta['description']) and $meta['description'] != '') {
                    $meta['og_description'] = $meta['description'];
                } else {
                    $meta['og_description'] = trim($this->app->format->limit($this->app->format->clean_html($meta['content']), 300));
                }

            } else {
                $meta['title'] = $this->app->option->get('website_title', 'website');
                $meta['description'] = $this->app->option->get('website_description', 'website');
                $meta['content_meta_keywords'] = $this->app->option->get('website_keywords', 'website');

            }
            $meta['og_site_name'] = $this->app->option->get('website_title', 'website');
            if (!empty($meta)) {
                if (isset($meta['content_meta_title']) and $meta['content_meta_title'] != '') {
                    $meta['title'] = $meta['content_meta_title'];
                } else if (isset($meta['title']) and $meta['title'] != '') {

                } else {
                    $meta['title'] = $this->app->option->get('website_title', 'website');
                }
                if (isset($meta['description']) and $meta['description'] != '') {
                } else {
                    $meta['description'] = $this->app->option->get('website_description', 'website');
                }
                if (isset($meta['content_meta_keywords']) and $meta['content_meta_keywords'] != '') {
                } else {
                    $meta['content_meta_keywords'] = $this->app->option->get('website_keywords', 'website');
                }
                $l = str_replace('{content_meta_title}', addslashes($meta['title']), $l);
                $l = str_replace('{content_meta_description}', addslashes($meta['description']), $l);
                $l = str_replace('{content_meta_keywords}', addslashes($meta['content_meta_keywords']), $l);

                $l = str_replace('{content_image}', ($meta['content_image']), $l);
                $l = str_replace('{content_url}', $meta['content_url'], $l);
                $l = str_replace('{og_description}', addslashes($meta['og_description']), $l);
                $l = str_replace('{og_site_name}', addslashes($meta['og_site_name']), $l);
                $l = str_replace('{og_type}', addslashes($meta['og_type']), $l);

            }

            // d(TEMPLATE_URL);
            //d(crc32($l));
            //

            $l = execute_document_ready($l);

            event_trigger('frontend');

            $is_embed = $this->app->url->param('embed');

            if ($is_embed != false) {
                $this->isolate_by_html_id = $is_embed;
            }

            if ($this->isolate_by_html_id != false) {
                $id_sel = $this->isolate_by_html_id;
                $this->isolate_by_html_id = false;
                require_once (MW_APP_PATH . 'Utils' . DIRECTORY_SEPARATOR . 'phpQuery.php');
                $pq = \phpQuery::newDocument($l);
                foreach ($pq ['#' . $id_sel] as $elem) {

                    $l = pq($elem)->htmlOuter();
                }

                // return $pq->htmlOuter();
            }
            if (isset($_SESSION)) {
                if (!headers_sent()) {
                    setcookie('last_page', $page_url, time() + 5400);
                }
            }
            print $l;
            unset($l);
            //unset($content);

            if (isset($_GET['debug'])) {

                $is_admin = $this->app->user->is_admin();
                // if ($is_admin == true) {
                $this->app->content->debug_info();
                // }
            }


            exit();
        } else {

            //  print 'NO LAYOUT IN ' . __FILE__;

            print 'Error! Please try again later.';

            //  d($template_view);
            //d($page);
            $this->app->cache->purge();
            exit();
        }

    }

    public function admin()
    {
        if (!defined('MW_BACKEND')) {
            define('MW_BACKEND', true);
        }

        //create_mw_default_options();
        $this->app->content->define_constants();
        $l = new \Microweber\View(MW_ADMIN_VIEWS_DIR . 'admin.php');
        $l = $l->__toString();
        // var_dump($l);
        event_trigger('on_load');
        $layout = $this->app->parser->process($l, $options = false);
        // $layout = $this->app->parser->process($l, $options = false);
        $layout = execute_document_ready($layout);

        print $layout;

        if (isset($_GET['debug'])) {
            $this->app->content->debug_info();
            $is_admin = $this->app->user->is_admin();
            if ($is_admin == true) {

            }
        }
        exit();
    }

    public function rss()
    {
        if (MW_IS_INSTALLED == true) {
            event_trigger('mw_cron');
        }
    }

    public function api_html()
    {
        if (!defined('MW_API_HTML_OUTPUT')) {
            define('MW_API_HTML_OUTPUT', true);
        }
        $this->api();
    }

    public function api($api_function = false, $params = false)
    {


        if (isset($_REQUEST['api_key']) and user_id() == 0) {
            api_login($_REQUEST['api_key']);
        }

        if (!defined('MW_API_CALL')) {
            define('MW_API_CALL', true);
        }

        if (!isset($_SESSION)) {
            session_start();
        }


        $mod_class_api = false;
        $mod_class_api_called = false;
        $mod_class_api_class_exist = false;
        $caller_commander = false;
        $this->app->content->define_constants();
        if ($api_function == false) {
            $api_function_full = $this->app->url->string();
            $api_function_full = $this->app->format->replace_once('api_html', '', $api_function_full);
            $api_function_full = $this->app->format->replace_once('api', '', $api_function_full);
            //$api_function_full = substr($api_function_full, 4);
        } else {
            $api_function_full = $api_function;
        }


        //$api_function_full = str_ireplace('api/', '', $api_function_full);

        $api_function_full = str_replace('..', '', $api_function_full);
        $api_function_full = str_replace('\\', '/', $api_function_full);
        $api_function_full = str_replace('//', '/', $api_function_full);

        $api_function_full = $this->app->db->escape_string($api_function_full);
        if (is_string($api_function_full)) {
            $mod_api_class = explode('/', $api_function_full);
        } else {
            $mod_api_class = $api_function_full;

        }
        $try_class_func = array_pop($mod_api_class);

        // $try_class_func2 = array_pop($mod_api_class);
        $mod_api_class_copy = $mod_api_class;
        $try_class_func2 = array_pop($mod_api_class_copy);
        $mod_api_class2 = implode(DS, $mod_api_class_copy);


        $mod_api_class = implode(DS, $mod_api_class);
		$mod_api_class_clean = ltrim($mod_api_class,'/');
		$mod_api_class_clean_uc1 = ucfirst($mod_api_class_clean);
        //d($mod_api_class);

        $mod_api_class1 = normalize_path(MW_MODULES_DIR . $mod_api_class, false) . '.php';
        $mod_api_class_native = normalize_path(MW_APP_PATH . $mod_api_class, false) . '.php';
        $mod_api_class_native_global_ns = normalize_path(MW_APP_PATH . 'classes' . DS . $mod_api_class2, false) . '.php';
$mod_api_class1_uc1 = normalize_path(MW_MODULES_DIR . $mod_api_class_clean_uc1, false) . '.php';
        $mod_api_class_native_uc1 = normalize_path(MW_APP_PATH . $mod_api_class_clean_uc1, false) . '.php';
        $mod_api_class_native_global_ns_uc1 = normalize_path(MW_APP_PATH . 'classes' . DS . $mod_api_class_clean_uc1, false) . '.php';

        $try_class = str_replace('/', '\\', $mod_api_class);
        if (class_exists($try_class, false)) {
            $caller_commander = 'class_is_already_here';
            $mod_class_api_class_exist = true;
        } else {
            //
 
 
            if (is_file($mod_api_class1)) {
                $mod_class_api = true;
                include_once ($mod_api_class1);
            } else if (is_file($mod_api_class1_uc1)) {
                 $mod_class_api = true;
                include_once ($mod_api_class1_uc1);
            } else if (is_file($mod_api_class_native_global_ns_uc1)) {
                $try_class = str_replace('/', '\\', $mod_api_class2);
                $mod_class_api = true;
			
                include_once ($mod_api_class_native_global_ns_uc1);
            } else if (is_file($mod_api_class_native_global_ns)) {
                $try_class = str_replace('/', '\\', $mod_api_class2);
                $mod_class_api = true;
                include_once ($mod_api_class_native_global_ns);
            } else if (is_file($mod_api_class_native_uc1)) {
                $mod_class_api = true;
                include_once ($mod_api_class_native_uc1);

            }else if (is_file($mod_api_class_native)) {
                $mod_class_api = true;
                include_once ($mod_api_class_native);

            }


        }


        $api_exposed = '';

        // user functions
        $api_exposed .= 'user_login user_logout ';

        // content functions
        $api_exposed .= 'save_edit ';
        $api_exposed .= 'set_language ';
        $api_exposed .= (api_expose(true));
        $api_exposed = explode(' ', $api_exposed);
        $api_exposed = array_unique($api_exposed);
        $api_exposed = array_trim($api_exposed);


        if ($api_function == false) {
            $api_function = $this->app->url->segment(1);
        }

        if (!defined('MW_API_RAW')) {
            if ($mod_class_api != false) {
                $url_segs = $this->app->url->segment(-1);
                // $api_function = ;
                //d($api_functioan);
                //d($try_class);
            }
        } else {
            if (is_array($api_function)) {
                $url_segs = $api_function;

            } else {
                $url_segs = explode('/', $api_function);

            }

        }

        switch ($caller_commander) {
            case 'class_is_already_here' :
                if ($params != false) {
                    $data = $params;
                } else if (!$_POST and !$_GET) {
                    //  $data = $this->app->url->segment(2);
                    $data = $this->app->url->params(true);
                    if (empty($data)) {
                        $data = $this->app->url->segment(2);
                    }
                } else {
                    $data = $_REQUEST;

                }

                static $loaded_classes = array();

                //$try_class_n = src_
                if (isset($loaded_classes[$try_class]) == false) {
                    $res = new $try_class($data);
                    $loaded_classes[$try_class] = $res;
                } else {
                    $res = $loaded_classes[$try_class];
                    //
                }

                if (method_exists($res, $try_class_func) or method_exists($res, $try_class_func2)) {

                    if (method_exists($res, $try_class_func2)) {
                        $try_class_func = $try_class_func2;
                    }


                    $res = $res->$try_class_func($data);

                    if (defined('MW_API_RAW')) {
                        $mod_class_api_called = true;
                        return ($res);
                    }

                    if (!defined('MW_API_HTML_OUTPUT')) {
                        header('Content-Type: application/json');

                        print json_encode($res);
                    } else {

                        print($res);
                    }
                    exit();
                }

                break;

            default :
                if ($mod_class_api == true and $mod_api_class != false) {

                    $try_class = str_replace('/', '\\', $mod_api_class);
                    $try_class_full = str_replace('/', '\\', $api_function_full);

                    $try_class_full2 = str_replace('\\', '/', $api_function_full);
                    $mod_api_class_test = explode('/', $try_class_full2);
                    $try_class_func_test = array_pop($mod_api_class_test);
                    $mod_api_class_test_full = implode('/', $mod_api_class_test);
                    $mod_api_err = false;
                    if (!defined('MW_API_RAW')) {
                        if (!in_array($try_class_full, $api_exposed) and !in_array($try_class_full2, $api_exposed)and !in_array($mod_api_class_test_full, $api_exposed)) {
                            $mod_api_err = true;

                            foreach ($api_exposed as $api_exposed_value) {
                                //d($api_exposed_value);
                                if ($mod_api_err == true) {


                                    if ($api_exposed_value == $try_class_full) {
                                        $mod_api_err = false;
                                    } else if (strtolower('\\' . $api_exposed_value) == strtolower($try_class_full)) {

                                        $mod_api_err = false;
                                    } else if ($api_exposed_value == $try_class_full2) {

                                        $mod_api_err = false;
                                    } else {
                                        $convert_slashes = str_replace('\\', '/', $try_class_full);
                                        //$convert_slashes2 = str_replace('\\', '/', $try_class_full);

                                        //d($convert_slashes);
                                        // d($try_class_full);
                                        if ($convert_slashes == $api_exposed_value) {
                                            $mod_api_err = false;
                                        }
                                    }


                                }
                            }
                        } else {
                            $mod_api_err = false;

                        }
                    }

                    if ($mod_class_api and $mod_api_err == false) {

                        if (!class_exists($try_class, false)) {
                            $remove = $url_segs;
                            $last_seg = array_pop($remove);
                            $last_prev_seg = array_pop($remove);
                            $last_prev_seg2 = array_pop($remove);


                            if (class_exists($last_prev_seg, false)) {
                                $try_class = $last_prev_seg;
                            } else if (class_exists($last_prev_seg2, false)) {
                                $try_class = $last_prev_seg2;
                            }

                        }


                        if (!class_exists($try_class, false)) {
                            $try_class_mw = ltrim($try_class, '/');
                            $try_class_mw = ltrim($try_class_mw, '\\');
                            $try_class = '\\' . __NAMESPACE__ . '\\' . $try_class_mw;
                        }


                        if (class_exists($try_class, false)) {
                            if ($params != false) {
                                $data = $params;
                            } else if (!$_POST and !$_GET) {
                                //  $data = $this->app->url->segment(2);
                                $data = $this->app->url->params(true);
                                if (empty($data)) {
                                    $data = $this->app->url->segment(2);
                                }
                            } else {
                                $data = $_REQUEST;
                            }

                            $res = new $try_class($data);
                            //if (method_exists($res, $try_class_func)) {

                            if (method_exists($res, $try_class_func) or method_exists($res, $try_class_func2)) {


                                if (method_exists($res, $try_class_func2)) {
                                    $try_class_func = $try_class_func2;
                                }

                                //exit();
                                $res = $res->$try_class_func($data);

                                $mod_class_api_called = true;

                                if (defined('MW_API_RAW')) {
                                    return ($res);
                                }

                                if (!defined('MW_API_HTML_OUTPUT')) {
                                    header('Content-Type: application/json');

                                    print json_encode($res);
                                } else {

                                    print($res);
                                }

                                exit();
                            }

                        } else {
                            mw_error('The api class ' . $try_class . '  does not exist');

                        }

                    }

                }

                break;
        }

        if ($api_function) {

        } else {
            $api_function = 'index';
        }

        if ($api_function == 'module' and $mod_class_api_called == false) {
            $this->module();
        } else {
            $err = false;
            if (!in_array($api_function, $api_exposed)) {
                $err = true;
            }


            if ($err == true) {
                foreach ($api_exposed as $api_exposed_item) {
                    if ($api_exposed_item == $api_function) {
                        $err = false;
                    }
                }
            }


            if (isset($api_function_full)) {
                foreach ($api_exposed as $api_exposed_item) {
                    if (is_string($api_exposed_item) and is_string($api_function_full)) {
                        $api_function_full = str_replace('\\', '/', $api_function_full);
                        $api_function_full = ltrim($api_function_full, '/');
                        if (strtolower($api_exposed_item) == strtolower($api_function_full)) {

                            $err = false;
                        }
                    }

                }
            }

            if ($err == false) {
                //
                if ($mod_class_api_called == false) {
                    if (!$_POST and !$_GET) {
                        //  $data = $this->app->url->segment(2);
                        $data = $this->app->url->params(true);
                        if (empty($data)) {
                            $data = $this->app->url->segment(2);
                        }
                    } else {
                        $data = $_REQUEST;
                    }

                    $api_function_full_2 = explode('/', $api_function_full);
                    unset($api_function_full_2[count($api_function_full_2) - 1]);
                    $api_function_full_2 = implode('/', $api_function_full_2);


                    if (function_exists($api_function)) {

                        $res = $api_function($data);

                    } elseif (class_exists($api_function, false)) {
                        //
                        $segs = $this->app->url->segment();
                        $mmethod = array_pop($segs);

                        $class = new $api_function($this->app);

                        if (method_exists($class, $mmethod)) {
                            $res = $class->$mmethod($data);
                        }

                    } else {

                        $api_function_full_2 = str_replace(array('..', '/'), array('', '\\'), $api_function_full_2);
                        $api_function_full_2 = __NAMESPACE__ . '\\' . $api_function_full_2;


                        if (class_exists($api_function_full_2, false)) {
                            //

                            $segs = $this->app->url->segment();
                            $mmethod = array_pop($segs);

                            $class = new  $api_function_full_2($this->app);

                            if (method_exists($class, $mmethod)) {

                                $res = $class->$mmethod($data);
                            }

                        } elseif (isset($api_function_full)) {

                            $api_function_full = str_replace('\\', '/', $api_function_full);

                            $api_function_full1 = explode('/', $api_function_full);
                            $mmethod = array_pop($api_function_full1);
                            $mclass = array_pop($api_function_full1);

                            if (class_exists($mclass, false)) {
                                $class = new $mclass($this->app);

                                if (method_exists($class, $mmethod)) {
                                    $res = $class->$mmethod($data);
                                }
                            }
                        }
                    }

                }


                $hooks = api_hook(true);
			

                if (isset($res) and isset($hooks[$api_function]) and is_array($hooks[$api_function]) and !empty($hooks[$api_function])) {

                    foreach ($hooks[$api_function] as $hook_key => $hook_value) {
                        if ($hook_value != false and $hook_value != null) {
                            //d($hook_value);
                            $hook_value($res);
                            //
                        }
                    }

                } else {
                    //error('The api function ' . $api_function . ' does not exist', __FILE__, __LINE__);
                }

                // print $api_function;
            } else {

                mw_error('The api function ' . $api_function . ' is not defined in the allowed functions list');

            }
            if (isset($res)) {
                if (!defined('MW_API_HTML_OUTPUT')) {


                    if (!headers_sent()) {
                        header('Content-Type: application/json');

                        print json_encode($res);
                    }
                } else {

                    if (is_array($res)) {
                        print_r($res);
                    } else {
                        print($res);
                    }

                }
            }
            exit();
        }
        // exit ( $api_function );
    }

    public function module()
    {
        if (!defined('MW_API_CALL')) {
            //	define('MW_API_CALL', true);
        }
        if (!defined("MW_NO_SESSION")) {
            if (!isset($_SESSION)) {
                session_start();
            }
        }
        $page = false;

        $custom_display = false;
        if (isset($_REQUEST['data-display']) and $_REQUEST['data-display'] == 'custom') {
            $custom_display = true;
        }

        if (isset($_REQUEST['data-module-name'])) {
            $_REQUEST['module'] = $_REQUEST['data-module-name'];
            $_REQUEST['data-type'] = $_REQUEST['data-module-name'];

            if (!isset($_REQUEST['id'])) {
                $_REQUEST['id'] = $this->app->url->slug($_REQUEST['data-module-name'] . '-' . date("YmdHis"));
            }

        }

        if (isset($_REQUEST['data-type'])) {
            $_REQUEST['module'] = $_REQUEST['data-type'];
        }

        if (isset($_REQUEST['display']) and $_REQUEST['display'] == 'custom') {
            $custom_display = true;
        }
        if (isset($_REQUEST['view']) and $_REQUEST['view'] == 'admin') {
            $custom_display = FALSE;
        }

        if ($custom_display == true) {
            $custom_display_id = false;
            if (isset($_REQUEST['id'])) {
                $custom_display_id = $_REQUEST['id'];
            }
            if (isset($_REQUEST['data-id'])) {
                $custom_display_id = $_REQUEST['data-id'];
            }
        }
        if (isset($_SERVER["HTTP_REFERER"])) {
            $from_url = $_SERVER["HTTP_REFERER"];

        }
        if (isset($_REQUEST['from_url'])) {
            $from_url = $_REQUEST['from_url'];
        }

        if (isset($from_url) and $from_url != false) {

            $url = $from_url;

            if (strpos($url, '#')) {
                $url = substr($url, 0, strpos($url, '#'));
            }

            //$url = $_SERVER["HTTP_REFERER"];
            $url = explode('?', $url);
            $url = $url[0];

            if (trim($url) == '' or trim($url) == $this->app->url->site()) {
                //$page = $this->app->content->get_by_url($url);
                $page = $this->app->content->homepage();
                // var_dump($page);
            } else {

                $page = $this->app->content->get_by_url($url);

            }
        } else {
            $url = $this->app->url->string();
        }

        $this->app->content->define_constants($page);

        if ($custom_display == true) {

            $u2 = $this->app->url->site();
            $u1 = str_replace($u2, '', $url);
            $this->render_this_url = $u1;
            $this->isolate_by_html_id = $custom_display_id;
            $this->index();
            exit();
        }
        $url_last = false;
        if (!isset($_REQUEST['module'])) {
            $url = $this->app->url->string(0);
            if ($url == __FUNCTION__) {
                $url = $this->app->url->string(0);
            }
            /*
             $is_ajax = $this->app->url->is_ajax();

             if ($is_ajax == true) {
             $url = $this->app->url->string(true);
             }*/

            $url = $this->app->format->replace_once('module/', '', $url);
            $url = $this->app->format->replace_once('module_api/', '', $url);
            $url = $this->app->format->replace_once('m/', '', $url);

            if (is_module($url)) {
                $_REQUEST['module'] = $url;
                $mod_from_url = $url;

            } else {
                $url1 = $url_temp = explode('/', $url);
                $url_last = array_pop($url_temp);

                $try_intil_found = false;
                $temp1 = array();
                foreach ($url_temp as $item) {

                    $temp1[] = implode('/', $url_temp);
                    $url_laset = array_pop($url_temp);

                }

                $i = 0;
                foreach ($temp1 as $item) {
                    if ($try_intil_found == false) {

                        if (is_module($item)) {

                            $url_tempx = explode('/', $url);

                            $_REQUEST['module'] = $item;
                            $url_prev = $url_last;
                            $url_last = array_pop($url_tempx);
                            $url_prev = array_pop($url_tempx);

                            // d($url_prev);
                            $mod_from_url = $item;
                            $try_intil_found = true;
                        }

                    }
                    $i++;
                }

            }
        }

        $module_info = $this->app->url->param('module_info', true);

        if ($module_info) {
            if ($_REQUEST['module']) {
                $_REQUEST['module'] = str_replace('..', '', $_REQUEST['module']);
                $try_config_file = MW_MODULES_DIR . '' . $_REQUEST['module'] . '_config.php';
                $try_config_file = normalize_path($try_config_file, false);
                if (is_file($try_config_file)) {
                    include ($try_config_file);

                    if (!isset($config) or !is_array($config)) {
                        return false;
                    }


                    if (!isset($config['icon']) or $config['icon'] == false) {
                        $config['icon'] = MW_MODULES_DIR . '' . $_REQUEST['module'] . '.png';
                        $config['icon'] = $this->app->url->link_to_file($config['icon']);
                    }
                    print json_encode($config);
                    exit();
                }
            }
        }

        $admin = $this->app->url->param('admin', true);

        $mod_to_edit = $this->app->url->param('module_to_edit', true);
        $embed = $this->app->url->param('embed', true);

        $mod_iframe = false;
        if ($mod_to_edit != false) {
            $mod_to_edit = str_ireplace('_mw_slash_replace_', '/', $mod_to_edit);
            $mod_iframe = true;
        }
        //$data = $_REQUEST;

        if (($_POST)) {
            $data = $_POST;
        } else {
            $url = $this->app->url->segment();

            if (!empty($url)) {
                foreach ($url as $k => $v) {
                    $kv = explode(':', $v);
                    if (isset($kv[0]) and isset($kv[1])) {
                        $data[$kv[0]] = $kv[1];
                    }
                }
            }
        }

        if (!isset($_POST['id']) and !isset($data['id']) and isset($_REQUEST['id'])) {
            //	$data['id'] = $_REQUEST['id'];
        }

        $is_page_id = $this->app->url->param('page_id', true);
        if ($is_page_id != '') {
            //s  $data['page_id'] = $is_page_id;
        }

        $is_REQUEST_id = $this->app->url->param('post_id', true);
        if ($is_REQUEST_id != '') {
            //  $data['post_id'] = $is_REQUEST_id;
        }

        $is_category_id = $this->app->url->param('category_id', true);
        if ($is_category_id != '') {
            //   $data['category_id'] = $is_category_id;
        }

        $is_rel = $this->app->url->param('rel', true);
        if ($is_rel != '') {
            //   $data['rel'] = $is_rel;

            if ($is_rel == 'page') {


            }

            if ($is_rel == 'post') {
                // $refpage = get_ref_page ();

            }

            if ($is_rel == 'category') {
                // $refpage = get_ref_page ();

            }
        }

        $tags = false;
        $mod_n = false;

        if (isset($data['type']) != false) {
            if (trim($data['type']) != '') {
                $mod_n = $data['data-type'] = $data['type'];
            }
        }

        if (isset($data['data-module-name'])) {
            $mod_n = $data['data-type'] = $data['data-module-name'];
            unset($data['data-module-name']);
        }

        if (isset($data['data-type']) != false) {
            $mod_n = $data['data-type'];
        }
        if (isset($data['data-module']) != false) {
            if (trim($data['data-module']) != '') {
                $mod_n = $data['module'] = $data['data-module'];
            }
        }

        if (isset($data['module'])) {
            $mod_n = $data['data-type'] = $data['module'];
            unset($data['module']);
        }

        if (isset($data['type'])) {
            $mod_n = $data['data-type'] = $data['type'];
            unset($data['type']);
        }
        if (isset($data['data-type']) != false) {
            $data['data-type'] = rtrim($data['data-type'], '/');
            $data['data-type'] = rtrim($data['data-type'], '\\');
            $data['data-type'] = str_replace('__', '/', $data['data-type']);
        }
        if (!isset($data)) {
            $data = $_REQUEST;
        }
        if (!isset($data['module']) and isset($mod_from_url) and $mod_from_url != false) {
            $data['module'] = ($mod_from_url);
        }

        if (!isset($data['id']) and isset($_REQUEST['id']) == true) {
            $data['id'] = $_REQUEST['id'];
        }

        $has_id = false;
        if (isset($data) and is_array($data)) {
            foreach ($data as $k => $v) {

                if ($k == 'id') {
                    $has_id = true;
                }

                if (is_array($v)) {
                    $v1 = $this->app->format->array_to_base64($v);
                    $tags .= "{$k}=\"$v1\" ";
                } else {
                    $tags .= "{$k}=\"$v\" ";
                }
            }
        }
        if ($has_id == false) {

            //	$mod_n = $this->app->url->slug($mod_n) . '-' . date("YmdHis");
            //	$tags .= "id=\"$mod_n\" ";
        }

        $tags = "<module {$tags} />";

        $opts = array();
        if ($_REQUEST) {
            $opts = $_REQUEST;
        }
        $opts['admin'] = $admin;

        if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER'] != false) {
            $get_arr_from_ref = $_SERVER['HTTP_REFERER'];
            if (strstr($get_arr_from_ref, $this->app->url->site())) {
                $get_arr_from_ref_arr = parse_url($get_arr_from_ref);
                if (isset($get_arr_from_ref_arr['query']) and $get_arr_from_ref_arr['query'] != '') {
                    $restore_get = parse_str($get_arr_from_ref_arr['query'], $get_array);
                    if (is_array($get_array)) {

                        mw_var('mw_restore_get', $get_array);
                    }
                    //

                }
            }
        }

        $res = $this->app->parser->process($tags, $opts);
        $res = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $res);

        if ($embed != false) {
            $p_index = MW_INCLUDES_DIR . 'api/index.php';
            $p_index = normalize_path($p_index, false);
            $l = new $this->app->view($p_index);
            $layout = $l->__toString();
            $res = str_replace('{content}', $res, $layout);
        }

        if (isset($_REQUEST['live_edit'])) {
            $p_index = MW_INCLUDES_DIR . DS . 'toolbar' . DS . 'editor_tools' . DS . 'module_settings' . DS . 'index.php';
            $p_index = normalize_path($p_index, false);
            $l = new $this->app->view($p_index);
            $l->params = $data;
            $layout = $l->__toString();
            $res = str_replace('{content}', $res, $layout);
        }
        $res = $this->app->parser->process($res, $options = false);

        $res = execute_document_ready($res);
        if (!defined('MW_NO_OUTPUT')) {
            $res = $this->app->url->replace_site_url_back($res);
            print $res;
        }

        if ($url_last != __FUNCTION__) {
            if (function_exists($url_last)) {
                //
                $this->api($url_last);
            } else if (isset($url_prev) and function_exists($url_prev)) {
                $this->api($url_last);
            } elseif (class_exists($url_last, false)) {
                $this->api($url_last);
            } elseif (isset($url_prev) and class_exists($url_prev, false)) {
                $this->api($url_prev);
            }
        }
        exit();
    }


    public function m()
    {

        if (!defined('MW_API_CALL')) {
            define('MW_API_CALL', true);
        }

        if (!defined('MW_NO_OUTPUT')) {
            define('MW_NO_OUTPUT', true);
        }
        return $this->module();
    }

    public function sitemapxml()
    {


        $sm_file = MW_CACHE_DIR . 'sitemap.xml';

        $skip = false;
        if (is_file($sm_file)) {
            $filelastmodified = filemtime($sm_file);

            if (($filelastmodified - time()) > 3 * 3600) {
                $skip = 1;
            }

        }


        if ($skip == false) {
            $map = new \Microweber\Utils\Sitemap($sm_file);
            $map->file = MW_CACHE_DIR . 'sitemap.xml';

            $cont = get_content("is_active=y&is_deleted=n&limit=2500&fields=id,updated_on&orderby=updated_on desc");


            if (!empty($cont)) {
                foreach ($cont as $item) {
                    $map->addPage($this->app->content->link($item['id']), 'daily', 1, $item['updated_on']);
                }
            }
            $map = $map->create();

        }
        $map = $sm_file;
        $fp = fopen($map, 'r');

// send the right headers
        header("Content-Type: text/xml");
        header("Content-Length: " . filesize($map));

// dump the picture and stop the script
        fpassthru($fp);
        exit;


    }

    public function apijs()
    {

        define("MW_NO_SESSION", 1);


        $ref_page = false;

        if (isset($_GET['id'])) {
            $ref_page = $this->app->content->get_by_id($_GET['id']);
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $ref_page = $_SERVER['HTTP_REFERER'];
            if ($ref_page != '') {
                $ref_page = $this->app->content->get_by_url($ref_page);
                $page_id = $ref_page['id'];
                //	$ref_page['custom_fields'] = $this->app->content->custom_fields($page_id, false);
            }

        }
        //
        header("Content-type: text/javascript");
        $this->app->content->define_constants($ref_page);


        $l = new $this->app->view(MW_INCLUDES_DIR . 'api' . DS . 'api.js');
        $l = $l->__toString();
        // var_dump($l);
        //session_write_close();
        $l = str_replace('{SITE_URL}', $this->app->url->site(), $l);
        $l = str_replace('{MW_SITE_URL}', $this->app->url->site(), $l);
        $l = str_replace('%7BSITE_URL%7D', $this->app->url->site(), $l);
        //$l = $this->app->parser->process($l, $options = array('parse_only_vars' => 1));
        print $l;
        exit();
    }

    public function plupload()
    {
        $this->app->content->define_constants();
        $f = MW_APP_PATH . 'functions' . DIRECTORY_SEPARATOR . 'plupload.php';
        require ($f);
        exit();
    }

    public function install()
    {
        $installed = MW_IS_INSTALLED;

        if ($installed == false) {
            $f = MW_INCLUDES_DIR . 'install' . DIRECTORY_SEPARATOR . 'index.php';
            require ($f);
            exit();
        } else {
            if ($this->app->user->is_admin() == true) {
                $f = MW_INCLUDES_DIR . 'install' . DIRECTORY_SEPARATOR . 'index.php';
                require ($f);
                exit();
            } else {
                mw_error('You must login as admin');
            }
        }
    }

    public function editor_tools()
    {
        if (!defined('IN_ADMIN')) {
            define('IN_ADMIN', true);
        }

        if (MW_IS_INSTALLED == true) {
            //event_trigger('mw_db_init');
            //  event_trigger('mw_cron');
        }

        $tool = $this->app->url->segment(1);

        if ($tool) {

        } else {
            $tool = 'index';
        }
        $page = false;
        if (isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
            $url = explode('?', $url);
            $url = $url[0];

            if (trim($url) == '' or trim($url) == $this->app->url->site()) {
                //$page = $this->app->content->get_by_url($url);
                $page = $this->app->content->homepage();
                // var_dump($page);
            } else {

                $page = $this->app->content->get_by_url($url);
            }
        } else {
            $url = $this->app->url->string();
        }

        $this->app->content->define_constants($page);
        $tool = str_replace('..', '', $tool);

        $p_index = MW_INCLUDES_DIR . 'toolbar/editor_tools/index.php';
        $p_index = normalize_path($p_index, false);

        $p = MW_INCLUDES_DIR . 'toolbar/editor_tools/' . $tool . '/index.php';
        $p = normalize_path($p, false);

        $l = new $this->app->view($p_index);
        $layout = $l->__toString();
        // var_dump($l);

        if (isset($_REQUEST['plain'])) {
            if (is_file($p)) {
                $p = new $this->app->view($p);
                $layout = $p->__toString();
                print $layout;
                exit();

            }
        } else if (is_file($p)) {
            $p = new $this->app->view($p);
            $layout_tool = $p->__toString();
            $layout = str_replace('{content}', $layout_tool, $layout);

        } else {
            $layout = str_replace('{content}', 'Not found!', $layout);
        }

        $layout = $this->app->parser->process($layout, $options = false);

        $layout = execute_document_ready($layout);

        $layout = str_replace('{head}', '', $layout);

        $layout = str_replace('{content}', '', $layout);

        print $layout;
        exit();
        //
        //header("HTTP/1.0 404 Not Found");
        //$v = new $this->app->view(MW_ADMIN_VIEWS_DIR . '404.php');
        //echo $v;
    }

    public function show_404()
    {
        header("HTTP/1.0 404 Not Found");
        $v = new $this->app->view(MW_ADMIN_VIEWS_DIR . '404.php');
        echo $v;
    }

    function __get($name)
    {
        if (isset($this->vars[$name]))
            return $this->vars[$name];
    }

    function __set($name, $data)
    {
        if (is_callable($data))
            $this->functions[$name] = $data;
        else
            $this->vars[$name] = $data;
    }

    function __call($method, $args)
    {
        if (isset($this->functions[$method])) {
            call_user_func_array($this->functions[$method], $args);
        } else {
            // error out
        }
    }

    function __destruct()
    {
        //print 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

    }

}