<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MongoDB Audit, Log and Stat Library
 *
 * A library to record audit to the NoSQL database MongoDB. 
 *
 * @author		Metwara Narksook
 */

class Audit_lib
{
	
	private $CI;
	
	/**
	 *	--------------------------------------------------------------------------------
	 *	CONSTRUCTOR
	 *	--------------------------------------------------------------------------------
	 *
	 *	Automatically check if the Mongo PECL extension has been installed/enabled.
	 */
	
	function __construct(){
		if(!class_exists('Mongo')){
			show_error("The MongoDB PECL extension has not been installed or enabled", 500);
		}
		$this->CI =& get_instance();
	}
	
	/**
	 * add new audit action
	 * 
	 * @param app_id int id of app
	 * @param action_id int action number - unique
	 * @param stat boolean want to keep in stat or not
	 * @param description string description of action
	 */
	function add_audit_action($app_id = NULL, $action_id = NULL, $stat_app = NULL, 
							$stat_page = NULL, $stat_campaign = NULL, $description = NULL){
		$check_args = isset($app_id) && isset($action_id) && isset($stat_app) && 
						isset($stat_page) && isset($stat_campaign) && isset($description);
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		$this->CI->load->model('audit_action_model','audit_action');
		
		$data = array('app_id' => $app_id,
						'action_id' => $action_id,
						'stat_app' => $stat_app,
						'stat_page' => $stat_page,
						'stat_campaign' => $stat_campaign,
						'description' => $description);
		$result = $this->CI->audit_action->add_action($data);
		if(!$result){
			show_error("add new audit action fail", 500);
		}
		return $result;
	}
	
	/**
	 * edit exists audit action
	 * 
	 * @param app_id int id of app
	 * @param action_id int action number
	 * 
	 * @param data array contain ['stat_app', 'stat_page', 'stat_campaign', 'description']
	 */
	function edit_audit_action($app_id = NULL, $action_id = NULL, $data = NULL){
		$check_args = isset($app_id) && isset($action_id) && 
						(isset($data['stat_app']) || isset($data['stat_page']) || 
						isset($data['stat_campaign']) || isset($data['description']));
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		
		$this->CI->load->model('audit_action_model','audit_action');
		
		$data_to_add = array();
		if(isset($data['stat_app'])){
			$data_to_add['stat_app'] = $data['stat_app'];
		}
		if(isset($data['stat_page'])){
			$data_to_add['stat_page'] = $data['stat_page'];
		}
		if(isset($data['stat_campaign'])){
			$data_to_add['stat_campaign'] = $data['stat_campaign'];
		}
		if(isset($data['description'])){
			$data_to_add['description'] = $data['description'];
		}
		
		$result = $this->CI->audit_action->edit_action($app_id, $action_id, $data_to_add);
		if(!$result){
			show_error("edit audit action fail", 500);
		}
		return $result;
	}
	
	/**
	 * delete audit action
	 * 
	 * @param app_id int id of app
	 * @param action_id int action number[optional]
	 */
	function delete_audit_action($app_id = NULL, $action_id = NULL){
		$check_args = isset($app_id);
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		
		$this->CI->load->model('audit_action_model','audit_action');
		$result = $this->CI->audit_action->delete_action($app_id, $action_id);
		return $result;
	}
	
	/**
	 * list audit action
	 * 
	 * @param app_id int [optional]
	 */
	function list_audit_action($app_id = NULL){
		$this->CI->load->model('audit_action_model','audit_action');
		if(isset($app_id)){
			$result = $this->CI->audit_action->get_action($app_id);
		}else{
			$result = $this->CI->audit_action->get_action_list();
		}
		return $result;
	}
	
	/**
	 * get audit action
	 * 
	 * @param app_id int app_id
	 * @param action_id int action_id
	 * 
	 * @return array of attribute of an audit action item
	 */
	function get_audit_action($app_id = NULL, $action_id = NULL){
		$check_args = isset($app_id) && isset($action_id);
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		$this->CI->load->model('audit_action_model','audit_action');
		$result = $this->CI->audit_action->get_action($app_id, $action_id);
		if(count($result) == 0){
			return NULL;
		}else{
			return $result[0];
		}
	}
	
	/**
	 * list platform audit action
	 * 
	 * @return array of plat form action
	 */
	function list_platform_audit_action(){
		$this->CI->load->model('audit_action_model','audit_action');
		$result = $this->CI->audit_action->get_platform_action();
		return $result;
	}
	
	/**
	 * add new audit entry to database
	 * 
	 * @param app_id int
	 * @param subject string [optional]
	 * @param action_id int
	 * @param object string [optional]
	 * @param objecti string [optional]
	 * @param additional_data array of additional data may contains ['app_install_id', 'campaign_id', 'company_id', 'page_id']
	 * 
	 * @return result boolean
	 */
	function add_audit($app_id = NULL, $subject = NULL, $action_id = NULL, $object = NULL, $objecti = NULL, $additional_data = array()){
		$check_args = isset($app_id) && isset($action_id);
		if(!$check_args){
			show_error("Invalid or missing args", 500);
		}
		
		// check valid action_id
		$this->CI->load->model('audit_action_model','audit_action');
		$result_audit_action = $this->CI->audit_action->get_action($app_id, $action_id);
		if(count($result_audit_action) == 0){
			return FALSE;
		}else{
			$audit_action = $result[0];
		}
		$data_to_add = array();
		
		// basic data
		$data_to_add['app_id '] = $app_id;
		$data_to_add['subject '] = $subject;
		$data_to_add['action_id '] = $action_id;
		$data_to_add['objecti '] = $objecti;
		
		// additional data
		if($additional_data['app_install_id']){
			$data_to_add['app_install_id'] = $additional_data['app_install_id'];
		}
		if($additional_data['campaign_id']){
			$data_to_add['campaign_id'] = $additional_data['campaign_id'];
		}
		if($additional_data['company_id']){
			$data_to_add['company_id '] = $additional_data['company_id '];
		}
		if($additional_data['page_id']){
			$data_to_add['page_id'] = $additional_data['page_id'];
		}
		// @TODO: select stat to add
		$this->CI->load->model('audit_model','audit');
		$result_add_audit = $this->CI->audit->add_audit($data_to_add);
		if($result_add_audit){
			if(isset($data_to_add['app_install_id']) && isset($audit_action['stat_app']) && $audit_action['stat_app']){
				$this->CI->load->model('Stat_app_model','Stat_app');
				$result_stat = $this->CI->Stat_app_model->increment_stat_page($data_to_add['app_install_id'], $action_id, $this->_date());
			}
			if(isset($data_to_add['page_id']) && isset($audit_action['stat_page']) && $audit_action['stat_app']){
				$this->CI->load->model('Stat_page_model','Stat_page');
				$result_stat = $this->CI->Stat_page_model->increment_stat_page($data_to_add['page_id'], $action_id, $this->_date());
			}
			if(isset($data_to_add['campaign_id']) && isset($audit_action['stat_campaign']) && $audit_action['stat_campaign']){
				$this->CI->load->model('Stat_campaign_model','Stat_campaign');
				$result_stat = $this->CI->Stat_campaign_model->increment_stat_page($data_to_add['campaign_id'], $action_id, $this->_date());
			}
			
		}
		
		return $result_add_audit;
	}
	
	/**
	 * list audit data by input criteria to query
	 * 
	 * @param criteria array of attribute to query
	 * @param limit int number of results [optional - default 100]
	 * @param offset int offset number [optional - default 0]
	 * 
	 * @return result
	 */
	function list_audit($criteria = array(), $limit = 100, $offset = 0){
		$this->CI->load->model('audit_model','audit');
		$result = $this->CI->audit->list_audit($criteria, $limit, $offset);
		return $result;
	}
	
	/**
	 * list recent audit entry
	 * @param limit number of entries to get [optional - default 100]
	 * 
	 * @return array of audit object item
	 */
	function list_recent_audit($limit = 100){
		$this->CI->load->model('audit_model','audit');
		$result = $this->CI->audit->list_recent_audit($limit);
		return $result;
	}

	/**
	 * generate date format in yearmonthdate for stat ex. 20110531
	 * @return int
	 */
	function _date(){
		date_default_timezone_set('Asia/Bangkok');
		return Date('Ymd');
	}
	
	/*
	 * // optional date
	var $app_id = '';
	var $app_install_id = '';
	var $campaign_id = '';
	var $page_id = '';
	var $company_id = '';
	// basic data
	var $timestamp = '';
	var $subject = '';
	var $action_id = '';
	var $object = '';
	var $objecti = '';
	 * */
}

/* End of file audit_lib.php */
/* Location: ./application/libraries/audit_lib.php */