<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit extends CI_Controller {
	
	var $audit_lib = '';
	/**
	 * construct method
	 */
	function __construct(){
		parent::__construct();
		$this->load->library('audit_lib');
	}
	
	/**
	 * index method
	 */
	function index(){
		echo 'index created';
		echo $this->audit_lib->create_index();
	}
	
	function add_audit_action(){
		$app_id = 0;
		$action_id = 1;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'platform visit';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		
		$app_id = 0;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'platform register';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		
		$app_id = 1;
		$action_id = 1;
		$stat_app = TRUE;
		$stat_page = TRUE;
		$stat_campaign = TRUE;
		$description = 'app1 visit';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		
		$app_id = 1;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = TRUE;
		$stat_campaign = TRUE;
		$description = 'app1 register';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
	}

	function _add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign){
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		if($result){
			echo 'audit action added';
		}else{
			echo 'audit action add fail';
		}
		echo '$app_id: ' . $app_id . '<br/>';
		echo '$action_id: ' . $action_id . '<br/>';
		echo '$stat_app: ' . $stat_app . '<br/>';
		echo '$stat_page: ' . $stat_page . '<br/>';
		echo '$stat_campaign: ' . $stat_campaign . '<br/>';
		echo '$description: ' . $description . '<br/>';
	}
	
	
	/**
	 * platform visit
	 */
	function addlog01(){
		$app_id = 0;
		$action_id = 1;
		$subject = 'userX';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => 0);
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'platform visit';
	}

	/**
	 * platform register
	 */
	function addlog02(){
		$app_id = 0;
		$action_id = 2;
		$subject = 'userX';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => 0);
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'platform register';
	}
	
	/**
	 * app1 visit
	 */
	function addlog11(){
		$app_id = 1;
		$action_id = 1;
		$subject = 'userX';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => rand(1, 20),
								'campaign_id' => rand(1, 30),
								'company_id' => rand(1, 20),
								'page_id' => rand(1, 20));
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'app1 visit';
	}
	
	/**
	 * app1 register
	 */
	function addlog12(){
		$app_id = 1;
		$action_id = 2;
		$subject = 'userX';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => rand(1, 20),
								'campaign_id' => rand(1, 30),
								'company_id' => rand(1, 20),
								'page_id' => rand(1, 20));
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'app1 register';
	}
	
	
	function addlog(){
		echo 'addlog';
		$rand = rand(1, 4) . '';
		//echo $rand;
		switch ($rand) {
		case '1':
			$this->addlog01();
		break;
		
		case '2':
			$this->addlog02();
		break;
			
		case '3':
			$this->addlog11();
		break;
			
		case '4':
			$this->addlog12();
		break;
			
		default:
		
		break;
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
		//echo 'added';
	}
	
	/**
	 * list recent audit
	 */
	function list_audit(){
		$audit_list = $this->audit_lib->list_recent_audit();
		foreach ($audit_list as $audit) {
			//echo $audit['subject'] . "<br/>";
			echo '<pre>' . print_r($audit) . '</pre>';
		}
	}

}

/* End of file audit.php */
/* Location: ./application/controllers/audit.php */