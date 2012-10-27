<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['mongo_host'] = 'ec2-54-251-3-51.ap-southeast-1.compute.amazonaws.com,ec2-175-41-191-201.ap-southeast-1.compute.amazonaws.com';
$config['mongo_port'] = 27017;
$config['mongo_db'] = 'socialhappen';
$config['mongo_user'] = 'sohap';
$config['mongo_pass'] = 'figyfigy';
$config['mongo_options'] = array("replicaSet" => "shProdRep");
$config['mongo_persist'] = TRUE;
$config['mongo_persist_key'] = 'ci_mongo_persist';
// $config['mongo_testmode'] = FALSE; //If set to true, models will load databases with a prefix
$config['mongo_testmode_prefix'] = 'sh_test_';