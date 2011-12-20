<?php
if(!defined('BASEPATH'))
	exit('No direct script access allowed');

class All extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$all_pass = TRUE;
		foreach(glob(__DIR__."/*_test.php") as $filename) {
			$url = base_url().'test/'.basename($filename,'.php');
			$class_name = basename($filename,"_test.php");
			$result = '<h1>Class : <a href="'.$url.'">'.$class_name.'</a></h1>';
			$result .= file_get_contents($url);
			echo $result;
			if($all_pass == TRUE && strpos($result, 'TEST FAILED') !== FALSE){
				$all_pass = FALSE;
				echo '<script language="javascript">document.bgColor = "#FF6666";</script>';
			}
		}
		if($all_pass === TRUE){
			echo '<script language="javascript">document.bgColor = "#66FF66";</script>';
		}
	}

}

/* End of file all.php */
/* Location: ./application/controllers/test/all.php */
