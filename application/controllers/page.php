<?php
if(!defined('BASEPATH'))
	exit('No direct script access allowed');

class Page extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->socialhappen->check_logged_in();
		$this->load->library('controller/page_ctrl');
	}
	
	function index($page_id = NULL) {
		if(!$this->socialhappen->is_developer_or_features_enabled(array('page_id'=>$page_id))){
			redirect_back();
		}
		$result = $this->page_ctrl->main($page_id);
		if($result['success']){
			$data = $result['data'];
			$this -> parser -> parse('page/page_view', $data);
		} else {
			echo $result['error'];
		}
	}
	
	/**
	 * JSON : Count apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_count_apps($page_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('installed_apps_model','installed_apps');
		$count = $this->installed_apps->count_installed_apps_by_page_id($page_id);
		echo json_encode($count);
	}
	
	/**
	 * JSON : Count campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_count_campaigns($page_id = NULL, $campaign_status_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('campaign_model','campaigns');
		$count = $this->campaigns->count_campaigns_by_page_id_and_campaign_status_id($page_id, $campaign_status_id);
		echo json_encode($count);
	}

	/**
	 * JSON : Count active campaigns
	 * @param $page_id
	 * @author Weerapat P.
	 */
	function json_count_active_campaigns($page_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('campaign_model','campaigns');
		$count = $this->campaigns->count_active_campaigns_by_page_id($page_id);
		echo json_encode($count);
	}

	/**
	 * JSON : Count expired campaigns
	 * @param $page_id
	 * @author Weerapat P.
	 */
	function json_count_expired_campaigns($page_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('campaign_model','campaigns');
		$count = $this->campaigns->count_expired_campaigns_by_page_id($page_id);
		echo json_encode($count);
	}
	
	
	/**
	 * JSON : Count apps
	 * @param $page_id
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function json_count_user_apps($user_id = NULL, $page_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('user_apps_model','user_apps');
		$count = $this->user_apps->count_user_apps_by_user_id_and_page_id($user_id, $page_id);
		echo json_encode($count);
	}
	
	/**
	 * JSON : Count campaigns
	 * @param $user_id
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_count_user_campaigns($user_id = NULL, $page_id = NULL, $campaign_status_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('user_campaigns_model','user_campaigns');
		$count = $this->user_campaigns->count_user_campaigns_by_user_id_and_page_id_and_campaign_status_id($user_id, $page_id, $campaign_status_id);
		echo json_encode($count);
	}

	/**
	 * JSON : Count active user campaigns
	 * @param $user_id
	 * @param $page_id
	 * @author Weerapat P.
	 */
	function json_count_active_user_campaigns($user_id = NULL, $page_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('user_campaigns_model','user_campaigns');
		$count = $this->user_campaigns->count_active_user_campaigns($user_id); //TODO: put $page_id or not?
		echo json_encode($count);
	}

	/**
	 * JSON : Count expired user campaigns
	 * @param $user_id
	 * @param $page_id
	 * @author Weerapat P.
	 */
	function count_expired_user_campaigns($user_id = NULL, $page_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('user_campaigns_model','user_campaigns');
		$count = $this->user_campaigns->count_active_user_campaigns($user_id); //TODO: put $page_id or not?
		echo json_encode($count);
	}
		
	/**
	 * JSON : Count users
	 * @param $page_id
	 * @param array $labels
	 * @author Manassarn M.
	 */
	function json_count_users($page_id = NULL, $labels = array()){
		$this->socialhappen->ajax_check();
		$this->load->model('page_user_data_model','page_users');
		$count = $this->page_users->count_page_users_by_page_id($page_id);
		echo json_encode($count);
	}
	
	/**
	 * JSON : Get page profile
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_profile($page_id =NULL) {
		$this->socialhappen->ajax_check();
		$profile = $this -> page_ctrl -> json_get_profile($page_id);
		echo $profile;
	}

	/**
	 * JSON : Get install apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_installed_apps($page_id =NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$json_out = $this->page_ctrl->json_get_installed_apps($page_id, $limit, $offset);
		echo $json_out;
	}

	/**
	 * JSON : Get campaigns
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns($page_id =NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$campaigns = $this->page_ctrl->json_get_campaigns($page_id, $limit, $offset);
		echo $campaigns;
	}
	
	/**
	 * JSON : Get campaigns
	 * @param $page_id
	 * @param $campaign_status_id
	 * @author Manassarn M.
	 */
	function json_get_campaigns_using_status($page_id =NULL, $campaign_status_id = NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$campaigns = $this->page_ctrl->json_get_campaigns_using_status($page_id, $campaign_status_id, $limit, $offset);
		echo $campaigns;
	}

	/**
	 * JSON : Get users
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function json_get_users($page_id =NULL, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		$users = $this->page_ctrl->json_get_users($page_id, $limit, $offset);
		echo $users;
	}

	/**
	 * JSON : Add page
	 * @author Manassarn M.
	 * @author Prachya P. - add audit when install page
	 */
	function json_add() {
		$this->socialhappen->ajax_check();

		$facebook_page_id = $this -> input -> post('facebook_page_id'); 
		$company_id = $this -> input -> post('company_id'); 
		$page_name = $this -> input -> post('page_name'); 
		$page_detail = $this -> input -> post('page_detail'); 
		$page_all_member = $this -> input -> post('page_all_member'); 
		$page_new_member = $this -> input -> post('page_new_member'); 
		$page_image = $this -> input -> post('page_image');
		$json_add_result = $this->page_ctrl->json_add($facebook_page_id,$company_id,$page_name,$page_detail,$page_all_member,$page_new_member,$page_image);

		if($json_add_result['success']){
			$socialhappen_app_id = $this->config->item('facebook_app_id');
			if($this->facebook->install_facebook_app_to_facebook_page_tab($socialhappen_app_id, $facebook_page_id)){
				$result['status'] = 'OK';
				$result['page_id'] = $json_add_result['data']['page_id'];
				$result['facebook_tab_url'] = $this->facebook->get_facebook_tab_url($socialhappen_app_id, $facebook_page_id);
				$this->pages->update_facebook_tab_url_by_facebook_page_id($facebook_page_id, $result['facebook_tab_url']);
			} else {
				log_message('error','cannot install app to facebook page tab');
				$result['status'] = 'ERROR';
				$result['message'] = 'Please manually add Socialhappen facebook app by this <link>';
			}
		} else {
			log_message('error','json_add failed');
			$result['status'] = 'ERROR';
			$result['message'] = $json_add_result['error'];
		}
		
		echo json_encode($result);
	}
	
	/**
	 * JSON : Get facebook pages available to install
	 * @author Prachya P.
	 * @author Manassarn M. (get page_pics POST data)
	 */
	function json_get_not_installed_facebook_pages($company_id, $limit = NULL, $offset = NULL){
		$this->socialhappen->ajax_check();
		if(!$page_pics = json_decode($this->input->post('page_pics'), TRUE)){
			echo json_encode(array());
			return;
		}
		$this->load->model('page_model','page');
		$pages = $this->page->get_company_pages_by_company_id($company_id, $limit, $offset);
		$all_installed_fb_page_id=array();
		foreach($pages as $page){
			$all_installed_fb_page_id[]=$page['facebook_page_id'];
		}
		$pics = array();
		$has_added_app = array();
		foreach($page_pics as $page_pic){
			$pics[$page_pic['page_id']] = $page_pic['pic'];
			$has_added_app[$page_pic['page_id']] = $page_pic['has_added_app'];
		}
		$not_installed_facebook_pages=array();
		$facebook_pages=$this->facebook->get_user_pages();
		if($facebook_pages!=NULL){
			$facebook_pages=$facebook_pages['data'];
			foreach($facebook_pages as $facebook_page){
				if(!in_array($facebook_page['id'],$all_installed_fb_page_id)){
					$facebook_page['page_info']['picture'] = $pics[$facebook_page['id']];
					$facebook_page['has_added_app'] = $has_added_app[$facebook_page['id']];
					$not_installed_facebook_pages[]=$facebook_page;
				}
			}
		}
		echo json_encode($not_installed_facebook_pages);
	}
	
	/**
	 * JSON : update page order in dashboard
	 * @author Prachya P.
	 */
	function json_update_page_order_in_dashboard($company_id){
		$this->socialhappen->ajax_check();
		$this->load->model('page_model','page');
		$page_orders=$_POST['page_orders'];
		$i=0;
		foreach($page_orders as $page_id){
			$this->page->update_page_profile_by_page_id($page_id,array('order_in_dashboard'=>$i));
			$i++;	
		}		
	}
	
	/**
	 * fancybox for adding app 
	 * @author Prachya P.
	 */
	function addapp_lightbox($page_id = NULL) {
		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			exit('You are not admin');
		}
		if($page_id){			
			$this -> load -> model('company_model', 'companies');
			$company = $this -> companies -> get_company_profile_by_page_id($page_id);
			$this -> load -> model('page_model', 'pages');
			$page = $this -> pages -> get_page_profile_by_page_id($page_id);
			$data = array(
				'page_id' => $page_id,
				'header' => $this -> socialhappen -> get_header_lightbox( 
					array(
						'vars' => array('company_id'=>$company['company_id'],
							'page_id'=>$page_id,
							'page_name'=>$page['page_name'],
							'user_id'=>$this->session->userdata('user_id'),
							'facebook_page_id' => $page['facebook_page_id']
						),
						'script' => array(
							'common/functions',
							'common/shDragging',
							'common/onload',
							'page/addapp_lightbox',
							//for fancybox
							'common/fancybox/jquery.mousewheel-3.0.4.pack',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'company/main',
							'common/smoothness/jquery-ui-1.8.9.custom',
							//for fancybox
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'footer' => $this -> socialhappen -> get_footer_lightbox()
			);
			$this->parser->parse('page/addapp_lightbox_view', $data);
		}
	}
	
	function get_stat_graph($page_id = NULL, $start_date = NULL, $end_date = NULL){
		$this->load->library('audit_lib');

		if(isset($start_date) && isset($end_date)){
			if($start_date > $end_date){
				$temp = $start_date;
				$start_date = $end_date;
				$end_date = $temp;
			}
		}else{
			date_default_timezone_set('UTC');
			$end_date = $this->audit_lib->_date();
			$start_date = date('Ymd', time() - 2592000);
		}
		
		$dateRange = $this->audit_lib->get_date_range($start_date, $end_date);
		
		//print_r($dateRange);
		$action_id = $this->socialhappen->get_k('audit_action', 'User Register App');
		$res = $this->audit_lib->list_stat_page((int)$page_id, $action_id, (int)$start_date, $end_date);
		$stat_campaign_visit_db = array();
		foreach($res as $item){
			$stat_page_register_db[$item['date']] = $item['count'];
		}
		
		$action_id = $this->socialhappen->get_k('audit_action', 'User Visit');
		$res = $this->audit_lib->list_stat_page((int)$page_id, $action_id, (int)$start_date, $end_date);
		$stat_page_visit_db = array();
		foreach($res as $item){
			$stat_page_visit_db[$item['date']] = $item['count'];
		}
		
		$stat_page_register = array();
		$stat_page_visit = array();
		foreach ($dateRange as $date) {
			$stat_page_register[$date] = isset($stat_page_register_db[$date]) ? $stat_page_register_db[$date] : 0;
			$stat_page_visit[$date] = isset($stat_page_visit_db[$date]) ? $stat_page_visit_db[$date] : 0;
		}
		
		$data = array($stat_page_register, $stat_page_visit);
		$data_label = array('user register to app in page', 'user visit app in page');
		$title = 'Users Participation in Page';
		$div = array('id' => 'chart1',
					'width' => 900,
					'height' => 480,
					'class' => 'chart',
					'xlabel' => 'Dates',
					'ylabel' => 'Users');
		//echo json_encode($data);
		echo $this->audit_lib->render_stat_graph($data_label, $data, $title, $div);
	}
	
	function json_get_page_user_data($user_id = NULL, $page_id = NULL){
		$this->socialhappen->ajax_check();
		$this->load->model('page_user_data_model', 'page_users');
		$page_user = $this->page_users->get_page_user_by_user_id_and_page_id($user_id, $page_id);
		echo json_encode(issetor($page_user['user_data'], array()));
	}
	
	/**
	 * Redirect to page config
	 * @author Manassarn M.
	 */
	function config($page_id = NULL){
		redirect(base_url().'settings/page_apps/'.$page_id);
	}
}

/* End of file page.php */
/* Location: ./application/controllers/page.php */
