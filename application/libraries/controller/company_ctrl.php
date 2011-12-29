<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Company_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function main($company_id = NULL){
    	$result = array('success' => FALSE);
    	if(!$company_id){
			$result['error'] = 'Company id not specified';
		} else {
			if(!$this->CI->socialhappen->check_admin(array('company_id' => $company_id),array())){
				$result['error'] = 'User is not admin';
			} else {
				$this->CI-> load -> model('company_model', 'companies');
				$this->CI-> load -> model('package_users_model', 'package_users');
				if(!$company = $this->CI-> companies -> get_company_profile_by_company_id($company_id)){
					$result['error'] = 'Company not found';
				} else {
				
					$user_id = $this->CI->socialhappen->get_user_id();
					$user_have_package = false;
					$is_package_over_the_limit = false;
					$popup_name = '';
					$closeEnable = true;
					
					if($this->CI->package_users->get_package_by_user_id($user_id)) //If user have package
					{
						$user_have_package = true;
						//Noom : change user_id to check to get_user_id() for the time being
						$is_package_over_the_limit = $this->CI->socialhappen->check_package_over_the_limit_by_user_id($user_id);
					}
					
					if($user_have_package == false)
					{
						$popup_name = 'payment/payment_form';
						$closeEnable = false;
					} 
					else if($this->CI->input->get('popup') == 'thanks') //Thanks msg after sign up
					{
						$popup_name = 'home/signup_complete/';
					}
					else if($is_package_over_the_limit)
					{
						$popup_name = 'company/company_package_limited';
					}
					
					$result['data'] = array(
						'company_id' => $company_id,
						'header' => $this->CI-> socialhappen -> get_header( 
							array(
								'title' => $company['company_name'],
								'vars' => array('company_id'=>$company_id,
												'sh_default_fb_app_api_key'=>$this->CI->config->item('facebook_app_id'),
												'user_id'=>$user_id,
												'popup_name'=>$popup_name,
												'closeEnable'=>$closeEnable
												),
								'script' => array(
									'common/functions',
									'common/jquery.form',
									'common/bar',
									'common/shDragging',
									'company/company_dashboard',
									'common/fancybox/jquery.mousewheel-3.0.4.pack',
									'common/fancybox/jquery.fancybox-1.3.4.pack',
									'payment/payment',
									'common/jquery.joyride'
								),
								'style' => array(
									'common/main',
									'common/platform',
									'common/smoothness/jquery-ui-1.8.9.custom',
									'common/fancybox/jquery.fancybox-1.3.4',
									'common/joyride'
								)
							)
						),
						'company_image_and_name' => $this->CI-> load -> view('company/company_image_and_name', 
							array(
								'company' => $company
							),
						TRUE),
						'breadcrumb' => $this->CI-> load -> view('common/breadcrumb', 
							array(
								'breadcrumb' => 
									array( 
										$company['company_name'] => base_url() . "company/{$company['company_id']}"
									),
								'settings_url' => base_url()."settings/company/{$company['company_id']}"
							),
						TRUE),
						'company_profile' => $this->CI-> load -> view('company/company_profile', 
							array('company_profile' => $company),
						TRUE),
						'company_dashboard_tabs' => $this->CI-> load -> view('company/company_dashboard_tabs', 
							array('user_have_package'=>$user_have_package),
						TRUE),
						'company_dashboard_right_panel' => $this->CI-> load -> view('company/company_dashboard_right_panel', 
							array(),
						TRUE),
						'footer' => $this->CI-> socialhappen -> get_footer()
					);
					$result['success'] = TRUE;
				}
			}
		}
		return $result;
    }

	function json_get_pages($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('page_model','page');
		$pages = $this->CI->page->get_company_pages_by_company_id($company_id, $limit, $offset);
		return json_encode($pages);
	}

	function json_get_apps($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('company_apps_model','company_apps');
		$apps = $this->CI->company_apps->get_company_apps_by_company_id($company_id, $limit, $offset);
		return json_encode($apps);
	}

	function json_get_installed_apps($company_id = NULL, $limit = NULL, $offset = NULL){
		$this->CI->load->model('installed_apps_model','installed_apps');
		$apps = $this->CI->installed_apps->get_installed_apps_by_company_id($company_id, $limit, $offset);
		return json_encode($apps);
	}
	
	function json_get_profile($company_id = NULL){
		$this->CI->load->model('company_model','companies');
		$profile = $this->CI->companies->get_company_profile_by_company_id($company_id);
		return json_encode($profile);
	}

}

/* End of file company_ctrl.php */
/* Location: ./application/libraries/controller/company_ctrl.php */