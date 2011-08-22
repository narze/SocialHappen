<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
	}
	
	/**
	 * Error page
	 * @author Weerapat P.
	 */
	function index($error_num = NULL){
		$title = array(
			403 => 'Forbidden',
			404 => 'Page not Found',
		);
		$data = array(
			'header' => $this -> socialhappen -> get_header( 
				array(
					'title' => $title[$error_num],
					'script' => array(
						'common/functions',
						'common/jquery.form',
						'common/bar',
						'common/fancybox/jquery.fancybox-1.3.4.pack',
					),
					'style' => array(
						'common/platform',
						'common/fancybox/jquery.fancybox-1.3.4',
						'common/main'
					)
				)
			),
			'breadcrumb' => $this -> load -> view('common/breadcrumb', 
				array(
					'breadcrumb' => array( 
						'Error' => NULL
					)
				),
			TRUE),
			'error' => $this -> load -> view('error/'.$error_num, 
				array(

				),
			TRUE),
			'footer' => $this -> socialhappen -> get_footer()
		);
		$this -> parser -> parse('error/error_view', $data);
	}
}

/* End of file error.php */
/* Location: ./application/controllers/error.php */