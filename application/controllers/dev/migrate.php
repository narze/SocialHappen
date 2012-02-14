<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Migrate
 * @category Controller
 */
class Migrate extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('migrate_lib');
	}
	
	function index(){
		$this->migrate_lib->index();
	}

	function latest($backup = 1){
		$this->migrate_lib->latest($backup);
	}

	function version($target_version,$backup = 1){
		$this->migrate_lib->version($target_version,$backup);
	}

	function current($backup = 1){
		$this->migrate_lib->current($backup);
	}
}