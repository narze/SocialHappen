<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class app_component_lib_test extends CI_Controller {
  
  var $achievement_lib;
  
  function __construct(){
    parent::__construct();
    $this->load->library('unit_test');
    $this->load->library('app_component_lib');
    $this->load->library('achievement_lib');
    $this->load->model('app_component_model','app_component');
    $this->load->model('app_component_page_model','app_component_page');
    $this->load->model('achievement_info_model','achievement_info');
    $this->load->model('achievement_stat_model','achievement_stat');
    $this->load->model('achievement_stat_page_model','achievement_stat_page');
    $this->load->model('achievement_user_model','achievement_user');
  }

  function __destruct(){
    $this->unit->report_with_counter();
  }
  
  function index(){
    $class_methods = get_class_methods($this);
    foreach ($class_methods as $method) {
        if(preg_match("/(_test)$/",$method)){
          $this->$method();
        }
    }
  }

  function clear_data_before_test(){
    $this->achievement_info->drop_collection();
    $this->achievement_stat->drop_collection();
    $this->achievement_stat_page->drop_collection();
    $this->achievement_user->drop_collection();
    $this->app_component->drop_collection();
    $this->app_component_page->drop_collection();
  }
  
  function create_index_before_test(){
    $this->app_component_lib->create_index();
    $this->achievement_lib->create_index();
  }
  
  function add_campaign_test(){
    $campaign_id = 1;
    $app_id = 2;
    $app_install_id = 3;
    $page_id = 4;
    $app_component_data = array(
      'campaign_id' => $campaign_id,
      'homepage' => array(
        'enable' => TRUE,
        'image' => 'https://localhost/assets/images/blank.png',
        'message' => 'You are not this page\'s fan, please like this page first'
      ),
      'invite' => array(
        'facebook_invite' => TRUE,
        'email_invite' => TRUE,
        'criteria' => array(
          'score' => 1,
          'maximum' => 5,
          'cooldown' => 300,
          'acceptance_score' => array(
            'page' => 100,
            'campaign' => 20
          )
        ),
        'message' => array(
          'title' => 'You are invited',
          'text' => 'Welcome to the campaign',
          'image' => 'https://localhost/assets/images/blank.png'
        )
      ),
      'sharebutton' => array(
        'facebook_button' => TRUE,
        'twitter_button' => TRUE,
        'criteria' => array(
          'score' => 1,
          'maximum' => 5,
          'cooldown' => 300
        ),
        'message' => array(
          'title' => 'Join the campaign by this link',
          'caption' => 'This is caption',
          'text' => 'this is long description',
          'image' => 'https://localhost/assets/images/blank.png',
        )
      )
    );
    
    $result = $this->app_component_lib->add_campaign($app_component_data);
    $this->unit->run($result, TRUE,'Add app_component with full data', print_r($result, TRUE));
    $this->unit->run($this->app_component->count_all(), 1, 'count all app_component');
  }

  function add_page_test(){
    $campaign_id = 1;
    $app_id = 2;
    $app_install_id = 3;
    $page_id = 4;
    $info = array(
      'app_id' => $app_id,
      'app_install_id' => $app_install_id,
      'campaign_id' => $campaign_id
    );
    
    $app_component_page_data = array(
      'page_id' => $page_id,
      'classes' => array(
        array('name' => 'Founding',
              'invite_accepted' => 3),
        array('name' => 'VIP',
              'invite_accepted' => 10),
        array('name' => 'Prime',
              'invite_accepted' => 50)
      )
    );
    
    $result = $this->app_component_lib->add_page($app_component_page_data);
    $this->unit->run($result, TRUE,'Add app_component_page with full data', print_r($result, TRUE));
    $this->unit->run($this->app_component_page->count_all(), 1, 'count all app_component_page');
    
    $page = $this->app_component_lib->get_page($page_id);
    $classes = $page['classes'];

    $this->unit->run(count($classes), 3, 'count all classes');
    
    
    $achievement_list = $this->achievement_lib->list_achievement_info_by_page_id($page_id);
    $this->unit->run(count($achievement_list), count($classes), 'count all achievement_list');
    
    for($i = 0; $i < count($classes); $i++){
      $class = $classes[$i];
      
      $achievement = $this->achievement_lib->get_achievement_info($class['achievement_id']);
      // $this->unit->run($achievement['campaign_id'], $campaign_id, '');
      $this->unit->run($achievement['info']['name'],
        $class['name'] , '');
      $this->unit->run($achievement['criteria']['page.action.113.count'],
        $class['invite_accepted'] , '');
    }
  }

  function get_page_test(){
    $page_id = 4;
    $result = $this->app_component_lib->get_page($page_id);
    $this->unit->run($result['page_id'], $page_id,'Add app_component_page with full data', print_r($result, TRUE));
  }
  
  function update_page_class_test(){
    $page_id = 4;
    $page = $this->app_component_lib->get_page($page_id);
    $this->unit->run($page['page_id'], $page_id,'Add app_component_page with full data', print_r($page, TRUE));
    
    $calsses = $page['classes'];
    $calsses[0]['name'] = 'New Founder';
    $calsses[0]['invite_accepted'] = 4;
    
    $calsses[2]['name'] = 'Super Prime';
    $calsses[2]['invite_accepted'] = 54;
    
    // $result = $this->app_component_lib->update_page_classes($page_id, $result);
    // $this->unit->run($result, TRUE,'Add app_component_page with full data', print_r($result, TRUE));
  }
  
  function update_page_class_with_new_class_test(){
    $page_id = 4;
    $page = $this->app_component_lib->get_page($page_id);
    $this->unit->run($page['page_id'], $page_id,'Add app_component_page with full data', print_r($page, TRUE));
    
    $calsses = $page['classes'];   
    $calsses[3]['name'] = 'Super Prime';
    $calsses[3]['invite_accepted'] = 54;
    
    
  }

  function update_page_class_remove_test(){
    $page_id = 4;
    $page = $this->app_component_lib->get_page($page_id);
    $this->unit->run($page['page_id'], $page_id,'Add app_component_page with full data', print_r($page, TRUE));
    
    $classes = $page['classes'];
    unset($classes[0]);
    
    
  }
  
  function end_test(){
    // $this->achievement_info->drop_collection();
    // $this->achievement_stat->drop_collection();
    // $this->achievement_user->drop_collection();
  }
}
/* End of file app_component_lib_test.php */
/* Location: ./application/controllers/test/app_component_lib_test.php */