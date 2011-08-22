<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//original code from http://kentislearningcodeigniter.com/facebook_connect
//phnx : 16-02-2011
class Facebook{
 
        function __construct(){
			$this->_ci =& get_instance();
        }

		function authentication($redirect=''){
			//echo implode('+',explode('/',$redirect))."       ";
			if($this->getUser()==null)
				redirect(('connect/redirect/').implode('+',explode('/',$redirect)));
		}
		
		function is_authentication(){
			if($this->getUser()==null)
				return false;
			return true;
		}
		
        function get_facebook_cookie() {
                $app_id             = $this->_ci->config->item('facebook_app_id');
                $application_secret = $this->_ci->config->item('facebook_api_secret');
				
                if(isset($_COOKIE['fbs_' . $app_id])){
				
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
                }
                else{
                        return null;
                }
        }

        function getUser(){
               if($cookie = $this->get_facebook_cookie()){
	                return json_decode(file_get_contents(
	                                'https://graph.facebook.com/me?access_token=' .
	                                $cookie['access_token']), true);
			   } else {
				return FALSE;
			   }
        }

        function getFriendIds($include_self = TRUE){
                $cookie = $this->get_facebook_cookie();
                $friends = @json_decode(file_get_contents(
                                'https://graph.facebook.com/me/friends?access_token=' .
                                $cookie['access_token']), true);
                $friend_ids = array();
                foreach($friends['data'] as $friend){
                        $friend_ids[] = $friend['id'];
                }
                if($include_self == TRUE){
                        $friend_ids[] = $cookie['uid'];                 
                }       

                return $friend_ids;
        }

        function getFriends($include_self = TRUE){
                $cookie = $this->get_facebook_cookie();
                $friends = @json_decode(file_get_contents(
                                'https://graph.facebook.com/me/friends?access_token=' .
                                $cookie['access_token']), true);
                
                if($include_self == TRUE){
                        $friends['data'][] = array(
                                'name'   => 'You',
                                'id' => $cookie['uid']
                        );                      
                }       

                return $friends['data'];
        }

        function getFriendArray($include_self = TRUE){
                $cookie = $this->get_facebook_cookie();
                $friendlist = @json_decode(file_get_contents(
                                'https://graph.facebook.com/me/friends?access_token=' .
                                $cookie['access_token']), true);
                $friends = array();
                foreach($friendlist['data'] as $friend){
                        $friends[$friend['id']] = $friend['name'];
                }
                if($include_self == TRUE){
                        $friends[$cookie['uid']] = 'You';                       
                }       

                return $friends;
        }
		
		//phnx's modification
		function postWall($message=null){

			$this->load->library('curl');
			$cookie = $this->get_facebook_cookie();
			
			$access_token_string = 'access_token='.$cookie['access_token'];

			$url = "https://graph.facebook.com/feed";
			$this->curl->create($url);
			
			$this->load->database();
			$this->load->library('settings');
			$settings = $this->settings->get_settings();
			$post = array(
						'access_token'=>$cookie['access_token'],
						'message'=>$settings['share_fb_message'],
						'picture'=>$settings['share_fb_picture'],
						'link'=>$settings['share_fb_url'],
						'name'=>$settings['share_fb_name'],
						'caption'=>$settings['share_fb_caption'],
						'description'=>$settings['share_fb_description']
	
						);
						
			//print_r($post);
			$this->curl->post($post);
			$this->curl->execute();
			
			//print_r($this->curl->info);
		}
		
		function getGraph($id){
			$this->_ci->load->library('curl');
			$url = "https://graph.facebook.com/".$id;
			$this->_ci->curl->create($url);
			return json_decode($this->_ci->curl->execute());
		}

		/** 
		 * JSON : Get facebook pages owned by the current user
		 * @author Prachya P.
		 */
		function get_user_pages(){
			$cookie = $this->get_facebook_cookie();
			
			//include page from app
            /*$pages = json_decode(file_get_contents(
                            'https://graph.facebook.com/me/accounts?access_token=' .
                            $cookie['access_token']), true);*/
                           
            //not include page from app
            $pages = json_decode(file_get_contents(
                            'https://graph.facebook.com/me/accounts/page?access_token=' .
                            $cookie['access_token']), true);
            return $pages;
		}
		
		/** 
		 * JSON : Get facebook page info
		 * @author Prachya P.
		 * @param $fb_page_id
		 */
		function get_page_info($fb_page_id){
			$cookie = $this->get_facebook_cookie();
            $page_info = json_decode(file_get_contents(
                            'https://graph.facebook.com/'.$fb_page_id.'/?access_token=' .
                            $cookie['access_token']), true);
            return $page_info;
		}
		
		/**
		 * Get facebook profile picture
		 * @author Manassarn M.
		 * @param $facebook_user_id
		 */
		function get_profile_picture($facebook_user_id){
			return "https://graph.facebook.com/{$facebook_user_id}/picture";
		}
}
