<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['mongo_host'] = 'localhost';
$config['mongo_port'] = 27027;
$config['mongo_db'] = 'socialhappen';
$config['mongo_user'] = 'sohappen';
$config['mongo_pass'] = 'figyfigy';
$config['mongo_persist'] = TRUE;
$config['mongo_persist_key'] = 'ci_mongo_persist';
// $config['mongo_testmode'] = FALSE; //If set to true, models will load databases with a prefix
$config['mongo_testmode_prefix'] = 'sh_test_';