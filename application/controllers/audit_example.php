<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit_example extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->library('audit_lib');
	}
	
	function index(){
	echo $this->audit_lib->create_index();
		echo $this->audit_lib->add_audit_action(0, 1, 'page view', TRUE, 
							FALSE,FALSE);
	}
	
	function add(){
		echo $this->audit_lib->add_audit(1,'subject',1001,'object','objecti',array('user_id' => 1, 'campaign_id'=>1));
	}
	
	function list_audit(){
		echo '<pre>';
		 var_dump($this->audit_lib->list_audit(array('timestamp'=>1307781821)));
		 echo '</pre>';
	}
	
	function list_stat_app(){
		echo '<pre>';
		 var_dump($this->audit_lib->list_stat_app(1,1,20110101,20120101));
		 echo '</pre>';
	}
	
	function delete_stat_app(){
		$this->audit_lib->delete_stat_app("4df32abd624c3ae0db272261");
	}
	
}