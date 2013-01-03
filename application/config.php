<?php

defined('T') or die();

// Global site configuration
$config = array(
    // In development, debug mode unlocks extra error info
    'debug_mode' => false,
    'admin_url' => 'admin',
    'uri_protocol' => 'AUTO',
    'default_timezone' => 'UTC',
	'table_prefix' => '1es_1_', 
    'installed' => 'yes',
    // Database Settings
    'db' => array(
        'host' => 'localhost',
        'dbname' => 'new_db',
        'user' => 'root',
        'pass' => '123456'
    )
);
 
return $config;