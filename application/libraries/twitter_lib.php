<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Twitter Library
 * 
 * TwitterOAuth Wrapper library for codeigniter
 * 
 * @author Manassarn M.
 */
require_once('twitteroauth.php');

class Twitter_lib{
	private $CI;
	function __construct() {
		$this->CI =& get_instance();
		return $this;
    }

    function init($params = NULL){
		$consumer_key = $this->CI->config->item('twitter_consumer_key');
	    $consumer_secret = $this->CI->config->item('twitter_consumer_secret');
	    $oauth_token = isset($params['oauth_token']) ? $params['oauth_token'] : NULL;
	    $oauth_token_secret = isset($params['oauth_token_secret']) ? $params['oauth_token_secret']: NULL;
        
		$this->CI->twitter = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
    }

    function store_request_token($request_token = NULL){
		$twitter_userdata = array('oauth_token' => NULL, 'oauth_token_secret' => NULL);
		if(isset($request_token['oauth_token'])){
			$twitter_userdata['oauth_token'] = $request_token['oauth_token'];
		}
		if(isset($request_token['oauth_token_secret'])){
			$twitter_userdata['oauth_token_secret'] = $request_token['oauth_token_secret'];
		}
		$this->CI->session->set_userdata('twitter', $twitter_userdata);
    }

    function check_login_then_init(){
    	if($access_token = $this->CI->session->userdata('twitter_access_token')){
	    	$this->CI->twitter_lib->init($access_token);
	    	return TRUE;
	    } else {
	    	return FALSE;
	    }
    }
}
	