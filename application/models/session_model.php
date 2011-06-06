<?php
class Session_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get session id
	 * @param $user_id
	 * @author Manassarn M.
	 */
	function get_session_id_by_user_id($user_id = NULL){
		$result = $this->db->get_where('sessions', array('user_id'=>$user_id))->result_array();
		return issetor($result[0]['session_id']);
	}	
	
	/**
	 * Get user_id
	 * @param $session_id
	 * @author Manassarn M.
	 */
	function get_user_id_by_session_id($session_id = NULL){
		$result = $this->db->get_where('sessions', array('session_id'=>$session_id))->result_array();
		return issetor($result[0]['user_id']);
	}
}
/* End of file session_model.php */
/* Location: ./application/models/session_model.php */