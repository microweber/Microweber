<?php

namespace Microweber\Providers;

use DB;

/**
 * Class to work with categories.
 */
class CategoryManager
{
    public $app;
    public $tables = array();
    public $table_prefix = false;

    public function __construct($app = null)
    {
        if (!is_object($this->app)) {
            if (is_object($app)) {
                $this->app = $app;
            } else {
                $this->app = mw();
            }
        }

        $prefix = $this->app->config->get('database.connections.mysql.prefix');
        $this->tables = $this->app->content_manager->tables;
        if (!isset($this->tables['categories'])) {
            $this->tables['categories'] = 'categories';
        }
        if (!isset($this->tables['categories_items'])) {
            $this->tables['categories_items'] = 'categories_items';
        }
        if (!defined('MW_DB_TABLE_TAXONOMY')) {
            define('MW_DB_TABLE_TAXONOMY', $this->tables['categories']);
        }

        if (!defined('MW_DB_TABLE_TAXONOMY_ITEMS')) {
            define('MW_DB_TABLE_TAXONOMY_ITEMS', $this->tables['categories_items']);
        }
    }

    /**
     * category_tree.
     *
     * @desc        prints category_tree of UL and LI
     *
     * @category    categories
     *
     * @author      Microweber
     *
     * @param  $params = array();
     * @param  $params ['parent'] = false; //parent id
     * @param  $params ['link'] = false; // the link on for the <a href
     * @param  $params ['active_ids'] = array(); //ids of active categories
     * @param  $params ['active_code'] = false; //inserts this code for the active ids's
     * @param  $params ['remove_ids'] = array(); //remove those caregory ids
     * @param  $params ['ul_class_name'] = false; //class name for the ul
     * @param  $params ['include_first'] = false; //if true it will include the main parent category
     * @param  $params ['content_type'] = false; //if this is set it will include only categories from desired type
     * @param  $params ['add_ids'] = array(); //if you send array of ids it will add them to the category
     * @param  $params ['orderby'] = array(); //you can order by such array $params['orderby'] = array('created_at','asc');
     * @param  $params ['content_type'] = false; //if this is set it will include only categories from desired type
     * @param  $params ['list_tag'] = 'select';
     * @param  $params ['list_item_tag'] = "option";
     */
    public function tree($params = false)
    {
        //this whole code must be reworked

        $p2 = array();
        if (!is_array($params)) {
            if (is_string($params)) {
                parse_str($params, $p2);
                $params = $p2;
            }
        }
        if (isset($params['parent'])) {
            $parent = ($params['parent']);
        } elseif (isset($params['subtype_value'])) {
            $parent = ($params['subtype_value']);
        } else {
            $parent = 0;
        }

        asort($params);
        $function_cache_id = false;
        $function_cache_id = __FUNCTION__ . crc32(serialize($params));

        $active_cat = false;
        if (defined('CATEGORY_ID')) {
            $function_cache_id .= CATEGORY_ID;
            $active_cat = CATEGORY_ID;
        }

        $cat_url = $this->get_category_id_from_url();
        if ($cat_url != false) {
            $function_cache_id .= $cat_url;
            $active_cat = $cat_url;
        } else {
            $cat_url = $this->app->url_manager->param('categories', true);
            if ($cat_url != false) {
                $function_cache_id .= $cat_url;
            }
        }

        $cache_group = 'categories/global';
        if (isset($params['nest_level'])) {
            $depth_level_counter = $params['nest_level'];
        } else {
            $depth_level_counter = 0;
        }
        $nest_level_orig = $depth_level_counter;

        if (!isset($params['no_cache'])) {
            if ($nest_level_orig == 0) {
                $cache_content = $this->app->cache_manager->get($function_cache_id, $cache_group);
                $cache_content = false;
                if (($cache_content) != false) {
                    // echo $cache_content;

                    // return;
                }
            }
        }

        $link = isset($params['link']) ? $params['link'] : false;
        if ($link == false) {
            $link = "<a href='{categories_url}' data-category-id='{id}'  {active_code} class='{active_class} {nest_level}'>{title}</a>";
        }
        $link = str_replace('data-page-id', 'data-category-id', $link);

        $active_ids = isset($params['active_ids']) ? $params['active_ids'] : array($active_cat);
        if (isset($params['active_code'])) {
            $active_code = $params['active_code'];
        } else {
            $active_code = ' active ';
        }

        if (isset($params['remove_ids'])) {
            $remove_ids = $params['remove_ids'];
        } else {
            $remove_ids = false;
        }

        if (isset($params['removed_ids_code'])) {
            $removed_ids_code = $params['removed_ids_code'];
        } else {
            $removed_ids_code = false;
        }
        $ul_class_name = '';
        $ul_class_name_deep = '';
        if (isset($params['class'])) {
            $ul_class_name = $params['class'];
        }
        if (isset($params['ul_class'])) {
            $ul_class_name = $params['ul_class'];
        }

        if (isset($params['ul_class_name'])) {
            $ul_class_name = $params['ul_class_name'];
        }
        if (isset($params['ul_class_name_deep'])) {
            $ul_class_name_deep = $params['ul_class_name_deep'];
        }
        if (isset($params['li_class'])) {
            $li_class_name = $params['li_class'];
        }
        if (isset($params['users_can_create_content'])) {
            $users_can_create_content = $params['users_can_create_content'];
        } else {
            $users_can_create_content = false;
        }
        if (isset($params['li_class_name'])) {
            $li_class_name = $params['li_class_name'];
        }

        if (!isset($li_class_name)) {
            $li_class_name = false;
        }

        if (isset($params['include_first'])) {
            $include_first = $params['include_first'];
        } else {
            $include_first = false;
        }

        if (isset($params['content_type'])) {
            $content_type = $params['content_type'];
        } else {
            $content_type = false;
        }

        if (isset($params['add_ids'])) {
            $add_ids = $params['add_ids'];
        } else {
            $add_ids = false;
        }

        if (isset($params['orderby'])) {
            $orderby = $params['orderby'];
        } else {
            $orderby = false;
        }

        $table = $this->tables['categories'];
        if (isset($params['content_id'])) {
            $params['for_page'] = $params['content_id'];
        }
        if (isset($params['content_id'])) {
            $params['for_page'] = $params['content_id'];
        }

        if (isset($params['for_page']) and $params['for_page'] != false) {
            $page = $this->app->content_manager->get_by_id($params['for_page']);

            if ($page['subtype'] == 'dynamic' and intval($page['subtype_value']) > 0) {
                $parent = $page['subtype_value'];
            } else {
                $params['rel_type'] = 'content';
                $params['rel_id'] = $params['for_page'];
                $parent = 0;
            }
        }
        $active_code_tag = false;
        if (isset($params['active_code_tag']) and $params['active_code_tag'] != false) {
            $active_code_tag = $params['active_code_tag'];
        }

        if (isset($params['subtype_value']) and $params['subtype_value'] != false) {
            $parent = $params['subtype_value'];
        }

        $skip123 = false;
        $fors = array();
        if (isset($params['parent']) and $params['parent'] != false) {
            $parent = intval($params['parent']);

        } else {
            if (!isset($params['for'])) {
                $params['for'] = 'content';
            }

            if (!isset($params['content_id']) and isset($params['for']) and $params['for'] != false) {
                $table_assoc_name = $this->app->database_manager->assoc_table_name($params['for']);
                $skip123 = true;
                $str0 = 'no_cache=true&is_deleted=0&orderby=position asc&table=' . $table . '&limit=1000&data_type=category&what=categories&' . 'parent_id=0&rel_type=' . $table_assoc_name;
                $cat_get_params = array();
                $cat_get_params['is_deleted'] = 0;
                $cat_get_params['order_by'] = 'position asc';
                $cat_get_params['limit'] = '1000';
                $cat_get_params['data_type'] = 'category';
                $cat_get_params['no_cache'] = 1;
                $cat_get_params['parent_id'] = '0';
                $cat_get_params['table'] = $table;
                $cat_get_params['rel_type'] = $table_assoc_name;
                if ($users_can_create_content != false) {
                    $cat_get_params['users_can_create_content'] = $users_can_create_content;
                    $str0 = $str0 . '&users_can_create_content=' . $users_can_create_content;
                    // unset( $cat_get_params['parent_id']);
                }
                $fors = $this->app->database_manager->get($cat_get_params);
            }

            if (!isset($params['content_id']) and isset($params['try_rel_id']) and intval($params['try_rel_id']) != 0) {
                $skip123 = true;
                $str1 = 'no_cache=true&is_deleted=0&orderby=position asc&table=' . $table . '&limit=1000&parent_id=0&rel_id=' . $params['try_rel_id'];
                $fors1 = $this->app->database_manager->get($str1);
                if (is_array($fors1)) {
                    $fors = array_merge($fors, $fors1);
                }
            }
        }

        if (isset($params['not_for_page']) and $params['not_for_page'] != false) {
            $page = $this->app->content_manager->get_page($params['not_for_page']);
            $remove_ids = array($page['subtype_value']);
        }

        $max_level = false;
        if (isset($params['max_level'])) {
            $max_level = $params['max_level'];
        }
        $list_tag = false;
        if (isset($params['list_tag'])) {
            $list_tag = $params['list_tag'];
        }
        $list_item_tag = false;
        if (isset($params['list_item_tag'])) {
            $list_item_tag = $params['list_item_tag'];
        }

        $params['table'] = $table;
        if (is_string($add_ids)) {
            $add_ids = explode(',', $add_ids);
        }

        $tree_only_ids = false;

        if (isset($params['for-content-id'])) {
            $content_cats = $this->get_for_content($params['for-content-id']);
            $fors = array();
            if (is_array($content_cats) and !empty($content_cats)) {
                if (!is_array($add_ids)) {
                    $add_ids = array();
                }
                foreach ($content_cats as $content_cat_item) {
                    if (isset($content_cat_item['id'])) {
                        $add_ids[] = $content_cat_item['id'];
                        $tree_only_ids[] = $content_cat_item['id'];
                    }
                }
            }
        } elseif (isset($params['rel_type']) and $params['rel_type'] != false and isset($params['rel_id'])) {
            $table_assoc_name = $this->app->database_manager->assoc_table_name($params['rel_type']);
            $skip123 = true;
            $users_can_create_content_q = false;
            $cat_get_params = array();
            $cat_get_params['is_deleted'] = 0;
            $cat_get_params['order_by'] = 'position asc';
            $cat_get_params['limit'] = '1000';
            $cat_get_params['data_type'] = 'category';
            $cat_get_params['rel_id'] = ($params['rel_id']);
            $cat_get_params['table'] = $table;
            $cat_get_params['rel_type'] = $table_assoc_name;
            if (isset($parent) and $parent != false) {
                $page_for_parent = $this->get_page($parent);
                $cats_for_content = $this->get_for_content($params['rel_id']);
                if ($cats_for_content) {
                    foreach ($cats_for_content as $cat_for_content) {
                        if ($parent == $cat_for_content['id']) {
                            $cat_get_params['parent_id'] = $parent;
                            unset($cat_get_params['rel_type']);
                            unset($cat_get_params['rel_id']);

                        }
                    }
                }

            }

            // $cat_get_params['no_cache'] = 1;

            if ($users_can_create_content != false) {
                $cat_get_params['users_can_create_content'] = $users_can_create_content;
            }
            $fors = $this->app->database_manager->get($cat_get_params);

        }

        ob_start();

        if ($tree_only_ids != false) {
            $this->html_tree($parent, $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name, $include_first, $content_type, $li_class_name, $add_ids, $orderby, $only_with_content = false, $visible_on_frontend = false, $depth_level_counter, $max_level, $list_tag, $list_item_tag, $active_code_tag, $ul_class_name_deep, $tree_only_ids);
        } elseif ($skip123 == false) {
            $this->html_tree($parent, $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name, $include_first, $content_type, $li_class_name, $add_ids, $orderby, $only_with_content = false, $visible_on_frontend = false, $depth_level_counter, $max_level, $list_tag, $list_item_tag, $active_code_tag, $ul_class_name_deep);
        } else {
            if ($fors != false and is_array($fors) and !empty($fors)) {
                //
                foreach ($fors as $cat) {
                    $this->html_tree($cat['id'], $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name, $include_first = true, $content_type, $li_class_name, $add_ids, $orderby, $only_with_content = false, $visible_on_frontend = false, $depth_level_counter, $max_level, $list_tag, $list_item_tag, $active_code_tag, $ul_class_name_deep);
                }
            }
        }

        $content = ob_get_contents();
        if ($nest_level_orig == 0) {
            //  $this->app->cache_manager->save($content, $function_cache_id, $cache_group);
        }

        ob_end_clean();
        echo $content;

        return;
    }

    //remove me
    public function __OLD__tree($params = false)
    {
        //this whole code must be reworked

        $p2 = array();
        if (!is_array($params)) {
            if (is_string($params)) {
                parse_str($params, $p2);
                $params = $p2;
            }
        }
        if (isset($params['parent'])) {
            $parent = ($params['parent']);
        } elseif (isset($params['subtype_value'])) {
            $parent = ($params['subtype_value']);
        } else {
            $parent = 0;
        }

        asort($params);
        $function_cache_id = false;
        $function_cache_id = __FUNCTION__ . crc32(serialize($params));

        $active_cat = false;
        if (defined('CATEGORY_ID')) {
            $function_cache_id .= CATEGORY_ID;
            $active_cat = CATEGORY_ID;
        }

        $cat_url = $this->get_category_id_from_url();
        if ($cat_url != false) {
            $function_cache_id .= $cat_url;
            $active_cat = $cat_url;
        } else {
            $cat_url = $this->app->url_manager->param('categories', true);
            if ($cat_url != false) {
                $function_cache_id .= $cat_url;
            }
        }

        $cache_group = 'categories/global';
        if (isset($params['nest_level'])) {
            $depth_level_counter = $params['nest_level'];
        } else {
            $depth_level_counter = 0;
        }
        $nest_level_orig = $depth_level_counter;

        if (!isset($params['no_cache'])) {
            if ($nest_level_orig == 0) {
                $cache_content = $this->app->cache_manager->get($function_cache_id, $cache_group);
                $cache_content = false;
                if (($cache_content) != false) {
                    echo $cache_content;

                    return;
                }
            }
        }

        $link = isset($params['link']) ? $params['link'] : false;
        if ($link == false) {
            $link = "<a href='{categories_url}' data-category-id='{id}'  {active_code} class='{active_class} {nest_level}'>{title}</a>";
        }
        $link = str_replace('data-page-id', 'data-category-id', $link);

        $active_ids = isset($params['active_ids']) ? $params['active_ids'] : array($active_cat);
        if (isset($params['active_code'])) {
            $active_code = $params['active_code'];
        } else {
            $active_code = ' active ';
        }

        if (isset($params['remove_ids'])) {
            $remove_ids = $params['remove_ids'];
        } else {
            $remove_ids = false;
        }

        if (isset($params['removed_ids_code'])) {
            $removed_ids_code = $params['removed_ids_code'];
        } else {
            $removed_ids_code = false;
        }
        $ul_class_name = '';
        $ul_class_name_deep = '';
        if (isset($params['class'])) {
            $ul_class_name = $params['class'];
        }
        if (isset($params['ul_class'])) {
            $ul_class_name = $params['ul_class'];
        }

        if (isset($params['ul_class_name'])) {
            $ul_class_name = $params['ul_class_name'];
        }
        if (isset($params['ul_class_name_deep'])) {
            $ul_class_name_deep = $params['ul_class_name_deep'];
        }
        if (isset($params['li_class'])) {
            $li_class_name = $params['li_class'];
        }
        if (isset($params['users_can_create_content'])) {
            $users_can_create_content = $params['users_can_create_content'];
        } else {
            $users_can_create_content = false;
        }
        if (isset($params['li_class_name'])) {
            $li_class_name = $params['li_class_name'];
        }

        if (!isset($li_class_name)) {
            $li_class_name = false;
        }

        if (isset($params['include_first'])) {
            $include_first = $params['include_first'];
        } else {
            $include_first = false;
        }

        if (isset($params['content_type'])) {
            $content_type = $params['content_type'];
        } else {
            $content_type = false;
        }

        if (isset($params['add_ids'])) {
            $add_ids = $params['add_ids'];
        } else {
            $add_ids = false;
        }

        if (isset($params['orderby'])) {
            $orderby = $params['orderby'];
        } else {
            $orderby = false;
        }

        $table = $this->tables['categories'];
        if (isset($params['content_id'])) {
            $params['for_page'] = $params['content_id'];
        }
        if (isset($params['content_id'])) {
            $params['for_page'] = $params['content_id'];
        }

        if (isset($params['for_page']) and $params['for_page'] != false) {
            $page = $this->app->content_manager->get_by_id($params['for_page']);

            if ($page['subtype'] == 'dynamic' and intval($page['subtype_value']) > 0) {
                $parent = $page['subtype_value'];
            } else {
                $params['rel_type'] = 'content';
                $params['rel_id'] = $params['for_page'];
                $parent = 0;
            }
        }
        $active_code_tag = false;
        if (isset($params['active_code_tag']) and $params['active_code_tag'] != false) {
            $active_code_tag = $params['active_code_tag'];
        }

        if (isset($params['subtype_value']) and $params['subtype_value'] != false) {
            $parent = $params['subtype_value'];
        }

        $skip123 = false;
        $fors = array();
        if (isset($params['parent']) and $params['parent'] != false) {
            $parent = intval($params['parent']);
        } else {
            if (!isset($params['for'])) {
                $params['for'] = 'content';
            }

            if (!isset($params['content_id']) and isset($params['for']) and $params['for'] != false) {
                $table_assoc_name = $this->app->database_manager->assoc_table_name($params['for']);
                $skip123 = true;
                $str0 = 'no_cache=true&is_deleted=0&orderby=position asc&table=' . $table . '&limit=1000&data_type=category&what=categories&' . 'parent_id=0&rel_type=' . $table_assoc_name;
                $cat_get_params = array();
                $cat_get_params['is_deleted'] = 0;
                $cat_get_params['order_by'] = 'position asc';
                $cat_get_params['limit'] = '1000';
                $cat_get_params['data_type'] = 'category';
                $cat_get_params['no_cache'] = 1;
                $cat_get_params['parent_id'] = '0';
                $cat_get_params['table'] = $table;
                $cat_get_params['rel_type'] = $table_assoc_name;
                if ($users_can_create_content != false) {
                    $cat_get_params['users_can_create_content'] = $users_can_create_content;
                    $str0 = $str0 . '&users_can_create_content=' . $users_can_create_content;
                    // unset( $cat_get_params['parent_id']);
                }
                $fors = $this->app->database_manager->get($cat_get_params);
            }

            if (!isset($params['content_id']) and isset($params['try_rel_id']) and intval($params['try_rel_id']) != 0) {
                $skip123 = true;
                $str1 = 'no_cache=true&is_deleted=0&orderby=position asc&table=' . $table . '&limit=1000&parent_id=0&rel_id=' . $params['try_rel_id'];
                $fors1 = $this->app->database_manager->get($str1);
                if (is_array($fors1)) {
                    $fors = array_merge($fors, $fors1);
                }
            }
        }

        if (isset($params['not_for_page']) and $params['not_for_page'] != false) {
            $page = $this->app->content_manager->get_page($params['not_for_page']);
            $remove_ids = array($page['subtype_value']);
        }

        $max_level = false;
        if (isset($params['max_level'])) {
            $max_level = $params['max_level'];
        }
        $list_tag = false;
        if (isset($params['list_tag'])) {
            $list_tag = $params['list_tag'];
        }
        $list_item_tag = false;
        if (isset($params['list_item_tag'])) {
            $list_item_tag = $params['list_item_tag'];
        }

        $params['table'] = $table;
        if (is_string($add_ids)) {
            $add_ids = explode(',', $add_ids);
        }

        $tree_only_ids = false;

        if (isset($params['for-content-id'])) {
            $content_cats = $this->get_for_content($params['for-content-id']);
            $fors = array();
            if (is_array($content_cats) and !empty($content_cats)) {
                if (!is_array($add_ids)) {
                    $add_ids = array();
                }
                foreach ($content_cats as $content_cat_item) {
                    if (isset($content_cat_item['id'])) {
                        $add_ids[] = $content_cat_item['id'];
                        $tree_only_ids[] = $content_cat_item['id'];
                    }
                }
            }
        } elseif (isset($params['rel_type']) and $params['rel_type'] != false and isset($params['rel_id'])) {
            $table_assoc_name = $this->app->database_manager->assoc_table_name($params['rel_type']);
            $skip123 = true;
            $users_can_create_content_q = false;
            $cat_get_params = array();
            $cat_get_params['is_deleted'] = 0;
            $cat_get_params['order_by'] = 'position asc';
            $cat_get_params['limit'] = '1000';
            $cat_get_params['data_type'] = 'category';
            $cat_get_params['rel_id'] = ($params['rel_id']);
            $cat_get_params['table'] = $table;
            $cat_get_params['rel_type'] = $table_assoc_name;
            $cat_get_params['no_cache'] = 1;

            if ($users_can_create_content != false) {
                $cat_get_params['users_can_create_content'] = $users_can_create_content;
            }
            $fors = $this->app->database_manager->get($cat_get_params);
        }

        ob_start();

        if ($tree_only_ids != false) {
            $this->html_tree($parent, $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name, $include_first, $content_type, $li_class_name, $add_ids, $orderby, $only_with_content = false, $visible_on_frontend = false, $depth_level_counter, $max_level, $list_tag, $list_item_tag, $active_code_tag, $ul_class_name_deep, $tree_only_ids);
        } elseif ($skip123 == false) {
            $this->html_tree($parent, $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name, $include_first, $content_type, $li_class_name, $add_ids, $orderby, $only_with_content = false, $visible_on_frontend = false, $depth_level_counter, $max_level, $list_tag, $list_item_tag, $active_code_tag, $ul_class_name_deep);
        } else {
            if ($fors != false and is_array($fors) and !empty($fors)) {
                foreach ($fors as $cat) {
                    $this->html_tree($cat['id'], $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name, $include_first = true, $content_type, $li_class_name, $add_ids, $orderby, $only_with_content = false, $visible_on_frontend = false, $depth_level_counter, $max_level, $list_tag, $list_item_tag, $active_code_tag, $ul_class_name_deep);
                }
            }
        }

        $content = ob_get_contents();
        if ($nest_level_orig == 0) {
            $this->app->cache_manager->save($content, $function_cache_id, $cache_group);
        }

        ob_end_clean();
        echo $content;

        return;
    }

    /**
     * `.
     *
     * Prints the selected categories as an <UL> tree, you might pass several
     * options for more flexibility
     *
     * @param
     *            array
     * @param
     *            boolean
     *
     * @version 1.0
     *
     * @since   Version 1.0
     */
    private function html_tree($parent, $link = false, $active_ids = false, $active_code = false, $remove_ids = false, $removed_ids_code = false, $ul_class_name = false, $include_first = false, $content_type = false, $li_class_name = false, $add_ids = false, $orderby = false, $only_with_content = false, $visible_on_frontend = false, $depth_level_counter = 0, $max_level = false, $list_tag = false, $list_item_tag = false, $active_code_tag = false, $ul_class_deep = false, $only_ids = false)
    {
        $db_t_content = $this->tables['content'];

        $table = $db_categories = $this->tables['categories'];

        if ($parent == false) {
            $parent = (0);

            $include_first = false;
        } else {
            $parent = (int)$parent;
        }

        if (!is_array($orderby)) {
            $orderby[0] = 'position';

            $orderby[1] = 'ASC';
        }

        if (isset($remove_ids) and !is_array($remove_ids)) {
            $temp = intval($remove_ids);

            $remove_ids_q = " and id not in ($temp) ";
        } elseif (is_array($remove_ids) and !empty($remove_ids)) {
            $remove_ids_q = implode(',', $remove_ids);
            if ($remove_ids_q != '') {
                $remove_ids_q = " and id not in ($remove_ids_q) ";
            }
        } else {
            $remove_ids_q = false;
        }

        if (!empty($add_ids)) {
            $add_ids_q = implode(',', $add_ids);

            $add_ids_q = " and id in ($add_ids_q) ";
        } else {
            $add_ids_q = false;
        }

        if ($max_level != false and $depth_level_counter != false) {
            if (intval($depth_level_counter) >= intval($max_level)) {
                echo '';

                return;
            }
        }

        if (isset($list_tag) == false or $list_tag == false) {
            $list_tag = 'ul';
        }

        if (isset($active_code_tag) == false or $active_code_tag == false) {
            $active_code_tag = '';
        }

        if (isset($list_item_tag) == false or $list_item_tag == false) {
            $list_item_tag = 'li';
        }

        if (empty($limit)) {
            $limit = array(0, 10);
        }
        $table = $this->app->database_manager->real_table_name($table);
        $content_type = addslashes($content_type);
        $hard_limit = ' LIMIT 300 ';
        $inf_loop_fix = "  and $table.id!=$table.parent_id  ";
        //	$inf_loop_fix = "     ";

        if ($only_ids != false) {
            if (is_string($only_ids)) {
                $only_ids = explode(',', $only_ids);
            }

            $sql = "SELECT * FROM $table WHERE id IN (" . implode(',', $only_ids) . ') ';
            $sql = $sql . " and data_type='category'   and is_deleted=0  ";
            //   $sql = $sql . "$remove_ids_q  $add_ids_q $inf_loop_fix  ";
            $sql = $sql . " group by id order by {$orderby [0]}  {$orderby [1]}  $hard_limit";
        } elseif ($content_type == false) {
            if ($include_first == true) {
                $sql = "SELECT * FROM $table WHERE id=$parent ";
                $sql = $sql . " and data_type='category'   and is_deleted=0  ";
                $sql = $sql . "$remove_ids_q  $add_ids_q $inf_loop_fix  ";
                $sql = $sql . " group by id order by {$orderby [0]}  {$orderby [1]}  $hard_limit";
            } else {
                $sql = "SELECT * FROM $table WHERE parent_id=$parent AND data_type='category' AND is_deleted=0 ";
                $sql = $sql . "$remove_ids_q $add_ids_q $inf_loop_fix group by id order by {$orderby [0]}  {$orderby [1]}   $hard_limit";
            }
        } else {
            if ($include_first == true) {
                $sql = "SELECT * FROM $table WHERE id=$parent  AND is_deleted=0  ";
                $sql = $sql . "$remove_ids_q $add_ids_q   $inf_loop_fix group by id order by {$orderby [0]}  {$orderby [1]}  $hard_limit";
            } else {
                $sql = "SELECT * FROM $table WHERE parent_id=$parent AND is_deleted=0 AND data_type='category' AND (category_subtype='$content_type' OR category_subtype='inherit' ) ";
                $sql = $sql . " $remove_ids_q  $add_ids_q $inf_loop_fix group by id order by {$orderby [0]}  {$orderby [1]}   $hard_limit";

            }
        }

        if (!empty($limit)) {
            $my_offset = $limit[1] - $limit[0];

            $my_limit_q = " limit  {$limit[0]} , $my_offset  ";
        } else {
            $my_limit_q = false;
        }
        $output = '';

        $q = $this->app->database_manager->query($sql, $cache_id = 'html_tree_parent_cats_q_' . crc32($sql), 'categories/' . intval($parent));
        //$q = $this->app->database_manager->query($sql, false);

        $result = $q;

        $only_with_content2 = $only_with_content;

        $do_not_show_next = false;

        $chosen_categories_array = array();

        if (isset($result) and is_array($result) and !empty($result)) {
            ++$depth_level_counter;
            $i = 0;

            $do_not_show = false;

            if ($do_not_show == false) {
                $print1 = false;
                if (trim($list_tag) != '') {
                    if ($ul_class_name == false) {
                        $print1 = "<{$list_tag}  class='{active_class} category_tree depth-{$depth_level_counter}'>";
                    } else {
                        $cl_name = $ul_class_name;
                        if ($depth_level_counter > 1) {
                            $cl_name = $ul_class_deep;
                        }
                        $print1 = "<{$list_tag} class='{active_class} $cl_name depth-{$depth_level_counter}'>";
                    }
                }

                if (intval($parent) != 0 and intval($parent) == intval(CATEGORY_ID)) {
                    $print1 = str_replace('{active_class}', 'active', $print1);
                }
                $print1 = str_replace('{active_class}', '', $print1);

                echo $print1;

                foreach ($result as $item) {
                    if ($only_with_content == true) {
                        $do_not_show = false;

                        $check_in_content = false;
                        $children_content = array();

                        $do_not_show = false;
                        if (!empty($children_content)) {
                            $do_not_show = false;
                        } else {
                            $do_not_show = true;
                        }
                    } else {
                        $do_not_show = false;
                    }
                    $iid = $item['id'];
                    if ($do_not_show == false) {
                        $output = $output . $item['title'];

                        if ($li_class_name == false) {
                            $output = "<{$list_item_tag} class='{active_class} category_element depth-{$depth_level_counter} item_{$iid}'   value='{$item['id']}' data-category-id='{$item['id']}' data-category-parent-id='{$item['parent_id']}' data-item-id='{$item['id']}'  data-to-table='{$item['rel_type']}'  data-to-table-id='{$item['rel_id']}'    data-categories-type='{$item['data_type']}' {active_code_tag} title='{title_slashes}'>";
                        } else {
                            $output = "<{$list_item_tag} class='{active_class} $li_class_name  category_element depth-{$depth_level_counter} item_{$iid}'  value='{$item['id']}' data-item-id='{$item['id']}' data-category-id='{$item['id']}'  data-to-table='{$item['rel_type']}'  data-to-table-id='{$item['rel_id']}'  data-categories-type='{$item['data_type']}'  {active_code_tag} title='{title_slashes}' >";
                        }
                    }

                    if (intval($item['id']) != 0 and intval($item['id']) == intval(CATEGORY_ID)) {
                        $output = str_replace('{active_class}', 'active', $output);
                    } else {
                        $output = str_replace('{active_class}', '', $output);
                    }

                    if ($do_not_show == false) {
                        if ($link != false) {
                            $to_print = false;

                            $empty1 = intval($depth_level_counter);
                            $empty = '';
                            for ($i1 = 0; $i1 < $empty1; ++$i1) {
                                $empty = $empty . '&nbsp;&nbsp;';
                            }

                            $ext_classes = '';

                            $to_print = str_replace('{id}', $item['id'], $link);

                            if (stristr($link, '{items_count}')) {
                                $to_print = str_ireplace('{items_count}', $this->get_items_count($item['id']), $to_print);
                            }

                            $to_print = str_ireplace('{url}', $this->link($item['id']), $to_print);
                            $to_print = str_ireplace('{link}', $this->link($item['id']), $to_print);

                            $to_print = str_replace('{exteded_classes}', $ext_classes, $to_print);

                            $to_print = str_ireplace('{categories_url}', $this->link($item['id']), $to_print);
                            $to_print = str_ireplace('{nest_level}', 'depth-' . $depth_level_counter, $to_print);

                            $to_print = str_ireplace('{title}', $item['title'], $to_print);
                            $to_print = str_ireplace('{title_slashes}', addslashes($item['title']), $to_print);
                            $to_print = str_replace('{content_link_class}', '', $to_print);

                            $output = str_replace('{title_slashes}', addslashes($item['title']), $output);

                            $output = str_replace('{content_link_class}', '', $output);

                            $active_class = ' ';

                            $active_parent_class = '';
                            //if(isset($item['parent']) and intval($item['parent']) != 0){
                            if (intval($item['parent_id']) != 0 and intval($item['parent_id']) == intval(CATEGORY_ID)) {
                                $active_parent_class = 'active-parent';
                                $active_class = '';
                            } elseif (intval($item['id']) != 0 and intval($item['id']) == intval(CATEGORY_ID)) {
                                $active_parent_class = 'active-parent';
                                $active_class = 'active';
                            } else {
                                $active_parent_class = '';
                            }
                            $active_class = str_replace('"', ' ', $active_class);

                            $to_print = str_replace('{active_class}', $active_class, $to_print);
                            $to_print = str_replace('{active_parent_class}', $active_parent_class, $to_print);

                            if (isset($item['category_subtype'])) {
                                $to_print = str_ireplace('{category_subtype}', trim($item['category_subtype']), $to_print);
                            }

                            $to_print = str_replace('{empty}', $empty, $to_print);

                            $active_found = false;

                            if (is_string($active_ids)) {
                                $active_ids = explode(',', $active_ids);
                            }

                            if (is_array($active_ids) == true) {
                                $active_ids = array_trim($active_ids);

                                foreach ($active_ids as $value_active_cat) {
                                    if ($value_active_cat != '') {
                                        $value_active_cat = intval($value_active_cat);
                                        if (intval($item['id']) == $value_active_cat) {
                                            $active_found = $value_active_cat;
                                        }
                                    }
                                }

                                if ($active_found == true) {
                                    $to_print = str_replace('{active_code}', $active_code, $to_print);
                                    $to_print = str_replace('{active_class}', $active_class, $to_print);
                                    $to_print = str_replace('{active_code_tag}', $active_code_tag, $to_print);
                                    $output = str_replace('{active_code_tag}', $active_code_tag, $output);
                                } else {
                                    $to_print = str_replace('{active_code}', '', $to_print);
                                }
                            } else {
                                $to_print = str_ireplace('{active_code}', '', $to_print);
                            }
                            $output = str_replace('{active_code_tag}', '', $output);
                            $output = str_replace('{title_slashes}', '', $output);

                            $output = str_replace('{exteded_classes}', $ext_classes, $output);

                            $to_print = str_replace('{items_count}', '', $to_print);
                            $to_print = str_replace('{active_class}', '', $to_print);
                            $to_print = str_replace('{active_code_tag}', '', $to_print);

                            if (is_array($remove_ids) == true) {
                                if (in_array($item['id'], $remove_ids)) {
                                    if ($removed_ids_code == false) {
                                        $to_print = false;
                                    } else {
                                        $to_print = str_ireplace('{removed_ids_code}', $removed_ids_code, $to_print);
                                    }
                                } else {
                                    $to_print = str_ireplace('{removed_ids_code}', '', $to_print);
                                }
                            }

                            if (strval($to_print) == '') {
                                echo $output . $item['title'];
                            } else {
                                echo $output . $to_print;
                            }
                        } else {
                            echo $output . $item['title'];
                        }

                        $children_of_the_main_parent1 = array();

                        if (!isset($remove_ids) or !is_array($remove_ids)) {
                            $remove_ids = array();
                        }
                        $remove_ids[] = $item['id'];

                        if ($only_ids == false) {
                            $children = $this->html_tree($item['id'], $link, $active_ids, $active_code, $remove_ids, $removed_ids_code, $ul_class_name, false, $content_type, $li_class_name, $add_ids = false, $orderby, $only_with_content, $visible_on_frontend, $depth_level_counter, $max_level, $list_tag, $list_item_tag, $active_code_tag, $ul_class_deep);
                        }
                        echo "</{$list_item_tag}>";
                    }
                }
                if (trim($list_tag) != '') {
                    echo "</{$list_tag}>";
                }
            }
        }
    }

    public function link($id)
    {
        if (intval($id) == 0) {
            return false;
        }

        $function_cache_id = __FUNCTION__;

        $id = intval($id);
        $cache_group = 'categories';

        $cache_content = $this->app->cache_manager->get($function_cache_id, $cache_group);

        if (($cache_content) != false and isset($cache_content[$id])) {
            return $cache_content[$id];
        } else {
            if ($cache_content == false) {
                $cache_content = array();
            }

            $table = $this->tables['categories'];
            $c_infp = $this->get_by_id($id);

            if (!isset($c_infp['rel_type'])) {
                return;
            }

            if (trim($c_infp['rel_type']) != 'content') {
                return;
            }

            $content = $this->get_page($id);

            if (!empty($content)) {
                $url = $this->app->content_manager->link($content['id']);
            }

            if (isset($url) == false and defined('PAGE_ID')) {
                $url = $this->app->content_manager->link(PAGE_ID);
            }

            if (isset($url) != false) {
                if (isset($c_infp['url']) and trim($c_infp['url']) != '') {
                    $url = $url . '/category:' . trim($c_infp['url']);
                } else {
                    $url = $url . '/category:' . $id;
                }
                $cache_content[$id] = $url;
                $this->app->cache_manager->save($cache_content, $function_cache_id, $cache_group);

                return $url;
            }

            return;
        }
    }

    public function get_page($category_id)
    {
        $category_id = intval($category_id);
        if ($category_id == 0) {
            return false;
        } else {
        }
        $category = $this->get_by_id($category_id);
        if ($category != false) {
            if (isset($category['rel_id']) and intval($category['rel_id']) > 0) {
                if ($category['rel_type'] == 'content') {
                    $res = $this->app->content_manager->get_by_id($category['rel_id']);
                    if (is_array($res)) {
                        return $res;
                    }
                }
            }

            if ((!isset($category['rel_id']) or (isset($category['rel_id']) and intval($category['rel_id']) == 0)) and intval($category['parent_id']) > 0) {
                $category1 = $this->get_parents($category['id']);
                if (is_array($category1)) {
                    foreach ($category1 as $value) {
                        if (intval($value) != 0) {
                            $category2 = $this->get_by_id($value);
                            if (isset($category2['rel_id']) and intval($category2['rel_id']) > 0) {
                                if ($category2['rel_type'] == 'content') {
                                    $res = $this->app->content_manager->get_by_id($category2['rel_id']);
                                    if (is_array($res)) {
                                        return $res;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function get_parents($id = 0, $without_main_parrent = false, $data_type = 'category')
    {
        if (intval($id) == 0) {
            return false;
        }

        $table = $this->tables['categories'];

        $ids = array();

        $data = array();

        if (isset($without_main_parrent) and $without_main_parrent == true) {
            $with_main_parrent_q = ' and parent_id<>0 ';
        } else {
            $with_main_parrent_q = false;
        }
        $id = intval($id);
        $q = " select id, parent_id  from $table where id = $id and  data_type='{$data_type}' " . $with_main_parrent_q;

        $params = array();
        $params['table'] = $table;
        $params['id'] = $id;
        $params['data_type'] = $data_type;
        if (isset($without_main_parrent) and $without_main_parrent == true) {
            $params['parent_id'] = '[neq]0';
        }

        $taxonomies = $this->app->database_manager->get($params);

        //  $taxonomies = $this->app->database_manager->query($q, $cache_id = __FUNCTION__ . crc32($q), $cache_group = 'categories/' . $id);

        if (!empty($taxonomies)) {
            foreach ($taxonomies as $item) {
                if (intval($item['id']) != 0) {
                    $ids[] = $item['parent_id'];
                }
                if ($item['parent_id'] != $item['id']) {
                    $next = $this->get_parents($item['parent_id'], $without_main_parrent);

                    if (!empty($next)) {
                        foreach ($next as $n) {
                            if ($n != '') {
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

    public function get_children($parent_id = 0, $type = false, $visible_on_frontend = false)
    {
        $categories_id = $parent_id = intval($parent_id);
        $cache_group = 'categories/' . $categories_id;

        $table = $this->tables['categories'];

        $db_t_content = $this->tables['content'];

        if (isset($orderby) == false) {
            $orderby = array();
            //$orderby[0] = 'updated_at';

            //$orderby[1] = 'DESC';

            $orderby[0] = 'position';

            $orderby[1] = 'asc';
        }
        if ($parent_id == 0) {
            return false;
        }

        $data = array();

        $data['parent_id'] = $parent_id;

        if ($type != false) {
            $data['data_type'] = $type;
        } else {
            $type = 'category_item';
            $data['data_type'] = $type;
        }

        $cache_group = 'categories/' . $parent_id;
        $q = " SELECT id,  parent_id FROM $table WHERE parent_id=$parent_id   ";

        $params = array();
        $params['table'] = $table;
        $params['no_limit'] = true;
        $params['parent_id'] = $parent_id;

        $save = $this->app->database_manager->get($params);

        $q_cache_id = __FUNCTION__ . crc32($q);
        // $save = $this->app->database_manager->query($q, $q_cache_id, $cache_group);
        if (empty($save)) {
            return false;
        }
        $to_return = array();
        if (is_array($save) and !empty($save)) {
            foreach ($save as $item) {
                $to_return[] = $item['id'];
            }
        }

        $to_return = array_unique($to_return);

        return $to_return;
    }

    public function get_for_content($content_id, $data_type = 'categories')
    {
        if (intval($content_id) == 0) {
            return false;
        }

        if ($data_type == 'categories') {
            $data_type = 'category';
        }
        if ($data_type == 'tags') {
            $data_type = 'tag';
        }
        $get_category_items = $this->get_items('group_by=parent_id&rel_type=content&rel_id=' . ($content_id));
        $include_parents = array();
        $include_parents_str = '';

        if (!empty($get_category_items)) {
            foreach ($get_category_items as $get_category_item) {
                if (isset($get_category_item['parent_id'])) {
                    $include_parents[] = $get_category_item['parent_id'];
                }
            }
        }
        $get_category = $this->get('order_by=position desc&data_type=' . $data_type . '&rel_type=content&rel_id=' . ($content_id));
        if (empty($get_category)) {
            $get_category = array();
        }

        if (!empty($include_parents)) {
            $include_parents_str = 'order_by=position desc&data_type=' . $data_type . '&rel_type=content&ids=' . implode(',', $include_parents);
            $get_category2 = $this->get($include_parents_str);

            if (!empty($get_category2)) {
                foreach ($get_category2 as $item) {
                    $get_category[] = $item;
                }
            }
        }

//        if (is_array($get_category) and !empty($get_category)) {
//            array_unique($get_category);
//        }

        if (empty($get_category)) {
            return false;
        }

        return $get_category;
    }

    /**
     * Gets category items count.
     *
     * @param mixed $params Array or string with parameters
     * @param string $data_type
     *
     * @return array|bool
     */
    public function get_items_count($id, $rel_type = false)
    {
        if ($id == false) {
            return false;
        }

        $table_items = $this->tables['categories_items'];

        $params = array();
        $params['table'] = $table_items;
        $params['parent_id'] = $id;
        if ($rel_type != false) {
            $params['rel_type'] = $rel_type;
        }

        $params['count'] = true;
        $data = $this->app->database_manager->get($params);

        return $data;
    }

    /**
     * Gets category items.
     *
     * @param mixed $params Array or string with parameters
     * @param string $data_type
     *
     * @return array|bool
     */
    public function get_items($params, $data_type = 'categories')
    {
        if (is_string($params)) {
            $params = parse_str($params, $params2);
            $params = $options = $params2;
        }
        $table_items = $this->tables['categories_items'];
        $data = $params;

        $data['table'] = $table_items;
        if (!isset($params['limit'])) {
            //$data['no_limit'] =true;
        }

        $data = $this->app->database_manager->get($data);

        return $data;
    }

    public function get($params)
    {
        $params2 = array();
        $rel_id = 0;
        if (is_string($params)) {
            $params = parse_str($params, $params2);
            $params = $params2;
            extract($params);
        }

        $table = $this->tables['categories'];
        $table_items = $this->tables['categories_items'];

        $data = $params;

        $data['table'] = $table;
        if (isset($params['id'])) {
            $data['cache_group'] = $cache_group = 'categories/' . $params['id'];
        } else {
            $data['cache_group'] = $cache_group = 'categories/global';
        }

        if (isset($data['parent']) and !isset($data['parent_id'])) {
            $data['parent_id'] = $data['parent'];
        }

        if (!isset($data['rel_type'])) {
            $data['rel_type'] = 'content';
        }

        if (isset($params['rel_id'])) {
            $data['rel_id'] = $params['rel_id'];

        }

        if (isset($data['parent_page'])) {
            $data['rel_type'] = 'content';
            $data['rel_id'] = $data['parent_page'];
        }

        if (isset($data['parent_id'])) {
            if (isset($data['rel_type'])) {
                unset($data['rel_type']);
            }
            if (isset($data['rel_id'])) {
                unset($data['rel_id']);
            }
        }
//dd($data);
        $data = $this->app->database_manager->get($data);


        return $data;
    }

    public function save($data, $preserve_cache = false)
    {
        $sid = $this->app->user_manager->session_id();

        if (is_string($data)) {
            $data = parse_params($data);
        }
        $orig_data = $data;

        if ((isset($data['id']) and ($data['id']) == 0) or !isset($data['id'])) {
            if (!isset($data['title']) or (isset($data['title']) and $data['title'] == false)) {
                return array('error' => 'Title is required');
            }
        } elseif ((isset($data['id']) and ($data['id']) != 0)) {
            if ((isset($data['title']) and $data['title'] == false)) {
                return array('error' => 'Title cannot be blank');
            }
        }

        if ((isset($data['id']) and ($data['id']) != 0) and isset($data['parent_id'])) {
            if ((intval($data['id']) == intval($data['parent_id']))) {
                return array('error' => 'Invalid parent category');
            }
        }

        $table = $this->tables['categories'];
        $table_items = $this->tables['categories_items'];

        $content_ids = false;
        $simple_save = false;
        if (isset($data['content_id']) and !isset($data['rel_type'])) {
            $data['rel_type'] = 'content';
            $data['rel_id'] = $data['content_id'];
        }

        if (isset($data['rel']) and !isset($data['rel_type'])) {
            $data['rel_type'] = $data['rel'];
        }
        if (!isset($data['data_type']) or !($data['data_type'])) {
            $data['data_type'] = 'category';
        }
        if (isset($data['users_can_create_content']) and ($data['users_can_create_content']) == 'y') {
            $data['users_can_create_content'] = 1;
        } elseif (isset($data['users_can_create_content']) and ($data['users_can_create_content']) == 'n') {
            $data['users_can_create_content'] = 0;
        }

        if (isset($data['rel_type']) and ($data['rel_type'] == '') or !isset($data['rel_type'])) {
            $data['rel_type'] = 'content';
        }
        if (isset($data['simple_save'])) {
            $simple_save = $data['simple_save'];
        }
        if (isset($data['content_id'])) {
            if (is_array($data['content_id']) and !empty($data['content_id']) and trim($data['data_type']) != '') {
                $content_ids = $data['content_id'];
            }
        }

        if (isset($data['position'])) {
            $data['position'] = intval($data['position']);
        }

        if (isset($data['category_subtype_settings'])) {
            $data['category_subtype_settings'] = @json_encode($data['category_subtype_settings']);
        }

        $no_position_fix = false;
        if (isset($data['rel_type']) and isset($data['rel_id']) and trim($data['rel_type']) != '' and trim($data['rel_id']) != '') {
            $no_position_fix = true;
        }

        if (isset($data['parent_page'])) {
            $data['rel_type'] = 'content';
            $data['rel_id'] = $data['parent_page'];
        }

        if (isset($data['table']) and ($data['table'] != '')) {
            $table = $data['table'];
        }
        if (isset($data['id']) and intval($data['id']) != 0 and isset($data['parent_id']) and intval($data['parent_id']) != 0) {
            if ($data['id'] == $data['parent_id']) {
                unset($data['parent_id']);
            }
        } elseif ((!isset($data['id']) or intval($data['id']) == 0) and !isset($data['parent_id'])) {
            $data['parent_id'] = 0;
        }

        if (isset($data['rel_type']) and isset($data['rel_id']) and trim($data['rel_type']) == 'content' and intval($data['rel_id']) != 0) {
            $cont_check = $this->app->content_manager->get_by_id($data['rel_id']);

            if ($cont_check != false and isset($cont_check['subtype']) and $cont_check['subtype'] == 'static') {
                $cs = array();
                $cs['id'] = intval($data['rel_id']);
                $cs['subtype'] = 'dynamic';
                $table_c = $this->tables['content'];

                $save = $this->app->database_manager->save($table_c, $cs);
            }
        }
        if ((!isset($data['id']) or $data['id'] == 0) and !isset($data['is_deleted'])) {
            $data['is_deleted'] = 0;
        }

        if ((!isset($data['id']) or $data['id'] == 0)
            and (!isset($data['url']) or trim($data['url']) == false)
            and isset($data['title'])
        ) {
            $data['url'] = $data['title'];
        }

        $old_parent = false;
        if (isset($data['id'])) {
            $old_category = $this->get_by_id($data['id']);
            if (isset($old_category['parent_id'])) {
                $old_parent = $old_category['parent_id'];
            }
        }

        if (isset($data['url']) and trim($data['url']) != false) {
            $possible_slug = $this->app->url_manager->slug($data['url']);
            if ($possible_slug) {
                $possible_slug_check = $this->get_by_slug($possible_slug);
                if (isset($possible_slug_check['id'])) {
                    if (isset($data['id']) and $data['id'] == $possible_slug_check['id']) {
                        //slug is the same
                    } else {
                        $possible_slug = $possible_slug . '-' . date('YmdHis');
                    }
                }
            }
            if ($possible_slug) {
                $data['url'] = $possible_slug;
            } else {
                $data['url'] = false;
            }
        } elseif (isset($data['url']) and trim($data['url']) != false) {
            $data['url'] = false;
        }

        /* if (!empty($orig_data)) {
             $data_str = 'data_';
             $data_str_l = strlen($data_str);
             foreach ($orig_data as $k => $v) {
                 if (is_string($k)) {
                     if (strlen($k) > $data_str_l) {
                         $rest = substr($k, 0, $data_str_l);
                         $left = substr($k, $data_str_l, strlen($k));
                         if ($rest == $data_str) {
                             if (!isset($data['data_fields'])) {
                                 $data['data_fields'] = array();
                             }
                             $data['data_fields'][ $left ] = $v;
                         }
                     }
                 }
             }
         }*/
        $data['allow_html'] = true;

        // \Log::info(print_r($data, true));
        $id = $save = $this->app->database_manager->extended_save($table, $data);


        if ($simple_save == true) {
            return $save;
        }

        if (intval($save) == 0) {
            return false;
        }

        DB::transaction(function () use ($sid, $id) {
            DB::table($this->tables['custom_fields'])
                ->whereSessionId($sid)
                ->where(function ($query) {
                    $query->whereRelId(0)->orWhere('rel_id', null);
                })
                ->whereRelType('categories')
                ->update(['rel_type' => 'categories', 'rel_id' => $id]);

            DB::table($this->tables['media'])
                ->whereSessionId($sid)
                ->where(function ($query) {
                    $query->whereRelId(0)->orWhere('rel_id', null);
                })
                ->whereRelType('categories')
                ->update(['rel_id' => $id]);
        });

        //$this->app->cache_manager->clear('media');

        // $this->app->database_manager->q($clean);

        if (isset($content_ids) and !empty($content_ids)) {
            $content_ids = array_unique($content_ids);
            $data_type = trim($data['data_type']) . '_item';

            $content_ids_all = implode(',', $content_ids);

            $q = "DELETE FROM $table WHERE rel_type='content'
		AND content_type='post'
		AND parent_id=$save
		AND  data_type ='{$data_type}' ";

            $this->app->database_manager->q($q);

            foreach ($content_ids as $id) {
                $item_save = array();

                $item_save['rel_type'] = 'content';

                $item_save['rel_id'] = $id;

                $item_save['data_type'] = $data_type;

                $item_save['content_type'] = 'post';

                $item_save['parent_id'] = intval($save);

                $item_save = $this->app->database_manager->save($table_items, $item_save);

            }
        }
        if ($old_parent != false) {
            // $this->app->cache_manager->clear('categories' . DIRECTORY_SEPARATOR . $old_parent);
        }

        // $this->app->cache_manager->clear('categories');

        return $save;
    }

    public function save_item($params)
    {
        $params = parse_params($params);
        $table = $this->tables['categories_items'];
        $params['table'] = $table;
        $save = $this->app->database_manager->save($params);
        if (intval($save) == 0) {
            return false;
        }
    }

    /**
     * @desc        Get a single row from the categories_table by given ID and returns it as one dimensional array
     *
     * @param int
     *
     * @return array
     *
     * @author      Peter Ivanov
     *
     * @version     1.0
     *
     * @since       Version 1.0
     */
    public function get_by_id($id = 0, $by_field_name = 'id')
    {
        if (!$id) {
            return;
        }
        if ($by_field_name == 'id' and intval($id) == 0) {
            return false;
        }
        if (is_numeric($id)) {
            $id = intval($id);
            $cache_group_suffix = ceil($id / 50) * 50;
        } else {
            $id = trim($id);
            $cache_group_suffix = substr($id, 0, 1);
        }

        $function_cache_id = __FUNCTION__ . '-' . $by_field_name . '-' . $cache_group_suffix;

        $cache_group = 'categories';

        $cache_content = $this->app->cache_manager->get($function_cache_id, $cache_group);

        if (($cache_content) != false and isset($cache_content[$id])) {
            return $cache_content[$id];
        } else {
            if ($cache_content == false) {
                $cache_content = array();
            }

            $table = $this->tables['categories'];

            $get = array();

            $get[$by_field_name] = $id;
            $get['no_cache'] = true;
            $get['single'] = true;
            $q = $this->app->database_manager->get($table, $get);

            if (isset($q['category_subtype_settings'])) {
                $q['category_subtype_settings'] = @json_decode($q['category_subtype_settings'], true);
            }


            $cache_content[$id] = $q;
            $this->app->cache_manager->save($cache_content, $function_cache_id, $cache_group);

            return $q;
        }
    }

    public function get_by_slug($slug)
    {
        return $this->get_by_id($slug, 'url');
    }

    public function delete($data)
    {
        if (is_array($data) and isset($data['id'])) {
            $c_id = intval($data['id']);
        } else {
            $c_id = intval($data);
        }

        $del = $this->app->database_manager->delete_by_id('categories', $c_id);
        $this->app->database_manager->delete_by_id('categories', $c_id, 'parent_id');
        $this->app->database_manager->delete_by_id('categories_items', $c_id, 'parent_id');
        if (defined('MODULE_DB_MENUS')) {
            $this->app->database_manager->delete_by_id('menus', $c_id, 'categories_id');
        }

        return $del;
    }

    public function delete_item($data)
    {
        if (is_array($data) and isset($data['id'])) {
            $c_id = intval($data['id']);
        } else {
            $c_id = intval($data);
        }

        return $this->app->database_manager->delete_by_id('categories_items', $c_id);
    }

    public function reorder($data)
    {
        $table = $this->tables['categories'];
        $res = array();
        foreach ($data as $value) {
            if (is_array($value)) {
                $indx = array();
                $i = 0;
                foreach ($value as $value2) {
                    $indx[$i] = $value2;
                    ++$i;
                }

                $res[] = $this->app->database_manager->update_position_field($table, $indx);
            }
        }

        return $res;
    }

    public function get_category_id_from_url($url = false)
    {
        if ($url) {
            $cat_url = $this->app->url_manager->param('category', true, $url);
        } else {
            $cat_url = $this->app->url_manager->param('category', true);
        }
        if ($cat_url != false and !is_numeric($cat_url)) {
            $cat_url_by_slug = $this->get_by_slug($cat_url);
            if (isset($cat_url_by_slug['id'])) {
                $cat_url = $cat_url_by_slug['id'];
            }
        }

        return intval($cat_url);
    }
}
