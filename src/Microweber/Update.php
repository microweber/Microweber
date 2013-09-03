<?php
namespace Microweber;

if (defined("INI_SYSTEM_CHECK_DISABLED") == false) {
    define("INI_SYSTEM_CHECK_DISABLED", ini_get('disable_functions'));
}

class Update
{

    private $remote_api_url = 'http://api.microweber.com/service/update/';

    public $app;

    function __construct($app=null)
    {

        if (!is_object($this->app)) {

            if (is_object($app)) {
                $this->app = $app;
            } else {
                $this->app = mw('application');
            }

        }
    }

    function get_modules()
    {

    }

    function check($skip_cache = false)
    {
        $a = $this->app->user->is_admin();
        if ($a == false) {
            mw_error('Must be admin!');
        }
        if (!ini_get('safe_mode')) {
            if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'ini_set')) {
				ini_set("memory_limit", "160M");
                ini_set("set_time_limit", 0);
            }
            if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'set_time_limit')) {
                set_time_limit(0);
            }

        }
        $c_id = __FUNCTION__ . date("ymdh");
        //	$data['layouts'] = $t;

        if ($skip_cache == false) {

            $cache_content = $this->app->cache->get($c_id, 'update/global');
            //
            if (($cache_content) != false) {

                return $cache_content;
            }

        } else {
            $this->app->cache->delete('update/global');
        }

        $data = array();
        $data['mw_version'] = MW_VERSION;
        $data['mw_update_check_site'] = $this->app->url->site();

        $t = mw('content')->site_templates();
        $data['templates'] = $t;

        //	$t = scan_for_modules("cache_group=modules/global");
        $t = $this->app->module->get("ui=any");
        // d($t);
        $data['modules'] = $t;
        $data['module_templates'] = array();
        if (is_array($t)) {
            foreach ($t as $value) {
                if (isset($value['module'])) {
                    $module_templates = $this->app->module->templates($value['module']);
                    $mod_tpls = array();
                    if (is_array($module_templates)) {
                        foreach ($module_templates as $key1 => $value1) {

                            if (isset($value1['filename'])) {
                                $options = array();
                                $options['no_cache'] = 1;
                                $options['for_modules'] = 1;
                                $options['filename'] = $value1['filename'];
                                $module_templates_for_this = $this->app->layouts->scan($options);
                                if (isset($module_templates_for_this[0]) and is_array($module_templates_for_this[0])) {
                                    $mod_tpls[$key1] = $module_templates_for_this[0];
                                }

                            }
                        }
                        if (!empty($mod_tpls)) {

                            $data['module_templates'][$value['module']] = $mod_tpls;
                        }
                    }
                    //d($module_templates);


                }
                //d($value);
            }
        }

        $t = $this->app->layouts->get();
        $data['elements'] = $t;

        $result = $this->call('check_for_update', $data);


        //if ($skip_cache == false) {


        $count = 0;


        if (isset($result['modules'])) {
            $count = $count + sizeof($result['modules']);
        }
        if (isset($result['module_templates'])) {
            $count = $count + sizeof($result['module_templates']);
        }
        if (isset($result['core_update'])) {
            $count = $count + 1;
        }
        if (isset($result['elements'])) {
            $count = $count + sizeof($result['elements']);

        }




            $count = 0;
            if (isset($result['modules'])) {
                $count = $count + sizeof($result['modules']);
            }
            if (isset($result['module_templates'])) {
                $count = $count + sizeof($result['module_templates']);
            }
            if (isset($result['core_update'])) {
                $count = $count + 1;
            }
            if (isset($result['elements'])) {
                $count = $count + sizeof($result['elements']);
            }

            if ($count > 0) {
                $notif = array();
                $notif['replace'] = true;
                $notif['module'] = "updates";
                $notif['rel'] = "update_check";
                $notif['rel_id'] = 'updates';
                $notif['title'] = "New updates are available";
                $notif['description'] = "There are $count new updates are available";

                $this->app->notifications->save($notif);
            }


        /*if(function_exists('$this->app->notifications->save')){


               $count = 0;
                if (isset($result['modules'])) {
               $count = $count + sizeof($result['modules']);
               }
               if (isset($result['module_templates'])) {
                   $count = $count + sizeof($result['module_templates']);
               }
               if (isset($result['core_update'])) {
                   $count = $count + 1;
               }
               if (isset($result['elements'])) {
                   $count = $count + sizeof($result['elements']);
               }

           if($count > 0){
           $notif = array();
           $notif['module'] = "updates";
           $notif['rel'] = "updates";
           $notif['title'] = "New updates are avaiable";
           $notif['description'] = "There are $count new updates are available";
           // d($notif);
           //$this->app->notifications->save($notif);
           }

       }*/


        //}


		if(is_array($result)){
			$result['count'] = $count;
		}
		//$result =  $count;



		 if ($result != false) {
            $this->app->cache->save($result, $c_id, 'update/global');
        }





        return $result;
    }

    function post_update()
    {
        if (!ini_get('safe_mode')) {
            if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'ini_set')) {

                ini_set("set_time_limit", 0);
            }
            if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'set_time_limit')) {
                set_time_limit(0);
            }
        }
        mw_post_update();
    }

    function install_version($new_version)
    {
        only_admin_access();
        $params = array();
        if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'ini_set')) {

            ini_set("set_time_limit", 0);
        }
        if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'set_time_limit')) {
            set_time_limit(0);
        }

        $params['core_update'] = $new_version;
        $params['mw_update_check_site'] = $this->app->url->site();

        $result = $this->call('get_download_link', $params);
        //d($result);
        if (isset($result["core_update"])) {

            $value = trim($result["core_update"]);
            $fname = basename($value);
            $dir_c = MW_CACHE_DIR . 'update/downloads' . DS;
            if (!is_dir($dir_c)) {
                mkdir_recursive($dir_c);
            }
            $dl_file = $dir_c . $fname;
            if (!is_file($dl_file)) {
                $get = $this->app->url->download($value, $post_params = false, $save_to_file = $dl_file);
            }
            if (is_file($dl_file)) {
                $unzip = new \Microweber\Utils\Unzip();
                $target_dir = MW_ROOTPATH;
                $result = $unzip->extract($dl_file, $target_dir, $preserve_filepath = TRUE);
                $this->post_update();
                return $result;
                // skip_cache
            }

        }

    }

    function apply_updates($updates)
    {
        error_reporting(E_ERROR);

        if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'ini_set')) {

            ini_set("set_time_limit", 0);
        }
        if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'set_time_limit')) {
            set_time_limit(0);
        }

        $to_be_unzipped = array();
        $a = $this->app->user->is_admin();
        if ($a == false) {
            mw_error('Must be admin!');
        }
        print __FILE__ . __LINE__;

        print 1;
        return $updates;
        $down_dir = MW_CACHE_ROOT_DIR . 'downloads' . DS;
        if (!is_dir($down_dir)) {
            mkdir_recursive($down_dir);
        }
        if (isset($updates['mw_new_version_download'])) {
            $loc_fn = $this->app->url->slug($updates['mw_new_version_download']) . '.zip';
            $loc_fn_d = $down_dir . $loc_fn;
            if (!is_file($loc_fn_d)) {
                $loc_fn_d1 = $this->app->url->download($updates['mw_new_version_download'], false, $loc_fn_d);
            }
            if (is_file($loc_fn_d)) {
                $to_be_unzipped['root'][] = $loc_fn_d;
            }

        }

        $what_next = array('modules', 'elements');

        foreach ($what_next as $what_nex) {
            $down_dir2 = $down_dir . $what_nex . DS;
            if (!is_dir($down_dir2)) {
                mkdir_recursive($down_dir2);
            }
            // d($updates);
            // d($what_nex);
            if (isset($updates[$what_nex])) {

                foreach ($updates[$what_nex] as $key => $value) {

                    $loc_fn = $this->app->url->slug($value) . '.zip';
                    $loc_fn_d = $down_dir2 . $loc_fn;

                    if (!is_file($loc_fn_d)) {
                        $loc_fn_d1 = $this->app->url->download($value, false, $loc_fn_d);
                    }
                    if (is_file($loc_fn_d)) {
                        $to_be_unzipped[$what_nex][$key] = $loc_fn_d;
                    }
                }
            }
        }
        $unzipped = array();
        if (!empty($to_be_unzipped)) {
            if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'ini_set')) {

                ini_set("set_time_limit", 0);
            }
            if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'set_time_limit')) {
                set_time_limit(0);
            }
            foreach ($to_be_unzipped as $key => $value) {
                $unzip_loc = false;
                if ($key == 'root') {
                    $unzip_loc = MW_ROOTPATH;
                }

                if ($key == 'modules') {
                    $unzip_loc = MW_MODULES_DIR;
                }

                if ($key == 'elements') {
                    $unzip_loc = MW_ELEMENTS_DIR;
                }
                // $unzip_loc = MW_CACHE_ROOT_DIR;

                if ($unzip_loc != false and is_array($value) and !empty($value)) {
                    $unzip_loc = normalize_path($unzip_loc);
                    if (!is_dir($unzip_loc)) {
                        mkdir_recursive($unzip_loc);
                    }

                    foreach ($value as $key2 => $value2) {
                        $value2 = normalize_path($value2, 0);

                        $unzip = new Unzip();
                        $a = $unzip->extract($value2, $unzip_loc);
                        $unzip->close();
                        $unzipped = array_merge($a, $unzipped);
                        //  d($unzipped);
                        //  d($unzip_loc);
                        // d($value2);

                        if ($key == 'modules') {
                            $this->app->modules->install($key2);
                        }
                    }
                }
            }
        }
        $this->post_update();
        //$this->app->cache->delete('update/global');
        //$this->app->cache->flush();
        return $unzipped;
    }

    public function install_module_template($module, $layout)
    {

        $params = array();

        $skin_file = $this->app->module->templates($module, $layout);

        if (is_file($skin_file)) {

            $options = array();
            $options['no_cache'] = 1;
            $options['for_modules'] = 1;
            $options['filename'] = $skin_file;
            $skin_data = $this->app->layouts->scan($options);

            if ($skin_data != false) {
                $skin_data['module_template'] = $module;
                $skin_data['layout_file'] = $layout;
                $result = $this->call('get_download_link', $skin_data);
                if (isset($result["module_templates"])) {
                    foreach ($result["module_templates"] as $mod_k => $value) {


                        $fname = basename($value);
                        $dir_c = MW_CACHE_DIR . 'downloads' . DS;
                        if (!is_dir($dir_c)) {
                            mkdir_recursive($dir_c);
                        }
                        $dl_file = $dir_c . $fname;
                        if (!is_file($dl_file)) {
                            $get = $this->app->url->download($value, $post_params = false, $save_to_file = $dl_file);
                        }
                        if (is_file($dl_file)) {
                            $unzip = new \Microweber\Utils\Unzip();
                            $target_dir = MW_ROOTPATH;
                            //d($dl_file);
                            $result = $unzip->extract($dl_file, $target_dir, $preserve_filepath = TRUE);
                            // skip_cache
                        }


                    }

                }
            }
        }
        return $result;

    }

    function install_module($module_name)
    {

        $params = array();

        $params['module'] = $module_name;
        $result = $this->call('get_download_link', $params);
        if (isset($result["modules"])) {
            foreach ($result["modules"] as $mod_k => $value) {

                $fname = basename($value);
                $dir_c = MW_CACHE_DIR . 'downloads' . DS;
                if (!is_dir($dir_c)) {
                    mkdir_recursive($dir_c);
                }
                $dl_file = $dir_c . $fname;
                if (!is_file($dl_file)) {
                    $get = $this->app->url->download($value, $post_params = false, $save_to_file = $dl_file);
                }
                if (is_file($dl_file)) {
                    $unzip = new \Microweber\Utils\Unzip();
                    $target_dir = MW_ROOTPATH;
                    //d($dl_file);
                    $result = $unzip->extract($dl_file, $target_dir, $preserve_filepath = TRUE);
                    // skip_cache
                }
            }
            $params = array();
            $params['skip_cache'] = true;

            $data = $this->app->modules->get($params);
            //d($data);
            $this->app->cache->delete('update/global');
            $this->app->cache->delete('db');
            event_trigger('mw_db_init_default');
            event_trigger('mw_db_init_modules');

        }
        return $result;

    }

    function install_element($module_name)
    {

        $params = array();

        $params['element'] = $module_name;
        $result = $this->call('get_download_link', $params);
        if (isset($result["elements"])) {
            foreach ($result["elements"] as $mod_k => $value) {

                $fname = basename($value);
                $dir_c = MW_CACHE_DIR . 'downloads' . DS;
                if (!is_dir($dir_c)) {
                    mkdir_recursive($dir_c);
                }

                $dl_file = $dir_c . $fname;
                if (!is_file($dl_file)) {
                    $get = $this->app->url->download($value, $post_params = false, $save_to_file = $dl_file);
                }
                if (is_file($dl_file)) {
                    $unzip = new \Microweber\Utils\Unzip();
                    $target_dir = MW_ROOTPATH;

                    // $result = $unzip -> extract($dl_file, $target_dir, $preserve_filepath = TRUE);
                    // skip_cache
                }
            }
            $params = array();
            $params['skip_cache'] = true;

            $data = $this->app->modules->get($params);
            //d($data);

        }
        return $result;

    }


    public function send_anonymous_server_data($params = false){

        if ($params != false and is_string( $params)) {
            $params = parse_params($params);
        }

        if($params == false){
            $params = array();
        }

        $params['site_url'] = $this->app->url->site();
        $result = $this->call('send_anonymous_server_data', $params);
        return $result;
    }



    function call($method = false, $post_params = false)
    {
        $cookie = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookies' . DIRECTORY_SEPARATOR;
        if (!is_dir($cookie)) {
            mkdir($cookie);
        }
        $cookie_file = $cookie . 'cookie.txt';
        $requestUrl = $this->remote_api_url;
        if ($method != false) {
            $requestUrl = $requestUrl . '?api_function=' . $method;
        }

        $curl = new \Microweber\Utils\Curl();

        $curl->setUrl($requestUrl);
        $curl->url = $requestUrl;

        if (!is_array($post_params)) {
         //   $post_params = array();
        }
        $post_params['site_url'] = $this->app->url->site();
        $post_params['api_function'] = $method;

        if ($post_params != false and is_array($post_params)) {

            //$post_params = $this->http_build_query_for_curl($post_params);

            $post_paramsbase64 = base64_encode(serialize($post_params));
            $post_paramssjon = base64_encode(json_encode($post_params));

            $post_params_to_send = array('base64' => $post_paramsbase64, 'base64js' => $post_paramssjon,'serialized' => serialize($post_params));

            $result1 = $curl->post($post_params_to_send);

        } else {
            $result1 = false;
            // $result1 = $curl->post($post_params_to_send);
        }

        if ($result1 == '' or $result1 == false) {
            return false;
        }


        //	\curl_close($ch);
        $result = false;
        if ($result1 != false) {
            $result = json_decode($result1, 1);
        }
        if ($result == false) {
            print $result1;
        }
        return $result;
    }

    function http_build_query_for_curl($arrays, &$new = array(), $prefix = null)
    {

        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }

        foreach ($arrays AS $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (is_array($value) OR is_object($value)) {
                $this->http_build_query_for_curl($value, $new, $k);
            } else {
                $new[$k] = $value;
            }
        }
        return $new;
    }

}
