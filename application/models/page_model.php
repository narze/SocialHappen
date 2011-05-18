<?php
class Page_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	/* 
	 * Get pages by company
	 * @param $company_id
	 * @author Manassarn M.
	 */
	function get_company_pages($company_id = NULL){
		if(!$company_id) return array();
		return $this->db->get_where('page', array('company_id' => $company_id))->result();
	}
	
}
/* End of file page_model.php */
/* Location: ./application/models/page_model.php */