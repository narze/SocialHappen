<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SocialHappen Platform Library
 * 
 * This class includes frequently used functions such as sessions & authentications
 * 
 * @author Manassarn M.
 */
class SocialHappen{
	private $CI;
	function __construct() {
        $this->CI =& get_instance();
    }
	
	/**
	 * Global variables
	 * @author Manassarn M.
	 */
	var $global_variables = array(
		'item_type' => array(1=>'Package', 2=>'App'),
		'order_status' => array(1=>'Pending',2=>'Processed',3=>'Failed',4=>'Refunded',5=>'Voided',6=>'Canceled'),
		'app_install_status' => array(1=>'Installed', 2=>'Active', 3=>'Inactive'),
		'app_type' => array(1=>'Page Only', 2=>'Support Page', 3=>'Standalone'),
		'campaign_status' => array(1=>'Inactive', 2=>'Active'),
		'page_status' => array(1=>'Not Installed', 2=>'Installed'),
		'user_gender' => array(1=>'Not sure', 2=>'Female', 3=>'Male'),
		'audit_action' => array( //See actions in MongoDB
			//backend
			1 => 'Install App',
			2 => 'Install App To Page',
			3 => 'Remove App',
			4 => 'Update Config',
			5 => 'Install Page',
			6 => 'Create Company',
			7 => 'Buy Package',
			8 => 'Buy Most Expensive Package',
			//front end
			101 => 'User Register SocialHappen',
			102 => 'User Register App',
			103 => 'User Visit',
			104 => 'User Action',
			105 => 'User Join Campaign',
			106 => 'User Register Page',
			107 => 'User Share Profile',
			108 => 'User Share',
			109 => 'User Login',
			110 => 'User Connect Twitter',
			111 => 'User Connect Facebook',
			112 => 'User Connect Foursquare',
			113 => 'User Invite',
			114 => 'Invitee Accept Page Invite',
			115 => 'Invitee Accept Campaign Invite',
			116 => 'User Redeem Reward'
		)
	);
	
	/** Default urls
	 * @author Manassarn M.'
	 */
	var $default_urls = array(
		'app_image' => 'assets/images/default/app.png',
		'app_icon' => 'assets/images/default/app_icon.png',
		'campaign_image' => 'assets/images/default/campaign.png',
		'company_image' => 'assets/images/default/company.png',
		'user_image' => 'assets/images/default/user.png'
	);
	
	/**
	 * Get global variable
	 * @param $var_name
	 * @author Manassarn
	 */
	function get_global($var_name){
		return issetor($this->global_variables[$var_name]);
	}
	
	/**
	 * Get a global variable key
	 * @param $var_name
	 * @param $value
	 * @author Manassarn M.
	 */
	function get_k($var_name = NULL, $value = NULL){
		return array_search(strtolower($value),array_map('strtolower',issetor($this->global_variables[$var_name], NULL)));
	}
	
	/**
	 * Get a global variable value
	 * @param $var_name
	 * @param $key
	 * @author Manassarn M.
	 */
	function get_v($var_name = NULL, $key = NULL){
		return issetor($this->global_variables[$var_name][$key], NULL);
	}
	
	/**
	 * Map global value in array
	 * @param &$each
	 * @param $var_name_and_array_index
	 */
	function map_one_v(&$each = NULL, $var_name_and_array_index = NULL){
		if($each) {
			if(is_array($var_name_and_array_index)){
				foreach($var_name_and_array_index as $one){
					$each[$one] = $this->get_v($one,issetor($each["{$one}_id"]));
				}
			} else {
				$each[$var_name_and_array_index] = $this->get_v($var_name_and_array_index,issetor($each["{$var_name_and_array_index}_id"]));
			}
		}
		return $each;
	}
	
	/**
	 * Maps global values in multi array
	 * @param &$array
	 * @param $var_name_and_array_index
	 */
	function map_v(&$array = array(), $var_name_and_array_index = NULL){
		foreach($array as &$each){
			$this->map_one_v($each,$var_name_and_array_index);
		}
		unset($each);
		return $array;
	}
	
	/**
	 * Get a default url
	 * @param $var_name
	 * @author Manassarn M.
	 */
	function get_default_url($var_name = NULL){
		if(isset($this->default_urls[$var_name])){
			return base_url().$this->default_urls[$var_name];
		}
		return NULL;
	}
	
	/**
	 * Check if logged in (both facebook and SocialHappen) if not, redirect to specified url
	 * @param $redirect_url
	 * @author Manassarn M. 
	 */
	function check_logged_in($redirect_url = NULL){
		if($this->is_logged_in()){
			return TRUE;
		} else {
			// $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') 
                // === FALSE ? 'http' : 'https';
				$protocol = 'https';
			$url = "{$protocol}://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
			redirect($redirect_url."?next=".urlencode($url));
		}
	}
    
	/**
	 * Check if logged in (both facebook and SocialHappen)
	 * @author Manassarn M. 
	 */
	function is_logged_in(){
		return ($this->CI->session->userdata('logged_in'));
	}
	
	/**
	 * Get user from session
	 * @return SocialHappen user if found, otherwise FALSE
	 * @author Manassarn M.
	 */
	function get_user(){
		if($this->CI->session->userdata('logged_in') == TRUE){
			$this->CI->load->model('user_model','users');
			return $this->CI->users->get_user_profile_by_user_id($this->CI->session->userdata('user_id'));
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get user id from session
	 * @return $user_id
	 * @author Manassarn M.
	 */
	function get_user_id(){
		if($this->CI->session->userdata('logged_in') == TRUE){
			return (int) $this->CI->session->userdata('user_id');
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get user companies from database
	 * @return $user_companies
	 * @author Manassarn M.
	 */
	function get_user_companies(){
		if($this->CI->session->userdata('logged_in') == TRUE){
			$this->CI->load->model('user_companies_model','user_companies');
			return $this->CI->user_companies->get_user_companies_by_user_id($this->CI->session->userdata('user_id'));
		} else {
			return array();
		}
	}
	
	/**
	 * Get header view, predefine $user and $user_companies in views
	 * @return $header
	 * @author Manassarn M.
	 */
	function get_header($data = array()){
		$common = array();
		$user = $this->get_user();
		$facebook_user = $this->CI->facebook->getUser();
		
		//if(!$facebook_user = $this->CI->facebook->getUser()){
		if(!$user) {
			$this->logout(); // should relogin facebook to extend cookies TODO : fix
			$common = array(
				'facebook_app_id' => $this->CI->config->item('facebook_app_id'),
				'facebook_default_scope' => $this->CI->config->item('facebook_admin_scope'),
				'next' => $this->CI->input->get('next'),
				'user_can_create_company' => FALSE,
				'facebook_channel_url' => $this->CI->facebook->channel_url,
				'facebook_user' => $facebook_user
			);
		} else {
			$this->CI->load->library('notification_lib');
			$user = $this->get_user();
			$user_companies = $this->get_user_companies();
			$common = array(
				'node_base_url' => $this->CI->config->item('node_base_url'),
				'facebook_app_id' => $this->CI->config->item('facebook_app_id'),
				'user' => $user,
				'image_url' => base_url().'assets/images/',
				'facebook_user' => $facebook_user,
				'script' => array(
					'common/functions',
					'common/onload',
					'common/jquery.form',
					'common/bar',
					'common/fancybox/jquery.fancybox-1.3.4.pack',
					'home/lightbox',
				),
				'style' => array(
					'common/common',
					'common/platform',
					'common/main',
					'common/fancybox/jquery.fancybox-1.3.4'
				),
				'user_can_create_company' => !$user_companies ? TRUE : $this->check_package_by_user_id_and_mode($this->CI->session->userdata('user_id'), 'company'), //Check user can create company
				'notification_amount' => $this->CI->notification_lib->count_unread($user['user_id']),
				'all_notification_link' => base_url().'temp',
				'facebook_channel_url' => base_url().'assets/channel/fb.php',
				'facebook_default_scope' => $this->CI->config->item('facebook_admin_scope')
			);
		}
		
		$data = array_merge_recursive($common,$data);
		$data = array_unique_recursive($data);
		
		//Override, because array_merge_recursive cause merge(user_id, user_id) -> array(user_id,user_id)
		if(isset($user['user_id'])){
			$data['vars']['user_id'] = $user['user_id'];
		}
		
		// $this->login(); Don't audologin
		if($this->CI->session->userdata('logged_in') == TRUE){
			$data['user_companies'] = isset($user_companies) ? $user_companies : NULL;
			if($this->CI->input->get('logged_in') == TRUE){
				if($data['user_companies']){
					$data['vars']['popup_name'] = 'bar/select_company';
				}
			}
		}
		
		if( isset($data['vars']['popup_name']) && !isset($data['vars']['closeEnable']) ) $data['vars']['closeEnable'] = true;
		
		return $this->CI->load->view('common/header', $data, TRUE);
	}

	/**
	 * Get header view, predefine $user and $user_companies in views
	 * @return $header
	 * @author Manassarn M., Weerapat P.
	 */
	function get_header_bootstrap($data = array()){
		$common = array();
		$user = $this->get_user();
		$facebook_user = $this->CI->facebook->getUser();
		$scripts = array(
					'common/bootstrap.min',
					'common/functions',
					//'common/onload',
					'common/jquery.form',
					//'common/bar',
					//'common/fancybox/jquery.fancybox-1.3.4.pack',
					//'home/lightbox',
				);
		$style = array(
					'common/bootstrap',
					'common/bootstrap-responsive',
					//'common/common',
					//'common/platform',
					//'common/main',
					//'common/fancybox/jquery.fancybox-1.3.4'
				);
		
		if(!$user) {
			$this->logout(); // should relogin facebook to extend cookies TODO : fix
			$common = array(
				'facebook_app_id' => $this->CI->config->item('facebook_app_id'),
				'facebook_default_scope' => $this->CI->config->item('facebook_admin_scope'),
				'next' => $this->CI->input->get('next'),
				'user_can_create_company' => FALSE,
				'facebook_channel_url' => $this->CI->facebook->channel_url,
				'facebook_user' => $facebook_user,
				'script' => $scripts,
				'style' => $style,
			);
		} else {
			$this->CI->load->library('notification_lib');
			$user = $this->get_user();
			$user_companies = $this->get_user_companies();
			$common = array(
				'node_base_url' => $this->CI->config->item('node_base_url'),
				'facebook_app_id' => $this->CI->config->item('facebook_app_id'),
				'user' => $user,
				'image_url' => base_url().'assets/images/',
				'facebook_user' => $facebook_user,
				'script' => $scripts,
				'style' => $style,
				'user_can_create_company' => !$user_companies ? TRUE : $this->check_package_by_user_id_and_mode($this->CI->session->userdata('user_id'), 'company'), //Check user can create company
				'notification_amount' => $this->CI->notification_lib->count_unread($user['user_id']),
				'all_notification_link' => base_url().'temp',
				'facebook_channel_url' => base_url().'assets/channel/fb.php',
				'facebook_default_scope' => $this->CI->config->item('facebook_admin_scope')
			);
		}
		
		$data = array_merge_recursive($common,$data);
		$data = array_unique_recursive($data);
		
		//Override, because array_merge_recursive cause merge(user_id, user_id) -> array(user_id,user_id)
		if(isset($user['user_id'])){
			$data['vars']['user_id'] = $user['user_id'];
		}
		
		// $this->login(); Don't autologin
		if($this->CI->session->userdata('logged_in') == TRUE){
			$data['user_companies'] = isset($user_companies) ? $user_companies : NULL;
			if($this->CI->input->get('logged_in') == TRUE){
				if($data['user_companies']){
					$data['vars']['popup_name'] = 'bar/select_company';
				}
			}
		}
		
		if( isset($data['vars']['popup_name']) && !isset($data['vars']['closeEnable']) ) $data['vars']['closeEnable'] = true;
		
		$data['bar_view_bootstrap'] = TRUE; //Get new bar view

		if(isset($data['use_static_fb_root']) && $data['use_static_fb_root']) {
			$data['facebook_app_scope'] = $this->CI->config->item('facebook_player_scope');
		}

		return $this->CI->load->view('common/header', $data, TRUE);
	}
	
	/**
	 * Get header view, predefine $user and $user_companies in views
	 * @return $header
	 * @author Manassarn M., Prachya P.
	 */
	function get_header_lightbox($data = array()){
		if($this->CI->session->userdata('logged_in') == TRUE){
			$data['user'] = $this->get_user();
			$data['user_companies'] = $this->get_user_companies();
		}
		$data['image_url'] = base_url().'assets/images/';
		return $this->CI->load->view('common/header_lightbox', $data, TRUE);
	}
	
	/**
	 * Get footer view
	 * @return $footer
	 * @author Manassarn M.
	 * @todo add more elements
	 */
	function get_footer($data = array()){
		if($this->CI->session->userdata('logged_in') == TRUE){
			return $this->CI->load->view('common/footer', array() , TRUE);
		} else {
			return $this->CI->load->view('common/footer', array() , TRUE);
		}
	}
	
	/**
	 * Get footer view
	 * @return $footer
	 * @author Manassarn M.,Prachya P.
	 * @todo add more elements
	 */
	function get_footer_lightbox($data = array()){
		if($this->CI->session->userdata('logged_in') == TRUE){
			return $this->CI->load->view('common/footer_lightbox', array() , TRUE);
		} else {
			return $this->CI->load->view('common/footer_lightbox', array() , TRUE);
		}
	}
	
	/**
	 * Login into SocialHappen
	 * Stores user session with facebook id
	 * @param $redirect_url
	 * @author Manassarn M.
	 */
	function login($redirect_url = NULL){
		if($user = $this->CI->facebook->getUser()){
			$user_facebook_id = $user['id'];
			$this->CI->load->model('user_model','users');
			$user_id = $this->CI->users->get_user_id_by_user_facebook_id($user_facebook_id);
			if($user_id){
				if(!$this->CI->session->userdata('logged_in')){ //@TODO : Problem is it will separate logging in through platform & through facebook
					$this->CI->load->library('audit_lib');
					$action_id = $this->CI->socialhappen->get_k('audit_action','User Login');
					// $this->CI->audit_lib->add_audit(
					// 	0,
					// 	$user_id,
					// 	$action_id,
					// 	'', 
					// 	'',
					// 	array(
					// 		'app_install_id' => 0,
					// 		'user_id' => $user_id
					// 	)
					// );
					$this->CI->audit_lib->audit_add(array(
						'user_id' => $user_id,
						'action_id' => $action_id,
						'app_id' => 0,
						'app_install_id' => 0,
						'subject' => $user_id,
						'object' => NULL,
						'objecti' => NULL
					));
					
					$this->CI->load->library('achievement_lib');
					$info = array('action_id'=> $action_id, 'app_install_id'=>0);
					$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, 0, $user_id, $info, 1);
				}
				$userdata = array(
					'user_id' => $user_id,
					'user_facebook_id' => $user_facebook_id,
					'logged_in' => TRUE
				);
				$this->CI->session->set_userdata($userdata);
			}
		}
		else { //No facebook

		}

		if($redirect_url){
			redirect($redirect_url);
		}
		return issetor($user_id);
	}
	
	/**
	 * Logout
	 * @param $redirect_url
	 * @author Manassarn M.
	 */
	function logout($redirect_url = NULL){
	    $this->CI->session->sess_destroy();
		if($redirect_url){
			redirect($redirect_url);
		}
	}
	
	/**
	 * Upload and resize image
	 * @param $name
	 * @return $image_url
	 * @author Manassarn M.
	 */
	function upload_image($name = NULL, $resize = TRUE){ 
		$config['upload_path'] = './uploads/images/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '2048';
		$config['max_width']  = '2048';
		$config['max_height']  = '2048';
		$config['encrypt_name'] = TRUE;
		$this->CI->load->library('upload', $config);
		if ($_FILES[$name]['error'] == 0 && $this->CI->upload->do_upload($name)){
			log_message('error','uploaded');
			$image_data = $this->CI->upload->data();
			if($resize) { 
				$this->resize_image($image_data); 
			} else {
				rename($image_data['full_path'],"{$image_data['file_path']}{$image_data['raw_name']}_o{$image_data['file_ext']}");
			}
			return base_url()."uploads/images/{$image_data['raw_name']}_o{$image_data['file_ext']}";
		} else {
			return FALSE; 
		}
	}
	
	/**
	 * Replace image
	 * @param $new_image
	 * @param $old_image
	 * @author Manassarn M.
	 */
	function replace_image($new_image = NULL, $old_image = NULL, $resize = TRUE){
		if($new_image = $this->upload_image($new_image, $resize)){
			if(strpos($old_image, base_url()) === 0){
				$dimensions = array('q','t','s','n','o');
				$old_image = str_replace(base_url(), "./", $old_image); // "./uploads/images/[imagename]_o.[ext]", facebook image will not be removed
				$image_ext = pathinfo($old_image, PATHINFO_EXTENSION);
				$image_name = substr($old_image, 0, strrpos($old_image, "_"));
				foreach($dimensions as $dimension){
					if(file_exists("{$image_name}_{$dimension}.{$image_ext}")) unlink("{$image_name}_{$dimension}.{$image_ext}");
				}
			}
			return $new_image;
		} 
		return FALSE;
	}
	
	/**
	 * Resize image and save as separated files
	 * @param $image_data (see http://codeigniter.com/user_guide/libraries/file_uploading.html)
	 * @author Manassarn M.
	 */
	function resize_image($image_data = NULL, $dimensions = array()){
		$dimensions = array('q' => array(50,50),'t' => array(50,100),'s' => array(100,200),'n' => array(200,400));
		if($image_data) {
			$this->CI->load->library('image_lib'); 
			foreach($dimensions as $dimension_name => $dimension_size){
				$config['image_library'] = 'gd2';
				$config['source_image']	= "uploads/images/{$image_data['file_name']}";
				$config['new_image'] = "uploads/images/{$image_data['raw_name']}_{$dimension_name}{$image_data['file_ext']}";
				$config['maintain_ratio'] = TRUE;
				$config['master_dim'] = 'width';
				$config['width'] = $dimension_size[0];
				$config['height'] = $dimension_size[1];
				
				$this->CI->image_lib->initialize($config); 
				$this->CI->image_lib->resize();
			}
			rename($image_data['full_path'],"{$image_data['file_path']}{$image_data['raw_name']}_o{$image_data['file_ext']}");
		}
	}
	
	/**
	 * Check if is called with xml http request (ajax)
	 * @author Manassarn M.
	 */
	function ajax_check(){
		//file_get_content is allowed (no user agent)
		if( ! isset($_SERVER['HTTP_USER_AGENT']) ){
		
		} else if(!$this->CI->input->is_ajax_request()){
			exit();
		}
	}
	
	/**
	 * (Private) Check if user has company roles that override page roles
	 * @param $user_id
	 * @param $company_id
	 * @param (array) $roles
	 * @param (array) $required_roles
	 */
	function _has_company_roles($user_id = NULL, $company_id = NULL, $roles = array(), $required_roles = array()){
		$this->CI->load->model('user_companies_model','user_companies');
		$company_users = $this->CI->user_companies->get_company_users_by_company_id($company_id);
		foreach($company_users as $company_user){
			if($company_user['user_id'] == $user_id){
				if($company_user['role_all']){
					return TRUE;
				} else {
				//echo 'no role_all.';
					$has_company_roles = TRUE;
					foreach($roles as $role){
						if(in_array($role,$required_roles) && $company_user[$role] == FALSE){
							//echo 'no company role.';
							$has_company_roles = FALSE;
						}
					}						
					return $has_company_roles;
				}
			}
		}
		
	}
	
	/**
	 * Check if user is admin
	 * @param array $data
	 * @author Manassarn M.
	 */
	function check_admin($data = array(), $roles = array()){
		if(!$user_id = $this->CI->session->userdata('user_id')){
			return FALSE;
		}
		
		if ( ! function_exists('in_each_array')){
			function in_each_array($needle_key, $needle_value, $haystack_array = array()){
				foreach($haystack_array as $haystack){
					if(isset($haystack[$needle_key]) && $haystack[$needle_key] == $needle_value){
						return TRUE;
					}
				}
				return FALSE;
			}
		}
		
		if(isset($data['company_id'])){
			$this->CI->load->model('user_companies_model','user_companies');
			$company_users = $this->CI->user_companies->get_company_users_by_company_id($data['company_id']);
			if(!in_each_array('user_id',$user_id,$company_users)) {
				return FALSE;
			}
			if($roles){
				foreach($company_users as $company_user){
					if($company_user['user_id'] == $user_id){
						if($company_user['role_all'] == FALSE){
							foreach($roles as $role){
								if(in_array($role,array('role_company_edit','role_company_delete','role_all_company_pages_edit','role_all_company_pages_delete')) && $company_user[$role] == FALSE){
									return FALSE;
								}
							}						
						}
					}
				}
			}
		}
		if(isset($data['page_id'])){ 
			$this->CI->load->model('page_model','pages');
			$page = $this->CI->pages->get_page_profile_by_page_id($data['page_id']);
			$this->CI->load->model('user_pages_model','user_pages');
			$page_users = $this->CI->user_pages->get_page_users_by_page_id($data['page_id']);
			if(!in_each_array('user_id',$user_id,$page_users)) {
				return FALSE;
			}
			if($roles){
				$required_roles = array('role_all_company_pages_edit','role_all_company_pages_delete');
				if(!$this->_has_company_roles($user_id,$page['company_id'],$roles,$required_roles)){
					foreach($page_users as $page_user){
						if($page_user['user_id'] == $user_id){
							if($page_user['role_all'] == FALSE){
								foreach($roles as $role){
									if(in_array($role,array('role_page_edit','role_page_delete')) && $page_user[$role] == FALSE){
										return FALSE;
									}
								}						
							}
						}
					}
				}
			}
		}
		if(isset($data['app_install_id'])){
			$this->CI->load->model('installed_apps_model','installed_apps');
			$installed_app = $this->CI->installed_apps->get_app_profile_by_app_install_id($data['app_install_id']);
			if($installed_app['page_id'] == 0){ //Standalone app
				$this->CI->load->model('user_companies_model','user_companies');
				$company_users = $this->CI->user_companies->get_company_users_by_company_id($installed_app['company_id']);
				if(!in_each_array('user_id',$user_id,$company_users)) {
					return FALSE;
				}
				if($roles){
					$required_roles = array('role_all_company_apps_edit','role_all_company_apps_delete');
					if(!$this->_has_company_roles($user_id,$installed_app['company_id'],$roles,$required_roles)){
						return FALSE;
					}
				}
			} else {
				$this->CI->load->model('page_model','pages');
				$page = $this->CI->pages->get_page_profile_by_app_install_id($data['app_install_id']);
				$this->CI->load->model('user_pages_model','user_pages');
				$user_pages = $this->CI->user_pages->get_user_pages_by_user_id($user_id);
				if(!in_each_array('page_id',$installed_app['page_id'],$user_pages)) {
					return FALSE;
				}
				if($roles){ //echo 'has roles ';
					$required_roles = array('role_all_company_apps_edit','role_all_company_apps_delete');
					if(!$this->_has_company_roles($user_id,$page['company_id'],$roles,$required_roles)){
						//echo 'no company roles ';
						foreach($user_pages as $user_page){
							if($user_page['user_id'] == $user_id){
								if($user_page['role_all'] == FALSE){
									foreach($roles as $role){
										//echo "role = {$role}";
										if(in_array($role,array('role_app_edit','role_app_delete')) && $user_page[$role] == FALSE){
											//echo 'no role';
											return FALSE;
										}
									}						
								}
							}
						}
					}
				}
			}
		}
		if(isset($data['campaign_id'])){
			$this->CI->load->model('page_model','pages');
			$page = $this->CI->pages->get_page_profile_by_campaign_id($data['campaign_id']);
			$this->CI->load->model('user_pages_model','user_pages');
				$user_pages = $this->CI->user_pages->get_user_pages_by_user_id($user_id);
				$this->CI->load->model('campaign_model','campaigns');
				$campaign = $this->CI->campaigns->get_campaign_profile_by_campaign_id($data['campaign_id']);
				$this->CI->load->model('installed_apps_model','installed_apps');
				$installed_app = $this->CI->installed_apps->get_app_profile_by_app_install_id($campaign['app_install_id']);
				if(!in_each_array('page_id',$installed_app['page_id'],$user_pages)) {
					return FALSE;
				}
			if($roles){
				$required_roles = array('role_all_company_campaigns_edit','role_all_company_campaigns_delete');
				
				if(!$this->_has_company_roles($user_id,$page['company_id'],$roles,$required_roles)){
					foreach($user_pages as $user_page){
						if($user_page['user_id'] == $user_id){
							if($user_page['role_all'] == FALSE){
								foreach($roles as $role){
									if(in_array($role,array('role_campaign_edit','role_campaign_delete')) && $user_page[$role] == FALSE){
										return FALSE;
									}
								}						
							}
						}
					}
				}
			}
		}
		return TRUE;
	}
	
	/**
	 * Check if user is exist
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function check_user($user_id = NULL){
		$this->CI->load->model('user_model','users');
		$user = $this->CI->users->get_user_profile_by_user_id($user_id);
		return count($user) != 0;
	}
	
	/**
	 * Check package
	 * @param $user_id
	 * @param $mode
	 * @author Manassarn M.
	 */
	function check_package_by_user_id_and_mode($user_id = NULL, $mode = NULL){
		$this->CI->load->model("package_users_model","package_users");
		if($mode == "company"){
			return $this->CI->package_users->check_user_package_can_add_company($user_id);
		} else if ($mode == "page"){
			return $this->CI->package_users->check_user_package_can_add_page($user_id);
		} else if ($mode == "user"){
			return $this->CI->package_users->check_user_package_can_add_user($user_id);
		} else return FALSE;
	}
	
	/**
	 * Check package over the limit
	 * @param $user_id
	 * @author Weerapat P.
	 */
	function check_package_over_the_limit_by_user_id($user_id = NULL){
		//Get current package
		$this->CI->load->model('package_users_model','package_users');
		$current_package = $this->CI->package_users->get_package_by_user_id($user_id);
		//Count user companies
		$this->CI->load->model('company_model','companies');
		$user_companies = count($this->CI->companies->get_companies_by_user_id($user_id));
		
		//Count user pages
		$this->CI->load->model('user_pages_model','user_pages');
		$user_pages = count($this->CI->user_pages->get_user_pages_by_user_id($user_id));
		
		//Count members
		$members = $this->CI->package_users->count_user_members_by_user_id($user_id);
		
		$over = false;
		if($user_companies > $current_package['package_max_companies']) $over = true;
		if($user_pages > $current_package['package_max_pages']) $over = true;
		if($members > $current_package['package_max_users']) $over = true;
		
		return $over;
	}
	
	/** 
	 * Get socialhappen bar
	 * @param $data
	 * @author Manassarn M.
	 */
	function get_bar($data = array()){
		$app_install_id = issetor($data['app_install_id']);
		$page_id = issetor($data['page_id']);
		$user_id = issetor($data['user_id']);
		$user_facebook_id = issetor($data['user_facebook_id']);
		$app_mode = FALSE;
		$campaign_id = NULL;
		
		$this->CI->load->model('Installed_apps_model', 'app');
		$app = $this->CI->app->get_app_profile_by_app_install_id($app_install_id);
		if($app && isset($app['page_id'])){
			$app_mode = TRUE;
			$page_id = $app['page_id'];
			$sh_canvas_config = $app['app_config_facebook_canvas_path'] ? TRUE : FALSE;

			$this->CI->load->library('campaign_lib');
			$campaign = $this->CI->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
			if(issetor($campaign['in_campaign'])){
				$campaign_id = $campaign['campaign_id'];
			}

			$this->CI->load->library('homepage_lib');
			if($sh_non_fan_homepage = $this->CI->homepage_lib->get_homepage_for_unliked_users($app_install_id)){
				$sh_non_fan_homepage_content = '<p>'.$sh_non_fan_homepage['message'].'</p><p><img src="'.$sh_non_fan_homepage['image'].'"</img></p>';
			}
		}
		
		$this->CI->load->model('User_model', 'User');
		$this->CI->load->model('user_pages_model','user_pages');
		$this->CI->load->model('page_model','pages');
		$this->CI->load->model('session_model','session_model');
		$page = $this->CI->pages->get_page_profile_by_page_id($page_id);
		$user = $user_id ? $this->CI->User->get_user_profile_by_user_id($user_id) : $this->CI->User->get_user_profile_by_user_facebook_id($user_facebook_id);
		$user_id = issetor($user['user_id'], 0);

		if(!$page['enable_facebook_tab_bar'] && !$user['user_is_developer']) {
			return NULL;
		}

		$menu = array();		
		$menu['left'] = array();
		if($page_id){
			$apps = $this->CI->app->get_installed_apps_by_page_id($page_id);
			$this->CI->load->library('app_url');
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
		
		$is_user_register_to_page = $is_user_register_to_campaign = FALSE;
		$this->CI->load->model('page_user_data_model','page_user_data');
		if($user_id) {
			$is_user_register_to_page = $this->CI->page_user_data->get_page_user_by_user_id_and_page_id($user_id, $page_id);
		
			$this->CI->load->model('user_campaigns_model','campaign_user');
			$is_user_register_to_campaign = $this->CI->campaign_user->is_user_in_campaign($user_id, $campaign_id);
			
			//$this->CI->load->library('notification_lib');
			//$notification_amount = $this->CI->notification_lib->count_unread($user['user_id']);
			//$app_data = array('view'=>'notification', 'return_url' => $app['facebook_tab_url'] );
		}

		$domain_fragments = parse_url(base_url());
		$sh_domain = 'https://'.$domain_fragments['host']; //Use for cross-domain calling

		if($app_mode){
			$facebook_tab_url = $app['facebook_tab_url'];
		} else {
			$facebook_tab_url = $page['facebook_tab_url'];
		}

		$this->CI->load->vars(array(
			'vars' => array(
				'view_as' => '',
				'user_image' => '',
				'page_app_installed_id' => issetor($page['page_app_installed_id'],0),
				'is_user_register_to_page' => $is_user_register_to_page ? 1 : NULL,
				'is_user_register_to_campaign' => $is_user_register_to_campaign ? 1 : NULL,
				'user_id' => issetor($user_id,0),
				//for get started
				'facebook_access_token' => '',
			),
			// 'view_as' => $view_as,
			'node_base_url' => $this->CI->config->item('node_base_url'),
			'page_id' => $page_id,
			'app_install_id' => $app_install_id,
			'facebook_page_id' => $page['facebook_page_id'],
			'app_id' => $app['app_id'],
			'app_secret_key' => $app['app_secret_key'],
			'app_install_secret_key' => $app['app_install_secret_key'],
			'campaign_id' => issetor($campaign_id),
			'base_url' => base_url(),
			'app_mode' => $app_mode,
			'facebook_tab_url' => $facebook_tab_url,
			'sh_domain' => $sh_domain,
			'sh_canvas_config' => issetor($sh_canvas_config) ? 'true' : 'false' ,
			'sh_non_fan_homepage' => issetor($sh_non_fan_homepage),
			'sh_non_fan_homepage_content' => issetor($sh_non_fan_homepage_content,''),

			
		));
		return $this->CI->load->view('api/app_bar_view', array(), TRUE);
	}
	
	/** 
	 * Get app setting template
	 * @param $data
	 * @author Weerapat P.
	 */
	function get_setting_template($data = array()){
		$app_install_id = issetor($data['app_install_id']);
		$page_id = issetor($data['page_id']);
		$user_id = issetor($data['user_id']);
		$user_facebook_id = issetor($data['user_facebook_id']);
		$view = issetor($data['view'], 'app_setting_template');

		$this->CI->load->model('User_model', 'User');
		$this->CI->load->model('user_pages_model','user_pages');
		$this->CI->load->model('page_model','pages');
		$this->CI->load->model('session_model','session_model');
		$page = $this->CI->pages->get_page_profile_by_page_id($page_id);
		$user = $user_id ? $this->CI->User->get_user_profile_by_user_id($user_id) : $this->CI->User->get_user_profile_by_user_facebook_id($user_facebook_id);

		if(!$page['enable_facebook_tab_bar'] && !$user['user_is_developer']) {
			return NULL;
		}
		
		//app
		$this->CI->load->model('Installed_apps_model', 'app');
		$app = $this->CI->app->get_app_profile_by_app_install_id($app_install_id);
		
		//is_admin
		$this->CI->load->model('company_model','companies');
		$company = $this->CI->companies->get_company_profile_by_page_id($page_id);
		$this->CI->load->model('user_companies_model','user_companies');
		$is_admin = $this->CI->user_companies->is_company_admin($user_id, $company['company_id']);
		
		$this->CI->load->vars(array(
			'app' => $app,
			'is_logged_in' => true, //$this->is_logged_in(),
			'is_admin' => true, //$is_admin
		));
		return $this->CI->load->view('api/'.$view, array(), TRUE);
	}
	
	/** 
	 * Get socialhappen tab bar url
	 * @param $app_install_id
	 * @author Weerapat P.
	 */
	function get_tab_url_by_app_install_id($app_install_id = NULL) {
		$this->CI->load->model('installed_apps_model', 'installed_apps');
		$app = $this->CI->installed_apps->get_app_profile_by_app_install_id($app_install_id);
		$this->CI->load->model('page_model', 'pages');
		$page = $this->CI->pages->get_page_profile_by_page_id($app['page_id']);
		return $page['facebook_tab_url'];
	}

	/**
	 * Developer check
	 * @param $user_id : if not specified, will check current user
	 * @author Manassarn M.
	 */
	function is_developer($user_id = NULL){
		if(!$user_id) {
			$user = $this->get_user();
		} else {
			$this->CI->load->model('user_model');
			$user = $this->CI->user_model->get_user_profile_by_user_id($user_id);
		}
		return issetor($user['user_is_developer']) ? TRUE : FALSE;
	}

	/** 
	 * Developer + socialhappen features check
	 * @param array $input [campaign_id,app_install_id,page_id]
	 * @author Manassarn M.
	 */
	function is_developer_or_features_enabled($input = NULL){
		if($this->is_developer()){
			return TRUE;
		}
		if(!$input){
			return FALSE;
		}
		if(isset($input['campaign_id'])){
			$this->CI->load->model('campaign_model');
			if(!$campaign = $this->CI->campaign_model->get_campaign_profile_by_campaign_id($input['campaign_id'])){
				return FALSE;
			}
			$input['app_install_id'] = $campaign['app_install_id'];
		}
		if(isset($input['app_install_id'])){
			$this->CI->load->model('installed_apps_model');
			if(!$app = $this->CI->installed_apps_model->get_app_profile_by_app_install_id($input['app_install_id'])){
				return FALSE;
			}
			$input['page_id'] = $app['page_id'];
		}
		if(isset($input['page_id'])){
			$this->CI->load->model('page_model');
			$page = $this->CI->page_model->get_page_profile_by_page_id($input['page_id']);
			if($page['enable_socialhappen_features']){
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Developer + member limit check
	 * @param array $input [campaign_id,app_install_id,page_id]
	 * @author Manassarn M.
	 */
	function is_developer_or_member_under_limit($input = NULL){
		if($this->is_developer()){
			return TRUE;
		}
		if(!$input){
			return FALSE;
		}
		if(isset($input['campaign_id'])){
			$this->CI->load->model('campaign_model');
			if(!$campaign = $this->CI->campaign_model->get_campaign_profile_by_campaign_id($input['campaign_id'])){
				return FALSE;
			}
			$input['app_install_id'] = $campaign['app_install_id'];
		}
		if(isset($input['app_install_id'])){
			$this->CI->load->model('installed_apps_model');
			if(!$app = $this->CI->installed_apps_model->get_app_profile_by_app_install_id($input['app_install_id'])){
				return FALSE;
			}
			$input['page_id'] = $app['page_id'];
		}
		if(isset($input['page_id'])){
			$this->CI->load->model('page_model');
			$page = $this->CI->page_model->get_page_profile_by_page_id($input['page_id']);
			if($page['page_member_limit'] == 0){
				return TRUE; //Unlimited
			}
			$this->CI->load->model('page_user_data_model');
			$page_members = $this->CI->page_user_data_model->count_page_users_by_page_id($input['page_id']);
			if($page_members <= $page['page_member_limit']){
				return TRUE;
			}
		}
		return FALSE;
	}

	function player_login($user_id = NULL) {
		if($user_id){
			if(!$this->CI->session->userdata('logged_in')){ //@TODO : Problem is it will separate logging in through platform & through facebook
				$this->CI->load->library('audit_lib');
				$action_id = $this->CI->socialhappen->get_k('audit_action','User Login');
				// $this->CI->audit_lib->add_audit(
				// 	0,
				// 	$user_id,
				// 	$action_id,
				// 	'', 
				// 	'',
				// 	array(
				// 		'app_install_id' => 0,
				// 		'user_id' => $user_id
				// 	)
				// );
				$this->CI->audit_lib->audit_add(array(
					'user_id' => $user_id,
					'action_id' => $action_id,
					'app_id' => 0,
					'app_install_id' => 0,
					'subject' => $user_id,
					'object' => NULL,
					'objecti' => NULL
				));
				
				$this->CI->load->library('achievement_lib');
				$info = array('action_id'=> $action_id, 'app_install_id'=>0);
				$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, 0, $user_id, $info, 1);
			}
			$userdata = array(
				'user_id' => $user_id,
				// 'user_facebook_id' => $user_facebook_id,
				'logged_in_as_player' => TRUE,
				'logged_in' => TRUE
			);
			$this->CI->session->set_userdata($userdata);
		}
		return $user_id | FALSE;
	}

	function is_logged_in_as_player() {
		return $this->CI->session->userdata('logged_in_as_player');
	}

	function get_bar_data() {
		$user = $this->get_user();
		$facebook_user = $this->CI->facebook->getUser();

		$data = array(
			'is_user' => !!$user,
			'user' => $user,
			'is_facebook_user' => !!$facebook_user,
			'facebook_user' => $facebook_user,
			'user_can_create_company' => FALSE
		);

		if($user) {
			$this->CI->load->library('notification_lib');
			$user_companies = $this->get_user_companies();
			$data['user_can_create_company'] = 
				!$user_companies ? TRUE : $this->check_package_by_user_id_and_mode(
				$this->CI->session->userdata('user_id'), 'company');
			$data['notification_amount'] = 
				$this->CI->notification_lib->count_unread($user['user_id']);
		}
		return $data;
	}
}