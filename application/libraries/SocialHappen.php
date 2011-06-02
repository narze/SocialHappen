<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SocialHappen Platform Library
 * 
 * This class includes frequently used functions such as sessions & authentications
 * 
 * @author Manassarn M.
 */
class SocialHappen{
	private $CI;
	
	function __construct() {
        $this->CI =& get_instance();
		$this->CI->load->model('user_model','users');
    }
	
	/**
	 * Check if logged in (both facebook and SocialHappen) if not, redirect to specified url
	 * @param $redirect_url
	 * @author Manassarn M. 
	 */
	function check_logged_in($redirect_url = NULL){
		if(!$this->CI->session->userdata('logged_in') == TRUE){
			redirect($redirect_url);
		} else if (!$this->CI->facebook->get_facebook_cookie()){
			redirect($redirect_url);
		} else {
			return TRUE;
		}
	}
    
	/**
	 * Get user from session
	 * @return SocialHappen user if found, otherwise FALSE
	 * @author Manassarn M.
	 */
	function get_user(){
		if($this->CI->session->userdata('logged_in') == TRUE){
			return $this->CI->users->get_user_profile_by_user_id($this->CI->session->userdata('user_id'));
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get user id from session
	 * @return $user_id
	 * @author Manassarn M.
	 */
	function get_user_id(){
		if($this->CI->session->userdata('logged_in') == TRUE){
			return $this->CI->session->userdata('user_id');
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Login into SocialHappen
	 * Stores user session with facebook id
	 * @param $redirect_url
	 * @author Manassarn M.
	 */
	function login($redirect_url = NULL){
		if($facebook_cookie = $this->CI->facebook->get_facebook_cookie()){
			$user_facebook_id = $facebook_cookie['uid'];
			$user_id = $this->CI->users->get_user_id_by_user_facebook_id($user_facebook_id);
			$userdata = array(
								'user_id' => $user_id,
								'user_facebook_id' => $user_facebook_id,
								'logged_in' => TRUE
							);
			$this->CI->session->set_userdata($userdata);
		}
		if($redirect_url){
			redirect($redirect_url);
		}
	}
	
	/**
	 * Logout
	 * @param $redirect_url
	 * @author Manassarn M.
	 */
	function logout($redirect_url = NULL){
	    $this->CI->session->sess_destroy();
		if($redirect_url){
			redirect($redirect_url);
		}
	}
}
