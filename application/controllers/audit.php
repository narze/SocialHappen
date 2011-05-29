<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit extends CI_Controller {
	
	/**
	 * construct method
	 */
	function __construct(){
		parent::__construct();
		$this->load->model('Audit_model', 'Audit');
	}
	
	/**
	 * index method
	 */
	function index(){
		echo 'hello';
		/*
		for($i = 0; $i < 100; $i++){
			$audit = array('subject' => '' . ($i % 4),
						'action' => '' . ($i % 5),
						'object' => '' . ($i*$i % 5),
						'app_id' => '' . ($i*$i % 5));
			$this->Audit->add_audit($audit);
		}
		*/
		
		
		$res = $this->Audit->list_audit(array('app_id' => 0),30,1);
		
		echo '<pre>';
		print_r(count($res));
		echo '</pre>';
		
		foreach ($res as $audit) {
			echo '<pre>';
			print_r($audit);
			echo '</pre>';
		}
		
	}
	
	/**
	 * add new audit entry
	 */
	function add(){
		$audit = array('subject' => 'subject',
						'action' => '1',
						'object' => '2',
						'objecti' => '3',
						'type' => '4');
		$this->Audit->add_audit($audit);
		echo 'added';
	}
	
	/**
	 * list recent audit
	 */
	function list_audit(){
		$audit_list = $this->Audit->list_recent_audit();
		foreach ($audit_list as $audit) {
			echo $audit['subject'] . "<br/>";
			//print_r($audit);
		}
	}

}

/* End of file audit.php */
/* Location: ./application/controllers/audit.php */