<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sharebutton Library
 * 
 * @author Manassarn M.
 */
class Sharebutton_lib {

	function __construct() {
        $this->CI =& get_instance();
    }

	function facebook_share($sharebutton = NULL){
		$this->CI->load->library('fb_library/fb_library',
			array(
			  'appId'  => $this->CI->config->item('facebook_app_id'),
			  'secret' => $this->CI->config->item('facebook_api_secret'),
			  'cookie' => true,
			),
			'FB');
		if($facebook_user = $this->CI->FB->getUser() && $this->CI->socialhappen->get_user()){
			try {
				if($sharebutton){
			        $post = $this->CI->FB->api('/me/feed', 'POST',
	                    array(
	                      	'link' => 'google.com', //$sharebutton['message']['link'],
	                      	'message' => $sharebutton['message']['text']
	                 	));
	                if(isset($post['id'])){
	                	//TODO : do something with $sharebutton['criteria']
	                }
	            } else {
	            	$post = $this->CI->FB->api('/me/feed', 'POST',
	                    array(
	                      	'link' => 'www.example.com',
	                      	'message' => 'Posting with the PHP SDK!'
	                	));
	            }
		        return $post['id'];

	      	} catch(FacebookApiException $e) {
		        log_message('error', $e->getType());
		        log_message('error', $e->getMessage());
		        return FALSE;
	      	}   
      	} else {
      		//no fb || no sh user. please login again
      		return FALSE;
      	}
	}
}