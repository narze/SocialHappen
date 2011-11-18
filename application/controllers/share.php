<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Share extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	
	function index(){

	}

	function facebook($app_install_id = NULL){
		$this->load->library('campaign_lib');
		$campaign = $this->campaign_lib->get_current_campaign_by_app_install_id($app_install_id);
		if($campaign === FALSE || $campaign['in_campaign'] === FALSE){ //No campaign, or campaign ended : just share, but don't know what to share :(
			//$post_id = $this->sharebutton_lib->facebook_share($campaign_id);
			echo 'no campaign, cannot post';
		} else if($campaign['in_campaign']){
			$campaign_id = $campaign['campaign_id'];
			$this->load->model('app_component_model','app_component');
			$sharebutton = $this->app_component->get_sharebutton_by_campaign_id($campaign_id);
			$this->load->library('sharebutton_lib');
			if(!$sharebutton || !issetor($sharebutton['facebook_button'])){
				//Share with no criteria, no score
				$post_id = $this->sharebutton_lib->facebook_share();
				echo 'Post w/o campaign';
			} else {
				// Share and give score if not exceed maximum
				$post_id = $this->sharebutton_lib->facebook_share($sharebutton);
				'Post with campaign';
			}
			echo 'Posted on facebook with id : '.$post_id;
		}
	}

	function twitter(){
		
	}
}
/* End of file share.php */
/* Location: ./application/controllers/share.php */