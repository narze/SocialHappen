<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backendv2 extends CI_Controller {

	function __construct(){
		parent::__construct();
		if(!$this->socialhappen->is_developer()){
			redirect('login?next=backendv2');
		}
	}

	function index() {
		$template = array(
      'title' => 'Backend',
      'styles' => array(
        'common/bootstrap.min'
      )
    );

    $this->load->view('backend/main', $template);
	}
}

/* End of file backendv2.php */
/* Location: ./application/controllers/backendv2.php */