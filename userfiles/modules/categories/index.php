<?php
if (isset($params['class'])) {
    unset($params['class']);
}

if (!isset($params['ul_class'])) {
    $params['ul_class'] = 'nav nav-list';
}
$params['rel'] = 'content';
$category_tree_parent_page = get_option('data-content-id', $params['id']);

if ($category_tree_parent_page == false and isset($params['content_id'])) {
    $params['rel_id'] = $params['content_id'] = trim($params['content_id']);
} elseif ($category_tree_parent_page == false and isset($params['page-id'])) {
    $params['rel_id'] = $params['content_id'] = trim($params['page-id']);

}

if ($category_tree_parent_page != false and $category_tree_parent_page != '' and $category_tree_parent_page != 0) {
    $params['rel_id'] = $params['content_id'] = $category_tree_parent_page;
}

if ($category_tree_parent_page == false and isset($params['current-page']) and $params['current-page'] == true) {
    $params['rel_id'] = $params['content_id'] = PAGE_ID;

}






$module_template = get_option('data-template', $params['id']);
if ($module_template == false and isset($params['template'])) {
    $module_template = $params['template'];
}



if ($module_template != false) {
    $template_file = module_templates($config['module'], $module_template);
} else {
    $template_file = module_templates($config['module'], 'default');
}


if (isset($template_file) and is_file($template_file) != false) {
    include($template_file);
} else {
    $template_file = module_templates($config['module'], 'default');
    include($template_file);
}





?>
