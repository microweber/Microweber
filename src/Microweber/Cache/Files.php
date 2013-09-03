<?php

namespace Microweber\Cache;
$mw_cache_get_content_memory = array();
$mw_skip_memory = array();


if (!defined('MW_USE_APC_CACHE')) {
    $apc_exists = function_exists('apc_fetch');
    define("MW_USE_APC_CACHE", $apc_exists);
    if (!defined('APC_EXPIRES')) {
        define("APC_EXPIRES", 60);
    }
}
if (!defined('MW_CACHE_FILES_EXTENSION')) {
    define('MW_CACHE_FILES_EXTENSION', '.php');
}
if (!defined('MW_CACHE_CONTENT_PREPEND')) {
    define('MW_CACHE_CONTENT_PREPEND', '<?php exit(); ?>');
}
if (!defined('MW_CACHE_EXPIRES')) {
    //define("MW_CACHE_EXPIRES", 600); //10 min
}
class Files
{


    public $mw_cache_saved_files = array();
    public $mw_cache_deleted_groups = array();
    public $mw_cache_mem = array();
    public $mw_cache_mem_hits = array();
    public $apc = false;
    private $mw_cache_lock_timeout = 0;
    private $mw_cache_lock_time = false;
    private $time_now = false;

    public function save($data_to_cache, $cache_id, $cache_group = 'global')
    {

        global $mw_cache_get_content_memory;
        $mw_cache_get_content_memory[$cache_group][$cache_id] = $data_to_cache;
        $this->mw_cache_mem[$cache_group][$cache_id] = $data_to_cache;
        $apc_obj = false;
        if (defined('MW_USE_APC_CACHE') and MW_USE_APC_CACHE == true) {

            if ($this->apc == false) {
                $apc_obj = new \Microweber\Cache\Apc();
                $this->apc = $apc_obj;
            } else {
                $apc_obj = $this->apc;
            }
            $apc_obj_gt = $apc_obj->save($data_to_cache, $cache_id, $cache_group);
        }
        $dir_lock = $this->cache_get_dir('delete_lock');
        return $this->cache_save($data_to_cache, $cache_id, $cache_group);

    }

    /**
     *
     *
     * Stores your data in the cache.
     * It can store any value that can be serialized, such as strings, array, objects, etc.
     *
     * @param mixed $data_to_cache
     *            your data
     * @param string $cache_id
     *            of the cache, you must define it because you will use it later to
     *            load the file.
     * @param string $cache_group
     *            (default is 'global') - this is the subfolder in the cache dir.
     *
     * @return boolean
     * @author Peter Ivanov
     * @since Version 1.0
     * @uses $this->cache_write_to_file
     */

    function cache_save($data_to_cache, $cache_id, $cache_group = 'global')
    {

        if ($data_to_cache == false) {
            $cache_file = $this->cache_get_file_path($cache_id, $cache_group);
            $cache_file = normalize_path($cache_file, false);
            if (is_file($cache_file)) {
                @unlink($cache_file);
            }
            return false;
        } else {

            $mem_var = $this->mw_cache_mem;
            if (!isset($mem_var[$cache_group])) {
                $this->mw_cache_mem[$cache_group] = array();
            }

            if ($data_to_cache == '--true--') {
                $this->mw_cache_mem[$cache_group][$cache_id] = true;
            } else {
                $this->mw_cache_mem[$cache_group][$cache_id] = $data_to_cache;

            }

            $data_to_cache = serialize($data_to_cache);

            $this->cache_write_to_file($cache_id, $data_to_cache, $cache_group);

            return true;
        }
    }


    function cache_write_to_file($cache_id, $content, $cache_group = 'global')
    {

        $is_cleaning = mw_var('is_cleaning_now');

        //if (strval(trim($cache_id)) == '' or $is_cleaning == true) {
        if (strval(trim($cache_id)) == '') {

            return false;
        }

        $cache_file = $this->cache_get_file_path($cache_id, $cache_group);
        $cache_file = normalize_path($cache_file, false);

        if (strval(trim($content)) == '') {

            return false;
        } else {
            $cache_index = MW_CACHE_DIR . 'index.php';

            $cache_content1 = MW_CACHE_CONTENT_PREPEND;

            if ($cache_content1) {

                if (is_file($cache_index) == false) {
                    $this->_mkdirs(dirname($cache_index));
                    @touch($cache_index);
                }

            }

            $see_if_dir_is_there = dirname($cache_file);

            $content1 = MW_CACHE_CONTENT_PREPEND . $content;
            if (is_dir($see_if_dir_is_there) == false) {
                $this->_mkdirs($see_if_dir_is_there);
            }
            try {
                $is_cleaning_now = mw_var('is_cleaning_now');

                // if ($is_cleaning_now == false) {
                $cache_file_temp = MW_CACHE_DIR . DS . 'tmp' . uniqid() . '.php';

                $cacheDir_temp = dirname($cache_file_temp);
                if (!is_dir($cacheDir_temp)) {
                    $this->_mkdirs($cacheDir_temp);
                }

                $cache = @file_put_contents($cache_file, $content1);


            } catch (Exception $e) {
                // $this->cache_storage[$cache_id] = $content;
                $cache = false;
            }
        }

        return $content;
    }

    public function delete($cache_group = 'global')
    {
        $apc_obj = false;

        if ($this->mw_cache_deleted_groups == null) {
            $this->mw_cache_deleted_groups = array();
        }
        if (!in_array($cache_group, $this->mw_cache_deleted_groups)) {
            $this->mw_cache_deleted_groups[] = $cache_group;

        } else {
            //print $cache_group;
            return true;
            //	print $cache_id;
        }


        if (defined('MW_USE_APC_CACHE') and MW_USE_APC_CACHE == true) {
            if ($this->apc != false) {

                $this->apc->delete($cache_group);
            }
        }


        $this->cache_clean_group($cache_group);

    }

    /**
     *
     *
     * Deletes cache directory for given $cache_group recursively.
     *
     * @param string $cache_group
     *            (default is 'global') - this is the subfolder in the cache dir.
     * @return boolean
     * @author Peter Ivanov
     * @since Version 1.0
     */

    function cache_clean_group($cache_group = 'global')
    {

        $dir_lock = $this->cache_get_dir('delete_lock');
        if (!is_dir($dir_lock)) {
            $this->_mkdirs($dir_lock);
        }
        $cache_group_lock = $dir_lock . DS . 'lock_' . trim(crc32($cache_group)) . '.php';
        //@touch($cache_group_lock);
        //d($cache_group_lock);
        // return true;
        //$this->app->format->notif(__FUNCTION__.$cache_group);
        $is_cleaning_now = mw_var('is_cleaning_now');
        $use_apc = false;
        if ($is_cleaning_now == false) {
            mw_var('is_cleaning_now', true);
        }
        try {

            $cache_group_index = $this->cache_get_file_path('index', $cache_group);

            $dir = $this->cache_get_dir('global');

            if (is_dir($dir)) {
                $this->recursive_remove_from_cache_index($dir);
            }
            $dir = $this->cache_get_dir($cache_group);

            if (is_dir($dir)) {
                $this->recursive_remove_from_cache_index($dir);
            }
            //d($dir);
            mw_var('is_cleaning_now', false);
            // clearstatcache();
            return true;
        } catch (Exception $e) {
            mw_var('is_cleaning_now', false);
            return false;
            // $cache = false;
        }
    }

    //$mw_cache_mem = array();

    /**
     *
     *
     * Gets the full path cache directory for cache group
     * Also seperates the group in subfolders for each 1000 cache files
     * for performance reasons on huge sites.
     *
     * @param string $cache_group
     *            (default is 'global') - this is the subfolder in the cache dir.
     * @return string
     * @author Peter Ivanov
     * @since Version 1.0
     */
    function cache_get_dir($cache_group = 'global', $deleted_cache_dir = false)
    {
        $function_cache_id = false;
        $args = func_get_args();
        foreach ($args as $k => $v) {
            $function_cache_id = $function_cache_id . serialize($k) . serialize($v);
        }
        $function_cache_id = __FUNCTION__ . crc32($function_cache_id);
        $cache_content = '$this->cache_get_dir_' . $function_cache_id;

        if (!defined($cache_content)) {
            // define($cache_content, '1');
        } else {
            // var_dump(constant($cache_content));
            return (constant($cache_content));
        }

        if (strval($cache_group) != '') {
            $cache_group = str_replace('/', DIRECTORY_SEPARATOR, $cache_group);
            // we will seperate the dirs by 1000s
            $cache_group_explode = explode(DIRECTORY_SEPARATOR, $cache_group);
            $cache_group_new = array();
            foreach ($cache_group_explode as $item) {
                if (intval($item) != 0) {
                    $item_temp = intval($item) / 1000;
                    $item_temp = ceil($item_temp);
                    $item_temp = $item_temp . '000';
                    $cache_group_new[] = $item_temp;
                    $cache_group_new[] = $item;
                } else {
                    $cache_group_new[] = $item;
                }
            }
            $cache_group = implode(DIRECTORY_SEPARATOR, $cache_group_new);

            $cacheDir = MW_CACHE_DIR . $cache_group;

            //$cacheDir = str_replace(':','_',$cacheDir);

//$cacheDir = str_replace(':','_',$cacheDir.DIRECTORY_SEPARATOR);
            // if (!is_dir($cacheDir)) {
            // $this->_mkdirs($cacheDir);
            // }


            if (!defined($cache_content)) {
                define($cache_content, $cacheDir);
            }

            return $cacheDir;
        } else {
            if (!defined($cache_content)) {
                define($cache_content, $cache_group);
            }

            return $cache_group;
        }
    }

    function cache_get_file_path($cache_id, $cache_group = 'global')
    {
        //  $cache_group = str_replace('/', DIRECTORY_SEPARATOR, $cache_group);

        $cache_group = str_replace(array('/', ';', ':', '.'), array(DIRECTORY_SEPARATOR, '_', '_', '_'), $cache_group);

        $cache_id = str_replace(array('/', ';', ':', '.'), array(DIRECTORY_SEPARATOR, '_', '_', '_'), $cache_id);

        $f = $this->cache_get_dir($cache_group) . DIRECTORY_SEPARATOR . $cache_id . MW_CACHE_FILES_EXTENSION;

        return $f;
    }

    public function debug()
    {

        $debug = array();
        $debug['files_cache'] = $this->cache_get_content_from_memory(true);
        if (defined('MW_USE_APC_CACHE') and MW_USE_APC_CACHE == true) {

            if ($this->apc != false) {
                $debug['apc_cache'] = $this->apc->debug();

            }
        }

        return $debug;

    }

    public function get($cache_id, $cache_group = 'global', $time = false)
    {
        if ($time == false and defined('MW_CACHE_EXPIRES') and MW_CACHE_EXPIRES != false) {
            $time = MW_CACHE_EXPIRES;
        }

        global $mw_cache_get_content_memory;
        if (is_array($mw_cache_get_content_memory) and isset($mw_cache_get_content_memory[$cache_group]) and isset($mw_cache_get_content_memory[$cache_group][$cache_id])) {
            return $mw_cache_get_content_memory[$cache_group][$cache_id];
        }


        $apc_obj = false;
        if (defined('MW_USE_APC_CACHE') and MW_USE_APC_CACHE == true) {

            if ($this->apc == false) {
                $apc_obj = new \Microweber\Cache\Apc();
                $this->apc = $apc_obj;
            } else {
                $apc_obj = $this->apc;
            }


            $apc_obj_gt = $apc_obj->get($cache_id, $cache_group, $time);
            //unset( $apc_obj );
            if ($apc_obj_gt != false) {
                $mw_cache_get_content_memory[$cache_group][$cache_id] = $apc_obj_gt;
                return $apc_obj_gt;
            }
        }


        if ($this->mw_cache_saved_files == null) {
            $this->mw_cache_saved_files = array();
        }

        if (in_array($cache_id, $this->mw_cache_saved_files)) {
            return false;
        }


        /*$dir_lock = $this->cache_get_dir('delete_lock');
        $cache_group_lock = $dir_lock . DS . 'lock_' . trim(crc32($cache_group)) . '.php';

        if (is_file($cache_group_lock)) {
            if ($this->mw_cache_lock_time == false) {
                $this->mw_cache_lock_time = filemtime($cache_group_lock);
            }
            if ($this->mw_cache_lock_time > time() - $this->mw_cache_lock_timeout) {
                mwdbg($cache_group_lock);
                // print 'aQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAaaaa';
                return false;
            } else {
                @unlink($cache_group_lock);
            }
        }*/
        $ret = $this->cache_get_content($cache_id, $cache_group, $time);
        $mw_cache_get_content_memory[$cache_group][$cache_id] = $ret;

        if ($apc_obj != false) {
            $apc_obj->save($ret, $cache_id, $cache_group);
        }

        return $ret;

    }

    /**
     *
     *
     *
     * Gets the data from the cache.
     *
     *
     * Unserilaizer for the saved data from the $this->cache_get_content_encoded() function
     *
     * @param string $cache_id
     *            of the cache
     * @param string $cache_group
     *            (default is 'global') - this is the subfolder in the cache dir.
     *
     * @return mixed
     * @author Peter Ivanov
     * @since Version 1.0
     * @uses $this->cache_get_content_encoded
     */

    //static $results_map_hits = array();

    function cache_get_content($cache_id, $cache_group = 'global', $time = false)
    {

        $mem_var = $this->mw_cache_mem;
        if (isset($mem_var[$cache_group])) {
            if (isset($mem_var[$cache_group][$cache_id]) and $mem_var[$cache_group][$cache_id] != false) {
                //		 print $mem_var[$cache_group][$cache_id];
                return $mem_var[$cache_group][$cache_id];
            }

        }

        $mode = 2;

        if (!strstr($cache_group, 'global')) {
            $mode = 1;
        }

        $cache = $this->cache_get_content_encoded($cache_id, $cache_group, $time);

        if ($cache == false or $cache == '' or $cache == '---empty---' or $cache == '---false---' or (is_array($cache) and empty($cache))) {
            $this->mw_cache_mem[$cache_group][$cache_id] = false;
            return false;
        } else {
            //   $cache = base64_decode($cache);
            $cache = @unserialize($cache);
            $this->mw_cache_mem[$cache_group][$cache_id] = $cache;
            return $cache;
        }
    }

    /**
     *
     *
     *
     * Gets encoded data from the cache as a string.
     *
     *
     * @param string $cache_id
     *            of the cache
     * @param string $cache_group
     *            (default is 'global') - this is the subfolder in the cache dir.
     *
     * @return string
     * @author Peter Ivanov
     * @since Version 1.0
     */
    function cache_get_content_encoded($cache_id, $cache_group = 'global', $time = false)
    {
        $use_apc = false;
        if ($cache_group === null) {

            $cache_group = 'global';
        }

        if ($cache_id === null) {

            return false;
        }

        $cache_id = trim($cache_id);

        $cache_group = $cache_group . DS;

        $cache_group = reduce_double_slashes($cache_group);
        if ($use_apc == false) {
            $mem = $this->cache_get_content_from_memory($cache_id, $cache_group);
            //d($mem);
            if ($mem != false) {
                //d($cache_id);
                // exit();
                return $mem;
            }
        }

        $cache_file = $this->cache_get_file_path($cache_id, $cache_group);
        $cache_file = normalize_path($cache_file, false);
        $get_file = $cache_file;

        //if (is_file($rm_f)) {
        //   @unlink($rm_f);
        //return false;
        // }

        $cache = false;
        try {

            if ($get_file != false) {


                if (isset($get_file) == true and is_file($get_file)) {
                    $t = intval($time);
                    if ($time != false and  $t > 0) {
                        if (time() - $t > filemtime($get_file)) {
                            /// cache is expired
                            return false;
                        }
                    }

                    $cache = @file_get_contents($cache_file);


                } else {
                    $this->cache_get_content_from_memory($cache_id, $cache_group, false);

                    return false;
                }
            }
        } catch (Exception $e) {
            $cache = false;
        }

        if (isset($cache) and strval($cache) != '') {

            $search = MW_CACHE_CONTENT_PREPEND;

            $replace = '';

            $count = 1;

            $cache = str_replace($search, $replace, $cache, $count);
        }

        if (isset($cache) and $cache != false and ($cache) != '') {

            if ($use_apc == false) {

                $this->cache_get_content_from_memory($cache_id, $cache_group, $cache);
            } else {
                static $apc_apc_delete;
                if ($apc_apc_delete == false) {
                    $apc_apc_delete = function_exists('apc_delete');
                }
                if ($apc_apc_delete == true and $use_apc == true) {
                    apc_delete($cache_id);
                }

                if ($use_apc == true) {

                    @apc_store($cache_id, $cache, APC_EXPIRES);
                }
            }
            return $cache;
        }


        return false;
    }

    function cache_get_content_from_memory($cache_id, $cache_group = false, $replace_with_new = false)
    {

        if (is_bool($cache_id) and $cache_id == true) {
            return $this->mw_cache_mem_hits;
        }

        $cache_id_o = $cache_id;


        $mode = 2;
        switch ($mode) {

            case 2 :
                $cache_group = (int)crc32($cache_group);
                $cache_id = (int)crc32($cache_id);

                $key = $cache_group + $cache_id;
                $key = intval($key);
                if ($replace_with_new != false) {
                    $this->mw_cache_mem[$key] = $replace_with_new;

                    $this->mw_cache_mem_hits[$cache_id_o] = 1;

                }

                if (isset($this->mw_cache_mem[$key])) {

                    $this->mw_cache_mem_hits[$cache_id_o]++;
                    return $this->mw_cache_mem[$key];
                } else {
                    return false;
                }

                break;

            default :
                break;
        }
    }

    public function purge()
    {
        $apc_obj = false;
        if (defined('MW_USE_APC_CACHE') and MW_USE_APC_CACHE == true) {
            $apc_obj = new \Microweber\Cache\Apc();
            $apc_obj->purge();
        }

        return $this->clearcache();
    }

    function clearcache()
    {


        if (!defined('MW_IS_INSTALLED') or MW_IS_INSTALLED == false) {

            $this->recursive_remove_from_cache_index(MW_CACHE_DIR, true);
            return true;
        }
        if (is_admin() == false) {
            return array('error' => 'Not logged in as admin.');

        }

        $this->recursive_remove_from_cache_index(MW_CACHE_DIR, true);

        $this->recursive_remove_from_cache_index(MW_CACHE_ROOT_DIR, true);


        return array('success' => 'Cache is cleared');

    }

    function recursive_remove_from_cache_index($directory, $empty = true)
    {
        mw_var('is_cleaning_now', true);
        static $recycle_bin;


        $this->_rmdirs($directory);

        mw_var('is_cleaning_now', false);
        return true;

    }

    function cache_file_memory_storage($path)
    {
        static $mem = array();
        $path_md = crc32($path);
        if (isset($mem["{$path_md}"]) != false) {
            return $mem[$path_md];
        }
        $cont = @ file_get_contents($path);
        $mem[$path_md] = $cont;
        return $cont;
    }

    function cache_write($data_to_cache, $cache_id, $cache_group = 'global')
    {
        return $this->cache_write_to_file($cache_id, $data_to_cache, $cache_group);
    }

    /**
     * Writes the cache file in the MW_CACHE_DIR directory.
     *
     * @param string $cache_id
     *            of the cache
     * @param string $content
     *            content for the file, must be a string, if you want to store
     *            object or array, please use the cache_save() function
     * @param string $cache_group
     *            (default is 'global') - this is the subfolder in the cache dir.
     *
     * @return string
     * @author Peter Ivanov
     * @since Version 1.0
     * @see cache_save
     */
    function cache_get_index_file_path($cache_group)
    {
        $cache_group_clean = explode("/", $cache_group);
        if (isset($cache_group_clean[0])) {
            $cache_group_clean = "cache_index_" . $cache_group_clean[0];
        } else {
            $cache_group_clean = "cache_index_global";
        }

        $cache_group_index = MW_CACHE_DIR . $cache_group_clean . '.php';
        return $cache_group_index;
    }

    function cache_clear_recycle()
    {
        return true;
        static $recycle_bin;

        if ($recycle_bin == false) {
            $recycle_bin = MW_CACHE_DIR . '_recycle_bin' . DS;
            if (is_dir($recycle_bin)) {
                $this->_rmdirs($recycle_bin, false);
            }
        }
    }


    /**
     * Makes directory recursive, returns TRUE if exists or made and false on error
     *
     * @param string $pathname
     *            The directory path.
     * @return boolean
     *          returns TRUE if exists or made or FALSE on failure.
     *
     * @package Utils
     * @category Files
     */
    private function _mkdirs($pathname)
    {
        if ($pathname == '') {
            return false;
        }
        is_dir(dirname($pathname)) || $this->_mkdirs(dirname($pathname));
        return is_dir($pathname) || @mkdir($pathname);
    }


    private function _rmdirs($directory, $empty = true)
    {
        // if the path has a slash at the end we remove it here
        if (substr($directory, -1) == DIRECTORY_SEPARATOR) {
            $directory = substr($directory, 0, -1);
        }

        // if the path is not valid or is not a directory ...
        if (!file_exists($directory) || !is_dir($directory)) {
            // ... we return false and exit the function
            return FALSE;

            // ... if the path is not readable
        } elseif (!is_readable($directory)) {
            // ... we return false and exit the function
            return FALSE;

            // ... else if the path is readable
        } else {
            // we open the directory
            $handle = opendir($directory);

            // and scan through the items inside
            while (FALSE !== ($item = readdir($handle))) {
                // if the filepointer is not the current directory
                // or the parent directory
                if ($item != '.' && $item != '..') {
                    // we build the new path to delete
                    $path = $directory . DIRECTORY_SEPARATOR . $item;

                    // if the new path is a directory
                    if (is_dir($path)) {
                        // we call this function with the new path
                        $this->_rmdirs($path, $empty);
                        // if the new path is a file
                    } else {
                        //   $path = normalize_path($path, false);
                        try {
                            if (is_file($path)) {
                                @unlink($path);
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            }

            // close the directory
            closedir($handle);

            // if the option to empty is not set to true
            if ($empty == FALSE) {
                @rmdir($directory);

            }

            return TRUE;
        }
    }

}
