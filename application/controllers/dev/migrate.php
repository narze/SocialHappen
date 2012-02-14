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

	function latest(){
		$this->migrate_lib->latest();
	}

	function version($target_version){
		$this->migrate_lib->version($target_version);
	}

	function current(){
		$this->migrate_lib->current();
	}
}