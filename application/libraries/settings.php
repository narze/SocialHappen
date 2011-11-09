<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Settings Library
 * 
 * @author Manassarn M.
 */
class Settings{
	private $CI;
	
	function __construct() {
        $this->CI =& get_instance();
    }
	
	/**
	 * Get settings view
	 * Use for old settings controller only (not included page features and app components setting)
	 * @param $setting_name (account, company pages, company, page, package, reference)
	 * @param $param_id (corresponding id for $setting_name eg. user_id, company_id, page_id)
	 * @parem (optional) $company_id If set, the page will have 'go back' link
	 * @author Manassarn M.
	 */
	function view_settings($setting_name = NULL, $param_id = NULL, $company_id = NULL){
		
		$setting_names_and_ids = array('account'=>'user_id','company_pages' => 'company_id', 'company'=>'company_id','page'=>'page_id','package'=>'user_id','reference'=>'user_id');
		
			if(!$setting_name || !array_key_exists($setting_name, $setting_names_and_ids)){
				redirect("settings/account/".$this->CI->socialhappen->get_user_id());
			}
			$user = $this->CI->socialhappen->get_user();
			if($user_companies = $this->CI->socialhappen->get_user_companies()){
				$this->CI->load->model('page_model','pages');
				$company_pages = array();
				foreach ($user_companies as $user_company){
					$company_pages[$user_company['company_id']] = $this->CI->pages->get_company_pages_by_company_id($user_company['company_id']);
				}
			}
			
			$this->CI->load->model('package_users_model','package_users');
			$user_current_package = $this->CI->package_users->get_package_by_user_id($user['user_id']);
			$user_current_package_id = isset($user_current_package['package_id']) ? $user_current_package['package_id'] : 0;
			
			$this->CI-> load -> model('company_model', 'companies');
			$company = $this->CI-> companies -> get_company_profile_by_company_id($company_id);
			$data = array(
				'company_id' => $company_id,
				'header' => $this->CI-> socialhappen -> get_header( 
					array(
						'title' => 'Settings',
						'vars' => array('company_id'=>$company_id,
										'setting_name' => $setting_name,
										'param_id' => $param_id),
						'script' => array(
							'common/functions',
							'common/jquery.form',
							'common/bar',
							'settings/main_a',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'go_back' => $this->CI-> load -> view('settings/go_back', 
					array(
						'company' => $company
					),
				TRUE),
				'company_image_and_name' => $this->CI-> load -> view('company/company_image_and_name', 
					array(
						'company' => $company
					),
				TRUE),
				'breadcrumb' => $this->CI-> load -> view('common/breadcrumb', 
					array('breadcrumb' => 
						array( 
							'Settings' => base_url() . "settings"
							)
						)
					,
				TRUE),
				'sidebar' => $this->CI-> load -> view('settings/sidebar_a', 
					array(
						'company_pages' => issetor($company_pages),
						'user_current_package_id' => $user_current_package_id
					),
				TRUE),
				'main' => $this->CI-> load -> view("settings/main_a", 
					array(
						
					),
				TRUE),
				'footer' => $this->CI-> socialhappen -> get_footer()
				);
			$this->CI-> parser -> parse('settings/settings_view_a', $data);
		
	}

	/**
	 * Get page_app settings view
	 *
	 * @author Manassarn M.
	 */
	function view_page_app_settings($page_id = NULL, $config_name = NULL, $app_install_id = NULL){
		if($page_id){
			// if($this->CI->input->get('p')){
			// 	$page_id = $this->CI->input->get('p');
			// }
			// $config_name = $this->CI->input->get('c');
			// $app_install_id = $this->CI->input->get('id');
			$config_names_and_ids = array('signup_fields','badges','app');
		
			if(!in_array($config_name, $config_names_and_ids)){
				redirect("settings/page_apps/{$page_id}");
			}
			
			$this->CI->load->model('page_model','page');
			$this->CI->load->model('company_model','company');
			$this->CI->load->model('installed_apps_model','installed_apps');
			$page = $this->CI->page->get_page_profile_by_page_id($page_id);
			$company = $this->CI-> company -> get_company_profile_by_company_id($page['company_id']);
			$user = $this->CI->socialhappen->get_user();
			$this->CI->load->vars(array(
				'page' => $page,
				'company' => $company,
				'user' => $user,
				'page_apps' => $this->CI->installed_apps->get_installed_apps_by_page_id($page_id)
			));
			$data = array(
				'header' => $this->CI-> socialhappen -> get_header( 
					array(
						'title' => 'App settings',
						'vars' => array('page_id'=>$page_id,
										'config_name' => $config_name,
										'app_install_id' => $app_install_id),
						'script' => array(
							'common/functions',
							'common/jquery.form',
							'common/bar',
							'settings/main_page_app_settings',
							'common/fancybox/jquery.fancybox-1.3.4.pack'
						),
						'style' => array(
							'common/main',
							'common/platform',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'go_back' => $this->CI-> load -> view('settings/go_back', NULL, TRUE),
				'company_image_and_name' => $this->CI-> load -> view('company/company_image_and_name', NULL, TRUE),
				'breadcrumb' => $this->CI-> load -> view('common/breadcrumb', 
					array('breadcrumb' => 
						array( 
							$page['page_name'] => base_url() . "page/{$page['page_id']}",
							'Config' => NULL
							)
						)
					,
				TRUE),
				'sidebar' => $this->CI-> load -> view('settings/page_apps/sidebar', NULL, TRUE),
				'main' => $this->CI-> load -> view("settings/page_apps/main", NULL, TRUE),
				'footer' => $this->CI-> socialhappen -> get_footer()
				);
			$this->CI-> parser -> parse('settings/page_apps/page_apps_view', $data);
		}
	}
}
