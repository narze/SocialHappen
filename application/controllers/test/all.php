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
			$url = base_url().'test/'.basename($filename,'.php');
			$class_name = basename($filename,"_test.php");
			echo '<h1>Class : <a href="'.$url.'">'.$class_name.'</a></h1>';
			echo file_get_contents($url);
		}
	}

}

/* End of file all.php */
/* Location: ./application/controllers/test/all.php */
