<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['mongo_host'] = '54.251.38.196,54.251.42.250';
$config['mongo_port'] = 27017;
$config['mongo_db'] = 'socialhappen';
$config['mongo_user'] = 'sohap';
$config['mongo_pass'] = 'figyfigy';
$config['mongo_options'] = array("replicaSet" => "shProdRep");
$config['mongo_persist'] = TRUE;
$config['mongo_persist_key'] = 'ci_mongo_persist';
// $config['mongo_testmode'] = FALSE; //If set to true, models will load databases with a prefix
$config['mongo_testmode_prefix'] = 'sh_test_';