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
	 * Get user companies from database
	 * @return $user_companies
	 * @author Manassarn M.
	 */
	function get_user_companies(){
		if($this->CI->session->userdata('logged_in') == TRUE){
			$this->CI->load->model('user_companies_model','user_companies');
			return $this->CI->user_companies->get_user_companies_by_user_id($this->CI->session->userdata('user_id'));
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get header view, predefine $user and $user_companies in views
	 * @return $header
	 * @author Manassarn M.
	 */
	function get_header($data = array()){
		if($this->CI->session->userdata('logged_in') == TRUE){
			$data['user'] = $this->get_user();
			$data['user_companies'] = $this->get_user_companies();
		}
		$data['image_url'] = base_url().'assets/images/';
		return $this->CI->load->view('common/header', $data, TRUE);
	}
	
	/**
	 * Get header view, predefine $user and $user_companies in views
	 * @return $header
	 * @author Manassarn M., Prachya P.
	 */
	function get_header_lightbox($data = array()){
		if($this->CI->session->userdata('logged_in') == TRUE){
			$data['user'] = $this->get_user();
			$data['user_companies'] = $this->get_user_companies();
		}
		$data['image_url'] = base_url().'assets/images/';
		return $this->CI->load->view('common/header_lightbox', $data, TRUE);
	}
	
	/**
	 * Get footer view
	 * @return $footer
	 * @author Manassarn M.
	 * @todo add more elements
	 */
	function get_footer($data = array()){
		if($this->CI->session->userdata('logged_in') == TRUE){
			return $this->CI->load->view('common/footer', array() , TRUE);
		} else {
			return $this->CI->load->view('common/footer', array() , TRUE);
		}
	}
	
	/**
	 * Get footer view
	 * @return $footer
	 * @author Manassarn M.,Prachya P.
	 * @todo add more elements
	 */
	function get_footer_lightbox($data = array()){
		if($this->CI->session->userdata('logged_in') == TRUE){
			return $this->CI->load->view('common/footer_lightbox', array() , TRUE);
		} else {
			return $this->CI->load->view('common/footer_lightbox', array() , TRUE);
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
			if($user_id){
				$userdata = array(
					'user_id' => $user_id,
					'user_facebook_id' => $user_facebook_id,
					'logged_in' => TRUE
				);
				$this->CI->session->set_userdata($userdata);
			}
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
	
	/**
	 * Upload and resize image
	 * @param $name
	 * @return $image_url
	 * @author Manassarn M.
	 */
	function upload_image($name = NULL){
		$config['upload_path'] = './uploads/images/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$config['encrypt_name'] = TRUE;

		$this->CI->load->library('upload', $config);
		if ($this->CI->upload->do_upload($name)){
			$image_data = $this->CI->upload->data();
			$this->resize_image($image_data);
			return base_url()."uploads/images/{$image_data['raw_name']}_o{$image_data['file_ext']}";
		} return NULL;
	}
	
	/**
	 * Resize image and save as separated files
	 * @param $image_data (see http://codeigniter.com/user_guide/libraries/file_uploading.html)
	 * @author Manassarn M.
	 */
	function resize_image($image_data = NULL, $dimensions = array()){
		$dimensions = array('q' => array(50,50),'t' => array(50,100),'s' => array(100,200),'n' => array(200,400));
		if($image_data) {
			$this->CI->load->library('image_lib'); 
			foreach($dimensions as $dimension_name => $dimension_size){
				$config['image_library'] = 'gd2';
				$config['source_image']	= "uploads/images/{$image_data['file_name']}";
				$config['new_image'] = "uploads/images/{$image_data['raw_name']}_{$dimension_name}{$image_data['file_ext']}";
				$config['maintain_ratio'] = TRUE;
				$config['master_dim'] = 'width';
				$config['width'] = $dimension_size[0];
				$config['height'] = $dimension_size[1];
				
				$this->CI->image_lib->initialize($config); 
				$this->CI->image_lib->resize();
			}
			rename($image_data['full_path'],"{$image_data['file_path']}{$image_data['raw_name']}_o{$image_data['file_ext']}");
		}
	}
	
	/**
	 * Check if is called with xml http request (ajax)
	 * @author Manassarn M.
	 */
	function ajax_check(){
		if( empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){
			exit();
		}
	}
}
