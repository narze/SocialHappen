<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Mongodb_reset extends CI_Migration {

	public function up(){
    /** achievement_info **/
    // Upsert achievement by criteria field
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
    foreach($platform_achievements as $new_achievement) {
      $query = array(
        'criteria' => $new_achievement['criteria']
      );

      //get existing achievement
      if($old_achievement = $this->achievement_info_model->getOne($query)) {
        //update
        echo 'Updating achievement ' . $old_achievement['info']['name'];
        if($this->achievement_info_model->set(get_mongo_id($old_achievement), $new_achievement['app_id'], $new_achievement['app_install_id'], $new_achievement['info'], $new_achievement['criteria'])) {
          echo ' Success!';
        } else {
          echo ' Failed!';
        }

        echo '<br />';
      }
    }

    /** audit_actions **/
    // Upsert audit action by audit_action_id

		echo 'Upgraded to version 6<br />';
	}

	public function down()
	{
		//Do nothing
		echo 'Downgraded to version 5<br />';
	}
}