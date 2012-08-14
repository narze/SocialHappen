<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bar_ctrl {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function create_company($input = array())
    {
    	$result = array('success' => FALSE);
    	if(!$user_id = issetor($input['user_id'])){
    		$result['error'] = 'No user_id specified';
    	} else {
    		if(!isset($input['company_name']) || !isset($input['company_detail']) || !isset($input['company_image'])){
    			$result['error'] = 'Insufficient data';
    		} else {
			 	$company = array(
						       	'company_name' => $input['company_name'],
						       	'company_detail' => $input['company_detail'],
						       	'company_image' => !$input['company_image'] ? base_url().'assets/images/default/company.png' : $input['company_image'],
						       	'creator_user_id' => $user_id
							);
				$this->CI->load->model('company_model', 'companies');

				if ($company_id = $this->CI->companies->add_company($company))
				{
					$this->CI->load->model('user_companies_model','user_companies');
					$user_company = array(
							'user_id' => $user_id,
							'company_id' => $company_id,
							'user_role' => 1 //Company Admin
						);
					if($this->CI->user_companies->add_user_company($user_company)){
						$result['success'] = TRUE;
						$result['data'] = array(
							'company_id' => $company_id
						);
					} else {
						$result['error'] = "Error adding user company";
					}
				}
				else
				{
					// log_message('error','company add failed');
					$result['error'] = 'Error adding company';
				}
			}
		}
		return $result;
    }

    function select_company($user_id = NULL){
    	$result = array('success' => FALSE);
    	if(!$user_id){
    		$result['error'] = 'No user_id specified';
    	} else {
	    	$this->CI->load->model('user_companies_model','user_companies');
			$user_companies = $this->CI->user_companies->get_user_companies_by_user_id($user_id);

			if($user_companies)
			{
				$this->CI->load->model('page_model','page');
				$this->CI->load->model('installed_apps_model','installed_app');
				$this->CI->load->model('campaign_model','campaigns');
				foreach($user_companies as &$company)
				{
					$company['page_count'] = $this->CI->page->count_all(array("company_id" => $company['company_id']));
					$company['app_count'] = $this->CI->installed_app->count_all_distinct("app_id",array("company_id" => $company['company_id']));
					$company['campaign_count'] = $this->CI->campaigns->count_campaigns_by_company_id($company['company_id']);
				}
			}

			$result['data'] = array(
				'user_companies' => $user_companies,
				'user_can_create_company' => $this->CI->socialhappen->check_package_by_user_id_and_mode($user_id, 'company')  //Check user can create company
			);
			$result['success'] = TRUE;
		}
		return $result;
    }
}