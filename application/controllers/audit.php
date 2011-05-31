<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit extends CI_Controller {
	
	/**
	 * construct method
	 */
	function __construct(){
		parent::__construct();
		$this->load->model('Audit_model', 'Audit');
		$this->load->model('Audit_action_model', 'Audit_action');
		$this->load->model('Stat_page_model', 'Stat_page');
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
		
		/*
		$res = $this->Audit->list_audit();
		
		echo '<pre>';
		print_r(count($res));
		echo '</pre>';
		
		foreach ($res as $audit) {
			echo '<pre>';
			print_r($audit);
			echo '</pre>';
		}
		*/
		/*
		$this->Audit_action->create_index();
		for($i = 0; $i < 100; $i++){
			$audit = array('app_id' => ($i % 4),
						'action_id' => ($i % 5),
						'description' => '' . ($i*$i % 5),
						'stat' => ($i*$i % 5) == 0);
			$res = $this->Audit_action->add_action($audit);
			if($res){
				echo 'success<br/>';
			}else{
				echo 'fail<br/>';
			}
		}
		
		*/
		//$this->Audit_action->delete_action(2);
		/*
		$res = $this->Audit_action->get_action_list();
		echo '<pre>';
		print_r(count($res));
		echo '</pre>';
		
		foreach ($res as $audit) {
			echo '<pre>';
			print_r($audit);
			echo '</pre>';
		}
		*/
		/*
		$res = $this->Audit_action->edit_action(2, 3, array('description' => 'test', 'stat' => true));
		if($res){
			echo 'success';
		}else{
			echo 'fail';
		}
		*/
		//$this->Stat_page->create_index();
		/*
		for($i = 0 ;$i < 20; $i++){
			$this->Stat_page->add_stat_page(10 + ($i%2), 20 + ($i % 3), 20110530 + ($i % 4));
		}
		*/
		/*
		$res = $this->Stat_page->get_stat_page(array('date' => 20110532));
		echo '<pre>';
		print_r(count($res));
		echo '</pre>';
		
		foreach ($res as $stat){
			echo '<pre>';
			print_r($stat);
			echo '</pre>';
		}
		 */
		$this->Stat_page->increment_stat_page(21, 20, 20110532);
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