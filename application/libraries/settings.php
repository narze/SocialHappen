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
}
