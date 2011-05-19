<?php
class Installed_apps_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	/* 
	 * Get installed apps
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_installed_apps($page_id = NULL){
		if(!$page_id) return array();
		return $this->db->get_where('installed_apps', array('page_id' => $page_id))->result();
	}
	
}
/* End of file page_model.php */
/* Location: ./application/models/page_model.php */