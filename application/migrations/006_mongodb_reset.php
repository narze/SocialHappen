<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Mongodb_reset extends CI_Migration {

	public function up(){
    /** achievement_info **/
    // update achievement by criteria field
    $platform_achievements = array(
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'I\'m using SocialHappen',
          'description' => 'Share profile the 1st time',
          'criteria_string' => array('Share Profile = 1'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.107.count' => 1
        ),
        // 'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Bragger',
          'description' => 'Share profile 10 times',
          'criteria_string' => array('Share Profile = 10'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.107.count' => 10
        ),
        // 'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Hello World',
          'description' => 'Share the 1st time',
          'criteria_string' => array('Share = 1'),
          'badge_image' => base_url().'assets/images/badges/50-helloworld.png'
        ),
        'criteria' => array(
          'action.108.count' => 1
        ),
        // 'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Speaker',
          'description' => 'Share 10 times',
          'criteria_string' => array('Share = 10'),
          'badge_image' => base_url().'assets/images/badges/50-speaker.png'
        ),
        'criteria' => array(
          'action.108.count' => 10
        ),
        // 'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Crazy Reporter',
          'description' => 'Share 50 times',
          'criteria_string' => array('Share = 50'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.108.count' => 50
        ),
        // 'score' => 250
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'News Channel',
          'description' => 'Share 100 times',
          'criteria_string' => array('Share = 100'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.108.count' => 100
        ),
        // 'score' => 500
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Share Troll',
          'description' => 'Share 250 times',
          'criteria_string' => array('Share = 250'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.108.count' => 250
        ),
        // 'score' => 1000
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Just Arrived',
          'description' => 'Sign Up SocialHappen',
          'criteria_string' => array('Signup = 1'),
          'badge_image' => base_url().'assets/images/badges/50-arrived.png'
        ),
        'criteria' => array(
          'action.101.count' => 1
        ),
        // 'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Hello the Club',
          'description' => 'First time register to any page',
          'criteria_string' => array('Register Page = 1'),
          'badge_image' => base_url().'assets/images/badges/50-helloclub.png'
        ),
        'criteria' => array(
          'action.106.count' => 1
        ),
        // 'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Club Newbie',
          'description' => 'Joined 3 SocialHappen Pages',
          'criteria_string' => array('Register Page = 3'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.106.count' => 3
        ),
        // 'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Club Master',
          'description' => 'Joined 10 SocialHappen Pages',
          'criteria_string' => array('Register Page = 10'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.106.count' => 10
        ),
        // 'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Hello Old Friend',
          'description' => 'Login 5 times',
          'criteria_string' => array('Login Count = 5'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.109.count' => 5
        ),
        // 'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Kudos For Coming Back',
          'description' => 'Login 10 times',
          'criteria_string' => array('Login Count = 10'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.109.count' => 10
        ),
        // 'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Loyal Friend',
          'description' => 'Login 50 times',
          'criteria_string' => array('Login Count = 50'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.109.count' => 50
        ),
        // 'score' => 100
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'The Tweet Bird',
          'description' => 'Connect to Twitter',
          'criteria_string' => array('Connect Twitter Count = 1'),
          'badge_image' => base_url().'assets/images/badges/50-tw.png'
        ),
        'criteria' => array(
          'action.110.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'The Mark Zuckerburg Network Effect',
          'description' => 'Connect to Facebook',
          'criteria_string' => array('Connect Facebook Count = 1'),
          'badge_image' => base_url().'assets/images/badges/50-fb.png'
        ),
        'criteria' => array(
          'action.111.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'I\'m a Mayor',
          'description' => 'Connect to Foursquare',
          'criteria_string' => array('Connect Foursquare Count = 1'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.112.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'You\'re Not Alone',
          'description' => 'Invite your 1st friend',
          'criteria_string' => array('Invite = 1'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'I Have A Team',
          'description' => 'Invite 10 friends',
          'criteria_string' => array('Invite = 10'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 10
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Social Animal',
          'description' => 'Invite 50 friends',
          'criteria_string' => array('Invite = 50'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 50
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Celebrity',
          'description' => 'Invite 100 Friends',
          'criteria_string' => array('Invite = 100'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 100
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'The Invitation Engine',
          'description' => 'Invite 500 Friends',
          'criteria_string' => array('Invite = 500'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 500
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'You Are Admin',
          'description' => 'Buy a package',
          'criteria_string' => array('Package Bought = 1'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.7.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Nobel',
          'description' => 'Buy the most expensive package',
          'criteria_string' => array('Package Bought = Most Expensive'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.8.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Admin Newbie',
          'description' => 'Install SocialHappen to Facebook page',
          'criteria_string' => array('Install page = 1'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.5.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Page Admin Newbie', //Temp name
          'description' => 'Install apps to SocialHappen page',
          'criteria_string' => array('Install app to page = 1'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.2.count' => 1
        ),
        // 'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'First Challenge Done',
          'description' => 'Completed challenge the first time',
          'criteria_string' => array('Challenge completed = 1'),
          'badge_image' => base_url().'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.118.count' => 1
        ),
        // 'score' => 0
      ),
    );

    $this->load->model('achievement_info_model');
    $success_count = 0;
    foreach($platform_achievements as $new_achievement) {
      $query = array(
        'info.name' => $new_achievement['info']['name']
      );

      //get existing achievement
      if($old_achievement = $this->achievement_info_model->getOne($query)) {
        //update
        echo 'Updating achievement ' . $old_achievement['info']['name'];
        if($this->achievement_info_model->set(get_mongo_id($old_achievement), $new_achievement['app_id'], $new_achievement['app_install_id'], $new_achievement['info'], $new_achievement['criteria'])) {
          $success_count++;
          echo ' Success!';
        } else {
          echo ' Failed!';
        }

        echo '<br />';
      }
    }
    echo 'Updated '.$success_count.'/'.count(array_merge($platform_achievements)).' achievement_infos<br />';

    /** audit_actions **/
    // update audit action by audit_action_id
    $platform_audit_actions = array(
      array(
        'app_id' => 0,
        'action_id' => 1,
        'description' => 'Install App',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has installed {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 2,
        'description' => 'Install App To Page',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has installed {app:object} in {page:page_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 3,
        'description' => 'Remove App',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has removed {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 4,
        'description' => 'Update Config',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has updated {app:app_id} configuration',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 5,
        'description' => 'Install Page',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has installed {page:page_id} in {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 6,
        'description' => 'Create Company',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has created company {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 7,
        'description' => 'Buy Package',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has bought package {package:object}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 8,
        'description' => 'Buy Most Expensive Package',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has bought the most expensive package {package:object}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 101,
        'description' => 'User Register SocialHappen',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} has registered SocialHappen',
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'action_id' => 102,
        'description' => 'User Register App',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} has registered {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 103,
        'description' => 'User Visit',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} visited {app_install:app_install_id} in {page:page_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 104,
        'description' => 'User Action',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} has action', //@TODO What action?
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 105,
        'description' => 'User Join Campaign',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} has joined {campaign:campaign_id} in {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 106,
        'description' => 'User Register Page',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} registered {page:page_id}',
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'action_id' => 107,
        'description' => 'User Share Profile',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} shared profile',
        'score' => 5
      ),
      array(
        'app_id' => 0,
        'action_id' => 108,
        'description' => 'User Share For Star',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} shared on {app_install:app_install_id}',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 109,
        'description' => 'User Login',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} logged in',
        'score' => 5
      ),
      array(
        'app_id' => 0,
        'action_id' => 110,
        'description' => 'User Link to Twitter',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} linked with Twitter account',
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'action_id' => 111,
        'description' => 'User Link to Facebook',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} linked with Facebook account',
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'action_id' => 112,
        'description' => 'User Link to Foursquare',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} linked with Foursquare account',
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'action_id' => 113,
        'description' => 'User Invite Friend',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} invited a friend',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 114,
        'description' => 'Invitee Accept Page Invite',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} accepted page invite from {user:subject}',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 115,
        'description' => 'Invitee Accept Campaign Invite',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => TRUE,
        'format_string' => '{user:user_id} accepted campaign invite from {user:subject}',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 116,
        'description' => 'User Receive Coupon',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} received a reward coupon from company {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 117,
        'description' => 'User Join Challenge',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} joined challenge {challenge:objecti}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 118,
        'description' => 'User Complete Challenge',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} completed challenge {challenge:objecti}',
        'score' => 0 //User will get score from its reward
      ),
      array(
        'app_id' => 0,
        'action_id' => 119,
        'description' => 'User Redeem Reward',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} redeemed {string:object} from company {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 201,
        'description' => 'QR',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} entered QR code in challenge {challenge:objecti}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 202,
        'description' => 'Feedback',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} gave feedback and rated {string:object} in challenge {challenge:objecti}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 203,
        'description' => 'Check-In',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} check-in at {string:object} in challenge {challenge:objecti}',
        'score' => 0
      ),
    );

    $audit_actions = array(
      array(
        'app_id' => 5,
        'action_id' => 1001,
        'description' => 'View video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has viewed video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 5,
        'action_id' => 1002,
        'description' => 'Share video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has shared video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 6,
        'action_id' => 1001,
        'description' => 'View video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has viewed video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 6,
        'action_id' => 1002,
        'description' => 'Share video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has shared video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 7,
        'action_id' => 1001,
        'description' => 'Share feed',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has shared feed {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 8,
        'action_id' => 1001,
        'description' => 'Share feed',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} has shared feed {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 13,
        'action_id' => 2000,
        'description' => 'User answer a question',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} answered question {string: object} with answer {string: objecti} in {page: page_id}',
        'score' => 0
        ),
      array(
        'app_id' => 15,
        'action_id' => 1001,
        'description' => 'User votes an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} votes item {string: object} in {page: page_id}',
        'score' => 0
        ),
      array(
        'app_id' => 15,
        'action_id' => 1002,
        'description' => 'User shares an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} answered question {string: object} in {page: page_id}',
        'score' => 0
        ),
      array(
        'app_id' => 16,
        'action_id' => 1001,
        'description' => 'User votes an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} votes item {string: object} in {page: page_id}',
        'score' => 1
        ),
      array(
        'app_id' => 16,
        'action_id' => 1002,
        'description' => 'User shares an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} answered question {string: object} in {page: page_id}',
        'score' => 1
        ),
    );

    $this->load->model('audit_action_model');
    $success_count = 0;
    foreach(array_merge($platform_audit_actions, $audit_actions) as $new_audit_action) {
      $query = array(
        'app_id' => $new_audit_action['app_id'],
        'action_id' => $new_audit_action['action_id']
      );

      //get existing audit_action
      if($old_audit_action = $this->audit_action_model->getOne($query)) {
        //update
        $update_data = array(
          '$set' => $new_audit_action
        );

        echo 'Updating audit_action ' . $old_audit_action['description'];
        if($this->audit_action_model->update($query, $update_data)) {
          $success_count++;
          echo ' Success!';
        } else {
          echo ' Failed!';
        }

        echo '<br />';
      }
    }
    echo 'Updated '.$success_count.'/'.count(array_merge($platform_audit_actions, $audit_actions)).' audit_actions<br />';
		echo 'Upgraded to version 6<br />';
	}

	public function down()
	{
		//Do nothing
		echo 'Downgraded to version 5<br />';
	}
}