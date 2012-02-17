<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

	function sh_mongodb_load($config = NULL) {
		if(!isset($config['collection'])){
			show_error('Collection not specified');
			exit();
		}
		$collection = $config['collection'];

		$CI =& get_instance();

		$CI->config->load('mongo_db');
		$mongo_user = $CI->config->item('mongo_user');
		$mongo_pass = $CI->config->item('mongo_pass');
		$mongo_host = $CI->config->item('mongo_host');
		$mongo_port = $CI->config->item('mongo_port');
		$database = $CI->config->item('mongo_db');
		$mongo_testmode = $CI->config->item('mongo_testmode');
		$mongo_testmode_prefix = $CI->config->item('mongo_testmode_prefix');
		
		try{
			// connect to database
			$CI->connection = new Mongo("mongodb://".$mongo_user.":"
			.$mongo_pass
			."@".$mongo_host.":".$mongo_port);
			
			// select database
			$testmode_prefix = $mongo_testmode ? $mongo_testmode_prefix : NULL;
			$database = $testmode_prefix.$database;
			$CI->mongo_db = $CI->connection->{$database};

			// return collection
			return $CI->mongo_db->{$collection};
			
		} catch (Exception $e){
			show_error('Cannot connect to database');
			return FALSE;
		}
	}

