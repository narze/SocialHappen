<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class XD extends CI_Controller {

	function __construct(){
		parent::__construct();
		header("Access-Control-Allow-Origin: *");
	}

	function index(){
		$this->load->vars(array(
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'facebook_channel_url' => $this->facebook->channel_url
		));
		$this->load->view('xd/xd_view');
	}
	
	function login(){
		$this->socialhappen->login();
	}
	
	function logout(){
		$this->socialhappen->logout();
	}
	
	function get_user($page_id = NULL){
		if($user = $this->socialhappen->get_user()){
			$user['user_role'] = $this->get_role($user['user_id'], $page_id);
		} else {
			$user = array('user_id' => '', 'user_role' => 'guest');
		}
		// log_message('error',print_r($user,TRUE));
		echo json_encode($user);
	}
	
	function get_role($user_id = NULL, $page_id = NULL){
		$this->load->model('user_pages_model','user_page');
		$this->load->model('page_model','page');
		$page = $this->page->get_page_profile_by_page_id($page_id);
		if($this->user_page->is_page_admin($user_id, $page_id)){			
			$user_role = 'admin';
			
			$page_update = array();
			if(!$page['page_installed']){
				$page_update['page_installed'] = TRUE;
			} else if($page['page_app_installed_id'] != 0){
				$page_update['page_app_installed_id'] = 0;
			}		
			
			$this->page->update_page_profile_by_page_id($page_id, $page_update);
		} else {
			$user_role = 'user';
		}
		return $user_role;
	}
	
	function get_bar_content($view_as = NULL, $user_id = NULL, $page_id = NULL, $app_install_id = NULL){
		// $app_install_id = issetor($data['app_install_id']);
		// $page_id = issetor($data['page_id']);
		// $user_id = issetor($data['user_id']);
		// $user_facebook_id = issetor($data['user_facebook_id']);
		$app_mode = FALSE;
		
		$this->load->model('Installed_apps_model', 'app');
		$app = $this->app->get_app_profile_by_app_install_id($app_install_id);
		if($app && isset($app['page_id'])){
			$app_mode = TRUE;
			$page_id = $app['page_id'];
		}
		
		$this->load->model('User_model', 'User');
		$this->load->model('user_pages_model','user_pages');
		$this->load->model('page_model','pages');
		$this->load->model('session_model','session_model');
		$user = $this->User->get_user_profile_by_user_id($user_id);
		// $user = $user_id ? $this->User->get_user_profile_by_user_id($user_id) : $this->User->get_user_profile_by_user_facebook_id($user_facebook_id);
		$page = $this->pages->get_page_profile_by_page_id($page_id);
		if(!$page) {
			return;
		}

		$menu = array();
		//Right menu			
		//@TODO : This has problems with multiple login, cannot use user_agent to check due to blank user_agent when called via api
		if($view_as === 'guest'){ 
			$facebook_page = $this->facebook->get_page_info(issetor($page['facebook_page_id']));
			
			$signup_link = $facebook_page['link'].'?sk=app_'.$this->config->item('facebook_app_id');
		} else if($view_as === 'admin' && $this->user_pages->is_page_admin($user['user_id'], $page_id)){			
			
			
			$page_update = array();
			if(!$page['page_installed']){
				$page_update['page_installed'] = TRUE;
			} else if($page['page_app_installed_id'] != 0){
				$page_update['page_app_installed_id'] = 0;
			}		
			$this->pages->update_page_profile_by_page_id($page_id, $page_update);
		} else {
			// $view_as = 'user';
		}
		
		$menu['left'] = array();
		if($page_id){
			$apps = $this->app->get_installed_apps_by_page_id($page_id);
			$this->load->library('app_url');
			foreach($apps as $page_app){
				if($page_app['app_install_id'] != $app_install_id){
					$menu['left'][] = array(
						'location' => $page_app['facebook_tab_url'],
						'title' => $page_app['app_name'],
						'icon_url' => $page_app['app_icon'],
						'target' => '_top'
					);
				}
			}
		}
		
		// Moved to get_bar
		// $this->load->model('page_user_data_model','page_user_data');
		// $is_user_register_to_page = $this->page_user_data->get_page_user_by_user_id_and_page_id($user['user_id'], $page_id);
		
		$this->load->library('notification_lib');
		$notification_amount = $this->notification_lib->count_unread($user['user_id']);
		$app_data = array('view'=>'notification', 'return_url' => $app['facebook_tab_url'] );

		//User point
		$this->load->library('controller/tab_ctrl');
		$page_score = $this->tab_ctrl->get_page_score($user['user_facebook_id'], $page_id) | 0;

		$this->load->vars(array(
			'node_base_url' => $this->config->item('node_base_url'),
			'view_as' => $view_as,
			'app_install_id' => $app_install_id,
			'page_id' => $page_id,
			'page_score' => $page_score,
			'menu' => $menu,
			'user' => $user,
			'current_menu' => array(
				'icon_url' => $app_mode ? issetor($app['app_image']) : issetor($page['page_image']),
				'name' => $app_mode ? issetor($app['app_name']) : issetor($page['page_name'])
			),
			'signup_link' =>issetor($signup_link, '#'),
			'facebook_app_id' => $this->config->item('facebook_app_id'),
			'notification_amount' => $notification_amount,
			'all_notification_link' => $app_mode ? $this->socialhappen->get_tab_url_by_app_install_id($app_install_id).'&app_data='.base64_encode(json_encode($app_data)) : base_url().'tab/notifications/'.$user['user_id'],
			'app_mode' => $app_mode,
			
		));
		$this->load->view('api/app_bar_content');
	}

	function homepage($app_install_id = NULL){
		$this->load->library('homepage_lib');
		if(!$homepage = $this->homepage_lib->get_homepage_for_unliked_users($app_install_id)){
			echo json_encode(FALSE);
		}
		$result = array(
            'html' => '<p>'.$homepage['message'].'</p><p><img src="'.$homepage['image'].'"</img></p>'
            );
        echo json_encode($result);
	}

	function is_user_liked_page($facebook_page_id = NULL){
		echo json_encode($this->facebook->is_user_liked_page($facebook_page_id));
	}
}  

/* End of file xd.php */
/* Location: ./application/controllers/xd.php */