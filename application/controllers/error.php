<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends CI_Controller {

	function __construct(){
		parent::__construct();
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
		if(!array_key_exists($error_num, $title)){
			redirect('error/404');
		}
		return $this->socialhappen->error_page($error_num . ' : ' . $title[$error_num]);
	}
}

/* End of file error.php */
/* Location: ./application/controllers/error.php */