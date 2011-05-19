<?php
if(!defined('BASEPATH'))
	exit('No direct script access allowed');

class All extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this -> load -> library('unit_test');
		$this -> load -> model('user_model', 'users');
	}

	function __destruct() {
		echo $this -> unit -> report();
	}

	function index() {
		foreach(glob(__DIR__."/*_test.php") as $filename) {
			echo '<h1>Class : '.basename($filename,"_test.php").'</h1>';
			echo file_get_contents(base_url() . 'test/' . basename($filename, ".php"));
		}
	}

}

/* End of file all.php */
/* Location: ./application/controllers/test/all.php */
