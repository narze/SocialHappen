<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Session class
 * Extend CI's session class
 * @author Manassarn M.
 */
class MY_Session extends CI_Session {
	
	private $test_database_group = 'local_unit_test';
	
	/**
	 * Update $user_id
	 * @author Manassarn M.
	 */
	private function update_user_id(){
		if ($this->sess_use_database && isset($this->userdata['user_id'])){
			$this->CI->db->where('session_id', $this->userdata['session_id']);
			$this->CI->db->update($this->sess_table_name, array('user_id' => $this->userdata['user_id']));
		}
	}
	
	/**
	 * Write the session data, then update $user_id
	 * @author Manassarn M.
	 */
	function sess_write(){
		parent::sess_write();
		$this->update_user_id();
	}
	
	/**
	 * Create a new session, then update $user_id
	 * @author Manassarn M.
	 */
	function sess_create(){
		if($this->CI->uri->segment(1) === 'test' && $this->CI->db->database !== $this->test_database_group){
			$this->CI->db = $this->CI->load->database($this->test_database_group, TRUE);
		}
		parent::sess_create();
		$this->update_user_id();
	}
	
	/**
	 * Update an existing session, then update $user_id
	 * @author Manassarn M.
	 */
	function sess_update(){
		parent::sess_update();
		$this->update_user_id();
	}

	function sess_read(){
		if($this->CI->uri->segment(1) === 'test' && $this->CI->db->database !== $this->test_database_group){
			$this->CI->db = $this->CI->load->database($this->test_database_group, TRUE);
		}
		return parent::sess_read();
	}
}
/* End of file MY_Session.php */
/* Location: ./application/libraries/MY_Session.php */