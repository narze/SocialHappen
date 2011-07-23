<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MongoDB Audit, Log and Stat Library
 *
 * A library to record audit to the NoSQL database MongoDB. 
 *
 * @author Metwara Narksook
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
	 * 
	 * @author Metwara Narksook
	 */
	
	function __construct(){
		if(!class_exists('Mongo')){
			show_error("The MongoDB PECL extension has not been installed or enabled", 500);
		}
		$this->CI =& get_instance();
	}
	
	/**
	 * create index for all collection in database
	 */
	function create_index(){
		$this->CI->load->model('audit_model', 'audit');
		$this->CI->load->model('audit_action_model', 'audit_action');
		$this->CI->load->model('stat_page_model', 'stat_page');
		$this->CI->load->model('stat_app_model', 'stat_app');
		$this->CI->load->model('stat_campaign_model', 'stat_campaign');
		$this->CI->audit->create_index();
		$this->CI->audit_action->create_index();
		$this->CI->stat_page->create_index();
		$this->CI->stat_app->create_index();
		$this->CI->stat_campaign->create_index();
	}
	
	/**
	 * add new audit action
	 * 
	 * @param app_id int id of app
	 * @param action_id int action number - unique
	 * @param description string description of action
	 * @param stat_app boolean want to keep in stat app or not
	 * @param stat_page boolean want to keep in stat page or not
	 * @param stat_campaign boolean want to keep in stat campaign or not
	 * @param format_string string format of action string to be displayed
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function add_audit_action($app_id = NULL, $action_id = NULL, $description = NULL, $stat_app = NULL, 
							$stat_page = NULL, $stat_campaign = NULL, $format_string = NULL){
		$check_args = isset($app_id) && isset($action_id) && isset($stat_app) && 
						isset($stat_page) && isset($stat_campaign) && isset($description)
						&& isset($format_string);
		if(!$check_args){
			//show_error("Invalid or missing args", 500);
			return FALSE;
		}
		$this->CI->load->model('audit_action_model','audit_action');
		
		$data = array('app_id' =>(int)$app_id,
						'action_id' => (int)$action_id,
						'description' => $description,
						'format_string' => $format_string,
						'stat_app' => (boolean)$stat_app,
						'stat_page' => (boolean)$stat_page,
						'stat_campaign' => (boolean)$stat_campaign);
		$result = $this->CI->audit_action->add_action($data);
		/*
		if(!$result){
			show_error("add new audit action fail", 500);
		}
		*/
		return $result;
	}
	
	/**
	 * edit exists audit action
	 * 
	 * @param app_id int id of app
	 * @param action_id int action number
	 * @param data array contain ['stat_app', 'stat_page', 'stat_campaign', 'description', 'format_string']
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function edit_audit_action($app_id = NULL, $action_id = NULL, $data = NULL){
		$check_args = isset($app_id) && isset($action_id) && 
						(isset($data['stat_app']) || isset($data['stat_page']) || 
						isset($data['stat_campaign']) || isset($data['description'])
						|| isset($data['format_string']));
		if(!$check_args){
			//show_error("Invalid or missing args", 500);
			return FALSE;
		}
		
		$this->CI->load->model('audit_action_model','audit_action');
		
		$data_to_add = array();
		if(isset($data['stat_app'])){
			$data_to_add['stat_app'] = (boolean)$data['stat_app'];
		}
		if(isset($data['stat_page'])){
			$data_to_add['stat_page'] = (boolean)$data['stat_page'];
		}
		if(isset($data['stat_campaign'])){
			$data_to_add['stat_campaign'] = (boolean)$data['stat_campaign'];
		}
		if(isset($data['description'])){
			$data_to_add['description'] = $data['description'];
		}
		if(isset($data['format_string'])){
			$data_to_add['format_string'] = $data['format_string'];
		}
		
		$result = $this->CI->audit_action->edit_action((int)$app_id, (int)$action_id, $data_to_add);
		/*
		if(!$result){
			show_error("edit audit action fail", 500);
		}
		*/
		return $result;
	}
	
	/**
	 * delete audit action
	 * 
	 * @param app_id int id of app
	 * @param action_id int action number[optional]
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function delete_audit_action($app_id = NULL, $action_id = NULL){
		$check_args = isset($app_id);
		if(!$check_args){
			//show_error("Invalid or missing args", 500);
			return FALSE;
		}
		
		$this->CI->load->model('audit_action_model','audit_action');
		$result = $this->CI->audit_action->delete_action((int)$app_id, (int)$action_id);
		return $result;
	}
	
	/**
	 * list audit action
	 * 
	 * @param app_id int [optional]
	 * 
	 * @return result list of audit action
	 * 
	 * @author Metwara Narksook
	 */
	function list_audit_action($app_id = NULL){
		$this->CI->load->model('audit_action_model','audit_action');
		if(isset($app_id)){
			$result = $this->CI->audit_action->get_action((int)$app_id);
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
	 * 
	 * @author Metwara Narksook
	 */
	function get_audit_action($app_id = NULL, $action_id = NULL){
		$check_args = isset($app_id) && isset($action_id);
		if(!$check_args){
			return FALSE;
			//show_error("Invalid or missing args", 500);
		}
		$app_id = (int)$app_id;
		$action_id = (int)$action_id;
		$this->CI->load->model('audit_action_model','audit_action');
		if($action_id < 1000){ // default action
			$result = $this->CI->audit_action->get_action(0, $action_id);
		}else{ // custom action
			$result = $this->CI->audit_action->get_action($app_id, $action_id);
		}
		
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
	 * 
	 * @author Metwara Narksook
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
	 * @param additional_data array of additional data may contains ['app_install_id', 'user_id', 'campaign_id', 'company_id', 'page_id']
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function add_audit($app_id = NULL, $subject = NULL, $action_id = NULL, $object = NULL, $objecti = NULL, $additional_data = array()){
		$check_args = isset($app_id) && isset($action_id);
		if(!$check_args){
			//show_error("Invalid or missing args", 500);
			return FALSE;
		}
		
		$app_id = (int)$app_id;
		$action_id = (int)$action_id;
		// check valid action_id
		$this->CI->load->model('audit_action_model','audit_action');
		if($action_id < 1000){ // default action
			$result_audit_action = $this->CI->audit_action->get_action(0, $action_id);
		}else{ // custom action
			$result_audit_action = $this->CI->audit_action->get_action($app_id, $action_id);
		}
		
		if(count($result_audit_action) == 0){
			return FALSE;
		}else{
			$audit_action = $result_audit_action[0];
		}
		$data_to_add = array();
		
		// basic data
		$data_to_add['app_id'] = $app_id;
		$data_to_add['subject'] = ''.$subject;
		$data_to_add['action_id'] = $action_id;
		$data_to_add['object'] = ''.$object;
		$data_to_add['objecti'] = ''.$objecti;
		
		// additional data
		if(isset($additional_data['app_install_id'])){
			$data_to_add['app_install_id'] = (int)$additional_data['app_install_id'];
		}
		if(isset($additional_data['user_id'])){
			$data_to_add['user_id'] = (int)$additional_data['user_id'];
		}
		if(isset($additional_data['campaign_id'])){
			$data_to_add['campaign_id'] = (int)$additional_data['campaign_id'];
		}
		if(isset($additional_data['company_id'])){
			$data_to_add['company_id'] = (int)$additional_data['company_id'];
		}
		if(isset($additional_data['page_id'])){
			$data_to_add['page_id'] = (int)$additional_data['page_id'];
		}
		
		//echo '<pre>' . print_r($data_to_add, TRUE) . '</pre>';
		
		// TODO: select stat to add
		$this->CI->load->model('audit_model','audit');
		$result_add_audit = $this->CI->audit->add_audit($data_to_add);
		if($result_add_audit){
			if(isset($data_to_add['app_install_id']) && isset($audit_action['stat_app']) && $audit_action['stat_app']){
				$this->CI->load->model('stat_app_model','stat_app');
				$result_stat = $this->CI->stat_app->increment_stat_app($app_id, $data_to_add['app_install_id'], $action_id, $this->_date());
			}
			if(isset($data_to_add['page_id']) && isset($audit_action['stat_page']) && $audit_action['stat_page']){
				$this->CI->load->model('stat_page_model','stat_page');
				$result_stat = $this->CI->stat_page->increment_stat_page($app_id, $data_to_add['page_id'], $action_id, $this->_date());
			}
			if(isset($data_to_add['campaign_id']) && isset($audit_action['stat_campaign']) && $audit_action['stat_campaign']){
				$this->CI->load->model('stat_campaign_model','stat_campaign');
				$result_stat = $this->CI->stat_campaign->increment_stat_campaign($app_id, $data_to_add['campaign_id'], $action_id, $this->_date());
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
	 * 
	 * @author Metwara Narksook
	 */
	function list_audit($criteria = array(), $limit = 100, $offset = 0){
		$this->CI->load->model('audit_model','audit');
		$result = $this->CI->audit->list_audit($criteria, (int)$limit, (int)$offset);
		$action_list = array();
		
		//$this->CI->load->model('audit_action_model','audit_action');
		for($i = 0;$i < count($result); $i++){
			$app_id = $result[$i]['app_id'];
			$action_id = $result[$i]['action_id'];
			if(empty($action_list[$app_id.'_'.$action_id])){
				$result_action = $this->get_audit_action($app_id, $action_id);
				//var_dump($result_action);
				//$result_action = issetor($result_action[0]);
				if(isset($result_action)){
					$action_list[$app_id.'_'.$action_id] = $result_action;
				}
			}else{
				$result_action = $action_list[$app_id.'_'.$action_id];
			}
			
			if(isset($result_action['format_string'])){
				//echo '<pre>';
				//var_dump($result_action);
				//echo '</pre>';
				$result[$i]['message'] = $this->
						translate_format_string($result_action['format_string'],
												$result[$i]);
			}
		}
		return $result;
	}
	
	function transate_type($type, $value, $audit){
		$value = isset($audit[$value]) ? $audit[$value] : $value;
		switch ($type) {
			case 'app':
				$this->CI->load->model('app_model','app');
				$result = $this->CI->app->get_app_by_app_id($value);
				$app_name = isset($result['app_name']) ? $result['app_name'] : $value;
				$format_value = '<span class="type_app">'.$app_name.'</span>';
				break;
			case 'app_install':
				$this->CI->load->model('Installed_apps_model','app_install');
				$result = $this->CI->app_install->get_app_profile_by_app_install_id($value);
				$app_name = isset($result['app_name']) ? $result['app_name'] : $value;
				$format_value = '<span class="type_app_install"><a href="'.base_url()
						   		.'app/'.$value.'">'.$app_name.'</a></span>';
				break;
			case 'user':
				$this->CI->load->model('User_model','user');
				$result = $this->CI->user->get_user_profile_by_user_id($value);
				$name = isset($result['user_first_name']) && isset($result['user_last_name']) > 0 ? $result['user_first_name'].' '.$result['user_last_name'] : $value;
				$format_value = '<span class="type_user"><a href="'.base_url()
						   		.'user/app/'.$value.'/'.$audit['app_id'].'">'.$name.'</a></span>';
				break;
			case 'campaign':
				$this->CI->load->model('Campaign_model','campaign');
				$result = $this->CI->campaign->get_campaign_profile_by_campaign_id($value);
				$campaign_name = isset($result['campaign_name']) ? $result['campaign_name'] : $value;
				$format_value = '<span class="type_campaign"><a href="'.base_url()
						   		.'campaign/'.$value.'">'.$campaign_name.'</a></span>';
				break;
			case 'page':
				$this->CI->load->model('Page_model','page');
				$result = $this->CI->page->get_page_profile_by_page_id($value);
				$page_name = isset($result['page_name']) ? $result['page_name'] : $value;
				$format_value = '<span class="type_page"><a href="'.base_url()
						   		.'page/'.$value.'">'.$page_name.'</a></span>';
				break;
			case 'company':
				$this->CI->load->model('Company_model','company');
				$result = $this->CI->company->get_company_profile_by_company_id($value);
				$company_name = isset($result['company_name']) ? $result['company_name'] : $value;
				$format_value = '<span class="type_company"><a href="'.base_url()
						   		.'company/'.$value.'">'.$company_name.'</a></span>';
				break;
			case 'number':
				$format_value = $value;
				break;
			case 'string':
				$format_value = $value;
				break;
			default:
				$format_value = $value;
				break;
		}
		//$format_value = str_replace('{'.$type.'}', issetor($audit[$value]), $format_type[$type]);
		return $format_value;
	}
	
	function translate_format_string($format_string, $audit){
		
		preg_match_all('/(?P<type>\w+):(?P<variable>\w+)/', $format_string, $matches);
		/*
		echo '<pre>';
		print_r($matches);
		echo '</pre>';
		*/
		$type = $matches['type'];
		$value = $matches['variable'];
		
		$out = $format_string;
		for($i = 0; $i < count($type); $i++){
			//$format_type = $this->transate_type($type[$i], $value[$i], $audit);
			if(isset($audit[$value[$i]])){
				$format_value = $this->transate_type($type[$i], $value[$i], $audit);
				//$format_value = str_replace('{'.$type[$i].'}', issetor($audit[$value[$i]]), $format_type);
			}else{
				$format_value = $value[$i];
			}
			
			$out = str_replace('{'.$type[$i].':'.$value[$i].'}', $format_value, $out);
		}
		
		return $out;
	}
	/**
	 * list recent audit entry
	 * @param limit number of entries to get [optional - default 100]
	 * 
	 * @return array of audit object item
	 * 
	 * @author Metwara Narksook
	 */
	function list_recent_audit($limit = 100){
		$this->CI->load->model('audit_model','audit');
		//$result = $this->CI->audit->list_recent_audit((int)$limit);
		$result = $this->list_audit(array(), $limit, 0);
		return $result;
	}

	/**
	 * generate date format in yearmonthdate for stat ex. 20110531
	 * 
	 * @return int
	 * 
	 * @author Metwara Narksook
	 */
	function _date(){
		date_default_timezone_set('Asia/Bangkok');
		return (int)Date('Ymd');
	}
	
	function get_date_range($startDate, $endDate, $format="Ymd")
	{
		date_default_timezone_set('Asia/Bangkok');
	    //Create output variable
	    $datesArray = array();
	    //Calculate number of days in the range
	    $total_days = round(abs(strtotime($endDate) - strtotime($startDate)) / 86400, 0) + 1;
	    if($total_days<0) { return false; }
	    //Populate array of weekdays and counts
	    for($day=0; $day<$total_days; $day++)
	    {
	        $datesArray[] = date($format, strtotime("{$startDate} + {$day} days"));
	    }
	    //Return results array
	    return $datesArray;
	}
	
	/**
	 * list stat app by $app_install_id or $action_id or both
	 * you can input date range in $start_date and $end_date parameter,
	 * or omit it, or input just start_date in case of retrieving stat of one date
	 * 
	 * ** app_install_id and action_id cannot empty both **
	 * 
	 * @param app_install_id [optional]
	 * @param action_id [optional]
	 * @param start_date - date in format yyyymmdd ex. 20100531 [optional]
	 * @param end_date - date in format yyyymmdd ex. 20100531 [optional]
	 * 
	 * @return result array of stat
	 * 
	 * @author Metwara Narksook
	 */
	function list_stat_app($app_install_id = NULL, $action_id = NULL, $start_date = NULL, $end_date = NULL){
		$this->CI->load->model('stat_app_model','stat_app');
		//$check_args = isset($app_install_id) || isset($action_id);
		$criteria = array();

		if(isset($app_install_id)){
			$criteria['app_install_id'] = (int)$app_install_id;
		}
		if(isset($action_id)){
			$criteria['action_id'] = (int)$action_id;
		}
		if(isset($start_date) && isset($end_date)){
			
			if($start_date > $end_date){ // swap
				$tmp = (int)$start_date;
				$start_date = (int)$end_date;
				$end_date = $tmp;
			}
			
			$criteria['date'] = array('$gte' => (int)$start_date, '$lte' => (int)$end_date);
		}else if(isset($start_date) && empty($end_date)){
			$criteria['date'] = (int)$start_date;
		}else if(empty($start_date) && isset($end_date)){
			$criteria['date'] = (int)$end_date;
		}
		//echo '<pre>' . print_r($criteria, TRUE) . '</pre>';
		$result_stat = $this->CI->stat_app->get_stat_app($criteria, 0, 0);
		return $result_stat;
	}
	
	/**
	 * delete stat app
	 * 
	 * @param _id MongoDB object id in integer value ex. 4de65e1b6c993acc0e000000
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function delete_stat_app($_id){
		$this->CI->load->model('stat_app_model','stat_app');
		$result_stat = $this->CI->stat_app->delete_stat_app($_id);
		return $result_stat;
	}
	
	/**
	 * list stat page by $page_id or $action_id or both
	 * you can input date range in $start_date and $end_date parameter,
	 * or omit it, or input just start_date in case of retrieving stat of one date
	 * 
	 * @param page_id [optional]
	 * @param action_id [optional]
	 * @param start_date - date in format yyyymmdd ex. 20100531 [optional]
	 * @param end_date - date in format yyyymmdd ex. 20100531 [optional]
	 * 
	 * @return result array of stat
	 * 
	 * @author Metwara Narksook
	 */
	function list_stat_page($page_id = NULL, $action_id = NULL, $start_date = NULL, $end_date = NULL){
		$this->CI->load->model('stat_page_model','stat_page');
		//$check_args = isset($page_id) || isset($action_id);
		$criteria = array();
		
		if(isset($page_id)){
			$criteria['page_id'] = (int)$page_id;
		}
		if(isset($action_id)){
			$criteria['action_id'] = (int)$action_id;
		}
		if(isset($start_date) && isset($end_date)){
			
			if($start_date > $end_date){ // swap
				$tmp = (int)$start_date;
				$start_date = (int)$end_date;
				$end_date = $tmp;
			}
			
			$criteria['date'] = array('$gte' => (int)$start_date, '$lte' => (int)$end_date);
		}else if(isset($start_date) && empty($end_date)){
			$criteria['date'] = (int)$start_date;
		}else if(empty($start_date) && isset($end_date)){
			$criteria['date'] = (int)$end_date;
		}
		$result_stat = $this->CI->stat_page->get_stat_page($criteria, 0, 0);
		return $result_stat;
	}
	
	/**
	 * delete stat page
	 * 
	 * @param _id MongoDB object id in integer value ex. 4de65e1b6c993acc0e000000
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function delete_stat_page($_id){
		$this->CI->load->model('stat_page_model','stat_page');
		$result_stat = $this->CI->stat_page->delete_stat_page($_id);
		return $result_stat;
	}
	
	/**
	 * list stat campaign by $campaign_install_id or $action_id or both
	 * you can input date range in $start_date and $end_date parameter,
	 * or omit it, or input just start_date in case of retrieving stat of one date
	 * 
	 * @param campaign_install_id [optional]
	 * @param action_id [optional]
	 * @param start_date - date in format yyyymmdd ex. 20100531 [optional]
	 * @param end_date - date in format yyyymmdd ex. 20100531 [optional]
	 * 
	 * @return result array of stat
	 * 
	 * @author Metwara Narksook
	 */
	function list_stat_campaign($campaign_id = NULL, $action_id = NULL, $start_date = NULL, $end_date = NULL){
		$this->CI->load->model('stat_campaign_model','stat_campaign');
		//$check_args = isset($campaign_id) || isset($action_id);
		$criteria = array();
		
		if(isset($campaign_id)){
			$criteria['campaign_id'] = (int)$campaign_id;
		}
		if(isset($action_id)){
			$criteria['action_id'] = (int)$action_id;
		}
		if(isset($start_date) && isset($end_date)){
			
			if($start_date > $end_date){ // swap
				$tmp = (int)$start_date;
				$start_date = (int)$end_date;
				$end_date = $tmp;
			}
			
			$criteria['date'] = array('$gte' => (int)$start_date, '$lte' => (int)$end_date);
		}else if(isset($start_date) && empty($end_date)){
			$criteria['date'] = (int)$start_date;
		}else if(empty($start_date) && isset($end_date)){
			$criteria['date'] = (int)$end_date;
		}
		$result_stat = $this->CI->stat_campaign->get_stat_campaign($criteria, 0, 0);
		return $result_stat;
	}
	
	/**
	 * delete stat campaign
	 * 
	 * @param _id MongoDB object id in integer value ex. 4de65e1b6c993acc0e000000
	 * 
	 * @return result boolean
	 * 
	 * @author Metwara Narksook
	 */
	function delete_stat_campaign($_id){
		if(empty($_id)){
			return false;
		}
		$this->CI->load->model('stat_campaign_model','stat_campaign');
		$result_stat = $this->CI->stat_campaign->delete_stat_campaign($_id);
		return $result_stat;
	}
	
	/**
	 * count audit with distinct key for single day
	 * 
	 * @param key string to distinct (ex. subject)
	 * @param app_id [optional]
	 * @param action_id
	 * @param criteria array of criteria may contain ['app_install_id', 'subject', 'object', 'objecti', 'page_id', 'campaign_id']
	 * @param date - date in format yyyymmdd ex. 20100531
	 * 
	 * @return int
	 */
	function count_audit($key = NULL, $app_id = NULL, $action_id = NULL, $criteria = NULL, $date = NULL){
		return $this->count_audit_range($key, (int)$app_id, (int)$action_id, $criteria, $date, (int)$date);
	}
	
	/**
	 * count audit with distinct key for single day
	 * 
	 * @param key string to distinct (ex. subject)
	 * @param app_id [optional]
	 * @param action_id
	 * @param criteria array of criteria may contain ['app_install_id', 'subject', 'object', 'objecti', 'page_id', 'campaign_id']
	 * @param start_date - date in format yyyymmdd ex. 20100531
	 * @param end_date - date in format yyyymmdd ex. 20100531
	 * 
	 * @return int
	 */
	function count_audit_range($key = NULL, $app_id = NULL, $action_id = NULL, $criteria = NULL, $start_date = NULL, $end_date = NULL){
		$check_args = isset($key) && isset($action_id) && isset($criteria) && isset($start_date);
		if(!$check_args){
			return NULL;
		}
		$this->CI->load->model('audit_model', 'audit');
		
		$db_criteria = array();
		if(isset($app_id)){
			$db_criteria['app_id'] = (int)$app_id;
		}
		
		$db_criteria['action_id'] = (int)$action_id;
		
		if(isset($criteria['subject'])){
			$db_criteria['subject'] = ''.$criteria['subject'];
		}
		
		if(isset($criteria['object'])){
			$db_criteria['object'] = ''.$criteria['object'];
		}
		
		if(isset($criteria['objecti'])){
			$db_criteria['objecti'] = ''.$criteria['objecti'];
		}
		
		if(isset($criteria['app_install_id'])){
			$db_criteria['app_install_id'] = (int)$criteria['app_install_id'];
		}
		if(isset($criteria['page_id'])){
			$db_criteria['page_id'] = (int)$criteria['page_id'];
		}
		if(isset($criteria['campaign_id'])){
			$db_criteria['campaign_id'] = (int)$criteria['campaign_id'];
		}
		
		//echo 'count_audit <pre>';
		//var_dump($db_criteria);
		//echo '</pre>';
		
		return $this->CI->audit->count_distinct_audit($key, $db_criteria, $this->convert_statdate_to_date((int)$start_date), $this->convert_statdate_to_date((int)$end_date));
	}
	
	/**
	 * convert date in format yyyymmdd to timestamp like 1308589200 (from php time())
	 * @param date - date in format yyyymmdd ex. 20100531
	 */
	function convert_statdate_to_date($date = NULL){
		if(empty($date)){
			return NULL;
		}
		$date = $date . '';
		$y = substr($date, 0, 4);
		$m = substr($date, 4, 2);
		$d = substr($date, 6, 2);
		date_default_timezone_set('Asia/Bangkok');
		return mktime(0, 0, 0, $m, $d, $y);
	}
	
	/**
	 * render stat graph
	 * 
	 * @param data_label array of strings of labels of data line to be plotted
	 * @param data - array of array of stat date that has x and y
	 * @param title - string name of graph (title)
	 * @param div - array of div properties may contains ['id', 'width', 'height', 'class', 'xlabel', 'ylabel']
	 * 
	 * 
	 * @return html of stat graph
	 */
	function render_stat_graph($data_label = NULL, $data = NULL, $title = NULL, $div = NULL){
		$check_args = isset($data_label) && isset($data) && isset($title) && isset($div) && isset($div['id']) && 
						isset($div['height']) && isset($div['width']);
		if(!$check_args){
			return NULL;
		}
		$html = '<div id="'.$div['id'].'" style="height:'.$div['height'].'px;width:'.$div['width'].'px; class="'.$div['class'].'"></div>';
		$html .= '<script language="javascript" type="text/javascript">';
		$html .= "
		
$(document).ready(function(){";
$line_string = array();
foreach ($data as $line_key => $line_value) {
	$html .= 'var line' . $line_key . ' = [';
	$line_string[] = 'line' . $line_key;
	
	$point = array();
	foreach($line_value as $point_key => $point_value){
		$timestamp = $this->convert_statdate_to_date($point_key);
		date_default_timezone_set('Asia/Bangkok');
		$date = date('d', $timestamp) . '-' . date('M', $timestamp) . '-' . date('Y', $timestamp);
		$point[] = '[\'' . $date . '\', '.$point_value.']';
	}
	$html .= implode(', ', $point);
	$html .= "];\n";
}
/*
  var line1=[['23-May-08', 578.55], ['20-Jun-08', 566.5], ['25-Jul-08', 480.88], ['22-Aug-08', 509.84],
      ['26-Sep-08', 454.13], ['24-Oct-08', 379.75], ['21-Nov-08', 303], ['26-Dec-08', 308.56],
      ['23-Jan-09', 299.14], ['20-Feb-09', 346.51], ['20-Mar-09', 325.99], ['24-Apr-09', 386.15]];
 var line2=[['23-May-08', 78.55], ['20-Jun-08', 66.5], ['25-Jul-08', 80.88], ['22-Aug-08', 59.84],
      ['26-Sep-08', 54.13], ['24-Oct-08', 79.75], ['21-Nov-08', 53], ['26-Dec-08', 38.56],
      ['23-Jan-09', 99.14], ['20-Feb-09', 46.51], ['20-Mar-09', 25.99], ['24-Apr-09', 86.15]];
 * 
 * {label: '1'},
      {label: '2'},
      {label: '3'}
*/	
	$data_label_string = array();
	foreach($data_label as $label){
		$data_label_string [] = '{label:"'.$label.'"}';
	}
	$data_label_string = implode(', ', $data_label_string);

	$xlabel = isset($div['xlabel']) ? $div['xlabel'] : '';
	$ylabel = isset($div['ylabel']) ? $div['ylabel'] : '';
		$html .= "
  var plot1 = $.jqplot('".$div['id']."', [".implode(', ', $line_string)."], {
      title:'".$title."',
      legend: {
        show: true,
        location: 'ne'
      },
      seriesDefaults: {
      	//show: true,
      	shadow: true,
        showMarker:true,
        pointLabels: { show:true },
        label: '555' 
      },
      series:[
      ".$data_label_string."
      ],
      axes:{
        xaxis:{
          tickRenderer: $.jqplot.CanvasAxisTickRenderer,
          renderer:$.jqplot.DateAxisRenderer,
          tickOptions:{
            formatString:'%b %#d, %Y',
            angle: -30
          },
          label:'" . $xlabel . "'
        },
        yaxis:{
          tickOptions:{
            formatString:'%d'
            },
		  //min: 0,
		  label:'" . $ylabel . "'
        }
      },
      highlighter: {
        show: true,
        sizeAdjust: 7.5
      },
	  cursor:{
        show: true,
        zoom:true, 
        showTooltip:false
      }
      
  });
});
		
		";
		$html .= '</script>';
		return $html;
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