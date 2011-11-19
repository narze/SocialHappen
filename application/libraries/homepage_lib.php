<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Homepage_lib {

	private $CI;

	function __construct() {
        $this->CI =& get_instance();
    }

    function view_homepage_for_unliked_users($app_install_id = NULL){
        $this->CI->load->library('campaign_lib');
        $campaign = $this->CI->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
        if($campaign['in_campaign']){
            $campaign_id = $campaign['campaign_id'];
            $this->CI->load->model('app_component_model','app_component');
            $homepage = $this->CI->app_component->get_homepage_by_campaign_id($campaign_id);
            if($homepage && issetor($homepage['enable']) === TRUE && arenotempty($homepage, array('message','image'))){
                var_dump($homepage);
                //return view for non-fan homepage
            } else {
                //No homepage or homepage[enable] is false, do nothing
            }
        } else {
            //Campaign Ended 
            //$campaign_end_message = $campaign['campaign_end_message'];
        }
    }
}
	