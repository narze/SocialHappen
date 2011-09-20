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
			if($facebook_user = $this->_ci->session->userdata('facebook_user')){
				return json_decode(base64_decode($facebook_user), TRUE);
		    } else if($cookie = $this->get_facebook_cookie()){
				$facebook_result = file_get_contents(
								'https://graph.facebook.com/me?access_token=' .
								$cookie['access_token']);
								// $facebook_result = '{"id":"755758746","name":"Metwara Narksook","first_name":"Metwara","last_name":"Narksook","link":"http:\/\/www.facebook.com\/hybridknight","username":"hybridknight","bio":"127.0.0.1\r\n\r\nComputer Engineering Student, \r\nChulalongkorn University","gender":"male","email":"book2k\u0040hotmail.com","timezone":7,"locale":"en_US","verified":true,"updated_time":"2011-08-04T14:13:34+0000"}';
								// echo "<pre>" . $facebook_result . "</pre>";
				$this->_ci->session->set_userdata(array('facebook_user' => base64_encode($facebook_result)));
				return json_decode($facebook_result, true);
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
			$this->curl->ssl(FALSE)->execute();
			
			//print_r($this->curl->info);
		}
		
		function getGraph($id){
			$this->_ci->load->library('curl');
			$url = "https://graph.facebook.com/".$id;
			$this->_ci->curl->create($url);
			return json_decode($this->_ci->curl->ssl(FALSE)->execute());
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
						$page = file_get_contents(
                            'https://graph.facebook.com/'.$fb_page_id.'/?access_token=' .
                            $cookie['access_token']);
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
		function get_profile_picture($facebook_user_id){
			return "https://graph.facebook.com/{$facebook_user_id}/picture";
		}
}