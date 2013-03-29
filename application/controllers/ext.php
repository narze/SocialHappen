<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ext extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function like() {
		$url = $this->input->get('url');
		$facebook_app_id = $this->config->item('facebook_app_id');
		$facebook_channel_url = $this->facebook->channel_url;

		$data = compact('url', 'facebook_app_id', 'facebook_channel_url');
		$this->load->view('ext/like', $data);
	}
}

/* End of file ext.php */
/* Location: ./application/controllers/ext.php */