<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cert extends CI_Controller {

	function __construct(){
		parent::__construct();
		if (defined('ENVIRONMENT'))
		{
			if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing' ))
			{
				redirect();
			}
		}
	}

	function index(){
		echo '<a href="'.$this->config->item('node_base_url').'">Node server</a><br />';
		echo '<a href="https://apps.localhost.com">https://apps.localhost.com</a><br />';
		echo '<a href="'.base_url().'">Go Home</a><br />';
		echo '<iframe height="100%" width="45%" src="'.$this->config->item('node_base_url').'"></iframe>';
		echo '<iframe height="100%" width="45%" src="https://apps.localhost.com"></iframe>';
	}
}  

/* End of file cert.php */
/* Location: ./application/controllers/dev/cert.php */