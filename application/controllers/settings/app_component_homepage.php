<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_component_homepage extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	
	function index($s,$r,$t){
		echo $s.$r.$t;
	}
	
	function test(){
	echo 's';
	}
}
/* End of file app_component_homepage.php */
/* Location: ./application/controllers/settings/app_component_homepage.php */