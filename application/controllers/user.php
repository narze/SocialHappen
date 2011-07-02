<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('facebook');
	}
	
	function index(){
		$this -> socialhappen -> check_logged_in('home');
		$user_id = $this->input->get('uid');
		if($user_id){
			$page_id = $this->input->get('pid');
			$app_install_id = $this->input->get('aid');
			$campaign_id = $this->input->get('cid');
			if(!$page_id && !$app_install_id && !$campaign_id){
				//no id specified
			} else {
				$this -> load -> model('company_model', 'companies');
				$this -> load -> model('user_model','users');
				$user = $this -> users -> get_user_profile_by_user_id($user_id);
				$breadcrumb = array();
				if($page_id){
					$this -> load -> model('page_model', 'pages');
					$company = $this -> companies -> get_company_profile_by_page_id($page_id);
					$page = $this -> pages -> get_page_profile_by_page_id($page_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$page['page_name'] => base_url() . "page/{$page['page_id']}",
							$user['user_first_name'].' '.$user['user_last_name'] => NULL
						);
				} else if ($app_install_id){
					$this -> load -> model('installed_apps_model', 'installed_apps');
					$company = $this -> companies -> get_company_profile_by_app_install_id($app_install_id);
					$app = $this -> installed_apps -> get_app_profile_by_app_install_id($app_install_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$app['app_name'] => base_url() . "app/{$app['app_install_id']}",
							$user['user_first_name'].' '.$user['user_last_name'] => NULL
						);
				} else if ($campaign_id){
					$this -> load -> model('campaign_model', 'campaigns');
					$company = $this -> companies -> get_company_profile_by_campaign_id($campaign_id);
					$campaign = $this -> campaigns -> get_campaign_profile_by_campaign_id($campaign_id);
					$breadcrumb = 
						array( 
							$company['company_name'] => base_url() . "company/{$company['company_id']}",
							$campaign['campaign_name'] => base_url() . "campaign/{$campaign['campaign_id']}",
							$user['user_first_name'].' '.$user['user_last_name'] => NULL
						);
				}
				
				$data = array(
				'user_id' => $user_id,
				'page_id' => issetor($page_id),
				'app_install_id' => issetor($app_install_id),
				'campaign_id' => issetor($campaign_id),
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => $user['user_first_name'].' '.$user['user_last_name'],
						'vars' => array('user_id'=>$user_id),
						'script' => array(
							'common/bar',
							'user/user_stat',
							'user/user_activities',
							'user/user_tabs'
						),
						'style' => array(
							'common/main',
							'common/platform'
						)
					)
				),
				'company_image_and_name' => $this -> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', array('breadcrumb' => $breadcrumb),	TRUE),
				'user_profile' => $this -> load -> view('user/user_profile', 
					array('user_profile' => $user),
				TRUE),
				'user_tabs' => $this -> load -> view('user/user_tabs', 
					array(),
				TRUE), 
				'user_stat' => $this -> load -> view('user/user_stat', 
					array(),
				TRUE), 
				'user_activities' => $this -> load -> view('user/user_activities', 
					array(),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
				);
				$this -> parser -> parse('user/user_view', $data);
			}
			
		}
	}
	
	/**
	 * Redirect to index with get parameters
	 * @param $user_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function page($user_id, $page_id){
		redirect("user?uid={$user_id}&pid={$page_id}");
	}
	
	/**
	 * Redirect to index with get parameters
	 * @param $user_id
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function app($user_id, $app_install_id){
		redirect("user?uid={$user_id}&aid={$app_install_id}");
	}
	
	/**
	 * Redirect to index with get parameters
	 * @param $user_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function campaign($user_id, $campaign_id){
		redirect("user?uid={$user_id}&cid={$campaign_id}");
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_profile($user_id = NULL){
		$this->load->model('user_model','users');
		$profile = $this->users->get_user_profile_by_user_id($user_id);
		echo json_encode($profile);
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_apps($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('user_apps_model','user_apps');
		$apps = $this->user_apps->get_user_apps_by_user_id($user_id, $limit, $offset);
		echo json_encode($apps);
	}
	
	/** 
	 * JSON : Get user profile
	 * @param $user_id
	 * @author Prachya P.
	 * @author Manassarn M.
	 */
	function json_get_campaigns($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('user_campaigns_model','users_campaigns');
		$campaigns = $this->users_campaigns->get_user_campaigns_by_user_id($user_id, $limit, $offset);
		echo json_encode($campaigns);
	}
	
	/**
	 * JSON : Get facebook pages owned by the current user
	 * @author Prachya P.
	 */
	function json_get_facebook_pages_owned_by_user(){
		echo json_encode($this->facebook->get_user_pages());
	}
	
	/**
	 * JSON : Add user
	 * @author Manassarn M.
	 */
	function json_add(){
		$this->load->model('user_model','users');
		$post_data = array(
							'user_first_name' => $this->input->post('user_first_name'),
							'user_last_name' => $this->input->post('user_last_name'),
							'user_email' => $this->input->post('user_email'),
							'user_image' => $this->input->post('user_image'),
							'user_facebook_id' => $this->input->post('user_facebook_id')
						);
		if($user_id = $this->users->add_user($post_data)){
			$result->status = 'OK';
			$result->user_id = $user_id;
		} else {
			$result->status = 'ERROR';
		}
		echo json_encode($result);
	}
	
	/**
	 * JSON : Get user companies
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function json_get_companies($user_id = NULL, $limit = NULL, $offset = NULL){
		$this->load->model('user_companies_model','user_companies');
		$companies = $this->user_companies->get_user_companies_by_user_id($user_id, $limit, $offset);
		echo json_encode($companies);
	}
}


/* End of file user.php */
/* Location: ./application/controllers/user.php */