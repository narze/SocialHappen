<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
//original code from http://kentislearningcodeigniter.com/facebook_connect
//phnx : 16-02-2011
//Edit to match facebook PHP SDK V3.1.1 : Manassarn M.
class Facebook {

	public $channel_url;
	public $facebook_access_token = NULL;
	
	function __construct() {
		$this -> CI = &get_instance();
		$this->CI->load->library('fb_library/FB_library',
							array(
							  'appId'  => $this->CI->config->item('facebook_app_id'),
							  'secret' => $this->CI->config->item('facebook_api_secret')
							),
							'FB');
		$this->FB = $this->CI->FB;
		$this->channel_url = base_url().'assets/channel/fb.php';

		if(issetor($_GET['facebook_access_token'])){
			$this->FB = $this->FB->setAccessToken($_GET['facebook_access_token']);
			$this->facebook_access_token = $_GET['facebook_access_token'];
		}
	}

	// Deprecated
	// function get_facebook_cookie() {
	// 	if(($access_token = $this->FB->getAccessToken()) && ($facebook_uid = $this->FB->getUser())){
	// 		$DEPRECATED_facebook_cookie = array(
	// 			'access_token' => $this->FB->getAccessToken(),
	// 			'uid' => $this->FB->getUser()
	// 		);
	// 		return $DEPRECATED_facebook_cookie;
	// 	} else {
	// 		return FALSE;
	// 	}
	// }

	function getUser() {
		if ($this->facebook_access_token){ //Make new session with access_token if specified
			$facebook_result_array = $this->FB->api('/me');
			if(!isset($facebook_result_array['error']) && isset($facebook_result_array['id'])){
				$this -> CI -> session -> set_userdata(array('facebook_user' => base64_encode(json_encode($facebook_result_array))));

				return $facebook_result_array;
			} else {
				return FALSE;
			}
		} else {
			if ($facebook_user = $this -> CI -> session -> userdata('facebook_user')) { //1.Check session
				$facebook_user = json_decode(base64_decode($facebook_user), TRUE);
				if($this->FB->getUser() == $facebook_user['id']){
					return $facebook_user;
				}
			}
			$facebook_result_array = $this->FB->api('/me');
			if(!isset($facebook_result_array['error']) && isset($facebook_result_array['id'])){
				$facebook_result = json_encode($facebook_result_array);
				// $facebook_result = '{"id":"755758746","name":"Metwara Narksook","first_name":"Metwara","last_name":"Narksook","link":"http:\/\/www.facebook.com\/hybridknight","username":"hybridknight","bio":"127.0.0.1\r\n\r\nComputer Engineering Student, \r\nChulalongkorn University","gender":"male","email":"book2k\u0040hotmail.com","timezone":7,"locale":"en_US","verified":true,"updated_time":"2011-08-04T14:13:34+0000"}';
				// echo "<pre>" . $facebook_result . "</pre>";
				$this -> CI -> session -> set_userdata(array('facebook_user' => base64_encode($facebook_result)));
				return $facebook_result_array;
			} else {
				return FALSE;
			}
		}
	}

	/**
	 * JSON : Get facebook pages owned by the current user
	 * @author Prachya P.
	 */
	function get_user_pages() {
		return $this->FB->api('/me/accounts/page');
	}

	/**
	 * JSON : Get facebook page info
	 * @author Prachya P.
	 * @author Manassarn M.
	 * @param $fb_page_id
	 */
	function get_page_info($fb_page_id = NULL) {
		if(!$fb_page_id){
			return FALSE;
		} else {
			return $this->FB->api($fb_page_id);
		}
	}

	/**
	 * Get facebook profile picture
	 * @author Manassarn M.
	 * @param $facebook_user_id
	 */
	function get_profile_picture($facebook_user_id = NULL) {
		if(!$facebook_user_id){
			return FALSE;
		} else {
			return "https://graph.facebook.com/{$facebook_user_id}/picture";
		}
	}

	/**
	 * Get page access token //You must have manage_pages permission
	 * @param $facebook_page_id
	 * @author Manassarn M.
	 */
	function get_page_access_token_by_facebook_page_id($facebook_page_id = NULL){
		if(!$facebook_page_id) {
			return FALSE;
		}
		$data = $this->FB->api($facebook_page_id, 'GET', array('fields' => 'access_token'));
		if(isset($data['access_token'])){
			return $data['access_token'];
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get page tabs
	 * @param $facebook_page_id
	 * @author Manassarn M.
	 */
	function get_page_tabs_by_facebook_page_id($facebook_page_id = NULL){
		if(!$facebook_page_id) {
			return FALSE;
		}
		$facebook_page_access_token = $this->get_page_access_token_by_facebook_page_id($facebook_page_id);
		$tabs = $this->FB->api($facebook_page_id.'/tabs', 'GET', array('access_token' => $facebook_page_access_token));
		if(isset($tabs['data'])){
			return $tabs['data'];
		}
		return array();
	}
	
	/**
	 * Check if app is installed in facebook page
	 * @param $facebook_app_id
	 * @param $facebook_page_id
	 * @author Manassarn M.
	 */
	function is_facebook_app_installed_in_facebook_page($facebook_app_id = NULL, $facebook_page_id = NULL){
		$page_tabs = $this->get_page_tabs_by_facebook_page_id($facebook_page_id);
		if(is_array($page_tabs)){
			foreach($page_tabs as $tab){
				if(isset($tab['application']['id']) && $tab['application']['id'] == $facebook_app_id){
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * Install facebook app to page tab
	 * @param $facebook_app_id
	 * @param $facebook_page_id
	 */
	function install_facebook_app_to_facebook_page_tab($facebook_app_id = NULL, $facebook_page_id = NULL){
		if(!$page_access_token = $this->get_page_access_token_by_facebook_page_id($facebook_page_id)){
			return FALSE;
		}
		$post = array(
			'app_id' => $facebook_app_id, 
			'access_token' => $page_access_token
		);
		return $this->FB->api($facebook_page_id.'/tabs', 'POST', $post);
	}
	
	/**
	 * Get facebook tab url
	 * @param $facebook_app_id
	 * @param $facebook_page_id
	 * @author Manassarn M.
	 */
	function get_facebook_tab_url($facebook_app_id = NULL, $facebook_page_id = NULL){
		$page_tabs = $this->get_page_tabs_by_facebook_page_id($facebook_page_id);
		if(is_array($page_tabs)){
			foreach($page_tabs as $tab){
				if(isset($tab['application']['id']) && $tab['application']['id'] == $facebook_app_id){
					return $tab['link'];
				}
			}
		}
		return FALSE;
	}

	function post_profile($message = NULL, $link = NULL, $name = NULL) {
		$post = array(
			'message' => $message, 
			// 'picture' => $settings['share_fb_picture'], 
			'link' => $link, 
			'name' => $name ? $name : $link, 
			// 'caption' => 'share_fb_caption', 
			// 'description' => 'share_fb_description'
		);

		$result = $this->FB->api('me/feed', 'POST', $post);
		return $result;
	}
}
