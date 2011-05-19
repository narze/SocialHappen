<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	function index(){
		
	}
	
	/** 
	 * JSON : get installed app list
	 * @param $page_id
	 * @author Manassarn M.
	 * 
	 */
	function json_get_installed_app_list($page_id = NULL){
		$this->load->model('installed_apps_model','installed_apps');
		$apps = $this->installed_apps->get_installed_apps($page_id);
		echo json_encode($apps);
	}
	
}


/* End of file page.php */
/* Location: ./application/controllers/page.php */