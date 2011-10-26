<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
//original code from http://kentislearningcodeigniter.com/facebook_connect
//phnx : 16-02-2011
class Facebook {

	protected $page_access_token;
	
	function __construct() {
		$this -> _ci = &get_instance();
	}

	function authentication($redirect = '') {
		//echo implode('+',explode('/',$redirect))."       ";
		if ($this -> getUser() == null)
			redirect(('connect/redirect/') . implode('+', explode('/', $redirect)));
	}

	function is_authentication() {
		if ($this -> getUser() == null)
			return false;
		return true;
	}

	function get_facebook_cookie() {
		$app_id = $this -> _ci -> config -> item('facebook_app_id');
		$application_secret = $this -> _ci -> config -> item('facebook_api_secret');

		if (isset($_COOKIE['fbs_' . $app_id])) {

			$args = array();
			parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
			ksort($args);
			$payload = '';
			foreach ($args as $key => $value) {
				if ($key != 'sig') {
					$payload .= $key . '=' . $value;
				}
			}
			if (md5($payload . $application_secret) != $args['sig']) {
				return null;
			}
			return $args;
		} else {
			return null;
		}
	}

	function getUser($access_token = NULL) {
		if ($cookie = $this -> get_facebook_cookie()) { //2.Check facebook cookie
			if ($facebook_user = $this -> _ci -> session -> userdata('facebook_user')) { //1.Check session
				$facebook_user = json_decode(base64_decode($facebook_user), TRUE);
				if($cookie['uid'] == $facebook_user['id']){
					return $facebook_user;
				}
			}
			$facebook_result = file_get_contents('https://graph.facebook.com/me?access_token=' . $cookie['access_token']);
			// $facebook_result = '{"id":"755758746","name":"Metwara Narksook","first_name":"Metwara","last_name":"Narksook","link":"http:\/\/www.facebook.com\/hybridknight","username":"hybridknight","bio":"127.0.0.1\r\n\r\nComputer Engineering Student, \r\nChulalongkorn University","gender":"male","email":"book2k\u0040hotmail.com","timezone":7,"locale":"en_US","verified":true,"updated_time":"2011-08-04T14:13:34+0000"}';
			// echo "<pre>" . $facebook_result . "</pre>";
			$this -> _ci -> session -> set_userdata(array('facebook_user' => base64_encode($facebook_result)));
			return json_decode($facebook_result, true);
			
		} else if ($access_token){ //3.Make new session with access_token if specified
			$facebook_result = file_get_contents('https://graph.facebook.com/me?access_token=' . $access_token);
			$facebook_result_array = json_decode($facebook_result, TRUE);
			if(!isset($facebook_result_array['error']) && isset($facebook_result_array['id'])){
				$this -> _ci -> session -> set_userdata(array('facebook_user' => base64_encode($facebook_result)));
				return $facebook_result_array;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	function getFriendIds($include_self = TRUE) {
		$cookie = $this -> get_facebook_cookie();
		$friends = @json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token=' . $cookie['access_token']), true);
		$friend_ids = array();
		foreach ($friends['data'] as $friend) {
			$friend_ids[] = $friend['id'];
		}
		if ($include_self == TRUE) {
			$friend_ids[] = $cookie['uid'];
		}

		return $friend_ids;
	}

	function getFriends($include_self = TRUE) {
		$cookie = $this -> get_facebook_cookie();
		$friends = @json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token=' . $cookie['access_token']), true);

		if ($include_self == TRUE) {
			$friends['data'][] = array('name' => 'You', 'id' => $cookie['uid']);
		}

		return $friends['data'];
	}

	function getFriendArray($include_self = TRUE) {
		$cookie = $this -> get_facebook_cookie();
		$friendlist = @json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token=' . $cookie['access_token']), true);
		$friends = array();
		foreach ($friendlist['data'] as $friend) {
			$friends[$friend['id']] = $friend['name'];
		}
		if ($include_self == TRUE) {
			$friends[$cookie['uid']] = 'You';
		}

		return $friends;
	}

	//phnx's modification
	function postWall($message = null) {

		$this -> load -> library('curl');
		$cookie = $this -> get_facebook_cookie();

		$access_token_string = 'access_token=' . $cookie['access_token'];

		$url = "https://graph.facebook.com/feed";
		$this -> curl -> create($url);

		$this -> load -> database();
		$this -> load -> library('settings');
		$settings = $this -> settings -> get_settings();
		$post = array('access_token' => $cookie['access_token'], 'message' => $settings['share_fb_message'], 'picture' => $settings['share_fb_picture'], 'link' => $settings['share_fb_url'], 'name' => $settings['share_fb_name'], 'caption' => $settings['share_fb_caption'], 'description' => $settings['share_fb_description']);

		//print_r($post);
		$this -> curl -> post($post);
		$this -> curl -> ssl(FALSE) -> execute();

		//print_r($this->curl->info);
	}

	function getGraph($id = NULL) {
		$this -> _ci -> load -> library('curl');
		$url = "https://graph.facebook.com/" . $id;
		$this -> _ci -> curl -> create($url);
		return json_decode($this -> _ci -> curl -> ssl(FALSE) -> execute());
	}

	/**
	 * JSON : Get facebook pages owned by the current user
	 * @author Prachya P.
	 */
	function get_user_pages() {
		$cookie = $this -> get_facebook_cookie();

		//include page from app
		/*$pages = json_decode(file_get_contents(
		 'https://graph.facebook.com/me/accounts?access_token=' .
		 $cookie['access_token']), true);*/

		//not include page from app
		$pages = json_decode(file_get_contents('https://graph.facebook.com/me/accounts/page?access_token=' . $cookie['access_token']), true);
		return $pages;
	}

	/**
	 * JSON : Get facebook page info
	 * @author Prachya P.
	 * @param $fb_page_id
	 */
	function get_page_info($fb_page_id = NULL) {
		$cookie = $this -> get_facebook_cookie();
		$page = file_get_contents('https://graph.facebook.com/' . $fb_page_id . '/?access_token=' . $cookie['access_token']);
		// $page = '{"id":"135287989899131","name":"SHBeta","picture":"https:\/\/fbcdn-profile-a.akamaihd.net\/static-ak\/rsrc.php\/v1\/y0\/r\/XsEg9L6Ie5_.jpg","link":"http:\/\/www.facebook.com\/pages\/SHBeta\/135287989899131","likes":1,"category":"Community","has_added_app":true,"parking":{"street":0,"lot":0,"valet":0},"hours":{"mon_1_open":0,"mon_1_close":0,"tue_1_open":0,"tue_1_close":0,"wed_1_open":0,"wed_1_close":0,"thu_1_open":0,"thu_1_close":0,"fri_1_open":0,"fri_1_close":0,"sat_1_open":0,"sat_1_close":0,"sun_1_open":0,"sun_1_close":0,"mon_2_open":0,"mon_2_close":0,"tue_2_open":0,"tue_2_close":0,"wed_2_open":0,"wed_2_close":0,"thu_2_open":0,"thu_2_close":0,"fri_2_open":0,"fri_2_close":0,"sat_2_open":0,"sat_2_close":0,"sun_2_open":0,"sun_2_close":0},"payment_options":{"cash_only":0,"visa":0,"amex":0,"mastercard":0,"discover":0},"restaurant_services":{"reserve":0,"walkins":0,"groups":0,"kids":0,"takeout":0,"delivery":0,"catering":0,"waiter":0,"outdoor":0},"restaurant_specialties":{"breakfast":0,"lunch":0,"dinner":0,"coffee":0,"drinks":0},"can_post":true}';
		// echo "<pre>" . $page . "</pre>";
		$page_info = json_decode($page, true);
		return $page_info;
	}

	/**
	 * Get facebook profile picture
	 * @author Manassarn M.
	 * @param $facebook_user_id
	 */
	function get_profile_picture($facebook_user_id = NULL) {
		return "https://graph.facebook.com/{$facebook_user_id}/picture";
	}

	/**
	 * Get page access token
	 * @param $facebook_page_id
	 * @author Manassarn M.
	 */
	function get_page_access_token_by_facebook_page_id($facebook_page_id = NULL){
		if(!$facebook_page_id) {
			return NULL;
		}
		if(isset($this->page_access_token[$facebook_page_id])) {
			return $this->page_access_token[$facebook_page_id];
		}
		if($cookie = $this -> get_facebook_cookie()){
			$data = json_decode(file_get_contents('https://graph.facebook.com/'.$facebook_page_id.'?fields=access_token&access_token=' . $cookie['access_token']),TRUE);
			if(isset($data['access_token'])){
				return $data['access_token'];
			}	
		}
		return NULL;
	}
	
	/**
	 * Get page tabs
	 * @param $facebook_page_id
	 * @author Manassarn M.
	 */
	function get_page_tabs_by_facebook_page_id($facebook_page_id = NULL){
		$facebook_page_access_token = $this->get_page_access_token_by_facebook_page_id($facebook_page_id);
		$tabs = json_decode(file_get_contents('https://graph.facebook.com/'.$facebook_page_id.'/tabs?access_token=' . $facebook_page_access_token),TRUE);
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
		$page_access_token = $this->get_page_access_token_by_facebook_page_id($facebook_page_id);
		$url = "https://graph.facebook.com/{$facebook_page_id}/tabs";
		$post = array('app_id' => $facebook_app_id, 'access_token' => $page_access_token);
		$this -> _ci -> load -> library('curl');
		$this -> _ci -> curl -> create($url);
		$this -> _ci -> curl -> post($post);
		return $this -> _ci -> curl -> ssl(FALSE) -> execute();
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
}
