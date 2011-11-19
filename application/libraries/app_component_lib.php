<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * App componentLibrary
 *
 *
 * @author Metwara Narksook
 */

class App_component_lib
{
  
  private $CI;
  
  /**
   *  ------------------------------------------------------------------------
   *  CONSTRUCTOR
   *  ------------------------------------------------------------------------
   *
   *  Automatically check if the Mongo PECL extension has been 
   *  installed/enabled.
   * 
   * @author Metwara Narksook
   */
  
  function __construct(){
    if(!class_exists('Mongo')){
      show_error("The MongoDB PECL extension has not been installed or enabled",
       500);
    }
    $this->CI =& get_instance();
  }
  
  
  function create_index(){
    $this->CI->load->model('app_component_model','app_component');
    $this->CI->app_component->create_index();
  }
  
  /**
   * Add app_component
   * @param $app_component = array(
   *    'campaign_id' => [campaign_id] 
   *    [component_1] => [component 1 array data] 
   *    [component_2] => [component 2 array data]
   *    and so on...
   * )
   * @param $info = array(
   *  'app_id' =>
   *  'app_install_id' =>
   *  'page_id' =>
   *   // required for achievement
   * )
   * @author Metwara Narksook
   */
  function add_campaign($app_component = array(), $info = array()){
    
    $check_args = (isset($info['app_id']) && isset($info['app_install_id'])
      && isset($info['page_id']) && isset($app_component['campaign_id']));
    
    if(!$check_args){
      return FALSE;
    }
    
    $add_campaign_result = $this->CI->app_component->add($app_component);
    
    $app_id = $info['app_id'];
    $app_install_id = $info['app_install_id'];
    $page_id = $info['page_id'];
    $campaign_id = $app_component['campaign_id'];
    
    $add_all_achievement = TRUE;
    $added_achievement_id_list = array();
    if($add_campaign_result){
      $this->CI->load->library('achievement_lib');
      
      $classes = $app_component['invite']['classes'];
      
      for($i = 0; $i < count($classes); $i++){
        $class = $classes[$i];
        $info = array('name' => $class['name'],
                      'description' => $class['name'],
                      'criteria_string' => array('at least '
                       . $class['invite_accepted'] . ' invite accepted'),
                      'page_id' => $page_id,
                      'campaign_id' => $campaign_id);
        $criteria = array('page.action.113.count' => $class['invite_accepted']);
        
        $added_achievement_id = $this->CI->achievement_lib->
          add_achievement_info($app_id, $app_install_id, $info, $criteria);

        if(!isset($added_achievement_id)){
          $add_all_achievement = FALSE;
          break;
        }else{
          $app_component['invite']['classes'][$i]['achievement_id'] = 
            '' . $added_achievement_id;
          $added_achievement_id_list[] = '' . $added_achievement_id;
        }
      }
      
      $update_invite = $this->CI->app_component->
        update_invite_by_campaign_id($campaign_id, $app_component['invite']);
    }
    $result = $add_campaign_result && $add_all_achievement && $update_invite;
    
    if(!$result){
      // rollback
      foreach ($added_achievement_id_list as $achievement_id) {
        $this->CI->achievement_lib->delete_achievement_info($achievement_id);
      }
      
      $this->CI->app_component->delete($campaign_id);
      
      return FALSE;
    }else{
      return TRUE;
    }
    
  }
  
  function get_campaign($campaign_id = NULL){
    return $this->CI->app_component->get_by_campaign_id($campaign_id);
  }
  
}
/* End of file app_component_lib.php */
/* Location: ./application/libraries/app_component_lib.php */
    