<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function signup($input = array()){

    	$is_registered = issetor($input['is_registered']);
		$from = issetor($input['from']);
		$facebook_user_id = issetor($input['facebook_user_id']);
		$facebook_user = issetor($input['facebook_user']);

		$result['data'] = array(
			'header' => $this->CI-> socialhappen -> get_header( 
				array(
					'title' => 'Signup',
					'script' => array(
						'common/functions',
						'common/jquery.form',
						'common/bar',
						'common/jstz.min',
						'common/fancybox/jquery.fancybox-1.3.4.pack',
						'home/lightbox',
						'home/signup'
					),
					'style' => array(
						'common/platform',
						'common/main',
						'common/fancybox/jquery.fancybox-1.3.4'
					),
					'user_companies' => FALSE
				)
			),
			'breadcrumb' => $this->CI-> load -> view('common/breadcrumb', 
				array(
					'breadcrumb' => array( 
						'Signup' => NULL
					)
				),
			TRUE),
			'tutorial' => $this->CI-> load -> view('home/tutorial', 
				array(
					
				),
			TRUE),
			'footer' => $this->CI-> socialhappen -> get_footer(),
			'is_registered' => $is_registered,
			'from' => $from,
			'user_profile_picture'=>$this->CI->facebook->get_profile_picture($facebook_user_id),
			'facebook_user'=>$facebook_user
		);
		$result['success'] = TRUE;
		
		if(!$is_registered) 
		{
			$result['data']['signup_form'] = $this->CI-> load -> view('home/signup_form', NULL, TRUE);
		}
		return $result;
	}

	function signup_form($input = array()){
		$user_timezone = issetor($input['user_timezone']);
		$first_name = issetor($input['first_name']);
		$last_name = issetor($input['last_name']);
		$email = issetor($input['email']);
		$facebook_user_id = issetor($input['facebook_user_id']);
		$package_id = issetor($input['package_id']);
		$company_name = issetor($input['company_name']);
		$company_detail = issetor($input['company_detail']);
		$company_image = issetor($input['company_image']);
		$facebook_access_token = issetor($input['facebook_access_token']);
		$result = array('success'=>FALSE);

		$this->CI->load->library('timezone_lib');
			if(!$minute_offset = $this->CI->timezone_lib->get_minute_offset_from_timezone($user_timezone)){
				$minute_offset = 0;
			}

			$user = array(
					       	'user_first_name' => $first_name,
					       	'user_last_name' => $last_name,
					       	'user_email' => $email,
					       	'user_image' => $this->CI->facebook->get_profile_picture($facebook_user_id),
					       	'user_facebook_id' => $facebook_user_id,
					       	'user_timezone_offset' => $minute_offset,
					       	'user_facebook_access_token' => $facebook_access_token
						);
			
			$this->CI->load->model('user_model', 'users');
			$user_id = $this->CI->users->add_user($user);
			
			$company = array(
					       	'company_name' => $company_name,
					       	'company_detail' => $company_detail,
					       	'company_image' => $company_image ? $company_image : $this->CI->socialhappen->get_default_url('company_image'),
							'creator_user_id' => $user_id
						);
			$this->CI->load->model('company_model','company');
			$company_id = $this->CI->company->add_company($company);
			
			if (!($user_id && $company_id))
			{	
				$result['error'] = 'company,user add failed';
			}
			else
			{
				$this->CI->load->model('user_companies_model','user_companies');
				$add_user_company = $this->CI->user_companies->add_user_company(array(
				  'user_id' => $user_id,
				  'company_id' => $company_id,
				  'user_role' => 1 //Company admin
				));
        
				if(!$add_user_company){
					$result['error'] = 'company_user add failed';
				}else{
					$this->CI->load->library('audit_lib');
					$action_id = $this->CI->socialhappen->get_k('audit_action','User Register SocialHappen');
					// $this->CI->audit_lib->add_audit(
					// 	0,
					// 	$user_id,
					// 	$action_id,
					// 	'', 
					// 	'',
					// 	array(
					// 		'app_install_id' => 0,
					// 		'company_id' => $company_id,
					// 		'user_id' => $user_id
					// 	)
					// );
					$this->CI->audit_lib->audit_add(array(
						'user_id' => $user_id,
						'action_id' => $action_id,
						'app_id' => 0,
						'app_install_id' => 0,
						'company_id' => $company_id,
						'subject' => $user_id,
						'object' => NULL,
						'objecti' => NULL
					));
					
					$this->CI->load->library('achievement_lib');
					$info = array('action_id'=> $action_id, 'app_install_id'=>0);
					$stat_increment_result = $this->CI->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);

					$redirect_url = base_url().'home/package?payment=true';
					if($package_id) 
					{
						$redirect_url .= '&package_id='. $package_id;
					}

					$result['data'] = array(
						'user_id' => $user_id,
						'company_id' => $company_id,
						'redirect_url' => $redirect_url
					);
					$result['success'] = TRUE;
				}
			}
		return $result;
	}

	function apps(){
		
	}
}