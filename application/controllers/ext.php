<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ext extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function like() {
		$url = $this->input->get('url');
		$id = $this->input->get('id');
		$facebook_app_id = $this->config->item('facebook_app_id');
		$facebook_channel_url = $this->facebook->channel_url;
		$msg = "You may be prompted to login with facebook again.";
		$page_name = $this->input->get('page_name');

		$data = compact('url', 'id', 'facebook_app_id', 'page_name', 'facebook_channel_url', 'msg');
		$this->load->view('ext/like', $data);
	}
}

/* End of file ext.php */
/* Location: ./application/controllers/ext.php */