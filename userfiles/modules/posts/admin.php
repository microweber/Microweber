<?php $dir_name = normalize_path(modules_path()); ?>

<?php if (isset($params['live_edit_sidebar'])): ?>
    <?php include_once($dir_name . 'content' . DS . 'admin_live_edit_sidebar.php'); ?>
<?php elseif (isset($params['backend'])): ?>
    <?php include_once($config['path_to_module'] . 'admin_backend.php'); ?>
<?php else: ?>
    <?php include_once($config['path_to_module'] . 'admin_live_edit.php'); ?>
<?php endif; ?>

