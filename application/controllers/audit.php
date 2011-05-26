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